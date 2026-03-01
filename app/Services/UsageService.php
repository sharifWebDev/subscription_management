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
     * Check if user can use a feature
     */
    public function canUseFeature($userId, $featureCode, $quantity = 1)
    {
        $subscription = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if (! $subscription) {
            return [
                'can_use' => false,
                'message' => 'No active subscription found',
                'requires_subscription' => true,
            ];
        }

        return $this->checkUsageLimit($subscription->id, $featureCode, $quantity);
    }

    /**
     * Record CRUD generation usage
     */
    public function recordCrudGeneration($subscriptionId, $metadata = [])
    {
        return $this->recordUsage(
            $subscriptionId,
            'crud_generation',
            1,
            'generation',
            array_merge($metadata, ['type' => 'crud', 'generated_at' => now()->toDateTimeString()])
        );
    }

    /**
     * Get CRUD generation usage summary
     */
    public function getCrudGenerationSummary($subscriptionId)
    {
        return $this->getUsageSummary($subscriptionId, 'crud_generation');
    }

    /**
     * Check rate limit for a feature
     */
    public function checkRateLimit($subscriptionId, $featureCode)
    {
        $feature = DB::table('features')->where('code', $featureCode)->first();
        if (! $feature) {
            return ['allowed' => false, 'message' => 'Feature not found'];
        }

        $key = "subscription:{$subscriptionId}:feature:{$feature->id}";

        $rateLimit = RateLimit::where('subscription_id', $subscriptionId)
            ->where('feature_id', $feature->id)
            ->where('key', $key)
            ->first();

        if (! $rateLimit) {
            return ['allowed' => true, 'message' => 'No rate limit configured'];
        }

        if ($rateLimit->remaining <= 0) {
            return [
                'allowed' => false,
                'message' => 'Rate limit exceeded',
                'resets_at' => $rateLimit->resets_at,
                'remaining' => 0,
                'max_attempts' => $rateLimit->max_attempts,
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $rateLimit->remaining,
            'max_attempts' => $rateLimit->max_attempts,
            'resets_at' => $rateLimit->resets_at,
        ];
    }

    /**
     * Get metered usage aggregates for reporting
     */
    public function getMeteredAggregates($subscriptionId, $period = 'monthly', $startDate = null, $endDate = null)
    {
        $query = MeteredUsageAggregate::where('subscription_id', $subscriptionId)
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
     * Get usage statistics for dashboard
     */
    public function getUsageStatistics($userId)
    {
        $subscription = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing'])
            ->first();

        if (! $subscription) {
            return [
                'has_subscription' => false,
                'message' => 'No active subscription',
            ];
        }

        $summaries = $this->getAllUsageSummaries($subscription->id);

        // Get recent usage records
        $recentUsage = UsageRecord::where('subscription_id', $subscription->id)
            ->with('feature')
            ->orderBy('recorded_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'feature' => $record->feature->name ?? 'Unknown',
                    'feature_code' => $record->feature->code ?? null,
                    'quantity' => $record->quantity,
                    'unit' => $record->unit,
                    'recorded_at' => $record->recorded_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get daily aggregates for current month
        $dailyAggregates = MeteredUsageAggregate::where('subscription_id', $subscription->id)
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
            'subscription_id' => $subscription->id,
            'plan_name' => $subscription->plan->name ?? 'Unknown',
            'status' => $subscription->status,
            'summaries' => $summaries,
            'recent_usage' => $recentUsage,
            'daily_aggregates' => $dailyAggregates,
        ];
    }

    /**
     * Bulk record usage (for multiple operations)
     */
    public function bulkRecordUsage($subscriptionId, array $usages)
    {
        $results = [];

        foreach ($usages as $usage) {
            $result = $this->recordUsage(
                $subscriptionId,
                $usage['feature_code'],
                $usage['quantity'],
                $usage['unit'] ?? 'count',
                $usage['metadata'] ?? []
            );

            $results[] = $result;
        }

        return $results;
    }

    /**
     * Get usage forecast for upcoming billing
     */
    public function getUsageForecast($subscriptionId)
    {
        $subscription = Subscription::with('plan')->find($subscriptionId);
        if (! $subscription) {
            return null;
        }

        $daysInPeriod = $this->getDaysInPeriod($subscription);
        $daysElapsed = $this->getDaysElapsed($subscription);
        $daysRemaining = $daysInPeriod - $daysElapsed;

        $planFeatures = DB::table('plan_features')
            ->join('features', 'plan_features.feature_id', '=', 'features.id')
            ->where('plan_features.plan_id', $subscription->plan_id)
            ->select('features.*', 'plan_features.value as limit_value')
            ->get();

        $forecasts = [];

        foreach ($planFeatures as $feature) {
            if ($feature->limit_value === 'unlimited' || ! is_numeric($feature->limit_value)) {
                continue;
            }

            $currentUsage = $this->getCurrentPeriodUsage($subscriptionId, $feature->id);

            $dailyRate = $daysElapsed > 0 ? $currentUsage / $daysElapsed : 0;
            $projectedUsage = $currentUsage + ($dailyRate * $daysRemaining);

            $forecasts[] = [
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

        return [
            'subscription_id' => $subscriptionId,
            'period_start' => $subscription->current_period_starts_at?->format('Y-m-d'),
            'period_end' => $subscription->current_period_ends_at?->format('Y-m-d'),
            'days_elapsed' => $daysElapsed,
            'days_remaining' => $daysRemaining,
            'forecasts' => $forecasts,
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
