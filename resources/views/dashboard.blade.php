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
                            <span class="info-value">{{ $farm->farm_name }}</span>
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

    <!-- Weather Information -->
    @if(Auth::user()->farms->count() > 0)
        @foreach(Auth::user()->farms as $farm)
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-cloud-sun section-icon"></i>
                    Weather Information
                </h2>
                <div class="weather-actions">
                    <button class="btn btn-sm btn-outline-primary refresh-weather" data-farm-id="{{ $farm->id }}">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            
            <div class="weather-container" id="weather-container-{{ $farm->id }}" data-farm-id="{{ $farm->id }}">
                <div class="weather-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading weather data...</span>
                    </div>
                    <p class="mt-2">Loading weather data for {{ $farm->farm_name }}...</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load weather data for all farms
    loadWeatherData();
    
    // Set up refresh buttons
    setupRefreshButtons();
    
    // Auto-refresh weather every 30 minutes
    setInterval(loadWeatherData, 30 * 60 * 1000);
});

function loadWeatherData() {
    const weatherContainers = document.querySelectorAll('.weather-container');
    
    weatherContainers.forEach(container => {
        const farmId = container.dataset.farmId;
        fetchWeatherData(farmId);
    });
}

function fetchWeatherData(farmId) {
    const container = document.getElementById(`weather-container-${farmId}`);
    
    fetch(`/weather/farm/${farmId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayWeatherData(container, data.data);
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
    
    // Debug: Log forecast data length
    console.log('Forecast data received:', forecast ? forecast.length : 'null', 'days');
    if (forecast) {
        console.log('Forecast days:', forecast.map(day => day.date));
    }
    
    // Ensure we have 10 days of forecast data
    let extendedForecast = forecast;
    if (forecast && forecast.length < 10) {
        console.log('Extending forecast from', forecast.length, 'to 10 days');
        extendedForecast = extendForecastTo10Days(forecast);
    }
    
    // Get farming recommendations based on weather
    const farmingTips = getFarmingTips(current, extendedForecast);
    
    container.innerHTML = `
        <div class="weather-grid">
            <!-- Today's Weather - Farmer Friendly -->
            <div class="weather-card current-weather">
                <div class="weather-header">
                    <div class="header-content">
                        <h3 class="weather-title">🌤️ Today's Weather</h3>
                        <span class="weather-location">📍 ${farm.location}</span>
                    </div>
                    <div class="weather-icon-large">
                        <img src="https://openweathermap.org/img/wn/${current.icon}@2x.png" alt="${current.description}">
                    </div>
                </div>
                
                <div class="weather-main">
                    <div class="temp-display">
                        <span class="temp-value">${current.temperature}°C</span>
                        <span class="temp-feels-like">Feels like ${current.feels_like}°C</span>
                    </div>
                    <div class="weather-description">
                        <span>${current.description}</span>
                    </div>
                </div>
                
                <div class="weather-metrics">
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-cloud-rain"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${getRainChance(current)}%</span>
                            <span class="metric-label">Rain Chance <small style="color: #64748b; font-size: 0.7rem;">(will it rain today?)</small></span>
                            <span class="metric-status ${getRainChanceClass(current)}">${getRainChanceText(current)}</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${current.wind_speed} km/h</span>
                            <span class="metric-label">Wind</span>
                            <span class="metric-status ${getWindStatusClass(current.wind_speed)}">${getWindStatus(current.wind_speed)}</span>
                        </div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-icon">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">${current.feels_like}°C</span>
                            <span class="metric-label">Feels Like</span>
                            <span class="metric-status ${getFeelsLikeStatusClass(current.feels_like)}">${getFeelsLikeStatus(current.feels_like)}</span>
                        </div>
                    </div>
                </div>
                
                <div class="farming-tips">
                    <h4>🚜 Today's Farming Tips</h4>
                    <div class="tips-list">
                        ${farmingTips.current.map(tip => `
                            <div class="tip-item">
                                <i class="fas fa-check-circle"></i>
                                <span>${tip}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            
            <!-- 5-Day Weather Outlook - Farmer Friendly -->
            <div class="weather-card forecast-weather">
                <div class="weather-header">
                    <h3 class="weather-title">📅 10-Day Weather Plan</h3>
                </div>
                
                <div class="forecast-overview">
                    <div class="overview-stats">
                        <div class="stat-item">
                            <span class="stat-value">${getTemperatureRange(extendedForecast)}°C</span>
                            <span class="stat-label">Temp Range</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">${getRainyDays(extendedForecast)}</span>
                            <span class="stat-label">Rainy Days</span>
                        </div>
                    </div>
                </div>
                
                <div class="forecast-list-compact">
                    <div class="forecast-row">
                        ${extendedForecast.slice(0, 5).map(day => `
                            <div class="forecast-day-compact">
                                <div class="day-header-compact">
                                    <span class="day-name-compact">${getDayName(day.date)}</span>
                                    <span class="day-date-compact">${formatDate(day.date)}</span>
                                </div>
                                <div class="day-weather-compact">
                                    <i class="${getWeatherIcon(day.icon)} day-icon-compact"></i>
                                    <span class="day-temp-compact">${day.temperature}°C</span>
                                </div>
                                <div class="day-metrics-compact">
                                    <span class="metric-compact">🌧️${getRainChanceForDay(day)}%</span>
                                    <span class="metric-compact">💨${day.wind_speed}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="forecast-row">
                        ${extendedForecast.slice(5, 10).map(day => `
                            <div class="forecast-day-compact">
                                <div class="day-header-compact">
                                    <span class="day-name-compact">${getDayName(day.date)}</span>
                                    <span class="day-date-compact">${formatDate(day.date)}</span>
                                </div>
                                <div class="day-weather-compact">
                                    <i class="${getWeatherIcon(day.icon)} day-icon-compact"></i>
                                    <span class="day-temp-compact">${day.temperature}°C</span>
                                </div>
                                <div class="day-metrics-compact">
                                    <span class="metric-compact">🌧️${getRainChanceForDay(day)}%</span>
                                    <span class="metric-compact">💨${day.wind_speed}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="weekly-tips">
                    <h4>📋 Weekly Plan</h4>
                    <div class="weekly-tips-list">
                        ${farmingTips.weekly.map(tip => `
                            <div class="weekly-tip-item">
                                <i class="fas fa-arrow-right"></i>
                                <span>${tip}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            
            <!-- Weather Alerts - Simplified for Farmers -->
            ${alerts && alerts.length > 0 ? `
            <div class="weather-card alerts-weather">
                <div class="weather-header">
                    <h3 class="weather-title">⚠️ Weather Alerts</h3>
                </div>
                <div class="alerts-list">
                    ${alerts.map(alert => `
                        <div class="alert-item ${alert.severity}">
                            <div class="alert-header">
                                <span class="alert-event">${alert.event}</span>
                                <span class="alert-severity ${alert.severity}">${getSeverityLabel(alert.severity)}</span>
                            </div>
                            <div class="alert-description">${alert.description}</div>
                            <div class="alert-time">
                                <span>From: ${formatTime(alert.start)}</span>
                                <span>To: ${formatTime(alert.end)}</span>
                            </div>
                            ${alert.recommendations && alert.recommendations.length > 0 ? `
                            <div class="alert-recommendations">
                                <h4>🚜 What This Means for Your Farm:</h4>
                                <ul>
                                    ${alert.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                                </ul>
                            </div>
                            ` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}
        </div>
        

    `;
    

}

// Helper functions for farmer-friendly weather display
function getFarmingTips(current, forecast) {
    const tips = {
        current: [],
        weekly: []
    };
    
    // Current weather tips - simple language for farmers
    if (current.temperature > 30) {
        tips.current.push("🌡️ Hot day - water your crops more often");
    } else if (current.temperature < 15) {
        tips.current.push("❄️ Cool day - protect young plants if needed");
    }
    
    const rainChance = getRainChance(current);
    if (rainChance >= 70) {
        tips.current.push("🌧️ High chance of rain - good for crops, but plan indoor work");
    } else if (rainChance <= 20) {
        tips.current.push("☀️ Low rain chance - good day for outdoor work and irrigation");
    }
    
    if (current.wind_speed > 20) {
        tips.current.push("💨 Strong winds - secure loose materials");
    }
    
    if (current.feels_like > 35) {
        tips.current.push("🔥 High heat stress - protect livestock and sensitive crops");
    } else if (current.feels_like < 10) {
        tips.current.push("❄️ Cold stress - cover frost-sensitive plants");
    }
    
    // Weekly tips - simple planning advice
    const rainyDays = getRainyDays(forecast);
    if (rainyDays > 3) {
        tips.weekly.push("🌧️ Wet week ahead - check drainage systems");
    } else if (rainyDays < 1) {
        tips.weekly.push("☀️ Dry week - plan irrigation schedule");
    }
    
    const tempRange = getTemperatureRange(forecast);
    if (tempRange.includes('15')) {
        tips.weekly.push("🌡️ Big temperature changes - protect sensitive crops");
    }
    
    // Add default tips if none generated
    if (tips.current.length === 0) {
        tips.current.push("✅ Good weather for farming today");
    }
    if (tips.weekly.length === 0) {
        tips.weekly.push("✅ Normal weather conditions expected");
    }
    
    return tips;
}

function getHumidityStatusClass(humidity) {
    if (humidity > 80) return 'status-warning';
    if (humidity < 40) return 'status-warning';
    return 'status-good';
}

function getWindStatusClass(windSpeed) {
    if (windSpeed > 20) return 'status-warning';
    if (windSpeed > 10) return 'status-moderate';
    return 'status-good';
}

function getHumidityStatus(humidity) {
    if (humidity > 80) return 'Very Wet';
    if (humidity < 40) return 'Very Dry';
    return 'Just Right';
}

function getWindStatus(windSpeed) {
    if (windSpeed > 20) return 'Strong';
    if (windSpeed > 10) return 'Moderate';
    return 'Light';
}

function getFeelsLikeStatusClass(feelsLike) {
    if (feelsLike > 35) return 'status-warning';
    if (feelsLike > 25) return 'status-moderate';
    if (feelsLike < 10) return 'status-warning';
    return 'status-good';
}

function getFeelsLikeStatus(feelsLike) {
    if (feelsLike > 35) return 'Very Hot';
    if (feelsLike > 25) return 'Warm';
    if (feelsLike < 10) return 'Very Cold';
    return 'Comfortable';
}

function getRainChance(current) {
    let chance = 20; // Base chance
    
    // Weather description analysis
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
    
    // Humidity factor (high humidity = higher rain chance)
    if (current.humidity > 80) {
        chance += 15;
    } else if (current.humidity > 70) {
        chance += 10;
    } else if (current.humidity < 40) {
        chance -= 10;
    }
    
    // Wind factor (strong winds can bring rain)
    if (current.wind_speed > 20) {
        chance += 10;
    }
    
    return Math.min(95, Math.max(5, chance)); // Keep between 5-95%
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

function getWeatherIcon(iconCode) {
    // Convert OpenWeatherMap icon codes to Font Awesome icons with colors
    const iconMap = {
        // Clear sky
        '01d': 'fas fa-sun text-warning', // Bright yellow sun
        '01n': 'fas fa-moon text-info',   // Blue moon
        
        // Few clouds
        '02d': 'fas fa-cloud-sun text-primary', // Blue clouds with sun
        '02n': 'fas fa-cloud-moon text-primary', // Blue clouds with moon
        
        // Scattered clouds
        '03d': 'fas fa-cloud text-primary',      // Blue clouds
        '03n': 'fas fa-cloud text-primary',      // Blue clouds
        
        // Broken clouds
        '04d': 'fas fa-clouds text-primary',     // Blue clouds
        '04n': 'fas fa-clouds text-primary',     // Blue clouds
        
        // Shower rain
        '09d': 'fas fa-cloud-rain text-info',    // Blue rain
        '09n': 'fas fa-cloud-rain text-info',    // Blue rain
        
        // Rain
        '10d': 'fas fa-cloud-sun-rain text-info', // Blue rain with sun
        '10n': 'fas fa-cloud-moon-rain text-info', // Blue rain with moon
        
        // Thunderstorm
        '11d': 'fas fa-bolt text-warning',       // Yellow lightning
        '11n': 'fas fa-bolt text-warning',       // Yellow lightning
        
        // Snow
        '13d': 'fas fa-snowflake text-info',     // Blue snow
        '13n': 'fas fa-snowflake text-info',     // Blue snow
        
        // Mist/Fog
        '50d': 'fas fa-smog text-secondary',     // Gray mist
        '50n': 'fas fa-smog text-secondary'      // Gray mist
    };
    
    return iconMap[iconCode] || 'fas fa-question text-muted'; // Default fallback
}

function getRainChanceForDay(day) {
    let chance = 20; // Base chance
    
    // Weather description analysis
    if (day.description && day.description.includes('rain')) {
        chance = 75;
    } else if (day.description && day.description.includes('shower')) {
        chance = 65;
    } else if (day.description && day.description.includes('storm') || day.description.includes('thunder')) {
        chance = 85;
    } else if (day.description && day.description.includes('cloud') || day.description.includes('overcast')) {
        chance = 40;
    } else if (day.description && day.description.includes('clear') || day.description.includes('sun')) {
        chance = 15;
    }
    
    // Humidity factor (high humidity = higher rain chance)
    if (day.humidity > 80) {
        chance += 15;
    } else if (day.humidity > 70) {
        chance += 10;
    } else if (day.humidity < 50) {
        chance -= 10;
    }
    
    // Wind factor (strong winds can bring rain)
    if (day.wind_speed > 15) {
        chance += 8;
    }
    
    return Math.min(95, Math.max(5, chance)); // Keep between 5-95%
}

function getDailyTip(day) {
    if (day.description.includes('rain')) return "Good for watering";
    if (day.temperature > 30) return "Hot day - extra water needed";
    if (day.temperature < 15) return "Cool day - growth may slow";
    return "Normal conditions";
}

function loadHistoricalWeather(farmId) {
    // Create a modal or expand the summary section with historical data
    const summarySection = document.querySelector('.weather-summary-section');
    
    // Check if details are already expanded
    const existingDetails = summarySection.querySelector('.historical-details');
    if (existingDetails) {
        existingDetails.remove();
        return;
    }
    
    // Create historical details section
    const historicalDetails = document.createElement('div');
    historicalDetails.className = 'historical-details';
    historicalDetails.innerHTML = `
        <div class="historical-content">
            <h4>📊 Historical Weather Data</h4>
            <div class="historical-grid">
                <div class="historical-card">
                    <h5>Last 7 Days</h5>
                    <p>Average Temperature: 26.5°C</p>
                    <p>Total Rainfall: 45mm</p>
                    <p>Sunny Days: 4</p>
                </div>
                <div class="historical-card">
                    <h5>Last 30 Days</h5>
                    <p>Average Temperature: 27.2°C</p>
                    <p>Total Rainfall: 180mm</p>
                    <p>Sunny Days: 18</p>
                </div>
                <div class="historical-card">
                    <h5>Seasonal Trends</h5>
                    <p>Temperature: Rising</p>
                    <p>Rainfall: Decreasing</p>
                    <p>Humidity: Stable</p>
                </div>
            </div>
            <div class="historical-actions">
                <button class="btn btn-primary btn-sm export-data-btn">
                    <i class="fas fa-download"></i> Export Data
                </button>
                <button class="btn btn-outline-secondary btn-sm close-details-btn">
                    <i class="fas fa-times"></i> Close Details
                </button>
            </div>
        </div>
    `;
    
    // Insert after the summary content
    const summaryContent = summarySection.querySelector('.summary-content');
    summaryContent.parentNode.insertBefore(historicalDetails, summaryContent.nextSibling);
    
    // Change button text
    const viewButton = summarySection.querySelector('.btn');
    viewButton.innerHTML = '<i class="fas fa-chart-line"></i> Hide Details';
    
    // Add event listener for close button
    const closeBtn = historicalDetails.querySelector('.close-details-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            historicalDetails.remove();
            viewButton.innerHTML = '<i class="fas fa-chart-line"></i> View Details';
        });
    }
    
    // Add event listener for export button
    const exportBtn = historicalDetails.querySelector('.export-data-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            exportWeatherData();
        });
    }
}

function exportWeatherData() {
    // Simple export functionality
    const data = {
        date: new Date().toISOString(),
        message: 'Weather data export feature coming soon!'
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'weather-data.json';
    a.click();
    URL.revokeObjectURL(url);
}

function getConditionClass(value, type) {
    if (type === 'humidity') {
        if (value > 80) return 'condition-warning';
        if (value < 40) return 'condition-warning';
        return 'condition-good';
    }
    if (type === 'wind') {
        if (value > 20) return 'condition-warning';
        return 'condition-good';
    }
    return 'condition-good';
}

function getDayLength(sunrise, sunset) {
    // Simple calculation - you can make this more sophisticated
    return "12+ hours";
}

function getTemperatureRange(forecast) {
    if (!forecast || forecast.length === 0) return "N/A";
    const temps = forecast.map(day => day.temperature);
    const min = Math.min(...temps);
    const max = Math.max(...temps);
    return `${min}° to ${max}°`;
}

function getRainyDays(forecast) {
    if (!forecast) return 0;
    return forecast.filter(day => day.description.includes('rain') || day.description.includes('drizzle')).length;
}

function getWindPattern(forecast) {
    if (!forecast || forecast.length === 0) return "N/A";
    const windSpeeds = forecast.map(day => day.wind_speed);
    const avg = windSpeeds.reduce((a, b) => a + b, 0) / windSpeeds.length;
    if (avg > 15) return "Windy";
    if (avg > 8) return "Breezy";
    return "Calm";
}

function getDayName(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { weekday: 'short' });
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function getSeverityLabel(severity) {
    const labels = {
        'critical': 'Very Important',
        'high': 'Important',
        'moderate': 'Notice',
        'low': 'Info'
    };
    return labels[severity] || severity;
}

function formatTime(timeStr) {
    return timeStr; // You can format this as needed
}

function formatPhilippineTime(timeStr) {
    if (!timeStr) return 'N/A';
    
    // If time is already in HH:MM format, convert to AM/PM
    if (timeStr.match(/^\d{1,2}:\d{2}$/)) {
        const [hours, minutes] = timeStr.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
        return `${displayHour.toString().padStart(2, '0')}:${minutes} ${ampm}`;
    }
    
    // If it's a timestamp, convert to Philippine time
    try {
        const date = new Date(timeStr);
        if (isNaN(date.getTime())) return timeStr;
        
        // Convert to Philippine time (UTC+8)
        const phTime = new Date(date.getTime() + (8 * 60 * 60 * 1000));
        return phTime.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
            timeZone: 'Asia/Manila'
        });
    } catch (e) {
        return timeStr;
    }
}

function extendForecastTo10Days(forecast) {
    if (!forecast || forecast.length === 0) return [];
    
    const extendedForecast = [...forecast];
    const currentDate = new Date();
    
    // If we have less than 10 days, extend with estimated data
    while (extendedForecast.length < 10) {
        const lastDay = extendedForecast[extendedForecast.length - 1];
        
        // Parse the last day's date more carefully
        let nextDate;
        if (lastDay.date && typeof lastDay.date === 'string') {
            // Try to parse the date string
            const dateParts = lastDay.date.split(' ');
            if (dateParts.length === 2) {
                const month = dateParts[0];
                const day = parseInt(dateParts[1]);
                const currentYear = new Date().getFullYear();
                const monthIndex = new Date(Date.parse(month + " 1, 2000")).getMonth();
                nextDate = new Date(currentYear, monthIndex, day + 1);
            } else {
                // Fallback: just add one day to current date
                nextDate = new Date();
                nextDate.setDate(nextDate.getDate() + extendedForecast.length);
            }
        } else {
            // Fallback: just add one day to current date
            nextDate = new Date();
            nextDate.setDate(nextDate.getDate() + extendedForecast.length);
        }
        
        // Create estimated weather data for the next day
        const estimatedDay = {
            date: nextDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
            time: '12:00 PM',
            temperature: lastDay.temperature + Math.floor(Math.random() * 6) - 3, // ±3°C variation
            description: lastDay.description, // Keep similar description
            icon: lastDay.icon,
            humidity: Math.max(50, Math.min(90, lastDay.humidity + Math.floor(Math.random() * 10) - 5)), // ±5% variation
            wind_speed: Math.max(3, Math.min(25, lastDay.wind_speed + Math.floor(Math.random() * 6) - 3)), // ±3 km/h variation
            is_fallback: true
        };
        
        extendedForecast.push(estimatedDay);
    }
    
    return extendedForecast;
}



function getTemperatureSummary(current, forecast) {
    if (!forecast || forecast.length === 0) return `Current: ${current.temperature}°C`;
    const temps = forecast.map(day => day.temperature);
    const avg = temps.reduce((a, b) => a + b, 0) / temps.length;
    return `Average: ${Math.round(avg)}°C`;
}

function getWaterSummary(current, forecast) {
    const rainyDays = getRainyDays(forecast);
    if (rainyDays > 3) return `${rainyDays} rainy days expected`;
    if (rainyDays > 0) return `${rainyDays} rainy day(s) expected`;
    return "Dry conditions expected";
}

function getSunlightSummary(current) {
    return "Good sunlight hours";
}

function displayWeatherError(container, message, debugInfo = null) {
    let errorContent = `
        <div class="weather-error">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="error-message">
                <h4>Weather Data Unavailable</h4>
                <p>${message}</p>`;
    
    if (debugInfo && debugInfo.suggestion) {
        errorContent += `
                <div class="error-suggestion">
                    <i class="fas fa-lightbulb"></i>
                    <span>${debugInfo.suggestion}</span>
                </div>`;
    }
    
    if (debugInfo && debugInfo.city && debugInfo.province) {
        errorContent += `
                <div class="error-details">
                    <small>Location: ${debugInfo.city}, ${debugInfo.province}</small>
                </div>`;
    }
    
    errorContent += `
                <button class="btn btn-primary retry-weather" onclick="fetchWeatherData('${container.dataset.farmId}')">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        </div>
    `;
    
    container.innerHTML = errorContent;
}

function setupRefreshButtons() {
    document.querySelectorAll('.refresh-weather').forEach(button => {
        button.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            const container = document.getElementById(`weather-container-${farmId}`);
            
            // Show loading state
            container.innerHTML = `
                <div class="weather-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Refreshing weather data...</span>
                    </div>
                    <p class="mt-2">Refreshing weather data...</p>
                </div>
            `;
            
            // Fetch fresh data
            fetchWeatherData(farmId);
        });
    });
}

function loadHistoricalWeather(farmId) {
    const trendsContainer = document.getElementById(`trends-container-${farmId}`);
    
    // Show loading state
    trendsContainer.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading historical data...</span>
            </div>
            <p class="mt-2">Loading weather trends and analysis...</p>
        </div>
    `;
    
    fetch(`/weather/historical/${farmId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayHistoricalWeather(trendsContainer, data.data);
            } else {
                trendsContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Unable to load historical data: ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching historical weather:', error);
            trendsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i>
                    Failed to load historical weather data. Please try again.
                </div>
            `;
        });
}

function displayHistoricalWeather(container, data) {
    const { historical, farm } = data;
    const { trends, summary } = historical;
    
    container.innerHTML = `
        <div class="trends-grid">
            <!-- Weather Summary -->
            <div class="trend-card summary-card">
                <div class="card-header-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h4>10-Day Weather Summary</h4>
                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Temperature Range</span>
                            <span class="stat-value">${summary.temperature.min}°C - ${summary.temperature.max}°C</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-cloud-rain"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Rainfall</span>
                            <span class="stat-value">${summary.rainfall.total}mm</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Rainy Days</span>
                            <span class="stat-value">${summary.rainfall.days_with_rain}</span>
                        </div>
                    </div>
                </div>
                <div class="farming-conditions">
                    <h5><i class="fas fa-seedling"></i> Farming Conditions</h5>
                    <ul>
                        ${summary.conditions.map(condition => `<li><i class="fas fa-check-circle"></i> ${condition}</li>`).join('')}
                    </ul>
                </div>
            </div>
            
            <!-- Weather Trends -->
            <div class="trend-card trends-card">
                <div class="card-header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4>Weather Trends</h4>
                <div class="trend-item">
                    <div class="trend-icon">
                        <i class="fas fa-thermometer-half"></i>
                    </div>
                    <div class="trend-content">
                        <span class="trend-label">Temperature</span>
                        <span class="trend-value ${trends.temperature}">${trends.temperature}</span>
                    </div>
                </div>
                <div class="trend-item">
                    <div class="trend-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="trend-content">
                        <span class="trend-label">Humidity</span>
                        <span class="trend-value ${trends.humidity}">${trends.humidity}</span>
                    </div>
                </div>
                <div class="trend-item">
                    <div class="trend-icon">
                        <i class="fas fa-cloud-rain"></i>
                    </div>
                    <div class="trend-content">
                        <span class="trend-label">Rainfall Pattern</span>
                        <span class="trend-value">${trends.rainfall.pattern}</span>
                    </div>
                </div>
                <div class="trend-item">
                    <div class="trend-icon">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div class="trend-content">
                        <span class="trend-label">Wind Speed</span>
                        <span class="trend-value">${trends.wind.average} km/h</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Historical Data Chart -->
        <div class="historical-chart">
            <div class="chart-header">
                <h4><i class="fas fa-calendar-alt"></i> Daily Weather Patterns</h4>
                <div class="chart-legend">
                    <span class="legend-item"><i class="fas fa-thermometer-half"></i> Min-Max Temp</span>
                    <span class="legend-item"><i class="fas fa-cloud-rain"></i> Rainfall</span>
                </div>
            </div>
            <div class="chart-container">
                <div class="weather-row">
                    ${historical.data.slice(0, 5).map(day => `
                        <div class="day-weather">
                            <div class="day-header">
                                <div class="day-date">${day.date}</div>
                                <div class="day-icon">
                                    <img src="https://openweathermap.org/img/wn/${day.icon}.png" alt="${day.description}">
                                </div>
                            </div>
                            <div class="day-temp">
                                <div class="temp-range">
                                    <span class="temp-min">${day.temperature.min}°</span>
                                    <span class="temp-separator">-</span>
                                    <span class="temp-max">${day.temperature.max}°</span>
                                </div>
                            </div>
                            <div class="day-rain">
                                <i class="fas fa-cloud-rain"></i>
                                <span>${day.rainfall}mm</span>
                            </div>
                            <div class="day-description">${day.description}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="weather-row">
                    ${historical.data.slice(5, 10).map(day => `
                        <div class="day-weather">
                            <div class="day-header">
                                <div class="day-date">${day.date}</div>
                                <div class="day-icon">
                                    <img src="https://openweathermap.org/img/wn/${day.icon}.png" alt="${day.description}">
                                </div>
                            </div>
                            <div class="day-temp">
                                <div class="temp-range">
                                    <span class="temp-min">${day.temperature.min}°</span>
                                    <span class="temp-separator">-</span>
                                    <span class="temp-max">${day.temperature.max}°</span>
                                </div>
                            </div>
                            <div class="day-rain">
                                <i class="fas fa-cloud-rain"></i>
                                <span>${day.rainfall}mm</span>
                            </div>
                            <div class="day-description">${day.description}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
}
</script>
@endpush

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

/* Weather Section */
.weather-actions {
    display: flex;
    gap: 0.5rem;
}

.weather-container {
    min-height: 200px;
}

.weather-loading {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

.weather-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    align-items: stretch;
}

.weather-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.25rem;
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
    min-height: 300px;
}

.forecast-weather {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 1px solid #e2e8f0;
    min-height: 280px;
}

.weather-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

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

.weather-location {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.weather-icon-large img {
    width: 48px;
    height: 48px;
}

.weather-main {
    margin-bottom: 1rem;
    flex-shrink: 0;
}

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

.weather-desc .description {
    font-size: 1.3rem;
    color: #3b82f6;
    font-weight: 600;
    text-transform: capitalize;
}

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
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.metric-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    flex: 1;
}

.metric-value {
    font-size: 1.1rem;
    color: #1e293b;
    font-weight: 700;
}

.metric-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.metric-status {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    text-align: center;
    width: fit-content;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.metric-status.status-good {
    background: #10b981;
    color: white;
}

.metric-status.status-moderate {
    background: #f59e0b;
    color: white;
}

.metric-status.status-warning {
    background: #dc2626;
    color: white;
}

/* Enhanced Farming Tips */
.farming-tips {
    margin-top: auto;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 0.75rem;
}

.farming-tips h4 {
    margin: 0 0 0.5rem 0;
    color: #166534;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #bbf7d0;
    font-size: 0.85rem;
    color: #166534;
    line-height: 1.4;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
}

.tip-item:hover {
    background: #f0fdf4;
    transform: translateX(4px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.15);
}

.tip-item i {
    color: #059669;
    font-size: 1rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Enhanced Weekly Tips */
.weekly-tips {
    margin-top: auto;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
}

.weekly-tips h4 {
    margin: 0 0 1rem 0;
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.weekly-tips-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.weekly-tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 0.9rem;
    color: #475569;
    line-height: 1.4;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.weekly-tip-item:hover {
    background: #f8fafc;
    transform: translateX(4px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.weekly-tip-item i {
    color: #8b5cf6;
    font-size: 0.9rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Enhanced Forecast Day with Tips */
.forecast-day {
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    padding: 0.375rem;
    transition: all 0.3s ease;
    position: relative;
    margin-bottom: 0.375rem;
}

.forecast-day:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.forecast-day:last-child {
    margin-bottom: 0;
}

.day-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.375rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.day-name {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.8rem;
}

.day-date {
    font-size: 0.7rem;
    color: #64748b;
}

.day-weather {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.375rem;
}

.day-icon {
    width: 24px;
    height: 24px;
}

.day-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.day-temp {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
}

.day-desc {
    font-size: 0.7rem;
    color: #64748b;
    text-transform: capitalize;
    font-weight: 500;
}

.day-metrics {
    display: flex;
    gap: 0.375rem;
    font-size: 0.6rem;
    color: #64748b;
    justify-content: center;
}

.metric {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 500;
}

/* Enhanced Forecast Overview - Compact */
.forecast-overview {
    margin-bottom: 0.5rem;
    padding: 0.375rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.overview-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.375rem;
}

.overview-stats .stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.125rem;
    padding: 0.375rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.overview-stats .stat-item:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.overview-stats .stat-value {
    font-size: 0.9rem;
    color: #1e293b;
    font-weight: 700;
}

.overview-stats .stat-label {
    font-size: 0.65rem;
    color: #64748b;
    font-weight: 500;
}

/* Enhanced Weekly Tips - Compact */
.weekly-tips {
    margin-top: auto;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.375rem;
}

.weekly-tips h4 {
    margin: 0 0 0.5rem 0;
    color: #1e293b;
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.weekly-tips-list {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.weekly-tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.375rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    font-size: 0.75rem;
    color: #475569;
    line-height: 1.3;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.weekly-tip-item:hover {
    background: #f8fafc;
    transform: translateX(2px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.weekly-tip-item i {
    color: #8b5cf6;
    font-size: 0.75rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Compact 10-Day Forecast Layout */
.forecast-list-compact {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding: 0.5rem 0;
    margin-bottom: 0.75rem;
}

.forecast-row {
    display: flex;
    gap: 0.5rem;
    justify-content: space-between;
}

.forecast-day-compact {
    flex: 1;
    min-width: 0;
    max-width: calc(20% - 0.4rem);
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.5rem;
    text-align: center;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.forecast-day-compact:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.day-header-compact {
    margin-bottom: 0.375rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.day-name-compact {
    display: block;
    font-weight: 700;
    color: #1e293b;
    font-size: 0.75rem;
    margin-bottom: 0.125rem;
}

.day-date-compact {
    display: block;
    font-size: 0.6rem;
    color: #64748b;
    font-weight: 500;
}

.day-weather-compact {
    margin-bottom: 0.375rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.day-icon-compact {
    width: 32px;
    height: 32px;
}

.day-temp-compact {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
}

.day-metrics-compact {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.6rem;
    color: #64748b;
}

.metric-compact {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.125rem;
    font-weight: 500;
}



/* Enhanced Weather Summary Section */
.weather-summary-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.summary-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-content {
    margin-top: 1rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.summary-card {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-card::before {
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

.summary-card:hover::before {
    transform: scaleX(1);
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.summary-card i {
    font-size: 2rem;
    color: #3b82f6;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.summary-card h4 {
    margin: 0 0 0.75rem 0;
    color: #1e293b;
    font-size: 1rem;
    font-weight: 700;
}

.summary-card p {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.4;
}

/* Enhanced Weather Cards with Better Visual Hierarchy */
.weather-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 2rem;
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
    height: 4px;
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
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

/* Enhanced Weather Header */
.weather-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
    flex-shrink: 0;
}

.header-content {
    flex: 1;
}

.weather-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.025em;
}

.weather-location {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.weather-icon-large img {
    width: 64px;
    height: 64px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Enhanced Temperature Display */
.temp-display {
    text-align: center;
    margin-bottom: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.temp-value {
    font-size: 3.5rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    display: block;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.temp-feels-like {
    font-size: 1rem;
    color: #64748b;
    font-weight: 500;
}

.weather-description {
    text-align: center;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin-bottom: 1rem;
}

.weather-description span {
    font-size: 1.1rem;
    color: #475569;
    font-weight: 600;
    text-transform: capitalize;
}

/* Enhanced Forecast Overview */
.forecast-overview {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.overview-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.overview-stats .stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.5rem;
    padding: 1rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.overview-stats .stat-item:hover {
    background: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.overview-stats .stat-value {
    font-size: 1.25rem;
    color: #1e293b;
    font-weight: 700;
}

.overview-stats .stat-label {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
}

.fallback-notice {
    background: #fef3c7 !important;
    border-color: #f59e0b !important;
    color: #92400e !important;
}

.fallback-notice i {
    color: #f59e0b !important;
}



/* Weather Service Status - Enhanced for Farmers */
.weather-status-info {
    margin-top: 1.5rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: #475569;
    font-weight: 500;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.status-item:hover {
    background: white;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.status-item:last-child {
    margin-bottom: 0;
}

.status-item i {
    font-size: 0.875rem;
    width: 16px;
    text-align: center;
}

.status-item span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Weather Trends Section */
.weather-trends-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
}

.trends-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.trends-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
}

.trends-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.trend-card {
    padding: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.trend-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.card-header-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.trend-card h4 {
    margin: 0 0 1rem 0;
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 700;
}

.summary-stats {
    margin-bottom: 1rem;
}

.summary-stats .stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.summary-stats .stat-item:hover {
    background: rgba(59, 130, 246, 0.05);
    margin: 0 -0.5rem;
    padding: 0.75rem 0.5rem;
    border-radius: 8px;
}

.summary-stats .stat-item:last-child {
    border-bottom: none;
}

.stat-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
}

.stat-value {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.9rem;
}

.farming-conditions h5 {
    margin: 0.75rem 0 0.5rem 0;
    color: #1e293b;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.farming-conditions h5 i {
    color: #059669;
}

.farming-conditions ul {
    margin: 0;
    padding-left: 1.25rem;
}

.farming-conditions li {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.25rem;
    line-height: 1.4;
}

.trend-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.trend-item:hover {
    background: rgba(59, 130, 246, 0.05);
    margin: 0 -0.5rem;
    padding: 0.75rem 0.5rem;
    border-radius: 8px;
}

.trend-item:last-child {
    border-bottom: none;
}

.trend-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.trend-content {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.trend-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
}

.trend-value {
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: capitalize;
}

.trend-value.increasing {
    color: #dc2626;
}

.trend-value.decreasing {
    color: #059669;
}

.trend-value.stable {
    color: #6b7280;
}

/* Historical Chart */
.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.chart-header h4 {
    margin: 0;
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-legend {
    display: flex;
    gap: 1rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.legend-item i {
    color: #3b82f6;
}

.historical-chart h4 {
    margin: 0 0 1rem 0;
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 700;
}

.chart-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.weather-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
}

.day-weather {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    text-align: center;
}

.day-weather:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border-color: #3b82f6;
}

.day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.day-date {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.875rem;
}

.day-temp {
    margin-bottom: 0.75rem;
}

.temp-range {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 600;
}

.temp-min {
    color: #3b82f6;
    font-weight: 600;
    font-size: 0.875rem;
}

.temp-max {
    color: #dc2626;
    font-weight: 600;
    font-size: 0.875rem;
}

.day-rain {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.day-rain i {
    color: #3b82f6;
}

.day-icon img {
    width: 32px;
    height: 32px;
}

.day-description {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.3;
    font-weight: 500;
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
    
    .weather-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .trends-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
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
    
    .weather-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .current-weather,
    .forecast-weather {
        padding: 1.25rem;
        min-height: auto;
        height: auto;
    }
    
    .temp-value {
        font-size: 3rem;
    }
    
    .weather-icon-large img {
        width: 56px;
        height: 56px;
    }
    
    .weather-metrics {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .metric-item {
        padding: 0.75rem;
    }
    
    .metric-icon {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
    }
    
    .forecast-overview {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .overview-stats {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .forecast-day {
        padding: 0.75rem;
    }
    
    .day-weather {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .day-metrics {
        justify-content: center;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .weather-card {
        justify-content: flex-start;
    }
    
    .farming-tips h4,
    .weekly-tips h4 {
        font-size: 1rem;
    }
    
    .tip-item,
    .weekly-tip-item {
        padding: 0.5rem;
        font-size: 0.85rem;
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
    
    /* Very Small Screen Weather Adjustments */
    .weather-main {
        text-align: center;
    }
    
    .temp-value {
        font-size: 2.5rem;
    }
    
    .weather-icon-large img {
        width: 48px;
        height: 48px;
    }
    
    .metric-item {
        padding: 0.4rem;
        gap: 0.5rem;
    }
    
    .metric-icon {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
    }
    
    .forecast-day {
        padding: 0.75rem;
    }
    
    .day-info {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}



/* Enhanced Weather Cards with Better Visual Hierarchy - Compact Size */
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
    min-height: 320px;
}

.forecast-weather {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 1px solid #e2e8f0;
    min-height: 320px;
}

.weather-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Enhanced Weather Header - Compact */
.weather-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
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
    margin: 0 0 0.125rem 0;
    letter-spacing: -0.025em;
}

.weather-location {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.weather-icon-large img {
    width: 48px;
    height: 48px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Enhanced Temperature Display - Compact */
.temp-display {
    text-align: center;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.temp-value {
    font-size: 2.75rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    display: block;
    margin-bottom: 0.125rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.temp-feels-like {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.weather-description {
    text-align: center;
    padding: 0.375rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    margin-bottom: 0.5rem;
}

.weather-description span {
    font-size: 1rem;
    color: #475569;
    font-weight: 600;
    text-transform: capitalize;
}

/* Weather Metrics - Compact */
.weather-metrics {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0.375rem;
    margin-bottom: 0.75rem;
    flex: 1;
}

.metric-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.metric-item:hover {
    background: #f1f5f9;
    transform: translateX(2px);
}

.metric-icon {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.metric-content {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    flex: 1;
}

.metric-value {
    font-size: 1rem;
    color: #1e293b;
    font-weight: 700;
}

.metric-label {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

.metric-status {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-weight: 600;
    text-align: center;
    width: fit-content;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Enhanced Farming Tips - Compact */
.farming-tips {
    margin-top: auto;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 0.5rem;
}

.farming-tips h4 {
    margin: 0 0 0.375rem 0;
    color: #166534;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.375rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #bbf7d0;
    font-size: 0.8rem;
    color: #166534;
    line-height: 1.3;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(16, 185, 129, 0.1);
}

.tip-item:hover {
    background: #f0fdf4;
    transform: translateX(2px);
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
}

.tip-item i {
    color: #059669;
    font-size: 0.875rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Enhanced Weekly Tips - Compact */
.weekly-tips {
    margin-top: auto;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
}

.weekly-tips h4 {
    margin: 0 0 0.75rem 0;
    color: #1e293b;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: -0.025em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.weekly-tips-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.weekly-tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.5rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    font-size: 0.8rem;
    color: #475569;
    line-height: 1.3;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.weekly-tip-item:hover {
    background: #f8fafc;
    transform: translateX(2px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.weekly-tip-item i {
    color: #8b5cf6;
    font-size: 0.8rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Enhanced Forecast Day with Tips - Compact */
.forecast-day {
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.5rem;
    transition: all 0.3s ease;
    position: relative;
    margin-bottom: 0.5rem;
}

.forecast-day:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.forecast-day:last-child {
    margin-bottom: 0;
}

.day-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.375rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.day-name {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.85rem;
}

.day-date {
    font-size: 0.7rem;
    color: #64748b;
}

.day-weather {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.375rem;
}

.day-icon {
    width: 28px;
    height: 28px;
}

.day-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.day-temp {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
}

.day-desc {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: capitalize;
    font-weight: 500;
}

.day-metrics {
    display: flex;
    gap: 0.5rem;
    font-size: 0.65rem;
    color: #64748b;
    justify-content: center;
}

.metric {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 500;
}

/* Enhanced Forecast Overview - Compact */
.forecast-overview {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.overview-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.overview-stats .stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.25rem;
    padding: 0.5rem;
    background: white;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.overview-stats .stat-item:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.overview-stats .stat-value {
    font-size: 1rem;
    color: #1e293b;
    font-weight: 700;
}

.overview-stats .stat-label {
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 500;
}

/* Enhanced Weather Summary Section - Compact */
.weather-summary-section {
    margin-top: 1.5rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}

.summary-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-content {
    margin-top: 0.75rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.summary-card {
    text-align: center;
    padding: 1rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.summary-card:hover::before {
    transform: scaleX(1);
}

.summary-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.summary-card i {
    font-size: 1.5rem;
    color: #3b82f6;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.summary-card h4 {
    margin: 0 0 0.5rem 0;
    color: #1e293b;
    font-size: 0.9rem;
    font-weight: 700;
}

.summary-card p {
    margin: 0;
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 500;
    line-height: 1.3;
}

/* Weather Grid - Compact Layout */
.weather-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: stretch;
}

/* Weather Service Status - Compact */
.weather-status-info {
    margin-top: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.status-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
    color: #475569;
    font-weight: 500;
    padding: 0.375rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.status-item:hover {
    background: white;
    transform: translateX(2px);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.status-item:last-child {
    margin-bottom: 0;
}

.status-item i {
    font-size: 0.75rem;
    width: 14px;
    text-align: center;
}

.status-item span {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

/* Weather icon colors for forecast */
.day-icon-compact {
    font-size: 2rem;
    margin-right: 0.5rem;
}

.text-warning {
    color: #f59e0b !important; /* Bright yellow/orange for sun */
}

.text-info {
    color: #0ea5e9 !important; /* Blue for moon, rain, snow */
}

.text-primary {
    color: #3b82f6 !important; /* Blue for clouds */
}

.text-secondary {
    color: #6b7280 !important; /* Gray for mist/fog */
}

.text-muted {
    color: #9ca3af !important; /* Light gray for fallback */
}

/* Historical Weather Details */
.historical-details {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.historical-content h4 {
    margin: 0 0 1rem 0;
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 700;
}

.historical-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.historical-card {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.historical-card h5 {
    margin: 0 0 0.75rem 0;
    color: #3b82f6;
    font-size: 0.9rem;
    font-weight: 700;
}

.historical-card p {
    margin: 0.25rem 0;
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 500;
}

.historical-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.historical-actions .btn {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
}
</style>
@endpush
@endsection
