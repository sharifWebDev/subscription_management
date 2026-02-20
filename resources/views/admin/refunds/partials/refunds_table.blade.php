<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Refunds List"
                data-index-url="{{ url('api/v1/refunds') }}"
                data-create-url="{{ url('admin/refunds/create') }}"
                data-edit-url="{{ route('admin.refunds.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/refunds/destroy') }}/:id"
                data-show-url="{{ route('admin.refunds.show', ':id') }}"
                data-fields='["payment_master_id", "payment_transaction_id", "user_id", "refund_number", "type", "status", "initiated_by", "amount", "fee", "net_amount", "currency", "exchange_rate", "reason", "reason_details", "customer_comments", "requested_at", "approved_at", "approved_by", "processed_at", "completed_at", "failed_at", "gateway_refund_id", "gateway_response", "metadata", "documents", "processed_by", "rejection_reason"]'
                data-headers='["Payment Master", "Payment Transaction", "User", "Refund Number", "Type", "Status", "Initiated By", "Amount", "Fee", "Net Amount", "Currency", "Exchange Rate", "Reason", "Reason Details", "Customer Comments", "Requested At", "Approved At", "Approved By", "Processed At", "Completed At", "Failed At", "Gateway Refund", "Gateway Response", "Metadata", "Documents", "Processed By", "Rejection Reason"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('Refund', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('Refund', 'status', function(data, row) {
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
    