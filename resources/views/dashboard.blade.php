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
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-home"></i>
                        <span>{{ Auth::user()->farms->count() }} {{ Str::plural('Farm', Auth::user()->farms->count()) }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-seedling"></i>
                        <span>Dashboard</span>
                    </div>
                </div>
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
        <div class="section" id="farm-section-{{ $farm->id }}">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tractor section-icon"></i>
                    Farm Overview
                </h2>
                <div class="farm-actions">
                    <button class="btn btn-sm btn-outline-primary refresh-crop-data" data-farm-id="{{ $farm->id }}">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-outline-warning force-update-progress" data-farm-id="{{ $farm->id }}" title="Force update progress based on time">
                        <i class="fas fa-clock"></i> Update Time
                    </button>
                </div>
            </div>
            
            <div class="farm-grid" id="farm-grid-{{ $farm->id }}">
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
                    <div class="card-content" id="growth-content-{{ $farm->id }}">
                        <div class="crop-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading crop data...</span>
                            </div>
                            <p class="mt-2">Loading crop growth data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <!-- No Farms Message -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tractor section-icon"></i>
                    Farm Overview
                </h2>
            </div>
            <div class="no-farms-message">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-seedling" style="font-size: 4rem; color: #64748b; opacity: 0.5;"></i>
                    </div>
                    <h3 class="mb-3" style="color: #64748b;">No Farms Found</h3>
                    <p class="mb-4" style="color: #94a3b8;">You haven't created any farms yet. Create your first farm to start tracking crop growth!</p>
                    <a href="{{ route('crop-growth.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Your First Farm
                    </a>
                </div>
            </div>
        </div>
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
            <p class="section-subtitle">Access all your farming tools and features quickly</p>
        </div>
        
        <div class="actions-grid">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-tractor"></i>
                </div>
                <h3 class="action-title">Manage Farms</h3>
                <p class="action-description">Create, edit, and monitor your watermelon farms</p>
                <a href="{{ route('crop-growth.index') }}" class="action-button">
                    <i class="fas fa-cog"></i>
                    Manage Farms
                </a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <h3 class="action-title">Photo Diagnosis</h3>
                <p class="action-description">Upload crop photos for AI-powered health analysis</p>
                <a href="{{ route('photo-diagnosis.index') }}" class="action-button">
                    <i class="fas fa-upload"></i>
                    Upload Photos
                </a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="action-title">Track Progress</h3>
                <p class="action-description">Monitor crop growth stages and get recommendations</p>
                <a href="{{ route('crop-progress.index') }}" class="action-button">
                    <i class="fas fa-eye"></i>
                    View Progress
                </a>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <h3 class="action-title">Weather Data</h3>
                <p class="action-description">Check forecasts and weather-based farming tips</p>
                <a href="{{ route('weather.index') }}" class="action-button">
                    <i class="fas fa-cloud"></i>
                    Weather Info
                </a>
            </div>
        </div>
    </div>

    <!-- System Status & Quick Stats -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-chart-pie section-icon"></i>
                System Overview
            </h2>
            <p class="section-subtitle">Monitor your farming system performance and key metrics</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ Auth::user()->farms->count() }}</h3>
                    <p class="stat-label">Active Farms</p>
                    <p class="stat-description">Watermelon farms under management</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ App\Models\PhotoAnalysis::where('user_id', Auth::id())->count() }}</h3>
                    <p class="stat-label">Photo Analyses</p>
                    <p class="stat-description">AI-powered crop health assessments</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ App\Models\CropProgressUpdate::where('user_id', Auth::id())->count() }}</h3>
                    <p class="stat-label">Progress Updates</p>
                    <p class="stat-description">Crop growth monitoring records</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ Auth::user()->farms->where('weather_enabled', true)->count() }}</h3>
                    <p class="stat-label">Weather Monitoring</p>
                    <p class="stat-description">Farms with weather integration</p>
                </div>
            </div>
        </div>
        
        <!-- Additional System Info -->
        <div class="system-info-grid">
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-info-circle info-icon"></i>
                    <h4>System Status</h4>
                </div>
                <div class="info-content">
                    <div class="status-item">
                        <span class="status-dot active"></span>
                        <span>AI Analysis System</span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot active"></span>
                        <span>Weather API</span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot active"></span>
                        <span>Database Connection</span>
                    </div>
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-header">
                    <i class="fas fa-clock info-icon"></i>
                    <h4>Last Updated</h4>
                </div>
                <div class="info-content">
                    <p class="update-time">{{ now()->format('M d, Y H:i') }}</p>
                    <p class="update-note">All systems operational</p>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded, initializing crop data monitoring...');
    
    // Check if we have the required elements
    const farmSections = document.querySelectorAll('[id^="farm-section-"]');
    console.log(`Found ${farmSections.length} farm sections`);
    
    // Wait a bit to ensure authentication is properly loaded
    setTimeout(() => {
        if (isUserAuthenticated()) {
            console.log('User authenticated, checking for farms...');
            
            if (userHasFarms()) {
                console.log('User has farms, loading crop data...');
                
                // Load crop growth data for all farms
                try {
                    loadCropGrowthData();
                } catch (error) {
                    console.error('Error loading crop growth data:', error);
                }
            } else {
                console.log('User has no farms, skipping crop data loading');
            }
            
            // Load weather data for all farms
            try {
                loadWeatherData();
            } catch (error) {
                console.error('Error loading weather data:', error);
            }
            
            // Set up refresh buttons
            try {
                setupRefreshButtons();
                setupCropRefreshButtons();
            } catch (error) {
                console.error('Error setting up refresh buttons:', error);
            }
            
            // Auto-refresh crop data every 15 minutes
            setInterval(() => {
                try {
                    loadCropGrowthData();
                } catch (error) {
                    console.error('Error in auto-refresh crop data:', error);
                }
            }, 15 * 60 * 1000);
            
            // Auto-refresh weather every 30 minutes
            setInterval(() => {
                try {
                    loadWeatherData();
                } catch (error) {
                    console.error('Error in auto-refresh weather:', error);
                }
            }, 30 * 60 * 1000);
            
            // Real-time crop data monitoring (check every 2 minutes for changes)
            setInterval(() => {
                try {
                    checkCropDataUpdates();
                } catch (error) {
                    console.error('Error in crop data monitoring:', error);
                }
            }, 2 * 60 * 1000);
        } else {
            console.error('User not authenticated, cannot load crop data');
            // Show authentication error on all insights containers
            const insightsContainers = document.querySelectorAll('[id^="insights-container-"]');
            insightsContainers.forEach(container => {
                displayCropInsightsError(container, 'Authentication required. Please log in to view crop insights.');
            });
            
            // Also show error on growth containers
            const growthContainers = document.querySelectorAll('[id^="growth-content-"]');
            growthContainers.forEach(container => {
                displayCropGrowthError(container, 'Authentication required. Please log in to view crop data.');
            });
            
            // Show manual refresh option
            showManualRefreshOption();
        }
    }, 500);
});

