
            <form method="POST" id="editpayment_childrenForm" action="{{ url('api/v1/payment-children/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_master_id">Payment Master Id</label><br>
                        <input type="text" name="payment_master_id" class="form-control @error('payment_master_id') is-invalid @enderror" value="{{ old('payment_master_id', $query->payment_master_id ?? "") }}" placeholder="Enter Payment Master Id..." id="edit_payment_master_id" required>
                        @error('payment_master_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="item_type">Item Type</label><br>
                        <input type="text" name="item_type" class="form-control @error('item_type') is-invalid @enderror" value="{{ old('item_type', $query->item_type ?? "") }}" placeholder="Enter Item Type..." id="edit_item_type" required>
                        @error('item_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="item_id">Item Id</label><br>
                        <input type="text" name="item_id" class="form-control @error('item_id') is-invalid @enderror" value="{{ old('item_id', $query->item_id ?? "") }}" placeholder="Enter Item Id..." id="edit_item_id" required>
                        @error('item_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="subscription_id">Subscription Id</label><br>
                        <input type="text" name="subscription_id" class="form-control @error('subscription_id') is-invalid @enderror" value="{{ old('subscription_id', $query->subscription_id ?? "") }}" placeholder="Enter Subscription Id..." id="edit_subscription_id" required>
                        @error('subscription_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="plan_id">Plan Id</label><br>
                        <input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror" value="{{ old('plan_id', $query->plan_id ?? "") }}" placeholder="Enter Plan Id..." id="edit_plan_id" required>
                        @error('plan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="invoice_id">Invoice Id</label><br>
                        <input type="text" name="invoice_id" class="form-control @error('invoice_id') is-invalid @enderror" value="{{ old('invoice_id', $query->invoice_id ?? "") }}" placeholder="Enter Invoice Id..." id="edit_invoice_id" required>
                        @error('invoice_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="description">Description</label><br>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter Description..." id="edit_description" required>{{ old('description', $query->description ?? "") }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="item_code">Item Code</label><br>
                        <input type="text" name="item_code" class="form-control @error('item_code') is-invalid @enderror" value="{{ old('item_code', $query->item_code ?? "") }}" placeholder="Enter Item Code..." id="edit_item_code" required>
                        @error('item_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="unit_price">Unit Price</label><br>
                        <input type="number" step="any" name="unit_price" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $query->unit_price ?? "") }}" placeholder="Enter Unit Price..." id="edit_unit_price" required>
                        @error('unit_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="quantity">Quantity</label><br>
                        <input type="number" name="quantity" min="0" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $query->quantity ?? "") }}" placeholder="Enter Quantity..." id="edit_quantity" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="amount">Amount</label><br>
                        <input type="number" step="any" name="amount" min="0" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $query->amount ?? "") }}" placeholder="Enter Amount..." id="edit_amount" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="tax_amount">Tax Amount</label><br>
                        <input type="number" step="any" name="tax_amount" min="0" class="form-control @error('tax_amount') is-invalid @enderror" value="{{ old('tax_amount', $query->tax_amount ?? "") }}" placeholder="Enter Tax Amount..." id="edit_tax_amount" required>
                        @error('tax_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="discount_amount">Discount Amount</label><br>
                        <input type="number" step="any" name="discount_amount" min="0" class="form-control @error('discount_amount') is-invalid @enderror" value="{{ old('discount_amount', $query->discount_amount ?? "") }}" placeholder="Enter Discount Amount..." id="edit_discount_amount" required>
                        @error('discount_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="total_amount">Total Amount</label><br>
                        <input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $query->total_amount ?? "") }}" placeholder="Enter Total Amount..." id="edit_total_amount" required>
                        @error('total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="period_start">Period Start</label><br>
                        <input type="date" name="period_start" class="form-control @error('period_start') is-invalid @enderror" value="{{ old('period_start', $query->period_start ?? "") }}" id="edit_period_start" required>
                        @error('period_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="period_end">Period End</label><br>
                        <input type="date" name="period_end" class="form-control @error('period_end') is-invalid @enderror" value="{{ old('period_end', $query->period_end ?? "") }}" id="edit_period_end" required>
                        @error('period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="billing_cycle">Billing Cycle</label><br>
                        <input type="text" name="billing_cycle" class="form-control @error('billing_cycle') is-invalid @enderror" value="{{ old('billing_cycle', $query->billing_cycle ?? "") }}" placeholder="Enter Billing Cycle..." id="edit_billing_cycle" required>
                        @error('billing_cycle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="status">Status</label><br>
                        
                        <input type="radio" name="status" value="1" {{ old('status', $query->status ?? "") == 1 ? "checked" : "" }} id="edit_status_yes" checked>
                        <label for="edit_status_yes">Status Yes</label>
                        <input type="radio" name="status" value="0" {{ old('status', $query->status ?? "") == 0 ? "checked" : "" }} id="edit_status_no">
                        <label for="edit_status_no">Status No</label>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="paid_at">Paid At</label><br>
                        <input type="datetime-local" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror" value="{{ old('paid_at', $query->paid_at ?? "") }}" id="edit_paid_at" required>
                        @error('paid_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="allocated_amount">Allocated Amount</label><br>
                        <input type="number" step="any" name="allocated_amount" min="0" class="form-control @error('allocated_amount') is-invalid @enderror" value="{{ old('allocated_amount', $query->allocated_amount ?? "") }}" placeholder="Enter Allocated Amount..." id="edit_allocated_amount" required>
                        @error('allocated_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_fully_allocated">Is Fully Allocated</label><br>
                        
                                <input type="radio" name="is_fully_allocated" id="edit_is_fully_allocated_yes" value="1" {{ old('is_fully_allocated', $query->is_fully_allocated ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_fully_allocated_yes">Is Fully Allocated Yes</label>
                                <input type="radio" name="is_fully_allocated" id="edit_is_fully_allocated_no" value="0" {{ old('is_fully_allocated', $query->is_fully_allocated ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_fully_allocated_no">Is Fully Allocated No</label>
                        @error('is_fully_allocated')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="metadata">Metadata</label><br>
                        <input type="text" name="metadata" class="form-control @error('metadata') is-invalid @enderror" value="{{ old('metadata', $query->metadata ?? "") }}" placeholder="Enter Metadata..." id="edit_metadata" required>
                        @error('metadata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="tax_breakdown">Tax Breakdown</label><br>
                        <input type="text" name="tax_breakdown" class="form-control @error('tax_breakdown') is-invalid @enderror" value="{{ old('tax_breakdown', $query->tax_breakdown ?? "") }}" placeholder="Enter Tax Breakdown..." id="edit_tax_breakdown" required>
                        @error('tax_breakdown')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="discount_breakdown">Discount Breakdown</label><br>
                        <input type="text" name="discount_breakdown" class="form-control @error('discount_breakdown') is-invalid @enderror" value="{{ old('discount_breakdown', $query->discount_breakdown ?? "") }}" placeholder="Enter Discount Breakdown..." id="edit_discount_breakdown" required>
                        @error('discount_breakdown')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-children.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>