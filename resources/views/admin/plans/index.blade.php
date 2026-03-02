@extends('layouts.admin')
@section('title', 'Plan Management')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Plans</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" id="addPlanBtn">
                                <i class="fas fa-plus"></i> Add New Plan
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="plansTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Billing Period</th>
                                    <th>Prices</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>

    <!-- Add/Edit Plan Modal -->
    <div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="planModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <a href="{{ route('admin.plans.index') }}">view website all plans</a>
                    <h5 class="modal-title" id="planModalLabel">Add New Plan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="planForm" novalidate>
                    <div class="modal-body">
                        <input type="hidden" id="plan_id" name="id">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="planTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="basic-info-tab" data-toggle="tab" href="#basic-info"
                                    role="tab">Basic Information</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="features-tab" data-toggle="tab" href="#features"
                                    role="tab">Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="prices-tab" data-toggle="tab" href="#prices"
                                    role="tab">Pricing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="discounts-tab" data-toggle="tab" href="#discounts"
                                    role="tab">Discounts</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content mt-3">
                            <!-- Basic Information Tab -->
                            <div class="tab-pane active" id="basic-info" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="code">Code</label>
                                            <input type="text" class="form-control" id="code" name="code">
                                            <small class="text-muted">Leave empty to auto-generate</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="type" name="type">
                                                <option value="recurring">Recurring</option>
                                                <option value="usage">Usage Based</option>
                                                <option value="one_time">One Time</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="billing_period">Billing Period <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="billing_period" name="billing_period">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly" selected>Monthly</option>
                                                <option value="quarterly">Quarterly</option>
                                                <option value="yearly">Yearly</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="billing_interval">Billing Interval</label>
                                            <input type="number" class="form-control" id="billing_interval"
                                                name="billing_interval" value="1" min="1">
                                            <small class="text-muted">Every X periods</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="is_active"
                                                    name="is_active" value="1" checked>
                                                <label class="custom-control-label" for="is_active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="is_visible"
                                                    name="is_visible" value="1" checked>
                                                <label class="custom-control-label" for="is_visible">Visible</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="is_featured"
                                                    name="is_featured" value="1">
                                                <label class="custom-control-label" for="is_featured">Featured</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_order">Sort Order</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                                value="0" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Features Tab -->
                            <div class="tab-pane" id="features" role="tabpanel">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-success" id="addFeatureBtn" disabled>
                                        <i class="fas fa-plus"></i> Add Feature
                                    </button>
                                </div>
                                <table class="table table-bordered" id="featuresTable">
                                    <thead>
                                        <tr>
                                            <th>Feature</th>
                                            <th>Value</th>
                                            <th>Configuration</th>
                                            <th>Sort Order</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="featuresContainer"></tbody>
                                </table>
                            </div>

                            <!-- Pricing Tab -->
                            <div class="tab-pane" id="prices" role="tabpanel">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-success" id="addPriceBtn">
                                        <i class="fas fa-plus"></i> Add Price
                                    </button>
                                </div>
                                <table class="table table-bordered" id="pricesTable">
                                    <thead>
                                        <tr>
                                            <th>Currency</th>
                                            <th>Amount</th>
                                            <th>Interval</th>
                                            <th>Usage Type</th>
                                            <th>Stripe Price ID</th>
                                            <th>Valid From</th>
                                            <th>Valid To</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pricesContainer"></tbody>
                                </table>
                            </div>

                            <!-- Discounts Tab -->
                            <div class="tab-pane" id="discounts" role="tabpanel">
                                <table class="table table-bordered" id="discountsTable">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody id="discountsContainer"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="savePlanBtn">Save Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Plan Modal -->
    <div class="modal fade" id="viewPlanModal" tabindex="-1" role="dialog" aria-labelledby="viewPlanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPlanModalLabel">Plan Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="planDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .nav-tabs .nav-link {
            cursor: pointer;
        }
        .table td {
            vertical-align: middle;
        }
        .invalid-feedback {
            display: block;
        }
        .custom-switch {
            padding-left: 2.25rem;
        }
        .custom-switch .custom-control-label::before {
            left: -2.25rem;
        }
        .custom-switch .custom-control-label::after {
            left: calc(-2.25rem + 2px);
        }
    </style>
