<?php

// app/Http/Middleware/CheckUsage.php

namespace App\Http\Middleware;

use App\Services\UsageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUsage
{
    protected $usageService;

    public function __construct(UsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  string  $feature
     * @param  int  $quantity
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $feature, $quantity = 1)
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

        // Check if user can use the feature across all their subscriptions
        $canUse = $this->usageService->canUseFeature($user->id, $feature, $quantity);

        // Enhanced logging for debugging
        \Log::info('Usage check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'feature' => $feature,
            'quantity' => $quantity,
            'can_use' => $canUse['can_use'] ?? false,
            'total_subscriptions' => $canUse['total_subscriptions'] ?? 0,
            'total_remaining' => $canUse['total_remaining'] ?? 0,
            'message' => $canUse['message'] ?? 'No message',
        ]);

        if (! ($canUse['can_use'] ?? false)) {
            // Check if user has any active subscriptions
            if (($canUse['total_subscriptions'] ?? 0) === 0) {
                // No active subscriptions
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $canUse['message'] ?? 'No active subscription found',
                        'data' => [
                            'requires_subscription' => true,
                            'subscriptions_url' => route('user.subscriptions.index'),
                            'plans_url' => route('website.plans.index'),
                        ],
                    ], 403);
                }

                return redirect()->route('website.plans.index')
                    ->with('error', $canUse['message'] ?? 'Please subscribe to a plan to access this feature');
            }

            // Has subscriptions but all are at limit
            $bestSubscription = null;
            $bestRemaining = 0;

            if (isset($canUse['subscription_details'])) {
                foreach ($canUse['subscription_details'] as $detail) {
                    if (isset($detail['result']['remaining']) &&
                        is_numeric($detail['result']['remaining']) &&
                        $detail['result']['remaining'] > $bestRemaining) {
                        $bestRemaining = $detail['result']['remaining'];
                        $bestSubscription = $detail;
                    }
                }
            }

            $responseData = [
                'requires_subscription' => false,
                'requires_upgrade' => true,
                'current_usage' => $canUse['total_available_limit'] ?? 0,
                'limit' => $canUse['total_available_limit'] ?? 0,
                'remaining' => $canUse['total_remaining'] ?? 0,
                'total_subscriptions' => $canUse['total_subscriptions'] ?? 0,
                'upgrade_url' => route('website.plans.index'),
                'subscriptions_url' => route('user.subscriptions.index'),
            ];

            // Add best subscription info if available
            if ($bestSubscription) {
                $responseData['best_available_subscription'] = [
                    'subscription_id' => $bestSubscription['subscription_id'],
                    'plan_name' => $bestSubscription['plan_name'],
                    'remaining' => $bestSubscription['result']['remaining'] ?? 0,
                    'limit' => $bestSubscription['result']['limit'] ?? 0,
                    'current' => $bestSubscription['result']['current'] ?? 0,
                ];
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $canUse['message'] ?? 'You have reached your usage limit',
                    'data' => $responseData,
                ], 403);
            }

            // For web requests, store in session for display
            session()->flash('usage_error', [
                'message' => $canUse['message'] ?? 'You have reached your usage limit',
                'data' => $responseData,
            ]);

            return redirect()->back()->with('error', $canUse['message'] ?? 'Usage limit exceeded');
        }

        // Store usage info in request for later use
        $request->merge([
            '_usage_check' => [
                'feature' => $feature,
                'quantity' => $quantity,
                'allowed' => true,
                'total_remaining' => $canUse['total_remaining'] ?? null,
                'total_subscriptions' => $canUse['total_subscriptions'] ?? 0,
                'subscription_details' => $canUse['subscription_details'] ?? [],
            ],
        ]);

        // Add usage info to response for views
        view()->share('usage_check', [
            'feature' => $feature,
            'remaining' => $canUse['total_remaining'] ?? null,
        ]);

        return $next($request);
    }
}
