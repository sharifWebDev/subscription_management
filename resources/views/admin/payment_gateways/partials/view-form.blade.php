<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Name</label><br><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $query->name ?? "") }}" placeholder="Enter Name..." id="view_name" disabled>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="code">Code</label><br><input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $query->code ?? "") }}" placeholder="Enter Code..." id="view_code" disabled>@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_active">Is Active</label><br><input type="radio" name="is_active" id="view_is_active_yes" value="1" {{ old('is_active', $query->is_active ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_active_yes" disabled>Is Active Yes </label>
                                           <input type="radio" name="is_active" id="view_is_active_no" value="0" {{ old('is_active', $query->is_active ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_active_no" disabled>Is Active No </label>@error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_test_mode">Is Test Mode</label><br><input type="radio" name="is_test_mode" id="view_is_test_mode_yes" value="1" {{ old('is_test_mode', $query->is_test_mode ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_test_mode_yes" disabled>Is Test Mode Yes </label>
                                           <input type="radio" name="is_test_mode" id="view_is_test_mode_no" value="0" {{ old('is_test_mode', $query->is_test_mode ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_test_mode_no" disabled>Is Test Mode No </label>@error('is_test_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="supports_recurring">Supports Recurring</label><br><input type="radio" name="supports_recurring" id="view_supports_recurring_yes" value="1" {{ old('supports_recurring', $query->supports_recurring ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editsupports_recurring_yes" disabled>Supports Recurring Yes </label>
                                           <input type="radio" name="supports_recurring" id="view_supports_recurring_no" value="0" {{ old('supports_recurring', $query->supports_recurring ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editsupports_recurring_no" disabled>Supports Recurring No </label>@error('supports_recurring')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="supports_refunds">Supports Refunds</label><br><input type="radio" name="supports_refunds" id="view_supports_refunds_yes" value="1" {{ old('supports_refunds', $query->supports_refunds ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editsupports_refunds_yes" disabled>Supports Refunds Yes </label>
                                           <input type="radio" name="supports_refunds" id="view_supports_refunds_no" value="0" {{ old('supports_refunds', $query->supports_refunds ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editsupports_refunds_no" disabled>Supports Refunds No </label>@error('supports_refunds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="supports_installments">Supports Installments</label><br><input type="radio" name="supports_installments" id="view_supports_installments_yes" value="1" {{ old('supports_installments', $query->supports_installments ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editsupports_installments_yes" disabled>Supports Installments Yes </label>
                                           <input type="radio" name="supports_installments" id="view_supports_installments_no" value="0" {{ old('supports_installments', $query->supports_installments ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editsupports_installments_no" disabled>Supports Installments No </label>@error('supports_installments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="api_key">Api Key</label><br><textarea name="api_key" class="form-control @error('api_key') is-invalid @enderror" placeholder="Enter Api Key..." id="view_api_key" disabled>{{ old('api_key', $query->api_key ?? "") }}</textarea>@error('api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="api_secret">Api Secret</label><br><textarea name="api_secret" class="form-control @error('api_secret') is-invalid @enderror" placeholder="Enter Api Secret..." id="view_api_secret" disabled>{{ old('api_secret', $query->api_secret ?? "") }}</textarea>@error('api_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="webhook_secret">Webhook Secret</label><br><textarea name="webhook_secret" class="form-control @error('webhook_secret') is-invalid @enderror" placeholder="Enter Webhook Secret..." id="view_webhook_secret" disabled>{{ old('webhook_secret', $query->webhook_secret ?? "") }}</textarea>@error('webhook_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="merchant_id">Merchant Id</label><br><textarea name="merchant_id" class="form-control @error('merchant_id') is-invalid @enderror" placeholder="Enter Merchant Id..." id="view_merchant_id" disabled>{{ old('merchant_id', $query->merchant_id ?? "") }}</textarea>@error('merchant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="store_id">Store Id</label><br><textarea name="store_id" class="form-control @error('store_id') is-invalid @enderror" placeholder="Enter Store Id..." id="view_store_id" disabled>{{ old('store_id', $query->store_id ?? "") }}</textarea>@error('store_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="store_password">Store Password</label><br><textarea name="store_password" class="form-control @error('store_password') is-invalid @enderror" placeholder="Enter Store Password..." id="view_store_password" disabled>{{ old('store_password', $query->store_password ?? "") }}</textarea>@error('store_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="base_url">Base Url</label><br><input type="text" name="base_url" class="form-control @error('base_url') is-invalid @enderror" value="{{ old('base_url', $query->base_url ?? "") }}" placeholder="Enter Base Url..." id="view_base_url" disabled>@error('base_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="callback_url">Callback Url</label><br><input type="text" name="callback_url" class="form-control @error('callback_url') is-invalid @enderror" value="{{ old('callback_url', $query->callback_url ?? "") }}" placeholder="Enter Callback Url..." id="view_callback_url" disabled>@error('callback_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="webhook_url">Webhook Url</label><br><input type="text" name="webhook_url" class="form-control @error('webhook_url') is-invalid @enderror" value="{{ old('webhook_url', $query->webhook_url ?? "") }}" placeholder="Enter Webhook Url..." id="view_webhook_url" disabled>@error('webhook_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="supported_currencies">Supported Currencies</label><br><input type="text" name="supported_currencies" class="form-control @error('supported_currencies') is-invalid @enderror" value="{{ old('supported_currencies', $query->supported_currencies ?? "") }}" placeholder="Enter Supported Currencies..." id="view_supported_currencies" disabled>@error('supported_currencies')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="supported_countries">Supported Countries</label><br><input type="text" name="supported_countries" class="form-control @error('supported_countries') is-invalid @enderror" value="{{ old('supported_countries', $query->supported_countries ?? "") }}" placeholder="Enter Supported Countries..." id="view_supported_countries" disabled>@error('supported_countries')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="excluded_countries">Excluded Countries</label><br><input type="text" name="excluded_countries" class="form-control @error('excluded_countries') is-invalid @enderror" value="{{ old('excluded_countries', $query->excluded_countries ?? "") }}" placeholder="Enter Excluded Countries..." id="view_excluded_countries" disabled>@error('excluded_countries')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="percentage_fee">Percentage Fee</label><br><input type="number" step="any" name="percentage_fee" min="0" class="form-control @error('percentage_fee') is-invalid @enderror" value="{{ old('percentage_fee', $query->percentage_fee ?? "") }}" placeholder="Enter Percentage Fee..." id="view_percentage_fee" disabled>@error('percentage_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="fixed_fee">Fixed Fee</label><br><input type="number" step="any" name="fixed_fee" min="0" class="form-control @error('fixed_fee') is-invalid @enderror" value="{{ old('fixed_fee', $query->fixed_fee ?? "") }}" placeholder="Enter Fixed Fee..." id="view_fixed_fee" disabled>@error('fixed_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="fee_currency">Fee Currency</label><br><input type="text" name="fee_currency" class="form-control @error('fee_currency') is-invalid @enderror" value="{{ old('fee_currency', $query->fee_currency ?? "") }}" placeholder="Enter Fee Currency..." id="view_fee_currency" disabled>@error('fee_currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="fee_structure">Fee Structure</label><br><input type="text" name="fee_structure" class="form-control @error('fee_structure') is-invalid @enderror" value="{{ old('fee_structure', $query->fee_structure ?? "") }}" placeholder="Enter Fee Structure..." id="view_fee_structure" disabled>@error('fee_structure')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="config">Config</label><br><input type="text" name="config" class="form-control @error('config') is-invalid @enderror" value="{{ old('config', $query->config ?? "") }}" placeholder="Enter Config..." id="view_config" disabled>@error('config')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="settlement_days">Settlement Days</label><br><input type="number" name="settlement_days" min="0" class="form-control @error('settlement_days') is-invalid @enderror" value="{{ old('settlement_days', $query->settlement_days ?? "") }}" placeholder="Enter Settlement Days..." id="view_settlement_days" disabled>@error('settlement_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="refund_days">Refund Days</label><br><input type="number" name="refund_days" min="0" class="form-control @error('refund_days') is-invalid @enderror" value="{{ old('refund_days', $query->refund_days ?? "") }}" placeholder="Enter Refund Days..." id="view_refund_days" disabled>@error('refund_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="min_amount">Min Amount</label><br><input type="number" step="any" name="min_amount" min="0" class="form-control @error('min_amount') is-invalid @enderror" value="{{ old('min_amount', $query->min_amount ?? "") }}" placeholder="Enter Min Amount..." id="view_min_amount" disabled>@error('min_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="max_amount">Max Amount</label><br><input type="number" step="any" name="max_amount" min="0" class="form-control @error('max_amount') is-invalid @enderror" value="{{ old('max_amount', $query->max_amount ?? "") }}" placeholder="Enter Max Amount..." id="view_max_amount" disabled>@error('max_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-gateways.index') }}">Close</a>
                          </div>
                      </div>
                  </form>