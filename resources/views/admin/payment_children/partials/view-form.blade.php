<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="payment_master_id">Payment Master Id</label><br><input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id', $query->payment_master_id ?? "") }}" placeholder="Enter Payment Master Id..." id="view_payment_master_id" disabled>@error('payment_master_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="item_type">Item Type</label><br><input type="text" name="item_type" class="form-control @error('item_type') is-invalid @enderror" value="{{ old('item_type', $query->item_type ?? "") }}" placeholder="Enter Item Type..." id="view_item_type" disabled>@error('item_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="item_id">Item Id</label><br><input type="text" name="item_id" class="form-control @error('item_id') is-invalid @enderror" value="{{ old('item_id', $query->item_id ?? "") }}" placeholder="Enter Item Id..." id="view_item_id" disabled>@error('item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_id">Subscription Id</label><br><input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="view_subscription_id" disabled>@error('subscription_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="plan_id">Plan Id</label><br><input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id', $query->plan_id ?? "") }}" placeholder="Enter Plan Id..." id="view_plan_id" disabled>@error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="invoice_id">Invoice Id</label><br><input type="text" name="invoice_id" class="form-control @error('invoice_id') is-invalid @enderror" value="{{ old('invoice_id', $query->invoice_id ?? "") }}" placeholder="Enter Invoice Id..." id="view_invoice_id" disabled>@error('invoice_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="description">Description</label><br><textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="view_description" disabled>{{ old('description', $query->description ?? "") }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="item_code">Item Code</label><br><input type="text" name="item_code" class="form-control @error('item_code') is-invalid @enderror" value="{{ old('item_code', $query->item_code ?? "") }}" placeholder="Enter Item Code..." id="view_item_code" disabled>@error('item_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="unit_price">Unit Price</label><br><input type="number" step="any" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $query->unit_price ?? "") }}" placeholder="Enter Unit Price..." id="view_unit_price" disabled>@error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label><br><input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $query->quantity ?? "") }}" placeholder="Enter Quantity..." id="view_quantity" disabled>@error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="amount">Amount</label><br><input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $query->amount ?? "") }}" placeholder="Enter Amount..." id="view_amount" disabled>@error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="period_start">Period Start</label><br><input type="date" name="period_start" class="form-control @error('period_start') is-invalid @enderror" value="{{ old('period_start', $query->period_start ?? "") }}" placeholder="Enter Period Start..." id="view_period_start" disabled>@error('period_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="period_end">Period End</label><br><input type="date" name="period_end" class="form-control @error('period_end') is-invalid @enderror" value="{{ old('period_end', $query->period_end ?? "") }}" placeholder="Enter Period End..." id="view_period_end" disabled>@error('period_end')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="billing_cycle">Billing Cycle</label><br><input type="text" name="billing_cycle" class="form-control @error('billing_cycle') is-invalid @enderror" value="{{ old('billing_cycle', $query->billing_cycle ?? "") }}" placeholder="Enter Billing Cycle..." id="view_billing_cycle" disabled>@error('billing_cycle')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="paid_at">Paid At</label><br><input type="datetime-local" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at', $query->paid_at ?? "") }}" placeholder="Enter Paid At..." id="view_paid_at" disabled>@error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="allocated_amount">Allocated Amount</label><br><input type="number" step="any" name="allocated_amount" min="0" class="form-control @error('allocated_amount') is-invalid @enderror" value="{{ old('allocated_amount', $query->allocated_amount ?? "") }}" placeholder="Enter Allocated Amount..." id="view_allocated_amount" disabled>@error('allocated_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="is_fully_allocated">Is Fully Allocated</label><br><input type="radio" name="is_fully_allocated" id="view_is_fully_allocated_yes" value="1" {{ old('is_fully_allocated', $query->is_fully_allocated ?? "") == 1 ? "checked" : "" }} disabled> 
                                           <label for="editis_fully_allocated_yes" disabled>Is Fully Allocated Yes </label>
                                           <input type="radio" name="is_fully_allocated" id="view_is_fully_allocated_no" value="0" {{ old('is_fully_allocated', $query->is_fully_allocated ?? "") == 0 ? "checked" : "" }} disabled> 
                                           <label for="editis_fully_allocated_no" disabled>Is Fully Allocated No </label>@error('is_fully_allocated')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="metadata">Metadata</label><br><input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="view_metadata" disabled>@error('metadata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="tax_breakdown">Tax Breakdown</label><br><input type="text" name="tax_breakdown" class="form-control @error('tax_breakdown') is-invalid @enderror" value="{{ old('tax_breakdown', $query->tax_breakdown ?? "") }}" placeholder="Enter Tax Breakdown..." id="view_tax_breakdown" disabled>@error('tax_breakdown')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="discount_breakdown">Discount Breakdown</label><br><input type="text" name="discount_breakdown" class="form-control @error('discount_breakdown') is-invalid @enderror" value="{{ old('discount_breakdown', $query->discount_breakdown ?? "") }}" placeholder="Enter Discount Breakdown..." id="view_discount_breakdown" disabled>@error('discount_breakdown')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-children.index') }}">Close</a>
                          </div>
                      </div>
                  </form>