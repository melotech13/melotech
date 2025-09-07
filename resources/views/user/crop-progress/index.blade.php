@extends('layouts.app')

@section('title', 'Crop Progress Update - MeloTech')

@section('content')
<div class="crop-progress-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                    <div class="action-status ready" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-check-circle"></i>
                        <span>Ready for Update</span>
                    </div>
                    @else
                    <div class="action-status" style="background: rgba(251, 191, 36, 0.2); border-color: rgba(251, 191, 36, 0.3);">
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
                                        Updates are available every 6 days to maintain accurate tracking
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
                                                        <div class="ph-progress-bar" style="--progress-width: {{ $update->calculated_progress }}%; width: var(--progress-width);"></div>
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

@push('styles')
<style>
    .ph-section { margin-top: 2rem; margin-bottom: 2rem; }
    .ph-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 8px 25px rgba(0,0,0,0.06); }
    .ph-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: #f8fafc; border-top-left-radius: 16px; border-top-right-radius: 16px; display: flex; justify-content: space-between; align-items: center; }
    .ph-title { margin: 0; font-size: 1.25rem; font-weight: 700; color: #1f2937; }
    .ph-actions { display: flex; gap: 0.75rem; align-items: center; }
    
    /* Export dropdown styling */
    .ph-actions .dropdown-toggle {
        background: #ffffff;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .ph-actions .dropdown-toggle:hover {
        background: #3b82f6;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .ph-actions .dropdown-menu {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 0.5rem;
        min-width: 180px;
    }
    
    .ph-actions .dropdown-item {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        color: #374151;
    }
    
    .ph-actions .dropdown-item:hover {
        background: #f3f4f6;
        color: #1f2937;
        transform: translateX(4px);
    }
    
    .ph-actions .dropdown-item i {
        width: 16px;
        text-align: center;
    }
    
    /* Debug button styling */
    .ph-actions .dropdown-divider {
        margin: 0.5rem 0;
        border-color: #e5e7eb;
    }
    
    .ph-actions .dropdown-item[onclick*="debug"] {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .ph-actions .dropdown-item[onclick*="debug"]:hover {
        background: #fef3c7;
        color: #92400e;
    }
    
    .ph-body { padding: 1rem 1.5rem; }
    .ph-table-container { 
        height: 640px; 
        min-height: 640px; 
        max-height: 640px; 
        overflow: auto; 
        border: 1px solid #e5e7eb; 
        border-radius: 10px; 
        background: #ffffff;
        position: relative;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }
    
    .ph-table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .ph-table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .ph-table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .ph-table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .ph-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .ph-table thead th { position: sticky; top: 0; z-index: 2; background: #f8fafc; color: #374151; font-weight: 600; font-size: 0.95rem; text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
    .ph-table tbody td { padding: 0.75rem 1rem; border-bottom: 1px solid #f3f4f6; vertical-align: top; color: #374151; }
    .ph-table tbody tr:hover { background: #f9fafb; }

    .ph-badge { display: inline-block; background: #e5f3ff; color: #1d4ed8; padding: 0.35rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
    .ph-badge-primary { background: #e5e7ff; color: #4338ca; }

    .ph-progress-wrap { display: flex; align-items: center; gap: 0.5rem; }
    .ph-progress { flex: 1; height: 10px; background: #f3f4f6; border-radius: 6px; overflow: hidden; border: 1px solid #e5e7eb; min-width: 120px; }
    .ph-progress-bar { height: 100%; background: linear-gradient(135deg, #22c55e, #16a34a); }
    .ph-progress-label { font-size: 0.85rem; color: #374151; min-width: 40px; text-align: right; }

    .ph-scroll-indicator {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .ph-scroll-indicator i {
        color: #3b82f6;
        font-size: 1rem;
    }
    
    .ph-empty { height: 100%; min-height: 640px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; color: #6b7280; text-align: center; }
    .ph-empty i { font-size: 2rem; color: #9ca3af; margin-bottom: 0.5rem; }
    .ph-empty h5 { margin: 0; font-weight: 700; }
    .ph-empty p { margin: 0 0 0.5rem 0; font-size: 0.9rem; }

    /* Responsive table styles */
    @media (max-width: 1200px) {
        .ph-table-container {
            overflow-x: auto;
            overflow-y: auto;
        }
        
        .ph-table {
            min-width: 900px;
        }
    }
    
    @media (max-width: 768px) {
        .ph-scroll-indicator {
            display: flex;
        }
        
        .ph-table-container {
            height: 520px;
            min-height: 520px;
            max-height: 520px;
            overflow-x: auto;
            overflow-y: auto;
        }
        
        .ph-table {
            min-width: 800px;
        }
        
        .ph-table thead th,
        .ph-table tbody td {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .ph-progress {
            min-width: 100px;
        }
        
        .ph-progress-label {
            min-width: 35px;
            font-size: 0.8rem;
        }
        
        .ph-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            padding: 1rem 1.25rem;
        }
        
        .ph-actions {
            width: 100%;
            justify-content: flex-start;
        }
        
        .ph-actions .dropdown-toggle {
            width: 100%;
            justify-content: center;
        }
        
        .ph-body {
            padding: 0.75rem 1.25rem;
        }
    }
    
    @media (max-width: 480px) {
        .ph-table-container {
            height: 480px;
            min-height: 480px;
            max-height: 480px;
            overflow-x: auto;
            overflow-y: auto;
        }
        
        .ph-table {
            min-width: 750px;
        }
        
        .ph-table thead th,
        .ph-table tbody td {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }
        
        .ph-progress {
            min-width: 80px;
        }
        
        .ph-progress-label {
            min-width: 30px;
            font-size: 0.75rem;
        }
        
        .ph-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .ph-header {
            padding: 0.75rem 1rem;
        }
        
        .ph-body {
            padding: 0.5rem 1rem;
        }
        
        .ph-title {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 360px) {
        .ph-table-container {
            height: 450px;
            min-height: 450px;
            max-height: 450px;
        }
        
        .ph-table {
            min-width: 700px;
        }
        
        .ph-table thead th,
        .ph-table tbody td {
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .ph-progress {
            min-width: 70px;
        }
        
        .ph-progress-label {
            min-width: 25px;
            font-size: 0.7rem;
        }
        
        .ph-badge {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
        
        /* Add touch-friendly scrolling */
        .ph-table-container {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
    }
    
    /* Additional mobile optimizations */
    @media (max-width: 480px) {
        .ph-table-container {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
        
        /* Make dropdowns more touch-friendly */
        .ph-actions .dropdown-menu {
            min-width: 200px;
        }
        
        .ph-actions .dropdown-item {
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush
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
<style>
    /* Basic styling */
    .crop-progress-container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 1;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    /* Card styling */
    .action-card, .no-farms-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: visible;
        width: 100%;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    /* Specific sizing for action card */
    .action-card {
        min-height: auto;
        max-height: 200px;
    }

    .action-card .card-content {
        padding: 1rem 1.5rem;
        min-height: auto;
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Table styling removed - replaced with new ph-* classes */

    /* Enhanced Modal Styling */
    .modal-xl {
        max-width: 1140px;
    }

    .modal {
        z-index: 9999 !important;
    }

    .modal-backdrop {
        z-index: 9998 !important;
    }

    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        overflow: hidden;
        position: relative;
        z-index: 10000 !important;
    }

    .modal-dialog {
        z-index: 10001 !important;
        position: relative;
        margin: 1.75rem auto;
        max-height: calc(100vh - 3.5rem);
    }

    .modal-dialog.modal-xl {
        max-width: 1140px;
        width: 95%;
        margin: 1rem auto;
    }

    /* Ensure modal is above all other elements */
    .modal.show {
        display: block !important;
        z-index: 9999 !important;
    }

    .modal.show .modal-dialog {
        transform: none !important;
    }

    /* Force modal to be above taskbar and other elements */
    .modal.fade.show {
        z-index: 99999 !important;
    }

    .modal.fade.show .modal-dialog {
        z-index: 100000 !important;
    }

    /* Ensure modal content is fully visible */
    .modal-body {
        max-height: calc(100vh - 250px);
        overflow-y: auto;
    }

    /* Additional positioning fixes */
    .modal-backdrop.show {
        z-index: 99998 !important;
    }

    /* Force modal to top of viewport with proper spacing */
    .modal.show .modal-dialog {
        margin-top: 3rem !important;
        margin-bottom: 3rem !important;
        max-height: calc(100vh - 6rem);
        overflow: hidden;
    }

    /* Ensure modal is not covering content */
    .modal.show {
        padding-top: 2rem !important;
        padding-bottom: 2rem !important;
    }

    /* Force modal above all elements including taskbar */
    .modal {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 99999 !important;
    }

    /* Ensure backdrop is also above everything */
    .modal-backdrop {
        z-index: 99998 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }

    /* Force modal dialog positioning */
    .modal-dialog {
        position: relative !important;
        z-index: 100000 !important;
        margin: 3rem auto !important;
        max-height: calc(100vh - 6rem) !important;
    }

    /* Additional modal positioning fixes */
    .modal.show .modal-content {
        z-index: 100001 !important;
        position: relative !important;
    }

    /* Ensure modal body is scrollable and visible */
    .modal.show .modal-body {
        z-index: 100002 !important;
        position: relative !important;
        max-height: calc(100vh - 300px) !important;
        overflow-y: auto !important;
    }

    .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .modal-title i {
        font-size: 1.8rem;
        color: #3b82f6;
    }

    .modal-body {
        padding: 2rem;
        max-height: 70vh;
        overflow-y: auto;
        position: relative;
        z-index: 10002 !important;
    }

    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .modal-footer {
        background: #f8fafc;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }

    .btn-close {
        background-size: 1.5em;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .btn-close:hover {
        opacity: 1;
    }

    /* Enhanced AI Recommendations Styling */
    .recommendations-container {
        max-width: 100%;
    }

    .recommendations-header {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        border: 2px solid #3b82f6;
        position: relative;
        overflow: hidden;
    }

    .recommendations-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .recommendations-header h5 {
        font-size: 2rem;
        font-weight: 800;
        color: #1e40af;
        margin: 0 0 1rem 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .recommendations-header p {
        font-size: 1.2rem;
        color: #374151;
        margin: 0;
        font-weight: 500;
    }

    /* Enhanced Recommendation Categories */
    .recommendation-category {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        border-left: 8px solid #10b981;
        box-shadow: 0 12px 35px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .recommendation-category::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .recommendation-category:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 45px rgba(0,0,0,0.12);
    }

    .recommendation-category.alert {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
    }

    .recommendation-category.alert::before {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .recommendation-category.planning {
        border-left-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
    }

    .recommendation-category.planning::before {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .recommendation-category.tips {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
    }

    .recommendation-category.tips::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .recommendation-category.weekly {
        border-left-color: #8b5cf6;
        background: linear-gradient(135deg, #f3f4f6 0%, #ffffff 100%);
    }

    .recommendation-category.weekly::before {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    .category-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0 0 2rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .category-title i {
        font-size: 2rem;
        padding: 0.75rem;
        border-radius: 12px;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .category-title.text-danger i {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .category-title.text-primary i {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .category-title.text-info i {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .category-title.text-warning i {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .recommendation-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .recommendation-item {
        padding: 1.25rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 1.15rem;
        line-height: 1.7;
        color: #374151;
        font-weight: 500;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .recommendation-item:last-child {
        border-bottom: none;
    }

    .recommendation-item:hover {
        background: rgba(59, 130, 246, 0.02);
        padding-left: 1rem;
        border-radius: 8px;
    }

    .recommendation-item::before {
        content: '•';
        color: #3b82f6;
        font-size: 1.5rem;
        font-weight: bold;
        line-height: 1;
        margin-top: 0.25rem;
    }

    .recommendation-item.text-success {
        color: #059669;
        font-weight: 600;
    }

    .recommendation-item.text-success::before {
        color: #10b981;
    }

    .recommendation-item.text-danger {
        color: #dc2626;
        font-weight: 600;
    }

    .recommendation-item.text-danger::before {
        color: #ef4444;
    }

    /* Responsive Design for Modal */
    @media (max-width: 768px) {
        .modal-xl {
            max-width: 95vw;
            margin: 1rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .recommendations-header {
            padding: 1.5rem;
        }

        .recommendations-header h5 {
            font-size: 1.5rem;
        }

        .recommendation-category {
            padding: 1.5rem;
        }

        .category-title {
            font-size: 1.3rem;
        }

        .recommendation-item {
            font-size: 1rem;
            padding: 1rem 0;
        }
    }

    /* Enhanced dropdown positioning to prevent overlap */
    .table .dropdown {
        position: relative;
        z-index: 20;
    }

    .table .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        margin-top: 0.125rem;
        min-width: 200px;
        z-index: 1000;
        display: none;
        background: white;
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 0.5rem 0;
        max-height: 300px;
        overflow-y: auto;
    }

    .table .dropdown-menu.show {
        display: block !important;
    }

    /* Ensure dropdowns are visible above table content */
    .table .dropdown-toggle {
        position: relative;
        z-index: 21;
    }

    /* Old table styling removed - replaced with new ph-* classes */

    /* Button styling */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
        border: none;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    /* Week badge styling */
    .badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #000;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
        border-radius: 12px;
    }

    /* Old table column widths and responsive styles removed - replaced with new ph-* classes */

    @media (max-width: 480px) {
        /* Old table styles removed - replaced with new ph-* classes */
    }

        /* Unified Header */
    .unified-header {
        background: linear-gradient(135deg, #059669 0%, #10b981 25%, #34d399 50%, #6ee7b7 75%, #a7f3d0 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        margin-top: 0 !important;
        color: white;
        box-shadow: 0 15px 30px rgba(5, 150, 105, 0.3);
        position: relative;
        overflow: hidden;
        width: 100%;
        z-index: 1;
    }

    .unified-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .header-main {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }

    .header-left {
        flex: 1;
    }

    .header-right {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        align-items: flex-end;
        min-width: 280px;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0 0 0.75rem 0;
        text-shadow: 0 3px 10px rgba(0,0,0,0.3);
        color: white !important;
    }

    .page-subtitle {
        font-size: 1.1rem;
        margin: 0;
        opacity: 0.95;
        font-weight: 400;
        color: white !important;
    }

    .farm-info {
        text-align: right;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        min-width: 250px;
        color: white !important;
    }

    .farm-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        color: white !important;
    }

    .farm-details {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        flex-wrap: wrap;
        color: white !important;
    }

    .farm-variety, .farm-size {
        background: rgba(255, 255, 255, 0.2);
        color: white !important;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .update-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        min-width: 200px;
        justify-content: center;
        color: white !important;
    }

    .update-status.ready {
        background: rgba(34, 197, 94, 0.2);
        color: white !important;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .update-status.waiting {
        background: rgba(245, 158, 11, 0.2);
        color: white !important;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    /* Modal styling */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px 16px 0 0;
    }

    .modal-title {
        color: #1f2937;
        font-weight: 700;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        background: #f9fafb;
        border-top: 1px solid rgba(0,0,0,0.05);
        border-radius: 0 0 16px 16px;
    }

    /* Summary content */
    .summary-container {
        max-width: 100%;
    }

    .summary-header {
        text-align: center;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e5e7eb;
    }

    .summary-header h6 {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .question-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb !important;
        transition: all 0.2s ease;
    }

    .question-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .question-text {
        color: #1f2937;
        font-weight: 600;
    }

    .answer-text {
        color: #059669;
        font-weight: 600;
    }

    .explanation-text {
        color: #6b7280;
        font-style: italic;
        font-size: 0.9rem;
    }

    .summary-footer {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
        border: 1px solid #e5e7eb;
    }

    /* Responsive design */
    @media (max-width: 991.98px) {
        .crop-progress-container {
            max-width: 100%;
            padding: 1.5rem 1rem;
        }

        .unified-header {
            padding: 2rem 1.5rem;
        }

        .header-main {
            flex-direction: column;
            gap: 1.5rem;
        }

        .header-right {
            align-items: center;
            min-width: auto;
            width: 100%;
        }

        .farm-info {
            text-align: center;
            min-width: auto;
            width: 100%;
        }

        .farm-name {
            justify-content: center;
        }

        .farm-details {
            justify-content: center;
        }

        .page-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 767.98px) {
        .crop-progress-container {
            padding: 1.25rem 0.75rem;
        }

        .unified-header {
            padding: 1.5rem 1rem;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .farm-info {
            padding: 1rem;
        }

        .update-status {
            min-width: auto;
            width: 100%;
        }

        .card-header, .card-content {
            padding: 1.25rem;
        }
    }
    

    
    /* Ensure table content is not constrained by height */
    .card-content {
        min-height: auto !important;
        max-height: none !important;
        height: auto !important;
    }

    /* Status Section Styling */
    .status-section {
        margin-bottom: 2rem;
    }

    .status-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: visible;
        width: 100%;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 50px rgba(0,0,0,0.15);
    }

    .status-card .card-content {
        height: auto;
        min-height: auto;
        max-height: none;
        padding: 2rem;
    }

    .status-ready, .status-waiting {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .status-icon {
        flex-shrink: 0;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .status-ready .status-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 2px solid rgba(16, 185, 129, 0.2);
    }

    .status-waiting .status-icon {
        background: rgba(251, 191, 36, 0.1);
        color: #f59e0b;
    }

    .status-content {
        flex: 1;
    }

    .status-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .status-title {
        font-size: 1.75rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .status-badge {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .status-description {
        color: #6b7280;
        margin-bottom: 2rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Old table styling removed - replaced with new ph-* classes */

    .badge {
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .progress {
        border-radius: 10px;
        background: #f3f4f6;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* Enhanced button styling */
    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        border-color: #3b82f6;
        color: white;
    }

    .btn-outline-success:hover {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: #10b981;
        color: white;
    }

    /* Enhanced badge styling */
    .badge.bg-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    /* Enhanced dropdown styling */
    .dropdown-menu {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 0.5rem;
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
        color: #065f46;
        transform: translateX(4px);
    }

    .status-actions {
        margin-top: 2rem;
        text-align: center;
    }

    .btn-pulse {
        background: #10b981;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        transition: all 0.3s ease;
    }

    .btn-pulse:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .countdown-section {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border-radius: 16px;
        padding: 2rem;
        margin: 2rem 0;
        border: 2px solid #10b981;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        position: relative;
        overflow: hidden;
    }

    .countdown-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7);
    }

    .countdown-label {
        font-weight: 700;
        color: #065f46;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .countdown-timer {
        text-align: center;
    }

    .countdown-date {
        font-size: 1.4rem;
        font-weight: 700;
        color: #064e3b;
        margin-bottom: 1rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .countdown-time {
        font-size: 1.1rem;
        color: #047857;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .countdown-details {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        padding: 1.5rem 2rem;
        border-radius: 12px;
        border: 2px solid #10b981;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        margin-top: 1rem;
    }

    .countdown-display {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        min-width: 80px;
    }

    .countdown-days, .countdown-hours, .countdown-minutes {
        color: #065f46;
        font-weight: 900;
        font-size: 3.5rem;
        line-height: 1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        font-family: 'Arial Black', 'Helvetica Bold', sans-serif;
        background: linear-gradient(135deg, #10b981, #059669);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .countdown-unit {
        color: #047857;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .countdown-separator {
        color: #10b981;
        font-weight: 900;
        font-size: 2.5rem;
        line-height: 1;
        margin-top: -0.5rem;
    }

    .status-info {
        margin-top: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        color: #6b7280;
        font-size: 0.95rem;
    }

    .info-item i {
        width: 20px;
        margin-right: 0.5rem;
    }

    /* Responsive Design for Status Section */
    @media (max-width: 768px) {
        .status-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .status-title {
            font-size: 1.5rem;
        }

        .btn-pulse {
            width: 100%;
            padding: 1.25rem 2rem;
        }

        .status-ready, .status-waiting {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .status-icon {
            align-self: center;
        }

        /* Countdown responsive adjustments */
        .countdown-section {
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .countdown-details {
            flex-direction: column;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
        }

        .countdown-separator {
            display: none;
        }

        .countdown-days, .countdown-hours, .countdown-minutes {
            font-size: 2.5rem;
        }

        .countdown-unit {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .countdown-section {
            padding: 1rem;
            margin: 1rem 0;
        }

        .countdown-details {
            padding: 0.75rem 1rem;
        }

        .countdown-days, .countdown-hours, .countdown-minutes {
            font-size: 2rem;
        }

        .countdown-unit {
            font-size: 0.75rem;
        }

        .countdown-date {
            font-size: 1.2rem;
        }

        .countdown-time {
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function() {
    // Initialize countdown timer if waiting for next update
    const countdownTimer = document.getElementById('countdown-timer');
    if (countdownTimer) {
        initializeCountdown();
    }
    
    // Progress bars are handled with simple CSS in the new table
});

function initializeCountdown() {
    const countdownElement = document.getElementById('countdown-timer');
    const nextUpdateDate = new Date('{{ $nextUpdateDate ? $nextUpdateDate->format("Y-m-d H:i:s") : "" }}');
    
    if (!nextUpdateDate || isNaN(nextUpdateDate.getTime())) {
        countdownElement.textContent = 'Calculating...';
        return;
    }

    function updateCountdown() {
        const now = new Date();
        const timeLeft = nextUpdateDate - now;

        if (timeLeft <= 0) {
            // Time is up, reload the page to show updated status
            location.reload();
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));

        // Update the countdown display elements in the status section
        const daysElement = document.getElementById('countdown-days');
        const hoursElement = document.getElementById('countdown-hours');
        const minutesElement = document.getElementById('countdown-minutes');

        if (daysElement) daysElement.textContent = days;
        if (hoursElement) hoursElement.textContent = hours;
        if (minutesElement) minutesElement.textContent = minutes;

        // Also update the main countdown timer if it exists
        if (countdownElement) {
            if (days > 0) {
                countdownElement.textContent = `${days}d ${hours}h ${minutes}m`;
            } else if (hours > 0) {
                countdownElement.textContent = `${hours}h ${minutes}m`;
            } else {
                countdownElement.textContent = `${minutes}m`;
            }
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 60000); // Update every minute
}

function exportToPDF(updateId) {
    // Prevent table shrinking during export
    const tableContainer = document.querySelector('.ph-table-container');
    const tableCard = document.querySelector('.ph-card');
    
    if (tableContainer) {
        // Store original dimensions
        tableContainer.dataset.originalWidth = tableContainer.style.width || '100%';
        tableContainer.dataset.originalMinWidth = tableContainer.style.minWidth || '';
        
        // Ensure table maintains size
        tableContainer.style.width = '100%';
        tableContainer.style.minWidth = '100%';
        tableContainer.style.flex = '1';
    }
    
    if (tableCard) {
        tableCard.style.minHeight = '400px';
    }
    
    // Create a loading state
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    const originalWidth = exportBtn.offsetWidth;
    const originalHeight = exportBtn.offsetHeight;
    
    // Set minimum dimensions to prevent layout shifts
    exportBtn.style.minWidth = originalWidth + 'px';
    exportBtn.style.minHeight = originalHeight + 'px';
    
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
    
    try {
        // Open the printable report in a new window
        const printWindow = window.open(`/crop-progress/export/${updateId}`, '_blank');
        
        // Wait for the page to load, then trigger print
        if (printWindow) {
            printWindow.onload = function() {
                // Small delay to ensure content is fully loaded
                setTimeout(() => {
                    printWindow.print();
                    // Show success message
                    showExportSuccess();
                    // Reset button text and dimensions after a delay
                    setTimeout(() => {
                        exportBtn.innerHTML = originalText;
                        exportBtn.style.minWidth = '';
                        exportBtn.style.minHeight = '';
                        // Restore table container dimensions
                        restoreTableDimensions();
                    }, 2000);
                }, 1000);
            };
            
            // Handle case where window fails to load
            printWindow.onerror = function() {
                exportBtn.innerHTML = originalText;
                exportBtn.style.minWidth = '';
                exportBtn.style.minHeight = '';
                showExportError('Failed to load the report. Please try again or use the Print Report option.');
            };
        } else {
            // Fallback if popup is blocked
            exportBtn.innerHTML = originalText;
            exportBtn.style.minWidth = '';
            exportBtn.style.minHeight = '';
            showExportInfo('Popup blocked. Opening export in new tab instead.');
            // Use the hidden form as fallback
            const form = document.getElementById(`export-form-${updateId}`);
            if (form) {
                form.submit();
            } else {
                // Last resort: redirect to the print page
                window.location.href = `/crop-progress/export/${updateId}`;
            }
        }
    } catch (error) {
        console.error('Export error:', error);
        exportBtn.innerHTML = originalText;
        exportBtn.style.minWidth = '';
        exportBtn.style.minHeight = '';
        showExportError('An error occurred while exporting. Please try again.');
    }
}

function restoreTableDimensions() {
    const tableContainer = document.querySelector('.ph-table-container');
    const tableCard = document.querySelector('.ph-card');
    
    if (tableContainer && tableContainer.dataset.originalWidth) {
        tableContainer.style.width = tableContainer.dataset.originalWidth;
        tableContainer.style.minWidth = tableContainer.dataset.originalMinWidth;
        tableContainer.style.flex = '';
    }
    
    if (tableCard) {
        tableCard.style.minHeight = '';
    }
}

function showExportSuccess() {
    // Create a temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    successDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    successDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        PDF generated successfully! Print dialog opened.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(successDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.remove();
        }
    }, 5000);
}

function showExportError(message) {
    // Create a temporary error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    errorDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(errorDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

function showExportInfo(message) {
    // Create a temporary info message
    const infoDiv = document.createElement('div');
    infoDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
    infoDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    infoDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(infoDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (infoDiv.parentNode) {
            infoDiv.remove();
        }
    }, 5000);
}

function showQuestionsSummary(updateId, type) {
    const modal = new bootstrap.Modal(document.getElementById('questionsSummaryModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    // Set modal title
    modalTitle.textContent = '📊 Progress Summary';
    
    // Show modal immediately
    modal.show();
    
    // Ensure modal is properly positioned above taskbar
    setTimeout(() => {
        const modalElement = document.getElementById('questionsSummaryModal');
        if (modalElement) {
            modalElement.style.zIndex = '99999';
            modalElement.style.display = 'block';
            modalElement.style.position = 'fixed';
            modalElement.style.top = '0';
            modalElement.style.left = '0';
            modalElement.style.width = '100%';
            modalElement.style.height = '100%';
        }
        
        // Also ensure modal dialog is properly positioned
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.zIndex = '100000';
            modalDialog.style.marginTop = '3rem';
            modalDialog.style.marginBottom = '3rem';
        }
    }, 100);
    
    // Use mock data instead of API call
    const mockSummary = {
        update_date: '{{ $lastUpdate ? $lastUpdate->update_date->format("M d, Y") : "N/A" }}',
        progress: '{{ $lastUpdate ? $lastUpdate->calculated_progress : 0 }}',
        method: '{{ $lastUpdate ? ucfirst($lastUpdate->update_method) : "N/A" }}',
        questions: [
            {
                question: 'Plant Health:',
                answer: 'Good',
                explanation: 'Plants are showing healthy growth patterns'
            },
            {
                question: 'Leaf Condition:',
                answer: 'Good',
                explanation: 'Leaves are green and well-formed'
            },
            {
                question: 'Growth Rate:',
                answer: 'Slower',
                explanation: 'Growth has slowed due to seasonal changes'
            },
            {
                question: 'Water Availability:',
                answer: 'Excellent',
                explanation: 'Adequate water supply maintained'
            },
            {
                question: 'Pest Pressure:',
                answer: 'Low',
                explanation: 'Minimal pest activity observed'
            },
            {
                question: 'Disease Issues:',
                answer: 'Minor',
                explanation: 'Some minor leaf spots detected'
            },
            {
                question: 'Nutrient Deficiency:',
                answer: 'Moderate',
                explanation: 'Slight yellowing indicates nutrient needs'
            },
            {
                question: 'Weather Impact:',
                answer: 'Positive',
                explanation: 'Favorable weather conditions'
            },
            {
                question: 'Stage Progression:',
                answer: 'On_track',
                explanation: 'Development progressing as expected'
            },
            {
                question: 'Overall Satisfaction:',
                answer: 'Satisfied',
                explanation: 'Crop performance meets expectations'
            }
        ]
    };
    
    // Display content based on type
    if (type === 'full') {
        modalContent.innerHTML = generateFullSummaryHTML(mockSummary);
    } else {
        modalContent.innerHTML = generateAnswerSummaryHTML(mockSummary);
    }
}

function showRecommendations(updateId) {
    const modal = new bootstrap.Modal(document.getElementById('questionsSummaryModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    // Set modal title
    modalTitle.innerHTML = '<i class="fas fa-robot"></i> AI-Powered Recommendations';
    
    // Show modal immediately
    modal.show();
    
    // Ensure modal is properly positioned above taskbar
    setTimeout(() => {
        const modalElement = document.getElementById('questionsSummaryModal');
        if (modalElement) {
            modalElement.style.zIndex = '99999';
            modalElement.style.display = 'block';
            modalElement.style.position = 'fixed';
            modalElement.style.top = '0';
            modalElement.style.left = '0';
            modalElement.style.width = '100%';
            modalElement.style.height = '100%';
        }
        
        // Also ensure modal dialog is properly positioned
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.zIndex = '100000';
            modalDialog.style.marginTop = '3rem';
            modalDialog.style.marginBottom = '3rem';
        }
    }, 100);
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading AI recommendations...</p>
        </div>
    `;
    
    // Fetch recommendations from the server
    fetch(`/crop-progress/${updateId}/recommendations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalContent.innerHTML = generateRecommendationsHTML(data.recommendations, data.recommendation_summary);
            } else {
                modalContent.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>Unable to load recommendations. Please try again.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching recommendations:', error);
            modalContent.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Error loading recommendations. Please try again.</p>
                </div>
            `;
        });
}

function generateRecommendationsHTML(recommendations, summary) {
    let html = `
        <div class="recommendations-container">
            <div class="recommendations-header">
                <h5>🤖 AI-Powered Recommendations</h5>
                <p>${summary}</p>
            </div>
    `;
    
    // Add priority alerts
    if (recommendations.priority_alerts && recommendations.priority_alerts.length > 0) {
        html += `
            <div class="recommendation-category alert">
                <h6 class="category-title text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Priority Alerts
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.priority_alerts.map(item => `
                        <li class="recommendation-item ${item.includes('✅') ? 'text-success' : 'text-danger'}">
                            ${item}
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add immediate actions
    if (recommendations.immediate_actions && recommendations.immediate_actions.length > 0) {
        html += `
            <div class="recommendation-category">
                <h6 class="category-title text-primary">
                    <i class="fas fa-bolt"></i>
                    Immediate Actions
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.immediate_actions.map(item => `
                        <li class="recommendation-item ${item.includes('✅') ? 'text-success' : ''}">
                            ${item}
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add weekly plan
    if (recommendations.weekly_plan && recommendations.weekly_plan.length > 0) {
        html += `
            <div class="recommendation-category weekly">
                <h6 class="category-title text-info">
                    <i class="fas fa-calendar-week"></i>
                    Weekly Plan
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.weekly_plan.map(item => `
                        <li class="recommendation-item">${item}</li>
                    `).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add long term tips
    if (recommendations.long_term_tips && recommendations.long_term_tips.length > 0) {
        html += `
            <div class="recommendation-category tips">
                <h6 class="category-title text-warning">
                    <i class="fas fa-lightbulb"></i>
                    Long-term Tips
                </h6>
                <ul class="recommendation-list">
                    <li class="recommendation-item">${recommendations.long_term_tips.join('</li><li class="recommendation-item">')}</li>
                </ul>
            </div>
        `;
    }
    
    html += `
        </div>
    `;
    
    return html;
}

function generateFullSummaryHTML(summary) {
    return `
        <div class="summary-container">
            <div class="summary-header text-center mb-4">
                <h5 class="text-primary mb-2">📊 Progress Summary</h5>
                <p class="text-muted mb-0">Updated on ${summary.update_date}</p>
            </div>
            
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="summary-stats mb-4">
                        <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded mb-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-chart-line fa-2x text-primary"></i>
                            </div>
                            <div class="stat-value text-primary fw-bold fs-4">${summary.progress}%</div>
                            <div class="stat-label text-muted">Progress</div>
                        </div>
                        
                        <div class="stat-card text-center p-3 bg-success bg-opacity-10 rounded">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-clipboard-check fa-2x text-success"></i>
                            </div>
                            <div class="stat-value text-success fw-bold fs-4">${summary.method}</div>
                            <div class="stat-label text-muted">Method</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="summary-status">
                        <h6 class="text-secondary mb-3">🌱 Current Status:</h6>
                        <div class="status-grid">
                            <div class="status-item d-flex align-items-center mb-2">
                                <i class="fas fa-leaf text-success me-3"></i>
                                <span>Plants are healthy and growing well</span>
                            </div>
                            <div class="status-item d-flex align-items-center mb-2">
                                <i class="fas fa-tint text-info me-3"></i>
                                <span>Water supply is adequate</span>
                            </div>
                            <div class="status-item d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-warning me-3"></i>
                                <span>Growth is on schedule</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-note mt-4 p-3 bg-light rounded text-center">
                <i class="fas fa-info-circle text-info me-2"></i>
                <span class="text-muted">Your crops are progressing well! Keep up the good work.</span>
            </div>
        </div>
    `;
}

function generateAnswerSummaryHTML(summary) {
    return `
        <div class="summary-container">
            <div class="summary-header mb-4">
                <h6 class="text-primary mb-2">Answer Summary</h6>
                <p class="text-muted mb-0">Date: ${summary.update_date}</p>
            </div>
            
            <div class="row">
                ${summary.questions.map((q, index) => `
                    <div class="col-md-6 mb-3">
                        <div class="answer-item p-3 border rounded h-100">
                            <div class="d-flex align-items-center">
                                <div class="question-answer">
                                    <strong class="question-label d-block mb-1">${q.question}</strong>
                                    <span class="answer-value badge bg-primary">${q.answer}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="summary-footer mt-4 p-3 bg-light rounded text-center">
                <strong>Total Progress:</strong> ${summary.progress}%
            </div>
        </div>
    `;
}

// Progress bars are now handled with simple CSS in the new table

function printSummary() {
    const modalContent = document.getElementById('modalContent');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Questions Summary</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .question-item { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
                    .question-text { color: #2563eb; margin-bottom: 10px; }
                    .answer-text { font-weight: bold; }
                    .explanation-text { color: #6b7280; font-style: italic; }
                    .summary-footer { background: #f9fafb; padding: 15px; border-radius: 5px; margin-top: 20px; }
                    .badge { background: #2563eb; color: white; padding: 5px 10px; border-radius: 3px; }
                </style>
            </head>
            <body>
                <h2>Questions Summary</h2>
                ${modalContent.innerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

// Export functionality removed - table now focuses on displaying progress history

// Export functions for Progress History table
function exportAsPrint() {
    const tableContainer = document.querySelector('.ph-table-container');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Progress History - Print</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background-color: #f8fafc; font-weight: bold; }
                    .badge { background: #e5f3ff; color: #1d4ed8; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .progress { width: 100px; height: 20px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
                    .progress-bar { height: 100%; background: #22c55e; }
                    h1 { color: #1f2937; margin-bottom: 20px; }
                    .export-info { color: #6b7280; margin-bottom: 20px; font-size: 14px; }
                </style>
            </head>
            <body>
                <h1>Progress History Report</h1>
                <div class="export-info">
                    <p><strong>Farm:</strong> {{ $selectedFarm ? $selectedFarm->name : 'N/A' }}</p>
                    <p><strong>Export Date:</strong> ${new Date().toLocaleDateString()}</p>
                </div>
                ${tableContainer.querySelector('table').outerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function exportAsExcel() {
    const table = document.querySelector('.ph-table-container table');
    if (!table) {
        alert('No table data to export');
        return;
    }
    
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csvContent += headers.join(',') + '\r\n';
    
    // Add data rows
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach((td, index) => {
            let cellText = '';
            if (index === 0) { // Week column
                cellText = td.querySelector('.ph-badge') ? td.querySelector('.ph-badge').textContent : '';
            } else if (index === 2) { // Method column
                cellText = td.querySelector('.ph-badge') ? td.querySelector('.ph-badge').textContent : '';
            } else if (index === 3) { // Progress column
                const progressBar = td.querySelector('.ph-progress-bar');
                const progressLabel = td.querySelector('.ph-progress-label');
                cellText = progressLabel ? progressLabel.textContent : '';
            } else {
                cellText = td.textContent.trim();
            }
            rowData.push('"' + cellText.replace(/"/g, '""') + '"');
        });
        csvContent += rowData.join(',') + '\r\n';
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'progress_history.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportAsPDF() {
    // Show loading state
    const exportBtn = event.target.closest('.dropdown-item');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
    
    try {
        // Use server-side PDF generation
        const pdfUrl = '{{ route("crop-progress.export-pdf") }}';
        
        // Create a temporary link to trigger the download
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = `progress_history_${new Date().toISOString().split('T')[0]}.pdf`;
        link.style.display = 'none';
        
        // Add to DOM, click, and remove
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Reset button after a short delay
        setTimeout(() => {
            exportBtn.innerHTML = originalText;
        }, 2000);
        
        console.log('PDF download initiated via server-side generation');
        
    } catch (error) {
        console.error('PDF export failed:', error);
        exportBtn.innerHTML = originalText;
        alert('PDF export failed. Please try again or use the Print option instead.');
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // Initialize countdown timer if waiting for next update
    const countdownTimer = document.getElementById('countdown-timer');
    if (countdownTimer) {
        initializeCountdown();
    }
    
    // Progress bars are handled with simple CSS in the new table
});

function initializeCountdown() {
    const countdownElement = document.getElementById('countdown-timer');
    const nextUpdateDate = new Date('{{ $nextUpdateDate ? $nextUpdateDate->format("Y-m-d H:i:s") : "" }}');
    
    if (!nextUpdateDate || isNaN(nextUpdateDate.getTime())) {
        countdownElement.textContent = 'Calculating...';
        return;
    }

    function updateCountdown() {
        const now = new Date();
        const timeLeft = nextUpdateDate - now;

        if (timeLeft <= 0) {
            // Time is up, reload the page to show updated status
            location.reload();
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));

        // Update the countdown display elements in the status section
        const daysElement = document.getElementById('countdown-days');
        const hoursElement = document.getElementById('countdown-hours');
        const minutesElement = document.getElementById('countdown-minutes');

        if (daysElement) daysElement.textContent = days;
        if (hoursElement) hoursElement.textContent = hours;
        if (minutesElement) minutesElement.textContent = minutes;

        // Also update the main countdown timer if it exists
        if (countdownElement) {
            if (days > 0) {
                countdownElement.textContent = `${days}d ${hours}h ${minutes}m`;
            } else if (hours > 0) {
                countdownElement.textContent = `${hours}h ${minutes}m`;
            } else {
                countdownElement.textContent = `${minutes}m`;
            }
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 60000); // Update every minute
}

function exportToPDF(updateId) {
    // Prevent table shrinking during export
    const tableContainer = document.querySelector('.ph-table-container');
    const tableCard = document.querySelector('.ph-card');
    
    if (tableContainer) {
        // Store original dimensions
        tableContainer.dataset.originalWidth = tableContainer.style.width || '100%';
        tableContainer.dataset.originalMinWidth = tableContainer.style.minWidth || '';
        
        // Ensure table maintains size
        tableContainer.style.width = '100%';
        tableContainer.style.minWidth = '100%';
        tableContainer.style.flex = '1';
    }
    
    if (tableCard) {
        tableCard.style.minHeight = '400px';
    }
    
    // Create a loading state
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    const originalWidth = exportBtn.offsetWidth;
    const originalHeight = exportBtn.offsetHeight;
    
    // Set minimum dimensions to prevent layout shifts
    exportBtn.style.minWidth = originalWidth + 'px';
    exportBtn.style.minHeight = originalHeight + 'px';
    
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
    
    try {
        // Open the printable report in a new window
        const printWindow = window.open(`/crop-progress/export/${updateId}`, '_blank');
        
        // Wait for the page to load, then trigger print
        if (printWindow) {
            printWindow.onload = function() {
                // Small delay to ensure content is fully loaded
                setTimeout(() => {
                    printWindow.print();
                    // Show success message
                    showExportSuccess();
                    // Reset button text and dimensions after a delay
                    setTimeout(() => {
                        exportBtn.innerHTML = originalText;
                        exportBtn.style.minWidth = '';
                        exportBtn.style.minHeight = '';
                        // Restore table container dimensions
                        restoreTableDimensions();
                    }, 2000);
                }, 1000);
            };
            
            // Handle case where window fails to load
            printWindow.onerror = function() {
                exportBtn.innerHTML = originalText;
                exportBtn.style.minWidth = '';
                exportBtn.style.minHeight = '';
                showExportError('Failed to load the report. Please try again or use the Print Report option.');
            };
        } else {
            // Fallback if popup is blocked
            exportBtn.innerHTML = originalText;
            exportBtn.style.minWidth = '';
            exportBtn.style.minHeight = '';
            showExportInfo('Popup blocked. Opening export in new tab instead.');
            // Use the hidden form as fallback
            const form = document.getElementById(`export-form-${updateId}`);
            if (form) {
                form.submit();
            } else {
                // Last resort: redirect to the print page
                window.location.href = `/crop-progress/export/${updateId}`;
            }
        }
    } catch (error) {
        console.error('Export error:', error);
        exportBtn.innerHTML = originalText;
        exportBtn.style.minWidth = '';
        exportBtn.style.minHeight = '';
        showExportError('An error occurred while exporting. Please try again.');
    }
}

function restoreTableDimensions() {
    const tableContainer = document.querySelector('.ph-table-container');
    const tableCard = document.querySelector('.ph-card');
    
    if (tableContainer && tableContainer.dataset.originalWidth) {
        tableContainer.style.width = tableContainer.dataset.originalWidth;
        tableContainer.style.minWidth = tableContainer.dataset.originalMinWidth;
        tableContainer.style.flex = '';
    }
    
    if (tableCard) {
        tableCard.style.minHeight = '';
    }
}

function showExportSuccess() {
    // Create a temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    successDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    successDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        PDF generated successfully! Print dialog opened.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(successDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.remove();
        }
    }, 5000);
}

function showExportError(message) {
    // Create a temporary error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    errorDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(errorDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 5000);
}

function showExportInfo(message) {
    // Create a temporary info message
    const infoDiv = document.createElement('div');
    infoDiv.className = 'alert alert-info alert-dismissible fade show position-fixed';
    infoDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    infoDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(infoDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (infoDiv.parentNode) {
            infoDiv.remove();
        }
    }, 5000);
}

function showQuestionsSummary(updateId, type) {
    const modal = new bootstrap.Modal(document.getElementById('questionsSummaryModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    // Set modal title
    modalTitle.textContent = '📊 Progress Summary';
    
    // Show modal immediately
    modal.show();
    
    // Ensure modal is properly positioned above taskbar
    setTimeout(() => {
        const modalElement = document.getElementById('questionsSummaryModal');
        if (modalElement) {
            modalElement.style.zIndex = '99999';
            modalElement.style.display = 'block';
            modalElement.style.position = 'fixed';
            modalElement.style.top = '0';
            modalElement.style.left = '0';
            modalElement.style.width = '100%';
            modalElement.style.height = '100%';
        }
        
        // Also ensure modal dialog is properly positioned
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.zIndex = '100000';
            modalDialog.style.marginTop = '3rem';
            modalDialog.style.marginBottom = '3rem';
        }
    }, 100);
    
    // Use mock data instead of API call
    const mockSummary = {
        update_date: '{{ $lastUpdate ? $lastUpdate->update_date->format("M d, Y") : "N/A" }}',
        progress: '{{ $lastUpdate ? $lastUpdate->calculated_progress : 0 }}',
        method: '{{ $lastUpdate ? ucfirst($lastUpdate->update_method) : "N/A" }}',
        questions: [
            {
                question: 'Plant Health:',
                answer: 'Good',
                explanation: 'Plants are showing healthy growth patterns'
            },
            {
                question: 'Leaf Condition:',
                answer: 'Good',
                explanation: 'Leaves are green and well-formed'
            },
            {
                question: 'Growth Rate:',
                answer: 'Slower',
                explanation: 'Growth has slowed due to seasonal changes'
            },
            {
                question: 'Water Availability:',
                answer: 'Excellent',
                explanation: 'Adequate water supply maintained'
            },
            {
                question: 'Pest Pressure:',
                answer: 'Low',
                explanation: 'Minimal pest activity observed'
            },
            {
                question: 'Disease Issues:',
                answer: 'Minor',
                explanation: 'Some minor leaf spots detected'
            },
            {
                question: 'Nutrient Deficiency:',
                answer: 'Moderate',
                explanation: 'Slight yellowing indicates nutrient needs'
            },
            {
                question: 'Weather Impact:',
                answer: 'Positive',
                explanation: 'Favorable weather conditions'
            },
            {
                question: 'Stage Progression:',
                answer: 'On_track',
                explanation: 'Development progressing as expected'
            },
            {
                question: 'Overall Satisfaction:',
                answer: 'Satisfied',
                explanation: 'Crop performance meets expectations'
            }
        ]
    };
    
    // Display content based on type
    if (type === 'full') {
        modalContent.innerHTML = generateFullSummaryHTML(mockSummary);
    } else {
        modalContent.innerHTML = generateAnswerSummaryHTML(mockSummary);
    }
}

function showRecommendations(updateId) {
    const modal = new bootstrap.Modal(document.getElementById('questionsSummaryModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    // Set modal title
    modalTitle.innerHTML = '<i class="fas fa-robot"></i> AI-Powered Recommendations';
    
    // Show modal immediately
    modal.show();
    
    // Ensure modal is properly positioned above taskbar
    setTimeout(() => {
        const modalElement = document.getElementById('questionsSummaryModal');
        if (modalElement) {
            modalElement.style.zIndex = '99999';
            modalElement.style.display = 'block';
            modalElement.style.position = 'fixed';
            modalElement.style.top = '0';
            modalElement.style.left = '0';
            modalElement.style.width = '100%';
            modalElement.style.height = '100%';
        }
        
        // Also ensure modal dialog is properly positioned
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.zIndex = '100000';
            modalDialog.style.marginTop = '3rem';
            modalDialog.style.marginBottom = '3rem';
        }
    }, 100);
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading AI recommendations...</p>
        </div>
    `;
    
    // Fetch recommendations from the server
    fetch(`/crop-progress/${updateId}/recommendations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalContent.innerHTML = generateRecommendationsHTML(data.recommendations, data.recommendation_summary);
            } else {
                modalContent.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <p>Unable to load recommendations. Please try again.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching recommendations:', error);
            modalContent.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Error loading recommendations. Please try again.</p>
                </div>
            `;
        });
}

function generateRecommendationsHTML(recommendations, summary) {
    let html = `
        <div class="recommendations-container">
            <div class="recommendations-header">
                <h5>🤖 AI-Powered Recommendations</h5>
                <p>${summary}</p>
            </div>
    `;
    
    // Add priority alerts
    if (recommendations.priority_alerts && recommendations.priority_alerts.length > 0) {
        html += `
            <div class="recommendation-category alert">
                <h6 class="category-title text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Priority Alerts
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.priority_alerts.map(item => `
                        <li class="recommendation-item ${item.includes('✅') ? 'text-success' : 'text-danger'}">
                            ${item}
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add immediate actions
    if (recommendations.immediate_actions && recommendations.immediate_actions.length > 0) {
        html += `
            <div class="recommendation-category">
                <h6 class="category-title text-primary">
                    <i class="fas fa-bolt"></i>
                    Immediate Actions
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.immediate_actions.map(item => `
                        <li class="recommendation-item ${item.includes('✅') ? 'text-success' : ''}">
                            ${item}
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add weekly plan
    if (recommendations.weekly_plan && recommendations.weekly_plan.length > 0) {
        html += `
            <div class="recommendation-category weekly">
                <h6 class="category-title text-info">
                    <i class="fas fa-calendar-week"></i>
                    Weekly Plan
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.weekly_plan.map(item => `
                        <li class="recommendation-item">${item}</li>
                    `).join('')}
                </ul>
            </div>
        `;
    }
    
    // Add long term tips
    if (recommendations.long_term_tips && recommendations.long_term_tips.length > 0) {
        html += `
            <div class="recommendation-category tips">
                <h6 class="category-title text-warning">
                    <i class="fas fa-lightbulb"></i>
                    Long-term Tips
                </h6>
                <ul class="recommendation-list">
                    <li class="recommendation-item">${recommendations.long_term_tips.join('</li><li class="recommendation-item">')}</li>
                </ul>
            </div>
        `;
    }
    
    html += `
        </div>
    `;
    
    return html;
}

function generateFullSummaryHTML(summary) {
    return `
        <div class="summary-container">
            <div class="summary-header text-center mb-4">
                <h5 class="text-primary mb-2">📊 Progress Summary</h5>
                <p class="text-muted mb-0">Updated on ${summary.update_date}</p>
            </div>
            
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="summary-stats mb-4">
                        <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded mb-3">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-chart-line fa-2x text-primary"></i>
                            </div>
                            <div class="stat-value text-primary fw-bold fs-4">${summary.progress}%</div>
                            <div class="stat-label text-muted">Progress</div>
                        </div>
                        
                        <div class="stat-card text-center p-3 bg-success bg-opacity-10 rounded">
                            <div class="stat-icon mb-2">
                                <i class="fas fa-clipboard-check fa-2x text-success"></i>
                            </div>
                            <div class="stat-value text-success fw-bold fs-4">${summary.method}</div>
                            <div class="stat-label text-muted">Method</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="summary-status">
                        <h6 class="text-secondary mb-3">🌱 Current Status:</h6>
                        <div class="status-grid">
                            <div class="status-item d-flex align-items-center mb-2">
                                <i class="fas fa-leaf text-success me-3"></i>
                                <span>Plants are healthy and growing well</span>
                            </div>
                            <div class="status-item d-flex align-items-center mb-2">
                                <i class="fas fa-tint text-info me-3"></i>
                                <span>Water supply is adequate</span>
                            </div>
                            <div class="status-item d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-warning me-3"></i>
                                <span>Growth is on schedule</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-note mt-4 p-3 bg-light rounded text-center">
                <i class="fas fa-info-circle text-info me-2"></i>
                <span class="text-muted">Your crops are progressing well! Keep up the good work.</span>
            </div>
        </div>
    `;
}

function generateAnswerSummaryHTML(summary) {
    return `
        <div class="summary-container">
            <div class="summary-header mb-4">
                <h6 class="text-primary mb-2">Answer Summary</h6>
                <p class="text-muted mb-0">Date: ${summary.update_date}</p>
            </div>
            
            <div class="row">
                ${summary.questions.map((q, index) => `
                    <div class="col-md-6 mb-3">
                        <div class="answer-item p-3 border rounded h-100">
                            <div class="d-flex align-items-center">
                                <div class="question-answer">
                                    <strong class="question-label d-block mb-1">${q.question}</strong>
                                    <span class="answer-value badge bg-primary">${q.answer}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="summary-footer mt-4 p-3 bg-light rounded text-center">
                <strong>Total Progress:</strong> ${summary.progress}%
            </div>
        </div>
    `;
}

// Progress bars are now handled with simple CSS in the new table

function printSummary() {
    const modalContent = document.getElementById('modalContent');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Questions Summary</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .question-item { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
                    .question-text { color: #2563eb; margin-bottom: 10px; }
                    .answer-text { font-weight: bold; }
                    .explanation-text { color: #6b7280; font-style: italic; }
                    .summary-footer { background: #f9fafb; padding: 15px; border-radius: 5px; margin-top: 20px; }
                    .badge { background: #2563eb; color: white; padding: 5px 10px; border-radius: 3px; }
                </style>
            </head>
            <body>
                <h2>Questions Summary</h2>
                ${modalContent.innerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

// Export functionality removed - table now focuses on displaying progress history

// Export functions for Progress History table
function exportAsPrint() {
    const tableContainer = document.querySelector('.ph-table-container');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Progress History - Print</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                    th { background-color: #f8fafc; font-weight: bold; }
                    .badge { background: #e5f3ff; color: #1d4ed8; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .progress { width: 100px; height: 20px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
                    .progress-bar { height: 100%; background: #22c55e; }
                    h1 { color: #1f2937; margin-bottom: 20px; }
                    .export-info { color: #6b7280; margin-bottom: 20px; font-size: 14px; }
                </style>
            </head>
            <body>
                <h1>Progress History Report</h1>
                <div class="export-info">
                    <p><strong>Farm:</strong> {{ $selectedFarm ? $selectedFarm->name : 'N/A' }}</p>
                    <p><strong>Export Date:</strong> ${new Date().toLocaleDateString()}</p>
                </div>
                ${tableContainer.querySelector('table').outerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function exportAsExcel() {
    const table = document.querySelector('.ph-table-container table');
    if (!table) {
        alert('No table data to export');
        return;
    }
    
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csvContent += headers.join(',') + '\r\n';
    
    // Add data rows
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach((td, index) => {
            let cellText = '';
            if (index === 0) { // Week column
                cellText = td.querySelector('.ph-badge') ? td.querySelector('.ph-badge').textContent : '';
            } else if (index === 2) { // Method column
                cellText = td.querySelector('.ph-badge') ? td.querySelector('.ph-badge').textContent : '';
            } else if (index === 3) { // Progress column
                const progressBar = td.querySelector('.ph-progress-bar');
                const progressLabel = td.querySelector('.ph-progress-label');
                cellText = progressLabel ? progressLabel.textContent : '';
            } else {
                cellText = td.textContent.trim();
            }
            rowData.push('"' + cellText.replace(/"/g, '""') + '"');
        });
        csvContent += rowData.join(',') + '\r\n';
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'progress_history.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


</script>
@endpush
