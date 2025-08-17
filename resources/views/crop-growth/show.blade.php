@extends('layouts.crop-growth')

@section('title', 'Track Crop Growth - ' . $farm->farm_name . ' - MeloTech')

@section('content')
<div class="crop-tracking-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <div class="header-text">
                <h1 class="header-title">
                    <i class="fas fa-seedling header-icon"></i>
                    {{ $farm->farm_name }}
                </h1>
                <p class="header-subtitle">Track your crop growth progress with simple questions</p>
            </div>
            <div class="farm-info-card">
                <div class="farm-details">
                    <div class="detail-item">
                        <span class="detail-label">Location:</span>
                        <span class="detail-value">{{ $farm->city_municipality_name }}, {{ $farm->province_name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Variety:</span>
                        <span class="detail-value">{{ $farm->watermelon_variety }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Planting Date:</span>
                        <span class="detail-value">{{ $farm->planting_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Timeline -->
    <div class="timeline-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-chart-line section-icon"></i>
                Growth Timeline
            </h2>
        </div>
        
        <div class="growth-timeline">
            @foreach($stages as $stageKey => $stageInfo)
                <div class="timeline-item {{ $cropGrowth->current_stage === $stageKey ? 'current' : ($cropGrowth->overall_progress > (array_search($stageKey, array_keys($stages)) * 20) ? 'completed' : 'upcoming') }}">
                    <div class="timeline-marker">
                        <span class="marker-icon">{{ $stageInfo['icon'] }}</span>
                        @if($cropGrowth->current_stage === $stageKey)
                            <div class="current-indicator"></div>
                        @elseif($cropGrowth->overall_progress > (array_search($stageKey, array_keys($stages)) * 20))
                            <div class="completed-indicator">
                                <i class="fas fa-check"></i>
                            </div>
                        @endif
                    </div>
                    <div class="timeline-content">
                        <h3 class="stage-name">{{ $stageInfo['name'] }}</h3>
                        <p class="stage-description">{{ $stageInfo['description'] }}</p>
                        @if($cropGrowth->current_stage === $stageKey)
                            <div class="current-stage-progress">
                                <span class="progress-text">{{ $cropGrowth->stage_progress }}% Complete</span>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $cropGrowth->stage_progress }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if($loop->index < count($stages) - 1)
                    <div class="timeline-arrow">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Current Stage Tracking -->
    <div class="tracking-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-question-circle section-icon"></i>
                Current Stage: {{ $stages[$cropGrowth->current_stage]['name'] }}
            </h2>
        </div>
        
        <div class="tracking-content">
            <div class="stage-overview">
                <div class="stage-header">
                    <div class="stage-icon-large">
                        {{ $stages[$cropGrowth->current_stage]['icon'] }}
                    </div>
                    <div class="stage-info">
                        <h3>{{ $stages[$cropGrowth->current_stage]['name'] }}</h3>
                        <p>{{ $stages[$cropGrowth->current_stage]['description'] }}</p>
                        <div class="stage-progress-info">
                            <span class="progress-label">Stage Progress:</span>
                            <span class="progress-value">{{ $cropGrowth->stage_progress }}%</span>
                        </div>
                    </div>
                </div>
                
                <div class="progress-visual">
                    <div class="progress-circle">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-ring-bg" cx="60" cy="60" r="54" stroke-width="8"></circle>
                            <circle class="progress-ring-fill" cx="60" cy="60" r="54" stroke-width="8" 
                                    stroke-dasharray="{{ 2 * pi() * 54 }}" 
                                    stroke-dashoffset="{{ 2 * pi() * 54 * (1 - $cropGrowth->stage_progress / 100) }}"></circle>
                        </svg>
                        <div class="progress-text-center">
                            <span class="progress-number">{{ $cropGrowth->stage_progress }}%</span>
                            <span class="progress-label-small">Complete</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Questions -->
            <div class="questions-section">
                <h3>Quick Health Check</h3>
                <p class="questions-intro">Answer these simple questions to update your crop progress:</p>
                
                <div class="questions-grid">
                    
                    @foreach($questions as $questionId => $question)
                        <div class="question-card" data-question-id="{{ $questionId }}">
                            <div class="question-content">
                                <h4 class="question-text">{{ $question['text'] }}</h4>
                                <div class="question-actions">
                                    <button class="answer-btn answer-yes" data-answer="true">
                                        <i class="fas fa-check"></i>
                                        <span>Yes</span>
                                    </button>
                                    <button class="answer-btn answer-no" data-answer="false">
                                        <i class="fas fa-times"></i>
                                        <span>No</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Manual Progress Update -->
            <div class="manual-progress-section">
                <h3>Manual Progress Update</h3>
                <p>Or set your progress manually:</p>
                
                <div class="progress-input-group">
                    <label for="progress-slider">Stage Progress: <span id="progress-value">{{ $cropGrowth->stage_progress }}%</span></label>
                    <input type="range" id="progress-slider" min="0" max="100" value="{{ $cropGrowth->stage_progress }}" class="progress-slider">
                    <div class="slider-labels">
                        <span>0%</span>
                        <span>100%</span>
                    </div>
                </div>
                
                <div class="notes-section">
                    <label for="progress-notes">Notes (optional):</label>
                    <textarea id="progress-notes" placeholder="Add any notes about your crop's current condition..." class="notes-textarea"></textarea>
                </div>
                
                <button class="btn btn-primary update-progress-btn" data-farm-id="{{ $farm->id }}">
                    <i class="fas fa-save me-1"></i>
                    Update Progress
                </button>
            </div>
        </div>
    </div>
    
    <!-- Stage Advancement -->
    @if($cropGrowth->canAdvanceStage())
        <div class="advancement-section">
            <div class="advancement-card">
                <div class="advancement-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="advancement-content">
                    <h3>Ready for Next Stage!</h3>
                    <p>Your crop has completed the current stage. You can now advance to the next growth phase.</p>
                    <button class="btn btn-success advance-stage-btn" data-farm-id="{{ $farm->id }}">
                        <i class="fas fa-arrow-up me-1"></i>
                        Advance to {{ $cropGrowth->getNextStage() ? $stages[$cropGrowth->getNextStage()]['name'] : 'Next Stage' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.crop-tracking-container {
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
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 2rem;
}

        .header-text {
            flex: 1;
        }

.header-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    letter-spacing: -0.025em;
}

.header-icon {
    font-size: 1.75rem;
    color: #fbbf24;
}

.header-subtitle {
    font-size: 1.1rem;
    opacity: 0.95;
    margin: 0;
    font-weight: 400;
}

.farm-info-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.farm-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.detail-label {
    font-weight: 600;
    opacity: 0.9;
}

.detail-value {
    font-weight: 700;
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

/* Growth Timeline */
.timeline-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

.growth-timeline {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
}

.timeline-item.current {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 2px solid #10b981;
    transform: scale(1.02);
}

.timeline-item.completed {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    opacity: 0.8;
}

.timeline-item.upcoming {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    opacity: 0.6;
}

.timeline-marker {
    position: relative;
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.marker-icon {
    font-size: 1.5rem;
}

.current-indicator {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 20px;
    height: 20px;
    background: #10b981;
    border-radius: 50%;
    border: 3px solid white;
    animation: pulse 2s infinite;
}

.completed-indicator {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 20px;
    height: 20px;
    background: #10b981;
    border-radius: 50%;
    border: 3px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.timeline-content {
    flex: 1;
}

.stage-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.stage-description {
    color: #64748b;
    margin: 0;
    line-height: 1.5;
}

.current-stage-progress {
    margin-top: 1rem;
}

.progress-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #10b981;
    margin-bottom: 0.5rem;
    display: block;
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

.timeline-arrow {
    text-align: center;
    color: #94a3b8;
    font-size: 1.25rem;
    margin: 0.5rem 0;
}

/* Tracking Section */
.tracking-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

.tracking-content {
    display: grid;
    gap: 2rem;
}

.stage-overview {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.stage-header {
    flex: 1;
}

.stage-icon-large {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.stage-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.stage-info p {
    color: #64748b;
    margin: 0 0 1rem 0;
    line-height: 1.5;
}

.stage-progress-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-label {
    font-weight: 600;
    color: #475569;
}

.progress-value {
    font-weight: 700;
    color: #10b981;
    font-size: 1.1rem;
}

.progress-visual {
    flex-shrink: 0;
}

.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-bg {
    fill: none;
    stroke: #e2e8f0;
}

.progress-ring-fill {
    fill: none;
    stroke: #10b981;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease;
}

.progress-text-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-number {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: #10b981;
    line-height: 1;
}

.progress-label-small {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Questions Section */
.questions-section {
    padding: 2rem;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border-radius: 16px;
    border: 1px solid #bbf7d0;
}

.questions-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #166534;
    margin: 0 0 0.5rem 0;
}

.questions-intro {
    color: #166534;
    margin: 0 0 1.5rem 0;
    font-size: 1rem;
}

.questions-grid {
    display: grid;
    gap: 1rem;
}

.question-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #bbf7d0;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
    transition: all 0.3s ease;
}

.question-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.15);
}

.question-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #166534;
    margin: 0 0 1rem 0;
    line-height: 1.4;
}

.question-actions {
    display: flex;
    gap: 1rem;
}

.answer-btn {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.answer-btn.answer-yes {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.answer-btn.answer-yes:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
}

.answer-btn.answer-no {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.answer-btn.answer-no:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
}

.answer-btn.answered {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Manual Progress Section */
.manual-progress-section {
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.manual-progress-section h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.manual-progress-section p {
    color: #64748b;
    margin: 0 0 1.5rem 0;
}

.progress-input-group {
    margin-bottom: 1.5rem;
}

.progress-input-group label {
    display: block;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
}

.progress-slider {
    width: 100%;
    height: 8px;
    border-radius: 4px;
    background: #e2e8f0;
    outline: none;
    -webkit-appearance: none;
    margin-bottom: 0.5rem;
}

.progress-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #10b981;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.progress-slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #10b981;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.slider-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #64748b;
}

.notes-section {
    margin-bottom: 1.5rem;
}

.notes-section label {
    display: block;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
}

.notes-textarea {
    width: 100%;
    min-height: 100px;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    resize: vertical;
    font-family: inherit;
    transition: border-color 0.3s ease;
}

.notes-textarea:focus {
    outline: none;
    border-color: #10b981;
}

/* Advancement Section */
.advancement-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

.advancement-card {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 2rem;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border-radius: 16px;
    border: 2px solid #10b981;
}

.advancement-icon {
    width: 80px;
    height: 80px;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    flex-shrink: 0;
}

.advancement-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #166534;
    margin: 0 0 0.5rem 0;
}

.advancement-content p {
    color: #166534;
    margin: 0 0 1rem 0;
    line-height: 1.5;
}

/* Buttons */
.btn {
    padding: 0.75rem 1.5rem;
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
    font-size: 1rem;
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

/* Responsive Design */
@media (max-width: 768px) {
    .crop-tracking-container {
        padding: 1.5rem 1rem 1rem 1rem;
    }
    
    .header-section {
        padding: 1.5rem 1rem;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .header-title {
        font-size: 1.5rem;
    }
    
    .timeline-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .stage-overview {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .question-actions {
        flex-direction: column;
    }
    
    .advancement-card {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize progress slider
    const progressSlider = document.getElementById('progress-slider');
    const progressValue = document.getElementById('progress-value');
    
    if (progressSlider && progressValue) {
        progressSlider.addEventListener('input', function() {
            progressValue.textContent = this.value + '%';
        });
    }
    
    // Handle question answers
    document.querySelectorAll('.answer-btn').forEach(button => {
        button.addEventListener('click', function() {
            const questionCard = this.closest('.question-card');
            const questionId = questionCard.dataset.questionId;
            const answer = this.classList.contains('answer-yes');
            
            // Disable all buttons in this question
            questionCard.querySelectorAll('.answer-btn').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('answered');
            });
            
            // Send answer to server
            submitAnswer(questionId, answer, questionCard);
        });
    });
    
    // Handle manual progress update
    const updateProgressBtn = document.querySelector('.update-progress-btn');
    if (updateProgressBtn) {
        updateProgressBtn.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            const progress = document.getElementById('progress-slider').value;
            const notes = document.getElementById('progress-notes').value;
            
            updateProgress(farmId, progress, notes, this);
        });
    }
    
    // Handle stage advancement
    const advanceStageBtn = document.querySelector('.advance-stage-btn');
    if (advanceStageBtn) {
        advanceStageBtn.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            advanceStage(farmId, this);
        });
    }
});

function submitAnswer(questionId, answer, questionCard) {
    const farmId = window.location.pathname.split('/').pop();
    
    fetch(`/crop-growth/farm/${farmId}/quick-update`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            question_id: questionId,
            answer: answer
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message, 'success');
            
            // Update progress display
            updateProgressDisplay(data.crop_growth);
            
            // Check if stage advanced
            if (data.stage_advanced) {
                showNotification(`Stage advanced to ${data.new_stage}!`, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        } else {
            showNotification(data.message, 'error');
            // Re-enable buttons on error
            questionCard.querySelectorAll('.answer-btn').forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('answered');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while submitting your answer.', 'error');
        // Re-enable buttons on error
        questionCard.querySelectorAll('.answer-btn').forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('answered');
        });
    });
}

function updateProgress(farmId, progress, notes, button) {
    // Disable button
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
    
    fetch(`/crop-growth/farm/${farmId}/progress`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            stage_progress: progress,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Update progress display
            updateProgressDisplay(data.crop_growth);
            
            // Check if stage can be advanced
            if (data.can_advance) {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } else {
            showNotification(data.message, 'error');
        }
        
        // Re-enable button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-save me-1"></i>Update Progress';
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating progress.', 'error');
        
        // Re-enable button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-save me-1"></i>Update Progress';
    });
}

function advanceStage(farmId, button) {
    // Disable button
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
            showNotification(data.message, 'success');
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

function updateProgressDisplay(cropGrowth) {
    // Update progress bars and percentages
    const stageProgress = cropGrowth.stage_progress;
    const overallProgress = cropGrowth.overall_progress;
    
    // Update stage progress
    const stageProgressElements = document.querySelectorAll('.current-stage-progress .progress-fill');
    stageProgressElements.forEach(element => {
        element.style.width = stageProgress + '%';
    });
    
    const stageProgressTexts = document.querySelectorAll('.current-stage-progress .progress-text');
    stageProgressTexts.forEach(element => {
        element.textContent = stageProgress + '% Complete';
    });
    
    // Update overall progress
    const overallProgressElements = document.querySelectorAll('.overall-progress .progress-fill');
    overallProgressElements.forEach(element => {
        element.style.width = overallProgress + '%';
    });
    
    const overallProgressTexts = document.querySelectorAll('.overall-progress .progress-percentage');
    overallProgressTexts.forEach(element => {
        element.textContent = Math.round(overallProgress * 10) / 10 + '%';
    });
    
    // Update progress circle
    const progressCircle = document.querySelector('.progress-ring-fill');
    if (progressCircle) {
        const radius = 54;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference - (stageProgress / 100) * circumference;
        progressCircle.style.strokeDashoffset = offset;
    }
    
    const progressNumber = document.querySelector('.progress-number');
    if (progressNumber) {
        progressNumber.textContent = stageProgress + '%';
    }
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
