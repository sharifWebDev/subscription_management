<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentChild;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\PaymentIntent;

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

            if (! $price) {
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
            Log::error('Checkout initialization failed: '.$e->getMessage());

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
        // 🔐 Verify OTP & Create/Get User
        $otpResult = $this->otpService->verifyOtpAndCreateUser(
            $request->email,
            $request->otp,
            [
                'name' => $request->name,
                'phone' => $request->phone,
            ]
        );

        if (! $otpResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $otpResult['message'],
            ], 400);
        }

        $user = $otpResult['user'];

        // 🛒 Create Order
        $order = $this->createOrder($user, $request);
        $order = $order->load('items');

        // 💳 Create Payment Master
        $paymentMaster = $this->createPaymentMaster($user, $order, $request);

        // 🚀 Process Payment
        $paymentResult = $this->processGatewayPayment($paymentMaster, $order, $user, $request);

        if (! $paymentResult['success']) {
            throw new Exception($paymentResult['message'] ?? 'Payment processing failed');
        }

        // 🌍 Redirect-based gateway (SSLCommerz, bKash etc.)
        if (!empty($paymentResult['requires_redirect'])) {

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $paymentResult['message'] ?? 'Redirecting to payment gateway...',
                'data' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                    'requires_redirect' => true,
                    'redirect_url' => $paymentResult['redirect_url'],
                    'status' => 'requires_action',
                    'transaction_id' => $paymentResult['transaction_id'] ?? null,
                ],
            ]);
        }

        // ✅ If Payment Completed (Card / Instant Payment)
        if ($paymentResult['status'] === 'completed') {

            $subscription = $this->createSubscription($user, $order, $request, $paymentMaster);

            $order->items()->update([
                'subscription_id' => $subscription->id,
                'subscription_status' => 'active',
                'processed_at' => Carbon::now(),
            ]);

            $order->update([
                'status' => 'completed',
                'processed_at' => Carbon::now(),
            ]);

            // 🧾 Create Invoice
            $invoice = $this->invoice($order->fresh(), $subscription->id);

            // 💰 Record Payment
            $this->payment($invoice, $order->metadata['gateway'] ?? $request->gateway);

            // 💾 Save Payment Method
            if ($request->boolean('save_payment_method') && isset($paymentResult['payment_method_id'])) {
                $this->savePaymentMethod($user, $paymentResult, $request);
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Payment completed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                    'subscription_id' => $subscription->id,
                    'status' => 'completed',
                ],
            ];

            // 🔑 Attach token if new user
            if ($otpResult['is_new_user']) {
                $response['data']['token'] = $otpResult['token'];
                $response['data']['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }

            return response()->json($response);
        }

        // 🟡 Pending Payments (Bank transfer etc.)
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $paymentResult['message'] ?? 'Payment initiated successfully',
            'data' => [
                'order_id' => $order->id,
                'payment_master_id' => $paymentMaster->id,
                'status' => $paymentResult['status'] ?? 'pending',
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
            ],
        ]);

    } catch (Exception $e) {
        DB::rollBack();

        Log::error('OTP Checkout failed: '.$e->getMessage(), [
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

        // dd($request->all());

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

            if (! $paymentResult['success']) {
                throw new Exception($paymentResult['message'] ?? 'Payment processing failed');
            }

            // **Check if this is a redirect-based payment (SSLCommerz, bKash, etc.)**
            if (isset($paymentResult['requires_redirect']) && $paymentResult['requires_redirect'] === true) {
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $paymentResult['message'] ?? 'Redirecting to payment gateway...',
                    'data' => [
                        'order_id' => $order->id,
                        'payment_master_id' => $paymentMaster->id,
                        'requires_redirect' => true,
                        'redirect_url' => $paymentResult['redirect_url'],
                        'status' => 'requires_action',
                        'transaction_id' => $paymentResult['transaction_id'] ?? null,
                    ],
                ]);
            }

            // If payment is successful (no 3D Secure required)
            if ($paymentResult['status'] === 'completed') {
                // Create subscription
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

                $invoice = $this->invoice($order->fresh(), $subscription->id);

                $this->payment($invoice, $order->metadata['gateway'] ?? $request->gateway);

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
            Log::error('Authenticated checkout failed: '.$e->getMessage(), [
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
        $status = $request->route('status') ?? $request->get('status');

        try {
            Log::info('Payment callback received', [
                'gateway' => $gateway,
                'status' => $status,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'data' => $request->all(),
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
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Your payment is being processed. We will notify you once confirmed.',
                            'status' => 'pending',
                        ]);
                    }

                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'pending']);
                default:
                    throw new Exception('Unsupported gateway: '.$gateway);
            }
        } catch (Exception $e) {
            Log::error('Payment callback failed: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->route('website.plans.index', ['payment_status' => 'failed']);
        }
    }

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
            'description' => 'Subscription: '.($order->items->first()->plan_name ?? 'Plan'),
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
    protected function processStripePayment(array $data, Request $request): array
    {
        $userId = $request->user()->id ?? $request->id;
        try {
            // Get existing Stripe customer ID from user's payment methods
            $existingPaymentMethod = PaymentMethod::where('user_id', $userId )
                ->where('gateway', 'stripe')
                ->whereNotNull('gateway_customer_id')
                ->first();

            $stripeCustomerId = $existingPaymentMethod ? $existingPaymentMethod->gateway_customer_id : null;

            // Add customer data to payment data
            $paymentData = array_merge($data, [
                'customer_id' => $stripeCustomerId,
                'email' => $request->user()->email ?? $request->email,
                'name' => $request->name ?? $request->user()->name,
                'phone' => $request->phone ?? $request->user()->phone,
                'user_id' => $userId,
            ]);

            // If using saved payment method
            if ($request->payment_method_id) {
                $result = $this->stripeGateway->createPaymentIntentWithSavedMethod($paymentData, $request->payment_method_id);
            }
            // If using new card
            elseif (isset($request->payment_details['payment_method_id'])) {
                $result = $this->stripeGateway->createPaymentIntentWithSavedMethod($paymentData, $request->payment_details['payment_method_id']);
            } else {
                $result = $this->stripeGateway->createPaymentIntent($paymentData);
            }

            if (! $result['success']) {
                return $result;
            }

            // Get payment method details
            $paymentMethodDetails = null;
            if (isset($result['payment_method_id'])) {
                $pmResult = $this->stripeGateway->retrievePaymentMethod($result['payment_method_id']);
                if ($pmResult['success']) {
                    $pm = $pmResult['payment_method'];
                    $paymentMethodDetails = [
                        'id' => $pm->id,
                        'card_brand' => $pm->card->brand ?? null,
                        'card_last4' => $pm->card->last4 ?? null,
                        'card_exp_month' => $pm->card->exp_month ?? null,
                        'card_exp_year' => $pm->card->exp_year ?? null,
                        'customer_id' => $pm->customer ?? ($result['customer_id'] ?? null),
                    ];
                }
            }

            // Create transaction
            $transaction = PaymentTransaction::create([
                'payment_master_id' => $data['payment_master_id'],
                'user_id' => $data['user_id'],
                'transaction_id' => $result['intent_id'],
                'type' => 'payment',
                'payment_method' => 'card',
                'payment_gateway' => 'stripe',
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'status' => $result['status'] === 'completed' ? 'completed' : 'pending',
                'gateway_response' => json_encode($result),
                'gateway_customer_id' => $result['customer_id'] ?? ($paymentMethodDetails['customer_id'] ?? null),
                'gateway_payment_method_id' => $result['payment_method_id'] ?? ($paymentMethodDetails['id'] ?? null),
                'initiated_at' => Carbon::now(),
                'completed_at' => $result['status'] === 'completed' ? Carbon::now() : null,
            ]);

            // If payment is completed
            if ($result['status'] === 'completed') {
                // Update payment master
                $paymentMaster = PaymentMaster::find($data['payment_master_id']);
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $data['amount'],
                    'paid_at' => Carbon::now(),
                    'gateway_customer_id' => $result['customer_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'status' => 'completed',
                    'transaction_id' => $transaction->id,
                    'payment_method_id' => $result['payment_method_id'] ?? ($paymentMethodDetails['id'] ?? null),
                    'customer_id' => $result['customer_id'] ?? ($paymentMethodDetails['customer_id'] ?? null),
                    'payment_method_details' => $paymentMethodDetails,
                ];
            }

            // If payment requires action (should not happen with confirm=true)
            return [
                'success' => false,
                'message' => 'Payment requires additional authentication',
                'status' => $result['status'],
            ];

        } catch (Exception $e) {
            Log::error('Stripe payment processing error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Stripe payment failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(array $data, Request $request): array
    {
        $result = $this->paypalGateway->createOrder($data);

        $data['success_url'] = url('/payment/paypal/success');
        $data['cancel_url'] = url('/payment/paypal/cancel');

        // PayPal subscription success URL (specific case)
        $data['subscription_success_url'] = url('/payment/paypal/subscription/success');

        if (! $result['success']) {
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
        // **SurjoPay callback URLs আপডেট করুন**
        $data['success_url'] = url('/payment/surjopay/success');
        $data['fail_url'] = url('/payment/surjopay/fail');
        $data['cancel_url'] = url('/payment/surjopay/cancel');
        $data['ipn_url'] = url('/payment/surjopay/ipn');

        $result = $this->surjoPayGateway->createPayment($data);

        if (! $result['success']) {
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

        // **URL আপডেট করুন - নতুন ডায়নামিক রাউট অনুযায়ী**
        $successUrl = url('/api/payment/sslcommerz/success');
        $failUrl = url('/api/payment/sslcommerz/fail');
        $cancelUrl = url('/api/payment/sslcommerz/cancel');
        $ipnUrl = url('/api/payment/sslcommerz/ipn');

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
            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'cancel_url' => $cancelUrl,
            'ipn_url' => $ipnUrl,
        ]);

        if (! $result['success']) {
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
            'message' => 'Redirecting to SSLCommerz...',
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
        $data['callback_url'] = url('/payment/bkash/callback');

        $result = $this->bkashGateway->createPayment($data);

        if (! $result['success']) {
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
            'redirect_url' => $result['bkash_url'],
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
            'transaction_id' => 'BANK-'.time(),
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
            'transaction_id' => 'CASH-'.time(),
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

            if (! $transaction) {
                throw new Exception('Transaction not found');
            }

            // First retrieve the payment intent to check status
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

            // If already succeeded, no need to confirm again
            if ($paymentIntent->status === 'succeeded') {
                DB::beginTransaction();

                // Update transaction
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'gateway_response' => json_encode(['payment_intent' => $paymentIntent]),
                    'gateway_customer_id' => $paymentIntent->customer,
                    'gateway_payment_method_id' => $paymentIntent->payment_method,
                ]);

                // Update payment master
                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $transaction->amount,
                    'paid_at' => Carbon::now(),
                    'gateway_customer_id' => $paymentIntent->customer,
                ]);

                // Activate subscription
                $metadata = json_decode($paymentMaster->metadata ?? '{}', true);
                $orderId = $metadata['order_id'] ?? null;

                $subscriptionId = null;
                if ($orderId) {
                    $order = SubscriptionOrder::with('items')->find($orderId);
                    if ($order && ! $order->processed_at) {
                        $subscription = $this->activateOrderSubscription($order, $paymentMaster);
                        $subscriptionId = $subscription->id ?? null;
                    }
                }

                // Save payment method if user wants to save it
                if ($request->boolean('save_payment_method') && $paymentIntent->payment_method) {
                    // Get payment method details
                    try {
                        $pm = \Stripe\PaymentMethod::retrieve($paymentIntent->payment_method);
                        $paymentResult = [
                            'payment_method_id' => $pm->id,
                            'customer_id' => $pm->customer,
                            'card_brand' => $pm->card->brand ?? null,
                            'card_last4' => $pm->card->last4 ?? null,
                            'card_exp_month' => $pm->card->exp_month ?? null,
                            'card_exp_year' => $pm->card->exp_year ?? null,
                        ];

                        $request->merge([
                            'payment_method' => 'stripe',
                            'gateway' => 'stripe',
                        ]);

                        $this->savePaymentMethod($user, $paymentResult, $request);
                    } catch (Exception $e) {
                        Log::warning('Could not save payment method', ['error' => $e->getMessage()]);
                    }
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

            // If not succeeded, try to confirm
            $result = $this->stripeGateway->confirmPaymentIntent($request->payment_intent_id);

            if (! $result['success']) {
                throw new Exception($result['message'] ?? 'Payment confirmation failed');
            }

            if ($result['status'] === 'completed') {
                DB::beginTransaction();

                // Update transaction
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'gateway_response' => json_encode($result),
                    'gateway_customer_id' => $result['customer_id'] ?? null,
                    'gateway_payment_method_id' => $result['payment_method_id'] ?? null,
                ]);

                // Update payment master
                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_amount' => $transaction->amount,
                    'paid_at' => Carbon::now(),
                    'gateway_customer_id' => $result['customer_id'] ?? null,
                ]);

                // Activate subscription
                $metadata = json_decode($paymentMaster->metadata ?? '{}', true);
                $orderId = $metadata['order_id'] ?? null;

                $subscriptionId = null;
                if ($orderId) {
                    $order = SubscriptionOrder::with('items')->find($orderId);
                    if ($order && ! $order->processed_at) {
                        $subscription = $this->activateOrderSubscription($order, $paymentMaster);
                        $subscriptionId = $subscription->id ?? null;
                    }
                }

                // Save payment method if user wants to save it
                if ($request->boolean('save_payment_method') && isset($result['payment_method_id'])) {
                    $paymentResult = [
                        'payment_method_id' => $result['payment_method_id'],
                        'customer_id' => $result['customer_id'] ?? null,
                        'card_brand' => $result['payment_method_details']['card_brand'] ?? null,
                        'card_last4' => $result['payment_method_details']['card_last4'] ?? null,
                        'card_exp_month' => $result['payment_method_details']['card_exp_month'] ?? null,
                        'card_exp_year' => $result['payment_method_details']['card_exp_year'] ?? null,
                    ];

                    $request->merge([
                        'payment_method' => 'stripe',
                        'gateway' => 'stripe',
                    ]);

                    $this->savePaymentMethod($user, $paymentResult, $request);
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
            Log::error('Stripe payment confirmation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Stripe callback
     */
    protected function handleStripeCallback(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');
        $redirectStatus = $request->get('redirect_status');

        Log::info('Stripe callback received', [
            'payment_intent' => $paymentIntentId,
            'redirect_status' => $redirectStatus,
            'data' => $request->all(),
        ]);

        $transaction = PaymentTransaction::where('transaction_id', $paymentIntentId)->first();

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if ($redirectStatus === 'succeeded') {
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
                if ($order && ! $order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id,
                    ],
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Stripe callback error: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
        ], 400);
    }

    /**
     * Handle PayPal callback
     */
    protected function handlePayPalCallback(Request $request)
    {
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');

        if (! $token || ! $PayerID) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid PayPal callback',
            ], 400);
        }

        $transaction = PaymentTransaction::where('transaction_id', $token)->first();

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
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
                if ($order && ! $order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                if (! $request->expectsJson()) {
                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id,
                    ],
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('PayPal callback error: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment capture failed. Please try again.',
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

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
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
                    if ($order && ! $order->processed_at) {
                        $this->activateOrderSubscription($order, $paymentMaster);
                    }

                    DB::commit();

                    if (! $request->expectsJson()) {
                        return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful! Your subscription is now active.',
                        'data' => [
                            'subscription_id' => $order?->items()->first()?->subscription_id,
                            'order_id' => $order?->id,
                        ],
                    ]);
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('SurjoPay callback error: '.$e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment verification failed. Please contact support.',
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed.',
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

        if (! $transaction) {
            Log::error('SSLCommerz callback: Transaction not found', ['tran_id' => $tranId]);

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
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
            'message' => 'Invalid callback',
        ], 400);
    }

    /**
     * Process SSLCommerz success callback
     */
    protected function processSslCommerzSuccess(PaymentTransaction $transaction, Request $request)
    {
        try {
            // Validate payment
            $validation = $this->sslCommerzGateway->validatePayment($request);

            if (! $validation['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment validation failed',
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

            // Update gateway_transaction_id if column exists
            if (Schema::hasColumn('payment_transactions', 'gateway_transaction_id')) {
                $updateData['gateway_transaction_id'] = $request->input('tran_id');
            }

            $transaction->update($updateData);

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
                if ($order && ! $order->processed_at) {
                    $subscription = $this->activateOrderSubscription($order, $paymentMaster);
                    $subscriptionId = $subscription->id ?? null;
                }else {
                Log::warning('Order not found or already processed', ['order_id' => $orderId, 'processed_at' => $order->processed_at ?? null]);
            }
            }

            // **পেমেন্ট মেথড সংরক্ষণ করুন (SSLCommerz-এর জন্য)**
            $user = $transaction->user ?? $paymentMaster->user;

            if ($user && $request->input('card_type')) {
                $this->saveSslCommerzPaymentMethod($user, $transaction, $request, $paymentMaster);
            }

            DB::commit();

            // Get user for response
            $user = $transaction->user ?? $paymentMaster->user;

            // **API-এর জন্য Token generate (session check বাদ দিন)**
            $token = null;
            if ($user && $request->expectsJson()) {
                // User already authenticated? check by token or create new
                if (! Auth::guard('sanctum')->check()) {
                    $token = $user->createToken('auth_token')->plainTextToken;
                }
            }

            // **API response সবসময় JSON return করবে**
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
                        'email' => $user->email,
                    ] : null,
                    'token' => $token,
                    'redirect_url' => config('app.frontend_url').'/dashboard/subscriptions?payment_status=success',
                ],
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SSLCommerz success error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: '.$e->getMessage(),
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

            if (! $request->expectsJson()) {
                return redirect()->route('website.plans.index', ['payment_status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => 'failed',
                ],
            ], 400);

        } catch (Exception $e) {
            Log::error('SSLCommerz fail error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing failed payment',
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

            if (! $request->expectsJson()) {
                return redirect()->route('website.plans.index', ['payment_status' => 'cancelled']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment cancelled.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'status' => 'cancelled',
                ],
            ]);

        } catch (Exception $e) {
            Log::error('SSLCommerz cancel error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing cancelled payment',
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

            if (! $validation['success']) {
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
            if ($order && ! $order->processed_at) {
                $this->activateOrderSubscription($order, $paymentMaster);
            }

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SSLCommerz IPN processing error: '.$e->getMessage());

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

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
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
                    if ($order && ! $order->processed_at) {
                        $this->activateOrderSubscription($order, $paymentMaster);
                    }

                    DB::commit();

                    if (! $request->expectsJson()) {
                        return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful! Your subscription is now active.',
                        'data' => [
                            'subscription_id' => $order?->items()->first()?->subscription_id,
                            'order_id' => $order?->id,
                        ],
                    ]);
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('bKash callback error: '.$e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment verification failed. Please contact support.',
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
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

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
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
                if ($order && ! $order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                if (! $request->expectsJson()) {
                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id,
                    ],
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Rocket callback error: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
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

        if (! $transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
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
                if ($order && ! $order->processed_at) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                DB::commit();

                if (! $request->expectsJson()) {
                    return redirect()->route('website.dashboard.subscriptions', ['payment_status' => 'success']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Your subscription is now active.',
                    'data' => [
                        'subscription_id' => $order?->items()->first()?->subscription_id,
                        'order_id' => $order?->id,
                    ],
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Nagad callback error: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed. Please contact support.',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
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
            $transaction = PaymentTransaction::where('gateway_transaction_id', $tranId)
            ->first();
            if ($transaction) {
                return $transaction;
            }
        } catch (\Exception $e) {
            // Column doesn't exist, continue to next method
        }

        // Method 3: Search in gateway_response JSON
        $transactions = PaymentTransaction::where('gateway_response', 'like', '%'.$tranId.'%')
            ->orWhere('gateway_response', 'like', '%' . str_replace('SSLCOMMERZ-', '', $tranId) . '%')
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
        $paymentMasters = PaymentMaster::where('metadata', 'like', '%'.$tranId.'%')
        ->orWhere('metadata', 'like', '%' . str_replace('SSLCOMMERZ-', '', $tranId) . '%')
        ->get();

        foreach ($paymentMasters as $pm) {
            $metadata = json_decode($pm->metadata, true);
            if (isset($metadata['transaction_id']) && $metadata['transaction_id'] == $tranId) {
                $tranIdWithoutPrefix = str_replace('SSLCOMMERZ-', '', $tranId);
                return PaymentTransaction::where('payment_master_id', $pm->id)
                ->orwhere('transaction_id', $tranIdWithoutPrefix)
                ->first();
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
                'payment_master_id' => $request->payment_master_id,
                'recipient_user_id' => $request->id,
                'recipient_info' => json_encode([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email ?? $user->email,
                    'user_ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => $user->id,
                    'location' => $request->get('location', []),
                    'longitude' => $request->get('longitude', []),
                    'latitude' => $request->get('latitude', []),
                    'country' => $request->get('country', []),
                    'device' => $request->get('device', []),
                ]),
            ]),
            'ordered_at' => Carbon::now(),
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
            'recipient_user_id' => $request->id,
            'recipient_info' => json_encode([
                'id' => $request->id,
                'name' => $request->name,
                'email' => $request->email ?? $user->email,
                'phone' => $request->phone ?? $user->phone,
                'billing_address' => $request->billing_address ?? [],
                'metadata' => [
                    'plan_id' => $plan->id,
                    'price_id' => $price->id,
                    'gateway' => $request->gateway,
                ],
            ]),
            'unit_price' => $price->amount,
            'amount' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths($price->interval),
            'subscription_status' => 'pending',
            'created_by' => $user->id,
        ]);

        return $order;
    }

    /**
     * Create payment master
     */
    protected function createPaymentMaster(User $user, SubscriptionOrder $order, Request $request): PaymentMaster
    {
        $paymentMaster = PaymentMaster::create([
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
            'payment_date' => Carbon::now(),
            'payment_method_details' => json_encode($request->payment_details ?? []),
            'metadata' => json_encode([
                'order_id' => $order->id,
                'plan_id' => $request->plan_id,
                'price_id' => $request->price_id,
            ]),
            'created_by' => $user->id,
        ]);

        $this->createPaymentChildren($paymentMaster, $order->items);

        return $paymentMaster;
    }

    protected function createPaymentChildren(PaymentMaster $paymentMaster, $orderItems)
    {
        foreach ($orderItems as $item) {
            PaymentChild::create([
                'payment_master_id' => $paymentMaster->id,
                'item_type' => 'subscription',
                'item_id' => $item->id,
                'subscription_id' => $item->subscription_id,
                'plan_id' => $item->plan_id,
                'invoice_id' => $item->invoice_id,
                'description' => $item->plan_name,
                'item_code' => 'SUBSCRIPTION_'.$item->plan_id,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'amount' => $item->amount,
                'tax_amount' => $item->tax_amount,
                'total_amount' => $item->total_amount,
                'discount_amount' => $item->discount_amount,
                'period_start' => $item->period_start,
                'period_end' => $item->period_end,
                'billing_cycle' => $item->billing_cycle,
                'status' => 'paid',
                'paid_at' => $item->paid_at ?? Carbon::now(),
                'allocated_amount' => $item->total_amount,
                // 'is_fully_allocated' => 1,
                'metadata' => $paymentMaster->metadata,
                'tax_breakdown' => json_encode([
                    'name' => 'Tax',
                    'amount' => $paymentMaster->tax_amount,
                    'rate' => $paymentMaster->tax_amount > 0 ? round(($paymentMaster->tax_amount / $paymentMaster->total_amount) * 100, 2) : 0,
                ]),
                'discount_breakdown' => json_encode([
                    'name' => 'Discount',
                        'amount' => $paymentMaster->discount_amount,
                        'rate' => $paymentMaster->discount_amount > 0 ? round(($paymentMaster->discount_amount / $paymentMaster->total_amount) * 100, 2) : 0,
                ]),
                'created_by' => $item->created_by,
            ]);
        }
    }

    protected function invoice(SubscriptionOrder $order, $subscriptionId)
    {
        return Invoice::create([
            'user_id' => $order->user_id,
            'subscription_id' => $subscriptionId,
            'number' => $this->generateInvoiceNumber($order->id),
            'external_id' => 'INV-'.$order->id.'-'.Str::upper(Str::random(6)),
            'type' => 'subscription',
            'status' => $order->status,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax_amount,
            'total' => $order->total_amount,
            'amount_due' => $order->total_amount,
            'amount_paid' => 0,
            'currency' => $order->currency,
            'issue_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(30),
            'paid_at' => null,
            'finalized_at' => null,
            'line_items' => json_encode([
                [
                    'description' => $order->items->first()->plan_name,
                    'quantity' => $order->items->first()->quantity,
                    'unit_price' => $order->items->first()->unit_price,
                    'amount' => $order->items->first()->amount,
                    'tax_amount' => $order->items->first()->tax_amount,
                    'total_amount' => $order->items->first()->total_amount,
                ],
            ]),
            'tax_rates' => json_encode([
                'name' => 'Tax',
                'amount' => $order->tax_amount,
                'rate' => $order->tax_amount > 0 ? round(($order->tax_amount / $order->total_amount) * 100, 2) : 0,
            ]),
            'discounts' => json_encode([
                'name' => 'Discount',
                'amount' => 0,
                'rate' => 0,
            ]),
            'metadata' => json_encode([
                'order_id' => $order->id,
                'plan_id' => $order->items->first()->plan_id ?? null,
                'price_id' => $order->items->first()->price_id ?? null,
            ]),
            'history' => json_encode([
                [
                    'status' => $order->status,
                    'changed_at' => Carbon::now(),
                    'note' => 'Invoice created from order',
                ],
            ]),
            'pdf_url' => null,
            'created_by' => $order->user_id,
            'updated_by' => $order->user_id,
        ]);
    }

    protected function generateInvoiceNumber($orderId)
    {
        return 'INV-'.Carbon::now()->format('Ym'). '-' . $orderId.'-'.Str::upper(Str::random(10));
    }
    protected function payment($invoice, $gateway)
    {
        return Payment::create([
           'invoice_id' => $invoice->id,
           'user_id' => $invoice->user_id,
           'external_id' => 'PAY-'.Str::upper(Str::random(10)),
           'type' => 'subscription',
           'status' => 'succeeded',
           'amount' => $invoice->total,
           'fee' => 0,
           'net_amount' => $invoice->total,
           'currency' => $invoice->currency,
           'gateway' => $gateway,
           'gateway_response' => $invoice->metadata['gateway_response'] ?? null,
           'payment_method' => $invoice->metadata['payment_method'] ?? null,
           'processed_at' => Carbon::now(),
           'refunded_at' => null,
           'metadata' => json_encode([
                'order_id' => $invoice->metadata['order_id'] ?? null,
                'plan_id' => $invoice->metadata['plan_id'] ?? null,
                'price_id' => $invoice->metadata['price_id'] ?? null,
           ]),
           'fraud_indicators' => json_encode([
                'is_high_risk' => false,
                'is_fraud' => false,
                'risk_score' => 0,
           ]),
           'created_by' => auth()->id(),
           'updated_by' => auth()->id(),
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

        if ($orderItem && ! $orderItem->subscription_id) {
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
        $existing = PaymentMethod::where('user_id', $user->id)
            ->where('gateway', $request->gateway)
            ->where('gateway_payment_method_id', $paymentResult['payment_method_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->update([
                'last_used_at' => Carbon::now(),
                'usage_count' => $existing->usage_count + 1,
                'gateway_customer_id' => $paymentResult['customer_id'] ?? $existing->gateway_customer_id,
            ]);

            return;
        }

        // Check if this customer ID already exists for another payment method
        if (isset($paymentResult['customer_id']) && ! empty($paymentResult['customer_id'])) {
            // Update any existing payment methods without customer ID
            PaymentMethod::where('user_id', $user->id)
                ->where('gateway', $request->gateway)
                ->whereNull('gateway_customer_id')
                ->update(['gateway_customer_id' => $paymentResult['customer_id']]);
        }

        PaymentMethod::create([
            'user_id' => $user->id,
            'type' => $request->payment_method,
            'gateway' => $request->gateway,
            'gateway_customer_id' => $paymentResult['customer_id'] ?? null,
            'gateway_payment_method_id' => $paymentResult['payment_method_id'] ?? null,
            'is_default' => ! PaymentMethod::where('user_id', $user->id)->exists(),
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

        $number = $prefix.'-'.$date.'-'.$random;

        while (SubscriptionOrder::where('order_number', $number)->exists()) {
            $random = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $number = $prefix.'-'.$date.'-'.$random;
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

        return $prefix.'-'.$date.'-'.$random;
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId($prefix = 'TXN'): string
    {
        return strtoupper($prefix).'-'.time().'-'.rand(1000, 9999);
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

        return $symbol.' '.number_format($amount, 2);
    }

    /**
 * Save SSLCommerz payment method
 */
protected function saveSslCommerzPaymentMethod(User $user, PaymentTransaction $transaction, Request $request, PaymentMaster $paymentMaster)
{
    try {
        $cardType = $request->input('card_type', '');
        $cardBrand = $request->input('card_brand', '');
        $cardIssuer = $request->input('card_issuer', '');

        // Generate a unique payment method ID from transaction
        $gatewayPaymentMethodId = 'sslcommerz_' . $request->input('tran_id') . '_' . time();

        // Determine wallet type based on card type
        $walletType = null;
        $walletNumber = null;

        if (strpos($cardType, 'BKASH') !== false) {
            $walletType = 'bkash';
        } elseif (strpos($cardType, 'NAGAD') !== false) {
            $walletType = 'nagad';
        } elseif (strpos($cardType, 'ROCKET') !== false) {
            $walletType = 'rocket';
        } elseif (strpos($cardType, 'VISA') !== false) {
            $walletType = 'visa';
        } elseif (strpos($cardType, 'MASTER') !== false) {
            $walletType = 'mastercard';
        } elseif (strpos($cardType, 'AMEX') !== false) {
            $walletType = 'amex';
        } else {
            $walletType = strtolower(str_replace(' ', '_', $cardType));
        }

        // Prepare payment result data
        $paymentResult = [
            'payment_method_id' => $gatewayPaymentMethodId,
            'customer_id' => $user->id,
            'card_brand' => $cardBrand,
            'card_last4' => substr($request->input('bank_tran_id', ''), -4),
            'card_exp_month' => null,
            'card_exp_year' => null,
            'wallet_type' => $walletType,
            'bank_name' => $cardIssuer,
            'fingerprint' => $request->input('val_id'),
        ];

        // Create a new request with gateway info
        $methodRequest = new Request();
        $methodRequest->merge([
            'payment_method' => $walletType,
            'gateway' => 'sslcommerz',
            'payment_details' => [
                'card_type' => $cardType,
                'card_issuer' => $cardIssuer,
                'transaction_id' => $request->input('tran_id'),
                'val_id' => $request->input('val_id'),
            ],
            'metadata' => [
                'order_id' => $paymentMaster->metadata['order_id'] ?? null,
                'payment_master_id' => $paymentMaster->id,
            ]
        ]);

        // Check if this payment method already exists
        $existingMethod = PaymentMethod::where('user_id', $user->id)
            ->where('gateway', 'sslcommerz')
            ->where(function($query) use ($walletType, $cardIssuer) {
                $query->where('wallet_type', $walletType)
                      ->orWhere('bank_name', $cardIssuer);
            })
            ->first();

        if ($existingMethod) {
            // Update existing method
            $existingMethod->update([
                'last_used_at' => Carbon::now(),
                'usage_count' => $existingMethod->usage_count + 1,
                'gateway_metadata' => json_encode(array_merge(
                    json_decode($existingMethod->gateway_metadata ?? '{}', true),
                    ['last_transaction_id' => $request->input('tran_id')]
                )),
            ]);

            Log::info('Existing SSLCommerz payment method updated', [
                'method_id' => $existingMethod->id,
                'user_id' => $user->id
            ]);

            return;
        }

        // Create new payment method
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => $walletType,
            'gateway' => 'sslcommerz',
            'gateway_customer_id' => null, // SSLCommerz doesn't have customer ID
            'gateway_payment_method_id' => $gatewayPaymentMethodId,
            'is_default' => !PaymentMethod::where('user_id', $user->id)->exists(),
            'is_verified' => true,
            'verified_at' => Carbon::now(),
            'card_brand' => $cardBrand,
            'card_last4' => $paymentResult['card_last4'],
            'wallet_type' => $walletType,
            'wallet_number' => null,
            'bank_name' => $cardIssuer,
            'metadata' => json_encode([
                'card_type' => $cardType,
                'card_issuer' => $cardIssuer,
                'initial_transaction' => $request->input('tran_id'),
            ]),
            'gateway_metadata' => json_encode([
                'val_id' => $request->input('val_id'),
                'tran_id' => $request->input('tran_id'),
                'card_type' => $cardType,
                'card_issuer' => $cardIssuer,
            ]),
            'last_used_at' => Carbon::now(),
            'usage_count' => 1,
            'verified_by' => $user->id,
            'created_by' => $user->id,
        ]);

        Log::info('New SSLCommerz payment method saved', [
            'method_id' => $paymentMethod->id,
            'user_id' => $user->id,
            'wallet_type' => $walletType
        ]);

    } catch (Exception $e) {
        Log::error('Failed to save SSLCommerz payment method: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => $user->id ?? null
        ]);
        // Don't throw exception - payment already successful
    }
}
}
