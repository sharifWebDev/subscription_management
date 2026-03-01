@extends('website.layouts.app')

@section('title', 'Account Settings')

@section('content')
    <!-- Main Content -->
    <div class="col-lg-9">

        <div class="pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="p mb-0"> <i class="fas fa-home me-2"></i> Home
                    <i class="fas fa-chevron-right mx-2 text-muted small"></i>
                    <span class="text-muted small"> Settings</span>
                </span>
                <a href="{{ route('website.plans.index') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-plus me-2"></i> Subscribe to New Plan
                </a>
            </div>
        </div>
        <!-- Loading State -->
        <div id="settingsLoader" class="text-center py-5">
            <div class="loader"></div>
            <p class="mt-3 text-muted">Loading settings...</p>
        </div>

        <!-- Settings Content -->
        <div id="settingsContent" style="display: none;">
            <!-- Notification Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Notification Preferences</h5>
                </div>
                <div class="card-body">
                    <form id="notificationSettingsForm">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                                <div class="text-muted small">Receive email updates about your account</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="invoice_notifications" checked>
                                <label class="form-check-label" for="invoice_notifications">
                                    Invoice Notifications
                                </label>
                                <div class="text-muted small">Receive email when new invoices are generated</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="payment_notifications" checked>
                                <label class="form-check-label" for="payment_notifications">
                                    Payment Notifications
                                </label>
                                <div class="text-muted small">Receive email about payment status</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="marketing_emails">
                                <label class="form-check-label" for="marketing_emails">
                                    Marketing Emails
                                </label>
                                <div class="text-muted small">Receive updates about new features and offers</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="saveNotificationSettings">
                            <i class="fas fa-save me-2"></i>Save Preferences
                        </button>
                    </form>
                </div>
            </div>

            <!-- Billing Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Billing Settings</h5>
                </div>
                <div class="card-body">
                    <form id="billingSettingsForm">
                        <div class="mb-3">
                            <label class="form-label">Preferred Currency</label>
                            <select class="form-select" id="preferred_currency">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="BDT">BDT (৳)</option>
                                <option value="INR">INR (₹)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tax ID / VAT Number</label>
                            <input type="text" class="form-control" id="tax_id"
                                placeholder="Enter your tax ID if applicable">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_renew">
                                <label class="form-check-label" for="auto_renew">
                                    Auto-renew subscriptions by default
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tax_exempt">
                                <label class="form-check-label" for="tax_exempt">
                                    I am tax exempt
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="saveBillingSettings">
                            <i class="fas fa-save me-2"></i>Save Billing Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Security Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold">Change Password</h6>
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" required>
                                <div class="text-muted small mt-1">Must be at least 8 characters</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-warning" id="changePasswordBtn">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </form>
                    </div>

                    <hr>

                    <div>
                        <h6 class="fw-bold text-danger">Danger Zone</h6>
                        <p class="text-muted small">Once you delete your account, there is no going back. Please be
                            certain.</p>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="fas fa-exclamation-triangle me-2"></i>Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold text-danger">Warning: This action cannot be undone!</p>
                    <p>Deleting your account will:</p>
                    <ul>
                        <li>Cancel all active subscriptions</li>
                        <li>Remove all your personal information</li>
                        <li>Delete your payment methods</li>
                        <li>Remove access to all services</li>
                    </ul>
                    <div class="mb-3">
                        <label class="form-label">Type "DELETE" to confirm</label>
                        <input type="text" class="form-control" id="deleteConfirm" placeholder="DELETE">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAccountBtn" disabled>
                        <i class="fas fa-trash me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadSettings();

            // Enable delete button only when "DELETE" is typed
            $('#deleteConfirm').on('input', function() {
                $('#confirmDeleteAccountBtn').prop('disabled', $(this).val() !== 'DELETE');
            });

            // Notification settings form submit
            $('#notificationSettingsForm').submit(function(e) {
                e.preventDefault();
                saveNotificationSettings();
            });

            // Billing settings form submit
            $('#billingSettingsForm').submit(function(e) {
                e.preventDefault();
                saveBillingSettings();
            });

            // Change password form submit
            $('#changePasswordForm').submit(function(e) {
                e.preventDefault();
                changePassword();
            });

            // Confirm delete account
            $('#confirmDeleteAccountBtn').click(function() {
                deleteAccount();
            });

            function loadSettings() {
                $('#settingsLoader').show();
                $('#settingsContent').hide();

                // Load user settings from API
                axios.get('/user/settings')
                    .then(response => {
                        const settings = response.data.data || {};

                        // Populate notification settings
                        $('#email_notifications').prop('checked', settings.email_notifications ?? true);
                        $('#invoice_notifications').prop('checked', settings.invoice_notifications ?? true);
                        $('#payment_notifications').prop('checked', settings.payment_notifications ?? true);
                        $('#marketing_emails').prop('checked', settings.marketing_emails ?? false);

                        // Populate billing settings
                        $('#preferred_currency').val(settings.preferred_currency || 'USD');
                        $('#tax_id').val(settings.tax_id || '');
                        $('#auto_renew').prop('checked', settings.auto_renew ?? true);
                        $('#tax_exempt').prop('checked', settings.is_tax_exempt ?? false);

                        $('#settingsLoader').hide();
                        $('#settingsContent').show();
                    })
                    .catch(error => {
                        console.error('Error loading settings:', error);
                        $('#settingsLoader').hide();
                        // Show default values
                        $('#settingsContent').show();
                    });
            }

            function saveNotificationSettings() {
                const data = {
                    email_notifications: $('#email_notifications').is(':checked'),
                    invoice_notifications: $('#invoice_notifications').is(':checked'),
                    payment_notifications: $('#payment_notifications').is(':checked'),
                    marketing_emails: $('#marketing_emails').is(':checked')
                };

                $('#saveNotificationSettings').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                axios.put('/user/notification-settings', data)
                    .then(response => {
                        toastr.success('Notification preferences saved successfully');
                    })
                    .catch(error => {
                        toastr.error('Failed to save preferences');
                    })
                    .finally(() => {
                        $('#saveNotificationSettings').prop('disabled', false)
                            .html('<i class="fas fa-save me-2"></i>Save Preferences');
                    });
            }

            function saveBillingSettings() {
                const data = {
                    preferred_currency: $('#preferred_currency').val(),
                    tax_id: $('#tax_id').val(),
                    auto_renew: $('#auto_renew').is(':checked'),
                    is_tax_exempt: $('#tax_exempt').is(':checked')
                };

                $('#saveBillingSettings').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                axios.put('/user/billing-settings', data)
                    .then(response => {
                        toastr.success('Billing settings saved successfully');
                    })
                    .catch(error => {
                        toastr.error('Failed to save billing settings');
                    })
                    .finally(() => {
                        $('#saveBillingSettings').prop('disabled', false)
                            .html('<i class="fas fa-save me-2"></i>Save Billing Settings');
                    });
            }

            function changePassword() {
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#new_password_confirmation').val();

                if (newPassword !== confirmPassword) {
                    toastr.error('New passwords do not match');
                    return;
                }

                if (newPassword.length < 8) {
                    toastr.error('Password must be at least 8 characters');
                    return;
                }

                const data = {
                    current_password: $('#current_password').val(),
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                };

                $('#changePasswordBtn').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Changing...');

                axios.post('/user/change-password', data)
                    .then(response => {
                        toastr.success('Password changed successfully');
                        $('#changePasswordForm')[0].reset();
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Failed to change password';
                        toastr.error(message);
                    })
                    .finally(() => {
                        $('#changePasswordBtn').prop('disabled', false)
                            .html('<i class="fas fa-key me-2"></i>Change Password');
                    });
            }

            function deleteAccount() {
                $('#confirmDeleteAccountBtn').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');

                axios.delete('/user/account')
                    .then(response => {
                        toastr.success('Account deleted successfully');
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 2000);
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Failed to delete account';
                        toastr.error(message);
                        $('#confirmDeleteAccountBtn').prop('disabled', false)
                            .html('<i class="fas fa-trash me-2"></i>Delete Account');
                    });
            }
        });
    </script>
@endpush
