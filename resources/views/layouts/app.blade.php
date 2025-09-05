<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MeloTech')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Heroicons CSS -->
    <link rel="stylesheet" href="{{ asset('css/heroicons.css') }}">

    <!-- Custom Styles -->
    @stack('styles')
    
    <!-- Admin-specific styles for admin pages -->
    @if(request()->routeIs('admin.*'))
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endif
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Navigation Bar Styles - Fully Bootstrap Compliant */
        .navbar {
            background: linear-gradient(135deg, #2E8B57 0%, #90EE90 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(46, 139, 87, 0.2);
            box-shadow: 0 4px 20px rgba(46, 139, 87, 0.15);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
            min-height: 80px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-right: 2rem;
        }

        .navbar-brand:hover {
            color: #f0f8f0 !important;
            transform: translateY(-1px);
        }

        .navbar-brand .logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(59, 130, 246, 0.2));
        }

        .navbar-brand .brand-text {
            background: linear-gradient(135deg, #ffffff 0%, #f0f8f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        /* Bootstrap-compliant navbar navigation */
        .navbar-nav {
            gap: 0.5rem;
            align-items: center;
        }

        .navbar-nav .nav-item {
            margin: 0;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }

        .navbar-nav .nav-link:hover {
            color: #f0f8f0 !important;
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .navbar-nav .nav-link.active {
            color: #f0f8f0 !important;
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .navbar-nav .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* Bootstrap-compliant navbar toggler */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Hide mobile toggle when no navigation links */
        .navbar:not(:has(.navbar-nav)) .navbar-toggler {
            display: none !important;
        }

        /* User menu styling */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        /* User dropdown button */
        .user-dropdown-btn {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 12px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            min-width: 160px !important;
        }

        .user-dropdown-btn:hover,
        .user-dropdown-btn:focus,
        .user-dropdown-btn.show {
            background: rgba(255, 255, 255, 0.25) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2) !important;
        }

        .user-dropdown-btn .user-name {
            font-weight: 500;
            color: white;
            margin-right: auto;
        }

        .user-dropdown-btn::after {
            display: none !important;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        /* Bootstrap-compliant dropdown menu */
        .user-dropdown-menu {
            background: white !important;
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
            padding: 0.5rem 0 !important;
            min-width: 280px !important;
            margin-top: 0.5rem !important;
        }

        .dropdown-header {
            padding: 1rem 1.25rem 0.75rem !important;
            background: transparent !important;
            border: none !important;
        }

        .dropdown-user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-user-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .dropdown-user-details {
            flex: 1;
        }

        .dropdown-user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .dropdown-user-email {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .dropdown-divider {
            margin: 0.5rem 0 !important;
            border-color: #e5e7eb !important;
        }

        .dropdown-item {
            padding: 0.75rem 1.25rem !important;
            color: #374151 !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            border: none !important;
            background: transparent !important;
            text-decoration: none !important;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: #f3f4f6 !important;
            color: #1f2937 !important;
            transform: translateX(4px) !important;
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        .logout-dropdown-item {
            color: #dc2626 !important;
            font-weight: 600 !important;
        }

        .logout-dropdown-item:hover {
            background: #fef2f2 !important;
            color: #b91c1c !important;
        }

        /* Bootstrap-compliant auth buttons */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border: none;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid #3b82f6;
            color: #3b82f6 !important;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-outline-primary:hover {
            background: #3b82f6;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Responsive Design - Bootstrap Breakpoints */
        @media (max-width: 991.98px) {
            .navbar-brand {
                margin-right: 1rem;
                font-size: 1.25rem;
            }
            
            .navbar-brand .logo {
                width: 32px;
                height: 32px;
            }

            .navbar-nav {
                margin-top: 1rem;
                gap: 0.25rem;
                width: 100%;
            }

            .navbar-nav .nav-item {
                margin: 0.25rem 0;
            }

            .navbar-nav .nav-link {
                justify-content: center;
                padding: 0.75rem 1rem !important;
                border-radius: 8px;
            }

            .user-menu {
                flex-direction: column;
                align-items: stretch;
                margin-top: 1rem;
                gap: 0.5rem;
                width: 100%;
            }

            /* Only apply full-width styling to user dropdown, not auth buttons */
            .user-menu .user-dropdown-btn {
                justify-content: center;
                width: 100%;
                min-width: auto !important;
            }

            /* Responsive auth buttons on mobile */
            .auth-buttons-responsive {
                margin-right: 1rem !important;
                display: flex !important;
                visibility: visible !important;
            }
            
            .auth-buttons-responsive .auth-btn {
                padding: 0.375rem 0.75rem !important;
                font-size: 0.875rem !important;
                display: inline-flex !important;
                visibility: visible !important;
            }
            
            .auth-buttons-responsive .auth-text {
                display: inline !important;
            }


        }

        @media (max-width: 767.98px) {
            .navbar-brand {
                font-size: 1.1rem;
                margin-right: 0.5rem;
            }
            
            .navbar-brand .logo {
                width: 28px;
                height: 28px;
            }
            
            .navbar-nav .nav-link {
                font-size: 0.85rem;
                padding: 0.6rem 0.8rem !important;
            }

            .user-menu .user-dropdown-btn {
                min-width: 140px !important;
                padding: 0.5rem 0.75rem !important;
            }

            .user-menu .user-dropdown-btn .user-name {
                display: none;
            }

            .user-dropdown-menu {
                min-width: 250px !important;
                right: 0 !important;
                left: auto !important;
            }

            /* Mobile auth button adjustments for smaller screens */
            .auth-buttons-mobile .d-flex.gap-2 {
                gap: 0.5rem !important;
            }

            .auth-buttons-mobile .btn {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
                max-width: 120px;
            }
        }

        @media (max-width: 575.98px) {
            .navbar {
                padding: 0.75rem 0;
                min-height: 70px;
            }

            .navbar-brand {
                font-size: 1rem;
            }
            
            .navbar-brand .logo {
                width: 24px;
                height: 24px;
            }

            .navbar-nav .nav-link {
                font-size: 0.8rem;
                padding: 0.5rem 0.75rem !important;
            }

            /* Ensure auth buttons are still visible on very small screens */
            .user-menu .d-flex.gap-2 {
                gap: 0.25rem !important;
            }

            .user-menu .d-flex.gap-2 .btn {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
                max-width: 100px;
            }

            .user-menu .d-flex.gap-2 .btn i {
                font-size: 0.7rem;
            }
        }

        /* Ensure content doesn't overlap with fixed navbar */
        body {
            padding-top: 80px;
        }

        .main-content {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }

        /* Additional responsive adjustments */
        .navbar-collapse {
            flex-basis: 100%;
            flex-grow: 1;
        }

        .navbar .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Ensure proper spacing and alignment */
        .navbar-nav .nav-item:first-child .nav-link {
            margin-left: 0;
        }

        /* Force visibility for all navbar elements */
        .navbar .btn-primary,
        .navbar .btn-outline-primary,
        .navbar .user-menu,
        .navbar-nav .nav-link {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure auth buttons are always visible and properly styled */
        .auth-buttons-responsive {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 1000 !important;
        }
        
        .auth-buttons-responsive .d-flex.gap-2 {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex-direction: row !important;
            align-items: center !important;
        }
        
        /* Override any potential hiding */
        .auth-buttons-responsive,
        .auth-buttons-responsive * {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .auth-buttons-responsive .auth-btn {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Large screen responsive behavior */
        @media (min-width: 992px) {
            .auth-buttons-responsive {
                display: flex !important;
                visibility: visible !important;
            }
            
            .auth-buttons-responsive .auth-btn {
                padding: 0.5rem 1rem !important;
                font-size: 1rem !important;
                display: inline-flex !important;
                visibility: visible !important;
            }
            
            .auth-buttons-responsive .auth-text {
                display: inline !important;
            }
        }

        .user-menu .d-flex.gap-2 .btn {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            white-space: nowrap !important;
        }

        /* Responsive Auth Buttons - Always Visible */
        .auth-buttons-responsive {
            display: flex !important;
            align-items: center !important;
            margin-left: auto !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .auth-buttons-responsive .d-flex.gap-2 {
            gap: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
        }

        .auth-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
            transition: all 0.3s ease !important;
            visibility: visible !important;
            opacity: 1 !important;
            text-decoration: none !important;
        }

        .auth-btn i {
            font-size: 0.875rem !important;
            margin-right: 0.25rem !important;
        }

        .auth-text {
            display: inline !important;
        }

        .auth-buttons-mobile .d-flex.gap-2 {
            gap: 0.5rem !important;
        }

        .auth-buttons-mobile .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.375rem !important;
            white-space: nowrap !important;
            min-width: auto !important;
        }

        .auth-buttons-mobile .btn i {
            font-size: 0.875rem !important;
        }

        /* Responsive adjustments for mobile auth buttons */
        @media (max-width: 575.98px) {
            .auth-buttons-responsive {
                margin-right: 0.5rem !important;
                display: flex !important;
                visibility: visible !important;
            }

            .auth-buttons-responsive .d-flex.gap-2 {
                gap: 0.25rem !important;
                display: flex !important;
            }

            .auth-buttons-responsive .auth-btn {
                padding: 0.25rem 0.5rem !important;
                font-size: 0.75rem !important;
                display: inline-flex !important;
                visibility: visible !important;
            }

            .auth-buttons-responsive .auth-btn i {
                font-size: 0.75rem !important;
            }
            
            .auth-buttons-responsive .auth-text {
                display: inline !important;
            }
        }

        /* Basic body styles */
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Main content container */
        .main-content {
            position: relative;
            z-index: 1;
            margin-top: 0;
            padding-top: 1rem;
        }

        /* Ensure content doesn't overlap with fixed navbar */
        body {
            padding-top: 80px;
        }

        .navbar {
            margin-bottom: 0;
        }

        /* Ensure all page content has proper spacing from navbar */
        .hero-section,
        .page-header,
        .section-card,
        .action-card,
        .table-card,
        .no-farms-section,
        .section {
            margin-top: 0;
        }
        


        /* Ensure first content element has proper spacing */
        .main-content > *:first-child {
            margin-top: 0;
        }
        
        /* Container adjustments */
        .container {
            position: relative;
            z-index: 1;
        }
        
        /* Ensure proper spacing for all content sections */
        .section-card,
        .action-card,
        .table-card,
        .no-farms-section,
        .section {
            margin-top: 0.5rem;
        }
        
        /* Fix for any potential overflow issues */
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
        }
        
        /* Fast page transitions */
        .page-transition {
            animation: fadeIn 0.1s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Performance optimizations for animations */
        .navbar,
        .main-content,
        .unified-header,
        .section-card,
        .stat-card,
        .analysis-card,
        .action-card {
            will-change: transform;
            backface-visibility: hidden;
            transform: translateZ(0);
        }

        /* Welcome Section Design - For dashboard pages */
        .welcome-section {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            margin-bottom: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .welcome-section .welcome-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .welcome-section .welcome-text {
            flex: 1;
        }

        .welcome-section .welcome-visual {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-section .welcome-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.025em;
            color: white;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .welcome-section .welcome-title i {
            font-size: 2rem;
            color: #ffd700;
        }

        .welcome-section .welcome-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            margin: 0 0 1rem 0;
            font-weight: 400;
            line-height: 1.6;
            color: white;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .welcome-section .welcome-circle {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .welcome-section .welcome-circle i {
            font-size: 2.25rem;
            color: #fbbf24;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        /* Unified Header Design - Based on welcome-section pattern */
        .unified-header {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            margin-bottom: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
        }

        .unified-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .unified-header .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .unified-header .header-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .unified-header .header-left {
            flex: 1;
        }

        .unified-header .header-right {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .unified-header .page-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.025em;
            color: white;
        }

        .unified-header .page-title i {
            font-size: 2rem;
            color: #ffd700;
        }

        .unified-header .page-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            margin: 0 0 1rem 0;
            font-weight: 400;
            line-height: 1.6;
            color: white;
        }

        .unified-header .header-visual {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .unified-header .header-circle {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .unified-header .header-circle i {
            font-size: 2.25rem;
            color: #fbbf24;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .unified-header .header-stats {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .unified-header .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            color: white;
        }

        .unified-header .stat-badge i {
            color: #fbbf24;
            font-size: 0.875rem;
        }

        .unified-header .stats-overview {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .unified-header .stat-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .unified-header .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffd700;
            margin-bottom: 0.25rem;
        }

        .unified-header .stat-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .unified-header .action-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .unified-header .action-status i {
            color: #fbbf24;
        }

        .unified-header .farm-info {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .unified-header .farm-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.5rem;
        }

        .unified-header .farm-details {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .unified-header .farm-variety {
            margin-right: 0.75rem;
        }

        .unified-header .update-status {
            margin-top: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .unified-header .update-status.ready {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        /* Page Header Alternative Styles */
        .page-header {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            margin-bottom: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .page-header .header-content {
            position: relative;
            z-index: 1;
        }

        .page-header .page-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            letter-spacing: -0.025em;
            color: white;
        }

        .page-header .page-title i {
            font-size: 2rem;
            color: #ffd700;
        }

        .page-header .page-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            margin: 0 0 1rem 0;
            font-weight: 400;
            line-height: 1.6;
            color: white;
        }

        /* Responsive adjustments for welcome section */
        @media (max-width: 991.98px) {
            .welcome-section {
                padding: 2rem 1.5rem;
            }
            
            .welcome-section .welcome-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }

            .welcome-section .welcome-visual {
                width: 100%;
                justify-content: flex-start;
            }

            .welcome-section .welcome-title {
                font-size: 1.875rem;
            }
            
            .welcome-section .welcome-subtitle {
                font-size: 1rem;
            }
            
            .welcome-section .header-stats {
                flex-direction: column;
                gap: 0.75rem;
                align-items: flex-start;
            }
            
            .welcome-section .stat-badge {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
        }

        /* Responsive adjustments for unified headers */
        @media (max-width: 991.98px) {
            .unified-header .header-main {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }

            .unified-header .header-right {
                width: 100%;
                justify-content: flex-start;
            }

            .unified-header .stats-overview {
                flex-wrap: wrap;
            }

            .unified-header .page-title {
                font-size: 1.875rem;
            }

            .page-header .page-title {
                font-size: 1.875rem;
            }
        }

        @media (max-width: 767.98px) {
            .welcome-section {
                padding: 1.5rem 1rem;
                margin-bottom: 1.5rem;
            }

            .welcome-section .welcome-content {
                gap: 1rem;
            }

            .welcome-section .welcome-title {
                font-size: 1.5rem;
                gap: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .welcome-section .welcome-title i {
                font-size: 1.5rem;
            }

            .welcome-section .welcome-subtitle {
                font-size: 0.95rem;
                margin-bottom: 0.75rem;
            }

            .welcome-section .welcome-circle {
                width: 60px;
                height: 60px;
            }

            .welcome-section .welcome-circle i {
                font-size: 1.5rem;
            }
            
            .welcome-section .header-stats {
                gap: 0.5rem;
            }
            
            .welcome-section .stat-badge {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }

            .unified-header {
                padding: 2rem 1.5rem;
            }

            .page-header {
                padding: 2rem 1.5rem;
            }

            .unified-header .page-title {
                font-size: 1.5rem;
            }

            .page-header .page-title {
                font-size: 1.5rem;
            }

            .unified-header .page-title i {
                font-size: 1.5rem;
            }

            .page-header .page-title i {
                font-size: 1.5rem;
            }

            .unified-header .header-circle {
                width: 70px;
                height: 70px;
            }

            .unified-header .header-circle i {
                font-size: 1.75rem;
            }
        }
        
        /* Extra small devices (phones) */
        @media (max-width: 575.98px) {
            .welcome-section {
                padding: 1rem 0.75rem;
                margin-bottom: 1rem;
                border-radius: 16px;
            }

            .welcome-section .welcome-content {
                gap: 0.75rem;
            }

            .welcome-section .welcome-title {
                font-size: 1.25rem;
                gap: 0.5rem;
                margin-bottom: 0.5rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .welcome-section .welcome-title i {
                font-size: 1.25rem;
            }

            .welcome-section .welcome-subtitle {
                font-size: 0.875rem;
                margin-bottom: 0.5rem;
                line-height: 1.4;
            }

            .welcome-section .welcome-circle {
                width: 50px;
                height: 50px;
            }

            .welcome-section .welcome-circle i {
                font-size: 1.25rem;
            }
            
            .welcome-section .header-stats {
                gap: 0.4rem;
                flex-direction: column;
                width: 100%;
            }
            
            .welcome-section .stat-badge {
                font-size: 0.75rem;
                padding: 0.3rem 0.5rem;
                width: 100%;
                justify-content: flex-start;
            }
            
            .welcome-section .welcome-visual {
                align-self: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <!-- Brand/Logo -->
            <a class="navbar-brand" href="{{ Auth::check() ? route('dashboard') : route('home') }}">
                <img src="{{ asset('images/melotech.png') }}" alt="MeloTech Logo" class="logo">
                <span class="brand-text">MeloTech</span>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                @if(Auth::check())
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @if(Auth::user()->role === 'admin')
                            <!-- Admin Navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-shield-alt"></i>
                                    Admin Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                    <i class="fas fa-users"></i>
                                    Manage Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.farms.*') ? 'active' : '' }}" href="{{ route('admin.farms.index') }}">
                                    <i class="fas fa-tractor"></i>
                                    Manage Farms
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}" href="{{ route('admin.statistics') }}">
                                    <i class="fas fa-chart-bar"></i>
                                    Statistics
                                </a>
                            </li>
                        @else
                            <!-- Regular User Navigation -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('crop-growth.*') ? 'active' : '' }}" href="{{ route('crop-growth.index') }}">
                                    <i class="fas fa-seedling"></i>
                                    Crop Growth
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('weather.*') ? 'active' : '' }}" href="{{ route('weather.index') }}">
                                    <i class="fas fa-cloud-sun"></i>
                                    Weather
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('crop-progress.*') ? 'active' : '' }}" href="{{ route('crop-progress.index') }}">
                                    <i class="fas fa-chart-line"></i>
                                    Crop Progress
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('photo-diagnosis.*') ? 'active' : '' }}" href="{{ route('photo-diagnosis.index') }}">
                                    <i class="fas fa-camera"></i>
                                    Photo Diagnosis
                                </a>
                            </li>
                        @endif
                    </ul>
                @endif

                <!-- User Menu -->
                <div class="user-menu">
                    @if(Auth::check())
                        <!-- User Dropdown for authenticated users -->
                        <div class="dropdown">
                            <button class="btn dropdown-toggle user-dropdown-btn" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="user-name">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu user-dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li class="dropdown-header">
                                    <div class="dropdown-user-info">
                                        <div class="dropdown-user-avatar">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                        <div class="dropdown-user-details">
                                            <div class="dropdown-user-name">{{ auth()->user()->name }}</div>
                                            <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if(Auth::user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            Admin Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-users me-2"></i>
                                            Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.farms.index') }}">
                                            <i class="fas fa-tractor me-2"></i>
                                            Manage Farms
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.statistics') }}">
                                            <i class="fas fa-chart-bar me-2"></i>
                                            Statistics
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.settings') }}">
                                        <i class="fas fa-user me-2"></i>
                                        Profile Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout-dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Auth Buttons for non-authenticated users (Always Visible) -->
            @if(!Auth::check())
                <div class="auth-buttons-responsive">
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary auth-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="auth-text">Login</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary auth-btn">
                            <i class="fas fa-user-plus"></i>
                            <span class="auth-text">Register</span>
                        </a>
                    </div>
                </div>
            @endif
                </div>
            </div>


        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} MeloTech. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">

                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <!-- Admin-specific scripts for admin pages -->
    @if(request()->routeIs('admin.*'))
        <script src="{{ asset('js/admin.js') }}"></script>
    @endif
    

    
    <!-- User Dropdown Enhancement Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced dropdown functionality
        const userDropdown = document.getElementById('userDropdown');
        const dropdownMenu = document.querySelector('.user-dropdown-menu');
        
        if (userDropdown && dropdownMenu) {
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!userDropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(userDropdown);
                    if (bsDropdown) {
                        bsDropdown.hide();
                    }
                }
            });
            
            // Enhanced hover effects for dropdown items
            const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(4px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        }

        // Handle mobile toggle button visibility
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarNav = document.querySelector('.navbar-nav');
        
        if (navbarToggler && navbarNav) {
            // Hide mobile toggle if no navigation links
            if (!navbarNav.children.length) {
                navbarToggler.style.display = 'none';
            }
        }
    });
    </script>
</body>
</html>