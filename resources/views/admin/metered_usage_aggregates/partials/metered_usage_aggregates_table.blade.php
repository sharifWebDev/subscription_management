<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Metered Usage Aggregates List"
                data-index-url="{{ url('api/v1/metered-usage-aggregates') }}"
                data-create-url="{{ url('admin/metered-usage-aggregates/create') }}"
                data-edit-url="{{ route('admin.metered-usage-aggregates.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/metered-usage-aggregates/destroy') }}/:id"
                data-show-url="{{ route('admin.metered-usage-aggregates.show', ':id') }}"
                data-fields='["subscription_id", "feature_id", "aggregate_date", "aggregate_period", "total_quantity", "tier1_quantity", "tier2_quantity", "tier3_quantity", "total_amount", "record_count", "last_calculated_at"]'
                data-headers='["Subscription", "Feature", "Aggregate Date", "Aggregate Period", "Total Quantity", "Tier1 Quantity", "Tier2 Quantity", "Tier3 Quantity", "Total Amount", "Record Count", "Last Calculated At"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('MeteredUsageAggregate', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('MeteredUsageAggregate', 'status', function(data, row) {
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
    