<?php

namespace App\Services;

use App\DTOs\PaymentDto;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\PaymentMaster;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhookLog;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $gatewayConfigs = [];

    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository
    ) {
        $this->loadGatewayConfigs();
    }

    public function getAllPayments(Request $request): LengthAwarePaginator
    {
        $length = $request->input('length', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $sortColumnIndex = $request->input('order.0.column');
        $sortDirection = $request->input('order.0.dir', 'desc');

        $columns = [
            0 => 'id',
            1 => 'invoice_id',
            2 => 'user_id',
            3 => 'external_id',
            4 => 'type',
            5 => 'status',
            6 => 'amount',
            7 => 'fee',
            8 => 'net',
            9 => 'currency',
            10 => 'gateway',
            11 => 'gateway_response',
            12 => 'payment_method',
            13 => 'processed_at',
            14 => 'refunded_at',
            15 => 'metadata',
            16 => 'fraud_indicators',
            17 => 'created_by',
            18 => 'updated_by',
            19 => 'created_at',
            20 => 'updated_at',
            21 => 'deleted_at',
        ];

        $sortColumn = $columns[$sortColumnIndex] ?? 'id';

        $query = $this->paymentRepository->get($request);

        $query
            ->when($search && is_string($search), function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach ((new Payment)->getFillable() as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            })
            ->when(! empty($fromDate) && ! empty($toDate), function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [
                    "{$fromDate} 00:00:00",
                    "{$toDate} 23:59:59",
                ]);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            });

        $query->orderBy($sortColumn, $sortDirection);

        return $length === -1
            ? $query->paginate($query->get()->count())
            : $query->paginate($length);
    }

    public function getPaymentById(int $id): ?Payment
    {
        $payment = $this->paymentRepository->find($id);
        if (! $payment) {
            throw new ModelNotFoundException;
        }

        return $payment;
    }

    // public function storePayment(PaymentDto $dto, array $data): Payment
    // {
    //  //handleFileUploa
    //  return $this->paymentRepository->create((array) $dto);
    //  }

    public function storePayment(array $data): Payment
    {

        return $this->paymentRepository->create($data);
    }

    public function updatePayment(int $id, array $data): Payment
    {

        return $this->paymentRepository->update($id, $data);
    }

    public function deletePayment(int $id): bool
    {
        return $this->paymentRepository->delete($id);
    }
    // /

    /**
     * Load payment gateway configurations
     */
    protected function loadGatewayConfigs(): void
    {
        $gateways = PaymentGateway::where('is_active', true)->get();

        foreach ($gateways as $gateway) {
            $this->gatewayConfigs[$gateway->code] = $gateway;
        }
    }

    /**
     * Process payment through selected gateway
     */
    public function processPayment(array $data): array
    {
        $gateway = $data['gateway'];
        $method = $data['payment_method'];

        switch ($gateway) {
            case 'stripe':
                return $this->processStripePayment($data);
            case 'paypal':
                return $this->processPayPalPayment($data);
            case 'bkash':
                return $this->processBkashPayment($data);
            case 'nagad':
                return $this->processNagadPayment($data);
            case 'rocket':
                return $this->processRocketPayment($data);
            case 'surjopay':
                return $this->processSurjoPayPayment($data);
            case 'paytm':
                return $this->processPaytmPayment($data);
            case 'bank_transfer':
                return $this->processBankTransfer($data);
            default:
                throw new Exception("Unsupported payment gateway: {$gateway}");
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(array $data): array
    {
        try {
            $config = $this->gatewayConfigs['stripe'] ?? null;

            if (! $config) {
                throw new Exception('Stripe gateway not configured');
            }

            $stripe = new \Stripe\StripeClient($config->api_key);

            // Create payment intent
            $intent = $stripe->paymentIntents->create([
                'amount' => $data['amount'] * 100, // Convert to cents
                'currency' => strtolower($data['currency']),
                'payment_method' => $data['payment_details']['payment_method_id'] ?? null,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'metadata' => [
                    'payment_master_id' => $data['payment_master_id'],
                ],
            ]);

            // Create transaction record
            $transaction = $this->createTransaction([
                'payment_master_id' => $data['payment_master_id'],
                'transaction_id' => $intent->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'gateway' => 'stripe',
                'status' => $intent->status,
                'response' => $intent->toArray(),
            ]);

            if ($intent->status === 'succeeded') {
                $this->updatePaymentMaster($data['payment_master_id'], 'paid');

                return [
                    'success' => true,
                    'transaction_id' => $intent->id,
                    'status' => 'completed',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment requires additional action',
                    'next_action' => $intent->next_action ?? null,
                    'client_secret' => $intent->client_secret,
                ];
            }

        } catch (\Stripe\Exception\CardException $e) {
            return [
                'success' => false,
                'message' => $e->getError()->message,
            ];
        } catch (Exception $e) {
            Log::error('Stripe payment error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment processing failed',
            ];
        }
    }

    /**
     * Process bKash payment
     */
    protected function processBkashPayment(array $data): array
    {
        try {
            $config = $this->gatewayConfigs['bkash'] ?? null;

            if (! $config) {
                throw new Exception('bKash gateway not configured');
            }

            // bKash API integration
            $token = $this->getBkashToken($config);

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $config->api_key,
            ])->post($config->base_url.'/create-payment', [
                'amount' => $data['amount'],
                'currency' => 'BDT',
                'merchantInvoiceNumber' => 'INV-'.time(),
                'callbackURL' => $config->callback_url,
            ]);

            if ($response->successful()) {
                $result = $response->json();

                $this->createTransaction([
                    'payment_master_id' => $data['payment_master_id'],
                    'transaction_id' => $result['paymentID'],
                    'amount' => $data['amount'],
                    'currency' => 'BDT',
                    'gateway' => 'bkash',
                    'status' => 'pending',
                    'response' => $result,
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $result['bkashURL'],
                    'payment_id' => $result['paymentID'],
                ];
            }

            return [
                'success' => false,
                'message' => 'bKash payment creation failed',
            ];

        } catch (Exception $e) {
            Log::error('bKash payment error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'bKash payment failed',
            ];
        }
    }

    /**
     * Process SurjoPay payment
     */
    protected function processSurjoPayPayment(array $data): array
    {
        try {
            $config = $this->gatewayConfigs['surjopay'] ?? null;

            if (! $config) {
                throw new Exception('SurjoPay gateway not configured');
            }

            $postData = [
                'merchant_id' => $config->merchant_id,
                'merchant_password' => $config->api_secret,
                'merchant_key' => $config->api_key,
                'transaction_id' => 'TXN'.time(),
                'transaction_amount' => $data['amount'],
                'transaction_currency' => $data['currency'],
                'customer_name' => $data['customer_name'] ?? 'Customer',
                'customer_email' => $data['customer_email'] ?? '',
                'customer_mobile' => $data['customer_mobile'] ?? '',
                'success_url' => route('payment.surjopay.success'),
                'fail_url' => route('payment.surjopay.fail'),
                'cancel_url' => route('payment.surjopay.cancel'),
            ];

            $response = Http::post($config->base_url.'/payment/create', $postData);

            if ($response->successful()) {
                $result = $response->json();

                $this->createTransaction([
                    'payment_master_id' => $data['payment_master_id'],
                    'transaction_id' => $postData['transaction_id'],
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'gateway' => 'surjopay',
                    'status' => 'pending',
                    'response' => $result,
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $result['payment_url'],
                    'transaction_id' => $postData['transaction_id'],
                ];
            }

            return [
                'success' => false,
                'message' => 'SurjoPay payment creation failed',
            ];

        } catch (Exception $e) {
            Log::error('SurjoPay payment error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'SurjoPay payment failed',
            ];
        }
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(array $data): array
    {
        try {
            $config = $this->gatewayConfigs['paypal'] ?? null;

            if (! $config) {
                throw new Exception('PayPal gateway not configured');
            }

            // Get access token
            $auth = base64_encode($config->api_key.':'.$config->api_secret);

            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Basic '.$auth,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($config->base_url.'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

            if (! $tokenResponse->successful()) {
                throw new Exception('Failed to get PayPal token');
            }

            $token = $tokenResponse->json()['access_token'];

            // Create order
            $orderResponse = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
            ])->post($config->base_url.'/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $data['payment_master_id'],
                        'amount' => [
                            'currency_code' => $data['currency'],
                            'value' => $data['amount'],
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('payment.paypal.success'),
                    'cancel_url' => route('payment.paypal.cancel'),
                ],
            ]);

            if ($orderResponse->successful()) {
                $order = $orderResponse->json();

                $this->createTransaction([
                    'payment_master_id' => $data['payment_master_id'],
                    'transaction_id' => $order['id'],
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'gateway' => 'paypal',
                    'status' => 'pending',
                    'response' => $order,
                ]);

                // Find approval link
                $approvalLink = collect($order['links'])->firstWhere('rel', 'approve')['href'];

                return [
                    'success' => true,
                    'redirect_url' => $approvalLink,
                    'order_id' => $order['id'],
                ];
            }

            return [
                'success' => false,
                'message' => 'PayPal order creation failed',
            ];

        } catch (Exception $e) {
            Log::error('PayPal payment error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'PayPal payment failed',
            ];
        }
    }

    /**
     * Process bank transfer
     */
    protected function processBankTransfer(array $data): array
    {
        try {
            // Bank transfer just creates a pending payment
            $transaction = $this->createTransaction([
                'payment_master_id' => $data['payment_master_id'],
                'transaction_id' => 'BANK-'.time(),
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'gateway' => 'bank_transfer',
                'status' => 'pending',
                'response' => ['method' => 'bank_transfer'],
            ]);

            return [
                'success' => true,
                'message' => 'Bank transfer initiated',
                'transaction_id' => $transaction->transaction_id,
                'instructions' => $this->getBankTransferInstructions(),
            ];

        } catch (Exception $e) {
            Log::error('Bank transfer error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Bank transfer initiation failed',
            ];
        }
    }

    /**
     * Process recurring payment
     */
    public function processRecurringPayment(array $data): array
    {
        try {
            $subscription = \App\Models\Subscription::findOrFail($data['subscription_id']);

            // Get saved payment method
            $paymentMethod = PaymentMethod::where('user_id', $subscription->user_id)
                ->where('is_default', true)
                ->first();

            if (! $paymentMethod) {
                throw new Exception('No default payment method found');
            }

            // Process payment based on gateway
            $paymentData = [
                'payment_master_id' => $data['payment_master_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $subscription->currency,
                'gateway' => $paymentMethod->gateway,
                'payment_method' => $paymentMethod->type,
                'payment_details' => [
                    'payment_method_id' => $paymentMethod->gateway_payment_method_id,
                    'customer_id' => $paymentMethod->gateway_customer_id,
                ],
            ];

            return $this->processPayment($paymentData);

        } catch (Exception $e) {
            Log::error('Recurring payment error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Recurring payment failed',
            ];
        }
    }

    /**
     * Process refund
     */
    public function processRefund(array $data): array
    {
        try {
            $transaction = PaymentTransaction::findOrFail($data['payment_id']);
            $gateway = $transaction->payment_gateway;

            switch ($gateway) {
                case 'stripe':
                    return $this->processStripeRefund($data, $transaction);
                case 'paypal':
                    return $this->processPayPalRefund($data, $transaction);
                default:
                    throw new Exception("Refund not supported for gateway: {$gateway}");
            }

        } catch (Exception $e) {
            Log::error('Refund processing error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Refund processing failed',
            ];
        }
    }

    /**
     * Handle webhook from payment gateway
     */
    public function handleWebhook(string $gateway, array $payload, array $headers): array
    {
        try {
            // Log webhook
            $webhookLog = PaymentWebhookLog::create([
                'gateway' => $gateway,
                'event_type' => $payload['type'] ?? 'unknown',
                'webhook_id' => $payload['id'] ?? null,
                'payload' => json_encode($payload),
                'headers' => json_encode($headers),
                'status' => 'received',
                'received_at' => Carbon::now(),
            ]);

            // Verify webhook
            $verified = $this->verifyWebhook($gateway, $payload, $headers);

            if (! $verified) {
                $webhookLog->update([
                    'status' => 'failed',
                    'verification_error' => 'Webhook verification failed',
                ]);

                return ['success' => false, 'message' => 'Webhook verification failed'];
            }

            // Process based on event type
            $result = $this->processWebhookEvent($gateway, $payload);

            $webhookLog->update([
                'status' => 'processed',
                'processed_at' => Carbon::now(),
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error('Webhook processing error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Webhook processing failed',
            ];
        }
    }

    /**
     * Create transaction record
     */
    protected function createTransaction(array $data): PaymentTransaction
    {
        return PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'transaction_id' => $data['transaction_id'],
            'type' => 'payment',
            'payment_method' => $data['gateway'],
            'payment_gateway' => $data['gateway'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $data['status'],
            'gateway_response' => json_encode($data['response'] ?? []),
            'initiated_at' => Carbon::now(),
        ]);
    }

    /**
     * Update payment master status
     */
    protected function updatePaymentMaster(int $paymentMasterId, string $status): void
    {
        $master = PaymentMaster::find($paymentMasterId);
        if ($master) {
            $master->update([
                'status' => $status,
                'paid_amount' => $master->total_amount,
                'paid_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Get bKash token
     */
    protected function getBkashToken($config): string
    {
        $response = Http::withHeaders([
            'username' => $config->api_key,
            'password' => $config->api_secret,
        ])->post($config->base_url.'/tokenized/checkout/token/grant', [
            'app_key' => $config->api_key,
            'app_secret' => $config->api_secret,
        ]);

        if ($response->successful()) {
            return $response->json()['id_token'];
        }

        throw new Exception('Failed to get bKash token');
    }

    /**
     * Get bank transfer instructions
     */
    protected function getBankTransferInstructions(): array
    {
        return [
            'bank_name' => 'Example Bank',
            'account_name' => 'Your Company Name',
            'account_number' => '1234567890',
            'routing_number' => '123456789',
            'swift_code' => 'EXBKUS33',
            'notes' => 'Please include your order number in the transfer description',
        ];
    }

    /**
     * Verify webhook signature
     */
    protected function verifyWebhook(string $gateway, array $payload, array $headers): bool
    {
        $config = $this->gatewayConfigs[$gateway] ?? null;

        if (! $config || ! $config->webhook_secret) {
            return true; // Skip verification if no secret configured
        }

        switch ($gateway) {
            case 'stripe':
                $signature = $headers['stripe-signature'][0] ?? '';
                try {
                    \Stripe\Webhook::constructEvent(
                        json_encode($payload),
                        $signature,
                        $config->webhook_secret
                    );

                    return true;
                } catch (\Exception $e) {
                    return false;
                }

            case 'paypal':
                // PayPal webhook verification
                $authAlgo = $headers['paypal-auth-algo'][0] ?? '';
                $certUrl = $headers['paypal-cert-url'][0] ?? '';
                $transmissionId = $headers['paypal-transmission-id'][0] ?? '';
                $transmissionSig = $headers['paypal-transmission-sig'][0] ?? '';
                $transmissionTime = $headers['paypal-transmission-time'][0] ?? '';

                // Implement PayPal verification
                return true;

            default:
                return true;
        }
    }

    /**
     * Process webhook event
     */
    protected function processWebhookEvent(string $gateway, array $payload): array
    {
        $eventType = $payload['type'] ?? '';

        switch ($eventType) {
            case 'payment_intent.succeeded':
            case 'checkout.session.completed':
                return $this->handlePaymentSuccess($payload);

            case 'payment_intent.payment_failed':
                return $this->handlePaymentFailure($payload);

            case 'customer.subscription.deleted':
                return $this->handleSubscriptionCanceled($payload);

            case 'invoice.payment_succeeded':
                return $this->handleInvoicePaymentSucceeded($payload);

            case 'invoice.payment_failed':
                return $this->handleInvoicePaymentFailed($payload);

            default:
                return ['success' => true, 'message' => 'Event ignored'];
        }
    }

    /**
     * Handle payment success webhook
     */
    protected function handlePaymentSuccess(array $payload): array
    {
        $paymentIntentId = $payload['data']['object']['id'] ?? null;

        if ($paymentIntentId) {
            $transaction = PaymentTransaction::where('transaction_id', $paymentIntentId)->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);

                $this->updatePaymentMaster($transaction->payment_master_id, 'paid');
            }
        }

        return ['success' => true];
    }

    /**
     * Handle payment failure webhook
     */
    protected function handlePaymentFailure(array $payload): array
    {
        $paymentIntentId = $payload['data']['object']['id'] ?? null;

        if ($paymentIntentId) {
            $transaction = PaymentTransaction::where('transaction_id', $paymentIntentId)->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'failed',
                    'failed_at' => Carbon::now(),
                    'failure_reason' => $payload['data']['object']['last_payment_error']['message'] ?? null,
                ]);

                $this->updatePaymentMaster($transaction->payment_master_id, 'failed');
            }
        }

        return ['success' => true];
    }

    /**
     * Handle subscription canceled webhook
     */
    protected function handleSubscriptionCanceled(array $payload): array
    {
        $subscriptionId = $payload['data']['object']['id'] ?? null;

        if ($subscriptionId) {
            $subscription = \App\Models\Subscription::where('gateway_subscription_id', $subscriptionId)->first();

            if ($subscription) {
                $subscription->update([
                    'status' => 'canceled',
                    'canceled_at' => Carbon::now(),
                ]);
            }
        }

        return ['success' => true];
    }

    /**
     * Handle invoice payment succeeded webhook
     */
    protected function handleInvoicePaymentSucceeded(array $payload): array
    {
        $invoiceId = $payload['data']['object']['id'] ?? null;

        if ($invoiceId) {
            $invoice = \App\Models\Invoice::where('external_id', $invoiceId)->first();

            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);
            }
        }

        return ['success' => true];
    }

    /**
     * Handle invoice payment failed webhook
     */
    protected function handleInvoicePaymentFailed(array $payload): array
    {
        $invoiceId = $payload['data']['object']['id'] ?? null;
        $subscriptionId = $payload['data']['object']['subscription'] ?? null;

        if ($subscriptionId) {
            $subscription = \App\Models\Subscription::where('gateway_subscription_id', $subscriptionId)->first();

            if ($subscription) {
                $subscription->update(['status' => 'past_due']);
            }
        }

        return ['success' => true];
    }

    // getMethods
    public function getMethods(): array
    {
        $auth = auth()->user();

        return PaymentMethod::where('user_id', $auth->id)->get()->toArray();
    }

    // addMethod
    public function addMethod(array $data): array
    {
        $auth = auth()->user();
        $data['user_id'] = $auth->id;
        $data['gateway'] = isset($data['gateway']) ? $data['gateway'] : 'stripe';

        return PaymentMethod::create($data)->toArray();
    }

    // removeMethod
    public function removeMethod(int $id)
    {
        return PaymentMethod::where('id', $id)->delete();
    }
}
