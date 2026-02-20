
            <form method="POST" id="editsubscriptionsForm" action="{{ url('api/v1/subscriptions/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="user_id">User Id</label><br>
                        <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="edit_user_id" required>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="plan_id">Plan Id</label><br>
                        <input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id', $query->plan_id ?? "") }}" placeholder="Enter Plan Id..." id="edit_plan_id" required>
                        @error('plan_id')
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
                        <label for="parent_subscription_id">Parent Subscription Id</label><br>
                        <input type="text" name="parent_subscription_id" class="form-control @error('parent_subscription_id') is-invalid @enderror" value="{{ old('parent_subscription_id', $query->parent_subscription_id ?? "") }}" placeholder="Enter Parent Subscription Id..." id="edit_parent_subscription_id" required>
                        @error('parent_subscription_id')
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
                        <label for="billing_cycle_anchor">Billing Cycle Anchor</label><br>
                        <input type="text" name="billing_cycle_anchor" class="form-control @error('billing_cycle_anchor') is-invalid @enderror" value="{{ old('billing_cycle_anchor', $query->billing_cycle_anchor ?? "") }}" placeholder="Enter Billing Cycle Anchor..." id="edit_billing_cycle_anchor" required>
                        @error('billing_cycle_anchor')
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
                        <label for="currency">Currency</label><br>
                        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="edit_currency" required>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="trial_starts_at">Trial Starts At</label><br>
                        <input type="datetime-local" name="trial_starts_at" class="form-control @error('trial_starts_at') is-invalid @enderror" value="{{ old('trial_starts_at', $query->trial_starts_at ?? "") }}" id="edit_trial_starts_at" required>
                        @error('trial_starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="trial_ends_at">Trial Ends At</label><br>
                        <input type="datetime-local" name="trial_ends_at" class="form-control @error('trial_ends_at') is-invalid @enderror" value="{{ old('trial_ends_at', $query->trial_ends_at ?? "") }}" id="edit_trial_ends_at" required>
                        @error('trial_ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="trial_converted">Trial Converted</label><br>
                        
                                <input type="radio" name="trial_converted" id="edit_trial_converted_yes" value="1" {{ old('trial_converted', $query->trial_converted ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="edittrial_converted_yes">Trial Converted Yes</label>
                                <input type="radio" name="trial_converted" id="edit_trial_converted_no" value="0" {{ old('trial_converted', $query->trial_converted ?? "") == 0 ? "checked" : "" }}>
                                <label for="edittrial_converted_no">Trial Converted No</label>
                        @error('trial_converted')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="current_period_starts_at">Current Period Starts At</label><br>
                        <input type="datetime-local" name="current_period_starts_at" class="form-control @error('current_period_starts_at') is-invalid @enderror" value="{{ old('current_period_starts_at', $query->current_period_starts_at ?? "") }}" id="edit_current_period_starts_at" required>
                        @error('current_period_starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="current_period_ends_at">Current Period Ends At</label><br>
                        <input type="datetime-local" name="current_period_ends_at" class="form-control @error('current_period_ends_at') is-invalid @enderror" value="{{ old('current_period_ends_at', $query->current_period_ends_at ?? "") }}" id="edit_current_period_ends_at" required>
                        @error('current_period_ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="billing_cycle_anchor_date">Billing Cycle Anchor Date</label><br>
                        <input type="datetime-local" name="billing_cycle_anchor_date" class="form-control @error('billing_cycle_anchor_date') is-invalid @enderror" value="{{ old('billing_cycle_anchor_date', $query->billing_cycle_anchor_date ?? "") }}" id="edit_billing_cycle_anchor_date" required>
                        @error('billing_cycle_anchor_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="canceled_at">Canceled At</label><br>
                        <input type="datetime-local" name="canceled_at" class="form-control @error('canceled_at') is-invalid @enderror" value="{{ old('canceled_at', $query->canceled_at ?? "") }}" id="edit_canceled_at" required>
                        @error('canceled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="cancellation_reason">Cancellation Reason</label><br>
                        <input type="text" name="cancellation_reason" class="form-control @error('cancellation_reason') is-invalid @enderror" value="{{ old('cancellation_reason', $query->cancellation_reason ?? "") }}" placeholder="Enter Cancellation Reason..." id="edit_cancellation_reason" required>
                        @error('cancellation_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="prorate">Prorate</label><br>
                        
                                <input type="radio" name="prorate" id="edit_prorate_yes" value="1" {{ old('prorate', $query->prorate ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editprorate_yes">Prorate Yes</label>
                                <input type="radio" name="prorate" id="edit_prorate_no" value="0" {{ old('prorate', $query->prorate ?? "") == 0 ? "checked" : "" }}>
                                <label for="editprorate_no">Prorate No</label>
                        @error('prorate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="proration_amount">Proration Amount</label><br>
                        <input type="number" step="any" name="proration_amount" min="0" class="form-control @error('proration_amount') is-invalid @enderror" value="{{ old('proration_amount', $query->proration_amount ?? "") }}" placeholder="Enter Proration Amount..." id="edit_proration_amount" required>
                        @error('proration_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="proration_date">Proration Date</label><br>
                        <input type="datetime-local" name="proration_date" class="form-control @error('proration_date') is-invalid @enderror" value="{{ old('proration_date', $query->proration_date ?? "") }}" id="edit_proration_date" required>
                        @error('proration_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway">Gateway</label><br>
                        <input type="text" name="gateway" class="form-control @error('gateway') is-invalid @enderror" value="{{ old('gateway', $query->gateway ?? "") }}" placeholder="Enter Gateway..." id="edit_gateway" required>
                        @error('gateway')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_subscription_id">Gateway Subscription Id</label><br>
                        <input type="text" name="gateway_subscription_id" class="form-control @error('gateway_subscription_id') is-invalid @enderror" value="{{ old('gateway_subscription_id', $query->gateway_subscription_id ?? "") }}" placeholder="Enter Gateway Subscription Id..." id="edit_gateway_subscription_id" required>
                        @error('gateway_subscription_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_customer_id">Gateway Customer Id</label><br>
                        <input type="text" name="gateway_customer_id" class="form-control @error('gateway_customer_id') is-invalid @enderror" value="{{ old('gateway_customer_id', $query->gateway_customer_id ?? "") }}" placeholder="Enter Gateway Customer Id..." id="edit_gateway_customer_id" required>
                        @error('gateway_customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_metadata">Gateway Metadata</label><br>
                        <input type="text" name="gateway_metadata" class="form-control @error('gateway_metadata') is-invalid @enderror" value="{{ old('gateway_metadata', $query->gateway_metadata ?? "") }}" placeholder="Enter Gateway Metadata..." id="edit_gateway_metadata" required>
                        @error('gateway_metadata')
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
                        <label for="history">History</label><br>
                        <input type="text" name="history" class="form-control @error('history') is-invalid @enderror" value="{{ old('history', $query->history ?? "") }}" placeholder="Enter History..." id="edit_history" required>
                        @error('history')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_active">Is Active</label><br>
                        
                                <input type="radio" name="is_active" id="edit_is_active_yes" value="1" {{ old('is_active', $query->is_active ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_active_yes">Is Active Yes</label>
                                <input type="radio" name="is_active" id="edit_is_active_no" value="0" {{ old('is_active', $query->is_active ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_active_no">Is Active No</label>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscriptions.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>