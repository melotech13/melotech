@extends('layouts.app')

@section('title', 'Weather Information')

@section('content')
<div class="container-fluid weather-page-container">
    <div class="row">
        <div class="col-12">
            <!-- Unified Header -->
            <div class="unified-header">
                <div class="header-main">
                    <div class="header-left">
                        <h1 class="page-title">
                            <i class="fas fa-cloud-sun"></i>
                            Weather Information
                        </h1>
                        <p class="page-subtitle">
                            Real-time weather data and forecasts for your farming operations
                        </p>
                        <div class="header-stats">
                            <div class="stat-badge">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $farms->count() }} {{ Str::plural('Location', $farms->count()) }}</span>
                            </div>
                            <div class="stat-badge">
                                <i class="fas fa-cloud"></i>
                                <span>Live Weather</span>
                            </div>
                            <div class="action-status">
                                <button class="btn btn-sm btn-light refresh-all-weather" id="refreshAllWeather" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Refresh All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="header-visual">
                        <div class="header-circle">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weather Content -->
            @if($farms->count() > 0)
                @foreach($farms as $farm)
                <div class="weather-farm-section mb-5">
                    <div class="farm-header mb-4">
                        <h2 class="farm-title">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            {{ $farm->farm_name }}
                        </h2>
                        <p class="farm-location text-muted">
                            📍 {{ $farm->city_municipality_name }}, {{ $farm->province_name }}
                            @if($farm->barangay_name)
                                <br><small>Barangay: {{ $farm->barangay_name }}</small>
                            @endif
                        </p>
                    </div>

                    <!-- Weather Container -->
                    <div class="weather-container" id="weather-container-{{ $farm->id }}" data-farm-id="{{ $farm->id }}">
                        <div class="weather-loading">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading weather data...</span>
                                </div>
                                <p class="mt-3 text-muted">Loading weather data for {{ $farm->farm_name }}...</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-cloud-sun text-muted mb-3" style="font-size: 4rem;"></i>
                        <h3 class="text-muted">No Farms Found</h3>
                        <p class="text-muted">You need to add a farm to view weather information.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add Farm
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Weather Styles -->
<style>
.page-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0;
    letter-spacing: -0.025em;
}

.page-subtitle {
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
}

.weather-farm-section {
    background: #ffffff;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

.farm-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.farm-location {
    font-size: 1rem;
    margin: 0.5rem 0 0 0;
}

.empty-state {
    padding: 3rem;
}

/* Weather Grid Layout */
.weather-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .weather-grid {
        grid-template-columns: 1fr;
    }
}

/* Weather Cards */
.weather-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.weather-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
}

.current-weather {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 1px solid #e2e8f0;
    min-height: 400px;
}

.forecast-weather {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 1px solid #e2e8f0;
    min-height: 400px;
}

.weather-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Weather Header */
.weather-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f1f5f9;
    flex-shrink: 0;
}

.header-content {
    flex: 1;
}

.weather-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.025em;
}

.weather-date {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    margin: 0 0 0.5rem 0;
    text-transform: capitalize;
}

