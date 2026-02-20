<form method="POST" id="createplan_pricesForm" action="{{ url('api/v1/plan-prices') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="plan_id">Plan Id</label>
        <input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id') }}" placeholder="Enter Plan Id..." id="create_plan_id" required>
        @error('plan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="currency">Currency</label>
        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency') }}" placeholder="Enter Currency..." id="create_currency" required>
        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Enter Amount..." id="create_amount" required>
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="interval">Interval</label>
        <input type="text" name="interval" class="form-control @error('interval') is-invalid @enderror" value="{{ old('interval') }}" placeholder="Enter Interval..." id="create_interval" required>
        @error('interval')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="interval_count">Interval Count</label>
        <input type="number" name="interval_count" min="0" class="form-control @error('interval_count') is-invalid @enderror" value="{{ old('interval_count') }}" placeholder="Enter Interval Count..." id="create_interval_count" required>
        @error('interval_count')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="usage_type">Usage Type</label>
        <input type="text" name="usage_type" class="form-control @error('usage_type') is-invalid @enderror" value="{{ old('usage_type') }}" placeholder="Enter Usage Type..." id="create_usage_type" required>
        @error('usage_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tiers">Tiers</label>
        <input type="text" name="tiers" class="form-control @error('tiers') is-invalid @enderror" value="{{ old('tiers') }}" placeholder="Enter Tiers..." id="create_tiers" required>
        @error('tiers')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="transformations">Transformations</label>
        <input type="text" name="transformations" class="form-control @error('transformations') is-invalid @enderror" value="{{ old('transformations') }}" placeholder="Enter Transformations..." id="create_transformations" required>
        @error('transformations')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="stripe_price_id">Stripe Price Id</label>
        <input type="text" name="stripe_price_id" class="form-control @error('stripe_price_id') is-invalid @enderror" value="{{ old('stripe_price_id') }}" placeholder="Enter Stripe Price Id..." id="create_stripe_price_id" required>
        @error('stripe_price_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="active_from">Active From</label>
        <input type="datetime-local" name="active_from" class="form-control @error('active_from') is-invalid @enderror" value="{{ old('active_from') }}" id="create_active_from" required>
        @error('active_from')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="active_to">Active To</label>
        <input type="datetime-local" name="active_to" class="form-control @error('active_to') is-invalid @enderror" value="{{ old('active_to') }}" id="create_active_to" required>
        @error('active_to')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.plan-prices.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>