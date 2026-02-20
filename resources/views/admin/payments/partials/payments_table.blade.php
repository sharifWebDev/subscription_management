<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payments List"
                data-index-url="{{ url('api/v1/payments') }}"
                data-create-url="{{ url('admin/payments/create') }}"
                data-edit-url="{{ route('admin.payments.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payments/destroy') }}/:id"
                data-show-url="{{ route('admin.payments.show', ':id') }}"
                data-fields='["invoice_id", "user_id", "external_id", "type", "status", "amount", "fee", "net", "currency", "gateway", "gateway_response", "payment_method", "processed_at", "refunded_at", "metadata", "fraud_indicators"]'
                data-headers='["Invoice", "User", "External", "Type", "Status", "Amount", "Fee", "Net", "Currency", "Gateway", "Gateway Response", "Payment Method", "Processed At", "Refunded At", "Metadata", "Fraud Indicators"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('Payment', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('Payment', 'status', function(data, row) {
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
    