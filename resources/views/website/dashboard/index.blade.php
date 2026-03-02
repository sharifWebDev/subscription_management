{{-- resources/views/dashboard/usage.blade.php --}}
@extends('website.layouts.app')

@section('content')
    <div class="col-9">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Usage Dashboard</h4>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Total Subscriptions</p>
                                <h5 id="total-subscriptions" class="mb-0">-</h5>
                            </div>
                            <div class="avatar-md bg-primary-light rounded-circle">
                                <i class="fas fa-credit-card fa-md text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Total CRUD Generations</p>
                                <h5 id="total-crud-usage" class="mb-0">-</h5>
                            </div>
                            <div class="avatar-md bg-success-light rounded-circle">
                                <i class="fas fa-code fa-md text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Total API Requests</p>
                                <h5 id="total-api-usage" class="mb-0">-</h5>
                            </div>
                            <div class="avatar-md bg-info-light rounded-circle">
                                <i class="fas fa-globe fa-md text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Storage Used</p>
                                <h5 id="total-storage-usage" class="mb-0">-</h5>
                            </div>
                            <div class="avatar-md bg-warning-light rounded-circle">
                                <i class="fas fa-database fa-md text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Usage by Feature</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="usageByFeatureChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Daily CRUD Generation Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyTrendChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forecast Alert Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" id="forecastAlertCard" style="display: none;">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0" id="forecastAlert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="forecastMessage"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Progress Row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">CRUD Generation by Subscription</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Subscription ID</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Usage</th>
                                        <th>Limit</th>
                                        <th>Progress</th>
                                        <th>Remaining</th>
                                        <th>Projected</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="subscriptionTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity and Forecast Details Row -->
        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentActivityList">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Forecast Details</h5>
                    </div>
                    <div class="card-body">
                        <div id="forecastDetails">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .avatar-md {
                height: 3rem;
                width: 3rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .bg-primary-light {
                background-color: rgba(13, 110, 253, 0.1);
            }

            .bg-success-light {
                background-color: rgba(25, 135, 84, 0.1);
            }

            .bg-info-light {
                background-color: rgba(13, 202, 240, 0.1);
            }

            .bg-warning-light {
                background-color: rgba(255, 193, 7, 0.1);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                let usageData = null;
                let forecastData = null;

                // Load both APIs
                Promise.all([
                    $.ajax({
                        url: '/api/v1/usage-stats',
                        method: 'GET'
                    }),
                    $.ajax({
                        url: '/api/v1/usage-forecast',
                        method: 'GET'
                    })
                ]).then(function(responses) {
                    usageData = responses[0].data;
                    forecastData = responses[1].data;

                    updateSummaryCards();
                    updateUsageByFeatureChart();
                    updateDailyTrendChart();
                    updateForecastAlert();
                    updateSubscriptionTable();
                    updateRecentActivity();
                    updateForecastDetails();
                }).catch(function(error) {
                    console.error('Failed to load data:', error);
                    showError();
                });

                function updateSummaryCards() {
                    if (!usageData) return;

                    $('#total-subscriptions').text(usageData.total_subscriptions || 0);

                    const crudFeature = usageData.aggregated_by_feature?.crud_generation;
                    const apiFeature = usageData.aggregated_by_feature?.api_requests;
                    const storageFeature = usageData.aggregated_by_feature?.storage_gb;

                    $('#total-crud-usage').text(crudFeature ? crudFeature.total_usage + ' / ' + crudFeature
                        .total_limit : '0');
                    $('#total-api-usage').text(apiFeature ? apiFeature.total_usage + ' / ' + apiFeature.total_limit :
                        '0');
                    $('#total-storage-usage').text(storageFeature ? storageFeature.total_usage + ' GB / ' +
                        storageFeature.total_limit + ' GB' : '0 GB');
                }

                function updateUsageByFeatureChart() {
                    if (!usageData || !usageData.aggregated_by_feature) return;

                    const ctx = document.getElementById('usageByFeatureChart').getContext('2d');
                    const features = Object.values(usageData.aggregated_by_feature);

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: features.map(f => f.feature_name),
                            datasets: [{
                                data: features.map(f => f.total_usage),
                                backgroundColor: [
                                    '#0d6efd', '#198754', '#0dcaf0', '#ffc107', '#dc3545',
                                    '#6610f2', '#fd7e14', '#20c997', '#6f42c1', '#d63384'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const feature = features[context.dataIndex];
                                            const percentage = (feature.total_usage / feature.total_limit *
                                                100).toFixed(1);
                                            return `${feature.feature_name}: ${feature.total_usage} / ${feature.total_limit} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                function updateDailyTrendChart() {
                    if (!usageData || !usageData.daily_aggregates) return;

                    const crudAggregates = usageData.daily_aggregates['3']; // Feature ID 3 is CRUD Generation
                    if (!crudAggregates) return;

                    const ctx = document.getElementById('dailyTrendChart').getContext('2d');

                    // Group by date
                    const dailyData = {};
                    crudAggregates.data.forEach(item => {
                        const date = item.date.split('T')[0];
                        dailyData[date] = (dailyData[date] || 0) + parseFloat(item.quantity);
                    });

                    const dates = Object.keys(dailyData).sort();
                    const quantities = dates.map(date => dailyData[date]);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dates.map(date => new Date(date).toLocaleDateString()),
                            datasets: [{
                                label: 'CRUD Generations',
                                data: quantities,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }

                function updateForecastAlert() {
                    if (!forecastData || !forecastData.forecasts_by_feature) return;

                    const crudForecast = forecastData.forecasts_by_feature.crud_generation;
                    if (!crudForecast) return;

                    if (crudForecast.will_exceed) {
                        const overage = (crudForecast.total_projected - crudForecast.total_limit).toFixed(2);
                        $('#forecastMessage').html(
                            `Warning: Based on your current usage pattern, you're projected to exceed your CRUD generation limit by ${overage} units. ` +
                            `Consider upgrading your plan or reducing usage.`
                        );
                        $('#forecastAlertCard').show();
                    }
                }

                function updateSubscriptionTable() {
                    if (!usageData || !forecastData) return;

                    const crudSubscriptions = usageData.detailed_summaries.filter(s => s.feature_code ===
                        'crud_generation');
                    const forecastSubscriptions = forecastData.forecasts_by_feature?.crud_generation?.subscriptions ||
                    [];

                    let html = '';

                    crudSubscriptions.forEach(sub => {
                        const forecast = forecastSubscriptions.find(f => f.subscription_id === sub
                            .subscription_id) || {};
                        const percentage = sub.percentage;

                        let statusClass = 'success';
                        let statusText = 'Good';

                        if (percentage >= 90) {
                            statusClass = 'danger';
                            statusText = 'Critical';
                        } else if (percentage >= 75) {
                            statusClass = 'warning';
                            statusText = 'Warning';
                        }

                        html += '<tr>';
                        html += `<td>#${sub.subscription_id}</td>`;
                        html += `<td>${sub.plan_name}</td>`;
                        html += `<td><span class="badge bg-success">${sub.subscription_status}</span></td>`;
                        html += `<td>${sub.current_usage}</td>`;
                        html += `<td>${sub.limit}</td>`;
                        html += '<td style="width: 200px;">';
                        html += '<div class="progress" style="height: 8px;">';
                        html +=
                            `<div class="progress-bar bg-${statusClass}" style="width: ${percentage}%"></div>`;
                        html += '</div>';
                        html += `<small>${percentage}%</small>`;
                        html += '</td>';
                        html += `<td>${sub.remaining}</td>`;
                        html +=
                            `<td>${forecast.projected_usage ? forecast.projected_usage.toFixed(2) : '0'}</td>`;
                        html += `<td><span class="badge bg-${statusClass}">${statusText}</span></td>`;
                        html += '</tr>';
                    });

                    if (html === '') {
                        html =
                            '<tr><td colspan="9" class="text-center text-muted">No subscription data available</td></tr>';
                    }

                    $('#subscriptionTableBody').html(html);
                }

                function updateRecentActivity() {
                    if (!usageData || !usageData.recent_usage) return;

                    let html = '<div class="list-group">';

                    usageData.recent_usage.forEach(record => {
                        html += '<div class="list-group-item">';
                        html += '<div class="d-flex justify-content-between align-items-center">';
                        html += '<div>';
                        html += `<h6 class="mb-1">${record.feature}</h6>`;
                        html += `<small class="text-muted">Subscription #${record.subscription_id}</small>`;
                        html += '</div>';
                        html += '<div class="text-end">';
                        html += `<span class="badge bg-primary">+${record.quantity} ${record.unit}</span>`;
                        html += `<br><small class="text-muted">${record.recorded_at}</small>`;
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    });

                    html += '</div>';

                    if (usageData.recent_usage.length === 0) {
                        html = '<p class="text-muted small text-center">No recent activity</p>';
                    }

                    $('#recentActivityList').html(html);
                }

                function updateForecastDetails() {
                    if (!forecastData || !forecastData.forecasts_by_feature) return;

                    let html = '';

                    Object.values(forecastData.forecasts_by_feature).forEach(feature => {
                        const willExceed = feature.will_exceed;
                        const progress = (feature.total_current_usage / feature.total_limit * 100) || 0;

                        html += '<div class="mb-4">';
                        html += `<h6>${feature.feature_name}</h6>`;
                        html += '<div class="d-flex justify-content-between mb-1">';
                        html += `<span>Current: ${feature.total_current_usage}</span>`;
                        html += `<span>Projected: ${feature.total_projected.toFixed(2)}</span>`;
                        html += `<span>Limit: ${feature.total_limit}</span>`;
                        html += '</div>';

                        html += '<div class="progress mb-2" style="height: 8px;">';
                        html +=
                            `<div class="progress-bar ${willExceed ? 'bg-warning' : 'bg-success'}" style="width: ${Math.min(progress, 100)}%"></div>`;
                        if (feature.total_projected > feature.total_limit) {
                            const overagePercent = Math.min((feature.total_projected / feature.total_limit *
                                100) - 100, 100);
                            html +=
                                `<div class="progress-bar bg-danger" style="width: ${overagePercent}%"></div>`;
                        }
                        html += '</div>';

                        if (willExceed) {
                            const overage = (feature.total_projected - feature.total_limit).toFixed(2);
                            html += `<small class="text-danger">⚠️ Expected to exceed by ${overage}</small>`;
                        }
                        html += '</div>';
                    });

                    $('#forecastDetails').html(html);
                }

                function showError() {
                    $('.spinner-border').remove();
                    $('table tbody').html(
                        '<tr><td colspan="9" class="text-center text-danger">Failed to load data. Please refresh the page.</td></tr>'
                        );
                    $('#recentActivityList').html('<p class="text-danger text-center">Failed to load data</p>');
                    $('#forecastDetails').html('<p class="text-danger text-center">Failed to load data</p>');
                }
            });
        </script>
    @endpush
@endsection
