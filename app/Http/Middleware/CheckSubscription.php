<?php

// app/Http/Middleware/CheckSubscription.php

namespace App\Http\Middleware;

use App\Models\Subscription;
use App\Services\UsageService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    protected $usageService;

    public function __construct(UsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  string|null  $requiredPlan
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $requiredPlan = null)
    {
        // Check if user is logged in
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to access this feature',
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Please login to access this feature');
        }

        $user = Auth::user();

        // Get user's active subscriptions (multiple)
        $activeSubscriptions = $this->getActiveSubscriptions($user);

        // Check if any free plan exists
        $hasFreePlan = $this->hasFreePlan($user);

        // Check subscription validity across all subscriptions
        $subscriptionStatus = $this->checkSubscriptionsValidity($user, $activeSubscriptions);

        // Enhanced logging for debugging
        \Log::info('Subscription check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'has_free_plan' => $hasFreePlan,
            'total_subscriptions' => $activeSubscriptions->count(),
            'subscription_status' => $subscriptionStatus['status'],
            'valid_subscriptions' => $subscriptionStatus['valid_count'],
            'message' => $subscriptionStatus['message'],
            'required_plan' => $requiredPlan,
        ]);

        // No valid subscriptions and no free plan
        if (! $subscriptionStatus['valid'] && ! $hasFreePlan) {
            $responseData = [
                'requires_subscription' => true,
                'redirect_to' => route('website.plans.index'),
                'plans_url' => route('website.plans.index'),
                'subscriptions_url' => route('user.subscriptions.index'),
                'message' => $subscriptionStatus['message'],
            ];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $subscriptionStatus['message'],
                    'data' => $responseData,
                ], 403);
            }

            return redirect()->route('website.plans.index')
                ->with('error', $subscriptionStatus['message']);
        }

        // Check specific plan requirement across subscriptions
        if ($requiredPlan && $requiredPlan !== 'any') {
            $hasRequiredPlan = $this->hasRequiredPlan($user, $requiredPlan, $activeSubscriptions);

            if (! $hasRequiredPlan) {
                $currentPlans = $this->getUserPlanNames($user, $activeSubscriptions);
                $message = $this->getPlanRequirementMessage($requiredPlan, $currentPlans);

                $responseData = [
                    'required_plan' => $requiredPlan,
                    'current_plans' => $currentPlans,
                    'upgrade_url' => route('website.plans.index'),
                    'subscriptions_url' => route('user.subscriptions.index'),
                ];

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'data' => $responseData,
                    ], 403);
                }

                return redirect()->route('website.plans.index')
                    ->with('error', $message);
            }
        }

        // Check specific feature if needed (via middleware parameter)
        // Example: subscription:any,crud_generation
        if ($requiredPlan && strpos($requiredPlan, ',') !== false) {
            [$plan, $feature] = explode(',', $requiredPlan);

            if (! $this->hasFeature($user, $feature, $activeSubscriptions)) {
                $message = "Your current plan does not support the '{$feature}' feature.";

                // Get which subscriptions have this feature
                $subscriptionsWithFeature = $this->getSubscriptionsWithFeature($activeSubscriptions, $feature);

                $responseData = [
                    'required_feature' => $feature,
                    'subscriptions_with_feature' => $subscriptionsWithFeature,
                    'upgrade_url' => route('website.plans.index'),
                ];

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'data' => $responseData,
                    ], 403);
                }

                return redirect()->route('website.plans.index')
                    ->with('error', $message);
            }
        }

        // Store subscription info in request for later use
        $request->merge([
            '_subscription_check' => [
                'valid' => true,
                'total_subscriptions' => $activeSubscriptions->count(),
                'valid_subscriptions' => $subscriptionStatus['valid_subscriptions'],
                'has_free_plan' => $hasFreePlan,
                'subscriptions' => $activeSubscriptions->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'plan_name' => $sub->plan->name ?? 'Unknown',
                        'status' => $sub->status,
                        'expires_at' => $sub->current_period_ends_at?->format('Y-m-d'),
                    ];
                }),
            ],
        ]);

        // Share subscription info with views
        view()->share('active_subscriptions', $activeSubscriptions);
        view()->share('has_active_subscription', $subscriptionStatus['valid'] || $hasFreePlan);
        view()->share('subscription_status', $subscriptionStatus);

        return $next($request);
    }

    /**
     * Get user's active subscriptions (multiple)
     */
    private function getActiveSubscriptions($user)
    {
        return Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->where(function ($query) {
                $query->whereNull('current_period_ends_at')
                    ->orWhere('current_period_ends_at', '>', Carbon::now());
            })
            ->with('plan')
            ->orderBy('created_at') // Oldest first, or you can implement priority logic
            ->get();
    }

    /**
     * Check validity across all subscriptions
     */
    private function checkSubscriptionsValidity($user, $subscriptions): array
    {
        if ($subscriptions->isEmpty()) {
            return [
                'valid' => false,
                'valid_count' => 0,
                'status' => 'no_subscription',
                'message' => 'Please subscribe to a plan to access this feature',
                'valid_subscriptions' => [],
            ];
        }

        $validSubscriptions = [];
        $expiredSubscriptions = [];
        $pastDueSubscriptions = [];

        foreach ($subscriptions as $subscription) {
            // Check if subscription is expired
            if ($subscription->current_period_ends_at &&
                $subscription->current_period_ends_at <= Carbon::now()) {
                $expiredSubscriptions[] = [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->plan->name ?? 'Unknown',
                    'expired_at' => $subscription->current_period_ends_at->format('Y-m-d'),
                ];
                continue;
            }

            // Check if subscription is past_due
            if ($subscription->status === 'past_due') {
                $pastDueSubscriptions[] = [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->plan->name ?? 'Unknown',
                ];
                continue;
            }

            // Valid subscription
            $daysLeft = $subscription->current_period_ends_at
                ? Carbon::now()->diffInDays($subscription->current_period_ends_at, false)
                : null;

            $validSubscriptions[] = [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'plan_name' => $subscription->plan->name ?? 'Unknown',
                'plan_id' => $subscription->plan_id,
                'days_left' => $daysLeft,
                'expires_at' => $subscription->current_period_ends_at?->format('Y-m-d'),
                'amount' => $subscription->amount,
                'currency' => $subscription->currency,
            ];
        }

        $validCount = count($validSubscriptions);
        $hasValid = $validCount > 0;

        // Build appropriate message
        $message = $this->buildValidityMessage($validCount, $expiredSubscriptions, $pastDueSubscriptions);

        return [
            'valid' => $hasValid,
            'valid_count' => $validCount,
            'status' => $hasValid ? 'valid' : 'invalid',
            'message' => $message,
            'valid_subscriptions' => $validSubscriptions,
            'expired_subscriptions' => $expiredSubscriptions,
            'past_due_subscriptions' => $pastDueSubscriptions,
            'total_subscriptions' => $subscriptions->count(),
        ];
    }

    /**
     * Build validity message based on subscription statuses
     */
    private function buildValidityMessage($validCount, $expired, $pastDue): string
    {
        if ($validCount > 0) {
            return "You have {$validCount} active subscription(s)";
        }

        if (!empty($expired) && !empty($pastDue)) {
            return "Your subscriptions have expired or are past due. Please renew to continue.";
        }

        if (!empty($expired)) {
            $count = count($expired);
            return "Your subscription" . ($count > 1 ? 's have' : ' has') . " expired. Please renew to continue.";
        }

        if (!empty($pastDue)) {
            $count = count($pastDue);
            return "Your subscription payment" . ($count > 1 ? 's are' : ' is') . " past due. Please update payment method.";
        }

        return "No valid subscriptions found";
    }

    /**
     * Check if user has free plan
     */
    private function hasFreePlan($user): bool
    {
        // Check for free plan subscription
        $freeSubscription = Subscription::where('user_id', $user->id)
            ->whereHas('plan', function ($query) {
                $query->where('name', 'LIKE', '%free%')
                    ->orWhere('code', 'LIKE', '%FREE%')
                    ->orWhere('amount', 0);
            })
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if ($freeSubscription) {
            return true;
        }

        // Check if user is in trial period
        $hasTrial = Subscription::where('user_id', $user->id)
            ->where('status', 'trialing')
            ->where('trial_ends_at', '>', Carbon::now())
            ->exists();

        return $hasTrial;
    }

    /**
     * Check if user has required plan across all subscriptions
     */
    private function hasRequiredPlan($user, $requiredPlan, $subscriptions): bool
    {
        if ($subscriptions->isEmpty()) {
            return false;
        }

        $requiredPlan = strtolower($requiredPlan);

        foreach ($subscriptions as $subscription) {
            if (!$subscription->plan) {
                continue;
            }

            $planName = strtolower($subscription->plan->name ?? '');
            $planCode = strtolower($subscription->plan->code ?? '');

            // Check by plan name or code
            if (strpos($planName, $requiredPlan) !== false ||
                strpos($planCode, $requiredPlan) !== false) {
                return true;
            }

            // Check by plan ID if numeric
            if (is_numeric($requiredPlan) && $subscription->plan_id == $requiredPlan) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user's current plan names
     */
    private function getUserPlanNames($user, $subscriptions): array
    {
        if ($subscriptions->isEmpty()) {
            return ['No Plan'];
        }

        return $subscriptions->map(function($sub) {
            return $sub->plan->name ?? 'Unknown';
        })->toArray();
    }

    /**
     * Get plan requirement message
     */
    private function getPlanRequirementMessage($requiredPlan, $currentPlans): string
    {
        $currentPlansList = implode(', ', $currentPlans);

        if (strpos($requiredPlan, ',') !== false) {
            [$plan, $feature] = explode(',', $requiredPlan);
            return "The '{$feature}' feature requires a {$plan} plan. Your current plan(s): {$currentPlansList}";
        }

        return "This feature requires a {$requiredPlan} plan. Your current plan(s): {$currentPlansList}";
    }

    /**
     * Check if user has specific feature across all subscriptions
     */
    private function hasFeature($user, $featureCode, $subscriptions): bool
    {
        if ($subscriptions->isEmpty()) {
            return false;
        }

        foreach ($subscriptions as $subscription) {
            if (!$subscription->plan) {
                continue;
            }

            // Check if feature exists in plan_features table
            $hasFeature = \DB::table('plan_features')
                ->join('features', 'plan_features.feature_id', '=', 'features.id')
                ->where('plan_features.plan_id', $subscription->plan_id)
                ->where('features.code', $featureCode)
                ->exists();

            if ($hasFeature) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get subscriptions that have a specific feature
     */
    private function getSubscriptionsWithFeature($subscriptions, $featureCode): array
    {
        $result = [];

        foreach ($subscriptions as $subscription) {
            if (!$subscription->plan) {
                continue;
            }

            $hasFeature = \DB::table('plan_features')
                ->join('features', 'plan_features.feature_id', '=', 'features.id')
                ->where('plan_features.plan_id', $subscription->plan_id)
                ->where('features.code', $featureCode)
                ->exists();

            if ($hasFeature) {
                $result[] = [
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan->name ?? 'Unknown',
                    'status' => $subscription->status,
                ];
            }
        }

        return $result;
    }

    /**
     * Get subscription usage for a specific feature across all subscriptions
     */
    private function getFeatureUsage($user, $featureCode): array
    {
        $feature = \DB::table('features')->where('code', $featureCode)->first();

        if (!$feature) {
            return ['used' => 0, 'limit' => 0, 'percentage' => 0];
        }

        $subscriptions = $this->getActiveSubscriptions($user);
        $totalUsage = 0;
        $totalLimit = 0;
        $isUnlimited = false;

        foreach ($subscriptions as $subscription) {
            // Get current period usage
            $usage = \DB::table('usage_records')
                ->where('subscription_id', $subscription->id)
                ->where('feature_id', $feature->id)
                ->whereMonth('billing_date', Carbon::now()->month)
                ->whereYear('billing_date', Carbon::now()->year)
                ->sum('quantity');

            // Get feature limit from plan_features
            $limit = \DB::table('plan_features')
                ->join('subscriptions', 'plan_features.plan_id', '=', 'subscriptions.plan_id')
                ->where('subscriptions.id', $subscription->id)
                ->where('plan_features.feature_id', $feature->id)
                ->value('plan_features.value');

            $totalUsage += (float) $usage;

            if ($limit === 'unlimited') {
                $isUnlimited = true;
            } elseif (is_numeric($limit)) {
                $totalLimit += (float) $limit;
            }
        }

        return [
            'used' => $totalUsage,
            'limit' => $isUnlimited ? 'unlimited' : $totalLimit,
            'is_unlimited' => $isUnlimited,
            'percentage' => (!$isUnlimited && $totalLimit > 0) ? round(($totalUsage / $totalLimit) * 100, 2) : 0,
        ];
    }
}