function loadCropGrowthData() {
    console.log('Loading crop growth data...');
    
    // Check if user is authenticated
    if (!isUserAuthenticated()) {
        console.error('User not authenticated, cannot load crop data');
        return;
    }
    
    const growthContainers = document.querySelectorAll('[id^="growth-content-"]');
    const insightsContainers = document.querySelectorAll('[id^="insights-container-"]');
    
    console.log(`Found ${growthContainers.length} growth containers and ${insightsContainers.length} insights containers`);
    
    if (growthContainers.length === 0 && insightsContainers.length === 0) {
        console.log('No containers found, user may not have farms or containers not loaded yet');
        
        // Check if we have farm sections but no growth containers (indicating farms exist but containers failed to render)
        const farmSections = document.querySelectorAll('[id^="farm-section-"]');
        if (farmSections.length > 0) {
            console.error('Farm sections found but no growth containers - potential rendering issue');
            farmSections.forEach(section => {
                const farmId = section.id.replace('farm-section-', '');
                const growthContainer = document.getElementById(`growth-content-${farmId}`);
                if (growthContainer) {
                    displayCropGrowthError(growthContainer, 'Failed to initialize crop growth display. Please refresh the page.');
                }
            });
        }
        return;
    }
    
    growthContainers.forEach(container => {
        try {
            const farmId = container.id.replace('growth-content-', '');
            console.log(`Loading crop growth data for farm ${farmId}`);
            fetchCropGrowthData(farmId);
        } catch (error) {
            console.error('Error processing growth container:', error);
        }
    });
    
    insightsContainers.forEach(container => {
        try {
            const farmId = container.id.replace('insights-container-', '');
            console.log(`Loading crop insights data for farm ${farmId}`);
            fetchCropInsightsData(farmId);
        } catch (error) {
            console.error('Error processing insights container:', error);
        }
    });
}

function isUserAuthenticated() {
    // Check for CSRF token
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.log('CSRF token not found');
        return false;
    }
    
    // Check if we're on the dashboard page (not login/register)
    const currentPath = window.location.pathname;
    if (currentPath === '/login' || currentPath === '/register') {
        console.log('On login/register page');
        return false;
    }
    
    // Check if user name is displayed (indicating authentication)
    const userNameElement = document.querySelector('.welcome-title');
    if (userNameElement && userNameElement.textContent.includes('Welcome back')) {
        console.log('User name found in welcome title');
        return true;
    }
    
    // Additional check: look for logout button or user dropdown
    const logoutButton = document.querySelector('a[href*="logout"], button[onclick*="logout"]');
    if (logoutButton) {
        console.log('Logout button found');
        return true;
    }
    
    console.log('No authentication indicators found');
    return false;
}

function userHasFarms() {
    const farmSections = document.querySelectorAll('[id^="farm-section-"]');
    const noFarmsMessage = document.querySelector('.no-farms-message');
    
    console.log(`Farm sections found: ${farmSections.length}, No farms message: ${noFarmsMessage ? 'present' : 'absent'}`);
    
    return farmSections.length > 0;
}

function checkCropDataUpdates() {
    // Check if user is still authenticated
    if (!isUserAuthenticated()) {
        console.log('User no longer authenticated, stopping crop data updates');
        return;
    }
    
    // Check if any crop data has been updated recently
    fetch('/crop-growth/dashboard-data', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            // Check if response is HTML (login page) instead of JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                throw new Error('Session expired - received HTML response');
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Check if any farm data has been updated in the last 5 minutes
                const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000);
                const updatedFarms = data.data.filter(farm => {
                    const lastUpdated = new Date(farm.last_updated);
                    return lastUpdated > fiveMinutesAgo;
                });
                
                if (updatedFarms.length > 0) {
                    console.log('Crop data updates detected, refreshing dashboard...');
                    
                    // Show notification for updates
                    updatedFarms.forEach(farm => {
                        showCropUpdateNotification(farm);
                    });
                    
                    // Refresh the dashboard data
                    loadCropGrowthData();
                }
            }
        })
        .catch(error => {
            console.error('Error checking for crop data updates:', error);
            
            if (error.message.includes('Session expired')) {
                console.log('Session expired, showing notification');
                showSessionExpiredNotification();
            } else if (error.message.includes('HTML response')) {
                console.log('Received HTML response, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Authentication required')) {
                console.log('Authentication required, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('CSRF token not found')) {
                console.log('CSRF token not found, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Not authenticated')) {
                console.log('Not authenticated, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Failed to load')) {
                console.log('Failed to load data, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Farm data not found')) {
                console.log('Farm data not found, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('API returned error')) {
                console.log('API returned error, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Missing required data')) {
                console.log('Missing required data, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Incomplete data')) {
                console.log('Incomplete data, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Farm data not found for ID')) {
                console.log('Farm data not found for ID, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Error fetching crop insights data')) {
                console.log('Error fetching crop insights data, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Error checking for crop data updates')) {
                console.log('Error checking for crop data updates, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Error fetching crop growth data')) {
                console.log('Error fetching crop growth data, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Error fetching weather data')) {
                console.log('Error fetching weather data, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Error checking for weather data updates')) {
                console.log('Error checking for weather data updates, showing manual refresh option');
                showManualRefreshOption();
            } else if (error.message.includes('Error fetching crop data')) {
                console.log('Error fetching crop data, showing manual refresh option');
                showManualRefreshOption();
            }
        });
}

function showCropUpdateNotification(farmData) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'crop-update-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-seedling"></i>
            </div>
            <div class="notification-text">
                <h5>Crop Growth Updated</h5>
                <p>${farmData.farm_name}: ${farmData.stage_name} - ${Math.round(farmData.stage_progress)}% complete</p>
            </div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
    
    // Update sync indicator to show recent activity
    updateSyncIndicator(true);
}

function showSessionExpiredNotification() {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'session-expired-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="notification-text">
                <h5>Session Expired</h5>
                <p>Your session has expired. Please log in again to continue.</p>
            </div>
            <div class="notification-actions">
                <a href="/login" class="btn btn-primary btn-sm">Login</a>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 10000);
}

function showRefreshNotification() {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'refresh-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="notification-text">
                <h5>Page Refresh Required</h5>
                <p>Please refresh the page to reload your session and view crop insights.</p>
            </div>
            <div class="notification-actions">
                <button class="btn btn-primary btn-sm" onclick="window.location.reload()">Refresh Page</button>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 15 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 15000);
}

function redirectToLogin() {
    // Show a brief message before redirecting
    const notification = document.createElement('div');
    notification.className = 'redirect-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <div class="notification-text">
                <h5>Redirecting to Login</h5>
                <p>Please log in to continue using the dashboard.</p>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Redirect after 2 seconds
    setTimeout(() => {
        window.location.href = '/login';
    }, 2000);
}

function showManualRefreshOption() {
    // Show a notification with manual refresh option
    const notification = document.createElement('div');
    notification.className = 'manual-refresh-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="notification-text">
                <h5>Manual Refresh Required</h5>
                <p>Click the refresh button below to reload your session and view crop insights.</p>
            </div>
            <div class="notification-actions">
                <button class="btn btn-primary btn-sm" onclick="window.location.reload()">Refresh Now</button>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto-remove after 20 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 20000);
}

