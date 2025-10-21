<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Page Title' }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Barcode scanner library -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Inter font from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Theme tokens: change colors here to affect entire layout */
        :root {
            /* Backgrounds */
            --page-bg: #f5f7fa;
            /* body background */
            --surface: #ffffff;
            /* cards, sidebar surface */


            /* Primary / Brand */
            --primary: #0d6efd;
            /* main blue (links, active) */
            --primary-600: #0b5ed7;
            /* hover background for links */
            --primary-100: #e9f0ff;
            /* main blue (links, active) */





            /* Accent */
            --accent: #198754;
            /* green (amounts, success) */


            /* Muted / borders / text */
            --muted: #6c757d;
            /* secondary text */
            --muted-2: #495057;
            /* nav link text */
            --border: #e0e0e0;
            /* general border color */
            --muted-3: #dee2e6;
            /* scrollbar / thumb */



            /* Status colors */
            --success-bg: #d1e7dd;
            --success-text: #0f5132;
            --warning-bg: #fff3cd;
            --warning-text: #664d03;
            --danger-bg: #f8d7da;
            --danger-text: #842029;


            /* Topbar / sidebar specifics */
            --sidebar-bg: #ffffff;
            --topbar-bg: #ffffff;


            /* Text */
            --text: #212529;


            /* Avatars */
            --avatar-bg: var(--primary);
            --avatar-text: #ffffff;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--page-bg);
            letter-spacing: -0.01em;
        }

        /* Sidebar styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            padding: 20px 0;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 1040;
            overflow-y: auto;
            /* Enable vertical scrolling */
            overflow-x: hidden;
            /* Hide horizontal overflow */
        }

        /* Add custom scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--page-bg);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: var(--muted-3);
            border-radius: 6px;
        }

        /* Add padding to the bottom of sidebar to ensure last items are visible */
        .sidebar .nav {
            padding-bottom: 50px;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.25rem;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 10px;
        }

        .sidebar.collapsed .nav-link.dropdown-toggle::after {
            display: none;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            margin-bottom: 15px;
        }

        .sidebar-title {
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        /* Navigation styles */
        .nav-item {
            margin: 2px 0;
            /* Reduced from 5px to 2px */
        }

        .nav-link {
            color: var(--muted-2);
            padding: 8px 20px;
            /* Reduced top/bottom padding from 10px to 8px */
            border-radius: 6px;
            transition: all 0.2s;
        }


        .nav-link.active {
            background-color: var(--primary-100);
            color: var(--primary);
            font-weight: 500;
        }

        .nav-link:focus,
        .nav-link:hover,
        .nav-link:focus-visible {
            color: var(--primary);

            outline: none;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .nav-link.dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
            float: right;
            margin-top: 8px;
        }

        #inventorySubmenu .nav-link,
        #hrSubmenu .nav-link,
        #salesSubmenu .nav-link,
        #stockSubmenu .nav-link,
        #purchaseSubmenu .nav-link {
            padding: 5px 15px;
            /* Reduced padding for all submenu links */
            font-size: 0.9rem;
        }

        /* Add these styles to further improve submenu spacing */
        .collapse .nav-item {
            margin: 1px 0;
            /* Even more compact spacing for submenu items */
        }

        .collapse .nav.flex-column {
            padding-bottom: 0;
            /* Remove extra bottom padding from nested menus */
            padding-top: 2px;
            /* Add small top padding to separate from parent */
        }

        .collapse .nav-item:last-child {
            margin-bottom: 3px;
            /* Add small space after last submenu item */
        }

        /* Disabled menu item styles */
        .nav-link.disabled {
            color: #adb5bd !important;
            cursor: not-allowed !important;
            opacity: 0.6;
            pointer-events: none;
        }

        .nav-link.disabled i {
            color: #adb5bd !important;
        }

        .nav-link.disabled:hover {
            background-color: transparent !important;
            color: #adb5bd !important;
        }

        /* Top bar styles */
        .top-bar {
            height: 60px;
            background-color: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 1000;
            display: flex;
            align-items: center;
            transition: left 0.3s ease;
        }

        .top-bar.collapsed {
            left: 70px;
        }

        /* User info styles */
        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .admin-info:hover {
            background-color: var(--page-bg);
        }

        .admin-avatar,
        .staff-avatar,
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--avatar-bg);
            color: var(--avatar-text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            letter-spacing: -0.03em;
        }

        .admin-name {
            font-weight: 500;
        }

        /* Dropdown menu styles */
        .dropdown-toggle {
            cursor: pointer;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 0;
            margin-top: 10px;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 8px 16px;
            display: flex;
            align-items: center;
        }

        .dropdown-item:hover {
            background-color: var(--primary-100);
        }

        .dropdown-item i {
            font-size: 1rem;
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            min-height: calc(100vh - 60px);
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        /* Card styles */
        .stat-card,
        .widget-container {
            background: var(--surface);
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            border: none;
            padding: 1.25rem;
            height: 100%;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-change {
            color: var(--accent);
            font-size: 13px;
        }

        .stat-change-alert {
            color: var(--danger-text);
            font-size: 13px;
        }

        /* Tab navigation */
        .content-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .content-tab {
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 500;
            color: var(--muted-2);
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .content-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }

        .content-tab:hover:not(.active) {
            color: var(--primary);
            border-bottom-color: var(--border);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Chart cards */
        .chart-card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .chart-header {
            background-color: var(--surface);
            padding: 1.25rem;
            border-bottom: 1px solid var(--border);
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            padding: 1.5rem;
        }

        /* Recent sales */
        .recent-sales-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            height: 380px;
            width: 100%;
        }

        .avatar {
            width: 40px;
            height: 40px;
            margin-right: 15px;
        }

        .amount {
            font-weight: bold;
            color: var(--accent);
        }

        /* Widget components */
        .widget-header h6 {
            font-size: 1.25rem;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .widget-header p {
            font-size: 0.875rem;
            color: var(--muted-2);
            margin-bottom: 0;
        }

        /* Item rows in widgets */
        .item-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .item-details {
            flex-grow: 1;
            margin-right: 10px;
        }

        .item-details h6 {
            font-size: 1rem;
            margin-bottom: 3px;
            color: var(--text);
        }

        .item-details p {
            font-size: 0.875rem;
            color: var(--muted);
            margin-bottom: 0;
        }

        /* Status badges */
        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .in-stock {
            background-color: #d1e7dd;
            color: var(--success-text);
        }

        .low-stock {
            background-color: var(--warning-bg);
            color: var(--warning-text);
        }

        .out-of-stock {
            background-color: var(--danger-bg);
            color: var(--danger-text);
        }

        /* Progress bars */
        .progress {
            height: 0.5rem;
            margin-top: 5px;
            background-color: var(--muted-3);
            border-radius: 0.25rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 0.5rem;
        }

        /* Scrollable containers */
        .inventory-container,
        .staff-sales-container,
        .chart-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 #f8f9fa;
            max-height: 400px;
            overflow-y: auto;
        }

        .chart-scroll-container {
            width: 100%;
            overflow-x: auto;
        }

        .inventory-container::-webkit-scrollbar,
        .staff-sales-container::-webkit-scrollbar,
        .chart-scroll-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .inventory-container::-webkit-scrollbar-track,
        .staff-sales-container::-webkit-scrollbar-track,
        .chart-scroll-container::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        .inventory-container::-webkit-scrollbar-thumb,
        .staff-sales-container::-webkit-scrollbar-thumb,
        .chart-scroll-container::-webkit-scrollbar-thumb {
            background-color: var(--muted-3);
            border-radius: 10px;
        }

        .modal-backdrop.show {
            z-index: 1040 !important;
        }

        .modal.show {
            z-index: 1050 !important;
        }

        /* Responsive styles */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px;
                /* Ensure sidebar takes full height but allows scrolling on mobile */
                height: 100%;
                bottom: 0;
                top: 0;
                overflow-y: auto;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            }

            .sidebar.collapsed.show {
                width: 250px;
            }

            .top-bar {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
    @stack('styles')
    @livewireStyles
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header d-flex justify-content-center">
                <div class="sidebar-title">
                    <img src="{{ asset('images/USN.png') }}" alt="Logo" width="150">
                </div>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-bar-chart-line"></i> <span>Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#hrSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="hrSubmenu">
                        <i class="bi bi-people"></i> <span>HR Management</span>
                    </a>
                    <div class="collapse" id="hrSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.manage-admin') }}">
                                    <i class="bi bi-shield-lock"></i> <span>Manage Admin</span>
                                </a>
                            </li>
                            <!-- Disabled: Manage Staff -->
                            <li class="nav-item">
                                <a class="nav-link py-2 disabled" href="#">
                                    <i class="bi bi-person-lines-fill"></i> <span>Manage Staff</span>
                                </a>
                            </li>
                            <!-- Disabled: Staff Attendance -->
                            <li class="nav-item">
                                <a class="nav-link py-2 disabled" href="#">
                                    <i class="bi bi-calendar-check"></i> <span>Staff Attendance</span>
                                </a>
                            </li>
                            <!-- Disabled: Staff Salary -->
                            <li class="nav-item">
                                <a class="nav-link py-2 disabled" href="#">
                                    <i class="bi bi-currency-dollar"></i> <span>Staff Salary</span>
                                </a>
                            </li>
                            <!-- Disabled: Loan Management -->
                            <li class="nav-item">
                                <a class="nav-link py-2 disabled" href="#">
                                    <i class="bi bi-credit-card"></i> <span>Loan Management</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.manage-customer') }}">
                                    <i class="bi bi-people"></i> <span>Manage Customer</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#inventorySubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="inventorySubmenu">
                        <i class="bi bi-box-seam"></i> <span>Inventory</span>
                    </a>
                    <div class="collapse" id="inventorySubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.Productes') }}">
                                    <i class="bi bi-box-seam"></i> <span>Product Details</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.Product-brand') }}">
                                    <i class="bi bi-tag-fill"></i> <span>Product Brand</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.Product-category') }}">
                                    <i class="bi bi-collection"></i> <span>Product Category</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.return-product') }}">
                                    <i class="bi bi-collection"></i> <span>Return Product</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#salesSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="salesSubmenu">
                        <i class="bi bi-people"></i> <span>Sales</span>
                    </a>
                    <div class="collapse" id="salesSubmenu">
                        <ul class="nav flex-column ms-3">
                            <!-- Disabled: Sales Approvals -->
                            <li class="nav-item">
                                <a class="nav-link py-2 disabled" href="#">
                                    <i class="bi bi-shield-check"></i> <span>Sales Approvals</span>
                                </a>
                            </li>
                            <!-- Disabled: Staff Sales -->
                            <li class="nav-item">
                                <a class="nav-link py-2 disabled" href="#">
                                    <i class="bi bi-shield-lock"></i> <span>Staff Sales</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.customer-sale-details') }}">
                                    <i class="bi bi-people"></i> <span>Customer Sales</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.due-payments') }}">
                                    <i class="bi bi-cash-coin"></i> <span>Due Payments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.view-payments') }}">
                                    <i class="bi bi-credit-card-2-back"></i> <span>View Payments</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.payment-approvals') ? 'active' : '' }}"
                                    href="{{ route('admin.payment-approvals') }}">
                                    <i class="bi bi-shield-check "></i><span class="nav-link-text ms-1">Payment
                                        Approvals</span>
                                    @php
                                    $pendingCount = \App\Models\Payment::where('status',
                                    'pending')->where('is_completed', 0)->count();
                                    @endphp
                                    @if($pendingCount > 0)
                                    <span class="badge bg-danger ms-auto">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#stockSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="stockSubmenu">
                        <i class="bi bi-people"></i> <span>Stock Deails</span>
                    </a>
                    <div class="collapse" id="stockSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.Product-stock-details') }}">
                                    <i class="bi bi-shield-lock"></i> <span>Product Stock</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#purchaseSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="purchaseSubmenu">
                        <i class="bi bi-key"></i><span>Purchase</span>
                    </a>
                    <div class="collapse" id="purchaseSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.supplier-management') }}">
                                    <i class="bi bi-briefcase"></i> <span>Supplier Manage</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.quotation') }}">
                                    <i class="bi bi-receipt"></i> <span>Purchase Order</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{ route('admin.grn') }}">
                                    <i class="bi bi-box-seam"></i><span>GRN</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- //add financing -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#FinancingSubmenu" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="FinancingSubmenu">
                        <i class="bi bi-cash-stack"></i> <span>Financing</span>
                    </a>
                    <div class="collapse" id="FinancingSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{route('admin.expenses')}}">
                                    <i class="bi bi-wallet2"></i> <span>Expenses</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2" href="{{route('admin.income')}}">
                                    <i class="bi bi-graph-up-arrow"></i> <span>Income</span>

                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.store-billing') }}">
                        <i class="bi bi-cash"></i> <span>Store Billing</span>
                    </a>
                </li>

                </li>

              
                <a class="nav-link" href="{{ route('admin.systemsetting') }}">


                <a class="nav-link" href="{{ route('admin.settings') }}">

                    <i class="bi bi-gear"></i> <span>Settings</span>
                </a>

            </ul>
        </div>


        <!-- Top Navigation Bar -->
        <nav class="top-bar d-flex justify-content-between align-items-center">
            <!-- Sidebar toggle button -->
            <button id="sidebarToggler" class="btn btn-sm btn-light d-flex align-items-center">
                <i class="bi bi-list fs-5"></i>
            </button>

            <!-- Centered Company Name -->
            <div class="flex-grow-1 d-flex justify-content-center">
                <h2 class="mb-0"> <b>USN Auto Parts</b></h2>
            </div>
            @php
            use App\Models\CashInHand as CashModel;
            $cashInHand = CashModel::where('key', 'cash in hand')->value('value') ?? 0;
            @endphp

            <!-- Editable Cash in Hand Display -->
            <div class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill shadow-sm border border-success border-opacity-25 d-flex align-items-center gap-2">
                <i class="bi bi-wallet2"></i>
                <span class="fw-semibold">Cash in Hand:</span>
                <span class="fw-bold">Rs. {{ number_format($cashInHand, 2) }}</span>

                <!-- Edit button -->
                <button class="btn btn-sm btn-outline-success border-0 ms-2 p-0" data-bs-toggle="modal" data-bs-target="#editCashAdminModal">
                    <i class="bi bi-pencil-square"></i>
                </button>

            </div>



            <!-- Admin dropdown -->
            <div class="dropdown ms-auto">
                <div class="admin-info dropdown-toggle" id="adminDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="admin-avatar">A</div>
                    <div class="admin-name">Admin</div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person me-2"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Modal for Updating Cash-in-Hand -->
        <div class="modal fade" id="editCashAdminModal" tabindex="-1" aria-labelledby="editCashAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-wallet2 text-warning me-2"></i> Update Cash-in-Hand
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.updateCashInHand') }}" method="POST" id="cashInHandForm">
                        @csrf
                        <div class="modal-body">

                            <!-- Amount Input -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">New Cash Amount (Rs.)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rs.</span>
                                    <input type="number" step="0.01" class="form-control" name="newCashInHand"
                                        placeholder="Enter new cash amount" required>
                                </div>
                            </div>

                            <!-- Current Total Preview -->
                            <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-calculator text-success me-2"></i> Current Cash in Hand
                                </h6>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Current:</span>
                                    <span class="fw-semibold text-success">Rs. {{ number_format($cashInHand, 2) }}</span>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="bi bi-check2-circle me-1"></i> Update Cash-in-Hand
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
       

        <!-- Main Content -->
        <main class="main-content">
            {{ $slot }}
        </main>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 from CDN (only need this one line) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include jQuery (required by Bootstrap 4 modal) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- In your main layout file -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define all elements once
            const sidebarToggler = document.getElementById('sidebarToggler');
            const sidebar = document.querySelector('.sidebar');
            const topBar = document.querySelector('.top-bar');
            const mainContent = document.querySelector('.main-content');

            // Initialize sidebar state
            function initializeSidebar() {
                // Check if sidebar state is saved in localStorage
                const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

                if (sidebarCollapsed && window.innerWidth >= 768) {
                    sidebar.classList.add('collapsed');
                    topBar.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                }

                // On mobile, always start with sidebar hidden
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('show');
                    topBar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                }
            }

            // Toggle sidebar function
            function toggleSidebar(event) {
                if (event) {
                    event.stopPropagation();
                }

                if (window.innerWidth < 768) {
                    // Mobile behavior
                    sidebar.classList.toggle('show');
                } else {
                    // Desktop behavior
                    sidebar.classList.toggle('collapsed');
                    topBar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');

                    // Save state to localStorage
                    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                }
            }

            // Handle sidebar interactions
            if (sidebarToggler && sidebar) {
                // Initialize sidebar
                initializeSidebar();

                // Set up single click handler
                sidebarToggler.addEventListener('click', toggleSidebar);

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth < 768 &&
                        sidebar.classList.contains('show') &&
                        !sidebar.contains(event.target) &&
                        !sidebarToggler.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        sidebar.classList.remove('show');

                        // Restore collapsed state on desktop
                        const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                        if (sidebarCollapsed) {
                            sidebar.classList.add('collapsed');
                            topBar.classList.add('collapsed');
                            mainContent.classList.add('collapsed');
                        } else {
                            sidebar.classList.remove('collapsed');
                            topBar.classList.remove('collapsed');
                            mainContent.classList.remove('collapsed');
                        }
                    } else {
                        // On mobile, remove collapsed styles
                        topBar.classList.remove('collapsed');
                        mainContent.classList.remove('collapsed');
                    }
                });
            }

            // Tab Switching Functionality
            const tabs = document.querySelectorAll('.content-tab');
            if (tabs.length > 0) {
                tabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        // Remove active class from all tabs
                        tabs.forEach(t => t.classList.remove('active'));

                        // Add active class to clicked tab
                        this.classList.add('active');

                        // Hide all tab contents
                        document.querySelectorAll('.tab-content').forEach(content => {
                            content.classList.remove('active');
                        });

                        // Show the selected tab content
                        const tabId = this.getAttribute('data-tab');
                        document.getElementById(tabId).classList.add('active');
                    });
                });
            }

            // Chart Initialization with proper cleanup
            const salesChartEl = document.getElementById('salesChart');
            let salesChart = null;

            function initializeChart() {
                if (salesChartEl) {
                    // Destroy existing chart if it exists
                    if (window.salesChart) {
                        window.salesChart.destroy();
                    }

                    const ctx = salesChartEl.getContext('2d');
                    window.salesChart = new Chart(ctx, {
                        // Your existing chart configuration
                        type: 'line',
                        data: {
                            // Your chart data
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            // Initialize chart
            initializeChart();

            // Re-initialize chart when Livewire updates
            document.addEventListener('livewire:load', function() {
                Livewire.hook('message.processed', () => {
                    initializeChart();
                });
            });

            // General submenu activation logic
            function activateParentMenuIfSubmenuActive(parentToggleSelector, submenuSelector) {
                const parentToggle = document.querySelector(parentToggleSelector);
                const submenu = document.querySelector(submenuSelector);
                const submenuLinks = submenu ? submenu.querySelectorAll('.nav-link') : [];

                let active = false;
                const currentPath = window.location.pathname;

                submenuLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && href !== '#') {
                        const hrefPath = href.replace(/^(https?:\/\/[^\/]+)/, '').split('?')[0];
                        const linkIsActive = currentPath === hrefPath ||
                            currentPath.endsWith(hrefPath) ||
                            currentPath.includes(hrefPath);

                        if (linkIsActive) {
                            link.classList.add('active');
                            active = true;
                        }
                    }
                });

                if (active && parentToggle && submenu) {
                    // Remove active from all main nav links
                    document.querySelectorAll('.sidebar > .nav > .nav-item > .nav-link:not(.dropdown-toggle)').forEach(link => {
                        link.classList.remove('active');
                    });
                    parentToggle.classList.add('active');
                    parentToggle.setAttribute('aria-expanded', 'true');
                    submenu.classList.add('show');
                }
            }

            // Initialize both HR and Inventory submenus
            activateParentMenuIfSubmenuActive('a[href="#hrSubmenu"]', '#hrSubmenu');
            activateParentMenuIfSubmenuActive('a[href="#inventorySubmenu"]', '#inventorySubmenu');
            activateParentMenuIfSubmenuActive('a[href="#salesSubmenu"]', '#salesSubmenu');
            activateParentMenuIfSubmenuActive('a[href="#stockSubmenu"]', '#stockSubmenu');
            activateParentMenuIfSubmenuActive('a[href="#purchaseSubmenu"]', '#purchaseSubmenu');

            // Replace the existing submenu activation logic with this comprehensive function
            function setActiveMenuItem() {
                // Get current path
                const currentPath = window.location.pathname;

                // First clear all active states
                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Reset all expanded states for dropdowns
                document.querySelectorAll('.collapse').forEach(submenu => {
                    submenu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.setAttribute('aria-expanded', 'false');
                });

                // Check for exact match first (highest priority)
                let activeFound = false;

                // First try to find exact matches
                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && href !== '#' && !href.startsWith('#')) {
                        const hrefPath = href.replace(/^(https?:\/\/[^\/]+)/, '').split('?')[0];

                        // Exact match gets priority
                        if (currentPath === hrefPath) {
                            link.classList.add('active');
                            activeFound = true;

                            // If this is a submenu link, expand its parent
                            const submenu = link.closest('.collapse');
                            if (submenu) {
                                submenu.classList.add('show');
                                const parentToggle = document.querySelector(`[href="#${submenu.id}"]`);
                                if (parentToggle) {
                                    parentToggle.classList.add('active');
                                    parentToggle.setAttribute('aria-expanded', 'true');
                                }
                            }
                        }
                    }
                });

                // If no exact match was found, try partial matches
                if (!activeFound) {
                    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                        const href = link.getAttribute('href');
                        if (href && href !== '#' && !href.startsWith('#')) {
                            const hrefPath = href.replace(/^(https?:\/\/[^\/]+)/, '').split('?')[0];

                            // Skip root path to avoid false positives
                            if (hrefPath !== '/' && currentPath.includes(hrefPath)) {
                                link.classList.add('active');

                                // If this is a submenu link, expand its parent
                                const submenu = link.closest('.collapse');
                                if (submenu) {
                                    submenu.classList.add('show');
                                    const parentToggle = document.querySelector(`[href="#${submenu.id}"]`);
                                    if (parentToggle) {
                                        parentToggle.classList.add('active');
                                        parentToggle.setAttribute('aria-expanded', 'true');
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Add this at the end of your document.addEventListener('DOMContentLoaded') function
            setActiveMenuItem();

            // Function to handle sidebar height and scrolling
            function adjustSidebarHeight() {
                const sidebar = document.querySelector('.sidebar');
                const windowHeight = window.innerHeight;

                if (sidebar) {
                    // Ensure the sidebar takes the full viewport height
                    sidebar.style.height = windowHeight + 'px';

                    // Check if content is taller than viewport
                    const sidebarContent = sidebar.querySelector('.nav.flex-column');
                    if (sidebarContent && sidebarContent.scrollHeight > windowHeight) {
                        // Add a class to indicate scrollable content
                        sidebar.classList.add('scrollable');
                    } else {
                        sidebar.classList.remove('scrollable');
                    }
                }
            }

            // Run on load and resize
            adjustSidebarHeight();
            window.addEventListener('resize', adjustSidebarHeight);
        });

        //cash in hand popup model 
        window.addEventListener('close-modal', event => {
            const modalId = event.detail.modalId;
            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
            if (modal) modal.hide();
        });
    </script>
    @stack('scripts')
</body>

</html>