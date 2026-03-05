@extends('website.layouts.app')

@section('title', 'Plan Details')

@section('content')
<!-- Breadcrumb -->
<section class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.plans.index') }}" class="text-decoration-none">Plans</a></li>
                <li class="breadcrumb-item active" aria-current="page" id="planBreadcrumb">Loading...</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Plan Details -->
<section class="py-5">
    <div class="container">
        <div id="planLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading plan details...</p>
        </div>

        <div id="planContent" style="display: none;">
            <div class="row">
                <!-- Plan Overview -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span id="featuredBadge" style="display: none;" class="badge bg-warning text-dark me-3 py-2 px-3">
                                    <i class="fas fa-star me-1"></i>Most Popular
                                </span>
                                <h1 class="h2 mb-0" id="planName"></h1>
                            </div>

                            <p class="lead text-muted mb-4" id="planDescription"></p>

                            <!-- Pricing Options -->
                            <div class="row g-3 mb-5" id="pricingOptions"></div>

                            <!-- Features List -->
                            <h3 class="h4 mb-4">All Features</h3>

                            <div class="row" id="featuresList"></div>

                            <!-- Additional Info -->
                            <div id="additionalInfo" style="display: none;">
                                <hr class="my-4">
                                <h3 class="h4 mb-3">Additional Information</h3>
                                <div class="row" id="metadataList"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Summary Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Plan Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Plan Type:</span>
                                <span class="badge bg-primary" id="planType"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Billing Cycle:</span>
                                <span id="billingCycle"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Starting Price:</span>
                                <span class="h5 mb-0 text-primary" id="startingPrice"></span>
                            </div>

                            <div id="discountsSection" style="display: none;">
                                <hr>
                                <h6 class="mb-3">Available Discounts</h6>
                                <div id="discountsList"></div>
                            </div>

                            <hr>

                            <a href="#" class="btn btn-primary btn-lg w-100" id="subscribeBtn">
                                <i class="fas fa-shopping-cart me-2"></i>Subscribe Now
                            </a>

                            <p class="text-center text-muted small mt-3">
                                <i class="fas fa-lock me-1"></i>Secure payment. 14-day free trial.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Quick Questions</h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            Can I change plans later?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes, you can upgrade or downgrade at any time. Changes are prorated.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            Is there a free trial?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Yes! All plans come with a 14-day free trial. No credit card required.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            What payment methods?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            We accept all major credit cards, PayPal, and bank transfers.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recommended Plans -->
