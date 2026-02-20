<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_id">User Id</label><br><input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="view_user_id" disabled>@error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_id">Subscription Id</label><br><input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="view_subscription_id" disabled>@error('subscription_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="number">Number</label><br><input type="text" name="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $query->number ?? "") }}" placeholder="Enter Number..." id="view_number" disabled>@error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="external_id">External Id</label><br><input type="text" name="external_id" class="form-control @error('external_id') is-invalid @enderror" value="{{ old('external_id', $query->external_id ?? "") }}" placeholder="Enter External Id..." id="view_external_id" disabled>@error('external_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="subtotal">Subtotal</label><br><input type="number" step="any" name="subtotal" min="0" class="form-control @error('subtotal') is-invalid @enderror" value="{{ old('subtotal', $query->subtotal ?? "") }}" placeholder="Enter Subtotal..." id="view_subtotal" disabled>@error('subtotal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tax">Tax</label><br><input type="number" step="any" name="tax" min="0" class="form-control @error('tax') is-invalid @enderror" value="{{ old('tax', $query->tax ?? "") }}" placeholder="Enter Tax..." id="view_tax" disabled>@error('tax')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="total">Total</label><br><input type="number" step="any" name="total" min="0" class="form-control @error('total') is-invalid @enderror" value="{{ old('total', $query->total ?? "") }}" placeholder="Enter Total..." id="view_total" disabled>@error('total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount_due">Amount Due</label><br><input type="number" step="any" name="amount_due" min="0" class="form-control @error('amount_due') is-invalid @enderror" value="{{ old('amount_due', $query->amount_due ?? "") }}" placeholder="Enter Amount Due..." id="view_amount_due" disabled>@error('amount_due')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount_paid">Amount Paid</label><br><input type="number" step="any" name="amount_paid" min="0" class="form-control @error('amount_paid') is-invalid @enderror" value="{{ old('amount_paid', $query->amount_paid ?? "") }}" placeholder="Enter Amount Paid..." id="view_amount_paid" disabled>@error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount_remaining">Amount Remaining</label><br><input type="number" step="any" name="amount_remaining" min="0" class="form-control @error('amount_remaining') is-invalid @enderror" value="{{ old('amount_remaining', $query->amount_remaining ?? "") }}" placeholder="Enter Amount Remaining..." id="view_amount_remaining" disabled>@error('amount_remaining')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="currency">Currency</label><br><input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="view_currency" disabled>@error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="issue_date">Issue Date</label><br><input type="datetime-local" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', $query->issue_date ?? "") }}" placeholder="Enter Issue Date..." id="view_issue_date" disabled>@error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="due_date">Due Date</label><br><input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $query->due_date ?? "") }}" placeholder="Enter Due Date..." id="view_due_date" disabled>@error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="paid_at">Paid At</label><br><input type="datetime-local" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at', $query->paid_at ?? "") }}" placeholder="Enter Paid At..." id="view_paid_at" disabled>@error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="finalized_at">Finalized At</label><br><input type="datetime-local" name="finalized_at" class="form-control @error('finalized_at') is-invalid @enderror" value="{{ old('finalized_at', $query->finalized_at ?? "") }}" placeholder="Enter Finalized At..." id="view_finalized_at" disabled>@error('finalized_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="line_items">Line Items</label><br><input type="text" name="line_items" class="form-control @error('line_items') is-invalid @enderror" value="{{ old('line_items', $query->line_items ?? "") }}" placeholder="Enter Line Items..." id="view_line_items" disabled>@error('line_items')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tax_rates">Tax Rates</label><br><input type="text" name="tax_rates" class="form-control @error('tax_rates') is-invalid @enderror" value="{{ old('tax_rates', $query->tax_rates ?? "") }}" placeholder="Enter Tax Rates..." id="view_tax_rates" disabled>@error('tax_rates')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="discounts">Discounts</label><br><input type="text" name="discounts" class="form-control @error('discounts') is-invalid @enderror" value="{{ old('discounts', $query->discounts ?? "") }}" placeholder="Enter Discounts..." id="view_discounts" disabled>@error('discounts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="history">History</label><br><input type="text" name="history" class="form-control @error('history') is-invalid @enderror" value="{{ old('history', $query->history ?? "") }}" placeholder="Enter History..." id="view_history" disabled>@error('history')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="pdf_url">Pdf Url</label><br><input type="text" name="pdf_url" class="form-control @error('pdf_url') is-invalid @enderror" value="{{ old('pdf_url', $query->pdf_url ?? "") }}" placeholder="Enter Pdf Url..." id="view_pdf_url" disabled>@error('pdf_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.invoices.index') }}">Close</a>
                          </div>
                      </div>
                  </form>