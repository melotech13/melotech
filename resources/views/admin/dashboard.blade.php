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
            <p class="section-subtitle">Manage your system efficiently</p>
        </div>
        <div class="actions-grid">
            <a href="{{ route('admin.users.index') }}" class="action-item">
                <div class="action-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-content">
                    <h4 class="action-title">Manage Users</h4>
                    <p class="action-description">View and manage all system users</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('admin.farms.index') }}" class="action-item">
                <div class="action-icon farms">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="action-content">
                    <h4 class="action-title">View Farms</h4>
                    <p class="action-description">Monitor all registered farms</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('admin.users.create') }}" class="action-item">
                <div class="action-icon create">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="action-content">
                    <h4 class="action-title">Add User</h4>
                    <p class="action-description">Create new user accounts</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="{{ route('admin.statistics') }}" class="action-item">
                <div class="action-icon analytics">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="action-content">
                    <h4 class="action-title">View Analytics</h4>
                    <p class="action-description">System statistics and reports</p>
                </div>
                <div class="action-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="content-section">
        <div class="section-header">
            <h3 class="section-title">
                <i class="fas fa-clock"></i>
                Recent Activity
            </h3>
            <p class="section-subtitle">Latest system activity</p>
        </div>
        <div class="activity-content">
            <!-- Recent Users -->
            <div class="activity-section">
                <h4 class="activity-section-title">
                    <i class="fas fa-users"></i>
                    Recent Users
                </h4>
                @if($stats['recent_users']->count() > 0)
                    <div class="activity-list">
                        @foreach($stats['recent_users']->take(3) as $user)
                            <div class="activity-item">
                                <div class="activity-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="activity-details">
                                    <div class="activity-name">{{ $user->name }}</div>
                                    <div class="activity-meta">{{ $user->email }}</div>
                                    <div class="activity-time">{{ $user->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="activity-badge {{ $user->role === 'admin' ? 'admin' : 'user' }}">
                                    {{ ucfirst($user->role) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No recent users</p>
                    </div>
                @endif
            </div>

            <!-- Recent Farms -->
            <div class="activity-section">
                <h4 class="activity-section-title">
                    <i class="fas fa-seedling"></i>
                    Recent Farms
                </h4>
                @if($stats['recent_farms']->count() > 0)
                    <div class="activity-list">
                        @foreach($stats['recent_farms']->take(3) as $farm)
                            <div class="activity-item">
                                <div class="activity-avatar farm">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="activity-details">
                                    <div class="activity-name">{{ $farm->farm_name }}</div>
                                    <div class="activity-meta">{{ $farm->user->name }}</div>
                                    <div class="activity-time">{{ $farm->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="activity-badge variety">
                                    {{ $farm->watermelon_variety }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-seedling"></i>
                        <p>No recent farms</p>
                    </div>
                @endif
            </div>
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
@endsection
