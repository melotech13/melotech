@extends('layouts.app')

@section('title', 'Crop Growth - MeloTech')

@section('content')
<div class="crop-growth-container">

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
                    <i class="fas fa-seedling"></i>
                    Crop Growth Dashboard
                </h1>
                <p class="page-subtitle">Comprehensive monitoring and management of your watermelon crop development</p>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-tractor"></i>
                        <span>{{ $farms->count() }} {{ Str::plural('Farm', $farms->count()) }}</span>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-chart-line"></i>
                        <span>Active Growth</span>
                    </div>
                </div>
            </div>
            <div class="header-visual">
                <div class="header-circle">
                    <i class="fas fa-leaf"></i>
                </div>
            </div>
        </div>
    </div>

         <!-- Combined Farm Selection and Crop Status -->
     @if($farms->count() > 0)
         <div class="combined-farm-status-section">
             <div class="combined-card">
                 <div class="combined-header">
                     <div class="farm-selection-area">
                         <h3 class="section-title">
                             <i class="fas fa-farm me-2"></i>
                             Select Farm
                         </h3>
                         <p class="section-subtitle">Choose a farm to view detailed growth information</p>
                         <div class="farm-cards">
                             @foreach($farms as $farm)
                                 <div class="farm-card {{ $selectedFarm && $selectedFarm->id == $farm->id ? 'active' : '' }}" 
                                      onclick="selectFarm('{{ $farm->id }}')">
                                     <div class="farm-card-header">
                                         <div class="farm-icon">
                                             <i class="fas fa-tractor"></i>
                                         </div>
                                         <div class="farm-status {{ $selectedFarm && $selectedFarm->id == $farm->id ? 'active' : '' }}">
                                             @if($selectedFarm && $selectedFarm->id == $farm->id)
                                                 <i class="fas fa-check-circle"></i>
                                             @else
                                                 <i class="fas fa-circle"></i>
                                             @endif
                                         </div>
                                     </div>
                                     <div class="farm-card-content">
                                         <h4 class="farm-name">{{ $farm->farm_name }}</h4>
                                         <p class="farm-variety">{{ $farm->watermelon_variety }}</p>
                                         <div class="farm-details">
                                             <span class="farm-size">{{ $farm->land_size }} {{ $farm->land_size_unit }}</span>
                                             <span class="farm-date">{{ $farm->planting_date->format('M d, Y') }}</span>
                                         </div>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                     </div>
                     
                     @if($selectedFarm && $selectedCropGrowth)
                         <div class="crop-status-area">
                             <h3 class="status-title">
                                 <i class="fas fa-seedling me-2"></i>
                                 Your Crop Status
                             </h3>
                             
                             <!-- Current Stage Display -->
                             <div class="current-stage-display">
                                 <h4>Current Stage: <span class="stage-name">{{ ucfirst($selectedCropGrowth->current_stage) }}</span></h4>
                                 <p class="stage-description">
                                     @if($selectedCropGrowth->current_stage == 'seedling')
                                         Your watermelon plants are just starting to grow
                                     @elseif($selectedCropGrowth->current_stage == 'vegetative')
                                         Your plants are growing leaves and stems rapidly
                                     @elseif($selectedCropGrowth->current_stage == 'flowering')
                                         Your plants are producing flowers for pollination
                                     @elseif($selectedCropGrowth->current_stage == 'fruiting')
                                         Fruits are developing and growing
                                     @else
                                         Ready for harvest!
                                     @endif
                                 </p>
                             </div>
                             
                             <!-- Overall Progress -->
                             <div class="progress-simple">
                                 <div class="progress-label">
                                     <span>Overall Progress</span>
                                     <span class="progress-number">{{ $selectedCropGrowth->overall_progress }}%</span>
                                 </div>
                                 <div class="simple-progress-bar">
                                     <div class="simple-progress-fill" style="--progress-width: {{ $selectedCropGrowth->overall_progress }}%"></div>
                                 </div>
                             </div>
                         </div>
                     @endif
                 </div>
                 
                 @if($selectedFarm && $selectedCropGrowth)
                     <div class="combined-content">
                         <!-- Key Information Grid -->
                         <div class="key-info-grid">
                             <div class="info-card">
                                 <div class="info-icon">
                                     <i class="fas fa-calendar-day"></i>
                                 </div>
                                 <div class="info-content">
                                     <h5>Days Since Planting</h5>
                                     <span class="info-value">{{ $selectedCropGrowth->days_elapsed }}</span>
                                 </div>
                             </div>
                             
                             <div class="info-card">
                                 <div class="info-icon">
                                     <i class="fas fa-clock"></i>
                                 </div>
                                 <div class="info-content">
                                     <h5>Days Until Harvest</h5>
                                     <span class="info-value">{{ $selectedCropGrowth->days_remaining }}</span>
                                 </div>
                             </div>
                             
                             <div class="info-card">
                                 <div class="info-icon">
                                     <i class="fas fa-percentage"></i>
                                 </div>
                                 <div class="info-content">
                                     <h5>Stage Completion</h5>
                                     <span class="info-value">{{ $selectedCropGrowth->stage_progress }}%</span>
                                 </div>
                             </div>
                             
                             <div class="info-card">
                                 <div class="info-icon">
                                     <i class="fas fa-calendar-check"></i>
                                 </div>
                                 <div class="info-content">
                                     <h5>Expected Harvest</h5>
                                     <span class="info-value">
                                         @php
                                             $harvestDate = null;
                                             $isCalculated = false;
                                             if ($selectedCropGrowth->harvest_date) {
                                                 $harvestDate = $selectedCropGrowth->harvest_date;
                                             } else {
                                                 // Calculate harvest date based on planting date + 80-90 days
                                                 $plantingDate = $selectedFarm->planting_date;
                                                 $harvestDate = $plantingDate->copy()->addDays(85); // Average 85 days
                                                 $isCalculated = true;
                                             }
                                         @endphp
                                         {{ $harvestDate->format('M d, Y') }}
                                         @if($isCalculated)
                                             <small class="d-block text-muted">(Estimated)</small>
                                         @endif
                                     </span>
                                 </div>
                             </div>
                         </div>
                         
                         <!-- Action Buttons -->
                         <div class="action-buttons">
                             <button class="btn btn-primary btn-sm" onclick="refreshCropData('{{ $selectedFarm->id }}')">
                                 <i class="fas fa-sync-alt me-1"></i>Update Data
                             </button>
                             <button class="btn btn-outline-secondary btn-sm" onclick="forceUpdateProgress('{{ $selectedFarm->id }}')">
                                 <i class="fas fa-clock me-1"></i>Recalculate Time
                             </button>
                         </div>
                     </div>
                 @endif
             </div>
         </div>

         <!-- Selected Farm Details -->
         @if($selectedFarm && $selectedCropGrowth)
             <div class="farm-details-section">
                 <div class="main-content">

                                         <!-- Enhanced Stage Milestones -->
                     <div class="stage-milestones-card">
                         <div class="card-header">
                             <h3 class="card-title">
                                 <i class="fas fa-route me-2"></i>
                                 Growth Stages Timeline
                             </h3>
                             <div class="stage-summary">
                                 <span class="stage-count">{{ $selectedCropGrowth->current_stage_number ?? 1 }}/5</span>
                                 <span class="stage-label">Stages Completed</span>
                             </div>
                         </div>
                         <div class="card-content">
                             <div class="stage-timeline">
                                 @php
                                     $stages = [
                                         'seedling' => ['name' => 'Seedling', 'description' => 'Early growth phase with developing roots and leaves', 'duration' => '10-15 days'],
                                         'vegetative' => ['name' => 'Vegetative', 'description' => 'Rapid leaf and stem development', 'duration' => '20-25 days'],
                                         'flowering' => ['name' => 'Flowering', 'description' => 'Flower production and pollination', 'duration' => '15-20 days'],
                                         'fruiting' => ['name' => 'Fruiting', 'description' => 'Fruit development and growth', 'duration' => '20-25 days'],
                                         'harvest' => ['name' => 'Harvest', 'description' => 'Ready for harvest', 'duration' => 'Ready']
                                     ];
                                     $currentStageIndex = array_search($selectedCropGrowth->current_stage, array_keys($stages));
                                 @endphp
                                 
                                 @foreach($stages as $stageKey => $stageData)
                                     @php
                                         $isCompleted = $currentStageIndex !== false && $loop->index < $currentStageIndex;
                                         $isCurrent = $currentStageIndex !== false && $loop->index == $currentStageIndex;
                                         $isUpcoming = $currentStageIndex !== false && $loop->index > $currentStageIndex;
                                     @endphp
                                     
                                     <div class="stage-item {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }} {{ $isUpcoming ? 'upcoming' : '' }}">
                                         <div class="stage-marker">
                                             @if($isCompleted)
                                                 <i class="fas fa-check-circle"></i>
                                             @elseif($isCurrent)
                                                 <i class="fas fa-play-circle"></i>
                                             @else
                                                 <i class="fas fa-circle"></i>
                                             @endif
                                         </div>
                                         <div class="stage-content">
                                             <div class="stage-header">
                                                 <h4 class="stage-name">{{ $stageData['name'] }}</h4>
                                                 <span class="stage-duration">{{ $stageData['duration'] }}</span>
                                             </div>
                                             <p class="stage-description">{{ $stageData['description'] }}</p>
                                             @if($isCurrent)
                                                 <div class="current-stage-progress">
                                                     <span class="progress-text">{{ $selectedCropGrowth->stage_progress }}% Complete</span>
                                                     <div class="mini-progress-bar">
                                                         <div class="mini-progress-fill" style="--progress-width: {{ $selectedCropGrowth->stage_progress }}%"></div>
                                                     </div>
                                                 </div>
                                             @endif
                                             @if($isCompleted)
                                                 <div class="stage-completion">
                                                     <i class="fas fa-check me-2"></i>
                                                     <span>Completed</span>
                                                 </div>
                                             @endif
                                         </div>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>

                     <!-- Growth Insights Card -->
                     <div class="insights-card">
                         <div class="card-header">
                             <h3 class="card-title">
                                 <i class="fas fa-lightbulb me-2"></i>
                                 Growth Insights
                             </h3>
                         </div>
                         <div class="card-content">
                             <div class="insight-item">
                                 <div class="insight-icon">
                                     <i class="fas fa-thermometer-half"></i>
                                 </div>
                                 <div class="insight-content">
                                     <h5>Optimal Temperature</h5>
                                     <p>75-85°F (24-29°C) for best growth</p>
                                 </div>
                             </div>
                             <div class="insight-item">
                                 <div class="insight-icon">
                                     <i class="fas fa-tint"></i>
                                 </div>
                                 <div class="insight-content">
                                     <h5>Watering Needs</h5>
                                     <p>1-2 inches per week, deep watering</p>
                                 </div>
                             </div>
                             <div class="insight-item">
                                 <div class="insight-icon">
                                     <i class="fas fa-sun"></i>
                                 </div>
                                 <div class="insight-content">
                                     <h5>Sunlight</h5>
                                     <p>Full sun (6-8 hours daily)</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                </div>

                <!-- Sidebar removed - Growth Insights moved to main content below Growth Stages Timeline -->
            </div>
        @endif
    @else
        <!-- No Farms Message -->
        <div class="no-farms-section">
            <div class="no-farms-content">
                <div class="no-farms-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h2>No Farms Yet</h2>
                <p>You haven't created any farms yet. Create your first farm to start tracking crop growth!</p>
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addFarmModal">
                    <i class="fas fa-plus me-2"></i>Create Your First Farm
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Add Farm Modal removed - single farm per account -->

