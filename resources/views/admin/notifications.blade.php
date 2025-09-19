@extends('layouts.admin')

@section('title', 'Notifications - MeloTech Admin')

@section('page-title', 'Notifications')

@section('content')
<!-- Simple Notifications Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="card-title mb-1">
                            <i class="fas fa-bell text-primary me-2"></i>
                            Notifications
                        </h2>
                        <p class="text-muted mb-0">Recent system alerts and updates</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" id="markAllRead">
                            <i class="fas fa-check-double me-1"></i>
                            Mark All Read
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Notifications List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="notification-item-simple {{ !$notification->read ? 'unread' : '' }}">
                            <div class="d-flex align-items-start p-3 border-bottom">
                                <div class="notification-icon-simple me-3">
                                    @if($notification->type === 'system')
                                        <i class="fas fa-cog text-info"></i>
                                    @elseif($notification->type === 'user')
                                        <i class="fas fa-user text-success"></i>
                                    @elseif($notification->type === 'farm')
                                        <i class="fas fa-seedling text-warning"></i>
                                    @else
                                        <i class="fas fa-bell text-primary"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                    @if($notification->action_url)
                                        <a href="{{ $notification->action_url }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    @endif
                                </div>
                                <div class="ms-2">
                                    @if(!$notification->read)
                                        <span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No Notifications</h5>
                        <p class="text-muted">You're all caught up!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark all as read functionality
    const markAllReadButton = document.getElementById('markAllRead');
    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', function() {
            // Disable button to prevent multiple clicks
            markAllReadButton.disabled = true;
            markAllReadButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
            
            // Make AJAX request to backend
            fetch('{{ route("admin.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    const unreadItems = document.querySelectorAll('.notification-item-simple.unread');
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                        const badge = item.querySelector('.badge');
                        if (badge) {
                            badge.remove();
                        }
                    });
                    
                    // Update notification icon in header if it exists
                    updateNotificationIcon(0);
                    
                    // Do not show a success alert
                } else {
                    throw new Error(data.message || 'Failed to mark notifications as read');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                    <i class=\"fas fa-exclamation-circle me-2\"></i>
                    Error: ${error.message}
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
                `;
                document.querySelector('.admin-content').insertBefore(alert, document.querySelector('.admin-content').firstChild);
            })
            .finally(() => {
                // Re-enable button
                markAllReadButton.disabled = false;
                markAllReadButton.innerHTML = '<i class="fas fa-check-double me-1"></i>Mark All Read';
            });
        });
    }
    
    // Function to update notification icon in header
    function updateNotificationIcon(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }
});
</script>
@endpush