function updateSyncIndicator(isActive = true) {
    const indicator = document.getElementById('sync-indicator');
    if (indicator) {
        if (isActive) {
            indicator.classList.add('active');
            indicator.innerHTML = `
                <i class="fas fa-sync-alt fa-spin"></i>
                <span>Real-time crop monitoring active - Updates detected</span>
            `;
            
            // Reset to normal state after 3 seconds
            setTimeout(() => {
                indicator.innerHTML = `
                    <i class="fas fa-sync-alt fa-spin"></i>
                    <span>Real-time crop monitoring active</span>
                `;
            }, 3000);
        } else {
            indicator.classList.remove('active');
            indicator.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <span>Crop monitoring paused</span>
            `;
        }
    }
}





function forceUpdateCropProgress(farmId) {
    // Call the backend to force update crop progress
    fetch(`/crop-growth/farm/${farmId}/force-update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showCropUpdateNotification({
                farm_name: 'Farm',
                stage_name: data.crop_growth.current_stage,
                stage_progress: data.crop_growth.stage_progress
            });
            
            // Refresh the data
            fetchCropGrowthData(farmId);
            fetchCropInsightsData(farmId);
        } else {
            console.error('Failed to force update:', data.message);
            // Show error notification
            const notification = document.createElement('div');
            notification.className = 'crop-update-notification';
            notification.style.background = 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)';
            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="notification-text">
                        <h5>Update Failed</h5>
                        <p>${data.message}</p>
                    </div>
                    <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
        }
    })
    .catch(error => {
        console.error('Error forcing update:', error);
        // Refresh anyway to show current state
        fetchCropGrowthData(farmId);
        fetchCropInsightsData(farmId);
    });
}





function loadWeatherData() {
    const weatherContainers = document.querySelectorAll('.weather-container');
    
    weatherContainers.forEach(container => {
        const farmId = container.dataset.farmId;
        fetchWeatherData(farmId);
    });
}

function fetchCropGrowthData(farmId) {
    const container = document.getElementById(`growth-content-${farmId}`);
    
    if (!container) {
        console.error(`Container not found for farm ${farmId}`);
        return;
    }
    
    // Show loading state
    container.innerHTML = `
        <div class="crop-loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading crop data...</p>
        </div>
    `;
    
    // Check authentication before making request
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found');
        displayCropGrowthError(container, 'Authentication required. Please refresh the page.');
        return;
    }
    
    console.log('Making request to /crop-growth/dashboard-data with token:', token.getAttribute('content').substring(0, 10) + '...');
    console.log('Farm ID:', farmId);
    
    // Add timeout and better error handling
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
    
    fetch(`/crop-growth/dashboard-data`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId); // Clear the timeout
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        console.log('Response URL:', response.url);
        
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Authentication required');
            } else if (response.status === 403) {
                throw new Error('Access denied');
            } else if (response.status === 500) {
                throw new Error('Server error');
            } else if (response.status === 419) {
                throw new Error('CSRF token expired. Please refresh the page.');
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Dashboard data received:', data);
        
        if (data.success) {
            if (data.data && data.data.length > 0) {
                const farmData = data.data.find(farm => farm.farm_id == farmId);
                if (farmData) {
                    console.log(`Found data for farm ${farmId}:`, farmData);
                    displayCropGrowthData(container, farmData);
                } else {
                    console.error(`No data found for farm ${farmId} in response`);
                    displayCropGrowthError(container, 'Farm data not found in response');
                }
            } else {
                console.error('No farm data in response');
                displayCropGrowthError(container, 'No farm data available');
            }
        } else {
            console.error('API returned error:', data.message);
            displayCropGrowthError(container, data.message || 'Failed to load crop data');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId); // Clear the timeout
        console.error('Error fetching crop growth data:', error);
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        
        let errorMessage = 'Failed to load crop growth data. Please try again.';
        let debugInfo = `Error: ${error.name} - ${error.message}`;
        
        if (error.name === 'AbortError') {
            errorMessage = 'Request timed out. Please check your connection and try again.';
            debugInfo += ' - Request was aborted due to timeout';
        } else if (error.message.includes('Authentication required')) {
            errorMessage = 'Please refresh the page and log in again.';
        } else if (error.message.includes('CSRF token expired')) {
            errorMessage = 'Session expired. Please refresh the page.';
        } else if (error.message.includes('Access denied')) {
            errorMessage = 'Access denied. Please check your permissions.';
        } else if (error.message.includes('Server error')) {
            errorMessage = 'Server error. Please try again later.';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage = 'Network error. The server might be down or unreachable. Please check if the Laravel server is running on http://localhost:8000';
            debugInfo += ' - This usually means the Laravel development server is not running.';
        } else if (error.message.includes('NetworkError')) {
            errorMessage = 'Network connection failed. Check your internet connection.';
        } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
            errorMessage = 'Connection error. Please check if the server is running on http://localhost:8000';
            debugInfo += ' - Make sure to run: php artisan serve';
        } else if (error.name === 'TypeError') {
            errorMessage = 'Connection error. Please check if the server is running.';
        }
        
        console.log('Final error message:', errorMessage);
        console.log('Debug info:', debugInfo);
        
        displayCropGrowthError(container, errorMessage);
    });
}

function fetchCropInsightsData(farmId) {
    const container = document.getElementById(`insights-container-${farmId}`);
    
    console.log(`Fetching crop insights for farm ${farmId}`);
    
    // Check if user is authenticated by looking for CSRF token
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found - user may not be authenticated');
        displayCropInsightsError(container, 'Authentication required. Please refresh the page and try again.');
        return;
    }
    
    // Add timeout and better error handling
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
    
    fetch(`/crop-growth/dashboard-data`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': token.getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        signal: controller.signal
    })
        .then(response => {
            clearTimeout(timeoutId); // Clear the timeout
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            // Check if response is HTML (login page) instead of JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                throw new Error('Authentication required - received HTML response');
            }
            
            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Authentication required');
                } else if (response.status === 403) {
                    throw new Error('Access denied');
                } else if (response.status === 419) {
                    throw new Error('CSRF token expired. Please refresh the page.');
                } else if (response.status === 500) {
                    throw new Error('Server error');
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.success) {
                const farmData = data.data.find(farm => farm.farm_id == farmId);
                console.log('Found farm data:', farmData);
                if (farmData) {
                    displayCropInsightsData(container, farmData);
                } else {
                    console.error('Farm data not found for ID:', farmId);
                    displayCropInsightsError(container, 'Farm data not found');
                }
            } else {
                console.error('API returned error:', data.message);
                displayCropInsightsError(container, data.message || 'Failed to load insights data');
            }
        })
        .catch(error => {
            clearTimeout(timeoutId); // Clear the timeout
            console.error('Error fetching crop insights data:', error);
            console.error('Error name:', error.name);
            console.error('Error message:', error.message);
            
            let errorMessage = 'Failed to load insights data. Please try again.';
            
            if (error.name === 'AbortError') {
                errorMessage = 'Request timed out. Please check your connection and try again.';
            } else if (error.message.includes('Authentication required') || error.message.includes('HTML response')) {
                errorMessage = 'Authentication required. Please refresh the page and log in again.';
                showManualRefreshOption();
            } else if (error.message.includes('CSRF token expired')) {
                errorMessage = 'Session expired. Please refresh the page.';
                showSessionExpiredNotification();
            } else if (error.message.includes('Server error')) {
                errorMessage = 'Server error. Please try again later.';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error. Please check if the server is running.';
            } else if (error.message.includes('Farm data not found')) {
                errorMessage = 'Farm data not found. Please refresh the page and try again.';
                showManualRefreshOption();
            } else {
                errorMessage = error.message || 'Failed to load insights data. Please try again.';
            }
            
            displayCropInsightsError(container, errorMessage);
        });
}

