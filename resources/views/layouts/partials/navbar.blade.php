<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid position-relative">

        <!-- Left -->
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#sidebar" role="button" data-lte-toggle="sidebar">
                    <i class="bi bi-list"></i>
                </a>
            </li>

            <li class="nav-item d-none d-sm-inline-block">
                <a href="/" class="nav-link goweb" target="_blank">
                    <i class="fa-solid fa-earth-americas"></i>
                </a>
            </li>
        </ul>

        <!-- Center Title -->
        <div class="mx-auto">
            <h1 class="h5 text-dark mb-0 text-nowrap">
                @yield('page-title', '')
            </h1>
        </div>


        <!-- Right navbar links -->
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <a id="optimize" class="btn btn-info btn-sm me-3" href="{{ url('/admin/artisan/optimize') }}">
                    Optimize
                </a>
            </li>

            <script>
                document.getElementById('optimize').addEventListener('click', function(event) {
                    event.preventDefault();

                    const url = this.href;

                    axios.get(url)
                        .then(function(response) {
                            toastr.success(response.data.message);
                        })
                        .catch(function(error) {
                            console.error('There was an error!', error);
                            alert('An error occurred. Check the console for details.');
                        });
                });
            </script>
            
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                    <i class="far fa-comments"></i>
                    <span class="badge bg-danger navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media d-flex align-items-center">
                            <img src="../backend/dist/img/user1-128x128.jpg" alt="User Avatar" class="me-3 img-size-50 rounded-circle">
                            <div class="media-body flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    Brad Diesel
                                    <span class="float-end text-sm text-danger"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm mb-0">Call me whenever you can...</p>
                                <p class="text-sm text-muted mb-0"><i class="me-1 far fa-clock"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media d-flex align-items-center">
                            <img src="../backend/dist/img/user8-128x128.jpg" alt="User Avatar" class="me-3 img-size-50 rounded-circle">
                            <div class="media-body flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    John Pierce
                                    <span class="float-end text-sm text-muted"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm mb-0">I got your message bro</p>
                                <p class="text-sm text-muted mb-0"><i class="me-1 far fa-clock"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media d-flex align-items-center">
                            <img src="../backend/dist/img/user3-128x128.jpg" alt="User Avatar" class="me-3 img-size-50 rounded-circle">
                            <div class="media-body flex-grow-1">
                                <h3 class="dropdown-item-title">
                                    Nora Silvester
                                    <span class="float-end text-sm text-warning"><i class="fas fa-star"></i></span>
                                </h3>
                                <p class="text-sm mb-0">The subject goes here</p>
                                <p class="text-sm text-muted mb-0"><i class="me-1 far fa-clock"></i> 4 Hours Ago</p>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer text-center">See All Messages</a>
                </div>
            </li>
            
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                    <i class="far fa-bell text-secondary"></i>
                    <span class="badge bg-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <span class="dropdown-item dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="me-2 fas fa-envelope"></i> 4 new messages
                        <span class="float-end text-sm text-muted">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="me-2 fas fa-users"></i> 8 friend requests
                        <span class="float-end text-sm text-muted">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="me-2 fas fa-file"></i> 3 new reports
                        <span class="float-end text-sm text-muted">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer text-center">See All Notifications</a>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fa-solid fa-expand"></i>
                </a>
            </li>
              
            <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle me-2" height="45" width="45" src="{{ \Illuminate\Support\Facades\Auth::user()->profile_photo_url ?? "" }}" alt="{{ \Illuminate\Support\Facades\Auth::user()->name ?? "" }}" style="object-fit: cover;">
                        <div class="d-flex flex-column">
                            <div class="text-gray-900 fw-semibold">{{ \Illuminate\Support\Facades\Auth::user()->name ?? "" }}</div>
                            <div class="text-sm text-gray-700">{{ \Illuminate\Support\Facades\Auth::user()->role ?? "" }}</div>
                        </div>
                        <i class="fa fa-angle-down ms-2"></i>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <div class="dropdown-header text-muted">
                            {{ __('Manage Account') }}
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>