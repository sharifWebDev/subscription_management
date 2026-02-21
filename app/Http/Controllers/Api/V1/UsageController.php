<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\MeteredUsageAggregate;
use App\Models\Subscription;
use App\Models\UsageRecord;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsageController extends Controller
{
    /**
     * Get usage statistics for user's subscriptions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $subscriptionId = $request->get('subscription_id');
            $period = $request->get('period', 'monthly');
            $date = $request->get('date', Carbon::now()->format('Y-m'));

            // Get user's active subscriptions
            $subscriptions = Subscription::with(['plan', 'items.feature'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['active', 'trialing'])
                ->get();

            if ($subscriptions->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'has_subscription' => false,
                        'message' => 'No active subscriptions found',
                    ],
                ]);
            }

            // If specific subscription requested
            if ($subscriptionId) {
                $subscription = $subscriptions->find($subscriptionId);
                if (! $subscription) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Subscription not found',
                    ], 404);
                }

                $usageData = $this->getSubscriptionUsage($subscription, $period, $date);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'has_subscription' => true,
                        'subscriptions' => $subscriptions,
                        'current_subscription' => $subscription,
                        'usage' => $usageData,
                    ],
                ]);
            }

            // Return list of subscriptions for selector
            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched subscriptions',
                'data' => [
                    'has_subscription' => true,
                    'subscriptions' => $subscriptions,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch usage data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed usage for a specific subscription
     */
    public function show($subscriptionId, Request $request): JsonResponse
    {
        try {
            $subscriptionId = (int) $subscriptionId;
            $user = Auth::user();

            $subscription = Subscription::with(['plan', 'items.feature'])
                ->where('user_id', $user->id)
                ->where('id', $subscriptionId)
                ->firstOrFail();

            $period = $request->get('period', 'monthly');
            $date = $request->get('date', Carbon::now()->format('Y-m'));

            $usageData = $this->getSubscriptionUsage($subscription, $period, $date);

            // Get daily breakdown for chart
            $dailyUsage = $this->getDailyUsage($subscription->id, $date);

            // Get feature limits from plan
            $featureLimits = $this->getFeatureLimits($subscription);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched subscription usage',
                'data' => [
                    'subscription' => $subscription,
                    'summary' => $usageData['summary'],
                    'features' => $usageData['features'],
                    'chart_data' => $dailyUsage,
                    'limits' => $featureLimits,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription usage',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get usage statistics across all user subscriptions
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $startDate = $request->get('start_date', Carbon::now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            $subscriptions = Subscription::where('user_id', $user->id)
                ->whereIn('status', ['active', 'trialing', 'past_due'])
                ->pluck('id');

            // Get monthly aggregates
            $monthlyStats = MeteredUsageAggregate::whereIn('subscription_id', $subscriptions)
                ->where('aggregate_period', 'monthly')
                ->whereBetween('aggregate_date', [$startDate, $endDate])
                ->with('feature')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->aggregate_date)->format('Y-m');
                });

            // Calculate totals
            $totalUsage = UsageRecord::whereIn('subscription_id', $subscriptions)
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->sum('quantity');

            $totalCost = UsageRecord::whereIn('subscription_id', $subscriptions)
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->sum('amount');

            // Most used features
            $topFeatures = UsageRecord::whereIn('subscription_id', $subscriptions)
                ->whereBetween('billing_date', [$startDate, $endDate])
                ->select('feature_id', DB::raw('SUM(quantity) as total_quantity'))
                ->with('feature')
                ->groupBy('feature_id')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched usage statistics',
                'data' => [
                    'period' => [
                        'start' => $startDate,
                        'end' => $endDate,
                    ],
                    'totals' => [
                        'total_usage' => $totalUsage,
                        'total_cost' => $totalCost,
                        'subscription_count' => $subscriptions->count(),
                    ],
                    'monthly_breakdown' => $monthlyStats,
                    'top_features' => $topFeatures,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch usage statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time usage for current billing period
     */
    public function currentBilling(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $subscriptionId = (int) $request->get('subscription_id');

            $subscription = Subscription::where('user_id', $user->id)
                ->where('id', $subscriptionId)
                ->whereIn('status', ['active', 'trialing'])
                ->firstOrFail();

            $periodStart = $subscription->current_period_starts_at;
            $periodEnd = $subscription->current_period_ends_at;

            // Get usage for current period
            $usage = UsageRecord::where('subscription_id', $subscription->id)
                ->whereBetween('billing_date', [$periodStart, $periodEnd])
                ->with('feature')
                ->get()
                ->groupBy('feature_id');

            // Calculate costs
            $currentUsage = [];
            $totalCost = 0;

            foreach ($usage as $featureId => $records) {
                $feature = $records->first()->feature;
                $totalQuantity = $records->sum('quantity');
                $featureCost = $records->sum('amount');
                $totalCost += $featureCost;

                $currentUsage[] = [
                    'feature_id' => $featureId,
                    'feature_name' => $feature->name,
                    'feature_code' => $feature->code,
                    'unit' => $records->first()->unit,
                    'total_quantity' => $totalQuantity,
                    'total_cost' => $featureCost,
                    'record_count' => $records->count(),
                ];
            }

            // Get remaining days in period
            $daysRemaining = Carbon::now()->diffInDays($periodEnd, false);
            $totalDays = Carbon::parse($periodStart)->diffInDays($periodEnd);
            $daysPassed = $totalDays - max(0, $daysRemaining);

            return response()->json([
                'success' => true,
                'message' => 'Successfully fetched current billing usage',
                'data' => [
                    'subscription' => [
                        'id' => $subscription->id,
                        'plan' => $subscription->plan->name,
                        'period_start' => $periodStart,
                        'period_end' => $periodEnd,
                        'days_passed' => $daysPassed,
                        'days_remaining' => max(0, $daysRemaining),
                        'progress_percentage' => ($daysPassed / $totalDays) * 100,
                    ],
                    'current_usage' => $currentUsage,
                    'total_cost' => $totalCost,
                    'currency' => $subscription->currency,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch current billing usage',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get usage for a specific subscription
     */
    protected function getSubscriptionUsage(Subscription $subscription, string $period, string $date): array
    {
        $startDate = $this->getPeriodStartDate($period, $date);
        $endDate = $this->getPeriodEndDate($period, $date);

        // Get usage records for the period
        $usageRecords = UsageRecord::where('subscription_id', $subscription->id)
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->with('feature')
            ->get();

        // Calculate summary
        $summary = [
            'total_usage' => $usageRecords->sum('quantity'),
            'total_cost' => $usageRecords->sum('amount'),
            'record_count' => $usageRecords->count(),
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];

        // Group by feature
        $features = [];
        foreach ($usageRecords->groupBy('feature_id') as $featureId => $records) {
            $feature = $records->first()->feature;
            $totalQuantity = $records->sum('quantity');

            // Get feature limit from subscription
            $limit = $this->getFeatureLimit($subscription, $featureId);

            $features[] = [
                'feature_id' => $featureId,
                'feature_name' => $feature->name,
                'feature_code' => $feature->code,
                'description' => $feature->description,
                'unit' => $records->first()->unit,
                'total_quantity' => $totalQuantity,
                'total_cost' => $records->sum('amount'),
                'record_count' => $records->count(),
                'limit' => $limit,
                'percentage' => $limit > 0 ? min(100, round(($totalQuantity / $limit) * 100, 2)) : 0,
                'daily_average' => $records->count() > 0 ? round($totalQuantity / $records->count(), 2) : 0,
            ];
        }

        return [
            'summary' => $summary,
            'features' => $features,
        ];
    }

    /**
     * Get daily usage for chart
     */
    protected function getDailyUsage(int $subscriptionId, string $month): array
    {
        $startDate = Carbon::parse($month.'-01')->startOfMonth();
        $endDate = Carbon::parse($month.'-01')->endOfMonth();

        $dailyUsage = UsageRecord::where('subscription_id', $subscriptionId)
            ->whereBetween('billing_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(billing_date) as date'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as record_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with zero
        $result = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayData = $dailyUsage->firstWhere('date', $dateStr);

            $result[] = [
                'date' => $dateStr,
                'label' => $currentDate->format('M d'),
                'quantity' => $dayData ? (float) $dayData->total_quantity : 0,
                'amount' => $dayData ? (float) $dayData->total_amount : 0,
                'records' => $dayData ? (int) $dayData->record_count : 0,
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    /**
     * Get feature limits from subscription
     */
    protected function getFeatureLimits(Subscription $subscription): array
    {
        $limits = [];

        foreach ($subscription->items as $item) {
            $limits[$item->feature_id] = [
                'limit' => $item->quantity,
                'unit' => $item->feature->unit ?? 'units',
                'name' => $item->feature->name,
            ];
        }

        return $limits;
    }

    /**
     * Get limit for a specific feature
     */
    protected function getFeatureLimit(Subscription $subscription, int $featureId): float
    {
        $item = $subscription->items->firstWhere('feature_id', $featureId);

        return $item ? (float) $item->quantity : 0;
    }

    /**
     * Get period start date based on period type
     */
    protected function getPeriodStartDate(string $period, string $date): string
    {
        $dateObj = Carbon::parse($date);

        switch ($period) {
            case 'daily':
                return $dateObj->startOfDay()->format('Y-m-d H:i:s');
            case 'weekly':
                return $dateObj->startOfWeek()->format('Y-m-d H:i:s');
            case 'monthly':
                return $dateObj->startOfMonth()->format('Y-m-d H:i:s');
            case 'yearly':
                return $dateObj->startOfYear()->format('Y-m-d H:i:s');
            default:
                return $dateObj->startOfMonth()->format('Y-m-d H:i:s');
        }
    }

    /**
     * Get period end date based on period type
     */
    protected function getPeriodEndDate(string $period, string $date): string
    {
        $dateObj = Carbon::parse($date);

        switch ($period) {
            case 'daily':
                return $dateObj->endOfDay()->format('Y-m-d H:i:s');
            case 'weekly':
                return $dateObj->endOfWeek()->format('Y-m-d H:i:s');
            case 'monthly':
                return $dateObj->endOfMonth()->format('Y-m-d H:i:s');
            case 'yearly':
                return $dateObj->endOfYear()->format('Y-m-d H:i:s');
            default:
                return $dateObj->endOfMonth()->format('Y-m-d H:i:s');
        }
    }
}
