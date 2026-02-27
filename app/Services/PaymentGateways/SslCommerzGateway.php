<?php

namespace App\Services\PaymentGateways;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzGateway
{
    protected $storeId;

    protected $storePassword;

    protected $baseUrl;

    protected $isSandbox;

    public function __construct()
    {
        $this->isSandbox = config('payment.gateways.sslcommerz.sandbox', true);

        // Sandbox and live credentials আলাদা করুন
        if ($this->isSandbox) {
            $this->storeId = config('payment.gateways.sslcommerz.test_store_id', 'testbox');
            $this->storePassword = config('payment.gateways.sslcommerz.test_store_password', 'qwerty');
        } else {
            $this->storeId = config('payment.gateways.sslcommerz.store_id');
            $this->storePassword = config('payment.gateways.sslcommerz.store_password');
        }

        $this->baseUrl = $this->isSandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://secure.sslcommerz.com';
    }

    /**
     * Initialize SSLCommerz payment
     */
    public function initPayment(array $data): array
    {
        try {
            // Generate unique transaction ID if not provided
            $tranId = $data['transaction_id'] ?? 'SSL-'.uniqid().'-'.time();
            $postData = [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'total_amount' => round($data['amount'], 2),
                'currency' => $data['currency'] ?? 'BDT',
                'tran_id' => $tranId,
                'success_url' => $data['success_url'] ?? '',
                'fail_url' => $data['fail_url'] ?? '',
                'cancel_url' => $data['cancel_url'] ?? '',
                'ipn_url' => $data['ipn_url'] ?? '',
                'cus_name' => $data['customer_name'] ?? 'Customer',
                'cus_email' => $data['email'] ?? 'customer@example.com',
                'cus_phone' => $data['phone'] ?? '01700000000',
                'cus_add1' => $data['address'] ?? 'N/A',
                'cus_city' => $data['city'] ?? 'Dhaka',
                'cus_country' => $data['country'] ?? 'Bangladesh',
                'cus_postcode' => $data['post_code'] ?? '1000',
                'cus_state' => $data['state'] ?? 'Dhaka',
                'shipping_method' => 'NO',
                'product_name' => $data['product_name'] ?? 'Subscription',
                'product_category' => $data['product_category'] ?? 'Subscription',
                'product_profile' => $data['product_profile'] ?? 'general',
                'num_of_item' => 1,
            ];

            // Add EMI options if applicable
            if (isset($data['emi_option'])) {
                $postData['emi_option'] = $data['emi_option'];
            }

            // Make API request with proper error handling
            $response = Http::asForm()
                ->timeout(30)
                ->retry(3, 100)
                ->post($this->baseUrl.'/gwprocess/v4/api.php', $postData);

            if ($response->failed()) {
                Log::error('SSLCommerz HTTP failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to connect to SSLCommerz',
                ];
            }

            $result = $response->json();

            Log::info('SSLCommerz init response', ['result' => $result]);

            if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'gateway_url' => $result['GatewayPageURL'],
                    'sessionkey' => $result['sessionkey'] ?? null,
                    'tran_id' => $result['tran_id'] ?? $tranId,
                ];
            } else {
                $errorMsg = $result['failedreason'] ?? 'Payment initialization failed';
                if (isset($result['error'])) {
                    $errorMsg = $result['error'];
                }

                return [
                    'success' => false,
                    'message' => $errorMsg,
                ];
            }

        } catch (Exception $e) {
            Log::error('SSLCommerz init error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment gateway error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Validate payment (IPN validation)
     */
    public function validatePayment(Request $request): array
    {
        try {
            $validatedId = $request->input('val_id');
            $tranId = $request->input('tran_id');
            $amount = $request->input('amount');
            $currency = $request->input('currency');
            $status = $request->input('status');

            Log::info('SSLCommerz validation started', [
                'val_id' => $validatedId,
                'tran_id' => $tranId,
                'status' => $status,
            ]);

            if ($status !== 'VALID' && $status !== 'VALIDATED') {
                return [
                    'success' => false,
                    'message' => 'Payment not validated',
                ];
            }

            // Verify with SSLCommerz API
            $validationUrl = $this->baseUrl.'/validator/api/validationserverAPI.php';

            $response = Http::get($validationUrl, [
                'val_id' => $validatedId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $validation = $response->json();

                Log::info('SSLCommerz validation response', ['validation' => $validation]);

                if (isset($validation['status']) && $validation['status'] === 'VALID') {
                    return [
                        'success' => true,
                        'data' => $validation,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Validation failed',
            ];

        } catch (Exception $e) {
            Log::error('SSLCommerz validation error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Validation error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Query transaction status
     */
    public function queryTransaction($tranId): array
    {
        try {
            $queryUrl = $this->baseUrl.'/validator/api/merchantTransIDvalidationAPI.php';

            $response = Http::get($queryUrl, [
                'merchant_trans_id' => $tranId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Query failed',
            ];

        } catch (Exception $e) {
            Log::error('SSLCommerz query error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Query error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Initiate refund
     */
    public function initiateRefund($tranId, $amount, $reason = ''): array
    {
        try {
            $refundUrl = $this->baseUrl.'/validator/api/merchantTransIDvalidationAPI.php';

            $response = Http::post($refundUrl, [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'refund_amount' => $amount,
                'refund_remarks' => $reason,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'success') {
                    return [
                        'success' => true,
                        'refund_id' => $result['refund_id'] ?? null,
                        'data' => $result,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Refund failed',
            ];

        } catch (Exception $e) {
            Log::error('SSLCommerz refund error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Refund error: '.$e->getMessage(),
            ];
        }
    }
}
