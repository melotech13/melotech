@extends('layouts.app')

@section('title', 'Welcome - MeloTech')

@push('styles')
<style>
    /* Melon-inspired color palette */
    :root {
        --melon-green: #2E8B57;
        --melon-light-green: #90EE90;
        --melon-pink: #FF6B9D;
        --melon-orange: #FF8C42;
        --melon-yellow: #FFD93D;
        --melon-cream: #FFF8DC;
        --melon-dark: #1B4332;
        --melon-gray: #6C757D;
    }

    /* Custom animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Custom Icons */
    .custom-icon {
        width: 24px;
        height: 24px;
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px;
    }

    .icon-large {
        width: 48px;
        height: 48px;
    }

    .icon-xl {
        width: 64px;
        height: 64px;
    }

    .icon-2xl {
        width: 96px;
        height: 96px;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, var(--melon-green) 0%, var(--melon-light-green) 100%);
        color: white;
        padding: 6rem 0;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        animation: float 6s ease-in-out infinite;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        background: linear-gradient(45deg, #fff, var(--melon-light-green));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.95;
        line-height: 1.6;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        background: linear-gradient(45deg, var(--melon-pink), var(--melon-orange));
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        color: white;
    }

    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
        transform: translateY(-2px);
    }

    .hero-visual {
        position: relative;
        animation: float 4s ease-in-out infinite;
    }

    .watermelon-garden {
        position: relative;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .melon-icon-large {
        font-size: 8rem;
        color: var(--melon-pink);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        position: relative;
        z-index: 3;
    }

    .melon-icon-medium {
        font-size: 4rem;
        color: var(--melon-pink);
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        position: absolute;
        z-index: 2;
    }

    .melon-icon-small {
        font-size: 2.5rem;
        color: var(--melon-pink);
        filter: drop-shadow(0 1px 3px rgba(0,0,0,0.1));
        position: absolute;
        z-index: 1;
    }

    .melon-icon-tiny {
        font-size: 1.5rem;
        color: var(--melon-pink);
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
        position: absolute;
        z-index: 1;
    }

    /* Watermelon positioning and animations */
    .melon-1 {
        top: 20%;
        left: 15%;
        animation: float 3s ease-in-out infinite;
        animation-delay: 0.5s;
    }

    .melon-2 {
        top: 70%;
        right: 20%;
        animation: float 4s ease-in-out infinite;
        animation-delay: 1s;
    }

    .melon-3 {
        top: 30%;
        right: 10%;
        animation: float 3.5s ease-in-out infinite;
        animation-delay: 1.5s;
    }

    .melon-4 {
        bottom: 25%;
        left: 25%;
        animation: float 4.5s ease-in-out infinite;
        animation-delay: 2s;
    }

    .melon-5 {
        top: 15%;
        left: 5%;
        animation: float 2.5s ease-in-out infinite;
        animation-delay: 2.5s;
    }

    .melon-6 {
        bottom: 15%;
        right: 5%;
        animation: float 3s ease-in-out infinite;
        animation-delay: 3s;
    }

    .main-melon {
        animation: pulse 4s ease-in-out infinite;
    }

    /* Features Section */
    .features-section {
        padding: 5rem 0;
        background: var(--melon-cream);
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--melon-dark);
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: var(--melon-gray);
        text-align: center;
        margin-bottom: 3rem;
    }

    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, var(--melon-green), var(--melon-light-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1);
        background: linear-gradient(45deg, var(--melon-pink), var(--melon-orange));
    }

    .feature-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--melon-dark);
        margin-bottom: 1rem;
    }

    .feature-description {
        color: var(--melon-gray);
        line-height: 1.6;
    }

    /* How It Works Section */
    .how-it-works {
        padding: 5rem 0;
        background: white;
    }

    .step-card {
        text-align: center;
        padding: 2rem;
        position: relative;
    }

    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, var(--melon-pink), var(--melon-orange));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
    }

    .step-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--melon-dark);
        margin-bottom: 1rem;
    }

    .step-description {
        color: var(--melon-gray);
        line-height: 1.6;
    }

    /* AI Technologies Section */
    .ai-technologies {
        padding: 5rem 0;
        background: linear-gradient(135deg, var(--melon-dark) 0%, #2c5f4a 100%);
        color: white;
    }

    .ai-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        height: 100%;
        transition: all 0.3s ease;
    }

    .ai-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .ai-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(45deg, var(--melon-pink), var(--melon-orange));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.75rem;
        color: white;
    }

    .ai-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--melon-light-green);
    }

    .ai-description {
        opacity: 0.9;
        line-height: 1.6;
    }

    /* CTA Section */
    .cta-section {
        padding: 5rem 0;
        background: linear-gradient(135deg, var(--melon-green) 0%, var(--melon-light-green) 100%);
        color: white;
        text-align: center;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .cta-description {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.95;
    }

    .btn-cta {
        background: linear-gradient(45deg, var(--melon-pink), var(--melon-orange));
        border: none;
        color: white;
        padding: 1.25rem 3rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        display: inline-block;
    }

    .btn-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        color: white;
    }

    .cta-note {
        margin-top: 1rem;
        opacity: 0.8;
        font-size: 0.9rem;
    }

    /* Free Badge */
    .free-badge {
        background: linear-gradient(45deg, var(--melon-yellow), var(--melon-orange));
        color: var(--melon-dark);
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(255, 217, 61, 0.3);
    }

    .badge-watermelon {
        font-size: 1.2rem;
        margin: 0 0.5rem;
        animation: bounce 2s ease-in-out infinite;
    }

    .badge-watermelon.left {
        animation-delay: 0s;
    }

    .badge-watermelon.right {
        animation-delay: 1s;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-hero-primary,
        .btn-hero-secondary {
            width: 100%;
            max-width: 300px;
            text-align: center;
        }
        
        .melon-icon-large {
            font-size: 5rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .feature-card,
        .ai-card {
            margin-bottom: 2rem;
        }
    }

    /* Animation classes */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .animate-on-scroll.animated {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <div class="free-badge">
                    <span class="badge-watermelon left">🍉</span>
                    <svg class="custom-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    100% FREE FOR FARMERS
                    <span class="badge-watermelon right">🍉</span>
                </div>
                <h1 class="hero-title">
                    Smart Watermelon Farming
                </h1>
                <p class="hero-subtitle">
                    Revolutionize your watermelon farming with AI-powered analysis, 
                    photo-based crop monitoring, and data-driven insights for maximum yield and sustainable practices. 
                    <strong>Completely free - no hidden costs, no subscriptions, no fees.</strong>
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn-hero-primary">
                        <svg class="custom-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Start Free Today
                    </a>
                    <a href="#features" class="btn-hero-secondary">
                        <svg class="custom-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Learn More
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center hero-visual">
                <div class="watermelon-garden">
                    <div class="melon-icon-large main-melon">
                        🍉
                    </div>
                    <div class="melon-icon-medium melon-1">
                        🍉
                    </div>
                    <div class="melon-icon-small melon-2">
                        🍉
                    </div>
                    <div class="melon-icon-medium melon-3">
                        🍉
                    </div>
                    <div class="melon-icon-small melon-4">
                        🍉
                    </div>
                    <div class="melon-icon-tiny melon-5">
                        🍉
                    </div>
                    <div class="melon-icon-tiny melon-6">
                        🍉
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">
                    <svg class="custom-icon icon-large" fill="none" stroke="var(--melon-green)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Why Choose MeloTech?
                </h2>
                <p class="section-subtitle">Advanced AI technology meets traditional farming wisdom - <strong>100% free for farmers</strong></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">AI-Powered Analysis</h4>
                    <p class="feature-description">Advanced machine learning algorithms analyze your crop data and photos to provide intelligent insights and predictions for optimal growth.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">Photo-Based Monitoring</h4>
                    <p class="feature-description">Upload photos of your crops and get instant analysis of plant health, nutrient deficiencies, and growth progress with visual feedback.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">Smart Predictions</h4>
                    <p class="feature-description">AI algorithms predict harvest dates, nutrient needs, and optimal growing conditions based on your data and weather patterns.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">Mobile Access</h4>
                    <p class="feature-description">Monitor your farm from anywhere with our responsive mobile application. Upload photos and view insights on the go.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">Weather Integration</h4>
                    <p class="feature-description">Local weather data integration helps adjust predictions and recommendations based on current and forecasted conditions.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">Sustainable Insights</h4>
                    <p class="feature-description">Get recommendations for sustainable farming practices that optimize water usage and reduce environmental impact.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">
                    <svg class="custom-icon icon-large" fill="none" stroke="var(--melon-green)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    How It Works
                </h2>
                <p class="section-subtitle">Get started in three simple steps - <strong>completely free</strong></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 text-center mb-4">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h4 class="step-title">Register Your Farm</h4>
                    <p class="step-description">Create your account and provide basic farm information including watermelon variety for personalized insights.</p>
                </div>
            </div>
            
            <div class="col-lg-4 text-center mb-4">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h4 class="step-title">Upload Crop Photos</h4>
                    <p class="step-description">Take photos of your watermelon plants and upload them to our AI-powered analysis system for instant health assessment.</p>
                </div>
            </div>
            
            <div class="col-lg-4 text-center mb-4">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h4 class="step-title">Get AI Insights</h4>
                    <p class="step-description">Receive intelligent analysis, growth predictions, and actionable recommendations to improve your yields and farm efficiency.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AI Technologies Section -->
<section class="ai-technologies">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="section-title" style="color: white;">
                    <svg class="custom-icon icon-large" fill="none" stroke="var(--melon-light-green)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Three Core AI Technologies
                </h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Powering the future of smart farming - <strong>free for all farmers</strong></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="ai-card animate-on-scroll">
                    <div class="ai-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="ai-title">Machine Learning (ML) for Predictions</h4>
                    <p class="ai-description">Farmers input data such as planting date, leaf color, and fruit size. The ML model analyzes this data to predict nutrient needs and the ideal harvest time with high accuracy.</p>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="ai-card animate-on-scroll">
                    <div class="ai-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h4 class="ai-title">Image Recognition AI</h4>
                    <p class="ai-description">Farmers upload photos of their crops, and the AI compares them against a database of plant images to identify nutrient deficiencies and growth issues instantly.</p>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="ai-card animate-on-scroll">
                    <div class="ai-icon">
                        <svg class="icon-xl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <h4 class="ai-title">Weather-Integrated AI</h4>
                    <p class="ai-description">The system monitors local weather forecasts and adjusts harvest predictions accordingly, helping farmers plan for heat, rain, or other conditions that affect crop growth.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="free-badge">
                    <svg class="custom-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    NO COST • NO FEES • NO SUBSCRIPTIONS
                </div>
                <h2 class="cta-title">
                    <svg class="custom-icon icon-large" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Ready to Transform Your Farming?
                </h2>
                <p class="cta-description">
                    Join thousands of farmers who are already using MeloTech's AI-powered insights to increase their yields, reduce costs, and implement sustainable farming practices. 
                    <strong>Start today - it's completely free!</strong>
                </p>
                <a href="{{ route('register') }}" class="btn-cta">
                    <svg class="custom-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Start Free Today
                </a>
                <p class="cta-note">100% Free for Farmers • No Credit Card Required • No Hidden Costs • No Trial Period</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);

    // Observe all elements with animate-on-scroll class
    document.addEventListener('DOMContentLoaded', () => {
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        animatedElements.forEach(el => observer.observe(el));
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush
