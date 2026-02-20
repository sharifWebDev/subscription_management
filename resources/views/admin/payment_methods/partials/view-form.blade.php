<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_id">User Id</label><br><input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="view_user_id" disabled>@error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="gateway">Gateway</label><br><input type="text" name="gateway" class="form-control @error('gateway') is-invalid @enderror" value="{{ old('gateway', $query->gateway ?? "") }}" placeholder="Enter Gateway..." id="view_gateway" disabled>@error('gateway')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="gateway_customer_id">Gateway Customer Id</label><br><input type="text" name="gateway_customer_id" class="form-control @error('gateway_customer_id') is-invalid @enderror" value="{{ old('gateway_customer_id', $query->gateway_customer_id ?? "") }}" placeholder="Enter Gateway Customer Id..." id="view_gateway_customer_id" disabled>@error('gateway_customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="gateway_payment_method_id">Gateway Payment Method Id</label><br><input type="text" name="gateway_payment_method_id" class="form-control @error('gateway_payment_method_id') is-invalid @enderror" value="{{ old('gateway_payment_method_id', $query->gateway_payment_method_id ?? "") }}" placeholder="Enter Gateway Payment Method Id..." id="view_gateway_payment_method_id" disabled>@error('gateway_payment_method_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="nickname">Nickname</label><br><input type="text" name="nickname" class="form-control @error('nickname') is-invalid @enderror" value="{{ old('nickname', $query->nickname ?? "") }}" placeholder="Enter Nickname..." id="view_nickname" disabled>@error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_default">Is Default</label><br><input type="radio" name="is_default" id="view_is_default_yes" value="1" {{ old('is_default', $query->is_default ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_default_yes" disabled>Is Default Yes </label>
                                           <input type="radio" name="is_default" id="view_is_default_no" value="0" {{ old('is_default', $query->is_default ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_default_no" disabled>Is Default No </label>@error('is_default')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_verified">Is Verified</label><br><input type="radio" name="is_verified" id="view_is_verified_yes" value="1" {{ old('is_verified', $query->is_verified ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_verified_yes" disabled>Is Verified Yes </label>
                                           <input type="radio" name="is_verified" id="view_is_verified_no" value="0" {{ old('is_verified', $query->is_verified ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_verified_no" disabled>Is Verified No </label>@error('is_verified')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="card_last4">Card Last4</label><br><input type="text" name="card_last4" class="form-control @error('card_last4') is-invalid @enderror" value="{{ old('card_last4', $query->card_last4 ?? "") }}" placeholder="Enter Card Last4..." id="view_card_last4" disabled>@error('card_last4')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="card_brand">Card Brand</label><br><input type="text" name="card_brand" class="form-control @error('card_brand') is-invalid @enderror" value="{{ old('card_brand', $query->card_brand ?? "") }}" placeholder="Enter Card Brand..." id="view_card_brand" disabled>@error('card_brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="card_exp_month">Card Exp Month</label><br><input type="number" name="card_exp_month" min="0" class="form-control @error('card_exp_month') is-invalid @enderror" value="{{ old('card_exp_month', $query->card_exp_month ?? "") }}" placeholder="Enter Card Exp Month..." id="view_card_exp_month" disabled>@error('card_exp_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="card_exp_year">Card Exp Year</label><br><input type="number" name="card_exp_year" min="0" class="form-control @error('card_exp_year') is-invalid @enderror" value="{{ old('card_exp_year', $query->card_exp_year ?? "") }}" placeholder="Enter Card Exp Year..." id="view_card_exp_year" disabled>@error('card_exp_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="card_country">Card Country</label><br><input type="text" name="card_country" class="form-control @error('card_country') is-invalid @enderror" value="{{ old('card_country', $query->card_country ?? "") }}" placeholder="Enter Card Country..." id="view_card_country" disabled>@error('card_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label><br><input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $query->bank_name ?? "") }}" placeholder="Enter Bank Name..." id="view_bank_name" disabled>@error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="bank_account_last4">Bank Account Last4</label><br><input type="text" name="bank_account_last4" class="form-control @error('bank_account_last4') is-invalid @enderror" value="{{ old('bank_account_last4', $query->bank_account_last4 ?? "") }}" placeholder="Enter Bank Account Last4..." id="view_bank_account_last4" disabled>@error('bank_account_last4')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="bank_account_type">Bank Account Type</label><br><input type="text" name="bank_account_type" class="form-control @error('bank_account_type') is-invalid @enderror" value="{{ old('bank_account_type', $query->bank_account_type ?? "") }}" placeholder="Enter Bank Account Type..." id="view_bank_account_type" disabled>@error('bank_account_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="bank_routing_number">Bank Routing Number</label><br><input type="text" name="bank_routing_number" class="form-control @error('bank_routing_number') is-invalid @enderror" value="{{ old('bank_routing_number', $query->bank_routing_number ?? "") }}" placeholder="Enter Bank Routing Number..." id="view_bank_routing_number" disabled>@error('bank_routing_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="wallet_type">Wallet Type</label><br><input type="text" name="wallet_type" class="form-control @error('wallet_type') is-invalid @enderror" value="{{ old('wallet_type', $query->wallet_type ?? "") }}" placeholder="Enter Wallet Type..." id="view_wallet_type" disabled>@error('wallet_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="wallet_number">Wallet Number</label><br><input type="text" name="wallet_number" class="form-control @error('wallet_number') is-invalid @enderror" value="{{ old('wallet_number', $query->wallet_number ?? "") }}" placeholder="Enter Wallet Number..." id="view_wallet_number" disabled>@error('wallet_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="crypto_currency">Crypto Currency</label><br><input type="text" name="crypto_currency" class="form-control @error('crypto_currency') is-invalid @enderror" value="{{ old('crypto_currency', $query->crypto_currency ?? "") }}" placeholder="Enter Crypto Currency..." id="view_crypto_currency" disabled>@error('crypto_currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="crypto_address">Crypto Address</label><br><input type="text" name="crypto_address" class="form-control @error('crypto_address') is-invalid @enderror" value="{{ old('crypto_address', $query->crypto_address ?? "") }}" placeholder="Enter Crypto Address..." id="view_crypto_address" disabled>@error('crypto_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="encrypted_data">Encrypted Data</label><br><input type="text" name="encrypted_data" class="form-control @error('encrypted_data') is-invalid @enderror" value="{{ old('encrypted_data', $query->encrypted_data ?? "") }}" placeholder="Enter Encrypted Data..." id="view_encrypted_data" disabled>@error('encrypted_data')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="fingerprint">Fingerprint</label><br><input type="text" name="fingerprint" class="form-control @error('fingerprint') is-invalid @enderror" value="{{ old('fingerprint', $query->fingerprint ?? "") }}" placeholder="Enter Fingerprint..." id="view_fingerprint" disabled>@error('fingerprint')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_compromised">Is Compromised</label><br><input type="radio" name="is_compromised" id="view_is_compromised_yes" value="1" {{ old('is_compromised', $query->is_compromised ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_compromised_yes" disabled>Is Compromised Yes </label>
                                           <input type="radio" name="is_compromised" id="view_is_compromised_no" value="0" {{ old('is_compromised', $query->is_compromised ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_compromised_no" disabled>Is Compromised No </label>@error('is_compromised')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="gateway_metadata">Gateway Metadata</label><br><input type="text" name="gateway_metadata" class="form-control @error('gateway_metadata') is-invalid @enderror" value="{{ old('gateway_metadata', $query->gateway_metadata ?? "") }}" placeholder="Enter Gateway Metadata..." id="view_gateway_metadata" disabled>@error('gateway_metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="verified_at">Verified At</label><br><input type="datetime-local" name="verified_at" class="form-control @error('verified_at') is-invalid @enderror" value="{{ old('verified_at', $query->verified_at ?? "") }}" placeholder="Enter Verified At..." id="view_verified_at" disabled>@error('verified_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="verified_by">Verified By</label><br><input type="text" name="verified_by" class="form-control @error('verified_by') is-invalid @enderror" value="{{ old('verified_by', $query->verified_by ?? "") }}" placeholder="Enter Verified By..." id="view_verified_by" disabled>@error('verified_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="last_used_at">Last Used At</label><br><input type="datetime-local" name="last_used_at" class="form-control @error('last_used_at') is-invalid @enderror" value="{{ old('last_used_at', $query->last_used_at ?? "") }}" placeholder="Enter Last Used At..." id="view_last_used_at" disabled>@error('last_used_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="usage_count">Usage Count</label><br><input type="number" name="usage_count" min="0" class="form-control @error('usage_count') is-invalid @enderror" value="{{ old('usage_count', $query->usage_count ?? "") }}" placeholder="Enter Usage Count..." id="view_usage_count" disabled>@error('usage_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-methods.index') }}">Close</a>
                          </div>
                      </div>
                  </form>