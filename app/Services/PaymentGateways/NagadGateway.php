<?php

namespace App\Services\PaymentGateways;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NagadGateway
{
    protected $merchantId;

    protected $merchantKey;

    protected $baseUrl;

    protected $isSandbox;

    protected $callbackUrl;

    // API endpoints
    const SANDBOX_URL = 'https://sandbox.mynagad.com';

    const PRODUCTION_URL = 'https://api.mynagad.com';

    const CHECKOUT_INIT = '/api/checkout/initialize';

    const CHECKOUT_COMPLETE = '/api/checkout/complete';

    const VERIFY_PAYMENT = '/api/verify/payment';

    public function __construct()
    {
        $this->isSandbox = config('payment.gateways.nagad.sandbox', true);
        $this->merchantId = config('payment.gateways.nagad.merchant_id');
        $this->merchantKey = config('payment.gateways.nagad.merchant_key');
        $this->baseUrl = $this->isSandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $this->callbackUrl = config('payment.gateways.nagad.callback_url');
    }

    /**
     * Initialize Nagad payment
     */
    public function createPayment(array $data): array
    {
        try {
            // Generate unique transaction ID
            $transactionId = $this->generateTransactionId();

            // Prepare request data
            $requestData = [
                'merchantId' => $this->merchantId,
                'orderId' => $transactionId,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'BDT',
                'merchantCallbackURL' => $this->callbackUrl,
                'customerName' => $data['customer_name'] ?? 'Customer',
                'customerEmail' => $data['email'] ?? '',
                'customerMobile' => $data['phone'] ?? '',
                'productName' => $data['product_name'] ?? 'Subscription',
                'productDescription' => $data['description'] ?? '',
            ];

            // Generate signature
            $requestData['signature'] = $this->generateSignature($requestData);

            // Make API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl.self::CHECKOUT_INIT, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'Success') {
                    // Store transaction data
                    $transactionData = [
                        'merchant_id' => $this->merchantId,
                        'order_id' => $transactionId,
                        'amount' => $data['amount'],
                        'currency' => $data['currency'] ?? 'BDT',
                        'customer_name' => $data['customer_name'] ?? 'Customer',
                        'customer_email' => $data['email'] ?? '',
                        'customer_phone' => $data['phone'] ?? '',
                        'product_name' => $data['product_name'] ?? 'Subscription',
                        'payment_url' => $result['paymentURL'] ?? null,
                        'sensitive_data' => $result['sensitiveData'] ?? null,
                        'merchant_callback_url' => $this->callbackUrl,
                    ];

                    // Store in cache or session for later verification
                    cache()->put('nagad_'.$transactionId, $transactionData, now()->addHours(2));

                    return [
                        'success' => true,
                        'message' => 'Payment initiated successfully',
                        'payment_id' => $transactionId,
                        'payment_url' => $result['paymentURL'],
                        'sensitive_data' => $result['sensitiveData'] ?? null,
                        'transaction_id' => $transactionId,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Payment initialization failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to connect to Nagad gateway',
            ];
        } catch (Exception $e) {
            Log::error('Nagad payment initiation error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Payment gateway error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Complete/Execute payment
     */
    public function completePayment(string $paymentId, array $data = []): array
    {
        try {
            $requestData = [
                'merchantId' => $this->merchantId,
                'orderId' => $paymentId,
                'paymentRefId' => $data['payment_ref_id'] ?? null,
                'amount' => $data['amount'] ?? null,
            ];

            $requestData['signature'] = $this->generateSignature($requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl.self::CHECKOUT_COMPLETE, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'Success') {
                    return [
                        'success' => true,
                        'message' => 'Payment completed successfully',
                        'transaction_id' => $result['transactionId'] ?? null,
                        'payment_ref_id' => $result['paymentRefId'] ?? null,
                        'amount' => $result['amount'] ?? null,
                        'data' => $result,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Payment completion failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to complete payment with Nagad',
            ];
        } catch (Exception $e) {
            Log::error('Nagad payment completion error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment completion error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(string $paymentId): array
    {
        try {
            $requestData = [
                'merchantId' => $this->merchantId,
                'orderId' => $paymentId,
            ];

            $requestData['signature'] = $this->generateSignature($requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl.self::VERIFY_PAYMENT, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'Success') {
                    return [
                        'success' => true,
                        'status' => $this->mapPaymentStatus($result['paymentStatus'] ?? 'PENDING'),
                        'transaction_id' => $result['transactionId'] ?? null,
                        'amount' => $result['amount'] ?? null,
                        'payment_time' => $result['paymentTime'] ?? null,
                        'data' => $result,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Payment verification failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to verify payment with Nagad',
            ];
        } catch (Exception $e) {
            Log::error('Nagad payment verification error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Verification error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Process payment callback
     */
    public function processCallback(Request $request): array
    {
        try {
            $data = $request->all();

            Log::info('Nagad callback received', $data);

            $paymentId = $data['orderId'] ?? null;
            $status = $data['status'] ?? null;

            if (! $paymentId) {
                throw new Exception('Invalid callback data');
            }

            // Get stored transaction data
            $transactionData = cache()->get('nagad_'.$paymentId);

            if ($status === 'Success') {
                // Verify payment
                $verification = $this->verifyPayment($paymentId);

                if ($verification['success']) {
                    return [
                        'success' => true,
                        'payment_id' => $paymentId,
                        'status' => 'completed',
                        'transaction_id' => $verification['transaction_id'],
                        'amount' => $verification['amount'],
                        'data' => array_merge($data, $verification),
                    ];
                }
            }

            return [
                'success' => false,
                'status' => 'failed',
                'payment_id' => $paymentId,
                'message' => 'Payment '.strtolower($status),
                'data' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Nagad callback processing error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Callback processing failed',
            ];
        }
    }

    /**
     * Initiate refund
     */
    public function initiateRefund(string $paymentId, float $amount, string $reason = ''): array
    {
        try {
            $requestData = [
                'merchantId' => $this->merchantId,
                'orderId' => $paymentId,
                'refundAmount' => $amount,
                'refundReason' => $reason,
            ];

            $requestData['signature'] = $this->generateSignature($requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl.'/api/refund/initiate', $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'Success') {
                    return [
                        'success' => true,
                        'refund_id' => $result['refundId'] ?? null,
                        'amount' => $result['refundedAmount'] ?? $amount,
                        'message' => 'Refund initiated successfully',
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Refund initiation failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to initiate refund with Nagad',
            ];
        } catch (Exception $e) {
            Log::error('Nagad refund error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Refund error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Generate signature for request
     */
    protected function generateSignature(array $data): string
    {
        $dataString = json_encode($data);

        return hash_hmac('sha256', $dataString, $this->merchantKey);
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(): string
    {
        $prefix = 'NAG';
        $timestamp = time();
        $random = rand(1000, 9999);

        return $prefix.$timestamp.$random;
    }

    /**
     * Map Nagad payment status to internal status
     */
    protected function mapPaymentStatus(string $nagadStatus): string
    {
        $statusMap = [
            'SUCCESS' => 'completed',
            'COMPLETED' => 'completed',
            'PENDING' => 'pending',
            'PROCESSING' => 'processing',
            'FAILED' => 'failed',
            'CANCELLED' => 'cancelled',
            'REFUNDED' => 'refunded',
        ];

        return $statusMap[strtoupper($nagadStatus)] ?? 'unknown';
    }

    /**
     * Get merchant information
     */
    public function getMerchantInfo(): array
    {
        return [
            'merchant_id' => $this->merchantId,
            'is_sandbox' => $this->isSandbox,
            'supported_currencies' => ['BDT'],
            'supported_countries' => ['BD'],
        ];
    }

    /**
     * Validate webhook notification
     */
    public function validateWebhook(Request $request): bool
    {
        $signature = $request->header('X-Nagad-Signature');
        $payload = $request->getContent();

        if (! $signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->merchantKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Format amount for Nagad
     */
    protected function formatAmount(float $amount): float
    {
        return round($amount, 2);
    }

    /**
     * Check if gateway is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl.'/api/health');

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get payment status with retry
     */
    public function getPaymentStatusWithRetry(string $paymentId, int $maxRetries = 3): array
    {
        for ($i = 0; $i < $maxRetries; $i++) {
            $result = $this->verifyPayment($paymentId);

            if ($result['success'] && in_array($result['status'], ['completed', 'failed', 'cancelled'])) {
                return $result;
            }

            if ($i < $maxRetries - 1) {
                sleep(2); // Wait 2 seconds before retry
            }
        }

        return [
            'success' => false,
            'message' => 'Unable to get final payment status',
        ];
    }
}
