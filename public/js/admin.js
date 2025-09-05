// Admin Interface JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminInterface();
});

function initializeAdminInterface() {
    // Initialize all admin components
    initializeAnimations();
    initializeInteractiveElements();
    initializeRealTimeUpdates();
    initializeSearchAndFilter();
    initializeDataTables();
    initializeCharts();
    initializeNotifications();
}

// Animation Management
function initializeAnimations() {
    // Intersection Observer for scroll-triggered animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all admin cards and elements
    document.querySelectorAll('.admin-card, .stats-card, .admin-table tbody tr').forEach(el => {
        observer.observe(el);
    });

    // Add staggered animation delays
    document.querySelectorAll('.stats-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Add hover effects with sound (optional)
    document.querySelectorAll('.admin-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Interactive Elements
function initializeInteractiveElements() {
    // Enhanced table interactions
    document.querySelectorAll('.admin-table tbody tr').forEach(row => {
        row.addEventListener('click', function() {
            // Remove active class from all rows
            document.querySelectorAll('.admin-table tbody tr').forEach(r => {
                r.classList.remove('active-row');
            });
            
            // Add active class to clicked row
            this.classList.add('active-row');
            
            // Add subtle animation
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });

    // Enhanced card interactions
    document.querySelectorAll('.admin-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
        });
    });

    // Enhanced button interactions
    document.querySelectorAll('.admin-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Real-time Updates
function initializeRealTimeUpdates() {
    // Update statistics in real-time
    setInterval(() => {
        updateStatistics();
    }, 30000); // Update every 30 seconds

    // Live notification system
    initializeLiveNotifications();
}

function updateStatistics() {
    // Animate number counters
    document.querySelectorAll('.stats-number').forEach(counter => {
        const target = parseInt(counter.textContent);
        animateCounter(counter, 0, target, 1000);
    });
}

function animateCounter(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = Math.floor(current);
        
        if (current >= end) {
            element.textContent = end;
            clearInterval(timer);
        }
    }, 16);
}

// Search and Filter Functionality
function initializeSearchAndFilter() {
    const searchInput = document.querySelector('.admin-search');
    if (searchInput) {
        const handler = debounce(function() {
            const searchTerm = searchInput.value.toLowerCase();
            filterTable(searchTerm);
        }, 250);
        searchInput.addEventListener('input', handler);
    }

    // Role filters on users page
    document.querySelectorAll('[data-filter-role]')?.forEach(btn => {
        btn.addEventListener('click', () => {
            const role = btn.getAttribute('data-filter-role');
            window.currentRoleFilter = role;
            const searchTerm = (document.querySelector('.admin-search')?.value || '').toLowerCase();
            filterTable(searchTerm);
        });
    });
}

function filterTable(searchTerm) {
    const tableRows = document.querySelectorAll('.admin-table tbody tr');
    const roleFilter = window.currentRoleFilter || 'all';

    tableRows.forEach((row, index) => {
        const text = row.textContent.toLowerCase();
        const rowRole = row.getAttribute('data-role') || '';
        const matchesText = text.includes(searchTerm);
        const matchesRole = roleFilter === 'all' || rowRole === roleFilter;

        if (matchesText && matchesRole) {
            row.style.display = '';
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.add('fade-in');
        } else {
            row.style.display = 'none';
            row.classList.remove('fade-in');
        }
    });
}

// Enhanced Data Tables
function initializeDataTables() {
    // Add sorting functionality by explicit data keys
    document.querySelectorAll('.admin-table th[data-sortable]').forEach(header => {
        header.addEventListener('click', function() {
            const column = this.cellIndex;
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const sortKey = this.getAttribute('data-sort-key');
            
            // Toggle sort direction
            const isAscending = this.classList.contains('sort-asc');
            
            // Remove sort classes from all headers
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Add sort class to current header
            this.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
            
            // Sort rows
            rows.sort((a, b) => {
                let aValue = '';
                let bValue = '';

                if (sortKey) {
                    // Prefer data attributes when present
                    aValue = a.getAttribute('data-' + sortKey) ?? '';
                    bValue = b.getAttribute('data-' + sortKey) ?? '';
                } else {
                    aValue = a.cells[column].textContent.trim();
                    bValue = b.cells[column].textContent.trim();
                }

                // Numeric sort fallback when both values are numbers
                const aNum = parseFloat(aValue);
                const bNum = parseFloat(bValue);
                const bothNumeric = !isNaN(aNum) && !isNaN(bNum);

                if (bothNumeric) {
                    return isAscending ? (bNum - aNum) : (aNum - bNum);
                }

                // String sort (case-insensitive)
                aValue = String(aValue).toLowerCase();
                bValue = String(bValue).toLowerCase();
                return isAscending ? bValue.localeCompare(aValue) : aValue.localeCompare(bValue);
            });
            
            // Reorder rows with animation
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('slide-in');
                tbody.appendChild(row);
            });
        });
    });
}

