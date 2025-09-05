@extends('layouts.admin')

@section('title', 'Manage Farms - MeloTech')

@section('page-title', 'Manage Farms')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
@endpush
<!-- Header + Quick Stats (match Users page) -->
<div class="section">
    <div class="section-header animate-fade-in-up">
        <div class="section-title">
            <i class="fas fa-tractor section-icon"></i>
            Farms Management
        </div>
        <p class="section-subtitle mb-0">Manage farms, locations, and crop details.</p>
    </div>

    <div class="users-stats-grid mb-3">
        <div class="stat-card animate-fade-in-up" style="animation-delay: .0s;">
            <div class="stat-icon"><i class="fas fa-tractor"></i></div>
            <div class="stat-content">
                <div class="stat-number stats-number">{{ $stats['total_farms'] ?? ($farms->total() ?? 0) }}</div>
                <div class="stat-label">Total Farms</div>
                <div class="stat-description">Registered watermelon farms</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up" style="animation-delay: .05s;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['unique_owners'] ?? 0 }}</div>
                <div class="stat-label">Farm Owners</div>
                <div class="stat-description">Unique owners</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up" style="animation-delay: .1s;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><i class="fas fa-seedling"></i></div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['active_farms'] ?? 0 }}</div>
                <div class="stat-label">Active Farms</div>
                <div class="stat-description">With planting dates</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up" style="animation-delay: .15s;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"><i class="fas fa-plus"></i></div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['recent_additions'] ?? 0 }}</div>
                <div class="stat-label">Last 30 Days</div>
                <div class="stat-description">Farms added</div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center mt-2" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center mt-2" role="alert">
        <i class="fas fa-triangle-exclamation me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif

