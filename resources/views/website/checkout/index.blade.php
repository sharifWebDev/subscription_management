@extends('website.layouts.app')

@section('title', 'Checkout')

@section('content')
    <!-- Breadcrumb -->
    <section class="bg-light pb-2 pt-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('website.plans.index') }}"
                            class="text-decoration-none">Plans</a></li>
                    <li class="breadcrumb-item"><a href="#" id="planBreadcrumbLink"
                            class="text-decoration-none">Loading...</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Checkout Form -->
    <section class="py-2">
        <div class="container">
            <div id="checkoutLoader" class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading checkout information...</p>
            </div>

            <div id="checkoutContent" style="display: none;">
                <div class="row g-4">
                    <!-- Payment Form -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div
                                class="card-header bg-white border-0 px-4 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                <div class="align-items-center">
                                    <h5 class="mb-0 fw-bold">Complete Your Purchase</h5>
                                    <p class="text-muted mb-0 small mt-1">Choose your payment method and fill in the details
                                    </p>
                                </div>
                                <!-- Billing Cycle -->
                                <div class="w-50 pt-3 align-items-center justify-content-center text-center">
                                    <div class="form-label h5 fw-bold">Billing Cycle</div>
                                    <div class="btn-group w-100 flex-wrap small" role="group" id="billingCycles"></div>
                                </div>
                            </div>

                            <div class="card-body px-4">
                                <form action="javascript:void(0);" id="checkoutForm">
                                    @csrf
                                    <input type="hidden" name="plan_id" id="plan_id" value="{{ $plan_id }}">
                                    <input type="hidden" name="price_id" id="price_id" value="">
                                    <input type="hidden" name="gateway" id="selected_gateway" value="">
                                    <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                                    <input type="hidden" name="save_payment_method" id="save_payment_method"
                                        value="">

                                    <!-- Personal Information -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-6 col-md-6 col-lg-4">
                                            <label for="name" class="form-label small"> Name</label>
                                            <input type="text" class="form-control form-control-md rounded-3"
                                                placeholder="Enter your name..." id="name" name="name" required
                                                value="{{ old('name', auth()->user()?->name ?? '') }}">
                                        </div>
                                        <div class="col-6 col-md-6 col-lg-4">
                                            <label for="email" class="form-label small">Email Address</label>
                                            <input type="email" class="form-control form-control-md rounded-3"
                                                placeholder="Enter your email address..." id="email" name="email"
                                                required value="{{ old('email', auth()->user()?->email ?? '') }}">
                                        </div>
                                        <div class="col-6 col-md-6 col-lg-4">
                                            <label for="phone" class="form-label small">Phone Number</label>
                                            <input type="tel" value="{{ old('phone', auth()->user()?->phone ?? '') }}"
                                                class="form-control form-control-md rounded-3"
                                                placeholder="Enter your phone number..." id="phone" name="phone">
                                        </div>
                                    </div>

                                    <!-- Saved Payment Methods (for logged in users) -->
                                    @auth
                                        <div class="mb-4" id="savedPaymentMethodsSection" style="display: none;">
                                            <label class="form-label small">Your Saved Payment Methods</label>
                                            <div id="savedMethodsLoader" class="text-center py-2">
                                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                            </div>
                                            <div id="savedMethodsContainer" class="row g-3"></div>

                                            <div class="mt-3 text-center">
                                                <button type="button" class="btn btn-link text-decoration-none"
                                                    id="useNewMethodBtn">
                                                    <i class="fas fa-plus-circle me-2"></i>Use a different payment method
                                                </button>
                                            </div>
                                        </div>
                                    @endauth

                                    <!-- Payment Methods Grid -->
                                    <div class="mb-3" id="paymentMethodsSection">
                                        <label class="form-label small fw-semibold">Select Payment Method</label>
                                        <span id="viewSavedMethods" class="text-decoration-none"></span>
                                        <div class="row g-2" id="paymentMethodsGrid"></div>

                                        <!-- Dynamic Payment Details Section -->
                                        <div id="paymentDetailsContainer" class="mt-3"></div>
                                    </div>

                                    <!-- Save Payment Method Checkbox -->
                                    @auth
                                        <div class="mb-2" id="saveMethodCheckbox" style="display: none;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="save_payment_method_checkbox" checked>
                                                <label class="form-check-label small" for="save_payment_method_checkbox">
                                                    Save this payment method for future purchases
                                                </label>
                                            </div>
                                        </div>
                                    @endauth

                                    <!-- Terms -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="terms"
                                                id="terms" required>
                                            <label class="form-check-label small" for="terms">
                                                I agree to the <a href="#" class="text-decoration-none">Terms of
                                                    Service</a> and
                                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-md w-100 rounded-3 py-2 fw-semibold"
                                        id="submitBtn">
                                        <i class="fas fa-paper-plane me-2 transform rotate-45"></i>Complete Purchase
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 2rem;">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="mb-0 fw-bold">Order Summary</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div id="orderSummaryLoader" class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                </div>

                                <div id="orderSummaryContent" style="display: none;">
                                    <!-- Plan Details -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                            <i class="fas fa-crown text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold" id="summaryPlanName"></h6>
                                            <p class="text-muted small mb-0" id="summaryInterval"></p>
                                        </div>
                                    </div>

                                    <!-- Price Breakdown -->
                                    <div class="bg-light rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Subtotal:</span>
                                            <span class="fw-medium" id="summarySubtotal"></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Tax ({{ config('app.tax_rate', 10) }}%):</span>
                                            <span class="fw-medium" id="summaryTax"></span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Total:</span>
                                            <span class="h5 mb-0 text-primary fw-bold" id="summaryTotal"></span>
                                        </div>
                                    </div>

                                    <!-- Features -->
                                    <div class="mb-3">
                                        <p class="small text-muted mb-2"><i
                                                class="fas fa-check-circle text-success me-2"></i>Secure SSL encrypted
                                            payment</p>
                                        <p class="small text-muted mb-2"><i
                                                class="fas fa-check-circle text-success me-2"></i>Money-back guarantee</p>
                                        <p class="small text-muted mb-0"><i
                                                class="fas fa-check-circle text-success me-2"></i>Cancel anytime</p>
                                    </div>

                                    <!-- Trust Badges -->
                                    <div class="text-center mt-4">
                                        <div class="d-flex justify-content-center gap-3">
                                            <i class="fab fa-cc-visa fa-2x text-secondary"></i>
                                            <i class="fab fa-cc-mastercard fa-2x text-secondary"></i>
                                            <i class="fab fa-cc-amex fa-2x text-secondary"></i>
                                            <i class="fab fa-cc-paypal fa-2x text-secondary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTP Verification Modal -->
        <div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="otpModalLabel">Verify Your Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div id="otpStep1">
                            <p class="text-muted mb-4">We've sent a verification code to <strong class="text-dark"
                                    id="otpEmailDisplay"></strong></p>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Enter 6-digit code</label>
                                <input type="text" class="form-control form-control-md text-center rounded-3"
                                    id="otpInput" maxlength="6" placeholder="000000"
                                    style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="text-muted small">Code expires in <span id="otpTimer"
                                        class="fw-bold text-primary">10:00</span></span>
                                <button type="button" class="btn btn-link text-decoration-none p-0"
                                    id="resendOtpBtn">Resend code</button>
                            </div>

                            <button type="button" class="btn btn-primary w-100 rounded-3 py-3 fw-semibold"
                                id="verifyOtpBtn" disabled>
                                <span class="spinner-border spinner-border-sm d-none" id="otpSpinner"></span>
                                Verify & Complete Purchase
                            </button>
                        </div>

                        <div id="otpStep2" style="display: none;">
                            <div class="text-center py-4">
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-success fa-4x"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Verification Successful!</h5>
                                <p class="text-muted mb-4">Please wait while we process your payment...</p>
                                <div class="spinner-border text-primary"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .payment-method-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent !important;
            height: 100%;
        }

        .payment-method-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .payment-method-card.selected {
            border-color: #0d6efd !important;
            background-color: #f0f7ff;
        }

        .payment-method-card .btn-check:checked+.btn {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .payment-method-card .btn {
            transition: all 0.3s;
            height: 100%;
            min-height: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 2px solid #e9ecef;
        }

        .payment-method-card .btn:hover {
            background-color: #f8f9fa;
        }

        .saved-method-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            border-radius: 12px;
            height: 100%;
        }

        .saved-method-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .saved-method-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }

        .saved-method-card.default {
            border-color: #198754;
            background-color: #f0fff4;
        }

        .btn-group .btn {
            flex: 1;
            border-radius: 12px !important;
            margin: 0 4px;
            padding: 12px;
        }

        .btn-group .btn:first-child {
            margin-left: 0;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        #stripe-card-element {
            padding: 12px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
        }

        .sticky-top {
            top: 2rem;
            z-index: 1020;
        }

        .bank-detail-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            border-left: 3px solid #0d6efd;
        }

        .copy-btn {
            transition: all 0.2s;
        }

        .copy-btn:hover {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $(document).ready(function() {
                const planId = '{{ $plan_id }}';
                const urlParams = new URLSearchParams(window.location.search);
                const selectedPriceId = urlParams.get('price_id');
                const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

                // State variables
                let planData = null;
                let selectedPrice = null;
                let userPaymentMethods = [];
                let paymentGateways = [];
                let selectedPaymentMethod = null;
                let selectedGateway = null;
                let stripe = null;
                let stripeElements = null;
                let stripeCard = null;
                let checkoutData = {};
                let otpTimer = null;

                // Constants
                const TAX_RATE = {{ config('app.tax_rate', 10) }};
                const CURRENCY = '{{ config('app.currency', 'USD') }}';

                // Initialize page
                fetchPlanDetails();
                if (isLoggedIn) {
                    fetchUserPaymentMethods();
                }
                fetchPaymentGateways();

                /**
                 * Fetch plan details from API
                 */
                function fetchPlanDetails() {
                    axios.get(`/plans/${planId}`)
                        .then(response => {
                            planData = response.data.data || response.data;
                            renderCheckout();
                            $('#checkoutLoader').hide();
                            $('#checkoutContent').fadeIn();
                        })
                        .catch(error => {
                            console.error('Error fetching plan:', error);
                            $('#checkoutLoader').hide();
                            $('#checkoutContent').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                        <h3>Error Loading Checkout</h3>
                        <p class="text-muted">The plan you're trying to purchase could not be found.</p>
                        <a href="{{ route('website.plans.index') }}" class="btn btn-primary mt-3 rounded-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Plans
                        </a>
                    </div>
                `).fadeIn();
                        });
                }

                /**
                 * Fetch user's saved payment methods
                 */
                function fetchUserPaymentMethods() {
                    axios.get('/payment-methods')
                        .then(response => {
                            userPaymentMethods = response.data.data || response.data || [];
                            if (userPaymentMethods.length > 0) {
                                renderSavedPaymentMethods();
                                $('#savedPaymentMethodsSection').fadeIn();
                                $('#paymentMethodsSection').hide();
                            } else {
                                $('#savedMethodsLoader').hide();
                                $('#savedPaymentMethodsSection').hide();
                                $('#paymentMethodsSection').fadeIn();
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching payment methods:', error);
                            $('#savedMethodsLoader').hide();
                            $('#savedPaymentMethodsSection').hide();
                            $('#paymentMethodsSection').fadeIn();
                        });
                }

                /**
                 * Render saved payment methods
                 */
                function renderSavedPaymentMethods() {
                    if (!userPaymentMethods?.length) return;

                    let html = '';
                    const defaultMethod = userPaymentMethods.find(m => m.is_default);

                    userPaymentMethods.forEach(method => {
                        const isDefault = method.is_default ? 'default' : '';
                        const selected = method.is_default ? 'selected' : '';
                        const gatewayMethodId = method.gateway_payment_method_id;

                        // Display card info
                        let displayText = 'Card';
                        if (method.card_brand && method.card_last4) {
                            displayText = `${method.card_brand.toUpperCase()} •••• ${method.card_last4}`;
                        }

                        let icon = method.card_brand === 'visa' ? 'fab fa-cc-visa' :
                            method.card_brand === 'mastercard' ? 'fab fa-cc-mastercard' :
                            method.card_brand === 'amex' ? 'fab fa-cc-amex' : 'fas fa-credit-card';

                        html += `
                <div class="col-md-6 col-lg-4 col-sm-6">
                    <div class="card saved-method-card ${isDefault} ${selected} p-3"
                        onclick="selectSavedMethod('${gatewayMethodId}', '${method.id}', this, '${method.gateway}')">
                        <div class="d-flex align-items-center">
                            <i class="${icon} fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="mb-0 fw-semibold">${displayText}</h6>
                                ${isDefault ? '<span class="badge bg-success mt-1">Default</span>' : ''}
                                <small class="text-muted d-block">${method.gateway}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    });

                    $('#savedMethodsContainer').html(html);
                    $('#savedMethodsLoader').hide();

                    if (defaultMethod) {
                        selectSavedMethod(
                            defaultMethod.gateway_payment_method_id,
                            defaultMethod.id,
                            null,
                            defaultMethod.gateway
                        );
                    }
                }

                /**
                 * Select saved payment method
                 */
                window.selectSavedMethod = function(gatewayMethodId, localMethodId, element, gateway) {
                    selectedPaymentMethod = userPaymentMethods.find(m =>
                        m.gateway_payment_method_id === gatewayMethodId || m.id == localMethodId
                    );

                    if (!selectedPaymentMethod) return;

                    selectedGateway = gateway || selectedPaymentMethod.gateway;

                    $('.saved-method-card').removeClass('selected');
                    if (element) {
                        $(element).addClass('selected');
                    } else {
                        $(`.saved-method-card[onclick*="${gatewayMethodId}"]`).addClass('selected');
                    }

                    $('#selected_gateway').val(selectedGateway);
                    $('#payment_method_id').val(gatewayMethodId);
                    $('#paymentMethodsSection').hide();
                    $('#paymentDetailsContainer').empty();
                    $('#saveMethodCheckbox').hide();
                };

                /**
                 * Fetch available payment gateways
                 */
                function fetchPaymentGateways() {
                    return axios.get('/payment-gateways')
                        .then(response => {
                            const responseData = response.data;
                            if (responseData.data && responseData.data.data) {
                                paymentGateways = responseData.data.data;
                            } else if (responseData.data) {
                                paymentGateways = responseData.data;
                            } else {
                                paymentGateways = responseData;
                            }
                            renderPaymentMethodsGrid();
                            initializeStripe();
                            return paymentGateways;
                        })
                        .catch(error => {
                            console.error('Error fetching gateways:', error);
                            return [];
                        });
                }

                /**
                 * Initialize Stripe
                 */
                function initializeStripe() {
                    const stripeGateway = paymentGateways.find(g => g.code === 'stripe');
                    if (stripeGateway) {
                        const publicKey = stripeGateway.public_key ||
                            '{{ config('payment.gateways.stripe.test_public_key') }}';
                        stripe = Stripe(publicKey);
                        console.log('Stripe initialized');
                    }
                }

                /**
                 * Render checkout page
                 */
                function renderCheckout() {
                    if (planData && planData.slug) {
                        $('#planBreadcrumbLink').attr('href', `/plan/${planData.slug}`).text(planData.name);
                    }

                    if (selectedPriceId && planData?.prices) {
                        selectedPrice = planData.prices.find(p => p.id == selectedPriceId);
                    }
                    if (!selectedPrice && planData?.prices?.length > 0) {
                        selectedPrice = planData.prices[0];
                    }

                    if (selectedPrice) {
                        $('#price_id').val(selectedPrice.id);
                    }

                    renderBillingCycles();
                    renderOrderSummary();
                }

                /**
                 * Render billing cycle options
                 */
                function renderBillingCycles() {
                    if (!planData?.prices?.length) return;

                    let html = '';
                    planData.prices.forEach(price => {
                        let isSelected = selectedPrice && price.id === selectedPrice.id;
                        let badgeHtml = price.interval === 'year' ?
                            '<span class="badge bg-success ms-1">Save 20%</span>' : '';

                        html += `
                <span class="alert w-100 text-center py-1 ${isSelected ? 'alert-primary' : 'alert-outline-primary'} billing-cycle"
                      style="cursor: pointer;"
                      data-price-id="${price.id}"
                      data-amount="${price.amount}"
                      data-interval="${price.interval}">
                    ${ucfirst(price.interval)}ly ${badgeHtml}
                </span>
            `;
                    });

                    $('#billingCycles').html(html);

                    $('.billing-cycle').click(function() {
                        $('.billing-cycle').removeClass('alert-primary').addClass('alert-outline-primary');
                        $(this).addClass('alert-primary').removeClass('alert-outline-primary');

                        let priceId = $(this).data('price-id');
                        $('#price_id').val(priceId);

                        selectedPrice = planData.prices.find(p => p.id == priceId);
                        updateOrderSummary(selectedPrice);
                    });
                }

                /**
                 * Render order summary
                 */
                function renderOrderSummary() {
                    if (!selectedPrice) return;

                    let amount = parseFloat(selectedPrice.amount);
                    let tax = amount * (TAX_RATE / 100);
                    let total = amount + tax;

                    $('#summaryPlanName').text(planData.name);
                    $('#summaryInterval').text(selectedPrice.interval_description || '/' + selectedPrice.interval);
                    $('#summarySubtotal').text(formatMoney(amount));
                    $('#summaryTax').text(formatMoney(tax));
                    $('#summaryTotal').text(formatMoney(total));

                    $('#orderSummaryLoader').hide();
                    $('#orderSummaryContent').fadeIn();
                }

                /**
                 * Update order summary when billing cycle changes
                 */
                function updateOrderSummary(price) {
                    let amount = parseFloat(price.amount);
                    let tax = amount * (TAX_RATE / 100);
                    let total = amount + tax;

                    $('#summaryInterval').text(price.interval_description || '/' + price.interval);
                    $('#summarySubtotal').text(formatMoney(amount));
                    $('#summaryTax').text(formatMoney(tax));
                    $('#summaryTotal').text(formatMoney(total));
                }

                /**
                 * Render payment methods grid
                 */
                function renderPaymentMethodsGrid() {
                    if (!paymentGateways?.length) return;

                    let html = '';
                    paymentGateways.forEach(gateway => {
                        const icon = getGatewayIcon(gateway.code);
                        const name = gateway.name || ucfirst(gateway.code.replace('_', ' '));

                        html += `
                <div class="col-md-4 col-lg-2 col-sm-6">
                    <div class="payment-method-card">
                        <input type="radio" class="btn-check" name="payment_method"
                               id="gateway_${gateway.code}" value="${gateway.code}" autocomplete="off"
                               data-gateway="${gateway.code}">
                        <label class="btn btn-outline-secondary w-100 py-1" for="gateway_${gateway.code}">
                            <i class="${icon} fa-xl mb-3 mt-3"></i>
                            <span style="font-size: 11px">${name}</span>
                        </label>
                    </div>
                </div>
            `;
                    });

                    $('#paymentMethodsGrid').html(html);
                }

                /**
                 * Payment method selection handler
                 */
                $(document).on('change', 'input[name="payment_method"]', function() {
                        $('.payment-method-card').removeClass('selected');
                        $(this).closest('.payment-method-card').addClass('selected');

                        selectedGateway = $(this).data('gateway');
                        selectedPaymentMethod = null;
                        $('#selected_gateway').val(selectedGateway);
                        $('#payment_method_id').val('');

                        loadPaymentDetails(selectedGateway);

                        @auth
                        $('#saveMethodCheckbox').fadeIn();
                    @endauth
                });

            /**
             * Load payment details form based on gateway
             */
            function loadPaymentDetails(gateway) {
                $('#paymentDetailsContainer').empty();

                switch (gateway) {
                    case 'stripe':
                        loadStripePayment();
                        break;
                    case 'bank_transfer':
                        loadBankTransferPayment();
                        break;
                    case 'bkash':
                    case 'rocket':
                    case 'nagad':
                    case 'surjopay':
                    case 'sslcommerz':
                    case 'paypal':
                        loadRedirectPayment(gateway);
                        break;
                    default:
                        loadRedirectPayment(gateway);
                        break;
                }
            }

            /**
             * Load Stripe payment form
             */
            function loadStripePayment() {
                let template = `
            <div class="card border-0 bg-light rounded-3 p-4">
                <h6 class="fw-semibold mb-3">Card Details</h6>
                <div id="stripe-card-element" class="form-control form-control-md rounded-3"></div>
                <div id="stripe-errors" class="text-danger mt-2 small"></div>

                <div class="mt-3 small text-muted">
                    <i class="fas fa-lock me-2"></i>Your card information is securely processed by Stripe
                </div>
            </div>
        `;

                $('#paymentDetailsContainer').html(template);

                if (!stripe) {
                    initializeStripe();
                }

                if (stripe) {
                    stripeElements = stripe.elements();
                    stripeCard = stripeElements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
                                '::placeholder': {
                                    color: '#aab7c4'
                                }
                            }
                        }
                    });

                    stripeCard.mount('#stripe-card-element');

                    stripeCard.on('change', function(event) {
                        let displayError = document.getElementById('stripe-errors');
                        if (event.error) {
                            displayError.textContent = event.error.message;
                        } else {
                            displayError.textContent = '';
                        }
                    });
                }
            }

            /**
             * Load bank transfer payment form
             */
            function loadBankTransferPayment() {
                const bankGateway = paymentGateways.find(g => g.code === 'bank_transfer');

                const bankDetails = {
                    bank_name: bankGateway?.config?.bank_name || 'Example Bank',
                    account_name: bankGateway?.config?.account_name || 'Your Company Name',
                    account_number: bankGateway?.config?.account_number || '1234567890',
                    routing_number: bankGateway?.config?.routing_number || '987654321',
                    swift_code: bankGateway?.config?.swift_code || 'EXMPLBDX'
                };

                let template = `
            <div class="card border-0 bg-light rounded-3 p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-university fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-semibold mb-1">Bank Transfer</h6>
                        <p class="text-muted small mb-0">Complete your payment via bank transfer</p>
                    </div>
                </div>

                <!-- Bank Account Details -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-0 py-2 px-4">
                        <h6 class="fw-semibold mb-0">Bank Account Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-2">
                            <div class="col-md-6 col-lg-4">
                                <label class="text-muted small mb-1">Bank Name</label>
                                <p class="fw-semibold mb-0">${bankDetails.bank_name}</p>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="text-muted small mb-1">Account Name</label>
                                <p class="fw-semibold mb-0">${bankDetails.account_name}</p>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="text-muted small mb-1">Account Number</label>
                                <p class="fw-semibold mb-0">${bankDetails.account_number}</p>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="text-muted small mb-1">Routing Number</label>
                                <p class="fw-semibold mb-0">${bankDetails.routing_number}</p>
                            </div>
                            ${bankDetails.swift_code ? `
                                        <div class="col-md-6 col-lg-4">
                                            <label class="text-muted small mb-1">SWIFT Code</label>
                                            <p class="fw-semibold mb-0">${bankDetails.swift_code}</p>
                                        </div>
                                    ` : ''}
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary copy-btn mt-3" onclick="copyBankDetails()">
                            <i class="fas fa-copy me-1"></i>Copy All Details
                        </button>
                    </div>
                </div>

                <!-- User Payment Details -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-semibold mb-0">Your Payment Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sender_account_number" class="form-label fw-semibold">
                                    Your Account Number <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-md rounded-3"
                                       id="sender_account_number"
                                       name="sender_account_number"
                                       placeholder="Enter your bank account number"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_transaction_id" class="form-label fw-semibold">
                                    Transaction ID <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-md rounded-3"
                                       id="bank_transaction_id"
                                       name="bank_transaction_id"
                                       placeholder="Enter transaction/reference ID"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="transfer_date" class="form-label fw-semibold">
                                    Transfer Date <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control form-control-md rounded-3"
                                       id="transfer_date"
                                       name="transfer_date"
                                       value="${new Date().toISOString().split('T')[0]}"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="transfer_amount" class="form-label fw-semibold">
                                    Transfer Amount <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control form-control-md rounded-3"
                                       id="transfer_amount"
                                       name="transfer_amount"
                                       placeholder="Enter amount transferred"
                                       step="0.01"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="alert alert-warning mt-4 mb-0">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                        <div class="small">
                            <p class="fw-semibold mb-2">Important Instructions:</p>
                            <ul class="mb-0 ps-3">
                                <li>Make the transfer to the bank account details shown above</li>
                                <li>Use your name as the reference/description</li>
                                <li>Your subscription will be activated within 1-2 business days</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;

                $('#paymentDetailsContainer').html(template);

                window.copyBankDetails = function() {
                    const details = `Bank Name: ${bankDetails.bank_name}
Account Name: ${bankDetails.account_name}
Account Number: ${bankDetails.account_number}
Routing Number: ${bankDetails.routing_number}
SWIFT Code: ${bankDetails.swift_code}`;

                    navigator.clipboard.writeText(details).then(() => {
                        toastr.success('Bank details copied to clipboard');
                    }).catch(() => {
                        toastr.error('Failed to copy details');
                    });
                };
            }

            /**
             * Load redirect-based payment method
             */
            function loadRedirectPayment(gateway) {
                let gatewayObj = paymentGateways.find(g => g.code === gateway);
                let gatewayName = gatewayObj ? gatewayObj.name : ucfirst(gateway.replace('_', ' '));
                let icon = getGatewayIcon(gateway);

                let template = `
            <div class="card border-0 rounded-3 text-center">
                <div class="alert alert-info py-2 mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    <small>You will be redirected to ${gatewayName} to complete your payment</small>
                </div>
            </div>
        `;

                $('#paymentDetailsContainer').html(template);
            }

            /**
             * Use new payment method (hide saved methods)
             */
            window.useNewMethod = function() {
                selectedPaymentMethod = null;
                selectedGateway = null;
                $('#payment_method_id').val('');
                $('#selected_gateway').val('');
                $('.saved-method-card').removeClass('selected');
                $('#savedPaymentMethodsSection').hide();
                $('#paymentMethodsSection').fadeIn();

                @auth
                $('#saveMethodCheckbox').show();
            @endauth
        };

        $('#useNewMethodBtn').click(useNewMethod);

        /**
         * Form submission handler
         */
        $('#checkoutForm').on('submit', async function(e) {
            e.preventDefault();

            if (!validateForm()) return;

            $('#submitBtn').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Processing...'
            );

            if (isLoggedIn) {
                await processAuthenticatedCheckout();
            } else {
                await processGuestCheckout();
            }
        });

        /**
         * Process authenticated checkout (logged in user)
         */
        async function processAuthenticatedCheckout() {
            let paymentMethodId = null;

            // minimum amount
            if (selectedGateway === 'stripe') {
                const amount = parseFloat(selectedPrice.amount);
                let minAmount = 0.50; // default USD

                // currency-specific minimums
                const currency = selectedPrice.currency || 'USD';

                switch (currency) {
                    case 'USD':
                    case 'EUR':
                    case 'AUD':
                    case 'CAD':
                        minAmount = 0.50;
                        break;
                    case 'GBP':
                        minAmount = 0.30;
                        break;
                    case 'JPY':
                        minAmount = 50;
                        break;
                    case 'BDT':
                        minAmount = 50; // টাকার জন্য ৫০ টাকা ন্যূনতম
                        break;
                    default:
                        minAmount = 0.50;
                }

                if (amount < minAmount) {
                    toastr.error(`${currency} currency specific minimum amount ${minAmount} ${currency} required.`);
                    $('#submitBtn').prop('disabled', false).html(
                        '<i class="fas fa-paper-plane me-2"></i>Complete Purchase');
                    return false;
                }
            }

            // Create Stripe payment method for new card
            if (selectedGateway === 'stripe' && !selectedPaymentMethod) {
                const paymentMethod = await createStripePaymentMethod();
                if (!paymentMethod) {
                    $('#submitBtn').prop('disabled', false).html(
                        '<i class="fas fa-paper-plane me-2"></i>Complete Purchase');
                    return;
                }
                paymentMethodId = paymentMethod.id;
            }

            const data = {
                plan_id: $('#plan_id').val(),
                price_id: $('#price_id').val(),
                payment_method: selectedGateway,
                gateway: selectedGateway,
                payment_method_id: $('#payment_method_id').val() || paymentMethodId,
                payment_details: collectPaymentDetails(),
                save_payment_method: $('#save_payment_method_checkbox').is(':checked'),
                terms: $('#terms').is(':checked'),
                name: $('#name').val(),
                email: $('#email').val(),
                phone: $('#phone').val()
            };

            try {
                const response = await axios.post('/checkout/process-authenticated', data);

                if (response.data.success || response.data.data?.success || response.data.data?.status ===
                    'completed') {
                    // Handle redirect-based gateways
                    if (response.data.data?.redirect_url) {
                        if (response.data.data?.redirect_url === '/payment/sslcommerz/success') {
                            setTimeout(() => {
                                toastr.success('Redirecting to payment gateway...');
                                axios.get('/payment/sslcommerz/success')
                                    .then((response) => {
                                        toastr.success('Payment completed successfully!');
                                        console.log('Payment success response:', response.data);
                                        // window.location.href = response.data.redirect_url || '/dashboard/subscriptions';
                                    })
                                    .catch((error) => {
                                        toastr.error('Error processing SSLCommerz payment' + (error.response?.data?.message ? ': ' + error.response.data.message : ''));
                                        window.location.href = '/dashboard/subscriptions';
                                    });
                            }, 2000);
                            return;
                        } else {
                            toastr.success('Redirecting to payment gateway...');
                           window.location.href = response.data.data.redirect_url || response.data.redirect_url ||
                               '/dashboard/subscriptions';
                            return; // Stop execution
                        }
                    }
                    // Handle completed payment
                    else if (response.data.data?.status === 'completed') {
                        toastr.success('Payment completed successfully!');
                        setTimeout(() => {
                            window.location.href = '/dashboard/subscriptions';
                        }, 2000);
                    }
                    // Handle pending payment (bank transfer, cash, etc.)
                    else {
                        toastr.success(response.data.message || 'Payment initiated successfully');
                        setTimeout(() => {
                            window.location.href = '/dashboard/subscriptions';
                        }, 2000);
                    }
                } else {
                    toastr.error(response.data.message || 'Checkout failed');
                    $('#submitBtn').prop('disabled', false).html(
                        '<i class="fas fa-paper-plane me-2"></i>Complete Purchase');
                }
            } catch (error) {
                console.error('Checkout error:', error);
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    let errorMessage = '';
                    for (let key in errors) {
                        errorMessage += errors[key][0] + '\n';
                    }
                    toastr.error(errorMessage);
                } else {
                    toastr.error(error.response?.data?.message || 'Checkout failed');
                }
                $('#submitBtn').prop('disabled', false).html(
                    '<i class="fas fa-paper-plane me-2"></i>Complete Purchase');
            }
        }

        /**
         * Create Stripe payment method
         */
        async function createStripePaymentMethod() {
            if (!stripe || !stripeCard) {
                toastr.error('Stripe not initialized');
                return null;
            }

            try {
                const {
                    paymentMethod,
                    error
                } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: stripeCard,
                    billing_details: {
                        name: $('#name').val(),
                        email: $('#email').val(),
                        phone: $('#phone').val() || undefined,
                    }
                });

                if (error) {
                    toastr.error(error.message);
                    return null;
                }

                return paymentMethod;
            } catch (error) {
                toastr.error('An error occurred with payment processing');
                return null;
            }
        }

        /**
         * Process guest checkout (with OTP)
         */
        async function processGuestCheckout() {
            const email = $('#email').val();

            checkoutData = {
                plan_id: $('#plan_id').val(),
                price_id: $('#price_id').val(),
                payment_method: selectedGateway,
                gateway: selectedGateway,
                name: $('#name').val(),
                email: email,
                phone: $('#phone').val(),
                terms: $('#terms').is(':checked'),
                payment_details: collectPaymentDetails()
            };

            try {
                const response = await axios.post('/checkout/send-otp', {
                    email
                });

                if (response.data.success) {
                    $('#otpEmailDisplay').text(email);
                    $('#otpModal').modal('show');

                    const expiresAt = new Date(response.data.expires_at).getTime();
                    startOtpTimer(expiresAt);

                    $('#otpInput').off('input').on('input', function() {
                        const otp = $(this).val().replace(/\D/g, '');
                        $(this).val(otp);
                        $('#verifyOtpBtn').prop('disabled', otp.length !== 6);
                    });

                    $('#otpStep1').show();
                    $('#otpStep2').hide();

                    $('#submitBtn').prop('disabled', false).html(
                        '<i class="fas fa-paper-plane me-2"></i>Complete Purchase');
                }
            } catch (error) {
                toastr.error(error.response?.data?.message || 'Failed to send OTP');
                $('#submitBtn').prop('disabled', false).html(
                    '<i class="fas fa-paper-plane me-2"></i>Complete Purchase');
            }
        }

        /**
         * Verify OTP and complete checkout
         */
        $('#verifyOtpBtn').on('click', async function() {
            const otp = $('#otpInput').val();

            if (!otp || otp.length !== 6) {
                toastr.error('Please enter a valid 6-digit OTP');
                return;
            }

            $(this).prop('disabled', true);
            $('#otpSpinner').removeClass('d-none');
            checkoutData.otp = otp;

            try {
                const response = await axios.post('/checkout/verify-otp', checkoutData);

                if (response.data.success) {
                    $('#otpStep1').hide();
                    $('#otpStep2').show();

                    if (otpTimer) clearInterval(otpTimer);

                    if (response.data.data?.redirect_url) {
                        setTimeout(() => {
                            window.location.href = response.data.data.redirect_url;
                        }, 2000);
                    } else {
                        toastr.success('Payment completed successfully!');
                        setTimeout(() => {
                            window.location.href = '/dashboard/subscriptions';
                        }, 2000);
                    }
                }
            } catch (error) {
                toastr.error(error.response?.data?.message || 'Checkout failed');
                $('#verifyOtpBtn').prop('disabled', false);
                $('#otpSpinner').addClass('d-none');
            }
        });

        /**
         * Resend OTP
         */
        $('#resendOtpBtn').on('click', async function() {
            const email = $('#email').val();

            $(this).prop('disabled', true).text('Sending...');

            try {
                const response = await axios.post('/checkout/send-otp', {
                    email
                });

                if (response.data.success) {
                    toastr.success('OTP resent successfully');
                    const expiresAt = new Date(response.data.expires_at).getTime();
                    startOtpTimer(expiresAt);
                    $('#otpInput').val('');
                    $('#verifyOtpBtn').prop('disabled', true);
                }
            } catch (error) {
                toastr.error('Failed to resend OTP');
            } finally {
                $(this).prop('disabled', false).text('Resend OTP');
            }
        });

        /**
         * Validate form before submission
         */
        function validateForm() {
            // Check required fields
            if (!$('#name').val() || !$('#phone').val() || !$('#email').val()) {
                toastr.error('Please fill in all required fields');
                return false;
            }

            // Validate email
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test($('#email').val())) {
                toastr.error('Please enter a valid email address');
                return false;
            }

            // Check payment method selection
            if (isLoggedIn) {
                if (!selectedPaymentMethod && !selectedGateway) {
                    toastr.error('Please select a payment method');
                    return false;
                }
            } else {
                if (!selectedGateway) {
                    toastr.error('Please select a payment method');
                    return false;
                }
            }

            // Validate bank transfer fields
            if (selectedGateway === 'bank_transfer') {
                if (!$('#sender_account_number').val()) {
                    toastr.error('Please enter your account number');
                    return false;
                }
                if (!$('#bank_transaction_id').val()) {
                    toastr.error('Please enter transaction ID');
                    return false;
                }
                if (!$('#transfer_date').val()) {
                    toastr.error('Please select transfer date');
                    return false;
                }
                if (!$('#transfer_amount').val()) {
                    toastr.error('Please enter transfer amount');
                    return false;
                }
            }

            // Check terms
            if (!$('#terms').is(':checked')) {
                toastr.error('You must agree to the terms and conditions');
                return false;
            }

            return true;
        }

        /**
         * Collect payment details based on selected gateway
         */
        function collectPaymentDetails() {
            const details = {};

            switch (selectedGateway) {
                case 'bank_transfer':
                    details.sender_account_number = $('#sender_account_number').val();
                    details.transaction_id = $('#bank_transaction_id').val();
                    details.transfer_date = $('#transfer_date').val();
                    details.transfer_amount = $('#transfer_amount').val();
                    break;
            }

            return details;
        }

        /**
         * Start OTP timer
         */
        function startOtpTimer(expiryTime) {
            if (otpTimer) clearInterval(otpTimer);

            otpTimer = setInterval(() => {
                const now = new Date().getTime();
                const distance = expiryTime - now;

                if (distance <= 0) {
                    clearInterval(otpTimer);
                    $('#otpTimer').text('00:00');
                    $('#verifyOtpBtn').prop('disabled', true);
                    $('#resendOtpBtn').prop('disabled', false);
                    toastr.warning('OTP expired. Please request a new one.');
                } else {
                    const minutes = Math.floor(distance / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    $('#otpTimer').text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                }
            }, 1000);
        }

        /**
         * Utility function: uppercase first letter
         */
        function ucfirst(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        /**
         * Format money with currency symbol
         */
        function formatMoney(amount) {
            const symbol = CURRENCY === 'USD' ? '$' :
                CURRENCY === 'EUR' ? '€' :
                CURRENCY === 'BDT' ? '৳' : '$';
            return symbol + parseFloat(amount).toFixed(2);
        }

        /**
         * Get Font Awesome icon for gateway
         */
        function getGatewayIcon(gatewayCode) {
            const icons = {
                stripe: 'fab fa-cc-stripe',
                paypal: 'fab fa-paypal',
                bkash: 'fas fa-mobile-alt',
                nagad: 'fas fa-money-bill-wave',
                rocket: 'fas fa-rocket',
                surjopay: 'fas fa-bolt',
                sslcommerz: 'fas fa-lock',
                bank_transfer: 'fas fa-university',
                cash: 'fas fa-money-bill-wave',
                google_pay: 'fab fa-google-pay',
                apple_pay: 'fab fa-apple-pay',
                amazon_pay: 'fab fa-amazon-pay'
            };
            return icons[gatewayCode] || 'fas fa-credit-card';
        }

        /**
         * OTP modal cleanup
         */
        $('#otpModal').on('hidden.bs.modal', function() {
        if (otpTimer) clearInterval(otpTimer);
        $('#otpInput').val('');
        $('#verifyOtpBtn').prop('disabled', true);
        $('#otpSpinner').addClass('d-none');
        $('#otpStep1').show();
        $('#otpStep2').hide();
        });
        });
    </script>
@endpush
