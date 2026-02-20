<form method="POST" id="createpayment_mastersForm" action="{{ url('api/v1/payment-masters') }}" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="row">
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
        <label for="payment_number">Payment Number</label>
        <input type="text" name="payment_number" class="form-control @error('payment_number') is-invalid @enderror" value="{{ old('payment_number') }}" placeholder="Enter Payment Number..." id="create_payment_number" required>
        @error('payment_number')
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
        <label for="total_amount">Total Amount</label>
        <input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount') }}" placeholder="Enter Total Amount..." id="create_total_amount" required>
        @error('total_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="subtotal">Subtotal</label>
        <input type="number" step="any" name="subtotal" min="0" class="form-control @error('subtotal') is-invalid @enderror" value="{{ old('subtotal') }}" placeholder="Enter Subtotal..." id="create_subtotal" required>
        @error('subtotal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tax_amount">Tax Amount</label>
        <input type="number" step="any" name="tax_amount" min="0" class="form-control @error('tax_amount') is-invalid @enderror" value="{{ old('tax_amount') }}" placeholder="Enter Tax Amount..." id="create_tax_amount" required>
        @error('tax_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="discount_amount">Discount Amount</label>
        <input type="number" step="any" name="discount_amount" min="0" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount') }}" placeholder="Enter Discount Amount..." id="create_discount_amount" required>
        @error('discount_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="fee_amount">Fee Amount</label>
        <input type="number" step="any" name="fee_amount" min="0" class="form-control @error('fee_amount') is-invalid @enderror" value="{{ old('fee_amount') }}" placeholder="Enter Fee Amount..." id="create_fee_amount" required>
        @error('fee_amount')
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
        <label for="paid_amount">Paid Amount</label>
        <input type="number" step="any" name="paid_amount" min="0" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount') }}" placeholder="Enter Paid Amount..." id="create_paid_amount" required>
        @error('paid_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="due_amount">Due Amount</label>
        <input type="number" step="any" name="due_amount" min="0" class="form-control @error('due_amount') is-invalid @enderror" value="{{ old('due_amount') }}" placeholder="Enter Due Amount..." id="create_due_amount" required>
        @error('due_amount')
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
        <label for="base_currency">Base Currency</label>
        <input type="text" name="base_currency" class="form-control @error('base_currency') is-invalid @enderror" value="{{ old('base_currency') }}" placeholder="Enter Base Currency..." id="create_base_currency" required>
        @error('base_currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="base_amount">Base Amount</label>
        <input type="number" step="any" name="base_amount" min="0" class="form-control @error('base_amount') is-invalid @enderror" value="{{ old('base_amount') }}" placeholder="Enter Base Amount..." id="create_base_amount" required>
        @error('base_amount')
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
        <label for="payment_method_details">Payment Method Details</label>
        <input type="text" name="payment_method_details" class="form-control @error('payment_method_details') is-invalid @enderror" value="{{ old('payment_method_details') }}" placeholder="Enter Payment Method Details..." id="create_payment_method_details" required>
        @error('payment_method_details')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="payment_gateway">Payment Gateway</label>
        <input type="text" name="payment_gateway" class="form-control @error('payment_gateway') is-invalid @enderror" value="{{ old('payment_gateway') }}" placeholder="Enter Payment Gateway..." id="create_payment_gateway" required>
        @error('payment_gateway')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="is_installment">Is Installment</label>
        <input type="radio" name="is_installment" id="create_is_installment_yes" value="1" {{ old('is_installment') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_installment_yes">Is Installment Yes</label>
<input type="radio" name="is_installment" id="create_is_installment_no" value="0" {{ old('is_installment') == 0 ? "checked" : "" }}> 
                            <label for="create_is_installment_no">Is Installment No</label>
        @error('is_installment')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="installment_count">Installment Count</label>
        <input type="number" name="installment_count" min="0" class="form-control @error('installment_count') is-invalid @enderror" value="{{ old('installment_count') }}" placeholder="Enter Installment Count..." id="create_installment_count" required>
        @error('installment_count')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="installment_frequency">Installment Frequency</label>
        <input type="text" name="installment_frequency" class="form-control @error('installment_frequency') is-invalid @enderror" value="{{ old('installment_frequency') }}" placeholder="Enter Installment Frequency..." id="create_installment_frequency" required>
        @error('installment_frequency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="payment_date">Payment Date</label>
        <input type="datetime-local" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date') }}" id="create_payment_date" required>
        @error('payment_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="due_date">Due Date</label>
        <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" id="create_due_date" required>
        @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="paid_at">Paid At</label>
        <input type="datetime-local" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at') }}" id="create_paid_at" required>
        @error('paid_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="cancelled_at">Cancelled At</label>
        <input type="datetime-local" name="cancelled_at" class="form-control @error('cancelled_at') is-invalid @enderror" value="{{ old('cancelled_at') }}" id="create_cancelled_at" required>
        @error('cancelled_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="expires_at">Expires At</label>
        <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}" id="create_expires_at" required>
        @error('expires_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="customer_reference">Customer Reference</label>
        <input type="text" name="customer_reference" class="form-control @error('customer_reference') is-invalid @enderror" value="{{ old('customer_reference') }}" placeholder="Enter Customer Reference..." id="create_customer_reference" required>
        @error('customer_reference')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="bank_reference">Bank Reference</label>
        <input type="text" name="bank_reference" class="form-control @error('bank_reference') is-invalid @enderror" value="{{ old('bank_reference') }}" placeholder="Enter Bank Reference..." id="create_bank_reference" required>
        @error('bank_reference')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="gateway_reference">Gateway Reference</label>
        <input type="text" name="gateway_reference" class="form-control @error('gateway_reference') is-invalid @enderror" value="{{ old('gateway_reference') }}" placeholder="Enter Gateway Reference..." id="create_gateway_reference" required>
        @error('gateway_reference')
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
        <label for="custom_fields">Custom Fields</label>
        <input type="text" name="custom_fields" class="form-control @error('custom_fields') is-invalid @enderror" value="{{ old('custom_fields') }}" placeholder="Enter Custom Fields..." id="create_custom_fields" required>
        @error('custom_fields')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter Notes..." id="create_notes" required>{{ old('notes') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="failure_reason">Failure Reason</label>
        <textarea name="failure_reason" class="form-control @error('failure_reason') is-invalid @enderror" placeholder="Enter Failure Reason..." id="create_failure_reason" required>{{ old('failure_reason') }}</textarea>
        @error('failure_reason')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-masters.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>