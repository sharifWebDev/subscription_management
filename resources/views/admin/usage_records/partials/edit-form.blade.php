
            <form method="POST" id="editusage_recordsForm" action="{{ url('api/v1/usage-records/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
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
                        <label for="subscription_item_id">Subscription Item Id</label><br>
                        <input type="text" name="subscription_item_id" class="form-control @error('subscription_item_id') is-invalid @enderror" value="{{ old('subscription_item_id', $query->subscription_item_id ?? "") }}" placeholder="Enter Subscription Item Id..." id="edit_subscription_item_id" required>
                        @error('subscription_item_id')
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
                        <label for="quantity">Quantity</label><br>
                        <input type="number" step="any" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $query->quantity ?? "") }}" placeholder="Enter Quantity..." id="edit_quantity" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="tier_quantity">Tier Quantity</label><br>
                        <input type="number" step="any" name="tier_quantity" min="0" class="form-control @error('tier_quantity') is-invalid @enderror" value="{{ old('tier_quantity', $query->tier_quantity ?? "") }}" placeholder="Enter Tier Quantity..." id="edit_tier_quantity" required>
                        @error('tier_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="amount">Amount</label><br>
                        <input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $query->amount ?? "") }}" placeholder="Enter Amount..." id="edit_amount" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="unit">Unit</label><br>
                        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $query->unit ?? "") }}" placeholder="Enter Unit..." id="edit_unit" required>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="status">Status</label><br>
                        
                        <input type="radio" name="status" value="1" {{ old('status', $query->status ?? "") == 1 ? "checked" : "" }} id="edit_status_yes" checked>
                        <label for="edit_status_yes">Status Yes</label>
                        <input type="radio" name="status" value="0" {{ old('status', $query->status ?? "") == 0 ? "checked" : "" }} id="edit_status_no">
                        <label for="edit_status_no">Status No</label>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="recorded_at">Recorded At</label><br>
                        <input type="datetime-local" name="recorded_at" class="form-control @error('recorded_at') is-invalid @enderror" value="{{ old('recorded_at', $query->recorded_at ?? "") }}" id="edit_recorded_at" required>
                        @error('recorded_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="billing_date">Billing Date</label><br>
                        <input type="date" name="billing_date" class="form-control @error('billing_date') is-invalid @enderror" value="{{ old('billing_date', $query->billing_date ?? "") }}" id="edit_billing_date" required>
                        @error('billing_date')
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
                        <label for="dimensions">Dimensions</label><br>
                        <input type="text" name="dimensions" class="form-control @error('dimensions') is-invalid @enderror" value="{{ old('dimensions', $query->dimensions ?? "") }}" placeholder="Enter Dimensions..." id="edit_dimensions" required>
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