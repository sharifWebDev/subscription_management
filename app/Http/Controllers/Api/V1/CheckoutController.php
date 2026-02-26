<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentMaster;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionOrderItem;
use App\Models\User;
use App\Services\OTPService;
use App\Services\PaymentGateways\BkashGateway;
use App\Services\PaymentGateways\NagadGateway;
use App\Services\PaymentGateways\PayPalGateway;
use App\Services\PaymentGateways\RocketGateway;
use App\Services\PaymentGateways\SslCommerzGateway;
use App\Services\PaymentGateways\StripeGateway;
use App\Services\PaymentGateways\SurjoPayGateway;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $otpService;
    protected $subscriptionService;
    protected $paymentService;
    protected $stripeGateway;
    protected $paypalGateway;
    protected $surjoPayGateway;
    protected $bkashGateway;
    protected $nagadGateway;
    protected $rocketGateway;
    protected $sslCommerzGateway;

    public function __construct(
        OTPService $otpService,
        SubscriptionService $subscriptionService,
        PaymentService $paymentService,
        StripeGateway $stripeGateway,
        PayPalGateway $paypalGateway,
        SurjoPayGateway $surjoPayGateway,
        BkashGateway $bkashGateway,
        NagadGateway $nagadGateway,
        RocketGateway $rocketGateway,
        SslCommerzGateway $sslCommerzGateway
    ) {
        $this->otpService = $otpService;
        $this->subscriptionService = $subscriptionService;
        $this->paymentService = $paymentService;
        $this->stripeGateway = $stripeGateway;
        $this->paypalGateway = $paypalGateway;
        $this->surjoPayGateway = $surjoPayGateway;
        $this->bkashGateway = $bkashGateway;
        $this->nagadGateway = $nagadGateway;
        $this->rocketGateway = $rocketGateway;
        $this->sslCommerzGateway = $sslCommerzGateway;
    }

    /**
     * Initialize checkout
     */
    public function initialize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'price_id' => 'required|exists:plan_prices,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $plan = Plan::with(['prices' => function ($q) use ($request) {
                $q->where('id', $request->price_id);
            }])->findOrFail($request->plan_id);

            $price = $plan->prices->first();

            if (!$price) {
                throw new Exception('Selected price not found');
            }

            // Calculate amounts
            $amount = $price->amount;
            $taxRate = config('app.tax_rate', 10);
            $tax = $amount * ($taxRate / 100);
            $total = $amount + $tax;

            return response()->json([
                'success' => true,
                'data' => [
                    'plan' => [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'description' => $plan->description,
                    ],
                    'price' => [
                        'id' => $price->id,
                        'amount' => $amount,
                        'currency' => $price->currency,
                        'interval' => $price->interval,
                        'interval_count' => $price->interval_count,
                        'formatted_amount' => $this->formatMoney($amount, $price->currency),
                    ],
                    'totals' => [
                        'subtotal' => $amount,
                        'tax_rate' => $taxRate,
                        'tax' => $tax,
                        'total' => $total,
                        'formatted_subtotal' => $this->formatMoney($amount, $price->currency),
                        'formatted_tax' => $this->formatMoney($tax, $price->currency),
                        'formatted_total' => $this->formatMoney($total, $price->currency),
                    ],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Checkout initialization failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize checkout',
            ], 500);
        }
    }

    /**
     * Send OTP for login/registration
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->generateAndSendOtp($request->email);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'expires_at' => $result['expires_at'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 500);
    }

    /**
     * Verify OTP and process checkout
     */
    public function verifyOtpAndCheckout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'plan_id' => 'required|exists:plans,id',
            'price_id' => 'required|exists:plan_prices,id',
            'payment_method' => 'required|string',
            'gateway' => 'required|string',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'billing_address' => 'nullable|array',
            'payment_details' => 'nullable|array',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Verify OTP and get/create user
            $otpResult = $this->otpService->verifyOtpAndCreateUser(
                $request->email,
                $request->otp,
                [
                    'name' => $request->name,
                    'phone' => $request->phone,
                ]
            );

            if (!$otpResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $otpResult['message'],
                ], 400);
            }

            $user = $otpResult['user'];

            // Create order
            $order = $this->createOrder($user, $request);
            $order = $order->load('items');

            // Create payment master
            $paymentMaster = $this->createPaymentMaster($user, $order, $request);

            // Process payment based on gateway
            $paymentResult = $this->processGatewayPayment($paymentMaster, $order, $user, $request);

            if (!$paymentResult['success']) {
                throw new Exception($paymentResult['message'] ?? 'Payment processing failed');
            }

            // If payment is immediately successful (like card), create subscription
            if ($paymentResult['status'] === 'completed') {
                $subscription = $this->createSubscription($user, $order, $request, $paymentMaster);

                // Update order with subscription
                $order->items()->update([
                    'subscription_id' => $subscription->id,
                    'subscription_status' => 'active',
                    'processed_at' => Carbon::now(),
                ]);

                $order->update([
                    'status' => 'completed',
                    'processed_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Checkout processed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                    'requires_redirect' => $paymentResult['requires_redirect'] ?? false,
                    'status' => $paymentResult['status'] ?? 'pending',
                ],
            ];

            // Add redirect URL if needed
            if (isset($paymentResult['redirect_url'])) {
                $response['data']['redirect_url'] = $paymentResult['redirect_url'];
            }

            // Add client secret for Stripe
            if (isset($paymentResult['client_secret'])) {
                $response['data']['client_secret'] = $paymentResult['client_secret'];
            }

            // Add authentication token for new users
            if ($otpResult['is_new_user']) {
                $response['data']['token'] = $otpResult['token'];
                $response['data']['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }

            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process authenticated checkout (logged in user)
     */
/**
 * Process authenticated checkout (logged in user)
 */
public function processAuthenticated(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'plan_id' => 'required|exists:plans,id',
        'price_id' => 'required|exists:plan_prices,id',
        'payment_method' => 'required|string',
        'gateway' => 'required|string',
        'payment_method_id' => 'nullable|string',
        'payment_details' => 'nullable|array',
        'save_payment_method' => 'boolean',
        'terms' => 'required|accepted',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {
        $user = Auth::user();

        // Create order
        $order = $this->createOrder($user, $request);
        $order = $order->load('items');

        // Create payment master
        $paymentMaster = $this->createPaymentMaster($user, $order, $request);

        // Process payment based on gateway
        $paymentResult = $this->processGatewayPayment($paymentMaster, $order, $user, $request);

        if (!$paymentResult['success']) {
            throw new Exception($paymentResult['message'] ?? 'Payment processing failed');
        }

        // If payment is immediately successful, create subscription
        if ($paymentResult['status'] === 'completed') {
            $subscription = $this->createSubscription($user, $order, $request, $paymentMaster);

            // Update order with subscription
            $order->items()->update([
                'subscription_id' => $subscription->id,
                'subscription_status' => 'active',
                'processed_at' => Carbon::now(),
            ]);

            $order->update([
                'status' => 'completed',
                'processed_at' => Carbon::now(),
            ]);

            // Save payment method if requested
            if ($request->boolean('save_payment_method') && isset($paymentResult['payment_method_id'])) {
                $this->savePaymentMethod($user, $paymentResult, $request);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                    'subscription_id' => $subscription->id,
                    'status' => 'completed',
                ],
            ]);
        }

        // For payments that require further action (like 3D Secure)
        if ($paymentResult['status'] === 'requires_action') {
            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Payment requires authentication',
                'data' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                    'requires_action' => true,
                    'status' => 'requires_action',
                    'client_secret' => $paymentResult['client_secret'] ?? null,
                    'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
                    'transaction_id' => $paymentResult['transaction_id'] ?? null,
                ],
            ];

            // Add redirect URL if needed (for gateways that redirect)
            if (isset($paymentResult['redirect_url'])) {
                $response['data']['redirect_url'] = $paymentResult['redirect_url'];
            }

            return response()->json($response);
        }

        // For pending payments (like bank transfers)
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $paymentResult['message'] ?? 'Payment initiated successfully',
            'data' => [
                'order_id' => $order->id,
                'payment_master_id' => $paymentMaster->id,
                'requires_action' => false,
                'status' => $paymentResult['status'] ?? 'pending',
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
            ],
        ]);

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Authenticated checkout failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
    /**
     * Handle payment callback/success
     */
    public function handleCallback(Request $request, $gateway = null)
    {
        $gateway = $gateway ?? $request->route('gateway') ?? $request->get('gateway');
        try {
            Log::info('Payment callback received', [
                'gateway' => $gateway,
                'method' => $request->method(),
                'data' => $request->all(),
                'has_session' => $request->hasSession() ? 'yes' : 'no'
            ]);

            switch ($gateway) {
                case 'stripe':
                    return $this->handleStripeCallback($request);
                case 'paypal':
                    return $this->handlePayPalCallback($request);
                case 'surjopay':
                    return $this->handleSurjoPayCallback($request);
                case 'sslcommerz':
                    return $this->handleSslCommerzCallback($request);
                case 'bkash':
                    return $this->handleBkashCallback($request);
                case 'rocket':
                    return $this->handleRocketCallback($request);
                case 'nagad':
                    return $this->handleNagadCallback($request);
                case 'cash':
                case 'bank_transfer':
                    return response()->json([
                        'success' => true,
                        'message' => 'Your payment is being processed. We will notify you once confirmed.',
                        'status' => 'pending'
                    ]);
                default:
                    throw new Exception('Unsupported gateway');
            }
        } catch (Exception $e) {
            Log::error('Payment callback failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process gateway payment
     */
  /**
 * Process gateway payment
 */
protected function processGatewayPayment(PaymentMaster $paymentMaster, SubscriptionOrder $order, User $user, Request $request): array
{
    $gateway = $request->gateway;

    $paymentData = [
        'payment_master_id' => $paymentMaster->id,
        'amount' => $order->total_amount,
        'currency' => $order->currency,
        'user_id' => $user->id,
        'customer_id' => $user->id,
        'email' => $user->email,
        'name' => $request->name ?? $user->name,
        'phone' => $request->phone ?? $user->phone,
        'plan_id' => $request->plan_id,
        'price_id' => $request->price_id,
        'description' => 'Subscription: ' . ($order->items->first()->plan_name ?? 'Plan'),
        'customer_name' => $request->name ?? '',
        'address' => $request->billing_address['address'] ?? 'N/A',
        'city' => $request->billing_address['city'] ?? 'Dhaka',
        'country' => $request->billing_address['country'] ?? 'Bangladesh',
        'product_name' => $order->items->first()->plan_name ?? 'Subscription',
        'product_category' => 'Subscription',
        'transaction_id' => $this->generateTransactionId($gateway),
    ];

    // Pass payment_method_id if provided
    if ($request->has('payment_method_id')) {
        $paymentData['payment_method_id'] = $request->payment_method_id;
    }

    // Pass payment_details if provided
    if ($request->has('payment_details')) {
        $paymentData['payment_details'] = $request->payment_details;
    }

    switch ($gateway) {
        case 'stripe':
            return $this->processStripePayment($paymentData, $request);
        case 'paypal':
            return $this->processPayPalPayment($paymentData, $request);
        case 'surjopay':
            return $this->processSurjoPayPayment($paymentData, $request);
        case 'sslcommerz':
            return $this->processSslCommerzPayment($paymentData, $request);
        case 'bkash':
            return $this->processBkashPayment($paymentData, $request);
        case 'rocket':
            return $this->processRocketPayment($paymentData, $request);
        case 'nagad':
            return $this->processNagadPayment($paymentData, $request);
        case 'bank_transfer':
            return $this->processBankTransfer($paymentData);
        case 'cash':
            return $this->processCashPayment($paymentData);
        default:
            throw new Exception("Unsupported gateway: {$gateway}");
    }
}

    /**
     * Process Stripe payment
     */
/**
 * Process Stripe payment
 */
protected function processStripePayment(array $data, Request $request): array
{
    try {
        // Case 1: Using saved payment method ID
        if ($request->payment_method_id) {
            $result = $this->stripeGateway->createPaymentIntentWithSavedMethod($data, $request->payment_method_id);
        }
        // Case 2: Confirming existing payment intent
        elseif (isset($request->payment_details['payment_intent_id']) && isset($request->payment_details['payment_method_id'])) {
            $result = $this->stripeGateway->confirmPaymentIntent(
                $request->payment_details['payment_intent_id'],
                $request->payment_details['payment_method_id']
            );
        }
        // Case 3: Creating new payment intent with payment method from details
        elseif (isset($request->payment_details['payment_method_id'])) {
            $result = $this->stripeGateway->createPaymentIntentWithSavedMethod($data, $request->payment_details['payment_method_id']);
        }
        // Case 4: Creating new payment intent (will be confirmed via client)
        else {
            $result = $this->stripeGateway->createPaymentIntent($data);
        }

        if (!$result['success']) {
            return $result;
        }

        // Create transaction
        $transaction = PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $result['intent_id'] ?? ($result['intent']->id ?? null),
            'type' => 'payment',
            'payment_method' => 'card',
            'payment_gateway' => 'stripe',
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $result['status'] ?? 'requires_confirmation',
            'gateway_response' => json_encode($result),
            'initiated_at' => Carbon::now(),
        ]);

        // If client secret is returned (requires action on client side)
        if (isset($result['client_secret'])) {
            return [
                'success' => true,
                'status' => 'requires_action',
                'requires_redirect' => false,
                'client_secret' => $result['client_secret'],
                'payment_intent_id' => $transaction->transaction_id,
                'transaction_id' => $transaction->id,
            ];
        }

        // If payment is already completed
        if ($result['status'] === 'completed') {
            // Update payment master
            $paymentMaster = PaymentMaster::find($data['payment_master_id']);
            $paymentMaster->update([
                'status' => 'paid',
                'paid_amount' => $data['amount'],
                'paid_at' => Carbon::now(),
            ]);

            $transaction->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);
        }

        return [
            'success' => true,
            'status' => $result['status'],
            'transaction_id' => $transaction->id,
            'payment_method_id' => $result['payment_method_id'] ?? null,
        ];

    } catch (Exception $e) {
        Log::error('Stripe payment processing error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'data' => $data
        ]);

        return [
            'success' => false,
            'message' => 'Stripe payment processing failed: ' . $e->getMessage(),
        ];
    }
}

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(array $data, Request $request): array
    {
        $result = $this->paypalGateway->createOrder($data);

        if (!$result['success']) {
            return $result;
        }

        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $result['order_id'],
            'type' => 'payment',
            'payment_method' => 'paypal',
            'payment_gateway' => 'paypal',
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => 'pending',
            'gateway_response' => json_encode($result),
            'initiated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'status' => 'requires_action',
            'requires_redirect' => true,
            'redirect_url' => $result['approval_url'],
            'order_id' => $result['order_id'],
        ];
    }

    /**
     * Process SurjoPay payment
     */
    protected function processSurjoPayPayment(array $data, Request $request): array
    {
        $data['customer_name'] = $data['customer_name'];
        $data['email'] = $data['email'];
        $data['phone'] = $data['phone'];

        $result = $this->surjoPayGateway->createPayment($data);

        if (!$result['success']) {
            return $result;
        }

        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $result['transaction_id'],
            'type' => 'payment',
            'payment_method' => 'surjopay',
            'payment_gateway' => 'surjopay',
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'BDT',
            'status' => 'pending',
            'gateway_response' => json_encode($result),
            'initiated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'status' => 'requires_action',
            'requires_redirect' => true,
            'redirect_url' => $result['payment_url'],
            'transaction_id' => $result['transaction_id'],
        ];
    }

    /**
     * Process SSLCommerz payment
     */
    protected function processSslCommerzPayment(array $data, Request $request): array
    {
        // Create transaction record first
        $transaction = PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $data['transaction_id'],
            'type' => 'payment',
            'payment_method' => 'sslcommerz',
            'payment_gateway' => 'sslcommerz',
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'BDT',
            'status' => 'pending',
            'gateway_response' => json_encode([]),
            'initiated_at' => Carbon::now(),
        ]);

        // Initialize payment with SSLCommerz
        $result = $this->sslCommerzGateway->initPayment([
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'BDT',
            'transaction_id' => $data['transaction_id'],
            'customer_name' => $data['customer_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'country' => $data['country'],
            'product_name' => $data['product_name'],
            'product_category' => $data['product_category'],
        ]);

        if (!$result['success']) {
            $transaction->update([
                'status' => 'failed',
                'gateway_response' => json_encode($result),
                'failed_at' => Carbon::now(),
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'SSLCommerz payment initialization failed',
            ];
        }

        // Update transaction with gateway response
        $updateData = [
            'gateway_response' => json_encode($result),
        ];

        // If the column exists, update it
        if (Schema::hasColumn('payment_transactions', 'gateway_transaction_id')) {
            $updateData['gateway_transaction_id'] = $result['tran_id'] ?? null;
        }

        $transaction->update($updateData);

        return [
            'success' => true,
            'status' => 'requires_action',
            'requires_redirect' => true,
            'redirect_url' => $result['gateway_url'],
            'transaction_id' => $transaction->id,
            'gateway_transaction_id' => $result['tran_id'] ?? null,
        ];
    }

    /**
     * Process bKash payment
     */
    protected function processBkashPayment(array $data, Request $request): array
    {
        $data['payer_reference'] = $request->phone ?? $request->email;

        $result = $this->bkashGateway->createPayment($data);

        if (!$result['success']) {
            return $result;
        }

        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $result['payment_id'],
            'type' => 'payment',
            'payment_method' => 'bkash',
            'payment_gateway' => 'bkash',
            'amount' => $data['amount'],
            'currency' => 'BDT',
            'status' => 'pending',
            'gateway_response' => json_encode($result),
            'initiated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'status' => 'requires_action',
            'requires_redirect' => true,
            'redirect_url' => $result['bkash_url'],
            'payment_id' => $result['payment_id'],
        ];
    }

    /**
     * Process Rocket payment
     */
    protected function processRocketPayment(array $data, Request $request): array
    {
        // Create transaction record
        $transaction = PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $data['transaction_id'],
            'type' => 'payment',
            'payment_method' => 'rocket',
            'payment_gateway' => 'rocket',
            'amount' => $data['amount'],
            'currency' => 'BDT',
            'status' => 'pending',
            'gateway_response' => json_encode(['method' => 'rocket', 'details' => $request->payment_details ?? []]),
            'initiated_at' => Carbon::now(),
        ]);

        // For Rocket, we need manual verification with transaction ID
        if (isset($request->payment_details['transaction_id'])) {
            return [
                'success' => true,
                'status' => 'pending',
                'requires_redirect' => false,
                'message' => 'Please complete the payment via Rocket and provide the transaction ID',
                'transaction_id' => $transaction->id,
            ];
        }

        return [
            'success' => true,
            'status' => 'requires_action',
            'requires_redirect' => false,
            'instructions' => [
                'merchant_number' => '01812345678',
                'payment_type' => 'rocket',
                'message' => 'Send payment to this Rocket number and enter the transaction ID',
            ],
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Process Nagad payment
     */
    protected function processNagadPayment(array $data, Request $request): array
    {
        // Create transaction record
        $transaction = PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => $data['transaction_id'],
            'type' => 'payment',
            'payment_method' => 'nagad',
            'payment_gateway' => 'nagad',
            'amount' => $data['amount'],
            'currency' => 'BDT',
            'status' => 'pending',
            'gateway_response' => json_encode(['method' => 'nagad', 'details' => $request->payment_details ?? []]),
            'initiated_at' => Carbon::now(),
        ]);

        // For Nagad, we need manual verification with transaction ID
        if (isset($request->payment_details['transaction_id'])) {
            return [
                'success' => true,
                'status' => 'pending',
                'requires_redirect' => false,
                'message' => 'Please complete the payment via Nagad and provide the transaction ID',
                'transaction_id' => $transaction->id,
            ];
        }

        return [
            'success' => true,
            'status' => 'requires_action',
            'requires_redirect' => false,
            'instructions' => [
                'merchant_number' => '01612345678',
                'payment_type' => 'nagad',
                'message' => 'Send payment to this Nagad number and enter the transaction ID',
            ],
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Process bank transfer
     */
    protected function processBankTransfer(array $data): array
    {
        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => 'BANK-' . time(),
            'type' => 'payment',
            'payment_method' => 'bank_transfer',
            'payment_gateway' => 'bank_transfer',
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => 'pending',
            'gateway_response' => json_encode(['method' => 'bank_transfer']),
            'initiated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Bank transfer initiated. Please complete the transfer using the bank details provided.',
        ];
    }

    /**
     * Process Cash payment
     */
    protected function processCashPayment(array $data): array
    {
        // Create transaction record
        $transaction = PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'user_id' => $data['user_id'],
            'transaction_id' => 'CASH-' . time(),
            'type' => 'payment',
            'payment_method' => 'cash',
            'payment_gateway' => 'cash',
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'status' => 'pending',
            'gateway_response' => json_encode(['method' => 'cash']),
            'initiated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'status' => 'pending',
            'requires_redirect' => false,
            'message' => 'Please have the exact amount ready for cash payment. Our representative will contact you to arrange payment.',
            'transaction_id' => $transaction->id,
        ];
    }


    /**
 * Confirm Stripe payment after 3D Secure
 */
