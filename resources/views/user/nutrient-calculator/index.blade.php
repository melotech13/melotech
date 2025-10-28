@extends('layouts.app')

@section('title', 'Nutrient Calculator - MeloTech')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nutrient-calculator.css') }}">
<link rel="stylesheet" href="{{ asset('css/nutrient-calculator-fixes.css') }}">
@endpush

@section('content')
<div class="nutrient-calculator-page">
    <div class="container main-container">
        <!-- Unified Header -->
        <div class="unified-header">
            <div class="header-main">
                <div class="header-left">
                    <h1 class="page-title">
                        <i class="fas fa-flask"></i>
                        AI-Powered Nutrient Calculator
                    </h1>
                    <p class="page-subtitle">Intelligent soil analysis and fertilizer recommendations powered by Mixtral AI</p>
                    <div class="header-stats">
                        <div class="stat-badge">
                            <i class="fas fa-seedling"></i>
                            <span>Soil Analysis</span>
                        </div>
                        <div class="stat-badge">
                            <i class="fas fa-brain"></i>
                            <span>AI Recommendations</span>
                        </div>
                        <div class="action-status">
                            <a href="{{ route('nutrient-calculator.history') }}" class="btn btn-history">
                                <i class="fas fa-history me-2"></i>View All Analyses
                            </a>
                        </div>
                    </div>
                </div>
                <div class="header-visual">
                    <div class="header-circle">
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card animate-fade-in">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_analyses'] }}</div>
                    <div class="stat-label">Total Analyses</div>
                </div>
            </div>
            
            <div class="stat-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['critical_issues'] }}</div>
                    <div class="stat-label">Critical Issues</div>
                </div>
            </div>
            
            <div class="stat-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['recent_analyses'] }}</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>

        <!-- Input Form Section -->
        <div class="input-section">
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-seedling"></i>
                            Soil Nutrient Input
                        </h2>
                        <p class="section-subtitle">Enter your soil test results for AI analysis</p>
                    </div>
                    
                    <form id="nutrientForm" class="nutrient-form">
                        @csrf
                        
                        <!-- NPK Inputs -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nitrogen">
                                    <i class="fas fa-leaf text-success"></i> Nitrogen (N)
                                    <span class="unit">ppm</span>
                                </label>
                                <input type="number" id="nitrogen" name="nitrogen" class="form-control" 
                                       placeholder="e.g., 75" min="0" max="1000" step="0.01" required>
                                <small class="form-hint">Optimal: 50-150 ppm</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="phosphorus">
                                    <i class="fas fa-vial text-warning"></i> Phosphorus (P)
                                    <span class="unit">ppm</span>
                                </label>
                                <input type="number" id="phosphorus" name="phosphorus" class="form-control" 
                                       placeholder="e.g., 50" min="0" max="1000" step="0.01" required>
                                <small class="form-hint">Optimal: 30-80 ppm</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="potassium">
                                    <i class="fas fa-atom text-primary"></i> Potassium (K)
                                    <span class="unit">ppm</span>
                                </label>
                                <input type="number" id="potassium" name="potassium" class="form-control" 
                                       placeholder="e.g., 200" min="0" max="1000" step="0.01" required>
                                <small class="form-hint">Optimal: 150-300 ppm</small>
                            </div>
                        </div>

                        <!-- Soil Conditions -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="soil_ph">
                                    <i class="fas fa-tint text-info"></i> Soil pH
                                </label>
                                <input type="number" id="soil_ph" name="soil_ph" class="form-control" 
                                       placeholder="e.g., 6.5" min="0" max="14" step="0.1" required>
                                <small class="form-hint">Optimal: 6.0-6.8</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="soil_moisture">
                                    <i class="fas fa-droplet text-cyan"></i> Soil Moisture
                                    <span class="unit">%</span>
                                </label>
                                <input type="number" id="soil_moisture" name="soil_moisture" class="form-control" 
                                       placeholder="e.g., 70" min="0" max="100" step="0.1" required>
                                <small class="form-hint">Optimal: 60-80%</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="growth_stage">
                                    <i class="fas fa-chart-line text-purple"></i> Growth Stage
                                </label>
                                <select id="growth_stage" name="growth_stage" class="form-control" required>
                                    <option value="">Select Stage</option>
                                    <option value="seedling">🌱 Seedling</option>
                                    <option value="vegetative">🌿 Vegetative</option>
                                    <option value="flowering">🌸 Flowering</option>
                                    <option value="fruiting">🍉 Fruiting</option>
                                    <option value="harvest">🌾 Harvest</option>
                                </select>
                                <small class="form-hint">Current crop growth stage</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="btn-container">
                            <button type="submit" class="btn-analyze" id="analyzeBtn">
                                <i class="fas fa-brain"></i>
                                <span>Analyze with AI</span>
                            </button>
                        </div>
                    </form>
                </div>
        </div>

        <!-- Results Section -->
        <div class="results-section" id="resultsSection" style="display: none;">
                <div class="section-card results-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-robot"></i>
                            AI Analysis Results
                            <span class="ai-badge">💡 AI Powered</span>
                        </h2>
                        <p class="section-subtitle">Intelligent recommendations from Mixtral AI</p>
                    </div>
                    
                    <!-- Loading State -->
                    <div id="loadingState" class="loading-state">
                        <div class="loading-animation">
                            <div class="loading-circle"></div>
                            <div class="loading-circle"></div>
                            <div class="loading-circle"></div>
                        </div>
                        <p class="loading-text">Analyzing soil nutrients with AI...</p>
                    </div>

                    <!-- Results Content -->
                    <div id="resultsContent" class="results-content" style="display: none;">
                        <!-- This will be dynamically populated by JavaScript -->
                    </div>
                </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/nutrient-calculator.js') }}"></script>
@endsection
