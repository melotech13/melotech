<form method="POST" action="{{ route('admin.farms.update', $farm) }}" id="editFarmForm">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-pen-to-square me-2"></i>Edit Farm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="farm_name" value="{{ old('farm_name', $farm->farm_name) }}" required>
                    <label>Farm Name</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="watermelon_variety" value="{{ old('watermelon_variety', $farm->watermelon_variety) }}">
                    <label>Variety</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="province_name" value="{{ old('province_name', $farm->province_name) }}" required>
                    <label>Province</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="city_municipality_name" value="{{ old('city_municipality_name', $farm->city_municipality_name) }}" required>
                    <label>City/Municipality</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="barangay_name" value="{{ old('barangay_name', $farm->barangay_name) }}">
                    <label>Barangay</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="number" step="0.01" min="0" class="form-control" name="field_size" value="{{ old('field_size', $farm->field_size) }}">
                    <label>Field Size</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" name="field_size_unit" value="{{ old('field_size_unit', $farm->field_size_unit) }}">
                    <label>Size Unit</label>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating">
                    <input type="date" class="form-control" name="planting_date" value="{{ old('planting_date', optional($farm->planting_date)->format('Y-m-d')) }}">
                    <label>Planting Date</label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>


