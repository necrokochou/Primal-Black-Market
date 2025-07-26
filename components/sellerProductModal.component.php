<!-- Product Management Modal for Sellers -->
<div id="product-modal" class="modal" style="display: none;">
    <div class="modal-content primal-card">
        <div class="modal-header">
            <h2 id="modal-title">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Product</span>
            </h2>
            <button class="modal-close" onclick="closeProductModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="product-form" class="modal-body">
            <input type="hidden" id="product-id" name="product_id" value="">
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="product-title">
                        <i class="fas fa-tag"></i> Product Title *
                    </label>
                    <input type="text" id="product-title" name="title" required 
                           placeholder="Enter descriptive product title" maxlength="100">
                    <small class="form-help">Be specific and descriptive (max 100 characters)</small>
                </div>
                
                <div class="form-group">
                    <label for="product-category">
                        <i class="fas fa-list"></i> Category *
                    </label>
                    <select id="product-category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="clothing">Clothing & Accessories</option>
                        <option value="food">Food & Sustenance</option>
                        <option value="forging-materials">Forging Materials</option>
                        <option value="hunting-materials">Hunting Materials</option>
                        <option value="infrastructure">Infrastructure</option>
                        <option value="pets">Pets & Companions</option>
                        <option value="prehistoric-drugs">Prehistoric Remedies</option>
                        <option value="ritual-artifacts">Ritual Artifacts</option>
                        <option value="spices-etc">Spices & Seasonings</option>
                        <option value="voodoo">Voodoo Items</option>
                        <option value="weapons">Weapons & Tools</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="product-price">
                        <i class="fas fa-dollar-sign"></i> Price *
                    </label>
                    <input type="number" id="product-price" name="price" required 
                           min="0.01" step="0.01" placeholder="0.00">
                    <small class="form-help">Price in USD</small>
                </div>
                
                <div class="form-group full-width">
                    <label for="product-description">
                        <i class="fas fa-align-left"></i> Description *
                    </label>
                    <textarea id="product-description" name="description" required 
                              placeholder="Describe your product in detail..." 
                              rows="4" maxlength="500"></textarea>
                    <small class="form-help">Detailed description (max 500 characters)</small>
                </div>
                
                <div class="form-group">
                    <label for="product-quantity">
                        <i class="fas fa-boxes"></i> Quantity Available
                    </label>
                    <input type="number" id="product-quantity" name="quantity" 
                           min="1" value="1" placeholder="1">
                    <small class="form-help">How many items you have</small>
                </div>
                
                <div class="form-group">
                    <label for="product-condition">
                        <i class="fas fa-star"></i> Condition
                    </label>
                    <select id="product-condition" name="condition">
                        <option value="new">Brand New</option>
                        <option value="excellent">Excellent</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                    </select>
                </div>
                
                <div class="form-group full-width">
                    <label for="product-image">
                        <i class="fas fa-camera"></i> Product Image
                    </label>
                    <div class="image-upload-area" id="image-upload-area">
                        <div class="image-preview" id="image-preview" style="display: none;">
                            <img id="preview-img" src="" alt="Product preview">
                            <button type="button" class="remove-image" onclick="removeImage()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="upload-placeholder" id="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload product image</p>
                            <small>PNG, JPG up to 5MB</small>
                        </div>
                        <input type="file" id="product-image" name="image" 
                               accept="image/*" style="display: none;">
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label class="checkbox-label">
                        <input type="checkbox" id="product-featured" name="featured">
                        <span class="checkmark"></span>
                        <span class="checkbox-text">
                            <i class="fas fa-star"></i> Feature this product 
                            <small>(Featured products appear first in search results)</small>
                        </span>
                    </label>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="primal-btn-secondary" onclick="closeProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="primal-btn-primary">
                    <i class="fas fa-save"></i> 
                    <span id="submit-text">Save Product</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Buyer Quick Actions Panel -->
