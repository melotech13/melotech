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
        @if($canUpdate)
            <!-- Update Action -->
            <div class="update-action-section">
                <div class="action-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle me-2"></i>
                            Ready to Update Progress?
                        </h3>
                    </div>
                    <div class="card-content text-center">
                        <p class="mb-4">Answer simple questions about your crop condition to track progress</p>
                        <a href="{{ route('crop-progress.questions') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i>Start Progress Update
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Progress Table -->
        <div class="progress-table-section">
            <div class="table-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Progress History
                    </h3>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                @if(isset($progressUpdates) && $progressUpdates->count() > 0)
                                    @foreach($progressUpdates as $update)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $update->getWeekName() }}</span>
                                            </td>
                                            <td>{{ $update->update_date->format('M d, Y') }}</td>
                                            <td><span class="badge bg-primary">{{ ucfirst($update->update_method) }}</span></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" data-width="{{ $update->calculated_progress }}">
                                                        {{ $update->calculated_progress }}%
                                                    </div>
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
                                @else
                                    <tr class="no-data-row">
                                        <td colspan="6" class="text-center py-5">
                                            <div class="no-data-content">
                                                <i class="fas fa-table fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Progress Updates Yet</h5>
                                                <p class="text-muted mb-3">Complete your first progress update to see data here</p>
                                                <a href="{{ route('crop-progress.questions') }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus me-2"></i>Start Progress Update
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    

                </div>
            </div>
        </div>
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
     <div class="modal-dialog modal-lg">
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
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        position: relative;
        z-index: 1;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    /* Card styling */
    .action-card, .table-card, .no-farms-section {
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

    /* Table styling */
    .table-responsive {
        overflow-x: auto;
        width: 100%;
        height: 400px;
        min-height: 400px;
        max-height: 400px;
        box-sizing: border-box;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #ffffff;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        height: 400px;
        min-height: 400px;
        max-height: 400px;
        table-layout: fixed;
        margin: 0;
    }

    .table tbody {
        height: 320px;
        min-height: 320px;
        max-height: 320px;
    }

    .table-card {
        height: 500px;
        min-height: 500px;
        max-height: 500px;
        box-sizing: border-box;
        overflow: hidden;
    }

    .card-content {
        height: 450px;
        min-height: 450px;
        max-height: 450px;
        padding: 1.5rem;
        box-sizing: border-box;
        overflow: hidden;
    }

    /* Table headers */
    .table th {
        background: #f8fafc;
        color: #374151;
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid #e5e7eb;
        font-size: 0.9rem;
        text-align: left;
        height: 60px;
        vertical-align: top;
        border-right: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table th:last-child {
        border-right: none;
    }

    /* Table cells */
    .table td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
        height: 80px;
        border-right: 1px solid #f3f4f6;
        padding-top: 1.25rem;
    }

    .table td:last-child {
        border-right: none;
    }

    /* Table rows */
    .table tbody tr {
        height: 80px;
        min-height: 80px;
        max-height: 80px;
        display: table-row;
        vertical-align: top;
    }

    .table tbody tr:first-child {
        vertical-align: top;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* No data row */
    .no-data-row {
        height: 320px;
        min-height: 320px;
        max-height: 320px;
        vertical-align: top;
    }

    .no-data-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        padding: 2rem;
        text-align: center;
        padding-top: 3rem;
    }

    .no-data-content i {
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .no-data-content h5 {
        color: #6b7280;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .no-data-content p {
        color: #9ca3af;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    /* Progress bar */
    .progress {
        height: 20px;
        border-radius: 10px;
        background-color: #f3f4f6;
        border: 1px solid #e5e7eb;
    }

    .progress-bar {
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        line-height: 18px;
    }

    /* Badge */
    .badge {
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
    }

    /* Button */
    .no-data-content .btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
    }

    /* Table hover */
    .table-hover tbody tr:hover {
        background-color: #f9fafb;
        transition: background-color 0.2s ease;
    }

    /* Table cell content */
    .table td .progress {
        margin: 0;
        width: 100%;
    }

    .table td .badge {
        display: inline-block;
        margin: 0;
    }

    /* Dropdown styling */
    .table .dropdown-toggle {
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #6b7280;
        transition: all 0.2s ease;
    }

    .table .dropdown-toggle:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
        color: #374151;
    }

    /* Fix dropdown positioning - always appear below and aligned to the right */
    .table .dropdown {
        position: relative;
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
    }

    .table .dropdown-menu.show {
        display: block !important;
    }

    /* Table container styling */
    .table-responsive {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    /* Font sizes */
    .table th,
    .table td {
        font-size: 0.875rem;
        line-height: 1.4;
    }

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

    /* Table column widths */
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 80px;
        min-width: 80px;
    }

    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: 100px;
        min-width: 100px;
    }

    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 80px;
        min-width: 80px;
    }

    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 120px;
        min-width: 120px;
    }

    .table th:nth-child(5),
    .table td:nth-child(5) {
        width: 100px;
        min-width: 100px;
    }

    .table th:nth-child(6),
    .table td:nth-child(6) {
        width: 80px;
        min-width: 80px;
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
            padding: 1rem 0.75rem;
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
    

    
    /* Override any height constraints that might hide content */
    .table-responsive,
    .table-card,
    .card-content {
        min-height: auto !important;
        max-height: none !important;
        height: auto !important;
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
    
    // Initialize progress bars
    initializeProgressBars();
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
            countdownElement.textContent = 'Available now!';
            location.reload(); // Reload to show update options
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));

        if (days > 0) {
            countdownElement.textContent = `${days}d ${hours}h ${minutes}m`;
        } else if (hours > 0) {
            countdownElement.textContent = `${hours}h ${minutes}m`;
        } else {
            countdownElement.textContent = `${minutes}m`;
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 60000); // Update every minute
}

function exportToPDF(updateId) {
    // Prevent table shrinking during export
    const tableContainer = document.querySelector('.table-responsive');
    const tableCard = document.querySelector('.table-card');
    
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
    const tableContainer = document.querySelector('.table-responsive');
    const tableCard = document.querySelector('.table-card');
    
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
    modalTitle.textContent = '🤖 AI Recommendations';
    
    // Show modal immediately
    modal.show();
    
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
            <div class="recommendations-header text-center mb-4">
                <h5 class="text-primary mb-2">🤖 AI-Powered Recommendations</h5>
                <p class="text-muted mb-0">${summary}</p>
            </div>
    `;
    
    // Add priority alerts
    if (recommendations.priority_alerts && recommendations.priority_alerts.length > 0) {
        html += `
            <div class="recommendation-category alert mb-4">
                <h6 class="category-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
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
            <div class="recommendation-category mb-4">
                <h6 class="category-title text-primary">
                    <i class="fas fa-bolt me-2"></i>
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
            <div class="recommendation-category mb-4">
                <h6 class="category-title text-info">
                    <i class="fas fa-calendar-week me-2"></i>
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
            <div class="recommendation-category mb-4">
                <h6 class="category-title text-warning">
                    <i class="fas fa-lightbulb me-2"></i>
                    Long-term Tips
                </h6>
                <ul class="recommendation-list">
                    ${recommendations.long_term_tips.map(item => `
                        <li class="recommendation-item">${item}</li>
                    `).join('')}
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

function initializeProgressBars() {
    // Find all progress bars with data-width attribute
    const progressBars = document.querySelectorAll('.progress-bar[data-width]');
    
    progressBars.forEach(bar => {
        const width = bar.getAttribute('data-width');
        if (width && !isNaN(width)) {
            bar.style.width = width + '%';
        }
    });
}

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
</script>
@endpush
