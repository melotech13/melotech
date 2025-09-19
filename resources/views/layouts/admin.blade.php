<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - MeloTech')</title>
    <link rel="icon" href="{{ asset('images/melotech.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('images/melotech.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/melotech.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-color: #3b82f6;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --border-color: #e5e7eb;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Reset any conflicting styles */
        .admin-sidebar * {
            box-sizing: border-box;
        }

        /* Override any conflicting styles */
        .admin-sidebar .nav-link-text,
        .admin-sidebar .sidebar-brand-text,
        .admin-sidebar .nav-section-title {
            display: inline-block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* All text elements are always visible */

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Admin Layout Structure */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: var(--shadow-xl);
            overflow-y: auto;
            overflow-x: hidden;
            display: block;
            transform: none;
        }

        /* Sidebar is now permanently fixed at full width */

        /* Sidebar Header */
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: var(--header-height);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-brand img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .sidebar-brand-text {
            transition: var(--transition);
        }

        /* Sidebar brand text is always visible */

        /* Sidebar toggle button removed - sidebar is now permanently fixed */

        /* Sidebar Navigation */
        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-title {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.5rem 0.5rem;
            margin-bottom: 0.5rem;
            transition: var(--transition);
            opacity: 1;
            visibility: visible;
        }

        /* Section titles are always visible */

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            font-weight: 500;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        /* Hover effect for navigation links */

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-right: 3px solid white;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .nav-link-text {
            transition: var(--transition);
            opacity: 1;
            visibility: visible;
            white-space: nowrap;
        }

        /* Navigation link text is always visible */

        /* Tooltips no longer needed since sidebar is always expanded */

        /* Main Content Area */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main content area is always positioned for full sidebar width */

        /* Top Header */
        .admin-header {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .user-role {
            color: var(--text-secondary);
            font-size: 0.75rem;
        }


        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            margin-right: 1rem;
        }

        .notification-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            color: var(--text-primary);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            border: none;
            cursor: pointer;
        }

        .notification-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .notification-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .notification-link i {
            font-size: 1.1rem;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-primary);
            animation: pulse 2s infinite;
            padding: 0 4px;
            box-sizing: border-box;
        }

        .notification-badge:empty {
            display: none;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Simple Notifications Styles */
        .notification-item-simple.unread {
            background-color: rgba(13, 110, 253, 0.05);
            border-left: 3px solid #0d6efd;
        }

        .notification-icon-simple {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            font-size: 1.1rem;
        }

        .notification-item-simple:hover {
            background-color: #f8f9fa;
        }

        .notification-item-simple:last-child .border-bottom {
            border-bottom: none !important;
        }

        /* Notification Dropdown Styles */
        .notification-dropdown {
            width: 350px;
            max-height: 400px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 0;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }

        .notification-header .dropdown-header {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        .notification-list {
            max-height: 250px;
            overflow-y: auto;
        }

        .notification-item-dropdown {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
            cursor: pointer;
        }

        .notification-item-dropdown:hover {
            background: var(--bg-secondary);
        }

        .notification-item-dropdown.unread {
            background: rgba(13, 110, 253, 0.05);
            border-left: 3px solid var(--primary-color);
        }

        .notification-item-dropdown:last-child {
            border-bottom: none;
        }

        .notification-icon-dropdown {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .notification-icon-dropdown i {
            font-size: 0.9rem;
        }

        .notification-content-dropdown {
            flex: 1;
            min-width: 0;
        }

        .notification-title-dropdown {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }

        .notification-message-dropdown {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .notification-time-dropdown {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .notification-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }

        /* Scrollbar for notification list */
        .notification-list::-webkit-scrollbar {
            width: 4px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 2px;
        }

        .notification-list::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Content Area */
        .admin-content {
            flex: 1;
            padding: 2rem;
        }

        /* Basic layout styles - detailed styles are in admin.css */

        /* Basic component styles - detailed styles are in admin.css */

        /* Basic responsive styles - detailed responsive styles are in admin.css */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1001;
            }

            .admin-sidebar.show {
                transform: translateX(0);
                position: fixed;
                z-index: 1001;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-content {
                padding: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .admin-header {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .user-info {
                display: none;
            }

            .admin-content {
                padding: 0.75rem;
            }
        }

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Basic loading and animation styles - detailed styles are in admin.css */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar Styling */
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* All text elements are always visible */

        /* All text elements are always visible */

        /* Ensure sidebar stays fixed and properly positioned */
        .admin-sidebar {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            height: 100vh !important;
            z-index: 1000 !important;
        }

        /* Ensure main content area is properly positioned */
        .admin-main {
            position: relative;
            z-index: 1;
        }

        /* Ensure header stays above content but below sidebar */
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Sidebar visual styling */

        /* Sidebar is permanently fixed at full width */

        /* Sidebar is permanently fixed and expanded */
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Admin Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('images/melotech.png') }}" alt="MeloTech">
                    <span class="sidebar-brand-text">MeloTech Admin</span>
                </a>
                <!-- Sidebar toggle button removed - sidebar is now permanently fixed -->
            </div>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <!-- Dashboard Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-link-text">Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span class="nav-link-text">Users</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.farms.index') }}" class="nav-link {{ request()->routeIs('admin.farms.*') ? 'active' : '' }}">
                            <i class="fas fa-tractor"></i>
                            <span class="nav-link-text">Farms</span>
                        </a>
                    </div>
                </div>

                <!-- Analytics Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Analytics</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.statistics') }}" class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-link-text">Statistics</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.notifications') }}" class="nav-link {{ request()->routeIs('admin.notifications') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i>
                            <span class="nav-link-text">Notifications</span>
                        </a>
                    </div>
                </div>

                <!-- System Section -->
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-link-text">Settings</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link" style="background: none; border: none; width: 100%; text-align: left;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="nav-link-text">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main" id="adminMain">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="btn btn-outline-primary d-lg-none" id="mobileSidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Admin Dashboard')</h1>
                </div>
                <div class="header-right">
                    <div class="notification-bell">
                        <div class="dropdown">
                            <button class="notification-link dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span class="notification-badge" id="notificationBadge" data-initial-count="{{ $unreadNotificationCount ?? 0 }}">
                                    @if(($unreadNotificationCount ?? 0) > 0)
                                        {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                                    @endif
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                                <div class="notification-header">
                                    <h6 class="dropdown-header">Notifications</h6>
                                    <a href="{{ route('admin.notifications') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="notification-list" id="notificationList">
                                    <div class="text-center p-3">
                                        <i class="fas fa-spinner fa-spin"></i> Loading notifications...
                                    </div>
                                </div>
                                <div class="notification-footer">
                                    <button class="btn btn-sm btn-outline-secondary w-100" id="markAllReadDropdown">
                                        <i class="fas fa-check-double me-1"></i>
                                        Mark All as Read
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            const main = document.getElementById('adminMain');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Sidebar toggle functionality removed - sidebar is now permanently fixed

            // Mobile sidebar toggle
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.add('show');
                    sidebarOverlay.classList.add('show');
                });
            }

            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // Sidebar is permanently fixed and expanded - no state management needed

            // Close mobile sidebar when clicking on a link
            const navLinks = sidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });

            // Add fade-in animation to content
            const content = document.querySelector('.admin-content');
            if (content) {
                content.classList.add('fade-in');
            }

            // Initialize notification badge with server-side count
            const initialCount = parseInt(document.getElementById('notificationBadge').getAttribute('data-initial-count')) || 0;
            updateNotificationBadge(initialCount);
            
            // Load notifications on page load (but don't override the initial count immediately)
            loadNotifications(false);
            
            // Load notifications every 10 seconds for real-time updates
            setInterval(loadNotifications, 10000);
            
            // Also load notifications when the dropdown is opened
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('shown.bs.dropdown', function() {
                    loadNotifications(true); // Update count when dropdown is opened
                });
            }

            // Global function to refresh notifications after admin actions
            window.refreshNotifications = function() {
                refreshNotificationCount();
                loadNotifications();
            };

            // Notification dropdown functionality
            const markAllReadDropdown = document.getElementById('markAllReadDropdown');
            if (markAllReadDropdown) {
                markAllReadDropdown.addEventListener('click', function() {
                    // Disable button to prevent multiple clicks
                    markAllReadDropdown.disabled = true;
                    markAllReadDropdown.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
                    
                    // Make AJAX request to backend
                    fetch('{{ route("admin.notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh notification count and list
                            refreshNotificationCount();
                            loadNotifications();
                            
                            // Do not show a success alert
                        } else {
                            throw new Error(data.message || 'Failed to mark notifications as read');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-danger alert-dismissible fade show';
                        alert.innerHTML = `
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error: ${error.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.admin-content').insertBefore(alert, document.querySelector('.admin-content').firstChild);
                    })
                    .finally(() => {
                        // Re-enable button
                        markAllReadDropdown.disabled = false;
                        markAllReadDropdown.innerHTML = '<i class="fas fa-check-double me-1"></i>Mark All as Read';
                    });
                });
            }

            // Function to load notifications from backend
            function loadNotifications(updateCount = true) {
                fetch('{{ route("admin.notifications.dropdown") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    cache: 'no-cache'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && typeof data === 'object') {
                        updateNotificationDropdown(data.notifications || []);
                        // Only update count if explicitly requested or if it's different from current
                        if (updateCount) {
                            const currentCount = parseInt(document.getElementById('notificationBadge').textContent) || 0;
                            const newCount = data.unread_count || 0;
                            if (currentCount !== newCount) {
                                updateNotificationBadge(newCount);
                            }
                        }
                    } else {
                        console.warn('Invalid notification data received:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    // Show error state in dropdown
                    const notificationList = document.getElementById('notificationList');
                    if (notificationList) {
                        notificationList.innerHTML = '<div class="text-center p-3 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load notifications</div>';
                    }
                });
            }

            // Function to update notification dropdown content
            function updateNotificationDropdown(notifications) {
                const notificationList = document.getElementById('notificationList');
                
                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div class="text-center p-3 text-muted">No notifications</div>';
                    return;
                }

                let html = '';
                notifications.forEach(notification => {
                    const timeAgo = getTimeAgo(notification.created_at);
                    const iconClass = getNotificationIcon(notification.type);
                    const unreadClass = !notification.read ? 'unread' : '';
                    
                    html += `
                        <div class="notification-item-dropdown ${unreadClass}" data-id="${notification.id}">
                            <div class="notification-icon-dropdown">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="notification-content-dropdown">
                                <div class="notification-title-dropdown">${notification.title}</div>
                                <div class="notification-message-dropdown">${notification.message}</div>
                                <div class="notification-time-dropdown">${timeAgo}</div>
                            </div>
                        </div>
                    `;
                });
                
                notificationList.innerHTML = html;
            }

            // Function to update notification badge
            function updateNotificationBadge(count) {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    // Ensure count is a valid number
                    const validCount = parseInt(count) || 0;
                    
                    if (validCount > 0) {
                        badge.textContent = validCount > 99 ? '99+' : validCount.toString();
                        badge.style.display = 'flex';
                        badge.style.visibility = 'visible';
                    } else {
                        badge.style.display = 'none';
                        badge.style.visibility = 'hidden';
                    }
                }
            }

            // Function to refresh notification count only
            function refreshNotificationCount() {
                fetch('{{ route("admin.notifications.unread-count") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    cache: 'no-cache'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && typeof data === 'object') {
                        updateNotificationBadge(data.unread_count || 0);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing notification count:', error);
                });
            }

            // Function to get notification icon based on type
            function getNotificationIcon(type) {
                const icons = {
                    'system': 'fas fa-cog text-info',
                    'user': 'fas fa-user text-success',
                    'farm': 'fas fa-seedling text-warning',
                    'analysis': 'fas fa-camera text-primary',
                    'progress': 'fas fa-chart-line text-info',
                    'default': 'fas fa-bell text-primary'
                };
                return icons[type] || icons.default;
            }

            // Function to get time ago string
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'Just now';
                if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
                if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
                if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
                return date.toLocaleDateString();
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.querySelector('.notification-dropdown');
                const button = document.querySelector('#notificationDropdown');
                
                if (dropdown && button && !button.contains(event.target) && !dropdown.contains(event.target)) {
                    const bsDropdown = new bootstrap.Dropdown(button);
                    bsDropdown.hide();
                }
            });

            // Dashboard animations and interactions
            console.log('Initializing dashboard animations...');
            initializeDashboardAnimations();

            // Function to ensure sidebar positioning
            function ensureSidebarPosition() {
                if (sidebar) {
                    sidebar.style.position = 'fixed';
                    sidebar.style.left = '0';
                    sidebar.style.top = '0';
                    sidebar.style.height = '100vh';
                    sidebar.style.zIndex = '1000';
                }
            }

            // Ensure sidebar positioning on load
            ensureSidebarPosition();

            // Ensure sidebar positioning on window resize
            window.addEventListener('resize', ensureSidebarPosition);

            // Dashboard animations function
            function initializeDashboardAnimations() {
                console.log('Dashboard animations function called');
                
                // Animate metric cards on load
                const metricCards = document.querySelectorAll('.metric-card');
                metricCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        card.style.transition = 'all 0.6s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });

                // Animate welcome card
                const welcomeCard = document.querySelector('.welcome-card');
                if (welcomeCard) {
                    welcomeCard.style.opacity = '0';
                    welcomeCard.style.transform = 'translateY(-20px)';
                    
                    setTimeout(() => {
                        welcomeCard.style.transition = 'all 0.8s ease';
                        welcomeCard.style.opacity = '1';
                        welcomeCard.style.transform = 'translateY(0)';
                    }, 200);
                }

                // Animate content sections
                const contentSections = document.querySelectorAll('.content-section');
                contentSections.forEach((section, index) => {
                    section.style.opacity = '0';
                    section.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        section.style.transition = 'all 0.6s ease';
                        section.style.opacity = '1';
                        section.style.transform = 'translateX(0)';
                    }, 600 + (index * 200));
                });

                // Animate system status
                const statusCard = document.querySelector('.status-card');
                if (statusCard) {
                    statusCard.style.opacity = '0';
                    statusCard.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        statusCard.style.transition = 'all 0.6s ease';
                        statusCard.style.opacity = '1';
                        statusCard.style.transform = 'translateY(0)';
                    }, 1000);
                }

                // Add hover effects to action items
                const actionItems = document.querySelectorAll('.action-item');
                actionItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(8px)';
                        this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
                    });
                    
                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                        this.style.boxShadow = 'none';
                    });
                });

                // Add hover effects to metric cards
                metricCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px)';
                        this.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.15)';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.05)';
                    });
                });

                // Animate activity items
                const activityItems = document.querySelectorAll('.activity-item');
                activityItems.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-10px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'all 0.4s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, 1200 + (index * 100));
                });

                // Add pulse animation to status dot
                const statusDot = document.querySelector('.status-dot');
                if (statusDot) {
                    statusDot.style.animation = 'pulse 2s infinite';
                }

                // Add real-time clock update
                updateClock();
                setInterval(updateClock, 1000);

                // Add click animations to metric cards
                metricCards.forEach(card => {
                    card.addEventListener('click', function() {
                        this.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            this.style.transform = 'translateY(-8px) scale(1.02)';
                        }, 150);
                    });
                });

                // Add ripple effect to action items
                actionItems.forEach(item => {
                    item.addEventListener('click', function(e) {
                        const ripple = document.createElement('span');
                        const rect = this.getBoundingClientRect();
                        const size = Math.max(rect.width, rect.height);
                        const x = e.clientX - rect.left - size / 2;
                        const y = e.clientY - rect.top - size / 2;
                        
                        ripple.style.width = ripple.style.height = size + 'px';
                        ripple.style.left = x + 'px';
                        ripple.style.top = y + 'px';
                        ripple.classList.add('ripple');
                        
                        this.appendChild(ripple);
                        
                        setTimeout(() => {
                            ripple.remove();
                        }, 600);
                    });
                });

                            // Add number counting animation to metrics
            animateNumbers();
            
            // Initialize progress bars
            initializeProgressBars();
        }
        
        // Function to initialize progress bars
        function initializeProgressBars() {
            const progressBars = document.querySelectorAll('.progress-fill[data-width]');
            progressBars.forEach(bar => {
                const width = bar.getAttribute('data-width');
                bar.style.width = width + '%';
            });
        }



            // Clock update function
            function updateClock() {
                const timeElement = document.querySelector('.current-time-small');
                if (timeElement) {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    timeElement.textContent = timeString;
                }
            }

            // Number counting animation function
            function animateNumbers() {
                const metricNumbers = document.querySelectorAll('.metric-number');
                
                metricNumbers.forEach(element => {
                    const target = parseInt(element.textContent);
                    const duration = 2000; // 2 seconds
                    const increment = target / (duration / 16); // 60fps
                    let current = 0;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        element.textContent = Math.floor(current);
                    }, 16);
                });
            }
        });
    </script>

    <!-- Success Modal Component -->
    @include('components.success-modal')

    <!-- Locations JavaScript for dropdowns -->
    <script src="{{ asset('js/locations-simple.js') }}?v={{ time() }}"></script>
</body>
</html>

