
            <form method="POST" id="editfeaturesForm" action="{{ url('api/v1/features/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="name">Name</label><br>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $query->name ?? "") }}" placeholder="Enter Name..." id="edit_name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="code">Code</label><br>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $query->code ?? "") }}" placeholder="Enter Code..." id="edit_code" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="description">Description</label><br>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="edit_description" required>{{ old('description', $query->description ?? "") }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="type">Type</label><br>
                        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="edit_type" required>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="scope">Scope</label><br>
                        <input type="text" name="scope" class="form-control @error('scope') is-invalid @enderror" value="{{ old('scope', $query->scope ?? "") }}" placeholder="Enter Scope..." id="edit_scope" required>
                        @error('scope')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_resettable">Is Resettable</label><br>
                        
                                <input type="radio" name="is_resettable" id="edit_is_resettable_yes" value="1" {{ old('is_resettable', $query->is_resettable ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_resettable_yes">Is Resettable Yes</label>
                                <input type="radio" name="is_resettable" id="edit_is_resettable_no" value="0" {{ old('is_resettable', $query->is_resettable ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_resettable_no">Is Resettable No</label>
                        @error('is_resettable')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="reset_period">Reset Period</label><br>
                        <input type="text" name="reset_period" class="form-control @error('reset_period') is-invalid @enderror" value="{{ old('reset_period', $query->reset_period ?? "") }}" placeholder="Enter Reset Period..." id="edit_reset_period" required>
                        @error('reset_period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="metadata">Metadata</label><br>
                        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="edit_metadata" required>
                        @error('metadata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="validations">Validations</label><br>
                        <input type="text" name="validations" class="form-control @error('validations') is-invalid @enderror" value="{{ old('validations', $query->validations ?? "") }}" placeholder="Enter Validations..." id="edit_validations" required>
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