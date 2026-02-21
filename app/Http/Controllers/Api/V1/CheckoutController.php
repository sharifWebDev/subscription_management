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
use App\Services\PaymentGateways\PayPalGateway;
use App\Services\PaymentGateways\StripeGateway;
use App\Services\PaymentGateways\SurjoPayGateway;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function __construct(
        OTPService $otpService,
        SubscriptionService $subscriptionService,
        PaymentService $paymentService,
        StripeGateway $stripeGateway,
        PayPalGateway $paypalGateway,
        SurjoPayGateway $surjoPayGateway,
        BkashGateway $bkashGateway
    ) {
        $this->otpService = $otpService;
        $this->subscriptionService = $subscriptionService;
        $this->paymentService = $paymentService;
        $this->stripeGateway = $stripeGateway;
        $this->paypalGateway = $paypalGateway;
        $this->surjoPayGateway = $surjoPayGateway;
        $this->bkashGateway = $bkashGateway;
    }

    /**
     * Initialize checkout
     */
    public function initialize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'price_id' => 'required|exists:plan_prices,id',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly',
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
    public function sendOtp(Request $request)
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
    public function verifyOtpAndCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'plan_id' => 'required|exists:plans,id',
            'price_id' => 'required|exists:plan_prices,id',
            'payment_method' => 'required|string',
            'gateway' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
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
                    'name' => $request->first_name.' '.$request->last_name,
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

            // Create order
            $order = $this->createOrder($user, $request);
            $order = $order->with('items')->first();

            // Create payment master
            $paymentMaster = $this->createPaymentMaster($user, $order, $request);

            // Process payment based on gateway
            $paymentResult = $this->processGatewayPayment($paymentMaster, $order, $user, $request);

            if (! $paymentResult['success']) {
                throw new Exception($paymentResult['message'] ?? 'Payment processing failed');
            }

            // If payment is immediately successful (like card), create subscription
            if ($paymentResult['status'] === 'completed') {
                $subscription = $this->createSubscription($user, $order, $request, $paymentMaster);

                // Update order with subscription
                $order->items()->update([
                    'subscription_id' => $subscription->id,
                    'subscription_status' => 'created',
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
            Log::error('Checkout failed: '.$e->getMessage(), [
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
    public function processAuthenticated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'price_id' => 'required|exists:plan_prices,id',
            'payment_method' => 'required|string',
            'gateway' => 'required|string',
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
            $order = $order->with('items')->first();

            // Create payment master
            $paymentMaster = $this->createPaymentMaster($user, $order, $request);

            // Process payment based on gateway
            $paymentResult = $this->processGatewayPayment($paymentMaster, $order, $user, $request);

            if (! $paymentResult['success']) {
                throw new Exception($paymentResult['message'] ?? 'Payment processing failed');
            }

            // If payment is immediately successful, create subscription
            if ($paymentResult['status'] === 'completed') {
                $subscription = $this->createSubscription($user, $order, $request, $paymentMaster);

                // Update order with subscription
                $order->items()->update([
                    'subscription_id' => $subscription->id,
                    'subscription_status' => 'created',
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
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Checkout processed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                    'requires_redirect' => $paymentResult['requires_redirect'] ?? false,
                ],
            ];

            if (isset($paymentResult['redirect_url'])) {
                $response['data']['redirect_url'] = $paymentResult['redirect_url'];
            }

            if (isset($paymentResult['client_secret'])) {
                $response['data']['client_secret'] = $paymentResult['client_secret'];
            }

            return response()->json($response);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Authenticated checkout failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle payment callback/success
     */
    public function handleCallback(Request $request, $gateway)
    {
        try {
            switch ($gateway) {
                case 'stripe':
                    return $this->handleStripeCallback($request);
                case 'paypal':
                    return $this->handlePayPalCallback($request);
                case 'surjopay':
                    return $this->handleSurjoPayCallback($request);
                case 'bkash':
                    return $this->handleBkashCallback($request);
                default:
                    throw new Exception('Unsupported gateway');
            }
        } catch (Exception $e) {
            Log::error('Payment callback failed: '.$e->getMessage());

            return redirect()->route('website.plans.index')
                ->with('error', 'Payment processing failed');
        }
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
                'name' => $request->first_name.' '.$request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
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
     * Process gateway payment
     */
    protected function processGatewayPayment(PaymentMaster $paymentMaster, SubscriptionOrder $order, User $user, Request $request): array
    {
        \Log::info('Processing gateway payment', [
            'paymentMaster' => $paymentMaster,
            'order' => $order,
            'user' => $user,
            'request' => $request->all(),
        ]);
        $gateway = $request->gateway;
        $paymentData = [
            'payment_master_id' => $paymentMaster->id,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'user_id' => $user->id,
            'email' => $user->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'plan_id' => $request->plan_id,
            'price_id' => $request->price_id,
            'description' => 'Subscription: '.$order->items->first()->plan_name,
        ];

        switch ($gateway) {
            case 'stripe':
                return $this->processStripePayment($paymentData, $request);
            case 'paypal':
                return $this->processPayPalPayment($paymentData, $request);
            case 'surjopay':
                return $this->processSurjoPayPayment($paymentData, $request);
            case 'bkash':
                return $this->processBkashPayment($paymentData, $request);
            case 'bank_transfer':
                return $this->processBankTransfer($paymentData);
            default:
                throw new Exception("Unsupported gateway: {$gateway}");
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(array $data, Request $request): array
    {
        if (isset($request->payment_details['payment_method_id'])) {
            // Confirm payment with existing payment method
            $result = $this->stripeGateway->confirmPaymentIntent(
                $request->payment_details['payment_intent_id'],
                $request->payment_details['payment_method_id']
            );
        } else {
            // Create new payment intent
            $result = $this->stripeGateway->createPaymentIntent($data);
        }

        if (! $result['success']) {
            return $result;
        }

        // Create transaction
        $transaction = PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
            'transaction_id' => $result['intent_id'] ?? $result['intent']->id,
            'type' => 'payment',
            'payment_method' => 'card',
            'payment_gateway' => 'stripe',
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => $result['status'] ?? 'requires_confirmation',
            'gateway_response' => json_encode($result),
            'initiated_at' => Carbon::now(),
        ]);

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

        return [
            'success' => true,
            'status' => 'completed',
            'transaction_id' => $transaction->id,
            'payment_method_id' => $request->payment_details['payment_method_id'] ?? null,
        ];
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(array $data, Request $request): array
    {
        $result = $this->paypalGateway->createOrder($data);

        if (! $result['success']) {
            return $result;
        }

        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
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
        $data['customer_name'] = $request->first_name.' '.$request->last_name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;

        $result = $this->surjoPayGateway->createPayment($data);

        if (! $result['success']) {
            return $result;
        }

        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
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
     * Process bKash payment
     */
    protected function processBkashPayment(array $data, Request $request): array
    {
        $data['payer_reference'] = $request->phone ?? $request->email;

        $result = $this->bkashGateway->createPayment($data);

        if (! $result['success']) {
            return $result;
        }

        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
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
     * Process bank transfer
     */
    protected function processBankTransfer(array $data): array
    {
        // Create transaction
        PaymentTransaction::create([
            'payment_master_id' => $data['payment_master_id'],
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
            'message' => 'Bank transfer initiated',
        ];
    }

    /**
     * Create subscription
     */
    protected function createSubscription(User $user, SubscriptionOrder $order, Request $request, PaymentMaster $paymentMaster)
    {
        return $this->subscriptionService->createSubscription([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'price_id' => $request->price_id,
            'quantity' => 1,
            'gateway' => $request->gateway,
            'metadata' => [
                'order_id' => $order->id,
                'payment_master_id' => $paymentMaster->id,
            ],
        ]);
    }

    /**
     * Save payment method for future use
     */
    protected function savePaymentMethod(User $user, array $paymentResult, Request $request): void
    {
        $paymentMethod = PaymentMethod::create([
            'user_id' => $user->id,
            'type' => $request->payment_method,
            'gateway' => $request->gateway,
            'gateway_payment_method_id' => $paymentResult['payment_method_id'] ?? null,
            'is_default' => ! PaymentMethod::where('user_id', $user->id)->exists(),
            'is_verified' => true,
            'metadata' => json_encode($request->payment_details ?? []),
            'last_used_at' => Carbon::now(),
            'usage_count' => 1,
        ]);

        // Update user's default payment method
        if ($paymentMethod->is_default) {
            $user->update(['preferred_payment_method' => $request->payment_method]);
        }
    }

    /**
     * Handle Stripe callback
     */
    protected function handleStripeCallback(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');

        $transaction = PaymentTransaction::where('transaction_id', $paymentIntentId)->first();

        if (! $transaction) {
            throw new Exception('Transaction not found');
        }

        if ($request->get('redirect_status') === 'succeeded') {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);

            $paymentMaster = $transaction->paymentMaster;
            $paymentMaster->update([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            // Activate subscription
            $order = SubscriptionOrder::find($paymentMaster->metadata['order_id']);
            if ($order) {
                $this->activateOrderSubscription($order, $paymentMaster);
            }

            return redirect()->route('website.dashboard.subscriptions')
                ->with('success', 'Payment successful! Your subscription is now active.');
        }

        return redirect()->route('website.plans.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle PayPal callback
     */
    protected function handlePayPalCallback(Request $request)
    {
        $token = $request->get('token');
        $PayerID = $request->get('PayerID');

        if (! $token || ! $PayerID) {
            throw new Exception('Invalid PayPal callback');
        }

        $transaction = PaymentTransaction::where('transaction_id', $token)->first();

        if (! $transaction) {
            throw new Exception('Transaction not found');
        }

        // Capture payment
        $result = $this->paypalGateway->captureOrder($token);

        if ($result['success']) {
            $transaction->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
                'gateway_response' => json_encode($result),
            ]);

            $paymentMaster = $transaction->paymentMaster;
            $paymentMaster->update([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);

            // Activate subscription
            $order = SubscriptionOrder::find($paymentMaster->metadata['order_id']);
            if ($order) {
                $this->activateOrderSubscription($order, $paymentMaster);
            }

            return redirect()->route('website.dashboard.subscriptions')
                ->with('success', 'Payment successful! Your subscription is now active.');
        }

        return redirect()->route('website.plans.index')
            ->with('error', 'Payment capture failed. Please try again.');
    }

    /**
     * Handle SurjoPay callback
     */
    protected function handleSurjoPayCallback(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status');

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();

        if (! $transaction) {
            throw new Exception('Transaction not found');
        }

        if ($status === 'COMPLETED') {
            // Verify payment
            $verification = $this->surjoPayGateway->verifyPayment($transactionId);

            if ($verification['success']) {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'gateway_response' => json_encode($verification),
                ]);

                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);

                // Activate subscription
                $order = SubscriptionOrder::find($paymentMaster->metadata['order_id']);
                if ($order) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                return redirect()->route('website.dashboard.subscriptions')
                    ->with('success', 'Payment successful! Your subscription is now active.');
            }
        }

        return redirect()->route('website.plans.index')
            ->with('error', 'Payment verification failed.');
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
            throw new Exception('Transaction not found');
        }

        if ($status === 'success') {
            // Execute payment
            $result = $this->bkashGateway->executePayment($paymentId);

            if ($result['success']) {
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'gateway_response' => json_encode($result),
                ]);

                $paymentMaster = $transaction->paymentMaster;
                $paymentMaster->update([
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);

                // Activate subscription
                $order = SubscriptionOrder::find($paymentMaster->metadata['order_id']);
                if ($order) {
                    $this->activateOrderSubscription($order, $paymentMaster);
                }

                return redirect()->route('website.dashboard.subscriptions')
                    ->with('success', 'Payment successful! Your subscription is now active.');
            }
        }

        return redirect()->route('website.plans.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Activate subscription from order
     */
    protected function activateOrderSubscription(SubscriptionOrder $order, PaymentMaster $paymentMaster)
    {
        $orderItem = $order->items()->first();

        if ($orderItem && ! $orderItem->subscription_id) {
            $subscription = $this->subscriptionService->createSubscription([
                'user_id' => $order->user_id,
                'plan_id' => $orderItem->plan_id,
                'price_id' => $order->metadata['price_id'],
                'quantity' => $orderItem->quantity,
                'gateway' => $order->metadata['gateway'],
                'metadata' => [
                    'order_id' => $order->id,
                    'payment_master_id' => $paymentMaster->id,
                ],
            ]);

            $orderItem->update([
                'subscription_id' => $subscription->id,
                'subscription_status' => 'created',
                'processed_at' => Carbon::now(),
            ]);

            $order->update([
                'status' => 'completed',
                'payment_master_id' => $paymentMaster->id,
                'processed_at' => Carbon::now(),
            ]);
        }
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
}