<div id="buyer-actions" class="buyer-quick-actions" style="display: none;">
    <div class="quick-action-card primal-card">
        <h3><i class="fas fa-shopping-cart"></i> Quick Actions</h3>
        <div class="action-buttons">
            <a href="/pages/shop" class="action-btn">
                <i class="fas fa-store"></i>
                <span>Browse All Products</span>
            </a>
            <a href="/pages/shop?category=featured" class="action-btn">
                <i class="fas fa-star"></i>
                <span>Featured Items</span>
            </a>
            <a href="/pages/shop?sort=newest" class="action-btn">
                <i class="fas fa-clock"></i>
                <span>New Arrivals</span>
            </a>
            <a href="/pages/cart" class="action-btn">
                <i class="fas fa-shopping-bag"></i>
                <span>View Cart</span>
                <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
        
        <div class="category-quick-links">
            <h4>Popular Categories</h4>
            <div class="category-links">
                <a href="/pages/shop?category=weapons" class="category-link">
                    <i class="fas fa-sword"></i> Weapons
                </a>
                <a href="/pages/shop?category=clothing" class="category-link">
                    <i class="fas fa-tshirt"></i> Clothing
                </a>
                <a href="/pages/shop?category=food" class="category-link">
                    <i class="fas fa-apple-alt"></i> Food
                </a>
                <a href="/pages/shop?category=pets" class="category-link">
                    <i class="fas fa-paw"></i> Pets
                </a>
            </div>
        </div>
    </div>
    
    <div class="recommendations-card primal-card">
        <h3><i class="fas fa-thumbs-up"></i> Recommended for You</h3>
        <div class="recommendation-list" id="recommendations">
            <div class="recommendation-item">
                <div class="rec-image">
                    <i class="fas fa-box"></i>
                </div>
                <div class="rec-info">
                    <h4>Discover Products</h4>
                    <p>Start shopping to get personalized recommendations</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Product Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal.show {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    background: linear-gradient(135deg, rgba(127, 79, 36, 0.95), rgba(101, 109, 74, 0.95));
    border: 2px solid rgba(255, 140, 0, 0.3);
    border-radius: 15px;
    overflow: hidden;
    transform: translateY(50px);
    transition: transform 0.3s ease;
}

.modal.show .modal-content {
    transform: translateY(0);
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid rgba(255, 140, 0, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(127, 79, 36, 0.3);
}

.modal-header h2 {
    color: #ff8c00;
    font-family: 'Cinzel', serif;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.modal-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.3s ease;
    padding: 0.5rem;
    border-radius: 50%;
}

.modal-close:hover {
    color: #ff8c00;
    background: rgba(255, 140, 0, 0.1);
}

.modal-body {
    padding: 2rem;
    max-height: calc(90vh - 200px);
    overflow-y: auto;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    color: #ff8c00;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    background: rgba(0, 0, 0, 0.3);
    border: 2px solid rgba(255, 140, 0, 0.2);
    border-radius: 8px;
    color: white;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff8c00;
    box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
}

.form-help {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
    margin-top: 0.3rem;
    display: block;
}

/* Image Upload Styles */
.image-upload-area {
    border: 2px dashed rgba(255, 140, 0, 0.3);
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.image-upload-area:hover {
    border-color: rgba(255, 140, 0, 0.5);
    background: rgba(255, 140, 0, 0.05);
}

.upload-placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.upload-placeholder i {
    font-size: 2rem;
    color: #ff8c00;
    margin-bottom: 1rem;
}

.image-preview {
    position: relative;
    display: inline-block;
}

.image-preview img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 10px;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Checkbox Styles */
.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
    cursor: pointer;
    color: rgba(255, 255, 255, 0.9);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-label .checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 140, 0, 0.5);
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
    margin-top: 0.1rem;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: linear-gradient(135deg, #ff8c00, #e67e00);
    border-color: #ff8c00;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.checkbox-text small {
    display: block;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
    margin-top: 0.2rem;
}

.modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid rgba(255, 140, 0, 0.2);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    background: rgba(127, 79, 36, 0.3);
}

/* Buyer Quick Actions */
.buyer-quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.quick-action-card {
    padding: 1.5rem;
}

