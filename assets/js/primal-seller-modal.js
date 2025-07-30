/**
 * PRIMAL BLACK MARKET - SELLER PRODUCT MODAL FUNCTIONALITY
 * Enhanced modal with category integration and primal theme features
 * 
 * DEBUG MODE: Set window.PRIMAL_DEBUG = true in console for extra logging
 * DEBUGGING FEATURES: 
 * - Modal stays open after successful product creation
 * - Console logs preserved for analysis
 * - No automatic page refresh
 * - Success state clearly indicated in modal
 */

// Global variables
let CATEGORY_DATA = [];
let isVendor = false;

// Debug mode management
function enableDebugMode() {
    window.PRIMAL_DEBUG = true;
    localStorage.setItem('primal_debug', 'true');
    console.log('🔍 DEBUG MODE ENABLED - Extra logging active');
}

function disableDebugMode() {
    window.PRIMAL_DEBUG = false;
    localStorage.removeItem('primal_debug');
    console.log('🔍 DEBUG MODE DISABLED');
}

// Check if debug mode is active
function isDebugMode() {
    return window.PRIMAL_DEBUG || localStorage.getItem('primal_debug') === 'true';
}

// Debug helper function
function debugLog(...args) {
    if (isDebugMode()) {
        console.log('🔍 DEBUG:', ...args);
    }
}

// Main initialization function called from the component
function initializeSellerModal(categories) {
    console.log('Initializing seller modal with categories:', categories);
    CATEGORY_DATA = categories;
    
    // Clear any existing initialization flag
    window.sellerModalInitialized = false;
    
    // Initialize all modal functionality when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupEventListeners);
    } else {
        setupEventListeners();
    }
    
    console.log('Seller modal initialization complete');
}

// Category examples for help modal
const CATEGORY_EXAMPLES = {
    'Weapons': ['Obsidian War Spear', 'Bone Club', 'Stone Axe', 'Sharpened Flint Knife'],
    'Hunting Equipment': ['Pterodactyl Net', 'Mammoth Trap', 'Tracking Scent', 'Bone Whistle'],
    'Prehistoric Drugs': ['Healing Moss Potion', 'Strength Berries', 'Vision Mushrooms', 'Pain Relief Bark'],
    'Food': ['Smoked Dino Meat', 'Fermented Cave Fish', 'Dried Fruits', 'Bone Marrow Soup'],
    'Spices and etc.': ['Salt Crystals', 'Bone Ash Seasoning', 'Wild Herbs', 'Cave Pepper'],
    'General Equipment': ['Fire Starting Kit', 'Shelter Materials', 'Rope from Vines', 'Water Containers'],
    'Forging Materials': ['Meteor Iron', 'Dragon Scales', 'Rare Crystals', 'Hardened Clay'],
    'Clothing': ['Mammoth Hide Coat', 'Feathered Headdress', 'Bone Jewelry', 'Fur Boots'],
    'Infrastructure': ['Stone Blocks', 'Wooden Beams', 'Clay Bricks', 'Metal Reinforcements'],
    'Voodoo': ['Cursed Totems', 'Spirit Crystals', 'Mystical Powders', 'Enchanted Trinkets'],
    'Ritual Artifacts': ['Ceremonial Masks', 'Sacred Drums', 'Blessing Stones', 'Ritual Daggers'],
    'Pets': ['Trained Raptors', 'Cave Bears', 'Flying Lizards', 'Hunting Dogs']
};