<section class="py-5 bg-light" id="recommendedSection" style="display: none;">
    <div class="container">
        <h2 class="text-center mb-5">You Might Also Like</h2>

        <div class="row g-4" id="recommendedPlans"></div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container">
        <div class="card bg-primary text-white border-0">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="h2 mb-2" id="ctaTitle">Ready to get started?</h3>
                        <p class="lead mb-lg-0">Join thousands of satisfied customers and start growing today.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="#" class="btn btn-light btn-lg" id="ctaBtn">
                            <i class="fas fa-arrow-right me-2"></i>Subscribe Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const slug = '{{ $slug }}';
        let planData = null;

        // Fetch plan details
        fetchPlanDetails();

        function fetchPlanDetails() {
            axios.get(`/subscription-plans/${slug}`)
                .then(response => {
                    planData = response.data?.data;
                    renderPlanDetails();

                    // Fetch recommended plans
                    return axios.get('/subscription-plans', {
                        params: {
                            is_active: true,
                            is_visible: true,
                            exclude_id: planData.id,
                            limit: 3
                        }
                    });
                })
                .then(response => {
                    renderRecommendedPlans(response.data?.data?.data || []);

                    $('#planLoader').hide();
                    $('#planContent').show();

                    if (response.data.data?.length > 0) {
                        $('#recommendedSection').show();
                    }
                })
                .catch(error => {
                    console.error('Error fetching plan:', error);
                    $('#planLoader').hide();
                    $('#planContent').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                            <h3>Error Loading Plan</h3>
                            <p class="text-muted">The plan you're looking for could not be found.</p>
                            <a href="{{ route('website.plans.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Back to Plans
                            </a>
                        </div>
                    `).show();
                });
        }

        function renderPlanDetails() {
            // Update breadcrumb
            $('#planBreadcrumb').text(planData?.name ?? '');

            // Featured badge
            if (planData.is_featured) {
                $('#featuredBadge').show();
            }

            // Basic info
            $('#planName').text(planData?.name ?? '');
            $('#planDescription').text(planData.description || 'No description available');

            // Plan type badge
            $('#planType').text(planData.type.charAt(0).toUpperCase() + planData.type.slice(1));

            // Billing cycle
            $('#billingCycle').text(planData.billing_period + ' (x' + planData.billing_interval + ')');

            // Pricing options
            renderPricingOptions();

            // Features
            renderFeatures();

            // Metadata
            if (planData.metadata && Object.keys(planData.metadata).length > 0) {
                renderMetadata();
                $('#additionalInfo').show();
            }

            // Discounts
            if (planData.discounts && planData.discounts.length > 0) {
                renderDiscounts();
                $('#discountsSection').show();
            }

            // Starting price
            if (planData.prices && planData.prices.length > 0) {
                let lowestPrice = planData.prices.reduce((min, p) => p.amount < min.amount ? p : min, planData.prices[0]);
                $('#startingPrice').text(lowestPrice.amount_with_currency || '$' + parseFloat(lowestPrice.amount).toFixed(2));
            }

            // Update CTA
            $('#ctaTitle').text(`Ready to get started with ${planData.name}?`);

            let firstPrice = planData.prices?.[0];
            if (firstPrice) {
                let checkoutUrl = `/checkout/${planData.id}?price_id=${firstPrice.id}`;
                $('#subscribeBtn, #ctaBtn').attr('href', checkoutUrl);
            }
        }

        function renderPricingOptions() {
            let html = '';

            if (planData.prices && planData.prices.length > 0) {
                planData.prices.forEach((price, index) => {
                    let isBestValue = price.interval === 'year';
                    let cardClass = index === 0 ? 'border-primary' : '';

                    html += `
                        <div class="col-md-4">
                            <div class="card h-100 ${cardClass}">
                                <div class="card-body text-center">
                                    ${isBestValue ? '<span class="badge bg-success mb-2">Best Value</span>' : ''}
                                    <h3 class="h2 mb-0">${price.amount_with_currency || '$' + parseFloat(price.amount).toFixed(2)}</h3>
                                    <p class="text-muted">${price.interval_description || '/' + price.interval}</p>

                                    ${price.usage_type === 'metered' ? '<span class="badge bg-info">Usage Based</span>' : ''}

                                    ${index === 0 ? `
                                        <hr>
                                        <a href="/checkout/${planData.id}?price_id=${price.id}"
                                           class="btn btn-primary w-100 mt-3">
                                            <i class="fas fa-shopping-cart me-2"></i>Select Plan
                                        </a>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<div class="col-12 text-center"><p class="text-muted">No pricing available</p></div>';
            }

            $('#pricingOptions').html(html);
        }

        function renderFeatures() {
            let html = '';

            if (planData.features && planData.features.length > 0) {
                planData.features.forEach(feature => {
                    let badge = '';
                    if (feature.value === 'unlimited') {
                        badge = '<span class="badge bg-info ms-2">Unlimited</span>';
                    } else if (feature.value === 'true' || feature.value === '1') {
                        badge = '<span class="badge bg-success ms-2">Included</span>';
                    } else if (isNumeric(feature.value)) {
                        badge = `<span class="badge bg-primary ms-2">${feature.value}</span>`;
                    } else {
                        badge = `<span class="badge bg-secondary ms-2">${feature.value}</span>`;
                    }

                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-circle text-success mt-1 me-3 flex-shrink-0"></i>
                                <div>
                                    <strong>${feature.feature_name || 'Feature'}</strong>
                                    ${feature.feature_description ?
                                        `<p class="text-muted small mb-1">${feature.feature_description}</p>` : ''}
                                    <div>${badge}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<div class="col-12 text-center"><p class="text-muted">No features listed</p></div>';
            }

            $('#featuresList').html(html);
        }

        function renderMetadata() {
            let html = '';

            Object.entries(planData.metadata).forEach(([key, value]) => {
                let displayValue = typeof value === 'object' ? JSON.stringify(value) : value;
                html += `
                    <div class="col-md-6 mb-2">
                        <strong>${formatKey(key)}:</strong> ${displayValue}
                    </div>
                `;
            });

            $('#metadataList').html(html);
        }

        function renderDiscounts() {
            let html = '';

            planData.discounts.forEach(discount => {
                let amount = discount.type === 'percentage' ? discount.amount + '%' : '$' + discount.amount;
                let duration = discount.duration !== 'forever' ? `(${discount.duration})` : '';

                html += `
                    <div class="alert alert-success py-2 px-3 mb-2">
                        <i class="fas fa-tag me-2"></i>
                        <strong>${discount.code}</strong> - ${discount.name}
                        <br>
                        <small>${amount} off ${duration}</small>
                    </div>
                `;
            });

            $('#discountsList').html(html);
        }

        function renderRecommendedPlans(plans) {
            let html = '';

            plans.forEach(plan => {
                let price = plan.prices?.[0];
                let priceDisplay = price ? (price.amount_with_currency || '$' + parseFloat(price.amount).toFixed(2)) : 'Contact Us';

                html += `
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">${plan.name}</h5>
                                <p class="card-text text-muted small">${truncateText(plan.description || '', 80)}</p>

                                <p class="h5 mb-3">
                                    ${priceDisplay}
                                    <small class="text-muted">/${price?.interval || ''}</small>
                                </p>

                                <a href="/plan/${plan.slug}" class="btn btn-outline-primary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#recommendedPlans').html(html);
        }

        // Helper functions
        function isNumeric(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        }

        function formatKey(key) {
            return key.split('_').map(word =>
                word.charAt(0).toUpperCase() + word.slice(1)
            ).join(' ');
        }

        function truncateText(text, length) {
            if (text.length <= length) return text;
            return text.substring(0, length) + '...';
        }
    });
</script>
@endpush
