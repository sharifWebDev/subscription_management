
            <form method="POST" id="editrate_limitsForm" action="{{ url('api/v1/rate-limits/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="subscription_id">Subscription Id</label><br>
                        <input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="edit_subscription_id" required>
                        @error('subscription_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="feature_id">Feature Id</label><br>
                        <input type="text" name="feature_id" class="form-control @error('feature_id') is-invalid @enderror" value="{{ old('feature_id', $query->feature_id ?? "") }}" placeholder="Enter Feature Id..." id="edit_feature_id" required>
                        @error('feature_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="key">Key</label><br>
                        <input type="text" name="key" class="form-control @error('key') is-invalid @enderror" value="{{ old('key', $query->key ?? "") }}" placeholder="Enter Key..." id="edit_key" required>
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="max_attempts">Max Attempts</label><br>
                        <input type="number" name="max_attempts" min="0" class="form-control @error('max_attempts') is-invalid @enderror" value="{{ old('max_attempts', $query->max_attempts ?? "") }}" placeholder="Enter Max Attempts..." id="edit_max_attempts" required>
                        @error('max_attempts')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="decay_seconds">Decay Seconds</label><br>
                        <input type="number" name="decay_seconds" min="0" class="form-control @error('decay_seconds') is-invalid @enderror" value="{{ old('decay_seconds', $query->decay_seconds ?? "") }}" placeholder="Enter Decay Seconds..." id="edit_decay_seconds" required>
                        @error('decay_seconds')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="remaining">Remaining</label><br>
                        <input type="number" name="remaining" min="0" class="form-control @error('remaining') is-invalid @enderror" value="{{ old('remaining', $query->remaining ?? "") }}" placeholder="Enter Remaining..." id="edit_remaining" required>
                        @error('remaining')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="resets_at">Resets At</label><br>
                        <input type="datetime-local" name="resets_at" class="form-control @error('resets_at') is-invalid @enderror" value="{{ old('resets_at', $query->resets_at ?? "") }}" id="edit_resets_at" required>
                        @error('resets_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.rate-limits.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>