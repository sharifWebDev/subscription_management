<?php

// app/Traits/UsageTrait.php

namespace App\Traits;

use App\Models\MeteredUsageAggregate;
use App\Models\RateLimit;
use App\Models\Subscription;
use App\Models\UsageRecord;
use Illuminate\Support\Facades\DB;

trait UsageTrait
{
    /**
     * Record usage for a subscription - will try multiple subscriptions if needed
     */
    public function recordUsage($subscriptionIds, $featureCode, $quantity, $unit = 'count', $metadata = [])
    {
        try {
            DB::beginTransaction();

            // Get feature ID
            $feature = DB::table('features')->where('code', $featureCode)->first();
            if (! $feature) {
                throw new \Exception("Feature not found: {$featureCode}");
            }

            // Handle single subscription ID or array
            $subscriptionIds = is_array($subscriptionIds) ? $subscriptionIds : [$subscriptionIds];

            // Get active subscriptions in order of priority
            $subscriptions = Subscription::with('plan')
                ->whereIn('id', $subscriptionIds)
                ->whereIn('status', ['active', 'trialing'])
                ->orderBy('created_at') // Oldest first, or you can implement custom priority logic
                ->get();

            if ($subscriptions->isEmpty()) {
                throw new \Exception("No active subscriptions found");
            }

            $usageRecord = null;
            $usedSubscription = null;
            $canUseResult = null;

            // Try each subscription until we find one that can accommodate the usage
            foreach ($subscriptions as $subscription) {
                $canUseResult = $this->checkUsageLimit($subscription->id, $featureCode, $quantity);

                if ($canUseResult['allowed']) {
                    $usedSubscription = $subscription;
                    break;
                }
            }

            // If no subscription can accommodate the usage
            if (!$usedSubscription) {
                DB::rollBack();

                // Get the best available result for feedback
                $bestResult = $this->getBestAvailableLimit($subscriptions, $featureCode);

                return [
                    'success' => false,
                    'message' => 'No subscription with sufficient limits available',
                    'data' => $bestResult,
                ];
            }

            // Get subscription item for the used subscription
            $subscriptionItem = DB::table('subscription_items')
                ->where('subscription_id', $usedSubscription->id)
                ->where('feature_id', $feature->id)
                ->first();

            if (! $subscriptionItem) {
                throw new \Exception("Subscription item not found for feature: {$featureCode}");
            }

            // Create usage record
            $usageRecord = UsageRecord::create([
                'subscription_id' => $usedSubscription->id,
                'subscription_item_id' => $subscriptionItem->id,
                'feature_id' => $feature->id,
                'quantity' => $quantity,
                'unit' => $unit,
                'status' => 'pending',
                'recorded_at' => now(),
                'billing_date' => now()->toDateString(),
                'metadata' => json_encode($metadata),
            ]);

            // Update metered usage aggregates
            $this->updateMeteredAggregates($usedSubscription->id, $feature->id, $quantity);

            // Update rate limits
            $this->updateRateLimit($usedSubscription->id, $feature->id, $quantity);

            // Log subscription event
            DB::table('subscription_events')->insert([
                'subscription_id' => $usedSubscription->id,
                'type' => 'usage_recorded',
                'data' => json_encode([
                    'feature' => $featureCode,
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'recorded_at' => now()->toDateTimeString(),
                    'alternative_subscriptions_tried' => $subscriptions->count() - 1,
                ]),
                'occurred_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Get updated usage summary
            $summary = $this->getUsageSummary($usedSubscription->id, $featureCode);

            return [
                'success' => true,
                'message' => 'Usage recorded successfully',
                'data' => [
                    'subscription_id' => $usedSubscription->id,
                    'usage_record_id' => $usageRecord->id,
                    'current_usage' => $summary['current_usage'],
                    'limit' => $summary['limit'],
                    'percentage' => $summary['percentage'],
                    'remaining' => $summary['remaining'],
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Usage recording failed: '.$e->getMessage(), [
                'subscription_ids' => $subscriptionIds,
                'feature' => $featureCode,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to record usage: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get the best available limit across all subscriptions
     */
    protected function getBestAvailableLimit($subscriptions, $featureCode)
    {
        $bestResult = null;
        $bestRemaining = -1;

        foreach ($subscriptions as $subscription) {
            $result = $this->checkUsageLimit($subscription->id, $featureCode, 0);

            if ($result['allowed'] && isset($result['remaining'])) {
                $remaining = is_numeric($result['remaining']) ? $result['remaining'] : PHP_INT_MAX;

                if ($remaining > $bestRemaining) {
                    $bestRemaining = $remaining;
                    $bestResult = [
                        'subscription_id' => $subscription->id,
                        'subscription_status' => $subscription->status,
                        'plan_name' => $subscription->plan->name ?? 'Unknown',
                        'limit' => $result['limit'] ?? 'unknown',
                        'current' => $result['current'] ?? 0,
                        'remaining' => $result['remaining'] ?? 0,
                    ];
                }
            }
        }

        return [
            'best_available' => $bestResult,
            'message' => 'Consider upgrading your plan or adding more subscriptions',
        ];
    }

    /**
     * Check if usage is within limits for a specific subscription
     */
    public function checkUsageLimit($subscriptionId, $featureCode, $quantity = 1)
    {
        // Get subscription
        $subscription = Subscription::with('plan')->find($subscriptionId);
        if (! $subscription) {
            return [
                'allowed' => false,
                'message' => 'Subscription not found',
            ];
        }

        // Check if subscription is active or trialing
        if (!in_array($subscription->status, ['active', 'trialing'])) {
            return [
                'allowed' => false,
                'message' => "Subscription is {$subscription->status}, not active",
                'subscription_status' => $subscription->status,
            ];
        }

        // Get feature
        $feature = DB::table('features')->where('code', $featureCode)->first();
        if (! $feature) {
            return [
                'allowed' => false,
                'message' => 'Feature not found',
            ];
        }

        // Get plan feature limit
        $planFeature = DB::table('plan_features')
            ->where('plan_id', $subscription->plan_id)
            ->where('feature_id', $feature->id)
            ->first();

        if (! $planFeature) {
            return [
                'allowed' => false,
                'message' => 'Feature not available in your plan',
                'requires_upgrade' => true,
            ];
        }

        // Check if feature is boolean (true/false)
        if ($planFeature->value === 'false' || $planFeature->value === '0') {
            return [
                'allowed' => false,
                'message' => 'Feature not enabled in your plan',
                'requires_upgrade' => true,
            ];
        }

        // Unlimited usage
        if ($planFeature->value === 'unlimited') {
            return [
                'allowed' => true,
                'message' => 'Unlimited usage',
                'limit' => 'unlimited',
                'current' => 0,
                'remaining' => 'unlimited',
            ];
        }

        // Numeric limit
        if (is_numeric($planFeature->value)) {
            $limit = (float) $planFeature->value;

            // Get current period usage
            $currentUsage = $this->getCurrentPeriodUsage($subscriptionId, $feature->id);

            // Check if adding quantity would exceed limit
            if (($currentUsage + $quantity) > $limit) {
                return [
                    'allowed' => false,
                    'message' => "You have reached your limit of {$limit} {$featureCode}",
                    'limit' => $limit,
                    'current' => $currentUsage,
                    'attempted' => $currentUsage + $quantity,
                    'remaining' => max(0, $limit - $currentUsage),
                ];
            }

            return [
                'allowed' => true,
                'message' => 'Within limits',
                'limit' => $limit,
                'current' => $currentUsage,
                'remaining' => $limit - $currentUsage,
                'percentage' => round(($currentUsage / $limit) * 100, 2),
            ];
        }

        return [
            'allowed' => true,
            'message' => 'Usage allowed',
        ];
    }

    /**
     * Get current period usage for a feature
     */
    public function getCurrentPeriodUsage($subscriptionId, $featureId)
    {
        $subscription = Subscription::find($subscriptionId);

        if (! $subscription) {
            return 0;
        }

        // Determine billing period based on plan
        $period = $subscription->plan->billing_period ?? 'monthly';

        $startDate = match ($period) {
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            'quarterly' => now()->startOfQuarter(),
            'weekly' => now()->startOfWeek(),
            default => now()->startOfMonth()
        };

        return UsageRecord::where('subscription_id', $subscriptionId)
            ->where('feature_id', $featureId)
            ->where('billing_date', '>=', $startDate->toDateString())
            ->sum('quantity');
    }

    /**
     * Update metered usage aggregates
     */
    public function updateMeteredAggregates($subscriptionId, $featureId, $quantity)
    {
        $today = now()->toDateString();
        $month = now()->format('Y-m');

        // Update daily aggregate
        MeteredUsageAggregate::updateOrCreate(
            [
                'subscription_id' => $subscriptionId,
                'feature_id' => $featureId,
                'aggregate_date' => $today,
                'aggregate_period' => 'daily',
            ],
            [
                'total_quantity' => DB::raw("total_quantity + {$quantity}"),
                'record_count' => DB::raw('record_count + 1'),
                'last_calculated_at' => now(),
            ]
        );

        // Update monthly aggregate
        MeteredUsageAggregate::updateOrCreate(
            [
                'subscription_id' => $subscriptionId,
                'feature_id' => $featureId,
                'aggregate_date' => $month.'-01',
                'aggregate_period' => 'monthly',
            ],
            [
                'total_quantity' => DB::raw("total_quantity + {$quantity}"),
                'record_count' => DB::raw('record_count + 1'),
                'last_calculated_at' => now(),
            ]
        );
    }

    /**
     * Update rate limit
     */
    public function updateRateLimit($subscriptionId, $featureId, $quantity)
    {
        $key = "subscription:{$subscriptionId}:feature:{$featureId}";

        $rateLimit = RateLimit::where('subscription_id', $subscriptionId)
            ->where('feature_id', $featureId)
            ->where('key', $key)
            ->first();

        if ($rateLimit) {
            $rateLimit->decrement('remaining', $quantity);
            $rateLimit->updated_at = now();
            $rateLimit->save();
        }
    }

    /**
     * Initialize rate limits for a subscription
     */
    public function initializeRateLimits($subscriptionId)
    {
        $subscription = Subscription::with('plan')->find($subscriptionId);
        if (! $subscription) {
            return false;
        }

        // Get all features for this plan
        $planFeatures = DB::table('plan_features')
            ->where('plan_id', $subscription->plan_id)
            ->get();

        foreach ($planFeatures as $planFeature) {
            // Only create rate limits for numeric limits
            if (is_numeric($planFeature->value)) {
                $key = "subscription:{$subscriptionId}:feature:{$planFeature->feature_id}";

                // Determine decay seconds based on plan billing period
                $decaySeconds = match ($subscription->plan->billing_period ?? 'monthly') {
                    'monthly' => 30 * 24 * 60 * 60,
                    'yearly' => 365 * 24 * 60 * 60,
                    'quarterly' => 90 * 24 * 60 * 60,
                    'weekly' => 7 * 24 * 60 * 60,
                    default => 30 * 24 * 60 * 60
                };

                RateLimit::updateOrCreate(
                    [
                        'subscription_id' => $subscriptionId,
                        'feature_id' => $planFeature->feature_id,
                        'key' => $key,
                    ],
                    [
                        'max_attempts' => (int) $planFeature->value,
                        'decay_seconds' => $decaySeconds,
                        'remaining' => (int) $planFeature->value,
                        'resets_at' => now()->addSeconds($decaySeconds),
                    ]
                );
            }
        }

        return true;
    }

    /**
     * Get usage summary for a feature
     */
    public function getUsageSummary($subscriptionId, $featureCode)
    {
        $feature = DB::table('features')->where('code', $featureCode)->first();
        if (! $feature) {
            return null;
        }

        $subscription = Subscription::with('plan')->find($subscriptionId);
        if (! $subscription) {
            return null;
        }

        $planFeature = DB::table('plan_features')
            ->where('plan_id', $subscription->plan_id)
            ->where('feature_id', $feature->id)
            ->first();

        $currentUsage = $this->getCurrentPeriodUsage($subscriptionId, $feature->id);

        $limit = $planFeature->value ?? 0;
        $isUnlimited = $limit === 'unlimited';

        return [
            'feature' => $featureCode,
            'feature_name' => $feature->name,
            'current_usage' => $currentUsage,
            'limit' => $isUnlimited ? 'unlimited' : (float) $limit,
            'is_unlimited' => $isUnlimited,
            'percentage' => $isUnlimited ? 0 : round(($currentUsage / max(1, (float) $limit)) * 100, 2),
            'remaining' => $isUnlimited ? 'unlimited' : max(0, (float) $limit - $currentUsage),
            'period' => $subscription->plan->billing_period ?? 'monthly',
        ];
    }

    /**
     * Get all usage summaries for all active subscriptions of a user
     */
    public function getAllUsageSummaries($userId = null, $subscriptionId = null)
    {
        if ($subscriptionId) {
            $subscriptions = Subscription::with('plan')->where('id', $subscriptionId)->get();
        } elseif ($userId) {
            $subscriptions = Subscription::with('plan')
                ->where('user_id', $userId)
                ->whereIn('status', ['active', 'trialing'])
                ->get();
        } else {
            return [];
        }

        if ($subscriptions->isEmpty()) {
            return [];
        }

        $allSummaries = [];

        foreach ($subscriptions as $subscription) {
            $planFeatures = DB::table('plan_features')
                ->join('features', 'plan_features.feature_id', '=', 'features.id')
                ->where('plan_features.plan_id', $subscription->plan_id)
                ->select('features.*', 'plan_features.value as limit_value')
                ->get();

            $summaries = [];

            foreach ($planFeatures as $feature) {
                $currentUsage = $this->getCurrentPeriodUsage($subscription->id, $feature->id);

                $summaries[] = [
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->status,
                    'plan_name' => $subscription->plan->name ?? 'Unknown',
                    'feature_code' => $feature->code,
                    'feature_name' => $feature->name,
                    'current_usage' => $currentUsage,
                    'limit' => $feature->limit_value,
                    'is_unlimited' => $feature->limit_value === 'unlimited',
                    'percentage' => $feature->limit_value !== 'unlimited' && is_numeric($feature->limit_value)
                        ? round(($currentUsage / max(1, (float) $feature->limit_value)) * 100, 2)
                        : 0,
                    'remaining' => $feature->limit_value === 'unlimited'
                        ? 'unlimited'
                        : (is_numeric($feature->limit_value) ? max(0, (float) $feature->limit_value - $currentUsage) : 0),
                ];
            }

            $allSummaries = array_merge($allSummaries, $summaries);
        }

        return $allSummaries;
    }

    /**
     * Reset usage for a subscription (called on renewal)
     */
    public function resetUsage($subscriptionId)
    {
        DB::beginTransaction();

        try {
            // Archive current usage to metered aggregates
            $usages = UsageRecord::where('subscription_id', $subscriptionId)
                ->where('status', 'pending')
                ->get();

            foreach ($usages as $usage) {
                $this->updateMeteredAggregates(
                    $subscriptionId,
                    $usage->feature_id,
                    $usage->quantity
                );

                $usage->update(['status' => 'billed']);
            }

            // Reset rate limits
            RateLimit::where('subscription_id', $subscriptionId)
                ->update([
                    'remaining' => DB::raw('max_attempts'),
                    'resets_at' => now()->addSeconds(DB::raw('decay_seconds')),
                ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Usage reset failed: '.$e->getMessage());

            return false;
        }
    }
}
