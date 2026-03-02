@extends('website.layouts.app')

@section('title', 'My Invoices')

@section('content')
    <!-- Main Content -->
    <div class="col-lg-9">
        <div class="pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="p mb-0"> <i class="fas fa-home me-2"></i> Home
                    <i class="fas fa-chevron-right mx-2 text-muted small"></i>
                    <span class="text-muted small"> Invoices</span>
                </span>
                <a href="{{ route('website.plans.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-plus me-2"></i> Subscribe to New Plan
                </a>
            </div>
        </div>
        <!-- Summary Cards -->
        <div class="row g-4 mb-4" id="summaryCards">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title text-white-50">Total Paid</h6>
                        <h3 class="mb-0" id="totalPaid">$0.00</h3>
                        <small>All time</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title text-white-50">This Month</h6>
                        <h3 class="mb-0" id="thisMonthTotal">$0.00</h3>
                        <small>{{ date('F Y') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title text-white-50">Outstanding</h6>
                        <h3 class="mb-0" id="outstandingTotal">$0.00</h3>
                        <small>Pending payments</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="invoicesLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading your invoices...</p>
        </div>

        <!-- Invoices Content -->
        <div id="invoicesContent" style="display: none;">
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="open">Open</option>
                                <option value="draft">Draft</option>
                                <option value="void">Void</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="filterFromDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="filterToDate">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="applyFilters">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Invoice History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span id="paginationInfo"></span>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary btn-sm me-2" id="prevPage" disabled>
                                <i class="fas fa-chevron-left"></i> Previous
                            </button>
                            <span id="currentPage">Page 1</span>
                            <button class="btn btn-outline-primary btn-sm ms-2" id="nextPage" disabled>
                                Next <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Invoices Message -->
        <div id="noInvoicesMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
            <h3>No Invoices Yet</h3>
            <p class="text-muted mb-4">Your invoices will appear here once you subscribe to a plan.</p>
            <a href="{{ route('website.plans.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Browse Plans
            </a>
        </div>
    </div>

    <!-- Invoice Details Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Invoice Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="invoiceDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="downloadInvoiceBtn">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let lastPage = 1;
            let filters = {
                status: '',
                from_date: '',
                to_date: ''
            };

            loadInvoices();

            // Apply filters
            $('#applyFilters').click(function() {
                filters.status = $('#filterStatus').val();
                filters.from_date = $('#filterFromDate').val();
                filters.to_date = $('#filterToDate').val();
                currentPage = 1;
                loadInvoices();
            });

            // Pagination
            $('#prevPage').click(function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadInvoices();
                }
            });

            $('#nextPage').click(function() {
                if (currentPage < lastPage) {
                    currentPage++;
                    loadInvoices();
                }
            });

            function loadInvoices() {
                $('#invoicesLoader').show();
                $('#invoicesContent').hide();
                $('#noInvoicesMessage').hide();

                let params = {
                    page: currentPage,
                    per_page: 10
                };

                if (filters.status) params.status = filters.status;
                if (filters.from_date) params.from_date = filters.from_date;
                if (filters.to_date) params.to_date = filters.to_date;

                axios.get('/invoices', {
                        params
                    })
                    .then(response => {
                        const apiResponse = response.data;
                        const invoices = apiResponse?.data?.data || [];
                        const meta = apiResponse?.data?.meta || {};
                        const totals = apiResponse?.data?.meta?.totals || {};

                        $('#invoicesLoader').hide();

                        if (invoices.length > 0) {
                            updateSummaryCards(totals);
                            renderInvoicesTable(invoices);
                            updatePagination(meta);
                            $('#invoicesContent').show();
                        } else {
                            $('#noInvoicesMessage').show();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading invoices:', error);
                        $('#invoicesLoader').hide();
                        $('#noInvoicesMessage').show();
                        $('#noInvoicesMessage h3').text('Error Loading Invoices');
                        $('#noInvoicesMessage p').text('Please try again later.');
                    });
            }

            function updateSummaryCards(totals) {
                $('#totalPaid').text(formatMoney(totals.total_paid || 0));
                $('#thisMonthTotal').text(formatMoney(totals.this_month || 0));
                $('#outstandingTotal').text(formatMoney(totals.total_due || 0));
            }

            function renderInvoicesTable(invoices) {
                let html = '';

                invoices.forEach(invoice => {
                    const date = new Date(invoice?.issue_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });

                    const statusClass = {
                        'paid': 'success',
                        'open': 'warning',
                        'draft': 'secondary',
                        'void': 'dark',
                        'uncollectible': 'danger'
                    } [invoice?.status] || 'secondary';

                    // Parse line items
                    const lineItems = invoice?.line_items || [];
                    const description = lineItems.length > 0 ?
                        lineItems[0].description :
                        'Subscription Invoice';

                    html += `
                    <tr>
                        <td>
                            <strong>${invoice?.number}</strong>
                        </td>
                        <td>${date}</td>
                        <td>
                            ${description}
                            ${lineItems.length > 1 ? `<br><small class="text-muted">+${lineItems.length - 1} more items</small>` : ''}
                        </td>
                        <td>
                            <strong>${formatMoney(invoice?.total)}</strong>
                            <br>
                            <small class="text-muted">${invoice?.currency}</small>
                        </td>
                        <td><span class="badge bg-${statusClass}">${invoice?.status}</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewInvoice(${invoice?.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="downloadInvoice(${invoice?.id})" title="Download PDF">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                });

                $('#invoicesTableBody').html(html);
            }

            function updatePagination(meta) {
                currentPage = meta.current_page || 1;
                lastPage = meta.last_page || 1;

                $('#paginationInfo').text(
                    `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total || 0} invoices`);
                $('#currentPage').text(`Page ${currentPage}`);

                $('#prevPage').prop('disabled', currentPage <= 1);
                $('#nextPage').prop('disabled', currentPage >= lastPage);
            }

            // View invoice details
            window.viewInvoice = function(id) {
                axios.get(`/invoices/${id}`)
                    .then(response => {
                        const invoice = response?.data?.data;


                        const issueDate = new Date(invoice?.issue_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        const dueDate = invoice?.due_date ?
                            new Date(invoice?.due_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            }) :
                            'N/A';

                        // Parse line items
                        const lineItems = invoice?.line_items || [];
                        let itemsHtml = '';

                        lineItems.forEach(item => {
                            itemsHtml += `
                            <tr>
                                <td>${item.description}</td>
                                <td class="text-center">${item.quantity || 1}</td>
                                <td class="text-end">${formatMoney(item.amount || 0)}</td>
                                <td class="text-end">${formatMoney((item.amount || 0) * (item.quantity || 1))}</td>
                            </tr>
                        `;
                        });

                        // Parse tax rates
                        const taxRates = invoice?.tax_rates || [];
                        let taxHtml = '';

                        // taxRates.forEach(tax => {
                            taxHtml = `
                            <tr>
                                <td>${taxRates.name || 'Tax'}</td>
                                <td class="text-end">${taxRates.rate}%</td>
                                <td class="text-end">${formatMoney(taxRates.amount || 0)}</td>
                            </tr>
                        `;
                        // });

                        // Parse discounts
                        const discounts = invoice?.discounts || [];
                        let discountHtml = '';

                        // discounts.forEach(discount => {
                            discountHtml = `
                            <tr>
                                <td>${discounts.name || 'Discount'}</td>
                                <td class="text-end">-${formatMoney(discounts.amount || 0)}</td>
                            </tr>
                        `;
                        // });

                        let html = `
                        <div class="row mb-4">
                            <div class="col-6">
                                <h6 class="fw-bold">Invoice To:</h6>
                                <p class="mb-1">{{ auth()->user()->name }}</p>
                                <p class="mb-1">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="col-6 text-end">
                                <h6 class="fw-bold">Invoice #${invoice?.number}</h6>
                                <p class="mb-1">Issue Date: ${issueDate}</p>
                                <p class="mb-1">Due Date: ${dueDate}</p>
                                <p class="mb-1">Status: <span class="badge bg-${invoice?.status === 'paid' ? 'success' : 'warning'}">${invoice?.status}</span></p>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-6">
                                ${taxHtml ? `
                                        <h6 class="fw-bold mt-3">Tax Breakdown</h6>
                                        <table class="table table-sm">
                                            ${taxHtml}
                                        </table>
                                    ` : ''}

                                ${discountHtml ? `
                                        <h6 class="fw-bold mt-3">Discounts</h6>
                                        <table class="table table-sm">
                                            ${discountHtml}
                                        </table>
                                    ` : ''}
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-end">${formatMoney(invoice?.subtotal)}</td>
                                    </tr>
                                    <tr>
                                        <th>Tax:</th>
                                        <td class="text-end">${formatMoney(invoice?.tax)}</td>
                                    </tr>
                                    ${discounts.length > 0 ? `
                                            <tr>
                                                <th>Discount:</th>
                                                <td class="text-end text-success">-${formatMoney(invoice?.discount_amount || 0)}</td>
                                            </tr>
                                        ` : ''}
                                    <tr class="fw-bold">
                                        <th>Total:</th>
                                        <td class="text-end text-primary">${formatMoney(invoice?.total)}</td>
                                    </tr>
                                    <tr class="text-success">
                                        <th>Paid:</th>
                                        <td class="text-end">${formatMoney(invoice?.amount_paid || 0)}</td>
                                    </tr>
                                    <tr class="text-danger">
                                        <th>Balance Due:</th>
                                        <td class="text-end">${formatMoney(invoice?.amount_due || 0)}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    `;

                        $('#invoiceDetails').html(html);

                        // Set download button
                        $('#downloadInvoiceBtn').off('click').on('click', function() {
                            downloadInvoice(invoice?.id);
                        });

                        $('#invoiceModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error loading invoice:', error);
                        toastr.error('Failed to load invoice details');
                    });
            };

            // Download invoice
            window.downloadInvoice = function(id) {
                axios.get(`/invoices/${id}/download`, {
                        responseType: 'blob'
                    })
                    .then(response => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', `invoice-${id}.pdf`);
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error('Error downloading invoice:', error);
                        toastr.error('Failed to download invoice');
                    });
            };

            // Helper function
            function formatMoney(amount) {
                if (amount === null || amount === undefined) return '$0.00';
                return '$' + parseFloat(amount).toFixed(2);
            }
        });
    </script>
@endpush
