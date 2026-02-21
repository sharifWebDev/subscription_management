@extends('website.layouts.app')

@section('title', 'My Profile')

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
                            <a href="{{ route('website.dashboard.profile') }}" class="btn btn-outline-primary btn-sm active">
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
                       class="list-group-item list-group-item-action">
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
                                 <span class="text-muted small"> My Profile</span>
                            </span>
                        <a href="{{ route('website.plans.index') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-plus me-2"></i> Subscribe to New Plan
                        </a>
                    </div>
                </div>
                <!-- Profile Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" enctype="multipart/form-data">
                            @csrf

                            <!-- Profile Picture -->
                            <div class="mb-4 text-center">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ auth()->user()->avatar ?? 'https://via.placeholder.com/120' }}"
                                         id="profilePreview"
                                         class="rounded-circle border" width="120" height="120" alt="Profile">
                                    <label for="avatar" class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-2 cursor-pointer" style="cursor: pointer;">
                                        <i class="fas fa-camera text-white"></i>
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                                </div>
                                <p class="text-muted small mt-2">Click the camera icon to change profile picture</p>
                            </div>

                            <!-- Basic Information -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="{{ explode(' ', auth()->user()->name)[0] ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="{{ explode(' ', auth()->user()->name)[1] ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ auth()->user()->email }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="{{ auth()->user()->phone ?? '' }}">
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Billing Address -->
                            <h6 class="fw-bold mb-3">Billing Address</h6>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Address Line 1</label>
                                    <input type="text" class="form-control" id="address_line1" name="address_line1"
                                           value="{{ auth()->user()->billing_address->line1 ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control" id="address_line2" name="address_line2"
                                           value="{{ auth()->user()->billing_address->line2 ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                           value="{{ auth()->user()->billing_address->city ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                           value="{{ auth()->user()->billing_address->state ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                           value="{{ auth()->user()->billing_address->postal_code ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <select class="form-select" id="country" name="country">
                                        <option value="">Select Country</option>
                                        <option value="US" {{ (auth()->user()->billing_address->country ?? '') == 'US' ? 'selected' : '' }}>United States</option>
                                        <option value="GB" {{ (auth()->user()->billing_address->country ?? '') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                        <option value="CA" {{ (auth()->user()->billing_address->country ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                                        <option value="AU" {{ (auth()->user()->billing_address->country ?? '') == 'AU' ? 'selected' : '' }}>Australia</option>
                                        <option value="BD" {{ (auth()->user()->billing_address->country ?? '') == 'BD' ? 'selected' : '' }}>Bangladesh</option>
                                        <option value="IN" {{ (auth()->user()->billing_address->country ?? '') == 'IN' ? 'selected' : '' }}>India</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Billing Type</label>
                                    <select class="form-select" id="billing_type" name="billing_type">
                                        <option value="personal" {{ auth()->user()->billing_type == 'personal' ? 'selected' : '' }}>Personal</option>
                                        <option value="business" {{ auth()->user()->billing_type == 'business' ? 'selected' : '' }}>Business</option>
                                        <option value="enterprise" {{ auth()->user()->billing_type == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Business Information (shown only if business type) -->
                            <div id="businessInfo" style="display: {{ auth()->user()->billing_type == 'personal' ? 'none' : 'block' }};">
                                <hr>
                                <h6 class="fw-bold mb-3">Business Information</h6>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                               value="{{ auth()->user()->metadata->company ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tax ID / VAT Number</label>
                                        <input type="text" class="form-control" id="tax_id" name="tax_id"
                                               value="{{ auth()->user()->tax_id ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide business info based on billing type
        $('#billing_type').change(function() {
            if ($(this).val() === 'personal') {
                $('#businessInfo').slideUp();
            } else {
                $('#businessInfo').slideDown();
            }
        });

        // Preview profile picture before upload
        $('#avatar').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        $('#profileForm').submit(function(e) {
            e.preventDefault();
            saveProfile();
        });

        function saveProfile() {
            const formData = new FormData(document.getElementById('profileForm'));

            // Append PUT method
            formData.append('_method', 'PUT');

            $('#saveProfileBtn').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            axios.post('/user/profile', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                toastr.success('Profile updated successfully');

                // Update displayed name if changed
                const firstName = $('#first_name').val();
                const lastName = $('#last_name').val();
                if (firstName || lastName) {
                    $('.navbar .user-name').text(firstName + ' ' + lastName);
                }
            })
            .catch(error => {
                const message = error.response?.data?.message || 'Failed to update profile';
                toastr.error(message);

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                }
            })
            .finally(() => {
                $('#saveProfileBtn').prop('disabled', false)
                    .html('<i class="fas fa-save me-2"></i>Save Changes');
            });
        }
    });
</script>
@endpush
