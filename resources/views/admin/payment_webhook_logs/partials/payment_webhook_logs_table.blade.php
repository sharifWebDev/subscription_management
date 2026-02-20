<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payment Webhook Logs List"
                data-index-url="{{ url('api/v1/payment-webhook-logs') }}"
                data-create-url="{{ url('admin/payment-webhook-logs/create') }}"
                data-edit-url="{{ route('admin.payment-webhook-logs.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payment-webhook-logs/destroy') }}/:id"
                data-show-url="{{ route('admin.payment-webhook-logs.show', ':id') }}"
                data-fields='["payment_gateway_id", "gateway", "event_type", "webhook_id", "reference_id", "payment_transaction_id", "payload", "headers", "response_code", "response_body", "status", "processing_error", "retry_count", "next_retry_at", "received_at", "processed_at", "ip_address", "is_verified", "verification_error"]'
                data-headers='["Payment Gateway", "Gateway", "Event Type", "Webhook", "Reference", "Payment Transaction", "Payload", "Headers", "Response Code", "Response Body", "Status", "Processing Error", "Retry Count", "Next Retry At", "Received At", "Processed At", "Ip Address", "Is Verified", "Verification Error"]'
                data-filters=''
                {{-- data-filters=']' --}}
                data-export="true"
                data-colvis="true"
                data-csv="true"
                data-excel="true"
                data-pdf="true"
                data-print="true">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>

        {{-- <!-- Quick Actions -->
        <div class="mt-3">
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('PaymentWebhookLog', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('PaymentWebhookLog', 'status', function(data, row) {
                    if (data === true || data === 1 || data === '1') {
                        return '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>';
                    } else if (data === false || data === 0 || data === '0') {
                        return '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Inactive</span>';
                    }
                    return '<span class="badge bg-secondary">' + data + '</span>';
                });
            }
        }, 1000);
    </script>
    {{-- @include('components.datatables'); --}}
    