function openProductModal(productData = null) {
    console.log('openProductModal called with data:', productData);
    
    const modal = document.getElementById('sellerProductModal');
    if (!modal) {
        console.error('Seller product modal not found!');
        return;
    }

    console.log('Modal element found:', modal);

    const modalTitle = document.getElementById('modal-title')?.querySelector('span');
    const submitText = document.getElementById('submit-text');
    const form = document.getElementById('product-form');
    
    if (productData) {
        // Edit mode
        if (modalTitle) modalTitle.textContent = 'Edit Product';
        if (submitText) submitText.textContent = 'Update Product';
        
        // Populate form fields
        const fields = {
            'product-id': productData.listing_id || productData.id,
            'product-title': productData.title,
            'product-category': productData.category_name || productData.category,
            'product-price': productData.price,
            'product-description': productData.description,
            'product-quantity': productData.quantity || 1,
            'product-status': productData.is_active ? '1' : '0',
            'product-tags': productData.tags || ''
        };

        Object.entries(fields).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) element.value = value;
        });
        
        if (productData.item_image) {
            showImagePreview(productData.item_image);
        }
    } else {
        // Add mode - ENSURE CLEAN STATE
        if (modalTitle) modalTitle.textContent = 'Add New Product';
        if (submitText) submitText.textContent = 'Publish Product';
        if (form) form.reset();
        hideImagePreview();
        
        // CRITICAL: Explicitly clear the product ID field for create operations
        const productIdField = document.getElementById('product-id');
        if (productIdField) {
            productIdField.value = '';
            console.log('✅ Cleared product-id field for create mode');
        }
        
        // Set default values
        const quantityInput = document.getElementById('product-quantity');
        const conditionSelect = document.getElementById('product-condition');
        const raritySelect = document.getElementById('product-rarity');
        const statusSelect = document.getElementById('product-status');
        
        if (quantityInput) quantityInput.value = 1;
        if (conditionSelect) conditionSelect.value = 'good';
        if (raritySelect) raritySelect.value = 'common';
        if (statusSelect) statusSelect.value = 'active';
        
        console.log('✅ Set default values for create mode');
    }
    
    // Remove inline display style and show modal
    modal.style.display = '';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    console.log('Modal should now be visible. Classes:', modal.classList.toString());
    
    // Focus first input
    setTimeout(() => {
        const titleInput = document.getElementById('product-title');
        if (titleInput) titleInput.focus();
    }, 100);
}

function closeProductModal() {
    const modal = document.getElementById('sellerProductModal');
    if (!modal) return;

    // Remove any debug success banners
    const debugBanner = document.getElementById('debug-success-banner');
    if (debugBanner) debugBanner.remove();
    
    // Reset modal header to original state
    const modalHeader = document.querySelector('#sellerProductModal .modal-header h2 span');
    const submitBtn = document.querySelector('#product-form button[type="submit"]');
    
    if (modalHeader) {
        modalHeader.innerHTML = 'Add New Product';
        modalHeader.style.color = '';
    }
    
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-rocket"></i> <span id="submit-text">Publish Product</span>';
        submitBtn.style.backgroundColor = '';
        submitBtn.disabled = false;
    }

    modal.classList.remove('show');
    document.body.style.overflow = '';
    
    // Enhanced form reset to prevent cross-contamination between create/edit
    const form = document.getElementById('product-form');
    if (form) {
        form.reset();
        
        // Explicitly clear key fields that might cause issues
        const criticalFields = ['product-id', 'product-title', 'product-category', 'product-price', 'product-description'];
        criticalFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.value = '';
            }
        });
        
        console.log('✅ Enhanced form reset completed');
    }
    
    hideImagePreview();
    
    // Reset character counter
    updateCharacterCount();
}

function showImagePreview(src) {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('upload-placeholder');
    const img = document.getElementById('preview-img');
    
    if (img) img.src = src;
    if (preview) preview.style.display = 'block';
    if (placeholder) placeholder.style.display = 'none';
}

function hideImagePreview() {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('upload-placeholder');
    
    if (preview) preview.style.display = 'none';
    if (placeholder) placeholder.style.display = 'block';
}

function removeImage() {
    const fileInput = document.getElementById('product-image');
    if (fileInput) fileInput.value = '';
    hideImagePreview();
}

function adjustQuantity(change) {
    const input = document.getElementById('product-quantity');
    if (!input) return;
    
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.max(1, currentValue + change);
    input.value = newValue;
}

