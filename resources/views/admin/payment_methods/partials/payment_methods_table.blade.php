<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payment Methods List"
                data-index-url="{{ url('api/v1/payment-methods') }}"
                data-create-url="{{ url('admin/payment-methods/create') }}"
                data-edit-url="{{ route('admin.payment-methods.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payment-methods/destroy') }}/:id"
                data-show-url="{{ route('admin.payment-methods.show', ':id') }}"
                data-fields='["user_id", "type", "gateway", "gateway_customer_id", "gateway_payment_method_id", "nickname", "is_default", "is_verified", "card_last4", "card_brand", "card_exp_month", "card_exp_year", "card_country", "bank_name", "bank_account_last4", "bank_account_type", "bank_routing_number", "wallet_type", "wallet_number", "crypto_currency", "crypto_address", "encrypted_data", "fingerprint", "is_compromised", "metadata", "gateway_metadata", "verified_at", "verified_by", "last_used_at", "usage_count"]'
                data-headers='["User", "Type", "Gateway", "Gateway Customer", "Gateway Payment Method", "Nickname", "Is Default", "Is Verified", "Card Last4", "Card Brand", "Card Exp Month", "Card Exp Year", "Card Country", "Bank Name", "Bank Account Last4", "Bank Account Type", "Bank Routing Number", "Wallet Type", "Wallet Number", "Crypto Currency", "Crypto Address", "Encrypted Data", "Fingerprint", "Is Compromised", "Metadata", "Gateway Metadata", "Verified At", "Verified By", "Last Used At", "Usage Count"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('PaymentMethod', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('PaymentMethod', 'status', function(data, row) {
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
    