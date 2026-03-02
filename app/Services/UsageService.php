<?php

// app/Services/UsageService.php

namespace App\Services;

use App\Models\MeteredUsageAggregate;
use App\Models\RateLimit;
use App\Models\Subscription;
use App\Models\UsageRecord;
use App\Traits\UsageTrait;
use Illuminate\Support\Facades\DB;

class UsageService
{
    use UsageTrait;

    /**
     * Check if user can use a feature across all their subscriptions
     */
    public function canUseFeature($userId, $featureCode, $quantity = 1)
    {
        $subscriptions = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->orderBy('created_at') // Oldest first, or implement priority logic
            ->get();

        if ($subscriptions->isEmpty()) {
            return [
                'can_use' => false,
                'message' => 'No active subscription found',
                'requires_subscription' => true,
                'subscriptions' => [],
            ];
        }

        $results = [];
        $totalAvailable = 0;
        $totalRemaining = 0;
        $canUseAny = false;

        foreach ($subscriptions as $subscription) {
            $result = $this->checkUsageLimit($subscription->id, $featureCode, $quantity);
 

            $results[] = [
                'subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,
                'plan_name' => $subscription->plan->name ?? 'Unknown',
                'result' => $result,
            ];

            if ($result['allowed']) {
                $canUseAny = true;
                if (isset($result['remaining']) && is_numeric($result['remaining'])) {
                    $totalRemaining += $result['remaining'];
                }
            }

            if (isset($result['limit']) && is_numeric($result['limit'])) {
                $totalAvailable += $result['limit'];
            }
        }

        return [
            'can_use' => $canUseAny,
            'message' => $canUseAny ? 'Usage allowed across subscriptions' : 'No subscription with sufficient limits',
            'total_subscriptions' => $subscriptions->count(),
            'total_available_limit' => $totalAvailable,
            'total_remaining' => $totalRemaining,
            'subscription_details' => $results,
        ];
    }

    /**
     * Record CRUD generation usage - automatically finds best subscription
     */
    public function recordCrudGeneration($userId, $metadata = [])
    {
        $subscriptionIds = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->pluck('id')
            ->toArray();

        if (empty($subscriptionIds)) {
            return [
                'success' => false,
                'message' => 'No active subscriptions found',
            ];
        }

        return $this->recordUsage(
            $subscriptionIds,
            'crud_generation',
            1,
            'generation',
            array_merge($metadata, ['type' => 'crud', 'generated_at' => now()->toDateTimeString()])
        );
    }

    /**
     * Get CRUD generation usage summary across all subscriptions
     */
    public function getCrudGenerationSummary($userId)
    {
        $subscriptions = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->get();

        if ($subscriptions->isEmpty()) {
            return null;
        }

        $summaries = [];
        $totalUsage = 0;
        $totalLimit = 0;
        $isUnlimited = false;

        foreach ($subscriptions as $subscription) {
            $summary = $this->getUsageSummary($subscription->id, 'crud_generation');
            if ($summary) {
                $summaries[] = array_merge($summary, [
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->status,
                ]);

                $totalUsage += $summary['current_usage'];

                if ($summary['is_unlimited']) {
                    $isUnlimited = true;
                } elseif (is_numeric($summary['limit'])) {
                    $totalLimit += $summary['limit'];
                }
            }
        }

        return [
            'subscriptions' => $summaries,
            'aggregated' => [
                'total_usage' => $totalUsage,
                'total_limit' => $isUnlimited ? 'unlimited' : $totalLimit,
                'is_unlimited' => $isUnlimited,
                'percentage' => $isUnlimited ? 0 : round(($totalUsage / max(1, $totalLimit)) * 100, 2),
                'remaining' => $isUnlimited ? 'unlimited' : max(0, $totalLimit - $totalUsage),
            ],
        ];
    }

