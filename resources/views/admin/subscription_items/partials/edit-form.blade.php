
            <form method="POST" id="editsubscription_itemsForm" action="{{ url('api/v1/subscription-items/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
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
                        <label for="plan_price_id">Plan Price Id</label><br>
                        <input type="text" name="plan_price_id" class="form-control @error('plan_price_id') is-invalid @enderror" value="{{ old('plan_price_id', $query->plan_price_id ?? "") }}" placeholder="Enter Plan Price Id..." id="edit_plan_price_id" required>
                        @error('plan_price_id')
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
                        <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $query->quantity ?? "") }}" placeholder="Enter Quantity..." id="edit_quantity" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="unit_price">Unit Price</label><br>
                        <input type="number" step="any" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $query->unit_price ?? "") }}" placeholder="Enter Unit Price..." id="edit_unit_price" required>
                        @error('unit_price')
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
                        <label for="metadata">Metadata</label><br>
                        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="edit_metadata" required>
                        @error('metadata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="tiers">Tiers</label><br>
                        <input type="text" name="tiers" class="form-control @error('tiers') is-invalid @enderror" value="{{ old('tiers', $query->tiers ?? "") }}" placeholder="Enter Tiers..." id="edit_tiers" required>
                        @error('tiers')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="effective_from">Effective From</label><br>
                        <input type="datetime-local" name="effective_from" class="form-control @error('effective_from') is-invalid @enderror" value="{{ old('effective_from', $query->effective_from ?? "") }}" id="edit_effective_from" required>
                        @error('effective_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="effective_to">Effective To</label><br>
                        <input type="datetime-local" name="effective_to" class="form-control @error('effective_to') is-invalid @enderror" value="{{ old('effective_to', $query->effective_to ?? "") }}" id="edit_effective_to" required>
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