<!-- Success Modal Component -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:12px;">
            <div class="modal-header" style="border:0;">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span id="successModalTitle">Success</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="mb-1" id="successModalMessage">Operation completed successfully.</p>
                <p class="text-muted mb-0">You can continue with your work.</p>
            </div>
            <div class="modal-footer" style="border:0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="successModalContinueBtn">
                    <i class="fas fa-check me-1"></i>Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global function to show success modal
function showSuccessModal(title = 'Success', message = 'Operation completed successfully.') {
    // Update modal content
    document.getElementById('successModalTitle').textContent = title;
    document.getElementById('successModalMessage').textContent = message;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    modal.show();
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        modal.hide();
    }, 3000);
}

// Global function to show success modal for specific operations
function showOperationSuccess(operation, entity, entityName = '') {
    const messages = {
        'create': {
            'user': `User ${entityName ? `"${entityName}"` : ''} created successfully.`,
            'farm': `Farm ${entityName ? `"${entityName}"` : ''} created successfully.`
        },
        'update': {
            'user': `User ${entityName ? `"${entityName}"` : ''} updated successfully.`,
            'farm': `Farm ${entityName ? `"${entityName}"` : ''} updated successfully.`
        },
        'delete': {
            'user': `User ${entityName ? `"${entityName}"` : ''} deleted successfully.`,
            'farm': `Farm ${entityName ? `"${entityName}"` : ''} deleted successfully.`
        }
    };
    
    const title = 'Success';
    const message = messages[operation]?.[entity] || 'Operation completed successfully.';
    
    showSuccessModal(title, message);
}
</script>
