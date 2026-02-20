
            <form method="POST" id="editpayment_mastersForm" action="{{ url('api/v1/payment-masters/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="user_id">User Id</label><br>
                        <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror" value="{{ old('user_id', $query->user_id ?? "") }}" placeholder="Enter User Id..." id="edit_user_id" required>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_number">Payment Number</label><br>
                        <input type="text" name="payment_number" class="form-control @error('payment_number') is-invalid @enderror" value="{{ old('payment_number', $query->payment_number ?? "") }}" placeholder="Enter Payment Number..." id="edit_payment_number" required>
                        @error('payment_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="type">Type</label><br>
                        <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $query->type ?? "") }}" placeholder="Enter Type..." id="edit_type" required>
                        @error('type')
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
                        <label for="total_amount">Total Amount</label><br>
                        <input type="number" step="any" name="total_amount" min="0" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount', $query->total_amount ?? "") }}" placeholder="Enter Total Amount..." id="edit_total_amount" required>
                        @error('total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="subtotal">Subtotal</label><br>
                        <input type="number" step="any" name="subtotal" min="0" class="form-control @error('subtotal') is-invalid @enderror" value="{{ old('subtotal', $query->subtotal ?? "") }}" placeholder="Enter Subtotal..." id="edit_subtotal" required>
                        @error('subtotal')
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
                        <label for="fee_amount">Fee Amount</label><br>
                        <input type="number" step="any" name="fee_amount" min="0" class="form-control @error('fee_amount') is-invalid @enderror" value="{{ old('fee_amount', $query->fee_amount ?? "") }}" placeholder="Enter Fee Amount..." id="edit_fee_amount" required>
                        @error('fee_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="net_amount">Net Amount</label><br>
                        <input type="number" step="any" name="net_amount" min="0" class="form-control @error('net_amount') is-invalid @enderror" value="{{ old('net_amount', $query->net_amount ?? "") }}" placeholder="Enter Net Amount..." id="edit_net_amount" required>
                        @error('net_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="paid_amount">Paid Amount</label><br>
                        <input type="number" step="any" name="paid_amount" min="0" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', $query->paid_amount ?? "") }}" placeholder="Enter Paid Amount..." id="edit_paid_amount" required>
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="due_amount">Due Amount</label><br>
                        <input type="number" step="any" name="due_amount" min="0" class="form-control @error('due_amount') is-invalid @enderror" value="{{ old('due_amount', $query->due_amount ?? "") }}" placeholder="Enter Due Amount..." id="edit_due_amount" required>
                        @error('due_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="currency">Currency</label><br>
                        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $query->currency ?? "") }}" placeholder="Enter Currency..." id="edit_currency" required>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="exchange_rate">Exchange Rate</label><br>
                        <input type="number" step="any" name="exchange_rate" min="0" class="form-control @error('exchange_rate') is-invalid @enderror" value="{{ old('exchange_rate', $query->exchange_rate ?? "") }}" placeholder="Enter Exchange Rate..." id="edit_exchange_rate" required>
                        @error('exchange_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="base_currency">Base Currency</label><br>
                        <input type="text" name="base_currency" class="form-control @error('base_currency') is-invalid @enderror" value="{{ old('base_currency', $query->base_currency ?? "") }}" placeholder="Enter Base Currency..." id="edit_base_currency" required>
                        @error('base_currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="base_amount">Base Amount</label><br>
                        <input type="number" step="any" name="base_amount" min="0" class="form-control @error('base_amount') is-invalid @enderror" value="{{ old('base_amount', $query->base_amount ?? "") }}" placeholder="Enter Base Amount..." id="edit_base_amount" required>
                        @error('base_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label><br>
                        <input type="text" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" value="{{ old('payment_method', $query->payment_method ?? "") }}" placeholder="Enter Payment Method..." id="edit_payment_method" required>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_method_details">Payment Method Details</label><br>
                        <input type="text" name="payment_method_details" class="form-control @error('payment_method_details') is-invalid @enderror" value="{{ old('payment_method_details', $query->payment_method_details ?? "") }}" placeholder="Enter Payment Method Details..." id="edit_payment_method_details" required>
                        @error('payment_method_details')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_gateway">Payment Gateway</label><br>
                        <input type="text" name="payment_gateway" class="form-control @error('payment_gateway') is-invalid @enderror" value="{{ old('payment_gateway', $query->payment_gateway ?? "") }}" placeholder="Enter Payment Gateway..." id="edit_payment_gateway" required>
                        @error('payment_gateway')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_installment">Is Installment</label><br>
                        
                                <input type="radio" name="is_installment" id="edit_is_installment_yes" value="1" {{ old('is_installment', $query->is_installment ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_installment_yes">Is Installment Yes</label>
                                <input type="radio" name="is_installment" id="edit_is_installment_no" value="0" {{ old('is_installment', $query->is_installment ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_installment_no">Is Installment No</label>
                        @error('is_installment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="installment_count">Installment Count</label><br>
                        <input type="number" name="installment_count" min="0" class="form-control @error('installment_count') is-invalid @enderror" value="{{ old('installment_count', $query->installment_count ?? "") }}" placeholder="Enter Installment Count..." id="edit_installment_count" required>
                        @error('installment_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="installment_frequency">Installment Frequency</label><br>
                        <input type="text" name="installment_frequency" class="form-control @error('installment_frequency') is-invalid @enderror" value="{{ old('installment_frequency', $query->installment_frequency ?? "") }}" placeholder="Enter Installment Frequency..." id="edit_installment_frequency" required>
                        @error('installment_frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label><br>
                        <input type="datetime-local" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $query->payment_date ?? "") }}" id="edit_payment_date" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="due_date">Due Date</label><br>
                        <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $query->due_date ?? "") }}" id="edit_due_date" required>
                        @error('due_date')
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
                        <label for="cancelled_at">Cancelled At</label><br>
                        <input type="datetime-local" name="cancelled_at" class="form-control @error('cancelled_at') is-invalid @enderror" value="{{ old('cancelled_at', $query->cancelled_at ?? "") }}" id="edit_cancelled_at" required>
                        @error('cancelled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="expires_at">Expires At</label><br>
                        <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at', $query->expires_at ?? "") }}" id="edit_expires_at" required>
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="customer_reference">Customer Reference</label><br>
                        <input type="text" name="customer_reference" class="form-control @error('customer_reference') is-invalid @enderror" value="{{ old('customer_reference', $query->customer_reference ?? "") }}" placeholder="Enter Customer Reference..." id="edit_customer_reference" required>
                        @error('customer_reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="bank_reference">Bank Reference</label><br>
                        <input type="text" name="bank_reference" class="form-control @error('bank_reference') is-invalid @enderror" value="{{ old('bank_reference', $query->bank_reference ?? "") }}" placeholder="Enter Bank Reference..." id="edit_bank_reference" required>
                        @error('bank_reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_reference">Gateway Reference</label><br>
                        <input type="text" name="gateway_reference" class="form-control @error('gateway_reference') is-invalid @enderror" value="{{ old('gateway_reference', $query->gateway_reference ?? "") }}" placeholder="Enter Gateway Reference..." id="edit_gateway_reference" required>
                        @error('gateway_reference')
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
                        <label for="custom_fields">Custom Fields</label><br>
                        <input type="text" name="custom_fields" class="form-control @error('custom_fields') is-invalid @enderror" value="{{ old('custom_fields', $query->custom_fields ?? "") }}" placeholder="Enter Custom Fields..." id="edit_custom_fields" required>
                        @error('custom_fields')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="notes">Notes</label><br>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Enter Notes..." id="edit_notes" required>{{ old('notes', $query->notes ?? "") }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="failure_reason">Failure Reason</label><br>
                        <textarea name="failure_reason" class="form-control @error('failure_reason') is-invalid @enderror" placeholder="Enter Failure Reason..." id="edit_failure_reason" required>{{ old('failure_reason', $query->failure_reason ?? "") }}</textarea>
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