.quick-action-card h3 {
    color: #ff8c00;
    font-family: 'Cinzel', serif;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.8rem;
    margin-bottom: 1.5rem;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: rgba(255, 140, 0, 0.1);
    border: 1px solid rgba(255, 140, 0, 0.2);
    border-radius: 10px;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.action-btn:hover {
    background: rgba(255, 140, 0, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 140, 0, 0.2);
}

.action-btn i {
    font-size: 1.5rem;
    color: #ff8c00;
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.category-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.8rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 6px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.category-link:hover {
    background: rgba(255, 140, 0, 0.1);
    color: #ff8c00;
}

.recommendations-card {
    padding: 1.5rem;
}

.recommendation-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    margin-bottom: 0.8rem;
}

.rec-image {
    width: 50px;
    height: 50px;
    background: rgba(255, 140, 0, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ff8c00;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.rec-info h4 {
    color: #ff8c00;
    margin: 0 0 0.3rem 0;
    font-size: 0.9rem;
}

.rec-info p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    font-size: 0.8rem;
    line-height: 1.3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .buyer-quick-actions {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
    
    .category-links {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Product Modal JavaScript
function openProductModal(productData = null) {
    const modal = document.getElementById('product-modal');
    const modalTitle = document.getElementById('modal-title').querySelector('span');
    const submitText = document.getElementById('submit-text');
    const form = document.getElementById('product-form');
    
    if (productData) {
        // Edit mode
        modalTitle.textContent = 'Edit Product';
        submitText.textContent = 'Update Product';
        
        // Populate form fields
        document.getElementById('product-id').value = productData.id;
        document.getElementById('product-title').value = productData.title;
        document.getElementById('product-category').value = productData.category;
        document.getElementById('product-price').value = productData.price;
        document.getElementById('product-description').value = productData.description;
        document.getElementById('product-quantity').value = productData.quantity || 1;
        document.getElementById('product-condition').value = productData.condition || 'new';
        document.getElementById('product-featured').checked = productData.featured || false;
        
        if (productData.image) {
            showImagePreview(productData.image);
        }
    } else {
        // Add mode
        modalTitle.textContent = 'Add New Product';
        submitText.textContent = 'Save Product';
        form.reset();
        hideImagePreview();
    }
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    const modal = document.getElementById('product-modal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    
    // Reset form
    document.getElementById('product-form').reset();
    hideImagePreview();
}

function showImagePreview(src) {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('upload-placeholder');
    const img = document.getElementById('preview-img');
    
    img.src = src;
    preview.style.display = 'block';
    placeholder.style.display = 'none';
}

function hideImagePreview() {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('upload-placeholder');
    
    preview.style.display = 'none';
    placeholder.style.display = 'block';
}

function removeImage() {
    const fileInput = document.getElementById('product-image');
    fileInput.value = '';
    hideImagePreview();
}

// Initialize product modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Image upload handling
    const imageUploadArea = document.getElementById('image-upload-area');
    const fileInput = document.getElementById('product-image');
    
    imageUploadArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                showImagePreview(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Product form submission
    document.getElementById('product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('.primal-btn-primary');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;
        
        // Simulate API call (replace with actual implementation)
        setTimeout(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Show success message
            showNotification('Product saved successfully!', 'success');
            
            // Close modal
            closeProductModal();
            
            // Refresh products list (implement based on your needs)
            // refreshProductsList();
        }, 2000);
    });
    
    // Show buyer actions if user is not a vendor
    const isVendor = <?php echo json_encode($isVendor); ?>;
    if (!isVendor) {
        const buyerActions = document.getElementById('buyer-actions');
        if (buyerActions) {
            buyerActions.style.display = 'grid';
        }
    }
    
    // Add product button functionality
    const addProductBtn = document.getElementById('add-product-btn');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => openProductModal());
    }
    
    // Edit product button functionality
    document.querySelectorAll('.edit-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            // Fetch product data and open modal (implement based on your needs)
            openProductModal({
                id: productId,
                title: 'Sample Product',
                category: 'weapons',
                price: '99.99',
                description: 'Sample description',
                quantity: 1,
                condition: 'new',
                featured: false
            });
        });
    });
});

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
