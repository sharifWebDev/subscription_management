<form method="POST" id="createrefundsForm" action="{{ url('api/v1/refunds') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="payment_master_id">Payment Master Id</label>
        <input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id') }}" placeholder="Enter Payment Master Id..." id="create_payment_master_id" required>
        @error('payment_master_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="payment_transaction_id">Payment Transaction Id</label>
        <input type="text" name="payment_transaction_id" class="form-control @error('payment_transaction_id') is-invalid @enderror" value="{{ old('payment_transaction_id') }}" placeholder="Enter Payment Transaction Id..." id="create_payment_transaction_id" required>
        @error('payment_transaction_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="user_id">User Id</label>
        <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" placeholder="Enter User Id..." id="create_user_id" required>
        @error('user_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="refund_number">Refund Number</label>
        <input type="text" name="refund_number" class="form-control @error('refund_number') is-invalid @enderror" value="{{ old('refund_number') }}" placeholder="Enter Refund Number..." id="create_refund_number" required>
        @error('refund_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="type">Type</label>
        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}" placeholder="Enter Type..." id="create_type" required>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="status">Status</label>
        <input type="text" name="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}" placeholder="Enter Status..." id="create_status" required>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="initiated_by">Initiated By</label>
        <input type="text" name="initiated_by" class="form-control @error('initiated_by') is-invalid @enderror" value="{{ old('initiated_by') }}" placeholder="Enter Initiated By..." id="create_initiated_by" required>
        @error('initiated_by')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="Enter Amount..." id="create_amount" required>
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="fee">Fee</label>
        <input type="number" step="any" name="fee" min="0" class="form-control @error('fee') is-invalid @enderror" value="{{ old('fee') }}" placeholder="Enter Fee..." id="create_fee" required>
        @error('fee')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="net_amount">Net Amount</label>
        <input type="number" step="any" name="net_amount" min="0" class="form-control @error('net_amount') is-invalid @enderror" value="{{ old('net_amount') }}" placeholder="Enter Net Amount..." id="create_net_amount" required>
        @error('net_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="currency">Currency</label>
        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency') }}" placeholder="Enter Currency..." id="create_currency" required>
        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="exchange_rate">Exchange Rate</label>
        <input type="number" step="any" name="exchange_rate" min="0" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate') }}" placeholder="Enter Exchange Rate..." id="create_exchange_rate" required>
        @error('exchange_rate')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="reason">Reason</label>
        <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason') }}" placeholder="Enter Reason..." id="create_reason" required>
        @error('reason')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="reason_details">Reason Details</label>
        <textarea name="reason_details" class="form-control @error('reason_details') is-invalid @enderror" placeholder="Enter Reason Details..." id="create_reason_details" required>{{ old('reason_details') }}</textarea>
        @error('reason_details')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="customer_comments">Customer Comments</label>
        <textarea name="customer_comments" class="form-control @error('customer_comments') is-invalid @enderror" placeholder="Enter Customer Comments..." id="create_customer_comments" required>{{ old('customer_comments') }}</textarea>
        @error('customer_comments')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="requested_at">Requested At</label>
        <input type="datetime-local" name="requested_at" class="form-control @error('requested_at') is-invalid @enderror" value="{{ old('requested_at') }}" id="create_requested_at" required>
        @error('requested_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="approved_at">Approved At</label>
        <input type="datetime-local" name="approved_at" class="form-control @error('approved_at') is-invalid @enderror" value="{{ old('approved_at') }}" id="create_approved_at" required>
        @error('approved_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="approved_by">Approved By</label>
        <input type="text" name="approved_by" class="form-control @error('approved_by') is-invalid @enderror" value="{{ old('approved_by') }}" placeholder="Enter Approved By..." id="create_approved_by" required>
        @error('approved_by')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="processed_at">Processed At</label>
        <input type="datetime-local" name="processed_at" class="form-control @error('processed_at') is-invalid @enderror" value="{{ old('processed_at') }}" id="create_processed_at" required>
        @error('processed_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="completed_at">Completed At</label>
        <input type="datetime-local" name="completed_at" class="form-control @error('completed_at') is-invalid @enderror" value="{{ old('completed_at') }}" id="create_completed_at" required>
        @error('completed_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="failed_at">Failed At</label>
        <input type="datetime-local" name="failed_at" class="form-control @error('failed_at') is-invalid @enderror" value="{{ old('failed_at') }}" id="create_failed_at" required>
        @error('failed_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="gateway_refund_id">Gateway Refund Id</label>
        <input type="text" name="gateway_refund_id" class="form-control @error('gateway_refund_id') is-invalid @enderror" value="{{ old('gateway_refund_id') }}" placeholder="Enter Gateway Refund Id..." id="create_gateway_refund_id" required>
        @error('gateway_refund_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="gateway_response">Gateway Response</label>
        <input type="text" name="gateway_response" class="form-control @error('gateway_response') is-invalid @enderror" value="{{ old('gateway_response') }}" placeholder="Enter Gateway Response..." id="create_gateway_response" required>
        @error('gateway_response')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="metadata">Metadata</label>
        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata') }}" placeholder="Enter Metadata..." id="create_metadata" required>
        @error('metadata')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="documents">Documents</label>
        <input type="text" name="documents" class="form-control @error('documents') is-invalid @enderror" value="{{ old('documents') }}" placeholder="Enter Documents..." id="create_documents" required>
        @error('documents')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="processed_by">Processed By</label>
        <input type="text" name="processed_by" class="form-control @error('processed_by') is-invalid @enderror" value="{{ old('processed_by') }}" placeholder="Enter Processed By..." id="create_processed_by" required>
        @error('processed_by')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="rejection_reason">Rejection Reason</label>
        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" placeholder="Enter Rejection Reason..." id="create_rejection_reason" required>{{ old('rejection_reason') }}</textarea>
        @error('rejection_reason')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.refunds.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>