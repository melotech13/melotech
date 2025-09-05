@extends('layouts.admin')

@section('title', 'Farm Details - MeloTech')

@section('page-title', $farm->farm_name)

@section('content')
<!-- Farm Overview Stats -->
<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Farm Owner</h3>
            <div class="card-icon primary">
                <i class="fas fa-user"></i>
            </div>
        </div>
        <div class="card-content">
            <div class="card-number">{{ $farm->user->name }}</div>
            <p class="card-description">{{ $farm->user->email }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.users.show', $farm->user) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye me-1"></i>
                View Profile
            </a>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Location</h3>
            <div class="card-icon success">
                <i class="fas fa-map-marker-alt"></i>
            </div>
        </div>
        <div class="card-content">
            <div class="card-number">{{ $farm->city_municipality_name }}</div>
            <p class="card-description">{{ $farm->province_name }}</p>
        </div>
        <div class="card-footer">
            @if($farm->barangay_name)
                <span class="text-muted small">{{ $farm->barangay_name }}</span>
            @endif
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Field Size</h3>
            <div class="card-icon warning">
                <i class="fas fa-ruler-combined"></i>
            </div>
        </div>
        <div class="card-content">
            <div class="card-number">{{ $farm->field_size }}</div>
            <p class="card-description">{{ $farm->field_size_unit }}</p>
        </div>
        <div class="card-footer">
            <span class="text-muted small">{{ $farm->watermelon_variety }}</span>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3 class="card-title">Planting Date</h3>
            <div class="card-icon danger">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="card-content">
            @if($farm->planting_date)
                <div class="card-number">{{ $farm->planting_date->format('M d') }}</div>
                <p class="card-description">{{ $farm->planting_date->format('Y') }}</p>
            @else
                <div class="card-number">Not set</div>
                <p class="card-description">No planting date</p>
            @endif
        </div>
        <div class="card-footer">
            <span class="text-muted small">Created {{ $farm->created_at->format('M d, Y') }}</span>
        </div>
    </div>
</div>

<!-- Farm Information Section -->
<div class="section-card">
    <div class="section-header">
        <h2 class="section-title">
            <div class="section-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            Farm Information
        </h2>
        <div class="d-flex align-items-center gap-3">
            <p class="text-muted mb-0">Complete details about this watermelon farm</p>
            <a href="{{ route('admin.farms.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Farms
            </a>
        </div>
    </div>
    <div class="section-content">
        <div class="row">
            <!-- Farm Details -->
            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-seedling me-2"></i>
                            Farm Details
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Farm Name:</td>
                                        <td>{{ $farm->farm_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Variety:</td>
                                        <td>{{ $farm->watermelon_variety }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Field Size:</td>
                                        <td>{{ $farm->field_size }} {{ $farm->field_size_unit }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Planting Date:</td>
                                        <td>
                                            @if($farm->planting_date)
                                                {{ $farm->planting_date->format('F d, Y') }}
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Created:</td>
                                        <td>{{ $farm->created_at->format('F d, Y \a\t g:i A') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Details -->
            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Location Details
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Province:</td>
                                        <td>{{ $farm->province_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">City/Municipality:</td>
                                        <td>{{ $farm->city_municipality_name }}</td>
                                    </tr>
                                    @if($farm->barangay_name)
                                    <tr>
                                        <td class="fw-semibold">Barangay:</td>
                                        <td>{{ $farm->barangay_name }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Owner Information Section -->
<div class="section-card">
    <div class="section-header">
        <h2 class="section-title">
            <div class="section-icon">
                <i class="fas fa-user"></i>
            </div>
            Farm Owner
        </h2>
        <p class="text-muted mb-0">Information about the farm owner</p>
    </div>
    <div class="section-content">
        <div class="dashboard-card">
            <div class="card-content">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center mb-3 mb-md-0">
                        <div class="user-avatar mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($farm->user->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h4 class="mb-2">{{ $farm->user->name }}</h4>
                        <p class="text-muted mb-2">{{ $farm->user->email }}</p>
                        <div class="row">
                            <div class="col-sm-6">
                                <small class="text-muted">Phone:</small>
                                <div>{{ $farm->user->phone ?? 'Not provided' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">Role:</small>
                                <div>
                                    @if($farm->user->role === 'admin')
                                        <span class="badge bg-primary">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Joined:</small>
                            <div>{{ $farm->user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <a href="{{ route('admin.users.show', $farm->user) }}" class="btn btn-primary">
                            <i class="fas fa-user me-2"></i>
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Crop Growth Information -->
@if($farm->cropGrowth)
<div class="section-card">
    <div class="section-header">
        <h2 class="section-title">
            <div class="section-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            Crop Growth Status
        </h2>
        <p class="text-muted mb-0">Current growth stage and progress information</p>
    </div>
    <div class="section-content">
        <div class="dashboard-card">
            <div class="card-content">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-3">{{ ucfirst($farm->cropGrowth->current_stage) }}</h4>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Stage Progress</span>
                                <span class="small">{{ $farm->cropGrowth->stage_progress }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ $farm->cropGrowth->stage_progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="h2 mb-1">{{ $farm->cropGrowth->overall_progress }}%</div>
                        <div class="text-muted">Overall Progress</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Progress Updates -->
@if($farm->cropProgressUpdates && $farm->cropProgressUpdates->count() > 0)
<div class="section-card">
    <div class="section-header">
        <h2 class="section-title">
            <div class="section-icon">
                <i class="fas fa-history"></i>
            </div>
            Recent Progress Updates
        </h2>
        <p class="text-muted mb-0">Latest crop progress tracking records</p>
    </div>
    <div class="section-content">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <i class="fas fa-calendar me-2"></i>Date
                        </th>
                        <th>
                            <i class="fas fa-seedling me-2"></i>Stage
                        </th>
                        <th>
                            <i class="fas fa-percentage me-2"></i>Progress
                        </th>
                        <th>
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($farm->cropProgressUpdates->take(5) as $update)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $update->created_at->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $update->stage_name }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $update->progress_percentage }}%"></div>
                                    </div>
                                    <span class="small">{{ $update->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($update->notes, 50) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
