@extends('layouts.crop-growth')

@section('title', 'Crop Growth Tracking - MeloTech')

@section('content')
<div class="crop-growth-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <div class="header-text">
                <h1 class="header-title">
                    <i class="fas fa-seedling header-icon"></i>
                    Crop Growth Tracking
                </h1>
                <p class="header-subtitle">Monitor your watermelon crops through each growth stage with simple yes/no questions</p>
            </div>
            <div class="header-visual">
                <div class="growth-timeline">
                    <div class="timeline-stage active">
                        <span class="stage-icon">🌱</span>
                        <span class="stage-name">Seedling</span>
                    </div>
                    <div class="timeline-arrow">→</div>
                    <div class="timeline-stage">
                        <span class="stage-icon">🌿</span>
                        <span class="stage-name">Vegetative</span>
                    </div>
                    <div class="timeline-arrow">→</div>
                    <div class="timeline-stage">
                        <span class="stage-icon">🌸</span>
                        <span class="stage-name">Flowering</span>
                    </div>
                    <div class="timeline-arrow">→</div>
                    <div class="timeline-stage">
                        <span class="stage-icon">🍉</span>
                        <span class="stage-name">Fruiting</span>
                    </div>
                    <div class="timeline-arrow">→</div>
                    <div class="timeline-stage">
                        <span class="stage-icon">✂️</span>
                        <span class="stage-name">Harvest</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($farms->count() > 0)
        <!-- Farms Overview -->
        <div class="farms-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tractor section-icon"></i>
                    Your Farms
                </h2>
            </div>
            
            <div class="farms-grid">
                @foreach($farms as $farm)
                    @php
                        $cropGrowth = $farm->cropGrowth;
                        if (!$cropGrowth) {
                            $cropGrowth = $farm->getOrCreateCropGrowth();
                        }
                        $currentStageInfo = $cropGrowth->getStageInfo();
                        $nextStage = $cropGrowth->getNextStage();
                        $nextStageInfo = $nextStage ? $cropGrowth->getStageInfo($nextStage) : null;
                    @endphp
                    
                    <div class="farm-card">
                        <div class="farm-header">
                            <div class="farm-info">
                                <h3 class="farm-name">{{ $farm->farm_name }}</h3>
                                <p class="farm-location">{{ $farm->city_municipality_name }}, {{ $farm->province_name }}</p>
                            </div>
                            <div class="farm-status">
                                <span class="status-badge {{ $cropGrowth->current_stage }}">
                                    {{ $currentStageInfo['icon'] }} {{ $currentStageInfo['name'] }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="growth-progress">
                            <div class="stage-progress">
                                <div class="progress-label">
                                    <span>Current Stage Progress</span>
                                    <span class="progress-percentage">{{ $cropGrowth->stage_progress }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $cropGrowth->stage_progress }}%"></div>
                                </div>
                            </div>
                            
                            <div class="overall-progress">
                                <div class="progress-label">
                                    <span>Overall Growth</span>
                                    <span class="progress-percentage">{{ round($cropGrowth->overall_progress, 1) }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $cropGrowth->overall_progress }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stage-details">
                            <div class="current-stage">
                                <h4>Current Stage: {{ $currentStageInfo['name'] }}</h4>
                                <p>{{ $currentStageInfo['description'] }}</p>
                                @if($cropGrowth->last_updated)
                                    <small class="last-updated">Last updated: {{ $cropGrowth->last_updated->format('M d, Y') }}</small>
                                @endif
                            </div>
                            
                            @if($nextStageInfo)
                                <div class="next-stage">
                                    <h4>Next: {{ $nextStageInfo['name'] }}</h4>
                                    <p>{{ $nextStageInfo['description'] }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="farm-actions">
                            <a href="{{ route('crop-growth.show', $farm) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-chart-line me-1"></i>
                                Track Growth
                            </a>
                            @if($cropGrowth->canAdvanceStage())
                                <button class="btn btn-success btn-sm advance-stage-btn" data-farm-id="{{ $farm->id }}">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    Advance Stage
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- No Farms Message -->
        <div class="no-farms-section">
            <div class="no-farms-content">
                <div class="no-farms-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h3>No Farms Found</h3>
                <p>You need to create a farm first to start tracking crop growth.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Create Farm
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>

.crop-growth-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1.5rem 2rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Section */
.header-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    margin-bottom: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}



.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.header-text {
    flex: 1;
}

.header-title {
    font-size: 2.25rem;
    font-weight: 800;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    letter-spacing: -0.025em;
}

.header-icon {
    font-size: 2rem;
    color: #fbbf24;
}

