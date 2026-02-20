<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payment Gateways List"
                data-index-url="{{ url('api/v1/payment-gateways') }}"
                data-create-url="{{ url('admin/payment-gateways/create') }}"
                data-edit-url="{{ route('admin.payment-gateways.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payment-gateways/destroy') }}/:id"
                data-show-url="{{ route('admin.payment-gateways.show', ':id') }}"
                data-fields='["name", "code", "type", "is_active", "is_test_mode", "supports_recurring", "supports_refunds", "supports_installments", "api_key", "api_secret", "webhook_secret", "merchant_id", "store_id", "store_password", "base_url", "callback_url", "webhook_url", "supported_currencies", "supported_countries", "excluded_countries", "percentage_fee", "fixed_fee", "fee_currency", "fee_structure", "config", "metadata", "settlement_days", "refund_days", "min_amount", "max_amount"]'
                data-headers='["Name", "Code", "Type", "Is Active", "Is Test Mode", "Supports Recurring", "Supports Refunds", "Supports Installments", "Api Key", "Api Secret", "Webhook Secret", "Merchant", "Store", "Store Password", "Base Url", "Callback Url", "Webhook Url", "Supported Currencies", "Supported Countries", "Excluded Countries", "Percentage Fee", "Fixed Fee", "Fee Currency", "Fee Structure", "Config", "Metadata", "Settlement Days", "Refund Days", "Min Amount", "Max Amount"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('PaymentGateway', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('PaymentGateway', 'status', function(data, row) {
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
    