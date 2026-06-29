<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" data-navigation-type="default" data-navbar-horizontal-shape="default">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', \App\Support\Branding::name())</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ \App\Support\Branding::url('logo-white-bg.png') }}">
    <link rel="icon" type="image/png" href="{{ \App\Support\Branding::url('logo-white-bg.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ \App\Support\Branding::url('logo-white-bg.png') }}">
    <meta name="theme-color" content="#ffffff">

    <script src="{{ asset('phoenix/vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('phoenix/assets/js/config.js') }}"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('phoenix/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('phoenix/assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('phoenix/assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('phoenix/assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('phoenix/assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>
    <style>
        .form-label{
            padding-left: 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <main class="main" id="top">
        <nav class="navbar navbar-vertical navbar-expand-lg">
            <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                <div class="navbar-vertical-content">
                    <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                        <li class="nav-item">
                            <div class="nav-item-wrapper">
                                <a class="nav-link label-1 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span data-feather="pie-chart"></span></span>
                                        <span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span>
                                    </div>
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <p class="navbar-vertical-label">Operations</p>
                            <hr class="navbar-vertical-line" />
                            @can('view members')
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('members.*') ? 'active' : '' }}" href="{{ route('members.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="users"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Members</span></span>
                                        </div>
                                    </a>
                                </div>
                            @endcan
                            @can('view deposits')
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('deposits.*') ? 'active' : '' }}" href="{{ route('deposits.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="dollar-sign"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Deposits</span></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('deposit-status') ? 'active' : '' }}" href="{{ route('deposit-status') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="check-square"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Deposit Status</span></span>
                                        </div>
                                    </a>
                                </div>
                            @endcan
                            <div class="nav-item-wrapper">
                                <a class="nav-link label-1 {{ request()->routeIs('constitution') ? 'active' : '' }}" href="{{ route('constitution') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span data-feather="book"></span></span>
                                        <span class="nav-link-text-wrapper"><span class="nav-link-text">Constitution</span></span>
                                    </div>
                                </a>
                            </div>
                            @can('manage shares')
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('shares.distribution') ? 'active' : '' }}" href="{{ route('shares.distribution') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="trending-up"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Share Distribution</span></span>
                                        </div>
                                    </a>
                                </div>
                            @endcan
                            @can('view expenses')
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="file-text"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Expenses</span></span>
                                        </div>
                                    </a>
                                </div>
                            @endcan
                            @can('view investments')
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('investments.*') ? 'active' : '' }}" href="{{ route('investments.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="trending-up"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Investments</span></span>
                                        </div>
                                    </a>
                                </div>
                            @endcan
                            @if(auth()->user()->canAny(['view deposits', 'view expenses', 'view investments']))
                                <div class="nav-item-wrapper">
                                    <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="#nv-reports" role="button" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}" aria-controls="nv-reports">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div>
                                            <span class="nav-link-icon"><span data-feather="file-text"></span></span>
                                            <span class="nav-link-text">Reports</span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent {{ request()->routeIs('reports.*') ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-reports">
                                            @can('view deposits')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('reports.deposits') ? 'active' : '' }}" href="{{ route('reports.deposits') }}">
                                                        <div class="d-flex align-items-center"><span class="nav-link-text">Deposit Report</span></div>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('view expenses')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('reports.expenses') ? 'active' : '' }}" href="{{ route('reports.expenses') }}">
                                                        <div class="d-flex align-items-center"><span class="nav-link-text">Expense Report</span></div>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('view investments')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('reports.investments') ? 'active' : '' }}" href="{{ route('reports.investments') }}">
                                                        <div class="d-flex align-items-center"><span class="nav-link-text">Investment Report</span></div>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            @can('view accounting')
                                <div class="nav-item-wrapper">
                                    <a class="nav-link dropdown-indicator label-1 {{ request()->routeIs('accounting.*') ? 'active' : '' }}" href="#nv-accounting" role="button" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('accounting.*') ? 'true' : 'false' }}" aria-controls="nv-accounting">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div>
                                            <span class="nav-link-icon"><span data-feather="book"></span></span>
                                            <span class="nav-link-text">Accounting</span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent {{ request()->routeIs('accounting.*') ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-accounting">
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('accounting.reports.dashboard') ? 'active' : '' }}" href="{{ route('accounting.reports.dashboard') }}">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Dashboard</span></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('accounting.chart-of-accounts.*') ? 'active' : '' }}" href="{{ route('accounting.chart-of-accounts.index') }}">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Chart of Accounts</span></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('accounting.journal-vouchers.*') ? 'active' : '' }}" href="{{ route('accounting.journal-vouchers.index') }}">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Journal Vouchers</span></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('accounting.reports.general-ledger') ? 'active' : '' }}" href="{{ route('accounting.reports.general-ledger') }}">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">General Ledger</span></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link {{ request()->routeIs('accounting.reports.trial-balance') ? 'active' : '' }}" href="{{ route('accounting.reports.trial-balance') }}">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Trial Balance</span></div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endcan
                        </li>
                        @if (auth()->user()->can('manage permissions') || auth()->user()->can('manage roles') || auth()->user()->can('manage users') || auth()->user()->can('manage expenses'))
                            <li class="nav-item">
                                <p class="navbar-vertical-label">Administration</p>
                                <hr class="navbar-vertical-line" />
                                <div class="nav-item-wrapper">
                                    <a class="nav-link dropdown-indicator label-1" href="#nv-user-management" role="button" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('user-management.*') ? 'true' : 'false' }}" aria-controls="nv-user-management">
                                        <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div>
                                            <span class="nav-link-icon"><span data-feather="shield"></span></span>
                                            <span class="nav-link-text">User Management</span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent {{ request()->routeIs('user-management.*') ? 'show' : '' }}" data-bs-parent="#navbarVerticalCollapse" id="nv-user-management">
                                            @can('manage permissions')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('user-management.permissions.*') ? 'active' : '' }}" href="{{ route('user-management.permissions.index') }}">
                                                        <div class="d-flex align-items-center"><span class="nav-link-text">Permissions</span></div>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('manage roles')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('user-management.roles.*') ? 'active' : '' }}" href="{{ route('user-management.roles.index') }}">
                                                        <div class="d-flex align-items-center"><span class="nav-link-text">Roles</span></div>
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('manage users')
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('user-management.users.*') ? 'active' : '' }}" href="{{ route('user-management.users.index') }}">
                                                        <div class="d-flex align-items-center"><span class="nav-link-text">Users</span></div>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                                @can('manage expenses')
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ request()->routeIs('expense-categories.*') ? 'active' : '' }}" href="{{ route('expense-categories.index') }}">
                                            <div class="d-flex align-items-center">
                                                <span class="nav-link-icon"><span data-feather="tag"></span></span>
                                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Expense Categories</span></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}" href="{{ route('payment-methods.index') }}">
                                            <div class="d-flex align-items-center">
                                                <span class="nav-link-icon"><span data-feather="credit-card"></span></span>
                                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Payment Methods</span></span>
                                            </div>
                                        </a>
                                    </div>
                                @endcan
                                @can('manage investment types')
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ request()->routeIs('investment-types.*') ? 'active' : '' }}" href="{{ route('investment-types.index') }}">
                                            <div class="d-flex align-items-center">
                                                <span class="nav-link-icon"><span data-feather="layers"></span></span>
                                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Investment Types</span></span>
                                            </div>
                                        </a>
                                    </div>
                                @endcan
                            </li>
                        @endif
                        @can('manage organization profile')
                            <li class="nav-item">
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ request()->routeIs('organization-profile.*') ? 'active' : '' }}" href="{{ route('organization-profile.index') }}">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span data-feather="settings"></span></span>
                                            <span class="nav-link-text-wrapper"><span class="nav-link-text">Organization Profile</span></span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                        @endcan
                    </ul>
                </div>
                <div class="navbar-vertical-footer">
                    <button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
                        <span class="uil uil-left-arrow-to-left fs-8"></span>
                        <span class="uil uil-arrow-from-right fs-8"></span>
                        <span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
                    </button>
                </div>
            </div>
        </nav>

        <nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="navbar-logo">
                    <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                    </button>
                    <a class="navbar-brand me-1 me-sm-3" href="{{ route('dashboard') }}">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ \App\Support\Branding::url('logo-icon.png') }}" alt="{{ \App\Support\Branding::name() }}" height="50" />
                                <p class="logo-text ms-2 d-none d-sm-block mb-0 fw-bold">{{ \App\Support\Branding::name() }}</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="search-box navbar-search ms-auto me-4 position-relative" id="globalSearchBox" style="width: 360px; flex-shrink: 0;">
                    <form class="position-relative" onsubmit="return false;" autocomplete="off">
                        <input class="search-input form-control search-input-icon web-search-input" id="globalSearchInput" type="search" placeholder="Search members, deposits, expenses..." aria-label="Search" style="padding-right: 2.5rem; font-size: 0.875rem; height: 38px;" />
                        <span class="position-absolute" style="right: 0.6rem; top: 50%; transform: translateY(-50%); z-index: 10;">
                            <span class="spinner-border spinner-border-sm text-body-tertiary d-none" id="globalSearchSpinner" style="width: 16px; height: 16px;"></span>
                            <span data-feather="search" id="globalSearchIcon" style="width: 18px; height: 18px;"></span>
                        </span>
                    </form>
                    <div class="dropdown-menu w-100 shadow border mt-1 p-0" id="globalSearchResults" style="max-height: 70vh; overflow-y: auto; display: none;"></div>
                </div>

                <ul class="navbar-nav navbar-nav-icons flex-row ms-auto">
                    <!-- Theme Toggle -->
                    <li class="nav-item d-flex align-items-center ms-4">
                        <button class="btn btn-link navbar-text-body p-1 m-0" id="themeToggle" type="button" data-bs-toggle="tooltip" title="Toggle theme" style="display: flex; align-items: center;">
                            <span data-feather="sun" style="width: 20px; height: 20px;"></span>
                        </button>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown d-flex align-items-center ms-2">
                        <a class="nav-link lh-1 p-1 position-relative" id="navbarNotifications" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" style="display: flex; align-items: center;">
                            <span data-feather="bell" style="width: 20px; height: 20px;"></span>
                            <span class="badge badge-phoenix badge-phoenix-danger badge-circle d-none" id="notifBadge" style="position: absolute; top: 0; right: -8px; width: 20px; height: 20px; font-size: 10px;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 shadow border" aria-labelledby="navbarNotifications" style="min-width: 400px;">
                            <div class="card position-relative border-0">
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                                        <h6 class="mb-0 fw-semibold">Notifications</h6>
                                        <form method="POST" action="{{ route('notifications.read-all') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.75rem;">Mark all read</button>
                                        </form>
                                    </div>
                                    <div class="overflow-auto scrollbar" id="notifList" style="max-height: 320px;">
                                        <div class="p-3 text-body-tertiary fs-9">Loading…</div>
                                    </div>
                                    <div class="p-2 border-top text-center">
                                        <a href="{{ route('notifications.index') }}" class="fs-9 fw-semibold text-decoration-none">View all</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Apps Grid -->
                    <li class="nav-item dropdown d-flex align-items-center ms-2">
                        <a class="nav-link lh-1 p-1" id="navbarAppsDropdown" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" style="display: flex; align-items: center;">
                            <span data-feather="grid" style="width: 20px; height: 20px;"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 shadow border" aria-labelledby="navbarAppsDropdown" style="min-width: 280px;">
                            <div class="card position-relative border-0">
                                <div class="card-body p-3">
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <a class="text-decoration-none" href="{{ route('members.index') }}" data-bs-toggle="tooltip" title="Members">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span data-feather="users" class="mb-2"></span>
                                                    <span class="fs-9 fw-semibold">Members</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            <a class="text-decoration-none" href="{{ route('investments.index') }}" data-bs-toggle="tooltip" title="Investments">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span data-feather="trending-up" class="mb-2"></span>
                                                    <span class="fs-9 fw-semibold">Investments</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            <a class="text-decoration-none" href="{{ route('expenses.index') }}" data-bs-toggle="tooltip" title="Expenses">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span data-feather="credit-card" class="mb-2"></span>
                                                    <span class="fs-9 fw-semibold">Expenses</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            <a class="text-decoration-none" href="{{ route('deposits.index') }}" data-bs-toggle="tooltip" title="Deposits">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span data-feather="dollar-sign" class="mb-2"></span>
                                                    <span class="fs-9 fw-semibold">Deposits</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            <a class="text-decoration-none" href="{{ route('investment-types.index') }}" data-bs-toggle="tooltip" title="Investment Types">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span data-feather="layers" class="mb-2"></span>
                                                    <span class="fs-9 fw-semibold">Types</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            @can('view accounting')
                                                <a class="text-decoration-none" href="{{ route('accounting.reports.dashboard') }}" data-bs-toggle="tooltip" title="Accounting">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span data-feather="book" class="mb-2"></span>
                                                        <span class="fs-9 fw-semibold">Accounting</span>
                                                    </div>
                                                </a>
                                            @else
                                                <a class="text-decoration-none" href="{{ route('dashboard') }}" data-bs-toggle="tooltip" title="Dashboard">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span data-feather="home" class="mb-2"></span>
                                                        <span class="fs-9 fw-semibold">Dashboard</span>
                                                    </div>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown d-flex align-items-center ms-2">
                        <a class="nav-link lh-1 p-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                            <div class="avatar avatar-m" style="width: 36px; height: 36px;">
                                <div class="avatar-name rounded-circle" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600;">
                                    <span>{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
                            <div class="card position-relative border-0">
                                <div class="card-body p-0">
                                    <div class="text-center pt-4 pb-3">
                                        <div class="avatar avatar-xl">
                                            <div class="avatar-name rounded-circle">
                                                <span>{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <h6 class="mt-2 text-body-emphasis mb-0">{{ auth()->user()->name }}</h6>
                                        <p class="text-body-secondary fs-9 mb-0">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                <div class="overflow-auto scrollbar" style="height: 10rem;">
                                    <ul class="nav d-flex flex-column mb-2 pb-1">
                                        <li class="nav-item">
                                            <a class="nav-link px-3 d-block" href="{{ route('dashboard') }}">
                                                <span class="me-2 text-body align-bottom" data-feather="home"></span>
                                                <span>Dashboard</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-footer p-0 border-top border-translucent">
                                    <form method="POST" action="{{ route('tyro-login.logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-phoenix-secondary d-flex flex-center w-100 rounded-top-0 rounded-bottom-2 py-3">
                                            <span class="me-2" data-feather="log-out"></span>Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="content">
            @yield('content')
            <footer class="footer position-absolute">
                <div class="row g-0 justify-content-between align-items-center h-100">
                    <div class="col-12 col-sm-auto text-center">
                        <p class="mb-0 mt-2 mt-sm-0 text-body">{{ \App\Support\Branding::name() }}<span class="d-none d-sm-inline-block"></span><span class="mx-1">|</span><br class="d-sm-none" />Association investment platform</p>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <script src="{{ asset('phoenix/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('phoenix/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('phoenix/assets/js/phoenix.js') }}"></script>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    @stack('scripts')

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        if (themeToggle) {
            // Set initial theme based on localStorage or system preference
            const savedTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-bs-theme', savedTheme);
            updateThemeIcon(savedTheme);

            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                html.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });

            function updateThemeIcon(theme) {
                const icon = themeToggle.querySelector('[data-feather]');
                if (icon) {
                    icon.setAttribute('data-feather', theme === 'dark' ? 'moon' : 'sun');
                    feather.replace();
                }
            }
        }
    </script>

    <script>
        // Global quick search (top navbar)
        (function () {
            const input = document.getElementById('globalSearchInput');
            const box = document.getElementById('globalSearchBox');
            const panel = document.getElementById('globalSearchResults');
            const spinner = document.getElementById('globalSearchSpinner');
            const icon = document.getElementById('globalSearchIcon');
            if (!input || !panel) return;

            const SEARCH_URL = "{{ route('search.quick') }}";
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
            let debounce = null;
            let controller = null;

            function esc(s) {
                return (s ?? '').toString().replace(/[&<>"']/g, c => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[c]));
            }

            function setLoading(on) {
                spinner.classList.toggle('d-none', !on);
                icon.classList.toggle('d-none', on);
            }

            function hidePanel() { panel.style.display = 'none'; panel.innerHTML = ''; }

            function showMessage(msg) {
                panel.innerHTML = `<div class="px-3 py-3 text-body-tertiary small">${esc(msg)}</div>`;
                panel.style.display = 'block';
            }

            function render(groups) {
                if (!groups.length) { showMessage('No results found.'); return; }
                let html = '';
                groups.forEach(g => {
                    html += `<h6 class="dropdown-header text-uppercase fs-10 fw-bold text-body-tertiary px-3 pt-2 pb-1">${esc(g.label)}</h6>`;
                    g.items.forEach(it => {
                        html += `<a href="${esc(it.url)}" class="dropdown-item px-3 py-2 border-top">
                            <div class="fw-semibold text-body-emphasis text-truncate" style="font-size:0.85rem;">${esc(it.title)}</div>
                            ${it.subtitle ? `<div class="text-body-tertiary text-truncate" style="font-size:0.72rem;">${esc(it.subtitle)}</div>` : ''}
                        </a>`;
                    });
                });
                panel.innerHTML = html;
                panel.style.display = 'block';
            }

            async function run(term) {
                if (controller) controller.abort();
                controller = new AbortController();
                setLoading(true);
                try {
                    const res = await fetch(`${SEARCH_URL}?q=${encodeURIComponent(term)}`, {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        signal: controller.signal,
                    });
                    const data = await res.json();
                    render(data.groups || []);
                } catch (e) {
                    if (e.name !== 'AbortError') hidePanel();
                } finally {
                    setLoading(false);
                }
            }

            input.addEventListener('input', function () {
                const term = this.value.trim();
                clearTimeout(debounce);
                if (term.length < 2) { hidePanel(); setLoading(false); return; }
                debounce = setTimeout(() => run(term), 250);
            });

            input.addEventListener('focus', function () {
                if (this.value.trim().length >= 2 && panel.innerHTML) panel.style.display = 'block';
            });

            document.addEventListener('click', function (e) {
                if (!box.contains(e.target)) hidePanel();
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { hidePanel(); this.blur(); }
            });
        })();
    </script>

    <script>
        // Notifications bell
        (function () {
            const badge = document.getElementById('notifBadge');
            const list = document.getElementById('notifList');
            if (!badge || !list) return;

            const FETCH_URL = "{{ route('notifications.fetch') }}";

            function esc(s) {
                return (s ?? '').toString().replace(/[&<>"']/g, c => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                }[c]));
            }

            async function load() {
                try {
                    const res = await fetch(FETCH_URL, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }

                    if (!data.items.length) {
                        list.innerHTML = '<div class="p-3 text-body-tertiary fs-9">No notifications yet.</div>';
                        return;
                    }

                    list.innerHTML = data.items.map(it => `
                        <a href="${esc(it.url)}" class="d-flex gap-2 p-3 border-bottom text-decoration-none ${it.read ? '' : 'bg-primary-subtle'}">
                            <span data-feather="${esc(it.icon)}" style="width:16px;height:16px;" class="mt-1 text-body-secondary"></span>
                            <div class="flex-1" style="min-width:0;">
                                <div class="fw-semibold text-body-emphasis" style="font-size:0.8rem;">${esc(it.title)}</div>
                                <div class="text-body-secondary" style="font-size:0.72rem;">${esc(it.message)}</div>
                                <div class="text-body-tertiary" style="font-size:0.68rem;">${esc(it.time)}</div>
                            </div>
                        </a>`).join('');

                    if (window.feather) window.feather.replace();
                } catch (e) { /* leave previous state */ }
            }

            load();
            setInterval(load, 60000);
            document.getElementById('navbarNotifications')?.addEventListener('click', load);
        })();
    </script>

    <!-- SweetAlert: flash toasts + global confirmations -->
    <script>
        (function () {
            const Toast = Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false,
                timer: 3500, timerProgressBar: true,
            });
            window.appToast = (icon, title) => Toast.fire({ icon, title });

            @if (session('success')) appToast('success', @json(session('success'))); @endif
            @if (session('error'))   appToast('error',   @json(session('error')));   @endif
            @if (session('warning')) appToast('warning', @json(session('warning'))); @endif
            @if (session('status'))  appToast('info',    @json(session('status')));  @endif

            // Promise-based confirm for inline JS: swalConfirm('msg').then(ok => {...})
            window.swalConfirm = (text, opts = {}) => Swal.fire(Object.assign({
                title: 'Are you sure?', text, icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Yes', cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545', reverseButtons: true,
            }, opts)).then(r => r.isConfirmed);

            // Forms with data-confirm
            document.addEventListener('submit', function (e) {
                const form = e.target.closest('form[data-confirm]');
                if (!form || form.dataset.confirmed === '1') return;
                e.preventDefault();
                swalConfirm(form.dataset.confirm).then(ok => {
                    if (ok) { form.dataset.confirmed = '1'; form.submit(); }
                });
            }, true);

            // Buttons/links with data-confirm
            document.addEventListener('click', function (e) {
                const el = e.target.closest('[data-confirm]');
                if (!el || el.tagName === 'FORM' || el.dataset.confirmed === '1') return;
                const form = el.closest('form');
                // Let form[data-confirm] submit handler deal with submit buttons inside confirm-forms
                if (form && form.hasAttribute('data-confirm')) return;
                e.preventDefault();
                e.stopPropagation();
                swalConfirm(el.dataset.confirm).then(ok => {
                    if (!ok) return;
                    el.dataset.confirmed = '1';
                    if (el.tagName === 'A' && el.href) { window.location = el.href; }
                    else if (form) { form.requestSubmit ? form.requestSubmit(el) : form.submit(); }
                });
            }, true);
        })();
    </script>

</body>
</html>
