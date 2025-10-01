@extends('layouts.app')

@section('title', 'Photo Diagnosis - MeloTech')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/photo-diagnosis.css') }}">
@endpush

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

    <!-- Analysis Management Section -->
    <div class="analyses-section">
        <div class="section-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history me-2"></i>
                    Analysis Management
                </h3>
            </div>
            <div class="card-content">
                <!-- Actions: create new analysis -->
                <div class="action-bar animate-fade-in-up" style="animation-delay: .2s;">
                    <div class="action-bar-right d-flex align-items-center gap-2">
                        <a href="{{ route('photo-diagnosis.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            New Analysis
                        </a>
                    </div>
                </div>

                @if($analyses->count() > 0)
                    <div class="analyses-grid">
                        @foreach($analyses as $analysis)
                            @php
                                $safeRecommendations = '';
                                if ($analysis->recommendations) {
                                    if (is_array($analysis->recommendations)) {
                                        // Handle new structured recommendations format
                                        if (isset($analysis->recommendations['recommendations']) && is_array($analysis->recommendations['recommendations'])) {
                                            $safeRecommendations = strtolower(implode(' ', $analysis->recommendations['recommendations']));
                                        } elseif (isset($analysis->recommendations['overall'])) {
                                            $safeRecommendations = strtolower($analysis->recommendations['overall']);
                                        } else {
                                            // Fallback: try to extract text from the array
                                            $textParts = [];
                                            foreach ($analysis->recommendations as $key => $value) {
                                                if (is_string($value)) {
                                                    $textParts[] = $value;
                                                } elseif (is_array($value) && isset($value['action'])) {
                                                    $textParts[] = $value['action'];
                                                }
                                            }
                                            $safeRecommendations = strtolower(implode(' ', $textParts));
                                        }
                                    } else {
                                        $safeRecommendations = strtolower($analysis->recommendations);
                                    }
                                }
                            @endphp
                            <div class="analysis-card" 
                                 data-type="{{ strtolower($analysis->analysis_type ?? '') }}" 
                                 data-condition="{{ strtolower($analysis->identified_condition ?? '') }}" 
                                 data-confidence="{{ $analysis->confidence_score ?? 0 }}"
                                 data-date="{{ $analysis->created_at->format('Y-m-d') }}"
                                 data-identified-type="{{ strtolower($analysis->identified_type ?? '') }}"
                                 data-recommendations="{{ $safeRecommendations }}"
                                 data-created="{{ $analysis->created_at->format('Y-m-d') }}">
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
                                        <div class="analysis-actions">
                                            <a href="{{ route('photo-diagnosis.show', $analysis->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <form method="POST" action="{{ route('photo-diagnosis.destroy', $analysis) }}" class="m-0 p-0 d-inline delete-analysis-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Analysis">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    
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


@push('scripts')
<script src="{{ asset('js/photo-diagnosis.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
        const width = bar.getAttribute('data-width');
        setTimeout(() => {
            bar.style.width = width + '%';
        }, 100);
    });

    // Simple, reliable deletion with native confirm + normal form submit
    document.querySelectorAll('.delete-analysis-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const card = this.closest('.analysis-card');
            const date = card?.querySelector('.analysis-date')?.textContent?.trim() || 'this analysis';
            const ok = window.confirm(`Delete ${date}? This cannot be undone.`);
            if (!ok) return;
            // Show lightweight button spinner while submitting
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';
            }
            this.submit();
        });
    });

    // Initialize any tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@include('components.success-modal')
@endsection

<!-- (Replaced by admin-style injected modal in scripts) -->

