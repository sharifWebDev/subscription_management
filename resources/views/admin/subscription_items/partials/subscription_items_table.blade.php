<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Subscription Items List"
                data-index-url="{{ url('api/v1/subscription-items') }}"
                data-create-url="{{ url('admin/subscription-items/create') }}"
                data-edit-url="{{ route('admin.subscription-items.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/subscription-items/destroy') }}/:id"
                data-show-url="{{ route('admin.subscription-items.show', ':id') }}"
                data-fields='["subscription_id", "plan_price_id", "feature_id", "quantity", "unit_price", "amount", "metadata", "tiers", "effective_from", "effective_to"]'
                data-headers='["Subscription", "Plan Price", "Feature", "Quantity", "Unit Price", "Amount", "Metadata", "Tiers", "Effective From", "Effective To"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('SubscriptionItem', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('SubscriptionItem', 'status', function(data, row) {
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
    