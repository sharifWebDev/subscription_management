<form method="POST" id="createinvoicesForm" action="{{ url('api/v1/invoices') }}" enctype="multipart/form-data">
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
        <label for="subscription_id">Subscription Id</label>
        <input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id') }}" placeholder="Enter Subscription Id..." id="create_subscription_id" required>
        @error('subscription_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="number">Number</label>
        <input type="text" name="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}" placeholder="Enter Number..." id="create_number" required>
        @error('number')
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
        <label for="subtotal">Subtotal</label>
        <input type="number" step="any" name="subtotal" min="0" class="form-control @error('subtotal') is-invalid @enderror" value="{{ old('subtotal') }}" placeholder="Enter Subtotal..." id="create_subtotal" required>
        @error('subtotal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tax">Tax</label>
        <input type="number" step="any" name="tax" min="0" class="form-control @error('tax') is-invalid @enderror" value="{{ old('tax') }}" placeholder="Enter Tax..." id="create_tax" required>
        @error('tax')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="total">Total</label>
        <input type="number" step="any" name="total" min="0" class="form-control @error('total') is-invalid @enderror" value="{{ old('total') }}" placeholder="Enter Total..." id="create_total" required>
        @error('total')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="amount_due">Amount Due</label>
        <input type="number" step="any" name="amount_due" min="0" class="form-control @error('amount_due') is-invalid @enderror" value="{{ old('amount_due') }}" placeholder="Enter Amount Due..." id="create_amount_due" required>
        @error('amount_due')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="amount_paid">Amount Paid</label>
        <input type="number" step="any" name="amount_paid" min="0" class="form-control @error('amount_paid') is-invalid @enderror" value="{{ old('amount_paid') }}" placeholder="Enter Amount Paid..." id="create_amount_paid" required>
        @error('amount_paid')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="amount_remaining">Amount Remaining</label>
        <input type="number" step="any" name="amount_remaining" min="0" class="form-control @error('amount_remaining') is-invalid @enderror" value="{{ old('amount_remaining') }}" placeholder="Enter Amount Remaining..." id="create_amount_remaining" required>
        @error('amount_remaining')
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
        <label for="issue_date">Issue Date</label>
        <input type="datetime-local" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date') }}" id="create_issue_date" required>
        @error('issue_date')
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
        <label for="finalized_at">Finalized At</label>
        <input type="datetime-local" name="finalized_at" class="form-control @error('finalized_at') is-invalid @enderror" value="{{ old('finalized_at') }}" id="create_finalized_at" required>
        @error('finalized_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="line_items">Line Items</label>
        <input type="text" name="line_items" class="form-control @error('line_items') is-invalid @enderror" value="{{ old('line_items') }}" placeholder="Enter Line Items..." id="create_line_items" required>
        @error('line_items')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="tax_rates">Tax Rates</label>
        <input type="text" name="tax_rates" class="form-control @error('tax_rates') is-invalid @enderror" value="{{ old('tax_rates') }}" placeholder="Enter Tax Rates..." id="create_tax_rates" required>
        @error('tax_rates')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="discounts">Discounts</label>
        <input type="text" name="discounts" class="form-control @error('discounts') is-invalid @enderror" value="{{ old('discounts') }}" placeholder="Enter Discounts..." id="create_discounts" required>
        @error('discounts')
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
        <label for="history">History</label>
        <input type="text" name="history" class="form-control @error('history') is-invalid @enderror" value="{{ old('history') }}" placeholder="Enter History..." id="create_history" required>
        @error('history')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
    <div class="form-group">
        <label for="pdf_url">Pdf Url</label>
        <input type="text" name="pdf_url" class="form-control @error('pdf_url') is-invalid @enderror" value="{{ old('pdf_url') }}" placeholder="Enter Pdf Url..." id="create_pdf_url" required>
        @error('pdf_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
        <div class="mb-3 text-right">
            <a type="button" class="btn bg-danger text-white" href="{{ route('admin.invoices.index') }}">Cancel</a>
            <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>