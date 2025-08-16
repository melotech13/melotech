@extends('layouts.app')

@section('title', 'Dashboard - MeloTech')

@section('content')
<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Welcome Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-seedling me-3"></i>
                                Welcome, {{ Auth::user()->name }}!
                            </h2>
                            <p class="mb-0 lead">Your watermelon farm is now set up and ready for AI-powered analysis and insights.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <i class="fas fa-brain fa-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Farm Information -->
    @if(Auth::user()->farms->count() > 0)
        @foreach(Auth::user()->farms as $farm)
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-tractor text-primary me-2"></i>
                    Farm Overview
                </h3>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Farm Details
                                </h5>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Farm Name:</strong></p>
                                        <p class="mb-1"><strong>Variety:</strong></p>
                                        <p class="mb-1"><strong>Field Size:</strong></p>
                                        <p class="mb-0"><strong>Planting Date:</strong></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1">{{ $farm->name }}</p>
                                        <p class="mb-1">{{ $farm->watermelon_variety }}</p>
                                        <p class="mb-1">{{ $farm->field_size }} {{ $farm->field_size_unit }}</p>
                                        <p class="mb-0">{{ $farm->planting_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Growth Progress
                                </h5>
                                <div class="text-center">
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%">
                                            <span class="fw-semibold">25% Complete</span>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-0">Estimated harvest: {{ $farm->planting_date->addDays(80)->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-bolt text-primary me-2"></i>
                Quick Actions
            </h3>
            
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-camera fa-2x text-primary mb-3"></i>
                            <h5 class="card-title">Upload Photos</h5>
                            <p class="card-text">Upload crop photos for AI analysis and health assessment</p>
                            <button class="btn btn-outline-primary">Upload Photos</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-brain fa-2x text-info mb-3"></i>
                            <h5 class="card-title">AI Analysis</h5>
                            <p class="card-text">Get AI-powered insights and growth predictions</p>
                            <button class="btn btn-outline-info">View Analysis</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-cloud-sun fa-2x text-warning mb-3"></i>
                            <h5 class="card-title">Weather Data</h5>
                            <p class="card-text">Check weather forecasts and adjust predictions</p>
                            <button class="btn btn-outline-warning">Weather Info</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-file-alt fa-2x text-success mb-3"></i>
                            <h5 class="card-title">Reports</h5>
                            <p class="card-text">Generate comprehensive farm reports</p>
                            <button class="btn btn-outline-success">Create Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-history text-primary me-2"></i>
                Recent Activity
            </h3>
            
            <div class="card">
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Farm Setup Completed</h6>
                                <p class="text-muted mb-0">Your farm has been successfully configured for AI analysis</p>
                                <small class="text-muted">{{ now()->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">AI System Ready</h6>
                                <p class="text-muted mb-0">AI analysis system is now active and ready to process your crop photos</p>
                                <small class="text-muted">{{ now()->subMinutes(5)->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Weather Data Connected</h6>
                                <p class="text-muted mb-0">Local weather data integration is now active for enhanced predictions</p>
                                <small class="text-muted">{{ now()->subMinutes(10)->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-left: 1rem;
}
</style>
@endpush
@endsection
