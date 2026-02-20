
            <form method="POST" id="editpayment_methodsForm" action="{{ url('api/v1/payment-methods/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
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
                        <label for="type">Type</label><br>
                        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="edit_type" required>
                        @error('type')
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
                        <label for="gateway_customer_id">Gateway Customer Id</label><br>
                        <input type="text" name="gateway_customer_id" class="form-control @error('gateway_customer_id') is-invalid @enderror" value="{{ old('gateway_customer_id', $query->gateway_customer_id ?? "") }}" placeholder="Enter Gateway Customer Id..." id="edit_gateway_customer_id" required>
                        @error('gateway_customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_payment_method_id">Gateway Payment Method Id</label><br>
                        <input type="text" name="gateway_payment_method_id" class="form-control @error('gateway_payment_method_id') is-invalid @enderror" value="{{ old('gateway_payment_method_id', $query->gateway_payment_method_id ?? "") }}" placeholder="Enter Gateway Payment Method Id..." id="edit_gateway_payment_method_id" required>
                        @error('gateway_payment_method_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="nickname">Nickname</label><br>
                        <input type="text" name="nickname" class="form-control @error('nickname') is-invalid @enderror" value="{{ old('nickname', $query->nickname ?? "") }}" placeholder="Enter Nickname..." id="edit_nickname" required>
                        @error('nickname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_default">Is Default</label><br>
                        
                                <input type="radio" name="is_default" id="edit_is_default_yes" value="1" {{ old('is_default', $query->is_default ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_default_yes">Is Default Yes</label>
                                <input type="radio" name="is_default" id="edit_is_default_no" value="0" {{ old('is_default', $query->is_default ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_default_no">Is Default No</label>
                        @error('is_default')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_verified">Is Verified</label><br>
                        
                                <input type="radio" name="is_verified" id="edit_is_verified_yes" value="1" {{ old('is_verified', $query->is_verified ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_verified_yes">Is Verified Yes</label>
                                <input type="radio" name="is_verified" id="edit_is_verified_no" value="0" {{ old('is_verified', $query->is_verified ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_verified_no">Is Verified No</label>
                        @error('is_verified')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="card_last4">Card Last4</label><br>
                        <input type="text" name="card_last4" class="form-control @error('card_last4') is-invalid @enderror" value="{{ old('card_last4', $query->card_last4 ?? "") }}" placeholder="Enter Card Last4..." id="edit_card_last4" required>
                        @error('card_last4')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="card_brand">Card Brand</label><br>
                        <input type="text" name="card_brand" class="form-control @error('card_brand') is-invalid @enderror" value="{{ old('card_brand', $query->card_brand ?? "") }}" placeholder="Enter Card Brand..." id="edit_card_brand" required>
                        @error('card_brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="card_exp_month">Card Exp Month</label><br>
                        <input type="number" name="card_exp_month" min="0" class="form-control @error('card_exp_month') is-invalid @enderror" value="{{ old('card_exp_month', $query->card_exp_month ?? "") }}" placeholder="Enter Card Exp Month..." id="edit_card_exp_month" required>
                        @error('card_exp_month')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="card_exp_year">Card Exp Year</label><br>
                        <input type="number" name="card_exp_year" min="0" class="form-control @error('card_exp_year') is-invalid @enderror" value="{{ old('card_exp_year', $query->card_exp_year ?? "") }}" placeholder="Enter Card Exp Year..." id="edit_card_exp_year" required>
                        @error('card_exp_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="card_country">Card Country</label><br>
                        <input type="text" name="card_country" class="form-control @error('card_country') is-invalid @enderror" value="{{ old('card_country', $query->card_country ?? "") }}" placeholder="Enter Card Country..." id="edit_card_country" required>
                        @error('card_country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="bank_name">Bank Name</label><br>
                        <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $query->bank_name ?? "") }}" placeholder="Enter Bank Name..." id="edit_bank_name" required>
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="bank_account_last4">Bank Account Last4</label><br>
                        <input type="text" name="bank_account_last4" class="form-control @error('bank_account_last4') is-invalid @enderror" value="{{ old('bank_account_last4', $query->bank_account_last4 ?? "") }}" placeholder="Enter Bank Account Last4..." id="edit_bank_account_last4" required>
                        @error('bank_account_last4')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="bank_account_type">Bank Account Type</label><br>
                        <input type="text" name="bank_account_type" class="form-control @error('bank_account_type') is-invalid @enderror" value="{{ old('bank_account_type', $query->bank_account_type ?? "") }}" placeholder="Enter Bank Account Type..." id="edit_bank_account_type" required>
                        @error('bank_account_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="bank_routing_number">Bank Routing Number</label><br>
                        <input type="text" name="bank_routing_number" class="form-control @error('bank_routing_number') is-invalid @enderror" value="{{ old('bank_routing_number', $query->bank_routing_number ?? "") }}" placeholder="Enter Bank Routing Number..." id="edit_bank_routing_number" required>
                        @error('bank_routing_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="wallet_type">Wallet Type</label><br>
                        <input type="text" name="wallet_type" class="form-control @error('wallet_type') is-invalid @enderror" value="{{ old('wallet_type', $query->wallet_type ?? "") }}" placeholder="Enter Wallet Type..." id="edit_wallet_type" required>
                        @error('wallet_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="wallet_number">Wallet Number</label><br>
                        <input type="text" name="wallet_number" class="form-control @error('wallet_number') is-invalid @enderror" value="{{ old('wallet_number', $query->wallet_number ?? "") }}" placeholder="Enter Wallet Number..." id="edit_wallet_number" required>
                        @error('wallet_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="crypto_currency">Crypto Currency</label><br>
                        <input type="text" name="crypto_currency" class="form-control @error('crypto_currency') is-invalid @enderror" value="{{ old('crypto_currency', $query->crypto_currency ?? "") }}" placeholder="Enter Crypto Currency..." id="edit_crypto_currency" required>
                        @error('crypto_currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="crypto_address">Crypto Address</label><br>
                        <input type="text" name="crypto_address" class="form-control @error('crypto_address') is-invalid @enderror" value="{{ old('crypto_address', $query->crypto_address ?? "") }}" placeholder="Enter Crypto Address..." id="edit_crypto_address" required>
                        @error('crypto_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="encrypted_data">Encrypted Data</label><br>
                        <input type="text" name="encrypted_data" class="form-control @error('encrypted_data') is-invalid @enderror" value="{{ old('encrypted_data', $query->encrypted_data ?? "") }}" placeholder="Enter Encrypted Data..." id="edit_encrypted_data" required>
                        @error('encrypted_data')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="fingerprint">Fingerprint</label><br>
                        <input type="text" name="fingerprint" class="form-control @error('fingerprint') is-invalid @enderror" value="{{ old('fingerprint', $query->fingerprint ?? "") }}" placeholder="Enter Fingerprint..." id="edit_fingerprint" required>
                        @error('fingerprint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_compromised">Is Compromised</label><br>
                        
                                <input type="radio" name="is_compromised" id="edit_is_compromised_yes" value="1" {{ old('is_compromised', $query->is_compromised ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_compromised_yes">Is Compromised Yes</label>
                                <input type="radio" name="is_compromised" id="edit_is_compromised_no" value="0" {{ old('is_compromised', $query->is_compromised ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_compromised_no">Is Compromised No</label>
                        @error('is_compromised')
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
                        <label for="gateway_metadata">Gateway Metadata</label><br>
                        <input type="text" name="gateway_metadata" class="form-control @error('gateway_metadata') is-invalid @enderror" value="{{ old('gateway_metadata', $query->gateway_metadata ?? "") }}" placeholder="Enter Gateway Metadata..." id="edit_gateway_metadata" required>
                        @error('gateway_metadata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="verified_at">Verified At</label><br>
                        <input type="datetime-local" name="verified_at" class="form-control @error('verified_at') is-invalid @enderror" value="{{ old('verified_at', $query->verified_at ?? "") }}" id="edit_verified_at" required>
                        @error('verified_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="verified_by">Verified By</label><br>
                        <input type="text" name="verified_by" class="form-control @error('verified_by') is-invalid @enderror" value="{{ old('verified_by', $query->verified_by ?? "") }}" placeholder="Enter Verified By..." id="edit_verified_by" required>
                        @error('verified_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="last_used_at">Last Used At</label><br>
                        <input type="datetime-local" name="last_used_at" class="form-control @error('last_used_at') is-invalid @enderror" value="{{ old('last_used_at', $query->last_used_at ?? "") }}" id="edit_last_used_at" required>
                        @error('last_used_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="usage_count">Usage Count</label><br>
                        <input type="number" name="usage_count" min="0" class="form-control @error('usage_count') is-invalid @enderror" value="{{ old('usage_count', $query->usage_count ?? "") }}" placeholder="Enter Usage Count..." id="edit_usage_count" required>
                        @error('usage_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-methods.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>