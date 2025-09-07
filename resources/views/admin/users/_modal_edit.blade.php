<form method="POST" action="{{ route('admin.users.update', $user) }}" id="editUserForm">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-pen-to-square me-2"></i>Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="editName" name="name" value="{{ old('name', $user->name) }}" required>
                    <label for="editName">Full name</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="email" class="form-control" id="editEmail" name="email" value="{{ old('email', $user->email) }}" required>
                    <label for="editEmail">Email</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="editPhone" name="phone" value="{{ old('phone', $user->phone) }}">
                    <label for="editPhone">Phone</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="editPassword" name="password" value="{{ old('password', $user->password) }}" style="font-family: monospace;">
                    <label for="editPassword">Password</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="editRole" name="role" required>
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <label for="editRole">Role</label>
                </div>
            </div>
        </div>
        <div class="mt-3 small text-muted">
            Only basic profile fields are editable here.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>


