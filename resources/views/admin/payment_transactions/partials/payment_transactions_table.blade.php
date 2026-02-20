<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payment Transactions List"
                data-index-url="{{ url('api/v1/payment-transactions') }}"
                data-create-url="{{ url('admin/payment-transactions/create') }}"
                data-edit-url="{{ route('admin.payment-transactions.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payment-transactions/destroy') }}/:id"
                data-show-url="{{ route('admin.payment-transactions.show', ':id') }}"
                data-fields='["payment_master_id", "payment_child_id", "transaction_id", "reference_id", "type", "payment_method", "payment_gateway", "gateway_response", "payment_method_details", "amount", "fee", "tax", "net_amount", "currency", "exchange_rate", "status", "card_last4", "card_brand", "card_country", "card_exp_month", "card_exp_year", "bank_name", "bank_account_last4", "bank_routing_number", "wallet_type", "wallet_number", "wallet_transaction_id", "installment_number", "total_installments", "initiated_at", "authorized_at", "captured_at", "completed_at", "failed_at", "refunded_at", "fraud_indicators", "risk_score", "requires_review", "metadata", "custom_fields", "notes", "failure_reason", "ip_address", "user_agent", "location_data"]'
                data-headers='["Payment Master", "Payment Child", "Transaction", "Reference", "Type", "Payment Method", "Payment Gateway", "Gateway Response", "Payment Method Details", "Amount", "Fee", "Tax", "Net Amount", "Currency", "Exchange Rate", "Status", "Card Last4", "Card Brand", "Card Country", "Card Exp Month", "Card Exp Year", "Bank Name", "Bank Account Last4", "Bank Routing Number", "Wallet Type", "Wallet Number", "Wallet Transaction", "Installment Number", "Total Installments", "Initiated At", "Authorized At", "Captured At", "Completed At", "Failed At", "Refunded At", "Fraud Indicators", "Risk Score", "Requires Review", "Metadata", "Custom Fields", "Notes", "Failure Reason", "Ip Address", "User Agent", "Location Data"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('PaymentTransaction', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('PaymentTransaction', 'status', function(data, row) {
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
    