function updateCharacterCount() {
    const textarea = document.getElementById('product-description');
    const counter = document.getElementById('char-count');
    
    if (!textarea || !counter) return;
    
    const currentLength = textarea.value.length;
    counter.textContent = currentLength;
    
    // Change color based on usage
    const maxLength = 1000;
    const percentage = (currentLength / maxLength) * 100;
    
    if (percentage > 90) {
        counter.style.color = '#dc3545';
    } else if (percentage > 75) {
        counter.style.color = '#ff8c00';
    } else {
        counter.style.color = 'rgba(255, 255, 255, 0.5)';
    }
}

function openCategoryInfoModal(categoryValue) {
    const modal = document.getElementById('category-info-modal');
    const title = document.getElementById('category-info-title');
    const description = document.getElementById('category-info-description');
    const examplesList = document.getElementById('category-examples-list');
    
    if (!modal || !title || !description || !examplesList) return;
    
    // Find category data - convert categoryValue to match our data structure
    const category = CATEGORY_DATA.find(cat => {
        const categoryKey = cat.Name.toLowerCase().replace(/\s+/g, '-');
        return categoryValue === categoryKey;
    });
    
    if (category) {
        title.textContent = `${category.Name} - Category Guide`;
        description.innerHTML = `<p>${category.Description}</p>`;
        
        // Show examples
        const examples = CATEGORY_EXAMPLES[categoryValue] || [];
        examplesList.innerHTML = examples.map(example => 
            `<div class="example-item">${example}</div>`
        ).join('');
        
        modal.classList.add('show');
    }
}

function closeCategoryInfoModal() {
    const modal = document.getElementById('category-info-modal');
    if (modal) modal.classList.remove('show');
}

function handleImageFile(file) {
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showNotification('Please select a valid image file.', 'error');
        return;
    }
    
    // Validate file size (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Image must be less than 5MB.', 'error');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        showImagePreview(e.target.result);
    };
    reader.readAsDataURL(file);
}

