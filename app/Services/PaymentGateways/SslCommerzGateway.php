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
        $this->storeId = config('payment.gateways.sslcommerz.store_id');
        $this->storePassword = config('payment.gateways.sslcommerz.store_password');
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
            $postData = [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'total_amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'BDT',
                'tran_id' => $data['transaction_id'],
                'success_url' => route('payment.sslcommerz.success'),
                'fail_url' => route('payment.sslcommerz.fail'),
                'cancel_url' => route('payment.sslcommerz.cancel'),
                'ipn_url' => route('payment.sslcommerz.ipn'),
                'cus_name' => $data['customer_name'] ?? 'Customer',
                'cus_email' => $data['email'] ?? '',
                'cus_phone' => $data['phone'] ?? '',
                'cus_add1' => $data['address'] ?? 'N/A',
                'cus_city' => $data['city'] ?? 'Dhaka',
                'cus_country' => $data['country'] ?? 'Bangladesh',
                'shipping_method' => 'NO',
                'product_name' => $data['product_name'] ?? 'Subscription',
                'product_category' => $data['product_category'] ?? 'Subscription',
                'product_profile' => $data['product_profile'] ?? 'general',
            ];

            // Add optional parameters
            if (isset($data['emi_option'])) {
                $postData['emi_option'] = $data['emi_option'];
            }

            if (isset($data['emi_max_inst_option'])) {
                $postData['emi_max_inst_option'] = $data['emi_max_inst_option'];
            }

            if (isset($data['emi_selected_inst'])) {
                $postData['emi_selected_inst'] = $data['emi_selected_inst'];
            }

            // Make API request
            $response = Http::asForm()->post($this->baseUrl.'/gwprocess/v4/api.php', $postData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'SUCCESS') {
                    return [
                        'success' => true,
                        'gateway_url' => $result['GatewayPageURL'],
                        'sessionkey' => $result['sessionkey'] ?? null,
                        'tran_id' => $result['tran_id'] ?? $data['transaction_id'],
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['failedreason'] ?? 'Payment initialization failed',
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to connect to SSLCommerz',
            ];

        } catch (Exception $e) {
            Log::error('SSLCommerz init error: '.$e->getMessage());

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
