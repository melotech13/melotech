@extends('layouts.app')

@section('title', 'Analysis Results - MeloTech')

@section('content')
<div class="analysis-results-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Unified Header -->
    <div class="unified-header">
        <div class="header-main">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-search"></i>
                    Analysis Results
                </h1>
                <p class="page-subtitle">AI-powered diagnosis results for your crop photo</p>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $photoAnalysis->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-tag"></i>
                        <span>{{ ucfirst($photoAnalysis->analysis_type) }}</span>
                    </div>
                    <div class="action-status">
                        <a href="{{ route('photo-diagnosis.create') }}" class="btn btn-sm me-2" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; text-decoration: none;">
                            <i class="fas fa-plus me-2"></i>New Analysis
                        </a>
                        <a href="{{ route('photo-diagnosis.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; text-decoration: none;">
                            <i class="fas fa-arrow-left me-2"></i>Back to History
                        </a>
                    </div>
                </div>
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="results-content">
        <div class="results-grid">
            <!-- Photo Display -->
            <div class="photo-section">
                <div class="section-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-image me-2"></i>
                            Uploaded Photo
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="photo-display">
                            <img src="{{ Storage::url($photoAnalysis->photo_path) }}" 
                                 alt="Analysis Photo" 
                                 class="analysis-photo">
                            <div class="photo-badge">
                                <span class="badge badge-{{ $photoAnalysis->analysis_type === 'leaves' ? 'success' : 'info' }}">
                                    <i class="fas fa-{{ $photoAnalysis->analysis_type === 'leaves' ? 'leaf' : 'seedling' }} me-1"></i>
                                    {{ $photoAnalysis->analysis_type_label }} Analysis
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Details -->
            <div class="details-section">
                <div class="section-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Analysis Details
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-label">Analysis Type</div>
                                <div class="detail-value">{{ $photoAnalysis->analysis_type_label }}</div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Identified Type</div>
                                <div class="detail-value primary">{{ $photoAnalysis->identified_type_label }}</div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Analysis Date</div>
                                <div class="detail-value">{{ $photoAnalysis->formatted_analysis_date }}</div>
                            </div>
                            

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confidence Score -->
        <div class="confidence-section">
            <div class="section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-percentage me-2"></i>
                        Confidence Score
                    </h3>
                </div>
                <div class="card-content">
                    @if($photoAnalysis->confidence_score)
                        <div class="confidence-display">
                            <div class="confidence-bar">
                                <div class="progress">
                                    <div class="progress-bar @if($photoAnalysis->confidence_score >= 80) bg-success @elseif($photoAnalysis->confidence_score >= 60) bg-warning @else bg-danger @endif"
                                        role="progressbar" 
                                        style="width: {{ $photoAnalysis->confidence_score }}%" 
                                        aria-valuenow="{{ $photoAnalysis->confidence_score }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        {{ $photoAnalysis->confidence_score }}%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="confidence-info">
                                <div class="confidence-level">
                                    @if($photoAnalysis->confidence_score >= 80)
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span class="text-success">High Confidence</span>
                                    @elseif($photoAnalysis->confidence_score >= 60)
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        <span class="text-warning">Medium Confidence</span>
                                    @else
                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                        <span class="text-danger">Low Confidence</span>
                                    @endif
                                </div>
                                
                                <div class="confidence-description">
                                    @if($photoAnalysis->confidence_score >= 80)
                                        Very reliable result - High accuracy in diagnosis
                                    @elseif($photoAnalysis->confidence_score >= 60)
                                        Good result - Consider retaking photo for better accuracy
                                    @else
                                        Low accuracy - Recommend retaking photo with better lighting
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="no-confidence">
                            <i class="fas fa-question-circle fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Confidence score not available for this analysis</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        @if($photoAnalysis->recommendations)
        <div class="recommendations-section">
            <div class="section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb me-2"></i>
                        AI Recommendations
                    </h3>
                </div>
                <div class="card-content">
                    <div class="recommendations-content">
                        {!! $photoAnalysis->recommendations !!}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="quick-actions-section">
            <div class="section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-content">
                    <div class="actions-grid">
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-upload"></i>
                            </div>
                            <h5>New Analysis</h5>
                            <p>Upload another photo for analysis</p>
                            <a href="{{ route('photo-diagnosis.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Start New
                            </a>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <h5>View History</h5>
                            <p>Browse all your previous analyses</p>
                            <a href="{{ route('photo-diagnosis.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Browse All
                            </a>
                        </div>
                        
                        <div class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-share"></i>
                            </div>
                            <h5>Share Results</h5>
                            <p>Share these results with others</p>
                            <button class="btn btn-outline-secondary" onclick="shareResults()">
                                <i class="fas fa-share-alt me-2"></i>Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Basic styling */
    .analysis-results-container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 1.5rem;
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
        align-items: center;
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

    .action-buttons .btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .action-buttons .btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }

    /* Results Content */
    .results-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .results-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        width: 100%;
        height: 100%;
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

    /* Photo Section */
    .photo-display {
        text-align: center;
        position: relative;
    }

    .analysis-photo {
        max-width: 100%;
        max-height: 400px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        margin-bottom: 1rem;
    }

    .photo-badge {
        margin-top: 1rem;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-success {
        background: #10b981;
        color: white;
    }

    .badge-info {
        background: #06b6d4;
        color: white;
    }

    /* Details Section */
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .detail-item {
        padding: 1rem;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .detail-value.primary {
        color: #3b82f6;
    }

    /* Confidence Section */
    .confidence-display {
        text-align: center;
    }

    .confidence-bar {
        margin-bottom: 2rem;
    }

    .progress {
        height: 2rem;
        border-radius: 1rem;
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 1rem;
        font-size: 1rem;
        font-weight: 700;
        line-height: 2rem;
        transition: all 0.3s ease;
    }

    .confidence-info {
        text-align: center;
    }

    .confidence-level {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .confidence-description {
        color: #6b7280;
        font-size: 1rem;
        line-height: 1.5;
    }

    .no-confidence {
        text-align: center;
        padding: 2rem;
        color: #9ca3af;
    }

    /* Recommendations Section */
    .recommendations-content {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        line-height: 1.6;
    }

    .recommendations-content h1,
    .recommendations-content h2,
    .recommendations-content h3,
    .recommendations-content h4,
    .recommendations-content h5,
    .recommendations-content h6 {
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .recommendations-content p {
        margin-bottom: 1rem;
        color: #374151;
    }

    .recommendations-content ul,
    .recommendations-content ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    .recommendations-content li {
        margin-bottom: 0.5rem;
        color: #374151;
    }

    /* Quick actions */
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .action-card {
        text-align: center;
        padding: 2rem 1.5rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #d1d5db;
    }

    .action-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 1.5rem;
    }

    .action-card:nth-child(2) .action-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .action-card:nth-child(3) .action-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .action-card h5 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }

    .action-card p {
        color: #6b7280;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
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
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-2px);
    }

    .btn-outline-primary {
        background: transparent;
        color: #3b82f6;
        border: 1px solid #3b82f6;
    }

    .btn-outline-primary:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 991.98px) {
        .analysis-results-container {
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
            align-self: center;
        }

        .page-title {
            font-size: 2rem;
        }

        .results-grid {
            grid-template-columns: 1fr;
        }

        .actions-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .analysis-results-container {
            padding: 1.25rem 0.75rem;
        }

        .unified-header {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .card-header, .card-content {
            padding: 1.25rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function shareResults() {
    if (navigator.share) {
        navigator.share({
            title: 'Photo Analysis Results',
            text: 'Check out my crop photo analysis results from MeloTech!',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
        }, function() {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = window.location.href;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Link copied to clipboard!');
        });
    }
}
</script>
@endpush