function validateForm() {
    console.log('🔍 Starting form validation...');
    
    const title = document.getElementById('product-title')?.value?.trim();
    const category = document.getElementById('product-category')?.value;
    const price = document.getElementById('product-price')?.value;
    const description = document.getElementById('product-description')?.value?.trim();
    
    console.log('Form values:', { title, category, price, description: description?.substring(0, 50) + '...' });
    
    if (!title) {
        console.log('❌ Validation failed: Missing title');
        showNotification('Product title is required.', 'error');
        const titleInput = document.getElementById('product-title');
        if (titleInput) titleInput.focus();
        return false;
    }
    
    if (!category) {
        console.log('❌ Validation failed: Missing category');
        showNotification('Please select a category.', 'error');
        const categorySelect = document.getElementById('product-category');
        if (categorySelect) categorySelect.focus();
        return false;
    }
    
    if (!price || parseFloat(price) <= 0) {
        console.log('❌ Validation failed: Invalid price');
        showNotification('Please enter a valid price.', 'error');
        const priceInput = document.getElementById('product-price');
        if (priceInput) priceInput.focus();
        return false;
    }
    
    if (!description || description.length < 20) {
        console.log('❌ Validation failed: Description too short');
        showNotification('Description must be at least 20 characters long.', 'error');
        const descriptionTextarea = document.getElementById('product-description');
        if (descriptionTextarea) descriptionTextarea.focus();
        return false;
    }
    
    console.log('✅ Form validation passed!');
    return true;
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    notification.innerHTML = `
        <i class="fas ${icons[type] || icons.info}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, 
            ${getNotificationColor(type)}, 
            ${getNotificationColor(type, true)});
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        border: 2px solid ${getNotificationBorder(type)};
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        z-index: 10001;
        display: flex;
        align-items: center;
        gap: 1rem;
        max-width: 400px;
        font-family: inherit;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function getNotificationColor(type, dark = false) {
    const colors = {
        success: dark ? '#198754' : '#28a745',
        error: dark ? '#b02a37' : '#dc3545',
        warning: dark ? '#d39e00' : '#ffc107',
        info: dark ? '#0c5460' : '#17a2b8'
    };
    return colors[type] || colors.info;
}

function getNotificationBorder(type) {
    const borders = {
        success: 'rgba(40, 167, 69, 0.5)',
        error: 'rgba(220, 53, 69, 0.5)',
        warning: 'rgba(255, 193, 7, 0.5)',
        info: 'rgba(23, 162, 184, 0.5)'
    };
    return borders[type] || borders.info;
}

// Setup event listeners function
function setupEventListeners() {
    console.log('🛍️ Setting up Seller Product Modal event listeners...');
    
    // Prevent multiple setups
    if (window.sellerModalInitialized) {
        console.log('Seller modal already initialized, skipping...');
        return;
    }
    window.sellerModalInitialized = true;
    
    // First check if all required elements exist
    const requiredElements = {
        'sellerProductModal': document.getElementById('sellerProductModal'),
        'product-form': document.getElementById('product-form'),
        'image-upload-area': document.getElementById('image-upload-area'),
        'product-image': document.getElementById('product-image'),
        'product-description': document.getElementById('product-description')
    };
    
    console.log('Required elements check:');
    Object.entries(requiredElements).forEach(([name, element]) => {
        console.log(`${name}: ${element ? '✅ Found' : '❌ Missing'}`);
    });

    // Image upload handling with drag & drop
    const imageUploadArea = document.getElementById('image-upload-area');
    const fileInput = document.getElementById('product-image');
    
    if (imageUploadArea && fileInput) {
        // Click to upload
        imageUploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // Drag and drop
        imageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadArea.style.borderColor = 'rgba(255, 140, 0, 0.8)';
            imageUploadArea.style.backgroundColor = 'rgba(255, 140, 0, 0.1)';
        });
        
        imageUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            imageUploadArea.style.borderColor = 'rgba(255, 140, 0, 0.4)';
            imageUploadArea.style.backgroundColor = 'rgba(0, 0, 0, 0.2)';
        });
        
        imageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleImageFile(files[0]);
            }
            imageUploadArea.style.borderColor = 'rgba(255, 140, 0, 0.4)';
            imageUploadArea.style.backgroundColor = 'rgba(0, 0, 0, 0.2)';
        });
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleImageFile(file);
            }
        });
    }
    
    // Character counter for description
    const textarea = document.getElementById('product-description');
    if (textarea) {
        textarea.addEventListener('input', updateCharacterCount);
        updateCharacterCount();
    }
    
    // Price suggestions
    document.querySelectorAll('.price-suggestion').forEach(btn => {
        btn.addEventListener('click', function() {
            const price = this.dataset.price;
            const priceInput = document.getElementById('product-price');
            if (priceInput) {
                priceInput.value = price;
                
                // Visual feedback
                this.style.background = 'rgba(255, 140, 0, 0.3)';
                setTimeout(() => {
                    this.style.background = 'rgba(255, 140, 0, 0.1)';
                }, 200);
            }
        });
    });
    
    // Category change handler with info option
    const categorySelect = document.getElementById('product-category');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedValue = this.value;
            
            // Add info button next to category if category is selected
            const existingInfo = document.querySelector('.category-info-btn');
            if (existingInfo) existingInfo.remove();
            
            if (selectedValue) {
                const infoBtn = document.createElement('button');
                infoBtn.type = 'button';
                infoBtn.className = 'category-info-btn';
                infoBtn.innerHTML = '<i class="fas fa-info-circle"></i>';
                infoBtn.title = 'View category information and examples';
                infoBtn.onclick = () => openCategoryInfoModal(selectedValue);
                
                categorySelect.parentNode.appendChild(infoBtn);
            }
        });
    }
    
    // Enhanced form submission using event delegation
    document.addEventListener('submit', function(e) {
        // Check if this is our product form
        if (e.target.id === 'product-form') {
            e.preventDefault();
            
            console.log('🚀 Product form submission triggered');
            
            const form = e.target;
            console.log('Form element:', form);
            
            // Try multiple ways to find the submit button
            let submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) {
                submitBtn = form.querySelector('.primal-btn-primary');
            }
            console.log('Submit button found via delegation:', submitBtn);
            if (!submitBtn) {
                submitBtn = document.querySelector('#product-form button[type="submit"]');
            }
            if (!submitBtn) {
                submitBtn = document.querySelector('#sellerProductModal button[type="submit"]');
            }
            
            console.log('Form element:', form);
            console.log('Submit button found via delegation:', submitBtn);
            
            if (!submitBtn) {
                console.error('Submit button still not found!');
                console.log('All buttons in modal:');
                const allModalButtons = document.querySelectorAll('#sellerProductModal button');
                allModalButtons.forEach((btn, index) => {
                    console.log(`Button ${index}:`, btn, 'Type:', btn.type, 'Classes:', btn.className, 'Text:', btn.textContent.trim());
                });
                return;
            }
            
            const originalText = submitBtn.innerHTML;
            
            // Validation
            if (!validateForm()) {
                console.log('Form validation failed');
                return;
            }
            
            console.log('Form validation passed, proceeding with submission');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';
            submitBtn.disabled = true;
            
            // Get form data with intelligent file handling
            const formData = new FormData();
            const productId = document.getElementById('product-id').value;
            
            // Add all form fields except file inputs first
            const formElements = form.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                
                if (element.type === 'file') {
                    // Handle file inputs specially - only add if file is selected
                    if (element.files && element.files.length > 0 && element.files[0].size > 0) {
                        formData.append(element.name, element.files[0]);
                        console.log(`📎 File added: ${element.name} - ${element.files[0].name} (${element.files[0].size} bytes)`);
                    } else {
                        console.log(`📎 No file selected for: ${element.name} - skipping`);
                    }
                } else if (element.name && element.type !== 'submit' && element.type !== 'button') {
                    // Add regular form fields
                    let value = element.value;
                    if (element.type === 'checkbox') {
                        value = element.checked;
                    }
                    formData.append(element.name, value);
                }
            }
            
            // Determine if this is create or update
            const action = productId ? 'update' : 'create';
            formData.append('action', action);
            
            console.log(`Submitting ${action} request for product`);
            
            // Debug: Log the status field specifically
            const statusField = document.getElementById('product-status');
            const statusValue = statusField ? statusField.value : 'NOT_FOUND';
            console.log(`Status field value: "${statusValue}"`);
            console.log(`FormData status: "${formData.get('status')}"`);
            
            // CRITICAL VALIDATION: Ensure we have the minimum required data
            const criticalChecks = {
                'action': formData.get('action'),
                'title': formData.get('title'),
                'category': formData.get('category'),
                'price': formData.get('price'),
                'description': formData.get('description')
            };
            
            let hasAllRequired = true;
            Object.entries(criticalChecks).forEach(([field, value]) => {
                const isEmpty = !value || value.trim() === '';
                if (isEmpty && field !== 'action') { // action can be empty due to fallback
                    hasAllRequired = false;
                }
            });
            
            if (!hasAllRequired) {
                console.error('Critical validation failed - missing required fields');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                showNotification('Please fill in all required fields before submitting.', 'error');
                return;
            }
            
            // For create operations, ensure we have required fields
            if (action === 'create') {
                const requiredFields = ['title', 'category', 'price', 'description'];
                let createValid = true;
                
                requiredFields.forEach(field => {
                    const value = formData.get(field);
                    if (!value || value.trim() === '') {
                        createValid = false;
                    }
                });
                
                if (!createValid) {
                    console.error('Create validation failed - missing required fields');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    showNotification('Please fill in all required fields', 'error');
                    return;
                }
            }
            
            // Submit to products handler
            fetch('/handlers/products.handler.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'  // Mark as AJAX request
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                console.log('Response OK:', response.ok);
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Response content-type:', contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, get the text to see what's wrong
                    return response.text().then(text => {
                        console.error('Server returned non-JSON response:');
                        console.error('Response text:', text);
                        console.error('Content-Type:', contentType);
                        
                        // Try to find PHP errors in the response
                        if (text.includes('Fatal error') || text.includes('Parse error') || text.includes('Warning:') || text.includes('Notice:')) {
                            console.error('🚨 PHP ERROR DETECTED IN RESPONSE!');
                            console.error('Full response:', text);
                        }
                        
                        throw new Error('Server error: Expected JSON but got HTML/PHP error: ' + text.substring(0, 1000));
                    });
                }
            })
            .then(data => {
                console.log('Response data:', data);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Show success message
                    const message = action === 'create' ? 
                        '🎉 Product created successfully! Redirecting to your account...' : 
                        '✅ Product updated successfully! Redirecting to your account...';
                    showNotification(message, 'success');
                    
                    console.log('✅ SUCCESS: Product operation completed successfully');
                    console.log('📊 Success data:', data);
                    
                    // PRODUCTION MODE: Normal user experience
                    // Close modal and redirect to product management page
                    setTimeout(() => {
                        closeProductModal();
                        
                        console.log('Redirecting to account page...');
                        console.log('Current URL:', window.location.href);
                        
                        try {
                            // Enhanced redirect logic with multiple fallbacks
                            const targetUrl = '/pages/account/index.php';
                            
                            // Method 1: Try location.href first (most compatible)
                            if (window.location.pathname.includes('/handlers/')) {
                                // If stuck on handler page, force redirect with replace
                                window.location.replace(targetUrl);
                            } else {
                                // Normal redirect
                                window.location.href = targetUrl;
                            }
                            
                        } catch (error) {
                            console.error('Primary redirect failed, trying fallbacks:', error);
                            
                            // Fallback 1: Try window.location.assign
                            try {
                                window.location.assign('/pages/account/index.php');
                            } catch (e) {
                                console.error('Assign failed, trying replace:', e);
                                
                                // Fallback 2: Try window.location.replace
                                try {
                                    window.location.replace('/pages/account/index.php');
                                } catch (e2) {
                                    console.error('Replace failed, trying history API:', e2);
                                    
                                    // Fallback 3: Try history.pushState + reload
                                    try {
                                        history.pushState(null, '', '/pages/account/index.php');
                                        window.location.reload();
                                    } catch (e3) {
                                        console.error('All redirect methods failed:', e3);
                                        // Final fallback: Show manual redirect message
                                        showNotification('✅ Product saved! Please click <a href="/pages/account/index.php" style="color: white; text-decoration: underline;">here</a> to return to your account.', 'success');
                                    }
                                }
                            }
                        }
                    }, 1500); // Give user time to see the success notification
                    
                } else {
                    // Enhanced error handling with specific messages
                    let errorMessage = data.message || 'Unknown error occurred';
                    
                    console.error('❌ OPERATION FAILED:');
                    console.error('📊 Error data:', data);
                    console.error('📝 Error message:', errorMessage);
                    
                    // Check for specific error types and provide helpful messages
                    if (errorMessage.includes('Category') && errorMessage.includes('not found')) {
                        errorMessage = '❌ Category Error: ' + errorMessage + '\n\nThis usually means the database needs to be updated. Please contact an administrator.';
                    } else if (errorMessage.includes('No categories exist')) {
                        errorMessage = '❌ Database Error: No product categories are available.\n\nPlease contact an administrator to set up the database properly.';
                    } else if (errorMessage.includes('Foreign key')) {
                        errorMessage = '❌ Database Constraint Error: There\'s a database configuration issue.\n\nPlease contact an administrator.';
                    } else if (errorMessage.includes('Missing required fields')) {
                        errorMessage = '❌ Form Error: ' + errorMessage + '\n\nPlease check that all required fields are filled out.';
                    }
                    
                    console.error('Final error message to user:', errorMessage);
                    
                    // Show error notification
                    showNotification(errorMessage, 'error');
                }
                
            })
            .catch(error => {
                console.error('Network/Fetch Error:', error.message);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Show error notification
                showNotification('❌ Network error occurred. Please try again.', 'error');
            });
        }
    });
    
    console.log('✅ Event listeners setup complete');
    
    // Add product button functionality
    const addProductBtns = document.querySelectorAll('#add-product-btn, #add-new-product-btn');
    addProductBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            console.log('Add product button clicked');
            openProductModal();
        });
    });

    // Edit product button functionality
    document.querySelectorAll('.edit-product, .edit-seller-product').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.productId;
            console.log('Edit product clicked:', productId);

            // 🔗 Fetch real product data from backend
            fetch(`/handlers/products.handler.php?action=read&id=${productId}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 🎯 Pass actual product data to modal
                        openProductModal(data.product);
                    } else {
                        alert('Product not found or could not be loaded.');
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while fetching product info.');
                });
        });
    });

    // Modal click outside to close
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'sellerProductModal') {
                    closeProductModal();
                } else if (this.id === 'category-info-modal') {
                    closeCategoryInfoModal();
                }
            }
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape to close modals
        if (e.key === 'Escape') {
            const productModal = document.getElementById('sellerProductModal');
            const categoryModal = document.getElementById('category-info-modal');
            
            if (productModal && productModal.classList.contains('show')) {
                closeProductModal();
            } else if (categoryModal && categoryModal.classList.contains('show')) {
                closeCategoryInfoModal();
            }
        }
    });

    // Show buyer actions if user is not a vendor
    if (!isVendor) {
        const buyerActions = document.getElementById('buyer-actions');
        if (buyerActions) {
            buyerActions.style.display = 'grid';
        }
    }

    console.log('✅ Seller Product Modal initialized successfully');
}

