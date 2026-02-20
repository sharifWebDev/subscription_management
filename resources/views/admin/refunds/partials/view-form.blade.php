<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_master_id">Payment Master Id</label><br><input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id', $query->payment_master_id ?? "") }}" placeholder="Enter Payment Master Id..." id="view_payment_master_id" disabled>@error('payment_master_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_transaction_id">Payment Transaction Id</label><br><input type="text" name="payment_transaction_id" class="form-control @error('payment_transaction_id') is-invalid @enderror" value="{{ old('payment_transaction_id', $query->payment_transaction_id ?? "") }}" placeholder="Enter Payment Transaction Id..." id="view_payment_transaction_id" disabled>@error('payment_transaction_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_id">User Id</label><br><input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="view_user_id" disabled>@error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="refund_number">Refund Number</label><br><input type="text" name="refund_number" class="form-control @error('refund_number') is-invalid @enderror" value="{{ old('refund_number', $query->refund_number ?? "") }}" placeholder="Enter Refund Number..." id="view_refund_number" disabled>@error('refund_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="initiated_by">Initiated By</label><br><input type="text" name="initiated_by" class="form-control @error('initiated_by') is-invalid @enderror" value="{{ old('initiated_by', $query->initiated_by ?? "") }}" placeholder="Enter Initiated By..." id="view_initiated_by" disabled>@error('initiated_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="reason">Reason</label><br><input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason', $query->reason ?? "") }}" placeholder="Enter Reason..." id="view_reason" disabled>@error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="reason_details">Reason Details</label><br><textarea name="reason_details" class="form-control @error('reason_details') is-invalid @enderror" placeholder="Enter Reason Details..." id="view_reason_details" disabled>{{ old('reason_details', $query->reason_details ?? "") }}</textarea>@error('reason_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="customer_comments">Customer Comments</label><br><textarea name="customer_comments" class="form-control @error('customer_comments') is-invalid @enderror" placeholder="Enter Customer Comments..." id="view_customer_comments" disabled>{{ old('customer_comments', $query->customer_comments ?? "") }}</textarea>@error('customer_comments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="requested_at">Requested At</label><br><input type="datetime-local" name="requested_at" class="form-control @error('requested_at') is-invalid @enderror" value="{{ old('requested_at', $query->requested_at ?? "") }}" placeholder="Enter Requested At..." id="view_requested_at" disabled>@error('requested_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="approved_at">Approved At</label><br><input type="datetime-local" name="approved_at" class="form-control @error('approved_at') is-invalid @enderror" value="{{ old('approved_at', $query->approved_at ?? "") }}" placeholder="Enter Approved At..." id="view_approved_at" disabled>@error('approved_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="approved_by">Approved By</label><br><input type="text" name="approved_by" class="form-control @error('approved_by') is-invalid @enderror" value="{{ old('approved_by', $query->approved_by ?? "") }}" placeholder="Enter Approved By..." id="view_approved_by" disabled>@error('approved_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="processed_at">Processed At</label><br><input type="datetime-local" name="processed_at" class="form-control @error('processed_at') is-invalid @enderror" value="{{ old('processed_at', $query->processed_at ?? "") }}" placeholder="Enter Processed At..." id="view_processed_at" disabled>@error('processed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="gateway_refund_id">Gateway Refund Id</label><br><input type="text" name="gateway_refund_id" class="form-control @error('gateway_refund_id') is-invalid @enderror" value="{{ old('gateway_refund_id', $query->gateway_refund_id ?? "") }}" placeholder="Enter Gateway Refund Id..." id="view_gateway_refund_id" disabled>@error('gateway_refund_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="gateway_response">Gateway Response</label><br><input type="text" name="gateway_response" class="form-control @error('gateway_response') is-invalid @enderror" value="{{ old('gateway_response', $query->gateway_response ?? "") }}" placeholder="Enter Gateway Response..." id="view_gateway_response" disabled>@error('gateway_response')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="documents">Documents</label><br><input type="text" name="documents" class="form-control @error('documents') is-invalid @enderror" value="{{ old('documents', $query->documents ?? "") }}" placeholder="Enter Documents..." id="view_documents" disabled>@error('documents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="processed_by">Processed By</label><br><input type="text" name="processed_by" class="form-control @error('processed_by') is-invalid @enderror" value="{{ old('processed_by', $query->processed_by ?? "") }}" placeholder="Enter Processed By..." id="view_processed_by" disabled>@error('processed_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="rejection_reason">Rejection Reason</label><br><textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" placeholder="Enter Rejection Reason..." id="view_rejection_reason" disabled>{{ old('rejection_reason', $query->rejection_reason ?? "") }}</textarea>@error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.refunds.index') }}">Close</a>
                          </div>
                      </div>
                  </form>