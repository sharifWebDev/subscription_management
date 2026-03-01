@extends('website.layouts.app')

@section('title', 'My Subscriptions')

@section('content')

    <!-- Main Content -->
    <div class="col-lg-9">
        <div class="pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="p mb-0"> <i class="fas fa-home me-2"></i> Home
                    <i class="fas fa-chevron-right mx-2 text-muted small"></i>
                    <span class="text-muted small"> My Subscriptions</span>
                </span>
                <a href="{{ route('website.plans.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-plus me-2"></i> Subscribe to New Plan
                </a>
            </div>
        </div>
        <!-- Loading State -->
        <div id="subscriptionsLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading your subscriptions...</p>
        </div>

        <!-- Subscriptions Content -->
        <div id="subscriptionsContent" style="display: none;">
            <!-- Active Subscription -->
            <div id="activeSubscription" style="display: none;" class="mb-4">
                <h5 class="mb-3">Active Subscription</h5>
                <div class="card border-0 shadow-sm" id="activeSubscriptionCard"></div>
            </div>

            <!-- All Subscriptions -->
            <h4 class="mb-3">Subscription History</h4>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Period</th>
                                    <th>Next Billing</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="subscriptionsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Subscriptions Message -->
        <div id="noSubscriptionsMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
            <h3>No Subscriptions Yet</h3>
            <p class="text-muted mb-4">You haven't subscribed to any plans yet.</p>
            <a href="{{ route('website.plans.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Browse Plans
            </a>
        </div>
    </div>


    <!-- Subscription Details Modal -->
    <div class="modal fade" id="subscriptionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subscription Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="subscriptionDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Subscription Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelForm">
                    <div class="modal-body">
                        <input type="hidden" id="cancel_subscription_id">

                        <div class="mb-3">
                            <label class="form-label">Reason for Cancellation</label>
                            <select class="form-select" id="cancel_reason" required>
                                <option value="">Select a reason</option>
                                <option value="customer">No longer needed</option>
                                <option value="customer">Too expensive</option>
                                <option value="customer">Missing features</option>
                                <option value="customer">Switching to another service</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Details (Optional)</label>
                            <textarea class="form-control" id="cancel_details" rows="3"></textarea>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Your subscription will be cancelled immediately and you won't be charged again.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" id="cancelSubmitBtn">
                            <i class="fas fa-ban me-2"></i>Cancel Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="refundForm">
                    <div class="modal-body">
                        <input type="hidden" id="refund_subscription_id">

                        <div class="mb-3">
                            <label class="form-label">Reason for Refund</label>
                            <select class="form-select" id="refund_reason" required>
                                <option value="">Select a reason</option>
                                <option value="requested_by_customer">Not satisfied with service</option>
                                <option value="duplicate">Duplicate charge</option>
                                <option value="service_issue">Technical issues</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Details</label>
                            <textarea class="form-control" id="refund_details" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning" id="refundSubmitBtn">
                            <i class="fas fa-undo-alt me-2"></i>Request Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadSubscriptions();

            function loadSubscriptions() {
                // Show loader
                $('#subscriptionsLoader').show();
                $('#subscriptionsContent').hide();
                $('#noSubscriptionsMessage').hide();

                axios.get('/my-subscriptions')
                    .then(response => {
                        const data = response.data.data;

                        console.log('Subscription data:', data); // For debugging

                        $('#subscriptionsLoader').hide();

                        if (data.has_active && data.active) {
                            renderActiveSubscription(data.active);
                            $('#activeSubscription').show();
                        } else {
                            $('#activeSubscription').hide();
                        }

                        // Check if all subscriptions exist and have data array
                        if (data.all && data.all.data && data.all.data.length > 0) {
                            renderSubscriptionsTable(data.all.data);
                            $('#subscriptionsContent').show();
                        } else if (data.all && Array.isArray(data.all) && data.all.length > 0) {
                            // Handle if all is directly an array
                            renderSubscriptionsTable(data.all);
                            $('#subscriptionsContent').show();
                        } else {
                            $('#noSubscriptionsMessage').show();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading subscriptions:', error);
                        $('#subscriptionsLoader').hide();
                        $('#noSubscriptionsMessage').show();
                        $('#noSubscriptionsMessage h3').text('Error Loading Subscriptions');
                        $('#noSubscriptionsMessage p').text('Please try again later.');

                        if (error.response) {
                            console.error('Response data:', error.response.data);
                            console.error('Response status:', error.response.status);
                        }
                    });
            }

            function renderActiveSubscription(subscription) {
                const nextBilling = subscription.current_period_ends_at ?
                    new Date(subscription.current_period_ends_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) :
                    'N/A';

                // Get plan details - handle different possible structures
                const planName = subscription.plan_name ||
                    (subscription.plan_id_details ? subscription.plan_id_details.name : 'Unknown Plan');
                const planDescription = subscription.plan_id_details ?
                    subscription.plan_id_details.description : '';

                // Get price details
                const priceInterval = subscription.plan_price_id_details ?
                    subscription.plan_price_id_details.interval : 'month';

                // Format amount
                const amount = subscription.amount || 0;
                const currency = subscription.currency || 'USD';

                // Determine status badge class
                const statusClass = {
                    'active': 'success',
                    'trialing': 'info',
                    'past_due': 'warning',
                    'canceled': 'secondary',
                    'expired': 'dark'
                } [subscription.status] || 'secondary';

                let html = `
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title mb-3">${planName}</h5>
                            <p class="text-muted">${planDescription || ''}</p>

                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge bg-${statusClass}">${subscription.status}</span>
                                </div>
                                <div class="col-sm-4">
                                    <small class="text-muted d-block">Amount</small>
                                    <strong>${formatMoney(amount, currency)} / ${priceInterval}</strong>
                                </div>
                                <div class="col-sm-4">
                                    <small class="text-muted d-block">Next Billing</small>
                                    <strong>${nextBilling}</strong>
                                </div>
                            </div>

                            ${subscription.trial_ends_at && subscription.status === 'trialing' ? `
                                    <div class="alert alert-info mt-3 mb-0 py-2">
                                        <i class="fas fa-clock me-2"></i>
                                        Trial ends: ${new Date(subscription.trial_ends_at).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        })}
                                    </div>
                                ` : ''}
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button class="btn btn-outline-primary btn-sm mb-2" onclick="viewSubscription(${subscription.id})">
                                <i class="fas fa-eye me-2"></i>Details
                            </button>
                            ${subscription.status === 'active' || subscription.status === 'trialing' ? `
                                    <button class="btn btn-outline-danger btn-sm" onclick="showCancelModal(${subscription.id})">
                                        <i class="fas fa-ban me-2"></i>Cancel
                                    </button>
                                ` : ''}
                        </div>
                    </div>
                </div>
            `;

                $('#activeSubscriptionCard').html(html);
            }

            function renderSubscriptionsTable(subscriptions) {
                let html = '';

                subscriptions.forEach(sub => {
                    const nextBilling = sub.current_period_ends_at ?
                        new Date(sub.current_period_ends_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) :
                        'N/A';

                    const periodStart = sub.current_period_starts_at ?
                        new Date(sub.current_period_starts_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) :
                        'N/A';

                    const periodEnd = sub.current_period_ends_at ?
                        new Date(sub.current_period_ends_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        }) :
                        'N/A';

                    const statusClass = {
                        'active': 'success',
                        'trialing': 'info',
                        'past_due': 'warning',
                        'canceled': 'secondary',
                        'expired': 'dark'
                    } [sub.status] || 'secondary';

                    // Get plan name from different possible structures
                    const planName = sub.plan_name ||
                        (sub.plan_id_details ? sub.plan_id_details.name : 'Unknown Plan');
                    const planCode = sub.plan_id_details ? sub.plan_id_details.code : '';

                    // Get price interval
                    const priceInterval = sub.plan_price_id_details ?
                        sub.plan_price_id_details.interval : 'month';

                    // Format amount
                    const amount = sub.amount || 0;
                    const currency = sub.currency || 'USD';

                    html += `
                    <tr>
                        <td>
                            <strong>${planName}</strong>
                            <br>
                            <small class="text-muted">${planCode}</small>
                        </td>
                        <td><span class="badge bg-${statusClass}">${sub.status}</span></td>
                        <td>${formatMoney(amount, currency)} / ${priceInterval}</td>
                        <td>${periodStart} - ${periodEnd}</td>
                        <td>${nextBilling}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewSubscription(${sub.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${sub.status === 'active' || sub.status === 'trialing' ? `
                                        <button class="btn btn-outline-danger" onclick="showCancelModal(${sub.id})" title="Cancel">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    ` : ''}
                                ${sub.status === 'canceled' ? `
                                        <button class="btn btn-outline-warning" onclick="showRefundModal(${sub.id})" title="Request Refund">
                                            <i class="fas fa-undo-alt"></i>
                                        </button>
                                    ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
                });

                $('#subscriptionsTableBody').html(html);
            }

            // View subscription details
            window.viewSubscription = function(id) {
                axios.get(`/subscriptions/${id}`)
                    .then(response => {
                        const sub = response.data.data;

                        // Format dates
                        const createdDate = sub.created_at ?
                            new Date(sub.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) :
                            'N/A';

                        const periodStart = sub.current_period_starts_at ?
                            new Date(sub.current_period_starts_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) :
                            'N/A';

                        const periodEnd = sub.current_period_ends_at ?
                            new Date(sub.current_period_ends_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) :
                            'N/A';

                        // Get plan details
                        const planName = sub.plan_name ||
                            (sub.plan_id_details ? sub.plan_id_details.name : 'Unknown Plan');
                        const planType = sub.plan_id_details ? sub.plan_id_details.type : 'N/A';
                        const billingPeriod = sub.plan_id_details ?
                            `${sub.plan_id_details.billing_period} (x${sub.plan_id_details.billing_interval})` :
                            'N/A';

                        // Get price details
                        const priceInterval = sub.plan_price_id_details ?
                            sub.plan_price_id_details.interval : 'month';

                        // Format amount
                        const amount = sub.amount || 0;
                        const currency = sub.currency || 'USD';

                        // Parse history if available
                        let historyHtml = '';
                        if (sub.history && Array.isArray(sub.history)) {
                            sub.history.slice(0, 5).forEach(event => {
                                historyHtml += `
                                <div class="mb-2">
                                    <span class="badge bg-info">${event.event || 'event'}</span>
                                    <small class="text-muted ms-2">${new Date(event.date).toLocaleString()}</small>
                                </div>
                            `;
                            });
                        }

                        let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Plan Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Plan:</th>
                                        <td>${planName}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td><span class="badge bg-success">${sub.status}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td>${formatMoney(amount, currency)} / ${priceInterval}</td>
                                    </tr>
                                    <tr>
                                        <th>Current Period:</th>
                                        <td>${periodStart} - ${periodEnd}</td>
                                    </tr>
                                    <tr>
                                        <th>Billing Cycle:</th>
                                        <td>${billingPeriod}</td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>${createdDate}</td>
                                    </tr>
                                    <tr>
                                        <th>Gateway:</th>
                                        <td>${sub.gateway || 'Not specified'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Trial Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Trial Start:</th>
                                        <td>${sub.trial_starts_at ? new Date(sub.trial_starts_at).toLocaleDateString() : 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Trial End:</th>
                                        <td>${sub.trial_ends_at ? new Date(sub.trial_ends_at).toLocaleDateString() : 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Trial Converted:</th>
                                        <td>${sub.trial_converted ? 'Yes' : 'No'}</td>
                                    </tr>
                                </table>

                                <h6 class="fw-bold mt-3">Recent Events</h6>
                                <div class="small" id="eventsList">
                                    ${historyHtml || 'No events'}
                                </div>
                            </div>
                        </div>
                    `;

                        $('#subscriptionDetails').html(html);
                        $('#subscriptionModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error loading subscription details:', error);
                        toastr.error('Failed to load subscription details');
                    });
            };

            // Cancel subscription
            window.showCancelModal = function(id) {
                $('#cancel_subscription_id').val(id);
                $('#cancelModal').modal('show');
            };

            $('#cancelForm').submit(function(e) {
                e.preventDefault();

                const id = $('#cancel_subscription_id').val();
                const reason = $('#cancel_reason').val();
                const details = $('#cancel_details').val();

                $('#cancelSubmitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Cancelling...');

                axios.post(`/subscriptions/${id}/cancel`, {
                        reason: reason,
                        reason_details: details
                    })
                    .then(response => {
                        toastr.success('Subscription cancelled successfully');
                        $('#cancelModal').modal('hide');
                        loadSubscriptions(); // Reload list
                    })
                    .catch(error => {
                        console.error('Cancel error:', error);
                        toastr.error(error.response?.data?.message || 'Failed to cancel subscription');
                    })
                    .finally(() => {
                        $('#cancelSubmitBtn').prop('disabled', false).html(
                            '<i class="fas fa-ban me-2"></i>Cancel Subscription');
                    });
            });

            // Request refund
            window.showRefundModal = function(id) {
                $('#refund_subscription_id').val(id);
                $('#refundModal').modal('show');
            };

            $('#refundForm').submit(function(e) {
                e.preventDefault();

                const id = $('#refund_subscription_id').val();
                const reason = $('#refund_reason').val();
                const details = $('#refund_details').val();

                $('#refundSubmitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

                axios.post(`/subscriptions/${id}/refund`, {
                        reason: reason,
                        reason_details: details
                    })
                    .then(response => {
                        toastr.success('Refund requested successfully');
                        $('#refundModal').modal('hide');
                    })
                    .catch(error => {
                        console.error('Refund error:', error);
                        toastr.error(error.response?.data?.message || 'Failed to request refund');
                    })
                    .finally(() => {
                        $('#refundSubmitBtn').prop('disabled', false).html(
                            '<i class="fas fa-undo-alt me-2"></i>Request Refund');
                    });
            });

            // Renew subscription
            window.renewSubscription = function(id) {
                Swal.fire({
                    title: 'Renew Subscription?',
                    text: 'Your subscription will be renewed immediately',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'Yes, renew'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`/subscriptions/${id}/renew`)
                            .then(response => {
                                toastr.success('Subscription renewed successfully');
                                loadSubscriptions();
                            })
                            .catch(error => {
                                console.error('Renew error:', error);
                                toastr.error(error.response?.data?.message ||
                                    'Failed to renew subscription');
                            });
                    }
                });
            };

            // Helper functions
            function formatMoney(amount, currency = 'USD') {
                const symbols = {
                    'USD': '$',
                    'EUR': '€',
                    'GBP': '£',
                    'BDT': '৳',
                    'INR': '₹'
                };

                const symbol = symbols[currency] || '$';
                return symbol + ' ' + parseFloat(amount).toFixed(2);
            }

            // Initialize tooltips if needed
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