// Enhanced notification function for displaying messages
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.primal-notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `primal-notification primal-notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add notification styles if not already defined
    if (!document.querySelector('#primal-notification-styles')) {
        const style = document.createElement('style');
        style.id = 'primal-notification-styles';
        style.textContent = `
            .primal-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(10px);
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                display: flex;
                align-items: center;
                gap: 0.8rem;
                z-index: 10000;
                max-width: 400px;
                word-wrap: break-word;
                animation: slideInRight 0.3s ease;
                border-left: 4px solid var(--primal-orange);
            }
            
            .primal-notification-success {
                border-left-color: #50c878;
            }
            
            .primal-notification-error {
                border-left-color: #e74c3c;
            }
            
            .primal-notification button {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                margin-left: auto;
                padding: 0.2rem;
                border-radius: 3px;
                transition: background 0.3s ease;
            }
            
            .primal-notification button:hover {
                background: rgba(255, 255, 255, 0.1);
            }
            
            .primal-notification.persistent {
                cursor: pointer;
                border: 2px solid;
            }
            
            .primal-notification.persistent:hover {
                opacity: 0.9;
                transform: scale(1.02);
            }
            
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Persistent notification that doesn't auto-disappear and can be clicked
function showPersistentNotification(message, type = 'info', onClick = null) {
    // Remove existing notifications
    const existing = document.querySelectorAll('.primal-notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `primal-notification primal-notification-${type} persistent`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add click handler if provided
    if (onClick) {
        notification.addEventListener('click', (e) => {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I') {
                onClick();
            }
        });
        notification.title = 'Click to execute action';
    }
    
    document.body.appendChild(notification);
    
    console.log(`📢 Persistent ${type} notification shown:`, message);
    
    // Don't auto-remove persistent notifications
}

// Set category data and vendor status (called from PHP)
function setModalData(categories, vendorStatus) {
    CATEGORY_DATA = categories || [];
    isVendor = vendorStatus || false;
    console.log('Modal data set:', { categories: CATEGORY_DATA.length, isVendor });
}

// Make functions globally available
window.openProductModal = openProductModal;
window.closeProductModal = closeProductModal;
window.adjustQuantity = adjustQuantity;
window.removeImage = removeImage;
window.openCategoryInfoModal = openCategoryInfoModal;
window.closeCategoryInfoModal = closeCategoryInfoModal;
window.setModalData = setModalData;
window.initializeSellerModal = initializeSellerModal;

// Debug mode functions
window.enableDebugMode = enableDebugMode;
window.disableDebugMode = disableDebugMode;
window.isDebugMode = isDebugMode;

console.log('🎯 PRIMAL SELLER MODAL LOADED');
console.log('🔍 Debug Commands Available:');
console.log('  - enableDebugMode() : Enable extra debugging');
console.log('  - disableDebugMode() : Disable debugging'); 
console.log('  - isDebugMode() : Check if debug mode is active');
console.log('📝 Current Debug Status:', isDebugMode() ? '✅ ACTIVE' : '❌ INACTIVE');
