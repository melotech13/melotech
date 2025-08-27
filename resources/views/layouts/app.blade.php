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
            animation: fadeIn 0.15s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
    </style>
</head>
<body>
    <!-- Main Content -->
    <main class="main-content">
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
</body>
</html>