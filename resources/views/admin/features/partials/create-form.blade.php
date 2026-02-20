<form method="POST" id="createfeaturesForm" action="{{ url('api/v1/features') }}" enctype="multipart/form-data">
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
        <label for="scope">Scope</label>
        <input type="text" name="scope" class="form-control @error('scope') is-invalid @enderror" value="{{ old('scope') }}" placeholder="Enter Scope..." id="create_scope" required>
        @error('scope')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="is_resettable">Is Resettable</label>
        <input type="radio" name="is_resettable" id="create_is_resettable_yes" value="1" {{ old('is_resettable') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_resettable_yes">Is Resettable Yes</label>
<input type="radio" name="is_resettable" id="create_is_resettable_no" value="0" {{ old('is_resettable') == 0 ? "checked" : "" }}> 
                            <label for="create_is_resettable_no">Is Resettable No</label>
        @error('is_resettable')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="reset_period">Reset Period</label>
        <input type="text" name="reset_period" class="form-control @error('reset_period') is-invalid @enderror" value="{{ old('reset_period') }}" placeholder="Enter Reset Period..." id="create_reset_period" required>
        @error('reset_period')
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
        <label for="validations">Validations</label>
        <input type="text" name="validations" class="form-control @error('validations') is-invalid @enderror" value="{{ old('validations') }}" placeholder="Enter Validations..." id="create_validations" required>
        @error('validations')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.features.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>