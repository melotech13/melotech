<div class="modal-header">
    <h5 class="modal-title"><i class="fas fa-tractor me-2"></i>View Farm</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->farm_name }}">
                <label>Farm Name</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->user->name }}">
                <label>Owner</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->watermelon_variety }}">
                <label>Variety</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->barangay_name ? $farm->barangay_name . ', ' : '' }}{{ $farm->city_municipality_name }}, {{ $farm->province_name }}">
                <label>Location</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->land_size }} {{ $farm->land_size_unit }}">
                <label>Size</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->planting_date?->format('M d, Y') ?? 'Not set' }}">
                <label>Planting Date</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <input type="text" readonly class="form-control" value="{{ $farm->created_at?->format('M d, Y H:i') }}">
                <label>Created</label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
