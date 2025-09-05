@extends('layouts.admin')

@section('title', 'Manage Users - MeloTech')

@section('page-title', 'User Management')

@section('content')
<!-- Users Management (Professional Redesign) -->
<div class="section">
    <!-- Header + Quick Stats -->
    <div class="section-header animate-fade-in-up">
        <div class="section-title">
            <i class="fas fa-users section-icon"></i>
            User Management
        </div>
        <p class="section-subtitle mb-0">Manage platform users, roles, and access.</p>
    </div>

    @if(isset($stats))
    <div class="users-stats-grid mb-3">
        <div class="stat-card animate-fade-in-up" style="animation-delay: .0s;">
            <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
            <div class="stat-content">
                <div class="stat-number stats-number">{{ $stats['total_users'] ?? 0 }}</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-description">All registered accounts</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up" style="animation-delay: .05s;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"><i class="fas fa-user-shield"></i></div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['admin_count'] ?? 0 }}</div>
                <div class="stat-label">Admins</div>
                <div class="stat-description">Administrative users</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up" style="animation-delay: .1s;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><i class="fas fa-user"></i></div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['user_count'] ?? 0 }}</div>
                <div class="stat-label">Standard Users</div>
                <div class="stat-description">Users with basic access</div>
            </div>
        </div>
        <div class="stat-card animate-fade-in-up" style="animation-delay: .15s;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><i class="fas fa-user-plus"></i></div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['recent_registrations'] ?? 0 }}</div>
                <div class="stat-label">Last 30 Days</div>
                <div class="stat-description">New registrations</div>
            </div>
        </div>
    </div>
    @endif

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

    <!-- Actions: search, filter, create -->
    <div class="action-bar animate-fade-in-up" style="animation-delay: .2s;">
        <div class="action-bar-left d-flex align-items-center gap-2">
            <div class="input-group" style="max-width: 420px;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 admin-search" id="userSearch" placeholder="Search users by name or email" aria-label="Search users">
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle admin-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item" data-filter-role="all"><i class="fas fa-circle text-muted me-2"></i>All</button></li>
                    <li><button class="dropdown-item" data-filter-role="admin"><i class="fas fa-shield-halved text-primary me-2"></i>Admins</button></li>
                    <li><button class="dropdown-item" data-filter-role="user"><i class="fas fa-user text-success me-2"></i>Users</button></li>
                </ul>
            </div>
            <form method="GET" class="d-flex align-items-center ms-2" action="{{ route('admin.users.index') }}">
                <input type="hidden" name="q" value="{{ request('q') }}">
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
                    <li><a class="dropdown-item" href="{{ route('admin.users.print') }}" target="_blank">
                        <i class="fas fa-print me-2"></i>Print
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportUsersAsExcel()">
                        <i class="fas fa-file-excel me-2"></i>Excel
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportUsersAsPDF()">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </a></li>
                </ul>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary admin-btn">
                <i class="fas fa-user-plus me-2"></i>
                Add User
            </a>
        </div>
    </div>

    <!-- Users Grid (Rebuilt) -->
    <div class="users-grid-card animate-fade-in-up" style="animation-delay:.25s;">
        <div class="users-grid-header">
            <div class="users-grid-header-left">Full name</div>
            <div class="users-grid-header-mid">
                <div>Email</div>
                <div>Farm</div>
                <div>Phone</div>
            </div>
            <div class="users-grid-header-right d-flex align-items-center justify-content-between">
                <div>Status / Role</div>
                <div class="text-muted small" style="min-width: 60px; text-align:right;">Actions</div>
            </div>
        </div>
        @forelse($users as $user)
            <div class="user-card" 
                 data-name="{{ strtolower($user->name) }}" 
                 data-email="{{ strtolower($user->email) }}" 
                 data-phone="{{ strtolower($user->phone ?? '') }}" 
                 data-role="{{ strtolower($user->role) }}" 
                 data-verified="{{ $user->email_verified_at ? 1 : 0 }}"
                 data-created="{{ $user->created_at->format('M d, Y') }}">
                <div class="user-card-left">
                    <div class="user-avatar-small d-flex align-items-center justify-content-center">
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="user-ident">
                        <div class="user-name">{{ $user->name }}</div>
                    </div>
                </div>
                <div class="user-card-mid">
                    <div class="user-field">
                        <div class="field-label">Email</div>
                        <div class="cell-with-action">
                            <span class="email-value text-truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                        </div>
                    </div>
                    <div class="user-field">
                        <div class="field-label">Farm</div>
                        <div class="cell-with-action">
                            @php $firstFarm = $user->farms->first(); @endphp
                            @if($firstFarm)
                                <span class="farm-value text-truncate" title="{{ $firstFarm->farm_name }}">{{ $firstFarm->farm_name }}</span>
                                @if($user->farms->count() > 1)
                                    <span class="text-muted small ms-1">+{{ $user->farms->count() - 1 }} more</span>
                                @endif
                            @else
                                <span class="farm-missing">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="user-field">
                        <div class="field-label">Phone</div>
                        <div class="cell-with-action">
                            @if($user->phone)
                                <span class="phone-value text-truncate" title="{{ $user->phone }}">{{ $user->phone }}</span>
                            @else
                                <span class="phone-missing">N/A</span>
                            @endif
                        </div>
                    </div>
                    
                </div>
                <div class="user-card-right d-flex align-items-center justify-content-between">
                    <div class="badges">
                        @php $role = strtolower($user->role); @endphp
                        <span class="badge {{ $role === 'admin' ? 'bg-primary' : 'bg-success' }}">
                            <i class="fas {{ $role === 'admin' ? 'fa-shield-halved' : 'fa-user' }} me-1"></i>{{ strtoupper($user->role) }}
                        </span>
                    </div>
                    <div class="dropdown ms-2">
                        <button class="btn btn-light btn-sm action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions" data-user-id="{{ $user->id }}">
                            <i class="fas fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('admin.users.show', $user) }}" class="dropdown-item action-view" data-user-id="{{ $user->id }}" data-action="view">
                                    <i class="fas fa-eye me-2"></i>View
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.edit', $user) }}" class="dropdown-item action-edit" data-user-id="{{ $user->id }}" data-action="edit">
                                    <i class="fas fa-pen-to-square me-2"></i>Edit
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="m-0 p-0 d-inline delete-user-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger action-delete" data-user-id="{{ $user->id }}">
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
                <i class="fas fa-users"></i>
                <h4>No users found</h4>
                <p>Get started by adding your first user.</p>
                <div class="mt-3">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary admin-btn"><i class="fas fa-user-plus me-2"></i>Add first user</a>
                </div>
            </div>
        @endforelse

        <div class="pagination-container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="text-muted small">
                    Showing <strong>{{ $users->firstItem() ?? 0 }}</strong>–<strong>{{ $users->lastItem() ?? 0 }}</strong> of <strong>{{ $users->total() }}</strong> records
                </div>
                <div class="d-flex justify-content-center flex-grow-1">
                    {{ $users->appends(['per_page' => $perPage ?? request('per_page', 15)])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

 

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userSearch');
    const userRows = Array.from(document.querySelectorAll('.user-card'));

    function filterList() {
        const term = (searchInput?.value || '').toLowerCase();
        const roleFilter = window.currentRoleFilter || 'all';
        userRows.forEach((row, index) => {
            const name = row.getAttribute('data-name') || '';
            const email = row.getAttribute('data-email') || '';
            const role = row.getAttribute('data-role') || '';
            const phone = row.getAttribute('data-phone') || '';
            const roleMatches = roleFilter === 'all' || role === roleFilter;
            const textMatches = name.includes(term) || email.includes(term) || phone.includes(term);
            const isVisible = roleMatches && textMatches;
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                row.style.animationDelay = `${index * 0.03}s`;
                row.classList.add('fade-in');
            } else {
                row.classList.remove('fade-in');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterList);
    }

    // Role dropdown filter
    document.querySelectorAll('[data-filter-role]').forEach(btn => {
        btn.addEventListener('click', function() {
            window.currentRoleFilter = this.getAttribute('data-filter-role');
            filterList();
        });
    });
    // Modal helpers
    const modalContainer = document.createElement('div');
    modalContainer.innerHTML = `
        <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content shadow-lg border-0 rounded-3"></div>
            </div>
        </div>`;
    document.body.appendChild(modalContainer);

    const userModalEl = document.getElementById('userModal');
    const userModal = new bootstrap.Modal(userModalEl);

    function loadIntoModal(url) {
        fetch(url + (url.includes('?') ? '&' : '?') + 'modal=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.text())
            .then(html => {
                userModalEl.querySelector('.modal-content').innerHTML = html;
                userModal.show();

                // After content loads, if it's the edit form, wire up AJAX submit
                const editForm = userModalEl.querySelector('#editUserForm');
                if (editForm) {
                    editForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = e.currentTarget;
                        const action = form.getAttribute('action');
                        const formData = new FormData(form);
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
                        .then(({ user }) => {
                            if (!user) { userModal.hide(); return; }
                            const card = document.querySelector(`.user-card[data-email="${CSS.escape((user.email || '').toLowerCase())}"]`) ||
                                         document.querySelector(`.user-card .user-name:contains('${user.name}')`);
                            // Fallback find by ID in the displayed label
                            const allCards = Array.from(document.querySelectorAll('.user-card'));
                            const found = card || allCards.find(c => c.querySelector('.user-ident .text-muted')?.textContent?.includes(`ID: ${user.id}`));
                            if (found) {
                                found.setAttribute('data-name', (user.name || '').toLowerCase());
                                found.setAttribute('data-email', (user.email || '').toLowerCase());
                                found.setAttribute('data-phone', (user.phone || '').toLowerCase());
                                found.setAttribute('data-role', (user.role || '').toLowerCase());
                                // Update visible fields
                                const nameEl = found.querySelector('.user-name');
                                if (nameEl) nameEl.textContent = user.name || '';
                                const emailEl = found.querySelector('.email-value');
                                if (emailEl) { emailEl.textContent = user.email || ''; emailEl.title = user.email || ''; }
                                const phoneEl = found.querySelector('.phone-value');
                                const phoneMissing = found.querySelector('.phone-missing');
                                if (user.phone) {
                                    if (phoneMissing) phoneMissing.outerHTML = `<span class="phone-value text-truncate" title="${user.phone}">${user.phone}</span>`;
                                    if (phoneEl) { phoneEl.textContent = user.phone; phoneEl.title = user.phone; }
                                } else {
                                    if (phoneEl) phoneEl.outerHTML = '<span class="phone-missing">N/A</span>';
                                }
                                // Update badges
                                const badges = found.querySelector('.badges');
                                if (badges) {
                                    const roleLower = (user.role || '').toLowerCase();
                                    const roleBadge = `<span class="badge ${roleLower === 'admin' ? 'bg-primary' : 'bg-success'}"><i class="fas ${roleLower === 'admin' ? 'fa-shield-halved' : 'fa-user'} me-1"></i>${(user.role || '').toUpperCase()}</span>`;
                                    // Keep verified badge as-is; only replace role badge
                                    const existing = Array.from(badges.querySelectorAll('.badge'));
                                    if (existing.length) {
                                        const last = existing[existing.length - 1];
                                        last.outerHTML = roleBadge;
                                    } else {
                                        badges.insertAdjacentHTML('beforeend', roleBadge);
                                    }
                                }
                            }
                            userModal.hide();
                        })
                        .catch(async (err) => {
                            // Try to surface validation errors inline if HTML returned
                            if (err && err.errors) {
                                alert(Object.values(err.errors).flat().join('\n'));
                            } else {
                                alert('Failed to save changes.');
                            }
                        });
                    });
                }
            })
            .catch(() => {
                alert('Failed to load content.');
            });
    }

    // Open View/Edit in modal with event delegation (more reliable)
    document.addEventListener('click', function(e) {
        const viewLink = e.target.closest('.action-view');
        const editLink = e.target.closest('.action-edit');
        if (viewLink) {
            e.preventDefault();
            e.stopPropagation();
            loadIntoModal(viewLink.getAttribute('href'));
        }
        if (editLink) {
            e.preventDefault();
            e.stopPropagation();
            loadIntoModal(editLink.getAttribute('href'));
        }
    });

    // Delete confirm modal markup (Bootstrap)
    const deleteModalContainer = document.createElement('div');
    deleteModalContainer.innerHTML = `
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0" style="border-radius:12px;">
                    <div class="modal-header" style="border:0;">
                        <h5 class="modal-title"><i class="fas fa-triangle-exclamation text-danger me-2"></i>Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <p class="mb-1">You are about to delete <strong id="deleteUserName">this user</strong>.</p>
                        <p class="text-muted mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer" style="border:0;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                            <i class="fas fa-trash me-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.appendChild(deleteModalContainer);

    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = new bootstrap.Modal(deleteModalEl);
    const deleteUserNameEl = document.getElementById('deleteUserName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    let pendingDelete = null;

    // Hook delete forms to show modal, then run AJAX on confirm
    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const card = this.closest('.user-card');
            const name = card?.querySelector('.user-name')?.textContent?.trim() || 'this user';
            deleteUserNameEl.textContent = name;

            const button = this.querySelector('button[type="submit"]');
            const originalHtml = button.innerHTML;
            const action = this.getAttribute('action');
            const formData = new FormData(this);

            pendingDelete = { card, button, originalHtml, action, formData };
            deleteModal.show();
        });
    });

    // Confirm deletion handler
    confirmDeleteBtn.addEventListener('click', function() {
        if (!pendingDelete) return;
        const { card, button, originalHtml, action, formData } = pendingDelete;

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
            deleteModal.hide();
            if (card) {
                card.style.transition = 'opacity .25s ease, transform .25s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-6px)';
                setTimeout(() => { card.remove(); }, 260);
            }

            const alert = document.createElement('div');
            alert.className = 'alert alert-success d-flex align-items-center mt-2';
            alert.innerHTML = `<i class="fas fa-check-circle me-2"></i><div>${data.message || 'User deleted successfully.'}</div>`;
            const container = document.querySelector('.admin-content');
            container?.insertBefore(alert, container.firstChild);

            if (button) {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
            pendingDelete = null;
        })
        .catch((err) => {
            deleteModal.hide();
            const msg = (err && (err.message || (err.errors && Object.values(err.errors).flat().join(' ')))) || 'Failed to delete user.';
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger d-flex align-items-center mt-2';
            alert.innerHTML = `<i class=\"fas fa-triangle-exclamation me-2\"></i><div>${msg}</div>`;
            const container = document.querySelector('.admin-content');
            container?.insertBefore(alert, container.firstChild);

            if (button) {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
            pendingDelete = null;
        });
    });
    
});

