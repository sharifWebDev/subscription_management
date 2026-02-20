<form><div class="row"><div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_order_id">Subscription Order Id</label><br><input type="text" name="subscription_order_id" class="form-control @error('subscription_order_id') is-invalid @enderror" value="{{ old('subscription_order_id', $query->subscription_order_id ?? "") }}" placeholder="Enter Subscription Order Id..." id="view_subscription_order_id" disabled>@error('subscription_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="plan_id">Plan Id</label><br><input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id', $query->plan_id ?? "") }}" placeholder="Enter Plan Id..." id="view_plan_id" disabled>@error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="user_id">User Id</label><br><input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="view_user_id" disabled>@error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="plan_name">Plan Name</label><br><input type="text" name="plan_name" class="form-control @error('plan_name') is-invalid @enderror" value="{{ old('plan_name', $query->plan_name ?? "") }}" placeholder="Enter Plan Name..." id="view_plan_name" disabled>@error('plan_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="billing_cycle">Billing Cycle</label><br><input type="text" name="billing_cycle" class="form-control @error('billing_cycle') is-invalid @enderror" value="{{ old('billing_cycle', $query->billing_cycle ?? "") }}" placeholder="Enter Billing Cycle..." id="view_billing_cycle" disabled>@error('billing_cycle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label><br><input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $query->quantity ?? "") }}" placeholder="Enter Quantity..." id="view_quantity" disabled>@error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="recipient_user_id">Recipient User Id</label><br><input type="text" name="recipient_user_id" class="form-control @error('recipient_user_id') is-invalid @enderror" value="{{ old('recipient_user_id', $query->recipient_user_id ?? "") }}" placeholder="Enter Recipient User Id..." id="view_recipient_user_id" disabled>@error('recipient_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="recipient_info">Recipient Info</label><br><input type="text" name="recipient_info" class="form-control @error('recipient_info') is-invalid @enderror" value="{{ old('recipient_info', $query->recipient_info ?? "") }}" placeholder="Enter Recipient Info..." id="view_recipient_info" disabled>@error('recipient_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="unit_price">Unit Price</label><br><input type="number" step="any" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $query->unit_price ?? "") }}" placeholder="Enter Unit Price..." id="view_unit_price" disabled>@error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                        <label for="start_date">Start Date</label><br><input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $query->start_date ?? "") }}" placeholder="Enter Start Date..." id="view_start_date" disabled>@error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label><br><input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $query->end_date ?? "") }}" placeholder="Enter End Date..." id="view_end_date" disabled>@error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_id">Subscription Id</label><br><input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="view_subscription_id" disabled>@error('subscription_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="subscription_status">Subscription Status</label><br><input type="text" name="subscription_status" class="form-control @error('subscription_status') is-invalid @enderror" value="{{ old('subscription_status', $query->subscription_status ?? "") }}" placeholder="Enter Subscription Status..." id="view_subscription_status" disabled>@error('subscription_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="processing_error">Processing Error</label><br><textarea name="processing_error" class="form-control @error('processing_error') is-invalid @enderror" placeholder="Enter Processing Error..." id="view_processing_error" disabled>{{ old('processing_error', $query->processing_error ?? "") }}</textarea>@error('processing_error')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div>
<div class="mb-3 col-12 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label for="processed_at">Processed At</label><br><input type="datetime-local" name="processed_at" class="form-control @error('processed_at') is-invalid @enderror" value="{{ old('processed_at', $query->processed_at ?? "") }}" placeholder="Enter Processed At..." id="view_processed_at" disabled>@error('processed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    </div><div class="mb-3 text-right">
                              <a type="button" class="btn bg-danger text-white" href="{{ route('admin.subscription-order-items.index') }}">Close</a>
                          </div>
                      </div>
                  </form>