<form method="POST" id="createpaymentsForm" action="{{ url('api/v1/payments') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="invoice_id">Invoice Id</label>
        <input type="text" name="invoice_id" class="form-control @error('invoice_id') is-invalid @enderror" value="{{ old('invoice_id') }}" placeholder="Enter Invoice Id..." id="create_invoice_id" required>
        @error('invoice_id')
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
        <label for="external_id">External Id</label>
        <input type="text" name="external_id" class="form-control @error('external_id') is-invalid @enderror" value="{{ old('external_id') }}" placeholder="Enter External Id..." id="create_external_id" required>
        @error('external_id')
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
        <label for="net">Net</label>
        <input type="number" step="any" name="net" min="0" class="form-control @error('net') is-invalid @enderror" value="{{ old('net') }}" placeholder="Enter Net..." id="create_net" required>
        @error('net')
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
        <label for="gateway">Gateway</label>
        <input type="text" name="gateway" class="form-control @error('gateway') is-invalid @enderror" value="{{ old('gateway') }}" placeholder="Enter Gateway..." id="create_gateway" required>
        @error('gateway')
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
        <label for="payment_method">Payment Method</label>
        <input type="text" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" value="{{ old('payment_method') }}" placeholder="Enter Payment Method..." id="create_payment_method" required>
        @error('payment_method')
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
        <label for="refunded_at">Refunded At</label>
        <input type="datetime-local" name="refunded_at" class="form-control @error('refunded_at') is-invalid @enderror" value="{{ old('refunded_at') }}" id="create_refunded_at" required>
        @error('refunded_at')
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
        <label for="fraud_indicators">Fraud Indicators</label>
        <input type="text" name="fraud_indicators" class="form-control @error('fraud_indicators') is-invalid @enderror" value="{{ old('fraud_indicators') }}" placeholder="Enter Fraud Indicators..." id="create_fraud_indicators" required>
        @error('fraud_indicators')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payments.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>