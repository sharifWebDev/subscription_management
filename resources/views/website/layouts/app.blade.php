<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-base-url" content="{{ url('/api/v1') }}">

    <title>@yield('title', 'Subscription Plans') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .pricing-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 1rem;
            overflow: hidden;
        }
        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .pricing-card.featured {
            border: 2px solid #0d6efd;
            transform: scale(1.02);
        }
        .pricing-card.featured:hover {
            transform: scale(1.02) translateY(-5px);
        }
        .pricing-header {
            padding: 1.5rem;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .pricing-header.basic {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        .pricing-header.pro {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
        .pricing-header.enterprise {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        }
        .price-tag {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .price-period {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list i {
            width: 20px;
            margin-right: 10px;
        }
        .btn-subscribe {
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 50px;
        }
        .badge-popular {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #ffc107;
            color: #000;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .comparison-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .comparison-table td, .comparison-table th {
            padding: 1rem;
            vertical-align: middle;
        }
        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            margin-right: 1rem;
        }
        .faq-section {
            background-color: white;
            border-radius: 1rem;
            padding: 2rem;
        }
        .loader {
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3498db;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="fas fa-crown text-primary me-2"></i>
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('website.plans.*') ? 'active' : '' }}"
                           href="{{ route('website.plans.index') }}">
                            <i class="fas fa-tag me-1"></i> Plans
                        </a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        <div class="container mt-4">
            <div class="row">
               @if(!request()->routeIs('website.plans.*') && !request()->routeIs('website.checkout.*') && !request()->routeIs('website.plan.*'))
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
                    <a href="{{ route('website.dashboard.index') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-tags me-2"></i>Dashboard
                    </a>
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
                    {{-- crud-generates --}}
                    <a href="{{ url('/crud-generator') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-cog me-2"></i>crud-generator
                    </a>
                    <a href="{{ route('website.dashboard.settings') }}"
                       class="list-group-item list-group-item-action">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>
                @endif
        @yield('content')
        </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-5 py-4 border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-decoration-none text-muted me-3">Privacy Policy</a>
                    <a href="#" class="text-decoration-none text-muted me-3">Terms of Service</a>
                    <a href="#" class="text-decoration-none text-muted">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Initialize Axios -->
    <script>
        // Setup axios defaults
        const API_BASE_URL = document.querySelector('meta[name="api-base-url"]').getAttribute('content');

        axios.defaults.baseURL = API_BASE_URL;
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['Accept'] = 'application/json';
        axios.defaults.headers.common['Content-Type'] = 'application/json';

        // Add request interceptor for loading state
        axios.interceptors.request.use(function (config) {
            if (config.showLoader !== false) {
                showLoader();
            }
            return config;
        }, function (error) {
            hideLoader();
            return Promise.reject(error);
        });

        // Add response interceptor
        axios.interceptors.response.use(function (response) {
            hideLoader();
            return response;
        }, function (error) {
            hideLoader();
            handleApiError(error);
            return Promise.reject(error);
        });

        // Global functions
        function showLoader() {
            if ($('#global-loader').length === 0) {
                $('body').append('<div id="global-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div class="loader"></div></div>');
            }
            $('#global-loader').fadeIn();
        }

        function hideLoader() {
            $('#global-loader').fadeOut();
        }

        function handleApiError(error) {
            let message = 'An error occurred. Please try again.';

            if (error.response) {
                if (error.response.data && error.response.data.message) {
                    message = error.response.data.message;
                } else if (error.response.data && error.response.data.errors) {
                    message = Object.values(error.response.data.errors).flat().join('\n');
                }
            } else if (error.request) {
                message = 'No response from server. Please check your connection.';
            }

            toastr.error(message, 'Error');
        }

        // Toastr configuration
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };
    </script>

    @stack('scripts')

    @if(session('success'))
    <script>
        toastr.success('{{ session('success') }}', 'Success');
    </script>
    @endif

    @if(session('error'))
    <script>
        toastr.error('{{ session('error') }}', 'Error');
    </script>
    @endif

    <script>
        // Handle payment status from query parameters
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentStatus = urlParams.get('payment_status');

            if (paymentStatus === 'success') {
                toastr.success('Payment successful! Your subscription is now active.', 'Success');
                // Clean up URL
                const url = new URL(window.location);
                url.searchParams.delete('payment_status');
                window.history.replaceState({}, document.title, url);
            } else if (paymentStatus === 'failed') {
                toastr.error('Payment failed. Please try again.', 'Error');
                const url = new URL(window.location);
                url.searchParams.delete('payment_status');
                window.history.replaceState({}, document.title, url);
            } else if (paymentStatus === 'cancelled') {
                toastr.info('Payment was cancelled.', 'Information');
                const url = new URL(window.location);
                url.searchParams.delete('payment_status');
                window.history.replaceState({}, document.title, url);
            }
        });
    </script>
</body>
</html>
