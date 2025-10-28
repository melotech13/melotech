@extends('layouts.app')

@section('title', 'Recent Analyses - Nutrient Calculator')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nutrient-calculator.css') }}">
<link rel="stylesheet" href="{{ asset('css/nutrient-calculator-fixes.css') }}">

<style>
.history-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e8f5e9 100%);
    padding: 2rem 0 4rem 0;
}

.history-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.history-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-left: 5px solid #10b981;
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.history-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.history-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.history-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
}

.history-date i {
    color: #3b82f6;
}

.history-stage {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
}

.npk-values {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin: 1.25rem 0;
    width: 100%;
}

.npk-item {
    text-align: center;
    padding: 0.75rem 0.5rem;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    min-width: 0;
    overflow: hidden;
}

.npk-label {
    display: block;
    font-size: 0.7rem;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 0.35rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.npk-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 800;
    color: #1f2937;
    word-break: break-word;
}

.npk-value.nitrogen {
    color: #10b981;
}

.npk-value.phosphorus {
    color: #f59e0b;
}

.npk-value.potassium {
    color: #3b82f6;
}

.history-status {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.9rem;
}

.status-success {
    background: #d1fae5;
    color: #065f46;
}

.status-warning {
    background: #fef3c7;
    color: #92400e;
}

.status-danger {
    background: #fee2e2;
    color: #991b1b;
}

.history-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.75rem;
    width: 100%;
}

.btn-view, .btn-delete {
    flex: 1;
    padding: 0.65rem 0.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    white-space: nowrap;
    min-width: 0;
}

.btn-view {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
}

.btn-delete:hover {
    background: #fecaca;
    transform: translateY(-2px);
}

