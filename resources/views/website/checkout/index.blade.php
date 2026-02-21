@extends('website.layouts.app')

@section('title', 'Checkout')

@section('content')
<!-- Breadcrumb -->
<section class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.plans.index') }}" class="text-decoration-none">Plans</a></li>
                <li class="breadcrumb-item"><a href="#" id="planBreadcrumbLink" class="text-decoration-none">Loading...</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Checkout Form -->
<section class="py-5">
    <div class="container">
        <div id="checkoutLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading checkout information...</p>
        </div>

        <div id="checkoutContent" style="display: none;">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h4 class="mb-0">Payment Information</h4>
                        </div>
                        <div class="card-body p-4">
                            <form action="javascript:void(0);" id="checkoutForm">
                                @csrf
                                <input type="hidden" name="plan_id" id="plan_id" value="{{ $plan_id }}">
                                <input type="hidden" name="price_id" id="price_id" value="">
                                <input type="hidden" name="gateway" id="selected_gateway" value="">
                                <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                                <input type="hidden" name="save_payment_method" id="save_payment_method" value="1">

                                <!-- Billing Cycle -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Billing Cycle</label>
                                    <div class="btn-group w-100 flex-wrap" role="group" id="billingCycles"></div>
                                </div>

                                <!-- Personal Information -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required
                                               value="{{ old('first_name', auth()->user()?->name ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required
                                               value="{{ old('email', auth()->user()?->email ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>

                                <!-- Saved Payment Methods (for logged in users) -->
                                @auth
                                <div class="mb-4" id="savedPaymentMethodsSection" style="display: none;">
                                    <label class="form-label fw-bold">Your Saved Payment Methods</label>
                                    <div id="savedMethodsLoader" class="text-center py-2">
                                        <div class="loader" style="width: 30px; height: 30px;"></div>
                                    </div>
                                    <div id="savedMethodsContainer" class="row g-3"></div>

                                    <!-- Use New Payment Method Toggle -->
                                    <div class="mt-3 text-center">
                                        <button type="button" class="btn btn-link" id="useNewMethodBtn">
                                            <i class="fas fa-plus-circle me-2"></i>Use a different payment method
                                        </button>
                                    </div>
                                </div>
                                @endauth

                                <!-- Payment Gateways (shown when no saved methods or user clicks "use new") -->
                                <div class="mb-4" id="paymentGatewaysSection" style="display: none;">
                                    <label class="form-label fw-bold">Select Payment Method</label>

                                    <!-- Nav tabs for payment gateways -->
                                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="cards-tab" data-bs-toggle="tab" data-bs-target="#cards" type="button" role="tab">
                                                <i class="fas fa-credit-card me-2"></i>Cards
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab">
                                                <i class="fas fa-university me-2"></i>Bank
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="mobile-tab" data-bs-toggle="tab" data-bs-target="#mobile" type="button" role="tab">
                                                <i class="fas fa-mobile-alt me-2"></i>Mobile Banking
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="wallets-tab" data-bs-toggle="tab" data-bs-target="#wallets" type="button" role="tab">
                                                <i class="fas fa-wallet me-2"></i>Digital Wallets
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 border border-top-0 rounded-bottom">
                                        <!-- Cards -->
                                        <div class="tab-pane fade show active" id="cards" role="tabpanel">
                                            <div class="row g-3" id="cardGateways"></div>
                                        </div>

                                        <!-- Bank Transfer -->
                                        <div class="tab-pane fade" id="bank" role="tabpanel">
                                            <div class="row g-3" id="bankGateways"></div>
                                        </div>

                                        <!-- Mobile Banking -->
                                        <div class="tab-pane fade" id="mobile" role="tabpanel">
                                            <div class="row g-3" id="mobileGateways"></div>
                                        </div>

                                        <!-- Digital Wallets -->
                                        <div class="tab-pane fade" id="wallets" role="tabpanel">
                                            <div class="row g-3" id="walletGateways"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Payment Details Section (for new payment methods) -->
                                <div id="paymentDetailsContainer"></div>

                                <!-- Save Payment Method Checkbox (for new methods) -->
                                <div class="mb-4" id="saveMethodCheckbox" style="display: none;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_payment_method_checkbox" checked>
                                        <label class="form-check-label" for="save_payment_method_checkbox">
                                            Save this payment method for future purchases
                                        </label>
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and
                                            <a href="#" class="text-decoration-none">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                                    <i class="fas fa-lock me-2"></i>Complete Purchase
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 2rem;">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div id="orderSummaryLoader" class="text-center py-3">
                                <div class="loader" style="width: 30px; height: 30px;"></div>
                            </div>

                            <div id="orderSummaryContent" style="display: none;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" id="summaryPlanName"></h6>
                                        <p class="text-muted small mb-0" id="summaryInterval"></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold" id="summaryAmount"></span>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="summarySubtotal"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax ({{ config('app.tax_rate', 10) }}%):</span>
                                    <span id="summaryTax"></span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total:</span>
                                    <span class="h5 mb-0 text-primary" id="summaryTotal"></span>
                                </div>

                                <div class="alert alert-info py-2 px-3" id="summaryNote">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="summaryNoteText"></span>
                                </div>

                                <!-- Trust Badges -->
                                <div class="text-center mt-3">
                                    <i class="fab fa-cc-visa fa-2x text-muted me-2"></i>
                                    <i class="fab fa-cc-mastercard fa-2x text-muted me-2"></i>
                                    <i class="fab fa-cc-amex fa-2x text-muted me-2"></i>
                                    <i class="fab fa-cc-paypal fa-2x text-muted"></i>
                                    <i class="fas fa-mobile-alt fa-2x text-muted me-2"></i>
                                    <i class="fas fa-university fa-2x text-muted"></i>
                                </div>

                                <p class="text-center text-muted small mt-3 mb-0">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Secure SSL encrypted payment
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel">Verify Your Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="otpStep1">
                        <p>We've sent a One-Time Password (OTP) to <strong id="otpEmailDisplay"></strong></p>
                        <p class="text-muted small">Please enter the 6-digit code below to continue.</p>

                        <div class="otp-input-container text-center mb-3">
                            <input type="text" class="form-control form-control-lg text-center" id="otpInput"
                                   maxlength="6" placeholder="000000" style="font-size: 2rem; letter-spacing: 0.5rem;">
                        </div>

                        <div class="text-center mb-3">
                            <span class="text-muted">OTP expires in </span>
                            <span id="otpTimer" class="fw-bold text-primary">10:00</span>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" id="verifyOtpBtn" disabled>
                                <span class="spinner-border spinner-border-sm d-none" id="otpSpinner"></span>
                                Verify & Complete Purchase
                            </button>
                            <button type="button" class="btn btn-link" id="resendOtpBtn">Resend OTP</button>
                        </div>
                    </div>

                    <div id="otpStep2" style="display: none;">
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                            <h5>Verification Successful!</h5>
                            <p class="text-muted">Please wait while we process your payment...</p>
                            <div class="loader mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Payment Templates -->
