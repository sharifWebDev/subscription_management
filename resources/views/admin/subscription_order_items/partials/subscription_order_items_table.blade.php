<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Subscription Order Items List"
                data-index-url="{{ url('api/v1/subscription-order-items') }}"
                data-create-url="{{ url('admin/subscription-order-items/create') }}"
                data-edit-url="{{ route('admin.subscription-order-items.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/subscription-order-items/destroy') }}/:id"
                data-show-url="{{ route('admin.subscription-order-items.show', ':id') }}"
                data-fields='["subscription_order_id", "plan_id", "user_id", "plan_name", "billing_cycle", "quantity", "recipient_user_id", "recipient_info", "unit_price", "amount", "tax_amount", "discount_amount", "total_amount", "start_date", "end_date", "subscription_id", "subscription_status", "processing_error", "processed_at"]'
                data-headers='["Subscription Order", "Plan", "User", "Plan Name", "Billing Cycle", "Quantity", "Recipient User", "Recipient Info", "Unit Price", "Amount", "Tax Amount", "Discount Amount", "Total Amount", "Start Date", "End Date", "Subscription", "Subscription Status", "Processing Error", "Processed At"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('SubscriptionOrderItem', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('SubscriptionOrderItem', 'status', function(data, row) {
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
    