<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMaster;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\RateLimit;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\SubscriptionItem;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionOrderItem;
use App\Models\UsageRecord;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected $paymentService;

    protected $invoiceService;

    public function __construct(
        PaymentService $paymentService,
        InvoiceService $invoiceService
    ) {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Create a new subscription
     */
    public function createSubscription(array $data): Subscription
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($data['user_id']);
            $plan = Plan::with(['prices', 'planFeatures'])->findOrFail($data['plan_id']);
            $price = PlanPrice::findOrFail($data['price_id']);

            // Calculate dates
            $now = Carbon::now();
            $trialEndsAt = null;
            $currentPeriodStartsAt = $now;
            $currentPeriodEndsAt = $this->calculatePeriodEnd($now, $price->interval, $price->interval_count);

            // Check for trial
            if (isset($data['trial_days']) && $data['trial_days'] > 0 && ! $user->has_used_trial) {
                $trialEndsAt = $now->copy()->addDays($data['trial_days']);
                $currentPeriodStartsAt = $trialEndsAt;
                $currentPeriodEndsAt = $this->calculatePeriodEnd($trialEndsAt, $price->interval, $price->interval_count);
            }

            // Calculate amount (consider quantity if applicable)
            $quantity = $data['quantity'] ?? 1;
            $amount = $price->amount * $quantity;

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_price_id' => $price->id,
                'status' => $trialEndsAt ? 'trialing' : 'active',
                'quantity' => $quantity,
                'unit_price' => $price->amount,
                'amount' => $amount,
                'currency' => $price->currency,
                'trial_starts_at' => $trialEndsAt ? $now : null,
                'trial_ends_at' => $trialEndsAt,
                'trial_converted' => false,
                'current_period_starts_at' => $currentPeriodStartsAt,
                'current_period_ends_at' => $currentPeriodEndsAt,
                'billing_cycle_anchor_date' => $now,
                'gateway' => $data['gateway'] ?? 'stripe',
                'created_by' => $user->id,
                'metadata' => json_encode($data['metadata'] ?? []),
            ]);

            // Create subscription items for each feature
            foreach ($plan->planFeatures as $feature) {
                SubscriptionItem::create([
                    'subscription_id' => $subscription->id,
                    'plan_price_id' => $price->id,
                    'feature_id' => $feature->feature_id,
                    'quantity' => $feature->value === 'unlimited' ? -1 : (is_numeric($feature->value) ? $feature->value : 1),
                    'unit_price' => 0, // Features are included in plan price
                    'amount' => 0,
                    'metadata' => json_encode(['value' => $feature->value]),
                    'effective_from' => $now,
                ]);
            }

            // Log event
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'type' => $trialEndsAt ? 'trial_started' : 'created',
                'data' => json_encode([
                    'plan' => $plan->name,
                    'amount' => $amount,
                    'trial_end' => $trialEndsAt,
                ]),
                'occurred_at' => $now,
                'created_by' => $user->id,
            ]);

            // Update user trial status
            if ($trialEndsAt) {
                $user->update([
                    'has_used_trial' => true,
                    'trial_ends_at' => $trialEndsAt,
                ]);
            }

            DB::commit();

            return $subscription->load(['plan', 'user', 'items.feature']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create subscription: '.$e->getMessage());
            throw new Exception('Failed to create subscription: '.$e->getMessage());
        }
    }

    /**
     * Create subscription order
     */
    public function createOrder(array $data): SubscriptionOrder
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($data['user_id']);
            $plan = Plan::findOrFail($data['plan_id']);
            $price = PlanPrice::findOrFail($data['price_id']);

            // Calculate amounts
            $quantity = $data['quantity'] ?? 1;
            $subtotal = $price->amount * $quantity;
            $taxAmount = $subtotal * ($data['tax_rate'] ?? 0.1); // 10% default tax
            $discountAmount = $data['discount_amount'] ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Create order
            $order = SubscriptionOrder::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'type' => $data['type'] ?? 'new',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'currency' => $price->currency,
                'customer_info' => json_encode([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]),
                'billing_address' => json_encode($data['billing_address'] ?? $user->billing_address),
                'coupon_code' => $data['coupon_code'] ?? null,
                'applied_discounts' => json_encode($data['discounts'] ?? []),
                'metadata' => json_encode($data['metadata'] ?? []),
                'created_by' => $user->id,
            ]);

            // Create order item
            SubscriptionOrderItem::create([
                'subscription_order_id' => $order->id,
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'plan_name' => $plan->name,
                'billing_cycle' => $price->interval,
                'quantity' => $quantity,
                'recipient_user_id' => $data['recipient_user_id'] ?? $user->id,
                'unit_price' => $price->amount,
                'amount' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'start_date' => Carbon::now(),
                'subscription_status' => 'pending',
            ]);

            DB::commit();

            return $order->load('items');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order: '.$e->getMessage());
            throw new Exception('Failed to create order: '.$e->getMessage());
        }
    }

    /**
     * Process payment and activate subscription
     */
    public function processPaymentAndActivate(array $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($data['user_id']);
            $order = SubscriptionOrder::findOrFail($data['order_id']);
            $paymentMethod = $data['payment_method'];

            // Create payment master
            $paymentMaster = PaymentMaster::create([
                'user_id' => $user->id,
                'payment_number' => $this->generatePaymentNumber(),
                'type' => 'subscription',
                'status' => 'pending',
                'total_amount' => $order->total_amount,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'currency' => $order->currency,
                'payment_method' => $paymentMethod,
                'payment_gateway' => $data['gateway'],
                'metadata' => json_encode(['order_id' => $order->id]),
            ]);

            // Process payment through gateway
            $paymentResult = $this->paymentService->processPayment([
                'payment_master_id' => $paymentMaster->id,
                'amount' => $order->total_amount,
                'currency' => $order->currency,
                'gateway' => $data['gateway'],
                'payment_method' => $paymentMethod,
                'payment_details' => $data['payment_details'] ?? [],
            ]);

            if ($paymentResult['success']) {
                // Create subscription
                $subscription = $this->createSubscription([
                    'user_id' => $user->id,
                    'plan_id' => $order->items->first()->plan_id,
                    'price_id' => $data['price_id'],
                    'quantity' => $order->items->first()->quantity,
                    'gateway' => $data['gateway'],
                    'metadata' => ['order_id' => $order->id],
                ]);

                // Update order
                $order->update([
                    'status' => 'completed',
                    'payment_master_id' => $paymentMaster->id,
                    'processed_at' => Carbon::now(),
                ]);

                // Update order item
                $order->items()->update([
                    'subscription_id' => $subscription->id,
                    'subscription_status' => 'created',
                    'processed_at' => Carbon::now(),
                ]);

                // Create invoice
                $invoice = $this->invoiceService->createInvoice([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'currency' => $order->currency,
                    'items' => [
                        [
                            'description' => $order->items->first()->plan_name.' Subscription',
                            'amount' => $order->subtotal,
                            'quantity' => $order->items->first()->quantity,
                        ],
                    ],
                ]);

                DB::commit();

                return [
                    'success' => true,
                    'subscription' => $subscription,
                    'invoice' => $invoice,
                    'payment' => $paymentResult,
                ];
            } else {
                throw new Exception('Payment failed: '.($paymentResult['message'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment: '.$e->getMessage());
            throw new Exception('Failed to process payment: '.$e->getMessage());
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(int $subscriptionId, string $reason = 'customer'): bool
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::findOrFail($subscriptionId);

            if (! in_array($subscription->status, ['active', 'trialing'])) {
                throw new Exception('Subscription cannot be cancelled in its current state');
            }

            $oldStatus = $subscription->status;

            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => Carbon::now(),
                'cancellation_reason' => $reason,
            ]);

            // Log event
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'type' => 'canceled',
                'changes' => json_encode(['status' => [$oldStatus, 'canceled']]),
                'data' => json_encode(['reason' => $reason]),
                'occurred_at' => Carbon::now(),
                'created_by' => $subscription->user_id,
            ]);

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel subscription: '.$e->getMessage());
            throw new Exception('Failed to cancel subscription: '.$e->getMessage());
        }
    }

    /**
     * Process refund
     */
    public function processRefund(array $data): array
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::findOrFail($data['subscription_id']);
            $payment = Payment::findOrFail($data['payment_id']);

            // Create refund record
            $refund = \App\Models\Refund::create([
                'payment_master_id' => $payment->payment_master_id,
                'payment_transaction_id' => $payment->id,
                'user_id' => $subscription->user_id,
                'refund_number' => $this->generateRefundNumber(),
                'type' => $data['type'] ?? 'full',
                'status' => 'requested',
                'initiated_by' => $data['initiated_by'] ?? 'customer',
                'amount' => $data['amount'],
                'currency' => $subscription->currency,
                'reason' => $data['reason'],
                'reason_details' => $data['reason_details'] ?? null,
                'customer_comments' => $data['comments'] ?? null,
                'metadata' => json_encode(['subscription_id' => $subscription->id]),
            ]);

            // Process refund through gateway
            $refundResult = $this->paymentService->processRefund([
                'payment_id' => $payment->id,
                'amount' => $data['amount'],
                'refund_id' => $refund->id,
            ]);

            if ($refundResult['success']) {
                $refund->update([
                    'status' => 'completed',
                    'processed_at' => Carbon::now(),
                    'gateway_refund_id' => $refundResult['gateway_refund_id'],
                    'gateway_response' => json_encode($refundResult['response']),
                ]);

                // Cancel subscription if full refund
                if ($data['type'] === 'full') {
                    $this->cancelSubscription($subscription->id, 'refunded');
                }

                // Log event
                SubscriptionEvent::create([
                    'subscription_id' => $subscription->id,
                    'type' => 'refunded',
                    'data' => json_encode([
                        'amount' => $data['amount'],
                        'reason' => $data['reason'],
                    ]),
                    'occurred_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'refund' => $refund,
                'gateway_response' => $refundResult,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process refund: '.$e->getMessage());
            throw new Exception('Failed to process refund: '.$e->getMessage());
        }
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(int $subscriptionId): Subscription
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::with(['plan', 'price'])->findOrFail($subscriptionId);

            if ($subscription->status !== 'active') {
                throw new Exception('Only active subscriptions can be renewed');
            }

            $now = Carbon::now();
            $newPeriodEnd = $this->calculatePeriodEnd(
                $subscription->current_period_ends_at,
                $subscription->price->interval,
                $subscription->price->interval_count
            );

            // Create invoice for renewal
            $invoice = $this->invoiceService->createInvoice([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'type' => 'subscription',
                'amount' => $subscription->amount,
                'currency' => $subscription->currency,
                'items' => [
                    [
                        'description' => $subscription->plan->name.' - Renewal',
                        'amount' => $subscription->amount,
                        'quantity' => 1,
                    ],
                ],
            ]);

            // Process payment
            $payment = $this->paymentService->processRecurringPayment([
                'subscription_id' => $subscription->id,
                'amount' => $subscription->amount,
                'invoice_id' => $invoice->id,
            ]);

            if ($payment['success']) {
                // Update subscription period
                $subscription->update([
                    'current_period_starts_at' => $subscription->current_period_ends_at,
                    'current_period_ends_at' => $newPeriodEnd,
                ]);

                // Log event
                SubscriptionEvent::create([
                    'subscription_id' => $subscription->id,
                    'type' => 'renewed',
                    'data' => json_encode([
                        'new_period_end' => $newPeriodEnd,
                        'invoice_id' => $invoice->id,
                    ]),
                    'occurred_at' => $now,
                ]);
            } else {
                // Mark as past_due if payment fails
                $subscription->update(['status' => 'past_due']);

                SubscriptionEvent::create([
                    'subscription_id' => $subscription->id,
                    'type' => 'payment_failed',
                    'data' => json_encode(['invoice_id' => $invoice->id]),
                    'occurred_at' => $now,
                ]);
            }

            DB::commit();

            return $subscription->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to renew subscription: '.$e->getMessage());
            throw new Exception('Failed to renew subscription: '.$e->getMessage());
        }
    }

    /**
     * Record usage for metered billing
     */
    public function recordUsage(array $data): UsageRecord
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::with(['items' => function ($q) use ($data) {
                $q->where('feature_id', $data['feature_id']);
            }])->findOrFail($data['subscription_id']);

            $subscriptionItem = $subscription->items->first();

            if (! $subscriptionItem) {
                throw new Exception('Feature not found in subscription');
            }

            // Check rate limit
            $canUse = $this->checkRateLimit($subscription->id, $data['feature_id']);
            if (! $canUse) {
                throw new Exception('Rate limit exceeded for this feature');
            }

            // Record usage
            $usage = UsageRecord::create([
                'subscription_id' => $subscription->id,
                'subscription_item_id' => $subscriptionItem->id,
                'feature_id' => $data['feature_id'],
                'quantity' => $data['quantity'],
                'unit' => $data['unit'] ?? 'count',
                'status' => 'pending',
                'recorded_at' => Carbon::now(),
                'billing_date' => Carbon::now(),
                'metadata' => json_encode($data['metadata'] ?? []),
            ]);

            // Update rate limit
            $this->decrementRateLimit($subscription->id, $data['feature_id']);

            // Update aggregate
            $this->updateUsageAggregates($subscription->id, $data['feature_id'], $data['quantity']);

            DB::commit();

            return $usage;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to record usage: '.$e->getMessage());
            throw new Exception('Failed to record usage: '.$e->getMessage());
        }
    }

    /**
     * Get user subscriptions with details
     */
    public function getUserSubscriptions(int $userId): array
    {
        $subscriptions = Subscription::with([
            'plan',
            'price',
            'items.feature',
            'events' => function ($q) {
                $q->latest()->limit(5);
            },
            'invoices' => function ($q) {
                $q->latest()->limit(3);
            },
        ])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $activeSubscription = $subscriptions->whereIn('status', ['active', 'trialing'])->first();
        $pastSubscriptions = $subscriptions->whereIn('status', ['canceled', 'expired']);

        return [
            'active' => $activeSubscription,
            'all' => $subscriptions,
            'past' => $pastSubscriptions,
            'has_active' => ! is_null($activeSubscription),
        ];
    }

    /**
     * Calculate period end date
     */
    protected function calculatePeriodEnd(Carbon $startDate, string $interval, int $intervalCount): Carbon
    {
        switch ($interval) {
            case 'day':
                return $startDate->copy()->addDays($intervalCount);
            case 'week':
                return $startDate->copy()->addWeeks($intervalCount);
            case 'month':
                return $startDate->copy()->addMonths($intervalCount);
            case 'quarter':
                return $startDate->copy()->addMonths(3 * $intervalCount);
            case 'year':
                return $startDate->copy()->addYears($intervalCount);
            default:
                return $startDate->copy()->addMonth();
        }
    }

    /**
     * Check rate limit
     */
    protected function checkRateLimit(int $subscriptionId, int $featureId): bool
    {
        $rateLimit = RateLimit::where('subscription_id', $subscriptionId)
            ->where('feature_id', $featureId)
            ->first();

        if (! $rateLimit) {
            return true; // No rate limit configured
        }

        return $rateLimit->remaining > 0 && $rateLimit->resets_at > Carbon::now();
    }

    /**
     * Decrement rate limit
     */
    protected function decrementRateLimit(int $subscriptionId, int $featureId): void
    {
        $rateLimit = RateLimit::where('subscription_id', $subscriptionId)
            ->where('feature_id', $featureId)
            ->first();

        if ($rateLimit) {
            $rateLimit->decrement('remaining');
        }
    }

    /**
     * Update usage aggregates
     */
    protected function updateUsageAggregates(int $subscriptionId, int $featureId, float $quantity): void
    {
        $today = Carbon::now()->format('Y-m-d');
        $month = Carbon::now()->format('Y-m-01');

        // Update daily aggregate
        $dailyAggregate = \App\Models\MeteredUsageAggregate::firstOrNew([
            'subscription_id' => $subscriptionId,
            'feature_id' => $featureId,
            'aggregate_date' => $today,
            'aggregate_period' => 'daily',
        ]);

        $dailyAggregate->total_quantity = ($dailyAggregate->total_quantity ?? 0) + $quantity;
        $dailyAggregate->record_count = ($dailyAggregate->record_count ?? 0) + 1;
        $dailyAggregate->last_calculated_at = Carbon::now();
        $dailyAggregate->save();

        // Update monthly aggregate
        $monthlyAggregate = \App\Models\MeteredUsageAggregate::firstOrNew([
            'subscription_id' => $subscriptionId,
            'feature_id' => $featureId,
            'aggregate_date' => $month,
            'aggregate_period' => 'monthly',
        ]);

        $monthlyAggregate->total_quantity = ($monthlyAggregate->total_quantity ?? 0) + $quantity;
        $monthlyAggregate->record_count = ($monthlyAggregate->record_count ?? 0) + 1;
        $monthlyAggregate->last_calculated_at = Carbon::now();
        $monthlyAggregate->save();
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        return $prefix.'-'.$date.'-'.$random;
    }

    /**
     * Generate payment number
     */
    protected function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        return $prefix.'-'.$date.'-'.$random;
    }

    /**
     * Generate refund number
     */
    protected function generateRefundNumber(): string
    {
        $prefix = 'REF';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        return $prefix.'-'.$date.'-'.$random;
    }
}
