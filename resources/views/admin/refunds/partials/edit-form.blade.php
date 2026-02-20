
            <form method="POST" id="editrefundsForm" action="{{ url('api/v1/refunds/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
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
                        <label for="payment_transaction_id">Payment Transaction Id</label><br>
                        <input type="text" name="payment_transaction_id" class="form-control @error('payment_transaction_id') is-invalid @enderror" value="{{ old('payment_transaction_id', $query->payment_transaction_id ?? "") }}" placeholder="Enter Payment Transaction Id..." id="edit_payment_transaction_id" required>
                        @error('payment_transaction_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

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
                        <label for="refund_number">Refund Number</label><br>
                        <input type="text" name="refund_number" class="form-control @error('refund_number') is-invalid @enderror" value="{{ old('refund_number', $query->refund_number ?? "") }}" placeholder="Enter Refund Number..." id="edit_refund_number" required>
                        @error('refund_number')
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
                        <label for="initiated_by">Initiated By</label><br>
                        <input type="text" name="initiated_by" class="form-control @error('initiated_by') is-invalid @enderror" value="{{ old('initiated_by', $query->initiated_by ?? "") }}" placeholder="Enter Initiated By..." id="edit_initiated_by" required>
                        @error('initiated_by')
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
                        <label for="fee">Fee</label><br>
                        <input type="number" step="any" name="fee" min="0" class="form-control @error('fee') is-invalid @enderror" value="{{ old('fee', $query->fee ?? "") }}" placeholder="Enter Fee..." id="edit_fee" required>
                        @error('fee')
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
                        <label for="reason">Reason</label><br>
                        <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason', $query->reason ?? "") }}" placeholder="Enter Reason..." id="edit_reason" required>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="reason_details">Reason Details</label><br>
                        <textarea name="reason_details" class="form-control @error('reason_details') is-invalid @enderror" placeholder="Enter Reason Details..." id="edit_reason_details" required>{{ old('reason_details', $query->reason_details ?? "") }}</textarea>
                        @error('reason_details')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="customer_comments">Customer Comments</label><br>
                        <textarea name="customer_comments" class="form-control @error('customer_comments') is-invalid @enderror" placeholder="Enter Customer Comments..." id="edit_customer_comments" required>{{ old('customer_comments', $query->customer_comments ?? "") }}</textarea>
                        @error('customer_comments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="requested_at">Requested At</label><br>
                        <input type="datetime-local" name="requested_at" class="form-control @error('requested_at') is-invalid @enderror" value="{{ old('requested_at', $query->requested_at ?? "") }}" id="edit_requested_at" required>
                        @error('requested_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="approved_at">Approved At</label><br>
                        <input type="datetime-local" name="approved_at" class="form-control @error('approved_at') is-invalid @enderror" value="{{ old('approved_at', $query->approved_at ?? "") }}" id="edit_approved_at" required>
                        @error('approved_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="approved_by">Approved By</label><br>
                        <input type="text" name="approved_by" class="form-control @error('approved_by') is-invalid @enderror" value="{{ old('approved_by', $query->approved_by ?? "") }}" placeholder="Enter Approved By..." id="edit_approved_by" required>
                        @error('approved_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="processed_at">Processed At</label><br>
                        <input type="datetime-local" name="processed_at" class="form-control @error('processed_at') is-invalid @enderror" value="{{ old('processed_at', $query->processed_at ?? "") }}" id="edit_processed_at" required>
                        @error('processed_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="completed_at">Completed At</label><br>
                        <input type="datetime-local" name="completed_at" class="form-control @error('completed_at') is-invalid @enderror" value="{{ old('completed_at', $query->completed_at ?? "") }}" id="edit_completed_at" required>
                        @error('completed_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="failed_at">Failed At</label><br>
                        <input type="datetime-local" name="failed_at" class="form-control @error('failed_at') is-invalid @enderror" value="{{ old('failed_at', $query->failed_at ?? "") }}" id="edit_failed_at" required>
                        @error('failed_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_refund_id">Gateway Refund Id</label><br>
                        <input type="text" name="gateway_refund_id" class="form-control @error('gateway_refund_id') is-invalid @enderror" value="{{ old('gateway_refund_id', $query->gateway_refund_id ?? "") }}" placeholder="Enter Gateway Refund Id..." id="edit_gateway_refund_id" required>
                        @error('gateway_refund_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway_response">Gateway Response</label><br>
                        <input type="text" name="gateway_response" class="form-control @error('gateway_response') is-invalid @enderror" value="{{ old('gateway_response', $query->gateway_response ?? "") }}" placeholder="Enter Gateway Response..." id="edit_gateway_response" required>
                        @error('gateway_response')
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
                        <label for="documents">Documents</label><br>
                        <input type="text" name="documents" class="form-control @error('documents') is-invalid @enderror" value="{{ old('documents', $query->documents ?? "") }}" placeholder="Enter Documents..." id="edit_documents" required>
                        @error('documents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="processed_by">Processed By</label><br>
                        <input type="text" name="processed_by" class="form-control @error('processed_by') is-invalid @enderror" value="{{ old('processed_by', $query->processed_by ?? "") }}" placeholder="Enter Processed By..." id="edit_processed_by" required>
                        @error('processed_by')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason</label><br>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" placeholder="Enter Rejection Reason..." id="edit_rejection_reason" required>{{ old('rejection_reason', $query->rejection_reason ?? "") }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.refunds.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>