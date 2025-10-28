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
            
            <!-- Research Evidence Section -->
            <div id="research-evidence" class="research-evidence" style="display: none;">
                <h3 class="research-title">
                    <i class="fas fa-book"></i> 📚 Research Evidence (AI-Generated References)
                </h3>
                <p class="research-intro">These research-backed sources support the recommendations above and provide scientific validation for agricultural best practices.</p>
                <div id="research-papers-container" class="research-papers-container">
                    <!-- Research papers will be populated here by JavaScript -->
                </div>
            </div>
            
            <div class="success-actions">
                <a href="{{ route('crop-progress.index') }}" class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i>View Progress
                </a>
                <a href="#" class="btn btn-primary" id="view-recommendations">
                    <i class="fas fa-lightbulb me-2"></i>View Recommendations
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
                        <div class="question-header">
                            <h3 class="question-title">{{ $question['question'] }}</h3>
                            @if(isset($question['category']))
                                <span class="question-category {{ $question['category'] }}">
                                    @if($question['category'] === 'leaf')
                                        <i class="fas fa-leaf"></i> Leaf Assessment
                                    @else
                                        <i class="fas fa-chart-line"></i> Crop Progress
                                    @endif
                                </span>
                            @endif
                        </div>
                        
                        <div class="answer-options">
                            @if($question['type'] === 'rating')
                                {{-- Rating Scale (1-5 stars) --}}
                                <div class="rating-container">
                                    @foreach($question['options'] as $value => $label)
                                        <label class="rating-option">
                                            <input type="radio" 
                                                   name="answers[{{ $questionId }}]" 
                                                   value="{{ $value }}" 
                                                   required>
                                            <div class="rating-box">
                                                <div class="rating-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $value ? 'active' : '' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="rating-label">{{ $label }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            
                            @elseif($question['type'] === 'yesno')
                                {{-- Yes/No Toggle --}}
                                <div class="yesno-container">
                                    @foreach($question['options'] as $value => $label)
                                        <label class="yesno-option {{ $value }}">
                                            <input type="radio" 
                                                   name="answers[{{ $questionId }}]" 
                                                   value="{{ $value }}" 
                                                   required>
                                            <div class="yesno-box">
                                                <i class="fas {{ $value === 'yes' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                                <span class="yesno-label">{{ $label }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            
                            @elseif($question['type'] === 'slider')
                                {{-- Slider Input --}}
                                <div class="slider-container">
                                    <div class="slider-info">
                                        <span class="slider-value-display">
                                            <span id="slider-value-{{ $questionId }}">{{ isset($question['min']) ? $question['min'] : 0 }}</span>
                                            {{ $question['unit'] ?? '' }}
                                        </span>
                                        @if(isset($question['optimal']))
                                            <span class="slider-optimal">Optimal: {{ $question['optimal'] }}</span>
                                        @endif
                                    </div>
                                    <input type="range" 
                                           class="slider-input" 
                                           name="answers[{{ $questionId }}]" 
                                           id="slider-{{ $questionId }}"
                                           min="{{ $question['min'] ?? 0 }}" 
                                           max="{{ $question['max'] ?? 100 }}" 
                                           value="{{ $question['min'] ?? 0 }}"
                                           step="1"
                                           required>
                                    <div class="slider-labels">
                                        <span>{{ $question['min'] ?? 0 }}</span>
                                        <span>{{ $question['max'] ?? 100 }}</span>
                                    </div>
                                </div>
                            
                            @else
                                {{-- Default: Radio buttons --}}
                                @foreach($question['options'] as $value => $label)
                                    <label class="option-item">
                                        <input type="radio" 
                                               name="answers[{{ $questionId }}]" 
                                               value="{{ $value }}" 
                                               required>
                                        <span class="option-text">{{ $label }}</span>
                                    </label>
                                @endforeach
                            @endif
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

    .question-header {
        margin-bottom: 2rem;
    }

    .question-title {
        font-size: 1.3rem;
        color: #1f2937;
        margin: 0 0 1rem 0;
        line-height: 1.6;
        font-weight: 600;
    }

    .question-category {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .question-category.leaf {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #86efac;
    }

    .question-category.progress {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        border: 1px solid #93c5fd;
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
        margin: 3rem 0 2rem 0;
        text-align: left;
    }

    /* Decorative section heading */
    .ai-recommendations .research-title {
        position: relative;
    }
    .ai-recommendations .research-title:after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -8px;
        width: 90px;
        height: 4px;
        background: linear-gradient(90deg, #10b981, #3b82f6);
        border-radius: 4px;
        transform: translateX(-50%);
    }
    
    /* Research Evidence Styles */
    .research-evidence {
        margin: 2rem 0 3rem 0;
        text-align: left;
    }
    
    .research-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
        justify-content: center;
    }
    
    .research-intro {
        text-align: center;
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 2rem;
        font-style: italic;
    }
    
    .research-papers-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .research-paper {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border-left: 6px solid #3b82f6;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .research-paper:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    
    .paper-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 1rem 0;
        line-height: 1.4;
    }
    
    .paper-authors {
        font-size: 0.95rem;
        color: #6b7280;
        margin: 0 0 0.5rem 0;
        font-style: italic;
    }
    
    .paper-year {
        display: inline-block;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .paper-abstract {
        font-size: 1rem;
        color: #374151;
        line-height: 1.7;
        margin: 1rem 0;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border-left: 3px solid #93c5fd;
    }
    
    .paper-citation {
        font-size: 0.9rem;
        color: #6b7280;
        margin: 1rem 0;
        padding: 0.75rem;
        background: #f3f4f6;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
    }
    
    .paper-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: rgba(59, 130, 246, 0.1);
        transition: all 0.3s ease;
        margin-top: 0.5rem;
    }
    
    .paper-link:hover {
        background: rgba(59, 130, 246, 0.2);
        transform: translateX(3px);
    }
    
    .paper-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border-radius: 50%;
        font-weight: 700;
        font-size: 1rem;
        margin-right: 1rem;
        flex-shrink: 0;
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

    /* Animated gradient borders per section */
    .recommendation-category.alert { border-left-color: #ef4444; }
    .recommendation-category.planning { border-left-color: #3b82f6; }
    .recommendation-category.tips { border-left-color: #f59e0b; }

    /* Title embellishments */
    .category-title {
        position: relative;
        padding-left: 0.5rem;
    }
    .category-title:before {
        content: '';
        position: absolute;
        left: 0;
        bottom: -6px;
        width: 48px;
        height: 3px;
        background: currentColor;
        opacity: 0.25;
        border-radius: 3px;
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
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.85rem 0.25rem 0.85rem 0.25rem;
        border-bottom: 1px solid #f3f4f6;
        font-size: 1.1rem;
        line-height: 1.7;
        color: #374151;
        font-weight: 500;
        opacity: 0;
        transform: translateY(8px);
        animation: rec-item-in 0.6s ease forwards;
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

    /* Number chip for steps */
    .step-chip {
        flex: 0 0 auto;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        color: white;
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 4px 10px rgba(16,185,129,0.25);
        transform: translateY(2px);
    }

    .planning .step-chip { background: linear-gradient(135deg, #3b82f6, #1d4ed8); box-shadow: 0 4px 10px rgba(59,130,246,0.25); }
    .tips .step-chip { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 10px rgba(245,158,11,0.25); }

    /* Item content wraps nicely */
    .recommendation-item .item-text {
        flex: 1 1 auto;
    }

    /* Entrance animations */
    @keyframes rec-item-in {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes card-pop {
        0% { transform: scale(0.98); opacity: 0; }
        60% { transform: scale(1.01); opacity: 1; }
        100% { transform: scale(1); }
    }

    .recommendation-category { animation: card-pop 0.5s ease both; }

    /* Glow on hover for emphasis */
    .recommendation-category:hover { box-shadow: 0 16px 40px rgba(16,185,129,0.15), 0 0 0 1px rgba(16,185,129,0.08) inset; }
    .recommendation-category.planning:hover { box-shadow: 0 16px 40px rgba(59,130,246,0.15), 0 0 0 1px rgba(59,130,246,0.08) inset; }
    .recommendation-category.tips:hover { box-shadow: 0 16px 40px rgba(245,158,11,0.15), 0 0 0 1px rgba(245,158,11,0.08) inset; }

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

    /* Rating Scale Styles */
    .rating-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .rating-option {
        cursor: pointer;
    }

    .rating-option input[type="radio"] {
        display: none;
    }

    .rating-box {
        padding: 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #fafafa;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .rating-box:hover {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.05);
        transform: translateY(-1px);
    }

    .rating-option input[type="radio"]:checked + .rating-box {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.15);
    }

    .rating-stars {
        display: flex;
        gap: 0.25rem;
        font-size: 1.2rem;
    }

    .rating-stars .fa-star {
        color: #d1d5db;
        transition: color 0.2s ease;
    }

    .rating-stars .fa-star.active {
        color: #fbbf24;
    }

    .rating-option input[type="radio"]:checked + .rating-box .fa-star.active {
        color: #f59e0b;
    }

    .rating-label {
        font-size: 1.05rem;
        color: #374151;
        font-weight: 500;
        flex: 1;
    }

    /* Yes/No Toggle Styles */
    .yesno-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .yesno-option {
        cursor: pointer;
    }

    .yesno-option input[type="radio"] {
        display: none;
    }

    .yesno-box {
        padding: 1.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #fafafa;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
        min-height: 120px;
        justify-content: center;
    }

    .yesno-box i {
        font-size: 2.5rem;
        transition: all 0.3s ease;
    }

    .yesno-option.yes .yesno-box i {
        color: #9ca3af;
    }

    .yesno-option.no .yesno-box i {
        color: #9ca3af;
    }

    .yesno-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .yesno-option.yes input[type="radio"]:checked + .yesno-box {
        border-color: #10b981;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    }

    .yesno-option.yes input[type="radio"]:checked + .yesno-box i {
        color: #059669;
        transform: scale(1.1);
    }

    .yesno-option.no input[type="radio"]:checked + .yesno-box {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
    }

    .yesno-option.no input[type="radio"]:checked + .yesno-box i {
        color: #dc2626;
        transform: scale(1.1);
    }

    .yesno-label {
        font-size: 1.05rem;
        color: #374151;
        font-weight: 600;
    }

    /* Slider Styles */
    .slider-container {
        padding: 1rem 0;
    }

    .slider-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .slider-value-display {
        font-size: 1.8rem;
        font-weight: 700;
        color: #10b981;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        border: 2px solid #10b981;
    }

    .slider-optimal {
        font-size: 0.9rem;
        color: #6b7280;
        background: #f3f4f6;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
    }

    .slider-input {
        width: 100%;
        height: 12px;
        border-radius: 10px;
        background: linear-gradient(to right, #d1fae5 0%, #10b981 50%, #059669 100%);
        outline: none;
        -webkit-appearance: none;
        margin: 1rem 0;
    }

    .slider-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #10b981;
        cursor: pointer;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        transition: all 0.2s ease;
    }

    .slider-input::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
    }

    .slider-input::-moz-range-thumb {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #10b981;
        cursor: pointer;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        transition: all 0.2s ease;
    }

    .slider-input::-moz-range-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
    }

    .slider-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 600;
        margin-top: 0.5rem;
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

        .yesno-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .yesno-box {
            min-height: 100px;
        }

        .slider-value-display {
            font-size: 1.5rem;
            padding: 0.5rem 1rem;
        }

        .slider-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .rating-box {
            flex-direction: column;
            text-align: center;
        }

        .rating-stars {
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
    
    // Initialize slider listeners
    document.querySelectorAll('.slider-input').forEach(slider => {
        const questionId = slider.id.replace('slider-', '');
        const valueDisplay = document.getElementById(`slider-value-${questionId}`);
        
        slider.addEventListener('input', function() {
            valueDisplay.textContent = this.value;
        });
    });
    
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
            const q = questions[id];
            if (!q) { allAnswered = false; return; }

            if (q.type === 'slider') {
                const slider = document.querySelector(`input[name="answers[${id}]"]`);
                if (slider && slider.value !== undefined && slider.value !== null && slider.value !== '') {
                    answers[id] = slider.value;
                } else {
                    allAnswered = false;
                }
            } else {
                const selected = document.querySelector(`input[name="answers[${id}]"]:checked`);
                if (selected) {
                    answers[id] = selected.value;
                } else {
                    allAnswered = false;
                }
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
                
                // Display research papers if available
                if (data.research_papers && data.research_papers.length > 0) {
                    displayResearchPapers(data.research_papers);
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
        
        let html = '<h3 class="research-title"><i class="fas fa-brain"></i> 🌿 AI Recommendation Summary</h3>';
        
        // Add general status
        if (recommendations.general_status) {
            html += `
                <div class="recommendation-category">
                    <h4 class="category-title">
                        <i class="fas fa-info-circle"></i>
                        General Status
                    </h4>
                    <p class="recommendation-item positive" style="border: none; padding: 1rem;">${recommendations.general_status}</p>
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
        
        // Helper to render list with numbered chips and staggered animation
        const renderList = (items) => {
            return items.map((item, idx) => `
                <li class="recommendation-item" style="animation-delay: ${Math.min(idx * 90, 600)}ms;">
                    <span class="step-chip">${idx + 1}</span>
                    <span class="item-text">${item}</span>
                </li>
            `).join('');
        };

        // Add immediate actions
        if (recommendations.immediate_actions && recommendations.immediate_actions.length > 0) {
            html += `
                <div class="recommendation-category">
                    <h4 class="category-title">
                        <i class="fas fa-bolt"></i>
                        Immediate Actions
                    </h4>
                    <ul class="recommendation-list">
                        ${renderList(recommendations.immediate_actions)}
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
                        ${renderList(recommendations.weekly_plan)}
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
                        ${renderList(recommendations.long_term_tips)}
                    </ul>
                </div>
            `;
        }
        
        recommendationsContainer.innerHTML = html;

        // Reveal on scroll using IntersectionObserver
        try {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        // Unobserve after reveal
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.recommendation-category').forEach(card => {
                observer.observe(card);
            });
        } catch (e) {
            // Fallback: add in-view immediately
            document.querySelectorAll('.recommendation-category').forEach(card => card.classList.add('in-view'));
        }
    }
    
    function displayResearchPapers(papers) {
        const researchContainer = document.getElementById('research-evidence');
        const papersContainer = document.getElementById('research-papers-container');
        
        if (!papers || papers.length === 0) {
            return;
        }
        
        let html = '';
        
        papers.forEach((paper, index) => {
            html += `
                <div class="research-paper">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div class="paper-number">${index + 1}</div>
                        <div style="flex: 1;">
                            <h5 class="paper-title">${escapeHtml(paper.title)}</h5>
                            <p class="paper-authors"><strong>Authors:</strong> ${escapeHtml(paper.authors)}</p>
                            <span class="paper-year"><i class="fas fa-calendar"></i> ${escapeHtml(paper.year)}</span>
                            <div class="paper-abstract">
                                <strong>Abstract:</strong><br>
                                ${escapeHtml(paper.abstract)}
                            </div>
                            ${paper.citation ? `<div class="paper-citation"><strong>Citation:</strong> ${escapeHtml(paper.citation)}</div>` : ''}
                            ${paper.url && paper.url !== '#' ? `
                                <a href="${escapeHtml(paper.url)}" target="_blank" rel="noopener noreferrer" class="paper-link">
                                    <i class="fas fa-external-link-alt"></i>
                                    View Full Research Paper
                                </a>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        papersContainer.innerHTML = html;
        researchContainer.style.display = 'block';
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
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
