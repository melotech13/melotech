<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MeloTech') }} - @yield('title', 'Watermelon Farming Technology')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --dark-color: #2d3748;
            --light-color: #f7fafc;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --info-color: #4299e1;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --border-color: #e2e8f0;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.12);
            --navbar-height: 120px;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            min-height: 100vh;
            padding-top: 0; /* Ensure no body padding */
            margin: 0; /* Ensure no body margin */
        }

        html {
            scroll-padding-top: 160px; /* Ensure smooth scrolling with navbar */
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: translateY(-1px);
            color: #5a67d8 !important;
        }

        .navbar-brand img {
            object-fit: contain;
            max-height: 60px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        /* Enhanced Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 0.75rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
        }

        .navbar-nav .nav-item {
            margin: 0 0.25rem;
        }

        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            padding: 0.75rem 1.25rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .navbar-nav .nav-link:active {
            transform: translateY(0);
        }

        /* Main navigation tabs styling */
        .navbar-nav .nav-item:not(:last-child) .nav-link {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-color) !important;
            border: 1px solid rgba(59, 130, 246, 0.2);
            margin-right: 0.5rem;
            font-weight: 600;
        }

        .navbar-nav .nav-item:not(:last-child) .nav-link:hover {
            background: rgba(59, 130, 246, 0.2) !important;
            color: var(--primary-color) !important;
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        .navbar-nav .nav-item:not(:last-child) .nav-link.active {
            background: rgba(59, 130, 246, 0.25) !important;
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        /* Ensure proper spacing between nav items */
        .navbar-nav .nav-item {
            margin-right: 0.25rem;
        }

        .navbar-nav .nav-item:last-child {
            margin-right: 0;
        }

        /* Responsive navigation adjustments */
        @media (max-width: 991.98px) {
            .navbar-nav .nav-item:not(:last-child) .nav-link {
                margin-bottom: 0.5rem;
                margin-right: 0;
                text-align: center;
            }
            
            .navbar-nav .nav-item .nav-link {
                margin-right: 0;
                margin-bottom: 0.25rem;
            }
        }

        .nav-link {
            background: rgba(59, 130, 246, 0.8);
            color: white !important;
            border: none;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(59, 130, 246, 0.9) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .nav-link-register {
            background: rgba(34, 197, 94, 0.8);
            color: white !important;
            border: none;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
        }

        .nav-link-register:hover {
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
        }

        .user-dropdown {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            padding: 0.75rem 1.25rem !important;
        }

        .user-dropdown:hover {
            background: rgba(102, 126, 234, 0.15) !important;
        }

        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            color: var(--text-dark);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: rgba(0,0,0,0.08);
        }

        .custom-toggler {
            border: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .custom-toggler:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .custom-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(102, 126, 234, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #5a67d8;
            border-color: #5a67d8;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: border-color 0.2s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .watermelon-icon {
            color: var(--accent-color);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 4rem 0;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
            position: relative;
            z-index: 1;
        }

        /* Ensure navigation is properly contained */
        .navbar-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            width: 100%;
            background: white;
            height: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            position: relative;
            z-index: 1000;
            min-height: 80px;
            height: auto;
        }

        /* Ensure navbar container has proper height */
        .navbar-container {
            height: auto;
            min-height: 100px;
        }

        /* Ensure main content is properly positioned */
        main {
            position: relative;
            z-index: 1;
            width: 100%;
            margin-top: 140px !important; /* Force proper spacing */
            padding-top: 2rem; /* Additional padding for better spacing */
        }

        /* Ensure the navbar doesn't cover content */
        .navbar-container {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Specific styling for crop growth pages */
        .crop-growth-container,
        .crop-tracking-container {
            margin-top: 2rem !important; /* Force additional top margin */
            padding-top: 2rem; /* Increased padding for better spacing */
            position: relative;
            z-index: 1;
        }

        /* Ensure proper spacing for crop growth content */
        main .crop-growth-container,
        main .crop-tracking-container {
            position: relative;
            z-index: 1;
        }

        /* Additional spacing for crop growth page headers */
        .crop-growth-container .header-section,
        .crop-tracking-container .header-section {
            margin-top: 2rem !important; /* Force additional top margin */
            position: relative;
            z-index: 1;
        }

        /* Ensure content is not hidden behind navbar */
        .crop-growth-container,
        .crop-tracking-container {
            position: relative;
            z-index: 1;
            background: transparent;
            min-height: calc(100vh - 200px); /* Ensure minimum height */
        }

        /* Add some breathing room at the very top */
        .crop-growth-container > *:first-child,
        .crop-tracking-container > *:first-child {
            margin-top: 1rem !important; /* Force margin */
        }

        /* Additional safety spacing */
        .crop-growth-container::before,
        .crop-tracking-container::before {
            content: '';
            display: block;
            height: 2rem;
            width: 100%;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .feature-card {
                margin-bottom: 1rem;
            }
            
            /* Ensure proper spacing on mobile */
            main {
                margin-top: 120px !important; /* Adequate margin on mobile */
                padding-top: 1.5rem !important; /* Adequate padding on mobile */
            }

            /* Mobile-specific container spacing */
            .crop-growth-container,
            .crop-tracking-container {
                margin-top: 1.5rem !important;
                padding-top: 1.5rem;
            }
            
            .navbar {
                min-height: 60px;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column">
    <!-- Navigation -->
    <header class="navbar-container">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-lg">
            <div class="container">
                <a class="navbar-brand" href="{{ Auth::check() ? route('dashboard') : route('home') }}">
                    <img src="{{ asset('images/melotech.png') }}" alt="MeloTech Logo" height="60" class="me-2">
                    MeloTech
                </a>
                
                <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-register" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Register
                                </a>
                            </li>
                        @else
                            @php
                                $currentRoute = request()->route()->getName();
                                $showNavTabs = !in_array($currentRoute, ['home', 'login', 'register']);
                            @endphp
                            
                            @if($showNavTabs)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('crop-growth.index') }}">
                                        <i class="fas fa-seedling me-1"></i>
                                        Track Growth
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle user-dropdown" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('crop-growth.index') }}">
                                        <i class="fas fa-seedling me-2"></i>Track Growth
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><img src="{{ asset('images/melotech.png') }}" alt="MeloTech Logo" height="45" class="me-2">MeloTech</h5>
                    <p class="mb-0">Revolutionizing watermelon farming with smart technology.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} MeloTech. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    
    @stack('scripts')
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Bootstrap dropdown initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns
            const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            const dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Highlight active navigation tab if tabs are present
            const navTabs = document.querySelectorAll('.navbar-nav .nav-item:not(:last-child) .nav-link');
            if (navTabs.length > 0) {
                highlightActiveNavTab();
            }
        });

        // Function to highlight the active navigation tab
        function highlightActiveNavTab() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href) {
                    // Check if current path matches the route
                    if (currentPath === href || 
                        (href !== '/' && currentPath.startsWith(href))) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                }
            });
        }

        // Adjust content margin based on actual navbar height
        function adjustContentMargin() {
            const navbar = document.querySelector('.navbar-container');
            const main = document.querySelector('main');
            if (navbar && main) {
                const navbarHeight = navbar.offsetHeight;
                main.style.marginTop = navbarHeight + 'px';
                document.documentElement.style.setProperty('--navbar-height', navbarHeight + 'px');
                
                // Add additional spacing for better visual separation
                const additionalSpacing = 20; // 20px additional spacing
                main.style.paddingTop = (additionalSpacing / 16) + 'rem';
            }
        }

        // Call on page load and resize
        window.addEventListener('load', adjustContentMargin);
        window.addEventListener('resize', adjustContentMargin);

        // Auto logout functionality
        @if(Auth::check())
        // Set up session timeout and auto logout
        let sessionTimeout;
        
        function resetSessionTimeout() {
            clearTimeout(sessionTimeout);
            // Set timeout to 30 minutes (1800000 ms)
            sessionTimeout = setTimeout(function() {
                // Auto logout after 30 minutes of inactivity
                fetch('{{ route("logout") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                }).then(() => {
                    window.location.href = '{{ route("home") }}';
                });
            }, 1800000);
        }

        // Reset timeout on user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(function(event) {
            document.addEventListener(event, resetSessionTimeout, true);
        });

        // Reset timeout on page focus
        window.addEventListener('focus', resetSessionTimeout);
        
        // Initialize timeout
        resetSessionTimeout();

        // Handle page unload (browser close/refresh)
        window.addEventListener('beforeunload', function() {
            // Send logout request when page is closed/refreshed
            navigator.sendBeacon('{{ route("logout") }}', JSON.stringify({
                '_token': '{{ csrf_token() }}'
            }));
        });
        @endif
    </script>
</body>
</html>
