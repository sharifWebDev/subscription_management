<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_id">User Id</label><br><input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="view_user_id" disabled>@error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_master_id">Payment Master Id</label><br><input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id', $query->payment_master_id ?? "") }}" placeholder="Enter Payment Master Id..." id="view_payment_master_id" disabled>@error('payment_master_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="order_number">Order Number</label><br><input type="text" name="order_number" class="form-control @error('order_number') is-invalid @enderror" value="{{ old('order_number', $query->order_number ?? "") }}" placeholder="Enter Order Number..." id="view_order_number" disabled>@error('order_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="type">Type</label><br><input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="view_type" disabled>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subtotal">Subtotal</label><br><input type="number" step="any" name="subtotal" min="0" class="form-control @error('subtotal') is-invalid @enderror" value="{{ old('subtotal', $query->subtotal ?? "") }}" placeholder="Enter Subtotal..." id="view_subtotal" disabled>@error('subtotal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tax_amount">Tax Amount</label><br><input type="number" step="any" name="tax_amount" min="0" class="form-control @error('tax_amount') is-invalid @enderror" value="{{ old('tax_amount', $query->tax_amount ?? "") }}" placeholder="Enter Tax Amount..." id="view_tax_amount" disabled>@error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="discount_amount">Discount Amount</label><br><input type="number" step="any" name="discount_amount" min="0" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', $query->discount_amount ?? "") }}" placeholder="Enter Discount Amount..." id="view_discount_amount" disabled>@error('discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="total_amount">Total Amount</label><br><input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $query->total_amount ?? "") }}" placeholder="Enter Total Amount..." id="view_total_amount" disabled>@error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="currency">Currency</label><br><input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="view_currency" disabled>@error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="customer_info">Customer Info</label><br><input type="text" name="customer_info" class="form-control @error('customer_info') is-invalid @enderror" value="{{ old('customer_info', $query->customer_info ?? "") }}" placeholder="Enter Customer Info..." id="view_customer_info" disabled>@error('customer_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="billing_address">Billing Address</label><br><input type="text" name="billing_address" class="form-control @error('billing_address') is-invalid @enderror" value="{{ old('billing_address', $query->billing_address ?? "") }}" placeholder="Enter Billing Address..." id="view_billing_address" disabled>@error('billing_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="ordered_at">Ordered At</label><br><input type="datetime-local" name="ordered_at" class="form-control @error('ordered_at') is-invalid @enderror" value="{{ old('ordered_at', $query->ordered_at ?? "") }}" placeholder="Enter Ordered At..." id="view_ordered_at" disabled>@error('ordered_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="processed_at">Processed At</label><br><input type="datetime-local" name="processed_at" class="form-control @error('processed_at') is-invalid @enderror" value="{{ old('processed_at', $query->processed_at ?? "") }}" placeholder="Enter Processed At..." id="view_processed_at" disabled>@error('processed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="cancelled_at">Cancelled At</label><br><input type="datetime-local" name="cancelled_at" class="form-control @error('cancelled_at') is-invalid @enderror" value="{{ old('cancelled_at', $query->cancelled_at ?? "") }}" placeholder="Enter Cancelled At..." id="view_cancelled_at" disabled>@error('cancelled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="coupon_code">Coupon Code</label><br><input type="text" name="coupon_code" class="form-control @error('coupon_code') is-invalid @enderror" value="{{ old('coupon_code', $query->coupon_code ?? "") }}" placeholder="Enter Coupon Code..." id="view_coupon_code" disabled>@error('coupon_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="applied_discounts">Applied Discounts</label><br><input type="text" name="applied_discounts" class="form-control @error('applied_discounts') is-invalid @enderror" value="{{ old('applied_discounts', $query->applied_discounts ?? "") }}" placeholder="Enter Applied Discounts..." id="view_applied_discounts" disabled>@error('applied_discounts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscription-orders.index') }}">Close</a>
                          </div>
                      </div>
                  </form>