{{-- resources/views/crud-generator/create.blade.php --}}
@extends('website.layouts.app')

@section('content')
<div class="col-md-9">
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>CRUD Generator</h3>
                </div>
                <div class="card-body">

                    @if(isset($usageData))
                        @php
                            $crudFeature = $usageData['aggregated_by_feature']['crud_generation'] ?? null;
                        @endphp

                        @if($crudFeature)
                            <!-- Aggregated Usage Summary -->
                            <div class="alert alert-info mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Total CRUD Generation Usage (Across {{ $usageData['total_subscriptions'] }} Subscription{{ $usageData['total_subscriptions'] > 1 ? 's' : '' }})</strong>
                                        <p class="mb-0">
                                            Used: {{ number_format($crudFeature['total_usage'] ?? 0) }} /
                                            @if($crudFeature['is_unlimited'] ?? false)
                                                Unlimited
                                            @else
                                                {{ number_format($crudFeature['total_limit'] ?? 0) }}
                                            @endif
                                        </p>
                                    </div>
                                    @if(!($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_limit'] ?? 0) > 0)
                                        @php $percentage = ($crudFeature['total_usage'] / $crudFeature['total_limit']) * 100; @endphp
                                        <div class="text-end">
                                            <div class="progress" style="width: 200px; height: 10px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min(100, $percentage) }}%"
                                                    aria-valuenow="{{ $percentage }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ number_format($crudFeature['total_limit'] - $crudFeature['total_usage']) }} remaining across all subscriptions
                                            </small>
                                        </div>
                                    @elseif($crudFeature['is_unlimited'] ?? false)
                                        <span class="badge bg-success">Unlimited Usage</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Warning if near limit -->
                            @if(!($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_usage'] / $crudFeature['total_limit'] * 100) >= 80)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> You've used {{ number_format(($crudFeature['total_usage'] / $crudFeature['total_limit'] * 100), 1) }}% of your total CRUD generation limit.
                                    @if(($crudFeature['total_limit'] - $crudFeature['total_usage']) < 10)
                                        Only {{ number_format($crudFeature['total_limit'] - $crudFeature['total_usage']) }} generations remaining.
                                    @endif
                                </div>
                            @endif

                            <!-- No remaining usage warning -->
                            @if(!($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_limit'] - $crudFeature['total_usage']) <= 0)
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i>
                                    <strong>Limit Reached:</strong> You have no remaining CRUD generations across all your subscriptions.
                                    <a href="{{ route('website.plans.index') }}" class="alert-link">Upgrade your plan</a> or wait for the next billing cycle.
                                </div>
                            @endif
                        @endif
                    @endif

                    <form method="POST" action="{{ route('crud.generator.generate') }}" id="crudForm">
                        @csrf

                        <div class="mb-3">
                            <label for="table_name" class="form-label">Table Name</label>
                            <input type="text" class="form-control @error('table_name') is-invalid @enderror"
                                id="table_name" name="table_name" value="{{ old('table_name') }}" required>
                            @error('table_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="model_name" class="form-label">Model Name</label>
                            <input type="text" class="form-control @error('model_name') is-invalid @enderror"
                                id="model_name" name="model_name" value="{{ old('model_name') }}" required>
                            @error('model_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fields" class="form-label">Fields (JSON)</label>
                            <textarea class="form-control @error('fields') is-invalid @enderror"
                                id="fields" name="fields" rows="5" required>{{ old('fields', '[{"name":"title","type":"string"},{"name":"content","type":"text"}]') }}</textarea>
                            @error('fields')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter fields in JSON format. Each field needs a name and type.</small>
                        </div>

                        <button type="submit" class="btn btn-primary" id="generateBtn"
                            @if(isset($crudFeature) && !($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_limit'] - $crudFeature['total_usage']) <= 0) disabled @endif>
                            <i class="fas fa-cogs"></i> Generate CRUD
                        </button>

                        @if(isset($crudFeature) && !($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_limit'] - $crudFeature['total_usage']) <= 0)
                            <p class="text-danger mt-2 mb-0">
                                <small>Cannot generate: You've reached your usage limit.</small>
                            </p>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div id="quick-stats">
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

    <!-- Subscription Details Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="usageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab">Subscription Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="forecast-tab" data-bs-toggle="tab" data-bs-target="#forecast" type="button" role="tab">Usage Forecast</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab">Recent Activity</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="all-features-tab" data-bs-toggle="tab" data-bs-target="#all-features" type="button" role="tab">All Features</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="usageTabsContent">
                        <!-- Subscriptions Tab -->
                        <div class="tab-pane fade show active" id="subscriptions" role="tabpanel">
                            <div id="subscriptions-content">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Forecast Tab -->
                        <div class="tab-pane fade" id="forecast" role="tabpanel">
                            <div id="forecast-content">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity Tab -->
                        <div class="tab-pane fade" id="recent" role="tabpanel">
                            <div id="recent-content">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- All Features Tab -->
                        <div class="tab-pane fade" id="all-features" role="tabpanel">
                            <div id="all-features-content">
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
    </div>
</div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load all data on page load
    loadUsageStats();
    loadForecastData();

    function loadUsageStats() {
        $.ajax({
            url: '/api/v1/usage-stats',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    updateQuickStats(response.data);
                    updateSubscriptionsTab(response.data);
                    updateRecentActivityTab(response.data);
                    updateAllFeaturesTab(response.data);
                }
            },
            error: function(xhr) {
                console.error('Failed to load usage stats:', xhr);
                showError('subscriptions-content', 'Failed to load subscription data');
            }
        });
    }

    function loadForecastData() {
        $.ajax({
            url: '/api/v1/usage-forecast',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    updateForecastTab(response.data);
                }
            },
            error: function(xhr) {
                console.error('Failed to load forecast data:', xhr);
                showError('forecast-content', 'Failed to load forecast data');
            }
        });
    }

    function updateQuickStats(data) {
        let html = '';

        if (data.has_subscription) {
            const crudFeature = data.aggregated_by_feature?.crud_generation;

            if (crudFeature) {
                html += '<div class="mb-3">';
                html += '<small class="text-muted">Total Subscriptions</small>';
                html += '<p class="h4">' + data.total_subscriptions + '</p>';
                html += '</div>';

                html += '<div class="mb-3">';
                html += '<small class="text-muted">CRUD Generations</small>';
                html += '<p class="h4">' + crudFeature.total_usage + ' / ' + crudFeature.total_limit + '</p>';
                html += '<div class="progress" style="height: 5px;">';
                let percentage = (crudFeature.total_usage / crudFeature.total_limit) * 100;
                html += '<div class="progress-bar" style="width: ' + percentage + '%"></div>';
                html += '</div>';
                html += '</div>';

                html += '<div class="mb-3">';
                html += '<small class="text-muted">API Requests</small>';
                const apiFeature = data.aggregated_by_feature?.api_requests;
                if (apiFeature) {
                    html += '<p class="h5">' + apiFeature.total_usage + ' / ' + apiFeature.total_limit + '</p>';
                }
                html += '</div>';
            }
        } else {
            html = '<p class="text-muted">' + data.message + '</p>';
        }

        $('#quick-stats').html(html);
    }

    function updateSubscriptionsTab(data) {
        let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
        html += '<thead><tr><th>ID</th><th>Plan</th><th>Status</th><th>CRUD Usage</th><th>Remaining</th><th>Progress</th></tr></thead><tbody>';

        // Filter for CRUD generation feature only
        const crudSubscriptions = data.detailed_summaries.filter(s => s.feature_code === 'crud_generation');

        crudSubscriptions.forEach(function(sub) {
            html += '<tr>';
            html += '<td>#' + sub.subscription_id + '</td>';
            html += '<td>' + sub.plan_name + '</td>';
            html += '<td><span class="badge bg-success">' + sub.subscription_status + '</span></td>';
            html += '<td>' + sub.current_usage + ' / ' + sub.limit + '</td>';
            html += '<td>' + sub.remaining + '</td>';
            html += '<td style="width: 150px;">';
            html += '<div class="progress" style="height: 5px;">';
            html += '<div class="progress-bar" style="width: ' + sub.percentage + '%"></div>';
            html += '</div><small>' + sub.percentage + '%</small>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';

        if (crudSubscriptions.length === 0) {
            html = '<p class="text-muted">No subscription data available</p>';
        }

        $('#subscriptions-content').html(html);
    }

    function updateForecastTab(data) {
        let html = '';

        if (data.forecasts_by_feature && data.forecasts_by_feature.crud_generation) {
            const forecast = data.forecasts_by_feature.crud_generation;

            html += '<div class="alert ' + (forecast.will_exceed ? 'alert-warning' : 'alert-info') + '">';
            html += '<strong>CRUD Generation Forecast</strong><br>';
            html += 'Current: ' + forecast.total_current_usage + ' | ';
            html += 'Projected: ' + forecast.total_projected.toFixed(2) + ' | ';
            html += 'Limit: ' + forecast.total_limit + '<br>';
            if (forecast.will_exceed) {
                html += '<span class="text-danger">⚠️ Expected to exceed limit by ' + (forecast.total_projected - forecast.total_limit).toFixed(2) + '</span>';
            }
            html += '</div>';

            // Detailed forecast table
            html += '<div class="table-responsive mt-3"><table class="table table-sm table-hover">';
            html += '<thead><tr><th>Subscription</th><th>Plan</th><th>Current</th><th>Daily Rate</th><th>Projected</th><th>Limit</th><th>Status</th></tr></thead><tbody>';

            forecast.subscriptions.forEach(function(sub) {
                html += '<tr>';
                html += '<td>#' + sub.subscription_id + '</td>';
                html += '<td>' + sub.plan_name + '</td>';
                html += '<td>' + sub.current_usage + '</td>';
                html += '<td>' + sub.daily_rate.toFixed(2) + '</td>';
                html += '<td>' + sub.projected_usage.toFixed(2) + '</td>';
                html += '<td>' + sub.limit + '</td>';
                html += '<td>';
                if (sub.will_exceed) {
                    html += '<span class="badge bg-danger">Will Exceed</span>';
                } else {
                    html += '<span class="badge bg-success">On Track</span>';
                }
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
        } else {
            html = '<p class="text-muted">No forecast data available</p>';
        }

        $('#forecast-content').html(html);
    }

    function updateRecentActivityTab(data) {
        let html = '';

        if (data.recent_usage && data.recent_usage.length > 0) {
            html += '<div class="list-group">';

            data.recent_usage.forEach(function(record) {
                html += '<div class="list-group-item">';
                html += '<div class="d-flex justify-content-between align-items-center">';
                html += '<div>';
                html += '<strong>' + record.feature + '</strong><br>';
                html += '<small class="text-muted">Subscription #' + record.subscription_id + '</small>';
                html += '</div>';
                html += '<div class="text-end">';
                html += '<span class="badge bg-primary">+' + record.quantity + ' ' + record.unit + '</span><br>';
                html += '<small>' + record.recorded_at + '</small>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });

            html += '</div>';
        } else {
            html = '<p class="text-muted">No recent activity</p>';
        }

        $('#recent-content').html(html);
    }

    function updateAllFeaturesTab(data) {
        let html = '';

        if (data.aggregated_by_feature) {
            html += '<div class="table-responsive"><table class="table table-sm table-hover">';
            html += '<thead><tr><th>Feature</th><th>Total Usage</th><th>Total Limit</th><th>Usage %</th><th>Status</th></tr></thead><tbody>';

            Object.values(data.aggregated_by_feature).forEach(function(feature) {
                let percentage = (feature.total_usage / feature.total_limit * 100) || 0;

                html += '<tr>';
                html += '<td><strong>' + feature.feature_name + '</strong></td>';
                html += '<td>' + feature.total_usage + '</td>';
                html += '<td>' + (feature.is_unlimited ? 'Unlimited' : feature.total_limit) + '</td>';
                html += '<td style="width: 200px;">';
                if (!feature.is_unlimited) {
                    html += '<div class="progress" style="height: 5px;">';
                    html += '<div class="progress-bar" style="width: ' + percentage + '%"></div>';
                    html += '</div><small>' + percentage.toFixed(1) + '%</small>';
                } else {
                    html += '<span class="badge bg-info">Unlimited</span>';
                }
                html += '</td>';
                html += '<td>';
                if (feature.is_unlimited) {
                    html += '<span class="badge bg-success">Unlimited</span>';
                } else if (percentage >= 90) {
                    html += '<span class="badge bg-danger">Critical</span>';
                } else if (percentage >= 75) {
                    html += '<span class="badge bg-warning">Warning</span>';
                } else {
                    html += '<span class="badge bg-success">Good</span>';
                }
                html += '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
        } else {
            html = '<p class="text-muted">No feature data available</p>';
        }

        $('#all-features-content').html(html);
    }

    function showError(elementId, message) {
        $('#' + elementId).html('<div class="alert alert-danger">' + message + '</div>');
    }
});
</script>
@endpush
@endsection
