<?php
// Check if user is logged in
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login/index.php');
    exit;
}

require_once __DIR__ . '/../../layouts/header.php';

// Get user data from session
$user = $_SESSION['user'];
$username = $user['username'];
$alias = $user['alias'] ?? $username;
$email = $user['email'] ?? '';
$trustLevel = $user['trust_level'] ?? 0;
$isVendor = $user['is_vendor'] ?? false;
$isAdmin = $user['is_admin'] ?? false;
// Get user's listings from database (if they are a vendor)
$userListings = [];
if ($isVendor) {
    try {
        require_once BASE_PATH . '/bootstrap.php';
        require_once UTILS_PATH . '/DatabaseService.util.php';
        $db = DatabaseService::getInstance();
        // Note: This would require a method to get listings by vendor ID
        // For now, we'll show empty array until that method is implemented
        $userListings = [];
    } catch (Exception $e) {
        error_log("Database error getting user listings: " . $e->getMessage());
        $userListings = [];
    }
}

require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Account Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-account.css">

<main class="primal-account-bg min-vh-100">
    <div class="account-container">
        <!-- Account Header -->
        <div class="account-header primal-card <?php echo $isAdmin ? 'admin-header' : ''; ?>">
            <div class="account-avatar">
                <i class="fas fa-<?php echo $isAdmin ? 'shield-alt' : ($isVendor ? 'store' : 'user-circle'); ?>"></i>
            </div>
            <div class="account-info">
                <h1 class="account-name">
                    <?php
                    echo htmlspecialchars(
                        $alias === $username ? $username : "{$username} ({$alias})"
                    );
                    ?>
                </h1>
                <p class="account-type">
                    <?php
                    if ($isAdmin) {
                        echo '<i class="fas fa-crown"></i> Administrator Account';
                    } elseif ($isVendor) {
                        echo '<i class="fas fa-store"></i> Vendor Account';
                    } else {
                        echo '<i class="fas fa-user"></i> Member Account';
                    }
                    ?>
                </p>
                <span class="account-status active">Active</span>
                <p class="account-trust">
                    <i class="fas fa-star"></i> Trust Level: <?php echo number_format($trustLevel, 1); ?>
                    <?php if ($isAdmin): ?>
                        <span style="color: var(--primal-red); margin-left: 1rem;">
                            <i class="fas fa-shield-alt"></i> Administrative Privileges
                        </span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="account-actions">
                <?php if ($isAdmin): ?>
                    <a href="/pages/admin/index.php" class="primal-btn-secondary">
                        <i class="fas fa-shield-alt"></i> Admin Dashboard
                    </a>
                <?php endif; ?>
                <a href="/handlers/logout.handler.php" class="primal-btn-danger" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Account Navigation -->
        <div class="account-nav primal-card">
            <button class="nav-tab active" data-tab="products">
                <i class="fas fa-box"></i>
                <?php echo $isVendor ? 'My Products' : 'My Purchases'; ?>
            </button>
            <?php if ($isVendor): ?>
                <button class="nav-tab" data-tab="product-management">
                    <i class="fas fa-cogs"></i> Product Management
                </button>
            <?php endif; ?>
            <button class="nav-tab" data-tab="history">
                <i class="fas fa-history"></i>
                <?php echo $isVendor ? 'Sales History' : 'Purchase History'; ?>
            </button>
            <button class="nav-tab" data-tab="settings">
                <i class="fas fa-cog"></i> Account Settings
            </button>
            <?php if ($isAdmin): ?>
                <button class="nav-tab" data-tab="admin-tools">
                    <i class="fas fa-tools"></i> Admin Tools
                </button>
            <?php endif; ?>
        </div>

        <!-- Products/Purchases Tab -->
        <div id="products-content" class="tab-content active">
            <div class="section-header">
                <h2>
                    <i class="fas fa-<?php echo $isVendor ? 'store' : 'shopping-bag'; ?>"></i>
                    <?php echo $isVendor ? 'My Products' : 'My Purchases'; ?>
                </h2>
                <?php if ($isVendor): ?>
                    <button class="primal-btn-primary" id="add-product-btn">
                        <i class="fas fa-plus"></i> Add New Product
                    </button>
                <?php endif; ?>
            </div>
            <?php if ($isVendor): ?>
                <!-- Vendor Products Grid -->
                <div class="my-products-grid">
                    <?php if (empty($userListings)): ?>
                        <div class="empty-state primal-card">
                            <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
                                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <h3 style="color: var(--primal-beige); margin-bottom: 1rem;">No Products Yet</h3>
                                <p>Start building your primal marketplace by adding your first product.</p>
                                <button class="primal-btn-primary" style="margin-top: 1rem;" onclick="document.getElementById('add-product-btn').click();">
                                    <i class="fas fa-plus"></i> Add Your First Product
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($userListings, 0, 6) as $listing): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($listing['item_image'] ?? '/assets/images/default-product.png'); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" loading="lazy">
                                    <div class="product-status <?php echo $listing['is_active'] ? 'active' : 'inactive'; ?>"></div>
                                </div>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                                    <p class="product-category"><?php echo htmlspecialchars($listing['category']); ?></p>
                                    <p class="product-price">$<?php echo number_format($listing['price'], 2); ?></p>
                                    <p class="product-description"><?php echo htmlspecialchars($listing['description']); ?></p>
                                </div>
                                <div class="product-actions">
                                    <button class="primal-btn-secondary edit-product" data-product-id="<?php echo $listing['listing_id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="primal-btn-danger remove-product" data-product-id="<?php echo $listing['listing_id']; ?>">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Member Purchases -->
                <div class="purchases-section primal-card">
                    <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
                        <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <h3 style="color: var(--primal-beige); margin-bottom: 1rem;">No Purchases Yet</h3>
                        <p>Explore the primal marketplace and discover unique items.</p>
                        <a href="/pages/shop" class="primal-btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-store"></i> Browse Products
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($isVendor): ?>
            <!-- Product Management Tab (Only for Sellers) -->
            <div id="product-management-content" class="tab-content">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-cogs"></i> Product Management
                    </h2>
                    <div class="section-filters">
                        <input type="text" id="seller-product-search" class="search-input" placeholder="Search my products...">
                        <select id="seller-product-category-filter" class="filter-select">
                            <option value="all">All Categories</option>
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
                        <select id="seller-product-status-filter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button class="primal-btn-primary" id="add-new-product-btn">
                            <i class="fas fa-plus"></i> Add New Product
                        </button>
                    </div>
                </div>

                <div class="seller-products-management-grid">
                    <?php if (empty($userListings)): ?>
                        <div class="empty-state primal-card">
                            <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
                                <i class="fas fa-boxes" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <h3 style="color: var(--primal-beige); margin-bottom: 1rem;">No Products to Manage</h3>
                                <p>Start adding products to your inventory to manage them here.</p>
                                <button class="primal-btn-primary" style="margin-top: 1rem;" onclick="document.getElementById('add-new-product-btn').click();">
                                    <i class="fas fa-plus"></i> Add Your First Product
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($userListings as $listing): ?>
                            <div class="seller-product-management-item" data-product-id="<?php echo $listing['listing_id']; ?>">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($listing['item_image'] ?? '/assets/images/default-product.png'); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" loading="lazy">
                                    <div class="product-status-indicator <?php echo $listing['is_active'] ? 'active' : 'inactive'; ?>"></div>
                                </div>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                                    <p class="product-category"><?php echo htmlspecialchars($listing['category_name'] ?? $listing['category']); ?></p>
                                    <p class="product-price">$<?php echo number_format($listing['price'], 2); ?></p>
                                    <p class="product-stock">Stock: <?php echo $listing['quantity']; ?></p>
                                    <p class="product-views">Views: <?php echo $listing['views'] ?? 0; ?></p>
                                    <p class="product-sales">Sales: <?php echo $listing['sales'] ?? 0; ?></p>
                                </div>
                                <div class="product-management-actions">
                                    <button class="action-btn edit-seller-product" data-product-id="<?php echo $listing['listing_id']; ?>" title="Edit Product">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="action-btn toggle-seller-product-status" data-product-id="<?php echo $listing['listing_id']; ?>" title="Toggle Status">
                                        <i class="fas fa-<?php echo $listing['is_active'] ? 'pause' : 'play'; ?>"></i>
                                        <?php echo $listing['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                    <button class="action-btn duplicate-seller-product" data-product-id="<?php echo $listing['listing_id']; ?>" title="Duplicate Product">
                                        <i class="fas fa-copy"></i> Duplicate
                                    </button>
                                    <button class="action-btn delete-seller-product" data-product-id="<?php echo $listing['listing_id']; ?>" title="Delete Product">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Product Management Statistics -->
                <div class="seller-product-stats primal-card">
                    <h3><i class="fas fa-chart-bar"></i> Product Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($userListings); ?></span>
                            <span class="stat-label">Total Products</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count(array_filter($userListings, function ($l) {
                                                            return $l['is_active'];
                                                        })); ?></span>
                            <span class="stat-label">Active Products</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count(array_filter($userListings, function ($l) {
                                                            return !$l['is_active'];
                                                        })); ?></span>
                            <span class="stat-label">Inactive Products</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">$<?php echo number_format(array_sum(array_column($userListings, 'price')), 2); ?></span>
                            <span class="stat-label">Total Value</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Purchase/Sales History Tab -->
        <div id="history-content" class="tab-content">
            <div class="section-header">
                <h2>
                    <i class="fas fa-chart-line"></i>
                    <?php echo $isVendor ? 'Sales History' : 'Purchase History'; ?>
                </h2>
                <div class="history-filters">
                    <select id="status-filter" class="filter-select">
                        <option value="all">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <select id="date-filter" class="filter-select">
                        <option value="all">All Time</option>
                        <option value="week">Last Week</option>
                        <option value="month">Last Month</option>
                        <option value="year">Last Year</option>
                    </select>
                </div>
            </div>
            <div class="purchase-history-list primal-card">
                <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
                    <i class="fas fa-history" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <h3 style="color: var(--primal-beige); margin-bottom: 1rem;">No History Available</h3>
                    <p><?php echo $isVendor ? 'Your sales history will appear here once you make your first sale.' : 'Your purchase history will appear here once you make your first purchase.'; ?></p>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings-content" class="tab-content">
            <div class="section-header">
                <h2><i class="fas fa-user-cog"></i> Account Settings</h2>
            </div>

            <div class="settings-grid">
                <div class="settings-section primal-card">
                    <h3><i class="fas fa-user-edit"></i> Update Profile</h3>
                    <form id="alias-form">
                        <div class="form-group">
                            <label for="new-alias">Display Alias</label>
                            <input type="text" id="new-alias" placeholder="Enter new alias" value="<?php echo htmlspecialchars($alias); ?>">
                        </div>
                        <button type="submit" class="primal-btn-primary">
                            <i class="fas fa-save"></i> Update Alias
                        </button>
                    </form>
                </div>

                <div class="settings-section primal-card">
                    <h3><i class="fas fa-key"></i> Security Settings</h3>
                    <form id="password-form">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" placeholder="Enter current password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" placeholder="Enter new password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" placeholder="Confirm new password" required>
                        </div>
                        <button type="submit" class="primal-btn-primary">
                            <i class="fas fa-shield-alt"></i> Update Password
                        </button>
                    </form>
                </div>

                <div class="settings-section primal-card">
                    <h3><i class="fas fa-envelope"></i> Email Management</h3>
                    <p class="current-email">
                        <strong>Current Email:</strong> <?php echo htmlspecialchars($email); ?>
                    </p>
                    <form id="email-form">
                        <div class="form-group">
                            <label>New Email Address</label>
                            <input type="email" placeholder="Enter new email address" required>
                        </div>
                        <button type="submit" class="primal-btn-primary">
                            <i class="fas fa-at"></i> Update Email
                        </button>
                    </form>
                </div>
                <?php if ($isVendor): ?>
                    <div class="settings-section primal-card">
                        <h3><i class="fas fa-store"></i> Vendor Settings</h3>
                        <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1.5rem;">
                            Manage your vendor account preferences and store settings.
                        </p>
                        <button class="primal-btn-secondary" style="width: 100%;">
                            <i class="fas fa-cog"></i> Configure Store Settings
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!$isAdmin): ?>
                    <div class="settings-section primal-card danger-zone">
                        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                        <p>Once you delete your account, there is no going back. All your data, including purchases and vendor information, will be permanently removed.</p>
                        <button class="primal-btn-danger" id="delete-account-btn" style="width: 100%;">
                            <i class="fas fa-trash-alt"></i> Delete Account Permanently
                        </button>
                        <form id="delete-account-form" style="display: none;">
                            <input type="hidden" name="confirm" value="true">
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <!-- Admin Tools Tab -->
            <div id="admin-tools-content" class="tab-content">
                <div class="section-header">
                    <h2><i class="fas fa-shield-alt"></i> Administrative Tools</h2>
                </div>

                <div class="settings-grid">
                    <div class="settings-section primal-card">
                        <h3><i class="fas fa-tachometer-alt"></i> Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <a href="/pages/admin/index.php" class="primal-btn-primary" style="text-decoration: none; text-align: center;">
                                <i class="fas fa-desktop"></i> Full Admin Dashboard
                            </a>
                            <button class="primal-btn-secondary" style="width: 100%;">
                                <i class="fas fa-users"></i> View All Users
                            </button>
                            <button class="primal-btn-secondary" style="width: 100%;">
                                <i class="fas fa-box"></i> Manage Products
                            </button>
                        </div>
                    </div>

                    <div class="settings-section primal-card">
                        <h3><i class="fas fa-chart-bar"></i> System Overview</h3>
                        <div style="color: rgba(255, 255, 255, 0.8); line-height: 1.6;">
                            <p><strong>Account Type:</strong> Administrator</p>
                            <p><strong>Privileges:</strong> Full System Access</p>
                            <p><strong>Last Login:</strong> <?php echo date('M d, Y g:i A'); ?></p>
                            <p><strong>Trust Level:</strong> Maximum (<?php echo number_format($trustLevel, 1); ?>)</p>
                        </div>
                    </div>

                    <div class="settings-section primal-card">
                        <h3><i class="fas fa-cogs"></i> System Settings</h3>
                        <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1.5rem;">
                            Access advanced system configuration and maintenance tools.
                        </p>
                        <button class="primal-btn-secondary" style="width: 100%; margin-bottom: 0.75rem;">
                            <i class="fas fa-database"></i> Database Management
                        </button>
                        <button class="primal-btn-secondary" style="width: 100%;">
                            <i class="fas fa-file-alt"></i> System Logs
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Product Modal -->
<div class="modal-overlay" id="product-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title"><i class="fas fa-plus-circle"></i> Add New Product</h3>
            <button class="modal-close" aria-label="Close modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="product-form">
                <div class="form-group">
                    <label for="product-name"><i class="fas fa-tag"></i> Product Name</label>
                    <input type="text" id="product-name" name="name" placeholder="Enter product name" required>
                </div>

                <div class="form-group">
                    <label for="product-category"><i class="fas fa-list"></i> Category</label>
                    <select id="product-category" name="category" required>
                        <option value="">Select a category</option>
                        <option value="weapons">⚔️ Weapons & Tools</option>
                        <option value="armor">🛡️ Armor & Protection</option>
                        <option value="potions">🧪 Potions & Elixirs</option>
                        <option value="food">🍖 Food & Provisions</option>
                        <option value="materials">🔨 Crafting Materials</option>
                        <option value="artifacts">📿 Ritual Artifacts</option>
                        <option value="pets">🐺 Companions & Pets</option>
                        <option value="infrastructure">🏘️ Infrastructure</option>
                        <option value="drugs">💊 Prehistoric Drugs</option>
                        <option value="voodoo">🔮 Voodoo Items</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product-price"><i class="fas fa-dollar-sign"></i> Price (USD)</label>
                    <input type="number" id="product-price" name="price" step="0.01" min="0" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="product-quantity"><i class="fas fa-boxes"></i> Quantity Available</label>
                    <input type="number" id="product-quantity" name="quantity" min="1" placeholder="1" required>
                </div>

                <div class="form-group">
                    <label for="product-description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea id="product-description" name="description" rows="4" placeholder="Describe your primal offering..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="product-image"><i class="fas fa-image"></i> Product Image URL</label>
                    <input type="url" id="product-image" name="image" placeholder="https://example.com/image.jpg">
                    <small style="color: rgba(255, 255, 255, 0.6); font-size: 0.8rem; margin-top: 0.5rem; display: block;">
                        Optional: Provide a direct link to your product image
                    </small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="primal-btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                    <button type="button" class="primal-btn-secondary modal-close">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include seller product modal for vendors
if ($isVendor) {
    include __DIR__ . '/../../components/sellerProductModal.component.php';
}
?>

<script src="/assets/js/primal-account.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>