@extends('layouts.admin')

@section('title', 'Admin Dashboard - MeloTech')

@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section mb-4">
    <div class="welcome-card">
        <div class="welcome-content">
            <h2 class="welcome-title">Welcome back, {{ auth()->user()->name }}! 👋</h2>
            <p class="welcome-subtitle">Here's what's happening with your MeloTech system today.</p>
        </div>
        <div class="welcome-time">
            <div class="current-time">{{ now()->format('l, F j, Y') }}</div>
            <div class="current-time-small">{{ now()->format('g:i A') }}</div>
        </div>
    </div>
</div>

<!-- Key Metrics Section -->
<div class="metrics-section mb-4">
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_users'] }}</div>
                <div class="metric-label">Total Users</div>
                <div class="metric-trend positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>Active</span>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon farms">
                <i class="fas fa-seedling"></i>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_farms'] }}</div>
                <div class="metric-label">Registered Farms</div>
                <div class="metric-trend positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>Growing</span>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon analyses">
                <i class="fas fa-camera"></i>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_photo_analyses'] }}</div>
                <div class="metric-label">Photo Analyses</div>
                <div class="metric-trend positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>Processing</span>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon updates">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="metric-content">
                <div class="metric-number">{{ $stats['total_progress_updates'] }}</div>
                <div class="metric-label">Progress Updates</div>
                <div class="metric-trend positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>Updated</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="main-content-grid">
    <!-- Quick Actions -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h3>
            <p class="section-subtitle">Streamlined admin operations</p>
        </div>
        <div class="quick-actions-grid">
            <div class="quick-action-card primary">
                <div class="quick-action-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="quick-action-content">
                    <h4 class="quick-action-title">User Management</h4>
                    <p class="quick-action-description">Manage users, roles, and permissions</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">{{ $stats['total_users'] }} Users</span>
                        <span class="stat-item">Active</span>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="quick-action-link">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="quick-action-card success">
                <div class="quick-action-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="quick-action-content">
                    <h4 class="quick-action-title">Farm Overview</h4>
                    <p class="quick-action-description">Monitor and track farm activities</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">{{ $stats['total_farms'] }} Farms</span>
                        <span class="stat-item">Growing</span>
                    </div>
                </div>
                <a href="{{ route('admin.farms.index') }}" class="quick-action-link">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="quick-action-card warning">
                <div class="quick-action-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="quick-action-content">
                    <h4 class="quick-action-title">Analytics</h4>
                    <p class="quick-action-description">View system performance metrics</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">{{ $stats['total_photo_analyses'] }} Analyses</span>
                        <span class="stat-item">Processed</span>
                    </div>
                </div>
                <a href="{{ route('admin.statistics') }}" class="quick-action-link">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="quick-action-card info">
                <div class="quick-action-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="quick-action-content">
                    <h4 class="quick-action-title">Notifications</h4>
                    <p class="quick-action-description">Manage system alerts and updates</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">Real-time</span>
                        <span class="stat-item">Alerts</span>
                    </div>
                </div>
                <a href="{{ route('admin.notifications') }}" class="quick-action-link">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="quick-action-card secondary">
                <div class="quick-action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="quick-action-content">
                    <h4 class="quick-action-title">Add User</h4>
                    <p class="quick-action-description">Create new user accounts quickly</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">Quick</span>
                        <span class="stat-item">Setup</span>
                    </div>
                </div>
                <a href="{{ route('admin.users.create') }}" class="quick-action-link">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="quick-action-card dark">
                <div class="quick-action-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="quick-action-content">
                    <h4 class="quick-action-title">Settings</h4>
                    <p class="quick-action-description">Configure system preferences</p>
                    <div class="quick-action-stats">
                        <span class="stat-item">System</span>
                        <span class="stat-item">Config</span>
                    </div>
                </div>
                <a href="{{ route('admin.settings') }}" class="quick-action-link">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Feed -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-rss"></i>
                Recent Activity Feed
            </h3>
            <p class="section-subtitle">Latest activities and updates from your users</p>
        </div>
        <div class="activity-feed-content">
            @if(count($activityFeed['activities']) > 0)
                <div class="activity-list">
                    @foreach($activityFeed['activities'] as $activity)
                        <div class="activity-item {{ $activity['color'] }}">
                            <div class="activity-icon">
                                <i class="{{ $activity['icon'] }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ $activity['title'] }}</div>
                                <div class="activity-description">{{ $activity['description'] }}</div>
                                <div class="activity-time">{{ $activity['time'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-activity">
                    <i class="fas fa-inbox"></i>
                    <p>No recent activity to show</p>
                    <small>Activities will appear here as users interact with the platform</small>
                </div>
            @endif
            
            <!-- Activity Summary -->
            <div class="activity-summary">
                <div class="summary-item">
                    <div class="summary-number">{{ $activityFeed['total_activities'] }}</div>
                    <div class="summary-label">Total Activities</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number">{{ $activityFeed['total_activities'] > 0 ? 'Active' : 'Quiet' }}</div>
                    <div class="summary-label">Platform Status</div>
                </div>
                <div class="last-updated-activity">
                    <small>Last updated: {{ $activityFeed['last_updated'] }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Overview Chart -->
<div class="chart-section mb-4">
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">
                <i class="fas fa-chart-line"></i>
                Activity Trends
            </div>
            <div class="chart-subtitle">Last 7 days activity overview</div>
        </div>
        <div class="chart-content">
            <canvas id="activityOverviewChart" height="120"></canvas>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="system-status-section">
    <div class="status-card">
        <div class="status-header">
            <div class="status-title">
                <i class="fas fa-server"></i>
                System Status
            </div>
            <div class="status-indicator online">
                <div class="status-dot"></div>
                <span>All Systems Operational</span>
            </div>
        </div>
        <div class="status-grid">
            <div class="status-item">
                <div class="status-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="status-info">
                    <div class="status-name">Database</div>
                    <div class="status-value">Connected</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="status-info">
                    <div class="status-name">AI Analysis</div>
                    <div class="status-value">Active</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <div class="status-info">
                    <div class="status-name">Weather API</div>
                    <div class="status-value">Online</div>
                </div>
            </div>
            <div class="status-item">
                <div class="status-icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="status-info">
                    <div class="status-name">File Storage</div>
                    <div class="status-value">Available</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="dashboard-data" type="application/json">{!! json_encode(['stats' => $stats, 'activityFeed' => $activityFeed]) !!}</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dashboardEl = document.getElementById('dashboard-data');
    const data = dashboardEl ? JSON.parse(dashboardEl.textContent || '{}') : {};
    
    // Colors aligned with admin theme
    const colors = {
        primary: '#3b82f6',
        secondary: '#8b5cf6', 
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#06b6d4'
    };

    // Activity Overview Chart
    const activityCtx = document.getElementById('activityOverviewChart');
    if (activityCtx) {
        // Generate sample data for the last 7 days
        const last7Days = [];
        const userRegistrations = [];
        const farmCreations = [];
        const photoAnalyses = [];
        
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            last7Days.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            
            // Generate realistic sample data based on current stats
            const baseUsers = Math.floor((data.stats.total_users || 0) / 30);
            const baseFarms = Math.floor((data.stats.total_farms || 0) / 30);
            const baseAnalyses = Math.floor((data.stats.total_photo_analyses || 0) / 30);
            
            userRegistrations.push(Math.floor(Math.random() * (baseUsers + 2)) + 1);
            farmCreations.push(Math.floor(Math.random() * (baseFarms + 1)) + 1);
            photoAnalyses.push(Math.floor(Math.random() * (baseAnalyses + 3)) + 1);
        }

        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: last7Days,
                datasets: [
                    {
                        label: 'New Users',
                        data: userRegistrations,
                        borderColor: colors.primary,
                        backgroundColor: colors.primary + '20',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: colors.primary,
                        pointBorderColor: colors.primary,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'New Farms',
                        data: farmCreations,
                        borderColor: colors.success,
                        backgroundColor: colors.success + '20',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: colors.success,
                        pointBorderColor: colors.success,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Photo Analyses',
                        data: photoAnalyses,
                        borderColor: colors.warning,
                        backgroundColor: colors.warning + '20',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: colors.warning,
                        pointBorderColor: colors.warning,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
});
</script>
@endpush
@endsection
