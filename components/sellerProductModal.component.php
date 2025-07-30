<?php
require_once BASE_PATH . '/bootstrap.php';

// Load categories from database
try {
    require_once BASE_PATH . '/utils/DatabaseService.util.php';
    $db = DatabaseService::getInstance();
    $categories = $db->getCategories();
} catch (Exception $e) {
    error_log("Error loading categories: " . $e->getMessage());
    // Fallback to static data if database fails
    $categories = require_once DUMMIES_PATH . '/categories.staticData.php';
}
?>

<!-- Include Modal CSS -->
<link rel="stylesheet" href="/assets/css/primal-seller-modal.css">

<!-- Enhanced Product Management Modal for Sellers -->
<div id="sellerProductModal" class="modal">
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
        
        <form id="product-form" class="modal-body" action="/handlers/products.handler.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="product-id" name="id" value="">
            <input type="hidden" name="redirect_to" value="/pages/account/index.php">
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="product-title">
                        <i class="fas fa-tag"></i> Product Title *
                    </label>
                    <input type="text" id="product-title" name="title" required 
                           placeholder="Enter descriptive product title (e.g., 'Obsidian War Spear - Razor Sharp')" maxlength="100">
                    <small class="form-help">Be specific and descriptive - include material, condition, and unique features (max 100 characters)</small>
                </div>
                
                <div class="form-group">
                    <label for="product-category">
                        <i class="fas fa-list"></i> Category *
                    </label>
                    <select id="product-category" name="category" required>
                        <option value="">🏷️ Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <?php
                            // Handle both database format (name, categories_id) and static format (Name)
                            $categoryName = $category['name'] ?? $category['Name'] ?? '';
                            $categoryDesc = $category['description'] ?? $category['Description'] ?? '';
                            ?>
                            <option value="<?php echo htmlspecialchars($categoryName); ?>">
                                <?php 
                                // Add category-specific icons
                                $icon = match($categoryName) {
                                    'Weapons' => '⚔️',
                                    'Hunting Equipment' => '🏹',
                                    'Prehistoric Drugs' => '🧪',
                                    'Food' => '🍖',
                                    'Spices and etc.' => '🌿',
                                    'General Equipment' => '🔧',
                                    'Forging Materials' => '⛏️',
                                    'Clothing' => '👘',
                                    'Infrastructure' => '🏗️',
                                    'Voodoo' => '🔮',
                                    'Ritual Artifacts' => '📿',
                                    'Pets' => '🐺',
                                    default => '📦'
                                };
                                echo $icon . ' ' . htmlspecialchars($categoryName); 
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-help">Choose the category that best describes your product</small>
                </div>
                
                <div class="form-group">
                    <label for="product-price">
                        <i class="fas fa-dollar-sign"></i> Price (USD) *
                    </label>
                    <div class="price-input-wrapper">
                        <input type="number" id="product-price" name="price" required 
                               min="0.01" step="0.01" placeholder="0.00">
                        <div class="price-suggestions">
                            <button type="button" class="price-suggestion" data-price="9.99">$9.99</button>
                            <button type="button" class="price-suggestion" data-price="19.99">$19.99</button>
                            <button type="button" class="price-suggestion" data-price="49.99">$49.99</button>
                        </div>
                    </div>
                    <small class="form-help">Set competitive pricing for your primal goods</small>
                </div>
                
                <div class="form-group">
                    <label for="product-quantity">
                        <i class="fas fa-boxes"></i> Stock Quantity *
                    </label>
                    <div class="quantity-controls">
                        <button type="button" class="qty-btn minus" onclick="adjustQuantity(-1)">-</button>
                        <input type="number" id="product-quantity" name="stock" 
                               min="1" value="1" placeholder="1" required>
                        <button type="button" class="qty-btn plus" onclick="adjustQuantity(1)">+</button>
                    </div>
                    <small class="form-help">How many items you have in stock</small>
                </div>
                
                <div class="form-group full-width">
                    <label for="product-description">
                        <i class="fas fa-scroll"></i> Product Description *
                    </label>
                    <div class="description-wrapper">
                        <textarea id="product-description" name="description" required 
                                  placeholder="Describe your primal offering in detail... Include crafting method, materials used, condition, origin story, and any special properties or uses." 
                                  rows="4" maxlength="1000"></textarea>
                        <div class="char-counter">
                            <span id="char-count">0</span>/1000 characters
                        </div>
                    </div>
                    <small class="form-help">Paint a vivid picture of your item - buyers love detailed descriptions!</small>
                </div>
                
                <div class="form-group full-width">
                    <label for="product-image">
                        <i class="fas fa-camera"></i> Product Images
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
                            <p>📸 Upload Product Image</p>
                            <small>PNG, JPG, WebP up to 5MB • High quality images get more views!</small>
                        </div>
                        <input type="file" id="product-image" name="images" 
                               accept="image/*" style="display: none;">
                    </div>
                    <div class="image-tips">
                        <small>� <strong>Pro Tips:</strong> Use natural lighting, show multiple angles, include size reference objects</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="product-status">
                        <i class="fas fa-toggle-on"></i> Product Status
                    </label>
                    <select id="product-status" name="status">
                        <option value="active" selected>✅ Active - Visible to buyers</option>
                        <option value="inactive">❌ Inactive - Hidden from shop</option>
                    </select>
                    <small class="form-help">Active products appear in search results and shop listings</small>
                </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="product-tags">
                        <i class="fas fa-hashtag"></i> Tags (Optional)
                    </label>
                    <input type="text" id="product-tags" name="tags" 
                           placeholder="bone, handcrafted, sharp, hunting, warrior (separate with commas)">
                    <small class="form-help">Add relevant tags to help buyers find your item (not stored in database yet)</small>
                </div>
            </div>
            
            <div class="modal-footer">
                <div class="footer-info">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        Your listing will be reviewed and published within 24 hours
                    </small>
                </div>
                <div class="footer-actions">
                    <button type="button" class="primal-btn-secondary" onclick="closeProductModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="primal-btn-primary">
                        <i class="fas fa-rocket"></i> 
                        <span id="submit-text">Publish Product</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Category Information Modal -->
<div id="category-info-modal" class="modal" style="display: none;">
    <div class="modal-content category-info-content">
        <div class="modal-header">
            <h2>
                <i class="fas fa-info-circle"></i>
                <span id="category-info-title">Category Information</span>
            </h2>
            <button class="modal-close" onclick="closeCategoryInfoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="category-info-description"></div>
            <div class="category-examples">
                <h4><i class="fas fa-lightbulb"></i> Example Items:</h4>
                <div id="category-examples-list"></div>
            </div>
        </div>
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

<!-- Include Modal JavaScript -->
<script src="/assets/js/primal-seller-modal.js"></script>

<script>
// Initialize modal with category data when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Seller modal component loaded');
    
    const categories = <?php echo json_encode($categories); ?>;
    
    // Initialize the modal with category data
    if (typeof initializeSellerModal === 'function') {
        initializeSellerModal(categories);
    } else {
        console.error('initializeSellerModal function not found - check primal-seller-modal.js');
    }
});
</script>
