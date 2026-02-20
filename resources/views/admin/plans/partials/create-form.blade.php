<form method="POST" id="createplansForm" action="{{ url('api/v1/plans') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
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
        <label for="code">Code</label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Enter Code..." id="create_code" required>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="create_description" required>{{ old('description') }}</textarea>
        @error('description')
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
        <label for="billing_period">Billing Period</label>
        <input type="text" name="billing_period" class="form-control @error('billing_period') is-invalid @enderror" value="{{ old('billing_period') }}" placeholder="Enter Billing Period..." id="create_billing_period" required>
        @error('billing_period')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="billing_interval">Billing Interval</label>
        <input type="number" name="billing_interval" min="0" class="form-control @error('billing_interval') is-invalid @enderror" value="{{ old('billing_interval') }}" placeholder="Enter Billing Interval..." id="create_billing_interval" required>
        @error('billing_interval')
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
        <label for="is_visible">Is Visible</label>
        <input type="radio" name="is_visible" id="create_is_visible_yes" value="1" {{ old('is_visible') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_visible_yes">Is Visible Yes</label>
<input type="radio" name="is_visible" id="create_is_visible_no" value="0" {{ old('is_visible') == 0 ? "checked" : "" }}> 
                            <label for="create_is_visible_no">Is Visible No</label>
        @error('is_visible')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="sort_order">Sort Order</label>
        <input type="number" name="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order') }}" placeholder="Enter Sort Order..." id="create_sort_order" required>
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="is_featured">Is Featured</label>
        <input type="radio" name="is_featured" id="create_is_featured_yes" value="1" {{ old('is_featured') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_featured_yes">Is Featured Yes</label>
<input type="radio" name="is_featured" id="create_is_featured_no" value="0" {{ old('is_featured') == 0 ? "checked" : "" }}> 
                            <label for="create_is_featured_no">Is Featured No</label>
        @error('is_featured')
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
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.plans.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>