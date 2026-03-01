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

        // Check if user can use the feature
        $canUse = $this->usageService->canUseFeature($user->id, $feature, $quantity);

        if (! $canUse['allowed']) {
            \Log::warning('Usage check failed', [
                'user_id' => $user->id,
                'feature' => $feature,
                'quantity' => $quantity,
                'reason' => $canUse['message'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $canUse['message'],
                    'data' => [
                        'requires_subscription' => $canUse['requires_subscription'] ?? false,
                        'requires_upgrade' => $canUse['requires_upgrade'] ?? false,
                        'current_usage' => $canUse['current'] ?? null,
                        'limit' => $canUse['limit'] ?? null,
                        'remaining' => $canUse['remaining'] ?? null,
                        'upgrade_url' => route('website.plans.index'),
                    ],
                ], 403);
            }

            return redirect()->route('website.plans.index')
                ->with('error', $canUse['message']);
        }

        // Store usage info in request for later use
        $request->merge([
            '_usage_check' => [
                'feature' => $feature,
                'quantity' => $quantity,
                'allowed' => true,
                'remaining' => $canUse['remaining'] ?? null,
            ],
        ]);

        return $next($request);
    }
}
