<div class="container m-0 p-0">
        <!-- Modern Table with Export & Column Visibility -->
         <div class="dt-container m-0 p-0">
            <table id="leadType" class="table table-modern"
                data-dt
                data-title="Invoices List"
                data-index-url="{{ url('api/v1/invoices') }}"
                data-create-url="{{ url('admin/invoices/create') }}"
                data-edit-url="{{ route('admin.invoices.edit', ':id') }}"
                data-delete-url="{{ url('api/v1/invoices/destroy') }}/:id"
                data-show-url="{{ route('admin.invoices.show', ':id') }}"
                data-fields='["user_id", "subscription_id", "number", "external_id", "type", "status", "subtotal", "tax", "total", "amount_due", "amount_paid", "amount_remaining", "currency", "issue_date", "due_date", "paid_at", "finalized_at", "line_items", "tax_rates", "discounts", "metadata", "history", "pdf_url"]'
                data-headers='["User", "Subscription", "Number", "External", "Type", "Status", "Subtotal", "Tax", "Total", "Amount Due", "Amount Paid", "Amount Remaining", "Currency", "Issue Date", "Due Date", "Paid At", "Finalized At", "Line Items", "Tax Rates", "Discounts", "Metadata", "History", "Pdf Url"]'
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
            <button class="btn btn-primary me-2" onclick="dtHelpers.exportData('Invoice', 'excel')">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </button>
        </div> --}}
    </div>


    <script>
       // Custom status renderer for boolean status values
        setTimeout(() => {
            if (window.dtHelpers) {
                dtHelpers.registerRenderer('Invoice', 'status', function(data, row) {
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
    