@endpush

@push('.js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup axios defaults
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content');
            axios.defaults.headers.common['Accept'] = 'application/json';

            // Load features for dropdown
            let availableFeatures = [];
            let availableDiscounts = [];

            // Initialize DataTable
            var table = $('#plansTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/v1/plans',
                    dataSrc: function(json) {
                        return json.data || [];
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'type',
                        render: function(data) {
                            return data.charAt(0).toUpperCase() + data.slice(1).replace('_', ' ');
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return data.billing_period + ' (x' + data.billing_interval + ')';
                        }
                    },
                    {
                        data: 'prices',
                        render: function(data) {
                            if (!data || !data.length) return '<span class="badge badge-warning">No prices</span>';
                            return data.map(p => p.amount_with_currency + ' / ' + p
                                .interval_description).join('<br>');
                        }
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            return data ? '<span class="badge badge-success bg-success p-2">Active</span>' :
                                '<span class="badge badge-danger">Inactive</span>';
                        }
                    },
                    {
                        data: 'sort_order'
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-info view-plan" data-id="${data.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-plan" data-id="${data.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-plan" data-id="${data.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [
                    [7, 'asc']
                ],
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-sm btn-secondary'
                    }
                ],
                responsive: true,
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>'
                }
            });

            table.buttons().container().appendTo('#plansTable_wrapper .col-md-6:eq(0)');

            // Load features and discounts
            Promise.all([
                axios.get('/api/v1/features?all=true'),
                axios.get('/api/v1/discounts?all=true')
            ]).then(([featuresRes, discountsRes]) => {
                availableFeatures = featuresRes.data?.data.data || [];
                availableDiscounts = discountsRes.data?.data.data || [];
                loadDiscountsTable();
                $('#addFeatureBtn').prop('disabled', false);
            }).catch(error => {
                console.error('Failed to load features/discounts:', error);
                Swal.fire('Warning!', 'Failed to load features and discounts.', 'warning');
            });

            // Add Plan button click
            $('#addPlanBtn').click(function() {
                resetForm();
                $('#planModalLabel').text('Add New Plan');
                $('#plan_id').val('');
                $('#planForm')[0].reset();
                $('#is_active').prop('checked', true);
                $('#is_visible').prop('checked', true);
                $('#planModal').modal('show');
            });

            // Edit Plan
            $(document).on('click', '.edit-plan', function() {
                let id = $(this).data('id');
                loadPlanData(id);
            });

            // View Plan
            $(document).on('click', '.view-plan', function() {
                let id = $(this).data('id');
                viewPlan(id);
            });

            // Delete Plan
            $(document).on('click', '.delete-plan', function() {
                let id = $(this).data('id');
                deletePlan(id);
            });

            // Add Feature
            $('#addFeatureBtn').click(function() {
                addFeatureRow();
            });

            // Add Price
            $('#addPriceBtn').click(function() {
                addPriceRow();
            });

            // Form Submit
            $('#planForm').submit(function(e) {
                e.preventDefault();
                savePlan(table);
            });

            // Remove feature/price row
            $(document).on('click', '.remove-feature, .remove-price', function() {
                let row = $(this).closest('tr');

                // If it has an ID, mark it for deletion
                let id = row.data('id');
                if (id) {
                    // Add hidden input for deletion
                    let input = $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', $(this).hasClass('remove-feature') ? 'deleted_features[]' : 'deleted_prices[]')
                        .val(id);
                    $('#planForm').append(input);
                    row.remove();
                } else {
                    row.remove();
                }
            });

            // Functions
            function resetForm() {
                $('#planForm')[0].reset();
                $('#plan_id').val('');
                $('#featuresContainer').empty();
                $('#pricesContainer').empty();
                $('#discountsContainer input[type="checkbox"]').prop('checked', false);
                $('input[name^="deleted_"]').remove();
            }

            function loadPlanData(id) {
                axios.get(`/api/v1/plans/${id}`)
                    .then(response => {
                        let plan = response.data.data;

                        resetForm();

                        $('#planModalLabel').text('Edit Plan');
                        $('#plan_id').val(plan.id);
                        $('#name').val(plan.name);
                        $('#code').val(plan.code);
                        $('#description').val(plan.description);
                        $('#type').val(plan.type);
                        $('#billing_period').val(plan.billing_period);
                        $('#billing_interval').val(plan.billing_interval);
                        $('#is_active').prop('checked', plan.is_active);
                        $('#is_visible').prop('checked', plan.is_visible);
                        $('#is_featured').prop('checked', plan.is_featured);
                        $('#sort_order').val(plan.sort_order);

                        // Load features
                        if (plan.features && plan.features.length) {
                            plan.features.forEach(feature => {
                                addFeatureRow(feature);
                            });
                        }

                        // Load prices
                        if (plan.prices && plan.prices.length) {
                            plan.prices.forEach(price => {
                                addPriceRow(price);
                            });
                        }

                        // Load discounts
                        if (plan.discounts && plan.discounts.length) {
                            plan.discounts.forEach(discount => {
                                $(`#discount_${discount.id}`).prop('checked', true);
                            });
                        }

                        $('#planModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error loading plan:', error);
                        Swal.fire('Error!', 'Failed to load plan data', 'error');
                    });
            }

            function viewPlan(id) {
                axios.get(`/api/v1/plans/${id}`)
                    .then(response => {
                        let plan = response.data.data;

                        let html = `
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th style="width: 40%">Name:</th>
                                            <td>${plan.name}</td>
                                        </tr>
                                        <tr>
                                            <th>Code:</th>
                                            <td>${plan.code}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td>${plan.type}</td>
                                        </tr>
                                        <tr>
                                            <th>Billing:</th>
                                            <td>${plan.billing_period} (x${plan.billing_interval})</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>${plan.is_active ? '<span class="badge badge-success bg-success p-2">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}</td>
                                        </tr>
                                        <tr>
                                            <th>Visible:</th>
                                            <td>${plan.is_visible ? 'Yes' : 'No'}</td>
                                        </tr>
                                        <tr>
                                            <th>Featured:</th>
                                            <td>${plan.is_featured ? 'Yes' : 'No'}</td>
                                        </tr>
                                        <tr>
                                            <th>Sort Order:</th>
                                            <td>${plan.sort_order}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="card-title mb-0">Description</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">${plan.description || 'No description provided'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Features section
                        html += `
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="card-title mb-0">Features</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Feature</th>
                                                        <th>Value</th>
                                                        <th>Configuration</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                        `;

                        if (plan.features && plan.features.length) {
                            plan.features.forEach(f => {
                                html += `
                                    <tr>
                                        <td>${f.feature_name || f.feature?.name || 'N/A'}</td>
                                        <td>${f.value}</td>
                                        <td>${f.config ? '<pre class="mb-0"><code>' + JSON.stringify(f.config, null, 2) + '</code></pre>' : '-'}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `<tr><td colspan="3" class="text-center text-muted">No features assigned</td></tr>`;
                        }

                        html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Prices section
                        html += `
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="card-title mb-0">Pricing</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Currency</th>
                                                        <th>Amount</th>
                                                        <th>Interval</th>
                                                        <th>Usage Type</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                        `;

                        if (plan.prices && plan.prices.length) {
                            plan.prices.forEach(p => {
                                let isActive = p.is_active ? 'Active' : 'Inactive';
                                let statusClass = p.is_active ? 'success' : 'secondary';

                                html += `
                                    <tr>
                                        <td>${p.currency}</td>
                                        <td>${p.amount_with_currency}</td>
                                        <td>${p.interval_description}</td>
                                        <td>${p.usage_type}</td>
                                        <td><span class="badge badge-${statusClass}">${isActive}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `<tr><td colspan="5" class="text-center text-muted">No prices configured</td></tr>`;
                        }

                        html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Discounts section
                        html += `
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="card-title mb-0">Discounts</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Duration</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                        `;

                        if (plan.discounts.data && plan.discounts.data.length) {
                            plan.discounts.data.forEach(d => {
                                html += `
                                    <tr>
                                        <td><span class="badge badge-success bg-success p-2">${d.code}</span></td>
                                        <td>${d.name}</td>
                                        <td>${d.type}</td>
                                        <td>${d.amount}${d.type === 'percentage' ? '%' : ' ' + (d.currency || '')}</td>
                                        <td>${d.duration}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `<tr><td colspan="5" class="text-center text-muted">No discounts available</td></tr>`;
                        }

                        html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        $('#planDetails').html(html);
                        $('#viewPlanModal').modal('show');
                    })
                    .catch(error => {
                        console.error('Error viewing plan:', error);
                        Swal.fire('Error!', 'Failed to load plan details', 'error');
                    });
            }

            function deletePlan(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/api/v1/plans/${id}`)
                            .then(response => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Plan has been deleted.',
                                    timer: 2000
                                });
                                table.ajax.reload();
                            })
                            .catch(error => {
                                console.error('Error deleting plan:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Failed to delete plan.'
                                });
                            });
                    }
                });
            }

            function addFeatureRow(feature = null) {
                let index = $('#featuresContainer tr').length;
                let row = $('<tr></tr>');

                if (feature && feature.id) {
                    row.attr('data-id', feature.id);
                }

                // Feature select
                let featureSelect = $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `features[${index}][feature_id]`)
                    .attr('required', false);

                featureSelect.append($('<option>').val('').text('Select Feature'));

                if (availableFeatures && availableFeatures.length) {
                    availableFeatures.forEach(f => {
                        let option = $('<option>').val(f.id).text(f.name + ' (' + f.code + ')');
                        if (feature && feature.feature_id == f.id) {
                            option.attr('selected', true);
                        }
                        featureSelect.append(option);
                    });
                }

                let featureTd = $('<td></td>').append(featureSelect);
                row.append(featureTd);

                // Value input
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'text')
                        .attr('name', `features[${index}][value]`)
                        .attr('placeholder', 'e.g., 100, unlimited, true')
                        .attr('required', false)
                        .val(feature ? feature.value : '')
                ));

                // Config input
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'text')
                        .attr('name', `features[${index}][config]`)
                        .attr('placeholder', 'JSON config (optional)')
                        .val(feature && feature.config ? JSON.stringify(feature.config) : '')
                ));

                // Sort order
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'number')
                        .attr('name', `features[${index}][sort_order]`)
                        .attr('min', '0')
                        .val(feature ? feature.sort_order || 0 : 0)
                ));

                // Action buttons
                let actionTd = $('<td></td>');

                if (feature && feature.id) {
                    actionTd.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', `features[${index}][id]`)
                            .val(feature.id)
                    );
                }

                actionTd.append(
                    $('<button>')
                        .addClass('btn btn-sm btn-danger remove-feature')
                        .attr('type', 'button')
                        .html('<i class="fas fa-trash"></i>')
                );

                row.append(actionTd);

                $('#featuresContainer').append(row);
            }

            function addPriceRow(price = null) {
                let index = $('#pricesContainer tr').length;
                let row = $('<tr></tr>');

                if (price && price.id) {
                    row.attr('data-id', price.id);
                }

                // Currency
                let currencySelect = $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `prices[${index}][currency]`)
                    .attr('required', false)
                    .append(
                        $('<option>').val('USD').text('USD').prop('selected', !price || price.currency === 'USD'),
                        $('<option>').val('EUR').text('EUR').prop('selected', price && price.currency === 'EUR'),
                        $('<option>').val('GBP').text('GBP').prop('selected', price && price.currency === 'GBP'),
                        $('<option>').val('BDT').text('BDT').prop('selected', price && price.currency === 'BDT')
                    );

                row.append($('<td></td>').append(currencySelect));

                // Amount
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'number')
                        .attr('step', '0.01')
                        .attr('min', '0')
                        .attr('name', `prices[${index}][amount]`)
                        .attr('placeholder', '0.00')
                        .attr('required', false)
                        .val(price ? price.amount : '')
                ));

                // Interval
                let intervalTd = $('<td></td>');

                let intervalSelect = $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `prices[${index}][interval]`)
                    .attr('required', false)
                    .append(
                        $('<option>').val('month').text('Month').prop('selected', !price || price.interval === 'month'),
                        $('<option>').val('year').text('Year').prop('selected', price && price.interval === 'year'),
                        $('<option>').val('quarter').text('Quarter').prop('selected', price && price.interval === 'quarter'),
                        $('<option>').val('week').text('Week').prop('selected', price && price.interval === 'week'),
                        $('<option>').val('day').text('Day').prop('selected', price && price.interval === 'day')
                    );

                intervalTd.append(intervalSelect);

                intervalTd.append(
                    $('<input>')
                        .addClass('form-control form-control-sm mt-1')
                        .attr('type', 'number')
                        .attr('min', '1')
                        .attr('name', `prices[${index}][interval_count]`)
                        .attr('placeholder', 'Count')
                        .val(price ? price.interval_count || 1 : 1)
                );

                row.append(intervalTd);

                // Usage Type
                row.append($('<td></td>').append(
                    $('<select>')
                        .addClass('form-control form-control-sm')
                        .attr('name', `prices[${index}][usage_type]`)
                        .append(
                            $('<option>').val('licensed').text('Licensed').prop('selected', !price || price.usage_type === 'licensed'),
                            $('<option>').val('metered').text('Metered').prop('selected', price && price.usage_type === 'metered'),
                            $('<option>').val('tiered').text('Tiered').prop('selected', price && price.usage_type === 'tiered')
                        )
                ));

                // Stripe Price ID
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'text')
                        .attr('name', `prices[${index}][stripe_price_id]`)
                        .attr('placeholder', 'Optional')
                        .val(price ? price.stripe_price_id || '' : '')
                ));

                // Active From
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'date')
                        .attr('name', `prices[${index}][active_from]`)
                        .val(price && price.active_from ? new Date(price.active_from).toISOString().split('T')[0] : '')
                ));

                // Active To
                row.append($('<td></td>').append(
                    $('<input>')
                        .addClass('form-control form-control-sm')
                        .attr('type', 'date')
                        .attr('name', `prices[${index}][active_to]`)
                        .val(price && price.active_to ? new Date(price.active_to).toISOString().split('T')[0] : '')
                ));

                // Action
                let actionTd = $('<td></td>');

                if (price && price.id) {
                    actionTd.append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', `prices[${index}][id]`)
                            .val(price.id)
                    );
                }

                actionTd.append(
                    $('<button>')
                        .addClass('btn btn-sm btn-danger remove-price')
                        .attr('type', 'button')
                        .html('<i class="fas fa-trash"></i>')
                );

                row.append(actionTd);

                $('#pricesContainer').append(row);
            }

            function loadDiscountsTable() {
                let html = '';

                if (availableDiscounts && availableDiscounts.length) {
                    availableDiscounts.forEach(discount => {
                        html += `
                            <tr>
                                <td class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="discounts[]"
                                            id="discount_${discount.id}" value="${discount.id}">
                                        <label class="custom-control-label" for="discount_${discount.id}"></label>
                                    </div>
                                </td>
                                <td><span class="badge badge-success bg-success p-2">${discount.code}</span></td>
                                <td>${discount.name}</td>
                                <td><span class="badge badge-secondary">${discount.type}</span></td>
                                <td>${discount.amount}${discount.type === 'percentage' ? '%' : ' ' + (discount.currency || '')}</td>
                                <td>${discount.duration}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No discounts available</td></tr>';
                }

                $('#discountsContainer').html(html);
            }

            function savePlan(table) {
                // Show loading
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Collect form data
                let data = {
                    name: $('#name').val(),
                    code: $('#code').val(),
                    description: $('#description').val(),
                    type: $('#type').val(),
                    billing_period: $('#billing_period').val(),
                    billing_interval: $('#billing_interval').val(),
                    is_active: $('#is_active').is(':checked'),
                    is_visible: $('#is_visible').is(':checked'),
                    is_featured: $('#is_featured').is(':checked'),
                    sort_order: $('#sort_order').val() || 0,
                    features: [],
                    prices: [],
                    discounts: []
                };

                // Validate basic info
                if (!data.name) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Plan name is required.',
                        timer: 3000
                    });
                    $('#basic-info-tab').tab('show');
                    return;
                }

                // Collect features
                $('#featuresContainer tr').each(function() {
                    let row = $(this);
                    let featureId = row.find('select[name*="[feature_id]"]').val();

                    // Only collect if a feature is selected
                    if (featureId) {
                        let feature = {
                            feature_id: featureId,
                            value: row.find('input[name*="[value]"]').val() || '',
                            config: row.find('input[name*="[config]"]').val() || '',
                            sort_order: row.find('input[name*="[sort_order]"]').val() || 0
                        };

                        let id = row.data('id');
                        if (id) {
                            feature.id = id;
                        }

                        data.features.push(feature);
                    }
                });

                // Collect prices
                let hasValidPrice = false;
                $('#pricesContainer tr:visible').each(function() {
                    let row = $(this);
                    let currency = row.find('select[name*="[currency]"]').val();
                    let amount = row.find('input[name*="[amount]"]').val();
                    let interval = row.find('select[name*="[interval]"]').val();

                    if (currency && amount && interval && parseFloat(amount) > 0) {
                        hasValidPrice = true;
                        let price = {
                            currency: currency,
                            amount: amount,
                            interval: interval,
                            interval_count: row.find('input[name*="[interval_count]"]').val() || 1,
                            usage_type: row.find('select[name*="[usage_type]"]').val() || 'licensed',
                            stripe_price_id: row.find('input[name*="[stripe_price_id]"]').val() || null,
                            active_from: row.find('input[name*="[active_from]"]').val() || null,
                            active_to: row.find('input[name*="[active_to]"]').val() || null
                        };

                        let id = row.data('id');
                        if (id) {
                            price.id = id;
                        }

                        data.prices.push(price);
                    }
                });

                // Validate that at least one price exists for new plans
                let id = $('#plan_id').val();
                if (!id && !hasValidPrice) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'At least one valid price is required for the plan.',
                        timer: 3000
                    });
                    $('#prices-tab').tab('show');
                    return;
                }

                // Collect discounts
                $('input[name="discounts[]"]:checked').each(function() {
                    data.discounts.push($(this).val());
                });

                // Collect deleted items
                let deletedFeatures = [];
                let deletedPrices = [];

                $('input[name="deleted_features[]"]').each(function() {
                    deletedFeatures.push($(this).val());
                });

                $('input[name="deleted_prices[]"]').each(function() {
                    deletedPrices.push($(this).val());
                });

                if (deletedFeatures.length) {
                    data.deleted_features = deletedFeatures;
                }
                if (deletedPrices.length) {
                    data.deleted_prices = deletedPrices;
                }

                // Make request
                let request;
                if (id) {
                    request = axios.put(`/api/v1/plans/update/${id}`, data);
                } else {
                    request = axios.post('/api/v1/plans', data);
                }

                request.then(response => {
                    Swal.close();
                    $('#planModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Plan saved successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    table.ajax.reload();
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error saving plan:', error);

                    let message = 'Failed to save plan.';
                    if (error.response?.data?.message) {
                        message = error.response.data.message;
                    } else if (error.response?.data?.errors) {
                        // Handle validation errors
                        const errors = error.response.data.errors;
                        message = Object.values(errors).flat().join('\n');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message,
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    </script>
@endpush