function fetchWeatherData(farmId, forceRefresh = false) {
    const container = document.getElementById(`weather-container-${farmId}`);
    
    // Use refresh endpoint if forcing refresh
    const url = forceRefresh ? `/weather/farm/${farmId}/refresh` : `/weather/farm/${farmId}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayWeatherData(container, data.data);
                if (forceRefresh) {
                    // Show success message briefly
                    const successMsg = document.createElement('div');
                    successMsg.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000; font-size: 14px;';
                    successMsg.innerHTML = '✅ Weather data refreshed successfully';
                    document.body.appendChild(successMsg);
                    setTimeout(() => successMsg.remove(), 3000);
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

function displayCropGrowthData(container, farmData) {
    const {
        current_stage,
        stage_name,
        stage_icon,
        stage_color,
        stage_progress,
        overall_progress,
        harvest_date_formatted,
        days_remaining,
        days_elapsed,
        growth_status,
        last_updated,
        planting_date_formatted,
        next_stage,
        next_stage_name,
        can_advance,
        total_growth_period
    } = farmData;
    
    // Calculate more accurate progress metrics
    const progressMetrics = calculateAccurateProgress(farmData);
    
    container.innerHTML = `
        <div class="growth-progress-compact">
            <!-- Current Stage -->
            <div class="stage-header">
                <span class="stage-badge" style="background: ${stage_color};">
                    ${stage_icon} ${stage_name}
                </span>
                <span class="stage-percent">${Math.round(stage_progress)}%</span>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${Math.max(2, stage_progress)}%; background: ${stage_color};"></div>
                </div>
                <div class="progress-text">${Math.round(stage_progress)}% Stage Complete</div>
            </div>
            
            <!-- Key Info -->
            <div class="growth-info">
                <div class="info-row">
                    <span class="info-label">Days Elapsed</span>
                    <span class="info-value">${Math.round(days_elapsed)} days</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Days Remaining</span>
                    <span class="info-value">${Math.round(days_remaining)} days</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Est. Harvest</span>
                    <span class="info-value">${harvest_date_formatted}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Overall Progress</span>
                    <span class="info-value">${Math.round(overall_progress)}% Complete</span>
                </div>
            </div>
            
            <!-- Status -->
            <div class="growth-status">
                <div class="status-badge ${growth_status.color}">
                    <i class="${growth_status.icon}"></i>
                    <span>${growth_status.message}</span>
                </div>
            </div>
        </div>
    `;
}

function calculateAccurateProgress(farmData) {
    const { current_stage, stage_progress, overall_progress, days_elapsed, total_growth_period } = farmData;
    
    // Stage durations (in days)
    const stageDurations = {
        'seedling': 20,
        'vegetative': 25,
        'flowering': 15,
        'fruiting': 20,
        'harvest': 0
    };
    
    const totalDays = total_growth_period || 80;
    let stageExplanation = '';
    let overallExplanation = '';
    
    // Calculate accurate stage explanation
    switch (current_stage) {
        case 'seedling':
            const seedlingDays = Math.min(days_elapsed, stageDurations.seedling);
            stageExplanation = `${Math.round(seedlingDays)} of ${stageDurations.seedling} days completed in seedling stage`;
            break;
            
        case 'vegetative':
            const vegDaysElapsed = Math.max(0, days_elapsed - stageDurations.seedling);
            const vegDays = Math.min(vegDaysElapsed, stageDurations.vegetative);
            stageExplanation = `${Math.round(vegDays)} of ${stageDurations.vegetative} days completed in vegetative stage`;
            break;
            
        case 'flowering':
            const flowerDaysElapsed = Math.max(0, days_elapsed - stageDurations.seedling - stageDurations.vegetative);
            const flowerDays = Math.min(flowerDaysElapsed, stageDurations.flowering);
            stageExplanation = `${Math.round(flowerDays)} of ${stageDurations.flowering} days completed in flowering stage`;
            break;
            
        case 'fruiting':
            const fruitDaysElapsed = Math.max(0, days_elapsed - stageDurations.seedling - stageDurations.vegetative - stageDurations.flowering);
            const fruitDays = Math.min(fruitDaysElapsed, stageDurations.fruiting);
            stageExplanation = `${Math.round(fruitDays)} of ${stageDurations.fruiting} days completed in fruiting stage`;
            break;
            
        case 'harvest':
            stageExplanation = 'All growth stages completed - ready for harvest';
            break;
            
        default:
            stageExplanation = `${Math.round(stage_progress)}% complete in ${current_stage} stage`;
    }
    
    // Calculate overall explanation
    const daysCompleted = Math.min(days_elapsed, totalDays);
    const calculatedOverall = Math.min(100, (daysCompleted / totalDays) * 100);
    
    overallExplanation = `${Math.round(daysCompleted)} of ${totalDays} total growing days completed`;
    
    return {
        stageExplanation,
        overallExplanation,
        daysCompleted,
        totalDays,
        calculatedOverall
    };
}

function generateTimelineStages(farmData) {
    const { current_stage, days_elapsed } = farmData;
    
    const stages = [
        { key: 'seedling', name: 'Seedling', icon: '🌱', duration: 20, color: '#10b981' },
        { key: 'vegetative', name: 'Vegetative', icon: '🌿', duration: 25, color: '#059669' },
        { key: 'flowering', name: 'Flowering', icon: '🌸', duration: 15, color: '#8b5cf6' },
        { key: 'fruiting', name: 'Fruiting', icon: '🍉', duration: 20, color: '#f59e0b' },
        { key: 'harvest', name: 'Harvest', icon: '✂️', duration: 0, color: '#dc2626' }
    ];
    
    let cumulativeDays = 0;
    let stagesHtml = '';
    
    stages.forEach((stage, index) => {
        const stageStart = cumulativeDays;
        const stageEnd = cumulativeDays + stage.duration;
        const isCurrentStage = stage.key === current_stage;
        const isCompleted = days_elapsed >= stageEnd;
        const isActive = days_elapsed >= stageStart && days_elapsed < stageEnd;
        
        let stageClass = 'timeline-stage';
        if (isCompleted) stageClass += ' completed';
        if (isCurrentStage) stageClass += ' current';
        if (isActive) stageClass += ' active';
        
        // Calculate progress within this stage
        let stageProgress = 0;
        if (isCompleted) {
            stageProgress = 100;
        } else if (isActive || isCurrentStage) {
            const daysInStage = days_elapsed - stageStart;
            stageProgress = stage.duration > 0 ? Math.min(100, (daysInStage / stage.duration) * 100) : 0;
        }
        
        stagesHtml += `
            <div class="${stageClass}" style="--stage-color: ${stage.color}">
                <div class="stage-marker">
                    <span class="stage-icon">${stage.icon}</span>
                    ${isCurrentStage ? '<div class="current-indicator"></div>' : ''}
                </div>
                <div class="stage-info">
                    <div class="stage-name">${stage.name}</div>
                    <div class="stage-duration">${stage.duration > 0 ? stage.duration + ' days' : 'Ready'}</div>
                    ${isActive || isCurrentStage ? `<div class="stage-progress">${Math.round(stageProgress)}%</div>` : ''}
                </div>
            </div>
        `;
        
        cumulativeDays += stage.duration;
    });
    
    return stagesHtml;
}

// Keep the old function for backward compatibility
function getProgressExplanation(farmData) {
    return calculateAccurateProgress(farmData);
}

function displayCropInsightsData(container, farmData) {
    console.log('Displaying crop insights data:', farmData);
    
    const {
        nutrient_predictions,
        harvest_countdown,
        current_stage,
        stage_name
    } = farmData;
    
    // Check if required data exists
    if (!nutrient_predictions || !harvest_countdown) {
        console.error('Missing required data:', { nutrient_predictions, harvest_countdown });
        container.innerHTML = `
            <div class="insights-error">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="error-message">
                    <h4>Incomplete Data</h4>
                    <p>Missing nutrient predictions or harvest countdown data.</p>

                    <button class="btn btn-success btn-sm retry-insights" onclick="fetchCropInsightsData('${container.id.replace('insights-container-', '')}')">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                </div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="insights-grid">
            <div class="insight-card nutrient-predictions">
                <div class="card-header">
                    <h4><i class="fas fa-flask"></i> Nutrient Requirements</h4>
                </div>
                <div class="card-content">
                    <div class="nutrient-item">
                        <span class="nutrient-label">Nitrogen (N):</span>
                        <span class="nutrient-value">${nutrient_predictions.nitrogen}</span>
                    </div>
                    <div class="nutrient-item">
                        <span class="nutrient-label">Phosphorus (P):</span>
                        <span class="nutrient-value">${nutrient_predictions.phosphorus}</span>
                    </div>
                    <div class="nutrient-item">
                        <span class="nutrient-label">Potassium (K):</span>
                        <span class="nutrient-value">${nutrient_predictions.potassium}</span>
                    </div>
                    
                    <div class="recommendations">
                        <h5><i class="fas fa-lightbulb"></i> Recommendations</h5>
                        <ul>
                            ${nutrient_predictions.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="insight-card harvest-countdown">
                <div class="card-header">
                    <h4><i class="fas fa-calendar-check"></i> Harvest Countdown</h4>
                </div>
                <div class="card-content">
                    <div class="countdown-status ${harvest_countdown.color}">
                        <i class="${harvest_countdown.icon}"></i>
                        <span class="countdown-message">${harvest_countdown.message}</span>
                    </div>
                    
                    ${harvest_countdown.days > 0 ? `
                        <div class="countdown-days">
                            <span class="days-number">${harvest_countdown.days}</span>
                            <span class="days-label">days</span>
                        </div>
                    ` : ''}
                    
                    <div class="stage-info">
                        <span class="current-stage">${stage_name}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}



function displayCropGrowthError(container, message) {
    const farmId = container.id.replace('growth-content-', '');
    container.innerHTML = `
        <div class="crop-error">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="error-message">
                <h4>Crop Data Unavailable</h4>
                <p>${message}</p>
                <div class="error-actions">
                    <button class="btn btn-primary btn-sm retry-crop" onclick="fetchCropGrowthData('${farmId}')">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.location.reload()">
                        <i class="fas fa-refresh"></i> Refresh Page
                    </button>
                    <a href="/debug/crop-data" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-bug"></i> Debug Info
                    </a>
                </div>
            </div>
        </div>
    `;
}

function displayCropInsightsError(container, message) {
    let actionButton = '';
    
    if (message.includes('Authentication required')) {
        actionButton = `
            <button class="btn btn-primary btn-sm" onclick="window.location.reload()">
                <i class="fas fa-refresh"></i> Refresh Page
            </button>
            <a href="/login" class="btn btn-outline-primary btn-sm ms-2">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        `;
    } else {
        actionButton = `
            <button class="btn btn-success btn-sm retry-insights" onclick="fetchCropInsightsData('${container.id.replace('insights-container-', '')}')">
                <i class="fas fa-redo"></i> Try Again
            </button>
        `;
    }
    
    container.innerHTML = `
        <div class="insights-error">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="error-message">
                <h4>Insights Data Unavailable</h4>
                <p>${message}</p>
                <div class="error-actions">
                    ${actionButton}
                </div>
            </div>
        </div>
    `;
}

function displayWeatherData(container, weatherData) {
    const { current, forecast, alerts, farm } = weatherData;
    
    // Debug: Log forecast data length - v2.7 (Replaced with emoji-based Heroicons)
    console.log('=== WEATHER DEBUG v2.7 ===');
    console.log('Forecast data received:', forecast ? forecast.length : 'null', 'days');
    if (forecast) {
        console.log('Forecast days:', forecast.map(day => day.date));
        console.log('Full forecast data:', forecast);
        console.log('Is fallback data?', forecast.some(day => day.is_fallback));
        
        // Check for duplicates in received data
        const dates = forecast.map(day => day.date);
        const uniqueDates = [...new Set(dates)];
        if (dates.length !== uniqueDates.length) {
            console.error('DUPLICATE DATES DETECTED in received data:', dates);
        } else {
            console.log('✓ No duplicates in received data');
        }
        
        // Test day name calculation for first few days
        console.log('Day name verification:');
        forecast.slice(0, 3).forEach(day => {
            const dayName = getDayName(day.date, day.day_name);
            console.log(`  ${day.date} -> ${dayName} (from backend: ${day.day_name || 'none'})`);
        });
        
        // Debug weather icons
        console.log('Weather icon debugging:');
        forecast.slice(0, 5).forEach(day => {
            const iconClass = getWeatherIcon(day.icon);
            console.log(`  ${day.date}: icon="${day.icon}" -> class="${iconClass}" desc="${day.description}"`);
        });
        
        // Verify today is not included in forecast
        const todayForCheck = new Date().toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric',
            timeZone: 'Asia/Manila'
        });
        const todayInForecast = forecast.some(day => day.date === todayForCheck);
        if (todayInForecast) {
            console.warn('⚠️ Today (' + todayForCheck + ') found in forecast - should only show future days');
        } else {
            console.log('✓ Today (' + todayForCheck + ') correctly excluded from forecast');
        }
    }
    
    // Filter out any past dates that might have slipped through (safety measure)
    const today = new Date().toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        timeZone: 'Asia/Manila'
    });
    
    const filteredForecast = forecast ? forecast.filter(day => {
        // Parse forecast date and compare with today
        const forecastDate = new Date(`${day.date}, ${new Date().getFullYear()}`);
        const todayDate = new Date();
        
        // Only include future dates
        return forecastDate > todayDate;
    }) : [];
    
    console.log('Filtered forecast (future dates only):', filteredForecast.map(day => day.date));
    
    // Ensure we have 10 days of forecast data
    let extendedForecast = filteredForecast;
    if (filteredForecast && filteredForecast.length < 10) {
        console.log('Extending forecast from', filteredForecast.length, 'to 10 days');
        extendedForecast = extendForecastTo10Days(filteredForecast);
        
        // Check for duplicates after extension
        const extendedDates = extendedForecast.map(day => day.date);
        const uniqueExtendedDates = [...new Set(extendedDates)];
        if (extendedDates.length !== uniqueExtendedDates.length) {
            console.error('DUPLICATE DATES DETECTED after extension:', extendedDates);
        } else {
            console.log('✓ No duplicates after extension. Final dates:', extendedDates);
        }
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
                            <span class="metric-value">${Math.round(current.wind_speed)} km/h</span>
                            <span class="metric-label">Wind</span>
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
                            <span class="stat-value">${Math.round(getTemperatureRangeValue(extendedForecast))}°C</span>
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
                                    <span class="day-name-compact">${getDayName(day.date, day.day_name)}</span>
                                    <span class="day-date-compact">${formatDate(day.date)}</span>
                                </div>
                                <div class="day-weather-compact">
                                    <i class="${getWeatherIcon(day.icon)} day-icon-compact" title="Weather: ${day.description}"></i>
                                    <span class="day-temp-compact">${Math.round(day.temperature)}°C</span>
                                </div>
                                <div class="day-metrics-compact">
                                    <span class="metric-compact">🌧️${getRainChanceForDay(day)}%</span>
                                    <span class="metric-compact">💨${Math.round(day.wind_speed)}</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="forecast-row">
                        ${extendedForecast.slice(5, 10).map(day => `
                            <div class="forecast-day-compact">
                                <div class="day-header-compact">
                                    <span class="day-name-compact">${getDayName(day.date, day.day_name)}</span>
                                    <span class="day-date-compact">${formatDate(day.date)}</span>
                                </div>
                                <div class="day-weather-compact">
                                    <i class="${getWeatherIcon(day.icon)} day-icon-compact" title="Weather: ${day.description}"></i>
                                    <span class="day-temp-compact">${Math.round(day.temperature)}°C</span>
                                </div>
                                <div class="day-metrics-compact">
                                    <span class="metric-compact">🌧️${getRainChanceForDay(day)}%</span>
                                    <span class="metric-compact">💨${Math.round(day.wind_speed)}</span>
                                </div>
                            </div>
                        `).join('')}
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

function getTemperatureRangeValue(forecast) {
    if (!forecast || forecast.length === 0) return 0;
    const temps = forecast.map(day => day.temperature);
    const min = Math.min(...temps);
    const max = Math.max(...temps);
    return max - min;
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

function getDayName(dateStr, dayName = null) {
    // If we have day_name from backend, use it directly (it's already in Manila timezone)
    if (dayName) {
        return dayName.substring(0, 3); // Convert "Monday" to "Mon"
    }
    
    // Handle date strings like "Aug 20" by adding current year
    let fullDateStr = dateStr;
    if (dateStr && !dateStr.includes(',') && !dateStr.includes('/')) {
        const currentYear = new Date().getFullYear();
        fullDateStr = `${dateStr}, ${currentYear}`;
    }
    
    const date = new Date(fullDateStr);
    
    // Verify the date parsed correctly
    if (isNaN(date.getTime())) {
        console.error('Invalid date string:', dateStr, 'parsed as:', fullDateStr);
        return 'N/A';
    }
    
    // Use Manila timezone for consistent day names
    return date.toLocaleDateString('en-US', { 
        weekday: 'short',
        timeZone: 'Asia/Manila'
    });
}

function formatDate(dateStr) {
    // Handle date strings like "Aug 20" by adding current year
    let fullDateStr = dateStr;
    if (dateStr && !dateStr.includes(',') && !dateStr.includes('/')) {
        const currentYear = new Date().getFullYear();
        fullDateStr = `${dateStr}, ${currentYear}`;
    }
    
    const date = new Date(fullDateStr);
    
    // Verify the date parsed correctly
    if (isNaN(date.getTime())) {
        console.error('Invalid date string in formatDate:', dateStr, 'parsed as:', fullDateStr);
        return dateStr; // Return original string if parsing fails
    }
    
    // Use Manila timezone for consistent date formatting
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        timeZone: 'Asia/Manila'
    });
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
    
    // If we have less than 10 days, extend with estimated data
    while (extendedForecast.length < 10) {
        const lastDay = extendedForecast[extendedForecast.length - 1];
        
        // Calculate next date properly
        let nextDate;
        if (lastDay.date && typeof lastDay.date === 'string') {
            // Try to parse the date string (format: "Aug 20" or "Aug 20")
            try {
                const dateParts = lastDay.date.split(' ');
                if (dateParts.length === 2) {
                    const monthStr = dateParts[0];
                    const day = parseInt(dateParts[1]);
                    const currentYear = new Date().getFullYear();
                    
                    // Create a date object from the parsed date
                    const currentDate = new Date(`${monthStr} ${day}, ${currentYear}`);
                    
                    // Add one day
                    nextDate = new Date(currentDate);
                    nextDate.setDate(currentDate.getDate() + 1);
                } else {
                    throw new Error('Invalid date format');
                }
            } catch (e) {
                // Fallback: calculate from current date + days elapsed
                nextDate = new Date();
                nextDate.setDate(nextDate.getDate() + extendedForecast.length);
            }
        } else {
            // Fallback: calculate from current date + days elapsed
            nextDate = new Date();
            nextDate.setDate(nextDate.getDate() + extendedForecast.length);
        }
        
        // Create estimated weather data for the next day
        const estimatedDay = {
            date: nextDate.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                timeZone: 'Asia/Manila'
            }),
            day_name: nextDate.toLocaleDateString('en-US', { 
                weekday: 'long',
                timeZone: 'Asia/Manila'
            }),
            time: '12:00',
            temperature: lastDay.temperature + Math.floor(Math.random() * 6) - 3, // ±3°C variation
            description: lastDay.description, // Keep similar description
            icon: lastDay.icon,
            humidity: Math.max(50, Math.min(90, lastDay.humidity + Math.floor(Math.random() * 10) - 5)), // ±5% variation
            wind_speed: Math.round(Math.max(3, Math.min(25, lastDay.wind_speed + Math.floor(Math.random() * 6) - 3))), // ±3 km/h variation, rounded
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

function getTimeAgo(timestamp) {
    if (!timestamp) return 'Unknown';
    
    try {
        const now = new Date();
        const time = new Date(timestamp);
        const diffInMinutes = Math.floor((now - time) / (1000 * 60));
        
        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) return `${diffInHours}h ago`;
        
        const diffInDays = Math.floor(diffInHours / 24);
        return `${diffInDays}d ago`;
    } catch (e) {
        return 'Recently';
    }
}

