<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Subscriptions List"
                data-index-url="{{ url('api/v1/subscriptions') }}"
                data-create-url="{{ url('admin/subscriptions/create') }}"
                data-edit-url="{{ route('admin.subscriptions.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/subscriptions/destroy') }}/:id"
                data-show-url="{{ route('admin.subscriptions.show', ':id') }}"
                data-fields='["user_id", "plan_id", "plan_price_id", "parent_subscription_id", "status", "billing_cycle_anchor", "quantity", "unit_price", "amount", "currency", "trial_starts_at", "trial_ends_at", "trial_converted", "current_period_starts_at", "current_period_ends_at", "billing_cycle_anchor_date", "canceled_at", "cancellation_reason", "prorate", "proration_amount", "proration_date", "gateway", "gateway_subscription_id", "gateway_customer_id", "gateway_metadata", "metadata", "history", "is_active"]'
                data-headers='["User", "Plan", "Plan Price", "Parent Subscription", "Status", "Billing Cycle Anchor", "Quantity", "Unit Price", "Amount", "Currency", "Trial Starts At", "Trial Ends At", "Trial Converted", "Current Period Starts At", "Current Period Ends At", "Billing Cycle Anchor Date", "Canceled At", "Cancellation Reason", "Prorate", "Proration Amount", "Proration Date", "Gateway", "Gateway Subscription", "Gateway Customer", "Gateway Metadata", "Metadata", "History", "Is Active"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('Subscription', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('Subscription', 'status', function(data, row) {
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
    