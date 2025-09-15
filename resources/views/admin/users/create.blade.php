@extends('layouts.admin')

@section('title', 'Add User - MeloTech')

@section('page-title', 'Add New User')

@section('content')
<div class="section">
    <div class="section-header animate-fade-in-up">
        <div class="section-title">
            <i class="fas fa-user-plus section-icon"></i>
            Add User
        </div>
        <p class="section-subtitle mb-0">Create a new account. For Admin role, farm fields will be hidden.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger animate-fade-in-up" style="animation-delay:.05s;">
            <i class="fas fa-triangle-exclamation me-2"></i>
            Please fix the errors below.
        </div>
    @endif

    <div class="card p-3 p-md-4 shadow-sm animate-fade-in-up" style="animation-delay:.1s; border-radius: 12px;">
        <form method="POST" action="{{ route('admin.users.store') }}" id="addUserForm" novalidate>
            @csrf

            <div class="row g-4">
                <div class="col-12">
                    <h5 class="mb-2">Account Information</h5>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                        <label for="name">Full name</label>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                        <label for="email">Email</label>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                        <label for="password_confirmation">Confirm password</label>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone">
                        <label for="phone">Phone</label>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-floating">
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="user" {{ old('role','user')==='user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <label for="role">Role</label>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                

                <div class="col-12 mt-2">
                    <hr>
                </div>

                <div class="col-12 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Farm Information</h5>
                    <span class="text-muted small" id="farmInfoHint">Required for Users; hidden for Admins</span>
                </div>

                <div class="col-12" id="farmFields">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control @error('farm_name') is-invalid @enderror" id="farm_name" name="farm_name" value="{{ old('farm_name') }}" placeholder="Farm name">
                                <label for="farm_name">Farm name</label>
                                @error('farm_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div>
                                <label for="watermelon_variety" class="form-label">Melon/Watermelon variety</label>
                                <select class="form-select @error('watermelon_variety') is-invalid @enderror" id="watermelon_variety" name="watermelon_variety">
                                    <option value="">Select variety</option>
                                    @php
                                        $emojiMap = [
                                            'Cantaloupe / Muskmelon' => '🍈',
                                            'Honeydew Melon' => '🍈',
                                            'Watermelon' => '🍉',
                                            'Winter Melon' => '🥒',
                                            'Bitter Melon' => '🥒',
                                            'Snake Melon' => '🥒',
                                        ];
                                    @endphp
                                    @foreach(($defaultVarieties ?? []) as $variety)
                                        <option value="{{ $variety }}" {{ old('watermelon_variety') == $variety ? 'selected' : '' }}>
                                            {{ ($emojiMap[$variety] ?? '') }} {{ $variety }}
                                        </option>
                                    @endforeach
                                    @if(!empty($extraVarieties))
                                        <optgroup label="More varieties (from database)">
                                            @foreach($extraVarieties as $variety)
                                                <option value="{{ $variety }}" {{ old('watermelon_variety') == $variety ? 'selected' : '' }}>{{ $variety }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('watermelon_variety')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control @error('planting_date') is-invalid @enderror" id="planting_date" name="planting_date" value="{{ old('planting_date') }}" placeholder="Planting date">
                                <label for="planting_date">Planting date</label>
                                @error('planting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="row g-2">
                                <div class="col-8">
                                    <div class="form-floating">
                                        <input type="number" step="0.01" class="form-control @error('land_size') is-invalid @enderror" id="land_size" name="land_size" value="{{ old('land_size') }}" placeholder="Land size">
                                        <label for="land_size">Land size</label>
                                        @error('land_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-floating">
                                        <select class="form-select @error('land_size_unit') is-invalid @enderror" id="land_size_unit" name="land_size_unit">
                                            <option value="">Unit</option>
                                            <option value="m2" {{ old('land_size_unit')==='m2' ? 'selected' : '' }}>Square Meters (m²)</option>
                                            <option value="ha" {{ old('land_size_unit')==='ha' ? 'selected' : '' }}>Hectares (ha)</option>
                                        </select>
                                        <label for="land_size_unit">Unit</label>
                                        @error('land_size_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div>
                                <label for="province" class="form-label">Province</label>
                                <select class="form-select @error('province_name') is-invalid @enderror" id="province" name="province_name">
                                    <option value="">Loading provinces...</option>
                                </select>
                                @error('province_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div>
                                <label for="city_municipality" class="form-label">Municipality/City</label>
                                <select class="form-select @error('city_municipality_name') is-invalid @enderror" id="city_municipality" name="city_municipality_name">
                                    <option value="">Select Province first</option>
                                </select>
                                @error('city_municipality_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div>
                                <label for="barangay" class="form-label">Barangay (optional)</label>
                                <select class="form-select @error('barangay_name') is-invalid @enderror" id="barangay" name="barangay_name">
                                    <option value="">Select Municipality first</option>
                                </select>
                                @error('barangay_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary admin-btn"><i class="fas fa-check me-2"></i>Create User</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const farmFields = document.getElementById('farmFields');
    const farmInfoHint = document.getElementById('farmInfoHint');
    const requiredWhenUser = ['farm_name','province_name','city_municipality_name'];

    function setFarmVisibility() {
        const isAdmin = (roleSelect?.value || '').toLowerCase() === 'admin';
        if (farmFields) {
            farmFields.style.display = isAdmin ? 'none' : '';
        }
        if (farmInfoHint) {
            farmInfoHint.textContent = isAdmin ? 'Hidden for Admins' : 'Required for Users';
        }
        requiredWhenUser.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                if (isAdmin) {
                    el.removeAttribute('required');
                } else {
                    el.setAttribute('required', 'required');
                }
            }
        });
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', setFarmVisibility);
        setFarmVisibility();
    }
    // Initialize cascading dropdowns for Province / Municipality / Barangay
    try {
        if (window.LocationsManager && typeof window.LocationsManager.initializeCascadingDropdowns === 'function') {
            window.LocationsManager.initializeCascadingDropdowns({
                provinceSelector: '#province',
                municipalitySelector: '#city_municipality',
                barangaySelector: '#barangay'
            });
        } else {
            // Fallback loader for locations.js if not already present
            const script = document.createElement('script');
            script.src = '/js/locations.js';
            script.onload = function() {
                if (window.LocationsManager) {
                    window.LocationsManager.initializeCascadingDropdowns({
                        provinceSelector: '#province',
                        municipalitySelector: '#city_municipality',
                        barangaySelector: '#barangay'
                    });
                }
            };
            document.body.appendChild(script);
        }
    } catch (e) {
        console.error('Failed to initialize locations cascading dropdowns', e);
    }
});
</script>
@endpush
@endsection


