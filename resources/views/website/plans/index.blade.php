@extends('website.layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">Choose Your Perfect Plan</h1>
        <p class="lead mb-4">Simple, transparent pricing that grows with you. Try any plan free for 14 days.</p>
        <div class="d-flex justify-content-center gap-3">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-light active" id="monthlyBtn">Monthly</button>
                <button type="button" class="btn btn-outline-light" id="yearlyBtn">Yearly <span class="badge bg-success ms-2">Save 20%</span></button>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Cards -->
<section class="py-5">
    <div class="container">
        <div id="plansLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading plans...</p>
        </div>

        <div id="plansContainer" style="display: none;">
            <div class="row g-4 justify-content-center" id="plansRow"></div>
        </div>

        <div id="noPlansMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h3>No Plans Available</h3>
            <p class="text-muted">Please check back later for our subscription plans.</p>
        </div>
    </div>
</section>

<!-- Feature Comparison Table -->
<section class="py-5 bg-light" id="comparisonSection" style="display: none;">
    <div class="container">
        <h2 class="text-center mb-5">Compare All Features</h2>

        <div class="table-responsive">
            <table class="table table-bordered comparison-table">
                <thead>
                    <tr id="comparisonHeader"></tr>
                </thead>
                <tbody id="comparisonBody"></tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="faq-section">
            <h2 class="text-center mb-4">Frequently Asked Questions</h2>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon bg-primary text-white">
                            <i class="fas fa-question"></i>
                        </div>
                        <div>
                            <h5>Can I change plans later?</h5>
                            <p class="text-muted">Yes, you can upgrade or downgrade your plan at any time. Changes will be prorated.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon bg-primary text-white">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h5>What payment methods do you accept?</h5>
                            <p class="text-muted">We accept all major credit cards, PayPal, and bank transfers for annual plans.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon bg-primary text-white">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h5>Is there a free trial?</h5>
                            <p class="text-muted">Yes, all plans come with a 14-day free trial. No credit card required.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon bg-primary text-white">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <div>
                            <h5>Can I get a refund?</h5>
                            <p class="text-muted">We offer a 30-day money-back guarantee if you're not completely satisfied.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Get Started?</h2>
        <p class="lead mb-4">Join thousands of satisfied customers and take your business to the next level.</p>
        <a href="#top" class="btn btn-light btn-lg">
            <i class="fas fa-arrow-up me-2"></i>View Plans
        </a>
    </div>
</section>
@endsection

@push('styles')
<style>
    .bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .feature-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        let plans = [];
        let allFeatures = [];
        let currentBillingPeriod = 'month';

        // Fetch plans from API
        fetchPlans();

        function fetchPlans() {
            // Show loader
            $('#plansLoader').show();
            $('#plansContainer').hide();
            $('#noPlansMessage').hide();
            $('#comparisonSection').hide();

            axios.get('/subscription-plans', {
                params: {
                    is_active: true,
                    is_visible: true
                }
            })
            .then(response => {
                // Handle different response structures
                plans = response.data?.data?.data || response.data || [];

                // Make sure plans is an array
                if (!Array.isArray(plans)) {
                    plans = [];
                }

                // Also fetch features for comparison
                return axios.get('/features?all=true');
            })
            .then(response => {
                // Handle different response structures for features
                let featuresData = response.data?.data || response.data || [];

                // Make sure featuresData is an array
                if (Array.isArray(featuresData)) {
                    allFeatures = featuresData;
                } else {
                    allFeatures = [];
                }

                renderPlans();

                // Only render comparison table if we have features
                if (allFeatures.length > 0) {
                    renderComparisonTable();
                }

                $('#plansLoader').hide();
                if (plans.length > 0) {
                    $('#plansContainer').show();
                    if (allFeatures.length > 0) {
                        $('#comparisonSection').show();
                    }
                } else {
                    $('#noPlansMessage').show();
                }
            })
            .catch(error => {
                console.error('Error fetching plans:', error);
                $('#plansLoader').hide();
                $('#noPlansMessage').show();
                $('#noPlansMessage h3').text('Error Loading Plans');
                $('#noPlansMessage p').text('Please try again later.');

                // Log detailed error for debugging
                if (error.response) {
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                }
            });
        }

        function renderPlans() {
            if (!plans || !Array.isArray(plans) || plans.length === 0) {
                return;
            }

            let html = '';

            plans.forEach(plan => {
                // Safely access plan properties
                let monthlyPrice = null;
                let yearlyPrice = null;

                if (plan.prices && Array.isArray(plan.prices)) {
                    monthlyPrice = plan.prices.find(p => p && p.interval === 'month');
                    yearlyPrice = plan.prices.find(p => p && p.interval === 'year');
                }

                let priceToShow = currentBillingPeriod === 'month' ? monthlyPrice : yearlyPrice;

                if (!priceToShow && plan.prices && plan.prices.length > 0) {
                    priceToShow = plan.prices[0] || null;
                }

                let headerClass = 'basic';
                if (plan.is_featured) headerClass = 'pro';
                if (plan.type === 'enterprise') headerClass = 'enterprise';

                let featuredBadge = plan.is_featured ? '<div class="badge-popular">Most Popular</div>' : '';

                let priceDisplay = 'Contact Us';
                let periodDisplay = '';

                if (priceToShow) {
                    if (priceToShow.amount > 0) {
                        priceDisplay = priceToShow.amount_with_currency ||
                                      (priceToShow.currency || '$') + parseFloat(priceToShow.amount).toFixed(2);
                        periodDisplay = '/ ' + (priceToShow.interval_description || priceToShow.interval || 'month');
                    } else {
                        priceDisplay = 'Free';
                        periodDisplay = '';
                    }
                }

                let featuresHtml = '';
                let displayedFeatures = (plan.features && Array.isArray(plan.features)) ?
                                        plan.features.slice(0, 5) : [];

                displayedFeatures.forEach(feature => {
                    if (!feature) return;

                    let badge = feature.value === 'unlimited' ?
                               '<span class="badge bg-info ms-1">Unlimited</span>' : '';

                    featuresHtml += `
                        <li>
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>${feature.feature_name || feature.feature?.name || 'Feature'}:</strong>
                            ${feature.value || ''} ${badge}
                        </li>
                    `;
                });

                if (plan.features && plan.features.length > 5) {
                    featuresHtml += `
                        <li class="text-muted">
                            <i class="fas fa-plus-circle text-info"></i>
                            +${plan.features.length - 5} more features
                        </li>
                    `;
                }

                html += `
                    <div class="col-lg-4 col-md-6">
                        <div class="card pricing-card h-100 shadow-sm ${plan.is_featured ? 'featured' : ''}">
                            ${featuredBadge}

                            <div class="pricing-header ${headerClass}">
                                <h3 class="h4 mb-2">${plan.name || 'Unnamed Plan'}</h3>
                                <p class="mb-3 opacity-75">${plan.description || ''}</p>
                                <div class="price-tag"
                                     data-monthly-price="${monthlyPrice?.amount || 0}"
                                     data-yearly-price="${yearlyPrice?.amount || 0}">
                                    ${priceDisplay}
                                </div>
                                <div class="price-period"
                                     data-monthly-period="per month"
                                     data-yearly-period="per year">
                                    ${periodDisplay}
                                </div>
                            </div>

                            <div class="card-body">
                                <ul class="feature-list">
                                    ${featuresHtml || '<li class="text-muted">No features listed</li>'}
                                </ul>
                            </div>

                            <div class="card-footer bg-transparent border-0 pb-4">
                                <a href="/plan/${plan.slug || plan.id}"
                                   class="btn btn-outline-primary btn-subscribe w-100 mb-2">
                                    <i class="fas fa-info-circle me-2"></i>View Details
                                </a>

                                ${priceToShow ? `
                                    <a href="/checkout/${plan.id}?price_id=${priceToShow.id}"
                                       class="btn btn-primary btn-subscribe w-100">
                                        ${priceToShow.amount > 0 ?
                                            '<i class="fas fa-shopping-cart me-2"></i>Subscribe Now' :
                                            '<i class="fas fa-rocket me-2"></i>Get Started Free'}
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#plansRow').html(html);
        }

        function renderComparisonTable() {
            // Safety checks
            if (!plans || !Array.isArray(plans) || plans.length === 0) {
                console.log('No plans available for comparison');
                return;
            }

            if (!allFeatures || !Array.isArray(allFeatures) || allFeatures.length === 0) {
                console.log('No features available for comparison');
                return;
            }

            try {
                // Render header
                let headerHtml = '<th style="width: 200px;">Feature</th>';
                plans.forEach(plan => {
                    headerHtml += `<th class="text-center">${plan.name || 'Plan'}</th>`;
                });
                $('#comparisonHeader').html(headerHtml);

                // Render body
                let bodyHtml = '';

                allFeatures.forEach(feature => {
                    if (!feature) return;

                    bodyHtml += '<tr>';
                    bodyHtml += `
                        <td>
                            <strong>${feature.name || 'Feature'}</strong>
                            ${feature.description ? '<br><small class="text-muted">' + feature.description + '</small>' : ''}
                        </td>
                    `;

                    plans.forEach(plan => {
                        let planFeature = null;

                        // Safely find the feature in plan features
                        if (plan.features && Array.isArray(plan.features)) {
                            planFeature = plan.features.find(f => f && f.feature_id === feature.id);
                        }

                        if (planFeature && planFeature.value) {
                            if (planFeature.value === 'true' || planFeature.value === '1') {
                                bodyHtml += '<td class="text-center"><i class="fas fa-check-circle text-success fa-lg" title="Included"></i></td>';
                            } else if (planFeature.value === 'false' || planFeature.value === '0') {
                                bodyHtml += '<td class="text-center"><i class="fas fa-times-circle text-danger fa-lg" title="Not included"></i></td>';
                            } else {
                                let badgeClass = planFeature.value === 'unlimited' ? 'bg-info' : 'bg-primary';
                                bodyHtml += `<td class="text-center"><span class="badge ${badgeClass}">${planFeature.value}</span></td>`;
                            }
                        } else {
                            bodyHtml += '<td class="text-center"><i class="fas fa-minus text-muted"></i></td>';
                        }
                    });

                    bodyHtml += '</tr>';
                });

                $('#comparisonBody').html(bodyHtml);
            } catch (error) {
                console.error('Error rendering comparison table:', error);
                $('#comparisonSection').hide();
            }
        }

        // Monthly/Yearly toggle
        $('#monthlyBtn, #yearlyBtn').click(function() {
            let isMonthly = $(this).attr('id') === 'monthlyBtn';
            currentBillingPeriod = isMonthly ? 'month' : 'year';

            // Update button states
            $('#monthlyBtn, #yearlyBtn').removeClass('active btn-light').addClass('btn-outline-light');
            $(this).addClass('active btn-light').removeClass('btn-outline-light');

            // Update prices in cards
            $('.pricing-card').each(function() {
                let card = $(this);
                let priceTag = card.find('.price-tag');
                let periodTag = card.find('.price-period');

                let monthlyPrice = priceTag.data('monthly-price');
                let yearlyPrice = priceTag.data('yearly-price');

                if (isMonthly && monthlyPrice > 0) {
                    priceTag.text('$' + parseFloat(monthlyPrice).toFixed(2));
                    periodTag.text('/ month');
                } else if (!isMonthly && yearlyPrice > 0) {
                    priceTag.text('$' + parseFloat(yearlyPrice).toFixed(2));
                    periodTag.text('/ year');
                }
            });
        });
    });
</script>
@endpush