    /**
     * Check rate limit across all subscriptions
     */
    public function checkRateLimit($userId, $featureCode)
    {
        $subscriptions = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->get();

        if ($subscriptions->isEmpty()) {
            return ['allowed' => false, 'message' => 'No active subscriptions'];
        }

        $feature = DB::table('features')->where('code', $featureCode)->first();
        if (! $feature) {
            return ['allowed' => false, 'message' => 'Feature not found'];
        }

        $bestRateLimit = null;
        $bestRemaining = -1;

        foreach ($subscriptions as $subscription) {
            $key = "subscription:{$subscription->id}:feature:{$feature->id}";

            $rateLimit = RateLimit::where('subscription_id', $subscription->id)
                ->where('feature_id', $feature->id)
                ->where('key', $key)
                ->first();

            if ($rateLimit && $rateLimit->remaining > $bestRemaining) {
                $bestRemaining = $rateLimit->remaining;
                $bestRateLimit = $rateLimit;
            }
        }

        if (! $bestRateLimit) {
            return ['allowed' => true, 'message' => 'No rate limit configured'];
        }

        if ($bestRateLimit->remaining <= 0) {
            return [
                'allowed' => false,
                'message' => 'Rate limit exceeded across all subscriptions',
                'resets_at' => $bestRateLimit->resets_at,
                'remaining' => 0,
                'max_attempts' => $bestRateLimit->max_attempts,
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $bestRateLimit->remaining,
            'max_attempts' => $bestRateLimit->max_attempts,
            'resets_at' => $bestRateLimit->resets_at,
        ];
    }

    /**
     * Get metered usage aggregates for reporting across subscriptions
     */
    public function getMeteredAggregates($userId, $period = 'monthly', $startDate = null, $endDate = null)
    {
        $subscriptionIds = Subscription::where('user_id', $userId)
            ->pluck('id')
            ->toArray();

        if (empty($subscriptionIds)) {
            return collect();
        }

        $query = MeteredUsageAggregate::whereIn('subscription_id', $subscriptionIds)
            ->where('aggregate_period', $period);

        if ($startDate) {
            $query->where('aggregate_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('aggregate_date', '<=', $endDate);
        }

        return $query->orderBy('aggregate_date', 'desc')->get();
    }

    /**
     * Get usage statistics for dashboard across all user subscriptions
     */
    public function getUsageStatistics($userId)
    {
        $subscriptions = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->with('plan')
            ->get();

        if ($subscriptions->isEmpty()) {
            return [
                'has_subscription' => false,
                'message' => 'No active subscriptions',
                'subscriptions' => [],
            ];
        }

        $allSummaries = $this->getAllUsageSummaries($userId);

        // Group summaries by feature for aggregated view
        $aggregatedByFeature = [];
        foreach ($allSummaries as $summary) {
            $featureCode = $summary['feature_code'];
            if (!isset($aggregatedByFeature[$featureCode])) {
                $aggregatedByFeature[$featureCode] = [
                    'feature_name' => $summary['feature_name'],
                    'total_usage' => 0,
                    'total_limit' => 0,
                    'is_unlimited' => false,
                    'subscriptions' => [],
                ];
            }

            $aggregatedByFeature[$featureCode]['total_usage'] += $summary['current_usage'];
            $aggregatedByFeature[$featureCode]['subscriptions'][] = $summary;

            if ($summary['is_unlimited']) {
                $aggregatedByFeature[$featureCode]['is_unlimited'] = true;
            } elseif (is_numeric($summary['limit'])) {
                $aggregatedByFeature[$featureCode]['total_limit'] += $summary['limit'];
            }
        }

        // Get recent usage records across all subscriptions
        $recentUsage = UsageRecord::whereIn('subscription_id', $subscriptions->pluck('id'))
            ->with('feature')
            ->orderBy('recorded_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'subscription_id' => $record->subscription_id,
                    'feature' => $record->feature->name ?? 'Unknown',
                    'feature_code' => $record->feature->code ?? null,
                    'quantity' => $record->quantity,
                    'unit' => $record->unit,
                    'recorded_at' => $record->recorded_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get daily aggregates across all subscriptions
        $dailyAggregates = MeteredUsageAggregate::whereIn('subscription_id', $subscriptions->pluck('id'))
            ->where('aggregate_period', 'daily')
            ->whereMonth('aggregate_date', now()->month)
            ->orderBy('aggregate_date')
            ->get()
            ->groupBy('feature_id')
            ->map(function ($items, $featureId) {
                $feature = DB::table('features')->find($featureId);

                return [
                    'feature' => $feature->name ?? 'Unknown',
                    'data' => $items->map(function ($item) {
                        return [
                            'date' => $item->aggregate_date,
                            'quantity' => $item->total_quantity,
                        ];
                    }),
                ];
            });

        return [
            'has_subscription' => true,
            'total_subscriptions' => $subscriptions->count(),
            'subscriptions' => $subscriptions->map(function($sub) {
                return [
                    'id' => $sub->id,
                    'plan_name' => $sub->plan->name ?? 'Unknown',
                    'status' => $sub->status,
                ];
            }),
            'aggregated_by_feature' => $aggregatedByFeature,
            'detailed_summaries' => $allSummaries,
            'recent_usage' => $recentUsage,
            'daily_aggregates' => $dailyAggregates,
        ];
    }

    /**
     * Bulk record usage across best available subscriptions
     */
    public function bulkRecordUsage($userId, array $usages)
    {
        $subscriptionIds = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->pluck('id')
            ->toArray();

        if (empty($subscriptionIds)) {
            return [
                'success' => false,
                'message' => 'No active subscriptions found',
                'results' => [],
            ];
        }

        $results = [];

        foreach ($usages as $usage) {
            $result = $this->recordUsage(
                $subscriptionIds,
                $usage['feature_code'],
                $usage['quantity'],
                $usage['unit'] ?? 'count',
                $usage['metadata'] ?? []
            );

            $results[] = $result;
        }

        $successCount = count(array_filter($results, fn($r) => $r['success'] ?? false));

        return [
            'success' => $successCount > 0,
            'message' => "Processed {$successCount} of " . count($results) . " usage records",
            'results' => $results,
        ];
    }

    /**
     * Get usage forecast for upcoming billing across all subscriptions
     */
    public function getUsageForecast($userId)
    {
        $subscriptions = Subscription::with('plan')
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->get();

        if ($subscriptions->isEmpty()) {
            return null;
        }

        $forecasts = [];

        foreach ($subscriptions as $subscription) {
            $daysInPeriod = $this->getDaysInPeriod($subscription);
            $daysElapsed = $this->getDaysElapsed($subscription);
            $daysRemaining = $daysInPeriod - $daysElapsed;

            $planFeatures = DB::table('plan_features')
                ->join('features', 'plan_features.feature_id', '=', 'features.id')
                ->where('plan_features.plan_id', $subscription->plan_id)
                ->select('features.*', 'plan_features.value as limit_value')
                ->get();

            foreach ($planFeatures as $feature) {
                if ($feature->limit_value === 'unlimited' || ! is_numeric($feature->limit_value)) {
                    continue;
                }

                $currentUsage = $this->getCurrentPeriodUsage($subscription->id, $feature->id);

                $dailyRate = $daysElapsed > 0 ? $currentUsage / $daysElapsed : 0;
                $projectedUsage = $currentUsage + ($dailyRate * $daysRemaining);

                $forecasts[] = [
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->plan->name ?? 'Unknown',
                    'subscription_status' => $subscription->status,
                    'feature_code' => $feature->code,
                    'feature_name' => $feature->name,
                    'current_usage' => $currentUsage,
                    'limit' => (float) $feature->limit_value,
                    'daily_rate' => round($dailyRate, 2),
                    'projected_usage' => round($projectedUsage, 2),
                    'days_remaining' => $daysRemaining,
                    'will_exceed' => $projectedUsage > (float) $feature->limit_value,
                    'overage' => round(max(0, $projectedUsage - (float) $feature->limit_value), 2),
                ];
            }
        }

        // Group forecasts by feature across subscriptions
        $groupedForecasts = collect($forecasts)->groupBy('feature_code')->map(function($items) {
            return [
                'feature_name' => $items->first()['feature_name'],
                'total_current_usage' => $items->sum('current_usage'),
                'total_limit' => $items->sum('limit'),
                'total_projected' => $items->sum('projected_usage'),
                'will_exceed' => $items->contains('will_exceed', true),
                'subscriptions' => $items,
            ];
        });

        return [
            'user_id' => $userId,
            'total_subscriptions' => $subscriptions->count(),
            'forecasts_by_feature' => $groupedForecasts,
            'detailed_forecasts' => $forecasts,
        ];
    }

    /**
     * Get days in current billing period
     */
    private function getDaysInPeriod($subscription)
    {
        if (! $subscription->current_period_starts_at || ! $subscription->current_period_ends_at) {
            return 30; // Default to 30 days
        }

        return $subscription->current_period_starts_at->diffInDays($subscription->current_period_ends_at);
    }

    /**
     * Get days elapsed in current billing period
     */
    private function getDaysElapsed($subscription)
    {
        if (! $subscription->current_period_starts_at) {
            return 0;
        }

        return $subscription->current_period_starts_at->diffInDays(now());
    }
}
