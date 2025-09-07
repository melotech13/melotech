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
                <p class="page-subtitle">Answer stage-specific questions about your {{ ucfirst($farm->cropGrowth->current_stage ?? 'seedling') }} stage crop to track progress</p>
                @if($farm)
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
                    <i class="fas fa-chart-line"></i>
                    <span>{{ ucfirst($farm->cropGrowth->current_stage ?? 'seedling') }} Stage</span>
                </div>
                <div class="action-status ready" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-list"></i>
                    <span>{{ count($questions) }} Questions</span>
                </div>
                </div>
                @endif
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
                <a href="#" class="btn btn-primary" id="view-recommendations">
                    <i class="fas fa-lightbulb me-2"></i>View Recommendations
                </a>
                <a href="#" class="btn btn-primary" id="print-recommendations">
                    <i class="fas fa-print me-2"></i>Print Recommendations
                </a>
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
        background: linear-gradient(135deg, #059669 0%, #10b981 25%, #34d399 50%, #6ee7b7 75%, #a7f3d0 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        margin-top: 0 !important;
        color: white;
        box-shadow: 0 15px 30px rgba(5, 150, 105, 0.3);
        position: relative;
        overflow: hidden;
        width: 100%;
        z-index: 1;
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

    .header-visual {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header-circle {
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
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
        margin: 0 0 1.5rem 0;
        opacity: 0.95;
        font-weight: 400;
        color: white !important;
    }

    .header-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .stat-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white !important;
    }

    .action-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        min-width: 150px;
        justify-content: center;
        color: white !important;
    }

    .progress-section {
        margin-bottom: 2rem;
    }

    .progress-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: visible;
        width: 100%;
        z-index: 1;
    }

    .progress-header h3 {
        margin: 0 0 1.5rem 0;
        color: #1f2937;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .progress-bar {
        width: 100%;
        height: 12px;
        background: #f3f4f6;
        border-radius: 10px;
        overflow: hidden;
        margin: 0 0 1rem 0;
        border: 1px solid #e5e7eb;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        transition: width 0.5s ease;
        width: 0%;
        border-radius: 10px;
    }

    .progress-text {
        font-weight: 700;
        color: #059669;
        font-size: 1rem;
        background: rgba(16, 185, 129, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        display: inline-block;
    }

    .question-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        overflow: hidden;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }

    .question-content {
        padding: 2.5rem;
    }

    .question-title {
        font-size: 1.3rem;
        color: #1f2937;
        margin: 0 0 2rem 0;
        line-height: 1.6;
        font-weight: 600;
    }

    .answer-options {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .option-item:hover {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
    }

    .option-item input[type="radio"]:checked + .option-text {
        color: #059669;
        font-weight: 600;
    }

    .option-item:has(input[type="radio"]:checked) {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
    }

    .option-item input[type="radio"] {
        width: 20px;
        height: 20px;
        accent-color: #10b981;
        cursor: pointer;
    }

    .option-text {
        font-size: 1.1rem;
        color: #374151;
        flex: 1;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .btn {
        padding: 1rem 2rem;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 2px solid #d1d5db;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #374151;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-outline-success {
        background: transparent;
        color: #10b981;
        border: 2px solid #10b981;
    }

    .btn-outline-success:hover {
        background: rgba(16, 185, 129, 0.1);
        border-color: #059669;
        color: #059669;
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
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        padding: 3rem;
        margin-bottom: 2rem;
        text-align: center;
        border: 2px solid #10b981;
        position: relative;
        overflow: hidden;
    }

    .success-message::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .success-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .success-icon {
        font-size: 5rem;
        color: #10b981;
        margin-bottom: 1.5rem;
        animation: bounce 2s ease-in-out infinite;
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

    .success-title {
        color: #059669;
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0 0 1.5rem 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .success-text {
        color: #4b5563;
        font-size: 1.3rem;
        margin: 0 0 2.5rem 0;
        line-height: 1.7;
        font-weight: 500;
    }

    .success-actions {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
        align-items: center;
    }

    .success-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 200px;
        max-width: 220px;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .success-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Highlight animation for recommendations */
    @keyframes highlight {
        0% { background-color: rgba(16, 185, 129, 0.1); }
        50% { background-color: rgba(16, 185, 129, 0.2); }
        100% { background-color: transparent; }
    }

    /* AI Recommendations Styles */
    .ai-recommendations {
        margin: 3rem 0;
        text-align: left;
    }

    .recommendation-category {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-left: 6px solid #10b981;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .recommendation-category:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }

    .recommendation-category.alert {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
    }

    .recommendation-category.planning {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
    }

    .recommendation-category.tips {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
    }

    .category-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .category-title i {
        font-size: 1.6rem;
    }

    .recommendation-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .recommendation-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 1.1rem;
        line-height: 1.7;
        color: #374151;
        font-weight: 500;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .recommendation-item:last-child {
        border-bottom: none;
    }

    .recommendation-item.urgent {
        color: #dc2626;
        font-weight: 700;
        background: rgba(239, 68, 68, 0.05);
        padding: 1rem;
        border-radius: 8px;
        margin: 0.5rem 0;
    }

    .recommendation-item.positive {
        color: #059669;
        font-weight: 700;
        background: rgba(16, 185, 129, 0.05);
        padding: 1rem;
        border-radius: 8px;
        margin: 0.5rem 0;
    }

    .recommendation-summary {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 2px solid #bae6fd;
        text-align: center;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
    }

    .summary-text {
        font-size: 1.3rem;
        color: #0c4a6e;
        font-weight: 700;
        margin: 0;
        line-height: 1.6;
    }

    /* Responsive Design */
    @media (max-width: 991.98px) {
        .questions-container {
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

        .header-visual {
            align-items: center;
            justify-content: center;
        }

        .header-circle {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }

        .page-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .questions-container {
            padding: 1.25rem 0.75rem;
        }

        .unified-header {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .header-stats {
            flex-direction: column;
            gap: 0.75rem;
        }

        .stat-badge, .action-status {
            width: 100%;
            justify-content: center;
        }

        .navigation-buttons {
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .question-content {
            padding: 1.5rem;
        }

        .question-title {
            font-size: 1.2rem;
        }

        .option-item {
            padding: 1rem;
        }

        .option-text {
            font-size: 1rem;
        }

        .success-message {
            padding: 2rem 1.5rem;
        }

        .success-title {
            font-size: 1.8rem;
        }

        .success-text {
            font-size: 1.1rem;
        }

        .success-actions {
            flex-direction: column;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .success-actions .btn {
            min-width: 250px;
            max-width: 100%;
            font-size: 0.95rem;
            padding: 1rem 1.5rem;
            text-align: center;
        }
    }

    /* Extra small screens */
    @media (max-width: 480px) {
        .success-actions {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .success-actions .btn {
            min-width: 100%;
            max-width: 100%;
            padding: 0.875rem 1rem;
            font-size: 0.9rem;
        }

        .recommendation-category {
            padding: 1.5rem;
        }

        .category-title {
            font-size: 1.2rem;
        }

        .recommendation-item {
            font-size: 1rem;
            padding: 0.75rem 0;
        }

        .summary-text {
            font-size: 1.1rem;
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
