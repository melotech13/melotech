@extends('layouts.app')

@section('title', 'Crop Progress Questions - MeloTech')

@section('content')
<div class="questions-container">
    <!-- Unified Header -->
    <div class="unified-header">
        <div class="header-main">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-question-circle"></i>
                    Crop Progress Questions
                </h1>
                <p class="page-subtitle">Answer simple questions about your crop to track progress</p>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-tractor"></i>
                        <span>{{ $farm->farm_name }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-seedling"></i>
                        <span>{{ $farm->watermelon_variety }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-list"></i>
                        <span>{{ count($questions) }} Questions</span>
                    </div>
                </div>
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress-section">
        <div class="progress-card">
            <div class="progress-header">
                <h3>Question <span id="current-number">1</span> of <span id="total-questions">{{ count($questions) }}</span></h3>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <span class="progress-text" id="progress-text">0%</span>
            </div>
        </div>
    </div>

    <!-- Success Message with AI Recommendations -->
    <div id="success-message" class="success-message" style="display: none;">
        <div class="success-content">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="success-title">Progress Updated Successfully!</h3>
            <p class="success-text">Your crop progress has been recorded. Here are AI-powered recommendations based on your answers:</p>
            
            <!-- AI Recommendations Section -->
            <div id="ai-recommendations" class="ai-recommendations">
                <!-- Recommendations will be populated here by JavaScript -->
            </div>
            
            <div class="success-actions">
                <a href="{{ route('crop-progress.index') }}" class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i>View Progress
                </a>
                <button type="button" class="btn btn-outline-success" id="view-recommendations">
                    <i class="fas fa-lightbulb me-2"></i>View Recommendations
                </button>
                <button type="button" class="btn btn-outline-primary" id="print-recommendations">
                    <i class="fas fa-print me-2"></i>Print Recommendations
                </button>
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form id="questions-form" class="questions-form">
        @csrf
        <div id="questions-container">
            @foreach($questions as $questionId => $question)
                <div class="question-card" id="question-{{ $questionId }}" @if($loop->first) style="display: block;" @else style="display: none;" @endif>
                    <div class="question-content">
                        <h3 class="question-title">{{ $question['question'] }}</h3>
                        
                        <div class="answer-options">
                            @foreach($question['options'] as $value => $label)
                                <label class="option-item">
                                    <input type="radio" 
                                           name="answers[{{ $questionId }}]" 
                                           value="{{ $value }}" 
                                           required>
                                    <span class="option-text">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Navigation -->
        <div class="navigation-buttons">
            <button type="button" class="btn btn-outline-secondary" id="prev-btn" style="display: none;">
                <i class="fas fa-arrow-left me-2"></i>Previous
            </button>
            <button type="button" class="btn btn-primary" id="next-btn">
                Next<i class="fas fa-arrow-right ms-2"></i>
            </button>
            <button type="submit" class="btn btn-success" id="submit-btn" style="display: none;">
                <i class="fas fa-check me-2"></i>Submit
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    .questions-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 0 1rem;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    .page-header {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        text-align: center;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .page-subtitle {
        font-size: 1.1rem;
        margin: 0 0 1.5rem 0;
        opacity: 0.9;
    }

    .farm-info {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .farm-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .progress-section {
        margin-bottom: 2rem;
    }

    .progress-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 1.5rem;
        text-align: center;
    }

    .progress-header h3 {
        margin: 0 0 1rem 0;
        color: #374151;
        font-size: 1.2rem;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin: 0 0 0.5rem 0;
    }

    .progress-fill {
        height: 100%;
        background: #10b981;
        transition: width 0.3s ease;
        width: 0%;
    }

    .progress-text {
        font-weight: 600;
        color: #059669;
        font-size: 0.9rem;
    }

    .question-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .question-content {
        padding: 2rem;
    }

    .question-title {
        font-size: 1.1rem;
        color: #374151;
        margin: 0 0 1.5rem 0;
        line-height: 1.5;
    }

    .answer-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .option-item:hover {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
    }

    .option-item input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: #10b981;
    }

    .option-text {
        font-size: 1rem;
        color: #374151;
        flex: 1;
    }

    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1rem;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 2px solid #d1d5db;
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-outline-success {
        background: transparent;
        color: #10b981;
        border: 2px solid #10b981;
    }

    /* Highlight animation for recommendations */
    @keyframes highlight {
        0% { 
            background: rgba(16, 185, 129, 0.1);
            transform: scale(1);
        }
        50% { 
            background: rgba(16, 185, 129, 0.2);
            transform: scale(1.02);
        }
        100% { 
            background: transparent;
            transform: scale(1);
        }
    }

    /* Success Message Styles */
    .success-message {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        border: 2px solid #10b981;
    }

    .success-content {
        max-width: 500px;
        margin: 0 auto;
    }

    .success-icon {
        font-size: 4rem;
        color: #10b981;
        margin-bottom: 1rem;
    }

    .success-title {
        color: #059669;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
    }

    .success-text {
        color: #6b7280;
        font-size: 1.1rem;
        margin: 0 0 2rem 0;
        line-height: 1.6;
    }

    .success-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .success-actions .btn {
        min-width: 150px;
    }

    /* AI Recommendations Styles */
    .ai-recommendations {
        margin: 2rem 0;
        text-align: left;
    }

    .recommendation-category {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border-left: 4px solid #10b981;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .recommendation-category.alert {
        border-left-color: #ef4444;
    }

    .recommendation-category.planning {
        border-left-color: #3b82f6;
    }

    .recommendation-category.tips {
        border-left-color: #f59e0b;
    }

    .category-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #374151;
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .recommendation-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .recommendation-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #4b5563;
    }

    .recommendation-item:last-child {
        border-bottom: none;
    }

    .recommendation-item.urgent {
        color: #dc2626;
        font-weight: 600;
    }

    .recommendation-item.positive {
        color: #059669;
        font-weight: 600;
    }

    .recommendation-summary {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #bae6fd;
        text-align: center;
    }

    .summary-text {
        font-size: 1.1rem;
        color: #0c4a6e;
        font-weight: 600;
        margin: 0;
        line-height: 1.5;
    }

    @media (max-width: 767px) {
        .page-title {
            font-size: 1.5rem;
        }

        .navigation-buttons {
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            width: 100%;
        }
    }

    /* Print Styles */
    @media print {
        .questions-form,
        .navigation-buttons,
        .progress-section,
        .page-header {
            display: none !important;
        }
        
        .success-message {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
        
        .ai-recommendations {
            margin: 0 !important;
        }
        
        .recommendation-category {
            break-inside: avoid;
            margin-bottom: 1rem !important;
        }
        
        .success-actions {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = JSON.parse('{!! json_encode($questions) !!}');
    const questionIds = Object.keys(questions);
    let currentQuestionIndex = 0;
    
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const currentNumber = document.getElementById('current-number');
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');
    
    updateProgress();
    updateNavigation();
    
    prevBtn.addEventListener('click', showPrevious);
    nextBtn.addEventListener('click', showNext);
    
    document.getElementById('questions-form').addEventListener('submit', function(e) {
        e.preventDefault();
        submitProgress();
    });
    
    function showQuestion(index) {
        questionIds.forEach(id => {
            document.getElementById(`question-${id}`).style.display = 'none';
        });
        
        const currentQuestionId = questionIds[index];
        document.getElementById(`question-${currentQuestionId}`).style.display = 'block';
        
        currentQuestionIndex = index;
        updateProgress();
        updateNavigation();
    }
    
    function showNext() {
        if (currentQuestionIndex < questionIds.length - 1) {
            showQuestion(currentQuestionIndex + 1);
        }
    }
    
    function showPrevious() {
        if (currentQuestionIndex > 0) {
            showQuestion(currentQuestionIndex - 1);
        }
    }
    
    function updateProgress() {
        const progress = ((currentQuestionIndex + 1) / questionIds.length) * 100;
        progressFill.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '%';
        currentNumber.textContent = currentQuestionIndex + 1;
    }
    
    function updateNavigation() {
        prevBtn.style.display = currentQuestionIndex === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = currentQuestionIndex === questionIds.length - 1 ? 'none' : 'inline-block';
        submitBtn.style.display = currentQuestionIndex === questionIds.length - 1 ? 'inline-block' : 'none';
    }
    
    function submitProgress() {
        const answers = {};
        let allAnswered = true;
        
        questionIds.forEach(id => {
            const selected = document.querySelector(`input[name="answers[${id}]"]:checked`);
            if (selected) {
                answers[id] = selected.value;
            } else {
                allAnswered = false;
            }
        });
        
        if (!allAnswered) {
            alert('Please answer all questions before submitting.');
            return;
        }
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        
        // Append each answer individually to create an array format
        Object.keys(answers).forEach(key => {
            formData.append(`answers[${key}]`, answers[key]);
        });
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        
        fetch('{{ route("crop-progress.store-questions") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide the form and show success message
                document.getElementById('questions-form').style.display = 'none';
                document.getElementById('success-message').style.display = 'block';
                
                // Display AI recommendations if available
                if (data.recommendations) {
                    displayRecommendations(data.recommendations, data.recommendation_summary);
                }
                
                // Scroll to top to show the success message
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Submit';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Submit';
        });
    }
    
    function displayRecommendations(recommendations, summary) {
        const recommendationsContainer = document.getElementById('ai-recommendations');
        
        let html = '';
        
        // Add summary
        if (summary) {
            html += `
                <div class="recommendation-summary">
                    <p class="summary-text">${summary}</p>
                </div>
            `;
        }
        
        // Add priority alerts
        if (recommendations.priority_alerts && recommendations.priority_alerts.length > 0) {
            html += `
                <div class="recommendation-category alert">
                    <h4 class="category-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Priority Alerts
                    </h4>
                    <ul class="recommendation-list">
                        ${recommendations.priority_alerts.map(item => `
                            <li class="recommendation-item ${item.includes('✅') ? 'positive' : 'urgent'}">${item}</li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }
        
        // Add immediate actions
        if (recommendations.immediate_actions && recommendations.immediate_actions.length > 0) {
            html += `
                <div class="recommendation-category">
                    <h4 class="category-title">
                        <i class="fas fa-bolt"></i>
                        Immediate Actions
                    </h4>
                    <ul class="recommendation-list">
                        ${recommendations.immediate_actions.map(item => `
                            <li class="recommendation-item ${item.includes('✅') ? 'positive' : ''}">${item}</li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }
        
        // Add weekly plan
        if (recommendations.weekly_plan && recommendations.weekly_plan.length > 0) {
            html += `
                <div class="recommendation-category planning">
                    <h4 class="category-title">
                        <i class="fas fa-calendar-week"></i>
                        Weekly Plan
                    </h4>
                    <ul class="recommendation-list">
                        ${recommendations.weekly_plan.map(item => `
                            <li class="recommendation-item">${item}</li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }
        
        // Add long term tips
        if (recommendations.long_term_tips && recommendations.long_term_tips.length > 0) {
            html += `
                <div class="recommendation-category tips">
                    <h4 class="category-title">
                        <i class="fas fa-lightbulb"></i>
                        Long-term Tips
                    </h4>
                    <ul class="recommendation-list">
                        ${recommendations.long_term_tips.map(item => `
                            <li class="recommendation-item">${item}</li>
                        `).join('')}
                    </ul>
                </div>
            `;
        }
        
        recommendationsContainer.innerHTML = html;
    }
    
    // Add print functionality
    document.getElementById('print-recommendations').addEventListener('click', function() {
        window.print();
    });
    
    // Add view recommendations functionality
    document.getElementById('view-recommendations').addEventListener('click', function() {
        const recommendationsContainer = document.getElementById('ai-recommendations');
        
        // Scroll to recommendations section
        recommendationsContainer.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        
        // Add a subtle highlight effect
        recommendationsContainer.style.animation = 'highlight 2s ease-in-out';
        
        // Remove animation after it completes
        setTimeout(() => {
            recommendationsContainer.style.animation = '';
        }, 2000);
    });
});
</script>
@endpush
