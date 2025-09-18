@extends('layouts.app')

@section('title', 'Photo Diagnosis - MeloTech')

@section('content')
<div class="photo-diagnosis-container">

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Unified Header -->
    <div class="unified-header">
        <div class="header-main">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-camera"></i>
                    Photo Diagnosis
                </h1>
                <p class="page-subtitle">AI-powered analysis of your crop photos for disease detection and health assessment</p>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-chart-bar"></i>
                        <span>{{ $analyses->count() }} Total Analyses</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-leaf"></i>
                        <span>{{ $analyses->where('analysis_type', 'leaves')->count() }} Leaves</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-apple-alt"></i>
                        <span>{{ $analyses->where('analysis_type', 'watermelon')->count() }} Fruits</span>
                    </div>

                </div>
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-microscope"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Analyzing Section -->
    <div class="start-analyzing-section">
        <div class="section-card start-analyzing-card">
            <div class="start-analyzing-content">
                <div class="start-analyzing-left">
                    <div class="start-analyzing-icon">
                        <i class="fas fa-camera-retro"></i>
                    </div>
                    <div class="start-analyzing-text">
                        <h2 class="start-analyzing-title">Ready to Analyze Your Crops?</h2>
                        <p class="start-analyzing-description">
                            Upload a photo of your watermelon leaves or fruit and get instant AI-powered analysis. 
                            Our advanced system can detect diseases, assess health conditions, and provide actionable insights.
                        </p>
                        <div class="start-analyzing-features">
                            <div class="feature-item">
                                <i class="fas fa-bolt"></i>
                                <span>Instant Analysis</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-microscope"></i>
                                <span>AI-Powered Detection</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Accurate Results</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="start-analyzing-right">
                    <div class="start-analyzing-cta">
                        <a href="{{ route('photo-diagnosis.create') }}" class="btn-start-analyzing">
                            <div class="btn-content">
                                <i class="fas fa-play-circle"></i>
                                <span class="btn-text">Start Analyzing</span>
                            </div>
                            <div class="btn-subtext">Upload & Analyze Your Photos</div>
                        </a>
                        <div class="quick-info">
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>Results in seconds</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-images"></i>
                                <span>Support for multiple formats</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $analyses->count() }}</div>
                    <div class="stat-label">Total Analyses</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $analyses->where('analysis_type', 'leaves')->count() }}</div>
                    <div class="stat-label">Leaves Analyzed</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $analyses->where('analysis_type', 'watermelon')->count() }}</div>
                    <div class="stat-label">Watermelons Analyzed</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        @if($analyses->count() > 0)
                            {{ number_format($analyses->avg('confidence_score'), 1) }}%
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="stat-label">Average Confidence</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Analyses -->
    <div class="analyses-section">
        <div class="section-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history me-2"></i>
                    Recent Analyses
                </h3>
            </div>
            <div class="card-content">
                @if($analyses->count() > 0)
                    <div class="analyses-grid">
                        @foreach($analyses->take(6) as $analysis)
                            <div class="analysis-card">
                                <div class="analysis-image">
                                    @if($analysis->photo_url)
                                        <img src="{{ $analysis->photo_url }}" 
                                             alt="Analysis Photo" 
                                             class="img-fluid"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="no-image-placeholder" style="display: none;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">Photo not available</p>
                                        </div>
                                    @else
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="text-muted mt-2">Photo not available</p>
                                        </div>
                                    @endif
                                    <div class="analysis-type-badge">
                                        <i class="fas fa-{{ $analysis->analysis_type === 'leaves' ? 'leaf' : 'seedling' }}"></i>
                                        {{ ucfirst($analysis->analysis_type) }}
                                    </div>
                                </div>
                                <div class="analysis-details">
                                    <h6 class="analysis-title">{{ $analysis->identified_type_label }}</h6>
                                    <div class="confidence-score">
                                        <div class="progress">
                                            <div class="progress-bar 
                                                @if($analysis->confidence_score >= 80) bg-success
                                                @elseif($analysis->confidence_score >= 60) bg-warning
                                                @else bg-danger
                                                @endif" 
                                                data-width="{{ $analysis->confidence_score }}"
                                                style="width: 0%;">
                                                {{ $analysis->confidence_score }}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="analysis-meta">
                                        <span class="analysis-date">{{ $analysis->created_at->format('M d, Y') }}</span>
                                        <a href="{{ route('photo-diagnosis.show', $analysis->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($analyses->count() > 6)
                        <div class="view-all-section">
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>View All Analyses
                            </a>
                        </div>
                    @endif
                @else
                    <div class="no-data-content">
                        <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Analyses Yet</h5>
                        <p class="text-muted mb-3">Start by uploading your first photo for AI-powered diagnosis</p>
                        <a href="{{ route('photo-diagnosis.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-upload me-2"></i>Upload First Photo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>


