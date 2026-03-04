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

                            @if (isset($usageData))
                                @php
                                    $crudFeature = $usageData['aggregated_by_feature']['crud_generation'] ?? null;
                                @endphp

                                @if ($crudFeature)
                                    <!-- Aggregated Usage Summary -->
                                    <div class="alert alert-info mb-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Total CRUD Generation Usage (Across
                                                    {{ $usageData['total_subscriptions'] }}
                                                    Subscription{{ $usageData['total_subscriptions'] > 1 ? 's' : '' }})</strong>
                                                <p class="mb-0">
                                                    Used: {{ number_format($crudFeature['total_usage'] ?? 0) }} /
                                                    @if ($crudFeature['is_unlimited'] ?? false)
                                                        Unlimited
                                                    @else
                                                        {{ number_format($crudFeature['total_limit'] ?? 0) }}
                                                    @endif
                                                </p>
                                            </div>
                                            @if (!($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_limit'] ?? 0) > 0)
                                                @php $percentage = ($crudFeature['total_usage'] / $crudFeature['total_limit']) * 100; @endphp
                                                <div class="text-end">
                                                    <div class="progress" style="width: 200px; height: 10px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ min(100, $percentage) }}%"
                                                            aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ number_format($crudFeature['total_limit'] - $crudFeature['total_usage']) }}
                                                        remaining across all subscriptions
                                                    </small>
                                                </div>
                                            @elseif($crudFeature['is_unlimited'] ?? false)
                                                <span class="badge bg-success">Unlimited Usage</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Warning if near limit -->
                                    @if (!($crudFeature['is_unlimited'] ?? false) && ($crudFeature['total_usage'] / $crudFeature['total_limit']) * 100 >= 80)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Warning:</strong> You've used
                                            {{ number_format(($crudFeature['total_usage'] / $crudFeature['total_limit']) * 100, 1) }}%
                                            of your total CRUD generation limit.
                                            @if ($crudFeature['total_limit'] - $crudFeature['total_usage'] < 10)
                                                Only
                                                {{ number_format($crudFeature['total_limit'] - $crudFeature['total_usage']) }}
                                                generations remaining.
                                            @endif
                                        </div>
                                    @endif

                                    <!-- No remaining usage warning -->
                                    @if (!($crudFeature['is_unlimited'] ?? false) && $crudFeature['total_limit'] - $crudFeature['total_usage'] <= 0)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-times-circle"></i>
                                            <strong>Limit Reached:</strong> You have no remaining CRUD generations across
                                            all your subscriptions.
                                            <a href="{{ route('website.plans.index') }}" class="alert-link">Upgrade your
                                                plan</a> or wait for the next billing cycle.
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
                                    <textarea class="form-control @error('fields') is-invalid @enderror" id="fields" name="fields" rows="5"
                                        required>{{ old('fields', '[{"name":"title","type":"string"},{"name":"content","type":"text"}]') }}</textarea>
                                    @error('fields')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Enter fields in JSON format. Each field needs a name and
                                        type.</small>
                                </div>

                                <button type="submit" class="btn btn-primary" id="generateBtn"
                                    @if (isset($crudFeature) &&
                                            !($crudFeature['is_unlimited'] ?? false) &&
                                            $crudFeature['total_limit'] - $crudFeature['total_usage'] <= 0) disabled @endif>
                                    <i class="fas fa-cogs"></i> Generate CRUD
                                </button>

                                @if (isset($crudFeature) &&
                                        !($crudFeature['is_unlimited'] ?? false) &&
                                        $crudFeature['total_limit'] - $crudFeature['total_usage'] <= 0)
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
                            }
                        },
                        error: function(xhr) {
                            console.error('Failed to load usage stats:', xhr);
                            showError('subscriptions-content', 'Failed to load subscription data');
                        }
                    });
                }


                function showError(elementId, message) {
                    $('#' + elementId).html('<div class="alert alert-danger">' + message + '</div>');
                }
            });
        </script>
    @endpush
@endsection
