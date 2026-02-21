<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PayPalGateway
{
    protected $config;
    protected $accessToken;
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->config = PaymentGateway::where('code', 'paypal')
            ->where('is_active', true)
            ->first();

        if (!$this->config) {
            throw new Exception('PayPal gateway not configured. Please add PayPal to your payment_gateways table.');
        }

        $this->clientId = $this->config->api_key;
        $this->clientSecret = $this->config->api_secret;
        $this->baseUrl = $this->config->base_url ?? 'https://api-m.sandbox.paypal.com';

        if (!$this->clientId) {
            throw new Exception('PayPal Client ID not configured');
        }

        if (!$this->clientSecret) {
            throw new Exception('PayPal Client Secret not configured');
        }
    }

    /**
     * Get access token
     */
    protected function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $auth = base64_encode($this->clientId . ':' . $this->clientSecret);

            Log::info('Requesting PayPal access token', [
                'url' => $this->baseUrl . '/v1/oauth2/token'
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->timeout(30)->asForm()->post($this->baseUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

            if (!$response->successful()) {
                Log::error('PayPal token error response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Failed to get PayPal access token: ' . $response->body());
            }

            $data = $response->json();

            if (!isset($data['access_token'])) {
                throw new Exception('PayPal response missing access_token');
            }

            $this->accessToken = $data['access_token'];

            Log::info('PayPal access token obtained successfully');

            return $this->accessToken;

        } catch (Exception $e) {
            Log::error('PayPal token error: ' . $e->getMessage());
            throw new Exception('PayPal authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Create order
     */
    public function createOrder(array $data): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => (string) $data['payment_master_id'],
                        'description' => $data['description'] ?? 'Subscription Payment',
                        'amount' => [
                            'currency_code' => $data['currency'],
                            'value' => number_format($data['amount'], 2, '.', '')
                        ],
                        'custom_id' => json_encode([
                            'payment_master_id' => $data['payment_master_id'],
                            'plan_id' => $data['plan_id'] ?? null,
                            'user_id' => $data['user_id'] ?? null
                        ])
                    ]
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'landing_page' => 'BILLING',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('payment.paypal.success'),
                    'cancel_url' => route('payment.paypal.cancel')
                ]
            ];

            // Add payer info if available
            if (isset($data['email'])) {
                $orderData['payer'] = [
                    'email_address' => $data['email'],
                    'name' => [
                        'given_name' => $data['first_name'] ?? '',
                        'surname' => $data['last_name'] ?? ''
                    ]
                ];
            }

            Log::info('Creating PayPal order', [
                'amount' => $data['amount'],
                'currency' => $data['currency']
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ])->timeout(30)->post($this->baseUrl . '/v2/checkout/orders', $orderData);

            if (!$response->successful()) {
                Log::error('PayPal order creation failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('PayPal order creation failed: ' . $response->body());
            }

            $order = $response->json();

            // Find approval link
            $approvalLink = null;
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalLink = $link['href'];
                    break;
                }
            }

            if (!$approvalLink) {
                throw new Exception('No approval link found in PayPal response');
            }

            Log::info('PayPal order created successfully', [
                'order_id' => $order['id'],
                'status' => $order['status']
            ]);

            return [
                'success' => true,
                'order_id' => $order['id'],
                'approval_url' => $approvalLink,
                'status' => $order['status']
            ];

        } catch (Exception $e) {
            Log::error('PayPal order creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Capture order payment
     */
    public function captureOrder(string $orderId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            Log::info('Capturing PayPal order', ['order_id' => $orderId]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ])->timeout(30)->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture");

            if (!$response->successful()) {
                Log::error('PayPal capture failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('PayPal capture failed: ' . $response->body());
            }

            $capture = $response->json();

            // Extract capture details
            $captureId = null;
            $amount = null;
            $currency = null;

            if (isset($capture['purchase_units'][0]['payments']['captures'][0])) {
                $captureData = $capture['purchase_units'][0]['payments']['captures'][0];
                $captureId = $captureData['id'];
                $amount = $captureData['amount']['value'];
                $currency = $captureData['amount']['currency_code'];
            }

            Log::info('PayPal order captured successfully', [
                'order_id' => $orderId,
                'capture_id' => $captureId
            ]);

            return [
                'success' => true,
                'capture_id' => $captureId,
                'order_id' => $orderId,
                'status' => $capture['status'],
                'amount' => $amount,
                'currency' => $currency,
                'full_response' => $capture
            ];

        } catch (Exception $e) {
            Log::error('PayPal capture failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create subscription (for future use)
     */
    public function createSubscription(array $data): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $subscriptionData = [
                'plan_id' => $data['plan_id'],
                'start_time' => now()->addDay()->toIso8601String(),
                'subscriber' => [
                    'name' => [
                        'given_name' => $data['first_name'] ?? '',
                        'surname' => $data['last_name'] ?? ''
                    ],
                    'email_address' => $data['email']
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => 'en-US',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'return_url' => route('payment.paypal.subscription.success'),
                    'cancel_url' => route('payment.paypal.cancel')
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($this->baseUrl . '/v1/billing/subscriptions', $subscriptionData);

            if (!$response->successful()) {
                throw new Exception('PayPal subscription creation failed: ' . $response->body());
            }

            $subscription = $response->json();

            // Find approval link
            $approvalLink = null;
            foreach ($subscription['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalLink = $link['href'];
                    break;
                }
            }

            return [
                'success' => true,
                'subscription_id' => $subscription['id'],
                'approval_url' => $approvalLink,
                'status' => $subscription['status']
            ];

        } catch (Exception $e) {
            Log::error('PayPal subscription creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get order details
     */
    public function getOrder(string $orderId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($this->baseUrl . "/v2/checkout/orders/{$orderId}");

            if (!$response->successful()) {
                throw new Exception('Failed to get PayPal order: ' . $response->body());
            }

            return [
                'success' => true,
                'order' => $response->json()
            ];

        } catch (Exception $e) {
            Log::error('PayPal get order failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Process refund
     */
    public function refund(string $captureId, float $amount = null): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $data = [];
            if ($amount) {
                $data['amount'] = [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => 'USD' // You might want to make this dynamic
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($this->baseUrl . "/v2/payments/captures/{$captureId}/refund", $data);

            if (!$response->successful()) {
                throw new Exception('PayPal refund failed: ' . $response->body());
            }

            $refund = $response->json();

            return [
                'success' => true,
                'refund_id' => $refund['id'],
                'amount' => $refund['amount']['value'],
                'status' => $refund['status'],
                'full_response' => $refund
            ];

        } catch (Exception $e) {
            Log::error('PayPal refund failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook(array $headers, string $body): bool
    {
        // Implement PayPal webhook verification
        // This is a simplified version - you should implement proper verification
        $webhookId = $this->config->webhook_id ?? null;

        if (!$webhookId) {
            Log::warning('PayPal webhook ID not configured');
            return true;
        }

        // TODO: Implement proper PayPal webhook verification
        // See: https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature

        return true;
    }
}
