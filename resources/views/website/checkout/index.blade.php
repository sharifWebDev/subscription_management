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
    @php
        $gateway_name = 'SurjoPay';
        $merchant_number = '01965674161';

    @endphp
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
                            <form action="{{ route('website.checkout.process') }}" method="POST" id="checkoutForm">
                                @csrf
                                <input type="hidden" name="plan_id" id="plan_id" value="{{ $plan_id }}">
                                <input type="hidden" name="price_id" id="price_id" value="">
                                <input type="hidden" name="gateway" id="selected_gateway" value="">

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

                                <!-- Payment Gateways -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Payment Method</label>

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
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="others-tab" data-bs-toggle="tab" data-bs-target="#others" type="button" role="tab">
                                                <i class="fas fa-ellipsis-h me-2"></i>Others
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 border border-top-0 rounded-bottom">
                                        <!-- Cards -->
                                        <div class="tab-pane fade show active" id="cards" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_stripe" value="stripe" autocomplete="off"
                                                               data-gateway="stripe">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_stripe">
                                                            <i class="fab fa-cc-stripe fa-2x mb-2"></i>
                                                            <br>Stripe
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_paypal" value="paypal" autocomplete="off"
                                                               data-gateway="paypal">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_paypal">
                                                            <i class="fab fa-paypal fa-2x mb-2"></i>
                                                            <br>PayPal
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_paytm" value="paytm" autocomplete="off"
                                                               data-gateway="paytm">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_paytm">
                                                            <i class="fas fa-rupee-sign fa-2x mb-2"></i>
                                                            <br>Paytm
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bank Transfer -->
                                        <div class="tab-pane fade" id="bank" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_bank_transfer" value="bank_transfer" autocomplete="off"
                                                               data-gateway="bank_transfer">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_bank_transfer">
                                                            <i class="fas fa-university fa-2x mb-2"></i>
                                                            <br>Bank Transfer
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mobile Banking (BD) -->
                                        <div class="tab-pane fade" id="mobile" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_bkash" value="bkash" autocomplete="off"
                                                               data-gateway="bkash">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_bkash">
                                                            <img src="https://cdn.bkash.com/logo.png" alt="bKash" style="height: 30px;" class="mb-2">
                                                            <br>bKash
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_rocket" value="rocket" autocomplete="off"
                                                               data-gateway="rocket">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_rocket">
                                                            <i class="fas fa-rocket fa-2x mb-2"></i>
                                                            <br>Rocket
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_nagad" value="nagad" autocomplete="off"
                                                               data-gateway="nagad">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_nagad">
                                                            <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                                            <br>Nagad
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_surjo" value="surjopay" autocomplete="off"
                                                               data-gateway="surjopay">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_surjo">
                                                            <i class="fas fa-bolt fa-2x mb-2"></i>
                                                            <br>SurjoPay
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Digital Wallets -->
                                        <div class="tab-pane fade" id="wallets" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_google_pay" value="google_pay" autocomplete="off"
                                                               data-gateway="google_pay">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_google_pay">
                                                            <i class="fab fa-google-pay fa-2x mb-2"></i>
                                                            <br>Google Pay
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_apple_pay" value="apple_pay" autocomplete="off"
                                                               data-gateway="apple_pay">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_apple_pay">
                                                            <i class="fab fa-apple-pay fa-2x mb-2"></i>
                                                            <br>Apple Pay
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_amazon_pay" value="amazon_pay" autocomplete="off"
                                                               data-gateway="amazon_pay">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_amazon_pay">
                                                            <i class="fab fa-amazon-pay fa-2x mb-2"></i>
                                                            <br>Amazon Pay
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Others -->
                                        <div class="tab-pane fade" id="others" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_cash" value="cash" autocomplete="off"
                                                               data-gateway="cash">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_cash">
                                                            <i class="fas fa-money-bill fa-2x mb-2"></i>
                                                            <br>Cash
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="payment-method-card">
                                                        <input type="radio" class="btn-check" name="payment_method"
                                                               id="gateway_cheque" value="cheque" autocomplete="off"
                                                               data-gateway="cheque">
                                                        <label class="btn btn-outline-primary w-100 py-3" for="gateway_cheque">
                                                            <i class="fas fa-file-invoice fa-2x mb-2"></i>
                                                            <br>Cheque
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dynamic Payment Details Section -->
                                <div id="paymentDetailsContainer"></div>

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
            <h5 class="mb-3">
                 $gateway_name
                Payment</h5>
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
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                Send payment to: <strong>  $merchant_number  </strong> and enter the transaction ID above.
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="paytmTemplate">
    <div class="alert alert-info">
        <i class="fas fa-rupee-sign me-2"></i>
        You will be redirected to Paytm to complete your payment.
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
</style>
@endpush

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    $(document).ready(function() {
        const planId = '{{ $plan_id }}';
        const urlParams = new URLSearchParams(window.location.search);
        const selectedPriceId = urlParams.get('price_id');

        let planData = null;
        let selectedPrice = null;
        let stripe = null;
        let stripeElements = null;
        let stripeCard = null;

        // Tax rate from config
        const TAX_RATE = {{ config('app.tax_rate', 10) }};

        // Merchant numbers for different gateways
        const merchantNumbers = {
            bkash: '017XXXXXXXX',
            rocket: '018XXXXXXXX',
            nagad: '016XXXXXXXX'
        };

        // Fetch plan details
        fetchPlanDetails();

        function fetchPlanDetails() {
            axios.get(`/plans/${planId}`)
                .then(response => {
                    planData = response.data.data;
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

        function renderCheckout() {
            // Update breadcrumb
            $('#planBreadcrumbLink').attr('href', `/plan/${planData.slug}`).text(planData.name);

            // Set price ID
            if (selectedPriceId && planData.prices) {
                selectedPrice = planData.prices.find(p => p.id == selectedPriceId);
            }
            if (!selectedPrice && planData.prices && planData.prices.length > 0) {
                selectedPrice = planData.prices[0];
            }

            if (selectedPrice) {
                $('#price_id').val(selectedPrice.id);
            }

            // Render billing cycles
            renderBillingCycles();

            // Render order summary
            renderOrderSummary();

            // Setup payment method handlers
            setupPaymentMethods();
        }

        function renderBillingCycles() {
            let html = '';

            if (planData.prices && planData.prices.length > 0) {
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

            $('#summaryPlanName').text(planData.name);
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

        function setupPaymentMethods() {
            // Handle payment method selection
            $('input[name="payment_method"]').change(function() {
                let gateway = $(this).data('gateway');
                $('#selected_gateway').val(gateway);

                // Load payment details for selected gateway
                loadPaymentDetails(gateway);
            });

            // Initialize Stripe if needed
            if ($('#gateway_stripe').is(':checked')) {
                loadPaymentDetails('stripe');
            }
        }

        function loadPaymentDetails(gateway) {
            $('#paymentDetailsContainer').empty();

            switch(gateway) {
                case 'stripe':
                    loadStripePayment();
                    break;
                case 'bkash':
                case 'rocket':
                case 'nagad':
                    loadMobileBankingPayment(gateway);
                    break;
                case 'bank_transfer':
                    loadBankTransferPayment();
                    break;
                case 'paypal':
                    loadPayPalPayment();
                    break;
                case 'paytm':
                    loadPaytmPayment();
                    break;
                case 'surjopay':
                    loadSurjoPayPayment();
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
                stripe = Stripe('{{ config("services.stripe.key") }}');
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
            }
        }

        function loadMobileBankingPayment(gateway) {
            let template = $('#mobileBankingTemplate').html();
            let gatewayName = gateway.charAt(0).toUpperCase() + gateway.slice(1);
            let merchantNumber = merchantNumbers[gateway] || '01XXXXXXXXX';

            template = template.replace('{{ $gateway_name }}', gatewayName);
            template = template.replace('{{ $merchant_number }}', merchantNumber);

            $('#paymentDetailsContainer').html(template);
        }

        function loadBankTransferPayment() {
            let template = $('#bankTemplate').html();
            $('#paymentDetailsContainer').html(template);
        }

        function loadPayPalPayment() {
            let template = $('#paypalTemplate').html();
            $('#paymentDetailsContainer').html(template);
        }

        function loadPaytmPayment() {
            let template = $('#paytmTemplate').html();
            $('#paymentDetailsContainer').html(template);
        }

        function loadSurjoPayPayment() {
            let template = $('#surjopayTemplate').html();
            $('#paymentDetailsContainer').html(template);
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

        // Form submission
        $('#checkoutForm').submit(async function(e) {
            e.preventDefault();

            // Validate form
            if (!validateForm()) {
                return;
            }

            let gateway = $('#selected_gateway').val();

            // Handle Stripe payment specially
            if (gateway === 'stripe') {
                await processStripePayment();
            } else {
                // For other gateways, submit normally
                submitForm();
            }
        });

        async function processStripePayment() {
            if (!stripeCard) {
                toastr.error('Stripe not initialized', 'Error');
                return;
            }

            // Disable submit button
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

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
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Complete Purchase');
                    return;
                }

                // Add payment method ID to form
                $('<input>').attr({
                    type: 'hidden',
                    name: 'stripe_payment_method',
                    value: paymentMethod.id
                }).appendTo('#checkoutForm');

                // Submit form
                submitForm();

            } catch (error) {
                console.error('Stripe error:', error);
                toastr.error('An error occurred with payment processing', 'Error');
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-lock me-2"></i>Complete Purchase');
            }
        }

        function submitForm() {
            // Submit the form normally
            document.getElementById('checkoutForm').submit();
        }

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

            // Check if payment method selected
            if (!$('input[name="payment_method"]:checked').val()) {
                toastr.error('Please select a payment method', 'Validation Error');
                return false;
            }

            // Validate mobile banking fields if applicable
            let gateway = $('#selected_gateway').val();
            if (['bkash', 'rocket', 'nagad'].includes(gateway)) {
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

        function ucfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function formatMoney(amount) {
            return '$' + parseFloat(amount).toFixed(2);
        }
    });
</script>
@endpush
