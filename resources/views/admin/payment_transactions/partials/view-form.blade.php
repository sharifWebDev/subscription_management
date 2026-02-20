<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_master_id">Payment Master Id</label><br><input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id', $query->payment_master_id ?? "") }}" placeholder="Enter Payment Master Id..." id="view_payment_master_id" disabled>@error('payment_master_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_child_id">Payment Child Id</label><br><input type="text" name="payment_child_id" class="form-control @error('payment_child_id') is-invalid @enderror" value="{{ old('payment_child_id', $query->payment_child_id ?? "") }}" placeholder="Enter Payment Child Id..." id="view_payment_child_id" disabled>@error('payment_child_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="transaction_id">Transaction Id</label><br><input type="text" name="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" value="{{ old('transaction_id', $query->transaction_id ?? "") }}" placeholder="Enter Transaction Id..." id="view_transaction_id" disabled>@error('transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="reference_id">Reference Id</label><br><input type="text" name="reference_id" class="form-control @error('reference_id') is-invalid @enderror" value="{{ old('reference_id', $query->reference_id ?? "") }}" placeholder="Enter Reference Id..." id="view_reference_id" disabled>@error('reference_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method</label><br><input type="text" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" value="{{ old('payment_method', $query->payment_method ?? "") }}" placeholder="Enter Payment Method..." id="view_payment_method" disabled>@error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_gateway">Payment Gateway</label><br><input type="text" name="payment_gateway" class="form-control @error('payment_gateway') is-invalid @enderror" value="{{ old('payment_gateway', $query->payment_gateway ?? "") }}" placeholder="Enter Payment Gateway..." id="view_payment_gateway" disabled>@error('payment_gateway')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="gateway_response">Gateway Response</label><br><input type="text" name="gateway_response" class="form-control @error('gateway_response') is-invalid @enderror" value="{{ old('gateway_response', $query->gateway_response ?? "") }}" placeholder="Enter Gateway Response..." id="view_gateway_response" disabled>@error('gateway_response')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_method_details">Payment Method Details</label><br><input type="text" name="payment_method_details" class="form-control @error('payment_method_details') is-invalid @enderror" value="{{ old('payment_method_details', $query->payment_method_details ?? "") }}" placeholder="Enter Payment Method Details..." id="view_payment_method_details" disabled>@error('payment_method_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount">Amount</label><br><input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $query->amount ?? "") }}" placeholder="Enter Amount..." id="view_amount" disabled>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="fee">Fee</label><br><input type="number" step="any" name="fee" min="0" class="form-control @error('fee') is-invalid @enderror" value="{{ old('fee', $query->fee ?? "") }}" placeholder="Enter Fee..." id="view_fee" disabled>@error('fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tax">Tax</label><br><input type="number" step="any" name="tax" min="0" class="form-control @error('tax') is-invalid @enderror" value="{{ old('tax', $query->tax ?? "") }}" placeholder="Enter Tax..." id="view_tax" disabled>@error('tax')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="net_amount">Net Amount</label><br><input type="number" step="any" name="net_amount" min="0" class="form-control @error('net_amount') is-invalid @enderror" value="{{ old('net_amount', $query->net_amount ?? "") }}" placeholder="Enter Net Amount..." id="view_net_amount" disabled>@error('net_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="currency">Currency</label><br><input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="view_currency" disabled>@error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="exchange_rate">Exchange Rate</label><br><input type="number" step="any" name="exchange_rate" min="0" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $query->exchange_rate ?? "") }}" placeholder="Enter Exchange Rate..." id="view_exchange_rate" disabled>@error('exchange_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="status">Status</label><br><input type="radio" name="status" value="1" {{ old('status', $query->status ?? "") == 1 ? "checked" : "" }} id="view_status_yes" disabled> 
                                   <label for="view_status_yes" disabled>Status Yes </label>
                                   <input type="radio" name="status" value="0" {{ old('status', $query->status ?? "") == 0 ? "checked" : "" }} id="view_status_no" disabled> 
                                   <label for="view_status_no" disabled>Status No </label>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="card_country">Card Country</label><br><input type="text" name="card_country" class="form-control @error('card_country') is-invalid @enderror" value="{{ old('card_country', $query->card_country ?? "") }}" placeholder="Enter Card Country..." id="view_card_country" disabled>@error('card_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="wallet_transaction_id">Wallet Transaction Id</label><br><input type="text" name="wallet_transaction_id" class="form-control @error('wallet_transaction_id') is-invalid @enderror" value="{{ old('wallet_transaction_id', $query->wallet_transaction_id ?? "") }}" placeholder="Enter Wallet Transaction Id..." id="view_wallet_transaction_id" disabled>@error('wallet_transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="installment_number">Installment Number</label><br><input type="number" name="installment_number" min="0" class="form-control @error('installment_number') is-invalid @enderror" value="{{ old('installment_number', $query->installment_number ?? "") }}" placeholder="Enter Installment Number..." id="view_installment_number" disabled>@error('installment_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="total_installments">Total Installments</label><br><input type="number" name="total_installments" min="0" class="form-control @error('total_installments') is-invalid @enderror" value="{{ old('total_installments', $query->total_installments ?? "") }}" placeholder="Enter Total Installments..." id="view_total_installments" disabled>@error('total_installments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="initiated_at">Initiated At</label><br><input type="datetime-local" name="initiated_at" class="form-control @error('initiated_at') is-invalid @enderror" value="{{ old('initiated_at', $query->initiated_at ?? "") }}" placeholder="Enter Initiated At..." id="view_initiated_at" disabled>@error('initiated_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="authorized_at">Authorized At</label><br><input type="datetime-local" name="authorized_at" class="form-control @error('authorized_at') is-invalid @enderror" value="{{ old('authorized_at', $query->authorized_at ?? "") }}" placeholder="Enter Authorized At..." id="view_authorized_at" disabled>@error('authorized_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="captured_at">Captured At</label><br><input type="datetime-local" name="captured_at" class="form-control @error('captured_at') is-invalid @enderror" value="{{ old('captured_at', $query->captured_at ?? "") }}" placeholder="Enter Captured At..." id="view_captured_at" disabled>@error('captured_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="completed_at">Completed At</label><br><input type="datetime-local" name="completed_at" class="form-control @error('completed_at') is-invalid @enderror" value="{{ old('completed_at', $query->completed_at ?? "") }}" placeholder="Enter Completed At..." id="view_completed_at" disabled>@error('completed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="failed_at">Failed At</label><br><input type="datetime-local" name="failed_at" class="form-control @error('failed_at') is-invalid @enderror" value="{{ old('failed_at', $query->failed_at ?? "") }}" placeholder="Enter Failed At..." id="view_failed_at" disabled>@error('failed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="refunded_at">Refunded At</label><br><input type="datetime-local" name="refunded_at" class="form-control @error('refunded_at') is-invalid @enderror" value="{{ old('refunded_at', $query->refunded_at ?? "") }}" placeholder="Enter Refunded At..." id="view_refunded_at" disabled>@error('refunded_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="fraud_indicators">Fraud Indicators</label><br><input type="text" name="fraud_indicators" class="form-control @error('fraud_indicators') is-invalid @enderror" value="{{ old('fraud_indicators', $query->fraud_indicators ?? "") }}" placeholder="Enter Fraud Indicators..." id="view_fraud_indicators" disabled>@error('fraud_indicators')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="risk_score">Risk Score</label><br><input type="number" step="any" name="risk_score" min="0" class="form-control @error('risk_score') is-invalid @enderror" value="{{ old('risk_score', $query->risk_score ?? "") }}" placeholder="Enter Risk Score..." id="view_risk_score" disabled>@error('risk_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="requires_review">Requires Review</label><br><input type="radio" name="requires_review" id="view_requires_review_yes" value="1" {{ old('requires_review', $query->requires_review ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editrequires_review_yes" disabled>Requires Review Yes </label>
                                           <input type="radio" name="requires_review" id="view_requires_review_no" value="0" {{ old('requires_review', $query->requires_review ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editrequires_review_no" disabled>Requires Review No </label>@error('requires_review')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="custom_fields">Custom Fields</label><br><input type="text" name="custom_fields" class="form-control @error('custom_fields') is-invalid @enderror" value="{{ old('custom_fields', $query->custom_fields ?? "") }}" placeholder="Enter Custom Fields..." id="view_custom_fields" disabled>@error('custom_fields')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="notes">Notes</label><br><textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter Notes..." id="view_notes" disabled>{{ old('notes', $query->notes ?? "") }}</textarea>@error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="failure_reason">Failure Reason</label><br><textarea name="failure_reason" class="form-control @error('failure_reason') is-invalid @enderror" placeholder="Enter Failure Reason..." id="view_failure_reason" disabled>{{ old('failure_reason', $query->failure_reason ?? "") }}</textarea>@error('failure_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="ip_address">Ip Address</label><br><input type="text" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $query->ip_address ?? "") }}" placeholder="Enter Ip Address..." id="view_ip_address" disabled>@error('ip_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_agent">User Agent</label><br><textarea name="user_agent" class="form-control @error('user_agent') is-invalid @enderror" placeholder="Enter User Agent..." id="view_user_agent" disabled>{{ old('user_agent', $query->user_agent ?? "") }}</textarea>@error('user_agent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="location_data">Location Data</label><br><input type="text" name="location_data" class="form-control @error('location_data') is-invalid @enderror" value="{{ old('location_data', $query->location_data ?? "") }}" placeholder="Enter Location Data..." id="view_location_data" disabled>@error('location_data')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-transactions.index') }}">Close</a>
                          </div>
                      </div>
                  </form>