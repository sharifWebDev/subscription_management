<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Payment Children List"
                data-index-url="{{ url('api/v1/payment-children') }}"
                data-create-url="{{ url('admin/payment-children/create') }}"
                data-edit-url="{{ route('admin.payment-children.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/payment-children/destroy') }}/:id"
                data-show-url="{{ route('admin.payment-children.show', ':id') }}"
                data-fields='["payment_master_id", "item_type", "item_id", "subscription_id", "plan_id", "invoice_id", "description", "item_code", "unit_price", "quantity", "amount", "tax_amount", "discount_amount", "total_amount", "period_start", "period_end", "billing_cycle", "status", "paid_at", "allocated_amount", "is_fully_allocated", "metadata", "tax_breakdown", "discount_breakdown"]'
                data-headers='["Payment Master", "Item Type", "Item", "Subscription", "Plan", "Invoice", "Description", "Item Code", "Unit Price", "Quantity", "Amount", "Tax Amount", "Discount Amount", "Total Amount", "Period Start", "Period End", "Billing Cycle", "Status", "Paid At", "Allocated Amount", "Is Fully Allocated", "Metadata", "Tax Breakdown", "Discount Breakdown"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('PaymentChild', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('PaymentChild', 'status', function(data, row) {
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
    