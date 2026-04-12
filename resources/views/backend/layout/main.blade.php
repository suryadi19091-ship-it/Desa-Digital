<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin Panel Desa Ciuwlan</title>
    
    <!-- Local Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- Bootstrap CSS (included in AdminLTE) -->
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Styles -->
    <style>
        .wrapper {
            min-height: 100vh;
        }
        
        .main-sidebar {
            background: linear-gradient(180deg, #343a40 0%, #495057 100%);
        }
        
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
            background-color: rgba(255,255,255,.1);
        }
        
        .content-wrapper {
            background-color: #f4f6f9;
        }
        
        .nav-header {
            background: rgba(0,0,0,.1);
            color: #c2c7d0;
        }
        
        /* Dropdown fixes */
        .dropdown-menu.show {
            display: block !important;
        }
        
        .dropdown-toggle::after {
            display: none;
        }
        
        .navbar .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            min-width: 160px;
            padding: 5px 0;
            margin: 2px 0 0;
            font-size: 14px;
            color: #212529;
            background-color: #fff;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
        }
        
        .sidebar-mini .main-sidebar:hover .nav-link p {
            display: inline-block !important;
        }
        
        .brand-text {
            color: #fff !important;
        }

        /* ============================================================
         *  BACKEND DARK MODE
         * ============================================================ */

        /* --- Variables (light defaults) --- */
        :root {
            --dm-bg:        #1a1d23;
            --dm-surface:   #23272f;
            --dm-surface2:  #2c313a;
            --dm-border:    #3a3f4b;
            --dm-text:      #e2e8f0;
            --dm-muted:     #8b9ab1;
            --dm-link:      #60a5fa;
            --dm-navbar:    #1e2027;
        }

        /* --- Body / Wrapper --- */
        body.dark-mode,
        body.dark-mode .wrapper {
            background-color: var(--dm-bg) !important;
            color: var(--dm-text) !important;
        }

        /* --- Navbar --- */
        body.dark-mode .main-header.navbar {
            background-color: var(--dm-navbar) !important;
            border-bottom: 1px solid var(--dm-border) !important;
        }
        body.dark-mode .main-header .nav-link,
        body.dark-mode .main-header .navbar-nav .nav-link {
            color: var(--dm-text) !important;
        }
        body.dark-mode .main-header .nav-link:hover {
            color: #fff !important;
        }
        body.dark-mode .form-control-navbar {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .btn-navbar {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        /* --- Content Wrapper / Header --- */
        body.dark-mode .content-wrapper,
        body.dark-mode .content-header {
            background-color: var(--dm-bg) !important;
        }
        body.dark-mode .content-header h1,
        body.dark-mode .content-header h2,
        body.dark-mode .content-header h3,
        body.dark-mode .content-header h4,
        body.dark-mode .content-header h5 {
            color: var(--dm-text) !important;
        }

        /* --- Breadcrumb --- */
        body.dark-mode .breadcrumb {
            background-color: var(--dm-surface) !important;
            border: 1px solid var(--dm-border) !important;
        }
        body.dark-mode .breadcrumb-item,
        body.dark-mode .breadcrumb-item a,
        body.dark-mode .breadcrumb-item.active {
            color: var(--dm-muted) !important;
        }

        /* --- Cards --- */
        body.dark-mode .card {
            background-color: var(--dm-surface) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
            box-shadow: 0 1px 6px rgba(0,0,0,.4) !important;
        }
        body.dark-mode .card-header {
            background-color: var(--dm-surface2) !important;
            border-bottom-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .card-footer {
            background-color: var(--dm-surface2) !important;
            border-top-color: var(--dm-border) !important;
        }
        body.dark-mode .card-title,
        body.dark-mode .card-text {
            color: var(--dm-text) !important;
        }
        body.dark-mode .card-body p,
        body.dark-mode .card-body span,
        body.dark-mode .card-body small {
            color: var(--dm-muted) !important;
        }
        body.dark-mode .card-body h3,
        body.dark-mode .card-body h4,
        body.dark-mode .card-body h5 {
            color: var(--dm-text) !important;
        }

        /* --- Info Boxes --- */
        body.dark-mode .info-box {
            background-color: var(--dm-surface) !important;
            border: 1px solid var(--dm-border) !important;
            box-shadow: 0 1px 4px rgba(0,0,0,.3) !important;
        }
        body.dark-mode .info-box-text,
        body.dark-mode .info-box-number,
        body.dark-mode .info-box-more {
            color: var(--dm-text) !important;
        }
        body.dark-mode .info-box-more a {
            color: var(--dm-link) !important;
        }

        /* --- Small Box (Dashboard Stats) --- */
        body.dark-mode .small-box {
            background-color: var(--dm-surface) !important;
            border: 1px solid var(--dm-border) !important;
        }
        body.dark-mode .small-box p,
        body.dark-mode .small-box h3 {
            color: var(--dm-text) !important;
        }
        body.dark-mode .small-box .inner {
            color: var(--dm-text) !important;
        }

        /* --- Tables --- */
        body.dark-mode .table {
            color: var(--dm-text) !important;
        }
        body.dark-mode .table th,
        body.dark-mode .table td {
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255,255,255,.03) !important;
        }
        body.dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,.06) !important;
        }
        body.dark-mode .thead-light th {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-muted) !important;
        }

        /* --- Forms --- */
        body.dark-mode .form-control,
        body.dark-mode .custom-select {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .form-control:focus {
            background-color: var(--dm-surface2) !important;
            border-color: #60a5fa !important;
            color: var(--dm-text) !important;
            box-shadow: 0 0 0 0.2rem rgba(96,165,250,.25) !important;
        }
        body.dark-mode .form-control::placeholder {
            color: var(--dm-muted) !important;
        }
        body.dark-mode .input-group-text {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        /* --- Pagination --- */
        body.dark-mode .pagination .page-link {
            background-color: var(--dm-surface) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .pagination .page-item.active .page-link {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #fff !important;
        }
        body.dark-mode .pagination .page-item.disabled .page-link {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-muted) !important;
            opacity: 0.6;
        }
        body.dark-mode .pagination .page-link:hover {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-link) !important;
        }
        
        /* Ensure horizontal pagination */
        .pagination {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        .pagination .page-item {
            display: list-item !important; /* Keep as list-item so they stay in one line inside flexul */
        }
        body.dark-mode label,
        body.dark-mode .form-text {
            color: var(--dm-muted) !important;
        }

        /* --- Buttons (default/secondary/light) --- */
        body.dark-mode .btn-default,
        body.dark-mode .btn-secondary,
        body.dark-mode .btn-light {
            background-color: var(--dm-surface2) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .btn-outline-secondary {
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        /* --- Alerts --- */
        body.dark-mode .alert-info {
            background-color: #1e3a5f !important;
            border-color: #1d4ed8 !important;
            color: #bfdbfe !important;
        }
        body.dark-mode .alert-success {
            background-color: #14532d !important;
            border-color: #15803d !important;
            color: #bbf7d0 !important;
        }
        body.dark-mode .alert-warning {
            background-color: #451a03 !important;
            border-color: #b45309 !important;
            color: #fde68a !important;
        }
        body.dark-mode .alert-danger {
            background-color: #450a0a !important;
            border-color: #b91c1c !important;
            color: #fecaca !important;
        }

        /* --- Badges --- */
        body.dark-mode .badge-light {
            background-color: var(--dm-surface2) !important;
            color: var(--dm-text) !important;
        }

        /* --- Dropdowns --- */
        body.dark-mode .dropdown-menu {
            background-color: var(--dm-surface) !important;
            border-color: var(--dm-border) !important;
        }
        body.dark-mode .dropdown-item {
            color: var(--dm-text) !important;
        }
        body.dark-mode .dropdown-item:hover,
        body.dark-mode .dropdown-item:focus {
            background-color: var(--dm-surface2) !important;
            color: #fff !important;
        }
        body.dark-mode .dropdown-divider {
            border-top-color: var(--dm-border) !important;
        }
        body.dark-mode .dropdown-header {
            color: var(--dm-muted) !important;
        }

        /* --- Text helpers --- */
        body.dark-mode .text-muted {
            color: var(--dm-muted) !important;
        }
        body.dark-mode .text-dark {
            color: var(--dm-text) !important;
        }
        body.dark-mode p, body.dark-mode span:not(.badge):not(.nav-link):not(.info-box-icon) {
            color: inherit;
        }

        /* --- Main Sidebar (stays dark always, fine as-is) --- */
        /* --- Pagination --- */
        body.dark-mode .page-item .page-link {
            background-color: var(--dm-surface) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-link) !important;
        }
        body.dark-mode .page-item.active .page-link {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }
        body.dark-mode .page-item.disabled .page-link {
            color: var(--dm-muted) !important;
            background-color: var(--dm-surface2) !important;
        }

        /* --- Modal --- */
        body.dark-mode .modal-content {
            background-color: var(--dm-surface) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer {
            border-color: var(--dm-border) !important;
        }
        body.dark-mode .close {
            color: var(--dm-text) !important;
        }

        /* --- Dashboard Welcome Banner --- */
        body.dark-mode .welcome-banner {
            background: linear-gradient(135deg, #1e3a5f 0%, #312e81 100%) !important;
        }

        /* --- Misc --- */
        body.dark-mode hr {
            border-top-color: var(--dm-border) !important;
        }
        body.dark-mode .border,
        body.dark-mode .border-top,
        body.dark-mode .border-bottom {
            border-color: var(--dm-border) !important;
        }
        body.dark-mode pre,
        body.dark-mode code {
            background-color: var(--dm-surface2) !important;
            color: #f9a8d4 !important;
            border-color: var(--dm-border) !important;
        }

        /* ============================================================
         *  TAILWIND CLASS OVERRIDES FOR DARK MODE (Dashboard uses Tailwind)
         * ============================================================ */

        /* Stat cards / panels */
        body.dark-mode .bg-white {
            background-color: var(--dm-surface) !important;
        }
        body.dark-mode .bg-gray-50 {
            background-color: var(--dm-surface2) !important;
        }
        body.dark-mode .bg-gray-100 {
            background-color: var(--dm-surface2) !important;
        }
        body.dark-mode .bg-gray-200 {
            background-color: #374151 !important;
        }

        /* Text colors */
        body.dark-mode .text-gray-900 {
            color: var(--dm-text) !important;
        }
        body.dark-mode .text-gray-800 {
            color: #d1d5db !important;
        }
        body.dark-mode .text-gray-700 {
            color: #9ca3af !important;
        }
        body.dark-mode .text-gray-600 {
            color: #9ca3af !important;
        }
        body.dark-mode .text-gray-500 {
            color: var(--dm-muted) !important;
        }
        body.dark-mode .text-gray-400 {
            color: #6b7280 !important;
        }

        /* Borders */
        body.dark-mode .border-gray-200 {
            border-color: var(--dm-border) !important;
        }
        body.dark-mode .border-gray-300 {
            border-color: var(--dm-border) !important;
        }
        body.dark-mode .divide-gray-200 > * + * {
            border-color: var(--dm-border) !important;
        }

        /* Shadows */
        body.dark-mode .shadow,
        body.dark-mode .shadow-sm,
        body.dark-mode .shadow-md,
        body.dark-mode .shadow-lg {
            box-shadow: 0 2px 10px rgba(0,0,0,.5) !important;
        }

        /* Quick Action Cards (bg-blue-50, bg-green-50 etc.) */
        body.dark-mode .bg-blue-50 { background-color: #1e3a5f !important; }
        body.dark-mode .hover\:bg-blue-100:hover { background-color: #1d4ed8 !important; }
        body.dark-mode .bg-green-50 { background-color: #14532d !important; }
        body.dark-mode .hover\:bg-green-100:hover { background-color: #15803d !important; }
        body.dark-mode .bg-purple-50 { background-color: #3b0764 !important; }
        body.dark-mode .hover\:bg-purple-100:hover { background-color: #7e22ce !important; }
        body.dark-mode .bg-orange-50 { background-color: #431407 !important; }
        body.dark-mode .hover\:bg-orange-100:hover { background-color: #ea580c !important; }

        /* Activity dot ring items */
        body.dark-mode .bg-blue-100 { background-color: #1e3a5f !important; }
        body.dark-mode .bg-green-100 { background-color: #14532d !important; }
        body.dark-mode .bg-orange-100 { background-color: #431407 !important; }
        body.dark-mode .bg-purple-100 { background-color: #3b0764 !important; }

        /* Welcome banner text */
        body.dark-mode .bg-gradient-to-r {
            /* The gradient itself already has blue/purple so it's fine;
               just ensure text is visible */
        }

        /* Chart.js dark mode grid */
        body.dark-mode canvas {
            filter: none;
        }
    </style>
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <script>
        if (localStorage.getItem('backend-theme') === 'dark' || (!('backend-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.body.classList.add('dark-mode');
        }
    </script>
    <div class="wrapper">
        <!-- Navbar -->
        @include('backend.layout.navbar')
        
        <!-- Main Sidebar Container -->
        @include('backend.layout.sidebar')
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                    @if(View::hasSection('page_actions'))
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <div class="float-right">
                                    @yield('page_actions')
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-ban"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Terdapat kesalahan:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Main Content Area -->
                    @yield('content')
                </div>
            </section>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- JavaScript -->
    <script>
        // CSRF Token Setup
        window.csrf_token = '{{ csrf_token() }}';
        
        // Initialize AdminLTE components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns
            if (typeof $.fn.dropdown !== 'undefined') {
                $('[data-toggle="dropdown"]').dropdown();
                console.log('Bootstrap dropdown initialized');
            } else {
                console.error('Bootstrap dropdown not available');
                // Fallback: Manual dropdown toggle
                $('[data-toggle="dropdown"]').on('click', function(e) {
                    e.preventDefault();
                    const menu = $(this).next('.dropdown-menu');
                    $('.dropdown-menu').not(menu).removeClass('show');
                    menu.toggleClass('show');
                });
                
                // Close dropdown when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.dropdown').length) {
                        $('.dropdown-menu').removeClass('show');
                    }
                });
            }
            
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('[id$="-alert"]');
                alerts.forEach(alert => {
                    if (alert) {
                        alert.style.transition = 'opacity 0.5s ease-out';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }
                });
            }, 5000);
        });
        
        // Sidebar Toggle Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('hidden');
            }
        }
        
        // Loading Overlay Functions
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
            document.getElementById('loading-overlay').classList.add('flex');
        }
        
        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
            document.getElementById('loading-overlay').classList.remove('flex');
        }
        
        // Form Auto-submit with Loading
        function submitFormWithLoading(formId) {
            showLoading();
            document.getElementById(formId).submit();
        }
        
        // AJAX Helper Function
        function makeRequest(url, method = 'GET', data = null, headers = {}) {
            const defaultHeaders = {
                'X-CSRF-TOKEN': window.csrf_token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            
            return fetch(url, {
                method: method,
                headers: { ...defaultHeaders, ...headers },
                body: data ? JSON.stringify(data) : null
            });
        }
        
        // Confirmation Dialog
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        // Delete Function with Confirmation
        function deleteItem(url, message = 'Apakah Anda yakin ingin menghapus item ini?') {
            confirmAction(message, function() {
                showLoading();
                makeRequest(url, 'DELETE')
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menghapus item.');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus item.');
                    });
            });
        }
        
        // Responsive handling
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('show');
                overlay.classList.add('hidden');
            }
        });
        
        // Backend Theme Toggle logic
        const backendThemeToggleBtn = document.getElementById('backend-theme-toggle');
        const backendThemeIcon = document.getElementById('backend-theme-icon');
        const mainNavbar = document.querySelector('.main-header.navbar');

        function setBackendTheme(isDark) {
            if (isDark) {
                document.body.classList.add('dark-mode');
                if (mainNavbar) {
                    mainNavbar.classList.remove('navbar-white', 'navbar-light');
                    mainNavbar.classList.add('navbar-dark');
                }
                if (backendThemeIcon) {
                    backendThemeIcon.classList.remove('fa-moon');
                    backendThemeIcon.classList.add('fa-sun');
                }
            } else {
                document.body.classList.remove('dark-mode');
                if (mainNavbar) {
                    mainNavbar.classList.remove('navbar-dark');
                    mainNavbar.classList.add('navbar-white', 'navbar-light');
                }
                if (backendThemeIcon) {
                    backendThemeIcon.classList.remove('fa-sun');
                    backendThemeIcon.classList.add('fa-moon');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.body.classList.contains('dark-mode');
            setBackendTheme(isDark);
        });

        if (backendThemeToggleBtn) {
            backendThemeToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isCurrentlyDark = document.body.classList.contains('dark-mode');
                const willBeDark = !isCurrentlyDark;
                
                setBackendTheme(willBeDark);
                
                if (willBeDark) {
                    localStorage.setItem('backend-theme', 'dark');
                } else {
                    localStorage.setItem('backend-theme', 'light');
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>