function refreshWeatherData() {
    const refreshBtn = document.querySelector('.refresh-weather-btn');
    if (refreshBtn) {
        const icon = refreshBtn.querySelector('i');
        icon.classList.add('fa-spin');
        refreshBtn.disabled = true;
    }
    
    // Force refresh weather data for all farms
    const weatherContainers = document.querySelectorAll('[id^="weather-container-"]');
    weatherContainers.forEach(container => {
        const farmId = container.id.replace('weather-container-', '');
        fetchWeatherData(farmId, true); // true = force refresh
    });
    
    // Re-enable button after 3 seconds
    setTimeout(() => {
        if (refreshBtn) {
            const icon = refreshBtn.querySelector('i');
            icon.classList.remove('fa-spin');
            refreshBtn.disabled = false;
        }
    }, 3000);
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

function setupCropRefreshButtons() {
    document.querySelectorAll('.refresh-crop-data').forEach(button => {
        button.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            const container = document.getElementById(`growth-content-${farmId}`);
            
            // Show loading state
            container.innerHTML = `
                <div class="crop-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Refreshing crop data...</span>
                    </div>
                    <p class="mt-2">Refreshing crop growth data...</p>
                </div>
            `;
            
            // Fetch fresh data
            fetchCropGrowthData(farmId);
        });
    });
    
    document.querySelectorAll('.force-update-progress').forEach(button => {
        button.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            const container = document.getElementById(`growth-content-${farmId}`);
            
            // Show loading state
            container.innerHTML = `
                <div class="crop-loading">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Updating progress...</span>
                    </div>
                    <p class="mt-2">Updating crop progress based on time...</p>
                </div>
            `;
            
            // Force update progress
            forceUpdateCropProgress(farmId);
        });
    });
    
    document.querySelectorAll('.refresh-insights').forEach(button => {
        button.addEventListener('click', function() {
            const farmId = this.dataset.farmId;
            const container = document.getElementById(`insights-container-${farmId}`);
            
            // Show loading state
            container.innerHTML = `
                <div class="insights-loading">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Refreshing insights...</span>
                    </div>
                    <p class="mt-2">Refreshing crop insights...</p>
                </div>
            `;
            
            // Fetch fresh data
            fetchCropInsightsData(farmId);
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
                            <span class="stat-value">${Math.round(summary.temperature.min)}°C - ${Math.round(summary.temperature.max)}°C</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-cloud-rain"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Total Rainfall</span>
                            <span class="stat-value">${Math.round(summary.rainfall.total)}mm</span>
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
                                    <span class="temp-min">${Math.round(day.temperature.min)}°</span>
                                    <span class="temp-separator">-</span>
                                    <span class="temp-max">${Math.round(day.temperature.max)}°</span>
                                </div>
                            </div>
                            <div class="day-rain">
                                <i class="fas fa-cloud-rain"></i>
                                <span>${Math.round(day.rainfall)}mm</span>
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
                                    <span class="temp-min">${Math.round(day.temperature.min)}°</span>
                                    <span class="temp-separator">-</span>
                                    <span class="temp-max">${Math.round(day.temperature.max)}°</span>
                                </div>
                            </div>
                            <div class="day-rain">
                                <i class="fas fa-cloud-rain"></i>
                                <span>${Math.round(day.rainfall)}mm</span>
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
    max-width: 1400px;
    width: 100%;
    margin: 0 auto;
    padding: 0 1.5rem;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    position: relative;
    z-index: 1;
    margin-top: 0 !important;
    padding-top: 1.5rem !important;
}

/* Welcome Section */
.welcome-section {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 25%, #60a5fa 50%, #93c5fd 75%, #dbeafe 100%);
    border-radius: 24px;
    padding: 3rem 2.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(30, 64, 175, 0.3);
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
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 2rem;
}

.welcome-text {
    flex: 1;
}

.welcome-title {
    font-size: 3rem;
    font-weight: 900;
    margin: 0 0 1rem 0;
    text-shadow: 0 4px 12px rgba(0,0,0,0.3);
    color: #ffffff !important;
}

.welcome-icon {
    font-size: 2rem;
    color: #ffd700;
}

.welcome-subtitle {
    font-size: 1.25rem;
    margin: 0 0 1.5rem 0;
    color: #ffffff !important;
    font-weight: 400;
}



.sync-indicator {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Header Stats */
.header-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #ffffff !important;
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
    margin-bottom: 2.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f1f5f9;
    text-align: center;
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

.section-subtitle {
    font-size: 1rem;
    color: #64748b;
    margin: 0.5rem 0 0 0;
    font-weight: 400;
    line-height: 1.5;
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
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    padding: 1rem 0;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .actions-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 0.5rem 0;
    }
    
    .action-card {
        padding: 1.5rem;
        min-height: 260px;
    }
    
    .action-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 1.25rem;
    }
    
    .action-title {
        font-size: 1.1rem;
    }
    
    .action-description {
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
}

/* Entrance animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.action-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

.action-card:nth-child(1) { animation-delay: 0.1s; }
.action-card:nth-child(2) { animation-delay: 0.2s; }
.action-card:nth-child(3) { animation-delay: 0.3s; }
.action-card:nth-child(4) { animation-delay: 0.4s; }

.action-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 20px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    min-height: 240px;
    display: flex;
    flex-direction: column;
    align-items: center;
    backdrop-filter: blur(10px);
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
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.action-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #e2e8f0 0%, #cbd5e1 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
}





.action-card:hover::before {
    transform: scaleX(1);
}

.action-card:hover::after {
    opacity: 1;
}

.action-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 48px rgba(0, 0, 0, 0.12);
    border-color: #cbd5e1;
}

.action-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 12px 32px rgba(59, 130, 246, 0.3);
    position: relative;
}

.action-icon::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 22px;
    z-index: -1;
    opacity: 0.3;
    filter: blur(8px);
    transition: opacity 0.4s ease;
}

.action-card:hover .action-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 16px 40px rgba(59, 130, 246, 0.4);
}

.action-card:hover .action-icon::before {
    opacity: 0.6;
    filter: blur(12px);
}

.action-icon i {
    font-size: 1.25rem;
    color: white;
}

.action-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    letter-spacing: -0.025em;
    background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.action-description {
    color: #64748b;
    margin-bottom: 1.5rem;
    line-height: 1.5;
    font-size: 0.9rem;
    flex-grow: 1;
    font-weight: 500;
    max-width: 100%;
}

.action-button {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    border: none;
    padding: 0.6rem 1.25rem;
    border-radius: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    font-size: 0.85rem;
    width: auto;
    min-width: 120px;
    box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.action-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.action-button:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 32px rgba(59, 130, 246, 0.4);
    color: white;
    text-decoration: none;
}

.action-button:hover::before {
    left: 100%;
}

.action-button:focus {
    outline: 3px solid #3b82f6;
    outline-offset: 3px;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(59, 130, 246, 0.35);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
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

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.25);
}

.stat-icon i {
    font-size: 1.2rem;
    color: white;
}

.stat-content {
    text-align: left;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 700;
    color: #475569;
    margin: 0 0 0.25rem 0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-description {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

/* Responsive adjustments for stats */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1.25rem;
    }
    
    .stat-number {
        font-size: 1.75rem;
    }
}

/* System Info Grid */
.system-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}

