<div class="growth-progress-container">
    <h4 class="section-title">
        <i class="fas fa-seedling me-2"></i>
        Growth Progress
    </h4>
    
    <div class="progress-container mb-3">
        <div class="d-flex justify-content-between mb-1">
            <span>Growth Stage</span>
            <span class="text-primary fw-bold">{{ $growthData['stage'] ?? 'N/A' }}</span>
        </div>
        <div class="progress" style="height: 12px; border-radius: 6px;">
            <div class="progress-bar bg-success" 
                 role="progressbar" 
                 style="width: {{ $growthData['progress'] ?? 0 }}%;" 
                 aria-valuenow="{{ $growthData['progress'] ?? 0 }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {{ $growthData['progress'] ?? 0 }}%
            </div>
        </div>
    </div>

    @if(!empty($growthData['next_steps']))
        <div class="next-steps mt-4">
            <h5 class="mb-3"><i class="fas fa-tasks me-2"></i>Recommended Next Steps</h5>
            <ul class="list-unstyled">
                @foreach($growthData['next_steps'] as $step)
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        {{ $step }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="last-updated text-muted small mt-3">
        <i class="fas fa-sync-alt me-1"></i>
        Updated: {{ $growthData['last_updated'] ?? 'Just now' }}
    </div>
</div>

@push('styles')
<style>
.growth-progress-container {
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

.progress-container {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.progress {
    overflow: visible;
    position: relative;
}

.progress-bar {
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: width 0.6s ease;
}

.next-steps {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.next-steps h5 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
}

.next-steps ul li {
    padding: 0.5rem 0;
    border-bottom: 1px dashed #e2e8f0;
    display: flex;
    align-items: center;
}

.next-steps ul li:last-child {
    border-bottom: none;
}

.last-updated {
    text-align: right;
    font-size: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .growth-progress-container {
        padding: 1rem;
    }
    
    .section-title {
        font-size: 1rem;
    }
    
    .progress-container, .next-steps {
        padding: 0.75rem;
    }
}
</style>
@endpush
