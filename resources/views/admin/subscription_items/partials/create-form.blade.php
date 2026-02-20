<form method="POST" id="createsubscription_itemsForm" action="{{ url('api/v1/subscription-items') }}" enctype="multipart/form-data">
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
        <label for="plan_price_id">Plan Price Id</label>
        <input type="text" name="plan_price_id" class="form-control @error('plan_price_id') is-invalid @enderror" value="{{ old('plan_price_id') }}" placeholder="Enter Plan Price Id..." id="create_plan_price_id" required>
        @error('plan_price_id')
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
        <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="Enter Quantity..." id="create_quantity" required>
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="unit_price">Unit Price</label>
        <input type="number" step="any" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}" placeholder="Enter Unit Price..." id="create_unit_price" required>
        @error('unit_price')
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
        <label for="metadata">Metadata</label>
        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata') }}" placeholder="Enter Metadata..." id="create_metadata" required>
        @error('metadata')
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
        <label for="effective_from">Effective From</label>
        <input type="datetime-local" name="effective_from" class="form-control @error('effective_from') is-invalid @enderror" value="{{ old('effective_from') }}" id="create_effective_from" required>
        @error('effective_from')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="effective_to">Effective To</label>
        <input type="datetime-local" name="effective_to" class="form-control @error('effective_to') is-invalid @enderror" value="{{ old('effective_to') }}" id="create_effective_to" required>
        @error('effective_to')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscription-items.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>