// Chart Animations
function initializeCharts() {
    // Animate progress bars
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 500);
    });

    // Animate statistics cards
    document.querySelectorAll('.stats-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Notification System
function initializeNotifications() {
    // Show success/error messages with animations
    const alerts = document.querySelectorAll('.admin-alert');
    alerts.forEach(alert => {
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            dismissAlert(alert);
        }, 5000);
        
        // Add dismiss button functionality
        const dismissBtn = alert.querySelector('.alert-dismiss');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                dismissAlert(alert);
            });
        }
    });
}

function dismissAlert(alert) {
    alert.style.transform = 'translateX(100%)';
    alert.style.opacity = '0';
    
    setTimeout(() => {
        alert.remove();
    }, 300);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `admin-alert alert-${type}`;
    notification.innerHTML = `
        <div class="alert-content">
            <span class="alert-message">${message}</span>
            <button class="alert-dismiss">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    // Auto-dismiss
    setTimeout(() => {
        dismissAlert(notification);
    }, 5000);
}

// Live Notifications
function initializeLiveNotifications() {
    // Simulate real-time updates
    setInterval(() => {
        const notifications = [
            'New user registered',
            'Farm data updated',
            'System backup completed',
            'Weather data refreshed'
        ];
        
        const randomNotification = notifications[Math.floor(Math.random() * notifications.length)];
        const types = ['success', 'info', 'warning'];
        const randomType = types[Math.floor(Math.random() * types.length)];
        
        // Only show notifications occasionally (10% chance)
        if (Math.random() < 0.1) {
            showNotification(randomNotification, randomType);
        }
    }, 30000); // Check every 30 seconds
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for global use
window.AdminInterface = {
    showNotification,
    updateStatistics,
    filterTable,
    dismissAlert
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to buttons
    document.querySelectorAll('.admin-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('no-loading')) {
                this.classList.add('loading');
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 1000);
            }
        });
    });

    // Add smooth scrolling to all internal links
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

    // Add keyboard navigation for tables
    document.querySelectorAll('.admin-table tbody tr').forEach(row => {
        row.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
        
        // Make rows focusable
        row.setAttribute('tabindex', '0');
    });

    // Add search functionality
    const searchInput = document.querySelector('.admin-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const searchTerm = this.value.toLowerCase();
            filterTable(searchTerm);
        }, 300));
    }

    // Add sort indicators to table headers
    document.querySelectorAll('.admin-table th[data-sortable]').forEach(header => {
        header.addEventListener('click', function() {
            const isAscending = this.classList.contains('sort-asc');
            
            // Remove sort classes from all headers
            this.closest('table').querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Add sort class to current header
            this.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
        });
    });

    // Copy to clipboard for email/phone with toast
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.copy-btn');
        if (!btn) return;
        const value = btn.getAttribute('data-copy');
        if (!value) return;
        navigator.clipboard.writeText(value).then(() => {
            showNotification('Copied to clipboard', 'success');
        }).catch(() => {
            showNotification('Copy failed', 'warning');
        });
    });
});

// Utility function for smooth animations
function animateValue(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        element.textContent = Math.floor(current);
        
        if (current >= end) {
            element.textContent = end;
            clearInterval(timer);
        }
    }, 16);
}

// Add this to the global AdminInterface
window.AdminInterface.animateValue = animateValue;