<script type="text/template" id="stripeTemplate">
    <div class="mb-4">
        <h5 class="mb-3">Card Details</h5>
        <div id="stripe-card-element" class="form-control"></div>
        <div id="stripe-errors" class="text-danger mt-2"></div>
    </div>
</script>

<script type="text/template" id="bankTemplate">
    <div class="alert alert-info">
        <i class="fas fa-university me-2"></i>
        <strong>Bank Transfer Instructions:</strong>
        <p class="mb-0 mt-2">After submitting, you'll receive our bank details via email to complete the transfer. Your subscription will be activated once payment is received.</p>
    </div>
</script>

<script type="text/template" id="mobileBankingTemplate">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3" id="mobileGatewayName"></h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="mobile_number" class="form-label">Mobile Number</label>
                        <input type="tel" class="form-control" id="mobile_number" name="mobile_number" placeholder="01XXXXXXXXX">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter transaction ID">
                    </div>
                </div>
            </div>
            <div class="alert alert-warning" id="merchantInfo">
                <i class="fas fa-info-circle me-2"></i>
                Send payment to: <strong id="merchantNumber"></strong> and enter the transaction ID above.
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="paypalTemplate">
    <div class="alert alert-info">
        <i class="fab fa-paypal me-2"></i>
        You will be redirected to PayPal to complete your payment.
    </div>
</script>

<script type="text/template" id="surjopayTemplate">
    <div class="alert alert-info">
        <i class="fas fa-bolt me-2"></i>
        You will be redirected to SurjoPay to complete your payment.
    </div>
