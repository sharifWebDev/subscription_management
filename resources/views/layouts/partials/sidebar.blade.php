<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <style>
        .sidebar-menu .nav-link {
            color: #fff !important;
            font-weight: 400 !important;
            font-size: 11px !important;
        }
    </style>
    <div class="sidebar-brand">
        <a href="{{ url('admin/dashboard') }}" class="brand-link">
            <img src="{{ asset('backend/dist/img/AdminLTELogo.png') }}" alt="Logo"
                class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">Admin</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item">
                    <a href="{{ url('admin/dashboard') }}"
                        class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p> Dashboard </p>
                    </a>
                </li>




                <li class="nav-item">
                    <a href="{{ url('admin/plans') }}"
                        class="nav-link {{ request()->is('admin/plans*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Plans</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/plan-features') }}"
                        class="nav-link {{ request()->is('admin/plan-features*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Plan features</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('admin/plan-prices') }}"
                        class="nav-link {{ request()->is('admin/plan-prices*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Plan prices</p>
                    </a>
                </li>




                <li class="nav-item">
                    <a href="{{ url('admin/discounts') }}"
                        class="nav-link {{ request()->is('admin/discounts*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Discounts</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/features') }}"
                        class="nav-link {{ request()->is('admin/features*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Features</p>
                    </a>
                </li>

                
                <li>
                    <a href="">------</a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/subscriptions') }}"
                        class="nav-link {{ request()->is('admin/subscriptions*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Subscriptions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin/subscription-items') }}"
                        class="nav-link {{ request()->is('admin/subscription-items*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Subscription items</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin/subscription-orders') }}"
                        class="nav-link {{ request()->is('admin/subscription-orders*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Subscription orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin/subscription-order-items') }}"
                        class="nav-link {{ request()->is('admin/subscription-order-items*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Subscription order items</p>
                    </a>
                </li>



                <li class="nav-item">
                    <a href="{{ url('admin/usage-records') }}"
                        class="nav-link {{ request()->is('admin/usage-records*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Usage records</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/metered-usage-aggregates') }}"
                        class="nav-link {{ request()->is('admin/metered-usage-aggregates*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Metered usage aggregates</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/refunds') }}"
                        class="nav-link {{ request()->is('admin/refunds*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Refunds</p>
                    </a>
                </li>




                <li class="nav-item">
                    <a href="{{ url('admin/invoices') }}"
                        class="nav-link {{ request()->is('admin/invoices*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Invoices</p>
                    </a>
                </li>

                 <li class="nav-item">
                    <a href="{{ url('admin/rate-limits') }}"
                        class="nav-link {{ request()->is('admin/rate-limits*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Rate limits</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/subscription-events') }}"
                        class="nav-link {{ request()->is('admin/subscription-events*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Subscription events</p>
                    </a>
                </li>

                <li>
                    <a href="#">--------------</a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('admin/payment-gateways') }}"
                        class="nav-link {{ request()->is('admin/payment-gateways*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment gateways</p>
                    </a>
                </li>



                <li class="nav-item">
                    <a href="{{ url('admin/payment-methods') }}"
                        class="nav-link {{ request()->is('admin/payment-methods*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment methods</p>
                    </a>
                </li>



                <li class="nav-item">
                    <a href="{{ url('admin/payment-masters') }}"
                        class="nav-link {{ request()->is('admin/payment-masters*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment masters</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('admin/payment-children') }}"
                        class="nav-link {{ request()->is('admin/payment-children*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment children</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('admin/payment-allocations') }}"
                        class="nav-link {{ request()->is('admin/payment-allocations*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment allocations</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/payment-webhook-logs') }}"
                        class="nav-link {{ request()->is('admin/payment-webhook-logs*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment webhook logs</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ url('admin/payments') }}"
                        class="nav-link {{ request()->is('admin/payments*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('admin/payment-transactions') }}"
                        class="nav-link {{ request()->is('admin/payment-transactions*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-angle-double-right"></i>
                        <p>Payment transactions</p>
                    </a>
                </li>



                {{-- URLPath --}}

                {{-- reportURLPath --}}

            </ul>
        </nav>
    </div>
</aside>
