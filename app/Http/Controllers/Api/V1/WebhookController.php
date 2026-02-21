<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhookLog;
use App\Models\Subscription;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paymentService;

    protected $subscriptionService;

    protected $invoiceService;

    public function __construct(
        PaymentService $paymentService,
        SubscriptionService $subscriptionService,
        InvoiceService $invoiceService
    ) {
        $this->paymentService = $paymentService;
        $this->subscriptionService = $subscriptionService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle Stripe webhooks
     */
    public function handleStripe(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('Stripe-Signature');

        try {
            // Log webhook
            $webhookLog = PaymentWebhookLog::create([
                'gateway' => 'stripe',
                'event_type' => $payload['type'] ?? 'unknown',
                'webhook_id' => $payload['id'] ?? null,
                'payload' => json_encode($payload),
                'headers' => json_encode($request->headers->all()),
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Verify webhook signature (implement based on your config)
            // $this->verifyStripeWebhook($payload, $signature);

            // Process based on event type
            switch ($payload['type']) {
                case 'invoice.payment_succeeded':
                    $this->handleStripeInvoicePaymentSucceeded($payload['data']['object']);
                    break;

                case 'invoice.payment_failed':
                    $this->handleStripeInvoicePaymentFailed($payload['data']['object']);
                    break;

                case 'customer.subscription.created':
                    $this->handleStripeSubscriptionCreated($payload['data']['object']);
                    break;

                case 'customer.subscription.updated':
                    $this->handleStripeSubscriptionUpdated($payload['data']['object']);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleStripeSubscriptionDeleted($payload['data']['object']);
                    break;

                case 'payment_intent.succeeded':
                    $this->handleStripePaymentIntentSucceeded($payload['data']['object']);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handleStripePaymentIntentFailed($payload['data']['object']);
                    break;

                case 'charge.refunded':
                    $this->handleStripeChargeRefunded($payload['data']['object']);
                    break;

                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $payload['type']]);
            }

            $webhookLog->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error: '.$e->getMessage(), [
                'payload' => $payload,
            ]);

            if (isset($webhookLog)) {
                $webhookLog->update([
                    'status' => 'failed',
                    'processing_error' => $e->getMessage(),
                ]);
            }

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle PayPal webhooks
     */
    public function handlePayPal(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        try {
            $webhookLog = PaymentWebhookLog::create([
                'gateway' => 'paypal',
                'event_type' => $payload['event_type'] ?? 'unknown',
                'webhook_id' => $payload['id'] ?? null,
                'payload' => json_encode($payload),
                'headers' => json_encode($headers),
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Verify PayPal webhook
            // $this->verifyPayPalWebhook($request);

            $resource = $payload['resource'] ?? [];

            switch ($payload['event_type']) {
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handlePayPalSaleCompleted($resource);
                    break;

                case 'PAYMENT.SALE.REFUNDED':
                    $this->handlePayPalSaleRefunded($resource);
                    break;

                case 'BILLING.SUBSCRIPTION.CREATED':
                    $this->handlePayPalSubscriptionCreated($resource);
                    break;

                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    $this->handlePayPalSubscriptionCancelled($resource);
                    break;

                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    $this->handlePayPalSubscriptionSuspended($resource);
                    break;

                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handlePayPalSubscriptionActivated($resource);
                    break;

                default:
                    Log::info('Unhandled PayPal webhook event', ['type' => $payload['event_type']]);
            }

            $webhookLog->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            Log::error('PayPal webhook error: '.$e->getMessage(), [
                'payload' => $payload,
            ]);

            if (isset($webhookLog)) {
                $webhookLog->update([
                    'status' => 'failed',
                    'processing_error' => $e->getMessage(),
                ]);
            }

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle bKash webhooks
     */
    public function handleBkash(Request $request)
    {
        $payload = $request->all();

        try {
            $webhookLog = PaymentWebhookLog::create([
                'gateway' => 'bkash',
                'event_type' => $payload['type'] ?? 'payment_status_change',
                'webhook_id' => $payload['trxID'] ?? null,
                'payload' => json_encode($payload),
                'headers' => json_encode($request->headers->all()),
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Verify bKash webhook signature
            // $this->verifyBkashWebhook($request);

            if (isset($payload['trxID']) && isset($payload['paymentID'])) {
                $transaction = PaymentTransaction::where('transaction_id', $payload['paymentID'])->first();

                if ($transaction) {
                    $status = $payload['transactionStatus'] ?? 'completed';

                    $transaction->update([
                        'status' => $status === 'Completed' ? 'completed' : 'pending',
                        'completed_at' => $status === 'Completed' ? now() : null,
                        'gateway_response' => json_encode($payload),
                    ]);

                    if ($status === 'Completed') {
                        // Update payment master
                        $paymentMaster = $transaction->paymentMaster;
                        if ($paymentMaster) {
                            $paymentMaster->update([
                                'status' => 'paid',
                                'paid_amount' => $paymentMaster->total_amount,
                                'paid_at' => now(),
                            ]);
                        }

                        // Update invoice if exists
                        if ($paymentMaster && $paymentMaster->metadata) {
                            $metadata = json_decode($paymentMaster->metadata, true);
                            if (isset($metadata['order_id'])) {
                                // Create subscription from order
                                $this->activateSubscriptionFromOrder($metadata['order_id'], $paymentMaster->id);
                            }
                        }
                    }
                }
            }

            $webhookLog->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            Log::error('bKash webhook error: '.$e->getMessage());

            if (isset($webhookLog)) {
                $webhookLog->update([
                    'status' => 'failed',
                    'processing_error' => $e->getMessage(),
                ]);
            }

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle SurjoPay webhooks
     */
    public function handleSurjoPay(Request $request)
    {
        $payload = $request->all();

        try {
            $webhookLog = PaymentWebhookLog::create([
                'gateway' => 'surjopay',
                'event_type' => $payload['event'] ?? 'payment_update',
                'webhook_id' => $payload['transaction_id'] ?? null,
                'payload' => json_encode($payload),
                'headers' => json_encode($request->headers->all()),
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Verify SurjoPay webhook signature
            // $this->verifySurjoPayWebhook($request);

            if (isset($payload['transaction_id']) && isset($payload['status'])) {
                $transaction = PaymentTransaction::where('transaction_id', $payload['transaction_id'])->first();

                if ($transaction) {
                    $status = $payload['status'];

                    $transaction->update([
                        'status' => $status === 'COMPLETED' ? 'completed' : 'failed',
                        'completed_at' => $status === 'COMPLETED' ? now() : null,
                        'gateway_response' => json_encode($payload),
                        'failure_reason' => $payload['message'] ?? null,
                    ]);

                    if ($status === 'COMPLETED') {
                        $paymentMaster = $transaction->paymentMaster;
                        if ($paymentMaster) {
                            $paymentMaster->update([
                                'status' => 'paid',
                                'paid_amount' => $paymentMaster->total_amount,
                                'paid_at' => now(),
                            ]);

                            // Activate subscription
                            if ($paymentMaster->metadata) {
                                $metadata = json_decode($paymentMaster->metadata, true);
                                if (isset($metadata['order_id'])) {
                                    $this->activateSubscriptionFromOrder($metadata['order_id'], $paymentMaster->id);
                                }
                            }
                        }
                    }
                }
            }

            $webhookLog->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            Log::error('SurjoPay webhook error: '.$e->getMessage());

            if (isset($webhookLog)) {
                $webhookLog->update([
                    'status' => 'failed',
                    'processing_error' => $e->getMessage(),
                ]);
            }

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle Stripe invoice payment succeeded
     */
    protected function handleStripeInvoicePaymentSucceeded($invoiceData)
    {
        DB::transaction(function () use ($invoiceData) {
            // Find or create invoice
            $invoice = Invoice::where('external_id', $invoiceData['id'])->first();

            if (! $invoice) {
                // Find subscription
                $subscription = Subscription::where('gateway_subscription_id', $invoiceData['subscription'] ?? null)->first();

                if ($subscription) {
                    $invoice = $this->invoiceService->createInvoice([
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'external_id' => $invoiceData['id'],
                        'type' => 'subscription',
                        'amount' => $invoiceData['amount_paid'] / 100,
                        'currency' => strtoupper($invoiceData['currency']),
                        'items' => [
                            [
                                'description' => $subscription->plan->name.' - '.date('M Y'),
                                'amount' => $invoiceData['amount_paid'] / 100,
                                'quantity' => 1,
                            ],
                        ],
                        'metadata' => ['stripe_invoice' => $invoiceData],
                    ]);
                }
            }

            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'amount_paid' => $invoiceData['amount_paid'] / 100,
                    'paid_at' => now(),
                ]);

                // Find and update payment transaction
                $chargeId = $invoiceData['charge'] ?? null;
                if ($chargeId) {
                    $transaction = PaymentTransaction::where('transaction_id', $chargeId)->first();
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    }
                }

                // Update subscription period if needed
                if ($invoice->subscription) {
                    $subscription = $invoice->subscription;
                    $subscription->update([
                        'current_period_starts_at' => now(),
                        'current_period_ends_at' => now()->addMonth(),
                        'status' => 'active',
                    ]);
                }
            }
        });
    }

    /**
     * Handle Stripe invoice payment failed
     */
    protected function handleStripeInvoicePaymentFailed($invoiceData)
    {
        DB::transaction(function () use ($invoiceData) {
            $invoice = Invoice::where('external_id', $invoiceData['id'])->first();

            if ($invoice) {
                $invoice->update([
                    'status' => 'uncollectible',
                ]);

                // Update subscription status
                if ($invoice->subscription) {
                    $subscription = $invoice->subscription;
                    $subscription->update([
                        'status' => 'past_due',
                    ]);

                    // Log event
                    $subscription->events()->create([
                        'type' => 'payment_failed',
                        'data' => json_encode(['invoice_id' => $invoice->id]),
                        'occurred_at' => now(),
                    ]);
                }
            }
        });
    }

    /**
     * Handle Stripe subscription created
     */
    protected function handleStripeSubscriptionCreated($subscriptionData)
    {
        // Find local subscription by gateway ID
        $subscription = Subscription::where('gateway_subscription_id', $subscriptionData['id'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => $subscriptionData['status'],
                'current_period_starts_at' => date('Y-m-d H:i:s', $subscriptionData['current_period_start']),
                'current_period_ends_at' => date('Y-m-d H:i:s', $subscriptionData['current_period_end']),
                'gateway_metadata' => json_encode($subscriptionData),
            ]);

            // Log event
            $subscription->events()->create([
                'type' => 'created',
                'data' => json_encode(['gateway' => 'stripe']),
                'occurred_at' => now(),
            ]);
        }
    }

    /**
     * Handle Stripe subscription updated
     */
    protected function handleStripeSubscriptionUpdated($subscriptionData)
    {
        $subscription = Subscription::where('gateway_subscription_id', $subscriptionData['id'])->first();

        if ($subscription) {
            $oldStatus = $subscription->status;

            $subscription->update([
                'status' => $subscriptionData['status'],
                'current_period_starts_at' => date('Y-m-d H:i:s', $subscriptionData['current_period_start']),
                'current_period_ends_at' => date('Y-m-d H:i:s', $subscriptionData['current_period_end']),
                'gateway_metadata' => json_encode($subscriptionData),
            ]);

            // Log event
            $subscription->events()->create([
                'type' => 'updated',
                'changes' => json_encode(['status' => [$oldStatus, $subscriptionData['status']]]),
                'data' => json_encode(['gateway' => 'stripe']),
                'occurred_at' => now(),
            ]);
        }
    }

    /**
     * Handle Stripe subscription deleted
     */
    protected function handleStripeSubscriptionDeleted($subscriptionData)
    {
        $subscription = Subscription::where('gateway_subscription_id', $subscriptionData['id'])->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            // Log event
            $subscription->events()->create([
                'type' => 'canceled',
                'data' => json_encode(['gateway' => 'stripe']),
                'occurred_at' => now(),
            ]);
        }
    }

    /**
     * Handle Stripe payment intent succeeded
     */
    protected function handleStripePaymentIntentSucceeded($paymentIntent)
    {
        $transaction = PaymentTransaction::where('transaction_id', $paymentIntent['id'])->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'gateway_response' => json_encode($paymentIntent),
            ]);

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            if ($paymentMaster && $paymentMaster->status !== 'paid') {
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $paymentMaster->total_amount,
                    'paid_at' => now(),
                ]);

                // Activate subscription from order
                if ($paymentMaster->metadata) {
                    $metadata = json_decode($paymentMaster->metadata, true);
                    if (isset($metadata['order_id'])) {
                        $this->activateSubscriptionFromOrder($metadata['order_id'], $paymentMaster->id);
                    }
                }
            }
        }
    }

    /**
     * Handle Stripe payment intent failed
     */
    protected function handleStripePaymentIntentFailed($paymentIntent)
    {
        $transaction = PaymentTransaction::where('transaction_id', $paymentIntent['id'])->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $paymentIntent['last_payment_error']['message'] ?? 'Payment failed',
                'gateway_response' => json_encode($paymentIntent),
            ]);

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            if ($paymentMaster) {
                $paymentMaster->update([
                    'status' => 'failed',
                ]);
            }
        }
    }

    /**
     * Handle Stripe charge refunded
     */
    protected function handleStripeChargeRefunded($charge)
    {
        $transaction = PaymentTransaction::where('transaction_id', $charge['id'])->first();

        if ($transaction) {
            $refundAmount = $charge['amount_refunded'] / 100;

            $transaction->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            // Create refund record
            \App\Models\Refund::create([
                'payment_master_id' => $transaction->payment_master_id,
                'payment_transaction_id' => $transaction->id,
                'user_id' => $transaction->paymentMaster->user_id,
                'refund_number' => 'REF-'.time(),
                'type' => $refundAmount == $transaction->amount ? 'full' : 'partial',
                'status' => 'completed',
                'amount' => $refundAmount,
                'currency' => strtoupper($charge['currency']),
                'reason' => 'refunded',
                'processed_at' => now(),
                'gateway_refund_id' => $charge['refunds']['data'][0]['id'] ?? null,
                'gateway_response' => json_encode($charge),
            ]);

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            if ($paymentMaster) {
                $paymentMaster->update([
                    'status' => 'refunded',
                ]);
            }
        }
    }

    /**
     * Handle PayPal sale completed
     */
    protected function handlePayPalSaleCompleted($sale)
    {
        $transaction = PaymentTransaction::where('transaction_id', $sale['id'])->first();

        if (! $transaction) {
            // Try to find by billing agreement ID
            $billingAgreementId = $sale['billing_agreement_id'] ?? null;
            if ($billingAgreementId) {
                $subscription = Subscription::where('gateway_subscription_id', $billingAgreementId)->first();
                if ($subscription) {
                    // Create payment transaction
                    $paymentMaster = PaymentMaster::create([
                        'user_id' => $subscription->user_id,
                        'payment_number' => 'PAY-'.time(),
                        'type' => 'subscription',
                        'status' => 'paid',
                        'total_amount' => $sale['amount']['total'],
                        'currency' => $sale['amount']['currency'],
                        'payment_method' => 'paypal',
                        'payment_gateway' => 'paypal',
                        'paid_at' => now(),
                    ]);

                    $transaction = PaymentTransaction::create([
                        'payment_master_id' => $paymentMaster->id,
                        'transaction_id' => $sale['id'],
                        'type' => 'payment',
                        'payment_method' => 'paypal',
                        'payment_gateway' => 'paypal',
                        'amount' => $sale['amount']['total'],
                        'currency' => $sale['amount']['currency'],
                        'status' => 'completed',
                        'completed_at' => now(),
                        'gateway_response' => json_encode($sale),
                    ]);

                    // Create invoice
                    $this->invoiceService->createInvoice([
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'type' => 'subscription',
                        'amount' => $sale['amount']['total'],
                        'currency' => $sale['amount']['currency'],
                        'items' => [
                            [
                                'description' => $subscription->plan->name.' - Renewal',
                                'amount' => $sale['amount']['total'],
                                'quantity' => 1,
                            ],
                        ],
                        'metadata' => ['paypal_sale' => $sale],
                    ]);
                }
            }
        } else {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'gateway_response' => json_encode($sale),
            ]);
        }
    }

    /**
     * Handle PayPal sale refunded
     */
    protected function handlePayPalSaleRefunded($sale)
    {
        $transaction = PaymentTransaction::where('transaction_id', $sale['id'])->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);
        }
    }

    /**
     * Handle PayPal subscription created
     */
    protected function handlePayPalSubscriptionCreated($subscription)
    {
        $localSubscription = Subscription::where('gateway_subscription_id', $subscription['id'])->first();

        if ($localSubscription) {
            $localSubscription->update([
                'status' => 'active',
                'gateway_metadata' => json_encode($subscription),
            ]);
        }
    }

    /**
     * Handle PayPal subscription cancelled
     */
    protected function handlePayPalSubscriptionCancelled($subscription)
    {
        $localSubscription = Subscription::where('gateway_subscription_id', $subscription['id'])->first();

        if ($localSubscription) {
            $localSubscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }
    }

    /**
     * Activate subscription from order
     */
    protected function activateSubscriptionFromOrder(int $orderId, int $paymentMasterId)
    {
        $order = \App\Models\SubscriptionOrder::with('items')->find($orderId);

        if ($order && $order->items->isNotEmpty()) {
            $orderItem = $order->items->first();

            // Create subscription
            $subscription = $this->subscriptionService->createSubscription([
                'user_id' => $order->user_id,
                'plan_id' => $orderItem->plan_id,
                'price_id' => $orderItem->plan_price_id ?? null,
                'quantity' => $orderItem->quantity,
                'gateway' => $order->payment_method ?? 'system',
                'metadata' => ['order_id' => $orderId],
            ]);

            // Update order item
            $orderItem->update([
                'subscription_id' => $subscription->id,
                'subscription_status' => 'created',
                'processed_at' => now(),
            ]);

            // Update order
            $order->update([
                'status' => 'completed',
                'payment_master_id' => $paymentMasterId,
                'processed_at' => now(),
            ]);

            // Create invoice
            $this->invoiceService->createInvoice([
                'user_id' => $order->user_id,
                'subscription_id' => $subscription->id,
                'order_id' => $orderId,
                'type' => 'subscription',
                'amount' => $orderItem->total_amount,
                'currency' => $order->currency,
                'items' => [
                    [
                        'description' => $orderItem->plan_name.' Subscription',
                        'amount' => $orderItem->amount,
                        'quantity' => $orderItem->quantity,
                    ],
                ],
                'tax_rates' => [['rate' => $orderItem->tax_amount / $orderItem->amount * 100]],
                'discounts' => $order->applied_discounts,
            ]);

            return $subscription;
        }

        return null;
    }
}
