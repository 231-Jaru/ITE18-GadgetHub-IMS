/**
 * Global Button Functionality Fixes
 * Ensures all buttons work correctly across the application
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        fixCancelButtons();
        fixFormButtons();
        fixDeleteButtons();
        fixModalButtons();
        fixNavigationButtons();
    }

    /**
     * Fix Cancel/Back buttons to prevent form submission
     */
    function fixCancelButtons() {
        document.querySelectorAll('a.btn[href], button.btn[type="button"]').forEach(function(btn) {
            // Skip if it's a submit button or has data-bs-toggle
            if (btn.type === 'submit' || btn.hasAttribute('data-bs-toggle')) {
                return;
            }

            // Prevent any accidental form submission
            btn.addEventListener('click', function(e) {
                // If it's inside a form, make sure it doesn't submit
                const form = btn.closest('form');
                if (form && btn.tagName === 'BUTTON' && !btn.type) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (btn.href) {
                        window.location.href = btn.href;
                    }
                }
            });
        });
    }

    /**
     * Fix form submission buttons with loading states
     */
    function fixFormButtons() {
        document.querySelectorAll('form').forEach(function(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            // Store original button content
            if (!submitBtn.dataset.originalHtml) {
                submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            }

            form.addEventListener('submit', function(e) {
                // Check if form is valid
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return false;
                }

                // Add loading state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalHtml = submitBtn.dataset.originalHtml || submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    
                    // Re-enable after 10 seconds as fallback
                    setTimeout(function() {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }, 10000);
                }
            });
        });
    }

    /**
     * Fix delete buttons with proper confirmation
     * Ensures ALL destructive actions require confirmation across all modules
     */
    function fixDeleteButtons() {
        // Fix delete forms - check for DELETE method or delete-form class
        document.querySelectorAll('form[method="DELETE"], form[method="delete"], form.delete-form').forEach(function(form) {
            // Only add handler if form doesn't already have onsubmit
            if (!form.hasAttribute('onsubmit')) {
                form.addEventListener('submit', function(e) {
                    // Get item name from data attribute for better confirmation message
                    const itemName = form.getAttribute('data-item-name') || 'item';
                    const itemType = itemName.charAt(0).toUpperCase() + itemName.slice(1);
                    
                    // Create contextual confirmation message
                    let message = 'Are you sure you want to delete this ' + itemName + '?';
                    
                    // Add "cannot be undone" warning for all destructive actions
                    message += ' This action cannot be undone.';
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });

        // Also handle forms with @method('DELETE') - Laravel's way
        // BUT only if they're not already handled above and are actually delete forms
        document.querySelectorAll('form').forEach(function(form) {
            // Skip if already handled as delete-form class
            if (form.classList.contains('delete-form')) {
                return;
            }
            
            // Skip if form is an update form (has update-form class or PUT/PATCH method)
            if (form.classList.contains('update-form')) {
                return;
            }
            
            // Skip if form has PUT or PATCH method (update forms)
            const putMethod = form.querySelector('input[name="_method"][value="PUT"]');
            const patchMethod = form.querySelector('input[name="_method"][value="PATCH"]');
            if (putMethod || patchMethod) {
                return; // This is an update form, not a delete form
            }
            
            // Only handle forms with DELETE method that aren't update forms
            const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
            if (methodInput && !form.hasAttribute('onsubmit')) {
                form.addEventListener('submit', function(e) {
                    const itemName = form.getAttribute('data-item-name') || 'item';
                    const message = 'Are you sure you want to delete this ' + itemName + '? This action cannot be undone.';
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });

        // Fix delete buttons in dropdowns - prevent dropdown from closing
        document.querySelectorAll('.dropdown-item[type="submit"], .dropdown-item button[type="submit"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent dropdown from closing
            });
        });
    }

    /**
     * Fix modal buttons
     */
    function fixModalButtons() {
        // Fix modal open buttons
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const target = btn.getAttribute('data-bs-target') || btn.getAttribute('href');
                if (target && target.startsWith('#')) {
                    const modal = document.querySelector(target);
                    if (modal) {
                        // Ensure Bootstrap modal is initialized
                        if (typeof bootstrap !== 'undefined') {
                            const bsModal = new bootstrap.Modal(modal);
                            bsModal.show();
                        }
                    }
                }
            });
        });

        // Fix modal close buttons
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const modal = btn.closest('.modal');
                if (modal && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
            });
        });
    }

    /**
     * Fix navigation buttons
     */
    function fixNavigationButtons() {
        // Ensure all navigation links work properly
        document.querySelectorAll('a.nav-link, a.dropdown-item').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href !== '#' && !href.startsWith('javascript:')) {
                    // Let the browser handle navigation naturally
                    return true;
                }
            });
        });
    }

    /**
     * Global error handler for AJAX requests
     */
    window.handleAjaxError = function(error, defaultMessage) {
        console.error('AJAX Error:', error);
        const message = error.message || defaultMessage || 'An error occurred. Please try again.';
        
        // Show notification
        const notification = document.createElement('div');
        notification.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(function() {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };

    /**
     * Global success notification
     */
    window.showSuccessNotification = function(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);

        setTimeout(function() {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    };

})();

