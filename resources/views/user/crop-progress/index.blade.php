@extends('layouts.app')

@section('title', 'Crop Progress Update - MeloTech')

@section('content')
<div id="crop-progress-root" class="crop-progress-container" data-next-update-date="{{ $nextUpdateDate ? $nextUpdateDate->format('Y-m-d H:i:s') : '' }}" data-export-pdf-url="{{ route('crop-progress.export-pdf') }}" data-farm-name="{{ $selectedFarm ? $selectedFarm->name : 'N/A' }}">

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Unified Header -->
    <div class="unified-header">
        <div class="header-main">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-clipboard-check"></i>
                    Crop Progress Update
                </h1>
                <p class="page-subtitle">Update your crop progress through guided questions or image selection</p>
                @if($selectedFarm)
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-tractor"></i>
                        <span>{{ $selectedFarm->farm_name }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-seedling"></i>
                        <span>{{ $selectedFarm->watermelon_variety }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ ucfirst($selectedFarm->cropGrowth->current_stage ?? 'seedling') }} Stage</span>
                    </div>
                    @if($canUpdate)
                    <div class="action-status ready">
                        <i class="fas fa-check-circle"></i>
                        <span>Ready for Update</span>
                    </div>
                    @else
                    <div class="action-status waiting">
                        <i class="fas fa-clock"></i>
                        <span>Next: {{ $nextUpdateDate->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
    </div>

    @if($selectedFarm)
        <!-- Persistent Status Section - Always Visible -->
        <div class="status-section">
            <div class="status-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2"></i>
                        Progress Update Status
                    </h3>
                </div>
                <div class="card-content">
                    @if($canUpdate)
                        <div class="status-ready">
                            <div class="status-icon">
                                <i class="fas fa-rocket text-success"></i>
                            </div>
                            <div class="status-content">
                                <div class="status-header">
                                    <h4 class="status-title text-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Ready to Update
                                    </h4>
                                    <div class="status-badge">
                                        <i class="fas fa-clock me-1"></i>
                                        Available Now
                                    </div>
                                </div>
                                <p class="status-description">
                                    Answer stage-specific questions about your {{ ucfirst($selectedFarm->cropGrowth->current_stage ?? 'seedling') }} stage crop progress.
                                </p>
                                <div class="status-actions">
                                    <a href="{{ route('crop-progress.questions') }}" class="btn btn-success btn-lg btn-pulse">
                                        <i class="fas fa-play me-2"></i>
                                        <span>Start Questions</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="status-waiting">
                            <div class="status-icon">
                                <i class="fas fa-hourglass-half text-warning"></i>
                            </div>
                            <div class="status-content">
                                <h4 class="status-title text-warning">Next Update Coming Soon</h4>
                                <p class="status-description">Please wait until the next scheduled update date to answer new questions.</p>
                                
                                <div class="countdown-section">
                                    <div class="countdown-label">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Next Update Available:
                                    </div>
                                    <div class="countdown-timer" id="countdown-timer">
                                        <div class="countdown-date">
                                            {{ $nextUpdateDate->format('l, F d, Y') }}
                                        </div>
                                        <div class="countdown-time">
                                            {{ $nextUpdateDate->diffForHumans() }}
                                        </div>
                                        <div class="countdown-details">
                                            <div class="countdown-display">
                                                <span class="countdown-days" id="countdown-days">--</span>
                                                <span class="countdown-unit">days</span>
                                            </div>
                                            <div class="countdown-separator">:</div>
                                            <div class="countdown-display">
                                                <span class="countdown-hours" id="countdown-hours">--</span>
                                                <span class="countdown-unit">hours</span>
                                            </div>
                                            <div class="countdown-separator">:</div>
                                            <div class="countdown-display">
                                                <span class="countdown-minutes" id="countdown-minutes">--</span>
                                                <span class="countdown-unit">minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="status-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar-week me-2"></i>
                                        <strong>Next Week: Week {{ $nextUpdateDate ? \App\Models\CropProgressUpdate::getNextWeekNumber(auth()->user(), $selectedFarm) : 1 }}</strong>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Updates are available every 7 days to maintain accurate tracking
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>



        <!-- Progress History -->
        <section class="ph-section">
            <div class="ph-card">
                <div class="ph-header">
                    <h3 class="ph-title"><i class="fas fa-chart-line me-2"></i>Progress History</h3>
                    <div class="ph-actions">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('crop-progress.print') }}" target="_blank">
                            <i class="fas fa-print me-2"></i>Print
                        </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportAsExcel()">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportAsPDF()">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="ph-body">
                    <div class="ph-table-container">
                        @if(isset($progressUpdates) && $progressUpdates->count() > 0)
                            <div class="ph-scroll-indicator">
                                <i class="fas fa-arrows-alt-h"></i>
                                <span>Swipe to view more columns</span>
                            </div>
                            <table class="ph-table">
                                <thead>
                                    <tr>
                                        <th>Week</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Progress</th>
                                        <th>Next Update</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($progressUpdates as $update)
                                        <tr>
                                            <td><span class="ph-badge">{{ $update->getWeekName() }}</span></td>
                                            <td>{{ $update->update_date->format('M d, Y') }}</td>
                                            <td><span class="ph-badge ph-badge-primary">{{ ucfirst($update->update_method) }}</span></td>
                                            <td>
                                                <div class="ph-progress-wrap">
                                                    <div class="ph-progress">
                                                        <div class="ph-progress-bar" data-progress="{{ $update->calculated_progress }}"></div>
                                                    </div>
                                                    <span class="ph-progress-label">{{ $update->calculated_progress }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $update->next_update_date->format('M d, Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="showQuestionsSummary('{{ $update->id }}')">
                                                            <i class="fas fa-chart-line me-2"></i>View Progress Summary
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="showRecommendations('{{ $update->id }}')">
                                                            <i class="fas fa-lightbulb me-2"></i>View Recommendations
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="ph-empty">
                                <i class="fas fa-table"></i>
                                <h5>No progress updates yet</h5>
                                <p>Complete your first progress update to see data here.</p>
                                <a href="{{ route('crop-progress.questions') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>Start Progress Update
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
     @else
         <!-- No Farms Message -->
         <div class="no-farms-section">
             <div class="no-farms-content">
                 <div class="no-farms-icon">
                     <i class="fas fa-farm"></i>
                 </div>
                 <h2>No Farm Found</h2>
                 <p>You need to create a farm first before you can update crop progress.</p>
                 <a href="{{ route('crop-growth.index') }}" class="btn btn-primary btn-lg">
                     <i class="fas fa-plus me-2"></i>Create Your Farm
                 </a>
             </div>
         </div>
     @endif
 </div>

 <!-- Questions Summary Modal -->
 <div class="modal fade" id="questionsSummaryModal" tabindex="-1" aria-labelledby="questionsSummaryModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-xl">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="questionsSummaryModalLabel">
                     <i class="fas fa-clipboard-check me-2"></i>
                     <span id="modalTitle">Questions Answer Summary</span>
                 </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div id="modalContent">
                     <div class="text-center">
                         <div class="spinner-border text-primary" role="status">
                             <span class="visually-hidden">Loading...</span>
                         </div>
                         <p class="mt-2">Loading summary...</p>
                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-primary" onclick="printSummary()">
                     <i class="fas fa-print me-2"></i>Print
                 </button>
             </div>
         </div>
     </div>
 </div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/crop-progress-index.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/crop-progress-index.js') }}"></script>
<script src="{{ asset('js/enhanced-print-summary.js') }}"></script>
<script src="{{ asset('js/enhanced-print-recommendations.js') }}"></script>
@endpush

@endsection