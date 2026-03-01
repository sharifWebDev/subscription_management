{{-- resources/views/crud-generator/create.blade.php --}}
@extends('website.layouts.app')

@section('content')
<div class="col-md-8">
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>CRUD Generator</h3>
                </div>
                <div class="card-body">

                    @if(isset($usageSummary))
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>CRUD Generation Usage</strong>
                                <p class="mb-0">
                                    Used: {{ $usageSummary['current_usage'] }} /
                                    @if($usageSummary['is_unlimited'])
                                        Unlimited
                                    @else
                                        {{ $usageSummary['limit'] }}
                                    @endif
                                </p>
                            </div>
                            @if(!$usageSummary['is_unlimited'])
                            <div class="text-end">
                                <div class="progress" style="width: 150px; height: 10px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $usageSummary['percentage'] }}%"
                                         aria-valuenow="{{ $usageSummary['percentage'] }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $usageSummary['remaining'] }} remaining</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('crud.generator.generate') }}" id="crudForm">
                        @csrf

                        <div class="mb-3">
                            <label for="table_name" class="form-label">Table Name</label>
                            <input type="text" class="form-control" id="table_name" name="table_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="model_name" class="form-label">Model Name</label>
                            <input type="text" class="form-control" id="model_name" name="model_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="fields" class="form-label">Fields (JSON)</label>
                            <textarea class="form-control" id="fields" name="fields" rows="5"
                                      placeholder='[{"name":"title","type":"string"},{"name":"content","type":"text"}]' required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" id="generateBtn">
                            Generate CRUD
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div id="usage-stats">
                        <p class="text-muted">Loading...</p>
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
    // Load usage stats
    $.get('/usage-stats', function(response) {
        if (response.success) {
            let html = '';

            if (response.data.has_subscription) {
                html += '<p><strong>Plan:</strong> ' + response.data.plan_name + '</p>';

                response.data.summaries.forEach(function(summary) {
                    if (summary.feature_code === 'crud_generation') {
                        html += '<hr>';
                        html += '<p><strong>CRUD Generations:</strong></p>';
                        html += '<p>Used: ' + summary.current_usage;
                        if (!summary.is_unlimited) {
                            html += ' / ' + summary.limit + '</p>';
                            html += '<div class="progress mb-2"><div class="progress-bar" style="width: ' + summary.percentage + '%"></div></div>';
                            html += '<p>Remaining: ' + summary.remaining + '</p>';
                        } else {
                            html += ' (Unlimited)</p>';
                        }
                    }
                });

                html += '<hr>';
                html += '<a href="/usage-forecast" class="btn btn-sm btn-outline-info">View Forecast</a>';
            } else {
                html = '<p class="text-danger">' + response.data.message + '</p>';
            }

            $('#usage-stats').html(html);
        }
    });
});
</script>
@endpush
@endsection
