<div class="modal-header">
    <h5 class="modal-title"><i class="fas fa-user me-2"></i>View User</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
<div class="modal-body">
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" id="viewName" value="{{ $user->name }}">
                <label for="viewName">Full name</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="email" readonly class="form-control" id="viewEmail" value="{{ $user->email }}">
                <label for="viewEmail">Email</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" id="viewPhone" value="{{ $user->phone ?? 'N/A' }}">
                <label for="viewPhone">Phone</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" id="viewRole" value="{{ strtoupper($user->role) }}">
                <label for="viewRole">Role</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" id="viewVerified" value="{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}">
                <label for="viewVerified">Email Status</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                @if($user->isPasswordHashed())
                    <input type="text" readonly class="form-control" id="viewPassword" value="{{ $user->getDisplayPassword() }}" style="font-family: monospace; color: #dc3545;" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-action="convert" title="Click to convert hashed password">
                @else
                    <input type="text" readonly class="form-control" id="viewPassword" value="{{ str_repeat('•', strlen($user->password)) }}" style="font-family: monospace;" data-password="{{ $user->password }}" data-action="toggle" title="Click to reveal password">
                @endif
                <label for="viewPassword">Password</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" id="viewCreated" value="{{ $user->created_at?->format('M d, Y H:i') }}">
                <label for="viewCreated">Created</label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('viewPassword');
    if (passwordInput) {
        passwordInput.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            
            if (action === 'convert') {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                showConvertPasswordModal(userId, userName);
            } else if (action === 'toggle') {
                const password = this.getAttribute('data-password');
                toggleModalPassword(this, password);
            }
        });
    }
});
</script>