public function confirmStripePayment(Request $request): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'payment_intent_id' => 'required|string',
        'transaction_id' => 'required|exists:payment_transactions,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        $user = Auth::user();

        // Find the transaction
        $transaction = PaymentTransaction::with('paymentMaster')
            ->where('id', $request->transaction_id)
            ->where('transaction_id', $request->payment_intent_id)
            ->first();

        if (!$transaction) {
            throw new Exception('Transaction not found');
        }

        // Confirm the payment with Stripe
        $result = $this->stripeGateway->confirmPaymentIntent($request->payment_intent_id);

        if (!$result['success']) {
            throw new Exception($result['message'] ?? 'Payment confirmation failed');
        }

        if ($result['status'] === 'completed') {
            DB::beginTransaction();

            // Update transaction
            $transaction->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'gateway_response' => json_encode($result),
            ]);

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            $paymentMaster->update([
                'status' => 'paid',
                'paid_amount' => $transaction->amount,
                'paid_at' => Carbon::now(),
            ]);

            // Activate subscription
            $metadata = json_decode($paymentMaster->metadata ?? '{}', true);
            $orderId = $metadata['order_id'] ?? null;

            $subscriptionId = null;
            if ($orderId) {
                $order = SubscriptionOrder::with('items')->find($orderId);
                if ($order && !$order->processed_at) {
                    $subscription = $this->activateOrderSubscription($order, $paymentMaster);
                    $subscriptionId = $subscription->id ?? null;
                }
            }

            // Save payment method if user wants to save it
            if ($request->boolean('save_payment_method') && isset($result['payment_method_id'])) {
                //request merge type, gateway
                $request->merge([
                    'payment_method' => 'stripe',
                    'gateway' => 'stripe',
                ]);
                $this->savePaymentMethod($user, $result, $request);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'data' => [
                    'subscription_id' => $subscriptionId,
                    'order_id' => $orderId,
                    'transaction_id' => $transaction->id,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment not completed',
            'status' => $result['status'],
        ], 400);

    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Stripe payment confirmation failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Payment confirmation failed: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * Handle Stripe callback
     */
    protected function handleStripeCallback(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');

        $transaction = PaymentTransaction::where('transaction_id', $paymentIntentId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($request->get('redirect_status') === 'succeeded') {
            DB::beginTransaction();
            try {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);

                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $transaction->amount,
                    'paid_at' => Carbon::now(),
                ]);

                // Activate subscription
                $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
                if ($order && !$order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id
                    ]
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Stripe callback error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.'
        ], 400);
    }

    /**
     * Handle PayPal callback
     */
    protected function handlePayPalCallback(Request $request)
    {
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');

        if (!$token || !$PayerID) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid PayPal callback'
            ], 400);
        }

        $transaction = PaymentTransaction::where('transaction_id', $token)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // Capture payment
        $result = $this->paypalGateway->captureOrder($token);

        if ($result['success']) {
            DB::beginTransaction();
            try {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'gateway_response' => json_encode($result),
                ]);

                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $transaction->amount,
                    'paid_at' => Carbon::now(),
                ]);

                // Activate subscription
                $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
                if ($order && !$order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                if (!$request->expectsJson()) {
                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id
                    ]
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('PayPal callback error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment capture failed. Please try again.'
        ], 400);
    }

    /**
     * Handle SurjoPay callback
     */
    protected function handleSurjoPayCallback(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status');

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->orWhere('gateway_transaction_id', $transactionId)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($status === 'COMPLETED') {
            // Verify payment
            $verification = $this->surjoPayGateway->verifyPayment($transactionId);

            if ($verification['success']) {
                DB::beginTransaction();
                try {
                    $transaction->update([
                        'status' => 'completed',
                        'completed_at' => Carbon::now(),
                        'gateway_response' => json_encode($verification),
                    ]);

                    $paymentMaster = $transaction->paymentMaster;
                    $paymentMaster->update([
                        'status' => 'paid',
                        'paid_amount' => $transaction->amount,
                        'paid_at' => Carbon::now(),
                    ]);

                    // Activate subscription
                    $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
                    if ($order && !$order->processed_at) {
                        $this->activateOrderSubscription($order, $paymentMaster);
                    }

                    DB::commit();

                    if (!$request->expectsJson()) {
                        return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful! Your subscription is now active.',
                        'data' => [
                            'subscription_id' => $order?->items()->first()?->subscription_id,
                            'order_id' => $order?->id
                        ]
                    ]);
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('SurjoPay callback error: ' . $e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment verification failed. Please contact support.'
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed.'
        ], 400);
    }

    /**
     * Handle SSLCommerz callback
     */
    protected function handleSslCommerzCallback(Request $request)
    {
        $tranId = $request->input('tran_id') ?? $request->get('tran_id');

        // Find transaction by various methods
        $transaction = $this->findTransaction($tranId);

        if (!$transaction) {
            Log::error('SSLCommerz callback: Transaction not found', ['tran_id' => $tranId]);
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // Get callback type from URL
        $callbackType = $request->get('callback_type', 'success');

        if (str_contains($request->url(), 'success') || $callbackType === 'success') {
            return $this->processSslCommerzSuccess($transaction, $request);
        } elseif (str_contains($request->url(), 'fail') || $callbackType === 'fail') {
            return $this->processSslCommerzFail($transaction, $request);
        } elseif (str_contains($request->url(), 'cancel') || $callbackType === 'cancel') {
            return $this->processSslCommerzCancel($transaction, $request);
        } elseif (str_contains($request->url(), 'ipn') || $callbackType === 'ipn') {
            return $this->processSslCommerzIpn($transaction, $request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid callback'
        ], 400);
    }

    /**
     * Process SSLCommerz success callback
     */
    protected function processSslCommerzSuccess(PaymentTransaction $transaction, Request $request)
    {
        Log::info('SSLCommerz success callback started', [
            'transaction_id' => $transaction->id,
            'request_data' => $request->all()
        ]);

        try {
            // Validate payment
            $validation = $this->sslCommerzGateway->validatePayment($request);

            if (!$validation['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment validation failed'
                ], 400);
            }

            DB::beginTransaction();

            // Update transaction
            $updateData = [
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'gateway_response' => json_encode(array_merge(
                    json_decode($transaction->gateway_response ?? '{}', true),
                    $validation['data']
                )),
            ];

            if (isset($validation['data']['tran_id'])) {
                try {
                    $transaction->update(array_merge($updateData, [
                        'gateway_transaction_id' => $validation['data']['tran_id']
                    ]));
                } catch (\Exception $e) {
                    $transaction->update($updateData);
                }
            } else {
                $transaction->update($updateData);
            }

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            $paymentMaster->update([
                'status' => 'paid',
                'paid_amount' => $transaction->amount,
                'paid_at' => Carbon::now(),
            ]);

            // Activate subscription
            $metadata = json_decode($paymentMaster->metadata ?? '{}', true);
            $orderId = $metadata['order_id'] ?? null;

            $subscriptionId = null;
            if ($orderId) {
                $order = SubscriptionOrder::with('items')->find($orderId);
                if ($order && !$order->processed_at) {
                    $subscription = $this->activateOrderSubscription($order, $paymentMaster);
                    $subscriptionId = $subscription->id ?? null;
                }
            }

            DB::commit();

            // Get user for response
            $user = $transaction->user ?? $paymentMaster->user;

            // Generate token if needed (for API response)
            $token = null;
            if ($user && $request->expectsJson() && $request->hasSession()) {
                if (!Auth::check()) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                }
            }

            if (!$request->expectsJson()) {
                return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment successful! Your subscription is now active.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'payment_master_id' => $paymentMaster->id,
                    'subscription_id' => $subscriptionId,
                    'order_id' => $orderId,
                    'user' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ] : null,
                    'token' => $token
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SSLCommerz success error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process SSLCommerz fail callback
     */
    protected function processSslCommerzFail(PaymentTransaction $transaction, Request $request)
    {
        try {
            $transaction->update([
                'status' => 'failed',
                'failed_at' => Carbon::now(),
                'failure_reason' => $request->get('error') ?? 'Payment failed',
                'gateway_response' => json_encode(array_merge(
                    json_decode($transaction->gateway_response ?? '{}', true),
                    $request->all()
                )),
            ]);

            // Update payment master
            $paymentMaster = $transaction->paymentMaster;
            $paymentMaster->update([
                'status' => 'failed',
            ]);

            if (!$request->expectsJson()) {
                return redirect()->route('website.plans.index', ['payment_status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => 'failed'
                ]
            ], 400);

        } catch (Exception $e) {
            Log::error('SSLCommerz fail error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing failed payment'
            ], 500);
        }
    }

    /**
     * Process SSLCommerz cancel callback
     */
    protected function processSslCommerzCancel(PaymentTransaction $transaction, Request $request)
    {
        try {
            $transaction->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'gateway_response' => json_encode(array_merge(
                    json_decode($transaction->gateway_response ?? '{}', true),
                    $request->all()
                )),
            ]);

            if (!$request->expectsJson()) {
                return redirect()->route('website.plans.index', ['payment_status' => 'cancelled']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment cancelled.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => 'cancelled'
                ]
            ]);

        } catch (Exception $e) {
            Log::error('SSLCommerz cancel error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing cancelled payment'
            ], 500);
        }
    }

    /**
     * Process SSLCommerz IPN (Instant Payment Notification)
     */
    protected function processSslCommerzIpn(PaymentTransaction $transaction, Request $request)
    {
        Log::info('SSLCommerz IPN received', $request->all());

        try {
            $validation = $this->sslCommerzGateway->validatePayment($request);

            if (!$validation['success']) {
                return response()->json(['status' => 'validation_failed'], 400);
            }

            DB::beginTransaction();

            $transaction->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'gateway_response' => json_encode(array_merge(
                    json_decode($transaction->gateway_response ?? '{}', true),
                    $validation['data']
                )),
            ]);

            $paymentMaster = $transaction->paymentMaster;
            $paymentMaster->update([
                'status' => 'paid',
                'paid_amount' => $transaction->amount,
                'paid_at' => Carbon::now(),
            ]);

            // Activate subscription if not already active
            $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
            if ($order && !$order->processed_at) {
                $this->activateOrderSubscription($order, $paymentMaster);
            }

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SSLCommerz IPN processing error: ' . $e->getMessage());

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle bKash callback
     */
    protected function handleBkashCallback(Request $request)
    {
        $paymentId = $request->get('paymentID');
        $status = $request->get('status');

        $transaction = PaymentTransaction::where('transaction_id', $paymentId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($status === 'success') {
            // Execute payment
            $result = $this->bkashGateway->executePayment($paymentId);

            if ($result['success']) {
                DB::beginTransaction();
                try {
                    $transaction->update([
                        'status' => 'completed',
                        'completed_at' => Carbon::now(),
                        'gateway_response' => json_encode($result),
                    ]);

                    $paymentMaster = $transaction->paymentMaster;
                    $paymentMaster->update([
                        'status' => 'paid',
                        'paid_amount' => $transaction->amount,
                        'paid_at' => Carbon::now(),
                    ]);

                    // Activate subscription
                    $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
                    if ($order && !$order->processed_at) {
                        $this->activateOrderSubscription($order, $paymentMaster);
                    }

                    DB::commit();

                    if (!$request->expectsJson()) {
                        return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful! Your subscription is now active.',
                        'data' => [
                            'subscription_id' => $order?->items()->first()?->subscription_id,
                            'order_id' => $order?->id
                        ]
                    ]);
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('bKash callback error: ' . $e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment verification failed. Please contact support.'
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.'
        ], 400);
    }

    /**
     * Handle Rocket callback
     */
    protected function handleRocketCallback(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status');

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($status === 'success') {
            DB::beginTransaction();
            try {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);

                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $transaction->amount,
                    'paid_at' => Carbon::now(),
                ]);

                // Activate subscription
                $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
                if ($order && !$order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                if (!$request->expectsJson()) {
                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id
                    ]
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Rocket callback error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.'
        ], 400);
    }

    /**
     * Handle Nagad callback
     */
    protected function handleNagadCallback(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status');

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($status === 'success') {
            DB::beginTransaction();
            try {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                ]);

                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $transaction->amount,
                    'paid_at' => Carbon::now(),
                ]);

                // Activate subscription
                $order = SubscriptionOrder::find($paymentMaster->metadata['order_id'] ?? null);
                if ($order && !$order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                if (!$request->expectsJson()) {
                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id
                    ]
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Nagad callback error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.'
        ], 400);
    }

    /**
     * Find transaction by various methods
     */
    protected function findTransaction($tranId): ?PaymentTransaction
    {
        // Method 1: Direct match on transaction_id
        $transaction = PaymentTransaction::where('transaction_id', $tranId)->first();

        if ($transaction) {
            return $transaction;
        }

        // Method 2: Try gateway_transaction_id if column exists
        try {
            $transaction = PaymentTransaction::where('gateway_transaction_id', $tranId)->first();
            if ($transaction) {
                return $transaction;
            }
        } catch (\Exception $e) {
            // Column doesn't exist, continue to next method
        }

        // Method 3: Search in gateway_response JSON
        $transactions = PaymentTransaction::where('gateway_response', 'like', '%' . $tranId . '%')
            ->limit(10)
            ->get();

        foreach ($transactions as $trans) {
            $response = json_decode($trans->gateway_response, true);

            // Check various possible keys
            if (isset($response['tran_id']) && $response['tran_id'] == $tranId) {
                return $trans;
            }
            if (isset($response['transaction_id']) && $response['transaction_id'] == $tranId) {
                return $trans;
            }
            if (isset($response['data']['tran_id']) && $response['data']['tran_id'] == $tranId) {
                return $trans;
            }
        }

        // Method 4: Check by payment master metadata
        $paymentMasters = PaymentMaster::where('metadata', 'like', '%' . $tranId . '%')->get();

        foreach ($paymentMasters as $pm) {
            $metadata = json_decode($pm->metadata, true);
            if (isset($metadata['transaction_id']) && $metadata['transaction_id'] == $tranId) {
                return PaymentTransaction::where('payment_master_id', $pm->id)->first();
            }
        }

        return null;
    }

    /**
     * Create order
     */
    protected function createOrder(User $user, Request $request): SubscriptionOrder
    {
        $plan = Plan::findOrFail($request->plan_id);
        $price = PlanPrice::findOrFail($request->price_id);

        $subtotal = $price->amount;
        $taxRate = config('app.tax_rate', 10);
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        $order = SubscriptionOrder::create([
            'user_id' => $user->id,
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'type' => 'new',
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'currency' => $price->currency,
            'customer_info' => json_encode([
                'name' => $request->name,
                'email' => $request->email ?? $user->email,
                'phone' => $request->phone ?? $user->phone,
            ]),
            'billing_address' => json_encode($request->billing_address ?? []),
            'metadata' => json_encode([
                'plan_id' => $plan->id,
                'price_id' => $price->id,
                'gateway' => $request->gateway,
            ]),
            'created_by' => $user->id,
        ]);

        // Create order item
        SubscriptionOrderItem::create([
            'subscription_order_id' => $order->id,
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'plan_name' => $plan->name,
            'billing_cycle' => $price->interval,
            'quantity' => 1,
            'unit_price' => $price->amount,
            'amount' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'start_date' => Carbon::now(),
            'subscription_status' => 'pending',
        ]);

        return $order;
    }

    /**
     * Create payment master
     */
    protected function createPaymentMaster(User $user, SubscriptionOrder $order, Request $request): PaymentMaster
    {
        return PaymentMaster::create([
            'user_id' => $user->id,
            'payment_number' => $this->generatePaymentNumber(),
            'type' => 'subscription',
            'status' => 'pending',
            'total_amount' => $order->total_amount,
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax_amount,
            'currency' => $order->currency,
            'payment_method' => $request->payment_method,
            'payment_gateway' => $request->gateway,
            'payment_method_details' => json_encode($request->payment_details ?? []),
            'metadata' => json_encode([
                'order_id' => $order->id,
                'plan_id' => $request->plan_id,
                'price_id' => $request->price_id,
            ]),
            'created_by' => $user->id,
        ]);
    }

    /**
     * Create subscription
     */
    protected function createSubscription(User $user, SubscriptionOrder $order, ?Request $request, PaymentMaster $paymentMaster)
    {
        $price = PlanPrice::find($order->metadata['price_id'] ?? $request?->price_id);

        return $this->subscriptionService->createSubscription([
            'user_id' => $user->id,
            'plan_id' => $order->metadata['plan_id'] ?? $request?->plan_id,
            'price_id' => $price->id,
            'name' => $order->items->first()->plan_name,
            'quantity' => 1,
            'gateway' => $order->metadata['gateway'] ?? $request?->gateway,
            'amount' => $price->amount,
            'currency' => $price->currency,
            'interval' => $price->interval,
            'interval_count' => $price->interval_count,
            'metadata' => [
                'order_id' => $order->id,
                'payment_master_id' => $paymentMaster->id,
            ],
        ]);
    }

    /**
     * Activate subscription from order
     */
    protected function activateOrderSubscription(SubscriptionOrder $order, PaymentMaster $paymentMaster)
    {
        $orderItem = $order->items()->first();

        if ($orderItem && !$orderItem->subscription_id) {
            $metadata = json_decode($order->metadata, true);
            $price = PlanPrice::find($metadata['price_id']);

            $subscription = $this->subscriptionService->createSubscription([
                'user_id' => $order->user_id,
                'plan_id' => $orderItem->plan_id,
                'price_id' => $metadata['price_id'],
                'name' => $orderItem->plan_name,
                'quantity' => $orderItem->quantity,
                'gateway' => $metadata['gateway'],
                'amount' => $price->amount,
                'currency' => $price->currency,
                'interval' => $price->interval,
                'interval_count' => $price->interval_count,
                'metadata' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                ],
            ]);

            $orderItem->update([
                'subscription_id' => $subscription->id,
                'subscription_status' => 'active',
                'processed_at' => Carbon::now(),
            ]);

            $order->update([
                'status' => 'completed',
                'processed_at' => Carbon::now(),
            ]);

            return $subscription;
        }

        return null;
    }

    /**
     * Save payment method for future use
     */
    protected function savePaymentMethod(User $user, array $paymentResult, Request $request): void
    {
        PaymentMethod::create([
            'user_id' => $user->id,
            'type' => $request->payment_method,
            'gateway' => $request->gateway,
            'gateway_customer_id' => $paymentResult['customer_id'] ?? null,
            'gateway_payment_method_id' => $paymentResult['payment_method_id'] ?? null,
            'is_default' => !PaymentMethod::where('user_id', $user->id)->exists(),
            'is_verified' => true,
            'card_brand' => $paymentResult['card_brand'] ?? null,
            'card_last4' => $paymentResult['card_last4'] ?? null,
            'card_exp_month' => $paymentResult['card_exp_month'] ?? null,
            'card_exp_year' => $paymentResult['card_exp_year'] ?? null,
            'metadata' => json_encode($request->payment_details ?? []),
            'gateway_metadata' => json_encode($paymentResult),
            'last_used_at' => Carbon::now(),
            'usage_count' => 1,
            'card_country' => $paymentResult['card_country'] ?? null,
            'bank_name' => $paymentResult['bank_name'] ?? null,
            'bank_account_last4' => $paymentResult['bank_account_last4'] ?? null,
            'bank_account_type' => $paymentResult['bank_account_type'] ?? null,
            'bank_routing_number' => $paymentResult['bank_routing_number'] ?? null,
            'wallet_type' => $paymentResult['wallet_type'] ?? null,
            'wallet_number' => $paymentResult['wallet_number'] ?? null,
            'crypto_currency' => $paymentResult['crypto_currency'] ?? null,
            'crypto_address' => $paymentResult['crypto_address'] ?? null,
            'encrypted_data' => $paymentResult['encrypted_data'] ?? null,
            'fingerprint' => $paymentResult['fingerprint'] ?? null,
            'is_compromised' => $paymentResult['is_compromised'] ?? false,
            'metadata' => json_encode($request->metadata ?? []),
            'gateway_metadata' => json_encode($paymentResult),
            'verified_at' => Carbon::now(),
            'verified_by' => auth()->id() ?? null,
            'created_by' => auth()->id() ?? null,
            'updated_by' => auth()->id() ?? null,
        ]);
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $number = $prefix . '-' . $date . '-' . $random;

        while (SubscriptionOrder::where('order_number', $number)->exists()) {
            $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $number = $prefix . '-' . $date . '-' . $random;
        }

        return $number;
    }

    /**
     * Generate payment number
     */
    protected function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        return $prefix . '-' . $date . '-' . $random;
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId($prefix = 'TXN'): string
    {
        return strtoupper($prefix) . '-' . time() . '-' . rand(1000, 9999);
    }

    /**
     * Format money with currency
     */
    protected function formatMoney($amount, $currency = 'USD'): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'BDT' => '৳',
            'INR' => '₹',
        ];

        $symbol = $symbols[$currency] ?? $currency;

        return $symbol . ' ' . number_format($amount, 2);
    }
}