</script>
@endsection

@push('styles')
<style>
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s;
        height: 100%;
        border: 2px solid transparent;
    }
    .payment-method-card .btn-check:checked + .btn {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .payment-method-card .btn {
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .payment-method-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .saved-method-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        height: 100%;
    }
    .saved-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .saved-method-card.selected {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
    .saved-method-card.default {
        border-color: #198754;
        background-color: #f0fff4;
    }
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
        color: #0d6efd;
    }
    .btn-group .btn {
        flex: 1;
    }
    #stripe-card-element {
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background: white;
    }
    .otp-input-container input {
        font-size: 2rem;
        letter-spacing: 0.5rem;
        text-align: center;
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

        // Tax rate from config
        const TAX_RATE = {{ config('app.tax_rate', 10) }};

        // Merchant numbers for different gateways
        const merchantNumbers = {
            bkash: '01965674161',
            rocket: '01812345678',
            nagad: '01612345678',
            surjopay: '01965674161'
        };

        // Fetch plan details
        fetchPlanDetails();

        // If logged in, fetch payment methods and gateways
        if (isLoggedIn) {
            fetchUserPaymentMethods();
        }

        // Always fetch payment gateways (for both guest and logged in)
        fetchPaymentGateways();

        function fetchPlanDetails() {
            axios.get(`/plans/${planId}`)
                .then(response => {
                    // Fix: Check the actual response structure
                    planData = response.data.data || response.data;
                    renderCheckout();
                    $('#checkoutLoader').hide();
                    $('#checkoutContent').show();
                })
                .catch(error => {
                    console.error('Error fetching plan:', error);
                    $('#checkoutLoader').hide();
                    $('#checkoutContent').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                            <h3>Error Loading Checkout</h3>
                            <p class="text-muted">The plan you're trying to purchase could not be found.</p>
                            <a href="{{ route('website.plans.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Back to Plans
                            </a>
                        </div>
                    `).show();
                });
        }

        function fetchUserPaymentMethods() {
            axios.get('/payment-methods')
                .then(response => {
                    // Fix: Check the actual response structure
                    userPaymentMethods = response.data.data || response.data || [];
                    if (userPaymentMethods.length > 0) {
                        renderSavedPaymentMethods();
                        $('#savedPaymentMethodsSection').show();
                        $('#paymentGatewaysSection').hide();
                    } else {
                        $('#savedMethodsLoader').hide();
                        $('#savedPaymentMethodsSection').hide();
                        $('#paymentGatewaysSection').show();
                    }
                })
                .catch(error => {
                    console.error('Error fetching payment methods:', error);
                    $('#savedMethodsLoader').hide();
                    $('#savedPaymentMethodsSection').hide();
                    $('#paymentGatewaysSection').show();
                });
        }

        function fetchPaymentGateways() {
            axios.get('/payment-gateways')
                .then(response => {
                    // Fix: Access the nested data structure
                    const responseData = response.data;
                    if (responseData.data && responseData.data.data) {
                        paymentGateways = responseData.data.data;
                    } else if (responseData.data) {
                        paymentGateways = responseData.data;
                    } else {
                        paymentGateways = responseData;
                    }

                    console.log('Payment gateways loaded:', paymentGateways);
                    renderGatewayTabs();
                })
                .catch(error => {
                    console.error('Error fetching gateways:', error);
                });
        }

        function renderCheckout() {
            // Update breadcrumb
            if (planData && planData.slug) {
                $('#planBreadcrumbLink').attr('href', `/plan/${planData.slug}`).text(planData.name);
            } else {
                $('#planBreadcrumbLink').text('Plan');
            }

            // Set price ID
            if (selectedPriceId && planData && planData.prices) {
                selectedPrice = planData.prices.find(p => p.id == selectedPriceId);
            }
            if (!selectedPrice && planData && planData.prices && planData.prices.length > 0) {
                selectedPrice = planData.prices[0];
            }

            if (selectedPrice) {
                $('#price_id').val(selectedPrice.id);
            }

            // Render billing cycles
            renderBillingCycles();

            // Render order summary
            renderOrderSummary();
        }

        function renderBillingCycles() {
            let html = '';

            if (planData && planData.prices && planData.prices.length > 0) {
                planData.prices.forEach(price => {
                    let isSelected = selectedPrice && price.id === selectedPrice.id;
                    let isBestValue = price.interval === 'year';

                    html += `
                        <button type="button"
                                class="btn ${isSelected ? 'btn-primary' : 'btn-outline-primary'} billing-cycle"
                                data-price-id="${price.id}"
                                data-amount="${price.amount}"
                                data-interval="${price.interval}"
                                data-interval-desc="${price.interval_description || '/' + price.interval}">
                            ${ucfirst(price.interval)}ly
                            ${isBestValue ? '<span class="badge bg-success ms-1">Save 20%</span>' : ''}
                        </button>
                    `;
                });
            }

            $('#billingCycles').html(html);

            // Add click handlers
            $('.billing-cycle').click(function() {
                $('.billing-cycle').removeClass('btn-primary').addClass('btn-outline-primary');
                $(this).addClass('btn-primary').removeClass('btn-outline-primary');

                let priceId = $(this).data('price-id');
                $('#price_id').val(priceId);

                // Find selected price
                selectedPrice = planData.prices.find(p => p.id == priceId);

                // Update summary
                updateOrderSummary(selectedPrice);
            });
        }

        function renderOrderSummary() {
            if (!selectedPrice) {
                $('#orderSummaryLoader').hide();
                $('#orderSummaryContent').hide();
                return;
            }

            let amount = parseFloat(selectedPrice.amount);
            let tax = amount * (TAX_RATE / 100);
            let total = amount + tax;

            $('#summaryPlanName').text(planData ? planData.name : 'Plan');
            $('#summaryInterval').text(selectedPrice.interval_description || '/' + selectedPrice.interval);

            let amountFormatted = formatMoney(amount);
            let taxFormatted = formatMoney(tax);
            let totalFormatted = formatMoney(total);

            $('#summaryAmount').text(amountFormatted);
            $('#summarySubtotal').text(amountFormatted);
            $('#summaryTax').text(taxFormatted);
            $('#summaryTotal').text(totalFormatted);

            let noteText = `You'll be charged ${totalFormatted} ${selectedPrice.interval_description}. Cancel anytime.`;
            $('#summaryNoteText').text(noteText);

            $('#orderSummaryLoader').hide();
            $('#orderSummaryContent').show();
        }

        function updateOrderSummary(price) {
            let amount = parseFloat(price.amount);
            let tax = amount * (TAX_RATE / 100);
            let total = amount + tax;

            let amountFormatted = formatMoney(amount);
            let taxFormatted = formatMoney(tax);
            let totalFormatted = formatMoney(total);

            $('#summaryInterval').text(price.interval_description || '/' + price.interval);
            $('#summaryAmount, #summarySubtotal').text(amountFormatted);
            $('#summaryTax').text(taxFormatted);
            $('#summaryTotal').text(totalFormatted);

            let noteText = `You'll be charged ${totalFormatted} ${price.interval_description}. Cancel anytime.`;
            $('#summaryNoteText').text(noteText);
        }

        function renderSavedPaymentMethods() {
            if (!userPaymentMethods || userPaymentMethods.length === 0) {
                $('#savedMethodsLoader').hide();
                $('#savedPaymentMethodsSection').hide();
                $('#paymentGatewaysSection').show();
                return;
            }

            let html = '';
            const defaultMethod = userPaymentMethods.find(m => m.is_default);

            userPaymentMethods.forEach(method => {
                const isDefault = method.is_default ? 'default' : '';
                const selected = method.is_default ? 'selected' : '';

                let displayText = method.display || method.card?.last4 || 'Card';
                let icon = method.icon || 'fas fa-credit-card';

                html += `
                    <div class="col-md-6">
                        <div class="card saved-method-card ${isDefault} ${selected}"
                             onclick="selectSavedMethod(${method.id}, this)">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="${icon} fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">${displayText}</h6>
                                        ${isDefault ? '<span class="badge bg-success mt-1">Default</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#savedMethodsContainer').html(html);
            $('#savedMethodsLoader').hide();

            // Auto-select default method
            if (defaultMethod) {
                selectSavedMethod(defaultMethod.id, null);
            }
        }

        window.selectSavedMethod = function(methodId, element) {
            selectedPaymentMethod = userPaymentMethods.find(m => m.id === methodId);
            if (!selectedPaymentMethod) return;

            selectedGateway = selectedPaymentMethod.gateway;

            $('.saved-method-card').removeClass('selected');
            if (element) {
                $(element).addClass('selected');
            } else {
                // Find and select the element by method ID
                $(`.saved-method-card[onclick*="${methodId}"]`).addClass('selected');
            }

            $('#selected_gateway').val(selectedGateway);
            $('#payment_method_id').val(methodId);
            $('#paymentGatewaysSection').hide();
            $('#paymentDetailsContainer').empty();
            $('#saveMethodCheckbox').hide();

            // If it's Stripe card, we might need to confirm it's still valid
            if (selectedGateway === 'stripe' && selectedPaymentMethod.card) {
                // Check if card is expired
                const expYear = selectedPaymentMethod.card.exp_year;
                const expMonth = selectedPaymentMethod.card.exp_month;
                const now = new Date();
                const currentYear = now.getFullYear();
                const currentMonth = now.getMonth() + 1;

                if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
                    toastr.warning('Your saved card has expired. Please use a different method.');
                }
            }
        };

        window.useNewMethod = function() {
            selectedPaymentMethod = null;
            selectedGateway = null;
            $('#payment_method_id').val('');
            $('#selected_gateway').val('');
            $('.saved-method-card').removeClass('selected');
            $('#savedPaymentMethodsSection').hide();
            $('#paymentGatewaysSection').show();
            $('#saveMethodCheckbox').show();

            // Show the payment tabs
            $('.nav-tabs .nav-link:first').tab('show');
        };

        $('#useNewMethodBtn').click(function() {
            useNewMethod();
        });

        function renderGatewayTabs() {
            if (!paymentGateways || paymentGateways.length === 0) {
                console.log('No payment gateways to render');
                return;
            }

            // Fix: Properly categorize gateways based on their type and code
            const cardGateways = paymentGateways.filter(g => g.type === 'card' || g.code === 'stripe');
            const bankGateways = paymentGateways.filter(g => g.type === 'bank' || g.code === 'bank_transfer');
            const mobileGateways = paymentGateways.filter(g =>
                ['bkash', 'nagad', 'rocket', 'surjopay', 'sslcommerz'].includes(g.code) ||
                g.type === 'wallet' ||
                g.type === 'aggregator'
            );
            const walletGateways = paymentGateways.filter(g =>
                ['paypal', 'google_pay', 'apple_pay', 'amazon_pay'].includes(g.code)
            );

            renderGatewayGroup('#cardGateways', cardGateways);
            renderGatewayGroup('#bankGateways', bankGateways);
            renderGatewayGroup('#mobileGateways', mobileGateways);
            renderGatewayGroup('#walletGateways', walletGateways);

            // Hide empty tabs
            if (cardGateways.length === 0) $('#cards-tab').parent().hide();
            if (bankGateways.length === 0) $('#bank-tab').parent().hide();
            if (mobileGateways.length === 0) $('#mobile-tab').parent().hide();
            if (walletGateways.length === 0) $('#wallets-tab').parent().hide();
        }

        function renderGatewayGroup(selector, gateways) {
            if (gateways.length === 0) {
                $(selector).closest('.tab-pane').hide();
                return;
            }

            $(selector).closest('.tab-pane').show();

            let html = '';
            gateways.forEach(gateway => {
                const icon = getGatewayIcon(gateway.code);
                html += `
                    <div class="col-md-4">
                        <div class="payment-method-card">
                            <input type="radio" class="btn-check" name="payment_method"
                                   id="gateway_${gateway.code}" value="${gateway.code}" autocomplete="off"
                                   data-gateway="${gateway.code}">
                            <label class="btn btn-outline-primary w-100 py-3" for="gateway_${gateway.code}">
                                <i class="${icon} fa-2x mb-2"></i>
                                <br>${gateway.name}
                            </label>
                        </div>
                    </div>
                `;
            });
            $(selector).html(html);
        }

        // Handle payment method selection
        $(document).on('change', 'input[name="payment_method"]', function() {
            selectedGateway = $(this).data('gateway');
            selectedPaymentMethod = null;
            $('#selected_gateway').val(selectedGateway);
            $('#payment_method_id').val('');

            // Load payment details for selected gateway
            loadPaymentDetails(selectedGateway);
        });

        function loadPaymentDetails(gateway) {
            $('#paymentDetailsContainer').empty();

            switch(gateway) {
                case 'stripe':
                    loadStripePayment();
                    break;
                case 'bkash':
                case 'rocket':
                case 'nagad':
                case 'surjopay':
                case 'sslcommerz':
                    loadMobileBankingPayment(gateway);
                    break;
                case 'bank_transfer':
                    loadBankTransferPayment();
                    break;
                case 'paypal':
                    loadPayPalPayment();
                    break;
                case 'cash':
                    loadCashPayment();
                    break;
                case 'google_pay':
                case 'apple_pay':
                case 'amazon_pay':
                    loadDigitalWalletPayment(gateway);
                    break;
                default:
                    // No additional fields needed
                    break;
            }
        }

        function loadStripePayment() {
            let template = $('#stripeTemplate').html();
            $('#paymentDetailsContainer').html(template);

            // Initialize Stripe
            if (!stripe) {
                // Get Stripe publishable key
                const stripeGateway = paymentGateways.find(g => g.code === 'stripe');
                if (stripeGateway) {
                    const publicKey = stripeGateway.api_key || 'pk_test_your_key';
                    
                    stripe = Stripe(publicKey);
                    stripeElements = stripe.elements();

                    stripeCard = stripeElements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
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
                } else {
                    $('#paymentDetailsContainer').html('<div class="alert alert-danger">Stripe configuration not found</div>');
                }
            } else {
                // Re-mount card if needed
                if (stripeCard) {
                    stripeCard.mount('#stripe-card-element');
                }
            }
        }

        function loadMobileBankingPayment(gateway) {
            let template = $('#mobileBankingTemplate').html();
            let gatewayObj = paymentGateways.find(g => g.code === gateway);
            let gatewayName = gatewayObj ? gatewayObj.name : (gateway.charAt(0).toUpperCase() + gateway.slice(1));
            let merchantNumber = merchantNumbers[gateway] || '01965674161';

            $('#paymentDetailsContainer').html(template);
            $('#mobileGatewayName').text(gatewayName + ' Payment');
            $('#merchantNumber').text(merchantNumber);
        }

        function loadBankTransferPayment() {
            let template = $('#bankTemplate').html();
            $('#paymentDetailsContainer').html(template);

            // Add bank details from config if available
            const bankGateway = paymentGateways.find(g => g.code === 'bank_transfer');
            if (bankGateway && bankGateway.config) {
                const config = bankGateway.config;
                let bankDetails = '<div class="mt-3 p-3 bg-light rounded">';
                bankDetails += '<h6>Bank Account Details:</h6>';
                if (config.bank_name) bankDetails += `<p class="mb-1"><strong>Bank:</strong> ${config.bank_name}</p>`;
                if (config.account_name) bankDetails += `<p class="mb-1"><strong>Account Name:</strong> ${config.account_name}</p>`;
                if (config.account_number) bankDetails += `<p class="mb-1"><strong>Account Number:</strong> ${config.account_number}</p>`;
                if (config.routing_number) bankDetails += `<p class="mb-1"><strong>Routing Number:</strong> ${config.routing_number}</p>`;
                if (config.swift_code) bankDetails += `<p class="mb-1"><strong>SWIFT Code:</strong> ${config.swift_code}</p>`;
                bankDetails += '</div>';

                $('.alert-info').after(bankDetails);
            }
        }

        function loadPayPalPayment() {
            let template = $('#paypalTemplate').html();
            $('#paymentDetailsContainer').html(template);
        }

        function loadCashPayment() {
            $('#paymentDetailsContainer').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <strong>Cash Payment:</strong>
                    <p class="mb-0 mt-2">Please have the exact amount ready for cash payment. Our representative will contact you to arrange payment.</p>
                </div>
            `);
        }

        function loadDigitalWalletPayment(gateway) {
            let gatewayName = gateway.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            $('#paymentDetailsContainer').html(`
                <div class="alert alert-info">
                    <i class="fab fa-${gateway.replace('_', '-')} me-2"></i>
                    You will be redirected to ${gatewayName} to complete your payment.
                </div>
            `);
        }

        // Form submission handler
        $('#checkoutForm').on('submit', async function(e) {
            e.preventDefault();

            // Validate form
            if (!validateForm()) {
                return;
            }

            if (isLoggedIn) {
                // Process as authenticated user
                await processAuthenticatedCheckout();
            } else {
                // Process OTP flow for guest
                await processGuestCheckout();
            }
        });

        // Process guest checkout with OTP
        async function processGuestCheckout() {
            const email = $('#email').val();

            // Collect checkout data
            checkoutData = {
                plan_id: $('#plan_id').val(),
                price_id: $('#price_id').val(),
                payment_method: selectedGateway,
                gateway: selectedGateway,
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: email,
                phone: $('#phone').val(),
                terms: $('#terms').is(':checked'),
                payment_details: collectPaymentDetails(),
                save_payment_method: $('#save_payment_method_checkbox').is(':checked')
            };

            // Show loading
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending OTP...');

            try {
                // Send OTP
                const response = await axios.post('/checkout/send-otp', { email });

                if (response.data.success) {
                    // Show OTP modal
                    $('#otpEmailDisplay').text(email);
                    $('#otpModal').modal('show');

                    // Start timer
                    const expiresAt = new Date(response.data.expires_at).getTime();
                    startOtpTimer(expiresAt);

                    // Enable verify button when OTP is entered
                    $('#otpInput').off('input').on('input', function() {
                        const otp = $(this).val().replace(/\D/g, '');
                        $(this).val(otp);
                        $('#verifyOtpBtn').prop('disabled', otp.length !== 6);
                    });

                    // Reset modal state
                    $('#otpStep1').show();
                    $('#otpStep2').hide();
                }
            } catch (error) {
                console.error('OTP sending failed:', error);
                toastr.error(error.response?.data?.message || 'Failed to send OTP. Please try again.');
            } finally {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Complete Purchase');
            }
        }

        // Verify OTP and complete checkout
        $('#verifyOtpBtn').on('click', async function() {
            const otp = $('#otpInput').val();

            if (!otp || otp.length !== 6) {
                toastr.error('Please enter a valid 6-digit OTP');
                return;
            }

            // Show loading
            $(this).prop('disabled', true);
            $('#otpSpinner').removeClass('d-none');

            // Add OTP to checkout data
            checkoutData.otp = otp;

            try {
                const response = await axios.post('/checkout/verify-otp', checkoutData);

                if (response.data.success) {
                    // Show success step
                    $('#otpStep1').hide();
                    $('#otpStep2').show();

                    // Clear timer
                    if (otpTimer) clearInterval(otpTimer);

                    // Handle redirect if needed
                    if (response.data.data && response.data.data.requires_redirect) {
                        setTimeout(() => {
                            window.location.href = response.data.data.redirect_url;
                        }, 2000);
                    } else {
                        // Success - redirect to dashboard
                        toastr.success('Payment completed successfully!');
                        setTimeout(() => {
                            window.location.href = '/dashboard/subscriptions';
                        }, 2000);
                    }

                    // Save token if new user
                    if (response.data.data && response.data.data.token) {
                        localStorage.setItem('auth_token', response.data.data.token);
                    }
                }
            } catch (error) {
                console.error('Checkout failed:', error);
                toastr.error(error.response?.data?.message || 'Checkout failed. Please try again.');
                $('#verifyOtpBtn').prop('disabled', false);
                $('#otpSpinner').addClass('d-none');
            }
        });

        // Resend OTP
        $('#resendOtpBtn').on('click', async function() {
            const email = $('#email').val();

            $(this).prop('disabled', true).text('Sending...');

            try {
                const response = await axios.post('/checkout/send-otp', { email });

                if (response.data.success) {
                    toastr.success('OTP resent successfully');

                    // Reset timer
                    const expiresAt = new Date(response.data.expires_at).getTime();
                    startOtpTimer(expiresAt);

                    // Clear OTP input
                    $('#otpInput').val('');
                    $('#verifyOtpBtn').prop('disabled', true);
                }
            } catch (error) {
                toastr.error('Failed to resend OTP. Please try again.');
            } finally {
                $(this).prop('disabled', false).text('Resend OTP');
            }
        });

        // Process authenticated checkout
        async function processAuthenticatedCheckout() {
            // For Stripe, we need to create payment method first
            if (selectedGateway === 'stripe' && !selectedPaymentMethod) {
                const paymentMethod = await processStripePayment();
                if (!paymentMethod) {
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Complete Purchase');
                    return;
                }

                // Add payment method ID to data
                var paymentMethodId = paymentMethod.id;
            }

            const data = {
                plan_id: $('#plan_id').val(),
                price_id: $('#price_id').val(),
                payment_method: selectedGateway,
                gateway: selectedGateway,
                payment_method_id: $('#payment_method_id').val() || paymentMethodId,
                payment_details: collectPaymentDetails(),
                save_payment_method: $('#save_payment_method_checkbox').is(':checked'),
                terms: $('#terms').is(':checked')
            };

            // Disable submit button
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

            try {
                const response = await axios.post('/checkout/process-authenticated', data);

                if (response.data.success) {
                    if (response.data.data && response.data.data.requires_redirect) {
                        // Redirect to payment gateway
                        window.location.href = response.data.data.redirect_url;
                    } else {
                        // Success
                        toastr.success('Checkout successful!');
                        setTimeout(() => {
                            window.location.href = '/dashboard/subscriptions';
                        }, 2000);
                    }
                }
            } catch (error) {
                console.error('Checkout failed:', error);
                toastr.error(error.response?.data?.message || 'Checkout failed. Please try again.');
            } finally {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Complete Purchase');
            }
        }

        // Process Stripe payment
        async function processStripePayment() {
            if (!stripeCard) {
                toastr.error('Stripe not initialized. Please try again.', 'Error');
                return null;
            }

            try {
                // Create payment method
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: stripeCard,
                    billing_details: {
                        name: $('#first_name').val() + ' ' + $('#last_name').val(),
                        email: $('#email').val(),
                        phone: $('#phone').val(),
                    }
                });

                if (error) {
                    toastr.error(error.message, 'Payment Error');
                    return null;
                }

                return paymentMethod;
            } catch (error) {
                console.error('Stripe error:', error);
                toastr.error('An error occurred with payment processing', 'Error');
                return null;
            }
        }

        // Validate form
        function validateForm() {
            // Basic validation
            if (!$('#first_name').val() || !$('#last_name').val() || !$('#email').val()) {
                toastr.error('Please fill in all required fields', 'Validation Error');
                return false;
            }

            // Email validation
            let email = $('#email').val();
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                toastr.error('Please enter a valid email address', 'Validation Error');
                return false;
            }

            // Check if payment method selected (for logged in users using new method)
            if (isLoggedIn && !selectedPaymentMethod && !selectedGateway) {
                toastr.error('Please select a payment method', 'Validation Error');
                return false;
            }

            // Check if gateway selected (for guests)
            if (!isLoggedIn && !selectedGateway) {
                toastr.error('Please select a payment method', 'Validation Error');
                return false;
            }

            // Validate mobile banking fields if applicable
            if (['bkash', 'rocket', 'nagad', 'surjopay', 'sslcommerz'].includes(selectedGateway) && !selectedPaymentMethod) {
                if (!$('#mobile_number').val() || !$('#transaction_id').val()) {
                    toastr.error('Please fill in all mobile banking details', 'Validation Error');
                    return false;
                }
            }

            // Terms validation
            if (!$('#terms').is(':checked')) {
                toastr.error('You must agree to the terms and conditions', 'Validation Error');
                return false;
            }

            return true;
        }

        // Collect payment details based on gateway
        function collectPaymentDetails() {
            const details = {};

            switch (selectedGateway) {
                case 'stripe':
                    // Stripe payment method will be handled separately
                    return {};
                case 'bkash':
                case 'rocket':
                case 'nagad':
                case 'surjopay':
                case 'sslcommerz':
                    details.mobile_number = $('#mobile_number').val();
                    details.transaction_id = $('#transaction_id').val();
                    break;
            }

            return details;
        }

        // OTP Timer function
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
                    $('#otpTimer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
                }
            }, 1000);
        }

        // Helper functions
        function ucfirst(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function formatMoney(amount) {
            return '$' + parseFloat(amount).toFixed(2);
        }

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

        // Handle modal close
        $('#otpModal').on('hidden.bs.modal', function () {
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
