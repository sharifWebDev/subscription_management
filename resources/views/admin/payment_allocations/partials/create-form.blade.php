<form method="POST" id="createpayment_allocationsForm" action="{{ url('api/v1/payment-allocations') }}" enctype="multipart/form-data">
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
        <label for="payment_child_id">Payment Child Id</label>
        <input type="text" name="payment_child_id" class="form-control @error('payment_child_id') is-invalid @enderror" value="{{ old('payment_child_id') }}" placeholder="Enter Payment Child Id..." id="create_payment_child_id" required>
        @error('payment_child_id')
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
        <label for="allocatable_type">Allocatable Type</label>
        <input type="text" name="allocatable_type" class="form-control @error('allocatable_type') is-invalid @enderror" value="{{ old('allocatable_type') }}" placeholder="Enter Allocatable Type..." id="create_allocatable_type" required>
        @error('allocatable_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="allocatable_id">Allocatable Id</label>
        <input type="text" name="allocatable_id" class="form-control @error('allocatable_id') is-invalid @enderror" value="{{ old('allocatable_id') }}" placeholder="Enter Allocatable Id..." id="create_allocatable_id" required>
        @error('allocatable_id')
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
        <label for="base_amount">Base Amount</label>
        <input type="number" step="any" name="base_amount" min="0" class="form-control @error('base_amount') is-invalid @enderror" value="{{ old('base_amount') }}" placeholder="Enter Base Amount..." id="create_base_amount" required>
        @error('base_amount')
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
        <label for="currency">Currency</label>
        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency') }}" placeholder="Enter Currency..." id="create_currency" required>
        @error('currency')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="allocation_reference">Allocation Reference</label>
        <input type="text" name="allocation_reference" class="form-control @error('allocation_reference') is-invalid @enderror" value="{{ old('allocation_reference') }}" placeholder="Enter Allocation Reference..." id="create_allocation_reference" required>
        @error('allocation_reference')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="allocation_type">Allocation Type</label>
        <input type="text" name="allocation_type" class="form-control @error('allocation_type') is-invalid @enderror" value="{{ old('allocation_type') }}" placeholder="Enter Allocation Type..." id="create_allocation_type" required>
        @error('allocation_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="is_reversed">Is Reversed</label>
        <input type="radio" name="is_reversed" id="create_is_reversed_yes" value="1" {{ old('is_reversed') == 1 ? "checked" : "" }} checked> 
                            <label for="create_is_reversed_yes">Is Reversed Yes</label>
<input type="radio" name="is_reversed" id="create_is_reversed_no" value="0" {{ old('is_reversed') == 0 ? "checked" : "" }}> 
                            <label for="create_is_reversed_no">Is Reversed No</label>
        @error('is_reversed')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="reversed_at">Reversed At</label>
        <input type="datetime-local" name="reversed_at" class="form-control @error('reversed_at') is-invalid @enderror" value="{{ old('reversed_at') }}" id="create_reversed_at" required>
        @error('reversed_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="reversal_id">Reversal Id</label>
        <input type="text" name="reversal_id" class="form-control @error('reversal_id') is-invalid @enderror" value="{{ old('reversal_id') }}" placeholder="Enter Reversal Id..." id="create_reversal_id" required>
        @error('reversal_id')
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
        <label for="notes">Notes</label>
        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter Notes..." id="create_notes" required>{{ old('notes') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-allocations.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>