<!-- Help Modal removed - single farm per account -->

@push('styles')
<style>
    .crop-growth-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
        margin-top: 0 !important;
        padding-top: 2rem !important;
    }

    /* Enhanced Page Header */
    .page-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 25%, #60a5fa 50%, #93c5fd 75%, #dbeafe 100%);
        border-radius: 24px;
        padding: 3rem 2.5rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 20px 40px rgba(30, 64, 175, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .header-main {
        flex: 1;
    }

    .page-title {
        font-size: 3rem;
        font-weight: 900;
        margin: 0 0 1rem 0;
        text-shadow: 0 4px 12px rgba(0,0,0,0.3);
        color: #ffffff !important;
    }

    .page-subtitle {
        font-size: 1.25rem;
        margin: 0 0 1.5rem 0;
        color: #ffffff !important;
        font-weight: 400;
    }

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

    /* Header actions removed - single farm per account */

         /* Combined Farm Selection and Crop Status */
     .combined-farm-status-section {
         margin-bottom: 3rem;
     }
     
     .combined-card {
         background: white;
         border-radius: 20px;
         box-shadow: 0 8px 32px rgba(0,0,0,0.08);
         border: 1px solid rgba(0,0,0,0.05);
         overflow: hidden;
         transition: all 0.3s ease;
     }
     
     .combined-card:hover {
         transform: translateY(-2px);
         box-shadow: 0 12px 40px rgba(0,0,0,0.12);
     }
     
     .combined-header {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 2rem;
         padding: 2rem;
         background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
         border-bottom: 1px solid rgba(0,0,0,0.05);
     }
     
     .farm-selection-area {
         padding-right: 1rem;
         border-right: 1px solid rgba(0,0,0,0.1);
     }
     
     .crop-status-area {
         padding-left: 1rem;
     }
     
     .status-title {
         font-size: 1.35rem;
         font-weight: 700;
         color: #1f2937;
         margin: 0 0 1.5rem 0;
     }
     
     .combined-content {
         padding: 2rem;
     }
     
     /* Enhanced Farm Selection */
     .farm-selection-section {
         margin-bottom: 3rem;
     }

    .section-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
    }

    .section-subtitle {
        color: #6b7280;
        font-size: 1.1rem;
        margin: 0;
    }

    .farm-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .farm-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
    }

    .farm-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #10b981);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .farm-card:hover::before {
        transform: scaleX(1);
    }

    .farm-card:hover {
        border-color: #3b82f6;
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.15);
    }

    .farm-card.active {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.2);
    }

    .farm-card.active::before {
        background: linear-gradient(90deg, #10b981, #059669);
        transform: scaleX(1);
    }

    .farm-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .farm-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .farm-status {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d1d5db;
        transition: all 0.3s ease;
    }

    .farm-status.active {
        color: #10b981;
    }

    .farm-card-content h4 {
        margin: 0 0 0.5rem 0;
        color: #1f2937;
        font-weight: 700;
        font-size: 1.25rem;
    }

    .farm-variety {
        color: #6b7280;
        margin: 0 0 1rem 0;
        font-size: 0.95rem;
    }

    .farm-details {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .farm-size, .farm-date {
        background: #f3f4f6;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        color: #374151;
        font-weight: 500;
    }

    /* Enhanced Layout */
    .farm-details-section {
        display: block;
        max-width: 1400px;
        margin: 0 auto;
    }

    .main-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    /* Enhanced Cards */
    .growth-overview-card,
    .stage-milestones-card,
    .insights-card,
    .quick-actions-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .growth-overview-card:hover,
    .stage-milestones-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }

    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .stage-summary {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .stage-count {
        font-size: 1.5rem;
        font-weight: 800;
        color: #10b981;
        line-height: 1;
    }

    .stage-label {
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .card-actions {
        display: flex;
        gap: 0.75rem;
    }

    .card-content {
        padding: 1.75rem;
    }

         /* Simple Growth Progress Overview */
     .simple-status {
         padding: 1rem 0;
     }

     .status-main {
         display: flex;
         flex-direction: column;
         gap: 2rem;
         margin-bottom: 2rem;
     }

     .current-stage-display {
         text-align: center;
         padding: 1.5rem;
         background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
         border-radius: 16px;
         border: 2px solid #10b981;
     }

     .current-stage-display h4 {
         margin: 0 0 1rem 0;
         color: #1f2937;
         font-size: 1.5rem;
         font-weight: 700;
     }

     .stage-name {
         color: #10b981;
         font-weight: 800;
         text-transform: uppercase;
         letter-spacing: 0.5px;
     }

     .stage-description {
         margin: 0;
         color: #6b7280;
         font-size: 1.1rem;
         line-height: 1.6;
         font-weight: 500;
     }

     .progress-simple {
         background: white;
         padding: 1.5rem;
         border-radius: 16px;
         border: 1px solid #e5e7eb;
         box-shadow: 0 4px 20px rgba(0,0,0,0.08);
     }

     .progress-label {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1rem;
     }

     .progress-label span:first-child {
         color: #1f2937;
         font-weight: 600;
         font-size: 1.1rem;
     }

     .progress-number {
         color: #10b981;
         font-weight: 800;
         font-size: 1.5rem;
     }

     .simple-progress-bar {
         width: 100%;
         height: 20px;
         background: #f3f4f6;
         border-radius: 10px;
         overflow: hidden;
         border: 2px solid #e5e7eb;
     }

     .simple-progress-fill {
         height: 100%;
         width: var(--progress-width, 0%);
         background: linear-gradient(90deg, #10b981, #34d399);
         border-radius: 8px;
         transition: width 1s ease;
         position: relative;
     }

     .simple-progress-fill::after {
         content: '';
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
         animation: shimmer 2s infinite;
     }

     @keyframes shimmer {
         0% { transform: translateX(-100%); }
         100% { transform: translateX(100%); }
     }

     .key-info-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 1rem;
         margin-bottom: 2rem;
     }

     .info-card {
         background: white;
         padding: 1.25rem;
         border-radius: 12px;
         border: 1px solid #e5e7eb;
         box-shadow: 0 2px 10px rgba(0,0,0,0.05);
         transition: all 0.3s ease;
         text-align: center;
     }

     .info-card:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 20px rgba(0,0,0,0.1);
         border-color: #10b981;
     }

     .info-icon {
         width: 48px;
         height: 48px;
         background: linear-gradient(135deg, #10b981, #34d399);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         font-size: 1.25rem;
         margin: 0 auto 1rem auto;
     }

     .info-content h5 {
         margin: 0 0 0.5rem 0;
         color: #6b7280;
         font-size: 0.9rem;
         font-weight: 600;
         text-transform: uppercase;
         letter-spacing: 0.5px;
     }

     .info-value {
         color: #1f2937;
         font-size: 1.5rem;
         font-weight: 800;
         display: block;
     }

     .action-buttons {
         display: flex;
         gap: 1rem;
         justify-content: center;
         flex-wrap: wrap;
     }

     .action-buttons .btn {
         padding: 0.75rem 1.5rem;
         font-weight: 600;
         border-radius: 8px;
         transition: all 0.3s ease;
     }

     .action-buttons .btn:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 15px rgba(0,0,0,0.1);
     }

    /* Enhanced Stage Timeline */
    .stage-timeline {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stage-item {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        padding: 1.75rem;
        border-radius: 16px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .stage-item:hover {
        transform: translateX(4px);
    }

    .stage-item.completed {
        background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .stage-item.current {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-color: rgba(59, 130, 246, 0.2);
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.1);
    }

    .stage-item.upcoming {
        background: #f9fafb;
        border-color: rgba(209, 213, 219, 0.2);
    }

    .stage-marker {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.3rem;
        transition: all 0.3s ease;
    }

    .stage-item.completed .stage-marker {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .stage-item.current .stage-marker {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .stage-item.upcoming .stage-marker {
        background: #d1d5db;
        color: #6b7280;
    }

    .stage-content {
        flex: 1;
    }

    .stage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .stage-name {
        margin: 0;
        color: #1f2937;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .stage-duration {
        background: #f3f4f6;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 600;
    }

    .stage-description {
        margin: 0 0 1rem 0;
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .current-stage-progress {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: rgba(59, 130, 246, 0.1);
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .progress-text {
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .mini-progress-bar {
        width: 140px;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .mini-progress-fill {
        height: 100%;
        width: var(--progress-width, 0%);
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    .stage-completion {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #10b981;
        font-weight: 600;
        font-size: 0.9rem;
        margin-top: 0.75rem;
    }

    /* Sidebar Cards */
    .insights-card,
    .quick-actions-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .insights-card .card-header {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-bottom: 1px solid rgba(245, 158, 11, 0.2);
    }

    .quick-actions-card .card-header {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-bottom: 1px solid rgba(59, 130, 246, 0.2);
    }

    .insight-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        background: #f8fafc;
        transition: all 0.3s ease;
    }

    .insight-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .insight-item:last-child {
        margin-bottom: 0;
    }

    .insight-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .insight-content h5 {
        margin: 0 0 0.25rem 0;
        color: #1f2937;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .insight-content p {
        margin: 0;
        color: #6b7280;
        font-size: 0.85rem;
        line-height: 1.4;
    }

    /* Button block styles removed - no longer needed */

    /* Help Modal Styles removed - single farm per account */

    /* No Farms Section */
    .no-farms-section {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .no-farms-content {
        max-width: 500px;
        margin: 0 auto;
    }

    .no-farms-icon {
        font-size: 4rem;
        color: #64748b;
        opacity: 0.5;
        margin-bottom: 1.5rem;
    }

    .no-farms-content h2 {
        color: #64748b;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .no-farms-content p {
        color: #94a3b8;
        margin-bottom: 2rem;
        font-size: 1.1rem;
        line-height: 1.6;
    }

         /* Responsive Design */
     @media (max-width: 1200px) {
         .farm-details-section {
             max-width: 100%;
             padding: 0 1rem;
         }
         
         .combined-header {
             grid-template-columns: 1fr;
             gap: 1.5rem;
         }
         
         .farm-selection-area {
             padding-right: 0;
             border-right: none;
             border-bottom: 1px solid rgba(0,0,0,0.1);
             padding-bottom: 1.5rem;
         }
         
         .crop-status-area {
             padding-left: 0;
         }
     }

    @media (max-width: 991.98px) {
        .page-header {
            flex-direction: column;
            text-align: center;
            padding: 2.5rem 1.5rem;
        }

        .header-content {
            flex-direction: column;
            align-items: center;
        }

        .page-title {
            font-size: 2.5rem;
        }

        .farm-cards {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .growth-stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .crop-growth-container {
            padding: 1.25rem 0.75rem;
        }

        .page-header {
            padding: 2rem 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .header-stats {
            justify-content: center;
        }

        .farm-cards {
            grid-template-columns: 1fr;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .progress-details {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .stage-item {
            padding: 1.25rem;
        }

        .current-stage-progress {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .stage-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up activity buttons
    setupActivityButtons();
});

function selectFarm(farmId) {
    // Update URL with selected farm
    const url = new URL(window.location);
    url.searchParams.set('selected_farm', farmId);
    window.location.href = url.toString();
}

function refreshCropData(farmId) {
    // Show loading state
    const refreshBtn = event.target.closest('button');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Fetch fresh data
    fetch(`/crop-growth/dashboard-data`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to show updated data
                window.location.reload();
            } else {
                alert('Failed to refresh data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
            alert('Failed to refresh data. Please try again.');
        })
        .finally(() => {
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        });
}

function forceUpdateProgress(farmId) {
    if (!confirm('This will force update crop progress based on current time. Continue?')) {
        return;
    }
    
    const updateBtn = event.target.closest('button');
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
    updateBtn.disabled = true;
    
    // Convert string farmId to number for the URL
    const numericFarmId = parseInt(farmId, 10);
    
    fetch(`/crop-growth/farm/${numericFarmId}/force-update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal
            const farmName = document.querySelector('.farm-name')?.textContent?.trim() || '';
            showOperationSuccess('update', 'farm', farmName);
            window.location.reload();
        } else {
            alert('Failed to update progress: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating progress:', error);
        alert('Failed to update progress. Please try again.');
    })
    .finally(() => {
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    });
}

function setupActivityButtons() {
    // Any future activity button functionality can be added here
}

// Help and Quick Actions functions removed - single farm per account

// Show success modal if farm was just created
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have a selected_farm parameter in the URL, indicating a farm was just created
    const urlParams = new URLSearchParams(window.location.search);
    const selectedFarm = urlParams.get('selected_farm');
    
    if (selectedFarm) {
        // Get the farm name from the page
        const farmNameElement = document.querySelector('.farm-name, .farm-title, h2, h3');
        const farmName = farmNameElement ? farmNameElement.textContent.trim() : '';
        
        // Show success modal
        showOperationSuccess('create', 'farm', farmName);
        
        // Clean up the URL by removing the parameter
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('selected_farm');
        window.history.replaceState({}, '', newUrl);
    }
});
</script>
@endpush
