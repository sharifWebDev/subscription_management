<form method="POST" id="createhk_prod_uomsForm" action="{{ url('api/v1/hk-prod-uoms') }}" enctype="multipart/form-data">
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
        <label for="sequence">Sequence</label>
        <input type="number" name="sequence" min="0" class="form-control @error('sequence') is-invalid @enderror" value="{{ old('sequence') }}" placeholder="Enter Sequence..." id="create_sequence" required>
        @error('sequence')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.hk-prod-uoms.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>