<!-- Actions: search, filter, export -->
<div class="action-bar animate-fade-in-up" style="animation-delay: .2s;">
    <div class="action-bar-left d-flex align-items-center gap-2">
        <div class="input-group" style="max-width: 420px;">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0 admin-search" id="farmSearch" placeholder="Search farms by name, variety, location, or owner" aria-label="Search farms" value="{{ $search ?? '' }}">
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle admin-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
            <ul class="dropdown-menu">
                <li><button class="dropdown-item" data-filter-status="all"><i class="fas fa-circle text-muted me-2"></i>All Farms</button></li>
                <li><button class="dropdown-item" data-filter-status="active"><i class="fas fa-seedling text-success me-2"></i>Active Farms</button></li>
                <li><button class="dropdown-item" data-filter-status="inactive"><i class="fas fa-pause-circle text-warning me-2"></i>Inactive Farms</button></li>
                <li><button class="dropdown-item" data-filter-status="recent"><i class="fas fa-clock text-info me-2"></i>Recent (30 days)</button></li>
            </ul>
        </div>
        <form method="GET" class="d-flex align-items-center ms-2" action="{{ route('admin.farms.index') }}">
            <input type="hidden" name="q" value="{{ $search ?? '' }}">
            <input type="hidden" name="filter" value="{{ $filter ?? '' }}">
            <label class="me-2 text-muted small">Per page</label>
            <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach([10,15,25,50,100] as $size)
                    <option value="{{ $size }}" {{ ($perPage ?? request('per_page', 15)) == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="action-bar-right d-flex align-items-center gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.farms.print') }}?{{ http_build_query(request()->query()) }}" target="_blank">
                    <i class="fas fa-print me-2"></i>Print
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportFarmsAsExcel()">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </a></li>
                <li><a class="dropdown-item" href="{{ route('admin.farms.export-pdf') }}?{{ http_build_query(request()->query()) }}">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Farms List (Users-like UI) -->
<div class="users-grid-card animate-fade-in-up" style="animation-delay:.25s;">
    <div class="users-grid-header">
        <div class="users-grid-header-left">Farm</div>
        <div class="users-grid-header-mid">
            <div>Owner</div>
            <div>Location</div>
        </div>
        <div class="users-grid-header-right d-flex align-items-center justify-content-between">
            <div>Variety / Size</div>
            <div class="text-muted small" style="min-width: 60px; text-align:right;">Actions</div>
        </div>
    </div>
    @forelse($farms as $farm)
        <div class="user-card farm-card" 
            data-id="{{ $farm->id }}"
            data-name="{{ strtolower($farm->farm_name) }}"
            data-owner="{{ strtolower($farm->user->name) }}"
            data-location="{{ strtolower($farm->city_municipality_name . ', ' . $farm->province_name) }}"
            data-variety="{{ $farm->watermelon_variety ?: 'N/A' }}"
            data-field-size="{{ $farm->field_size ? $farm->field_size . ' ' . $farm->field_size_unit : 'N/A' }}"
            data-planting-date="{{ $farm->planting_date ? $farm->planting_date->format('M d, Y') : 'N/A' }}"
            data-created-date="{{ $farm->created_at->format('M d, Y') }}"
            data-status="{{ $farm->planting_date ? 'active' : 'inactive' }}"
            data-created="{{ $farm->created_at->format('Y-m-d') }}">
            <div class="user-card-left">
                <div class="user-avatar-small d-flex align-items-center justify-content-center">
                    <span><i class="fas fa-seedling"></i></span>
                </div>
                <div class="user-ident">
                    <div class="user-name">{{ $farm->farm_name }}</div>
                    <div class="text-muted small">ID: {{ $farm->id }}</div>
                </div>
            </div>
            <div class="user-card-mid">
                <div class="user-field">
                    <div class="field-label">Owner</div>
                    <div class="cell-with-action">
                        <span class="text-truncate" title="{{ $farm->user->email }}">{{ $farm->user->name }}</span>
                    </div>
                </div>
                <div class="user-field">
                    <div class="field-label">Location</div>
                    <div class="cell-with-action">
                        <span class="text-truncate" title="{{ $farm->province_name }}">{{ $farm->city_municipality_name }}, {{ $farm->province_name }}</span>
                    </div>
                </div>
            </div>
            <div class="user-card-right d-flex align-items-center justify-content-between">
                <div class="badges">
                    <span class="badge bg-success"><i class="fas fa-seedling me-1"></i>{{ $farm->watermelon_variety ?: 'N/A' }}</span>
                    <span class="badge bg-primary"><i class="fas fa-ruler-combined me-1"></i>{{ $farm->field_size }} {{ $farm->field_size_unit }}</span>
                </div>
                <div class="dropdown ms-2">
                    <button class="btn btn-light btn-sm action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions" data-farm-id="{{ $farm->id }}">
                        <i class="fas fa-ellipsis-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="{{ route('admin.farms.show', $farm) }}" class="dropdown-item action-view-farm" data-farm-id="{{ $farm->id }}">
                                <i class="fas fa-eye me-2"></i>View
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.farms.edit', $farm) }}" class="dropdown-item action-edit-farm" data-farm-id="{{ $farm->id }}">
                                <i class="fas fa-pen-to-square me-2"></i>Edit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('admin.farms.delete', $farm) }}" class="m-0 p-0 d-inline delete-farm-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-tractor"></i>
            <h4>No farms found</h4>
            <p>Start by adding your first farm.</p>
        </div>
    @endforelse

            <div class="pagination-container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="text-muted small">
                    Showing <strong>{{ $farms->firstItem() ?? 0 }}</strong>–<strong>{{ $farms->lastItem() ?? 0 }}</strong> of <strong>{{ $farms->total() }}</strong> records
                </div>
                <div class="d-flex justify-content-center flex-grow-1">
                    {{ $farms->appends(['per_page' => $perPage ?? request('per_page', 15), 'q' => $search ?? '', 'filter' => $filter ?? 'all'])->links() }}
                </div>
            </div>
        </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('farmSearch');
    const farmCards = Array.from(document.querySelectorAll('.farm-card'));

    function filterFarms() {
        const term = (searchInput?.value || '').toLowerCase();
        const statusFilter = window.currentStatusFilter || 'all';
        
        farmCards.forEach((card, index) => {
            const farmName = card.getAttribute('data-name') || '';
            const owner = card.getAttribute('data-owner') || '';
            const location = card.getAttribute('data-location') || '';
            const variety = card.getAttribute('data-variety') || '';
            const status = card.getAttribute('data-status') || '';
            const created = card.getAttribute('data-created') || '';
            
            // Check search term match
            const textMatches = !term.trim() || 
                              farmName.includes(term) || 
                              owner.includes(term) || 
                              location.includes(term) || 
                              variety.includes(term);
            
            // Check status filter match
            let statusMatches = true;
            if (statusFilter === 'active') {
                statusMatches = status === 'active';
            } else if (statusFilter === 'inactive') {
                statusMatches = status === 'inactive';
            } else if (statusFilter === 'recent') {
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                const createdDate = new Date(created);
                statusMatches = createdDate >= thirtyDaysAgo;
            }
            
            const isVisible = textMatches && statusMatches;
            
            if (isVisible) {
                card.style.display = '';
                card.style.animationDelay = `${index * 0.03}s`;
                card.classList.add('fade-in');
            } else {
                card.style.display = 'none';
                card.classList.remove('fade-in');
            }
        });
    }

    // Add search event listener
    if (searchInput) {
        searchInput.addEventListener('input', filterFarms);
        
        // Auto-submit form on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route("admin.farms.index") }}';
                
                const searchInput = document.createElement('input');
                searchInput.type = 'hidden';
                searchInput.name = 'q';
                searchInput.value = this.value;
                form.appendChild(searchInput);
                
                const filterInput = document.createElement('input');
                filterInput.type = 'hidden';
                filterInput.name = 'filter';
                filterInput.value = window.currentStatusFilter || 'all';
                form.appendChild(filterInput);
                
                const perPageInput = document.createElement('input');
                perPageInput.type = 'hidden';
                perPageInput.name = 'per_page';
                perPageInput.value = '{{ $perPage ?? request("per_page", 15) }}';
                form.appendChild(perPageInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Status filter dropdown
    document.querySelectorAll('[data-filter-status]').forEach(btn => {
        btn.addEventListener('click', function() {
            window.currentStatusFilter = this.getAttribute('data-filter-status');
            filterFarms();
            
            // Update button text to show current filter
            const dropdownToggle = this.closest('.dropdown').querySelector('.dropdown-toggle');
            const icon = this.querySelector('i').outerHTML;
            const text = this.textContent.trim();
            dropdownToggle.innerHTML = icon + ' ' + text;
        });
    });

    // Staggered fade-in for farm cards
    farmCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.03}s`;
        card.classList.add('fade-in');
    });

    // Modal helpers (reuse from users page)
    let userModalEl = document.getElementById('userModal');
    if (!userModalEl) {
        const modalContainer = document.createElement('div');
        modalContainer.innerHTML = `
            <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content shadow-lg border-0 rounded-3"></div>
                </div>
            </div>`;
        document.body.appendChild(modalContainer);
        userModalEl = document.getElementById('userModal');
    }
    const userModal = new bootstrap.Modal(userModalEl);

    function loadIntoModal(url, onLoaded) {
        fetch(url + (url.includes('?') ? '&' : '?') + 'modal=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.text())
            .then(html => {
                userModalEl.querySelector('.modal-content').innerHTML = html;
                userModal.show();
                if (typeof onLoaded === 'function') onLoaded();
            })
            .catch(() => alert('Failed to load content.'));
    }

    // Open View/Edit in modal (delegated for reliability)
    document.addEventListener('click', function(e) {
        const viewLink = e.target.closest('.action-view-farm');
        const editLink = e.target.closest('.action-edit-farm');
        if (viewLink) {
            e.preventDefault();
            e.stopPropagation();
            loadIntoModal(viewLink.getAttribute('href'));
        }
        if (editLink) {
            e.preventDefault();
            e.stopPropagation();
            const href = editLink.getAttribute('href');
            loadIntoModal(href, () => {
                const editForm = userModalEl.querySelector('#editFarmForm');
                if (!editForm) return;
                editForm.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    const form = ev.currentTarget;
                    const action = form.getAttribute('action');
                    const formData = new FormData(form);
                    fetch(action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData })
                        .then(async (res) => {
                            const isJson = res.headers.get('content-type')?.includes('application/json');
                            const data = isJson ? await res.json() : { success: res.ok };
                            if (!res.ok || data.success === false) throw data;
                            return data;
                        })
                        .then(({ farm }) => {
                            if (!farm) { userModal.hide(); return; }
                            const card = document.querySelector(`.farm-card[data-id="${farm.id}"]`);
                            if (card) {
                                card.setAttribute('data-name', (farm.farm_name || '').toLowerCase());
                                card.setAttribute('data-owner', (farm.user?.name || '').toLowerCase());
                                card.setAttribute('data-location', ((farm.city_municipality_name || '') + ', ' + (farm.province_name || '')).toLowerCase());
                                const nameEl = card.querySelector('.user-name');
                                if (nameEl) nameEl.textContent = farm.farm_name || '';
                                const badges = card.querySelector('.badges');
                                if (badges) {
                                    badges.innerHTML = `
                                        <span class=\"badge bg-success\"><i class=\"fas fa-seedling me-1\"></i>${farm.watermelon_variety || 'N/A'}</span>
                                        <span class=\"badge bg-primary\"><i class=\"fas fa-ruler-combined me-1\"></i>${farm.field_size ?? ''} ${farm.field_size_unit ?? ''}</span>`;
                                }
                                const locEl = card.querySelector('.user-field:nth-child(2) .text-truncate');
                                if (locEl) locEl.textContent = `${farm.city_municipality_name || ''}, ${farm.province_name || ''}`;
                            }
                            userModal.hide();
                        })
                        .catch((err) => {
                            if (err && err.errors) {
                                alert(Object.values(err.errors).flat().join('\n'));
                            } else {
                                alert('Failed to save changes.');
                            }
                        });
                });
            });
        }
    });

    // Delete confirm modal markup (Bootstrap)
    const deleteFarmModalContainer = document.createElement('div');
    deleteFarmModalContainer.innerHTML = `
        <div class="modal fade" id="deleteFarmConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius:12px;">
                    <div class="modal-header" style="border:0;">
                        <h5 class="modal-title"><i class="fas fa-triangle-exclamation text-danger me-2"></i>Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <p class="mb-1">You are about to delete <strong id="deleteFarmName">this farm</strong>.</p>
                        <p class="text-muted mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer" style="border:0;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteFarmBtn">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.appendChild(deleteFarmModalContainer);

    const deleteFarmModalEl = document.getElementById('deleteFarmConfirmModal');
    const deleteFarmModal = new bootstrap.Modal(deleteFarmModalEl);
    const deleteFarmNameEl = document.getElementById('deleteFarmName');
    const confirmDeleteFarmBtn = document.getElementById('confirmDeleteFarmBtn');

    let pendingFarmDelete = null;

    // Hook farm delete forms to modal-confirmed AJAX
    document.querySelectorAll('.delete-farm-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const card = this.closest('.farm-card');
            const name = card?.querySelector('.user-name')?.textContent?.trim() || 'this farm';
            deleteFarmNameEl.textContent = name;

            const button = this.querySelector('button[type="submit"]');
            const originalHtml = button.innerHTML;
            const action = this.getAttribute('action');
            const formData = new FormData(this);

            pendingFarmDelete = { card, button, originalHtml, action, formData };
            deleteFarmModal.show();
        });
    });

    // Confirm deletion executes AJAX
    confirmDeleteFarmBtn.addEventListener('click', function() {
        if (!pendingFarmDelete) return;
        const { card, button, originalHtml, action, formData } = pendingFarmDelete;

        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
        }

        fetch(action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(async (res) => {
            const isJson = res.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await res.json() : { success: res.ok };
            if (!res.ok || data.success === false) throw data;
            return data;
        })
        .then((data) => {
            deleteFarmModal.hide();
            if (card) {
                card.style.transition = 'opacity .25s ease, transform .25s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-6px)';
                setTimeout(() => { card.remove(); }, 260);
            }

            const alert = document.createElement('div');
            alert.className = 'alert alert-success d-flex align-items-center mt-2';
            alert.innerHTML = `<i class="fas fa-check-circle me-2"></i><div>${data.message || 'Farm deleted successfully.'}</div>`;
            const container = document.querySelector('.admin-content');
            container?.insertBefore(alert, container.firstChild);

            if (button) {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
            pendingFarmDelete = null;
        })
        .catch((err) => {
            deleteFarmModal.hide();
            const msg = (err && (err.message || (err.errors && Object.values(err.errors).flat().join(' ')))) || 'Failed to delete farm.';
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger d-flex align-items-center mt-2';
            alert.innerHTML = `<i class=\"fas fa-triangle-exclamation me-2\"></i><div>${msg}</div>`;
            const container = document.querySelector('.admin-content');
            container?.insertBefore(alert, container.firstChild);

            if (button) {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
            pendingFarmDelete = null;
        });
    });
});

