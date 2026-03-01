@extends('website.layouts.app')

@section('title', 'Usage Statistics')

@section('content')

    <!-- Main Content -->
    <div class="col-lg-9">
        <div class="pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="p mb-0"> <i class="fas fa-home me-2"></i> Home
                    <i class="fas fa-chevron-right mx-2 text-muted small"></i>
                    <span class="text-muted small"> Usage Statistics</span>
                </span>
                <a href="{{ route('website.plans.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-plus me-2"></i> Subscribe to New Plan
                </a>
            </div>
        </div>
        <!-- Loading State -->
        <div id="usageLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading usage statistics...</p>
        </div>

        <!-- No Subscription Message -->
        <div id="noSubscriptionMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
            <h3>No Active Subscription</h3>
            <p class="text-muted mb-4">You need an active subscription to view usage statistics.</p>
            <a href="{{ route('website.plans.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Browse Plans
            </a>
        </div>

        <!-- Usage Content -->
        <div id="usageContent" style="display: none;">
            <!-- Subscription Selector -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Select Subscription</label>
                            <select class="form-select" id="subscriptionSelector">
                                <option value="">Choose a subscription...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Period</label>
                            <select class="form-select" id="periodSelector">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Date</label>
                            <input type="month" class="form-control" id="dateSelector" value="{{ date('Y-m') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="applyFilters" title="Apply Filters">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Billing Period Card -->
            <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white" id="currentPeriodCard"
                style="display: none;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-3">Current Billing Period</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <small>Period Start</small>
                                    <h6 id="periodStart">-</h6>
                                </div>
                                <div class="col-md-4">
                                    <small>Period End</small>
                                    <h6 id="periodEnd">-</h6>
                                </div>
                                <div class="col-md-4">
                                    <small>Days Remaining</small>
                                    <h6 id="daysRemaining">-</h6>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-light" id="periodProgress" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="current-cost">
                                <small>Current Period Cost</small>
                                <h2 id="currentPeriodCost">$0.00</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Total Usage</h6>
                            <h3 class="mb-0" id="totalUsage">0</h3>
                            <small id="totalUsageUnit">units</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Total Cost</h6>
                            <h3 class="mb-0" id="totalCost">$0.00</h3>
                            <small>This period</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Records</h6>
                            <h3 class="mb-0" id="totalRecords">0</h3>
                            <small>Usage entries</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title text-white-50">Daily Avg</h6>
                            <h3 class="mb-0" id="dailyAverage">0</h3>
                            <small>Per day</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Chart -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daily Usage Trend</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary chart-type active" data-type="quantity">
                            <i class="fas fa-chart-bar me-1"></i>Quantity
                        </button>
                        <button class="btn btn-outline-primary chart-type" data-type="cost">
                            <i class="fas fa-dollar-sign me-1"></i>Cost
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="usageChart" style="height: 300px;"></canvas>
                    <div id="noChartData" class="text-center text-muted py-4" style="display: none;">
                        <i class="fas fa-chart-line fa-3x mb-2"></i>
                        <p>No usage data available for this period</p>
                    </div>
                </div>
            </div>

            <!-- Feature Usage Breakdown -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Feature Usage Breakdown</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Feature</th>
                                    <th>Usage</th>
                                    <th>Limit</th>
                                    <th>Remaining</th>
                                    <th>Cost</th>
                                    <th>Utilization</th>
                                </tr>
                            </thead>
                            <tbody id="featureUsageTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Usage Records -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Recent Usage Records</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Feature</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentUsageTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Usage Details Modal -->
    <div class="modal fade" id="usageDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Usage Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="usageDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .current-cost h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .btn-group .btn.active {
            background-color: #0d6efd;
            color: white;
        }

        .table> :not(caption)>*>* {
            padding: 0.75rem;
        }

        .badge-utilization {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let subscriptions = [];
            let selectedSubscriptionId = null;
            let usageChart = null;
            let currentChartType = 'quantity';

            // Initialize
            loadSubscriptions();

            // Event Handlers
            $('#applyFilters').click(function() {
                if (selectedSubscriptionId) {
                    loadUsageData(selectedSubscriptionId);
                }
            });

            $('#subscriptionSelector').change(function() {
                selectedSubscriptionId = $(this).val();
                if (selectedSubscriptionId) {
                    loadUsageData(selectedSubscriptionId);
                    loadCurrentBilling(selectedSubscriptionId);
                }
            });

            $('.chart-type').click(function() {
                $('.chart-type').removeClass('active');
                $(this).addClass('active');
                currentChartType = $(this).data('type');

                if (window.chartData) {
                    updateChart(window.chartData);
                }
            });

            // Load user's subscriptions
            function loadSubscriptions() {
                $('#usageLoader').show();
                $('#usageContent').hide();
                $('#noSubscriptionMessage').hide();

                axios.get('/usage')
                    .then(response => {
                        const data = response.data.data;

                        $('#usageLoader').hide();

                        if (data.has_subscription && data.subscriptions?.length > 0) {
                            subscriptions = data.subscriptions;
                            populateSubscriptionSelector();

                            // Select first subscription by default
                            selectedSubscriptionId = subscriptions[0].id;
                            $('#subscriptionSelector').val(selectedSubscriptionId);

                            loadUsageData(selectedSubscriptionId);
                            loadCurrentBilling(selectedSubscriptionId);
                            $('#usageContent').show();
                        } else {
                            $('#noSubscriptionMessage').show();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading subscriptions:', error);
                        $('#usageLoader').hide();
                        $('#noSubscriptionMessage').show();
                        $('#noSubscriptionMessage h3').text('Error Loading Data');
                        $('#noSubscriptionMessage p').text('Please try again later.');
                    });
            }

            // Populate subscription selector
            function populateSubscriptionSelector() {
                let options = '<option value="">Select Subscription</option>';

                subscriptions.forEach(sub => {
                    const planName = sub.plan?.name || sub.plan_name || 'Unknown Plan';
                    const status = sub.status || 'active';
                    const statusBadge = status === 'active' ? '🟢' : '🟡';

                    options += `<option value="${sub.id}">${statusBadge} ${planName}</option>`;
                });

                $('#subscriptionSelector').html(options);
            }

            // Load current billing information
            function loadCurrentBilling(subscriptionId) {
                //convert int subscription id
                axios.get(`/current-billing`, {
                        params: {
                            subscription_id: parseInt(subscriptionId)
                        }
                    })
                    .then(response => {
                        const data = response.data.data;

                        $('#periodStart').text(formatDate(data.subscription.period_start));
                        $('#periodEnd').text(formatDate(data.subscription.period_end));
                        $('#daysRemaining').text(data.subscription.days_remaining + ' days');
                        $('#periodProgress').css('width', data.subscription.progress_percentage + '%');
                        $('#currentPeriodCost').text(formatMoney(data.total_cost));

                        $('#currentPeriodCard').show();
                    })
                    .catch(error => {
                        console.error('Error loading current billing:', error);
                        $('#currentPeriodCard').hide();
                    });
            }

            // Load usage data for selected subscription
            function loadUsageData(subscriptionId) {
                const period = $('#periodSelector').val();
                const date = $('#dateSelector').val();

                const subscriptionIdInt = parseInt(subscriptionId);

                axios.get(`/usage/${subscriptionIdInt}`, {
                        params: {
                            period: period,
                            date: date
                        }
                    })
                    .then(response => {
                        const data = response.data.data;

                        updateSummaryCards(data.summary);
                        renderFeatureTable(data.features);
                        renderRecentUsage(data.features);

                        window.chartData = data.chart_data || [];
                        updateChart(window.chartData);
                    })
                    .catch(error => {
                        console.error('Error loading usage data:', error);
                        toastr.error('Failed to load usage data');
                    });
            }

            // Update summary cards
            function updateSummaryCards(summary) {
                $('#totalUsage').text(formatNumber(summary.total_usage));
                $('#totalUsageUnit').text('units');

                $('#totalCost').text(formatMoney(summary.total_cost));
                $('#totalRecords').text(formatNumber(summary.record_count));

                const dailyAvg = summary.record_count > 0 ?
                    (summary.total_usage / summary.record_count) :
                    0;
                $('#dailyAverage').text(formatNumber(dailyAvg));
            }

            // Render feature usage table
            function renderFeatureTable(features) {
                let html = '';

                if (!features || features.length === 0) {
                    html = '<tr><td colspan="6" class="text-center text-muted">No usage data available</td></tr>';
                } else {
                    features.forEach(feature => {
                        const percentage = feature.percentage || 0;
                        const progressClass = percentage > 80 ? 'bg-danger' : (percentage > 60 ?
                            'bg-warning' : 'bg-success');

                        const limit = feature.limit || '∞';
                        const remaining = feature.limit ? (feature.limit - feature.total_quantity) : '∞';

                        html += `
                        <tr onclick="showUsageDetails('${feature.feature_name}', ${feature.total_quantity}, ${feature.total_cost})" style="cursor: pointer;">
                            <td>
                                <strong>${feature.feature_name}</strong>
                                <br>
                                <small class="text-muted">${feature.feature_code || ''}</small>
                            </td>
                            <td>
                                <strong>${formatNumber(feature.total_quantity)}</strong>
                                <br>
                                <small class="text-muted">${feature.unit || 'units'}</small>
                            </td>
                            <td>${limit} ${feature.unit || ''}</td>
                            <td>${remaining} ${feature.unit || ''}</td>
                            <td>${formatMoney(feature.total_cost)}</td>
                            <td style="width: 200px;">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar ${progressClass}" style="width: ${percentage}%"></div>
                                    </div>
                                    <span class="ms-2 small fw-bold">${percentage}%</span>
                                </div>
                                <small class="text-muted">Daily avg: ${formatNumber(feature.daily_average)}</small>
                            </td>
                        </tr>
                    `;
                    });
                }

                $('#featureUsageTable').html(html);
            }

            // Render recent usage records
            function renderRecentUsage(features) {
                let html = '';
                let hasRecords = false;

                if (features && features.length > 0) {
                    // Collect all usage records from features
                    features.forEach(feature => {
                        if (feature.record_count > 0) {
                            hasRecords = true;
                            // This is simplified - in real app, you'd have actual records
                            html += `
                            <tr>
                                <td>${new Date().toLocaleDateString()}</td>
                                <td>${feature.feature_name}</td>
                                <td>${formatNumber(feature.total_quantity)}</td>
                                <td>${feature.unit || 'units'}</td>
                                <td>${formatMoney(feature.total_cost)}</td>
                                <td><span class="badge bg-success">billed</span></td>
                            </tr>
                        `;
                        }
                    });
                }

                if (!hasRecords) {
                    html = '<tr><td colspan="6" class="text-center text-muted">No recent usage records</td></tr>';
                }

                $('#recentUsageTable').html(html);
            }

            // Update chart
            function updateChart(chartData) {
                const ctx = document.getElementById('usageChart').getContext('2d');

                if (usageChart) {
                    usageChart.destroy();
                }

                if (!chartData || chartData.length === 0) {
                    $('#noChartData').show();
                    return;
                }

                $('#noChartData').hide();

                const labels = chartData.map(d => d.label);
                const values = chartData.map(d => currentChartType === 'quantity' ? d.quantity : d.amount);

                usageChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: currentChartType === 'quantity' ? 'Usage Quantity' :
                                'Usage Cost',
                            data: values,
                            backgroundColor: 'rgba(13, 110, 253, 0.7)',
                            borderColor: '#0d6efd',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (currentChartType === 'cost') {
                                                label += formatMoney(context.parsed.y);
                                            } else {
                                                label += formatNumber(context.parsed.y);
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (currentChartType === 'cost') {
                                            return formatMoney(value);
                                        }
                                        return formatNumber(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Show usage details modal
            window.showUsageDetails = function(featureName, quantity, cost) {
                const details = `
                <div class="text-center mb-3">
                    <h6 class="fw-bold">${featureName}</h6>
                </div>
                <table class="table table-sm">
                    <tr>
                        <th>Total Quantity:</th>
                        <td class="text-end">${formatNumber(quantity)}</td>
                    </tr>
                    <tr>
                        <th>Total Cost:</th>
                        <td class="text-end">${formatMoney(cost)}</td>
                    </tr>
                    <tr>
                        <th>Average Cost per Unit:</th>
                        <td class="text-end">${quantity > 0 ? formatMoney(cost / quantity) : '$0.00'}</td>
                    </tr>
                </table>
            `;

                $('#usageDetails').html(details);
                $('#usageDetailsModal').modal('show');
            };

            // Helper functions
            function formatNumber(num) {
                if (num === null || num === undefined) return '0';
                if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
                if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
                return num.toFixed(2);
            }

            function formatMoney(amount) {
                if (amount === null || amount === undefined) return '$0.00';
                return '$' + parseFloat(amount).toFixed(2);
            }

            function formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        });
    </script>
@endpush
