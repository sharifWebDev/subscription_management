<?php

namespace App\Services\PaymentGateways;

use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class StripeGateway
{
    protected $secretKey;

    protected $publicKey;

    protected $isSandbox;

    public function __construct()
    {
        $this->isSandbox = config('payment.gateways.stripe.sandbox', true);
        $this->secretKey = $this->isSandbox
            ? config('payment.gateways.stripe.test_secret_key')
            : config('payment.gateways.stripe.live_secret_key');
        $this->publicKey = $this->isSandbox
            ? config('payment.gateways.stripe.test_public_key')
            : config('payment.gateways.stripe.live_public_key');

        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create payment intent with saved payment method
     */
    public function createPaymentIntentWithSavedMethod(array $data, string $paymentMethodId): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToCents($data['amount']),
                'currency' => strtolower($data['currency'] ?? 'usd'),
                'payment_method' => $paymentMethodId,
                'off_session' => true,
                'confirm' => true,
                'receipt_email' => $data['receipt_email'] ?? null,
                'statement_descriptor' => $data['statement_descriptor'] ?? null,
                'description' => $data['description'] ?? 'Subscription Payment',
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                    'email' => $data['email'] ?? null,
                    'name' => $data['name'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'subscription_id' => $data['subscription_id'] ?? null,
                    'order_id' => $data['order_id'] ?? null,
                    'customer_id' => $data['customer_id'] ?? null,
                    'payment_intent_id' => $data['payment_intent_id'] ?? null,
                    'payment_master_id' => $data['payment_master_id'] ?? null,
                    'plan_id' => $data['plan_id'] ?? null,
                    'price_id' => $data['price_id'] ?? null,
                    'description' => $data['plan_name'] ?? null,
                    'customer_name' => $data['customer_name'] ?? null,
                    'address' => $data['address'] ?? null,
                    'city' => $data['city'] ?? null,
                    'country' => $data['country'] ?? null,
                    'product_name' => $data['product_name'] ?? null,
                    'product_category' => $data['product_category'] ?? null,
                    'transaction_id' => $data['transaction_id'] ?? null,
                ],
            ]);
            \Log::info('Stripe payment intent created with saved method: ', ['intent' => $paymentIntent]);

            return [
                'success' => true,
                'intent' => $paymentIntent,
                'intent_id' => $paymentIntent->id,
                'status' => $this->mapStatus($paymentIntent->status),
                'client_secret' => $paymentIntent->client_secret,
                'payment_method_id' => $paymentMethodId,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'card_error',
            ];
        } catch (Exception $e) {
            Log::error('Stripe payment intent with saved method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment processing failed',
            ];
        }
    }

    /**
     * Create payment intent
     */
    public function createPaymentIntent(array $data): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToCents($data['amount']),
                'currency' => strtolower($data['currency'] ?? 'usd'),
                'description' => $data['description'] ?? 'Subscription Payment',
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                    'payment_master_id' => $data['payment_master_id'] ?? null,
                    'plan_id' => $data['plan_id'] ?? null,
                    'price_id' => $data['price_id'] ?? null,
                ],
            ]);

            return [
                'success' => true,
                'intent' => $paymentIntent,
                'intent_id' => $paymentIntent->id,
                'status' => $this->mapStatus($paymentIntent->status),
                'client_secret' => $paymentIntent->client_secret,
            ];

        } catch (Exception $e) {
            Log::error('Stripe payment intent creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create payment intent',
            ];
        }
    }

    /**
     * Confirm payment intent
     */
    /**
     * Confirm payment intent (for 3D Secure)
     */
    public function confirmPaymentIntent(string $paymentIntentId): array
    {
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            // If payment intent already succeeded
            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'intent' => $paymentIntent,
                    'intent_id' => $paymentIntent->id,
                    'status' => 'completed',
                    'payment_method_id' => $paymentIntent->payment_method,
                ];
            }

            // Check if it requires confirmation
            if ($paymentIntent->status === 'requires_confirmation') {
                $paymentIntent->confirm();
            }

            return [
                'success' => true,
                'intent' => $paymentIntent,
                'intent_id' => $paymentIntent->id,
                'status' => $this->mapStatus($paymentIntent->status),
                'payment_method_id' => $paymentIntent->payment_method,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe card error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 'card_error',
            ];
        } catch (Exception $e) {
            Log::error('Stripe confirm payment intent failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to confirm payment',
            ];
        }
    }

    /**
     * Retrieve payment method
     */
    public function retrievePaymentMethod(string $paymentMethodId): array
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            return [
                'success' => true,
                'payment_method' => $paymentMethod,
                'card' => $paymentMethod->card ?? null,
            ];

        } catch (Exception $e) {
            Log::error('Stripe retrieve payment method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to retrieve payment method',
            ];
        }
    }

    /**
     * Attach payment method to customer
     */
    public function attachPaymentMethodToCustomer(string $paymentMethodId, string $customerId): array
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customerId]);

            return [
                'success' => true,
                'payment_method' => $paymentMethod,
            ];

        } catch (Exception $e) {
            Log::error('Stripe attach payment method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to attach payment method',
            ];
        }
    }

    /**
     * Create customer
     */
    public function createCustomer(array $data): array
    {
        try {
            $customer = \Stripe\Customer::create([
                'email' => $data['email'] ?? null,
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                ],
            ]);

            \Log::info('Stripe customer created: ', ['customer' => $customer]);

            return [
                'success' => true,
                'customer' => $customer,
                'customer_id' => $customer->id,
            ];

        } catch (Exception $e) {
            Log::error('Stripe create customer failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create customer',
            ];
        }
    }

    /**
     * Convert amount to cents
     */
    protected function convertToCents($amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Map Stripe status to internal status
     */
    protected function mapStatus(string $stripeStatus): string
    {
        $statusMap = [
            'succeeded' => 'completed',
            'processing' => 'processing',
            'requires_payment_method' => 'requires_action',
            'requires_confirmation' => 'requires_action',
            'requires_action' => 'requires_action',
            'canceled' => 'cancelled',
        ];

        return $statusMap[$stripeStatus] ?? 'pending';
    }

    /**
     * Get public key
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
