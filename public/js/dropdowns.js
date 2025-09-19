/**
 * Enhanced Bootstrap Dropdown Implementation
 * A lightweight enhancement for MeloTech's Bootstrap dropdowns
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns
    initBootstrapDropdowns();
    
    // Re-initialize dropdowns when content is dynamically loaded
    document.addEventListener('contentLoaded', function() {
        initBootstrapDropdowns();
    });
    
    // Close dropdowns when pressing Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeAllDropdowns();
        }
    });
});

/**
 * Initialize Bootstrap dropdowns with enhancements
 */
function initBootstrapDropdowns() {
    // Initialize all Bootstrap dropdowns
    const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    
    dropdownElements.forEach(element => {
        // Initialize Bootstrap dropdown if not already initialized
        if (!bootstrap.Dropdown.getInstance(element)) {
            new bootstrap.Dropdown(element);
        }
        
        // Add enhanced functionality
        enhanceDropdown(element);
    });
}

/**
 * Enhance a Bootstrap dropdown with additional functionality
 */
function enhanceDropdown(trigger) {
    const dropdownMenu = document.querySelector(`#${trigger.getAttribute('aria-controls')}`);
    
    if (!dropdownMenu) return;
    
    // Add smooth transitions
    dropdownMenu.addEventListener('show.bs.dropdown', function() {
        this.style.opacity = '0';
        this.style.transform = 'translateY(-10px)';
    });
    
    dropdownMenu.addEventListener('shown.bs.dropdown', function() {
        this.style.opacity = '1';
        this.style.transform = 'translateY(0)';
    });
    
    dropdownMenu.addEventListener('hide.bs.dropdown', function() {
        this.style.opacity = '0';
        this.style.transform = 'translateY(-10px)';
    });
    
    // Add keyboard navigation
    dropdownMenu.addEventListener('keydown', function(e) {
        const items = Array.from(this.querySelectorAll('.dropdown-item:not(.disabled)'));
        const currentIndex = items.indexOf(document.activeElement);
        let nextIndex = -1;
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                nextIndex = (currentIndex + 1) % items.length;
                break;
            case 'ArrowUp':
                e.preventDefault();
                nextIndex = (currentIndex - 1 + items.length) % items.length;
                break;
            case 'Home':
                e.preventDefault();
                nextIndex = 0;
                break;
            case 'End':
                e.preventDefault();
                nextIndex = items.length - 1;
                break;
            case 'Escape':
            case 'Esc':
                e.preventDefault();
                closeAllDropdowns();
                trigger.focus();
                return;
        }
        
        if (nextIndex >= 0 && nextIndex < items.length) {
            items[nextIndex].focus();
        }
    });
    
    // Focus first item when dropdown opens
    trigger.addEventListener('shown.bs.dropdown', function() {
        const firstItem = dropdownMenu.querySelector('.dropdown-item:not(.disabled)');
        if (firstItem) {
            firstItem.focus();
        }
    });
}

/**
 * Toggle a Bootstrap dropdown's visibility
 * @param {HTMLElement} trigger - The dropdown trigger button
 * @param {boolean} show - Whether to show or hide the dropdown
 */
function toggleDropdown(trigger, show) {
    const dropdown = bootstrap.Dropdown.getInstance(trigger);
    if (dropdown) {
        if (show) {
            dropdown.show();
        } else {
            dropdown.hide();
        }
    }
}

/**
 * Close all dropdowns on the page
 */
function closeAllDropdowns() {
    // Close all Bootstrap dropdowns
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(trigger => {
        const dropdown = bootstrap.Dropdown.getInstance(trigger);
        if (dropdown) {
            dropdown.hide();
        }
    });
}

// Make functions available globally
window.MeloTechDropdowns = {
    init: initBootstrapDropdowns,
    toggle: toggleDropdown,
    closeAll: closeAllDropdowns
};
