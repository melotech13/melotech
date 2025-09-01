@extends('layouts.app')

@section('title', 'Crop Progress - Waiting - MeloTech')

@section('content')
<div class="crop-progress-container">
    <!-- Unified Header -->
    <div class="unified-header">
        <div class="header-main">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-clock"></i>
                    Crop Progress Update
                </h1>
                <p class="page-subtitle">Please wait until the next scheduled update date</p>
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
                    <div class="action-status" style="background: rgba(251, 191, 36, 0.2); border-color: rgba(251, 191, 36, 0.3);">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Waiting Period</span>
                    </div>
                </div>
                @endif
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Waiting Content -->
    <div class="waiting-content">
        <div class="waiting-card">
            <div class="waiting-header">
                <div class="waiting-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h2 class="waiting-title">Update Not Available Yet</h2>
                <p class="waiting-subtitle">Progress Update Temporarily Unavailable</p>
            </div>

            <div class="waiting-body">
                <p class="waiting-description">
                    You've recently updated your crop progress. To maintain accurate tracking and prevent duplicate entries, 
                    please wait until the next scheduled update date.
                </p>

                <div class="next-update-box">
                    <div class="update-label">
                        <i class="fas fa-calendar-alt"></i>
                        Next Update Date
                    </div>
                    <div class="update-date">
                        {{ $nextUpdateDate->format('l, F d, Y') }}
                    </div>
                    <div class="update-time">
                        {{ $nextUpdateDate->diffForHumans() }}
                    </div>
                    <div class="next-week-info">
                        <i class="fas fa-calendar-week"></i>
                        <strong>Next Week: Week {{ $nextWeekNumber }}</strong>
                    </div>
                </div>

                <div class="waiting-actions">
                    <a href="{{ route('crop-progress.index') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line me-2"></i>View Progress History
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Section -->
    <div class="info-section">
        <div class="info-card">
            <div class="info-header">
                <h3 class="info-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Why Wait?
                </h3>
            </div>
            <div class="info-content">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="info-text">
                            <h4>Accurate Tracking</h4>
                            <p>Weekly updates ensure consistent progress monitoring and prevent data overlap.</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="info-text">
                            <h4>Growth Patterns</h4>
                            <p>Regular intervals help identify growth patterns and optimize farming practices.</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="info-text">
                            <h4>Better Recommendations</h4>
                            <p>Consistent data collection improves AI-powered farming recommendations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@push('styles')
<style>
    /* Basic styling - matching crop-progress index exactly */
    .crop-progress-container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 1;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    /* Waiting Content */
    .waiting-content {
        margin-bottom: 2rem;
    }

    .waiting-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        width: 100%;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .waiting-header {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        padding: 2rem;
        text-align: center;
        border-bottom: 1px solid #f59e0b;
    }

    .waiting-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: white;
        border: 3px solid #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);
    }

    .waiting-icon i {
        font-size: 2rem;
        color: #d97706;
    }

    .waiting-title {
        font-size: 2rem;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    .waiting-subtitle {
        font-size: 1.1rem;
        color: #d97706;
        font-weight: 500;
        margin: 0;
    }

    .waiting-body {
        padding: 2rem;
        text-align: center;
    }

    .waiting-description {
        font-size: 1.1rem;
        color: #6b7280;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto 2rem;
    }

    .next-update-box {
        background: #fef3c7;
        border: 2px solid #f59e0b;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .update-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #d97706;
        font-weight: 600;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .update-date {
        font-size: 1.25rem;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    .update-time {
        font-size: 0.9rem;
        color: #d97706;
        font-style: italic;
    }

    .next-week-info {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(245, 158, 11, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 1rem;
        color: #92400e;
    }

    .next-week-info i {
        color: #d97706;
    }

    .waiting-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        min-width: 180px;
        justify-content: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #64748b;
        border: 2px solid #cbd5e1;
    }

    .btn-outline-secondary:hover {
        background: #64748b;
        color: white;
        border-color: #64748b;
        transform: translateY(-2px);
    }

    /* Info Section */
    .info-section {
        margin-bottom: 2rem;
    }

    .info-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        width: 100%;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .info-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .info-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-title i {
        color: #3b82f6;
    }

    .info-content {
        padding: 2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .info-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        padding: 1.5rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .info-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-icon i {
        font-size: 1.5rem;
        color: #3b82f6;
    }

    .info-text h4 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .info-text p {
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    @media (max-width: 768px) {
        .waiting-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn {
            width: 100%;
            max-width: 300px;
        }

        .next-update-box {
            max-width: 100%;
            margin: 0 1rem 2rem 1rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .waiting-header {
            padding: 1.5rem;
        }

        .waiting-body {
            padding: 1.5rem;
        }

        .waiting-title {
            font-size: 1.75rem;
        }
    }
</style>
@endpush
@endsection
