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
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Enhanced navbar styling - modern and attractive */
        .navbar {
            padding: 1.25rem 2rem;
            min-height: 90px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 800;
            padding: 0.75rem 0;
            display: flex;
            align-items: center;
            color: white !important;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .navbar-brand:hover {
            color: #f0fdf4 !important;
            transform: translateY(-2px);
            text-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        
        .navbar-brand img {
            height: 40px !important;
            width: auto;
            max-width: none;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
        }
        
        .navbar-nav .nav-link {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
            font-weight: 600;
            color: rgba(255,255,255,0.95) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            margin: 0 0.375rem;
            position: relative;
            overflow: hidden;
        }
        
        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .navbar-nav .nav-link:hover::before {
            left: 100%;
        }
        
        .navbar-nav .nav-link:hover {
            color: rgba(255,255,255,1) !important;
            background: rgba(255,255,255,0.15);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .navbar-nav .nav-link.active {
            color: rgba(255,255,255,1) !important;
            font-weight: 700;
            background: rgba(255,255,255,0.2);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.3);
        }
        

        
        /* Enhanced dropdown menu */
        .dropdown-menu {
            margin-top: 1rem;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border-radius: 16px;
            padding: 1rem 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.2);
            min-width: 280px;
            transform-origin: top center;
            animation: dropdownSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .dropdown-header {
            padding: 1rem 1.5rem 0.75rem;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            margin-bottom: 0.75rem;
        }
        

        
        .dropdown-item {
            padding: 0.875rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 8px;
            margin: 0 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: #1f2937;
            transform: translateX(4px);
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
            color: #6b7280;
        }
        
        .dropdown-item:hover i {
            color: #059669;
        }
        
        .dropdown-divider {
            margin: 0.75rem 1rem;
            border-color: rgba(0,0,0,0.08);
        }
        
        /* Enhanced login/register buttons */
        .navbar-nav .nav-link.auth-btn {
            background: rgba(255,255,255,0.15);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50px;
            padding: 0.875rem 2rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .navbar-nav .nav-link.auth-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }
        
        .navbar-nav .nav-link.auth-btn:hover::before {
            left: 100%;
        }
        
        .navbar-nav .nav-link.auth-btn:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.6);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        }
        
        .navbar-nav .nav-link.auth-btn.login-btn {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border-color: #059669;
        }
        
        .navbar-nav .nav-link.auth-btn.register-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-color: #3b82f6;
        }
        
        .navbar-toggler {
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler:hover {
            border-color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.1);
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255,255,255,0.25);
        }
        
        /* Ensure proper spacing */
        .navbar-collapse {
            align-items: center;
        }
        
        /* Force navigation to be visible */
        .navbar-nav {
            display: flex !important;
            flex-direction: row;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        
        .navbar-nav li {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        /* Ensure navbar collapse works properly */
        .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
            align-items: center;
        }
        
        /* Override any Bootstrap collapse behavior */
        .navbar-collapse.collapse {
            display: flex !important;
        }
        
        .navbar-collapse.collapsing {
            display: flex !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .navbar {
                padding: 1rem 1.5rem;
                min-height: 80px;
            }
            
            .navbar-nav {
                margin-top: 1.5rem;
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }
            
            .navbar-nav .nav-link {
                padding: 1rem 0;
                width: 100%;
                margin: 0.375rem 0;
                border-radius: 8px;
            }
            
            .navbar-nav.ms-auto {
                margin-top: 1rem;
            }
            
            .navbar-nav .nav-link.auth-btn {
                text-align: center;
                margin: 0.75rem 0;
                width: 100%;
            }
            

        }
        
        /* Add margin to body to account for fixed header */
        body {
            padding-top: 90px;
            font-family: 'Figtree', sans-serif;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Enhanced animations */
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Enhanced focus states */
        .navbar-nav .nav-link:focus,
        .dropdown-item:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/melotech.png') }}" alt="MeloTech" class="d-inline-block align-text-top me-3">
                MeloTech
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @if(Auth::check())
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('weather.*') ? 'active' : '' }}" href="{{ route('weather.index') }}">
                                <i class="fas fa-cloud-sun me-2"></i>Weather
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('crop-growth.*') ? 'active' : '' }}" href="{{ route('crop-growth.index') }}">
                                <i class="fas fa-seedling me-2"></i>Crop Growth
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('crop-progress.*') ? 'active' : '' }}" href="{{ route('crop-progress.index') }}">
                                <i class="fas fa-clipboard-check me-2"></i>Progress Update
                            </a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">

                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('weather.index') }}">
                                    <i class="fas fa-cloud-sun"></i>Weather
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('crop-growth.index') }}">
                                    <i class="fas fa-seedling"></i>Crop Growth
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('crop-progress.index') }}">
                                    <i class="fas fa-clipboard-check"></i>Progress Update
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                
                                <li><a class="dropdown-item" href="#">
                                    <i class="fas fa-cog"></i>Preferences
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link auth-btn login-btn" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link auth-btn register-btn" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; {{ date('Y') }} MeloTech. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom Scripts -->
    @stack('scripts')
    
    <script>
        // Enhanced navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navbarCollapse = document.getElementById('navbarNav');
            if (navbarCollapse) {
                // Remove collapse class to ensure navigation is always visible
                navbarCollapse.classList.remove('collapse');
                navbarCollapse.style.display = 'flex';
                
                // Force all navbar-nav elements to be visible
                const navbarNavs = document.querySelectorAll('.navbar-nav');
                navbarNavs.forEach(nav => {
                    nav.style.display = 'flex';
                    nav.style.visibility = 'visible';
                    nav.style.opacity = '1';
                });
            }
            
            // Add scroll effect to navbar
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 50) {
                        navbar.style.background = 'linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%)';
                        navbar.style.boxShadow = '0 12px 40px rgba(0,0,0,0.15)';
                        navbar.style.padding = '1rem 2rem';
                        navbar.style.minHeight = '80px';
                    } else {
                        navbar.style.background = 'linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%)';
                        navbar.style.boxShadow = '0 8px 32px rgba(0,0,0,0.12)';
                        navbar.style.padding = '1.25rem 2rem';
                        navbar.style.minHeight = '90px';
                    }
                });
            }
            
            // Enhanced dropdown animations
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('show.bs.dropdown', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.style.animation = 'none';
                        menu.offsetHeight; // Trigger reflow
                        menu.style.animation = 'dropdownSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    }
                });
            });
            
            // Add fade-in animation to main content
            const mainContent = document.querySelector('main');
            if (mainContent) {
                mainContent.classList.add('fade-in');
            }
        });
        
        // Ensure navigation stays visible on window resize
        window.addEventListener('resize', function() {
            const navbarCollapse = document.getElementById('navbarNav');
            if (navbarCollapse) {
                navbarCollapse.style.display = 'flex';
            }
        });
        
        // Enhanced mobile navigation
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('navbar-toggler') || e.target.closest('.navbar-toggler')) {
                const navbar = document.querySelector('.navbar-collapse');
                if (navbar) {
                    setTimeout(() => {
                        if (navbar.classList.contains('show')) {
                            navbar.style.display = 'flex';
                        }
                    }, 100);
                }
            }
        });
    </script>
</body>
</html>
