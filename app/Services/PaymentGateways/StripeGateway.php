<?php

namespace App\Services\PaymentGateways;

use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
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
     * Create or retrieve Stripe customer
     */
    public function getOrCreateCustomer(array $data): array
    {
        try {
            // If customer ID is provided, retrieve existing customer
            if (isset($data['customer_id']) && ! empty($data['customer_id'])) {
                try {
                    $customer = Customer::retrieve($data['customer_id']);
                    if ($customer && ! isset($customer->deleted)) {
                        Log::info('Stripe customer retrieved', ['customer_id' => $customer->id]);

                        return [
                            'success' => true,
                            'customer' => $customer,
                            'customer_id' => $customer->id,
                            'is_new' => false,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('Stripe customer not found, will create new', [
                        'customer_id' => $data['customer_id'],
                        'error' => $e->getMessage(),
                    ]);
                    // Customer not found, will create new
                }
            }

            // Check if customer exists with this email (optional - can be disabled)
            if (isset($data['email']) && ! empty($data['email'])) {
                $customers = Customer::all([
                    'email' => $data['email'],
                    'limit' => 1,
                ]);

                if (count($customers->data) > 0) {
                    $customer = $customers->data[0];
                    Log::info('Stripe customer found by email', ['customer_id' => $customer->id]);

                    return [
                        'success' => true,
                        'customer' => $customer,
                        'customer_id' => $customer->id,
                        'is_new' => false,
                    ];
                }
            }

            // Create new customer
            $customerData = [
                'email' => $data['email'] ?? null,
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                    'source' => 'subscription_checkout',
                ],
            ];

            // Add address if provided
            if (isset($data['address']) || isset($data['city']) || isset($data['country'])) {
                $customerData['address'] = [
                    'line1' => $data['address'] ?? '',
                    'city' => $data['city'] ?? '',
                    'country' => $data['country'] ?? '',
                ];
            }

            $customer = Customer::create($customerData);

            Log::info('Stripe customer created', ['customer_id' => $customer->id]);

            return [
                'success' => true,
                'customer' => $customer,
                'customer_id' => $customer->id,
                'is_new' => true,
            ];

        } catch (Exception $e) {
            Log::error('Stripe create/get customer failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create/get customer: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Create payment intent with saved payment method
     */
    public function createPaymentIntentWithSavedMethod(array $data, string $paymentMethodId): array
    {
        try {
            // Get or create customer
            $customerResult = $this->getOrCreateCustomer([
                'customer_id' => $data['customer_id'] ?? null,
                'email' => $data['email'] ?? null,
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'user_id' => $data['user_id'] ?? null,
            ]);

            if (! $customerResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to create customer',
                ];
            }

            $customerId = $customerResult['customer_id'];

            // Attach payment method to customer
            try {
                $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
                if ($paymentMethod->customer !== $customerId) {
                    $paymentMethod->attach(['customer' => $customerId]);
                }
            } catch (Exception $e) {
                Log::warning('Error attaching payment method: '.$e->getMessage());
            }

            // **আপডেট করা return_url**
            $returnUrl = config('app.url').'/payment/stripe/success';

            // Create and confirm payment intent in one request
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToCents($data['amount']),
                'currency' => strtolower($data['currency'] ?? 'usd'),
                'customer' => $customerId,
                'payment_method' => $paymentMethodId,
                'confirm' => true,  // This will handle 3D Secure automatically
                'off_session' => false,
                'return_url' => $returnUrl,
                'receipt_email' => $data['receipt_email'] ?? $data['email'] ?? null,
                'description' => $data['description'] ?? 'Subscription Payment',
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                    'payment_master_id' => $data['payment_master_id'] ?? null,
                    'order_id' => $data['order_id'] ?? null,
                ],
            ]);

            Log::info('Stripe payment intent created', [
                'intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);

            // If payment succeeded immediately
            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'intent_id' => $paymentIntent->id,
                    'status' => 'completed',
                    'customer_id' => $customerId,
                    'payment_method_id' => $paymentMethodId,
                ];
            }

            // If payment requires action (3D Secure)
            if ($paymentIntent->status === 'requires_action') {
                return [
                    'success' => false,
                    'message' => 'Payment requires authentication',
                    'intent_id' => $paymentIntent->id,
                    'status' => 'requires_action',
                    'client_secret' => $paymentIntent->client_secret,
                ];
            }

            return [
                'success' => false,
                'message' => 'Payment failed: '.$paymentIntent->status,
            ];

        } catch (Exception $e) {
            Log::error('Stripe payment failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create payment intent (without payment method)
     */
    public function createPaymentIntent(array $data): array
    {
        try {
            // Get or create customer
            $customerResult = $this->getOrCreateCustomer([
                'customer_id' => $data['customer_id'] ?? null,
                'email' => $data['email'] ?? null,
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'user_id' => $data['user_id'] ?? null,
            ]);

            if (! $customerResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to create customer',
                ];
            }

            // **আপডেট করা return_url**
            $returnUrl = config('app.url').'/payment/stripe/success';

            $paymentIntentData = [
                'amount' => $this->convertToCents($data['amount']),
                'currency' => strtolower($data['currency'] ?? 'usd'),
                'customer' => $customerResult['customer_id'],
                'description' => $data['description'] ?? 'Subscription Payment',
                'return_url' => $returnUrl,
                'metadata' => [
                    'user_id' => $data['user_id'] ?? null,
                    'payment_master_id' => $data['payment_master_id'] ?? null,
                    'plan_id' => $data['plan_id'] ?? null,
                    'price_id' => $data['price_id'] ?? null,
                    'customer_id' => $customerResult['customer_id'],
                ],
            ];

            // Add setup_future_usage to save payment method for future use
            $paymentIntentData['setup_future_usage'] = 'off_session';

            // Use automatic_payment_methods with redirects disabled
            $paymentIntentData['automatic_payment_methods'] = [
                'enabled' => true,
                'allow_redirects' => 'never', // This prevents redirect-based payment methods
            ];

            // Add receipt email if provided
            if (isset($data['receipt_email']) || isset($data['email'])) {
                $paymentIntentData['receipt_email'] = $data['receipt_email'] ?? $data['email'] ?? null;
            }

            $paymentIntent = PaymentIntent::create($paymentIntentData);

            Log::info('Stripe payment intent created', [
                'intent_id' => $paymentIntent->id,
                'customer_id' => $customerResult['customer_id'],
            ]);

            return [
                'success' => true,
                'intent' => $paymentIntent,
                'intent_id' => $paymentIntent->id,
                'status' => $this->mapStatus($paymentIntent->status),
                'client_secret' => $paymentIntent->client_secret,
                'customer_id' => $customerResult['customer_id'],
                'customer' => $customerResult['customer'],
            ];

        } catch (Exception $e) {
            Log::error('Stripe payment intent creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create payment intent: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Confirm payment intent (for 3D Secure)
     */
    public function confirmPaymentIntent(string $paymentIntentId, ?string $paymentMethodId = null): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            // If payment intent already succeeded
            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'intent' => $paymentIntent,
                    'intent_id' => $paymentIntent->id,
                    'status' => 'completed',
                    'payment_method_id' => $paymentIntent->payment_method,
                    'customer_id' => $paymentIntent->customer,
                ];
            }

            // Check if it requires confirmation
            if ($paymentIntent->status === 'requires_confirmation' ||
                $paymentIntent->status === 'requires_payment_method' ||
                $paymentIntent->status === 'requires_action') {

                $confirmParams = [];
                if ($paymentMethodId) {
                    $confirmParams['payment_method'] = $paymentMethodId;
                }

                // **আপডেট করা return_url**
                $confirmParams['return_url'] = config('app.url').'/payment/stripe/success';

                $paymentIntent->confirm($confirmParams);
            }

            // Get payment method details
            $paymentMethodDetails = null;
            if ($paymentIntent->payment_method) {
                try {
                    $pm = PaymentMethod::retrieve($paymentIntent->payment_method);
                    $paymentMethodDetails = [
                        'id' => $pm->id,
                        'type' => $pm->type,
                        'card_brand' => $pm->card->brand ?? null,
                        'card_last4' => $pm->card->last4 ?? null,
                        'card_exp_month' => $pm->card->exp_month ?? null,
                        'card_exp_year' => $pm->card->exp_year ?? null,
                        'customer_id' => $pm->customer,
                    ];
                } catch (Exception $e) {
                    // Ignore
                }
            }

            return [
                'success' => true,
                'intent' => $paymentIntent,
                'intent_id' => $paymentIntent->id,
                'status' => $this->mapStatus($paymentIntent->status),
                'payment_method_id' => $paymentIntent->payment_method,
                'customer_id' => $paymentIntent->customer,
                'payment_method_details' => $paymentMethodDetails,
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
                'message' => 'Failed to confirm payment: '.$e->getMessage(),
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
                'customer_id' => $paymentMethod->customer,
            ];

        } catch (Exception $e) {
            Log::error('Stripe retrieve payment method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to retrieve payment method: '.$e->getMessage(),
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

            // Only attach if not already attached to this customer
            if ($paymentMethod->customer !== $customerId) {
                $paymentMethod->attach(['customer' => $customerId]);
                Log::info('Payment method attached to customer', [
                    'payment_method_id' => $paymentMethodId,
                    'customer_id' => $customerId,
                ]);
            }

            // Optionally set as default payment method
            $customer = Customer::retrieve($customerId);
            if (! $customer->invoice_settings->default_payment_method) {
                $customer->invoice_settings = ['default_payment_method' => $paymentMethodId];
                $customer->save();
            }

            return [
                'success' => true,
                'payment_method' => $paymentMethod,
                'customer_id' => $customerId,
                'is_default' => $customer->invoice_settings->default_payment_method === $paymentMethodId,
            ];

        } catch (Exception $e) {
            Log::error('Stripe attach payment method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to attach payment method: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Set default payment method for customer
     */
    public function setDefaultPaymentMethod(string $customerId, string $paymentMethodId): array
    {
        try {
            $customer = Customer::retrieve($customerId);
            $customer->invoice_settings = ['default_payment_method' => $paymentMethodId];
            $customer->save();

            return [
                'success' => true,
                'customer' => $customer,
                'customer_id' => $customerId,
                'payment_method_id' => $paymentMethodId,
            ];

        } catch (Exception $e) {
            Log::error('Stripe set default payment method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to set default payment method: '.$e->getMessage(),
            ];
        }
    }

    /**
     * List payment methods for customer
     */
    public function listPaymentMethods(string $customerId, array $types = ['card']): array
    {
        try {
            $paymentMethods = PaymentMethod::all([
                'customer' => $customerId,
                'type' => 'card',
                'limit' => 10,
            ]);

            return [
                'success' => true,
                'payment_methods' => $paymentMethods->data,
                'customer_id' => $customerId,
                'has_more' => $paymentMethods->has_more,
            ];

        } catch (Exception $e) {
            Log::error('Stripe list payment methods failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to list payment methods: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Detach payment method from customer
     */
    public function detachPaymentMethod(string $paymentMethodId): array
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);

            if ($paymentMethod->customer) {
                $paymentMethod->detach();
            }

            return [
                'success' => true,
                'payment_method_id' => $paymentMethodId,
            ];

        } catch (Exception $e) {
            Log::error('Stripe detach payment method failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to detach payment method: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Create customer (legacy method - use getOrCreateCustomer instead)
     */
    public function createCustomer(array $data): array
    {
        return $this->getOrCreateCustomer($data);
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
            'requires_capture' => 'requires_action',
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

    /**
     * সেটআপ ইনটেন্ট তৈরি করুন (কোনো টাকা কাটা ছাড়া)
     */
    public function createSetupIntent(array $data, string $paymentMethodId): array
    {
        try {
            $customerResult = $this->getOrCreateCustomer($data);

            $setupIntent = \Stripe\SetupIntent::create([
                'customer' => $customerResult['customer_id'],
                'payment_method' => $paymentMethodId,
                'usage' => 'off_session', // ভবিষ্যতে অনলাইনে পেমেন্টের জন্য
                'confirm' => true,
            ]);

            return [
                'success' => true,
                'setup_intent_id' => $setupIntent->id,
                'customer_id' => $customerResult['customer_id'],
                'status' => $setupIntent->status,
            ];

        } catch (Exception $e) {
            Log::error('Setup intent failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
