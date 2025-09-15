<form method="POST" action="{{ route('admin.farms.update', $farm) }}" id="editFarmForm" 
      data-current-province="{{ $farm->province_name }}" 
      data-current-municipality="{{ $farm->city_municipality_name }}" 
      data-current-barangay="{{ $farm->barangay_name }}">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-pen-to-square me-2"></i>Edit Farm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="edit_farm_name" name="farm_name" value="{{ old('farm_name', $farm->farm_name) }}" required>
                    <label for="edit_farm_name">Farm Name</label>
                </div>
            </div>
            
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="edit_watermelon_variety" name="watermelon_variety">
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
                            $defaultVarieties = [
                                'Cantaloupe / Muskmelon',
                                'Honeydew Melon',
                                'Watermelon',
                                'Winter Melon',
                                'Bitter Melon',
                                'Snake Melon',
                            ];
                            
                            // Get varieties from database
                            $dbVarieties = \App\Models\Farm::whereNotNull('watermelon_variety')
                                ->distinct()
                                ->orderBy('watermelon_variety')
                                ->pluck('watermelon_variety')
                                ->toArray();
                            
                            $extraVarieties = array_values(array_diff($dbVarieties, $defaultVarieties));
                        @endphp
                        
                        @foreach($defaultVarieties as $variety)
                            <option value="{{ $variety }}" {{ old('watermelon_variety', $farm->watermelon_variety) == $variety ? 'selected' : '' }}>
                                {{ ($emojiMap[$variety] ?? '') }} {{ $variety }}
                            </option>
                        @endforeach
                        
                        @if(!empty($extraVarieties))
                            <optgroup label="More varieties (from database)">
                                @foreach($extraVarieties as $variety)
                                    <option value="{{ $variety }}" {{ old('watermelon_variety', $farm->watermelon_variety) == $variety ? 'selected' : '' }}>
                                        {{ $variety }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    <label for="edit_watermelon_variety">Melon/Watermelon Variety</label>
                </div>
            </div>
            
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="date" class="form-control" id="edit_planting_date" name="planting_date" value="{{ old('planting_date', $farm->planting_date ? $farm->planting_date->format('Y-m-d') : '') }}">
                    <label for="edit_planting_date">Planting Date</label>
                </div>
            </div>
            
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="number" step="0.01" class="form-control" id="edit_land_size" name="land_size" value="{{ old('land_size', $farm->land_size) }}" min="0">
                    <label for="edit_land_size">Land Size</label>
                </div>
            </div>
            
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <select class="form-select" id="edit_land_size_unit" name="land_size_unit">
                        <option value="">Select unit</option>
                        <option value="m2" {{ old('land_size_unit', $farm->land_size_unit) == 'm2' ? 'selected' : '' }}>Square Meters (m²)</option>
                        <option value="ha" {{ old('land_size_unit', $farm->land_size_unit) == 'ha' ? 'selected' : '' }}>Hectares (ha)</option>
                    </select>
                    <label for="edit_land_size_unit">Size Unit</label>
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <div class="form-floating">
                    <select class="form-select" id="edit_province" name="province_name" required>
                        <option value="{{ $farm->province_name }}" selected>{{ $farm->province_name }}</option>
                    </select>
                    <label for="edit_province">Province</label>
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <div class="form-floating">
                    <select class="form-select" id="edit_city_municipality" name="city_municipality_name" required>
                        <option value="{{ $farm->city_municipality_name }}" selected>{{ $farm->city_municipality_name }}</option>
                    </select>
                    <label for="edit_city_municipality">City/Municipality</label>
                </div>
            </div>
            
            <div class="col-12 col-md-4">
                <div class="form-floating">
                    <select class="form-select" id="edit_barangay" name="barangay_name">
                        <option value="{{ $farm->barangay_name }}" selected>{{ $farm->barangay_name ?: 'No barangay' }}</option>
                    </select>
                    <label for="edit_barangay">Barangay</label>
                </div>
            </div>
        </div>
        
        <!-- Hidden fields to store current values for JavaScript initialization -->
        <input type="hidden" id="current_province" value="{{ $farm->province_name }}">
        <input type="hidden" id="current_city_municipality" value="{{ $farm->city_municipality_name }}">
        <input type="hidden" id="current_barangay" value="{{ $farm->barangay_name }}">
        
        <div class="mt-3 small text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Location fields will be populated automatically based on your selections.
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