</div>

@push('styles')
<style>
    /* Basic styling */
    .photo-diagnosis-container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 1;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    /* Unified Header */
    .unified-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 25%, #1e40af 50%, #1e3a8a 75%, #1e3a8a 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .unified-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .header-main {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }

    .header-left {
        flex: 1;
    }

    .header-right {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        align-items: flex-end;
        min-width: 300px;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0 0 0.75rem 0;
        text-shadow: 0 3px 10px rgba(0,0,0,0.3);
        color: white !important;
    }

    .page-subtitle {
        font-size: 1.1rem;
        margin: 0;
        opacity: 0.95;
        font-weight: 400;
        color: white !important;
    }

    .stats-overview {
        display: flex;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        min-width: 280px;
    }

    .stat-item {
        text-align: center;
        flex: 1;
    }

    .stat-item .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.25rem;
    }

    .stat-item .stat-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }

    .action-status .btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .action-status .btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Start Analyzing Section */
    .start-analyzing-section {
        margin-bottom: 2rem;
    }

    .start-analyzing-card {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 2px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        margin-bottom: 0;
    }

    .start-analyzing-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        border-color: #d1d5db;
    }

    .start-analyzing-content {
        display: flex;
        align-items: center;
        gap: 3rem;
        padding: 2.5rem;
    }

    .start-analyzing-left {
        flex: 1;
        display: flex;
        align-items: flex-start;
        gap: 2rem;
    }

    .start-analyzing-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        flex-shrink: 0;
    }

    .start-analyzing-text {
        flex: 1;
    }

    .start-analyzing-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 1rem 0;
        line-height: 1.2;
    }

    .start-analyzing-description {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
        margin: 0 0 1.5rem 0;
    }

    .start-analyzing-features {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #374151;
        font-weight: 500;
    }

    .feature-item i {
        color: #3b82f6;
        font-size: 1rem;
    }

    .start-analyzing-right {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        flex-shrink: 0;
        min-width: 280px;
    }

    .start-analyzing-cta {
        text-align: center;
    }

    .btn-start-analyzing {
        display: inline-block;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #1e40af 100%);
        color: white;
        text-decoration: none;
        border-radius: 16px;
        padding: 1.5rem 2.5rem;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transform: translateZ(0);
        margin-bottom: 1rem;
    }

    .btn-start-analyzing::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 50%, #1e3a8a 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .btn-start-analyzing:hover::before {
        opacity: 1;
    }

    .btn-start-analyzing:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.5);
        color: white;
        text-decoration: none;
    }

    .btn-start-analyzing:active {
        transform: translateY(-1px) scale(1.01);
    }

    .btn-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .btn-content i {
        font-size: 1.5rem;
    }

    .btn-text {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: 0.025em;
    }

    .btn-subtext {
        position: relative;
        z-index: 1;
        font-size: 0.875rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .quick-info {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        align-items: center;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .info-item i {
        color: #10b981;
        font-size: 1rem;
    }

    /* Statistics Section */
    .stats-section {
        margin-bottom: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-content .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .stat-content .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        width: 100%;
        margin-bottom: 2rem;
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Analyses Grid */
    .analyses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .analysis-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .analysis-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #d1d5db;
    }

    .analysis-image {
        position: relative;
        height: 200px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
    }

    .analysis-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        margin: 0 auto;
    }

    .no-image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        color: #6c757d;
        padding: 1rem;
    }

    .no-image-placeholder i {
        margin-bottom: 0.5rem;
    }

    .no-image-placeholder p {
        margin: 0;
        font-size: 0.875rem;
    }

    .analysis-type-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

    .analysis-details {
        padding: 1.5rem;
    }

    .analysis-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .confidence-score {
        margin-bottom: 1rem;
    }

    .progress {
        height: 8px;
        border-radius: 4px;
        background-color: #f3f4f6;
    }

    .progress-bar {
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .analysis-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .analysis-date {
        font-size: 0.875rem;
        color: #6b7280;
    }



    /* No Data Content */
    .no-data-content {
        text-align: center;
        padding: 3rem 2rem;
        color: #9ca3af;
    }

    .no-data-content i {
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .no-data-content h5 {
        color: #6b7280;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .no-data-content p {
        color: #9ca3af;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    /* View All Section */
    .view-all-section {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
        margin-top: 1.5rem;
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
        border: none;
    }

    .btn-outline-primary {
        background: transparent;
        color: #3b82f6;
        border: 1px solid #3b82f6;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    /* Responsive Design */
    @media (max-width: 991.98px) {
        .photo-diagnosis-container {
            max-width: 100%;
            padding: 1.5rem 1rem;
        }

        .unified-header {
            padding: 2rem 1.5rem;
        }

        .header-main {
            flex-direction: column;
            gap: 1.5rem;
        }

        .header-right {
            align-items: center;
            min-width: auto;
            width: 100%;
        }

        .stats-overview {
            min-width: auto;
            width: 100%;
        }

        .page-title {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .analyses-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .start-analyzing-content {
            flex-direction: column;
            gap: 2rem;
            padding: 2rem 1.5rem;
        }

        .start-analyzing-left {
            gap: 1.5rem;
            text-align: center;
        }

        .start-analyzing-right {
            min-width: auto;
            width: 100%;
        }

        .start-analyzing-features {
            justify-content: center;
        }
    }

    @media (max-width: 767.98px) {
        .photo-diagnosis-container {
            padding: 1.25rem 0.75rem;
        }

        .unified-header {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .stats-overview {
            flex-direction: column;
            gap: 0.75rem;
        }

        .stat-card {
            padding: 1.5rem;
        }

        .card-header, .card-content {
            padding: 1.25rem;
        }

        .start-analyzing-content {
            padding: 1.5rem 1rem;
        }

        .start-analyzing-left {
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .start-analyzing-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }

        .start-analyzing-title {
            font-size: 1.5rem;
        }

        .start-analyzing-features {
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .btn-start-analyzing {
            padding: 1.25rem 2rem;
            width: 100%;
            max-width: 280px;
        }

        .btn-text {
            font-size: 1.125rem;
        }
    }
</style>
@endpush

@section('scripts')
<script>
    // Initialize DataTable for better table handling
    $(document).ready(function() {
        if ($('#dataTable').length && $('#dataTable tbody tr').length > 0) {
            $('#dataTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthChange": false,
                "info": false,
                "order": [[ 4, "desc" ]], // Sort by date descending
                "columnDefs": [
                    { "orderable": false, "targets": [0, 5] } // Disable sorting for photo and actions columns
                ]
            });
        }

        // Animate progress bars with smooth transition
        $('.progress-bar[data-width]').each(function() {
            const $this = $(this);
            const width = $this.data('width');
            
            // Add transition for smooth animation
            $this.css('transition', 'width 0.8s ease-in-out');
            
            // Animate to the target width
            setTimeout(() => {
                $this.css('width', width + '%');
            }, 100);
        });
    });
</script>
@endsection
