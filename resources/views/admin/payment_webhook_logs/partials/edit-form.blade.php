
            <form method="POST" id="editpayment_webhook_logsForm" action="{{ url('api/v1/payment-webhook-logs/update/' . request()->id ?? "") }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="payment_gateway_id">Payment Gateway Id</label><br>
                        <input type="text" name="payment_gateway_id" class="form-control @error('payment_gateway_id') is-invalid @enderror" value="{{ old('payment_gateway_id', $query->payment_gateway_id ?? "") }}" placeholder="Enter Payment Gateway Id..." id="edit_payment_gateway_id" required>
                        @error('payment_gateway_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="gateway">Gateway</label><br>
                        <input type="text" name="gateway" class="form-control @error('gateway') is-invalid @enderror" value="{{ old('gateway', $query->gateway ?? "") }}" placeholder="Enter Gateway..." id="edit_gateway" required>
                        @error('gateway')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="event_type">Event Type</label><br>
                        <input type="text" name="event_type" class="form-control @error('event_type') is-invalid @enderror" value="{{ old('event_type', $query->event_type ?? "") }}" placeholder="Enter Event Type..." id="edit_event_type" required>
                        @error('event_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="webhook_id">Webhook Id</label><br>
                        <input type="text" name="webhook_id" class="form-control @error('webhook_id') is-invalid @enderror" value="{{ old('webhook_id', $query->webhook_id ?? "") }}" placeholder="Enter Webhook Id..." id="edit_webhook_id" required>
                        @error('webhook_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="reference_id">Reference Id</label><br>
                        <input type="text" name="reference_id" class="form-control @error('reference_id') is-invalid @enderror" value="{{ old('reference_id', $query->reference_id ?? "") }}" placeholder="Enter Reference Id..." id="edit_reference_id" required>
                        @error('reference_id')
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
                        <label for="payload">Payload</label><br>
                        <input type="text" name="payload" class="form-control @error('payload') is-invalid @enderror" value="{{ old('payload', $query->payload ?? "") }}" placeholder="Enter Payload..." id="edit_payload" required>
                        @error('payload')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="headers">Headers</label><br>
                        <input type="text" name="headers" class="form-control @error('headers') is-invalid @enderror" value="{{ old('headers', $query->headers ?? "") }}" placeholder="Enter Headers..." id="edit_headers" required>
                        @error('headers')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="response_code">Response Code</label><br>
                        <input type="number" name="response_code" min="0" class="form-control @error('response_code') is-invalid @enderror" value="{{ old('response_code', $query->response_code ?? "") }}" placeholder="Enter Response Code..." id="edit_response_code" required>
                        @error('response_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="response_body">Response Body</label><br>
                        <textarea name="response_body" class="form-control @error('response_body') is-invalid @enderror" placeholder="Enter Response Body..." id="edit_response_body" required>{{ old('response_body', $query->response_body ?? "") }}</textarea>
                        @error('response_body')
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
                        <label for="processing_error">Processing Error</label><br>
                        <textarea name="processing_error" class="form-control @error('processing_error') is-invalid @enderror" placeholder="Enter Processing Error..." id="edit_processing_error" required>{{ old('processing_error', $query->processing_error ?? "") }}</textarea>
                        @error('processing_error')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="retry_count">Retry Count</label><br>
                        <input type="number" name="retry_count" min="0" class="form-control @error('retry_count') is-invalid @enderror" value="{{ old('retry_count', $query->retry_count ?? "") }}" placeholder="Enter Retry Count..." id="edit_retry_count" required>
                        @error('retry_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="next_retry_at">Next Retry At</label><br>
                        <input type="datetime-local" name="next_retry_at" class="form-control @error('next_retry_at') is-invalid @enderror" value="{{ old('next_retry_at', $query->next_retry_at ?? "") }}" id="edit_next_retry_at" required>
                        @error('next_retry_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="received_at">Received At</label><br>
                        <input type="datetime-local" name="received_at" class="form-control @error('received_at') is-invalid @enderror" value="{{ old('received_at', $query->received_at ?? "") }}" id="edit_received_at" required>
                        @error('received_at')
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
                        <label for="ip_address">Ip Address</label><br>
                        <input type="text" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" value="{{ old('ip_address', $query->ip_address ?? "") }}" placeholder="Enter Ip Address..." id="edit_ip_address" required>
                        @error('ip_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="is_verified">Is Verified</label><br>
                        
                                <input type="radio" name="is_verified" id="edit_is_verified_yes" value="1" {{ old('is_verified', $query->is_verified ?? "") == 1 ? "checked" : "" }} checked>
                                <label for="editis_verified_yes">Is Verified Yes</label>
                                <input type="radio" name="is_verified" id="edit_is_verified_no" value="0" {{ old('is_verified', $query->is_verified ?? "") == 0 ? "checked" : "" }}>
                                <label for="editis_verified_no">Is Verified No</label>
                        @error('is_verified')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 col-12 col-md-4 col-lg-3">
                    <div class="form-group">
                        <label for="verification_error">Verification Error</label><br>
                        <input type="text" name="verification_error" class="form-control @error('verification_error') is-invalid @enderror" value="{{ old('verification_error', $query->verification_error ?? "") }}" placeholder="Enter Verification Error..." id="edit_verification_error" required>
                        @error('verification_error')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 text-right">
                    <a type="button" class="btn bg-danger text-white" href="{{ route('admin.payment-webhook-logs.index') }}">Cancel</a>
                    <button type="submit" id="submitButton" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>