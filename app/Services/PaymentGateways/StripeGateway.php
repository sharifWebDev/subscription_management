<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class StripeGateway
{
    protected $stripe;

    protected $config;

    protected $apiKey;

    protected $apiSecret;

    public function __construct()
    {
        $this->config = PaymentGateway::where('code', 'stripe')
            ->where('is_active', true)
            ->first();

        if (! $this->config) {
            throw new Exception('Stripe gateway not configured');
        }

        // Extract the API secret from the config
        $this->apiKey = $this->config->api_key;
        $this->apiSecret = $this->config->api_secret;

        if (! $this->apiSecret) {
            throw new Exception('Stripe API secret not configured');
        }

        // Initialize Stripe with just the API secret (string)
        Stripe::setApiKey($this->apiSecret);
        Stripe::setApiVersion('2023-10-16');
    }

    /**
     * Create payment intent
     */
    public function createPaymentIntent(array $data): array
    {
        try {
            $intentData = [
                'amount' => (int) ($data['amount'] * 100), // Convert to cents
                'currency' => strtolower($data['currency']),
                'payment_method_types' => ['card'],
                'metadata' => [
                    'payment_master_id' => (string) ($data['payment_master_id'] ?? ''),
                    'plan_id' => (string) ($data['plan_id'] ?? ''),
                    'user_id' => (string) ($data['user_id'] ?? ''),
                ],
            ];

            // Add customer if exists
            if (isset($data['customer_id'])) {
                $intentData['customer'] = $data['customer_id'];
            }

            // Add receipt email
            if (isset($data['email'])) {
                $intentData['receipt_email'] = $data['email'];
            }

            // Add description
            if (isset($data['description'])) {
                $intentData['description'] = $data['description'];
            }

            // Add setup future usage if saving card
            if (isset($data['setup_future_usage'])) {
                $intentData['setup_future_usage'] = $data['setup_future_usage'];
            }

            $intent = PaymentIntent::create($intentData);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id,
                'amount' => $intent->amount / 100,
                'currency' => $intent->currency,
                'status' => $intent->status,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe card error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Card error: '.$e->getError()->message,
                'code' => $e->getError()->code,
            ];
        } catch (\Stripe\Exception\RateLimitException $e) {
            Log::error('Stripe rate limit error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
            ];
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe invalid request: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Invalid request: '.$e->getMessage(),
            ];
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe authentication error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment gateway authentication failed',
            ];
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error('Stripe connection error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Network error. Please try again.',
            ];
        } catch (Exception $e) {
            Log::error('Stripe payment intent creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment processing failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Confirm payment intent
     */
    public function confirmPaymentIntent(string $intentId, ?string $paymentMethodId = null): array
    {
        try {
            $intent = PaymentIntent::retrieve($intentId);

            if ($paymentMethodId) {
                $intent->confirm([
                    'payment_method' => $paymentMethodId,
                ]);
            }

            return [
                'success' => true,
                'status' => $intent->status,
                'intent' => $intent->toArray(),
            ];

        } catch (Exception $e) {
            Log::error('Stripe payment confirmation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create subscription
     */
    public function createSubscription(array $data): array
    {
        try {
            $subscriptionData = [
                'customer' => $data['customer_id'],
                'items' => [
                    ['price' => $data['price_id']],
                ],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'subscription_id' => (string) ($data['subscription_id'] ?? ''),
                    'user_id' => (string) ($data['user_id'] ?? ''),
                ],
            ];

            // Add trial period if specified
            if (isset($data['trial_days']) && $data['trial_days'] > 0) {
                $subscriptionData['trial_period_days'] = $data['trial_days'];
            }

            // Add coupon if specified
            if (isset($data['coupon'])) {
                $subscriptionData['coupon'] = $data['coupon'];
            }

            $subscription = Subscription::create($subscriptionData);

            $result = [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
            ];

            // Add client secret if available
            if ($subscription->latest_invoice && $subscription->latest_invoice->payment_intent) {
                $result['client_secret'] = $subscription->latest_invoice->payment_intent->client_secret;
            }

            return $result;

        } catch (Exception $e) {
            Log::error('Stripe subscription creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create customer
     */
    public function createCustomer(array $data): array
    {
        try {
            $customerData = [
                'email' => $data['email'],
                'name' => $data['name'],
                'metadata' => [
                    'user_id' => (string) ($data['user_id'] ?? ''),
                ],
            ];

            if (isset($data['phone'])) {
                $customerData['phone'] = $data['phone'];
            }

            if (isset($data['payment_method'])) {
                $customerData['payment_method'] = $data['payment_method'];
            }

            $customer = Customer::create($customerData);

            return [
                'success' => true,
                'customer_id' => $customer->id,
            ];

        } catch (Exception $e) {
            Log::error('Stripe customer creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get customer
     */
    public function getCustomer(string $customerId): array
    {
        try {
            $customer = Customer::retrieve($customerId);

            return [
                'success' => true,
                'customer' => $customer->toArray(),
            ];

        } catch (Exception $e) {
            Log::error('Stripe customer retrieval failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update customer
     */
    public function updateCustomer(string $customerId, array $data): array
    {
        try {
            $customer = Customer::retrieve($customerId);

            foreach ($data as $key => $value) {
                $customer->$key = $value;
            }

            $customer->save();

            return [
                'success' => true,
                'customer' => $customer->toArray(),
            ];

        } catch (Exception $e) {
            Log::error('Stripe customer update failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process refund
     */
    public function refund(string $paymentIntentId, ?float $amount = null): array
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];

            if ($amount) {
                $params['amount'] = (int) ($amount * 100);
            }

            $refund = Refund::create($params);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
                'status' => $refund->status,
                'currency' => $refund->currency,
            ];

        } catch (Exception $e) {
            Log::error('Stripe refund failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            $subscription->cancel();

            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
            ];

        } catch (Exception $e) {
            Log::error('Stripe subscription cancellation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get subscription
     */
    public function getSubscription(string $subscriptionId): array
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);

            return [
                'success' => true,
                'subscription' => $subscription->toArray(),
            ];

        } catch (Exception $e) {
            Log::error('Stripe subscription retrieval failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update subscription
     */
    public function updateSubscription(string $subscriptionId, array $data): array
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);

            foreach ($data as $key => $value) {
                $subscription->$key = $value;
            }

            $subscription->save();

            return [
                'success' => true,
                'subscription' => $subscription->toArray(),
            ];

        } catch (Exception $e) {
            Log::error('Stripe subscription update failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment intent
     */
    public function getPaymentIntent(string $intentId): array
    {
        try {
            $intent = PaymentIntent::retrieve($intentId);

            return [
                'success' => true,
                'intent' => $intent->toArray(),
            ];

        } catch (Exception $e) {
            Log::error('Stripe payment intent retrieval failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook(string $payload, string $signature): bool
    {
        try {
            if (! $this->config->webhook_secret) {
                Log::warning('Stripe webhook secret not configured');

                return true; // Skip verification if no secret
            }

            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->config->webhook_secret
            );

            return true;

        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload: '.$e->getMessage());

            return false;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: '.$e->getMessage());

            return false;
        } catch (Exception $e) {
            Log::error('Stripe webhook verification error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $payload): array
    {
        $eventType = $payload['type'] ?? 'unknown';
        $eventData = $payload['data']['object'] ?? [];

        switch ($eventType) {
            case 'payment_intent.succeeded':
                return $this->handlePaymentIntentSucceeded($eventData);

            case 'payment_intent.payment_failed':
                return $this->handlePaymentIntentFailed($eventData);

            case 'invoice.payment_succeeded':
                return $this->handleInvoicePaymentSucceeded($eventData);

            case 'invoice.payment_failed':
                return $this->handleInvoicePaymentFailed($eventData);

            case 'customer.subscription.created':
                return $this->handleSubscriptionCreated($eventData);

            case 'customer.subscription.updated':
                return $this->handleSubscriptionUpdated($eventData);

            case 'customer.subscription.deleted':
                return $this->handleSubscriptionDeleted($eventData);

            case 'charge.refunded':
                return $this->handleChargeRefunded($eventData);

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $eventType]);

                return ['handled' => false];
        }
    }

    /**
     * Handle payment intent succeeded
     */
    protected function handlePaymentIntentSucceeded($data): array
    {
        Log::info('Payment intent succeeded', ['id' => $data['id']]);

        // Find and update transaction
        $transaction = PaymentTransaction::where('transaction_id', $data['id'])->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
                'gateway_response' => json_encode($data),
            ]);

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            if ($paymentMaster) {
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $paymentMaster->total_amount,
                    'paid_at' => now(),
                ]);
            }
        }

        return ['handled' => true];
    }

    /**
     * Handle payment intent failed
     */
    protected function handlePaymentIntentFailed($data): array
    {
        Log::warning('Payment intent failed', ['id' => $data['id'], 'error' => $data['last_payment_error'] ?? null]);

        $transaction = PaymentTransaction::where('transaction_id', $data['id'])->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $data['last_payment_error']['message'] ?? 'Payment failed',
                'gateway_response' => json_encode($data),
            ]);
        }

        return ['handled' => true];
    }

    /**
     * Handle invoice payment succeeded
     */
    protected function handleInvoicePaymentSucceeded($data): array
    {
        Log::info('Invoice payment succeeded', ['id' => $data['id']]);

        return ['handled' => true];
    }

    /**
     * Handle invoice payment failed
     */
    protected function handleInvoicePaymentFailed($data): array
    {
        Log::warning('Invoice payment failed', ['id' => $data['id']]);

        return ['handled' => true];
    }

    /**
     * Handle subscription created
     */
    protected function handleSubscriptionCreated($data): array
    {
        Log::info('Subscription created', ['id' => $data['id']]);

        return ['handled' => true];
    }

    /**
     * Handle subscription updated
     */
    protected function handleSubscriptionUpdated($data): array
    {
        Log::info('Subscription updated', ['id' => $data['id'], 'status' => $data['status']]);

        return ['handled' => true];
    }

    /**
     * Handle subscription deleted
     */
    protected function handleSubscriptionDeleted($data): array
    {
        Log::info('Subscription deleted', ['id' => $data['id']]);

        return ['handled' => true];
    }

    /**
     * Handle charge refunded
     */
    protected function handleChargeRefunded($data): array
    {
        Log::info('Charge refunded', ['id' => $data['id']]);

        return ['handled' => true];
    }
}

// composer require stripe/stripe-php
