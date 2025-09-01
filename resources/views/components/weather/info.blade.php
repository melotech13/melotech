<div class="weather-info-container">
    <h4 class="section-title">
        <i class="fas fa-cloud-sun me-2"></i>
        Weather Information
    </h4>
    
    <div class="current-weather mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="location mb-0">
                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                    {{ $location }}
                </h5>
                <div class="last-updated text-muted small">
                    Updated: {{ now()->format('M d, Y H:i') }}
                </div>
            </div>
            <div class="current-temp">
                <span class="display-4 fw-bold">{{ $weather['temperature'] ?? 'N/A' }}</span>
            </div>
        </div>
        
        <div class="weather-details d-flex justify-content-between">
            <div class="weather-condition text-center">
                <div class="weather-icon mb-2">
                    <i class="fas {{ $weather['icon'] ?? 'fa-cloud-sun' }} fa-2x text-primary"></i>
                </div>
                <div class="weather-text">
                    {{ $weather['condition'] ?? '--' }}
                </div>
            </div>
            
            <div class="weather-stats">
                <div class="weather-stat">
                    <i class="fas fa-tint text-info me-2"></i>
                    <span>Humidity: {{ $weather['humidity'] ?? '--' }}</span>
                </div>
                <div class="weather-stat">
                    <i class="fas fa-wind text-info me-2"></i>
                    <span>Wind: {{ $weather['wind'] ?? '--' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="weather-forecast">
        <h6 class="forecast-title mb-3">
            <i class="fas fa-calendar-day me-2"></i>
            5-Day Forecast
        </h6>
        
        <div class="forecast-items">
            @if(!empty($weather['forecast']))
                @foreach(array_slice($weather['forecast'], 0, 5) as $day)
                    <div class="forecast-item">
                        <div class="forecast-day">{{ $day['day'] ?? '--' }}</div>
                        <div class="forecast-icon">
                            <i class="fas {{ $day['icon'] ?? 'fa-cloud-sun' }} text-warning"></i>
                        </div>
                        <div class="forecast-temp">
                            <span class="high">{{ $day['high'] ?? '--' }}</span>
                            <span class="low text-muted">{{ $day['low'] ?? '--' }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted py-3">
                    <i class="fas fa-cloud-sun fa-2x mb-2"></i>
                    <p>Weather forecast not available</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="weather-footer text-end mt-3">
        <button class="btn btn-sm btn-outline-primary" onclick="loadWeatherInfo()">
            <i class="fas fa-sync-alt me-1"></i>
            Refresh
        </button>
    </div>
</div>

@push('styles')
<style>
.weather-info-container {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
}

.current-weather {
    background: #f8fafc;
    padding: 1.25rem;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.location {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
}

.current-temp {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e40af;
}

.weather-details {
    margin-top: 1rem;
}

.weather-condition {
    flex: 1;
}

.weather-icon {
    font-size: 2rem;
    color: #3b82f6;
}

.weather-text {
    font-size: 0.9rem;
    color: #4b5563;
}

.weather-stats {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.weather-stat {
    margin: 0.25rem 0;
    font-size: 0.9rem;
    color: #4b5563;
}

.forecast-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #4b5563;
    display: flex;
    align-items: center;
}

.forecast-items {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    padding: 0.5rem 0;
    gap: 0.75rem;
}

.forecast-item {
    min-width: 80px;
    text-align: center;
    padding: 0.75rem 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.forecast-day {
    font-size: 0.8rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #4b5563;
}

.forecast-icon {
    font-size: 1.25rem;
    margin: 0.5rem 0;
}

.forecast-temp {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.85rem;
}

.forecast-temp .high {
    font-weight: 600;
    color: #1e40af;
}

.forecast-temp .low {
    color: #6b7280;
}

.weather-footer {
    border-top: 1px solid #e2e8f0;
    padding-top: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .weather-info-container {
        padding: 1rem;
    }
    
    .section-title {
        font-size: 1rem;
    }
    
    .current-weather {
        padding: 1rem;
    }
    
    .forecast-item {
        min-width: 70px;
        padding: 0.5rem 0.25rem;
    }
    
    .forecast-temp {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>
@endpush
