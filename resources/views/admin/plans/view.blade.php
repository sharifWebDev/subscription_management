@extends('layouts.admin')
@section('title', 'Plan Management')

@section('content')
    <div class="container-fluid">
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
                    <h5 class="modal-title" id="planModalLabel">Add New Plan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="planForm">
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
                                            <input type="text" class="form-control" id="name" name="name"
                                                required>
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
                                            <select class="form-control" id="type" name="type" required>
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
                                            <select class="form-control" id="billing_period" name="billing_period"
                                                required>
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
                                    <button type="button" class="btn btn-sm btn-success" id="addFeatureBtn">
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

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup axios defaults
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content');

            // Load features for dropdown
            let availableFeatures = [];
            let availableDiscounts = [];

            // Fetch features and discounts
            Promise.all([
                axios.get('/api/v1/features?all=true'),
                axios.get('/api/v1/discounts?all=true')
            ]).then(([featuresRes, discountsRes]) => {
                availableFeatures = featuresRes.data.data || [];
                availableDiscounts = discountsRes.data.data || [];
                loadDiscountsTable();
            });

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
                        data: 'type'
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
                            if (!data || !data.length) return 'No prices';
                            return data.map(p => p.amount_with_currency + ' / ' + p
                                .interval_description).join('<br>');
                        }
                    },
                    {
                        data: 'is_active',
                        render: function(data) {
                            return data ? '<span class="badge badge-success">Active</span>' :
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
                        <button class="btn btn-sm btn-info view-plan" data-id="${data.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-plan" data-id="${data.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-plan" data-id="${data.id}">
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
                responsive: true
            });

            table.buttons().container().appendTo('#plansTable_wrapper .col-md-6:eq(0)');

            // Add Plan button click
            $('#addPlanBtn').click(function() {
                resetForm();
                $('#planModalLabel').text('Add New Plan');
                $('#plan_id').val('');
                $('#planForm')[0].reset();
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
                savePlan();
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
                        .attr('name', $(this).hasClass('remove-feature') ? 'features[' + row.index() +
                            '][_deleted]' : 'prices[' + row.index() + '][_deleted]')
                        .val('1');
                    row.append(input);
                    row.hide();
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
                            <table class="table table-sm">
                                <tr>
                                    <th>Name:</th>
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
                                    <td>${plan.is_active ? 'Active' : 'Inactive'}</td>
                                </tr>
                                <tr>
                                    <th>Visible:</th>
                                    <td>${plan.is_visible ? 'Yes' : 'No'}</td>
                                </tr>
                                <tr>
                                    <th>Featured:</th>
                                    <td>${plan.is_featured ? 'Yes' : 'No'}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Description</h6>
                                </div>
                                <div class="card-body">
                                    <p>${plan.description || 'No description'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Features</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Feature</th>
                                                <th>Value</th>
                                                <th>Config</th>
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
                                <td>${f.config ? JSON.stringify(f.config) : '-'}</td>
                            </tr>
                        `;
                            });
                        } else {
                            html += `<tr><td colspan="3" class="text-center">No features</td></tr>`;
                        }

                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Pricing</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Currency</th>
                                                <th>Amount</th>
                                                <th>Interval</th>
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
                                <td><span class="badge badge-${statusClass}">${isActive}</span></td>
                            </tr>
                        `;
                            });
                        } else {
                            html += `<tr><td colspan="4" class="text-center">No prices</td></tr>`;
                        }

                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Discounts</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                `;

                        if (plan.discounts && plan.discounts.length) {
                            plan.discounts.forEach(d => {
                                html += `
                            <tr>
                                <td>${d.code}</td>
                                <td>${d.name}</td>
                                <td>${d.type}</td>
                                <td>${d.amount}${d.type === 'percentage' ? '%' : ''}</td>
                            </tr>
                        `;
                            });
                        } else {
                            html += `<tr><td colspan="4" class="text-center">No discounts</td></tr>`;
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
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`/api/v1/plans/${id}`)
                            .then(response => {
                                Swal.fire('Deleted!', 'Plan has been deleted.', 'success');
                                table.ajax.reload();
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Failed to delete plan.', 'error');
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

                let featureSelect = $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `features[${index}][feature_id]`)
                    .attr('required', true);

                featureSelect.append($('<option>').val('').text('Select Feature'));

                availableFeatures.forEach(f => {
                    let option = $('<option>').val(f.id).text(f.name + ' (' + f.code + ')');
                    if (feature && feature.feature_id == f.id) {
                        option.attr('selected', true);
                    }
                    featureSelect.append(option);
                });

                let featureTd = $('<td></td>').append(featureSelect);
                row.append(featureTd);

                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'text')
                    .attr('name', `features[${index}][value]`)
                    .attr('required', true)
                    .val(feature ? feature.value : '')
                ));

                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'text')
                    .attr('name', `features[${index}][config]`)
                    .val(feature && feature.config ? JSON.stringify(feature.config) : '')
                ));

                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'number')
                    .attr('name', `features[${index}][sort_order]`)
                    .val(feature ? feature.sort_order || 0 : 0)
                ));

                row.append($('<td></td>').append(
                    $('<button>')
                    .addClass('btn btn-sm btn-danger remove-feature')
                    .attr('type', 'button')
                    .html('<i class="fas fa-trash"></i>')
                ));

                $('#featuresContainer').append(row);
            }

            function addPriceRow(price = null) {
                let index = $('#pricesContainer tr').length;
                let row = $('<tr></tr>');

                if (price && price.id) {
                    row.attr('data-id', price.id);
                }

                // Currency
                row.append($('<td></td>').append(
                    $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `prices[${index}][currency]`)
                    .attr('required', true)
                    .append(
                        $('<option>').val('USD').text('USD').prop('selected', !price || price.currency ===
                            'USD'),
                        $('<option>').val('EUR').text('EUR').prop('selected', price && price.currency ===
                            'EUR'),
                        $('<option>').val('GBP').text('GBP').prop('selected', price && price.currency ===
                            'GBP'),
                        $('<option>').val('BDT').text('BDT').prop('selected', price && price.currency ===
                            'BDT')
                    )
                ));

                // Amount
                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'number')
                    .attr('step', '0.01')
                    .attr('name', `prices[${index}][amount]`)
                    .attr('required', true)
                    .val(price ? price.amount : '')
                ));

                // Interval
                row.append($('<td></td>').append(
                    $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `prices[${index}][interval]`)
                    .attr('required', true)
                    .append(
                        $('<option>').val('month').text('Month').prop('selected', !price || price
                            .interval === 'month'),
                        $('<option>').val('year').text('Year').prop('selected', price && price.interval ===
                            'year'),
                        $('<option>').val('quarter').text('Quarter').prop('selected', price && price
                            .interval === 'quarter'),
                        $('<option>').val('week').text('Week').prop('selected', price && price.interval ===
                            'week'),
                        $('<option>').val('day').text('Day').prop('selected', price && price.interval ===
                            'day')
                    )
                    .after(
                        $('<input>')
                        .addClass('form-control form-control-sm mt-1')
                        .attr('type', 'number')
                        .attr('min', '1')
                        .attr('name', `prices[${index}][interval_count]`)
                        .attr('placeholder', 'Count')
                        .val(price ? price.interval_count || 1 : 1)
                    )
                ));

                // Usage Type
                row.append($('<td></td>').append(
                    $('<select>')
                    .addClass('form-control form-control-sm')
                    .attr('name', `prices[${index}][usage_type]`)
                    .append(
                        $('<option>').val('licensed').text('Licensed').prop('selected', !price || price
                            .usage_type === 'licensed'),
                        $('<option>').val('metered').text('Metered').prop('selected', price && price
                            .usage_type === 'metered'),
                        $('<option>').val('tiered').text('Tiered').prop('selected', price && price
                            .usage_type === 'tiered')
                    )
                ));

                // Stripe Price ID
                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'text')
                    .attr('name', `prices[${index}][stripe_price_id]`)
                    .val(price ? price.stripe_price_id || '' : '')
                ));

                // Active From
                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'date')
                    .attr('name', `prices[${index}][active_from]`)
                    .val(price && price.active_from ? new Date(price.active_from).toISOString().split('T')[
                        0] : '')
                ));

                // Active To
                row.append($('<td></td>').append(
                    $('<input>')
                    .addClass('form-control form-control-sm')
                    .attr('type', 'date')
                    .attr('name', `prices[${index}][active_to]`)
                    .val(price && price.active_to ? new Date(price.active_to).toISOString().split('T')[0] :
                        '')
                ));

                // Action
                row.append($('<td></td>').append(
                    $('<button>')
                    .addClass('btn btn-sm btn-danger remove-price')
                    .attr('type', 'button')
                    .html('<i class="fas fa-trash"></i>')
                ));

                $('#pricesContainer').append(row);
            }

            function loadDiscountsTable() {
                let html = '';

                if (availableDiscounts.length) {
                    availableDiscounts.forEach(discount => {
                        html += `
                    <tr>
                        <td>
                            <input type="checkbox" name="discounts[]" id="discount_${discount.id}" value="${discount.id}">
                        </td>
                        <td>${discount.code}</td>
                        <td>${discount.name}</td>
                        <td>${discount.type}</td>
                        <td>${discount.amount}${discount.type === 'percentage' ? '%' : ''}</td>
                        <td>${discount.duration}</td>
                    </tr>
                `;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">No discounts available</td></tr>';
                }

                $('#discountsContainer').html(html);
            }

            function savePlan() {
                let formData = new FormData(document.getElementById('planForm'));
                let data = {
                    name: formData.get('name'),
                    code: formData.get('code'),
                    description: formData.get('description'),
                    type: formData.get('type'),
                    billing_period: formData.get('billing_period'),
                    billing_interval: formData.get('billing_interval'),
                    is_active: formData.get('is_active') === 'on',
                    is_visible: formData.get('is_visible') === 'on',
                    is_featured: formData.get('is_featured') === 'on',
                    sort_order: formData.get('sort_order'),
                    features: [],
                    prices: [],
                    discounts: []
                };

                // Collect features
                $('#featuresContainer tr').each(function(index) {
                    let row = $(this);
                    if (!row.is(':hidden')) {
                        let feature = {
                            feature_id: row.find('select[name*="[feature_id]"]').val(),
                            value: row.find('input[name*="[value]"]').val(),
                            config: row.find('input[name*="[config]"]').val(),
                            sort_order: row.find('input[name*="[sort_order]"]').val()
                        };

                        let id = row.data('id');
                        if (id) {
                            feature.id = id;
                        }

                        data.features.push(feature);
                    }
                });

                // Collect prices
                $('#pricesContainer tr').each(function(index) {
                    let row = $(this);
                    if (!row.is(':hidden')) {
                        let price = {
                            currency: row.find('select[name*="[currency]"]').val(),
                            amount: row.find('input[name*="[amount]"]').val(),
                            interval: row.find('select[name*="[interval]"]').val(),
                            interval_count: row.find('input[name*="[interval_count]"]').val(),
                            usage_type: row.find('select[name*="[usage_type]"]').val(),
                            stripe_price_id: row.find('input[name*="[stripe_price_id]"]').val(),
                            active_from: row.find('input[name*="[active_from]"]').val(),
                            active_to: row.find('input[name*="[active_to]"]').val()
                        };

                        let id = row.data('id');
                        if (id) {
                            price.id = id;
                        }

                        data.prices.push(price);
                    }
                });

                // Collect discounts
                $('input[name="discounts[]"]:checked').each(function() {
                    data.discounts.push($(this).val());
                });

                let id = $('#plan_id').val();
                let request;

                if (id) {
                    request = axios.put(`/api/v1/plans/${id}`, data);
                } else {
                    request = axios.post('/api/v1/plans', data);
                }

                request.then(response => {
                        $('#planModal').modal('hide');
                        Swal.fire('Success!', 'Plan saved successfully.', 'success');
                        table.ajax.reload();
                    })
                    .catch(error => {
                        let message = 'Failed to save plan.';
                        if (error.response && error.response.data && error.response.data.message) {
                            message = error.response.data.message;
                        }
                        Swal.fire('Error!', message, 'error');
                    });
            }
        });
    </script>
@endpush