.info-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
}

.info-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.info-icon {
    color: #3b82f6;
    font-size: 1.1rem;
}

.info-header h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
}

.info-content {
    color: #64748b;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
}

.status-dot.active {
    background: #10b981;
}

.status-dot.inactive {
    background: #ef4444;
}

.update-time {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.update-note {
    font-size: 0.9rem;
    color: #10b981;
    margin: 0;
    font-weight: 600;
}

/* Responsive adjustments for system info */
@media (max-width: 768px) {
    .system-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-top: 1.5rem;
    }
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
    font-size: 1.5rem;
    color: inherit;
    display: inline-block;
    width: auto;
    height: auto;
    line-height: 1;
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



/* Farm Actions */
.farm-actions {
    display: flex;
    gap: 0.5rem;
}

.insights-actions {
    display: flex;
    gap: 0.5rem;
}

/* Crop Loading States */
.crop-loading,
.insights-loading {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

/* Overall Progress Bar */
.overall-progress-bar {
    width: 100%;
    height: 12px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 1rem;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-fill.overall {
    background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
    box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);
}

.progress-text.overall {
    color: #7c3aed;
}

/* Growth Status */
.growth-status {
    margin-top: 1rem;
}

.growth-status .status-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: white;
    text-align: center;
    justify-content: center;
}

.status-item.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.status-item.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.status-item.info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.status-item.primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.status-item.secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
}