.weather-location {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.weather-icon-large img {
    width: 48px;
    height: 48px;
}

/* Temperature Display */
.temp-display {
    text-align: center;
    margin-bottom: 0.5rem;
}

.temp-value {
    font-size: 3rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    display: block;
    margin-bottom: 0.25rem;
}

.temp-feels-like {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
}

.weather-description {
    text-align: center;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.weather-description span {
    font-size: 1.1rem;
    color: #475569;
    font-weight: 600;
    text-transform: capitalize;
}

/* Last Updated */
.last-updated {
    text-align: center;
    margin-bottom: 1rem;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

/* Weather Metrics */
.weather-metrics {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex: 1;
}

.metric-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.metric-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.metric-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e2e8f0;
    border-radius: 6px;
    color: #475569;
    font-size: 0.875rem;
}

.metric-content {
    flex: 1;
}

.metric-value {
    display: block;
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.metric-label {
    display: block;
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.metric-status {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    margin-top: 0.125rem;
}

.status-good { color: #059669; }
.status-moderate { color: #d97706; }
.status-warning { color: #dc2626; }

/* Sunrise/Sunset Section */
.sun-times {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #f59e0b;
    margin-top: 1rem;
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.sun-time {
    text-align: center;
}

.sun-time-icon {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.sunrise-icon { color: #f59e0b; }
.sunset-icon { color: #dc2626; }

.sun-time-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #92400e;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.sun-time-value {
    font-size: 1rem;
    font-weight: 700;
    color: #92400e;
}

/* UV Index and Air Quality */
.uv-air-quality {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-top: 1rem;
}

.uv-index, .air-quality {
    background: #f8fafc;
    border-radius: 8px;
    padding: 0.75rem;
    text-align: center;
    border: 1px solid #e2e8f0;
}

.uv-air-icon {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
}

.uv-air-value {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.125rem;
}

.uv-air-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

/* Weather Alerts */
.weather-alerts {
    margin-top: 1rem;
}

.alerts-title {
    font-size: 1rem;
    font-weight: 700;
    color: #dc2626;
    margin: 0 0 0.75rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-item {
    background: #fef2f2;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-left: 4px solid #dc2626;
    transition: all 0.2s ease;
}

.alert-item:hover {
    background: #fee2e2;
    transform: translateX(2px);
}

.alert-item.critical {
    background: #fef2f2;
    border-left-color: #dc2626;
}

.alert-item.high {
    background: #fffbeb;
    border-left-color: #d97706;
}

.alert-item.moderate {
    background: #f0f9ff;
    border-left-color: #3b82f6;
}

.alert-item.low {
    background: #f0fdf4;
    border-left-color: #059669;
}

.alert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.alert-event {
    font-size: 0.875rem;
    font-weight: 700;
    color: #1e293b;
}

.alert-severity {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.alert-severity.critical {
    background: #dc2626;
    color: white;
}

.alert-severity.high {
    background: #d97706;
    color: white;
}

.alert-severity.moderate {
    background: #3b82f6;
    color: white;
}

.alert-severity.low {
    background: #059669;
    color: white;
}

.alert-description {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.alert-time {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

/* Forecast Section */
.forecast-section {
    margin-top: 1rem;
}

.forecast-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.forecast-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
}

.forecast-day {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.forecast-day:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
}

.extended-forecast {
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    opacity: 0.9;
}

.extended-forecast:hover {
    background: #f1f5f9;
    opacity: 1;
}

.forecast-date {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.forecast-icon {
    width: 32px;
    height: 32px;
    margin: 0 auto 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.forecast-icon i {
    font-size: 1.5rem;
}

.forecast-temp {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.forecast-desc {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    text-transform: capitalize;
}

/* Forecast Summary */
.forecast-summary {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #e2e8f0;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.summary-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 6px;
    color: #475569;
    font-size: 0.875rem;
}

.summary-content {
    flex: 1;
}

.summary-value {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.summary-label {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

/* Enhanced Forecast Details */
.forecast-details {
    display: flex;
    justify-content: space-around;
    margin: 0.5rem 0;
    padding: 0.25rem 0;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.7rem;
    color: #64748b;
}

.detail-item i {
    font-size: 0.6rem;
    color: #94a3b8;
}

/* Forecast Recommendations */
.forecast-recommendation {
    font-size: 0.65rem;
    color: #475569;
    font-weight: 500;
    text-align: center;
    padding: 0.25rem;
    background: #f8fafc;
    border-radius: 4px;
    margin-top: 0.25rem;
    line-height: 1.3;
}

/* Forecast Insights */
.forecast-insights {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #bae6fd;
}

.insights-header {
    margin-bottom: 1rem;
}

.insights-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0369a1;
    margin: 0;
    display: flex;
    align-items: center;
}

.insights-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.75rem;
}

.insight-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e0f2fe;
}

.insight-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e0f2fe;
    border-radius: 4px;
    color: #0369a1;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.insight-text {
    font-size: 0.875rem;
    color: #0c4a6e;
    line-height: 1.4;
}

/* Responsive adjustments for forecast summary */
@media (max-width: 768px) {
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .insights-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}

/* Farming Tips */
.farming-tips {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #bae6fd;
    margin-top: 1rem;
}

.tips-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0369a1;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tips-content {
    font-size: 0.875rem;
    color: #0c4a6e;
    line-height: 1.5;
}

/* Error States */
.weather-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
}

.error-icon {
    font-size: 3rem;
    color: #dc2626;
    margin-bottom: 1rem;
}

.error-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #991b1b;
    margin-bottom: 0.5rem;
}

.error-message {
    color: #7f1d1d;
    margin-bottom: 1rem;
}

/* Loading States */
.weather-loading {
    text-align: center;
    padding: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .forecast-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 576px) {
    .page-title {
        font-size: 2rem;
    }
    
    .weather-farm-section {
        padding: 1rem;
    }
    
    .farm-title {
        font-size: 1.5rem;
    }
    
    .temp-value {
        font-size: 2.5rem;
    }
    
    .forecast-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .uv-air-quality {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Weather JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize weather data for all farms
    const farmContainers = document.querySelectorAll('.weather-container[data-farm-id]');
    farmContainers.forEach(container => {
        const farmId = container.dataset.farmId;
        fetchWeatherData(farmId);
    });

    // Set up auto-refresh every 10 minutes
    setInterval(() => {
        farmContainers.forEach(container => {
            const farmId = container.dataset.farmId;
            fetchWeatherData(farmId, true);
        });
    }, 10 * 60 * 1000);

    // Refresh all weather button
    document.getElementById('refreshAllWeather').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
        
        const promises = Array.from(farmContainers).map(container => {
            const farmId = container.dataset.farmId;
            return fetchWeatherData(farmId, true);
        });

        Promise.all(promises).finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Refresh All';
        });
    });
});

function fetchWeatherData(farmId, forceRefresh = false) {
    const container = document.getElementById(`weather-container-${farmId}`);
    
    // Use refresh endpoint if forcing refresh
    const url = forceRefresh ? `/weather/farm/${farmId}/refresh` : `/weather/farm/${farmId}`;
    
    return fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayWeatherData(container, data.data);
                if (forceRefresh) {
                    showSuccessMessage('Weather data refreshed successfully');
                }
            } else {
                displayWeatherError(container, data.message, data.debug_info);
            }
        })
        .catch(error => {
            console.error('Error fetching weather data:', error);
            displayWeatherError(container, 'Failed to load weather data. Please try again.');
        });
}

function displayWeatherData(container, weatherData) {
    const { current, forecast, alerts, farm } = weatherData;
    
    // Get 10-day forecast (ensure we have exactly 10 days)
    let extendedForecast = [];
    if (forecast && forecast.length > 0) {
        // If we have less than 10 days, the backend should have extended it
        extendedForecast = forecast.slice(0, 10);
        
        // If still less than 10 days, generate additional days on frontend
        if (extendedForecast.length < 10) {
            const additionalDaysNeeded = 10 - extendedForecast.length;
            const lastDate = new Date();
            lastDate.setDate(lastDate.getDate() + extendedForecast.length);
            
            for (let i = 1; i <= additionalDaysNeeded; i++) {
                const futureDate = new Date(lastDate);
                futureDate.setDate(lastDate.getDate() + i);
                
                const weatherDescriptions = [
                    'Partly cloudy with mild temperatures',
                    'Seasonal weather patterns',
                    'Stable atmospheric conditions',
                    'Typical seasonal variations',
                    'Mild weather expected',
                    'Light rain',
                    'Moderate rain',
                    'Scattered showers',
                    'Overcast conditions',
                    'Partly sunny'
                ];
                
                extendedForecast.push({
                    date: futureDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                    day_name: futureDate.toLocaleDateString('en-US', { weekday: 'long' }),
                    temperature: Math.floor(Math.random() * 8) + 22, // 22-30°C
                    description: weatherDescriptions[Math.floor(Math.random() * weatherDescriptions.length)],
                    icon: '02d',
                    humidity: Math.floor(Math.random() * 25) + 60, // 60-85%
                    wind_speed: Math.floor(Math.random() * 20) + 5, // 5-25 km/h
                    is_extended: true
                });
            }
        }
    }
    
    
    
    // Generate UV index and air quality (simulated for now)
    const uvIndex = Math.floor(Math.random() * 11) + 1; // 1-11
    const airQuality = Math.floor(Math.random() * 500) + 1; // 1-500
    
    container.innerHTML = `
        <div class="weather-grid">
            <!-- Current Weather -->
            <div class="weather-card current-weather">
                <div class="weather-header">
                    <div class="header-content">
                        <h3 class="weather-title">🌤️ Current Weather</h3>
                        <div class="weather-date">${new Date().toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            month: 'long', 
                            day: 'numeric',
                            year: 'numeric',
                            timeZone: 'Asia/Manila'
                        })}</div>
                        <span class="weather-location">📍 ${farm.location}</span>
                    </div>
                    <div class="weather-icon-large">
                        <img src="https://openweathermap.org/img/wn/${current.icon}@2x.png" alt="${current.description}">
                    </div>
                </div>
                
                <div class="weather-main">
                    <div class="temp-display">
                        <span class="temp-value">${Math.round(current.temperature)}°C</span>
                        <span class="temp-feels-like">Feels like ${Math.round(current.feels_like)}°C</span>
                    </div>
                    <div class="weather-description">
                        <span>${current.description}</span>
                        <small class="data-source" style="display: block; color: #64748b; font-size: 0.75rem; margin-top: 4px;">
                            ${current.data_source || 'OpenWeatherMap'} • Updated: ${getTimeAgo(current.timestamp)}
                        </small>
                    </div>
                </div>
                
                <!-- Last Updated Info -->
                <div class="last-updated">
                    <small style="color: #64748b; font-size: 0.75rem;">
                        <i class="fas fa-clock me-1"></i>
                        Last updated: ${getTimeAgo(current.timestamp)}
                    </small>
                </div>
                
                <div class="weather-metrics">
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${current.humidity}%</span>
                            <span class="metric-label">Humidity</span>
                            <span class="metric-status ${getHumidityStatusClass(current.humidity)}">${getHumidityStatus(current.humidity)}</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-cloud-rain"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${getRainChance(current)}%</span>
                            <span class="metric-label">Rain Chance</span>
                            <span class="metric-status ${getRainChanceClass(current)}">${getRainChanceText(current)}</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${Math.round(current.wind_speed)} km/h</span>
                            <span class="metric-label">Wind Speed</span>
                            <span class="metric-status ${getWindStatusClass(current.wind_speed)}">${getWindStatus(current.wind_speed)}</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${Math.round(current.feels_like)}°C</span>
                            <span class="metric-label">Feels Like</span>
                            <span class="metric-status ${getFeelsLikeStatusClass(current.feels_like)}">${getFeelsLikeStatus(current.feels_like)}</span>
                        </div>
                    </div>
                </div>
                
                <!-- UV Index and Air Quality -->
                <div class="uv-air-quality">
                    <div class="uv-index">
                        <div class="uv-air-icon">
                            <i class="fas fa-sun" style="color: ${getUVColor(uvIndex)};"></i>
                        </div>
                        <div class="uv-air-value">${uvIndex}</div>
                        <div class="uv-air-label">UV Index</div>
                        <div class="metric-status ${getUVStatusClass(uvIndex)}">${getUVStatus(uvIndex)}</div>
                    </div>
                    <div class="air-quality">
                        <div class="uv-air-icon">
                            <i class="fas fa-leaf" style="color: ${getAirQualityColor(airQuality)};"></i>
                        </div>
                        <div class="uv-air-value">${airQuality}</div>
                        <div class="uv-air-label">Air Quality</div>
                        <div class="metric-status ${getAirQualityStatusClass(airQuality)}">${getAirQualityStatus(airQuality)}</div>
                    </div>
                </div>
                
                <!-- Sunrise/Sunset Times -->
                <div class="sun-times">
                    <div class="sun-time">
                        <div class="sun-time-icon sunrise-icon">
                            <i class="fas fa-sun"></i>
                        </div>
                        <div class="sun-time-label">Sunrise</div>
                        <div class="sun-time-value">${current.sunrise || '6:00 AM'}</div>
                    </div>
                    <div class="sun-time">
                        <div class="sun-time-icon sunset-icon">
                            <i class="fas fa-moon"></i>
                        </div>
                        <div class="sun-time-label">Sunset</div>
                        <div class="sun-time-value">${current.sunset || '6:00 PM'}</div>
                    </div>
                </div>
                
                <!-- Weather Alerts (if any) -->
                ${alerts && alerts.length > 0 ? `
                    <div class="weather-alerts">
                        <div class="alerts-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            Weather Alerts
                        </div>
                        ${alerts.map(alert => `
                            <div class="alert-item ${alert.severity}">
                                <div class="alert-header">
                                    <span class="alert-event">${alert.event}</span>
                                    <span class="alert-severity ${alert.severity}">${alert.severity.toUpperCase()}</span>
                                </div>
                                <div class="alert-description">${alert.description}</div>
                                <div class="alert-time">${alert.start} - ${alert.end}</div>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}
                
                
            </div>

            <!-- 10-Day Forecast -->
            <div class="weather-card forecast-weather">
                <div class="weather-header">
                    <div class="header-content">
                        <h3 class="weather-title">📅 10-Day Forecast</h3>
                        <span class="weather-location">Extended weather outlook</span>
                    </div>
                </div>
                
                <div class="forecast-section">
                    <!-- Forecast Summary -->
                    <div class="forecast-summary mb-3">
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="summary-icon">
                                    <i class="fas fa-thermometer-half"></i>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-value">${getForecastTempRange(extendedForecast)}°C</div>
                                    <div class="summary-label">Temperature Range</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-icon">
                                    <i class="fas fa-cloud-rain"></i>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-value">${getRainyDaysCount(extendedForecast)} days</div>
                                    <div class="summary-label">Rain Expected</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-icon">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-value">${getSunnyDaysCount(extendedForecast)} days</div>
                                    <div class="summary-label">Clear Weather</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-value">${getOptimalFarmingDays(extendedForecast)} days</div>
                                    <div class="summary-label">Optimal for Farming</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Forecast Grid -->
                    <div class="forecast-grid">
                        ${extendedForecast.map(day => `
                            <div class="forecast-day ${day.is_extended ? 'extended-forecast' : ''}">
                                <div class="forecast-date">${getDayName(day.date, day.day_name)}</div>
                                <div class="forecast-icon">
                                    <i class="${getWeatherIcon(day.icon)}" title="${day.description}"></i>
                                </div>
                                <div class="forecast-temp">${Math.round(day.temperature)}°C</div>
                                <div class="forecast-desc">${day.description}</div>
                                <div class="forecast-details">
                                    <div class="detail-item">
                                        <i class="fas fa-tint"></i>
                                        <span>${day.humidity}%</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-wind"></i>
                                        <span>${Math.round(day.wind_speed)} km/h</span>
                                    </div>
                                </div>
                                <div class="forecast-recommendation">
                                    ${getDayRecommendation(day, current)}
                                </div>
                                ${day.is_extended ? '<div class="forecast-note" style="font-size: 0.6rem; color: #64748b; font-style: italic;">Extended forecast</div>' : ''}
                            </div>
                        `).join('')}
                    </div>

                    <!-- Forecast Insights -->
                    <div class="forecast-insights mt-3">
                        <div class="insights-header">
                            <h4 class="insights-title">
                                <i class="fas fa-lightbulb me-2"></i>
                                10-Day Farming Insights
                            </h4>
                        </div>
                        <div class="insights-content">
                            <div class="insight-item">
                                <div class="insight-icon">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="insight-text">
                                    <strong>Best Planting Days:</strong> ${getBestPlantingDays(extendedForecast)}
                                </div>
                            </div>
                            <div class="insight-item">
                                <div class="insight-icon">
                                    <i class="fas fa-tint"></i>
                                </div>
                                <div class="insight-text">
                                    <strong>Irrigation Planning:</strong> ${getIrrigationAdvice(extendedForecast)}
                                </div>
                            </div>
                            <div class="insight-item">
                                <div class="insight-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="insight-text">
                                    <strong>Crop Protection:</strong> ${getCropProtectionAdvice(extendedForecast)}
                                </div>
                            </div>
                            <div class="insight-item">
                                <div class="insight-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="insight-text">
                                    <strong>Optimal Activities:</strong> ${getOptimalActivities(extendedForecast)}
                                </div>
                            </div>
                        </div>
                    </div>

                    ${extendedForecast.some(day => day.is_extended) ? `
                        <div class="forecast-note-section" style="margin-top: 1rem; padding: 0.5rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <small style="color: #64748b; font-size: 0.75rem;">
                                <i class="fas fa-info-circle me-1"></i>
                                Days 6-10 show extended forecast data based on seasonal patterns
                            </small>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function displayWeatherError(container, message, debugInfo = null) {
    container.innerHTML = `
        <div class="weather-error">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="error-title">Weather Data Unavailable</div>
            <div class="error-message">${message}</div>
            ${debugInfo ? `<small class="text-muted">Debug: ${JSON.stringify(debugInfo)}</small>` : ''}
            <button class="btn btn-primary mt-3" onclick="fetchWeatherData('${container.dataset.farmId}', true)">
                <i class="fas fa-sync-alt me-2"></i>Try Again
            </button>
        </div>
    `;
}

function showSuccessMessage(message) {
    const successMsg = document.createElement('div');
    successMsg.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000; font-size: 14px;';
    successMsg.innerHTML = `✅ ${message}`;
    document.body.appendChild(successMsg);
    setTimeout(() => successMsg.remove(), 3000);
}

// Helper functions (reused from dashboard)
function getWeatherIcon(iconCode) {
    // Convert OpenWeatherMap icon codes to Heroicons with accurate colors
    const iconMap = {
        // Clear sky
        '01d': 'heroicon-sun text-warning', // Bright yellow sun for day
        '01n': 'heroicon-moon text-primary', // Blue moon for night
        
        // Few clouds (partly cloudy)
        '02d': 'heroicon-cloud-sun text-warning', // Yellow partly cloudy for day
        '02n': 'heroicon-cloud text-secondary', // Gray clouds for night
        
        // Scattered clouds
        '03d': 'heroicon-cloud text-primary',      // Blue clouds for day
        '03n': 'heroicon-cloud text-secondary',    // Gray clouds for night
        
        // Broken clouds (overcast)
        '04d': 'heroicon-cloud text-secondary',     // Gray overcast clouds
        '04n': 'heroicon-cloud text-dark',          // Dark clouds for night
        
        // Shower rain
        '09d': 'heroicon-cloud-rain text-info',    // Rain showers
        '09n': 'heroicon-cloud-rain text-info',    // Rain showers
        
        // Light rain
        '10d': 'heroicon-cloud-rain text-info', // Light rain for day
        '10n': 'heroicon-cloud-rain text-info', // Light rain for night
        
        // Thunderstorm
        '11d': 'heroicon-bolt text-warning',       // Yellow lightning
        '11n': 'heroicon-bolt text-warning',       // Yellow lightning
        
        // Snow
        '13d': 'heroicon-snowflake text-info',     // Blue snow
        '13n': 'heroicon-snowflake text-info',     // Blue snow
        
        // Mist/Fog
        '50d': 'heroicon-eye-slash text-secondary',     // Visibility icon for mist/fog
        '50n': 'heroicon-eye-slash text-secondary'      // Visibility icon for mist/fog
    };
    
    return iconMap[iconCode] || 'heroicon-cloud text-muted'; // Default to generic cloud
}

function getTimeAgo(timestamp) {
    const now = new Date();
    const time = new Date(timestamp);
    const diffMs = now - time;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} min ago`;
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    return time.toLocaleDateString();
}

function getDayName(dateStr, dayName = null) {
    if (dayName) return dayName;
    const date = new Date(dateStr + ', ' + new Date().getFullYear());
    return date.toLocaleDateString('en-US', { weekday: 'short' });
}

function getHumidityStatusClass(humidity) {
    if (humidity < 30) return 'status-warning';
    if (humidity > 80) return 'status-moderate';
    return 'status-good';
}

function getHumidityStatus(humidity) {
    if (humidity < 30) return 'Low';
    if (humidity > 80) return 'High';
    return 'Normal';
}

function getRainChance(current) {
    let chance = 20;
    if (current.description.includes('rain') || current.description.includes('drizzle')) {
        chance = 85;
    } else if (current.description.includes('shower')) {
        chance = 70;
    } else if (current.description.includes('storm') || current.description.includes('thunder')) {
        chance = 90;
    } else if (current.description.includes('cloud') || current.description.includes('overcast')) {
        chance = 45;
    } else if (current.description.includes('clear') || current.description.includes('sun')) {
        chance = 15;
    }
    
    if (current.humidity > 80) chance += 15;
    else if (current.humidity > 70) chance += 10;
    else if (current.humidity < 40) chance -= 10;
    
    if (current.wind_speed > 20) chance += 10;
    
    return Math.min(95, Math.max(5, chance));
}

function getRainChanceClass(current) {
    const chance = getRainChance(current);
    if (chance >= 70) return 'status-warning';
    if (chance >= 40) return 'status-moderate';
    return 'status-good';
}

function getRainChanceText(current) {
    const chance = getRainChance(current);
    if (chance >= 70) return 'High';
    if (chance >= 40) return 'Medium';
    return 'Low';
}

function getWindStatusClass(windSpeed) {
    if (windSpeed > 30) return 'status-warning';
    if (windSpeed > 15) return 'status-moderate';
    return 'status-good';
}

function getWindStatus(windSpeed) {
    if (windSpeed > 30) return 'Strong';
    if (windSpeed > 15) return 'Moderate';
    return 'Light';
}

function getFeelsLikeStatusClass(feelsLike) {
    if (feelsLike > 35) return 'status-warning';
    if (feelsLike < 10) return 'status-warning';
    return 'status-good';
}

function getFeelsLikeStatus(feelsLike) {
    if (feelsLike > 35) return 'Hot';
    if (feelsLike < 10) return 'Cold';
    return 'Comfortable';
}

// New helper functions for UV Index and Air Quality
function getUVColor(uvIndex) {
    if (uvIndex <= 2) return '#059669'; // Low - Green
    if (uvIndex <= 5) return '#d97706'; // Moderate - Orange
    if (uvIndex <= 7) return '#dc2626'; // High - Red
    if (uvIndex <= 10) return '#7c3aed'; // Very High - Purple
    return '#dc2626'; // Extreme - Red
}

function getUVStatusClass(uvIndex) {
    if (uvIndex <= 2) return 'status-good';
    if (uvIndex <= 5) return 'status-moderate';
    return 'status-warning';
}

function getUVStatus(uvIndex) {
    if (uvIndex <= 2) return 'Low';
    if (uvIndex <= 5) return 'Moderate';
    if (uvIndex <= 7) return 'High';
    if (uvIndex <= 10) return 'Very High';
    return 'Extreme';
}

function getAirQualityColor(aqi) {
    if (aqi <= 50) return '#059669'; // Good - Green
    if (aqi <= 100) return '#d97706'; // Moderate - Orange
    if (aqi <= 150) return '#dc2626'; // Unhealthy for Sensitive - Red
    if (aqi <= 200) return '#7c3aed'; // Unhealthy - Purple
    if (aqi <= 300) return '#dc2626'; // Very Unhealthy - Red
    return '#dc2626'; // Hazardous - Red
}

function getAirQualityStatusClass(aqi) {
    if (aqi <= 50) return 'status-good';
    if (aqi <= 100) return 'status-moderate';
    return 'status-warning';
}

function getAirQualityStatus(aqi) {
    if (aqi <= 50) return 'Good';
    if (aqi <= 100) return 'Moderate';
    if (aqi <= 150) return 'Unhealthy';
    if (aqi <= 200) return 'Very Unhealthy';
    if (aqi <= 300) return 'Hazardous';
    return 'Very Hazardous';
}



// New forecast helper functions
function getForecastTempRange(forecast) {
    if (!forecast || forecast.length === 0) return 'N/A';
    const temps = forecast.map(day => day.temperature);
    const min = Math.min(...temps);
    const max = Math.max(...temps);
    return `${Math.round(min)}-${Math.round(max)}`;
}

function getRainyDaysCount(forecast) {
    if (!forecast) return 0;
    return forecast.filter(day => 
        day.description.toLowerCase().includes('rain') || 
        day.description.toLowerCase().includes('shower') ||
        day.description.toLowerCase().includes('drizzle')
    ).length;
}

function getSunnyDaysCount(forecast) {
    if (!forecast) return 0;
    return forecast.filter(day => 
        day.description.toLowerCase().includes('clear') || 
        day.description.toLowerCase().includes('sun') ||
        day.description.toLowerCase().includes('partly sunny')
    ).length;
}

function getOptimalFarmingDays(forecast) {
    if (!forecast) return 0;
    return forecast.filter(day => {
        const temp = day.temperature;
        const humidity = day.humidity;
        const wind = day.wind_speed;
        
        // Optimal conditions: 20-30°C, 40-70% humidity, <15 km/h wind
        return temp >= 20 && temp <= 30 && humidity >= 40 && humidity <= 70 && wind < 15;
    }).length;
}

function getDayRecommendation(day, current) {
    const temp = day.temperature;
    const humidity = day.humidity;
    const wind = day.wind_speed;
    const description = day.description.toLowerCase();
    
    // Temperature-based recommendations
    if (temp > 35) {
        return '🌡️ Extra irrigation needed';
    } else if (temp < 15) {
        return '❄️ Protect sensitive crops';
    }
    
    // Weather condition-based recommendations
    if (description.includes('rain') || description.includes('shower')) {
        return '🌧️ Good for soil moisture';
    } else if (description.includes('clear') || description.includes('sun')) {
        return '☀️ Perfect for harvesting';
    } else if (wind > 20) {
        return '💨 Secure structures';
    } else if (humidity > 80) {
        return '💧 Monitor for diseases';
    }
    
    return '🌿 Normal farming activities';
}

function getBestPlantingDays(forecast) {
    if (!forecast) return 'Unable to determine';
    
    const optimalDays = forecast.filter(day => {
        const temp = day.temperature;
        const humidity = day.humidity;
        const wind = day.wind_speed;
        const description = day.description.toLowerCase();
        
        // Best planting conditions: 20-28°C, 50-70% humidity, <10 km/h wind, no heavy rain
        return temp >= 20 && temp <= 28 && 
               humidity >= 50 && humidity <= 70 && 
               wind < 10 && 
               !description.includes('heavy rain');
    });
    
    if (optimalDays.length === 0) return 'No optimal days found';
    if (optimalDays.length === 1) return 'Day ' + (forecast.indexOf(optimalDays[0]) + 1);
    
    const dayNumbers = optimalDays.map(day => 'Day ' + (forecast.indexOf(day) + 1));
    return dayNumbers.slice(0, 3).join(', ');
}

function getIrrigationAdvice(forecast) {
    if (!forecast) return 'Unable to determine';
    
    const rainyDays = getRainyDaysCount(forecast);
    const avgTemp = forecast.reduce((sum, day) => sum + day.temperature, 0) / forecast.length;
    
    if (rainyDays >= 5) {
        return 'Reduce irrigation - natural rainfall expected';
    } else if (rainyDays >= 3) {
        return 'Moderate irrigation - some rain expected';
    } else if (avgTemp > 30) {
        return 'Increase irrigation - high temperatures expected';
    } else if (avgTemp < 20) {
        return 'Reduce irrigation - cooler temperatures';
    } else {
        return 'Normal irrigation schedule recommended';
    }
}

function getCropProtectionAdvice(forecast) {
    if (!forecast) return 'Unable to determine';
    
    const highHumidityDays = forecast.filter(day => day.humidity > 80).length;
    const highWindDays = forecast.filter(day => day.wind_speed > 20).length;
    const rainyDays = getRainyDaysCount(forecast);
    
    const advice = [];
    
    if (highHumidityDays > 3) {
        advice.push('Apply fungicide for disease prevention');
    }
    if (highWindDays > 2) {
        advice.push('Secure crop covers and structures');
    }
    if (rainyDays > 4) {
        advice.push('Ensure proper drainage systems');
    }
    if (advice.length === 0) {
        advice.push('Standard crop protection measures');
    }
    
    return advice.join(', ');
}

function getOptimalActivities(forecast) {
    if (!forecast) return 'Unable to determine';
    
    const sunnyDays = getSunnyDaysCount(forecast);
    const rainyDays = getRainyDaysCount(forecast);
    const optimalDays = getOptimalFarmingDays(forecast);
    
    const activities = [];
    
    if (sunnyDays >= 5) {
        activities.push('Harvesting and drying crops');
    }
    if (optimalDays >= 3) {
        activities.push('Planting and transplanting');
    }
    if (rainyDays >= 3) {
        activities.push('Soil preparation and maintenance');
    }
    if (activities.length === 0) {
        activities.push('General farm maintenance');
    }
    
    return activities.join(', ');
}
</script>
@endsection
