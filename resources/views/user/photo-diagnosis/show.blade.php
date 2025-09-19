@extends('layouts.app')

@section('title', 'Analysis Results - MeloTech')

@section('content')
@php
    $userFarm = Auth::user()->farms()->first();
    $farmLocation = $userFarm ? $userFarm->city_municipality_name . ', ' . $userFarm->province_name : 'Manila, Philippines';
@endphp
<div class="analysis-results-container" data-analysis-id="{{ $photoAnalysis->id }}" data-farm-location="{{ $farmLocation }}">

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
                        <a href="{{ route('photo-diagnosis.create') }}" class="btn btn-sm me-2">
                            <i class="fas fa-plus me-2"></i>New Analysis
                        </a>
                        <a href="{{ route('photo-diagnosis.index') }}" class="btn btn-sm">
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
                            @if($photoAnalysis->photo_url)
                                <img src="{{ $photoAnalysis->photo_url }}" 
                                     alt="Analysis Photo" 
                                     class="analysis-photo">
                                <div class="no-image-placeholder hidden">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Photo not available</p>
                                </div>
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
                            <div class="detail-item detail-accent analysis-type-accent">
                                <div class="detail-label">Analysis Type</div>
                                <div class="detail-value">{{ $photoAnalysis->analysis_type_label }}</div>
                            </div>

                            <div class="detail-item detail-accent confidence-accent">
                                <div class="detail-label">Confidence</div>
                                <div class="detail-value">
                                    @if($photoAnalysis->confidence_score)
                                        <span class="confidence-badge" title="Confidence">
                                            <i class="fas fa-shield-check"></i>{{ $photoAnalysis->confidence_score }}%
                                        </span>
                                    @else
                                        <span class="text-muted">Not available</span>
                                    @endif
                                </div>
                            </div>

                            
                            

                            @if(is_array($photoAnalysis->condition_scores))
                            <div class="detail-item full-width probabilities-card">
                                <div class="probabilities-header">
                                    <div class="title">
                                        <i class="fas fa-percentage"></i>
                                        <span>Condition Probabilities</span>
                                    </div>
                                    @if($photoAnalysis->model_version)
                                        <span class="model-badge" title="Model version">Model: {{ $photoAnalysis->model_version }}</span>
                                    @endif
                                </div>
                                <div class="condition-probabilities">
                                    @php
                                        $labels = [
                                            'healthy' => 'Healthy',
                                            'fungal_infection' => 'Fungal Infection',
                                            'nutrient_deficiency' => 'Nutrient Deficiency',
                                            'pest_damage' => 'Pest Damage',
                                            'viral_infection' => 'Viral Infection',
                                        ];
                                        $icons = [
                                            'healthy' => 'fas fa-leaf',
                                            'fungal_infection' => 'fas fa-biohazard',
                                            'nutrient_deficiency' => 'fas fa-flask',
                                            'pest_damage' => 'fas fa-bug',
                                            'viral_infection' => 'fas fa-virus',
                                        ];
                                        $colors = [
                                            'healthy' => '#10b981',
                                            'fungal_infection' => '#ef4444',
                                            'nutrient_deficiency' => '#f59e0b',
                                            'pest_damage' => '#3b82f6',
                                            'viral_infection' => '#8b5cf6',
                                        ];
                                        $scores = is_array($photoAnalysis->condition_scores) ? $photoAnalysis->condition_scores : [];
                                        $sortedKeys = collect($scores)->sortDesc()->keys()->toArray();
                                        // Fallback to default order if collect() not available or scores empty
                                        if (empty($sortedKeys)) { $sortedKeys = array_keys($labels); }
                                    @endphp
                                    <div class="probabilities-list">
                                        @foreach($sortedKeys as $loopKey)
                                            @php
                                                $key = $loopKey;
                                                $label = $labels[$key] ?? ucfirst(str_replace('_',' ', $key));
                                                $value = (int)($scores[$key] ?? 0);
                                                $isTop = $loop->first;
                                            @endphp
                                            <div class="probability-row key-{{ $key }} @if($isTop) top @endif" role="group" aria-label="{{ $label }} {{ $value }} percent">
                                                <div class="left">
                                                    <span class="icon" aria-hidden="true" data-color="{{ $colors[$key] }}"><i class="{{ $icons[$key] ?? 'fas fa-info-circle' }}"></i></span>
                                                    <span class="label">{{ $label }}</span>
                                                    @if($isTop)
                                                        <span class="top-badge" title="Most likely condition"><i class="fas fa-crown"></i> Top</span>
                                                    @endif
                                                </div>
                                                <div class="right">
                                                    <div class="probability-bar" aria-hidden="true">
                                                        <div class="probability-fill" data-width="{{ $value }}" data-color="{{ $colors[$key] }}"></div>
                                                    </div>
                                                    <span class="percent-badge" data-color="{{ $colors[$key] }}">{{ $value }}%</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="probabilities-footer">
                                        <i class="fas fa-info-circle"></i>
                                        <span>These percentages are generated by our AI model to help you quickly assess plant health and choose the right care or treatment.</span>
                                    </div>
                                </div>
                            </div>
                            @endif
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

                                <!-- Specific Recommendations: One UI card per condition percentage -->
                                @php
                                    $colorChips = [
                                        'healthy' => '#10b981',
                                        'fungal_infection' => '#8b5cf6',
                                        'nutrient_deficiency' => '#f59e0b',
                                        'pest_damage' => '#ef4444',
                                        'viral_infection' => '#6d28d9',
                                    ];
                                    $conditionIcons = [
                                        'healthy' => 'fas fa-leaf',
                                        'fungal_infection' => 'fas fa-biohazard',
                                        'nutrient_deficiency' => 'fas fa-flask',
                                        'pest_damage' => 'fas fa-bug',
                                        'viral_infection' => 'fas fa-virus',
                                    ];
                                @endphp
                                <div class="recommendations-grid">
                                    @if(isset($photoAnalysis->recommendations['per_condition']) && is_array($photoAnalysis->recommendations['per_condition']))
                                        @foreach($photoAnalysis->recommendations['per_condition'] as $index => $rc)
                                            @php
                                                $k = $rc['key'];
                                                $chip = $colorChips[$k] ?? '#3b82f6';
                                            @endphp
                                            <div class="recommendation-card recommendation-{{ $index + 1 }}">
                                                <div class="recommendation-icon chip-{{ $k }}">
                                                    <i class="{{ $conditionIcons[$k] ?? 'fas fa-lightbulb' }}" aria-hidden="true"></i>
                                                </div>
                                                <div class="recommendation-content">
                                                    <div class="recommendation-text">
                                                        <div class="recommendation-header">
                                                            <div class="recommendation-number">{{ $index + 1 }}</div>
                                                            <div class="recommendation-title"><strong>{{ $rc['label'] ?? '' }} ({{ (int)($rc['percent'] ?? 0) }}%)</strong></div>
                                                        </div>
                                                        <div>{{ $rc['action'] ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @php
                                            $overallLabel = $photoAnalysis->recommendations['condition_label'] ?? 'Overall';
                                            // Ensure overall looks different from any single condition (e.g., Viral Infection)
                                            $overallKey = 'overall';
                                            $overallIcon = 'fas fa-seedling';
                                            $overallChip = '#14b8a6';
                                            $overallAction = $photoAnalysis->recommendations['overall'] ?? 'Overall care: combine the above steps. Monitor weekly, maintain even moisture, sanitize tools, apply balanced nutrients, scout for pests, and record observations to catch changes early.';
                                            $overallPercent = $photoAnalysis->recommendations['overall_percent'] ?? null;
                                        @endphp
                                        <div class="recommendation-card recommendation-overall">
                                            <div class="recommendation-icon chip-{{ $overallKey }}">
                                                <i class="{{ $overallIcon }}" aria-hidden="true"></i>
                                            </div>
                                            <div class="recommendation-content">
                                                <div class="recommendation-text">
                                                    <div class="recommendation-header">
                                                        <div class="recommendation-number">{{ (count($photoAnalysis->recommendations['per_condition']) ?? 0) + 1 }}</div>
                                                        <div class="recommendation-title"><strong>Overall Recommendation{{ $overallPercent ? ' ('.$overallPercent.'%)' : '' }}</strong></div>
                                                    </div>
                                                    <div>{{ $overallAction }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            // Fallback to existing list if new structure missing
                                            $legacy = $photoAnalysis->recommendations['recommendations'] ?? [];
                                        @endphp
                                        @foreach($legacy as $index => $recommendation)
                                            <div class="recommendation-card recommendation-{{ $index + 1 }}">
                                                <div class="recommendation-icon">
                                                    <i class="fas fa-lightbulb text-secondary"></i>
                                                </div>
                                                <div class="recommendation-content">
                                                    <div class="recommendation-text">
                                                        <div class="recommendation-header">
                                                            <div class="recommendation-number">{{ $index + 1 }}</div>
                                                            <div class="recommendation-title"><strong>Recommendation</strong></div>
                                                        </div>
                                                        <div>{!! nl2br(e($recommendation)) !!}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
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
                            <button class="btn btn-outline-secondary" id="share-results-btn">
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
<link rel="stylesheet" href="{{ asset('css/photo-diagnosis-show.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/photo-diagnosis-show.js') }}" defer></script>
@endpush