.status-item.danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

/* Last Updated */
.last-updated {
    margin-top: 1rem;
    text-align: center;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

/* Progress Explanation */
.progress-explanation {
    margin: 1rem 0;
    padding: 1rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 12px;
    border: 1px solid #0ea5e9;
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.1);
}

.explanation-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #0ea5e9;
}

.explanation-header i {
    color: #0ea5e9;
    font-size: 1rem;
}

.explanation-header span {
    font-weight: 600;
    color: #0c4a6e;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.explanation-content p {
    margin: 0.25rem 0;
    font-size: 0.85rem;
    color: #0c4a6e;
    line-height: 1.4;
}

.explanation-content strong {
    color: #0369a1;
    font-weight: 600;
}

/* Insights Grid */
.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.insight-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.insight-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.insight-card:hover::before {
    transform: scaleX(1);
}

.insight-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
}

.insight-card .card-header {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}

.insight-card .card-header h4 {
    margin: 0;
    color: #1e293b;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.insight-card .card-header h4 i {
    color: #10b981;
}

/* Nutrient Predictions */
.nutrient-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s ease;
}

.nutrient-item:hover {
    background: #f8fafc;
    margin: 0 -0.5rem;
    padding: 0.75rem 0.5rem;
    border-radius: 8px;
}

