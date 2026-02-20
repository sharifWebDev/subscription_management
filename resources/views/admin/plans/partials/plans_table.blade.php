<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Plans List"
                data-index-url="{{ url('api/v1/plans') }}"
                data-create-url="{{ url('admin/plans/create') }}"
                data-edit-url="{{ route('admin.plans.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/plans/destroy') }}/:id"
                data-show-url="{{ route('admin.plans.show', ':id') }}"
                data-fields='["name", "code", "description", "type", "billing_period", "billing_interval", "is_active", "is_visible", "sort_order", "is_featured", "metadata"]'
                data-headers='["Name", "Code", "Description", "Type", "Billing Period", "Billing Interval", "Is Active", "Is Visible", "Sort Order", "Is Featured", "Metadata"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('Plan', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('Plan', 'status', function(data, row) {
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
    