.unified-header {
    background: white;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.page-subtitle {
    color: #6b7280;
    margin: 0 0 1rem 0;
    font-size: 0.9375rem;
}

.header-stats {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.stat-badge {
    background: #f9fafb;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #4b5563;
    border: 1px solid #e5e7eb;
}

.stat-badge i {
    color: #10b981;
    font-size: 0.9em;
}

.action-status {
    margin-left: auto;
}

.btn-history {
    background: #f3f4f6;
    color: #374151;
    border: none;
    padding: 0.4rem 0.9rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-history:hover {
    background: #e5e7eb;
    color: #1f2937;
}

.header-visual {
    margin-left: 1.5rem;
}

.header-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #10b981;
    font-size: 1.5rem;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.stat-label {
    font-size: 0.8125rem;
    color: #6b7280;
    margin-top: 0.15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #9ca3af;
    margin-bottom: 1.5rem;
}

.pagination {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .history-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .npk-values {
        gap: 0.5rem;
    }
    
    .npk-item {
        padding: 0.5rem 0.25rem;
    }
    
    .npk-value {
        font-size: 1rem;
    }
    
    .btn-view, .btn-delete {
        font-size: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
}

@media (max-width: 480px) {
    .history-card {
        padding: 1rem;
    }
    
    .npk-label {
        font-size: 0.65rem;
    }
    
    .npk-value {
        font-size: 0.9rem;
    }
}
</style>

@section('content')
<div class="history-page">
    <div class="container main-container">
        <!-- Unified Header -->
        <div class="unified-header">
            <div class="header-main">
                <div class="header-left">
                    <h1 class="page-title">
                        <i class="fas fa-history"></i>
                        Recent Analyses History
                    </h1>
                    <p class="page-subtitle">View all your past soil nutrient analyses and track progress over time</p>
                    <div class="header-stats">
                        <div class="stat-badge">
                            <i class="fas fa-chart-line"></i>
                            <span>{{ $stats['total_analyses'] }} Total</span>
                        </div>
                        <div class="stat-badge">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ $stats['balanced_count'] }} Balanced</span>
                        </div>
                        <div class="action-status">
                            <a href="{{ route('nutrient-calculator.index') }}" class="btn btn-history">
                                <i class="fas fa-arrow-left me-2"></i>Back to Calculator
                            </a>
                        </div>
                    </div>
                </div>
                <div class="header-visual">
                    <div class="header-circle">
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card animate-fade-in">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_analyses'] }}</div>
                    <div class="stat-label">Total Analyses</div>
                </div>
            </div>
            
            <div class="stat-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['critical_issues'] }}</div>
                    <div class="stat-label">Critical Issues</div>
                </div>
            </div>
            
            <div class="stat-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['recent_analyses'] }}</div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>

        <!-- Analysis History Grid -->
        @if($analyses->count() > 0)
        <div class="history-grid">
            @foreach($analyses as $analysis)
            <div class="history-card">
                <div class="history-header">
                    <div class="history-date">
                        <i class="fas fa-calendar"></i>
                        {{ $analysis->analysis_date->format('M d, Y') }}
                    </div>
                    <div class="history-stage">
                        {{ $analysis->getGrowthStageName() }}
                    </div>
                </div>
                
                <div class="npk-values">
                    <div class="npk-item">
                        <span class="npk-label">Nitrogen</span>
                        <span class="npk-value nitrogen">{{ $analysis->nitrogen }}</span>
                        <small style="font-size: 0.7rem; color: #6b7280;">ppm</small>
                    </div>
                    <div class="npk-item">
                        <span class="npk-label">Phosphorus</span>
                        <span class="npk-value phosphorus">{{ $analysis->phosphorus }}</span>
                        <small style="font-size: 0.7rem; color: #6b7280;">ppm</small>
                    </div>
                    <div class="npk-item">
                        <span class="npk-label">Potassium</span>
                        <span class="npk-value potassium">{{ $analysis->potassium }}</span>
                        <small style="font-size: 0.7rem; color: #6b7280;">ppm</small>
                    </div>
                </div>
                
                <div class="history-status">
                    <span class="status-badge status-{{ $analysis->getNPKStatusColor() }}">
                        @if($analysis->getNPKStatusColor() === 'success')
                            🟢 Balanced
                        @elseif($analysis->getNPKStatusColor() === 'warning')
                            🟡 Moderate
                        @else
                            🔴 Critical
                        @endif
                    </span>
                </div>
                
                <div class="history-actions">
                    <button class="btn-view" onclick="viewAnalysisDetails({{ $analysis->id }})">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                    <button class="btn-delete" onclick="deleteAnalysis({{ $analysis->id }})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $analyses->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-flask"></i>
            <h3>No Analyses Yet</h3>
            <p>Start analyzing your soil to see your history here.</p>
            <a href="{{ route('nutrient-calculator.index') }}" class="btn btn-analyze" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i>
                <span>Create New Analysis</span>
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Simple click handler for view buttons - let the link handle navigation
    document.querySelectorAll('.view-analysis-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Default link behavior will handle the navigation
        });
    });
});
                    <div class="detail-label">Soil pH</div>
                    <div class="detail-value" style="font-size: 1.1rem; color: #f59e0b;">${analysis.soil_ph}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Moisture</div>
                    <div class="detail-value" style="font-size: 1.1rem; color: #06b6d4;">${analysis.soil_moisture}%</div>
                </div>
            </div>
        </div>

        <!-- NPK Values -->
        <div class="detail-section" style="border-left-color: #3b82f6;">
            <h5><i class="fas fa-flask"></i> Soil Nutrients (NPK)</h5>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Nitrogen (N)</div>
                    <div class="detail-value nitrogen">${analysis.nitrogen} <span style="font-size: 0.9rem; color: #6b7280;">ppm</span></div>
                    <small style="color: #6b7280;">Optimal: 50-150 ppm</small>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phosphorus (P)</div>
                    <div class="detail-value phosphorus">${analysis.phosphorus} <span style="font-size: 0.9rem; color: #6b7280;">ppm</span></div>
                    <small style="color: #6b7280;">Optimal: 30-80 ppm</small>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Potassium (K)</div>
                    <div class="detail-value potassium">${analysis.potassium} <span style="font-size: 0.9rem; color: #6b7280;">ppm</span></div>
                    <small style="color: #6b7280;">Optimal: 150-300 ppm</small>
                </div>
            </div>
        </div>

        <!-- Nutrient Status -->
        <div class="detail-section" style="border-left-color: #10b981;">
            <h5><i class="fas fa-chart-pie"></i> Nutrient Status</h5>
            <div class="status-box ${npkBalance}">
                ${npkBalance === 'balanced' ? '🟢 Balanced - ' : npkBalance === 'moderate' ? '🟡 Moderate Issues - ' : '🔴 Critical Issues - '}
                ${escapeHtml(analysis.nutrient_status || 'No status available')}
            </div>
            ${analysis.deficiency_detection ? `
                <div style="margin-top: 1rem; padding: 1rem; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <strong style="color: #92400e;">⚠️ Deficiency Detection:</strong>
                    <p style="margin: 0.5rem 0 0 0; color: #78350f;">${escapeHtml(analysis.deficiency_detection)}</p>
                </div>
            ` : ''}
        </div>

        <!-- AI Recommendations -->
        <div class="detail-section" style="border-left-color: #f59e0b;">
            <h5><i class="fas fa-lightbulb"></i> 💡 AI-Based Fertilizer Recommendations</h5>
            <div class="recommendation-box">
                ${formatRecommendations(analysis.ai_recommendations, detailed.recommendations)}
            </div>
        </div>

        <!-- Stage Advisory -->
        ${analysis.stage_advisory ? `
            <div class="detail-section" style="border-left-color: #8b5cf6;">
                <h5><i class="fas fa-seedling"></i> Stage-Based Advisory</h5>
                <div style="padding: 1rem; background: white; border-radius: 8px; line-height: 1.7;">
                    ${escapeHtml(analysis.stage_advisory)}
                </div>
            </div>
        ` : ''}

        <!-- Detailed Analysis -->
        ${detailed.ph_status || detailed.moisture_status || (detailed.priority_actions && detailed.priority_actions.length > 0) ? `
            <div class="detail-section" style="border-left-color: #06b6d4;">
                <h5><i class="fas fa-info-circle"></i> Additional Information</h5>
                ${detailed.ph_status ? `
                    <div style="padding: 1rem; background: white; border-radius: 8px; margin-bottom: 1rem;">
                        <strong style="color: #3b82f6;">💧 pH Status:</strong>
                        <p style="margin: 0.5rem 0 0 0;">${escapeHtml(detailed.ph_status)}</p>
                    </div>
                ` : ''}
                ${detailed.moisture_status ? `
                    <div style="padding: 1rem; background: white; border-radius: 8px; margin-bottom: 1rem;">
                        <strong style="color: #06b6d4;">💦 Moisture Status:</strong>
                        <p style="margin: 0.5rem 0 0 0;">${escapeHtml(detailed.moisture_status)}</p>
                    </div>
                ` : ''}
                ${detailed.priority_actions && detailed.priority_actions.length > 0 ? `
                    <div style="padding: 1rem; background: white; border-radius: 8px;">
                        <strong style="color: #10b981;">🎯 Priority Actions:</strong>
                        <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                            ${detailed.priority_actions.map(action => `<li>${escapeHtml(action)}</li>`).join('')}
                        </ul>
                    </div>
                ` : ''}
            </div>
        ` : ''}
    `;
    
    container.innerHTML = html;
}

function formatRecommendations(text, recommendations) {
    if (recommendations && recommendations.length > 0) {
        return `<ul>${recommendations.map(rec => `<li>${escapeHtml(rec)}</li>`).join('')}</ul>`;
    }
    
    if (text) {
        const lines = text.split('\n').filter(line => line.trim());
        if (lines.length > 1) {
            return `<ul>${lines.map(line => `<li>${escapeHtml(line.replace(/^[•\-]\s*/, ''))}</li>`).join('')}</ul>`;
        }
        return `<p style="margin: 0; line-height: 1.7;">${escapeHtml(text)}</p>`;
    }
    
    return '<p style="margin: 0; color: #6b7280;">No recommendations available</p>';
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function deleteAnalysis(id) {
    if (!confirm('Are you sure you want to delete this analysis?')) {
        return;
    }
    
    fetch(`/nutrient-calculator/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to delete analysis');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the analysis');
    });
}
</script>
@endpush
@endsection
