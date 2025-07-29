// Primal Registration JavaScript Enhancements

document.addEventListener('DOMContentLoaded', function() {
    
    // Account type selection functionality
    const accountTypeCards = document.querySelectorAll('.account-type-card');
    const accountTypeInputs = document.querySelectorAll('input[name="account_type"]');
    
    // Handle account type card clicks
    accountTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            const radioInput = this.querySelector('input[type="radio"]');
            if (radioInput && !radioInput.checked) {
                radioInput.checked = true;
                updateCardSelection();
                
                // Add visual feedback
                card.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    card.style.transform = '';
                }, 200);
            }
        });
        
        // Handle keyboard navigation
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
        
        // Make cards focusable
        card.setAttribute('tabindex', '0');
    });
    
    // Handle radio button changes
    accountTypeInputs.forEach(input => {
        input.addEventListener('change', updateCardSelection);
    });
    
    function updateCardSelection() {
        accountTypeCards.forEach(card => {
            const radioInput = card.querySelector('input[type="radio"]');
            const badge = card.querySelector('.card-badge');
            
            if (radioInput && radioInput.checked) {
                card.classList.add('selected');
                if (badge) {
                    badge.style.opacity = '1';
                    badge.style.transform = 'translateX(0)';
                }
            } else {
                card.classList.remove('selected');
                if (badge) {
                    badge.style.opacity = '0';
                    badge.style.transform = 'translateX(10px)';
                }
            }
        });
    }
    
    // Enhanced form validation
    const form = document.getElementById('register-form');
    const inputs = form.querySelectorAll('.primal-input');
    const submitButton = form.querySelector('.primal-btn-primary');
    
    // Clean up any existing validation elements from old scripts
    document.querySelectorAll('.strength-text, .validation-message, .error-message').forEach(el => {
        el.remove();
    });
    
    // Add real-time validation feedback
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', function(e) {
            // Clear error first, then validate in real-time
            clearFieldError(e.target);
            
            // Add a small delay to avoid validating on every keystroke
            clearTimeout(input.validationTimeout);
            input.validationTimeout = setTimeout(() => {
                validateFieldRealTime(e);
            }, 300); // 300ms delay for better UX
        });
    });
    
    function validateField(e) {
        const field = e.target;
        const value = field.value.trim();
        
        // Remove existing error styling
        field.classList.remove('error');
        clearFieldError(field);
        
        // Validate based on field type
        validateFieldLogic(field, value);
    }
    
    function validateFieldRealTime(e) {
        const field = e.target;
        const value = field.value.trim();
        
        // Only validate if field has content or if it's required and was previously validated
        if (value.length > 0 || field.hasAttribute('data-was-validated')) {
            field.setAttribute('data-was-validated', 'true');
            validateFieldLogic(field, value);
        }
    }
    
    function validateFieldLogic(field, value) {
        switch(field.name) {
            case 'username':
                if (value.length > 0 && value.length < 3) {
                    showFieldError(field, 'Username must be at least 3 characters');
                } else if (value.length > 0 && !/^[a-zA-Z0-9_-]+$/.test(value)) {
                    showFieldError(field, 'Username can only contain letters, numbers, _ and -');
                } else if (value.length >= 3 && /^[a-zA-Z0-9_-]+$/.test(value)) {
                    showFieldSuccess(field, 'Username looks good!');
                }
                break;
                
            case 'email':
                if (value.length > 0 && !isValidEmail(value)) {
                    showFieldError(field, 'Please enter a valid email address');
                } else if (value.length > 0 && isValidEmail(value)) {
                    showFieldSuccess(field, 'Email format is valid');
                }
                break;
                
            case 'password':
                if (value.length > 0 && value.length < 6) {
                    showFieldError(field, 'Password must be at least 6 characters');
                } else if (value.length >= 6) {
                    const strength = calculatePasswordStrength(value);
                    if (strength >= 3) {
                        showFieldSuccess(field, 'Strong password!');
                    } else if (strength >= 2) {
                        showFieldWarning(field, 'Good password, consider adding more variety');
                    } else {
                        showFieldWarning(field, 'Password could be stronger');
                    }
                    
                    // Also revalidate confirm password if it exists
                    const confirmPassword = form.querySelector('input[name="confirm_password"]');
                    if (confirmPassword && confirmPassword.value) {
                        setTimeout(() => {
                            validateFieldLogic(confirmPassword, confirmPassword.value);
                        }, 50);
                    }
                }
                break;
                
            case 'confirm_password':
                const password = form.querySelector('input[name="password"]').value;
                if (value.length > 0 && value !== password) {
                    showFieldError(field, 'Passwords do not match');
                } else if (value.length > 0 && value === password && password.length >= 6) {
                    showFieldSuccess(field, 'Passwords match!');
                }
                break;
        }
    }
    
    function clearFieldError(field) {
        field.classList.remove('error', 'success', 'warning');
        removeFieldFeedback(field);
        
        // Also remove any other validation elements that might exist
        const parentNode = field.parentNode;
        parentNode.querySelectorAll('.field-error, .strength-text, .validation-message, .error-message').forEach(el => {
            el.remove();
        });
    }
    
    function clearFieldErrorEvent(e) {
        clearFieldError(e.target);
    }
    
    function showFieldError(field, message) {
        field.classList.remove('success', 'warning');
        field.classList.add('error');
        showFieldFeedback(field, message, 'error');
    }
    
    function showFieldSuccess(field, message) {
        field.classList.remove('error', 'warning');
        field.classList.add('success');
        showFieldFeedback(field, message, 'success');
    }
    
    function showFieldWarning(field, message) {
        field.classList.remove('error', 'success');
        field.classList.add('warning');
        showFieldFeedback(field, message, 'warning');
    }
    
    function showFieldFeedback(field, message, type) {
        // Remove any existing feedback elements first
        const parentNode = field.parentNode;
        parentNode.querySelectorAll('.field-feedback, .field-error, .strength-text, .validation-message, .error-message').forEach(el => {
            el.remove();
        });
        
        // Create or update feedback element
        let feedbackElement = document.createElement('div');
        feedbackElement.className = `field-feedback field-feedback-${type}`;
        parentNode.appendChild(feedbackElement);
        
        // Add icon based on type
        let icon = '';
        switch(type) {
            case 'error':
                icon = '❌ ';
                break;
            case 'success':
                icon = '✅ ';
                break;
            case 'warning':
                icon = '⚠️ ';
                break;
        }
        feedbackElement.textContent = icon + message;
        feedbackElement.style.display = 'block';
    }
    
    function removeFieldFeedback(field) {
        const feedbackElement = field.parentNode.querySelector('.field-feedback');
        if (feedbackElement) {
            feedbackElement.style.display = 'none';
        }
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Enhanced form submission - prevent HTML5 validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Manually validate all fields to avoid HTML5 validation errors
        let isValid = true;
        
        // Check basic field validation
        inputs.forEach(input => {
            const event = new Event('blur');
            input.dispatchEvent(event);
            if (input.classList.contains('error')) {
                isValid = false;
            }
        });
        
        // Check if account type is selected
        const selectedAccountType = form.querySelector('input[name="account_type"]:checked');
        if (!selectedAccountType) {
            showNotification('Please select an account type', 'error');
            return;
        }
        
        // Check terms agreement (manual validation to avoid browser validation)
        const termsCheckbox = form.querySelector('input[name="terms"]');
        if (termsCheckbox && !termsCheckbox.checked) {
            showNotification('You must agree to the Terms of Service and Privacy Policy', 'error');
            return;
        }
        
        // Check all required fields manually
        const requiredFields = form.querySelectorAll('input[required]');
        requiredFields.forEach(field => {
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    showNotification('Please check the required terms and conditions', 'error');
                    isValid = false;
                }
            } else if (!field.value.trim()) {
                showFieldError(field, 'This field is required');
                isValid = false;
            } else {
                // Validate field content
                validateFieldLogic(field, field.value.trim());
                if (field.classList.contains('error')) {
                    isValid = false;
                }
            }
        });
        
        if (!isValid) {
            showNotification('Please fix the errors below', 'error');
            scrollToFirstError();
            return;
        }
        
        submitRegistration();
    });
    
    function submitRegistration() {
        const formData = new FormData(form);
        formData.append('action', 'register');
        
        // Show loading state
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Creating Account...';
        submitButton.disabled = true;
        submitButton.classList.add('loading');
        
        fetch('/handlers/auth.handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, get text and log it for debugging
                return response.text().then(text => {
                    console.error('Server returned non-JSON response:', text);
                    throw new Error('Server error: Expected JSON response but received: ' + text.substring(0, 200));
                });
            }
        })
        .then(data => {
            if (data.success) {
                showNotification('Account created successfully! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = '/pages/account/';
                }, 1500);
            } else {
                showNotification(data.error || 'Registration failed. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Registration error:', error);
            let errorMessage = 'Registration failed. Please try again.';
            if (error.message && error.message.includes('Server error:')) {
                errorMessage = 'Server error occurred. Please check your input and try again.';
            }
            showNotification(errorMessage, 'error');
        })
        .finally(() => {
            // Reset button state
            submitButton.textContent = originalText;
            submitButton.disabled = false;
            submitButton.classList.remove('loading');
        });
    }
    
    // Notification system
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);
    }
    
    // Custom checkbox handling
    const checkboxGroups = document.querySelectorAll('.checkbox-group');
    
    checkboxGroups.forEach(group => {
        const checkbox = group.querySelector('input[type="checkbox"]');
        const checkmark = group.querySelector('.checkmark');
        
        if (!checkbox || !checkmark) return;
        
        // Initialize visual state
        updateCheckboxVisual(checkbox, checkmark);
        
        // Handle clicks on the entire group
        group.addEventListener('click', function(e) {
            // If the click is on the checkbox itself, let it handle naturally
            if (e.target === checkbox) {
                return;
            }
            
            // If click is on other parts of the group, toggle the checkbox
            e.preventDefault();
            checkbox.checked = !checkbox.checked;
            
            // Trigger change event for any listeners
            const changeEvent = new Event('change', { bubbles: true });
            checkbox.dispatchEvent(changeEvent);
            
            // Update visual immediately
            updateCheckboxVisual(checkbox, checkmark);
        });
        
        // Handle direct checkbox changes (keyboard, programmatic)
        checkbox.addEventListener('change', function(e) {
            updateCheckboxVisual(this, checkmark);
        });
        
        // Handle keyboard accessibility
        checkbox.addEventListener('keydown', function(e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                this.checked = !this.checked;
                updateCheckboxVisual(this, checkmark);
                
                // Trigger change event
                const changeEvent = new Event('change', { bubbles: true });
                this.dispatchEvent(changeEvent);
            }
        });
    });
    
    // Function to update checkbox visual state
    function updateCheckboxVisual(checkbox, checkmark) {
        if (checkbox.checked) {
            checkmark.style.background = 'linear-gradient(135deg, #ff8c00, #e67e00)';
            checkmark.style.borderColor = '#ff8c00';
        } else {
            checkmark.style.background = '';
            checkmark.style.borderColor = 'rgba(255, 140, 0, 0.5)';
        }
    }
    
    // Progressive enhancement for password strength
    const passwordInput = form.querySelector('input[name="password"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });
    }
    
    function updatePasswordStrength(password) {
        // This could be enhanced with a password strength indicator
        const strength = calculatePasswordStrength(password);
        
        // You could add visual feedback here
        // For now, we just validate minimum requirements
    }
    
    function calculatePasswordStrength(password) {
        let score = 0;
        
        // Length
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;
        
        // Character types
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        
        return score;
    }
    
    // Add smooth scrolling to form errors
    function scrollToFirstError() {
        const firstError = form.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Focus the field and give it a subtle shake animation
            firstError.focus();
            firstError.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                firstError.style.animation = '';
            }, 500);
        }
    }
    
    // Initialize default selection (buyer)
    const defaultSelection = form.querySelector('input[name="account_type"][value="buyer"]');
    if (defaultSelection && !form.querySelector('input[name="account_type"]:checked')) {
        defaultSelection.checked = true;
        updateCardSelection();
    }
    
    // Add loading styles if not already defined
    if (!document.querySelector('#registration-styles')) {
        const style = document.createElement('style');
        style.id = 'registration-styles';
        style.textContent = `
            .primal-input.error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2) !important;
                background-color: rgba(220, 53, 69, 0.05) !important;
            }
            
            .primal-input.success {
                border-color: #28a745 !important;
                box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2) !important;
                background-color: rgba(40, 167, 69, 0.05) !important;
            }
            
            .primal-input.warning {
                border-color: #ffc107 !important;
                box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2) !important;
                background-color: rgba(255, 193, 7, 0.05) !important;
            }
            
            .field-feedback {
                font-size: 0.8rem;
                margin-top: 0.5rem;
                display: none;
                animation: fadeIn 0.3s ease;
                padding: 0.3rem 0.5rem;
                border-radius: 4px;
                font-weight: 500;
            }
            
            .field-feedback-error {
                color: #dc3545;
                background-color: rgba(220, 53, 69, 0.1);
                border-left: 3px solid #dc3545;
            }
            
            .field-feedback-success {
                color: #28a745;
                background-color: rgba(40, 167, 69, 0.1);
                border-left: 3px solid #28a745;
            }
            
            .field-feedback-warning {
                color: #856404;
                background-color: rgba(255, 193, 7, 0.1);
                border-left: 3px solid #ffc107;
            }
            
            .primal-btn-primary.loading {
                opacity: 0.7;
                cursor: not-allowed;
            }
            
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(0, 0, 0, 0.9);
                backdrop-filter: blur(10px);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 0.8rem;
                z-index: 10000;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.3s ease;
                max-width: 400px;
                word-wrap: break-word;
            }
            
            .notification-success {
                border-left: 4px solid #50c878;
            }
            
            .notification-error {
                border-left: 4px solid #dc3545;
            }
            
            .notification-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                margin-left: auto;
                padding: 0.2rem;
                border-radius: 3px;
                transition: background 0.3s ease;
            }
            
            .notification-close:hover {
                background: rgba(255, 255, 255, 0.1);
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
            
            @media (max-width: 768px) {
                .notification {
                    right: 10px;
                    left: 10px;
                    max-width: none;
                }
                
                .field-feedback {
                    font-size: 0.75rem;
                    padding: 0.25rem 0.4rem;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    console.log('Primal Registration enhancements loaded');
});