.header-subtitle {
    font-size: 1.125rem;
    opacity: 0.95;
    margin: 0;
    font-weight: 400;
    line-height: 1.6;
}

.header-visual {
    display: flex;
    align-items: center;
}

.growth-timeline {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 1rem;
    border-radius: 12px;
    backdrop-filter: blur(10px);
}

.timeline-stage {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.timeline-stage.active {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.stage-icon {
    font-size: 1.5rem;
}

.stage-name {
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}

.timeline-arrow {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: bold;
}

/* Section Styling */
.section-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    letter-spacing: -0.025em;
}

.section-icon {
    color: #10b981;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Farms Grid */
.farms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.farm-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.farm-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.farm-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.farm-card:hover::before {
    transform: scaleX(1);
}

.farm-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.farm-info {
    flex: 1;
}

.farm-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.025em;
}

.farm-location {
    color: #64748b;
    margin: 0;
    font-size: 0.9rem;
    font-weight: 500;
}

.farm-status {
    flex-shrink: 0;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: white;
}

.status-badge.seedling {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.status-badge.vegetative {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.status-badge.flowering {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.status-badge.fruiting {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.status-badge.harvest {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

/* Growth Progress */
.growth-progress {
    margin-bottom: 1.5rem;
}

.stage-progress,
.overall-progress {
    margin-bottom: 1rem;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #475569;
}

.progress-percentage {
    color: #10b981;
    font-weight: 700;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    border-radius: 4px;
    transition: width 0.5s ease;
}

/* Stage Details */
.stage-details {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.current-stage,
.next-stage {
    margin-bottom: 1rem;
}

.current-stage:last-child,
.next-stage:last-child {
    margin-bottom: 0;
}

.stage-details h4 {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.stage-details p {
    color: #64748b;
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

.last-updated {
    color: #94a3b8;
    font-size: 0.8rem;
    font-style: italic;
}

/* Farm Actions */
.farm-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
}

/* No Farms Section */
.no-farms-section {
    text-align: center;
    padding: 4rem 2rem;
}

.no-farms-content {
    max-width: 500px;
    margin: 0 auto;
}

.no-farms-icon {
    font-size: 4rem;
    color: #94a3b8;
    margin-bottom: 1.5rem;
}

.no-farms-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
}

.no-farms-section p {
    color: #64748b;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .crop-growth-container {
        padding: 1.5rem 1rem 1rem 1rem;
    }
    
    .header-section {
        padding: 1.5rem 1rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .header-top {
        text-align: center;
    }
    
    .header-title {
        font-size: 1.75rem;
    }
    
    .growth-timeline {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .farms-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .farm-card {
        padding: 1.5rem;
    }
    
    .farm-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .farm-actions {
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle advance stage buttons
    document.querySelectorAll('.advance-stage-btn').forEach(button => {
        button.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            advanceStage(farmId, this);
        });
    });
    
    // Initialize navigation stages
    initializeNavigationStages();
});

// Function to initialize and highlight the current stage in timeline
function initializeNavigationStages() {
    const farms = document.querySelectorAll('.farm-card');
    
    if (farms.length === 0) return;
    
    // Get the most advanced stage from all farms
    let mostAdvancedStage = 'seedling';
    let maxProgress = 0;
    
    farms.forEach(farm => {
        const progressBar = farm.querySelector('.overall-progress .progress-fill');
        if (progressBar) {
            const progress = parseInt(progressBar.style.width);
            if (progress > maxProgress) {
                maxProgress = progress;
                // Determine stage based on progress
                if (progress >= 80) mostAdvancedStage = 'harvest';
                else if (progress >= 60) mostAdvancedStage = 'fruiting';
                else if (progress >= 40) mostAdvancedStage = 'flowering';
                else if (progress >= 20) mostAdvancedStage = 'vegetative';
                else mostAdvancedStage = 'seedling';
            }
        }
    });
    
    // Update timeline to show current stage
    const timelineStages = document.querySelectorAll('.timeline-stage');
    timelineStages.forEach(stage => {
        stage.classList.remove('active');
        const stageName = stage.querySelector('.stage-name').textContent.toLowerCase();
        if (stageName === mostAdvancedStage) {
            stage.classList.add('active');
        }
    });
}

function advanceStage(farmId, button) {
    // Disable button to prevent double-clicking
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Advancing...';
    
    fetch(`/crop-growth/farm/${farmId}/advance`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message, 'error');
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-arrow-up me-1"></i>Advance Stage';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while advancing the stage.', 'error');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-arrow-up me-1"></i>Advance Stage';
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#dc2626'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 400px;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Hide and remove notification
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}
</script>
@endpush
