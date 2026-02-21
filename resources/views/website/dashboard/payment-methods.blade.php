@extends('website.layouts.app')

@section('title', 'Payment Methods')

@section('content')

<!-- Dashboard Content -->
<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <img src="{{ auth()->user()->avatar ?? 'https://via.placeholder.com/100' }}"
                             class="rounded-circle mb-3" width="80" height="80" alt="Profile">
                        <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>
                        <div class="d-grid">
                            <a href="{{ route('website.dashboard.profile') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <div class="list-group shadow-sm">
                    <a href="{{ route('website.dashboard.subscriptions') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-tags me-2"></i>My Subscriptions
                    </a>
                    <a href="{{ route('website.dashboard.invoices') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-file-invoice me-2"></i>Invoices
                    </a>
                    <a href="{{ route('website.dashboard.payment-methods') }}"
                       class="list-group-item list-group-item-action active">
                        <i class="fas fa-credit-card me-2"></i>Payment Methods
                    </a>
                    <a href="{{ route('website.dashboard.usage') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2"></i>Usage Statistics
                    </a>
                    <a href="{{ route('website.dashboard.settings') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                 <div class="pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="p mb-0"> <i class="fas fa-home me-2"></i> Home
                            <i class="fas fa-chevron-right mx-2 text-muted small"></i>
                                 <span class="text-muted small"> Payment Methods</span>
                            </span>
                        <a href="{{ route('website.plans.index') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-plus me-2"></i> Subscribe to New Plan
                        </a>
                    </div>
                </div>
                <!-- Loading State -->
                <div id="paymentMethodsLoader" class="text-center py-5">
                    <div class="loader"></div>
                    <p class="mt-3 text-muted">Loading payment methods...</p>
                </div>

                <!-- Payment Methods Content -->
                <div id="paymentMethodsContent" style="display: none;">
                    <!-- Add New Payment Method Button -->
                    <div class="mb-4">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
                            <i class="fas fa-plus-circle me-2"></i>Add New Payment Method
                        </button>
                    </div>

                    <!-- Payment Methods Grid -->
                    <div class="row g-4" id="paymentMethodsGrid"></div>

                    <!-- No Payment Methods Message -->
                    <div id="noPaymentMethodsMessage" class="text-center py-5" style="display: none;">
                        <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                        <h3>No Payment Methods</h3>
                        <p class="text-muted mb-4">You haven't added any payment methods yet.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
                            <i class="fas fa-plus-circle me-2"></i>Add Payment Method
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Payment Method Modal -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPaymentMethodForm">
                <div class="modal-body">
                    <!-- Payment Method Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payment Method Type</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="payment-type-card">
                                    <input type="radio" class="btn-check" name="payment_type"
                                           id="type_card" value="card" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary w-100 py-3" for="type_card">
                                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                                        <br>Credit Card
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-type-card">
                                    <input type="radio" class="btn-check" name="payment_type"
                                           id="type_bank" value="bank_account" autocomplete="off">
                                    <label class="btn btn-outline-primary w-100 py-3" for="type_bank">
                                        <i class="fas fa-university fa-2x mb-2"></i>
                                        <br>Bank Account
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Details -->
                    <div id="cardDetails">
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" name="card_number"
                                   placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" name="expiry"
                                       placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" name="cvv"
                                       placeholder="123" maxlength="4">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Cardholder Name</label>
                            <input type="text" class="form-control" name="cardholder_name"
                                   value="{{ auth()->user()->name }}">
                        </div>
                    </div>

                    <!-- Bank Account Details -->
                    <div id="bankDetails" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" class="form-control" name="bank_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-control" name="account_number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Routing Number</label>
                            <input type="text" class="form-control" name="routing_number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Type</label>
                            <select class="form-select" name="account_type">
                                <option value="checking">Checking</option>
                                <option value="savings">Savings</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_default" id="is_default" checked>
                        <label class="form-check-label" for="is_default">Set as default payment method</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addPaymentMethodBtn">
                        <i class="fas fa-plus-circle me-2"></i>Add Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment method? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .payment-type-card {
        cursor: pointer;
        transition: all 0.3s;
    }
    .payment-type-card .btn-check:checked + .btn {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .payment-type-card .btn {
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .payment-type-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .payment-method-card {
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    .payment-method-card.default {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
    .payment-method-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        loadPaymentMethods();

        // Toggle between card and bank details
        $('input[name="payment_type"]').change(function() {
            if ($(this).val() === 'card') {
                $('#cardDetails').show();
                $('#bankDetails').hide();
                $('#cardDetails input').prop('required', true);
                $('#bankDetails input, #bankDetails select').prop('required', false);
            } else {
                $('#cardDetails').hide();
                $('#bankDetails').show();
                $('#cardDetails input').prop('required', false);
                $('#bankDetails input, #bankDetails select').prop('required', true);
            }
        });

        // Add payment method form submit
        $('#addPaymentMethodForm').submit(function(e) {
            e.preventDefault();
            addPaymentMethod();
        });

        function loadPaymentMethods() {
            $('#paymentMethodsLoader').show();
            $('#paymentMethodsContent').hide();
            $('#noPaymentMethodsMessage').hide();

            axios.get('/payment-methods')
                .then(response => {
                    const methods = response.data.data || [];

                    $('#paymentMethodsLoader').hide();

                    if (methods.length > 0) {
                        renderPaymentMethods(methods);
                        $('#paymentMethodsContent').show();
                    } else {
                        $('#noPaymentMethodsMessage').show();
                        $('#paymentMethodsContent').show();
                    }
                })
                .catch(error => {
                    console.error('Error loading payment methods:', error);
                    $('#paymentMethodsLoader').hide();
                    $('#noPaymentMethodsMessage').show();
                    $('#noPaymentMethodsMessage h3').text('Error Loading Payment Methods');
                    $('#noPaymentMethodsMessage p').text('Please try again later.');
                });
        }

        function renderPaymentMethods(methods) {
            let html = '';

            methods.forEach(method => {
                const isDefault = method.is_default ? 'default' : '';
                const last4 = method.card_last4 || method.bank_account_last4 || '****';
                const brand = method.card_brand || method.bank_name || method.type;
                const expiry = method.card_exp_month && method.card_exp_year
                    ? `${method.card_exp_month}/${method.card_exp_year.toString().slice(-2)}`
                    : '';

                let icon = 'fa-credit-card';
                if (method.type === 'bank_account') icon = 'fa-university';
                if (method.type === 'paypal') icon = 'fa-paypal';
                if (method.type === 'bkash') icon = 'fa-mobile-alt';

                html += `
                    <div class="col-md-6">
                        <div class="card payment-method-card ${isDefault}" style="min-height: 190px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="fas ${icon} fa-2x text-primary"></i>
                                    </div>
                                    ${method.is_default ?
                                        '<span class="badge bg-primary">Default</span>' :
                                        '<span class="badge bg-secondary">Secondary</span>'
                                    }
                                </div>

                                <h6 class="card-title mb-1">
                                    ${method.card_brand || method.bank_name || method.type}
                                    **** ${last4}
                                </h6>

                                ${expiry ? `<p class="text-muted small mb-2">Expires: ${expiry}</p>` : ''}

                                <p class="text-muted small mb-3">
                                    Added: ${new Date(method.created_at).toLocaleDateString()}
                                </p>

                                <div class="d-flex justify-content-between">
                                    <div>
                                        ${!method.is_default ? `
                                            <button class="btn btn-sm btn-outline-primary" onclick="setDefault(${method.id})">
                                                <i class="fas fa-star me-1"></i>Set Default
                                            </button>
                                        ` : ''}
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(${method.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#paymentMethodsGrid').html(html);
        }

        function addPaymentMethod() {
            const formData = new FormData(document.getElementById('addPaymentMethodForm'));
            const data = {
                type: formData.get('payment_type'),
                is_default: formData.get('is_default') === 'on',
                details: {}
            };

            if (data.type === 'card') {
                data.details = {
                    card_number: formData.get('card_number'),
                    expiry: formData.get('expiry'),
                    cvv: formData.get('cvv'),
                    cardholder_name: formData.get('cardholder_name')
                };
            } else {
                data.details = {
                    bank_name: formData.get('bank_name'),
                    account_number: formData.get('account_number'),
                    routing_number: formData.get('routing_number'),
                    account_type: formData.get('account_type')
                };
            }

            $('#addPaymentMethodBtn').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Adding...');

            axios.post('/payment-methods', data)
                .then(response => {
                    toastr.success('Payment method added successfully');
                    $('#addPaymentMethodModal').modal('hide');
                    loadPaymentMethods();
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'Failed to add payment method';
                    toastr.error(message);
                })
                .finally(() => {
                    $('#addPaymentMethodBtn').prop('disabled', false)
                        .html('<i class="fas fa-plus-circle me-2"></i>Add Payment Method');
                });
        }

        // Set default payment method
        window.setDefault = function(id) {
            axios.put(`/payment-methods/${id}/default`)
                .then(response => {
                    toastr.success('Default payment method updated');
                    loadPaymentMethods();
                })
                .catch(error => {
                    toastr.error('Failed to update default payment method');
                });
        };

        // Delete confirmation
        let deleteId = null;

        window.confirmDelete = function(id) {
            deleteId = id;
            $('#deleteConfirmModal').modal('show');
        };

        $('#confirmDeleteBtn').click(function() {
            if (!deleteId) return;

            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

            axios.delete(`/payment-methods/${deleteId}`)
                .then(response => {
                    toastr.success('Payment method deleted successfully');
                    $('#deleteConfirmModal').modal('hide');
                    loadPaymentMethods();
                })
                .catch(error => {
                    toastr.error('Failed to delete payment method');
                })
                .finally(() => {
                    $(this).prop('disabled', false).html('<i class="fas fa-trash me-2"></i>Delete');
                    deleteId = null;
                });
        });
    });
</script>
@endpush
