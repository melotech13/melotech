@extends('layouts.app')

@section('title', 'Analysis Results - MeloTech')

@section('content')
<div class="analysis-results-container">

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
                            @if($photoAnalysis->photo_path && Storage::disk('public')->exists($photoAnalysis->photo_path))
                                <img src="{{ Storage::url($photoAnalysis->photo_path) }}" 
                                     alt="Analysis Photo" 
                                     class="analysis-photo">
                            @else
                                <div class="no-image-placeholder">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Photo not available</p>
                                </div>
                            @endif
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
                            
                            <div class="detail-item confidence-detail">
                                <div class="detail-label">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Confidence Score
                                </div>
                                <div class="detail-value confidence-value">
                                    @if($photoAnalysis->confidence_score)
                                        <div class="confidence-inline">
                                            <div class="score-container">
                                                <span class="score-number-inline">{{ $photoAnalysis->confidence_score }}%</span>
                                                <div class="score-subtitle">Accuracy Level</div>
                                            </div>
                                            <div class="confidence-level-inline">
                                                @if($photoAnalysis->confidence_score >= 80)
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    <span>High Confidence</span>
                                                @elseif($photoAnalysis->confidence_score >= 60)
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    <span>Medium Confidence</span>
                                                @else
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    <span>Low Confidence</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="confidence-progress-inline">
                                            <div class="progress-label">
                                                <span>Analysis Reliability</span>
                                                <span class="progress-percentage">{{ $photoAnalysis->confidence_score }}%</span>
                                            </div>
                                            <div class="progress-inline">
                                                <div class="progress-bar-inline @if($photoAnalysis->confidence_score >= 80) bg-success @elseif($photoAnalysis->confidence_score >= 60) bg-warning @else bg-danger @endif"
                                                    style="--progress-width: {{ $photoAnalysis->confidence_score }}%; width: var(--progress-width);" data-width="{{ $photoAnalysis->confidence_score }}">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Not available</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        @if($photoAnalysis->recommendations && !is_null($photoAnalysis->recommendations))
            @if(is_array($photoAnalysis->recommendations) && isset($photoAnalysis->recommendations['condition_label']))
                <!-- New Structured Recommendations Format -->
                <div class="recommendations-section">
                    <div class="section-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-lightbulb me-2"></i>
                                AI Recommendations
                            </h3>
                            <p class="card-subtitle">Specific, actionable advice based on detected {{ $photoAnalysis->recommendations['condition_label'] }}</p>
                        </div>
                        <div class="card-content">
                            <div class="recommendations-content">
                                <!-- Condition Summary -->
                                <div class="condition-summary">
                                    <div class="condition-header">
                                        <div class="condition-icon">
                                            @if($photoAnalysis->recommendations['urgency_level'] === 'high')
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            @elseif($photoAnalysis->recommendations['urgency_level'] === 'medium')
                                                <i class="fas fa-exclamation-circle text-warning"></i>
                                            @else
                                                <i class="fas fa-check-circle text-success"></i>
                                            @endif
                                        </div>
                                        <div class="condition-info">
                                            <h4>{{ $photoAnalysis->recommendations['condition_label'] }}</h4>
                                            <div class="condition-meta">
                                                <span class="urgency-badge urgency-{{ $photoAnalysis->recommendations['urgency_level'] }}">
                                                    @if($photoAnalysis->recommendations['urgency_level'] === 'high')
                                                        <i class="fas fa-exclamation-triangle me-1"></i>High Priority
                                                    @elseif($photoAnalysis->recommendations['urgency_level'] === 'medium')
                                                        <i class="fas fa-exclamation-circle me-1"></i>Medium Priority
                                                    @else
                                                        <i class="fas fa-check-circle me-1"></i>Low Priority
                                                    @endif
                                                </span>
                                                <span class="treatment-badge treatment-{{ $photoAnalysis->recommendations['treatment_category'] }}">
                                                    {{ ucfirst(str_replace('_', ' ', $photoAnalysis->recommendations['treatment_category'])) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Specific Recommendations -->
                                <div class="recommendations-grid">
                                    @foreach($photoAnalysis->recommendations['recommendations'] as $index => $recommendation)
                                    <div class="recommendation-card recommendation-{{ $index + 1 }}">
                                        <div class="recommendation-icon">
                                            @if($index === 0)
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            @elseif($index === 1)
                                                <i class="fas fa-medkit text-primary"></i>
                                            @elseif($index === 2)
                                                <i class="fas fa-leaf text-success"></i>
                                            @elseif($index === 3)
                                                <i class="fas fa-tint text-info"></i>
                                            @elseif($index === 4)
                                                <i class="fas fa-shield-alt text-warning"></i>
                                            @else
                                                <i class="fas fa-lightbulb text-secondary"></i>
                                            @endif
                                        </div>
                                        <div class="recommendation-content">
                                            <div class="recommendation-number">{{ $index + 1 }}</div>
                                            <div class="recommendation-text">
                                                {!! nl2br(e($recommendation)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <!-- Action Plan Footer -->
                                <div class="recommendations-footer">
                                    <div class="action-plan">
                                        <h5><i class="fas fa-clipboard-list me-2"></i>Recommended Action Plan</h5>
                                        <div class="action-steps">
                                            @if($photoAnalysis->recommendations['urgency_level'] === 'high')
                                                <div class="action-step urgent">
                                                    <i class="fas fa-clock me-2"></i>
                                                    <span><strong>Immediate Action Required:</strong> Address this issue within 24-48 hours to prevent further damage</span>
                                                </div>
                                            @elseif($photoAnalysis->recommendations['urgency_level'] === 'medium')
                                                <div class="action-step moderate">
                                                    <i class="fas fa-calendar-week me-2"></i>
                                                    <span><strong>Action Timeline:</strong> Address this issue within 3-7 days for optimal results</span>
                                                </div>
                                            @else
                                                <div class="action-step routine">
                                                    <i class="fas fa-calendar me-2"></i>
                                                    <span><strong>Routine Care:</strong> Continue monitoring and maintain current care practices</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="recommendation-tip">
                                        <i class="fas fa-info-circle"></i>
                                        <span>These specific recommendations are based on the actual condition detected in your photo. Always consider your local growing conditions and consult local experts if needed.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Legacy HTML Recommendations Format (Fallback) -->
                <div class="recommendations-section">
                    <div class="section-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-lightbulb me-2"></i>
                                Enhanced AI Recommendations
                            </h3>
                            <p class="card-subtitle">Specific, actionable advice for your {{ $photoAnalysis->analysis_type_label }} analysis with {{ $photoAnalysis->confidence_score ?? 'high' }}% confidence</p>
                        </div>
                        <div class="card-content">
                            <div class="recommendations-content">
                                <div class="recommendations-header">
                                    <div class="recommendations-icon">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <div class="recommendations-info">
                                        <h4>AI-Powered Recommendations</h4>
                                        <p>Expert advice for {{ $photoAnalysis->analysis_type_label }} analysis with {{ $photoAnalysis->confidence_score ?? 'high' }}% confidence</p>
                                    </div>
                                </div>
                                
                                <div class="recommendations-grid">
                                    @if($photoAnalysis->analysis_type === 'leaves')
                                        <!-- Enhanced Leaf Analysis Recommendations -->
                                        <div class="recommendation-card recommendation-1">
                                            <div class="recommendation-icon">
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-number">1</div>
                                                <div class="recommendation-text">
                                                    🚨 <strong>Immediate Assessment Required:</strong> Based on your leaf analysis, conduct a thorough inspection to identify specific issues. Check for spots, yellowing, wilting, or pest damage.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="recommendation-card recommendation-2">
                                            <div class="recommendation-icon">
                                                <i class="fas fa-medkit text-primary"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-number">2</div>
                                                <div class="recommendation-text">
                                                    🧪 <strong>Specific Treatment Plan:</strong> Apply targeted treatments based on symptoms: copper fungicide for spots, nitrogen fertilizer for yellowing, deep watering for wilting, or insecticidal soap for pests.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="recommendation-card recommendation-3">
                                            <div class="recommendation-icon">
                                                <i class="fas fa-leaf text-success"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-number">3</div>
                                                <div class="recommendation-text">
                                                    🌱 <strong>Preventive Care:</strong> Maintain 1-1.5 inches of water per week, apply balanced fertilizer (10-10-10) every 4-6 weeks, ensure 6-8 hours of sunlight, and space plants 2-3 feet apart.
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Enhanced Watermelon Analysis Recommendations -->
                                        <div class="recommendation-card recommendation-1">
                                            <div class="recommendation-icon">
                                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-number">1</div>
                                                <div class="recommendation-text">
                                                    🍉 <strong>Quality Assessment:</strong> Based on your watermelon analysis, check for ripeness indicators: hollow sound when tapped, creamy yellow ground spot, and brown tendrils near the fruit.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="recommendation-card recommendation-2">
                                            <div class="recommendation-icon">
                                                <i class="fas fa-medkit text-primary"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-number">2</div>
                                                <div class="recommendation-text">
                                                    💧 <strong>Optimal Care:</strong> Maintain 1.5-2 inches of water per week, apply low-nitrogen fertilizer (5-10-10) every 3 weeks, and protect fruit from ground contact with straw or cardboard.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="recommendation-card recommendation-3">
                                            <div class="recommendation-icon">
                                                <i class="fas fa-leaf text-success"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-number">3</div>
                                                <div class="recommendation-text">
                                                    📊 <strong>Growth Monitoring:</strong> Measure fruit diameter weekly - healthy watermelon should grow 1-2 inches per week during peak development. Monitor for pests and diseases regularly.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="recommendations-footer">
                                    <div class="recommendation-tip">
                                        <i class="fas fa-info-circle"></i>
                                        <span>These enhanced recommendations provide specific, actionable advice for your {{ $photoAnalysis->analysis_type_label }}. For even more detailed, condition-specific recommendations, consider uploading a new photo for analysis.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- No Recommendations Available -->
            <div class="recommendations-section">
                <div class="section-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>
                            No Recommendations Available
                        </h3>
                        <p class="card-subtitle">This analysis was completed before the enhanced recommendation system was implemented</p>
                    </div>
                    <div class="card-content">
                        <div class="no-recommendations-content">
                            <div class="no-recommendations-message">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <h4>Recommendations Not Available</h4>
                                <p>This photo analysis was completed before the enhanced AI recommendation system was implemented. To get specific, actionable recommendations, please upload a new photo for analysis.</p>
                                <div class="no-recommendations-actions">
                                    <a href="{{ route('photo-diagnosis.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>New Analysis
                                    </a>
                                </div>
                            </div>
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

    .card-subtitle {
        font-size: 0.9rem;
        color: #6b7280;
        margin-top: 0.5rem;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Photo Section */
    .photo-display {
        text-align: center;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 600px;
        padding: 4rem 0;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        margin: 1rem 0;
    }

    .analysis-photo {
        max-width: 100%;
        max-height: 700px;
        width: auto;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        margin-bottom: 1rem;
        object-fit: contain;
        display: block;
        margin-left: auto;
        margin-right: auto;
        transition: transform 0.3s ease;
    }

    .analysis-photo:hover {
        transform: scale(1.02);
        box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    }

    /* Responsive photo display */
    @media (max-width: 768px) {
        .photo-display {
            min-height: 450px;
            padding: 3rem 0;
        }
        
        .analysis-photo {
            max-height: 500px;
        }
    }

    @media (max-width: 576px) {
        .photo-display {
            min-height: 350px;
            padding: 2rem 0;
        }
        
        .analysis-photo {
            max-height: 400px;
        }
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

    /* Confidence Score in Analysis Details */
    .confidence-detail {
        grid-column: 1 / -1;
    }

    .confidence-value {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
    }

    .confidence-inline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }

    .score-number-inline {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e293b;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        min-width: 120px;
    }

    .confidence-level-inline {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        border: none;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .confidence-progress-inline {
        width: 100%;
        margin-top: 0.5rem;
    }

    .progress-inline {
        height: 12px;
        background-color: #e2e8f0;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .progress-bar-inline {
        height: 100%;
        border-radius: 6px;
        transition: width 0.6s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }

    /* Enhanced detail items for better spacing */
    .details-grid {
        gap: 1.5rem;
    }

    .detail-item {
        padding: 1rem;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .detail-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #e2e8f0;
    }

    .detail-item.confidence-detail:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    /* Additional confidence score elements */
    .score-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .score-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }

    .progress-percentage {
        font-weight: 600;
        color: #1e293b;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .confidence-inline {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .score-number-inline {
            font-size: 2rem;
        }
        
        .confidence-detail {
            padding: 1rem;
        }
    }

    /* Enhanced Confidence Section */
    .confidence-display {
        text-align: center;
        max-width: 300px;
        margin: 0 auto;
        padding: 1rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .confidence-score-large {
        margin-bottom: 1rem;
    }

    .score-number {
        font-size: 3rem;
        font-weight: 800;
        color: #1e293b;
        display: block;
        line-height: 1;
        margin-bottom: 0.25rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .confidence-level {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        max-width: fit-content;
        margin-left: auto;
        margin-right: auto;
    }

    .confidence-bar {
        margin-bottom: 1rem;
    }

    .progress {
        height: 6px;
        border-radius: 3px;
        background-color: #e2e8f0;
        border: none;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 6px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .confidence-description {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.3;
        font-weight: 500;
        padding: 0.5rem 0.75rem;
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .no-confidence {
        text-align: center;
        padding: 2rem;
        color: #9ca3af;
    }







    /* Recommendations Section */
    .recommendations-content {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 2.5rem;
        line-height: 1.6;
        position: relative;
        overflow: hidden;
    }

    /* Condition Summary */
    .condition-summary {
        margin-bottom: 2.5rem;
    }

    .condition-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 2rem;
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .condition-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .condition-icon .text-danger {
        color: #dc2626;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        width: 100%;
        height: 100%;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .condition-icon .text-warning {
        color: #d97706;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        width: 100%;
        height: 100%;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .condition-icon .text-success {
        color: #059669;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        width: 100%;
        height: 100%;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .condition-info h4 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.75rem 0;
        line-height: 1.3;
    }

    .condition-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .urgency-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .urgency-high {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
        border: 1px solid #fca5a5;
    }

    .urgency-medium {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
        border: 1px solid #fbbf24;
    }

    .urgency-low {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669;
        border: 1px solid #6ee7b7;
    }

    .treatment-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0284c7;
        border: 1px solid #93c5fd;
    }

    /* Action Plan */
    .action-plan {
        margin-bottom: 1.5rem;
    }

    .action-plan h5 {
        color: #1f2937;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-steps {
        margin-bottom: 1rem;
    }

    .action-step {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        line-height: 1.5;
        font-weight: 500;
    }

    .action-step.urgent {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
        border: 1px solid #fca5a5;
    }

    .action-step.moderate {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
        border: 1px solid #fbbf24;
    }

    .action-step.routine {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669;
        border: 1px solid #6ee7b7;
    }

    .action-step i {
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    /* No Recommendations Styling */
    .no-recommendations-content {
        text-align: center;
        padding: 3rem 2rem;
    }

    .no-recommendations-message {
        max-width: 500px;
        margin: 0 auto;
    }

    .no-recommendations-message i {
        font-size: 3rem;
        color: #f59e0b;
        margin-bottom: 1.5rem;
        display: block;
    }

    .no-recommendations-message h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .no-recommendations-message p {
        font-size: 1rem;
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .no-recommendations-actions {
        margin-top: 2rem;
    }

    .no-recommendations-actions .btn {
        padding: 0.875rem 2rem;
        font-size: 1rem;
        font-weight: 600;
    }

    .recommendations-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #10b981, #f59e0b, #8b5cf6, #ef4444);
        opacity: 0.9;
    }

    /* Recommendations Header */
    .recommendations-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
        padding: 2rem;
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .recommendations-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        flex-shrink: 0;
    }

    .recommendations-info h4 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.75rem 0;
        line-height: 1.3;
    }

    .recommendations-info p {
        font-size: 1rem;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
        line-height: 1.5;
    }

    /* Recommendations Grid */
    .recommendations-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .recommendation-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .recommendation-card:nth-child(1) { animation-delay: 0.1s; }
    .recommendation-card:nth-child(2) { animation-delay: 0.2s; }
    .recommendation-card:nth-child(3) { animation-delay: 0.3s; }
    .recommendation-card:nth-child(4) { animation-delay: 0.4s; }
    .recommendation-card:nth-child(5) { animation-delay: 0.5s; }
    .recommendation-card:nth-child(6) { animation-delay: 0.6s; }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .recommendation-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .recommendation-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        border-color: #d1d5db;
    }

    .recommendation-card:hover::before {
        opacity: 1;
    }

    /* Individual recommendation card styling */
    .recommendation-card.recommendation-1::before {
        background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    }

    .recommendation-card.recommendation-2::before {
        background: linear-gradient(90deg, #10b981, #059669);
    }

    .recommendation-card.recommendation-3::before {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .recommendation-card.recommendation-4::before {
        background: linear-gradient(90deg, #8b5cf6, #7c3aed);
    }

    .recommendation-card.recommendation-5::before {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    .recommendation-card.recommendation-6::before {
        background: linear-gradient(90deg, #06b6d4, #0891b2);
    }

    /* Recommendation Icon */
    .recommendation-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .recommendation-1 .recommendation-icon {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .recommendation-2 .recommendation-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .recommendation-3 .recommendation-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .recommendation-4 .recommendation-icon {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .recommendation-5 .recommendation-icon {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .recommendation-6 .recommendation-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    /* Recommendation Content */
    .recommendation-content {
        flex: 1;
        min-width: 0;
    }

    .recommendation-number {
        display: inline-block;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .recommendation-1 .recommendation-number {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .recommendation-2 .recommendation-number {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #059669;
        border-color: #6ee7b7;
    }

    .recommendation-3 .recommendation-number {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #d97706;
        border-color: #fbbf24;
    }

    .recommendation-4 .recommendation-number {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #7c3aed;
        border-color: #a78bfa;
    }

    .recommendation-5 .recommendation-number {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #dc2626;
        border-color: #f87171;
    }

    .recommendation-6 .recommendation-number {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0284c7;
        border-color: #93c5fd;
    }

    .recommendation-content p {
        margin: 0;
        color: #374151;
        font-size: 1rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .recommendation-text {
        margin: 0;
        color: #374151;
        font-size: 1rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .recommendation-content strong {
        color: #1f2937;
        font-weight: 700;
    }

    /* Enhanced recommendation styling for specific actions */
    .recommendation-text {
        white-space: pre-line;
        word-wrap: break-word;
    }

    /* Emoji and icon styling */
    .recommendation-text {
        font-size: 1.05rem;
        line-height: 1.7;
    }

    /* Priority indicators */
    .recommendation-card.recommendation-1 .recommendation-text {
        border-left: 4px solid #ef4444;
        padding-left: 1rem;
    }

    .recommendation-card.recommendation-2 .recommendation-text {
        border-left: 4px solid #3b82f6;
        padding-left: 1rem;
    }

    .recommendation-card.recommendation-3 .recommendation-text {
        border-left: 4px solid #10b981;
        padding-left: 1rem;
    }

    /* Recommendations Footer */
    .recommendations-footer {
        border-top: 1px solid #e5e7eb;
        padding-top: 2rem;
        margin-top: 1rem;
    }

    .recommendation-tip {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.5rem 1.75rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 16px;
        color: #0369a1;
        font-size: 0.95rem;
        line-height: 1.6;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .recommendation-tip i {
        color: #0284c7;
        font-size: 1.1rem;
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    .recommendation-uniqueness {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.5rem 1.75rem;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fbbf24;
        border-radius: 16px;
        color: #92400e;
        font-size: 0.95rem;
        line-height: 1.6;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(251, 191, 36, 0.1);
        margin-top: 1rem;
    }

    .recommendation-uniqueness i {
        color: #d97706;
        font-size: 1.1rem;
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    .uniqueness-details {
        margin-top: 1.5rem;
        padding: 1.5rem 1.75rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(3, 105, 161, 0.1);
    }

    .uniqueness-details h5 {
        color: #0369a1;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .uniqueness-details h5 i {
        color: #0284c7;
    }

    .uniqueness-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .uniqueness-factor {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid #e0f2fe;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .factor-label {
        font-weight: 600;
        color: #0369a1;
        font-size: 0.875rem;
    }

    .factor-value {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.875rem;
        background: #f1f5f9;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    /* Card Subtitle */
    .card-subtitle {
        font-size: 1rem;
        color: #6b7280;
        margin: 0.75rem 0 0 0;
        font-weight: 400;
        opacity: 0.9;
        line-height: 1.4;
    }

    /* Responsive adjustments for recommendations */
    @media (max-width: 1200px) {
        .recommendations-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .recommendations-header {
            flex-direction: column;
            text-align: center;
            gap: 1.25rem;
            padding: 1.5rem;
        }

        .recommendations-content {
            padding: 2rem;
        }

        .recommendations-grid {
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        .recommendation-card {
            padding: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .recommendations-content {
            padding: 1.5rem;
        }

        .recommendations-header {
            padding: 1.25rem;
        }

        .recommendation-card {
            padding: 1.25rem;
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .recommendation-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .recommendation-number {
            margin: 0 auto 0.75rem auto;
        }

        .uniqueness-grid {
            grid-template-columns: 1fr;
        }

        .uniqueness-factor {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
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

@php
    $userFarm = Auth::user()->farms()->first();
    $farmLocation = $userFarm ? $userFarm->city_municipality_name . ', ' . $userFarm->province_name : 'Manila, Philippines';
@endphp

@push('scripts')
<script>
// Function to load growth progress data
function loadGrowthProgress() {
    const growthProgressEl = document.getElementById('growth-progress-content');
    if (!growthProgressEl) return;

    fetch(`/api/analysis/{{ $photoAnalysis->id }}/growth-progress`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                growthProgressEl.innerHTML = data.html;
            } else {
                growthProgressEl.innerHTML = '<div class="alert alert-warning">No growth data available.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading growth progress:', error);
            growthProgressEl.innerHTML = '<div class="alert alert-danger">Failed to load growth data. Please try refreshing the page.</div>';
        });
}

// Function to load weather information
function loadWeatherInfo() {
    const weatherInfoEl = document.getElementById('weather-info-content');
    if (!weatherInfoEl) return;

    const farmLocation = '{{ $farmLocation }}';
    
    fetch(`/api/weather?location=${encodeURIComponent(farmLocation)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                weatherInfoEl.innerHTML = data.html;
            } else {
                weatherInfoEl.innerHTML = '<div class="alert alert-warning">Weather data not available.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading weather info:', error);
            weatherInfoEl.innerHTML = '<div class="alert alert-danger">Failed to load weather information. Please try again later.</div>';
        });
}

// Load data when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load growth progress and weather info
    loadGrowthProgress();
    loadWeatherInfo();
});

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

// Show success modal if photo analysis was just completed
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're viewing a newly created analysis (you can add URL parameter detection here)
    // For now, we'll show success modal for any photo analysis view
    const analysisTitle = document.querySelector('.page-title')?.textContent?.trim() || '';
    if (analysisTitle.includes('Analysis Results')) {
        showSuccessModal('Photo Analysis Complete', 'Your crop photo has been analyzed successfully!');
    }
});
</script>
@endpush
