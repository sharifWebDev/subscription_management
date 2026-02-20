<form method="POST" id="createusage_recordsForm" action="{{ url('api/v1/usage-records') }}" enctype="multipart/form-data">
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
        <label for="subscription_item_id">Subscription Item Id</label>
        <input type="text" name="subscription_item_id" class="form-control @error('subscription_item_id') is-invalid @enderror" value="{{ old('subscription_item_id') }}" placeholder="Enter Subscription Item Id..." id="create_subscription_item_id" required>
        @error('subscription_item_id')
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
        <label for="quantity">Quantity</label>
        <input type="number" step="any" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="Enter Quantity..." id="create_quantity" required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tier_quantity">Tier Quantity</label>
        <input type="number" step="any" name="tier_quantity" min="0" class="form-control @error('tier_quantity') is-invalid @enderror" value="{{ old('tier_quantity') }}" placeholder="Enter Tier Quantity..." id="create_tier_quantity" required>
        @error('tier_quantity')
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
        <label for="unit">Unit</label>
        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" placeholder="Enter Unit..." id="create_unit" required>
        @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="status">Status</label>
        <input type="text" name="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}" placeholder="Enter Status..." id="create_status" required>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="recorded_at">Recorded At</label>
        <input type="datetime-local" name="recorded_at" class="form-control @error('recorded_at') is-invalid @enderror" value="{{ old('recorded_at') }}" id="create_recorded_at" required>
        @error('recorded_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="billing_date">Billing Date</label>
        <input type="date" name="billing_date" class="form-control @error('billing_date') is-invalid @enderror" value="{{ old('billing_date') }}" id="create_billing_date" required>
        @error('billing_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="metadata">Metadata</label>
        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata') }}" placeholder="Enter Metadata..." id="create_metadata" required>
        @error('metadata')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="dimensions">Dimensions</label>
        <input type="text" name="dimensions" class="form-control @error('dimensions') is-invalid @enderror" value="{{ old('dimensions') }}" placeholder="Enter Dimensions..." id="create_dimensions" required>
        @error('dimensions')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.usage-records.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>