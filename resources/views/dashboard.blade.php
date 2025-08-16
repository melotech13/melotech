@extends('layouts.app')

@section('title', 'Dashboard - MeloTech')

@section('content')
<div class="dashboard-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <div class="welcome-text">
                <h1 class="welcome-title">
                    <i class="fas fa-seedling welcome-icon"></i>
                    Welcome back, {{ Auth::user()->name }}
                </h1>
                <p class="welcome-subtitle">Your watermelon farm is ready for AI-powered insights and analysis</p>
            </div>
            <div class="welcome-visual">
                <div class="welcome-circle">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Farm Information -->
    @if(Auth::user()->farms->count() > 0)
        @foreach(Auth::user()->farms as $farm)
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tractor section-icon"></i>
                    Farm Overview
                </h2>
            </div>
            
            <div class="farm-grid">
                <div class="farm-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle card-icon"></i>
                        <h3 class="card-title">Farm Details</h3>
                    </div>
                    <div class="card-content">
                        <div class="info-row">
                            <span class="info-label">Farm Name</span>
                            <span class="info-value">{{ $farm->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Variety</span>
                            <span class="info-value">{{ $farm->watermelon_variety }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Field Size</span>
                            <span class="info-value">{{ $farm->field_size }} {{ $farm->field_size_unit }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Planting Date</span>
                            <span class="info-value">{{ $farm->planting_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="farm-card">
                    <div class="card-header">
                        <i class="fas fa-chart-line card-icon"></i>
                        <h3 class="card-title">Growth Progress</h3>
                    </div>
                    <div class="card-content">
                        <div class="progress-container">
                            @php
                                $plantingDate = $farm->planting_date;
                                $currentDate = now();
                                $harvestDate = $plantingDate->copy()->addDays(80);
                                $totalDays = 80;
                                
                                // Check if planting date is in the future
                                $isFuture = $plantingDate->isAfter($currentDate);
                                
                                if ($isFuture) {
                                    // Future planting date
                                    $daysUntilPlanting = $currentDate->diffInDays($plantingDate);
                                    $progressPercentage = 0;
                                    $daysElapsed = 0;
                                    $daysRemaining = $totalDays;
                                    $growthStage = 'Not Yet Planted';
                                    $statusColor = 'secondary';
                                } else {
                                    // Past planting date - calculate real progress
                                    $daysElapsed = $plantingDate->diffInDays($currentDate);
                                    
                                    // Calculate progress with higher precision
                                    $progressPercentage = ($daysElapsed / $totalDays) * 100;
                                    
                                    // Ensure it's within bounds and round to 1 decimal
                                    $progressPercentage = min(100, max(0, $progressPercentage));
                                    $progressPercentage = round($progressPercentage, 1);
                                    
                                    // Round days to whole numbers for more accurate percentage calculation
                                    $daysElapsed = round($daysElapsed);
                                    $progressPercentage = ($daysElapsed / $totalDays) * 100;
                                    $progressPercentage = min(100, max(0, $progressPercentage));
                                    $progressPercentage = round($progressPercentage, 1);
                                    
                                    $daysRemaining = max(0, $totalDays - $daysElapsed);
                                    
                                    // Determine growth stage and status based on actual progress
                                    if ($progressPercentage >= 100) {
                                        $growthStage = 'Ready for Harvest';
                                        $statusColor = 'success';
                                    } elseif ($progressPercentage >= 75) {
                                        $growthStage = 'Fruit Development';
                                        $statusColor = 'warning';
                                    } elseif ($progressPercentage >= 50) {
                                        $growthStage = 'Flowering';
                                        $statusColor = 'info';
                                    } elseif ($progressPercentage >= 25) {
                                        $growthStage = 'Vegetative Growth';
                                        $statusColor = 'primary';
                                    } else {
                                        $growthStage = 'Early Growth';
                                        $statusColor = 'secondary';
                                    }
                                }
                            @endphp
                            <div class="growth-stage">
                                <span class="stage-badge {{ $statusColor }}">{{ $growthStage }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <span class="progress-text">{{ number_format($progressPercentage, 1) }}% Complete</span>
                        </div>
                        <div class="harvest-info">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Estimated harvest: {{ $harvestDate->format('M d, Y') }}</span>
                        </div>
                        <div class="growth-stats">
                            @if ($isFuture)
                                <div class="stat-item">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span>Days until planting: {{ round($daysUntilPlanting) }}</span>
                                </div>
                                <div class="stat-item">
                                    <i class="fas fa-hourglass-half"></i>
                                    <span>Growth period: {{ $totalDays }} days</span>
                                </div>
                            @else
                                <div class="stat-item">
                                    <i class="fas fa-hourglass-half"></i>
                                    <span>Days remaining: {{ round($daysRemaining) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    <!-- Quick Actions -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-bolt section-icon"></i>
                Quick Actions
            </h2>
        </div>
        
        <div class="actions-grid">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <h3 class="action-title">Upload Photos</h3>
                <p class="action-description">Upload crop photos for AI analysis and health assessment</p>
                <button class="action-button">
                    <i class="fas fa-upload"></i>
                    Upload Photos
                </button>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="action-title">AI Analysis</h3>
                <p class="action-description">Get AI-powered insights and growth predictions</p>
                <button class="action-button">
                    <i class="fas fa-chart-bar"></i>
                    View Analysis
                </button>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <h3 class="action-title">Weather Data</h3>
                <p class="action-description">Check weather forecasts and adjust predictions</p>
                <button class="action-button">
                    <i class="fas fa-cloud"></i>
                    Weather Info
                </button>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="action-title">Reports</h3>
                <p class="action-description">Generate comprehensive farm reports</p>
                <button class="action-button">
                    <i class="fas fa-download"></i>
                    Create Report
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-history section-icon"></i>
                Recent Activity
            </h2>
        </div>
        
        <div class="activity-card">
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-marker success"></div>
                    <div class="activity-content">
                        <h4 class="activity-title">Farm Setup Completed</h4>
                        <p class="activity-description">Your farm has been successfully configured for AI analysis</p>
                        <span class="activity-time">{{ now()->format('M d, Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-marker info"></div>
                    <div class="activity-content">
                        <h4 class="activity-title">AI System Ready</h4>
                        <p class="activity-description">AI analysis system is now active and ready to process your crop photos</p>
                        <span class="activity-time">{{ now()->subMinutes(5)->format('M d, Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-marker warning"></div>
                    <div class="activity-content">
                        <h4 class="activity-title">Weather Data Connected</h4>
                        <p class="activity-description">Local weather data integration is now active for enhanced predictions</p>
                        <span class="activity-time">{{ now()->subMinutes(10)->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.dashboard-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Welcome Section */
.welcome-section {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    margin-bottom: 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.welcome-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}

.welcome-text {
    flex: 1;
}

.welcome-title {
    font-size: 2.25rem;
    font-weight: 800;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    letter-spacing: -0.025em;
}

.welcome-icon {
    font-size: 2rem;
    color: #ffd700;
}

.welcome-subtitle {
    font-size: 1.125rem;
    opacity: 0.95;
    margin: 0;
    font-weight: 400;
    line-height: 1.6;
}

.welcome-visual {
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-circle {
    width: 90px;
    height: 90px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.welcome-circle i {
    font-size: 2.25rem;
    color: #fbbf24;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Section Styling */
.section {
    margin-bottom: 2.5rem;
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid #f1f5f9;
}

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
    color: #3b82f6;
    font-size: 1.5rem;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Farm Grid */
.farm-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 2rem;
}

.farm-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
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
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.farm-card:hover::before {
    transform: scaleX(1);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.card-icon {
    color: #3b82f6;
    font-size: 1.4rem;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    letter-spacing: -0.025em;
}

.card-content {
    color: #4a5568;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
}

.info-row:hover {
    background: #f8fafc;
    margin: 0 -0.5rem;
    padding: 0.75rem 0.5rem;
    border-radius: 8px;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
}

.info-value {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.95rem;
}

.progress-container {
    margin-bottom: 1rem;
}

.progress-bar {
    width: 100%;
    height: 14px;
    background: #e2e8f0;
    border-radius: 7px;
    overflow: hidden;
    margin-bottom: 1rem;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    border-radius: 7px;
    transition: width 0.5s ease;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.progress-text {
    font-size: 0.9rem;
    font-weight: 700;
    color: #059669;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.growth-stage {
    margin-bottom: 1rem;
    text-align: center;
}

.stage-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stage-badge.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.stage-badge.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.stage-badge.info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.stage-badge.primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
}

.stage-badge.secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.stage-badge.secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.harvest-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.harvest-info i {
    color: #3b82f6;
    font-size: 1rem;
}

.growth-stats {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
}

.stat-item i {
    color: #3b82f6;
    font-size: 0.9rem;
}

/* Actions Grid */
.actions-grid {
    display: flex;
    gap: 2rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    justify-content: center;
    flex-wrap: nowrap;
    padding: 1rem 0;
}

.action-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 1.75rem;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
    width: 220px;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.action-card:hover::before {
    transform: scaleX(1);
}

.action-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.action-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    transition: all 0.3s ease;
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.25);
}

.action-card:hover .action-icon {
    transform: scale(1.1);
}

.action-icon i {
    font-size: 1.25rem;
    color: white;
}

.action-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    letter-spacing: -0.025em;
}

.action-description {
    color: #64748b;
    margin-bottom: 1.5rem;
    line-height: 1.5;
    font-size: 0.9rem;
    flex-grow: 1;
    font-weight: 500;
}

.action-button {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    border: none;
    padding: 0.6rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.875rem;
    width: auto;
    min-width: 130px;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.25);
}

.action-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.35);
}

/* Activity Section */
.activity-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

.activity-list {
    position: relative;
}

.activity-list::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}

.activity-item {
    position: relative;
    padding-left: 2.5rem;
    margin-bottom: 1.5rem;
}

.activity-item:last-child {
    margin-bottom: 0;
}

.activity-marker {
    position: absolute;
    left: 11px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 0 0 2px #e2e8f0;
}

.activity-marker.success {
    background: #48bb78;
}

.activity-marker.info {
    background: #4299e1;
}

.activity-marker.warning {
    background: #ed8936;
}

.activity-content {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid #3b82f6;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
}

.activity-content:hover {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    transform: translateX(4px);
}

.activity-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
    letter-spacing: -0.025em;
}

.activity-description {
    color: #64748b;
    margin-bottom: 0.75rem;
    line-height: 1.5;
    font-weight: 500;
}

.activity-time {
    font-size: 0.875rem;
    color: #94a3b8;
    font-weight: 600;
    background: #f1f5f9;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    display: inline-block;
}

/* Custom Scrollbar for Actions Grid */
.actions-grid::-webkit-scrollbar {
    height: 6px;
}

.actions-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.actions-grid::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.actions-grid::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Action Card Hover Effects */
.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.action-card:hover .action-icon {
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .welcome-section {
        padding: 1.5rem 1rem;
    }
    
    .welcome-title {
        font-size: 1.75rem;
    }
    
    .welcome-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .welcome-circle {
        width: 70px;
        height: 70px;
    }
    
    .welcome-circle i {
        font-size: 1.75rem;
    }
    
    .farm-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        gap: 1rem;
        justify-content: flex-start;
    }
    
    .action-card {
        width: 180px;
        min-height: 160px;
        padding: 1.25rem;
    }
    
    .section-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .welcome-title {
        font-size: 1.5rem;
    }
    
    .welcome-subtitle {
        font-size: 0.9rem;
    }
    
    .farm-card {
        padding: 1.25rem;
    }
    
    .action-card {
        width: 160px;
        min-height: 140px;
        padding: 1rem;
    }
    
    .action-icon {
        width: 40px;
        height: 40px;
    }
    
    .action-icon i {
        font-size: 1rem;
    }
    
    .action-title {
        font-size: 0.9rem;
    }
    
    .action-description {
        font-size: 0.8rem;
    }
    
    .action-button {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
        min-width: 100px;
    }
}
</style>
@endpush
@endsection
