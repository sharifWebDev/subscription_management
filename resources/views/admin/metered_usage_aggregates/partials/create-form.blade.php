<form method="POST" id="createmetered_usage_aggregatesForm" action="{{ url('api/v1/metered-usage-aggregates') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="subscription_id">Subscription Id</label>
        <input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id') }}" placeholder="Enter Subscription Id..." id="create_subscription_id" required>
        @error('subscription_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="feature_id">Feature Id</label>
        <input type="text" name="feature_id" class="form-control @error('feature_id') is-invalid @enderror" value="{{ old('feature_id') }}" placeholder="Enter Feature Id..." id="create_feature_id" required>
        @error('feature_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="aggregate_date">Aggregate Date</label>
        <input type="date" name="aggregate_date" class="form-control @error('aggregate_date') is-invalid @enderror" value="{{ old('aggregate_date') }}" id="create_aggregate_date" required>
        @error('aggregate_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="aggregate_period">Aggregate Period</label>
        <input type="text" name="aggregate_period" class="form-control @error('aggregate_period') is-invalid @enderror" value="{{ old('aggregate_period') }}" placeholder="Enter Aggregate Period..." id="create_aggregate_period" required>
        @error('aggregate_period')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="total_quantity">Total Quantity</label>
        <input type="number" step="any" name="total_quantity" min="0" class="form-control @error('total_quantity') is-invalid @enderror" value="{{ old('total_quantity') }}" placeholder="Enter Total Quantity..." id="create_total_quantity" required>
        @error('total_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tier1_quantity">Tier1 Quantity</label>
        <input type="number" step="any" name="tier1_quantity" min="0" class="form-control @error('tier1_quantity') is-invalid @enderror" value="{{ old('tier1_quantity') }}" placeholder="Enter Tier1 Quantity..." id="create_tier1_quantity" required>
        @error('tier1_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tier2_quantity">Tier2 Quantity</label>
        <input type="number" step="any" name="tier2_quantity" min="0" class="form-control @error('tier2_quantity') is-invalid @enderror" value="{{ old('tier2_quantity') }}" placeholder="Enter Tier2 Quantity..." id="create_tier2_quantity" required>
        @error('tier2_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tier3_quantity">Tier3 Quantity</label>
        <input type="number" step="any" name="tier3_quantity" min="0" class="form-control @error('tier3_quantity') is-invalid @enderror" value="{{ old('tier3_quantity') }}" placeholder="Enter Tier3 Quantity..." id="create_tier3_quantity" required>
        @error('tier3_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="total_amount">Total Amount</label>
        <input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount') }}" placeholder="Enter Total Amount..." id="create_total_amount" required>
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="record_count">Record Count</label>
        <input type="number" name="record_count" min="0" class="form-control @error('record_count') is-invalid @enderror" value="{{ old('record_count') }}" placeholder="Enter Record Count..." id="create_record_count" required>
        @error('record_count')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="last_calculated_at">Last Calculated At</label>
        <input type="datetime-local" name="last_calculated_at" class="form-control @error('last_calculated_at') is-invalid @enderror" value="{{ old('last_calculated_at') }}" id="create_last_calculated_at" required>
        @error('last_calculated_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.metered-usage-aggregates.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>