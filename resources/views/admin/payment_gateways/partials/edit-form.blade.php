
            <form method="POST" id="editpayment_gatewaysForm" action="{{ url('api/v1/payment-gateways/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
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
                        <label for="type">Type</label><br>
                        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="edit_type" required>
                        @error('type')
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

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_test_mode">Is Test Mode</label><br>
                        
                                <input type="radio" name="is_test_mode" id="edit_is_test_mode_yes" value="1" {{ old('is_test_mode', $query->is_test_mode ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_test_mode_yes">Is Test Mode Yes</label>
                                <input type="radio" name="is_test_mode" id="edit_is_test_mode_no" value="0" {{ old('is_test_mode', $query->is_test_mode ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_test_mode_no">Is Test Mode No</label>
                        @error('is_test_mode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="supports_recurring">Supports Recurring</label><br>
                        
                                <input type="radio" name="supports_recurring" id="edit_supports_recurring_yes" value="1" {{ old('supports_recurring', $query->supports_recurring ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editsupports_recurring_yes">Supports Recurring Yes</label>
                                <input type="radio" name="supports_recurring" id="edit_supports_recurring_no" value="0" {{ old('supports_recurring', $query->supports_recurring ?? "") == 0 ? "checked" : "" }}>
                                <label for="editsupports_recurring_no">Supports Recurring No</label>
                        @error('supports_recurring')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="supports_refunds">Supports Refunds</label><br>
                        
                                <input type="radio" name="supports_refunds" id="edit_supports_refunds_yes" value="1" {{ old('supports_refunds', $query->supports_refunds ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editsupports_refunds_yes">Supports Refunds Yes</label>
                                <input type="radio" name="supports_refunds" id="edit_supports_refunds_no" value="0" {{ old('supports_refunds', $query->supports_refunds ?? "") == 0 ? "checked" : "" }}>
                                <label for="editsupports_refunds_no">Supports Refunds No</label>
                        @error('supports_refunds')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="supports_installments">Supports Installments</label><br>
                        
                                <input type="radio" name="supports_installments" id="edit_supports_installments_yes" value="1" {{ old('supports_installments', $query->supports_installments ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editsupports_installments_yes">Supports Installments Yes</label>
                                <input type="radio" name="supports_installments" id="edit_supports_installments_no" value="0" {{ old('supports_installments', $query->supports_installments ?? "") == 0 ? "checked" : "" }}>
                                <label for="editsupports_installments_no">Supports Installments No</label>
                        @error('supports_installments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="api_key">Api Key</label><br>
                        <textarea name="api_key" class="form-control @error('api_key') is-invalid @enderror" placeholder="Enter Api Key..." id="edit_api_key" required>{{ old('api_key', $query->api_key ?? "") }}</textarea>
                        @error('api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="api_secret">Api Secret</label><br>
                        <textarea name="api_secret" class="form-control @error('api_secret') is-invalid @enderror" placeholder="Enter Api Secret..." id="edit_api_secret" required>{{ old('api_secret', $query->api_secret ?? "") }}</textarea>
                        @error('api_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="webhook_secret">Webhook Secret</label><br>
                        <textarea name="webhook_secret" class="form-control @error('webhook_secret') is-invalid @enderror" placeholder="Enter Webhook Secret..." id="edit_webhook_secret" required>{{ old('webhook_secret', $query->webhook_secret ?? "") }}</textarea>
                        @error('webhook_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="merchant_id">Merchant Id</label><br>
                        <textarea name="merchant_id" class="form-control @error('merchant_id') is-invalid @enderror" placeholder="Enter Merchant Id..." id="edit_merchant_id" required>{{ old('merchant_id', $query->merchant_id ?? "") }}</textarea>
                        @error('merchant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="store_id">Store Id</label><br>
                        <textarea name="store_id" class="form-control @error('store_id') is-invalid @enderror" placeholder="Enter Store Id..." id="edit_store_id" required>{{ old('store_id', $query->store_id ?? "") }}</textarea>
                        @error('store_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="store_password">Store Password</label><br>
                        <textarea name="store_password" class="form-control @error('store_password') is-invalid @enderror" placeholder="Enter Store Password..." id="edit_store_password" required>{{ old('store_password', $query->store_password ?? "") }}</textarea>
                        @error('store_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="base_url">Base Url</label><br>
                        <input type="text" name="base_url" class="form-control @error('base_url') is-invalid @enderror" value="{{ old('base_url', $query->base_url ?? "") }}" placeholder="Enter Base Url..." id="edit_base_url" required>
                        @error('base_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="callback_url">Callback Url</label><br>
                        <input type="text" name="callback_url" class="form-control @error('callback_url') is-invalid @enderror" value="{{ old('callback_url', $query->callback_url ?? "") }}" placeholder="Enter Callback Url..." id="edit_callback_url" required>
                        @error('callback_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="webhook_url">Webhook Url</label><br>
                        <input type="text" name="webhook_url" class="form-control @error('webhook_url') is-invalid @enderror" value="{{ old('webhook_url', $query->webhook_url ?? "") }}" placeholder="Enter Webhook Url..." id="edit_webhook_url" required>
                        @error('webhook_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="supported_currencies">Supported Currencies</label><br>
                        <input type="text" name="supported_currencies" class="form-control @error('supported_currencies') is-invalid @enderror" value="{{ old('supported_currencies', $query->supported_currencies ?? "") }}" placeholder="Enter Supported Currencies..." id="edit_supported_currencies" required>
                        @error('supported_currencies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="supported_countries">Supported Countries</label><br>
                        <input type="text" name="supported_countries" class="form-control @error('supported_countries') is-invalid @enderror" value="{{ old('supported_countries', $query->supported_countries ?? "") }}" placeholder="Enter Supported Countries..." id="edit_supported_countries" required>
                        @error('supported_countries')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="excluded_countries">Excluded Countries</label><br>
                        <input type="text" name="excluded_countries" class="form-control @error('excluded_countries') is-invalid @enderror" value="{{ old('excluded_countries', $query->excluded_countries ?? "") }}" placeholder="Enter Excluded Countries..." id="edit_excluded_countries" required>
                        @error('excluded_countries')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="percentage_fee">Percentage Fee</label><br>
                        <input type="number" step="any" name="percentage_fee" min="0" class="form-control @error('percentage_fee') is-invalid @enderror" value="{{ old('percentage_fee', $query->percentage_fee ?? "") }}" placeholder="Enter Percentage Fee..." id="edit_percentage_fee" required>
                        @error('percentage_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="fixed_fee">Fixed Fee</label><br>
                        <input type="number" step="any" name="fixed_fee" min="0" class="form-control @error('fixed_fee') is-invalid @enderror" value="{{ old('fixed_fee', $query->fixed_fee ?? "") }}" placeholder="Enter Fixed Fee..." id="edit_fixed_fee" required>
                        @error('fixed_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="fee_currency">Fee Currency</label><br>
                        <input type="text" name="fee_currency" class="form-control @error('fee_currency') is-invalid @enderror" value="{{ old('fee_currency', $query->fee_currency ?? "") }}" placeholder="Enter Fee Currency..." id="edit_fee_currency" required>
                        @error('fee_currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="fee_structure">Fee Structure</label><br>
                        <input type="text" name="fee_structure" class="form-control @error('fee_structure') is-invalid @enderror" value="{{ old('fee_structure', $query->fee_structure ?? "") }}" placeholder="Enter Fee Structure..." id="edit_fee_structure" required>
                        @error('fee_structure')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="config">Config</label><br>
                        <input type="text" name="config" class="form-control @error('config') is-invalid @enderror" value="{{ old('config', $query->config ?? "") }}" placeholder="Enter Config..." id="edit_config" required>
                        @error('config')
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
                        <label for="settlement_days">Settlement Days</label><br>
                        <input type="number" name="settlement_days" min="0" class="form-control @error('settlement_days') is-invalid @enderror" value="{{ old('settlement_days', $query->settlement_days ?? "") }}" placeholder="Enter Settlement Days..." id="edit_settlement_days" required>
                        @error('settlement_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="refund_days">Refund Days</label><br>
                        <input type="number" name="refund_days" min="0" class="form-control @error('refund_days') is-invalid @enderror" value="{{ old('refund_days', $query->refund_days ?? "") }}" placeholder="Enter Refund Days..." id="edit_refund_days" required>
                        @error('refund_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="min_amount">Min Amount</label><br>
                        <input type="number" step="any" name="min_amount" min="0" class="form-control @error('min_amount') is-invalid @enderror" value="{{ old('min_amount', $query->min_amount ?? "") }}" placeholder="Enter Min Amount..." id="edit_min_amount" required>
                        @error('min_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="max_amount">Max Amount</label><br>
                        <input type="number" step="any" name="max_amount" min="0" class="form-control @error('max_amount') is-invalid @enderror" value="{{ old('max_amount', $query->max_amount ?? "") }}" placeholder="Enter Max Amount..." id="edit_max_amount" required>
                        @error('max_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-gateways.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>