<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payment Masters List"
                data-index-url="{{ url('api/v1/payment-masters') }}"
                data-create-url="{{ url('admin/payment-masters/create') }}"
                data-edit-url="{{ route('admin.payment-masters.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payment-masters/destroy') }}/:id"
                data-show-url="{{ route('admin.payment-masters.show', ':id') }}"
                data-fields='["user_id", "payment_number", "type", "status", "total_amount", "subtotal", "tax_amount", "discount_amount", "fee_amount", "net_amount", "paid_amount", "due_amount", "currency", "exchange_rate", "base_currency", "base_amount", "payment_method", "payment_method_details", "payment_gateway", "is_installment", "installment_count", "installment_frequency", "payment_date", "due_date", "paid_at", "cancelled_at", "expires_at", "customer_reference", "bank_reference", "gateway_reference", "metadata", "custom_fields", "notes", "failure_reason"]'
                data-headers='["User", "Payment Number", "Type", "Status", "Total Amount", "Subtotal", "Tax Amount", "Discount Amount", "Fee Amount", "Net Amount", "Paid Amount", "Due Amount", "Currency", "Exchange Rate", "Base Currency", "Base Amount", "Payment Method", "Payment Method Details", "Payment Gateway", "Is Installment", "Installment Count", "Installment Frequency", "Payment Date", "Due Date", "Paid At", "Cancelled At", "Expires At", "Customer Reference", "Bank Reference", "Gateway Reference", "Metadata", "Custom Fields", "Notes", "Failure Reason"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('PaymentMaster', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('PaymentMaster', 'status', function(data, row) {
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
    