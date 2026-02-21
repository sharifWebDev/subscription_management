<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashGateway
{
    protected $config;

    protected $token;

    protected $baseUrl;

    public function __construct()
    {
        $this->config = PaymentGateway::where('code', 'bkash')
            ->where('is_active', true)
            ->first();

        if (! $this->config) {
            throw new Exception('bKash gateway not configured');
        }

        $this->baseUrl = $this->config->base_url ?? 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    /**
     * Get token
     */
    protected function getToken(): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = Http::withHeaders([
            'username' => $this->config->api_key,
            'password' => $this->config->api_secret,
        ])->post($this->baseUrl.'/tokenized/checkout/token/grant', [
            'app_key' => $this->config->api_key,
            'app_secret' => $this->config->api_secret,
        ]);

        if (! $response->successful()) {
            throw new Exception('Failed to get bKash token');
        }
        \Log::info('bKash token: ',['token' => $response->json()]);

        // if (app()->environment('local')) {
            $this->token = 'fef3feqwsf';
        // }


        // $this->token = $response->json()['id_token'];

        return $this->token;
    }

    /**
     * Create payment
     */
    public function createPayment(array $data): array
    {
        try {
            $token = $this->getToken();

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->config->api_key,
            ])->post($this->baseUrl.'/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $data['payer_reference'] ?? '12345',
                'callbackURL' => route('payment.bkash.callback'),
                'amount' => number_format($data['amount'], 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => 'INV'.time(),
                'metadata' => json_encode([
                    'payment_master_id' => $data['payment_master_id'],
                    'plan_id' => $data['plan_id'] ?? null,
                    'user_id' => $data['user_id'] ?? null,
                ]),
            ]);

            if (! $response->successful()) {
                throw new Exception('bKash payment creation failed');
            }

            $result = $response->json();

            if ($result['statusCode'] === '0000') {
                return [
                    'success' => true,
                    'payment_id' => $result['paymentID'],
                    'bkash_url' => $result['bkashURL'],
                ];
            } else {
                throw new Exception($result['statusMessage'] ?? 'Payment creation failed');
            }

        } catch (Exception $e) {
            Log::error('bKash payment creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Execute payment
     */
    public function executePayment(string $paymentId): array
    {
        try {
            $token = $this->getToken();

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->config->api_key,
            ])->post($this->baseUrl.'/tokenized/checkout/execute', [
                'paymentID' => $paymentId,
            ]);

            if (! $response->successful()) {
                throw new Exception('bKash payment execution failed');
            }

            $result = $response->json();

            if ($result['transactionStatus'] === 'Completed') {
                return [
                    'success' => true,
                    'trx_id' => $result['trxID'],
                    'amount' => $result['amount'],
                    'payment_id' => $result['paymentID'],
                    'full_response' => $result,
                ];
            } else {
                throw new Exception($result['statusMessage'] ?? 'Payment execution failed');
            }

        } catch (Exception $e) {
            Log::error('bKash payment execution failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Query payment
     */
    public function queryPayment(string $paymentId): array
    {
        try {
            $token = $this->getToken();

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->config->api_key,
            ])->post($this->baseUrl.'/tokenized/checkout/payment/status', [
                'paymentID' => $paymentId,
            ]);

            if (! $response->successful()) {
                throw new Exception('bKash payment query failed');
            }

            $result = $response->json();

            return [
                'success' => true,
                'status' => $result['transactionStatus'],
                'amount' => $result['amount'] ?? null,
                'trx_id' => $result['trxID'] ?? null,
                'full_response' => $result,
            ];

        } catch (Exception $e) {
            Log::error('bKash payment query failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
