<?php

namespace App\Services\PaymentGateways;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RocketGateway
{
    protected $merchantId;

    protected $merchantKey;

    protected $baseUrl;

    protected $isSandbox;

    protected $callbackUrl;

    // API endpoints
    const SANDBOX_URL = 'https://sandbox.rocket.com.bd';

    const PRODUCTION_URL = 'https://api.rocket.com.bd';

    const PAYMENT_INIT = '/api/v1/payment/initiate';

    const PAYMENT_VERIFY = '/api/v1/payment/verify';

    const PAYMENT_REFUND = '/api/v1/payment/refund';

    const TRANSACTION_STATUS = '/api/v1/transaction/status';

    public function __construct()
    {
        $this->isSandbox = config('payment.gateways.rocket.sandbox', true);
        $this->merchantId = config('payment.gateways.rocket.merchant_id');
        $this->merchantKey = config('payment.gateways.rocket.merchant_key');
        $this->baseUrl = $this->isSandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $this->callbackUrl = config('payment.gateways.rocket.callback_url', route('payment.rocket.callback'));
    }

    /**
     * Initialize Rocket payment
     */
    public function createPayment(array $data): array
    {
        try {
            // Generate unique transaction ID
            $transactionId = $this->generateTransactionId();

            // Prepare request data
            $requestData = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'amount' => $this->formatAmount($data['amount']),
                'currency' => $data['currency'] ?? 'BDT',
                'description' => $data['description'] ?? 'Subscription Payment',
                'customer_name' => $data['customer_name'] ?? 'Customer',
                'customer_email' => $data['email'] ?? '',
                'customer_mobile' => $data['phone'] ?? '',
                'callback_url' => $this->callbackUrl,
                'ipn_url' => config('payment.gateways.rocket.ipn_url', route('payment.rocket.ipn')),
                'timestamp' => now()->timestamp,
            ];

            // Generate signature
            $requestData['signature'] = $this->generateSignature($requestData);

            // Make API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $this->merchantKey,
            ])->post($this->baseUrl.self::PAYMENT_INIT, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                    // Store transaction data in cache
                    $transactionData = [
                        'merchant_id' => $this->merchantId,
                        'transaction_id' => $transactionId,
                        'amount' => $data['amount'],
                        'currency' => $data['currency'] ?? 'BDT',
                        'customer_name' => $data['customer_name'] ?? 'Customer',
                        'customer_email' => $data['email'] ?? '',
                        'customer_phone' => $data['phone'] ?? '',
                        'description' => $data['description'] ?? '',
                        'payment_url' => $result['payment_url'] ?? null,
                        'qr_code' => $result['qr_code'] ?? null,
                        'expiry_time' => $result['expiry_time'] ?? now()->addHour()->timestamp,
                    ];

                    cache()->put('rocket_'.$transactionId, $transactionData, now()->addHours(2));

                    return [
                        'success' => true,
                        'message' => 'Payment initiated successfully',
                        'transaction_id' => $transactionId,
                        'payment_url' => $result['payment_url'] ?? null,
                        'qr_code' => $result['qr_code'] ?? null,
                        'expiry_time' => $result['expiry_time'] ?? null,
                        'instructions' => $this->getPaymentInstructions(),
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
                'message' => 'Failed to connect to Rocket gateway',
            ];
        } catch (Exception $e) {
            Log::error('Rocket payment initiation error: '.$e->getMessage(), [
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
     * Verify payment status
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $requestData = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'timestamp' => now()->timestamp,
            ];

            $requestData['signature'] = $this->generateSignature($requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $this->merchantKey,
            ])->post($this->baseUrl.self::PAYMENT_VERIFY, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status'])) {
                    return [
                        'success' => true,
                        'payment_status' => $this->mapPaymentStatus($result['status']),
                        'transaction_id' => $result['transaction_id'] ?? $transactionId,
                        'rocket_transaction_id' => $result['rocket_transaction_id'] ?? null,
                        'amount' => $result['amount'] ?? null,
                        'currency' => $result['currency'] ?? 'BDT',
                        'payment_time' => $result['payment_time'] ?? null,
                        'customer_mobile' => $result['customer_mobile'] ?? null,
                        'data' => $result,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to verify payment with Rocket',
            ];
        } catch (Exception $e) {
            Log::error('Rocket payment verification error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Verification error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $requestData = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'timestamp' => now()->timestamp,
            ];

            $requestData['signature'] = $this->generateSignature($requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $this->merchantKey,
            ])->post($this->baseUrl.self::TRANSACTION_STATUS, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'success' => true,
                    'status' => $this->mapPaymentStatus($result['status'] ?? 'UNKNOWN'),
                    'transaction_id' => $result['transaction_id'] ?? $transactionId,
                    'amount' => $result['amount'] ?? null,
                    'payment_time' => $result['payment_time'] ?? null,
                    'data' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get transaction status',
            ];
        } catch (Exception $e) {
            Log::error('Rocket transaction status error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Status check error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Process manual payment verification (for transaction ID based)
     */
    public function processManualPayment(string $transactionId, string $rocketTransactionId, float $amount): array
    {
        try {
            // Verify with Rocket API
            $verification = $this->verifyPayment($transactionId);

            if ($verification['success']) {
                // Check if amount matches
                if (abs($verification['amount'] - $amount) > 0.01) {
                    return [
                        'success' => false,
                        'message' => 'Amount mismatch. Expected: '.$amount.', Received: '.$verification['amount'],
                    ];
                }

                // Check if transaction is completed
                if ($verification['payment_status'] === 'completed') {
                    return [
                        'success' => true,
                        'status' => 'completed',
                        'transaction_id' => $transactionId,
                        'rocket_transaction_id' => $rocketTransactionId,
                        'amount' => $verification['amount'],
                        'payment_time' => $verification['payment_time'],
                        'message' => 'Payment verified successfully',
                    ];
                } else {
                    return [
                        'success' => false,
                        'status' => $verification['payment_status'],
                        'message' => 'Payment not completed. Current status: '.$verification['payment_status'],
                    ];
                }
            }

            // If API verification fails, store for manual review
            $this->storeForManualReview($transactionId, $rocketTransactionId, $amount);

            return [
                'success' => false,
                'message' => 'Payment verification failed. It will be reviewed manually.',
                'requires_manual_review' => true,
            ];
        } catch (Exception $e) {
            Log::error('Rocket manual payment error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Verification error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Initiate refund
     */
    public function initiateRefund(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $requestData = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'refund_amount' => $this->formatAmount($amount),
                'refund_reason' => $reason,
                'timestamp' => now()->timestamp,
            ];

            $requestData['signature'] = $this->generateSignature($requestData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $this->merchantKey,
            ])->post($this->baseUrl.self::PAYMENT_REFUND, $requestData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                    return [
                        'success' => true,
                        'refund_id' => $result['refund_id'] ?? null,
                        'amount' => $result['refunded_amount'] ?? $amount,
                        'message' => 'Refund initiated successfully',
                        'data' => $result,
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
                'message' => 'Failed to initiate refund with Rocket',
            ];
        } catch (Exception $e) {
            Log::error('Rocket refund error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Refund error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Process callback from Rocket
     */
    public function processCallback(Request $request): array
    {
        try {
            $data = $request->all();

            Log::info('Rocket callback received', $data);

            $transactionId = $data['transaction_id'] ?? null;
            $status = $data['status'] ?? null;
            $rocketTransactionId = $data['rocket_transaction_id'] ?? null;

            if (! $transactionId) {
                throw new Exception('Invalid callback data: missing transaction_id');
            }

            // Verify signature if provided
            if (isset($data['signature'])) {
                $expectedSignature = $this->generateSignature($data);
                if (! hash_equals($expectedSignature, $data['signature'])) {
                    throw new Exception('Invalid signature');
                }
            }

            // Map status
            $mappedStatus = $this->mapPaymentStatus($status);

            return [
                'success' => $mappedStatus === 'completed',
                'status' => $mappedStatus,
                'transaction_id' => $transactionId,
                'rocket_transaction_id' => $rocketTransactionId,
                'amount' => $data['amount'] ?? null,
                'payment_time' => $data['payment_time'] ?? null,
                'data' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Rocket callback processing error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Callback processing failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Generate signature for request
     */
    protected function generateSignature(array $data): string
    {
        // Sort data by keys
        ksort($data);

        // Create string to sign
        $stringToSign = '';
        foreach ($data as $key => $value) {
            if ($key !== 'signature' && $value !== null && $value !== '') {
                $stringToSign .= $key.'='.$value.'&';
            }
        }
        $stringToSign = rtrim($stringToSign, '&');

        // Generate HMAC-SHA256 signature
        return hash_hmac('sha256', $stringToSign, $this->merchantKey);
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(): string
    {
        $prefix = 'RKT';
        $timestamp = time();
        $random = rand(1000, 9999);
        $uniqid = uniqid();

        return $prefix.$timestamp.$random.substr($uniqid, -4);
    }

    /**
     * Format amount for Rocket
     */
    protected function formatAmount(float $amount): float
    {
        return round($amount, 2);
    }

    /**
     * Map Rocket payment status to internal status
     */
    protected function mapPaymentStatus(string $rocketStatus): string
    {
        $statusMap = [
            'SUCCESS' => 'completed',
            'COMPLETED' => 'completed',
            'PAID' => 'completed',
            'PENDING' => 'pending',
            'PROCESSING' => 'processing',
            'INITIATED' => 'initiated',
            'FAILED' => 'failed',
            'FAILURE' => 'failed',
            'CANCELLED' => 'cancelled',
            'CANCEL' => 'cancelled',
            'EXPIRED' => 'expired',
            'REFUNDED' => 'refunded',
            'PARTIALLY_REFUNDED' => 'partially_refunded',
        ];

        return $statusMap[strtoupper($rocketStatus)] ?? 'unknown';
    }

    /**
     * Get payment instructions
     */
    protected function getPaymentInstructions(): array
    {
        return [
            'merchant_number' => '01812345678',
            'payment_method' => 'Rocket',
            'steps' => [
                'Go to your Rocket mobile app',
                'Select "Send Money"',
                'Enter merchant number: 01812345678',
                'Enter the exact amount',
                'Add reference/transaction ID in notes',
                'Complete the payment and save the transaction ID',
            ],
            'note' => 'Please enter the Rocket transaction ID in the form below to verify your payment.',
        ];
    }

    /**
     * Store transaction for manual review
     */
    protected function storeForManualReview(string $transactionId, string $rocketTransactionId, float $amount): void
    {
        $reviewData = [
            'transaction_id' => $transactionId,
            'rocket_transaction_id' => $rocketTransactionId,
            'amount' => $amount,
            'submitted_at' => now()->toDateTimeString(),
            'status' => 'pending_review',
        ];

        cache()->put('rocket_review_'.$transactionId, $reviewData, now()->addDays(7));

        // You could also store in database for admin review
        Log::info('Rocket payment pending manual review', $reviewData);
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
     * Get merchant information
     */
    public function getMerchantInfo(): array
    {
        return [
            'merchant_id' => $this->merchantId,
            'is_sandbox' => $this->isSandbox,
            'supported_currencies' => ['BDT'],
            'supported_countries' => ['BD'],
            'payment_methods' => ['Send Money', 'Payment'],
        ];
    }

    /**
     * Validate webhook notification
     */
    public function validateWebhook(Request $request): bool
    {
        $signature = $request->header('X-Rocket-Signature');
        $payload = $request->getContent();

        if (! $signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->merchantKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get payment status with retry
     */
    public function getPaymentStatusWithRetry(string $transactionId, int $maxRetries = 3): array
    {
        for ($i = 0; $i < $maxRetries; $i++) {
            $result = $this->verifyPayment($transactionId);

            if ($result['success'] && in_array($result['payment_status'], ['completed', 'failed', 'cancelled'])) {
                return $result;
            }

            if ($i < $maxRetries - 1) {
                sleep(3); // Wait 3 seconds before retry
            }
        }

        return [
            'success' => false,
            'message' => 'Unable to get final payment status after '.$maxRetries.' attempts',
        ];
    }

    /**
     * Generate QR code data for payment
     */
    public function generateQRCodeData(string $transactionId, float $amount): string
    {
        $data = [
            'merchant' => $this->merchantId,
            'tid' => $transactionId,
            'amount' => $amount,
            'currency' => 'BDT',
            'type' => 'PAYMENT',
        ];

        return json_encode($data);
    }
}
