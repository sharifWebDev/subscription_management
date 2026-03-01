<?php

// app/Http/Middleware/CheckSubscription.php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
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

        // Get user's active subscription
        $activeSubscription = $this->getActiveSubscription($user);

        // Check subscription validity
        $subscriptionStatus = $this->checkSubscriptionValidity($user);

        // Log for debugging
        \Log::info('Subscription check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'has_subscription' => ! is_null($activeSubscription),
            'subscription_status' => $subscriptionStatus['status'],
            'message' => $subscriptionStatus['message'],
            'required_plan' => $requiredPlan,
        ]);

        // No valid subscription
        if (! $subscriptionStatus['valid']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $subscriptionStatus['message'],
                    'data' => [
                        'requires_subscription' => true,
                        'redirect_to' => route('website.plans.index'),
                    ],
                ], 403);
            }

            return redirect()->route('website.plans.index')
                ->with('error', $subscriptionStatus['message']);
        }

        // Check specific plan requirement
        if ($requiredPlan && $requiredPlan !== 'any') {
            if (! $this->hasRequiredPlan($user, $requiredPlan)) {
                $message = "This feature requires a {$requiredPlan} plan.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'data' => [
                            'required_plan' => $requiredPlan,
                            'current_plan' => $this->getUserPlanName($user),
                            'upgrade_url' => route('website.plans.index'),
                        ],
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

            if (! $this->hasFeature($user, $feature)) {
                $message = "Your current plan does not support the '{$feature}' feature.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'data' => [
                            'required_feature' => $feature,
                            'upgrade_url' => route('website.plans.index'),
                        ],
                    ], 403);
                }

                return redirect()->route('website.plans.index')
                    ->with('error', $message);
            }
        }

        // Subscription is valid, proceed with request
        return $next($request);
    }

    /**
     * Get user's active subscription
     */
    private function getActiveSubscription($user)
    {
        return Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'trialing'])
            ->where(function ($query) {
                $query->whereNull('current_period_ends_at')
                    ->orWhere('current_period_ends_at', '>', Carbon::now());
            })
            ->with('plan')
            ->first();
    }

    /**
     * Check subscription validity
     */
    private function checkSubscriptionValidity($user): array
    {
        $activeSubscription = $this->getActiveSubscription($user);

        // No subscription found
        if (! $activeSubscription) {
            // Check if user has any free plan
            $hasFreePlan = $this->hasFreePlan($user);

            if ($hasFreePlan) {
                return [
                    'valid' => true,
                    'status' => 'free',
                    'message' => 'You are on free plan',
                    'plan_name' => 'Free',
                    'plan_id' => null,
                ];
            }

            return [
                'valid' => false,
                'status' => 'no_subscription',
                'message' => 'Please subscribe to a plan to access this feature',
            ];
        }

        // Check if subscription is expired
        if ($activeSubscription->current_period_ends_at &&
            $activeSubscription->current_period_ends_at <= Carbon::now()) {

            return [
                'valid' => false,
                'status' => 'expired',
                'message' => 'Your subscription has expired. Please renew to continue.',
                'expired_at' => $activeSubscription->current_period_ends_at->format('Y-m-d'),
                'plan_name' => $activeSubscription->plan->name ?? 'Unknown',
            ];
        }

        // Check if subscription is past_due
        if ($activeSubscription->status === 'past_due') {
            return [
                'valid' => false,
                'status' => 'past_due',
                'message' => 'Your subscription payment is past due. Please update payment method.',
                'plan_name' => $activeSubscription->plan->name ?? 'Unknown',
            ];
        }

        // Valid subscription
        $daysLeft = $activeSubscription->current_period_ends_at
            ? Carbon::now()->diffInDays($activeSubscription->current_period_ends_at, false)
            : null;

        return [
            'valid' => true,
            'status' => $activeSubscription->status,
            'message' => 'Subscription is valid',
            'plan_name' => $activeSubscription->plan->name ?? 'Unknown',
            'plan_id' => $activeSubscription->plan_id,
            'subscription_id' => $activeSubscription->id,
            'days_left' => $daysLeft,
            'expires_at' => $activeSubscription->current_period_ends_at?->format('Y-m-d'),
            'amount' => $activeSubscription->amount,
            'currency' => $activeSubscription->currency,
        ];
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
     * Check if user has required plan
     */
    private function hasRequiredPlan($user, $requiredPlan): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        $planName = strtolower($subscription->plan->name ?? '');
        $planCode = strtolower($subscription->plan->code ?? '');
        $requiredPlan = strtolower($requiredPlan);

        // Check by plan name or code
        if (strpos($planName, $requiredPlan) !== false ||
            strpos($planCode, $requiredPlan) !== false) {
            return true;
        }

        // Check by plan ID if numeric
        if (is_numeric($requiredPlan) && $subscription->plan_id == $requiredPlan) {
            return true;
        }

        return false;
    }

    /**
     * Get user's current plan name
     */
    private function getUserPlanName($user): string
    {
        $subscription = $this->getActiveSubscription($user);

        return $subscription && $subscription->plan ? $subscription->plan->name : 'No Plan';
    }

    /**
     * Check if user has specific feature
     */
    private function hasFeature($user, $featureCode): bool
    {
        $subscription = $this->getActiveSubscription($user);

        if (! $subscription || ! $subscription->plan) {
            return false;
        }

        // Check if feature exists in plan_features table
        $hasFeature = \DB::table('plan_features')
            ->join('features', 'plan_features.feature_id', '=', 'features.id')
            ->where('plan_features.plan_id', $subscription->plan_id)
            ->where('features.code', $featureCode)
            ->exists();

        return $hasFeature;
    }

    /**
     * Get subscription usage for a specific feature
     */
    private function getFeatureUsage($subscriptionId, $featureCode): array
    {
        $feature = \DB::table('features')->where('code', $featureCode)->first();

        if (! $feature) {
            return ['used' => 0, 'limit' => 0, 'percentage' => 0];
        }

        // Get current period usage
        $usage = \DB::table('usage_records')
            ->where('subscription_id', $subscriptionId)
            ->where('feature_id', $feature->id)
            ->whereMonth('billing_date', Carbon::now()->month)
            ->whereYear('billing_date', Carbon::now()->year)
            ->sum('quantity');

        // Get feature limit from plan_features
        $limit = \DB::table('plan_features')
            ->join('subscriptions', 'plan_features.plan_id', '=', 'subscriptions.plan_id')
            ->where('subscriptions.id', $subscriptionId)
            ->where('plan_features.feature_id', $feature->id)
            ->value('plan_features.value');

        $limitValue = $limit === 'unlimited' ? PHP_INT_MAX : (float) $limit;

        return [
            'used' => (float) $usage,
            'limit' => $limit,
            'percentage' => $limitValue > 0 ? round(($usage / $limitValue) * 100, 2) : 0,
        ];
    }
}