// Export functions for User Management table
function exportUsersAsExcel() {
    const userCards = document.querySelectorAll('.user-card');
    if (userCards.length === 0) {
        alert('No user data to export');
        return;
    }
    
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    const headers = ['Name', 'Email', 'Phone', 'Role', 'Farm Name', 'Registered Date'];
    csvContent += headers.join(',') + '\r\n';
    
    // Add data rows
    userCards.forEach(card => {
        const name = card.querySelector('.user-name')?.textContent?.trim() || '';
        const email = card.querySelector('.email-value')?.textContent?.trim() || '';
        const phone = card.querySelector('.phone-value')?.textContent?.trim() || 'N/A';
        const role = card.querySelector('.badge')?.textContent?.trim() || '';
        const farmName = card.querySelector('.farm-value')?.textContent?.trim() || 'N/A';
        const registered = card.getAttribute('data-created') || '';
        
        const rowData = [
            `"${name.replace(/"/g, '""')}"`,
            `"${email.replace(/"/g, '""')}"`,
            `"${phone.replace(/"/g, '""')}"`,
            `"${role.replace(/"/g, '""')}"`,
            `"${farmName.replace(/"/g, '""')}"`,
            `"${registered.replace(/"/g, '""')}"`
        ];
        csvContent += rowData.join(',') + '\r\n';
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'users_management.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportUsersAsPDF() {
    const exportBtn = event.target.closest('.dropdown-item');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
    
    try {
        // Use server-side PDF generation
        const pdfUrl = '{{ route("admin.users.export-pdf") }}';
        
        // Create a temporary link to trigger the download
        const link = document.createElement('a');
        link.href = pdfUrl;
        link.download = `users_management_${new Date().toISOString().split('T')[0]}.pdf`;
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

</script>
@endsection
