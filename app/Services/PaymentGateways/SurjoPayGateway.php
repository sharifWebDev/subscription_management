<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SurjoPayGateway
{
    protected $config;

    protected $baseUrl;

    public function __construct()
    {
        $this->config = PaymentGateway::where('code', 'surjopay')
            ->where('is_active', true)
            ->first();

        if (! $this->config) {
            throw new Exception('SurjoPay gateway not configured');
        }

        $this->baseUrl = $this->config->base_url ?? 'https://engine.surjopay.com';
    }

    /**
     * Create payment
     */
    public function createPayment(array $data): array
    {
        try {
            $transactionId = 'TXN'.time().rand(100, 999);

            $postData = [
                'merchant_key' => $this->config->api_key,
                'merchant_id' => $this->config->merchant_id,
                'merchant_password' => $this->config->api_secret,
                'transaction_id' => $transactionId,
                'transaction_amount' => number_format($data['amount'], 2, '.', ''),
                'transaction_currency' => $data['currency'] ?? 'BDT',
                'transaction_type' => 'Purchase',
                'transaction_date' => now()->format('Y-m-d H:i:s'),
                'customer_name' => $data['customer_name'] ?? 'Customer',
                'customer_email' => $data['email'] ?? '',
                'customer_phone' => $data['phone'] ?? '',
                'customer_address_1' => $data['address'] ?? '',
                'customer_city' => $data['city'] ?? '',
                'customer_postcode' => $data['postcode'] ?? '',
                'customer_country' => $data['country'] ?? 'BD',
                'success_url' => route('payment.surjopay.success'),
                'fail_url' => route('payment.surjopay.fail'),
                'cancel_url' => route('payment.surjopay.cancel'),
                'ipn_url' => route('payment.surjopay.ipn'),
                'custom_data' => json_encode([
                    'payment_master_id' => $data['payment_master_id'],
                    'plan_id' => $data['plan_id'] ?? null,
                    'user_id' => $data['user_id'] ?? null,
                ]),
            ];

            // Generate signature
            $postData['signature'] = $this->generateSignature($postData);

            $response = Http::asForm()->post($this->baseUrl.'/payment/create', $postData);

            if (! $response->successful()) {
                throw new Exception('SurjoPay payment creation failed: '.$response->body());
            }

            $result = $response->json();

            if ($result['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'transaction_id' => $transactionId,
                    'payment_id' => $result['payment_id'] ?? null,
                ];
            } else {
                throw new Exception($result['message'] ?? 'Payment creation failed');
            }

        } catch (Exception $e) {
            Log::error('SurjoPay payment creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $postData = [
                'merchant_key' => $this->config->api_key,
                'merchant_id' => $this->config->merchant_id,
                'merchant_password' => $this->config->api_secret,
                'transaction_id' => $transactionId,
            ];

            $postData['signature'] = $this->generateSignature($postData);

            $response = Http::asForm()->post($this->baseUrl.'/payment/verify', $postData);

            if (! $response->successful()) {
                throw new Exception('SurjoPay verification failed: '.$response->body());
            }

            $result = $response->json();

            return [
                'success' => $result['status'] === 'SUCCESS',
                'status' => $result['status'],
                'amount' => $result['amount'] ?? null,
                'transaction_id' => $result['transaction_id'] ?? null,
                'payment_id' => $result['payment_id'] ?? null,
                'full_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('SurjoPay verification failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate signature
     */
    protected function generateSignature(array $data): string
    {
        $signatureData = $data['merchant_key'].$data['merchant_id'].$data['transaction_id'].$data['transaction_amount'];

        return hash('sha256', $signatureData.$this->config->api_secret);
    }
}