.nutrient-item:last-child {
    border-bottom: none;
}

.nutrient-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
}

.nutrient-value {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.9rem;
}

.recommendations {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f1f5f9;
}

.recommendations h5 {
    margin: 0 0 0.75rem 0;
    color: #1e293b;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.recommendations h5 i {
    color: #f59e0b;
}

.recommendations ul {
    margin: 0;
    padding-left: 1.25rem;
}

.recommendations li {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

/* Harvest Countdown */
.countdown-status {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    color: white;
    text-align: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.countdown-status.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.countdown-status.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.countdown-status.info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.countdown-status.primary {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.countdown-status.danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

.countdown-message {
    font-size: 1.1rem;
}

.countdown-days {
    text-align: center;
    margin-bottom: 1rem;
}

.days-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
}

.days-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stage-info {
    text-align: center;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.current-stage {
    font-size: 1rem;
    font-weight: 600;
    color: #475569;
}

/* Error States */
.crop-error,
.insights-error {
    text-align: center;
    padding: 2rem;
    color: #64748b;
}

/* Crop Update Notifications */
.crop-update-notification,
.session-expired-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    z-index: 9999;
    animation: slideInRight 0.3s ease-out;
    max-width: 350px;
    overflow: hidden;
}

.crop-update-notification {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.session-expired-notification {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.refresh-notification {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.redirect-notification {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.manual-refresh-notification {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
}

.notification-icon {
    font-size: 1.25rem;
    color: #fbbf24;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.notification-text {
    flex: 1;
}

.notification-text h5 {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.notification-text p {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.3;
    opacity: 0.95;
}

.notification-close {
    background: none;
    border: none;
    color: white;
    font-size: 0.875rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.notification-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.notification-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.notification-actions .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
}

.error-icon {
    margin-bottom: 1rem;
}

.error-icon i {
    font-size: 3rem;
    color: #f59e0b;
}

.error-message h4 {
    margin: 0 0 0.75rem 0;
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
}

.error-message p {
    margin: 0 0 1rem 0;
    color: #64748b;
    font-size: 1rem;
}

.error-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.error-actions .btn {
    min-width: 120px;
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
@media (max-width: 991.98px) {
    .dashboard-container {
        max-width: 100%;
        padding: 1.25rem 1rem;
    }
    
    .welcome-section {
        padding: 2rem 1.5rem;
    }
    
    .welcome-content {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .welcome-title {
        font-size: 2rem;
    }
    
    .welcome-subtitle {
        font-size: 1.1rem;
    }
    
    .header-stats {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem 0.75rem;
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
    
    .header-stats {
        justify-content: center;
        gap: 0.75rem;
    }
    
    .stat-badge {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
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
    
    .insights-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .insight-card {
        padding: 1.25rem;
    }
    
    .countdown-days .days-number {
        font-size: 2rem;
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
    
    .header-stats {
        gap: 0.5rem;
    }
    
    .stat-badge {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
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
    font-size: 1.5rem;
    margin-right: 0.5rem;
    color: inherit;
    display: inline-block;
    line-height: 1;
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

/* Compact Growth Progress Section */
.growth-progress-compact {
    /* Inherits card styling from parent */
    padding: 0;
}

.stage-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.stage-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.8rem;
    border-radius: 16px;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
}

.stage-percent {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
}

.progress-container {
    margin-bottom: 1rem;
}

.progress-bar {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.6s ease;
}

.progress-text {
    font-size: 0.8rem;
    color: #64748b;
    text-align: center;
}

.growth-info {
    margin-bottom: 1rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.4rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 500;
}

.info-value {
    font-size: 0.85rem;
    color: #1e293b;
    font-weight: 600;
}

.growth-status {
    margin-bottom: 1rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    width: 100%;
    justify-content: center;
}

.status-badge.primary {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.status-badge.success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.status-badge.info {
    background: rgba(6, 182, 212, 0.1);
    color: #06b6d4;
}


</style>
@endpush
@endsection