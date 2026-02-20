<form method="POST" id="creatediscountsForm" action="{{ url('api/v1/discounts') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="code">Code</label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Enter Code..." id="create_code" required>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter Name..." id="create_name" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="type">Type</label>
        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}" placeholder="Enter Type..." id="create_type" required>
        @error('type')
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
        <label for="currency">Currency</label>
        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency') }}" placeholder="Enter Currency..." id="create_currency" required>
        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="applies_to">Applies To</label>
        <input type="text" name="applies_to" class="form-control @error('applies_to') is-invalid @enderror" value="{{ old('applies_to') }}" placeholder="Enter Applies To..." id="create_applies_to" required>
        @error('applies_to')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="applies_to_ids">Applies To Ids</label>
        <input type="text" name="applies_to_ids" class="form-control @error('applies_to_ids') is-invalid @enderror" value="{{ old('applies_to_ids') }}" placeholder="Enter Applies To Ids..." id="create_applies_to_ids" required>
        @error('applies_to_ids')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="max_redemptions">Max Redemptions</label>
        <input type="number" name="max_redemptions" min="0" class="form-control @error('max_redemptions') is-invalid @enderror" value="{{ old('max_redemptions') }}" placeholder="Enter Max Redemptions..." id="create_max_redemptions" required>
        @error('max_redemptions')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="times_redeemed">Times Redeemed</label>
        <input type="number" name="times_redeemed" min="0" class="form-control @error('times_redeemed') is-invalid @enderror" value="{{ old('times_redeemed') }}" placeholder="Enter Times Redeemed..." id="create_times_redeemed" required>
        @error('times_redeemed')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="is_active">Is Active</label>
        <input type="radio" name="is_active" id="create_is_active_yes" value="1" {{ old('is_active') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_active_yes">Is Active Yes</label>
<input type="radio" name="is_active" id="create_is_active_no" value="0" {{ old('is_active') == 0 ? "checked" : "" }}> 
                            <label for="create_is_active_no">Is Active No</label>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="starts_at">Starts At</label>
        <input type="datetime-local" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at') }}" id="create_starts_at" required>
        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="expires_at">Expires At</label>
        <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}" id="create_expires_at" required>
        @error('expires_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="duration">Duration</label>
        <input type="text" name="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" placeholder="Enter Duration..." id="create_duration" required>
        @error('duration')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="duration_in_months">Duration In Months</label>
        <input type="number" name="duration_in_months" min="0" class="form-control @error('duration_in_months') is-invalid @enderror" value="{{ old('duration_in_months') }}" placeholder="Enter Duration In Months..." id="create_duration_in_months" required>
        @error('duration_in_months')
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
        <label for="restrictions">Restrictions</label>
        <input type="text" name="restrictions" class="form-control @error('restrictions') is-invalid @enderror" value="{{ old('restrictions') }}" placeholder="Enter Restrictions..." id="create_restrictions" required>
        @error('restrictions')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.discounts.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>