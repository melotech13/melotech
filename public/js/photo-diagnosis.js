/**
 * Photo Diagnosis Page JavaScript
 * Handles animations, data tables, and delete functionality
 * Optimized for performance with debouncing and efficient DOM manipulation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable for better table handling
    initializeDataTable();
    
    // Initialize progress bar animations
    initializeProgressBars();
    
    // Initialize delete functionality
    initializeDeleteFunctionality();
    
    // Initialize dropdown fixes
    initializeDropdownFixes();
});

/**
 * Initialize DataTable if present
 */
function initializeDataTable() {
    if (typeof $ !== 'undefined' && $('#dataTable').length && $('#dataTable tbody tr').length > 0) {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": true,
            "lengthChange": false,
            "info": false,
            "order": [[ 4, "desc" ]], // Sort by date descending
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] } // Disable sorting for photo and actions columns
            ]
        });
    }
}

/**
 * Initialize progress bar animations with smooth transitions
 */
function initializeProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar[data-width]');
    
    progressBars.forEach(function(progressBar) {
        const width = progressBar.getAttribute('data-width');
        
        // Add transition for smooth animation
        progressBar.style.transition = 'width 0.8s ease-in-out';
        
        // Animate to the target width with a slight delay for better visual effect
        setTimeout(() => {
            progressBar.style.width = width + '%';
        }, 100);
    });
}

/**
 * Initialize delete functionality for analysis cards
 */
function initializeDeleteFunctionality() {
    let deleteModal = null;
    let currentAnalysisId = null;
    
    // Initialize Bootstrap modal
    const modalElement = document.getElementById('deleteConfirmModal');
    if (modalElement) {
        deleteModal = new bootstrap.Modal(modalElement);
    }
    
    // Handle delete button clicks
    document.querySelectorAll('.delete-analysis-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentAnalysisId = this.dataset.analysisId;
            const analysisDate = this.dataset.analysisDate;
            
            // Update modal content
            const dateElement = document.getElementById('deleteAnalysisDate');
            if (dateElement) {
                dateElement.textContent = analysisDate;
            }
            
            // Show modal
            if (deleteModal) {
                deleteModal.show();
            }
        });
    });
    
    // Handle confirm delete
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (!currentAnalysisId) return;
            
            handleDeleteAnalysis(currentAnalysisId, this, deleteModal);
        });
    }
}

/**
 * Handle the actual deletion of an analysis
 * @param {string} analysisId - The ID of the analysis to delete
 * @param {HTMLElement} deleteBtn - The delete button element
 * @param {Object} deleteModal - The Bootstrap modal instance
 */