// Export functions for Farms table
function exportFarmsAsExcel() {
    // Only export visible (filtered) farm cards
    const farmCards = Array.from(document.querySelectorAll('.farm-card')).filter(card => 
        card.style.display !== 'none'
    );
    
    if (farmCards.length === 0) {
        alert('No farm data to export');
        return;
    }
    
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    const headers = ['Farm Name', 'Owner', 'Location', 'Variety', 'Field Size', 'Planting Date', 'Created Date'];
    csvContent += headers.join(',') + '\r\n';
    
    // Add data rows
    farmCards.forEach(card => {
        const rowData = [];
        
        // Farm Name
        const farmName = card.getAttribute('data-name') || '';
        rowData.push('"' + farmName.replace(/"/g, '""') + '"');
        
        // Owner
        const owner = card.getAttribute('data-owner') || '';
        rowData.push('"' + owner.replace(/"/g, '""') + '"');
        
        // Location
        const location = card.getAttribute('data-location') || '';
        rowData.push('"' + location.replace(/"/g, '""') + '"');
        
        // Variety
        const variety = card.getAttribute('data-variety') || 'N/A';
        rowData.push('"' + variety.replace(/"/g, '""') + '"');
        
        // Field Size
        const fieldSize = card.getAttribute('data-field-size') || 'N/A';
        rowData.push('"' + fieldSize.replace(/"/g, '""') + '"');
        
        // Planting Date
        const plantingDate = card.getAttribute('data-planting-date') || 'N/A';
        rowData.push('"' + plantingDate.replace(/"/g, '""') + '"');
        
        // Created Date
        const createdDate = card.getAttribute('data-created-date') || 'N/A';
        rowData.push('"' + createdDate.replace(/"/g, '""') + '"');
        
        csvContent += rowData.join(',') + '\r\n';
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'farms_management_' + new Date().toISOString().split('T')[0] + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