function handleDeleteAnalysis(analysisId, deleteBtn, deleteModal) {
    const originalContent = deleteBtn.innerHTML;
    
    // Show loading state
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.getAttribute('content') : '';
    
    // Send delete request
    fetch(`/photo-diagnosis/${analysisId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the analysis card from the DOM with animation
            removeAnalysisCard(analysisId);
            
            // Show success message
            showSuccessModal('Success', 'Analysis deleted successfully.');
        } else {
            showAlert('error', data.message || 'Failed to delete analysis');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while deleting the analysis');
    })
    .finally(() => {
        // Reset button state
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalContent;
        
        // Hide modal
        if (deleteModal) {
            deleteModal.hide();
        }
        
        currentAnalysisId = null;
    });
}

/**
 * Remove analysis card with smooth animation
 * @param {string} analysisId - The ID of the analysis to remove
 */
function removeAnalysisCard(analysisId) {
    const analysisCard = document.querySelector(`[data-analysis-id="${analysisId}"]`);
    if (!analysisCard) return;
    
    const cardElement = analysisCard.closest('.analysis-card');
    if (!cardElement) return;
    
    // Add exit animation
    cardElement.style.transition = 'all 0.3s ease';
    cardElement.style.opacity = '0';
    cardElement.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        cardElement.remove();
        
        // Check if there are no more analyses
        checkForEmptyState();
    }, 300);
}

/**
 * Check if analyses grid is empty and show no data message
 */
function checkForEmptyState() {
    const analysesGrid = document.querySelector('.analyses-grid');
    if (!analysesGrid || analysesGrid.children.length > 0) return;
    
    // Show no data message
    const noDataHtml = `
        <div class="no-data-content">
            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Analyses Yet</h5>
            <p class="text-muted mb-3">Start by uploading your first photo for AI-powered diagnosis</p>
            <a href="/photo-diagnosis/create" class="btn btn-primary btn-lg">
                <i class="fas fa-upload me-2"></i>Upload First Photo
            </a>
        </div>
    `;
    
    analysesGrid.parentElement.innerHTML = noDataHtml;
}

/**
 * Show alert message
 * @param {string} type - Alert type ('success' or 'error')
 * @param {string} message - Alert message
 */
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-of-type');
        if (alert && typeof bootstrap !== 'undefined') {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}

/**
 * Show success modal (if available)
 * @param {string} title - Modal title
 * @param {string} message - Modal message
 */
function showSuccessModal(title, message) {
    // Check if success modal function exists
    if (typeof showSuccessModal === 'function') {
        showSuccessModal(title, message);
    } else {
        // Fallback to alert
        showAlert('success', message);
    }
}

/**
 * Utility function to add smooth scroll behavior
 */
function addSmoothScroll() {
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Initialize page animations and interactions
 */
function initializePageAnimations() {
    // Add entrance animations to cards
    const cards = document.querySelectorAll('.stat-card, .analysis-card, .start-analyzing-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                // Remove will-change after animation to free up GPU resources
                entry.target.style.willChange = 'auto';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        card.style.willChange = 'transform, opacity';
        observer.observe(card);
    });
}

/**
 * Initialize dropdown fixes to ensure proper positioning and visibility
 */
function initializeDropdownFixes() {
    // Wait for Bootstrap to be loaded
    if (typeof bootstrap === 'undefined') {
        setTimeout(initializeDropdownFixes, 100);
        return;
    }
    
    // Fix dropdown positioning issues without interfering with toggle behavior
    const dropdowns = document.querySelectorAll('.dropdown:not(.dropdown-fixed)');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Add a class to mark this dropdown as fixed to prevent duplicate initialization
            dropdown.classList.add('dropdown-fixed');
            
            // Ensure dropdown has proper z-index
            dropdown.style.position = 'relative';
            dropdown.style.zIndex = '1000';
            menu.style.zIndex = '9999';
            
            // Initialize Bootstrap dropdown if not already initialized
            if (!bootstrap.Dropdown.getInstance(toggle)) {
                new bootstrap.Dropdown(toggle, {
                    boundary: 'viewport',
                    popperConfig: (defaultBsPopperConfig) => ({
                        ...defaultBsPopperConfig,
                        modifiers: [
                            ...(defaultBsPopperConfig.modifiers || []),
                            {
                                name: 'preventOverflow',
                                options: {
                                    boundary: document.body,
                                    altBoundary: true,
                                    padding: 10
                                }
                            },
                            {
                                name: 'flip',
                                options: {
                                    fallbackPlacements: ['top', 'bottom', 'left', 'right'],
                                    boundary: document.body,
                                    padding: 10
                                }
                            }
                        ]
                    })
                });
            }
            
            // Handle positioning when dropdown is shown
            const handleShow = () => {
                menu.style.zIndex = '9999';
                positionDropdown(menu, toggle);
                
                // Add click outside handler
                const handleClickOutside = (e) => {
                    if (!dropdown.contains(e.target) && !e.target.matches('.dropdown-toggle')) {
                        const dropdownInstance = bootstrap.Dropdown.getInstance(toggle);
                        if (dropdownInstance) {
                            dropdownInstance.hide();
                        }
                        document.removeEventListener('click', handleClickOutside);
                    }
                };
                
                // Add click outside handler after a small delay to avoid immediate close
                setTimeout(() => {
                    document.addEventListener('click', handleClickOutside);
                }, 10);
                
                // Clean up on hide
                const handleHide = () => {
                    document.removeEventListener('click', handleClickOutside);
                    dropdown.removeEventListener('hide.bs.dropdown', handleHide);
                };
                
                dropdown.addEventListener('hide.bs.dropdown', handleHide);
            };
            
            // Add show event listener
            dropdown.addEventListener('show.bs.dropdown', handleShow);
        }
    });
}

/**
 * Position dropdown menu correctly
 * @param {HTMLElement} menu - The dropdown menu element
 * @param {HTMLElement} toggle - The dropdown toggle element
 */
function positionDropdown(menu, toggle) {
    if (!menu || !toggle) return;
    
    const rect = toggle.getBoundingClientRect();
    const menuRect = menu.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    const viewportWidth = window.innerWidth;
    
    // Reset positioning
    menu.style.position = 'absolute';
    menu.style.top = '100%';
    menu.style.left = '0';
    menu.style.right = 'auto';
    menu.style.transform = 'none';
    menu.style.minWidth = 'max-content';
    menu.style.maxHeight = 'none';
    
    // Calculate available space
    const spaceBelow = viewportHeight - rect.bottom - 10; // 10px padding from bottom
    const spaceAbove = rect.top - 10; // 10px padding from top
    const spaceRight = viewportWidth - rect.right - 10; // 10px padding from right
    const spaceLeft = rect.left - 10; // 10px padding from left
    
    // Check if we need to adjust vertical position
    const shouldShowAbove = spaceBelow < menuRect.height && spaceAbove > menuRect.height;
    const shouldShowBelow = !shouldShowAbove || spaceBelow > spaceAbove;
    
    // Apply vertical positioning
    if (shouldShowAbove) {
        menu.style.top = 'auto';
        menu.style.bottom = '100%';
        menu.style.marginTop = '0';
        menu.style.marginBottom = '4px';
    } else {
        menu.style.top = '100%';
        menu.style.bottom = 'auto';
        menu.style.marginTop = '4px';
        menu.style.marginBottom = '0';
    }
    
    // Check if we need to adjust horizontal position
    const shouldShowLeft = spaceRight < menuRect.width && spaceLeft > menuRect.width;
    const shouldShowRight = spaceRight < menuRect.width && spaceLeft < spaceRight;
    
    // Apply horizontal positioning
    if (shouldShowLeft) {
        menu.style.left = 'auto';
        menu.style.right = '0';
    } else if (shouldShowRight) {
        menu.style.left = 'auto';
        menu.style.right = '0';
    } else {
        menu.style.left = '0';
        menu.style.right = 'auto';
    }
    
    // Ensure dropdown is visible and properly positioned
    menu.style.display = 'block';
    menu.style.visibility = 'visible';
    menu.style.opacity = '1';
    
    // Force reflow/repaint to ensure styles are applied
    // eslint-disable-next-line no-void
    void menu.offsetHeight;
    
    // Final check to ensure menu is within viewport
    const finalMenuRect = menu.getBoundingClientRect();
    
    // Adjust if going off right edge
    if (finalMenuRect.right > viewportWidth) {
        menu.style.left = 'auto';
        menu.style.right = '0';
    }
    
    // Adjust if going off left edge
    if (finalMenuRect.left < 0) {
        menu.style.left = '0';
        menu.style.right = 'auto';
    }
    
    // Adjust if going off bottom edge
    if (finalMenuRect.bottom > viewportHeight) {
        menu.style.top = 'auto';
        menu.style.bottom = '100%';
        menu.style.marginTop = '0';
        menu.style.marginBottom = '4px';
    }
    
    // Adjust if going off top edge
    if (finalMenuRect.top < 0) {
        menu.style.top = '100%';
        menu.style.bottom = 'auto';
        menu.style.marginTop = '4px';
        menu.style.marginBottom = '0';
    }
}

// Initialize page animations when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializePageAnimations();
    addSmoothScroll();
});
