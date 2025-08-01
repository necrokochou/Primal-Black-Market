<?php
// Start output buffering to prevent premature output
ob_start();

// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    // Clean output buffer before redirect
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Location: /pages/login/index.php');
    exit;
}

require_once BASE_PATH . '/bootstrap.php';
require_once LAYOUTS_PATH . '/header.php';

// Handle success/error messages from URL parameters (for non-AJAX form submissions)
$notification = null;
if (isset($_GET['success'])) {
    $notification = [
        'type' => 'success',
        'message' => urldecode($_GET['success'])
    ];
} elseif (isset($_GET['error'])) {
    $notification = [
        'type' => 'error', 
        'message' => urldecode($_GET['error'])
    ];
}

require_once BASE_PATH . '/bootstrap.php';

// Get user data from session
$user = $_SESSION['user'];
$userID = $user['user_id'];
$username = $user['username'];
$alias = $user['alias'] ?? $username;
$email = $user['email'] ?? '';
$trustLevel = $user['trust_level'] ?? 0;
$isVendor = $user['is_vendor'] ?? false;
$isAdmin = $user['is_admin'] ?? false;
// Get user's listings from database (if they are a vendor)
$userListings = [];
$purchaseHistory = [];
if ($isVendor) {
    try {
        require_once UTILS_PATH . '/DatabaseService.util.php';
        $db = DatabaseService::getInstance()->getConnection();

        // Get listings for this vendor
        $stmt = $db->prepare("
            SELECT listing_id, title, description, category, price, quantity, 
                   is_active, publish_date, item_image 
            FROM listings 
            WHERE vendor_id = :vendor_id 
            ORDER BY publish_date DESC
        ");
        $stmt->execute(['vendor_id' => $userID]);
        $userListings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Found " . count($userListings) . " listings for vendor: " . $userID);
        if (!empty($userListings)) {
            error_log("Sample listing data: " . json_encode(array_slice($userListings, 0, 2)));
        }

        // Get sales history for this vendor
        $stmt = $db->prepare("
            SELECT ph.*, l.title, l.item_image
            FROM purchase_history ph
            JOIN listings l ON ph.listing_id = l.listing_id
            WHERE l.vendor_id = :vendor_id
            ORDER BY ph.purchase_date DESC
        ");
        $stmt->execute(['vendor_id' => $userID]);
        $purchaseHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Database error (vendor data): " . $e->getMessage());
        $userListings = [];
        $purchaseHistory = [];
    }
} else {
    // Get purchase history for a regular user
    try {
        require_once UTILS_PATH . '/DatabaseService.util.php';
        $db = DatabaseService::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT ph.*, l.title, l.item_image
            FROM purchase_history ph
            JOIN listings l ON ph.listing_id = l.listing_id
            WHERE ph.user_id = :user_id
            ORDER BY ph.purchase_date DESC
        ");
        $stmt->execute(['user_id' => $userID]);
        $purchaseHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        error_log("Database error (user purchase history): " . $e->getMessage());
        $purchaseHistory = [];
    }
}

require_once LAYOUTS_PATH . '/header.php';

// Temporary debug information
if ($isVendor && isset($_GET['debug'])) {
    echo "<div style='background: rgba(255,140,0,0.1); border: 1px solid #ff8c00; padding: 1rem; margin: 1rem; border-radius: 8px;'>";
    echo "<h4 style='color: #ff8c00; margin: 0 0 1rem 0;'>🐛 Debug Info (remove ?debug from URL to hide)</h4>";
    echo "<p><strong>User ID:</strong> " . htmlspecialchars($user['user_id']) . "</p>";
    echo "<p><strong>Is Vendor:</strong> " . ($isVendor ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Products Found:</strong> " . count($userListings) . "</p>";
    if (!empty($userListings)) {
        echo "<p><strong>Latest Product:</strong> " . htmlspecialchars($userListings[0]['title']) . " (Added: " . $userListings[0]['publish_date'] . ")</p>";
    }
    echo "</div>";
}
?>

<!-- Account Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-account.css">

<main class="primal-account-bg min-vh-100">
    <!-- Display notifications from URL parameters -->
    <?php if ($notification): ?>
        <div class="notification-banner notification-<?php echo $notification['type']; ?>" id="url-notification">
            <i class="fas fa-<?php echo $notification['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <span><?php echo htmlspecialchars($notification['message']); ?></span>
            <button onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>
    
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
                <button class="nav-tab" data-tab="sales-history">
                    <i class="fas fa-chart-line"></i> Sales History
                </button>
            <?php endif; ?>
            <button class="nav-tab" data-tab="settings">
                <i class="fas fa-cog"></i> Account Settings
            </button>
        </div>

        <!-- Products/Purchases Tab -->
        <div id="products-content" class="tab-content active">
            <div class="section-header">
                <h2>
                    <i class="fas fa-<?php echo $isVendor ? 'store' : 'shopping-bag'; ?>"></i>
                    <?php echo $isVendor ? 'My Products' : 'My Purchases'; ?>
                </h2>
            </div>
            <?php if ($isVendor): ?>
                <!-- Vendor Products Grid -->
                <div class="my-products-grid">
                    <?php if (empty($userListings)): ?>
                        <div class="empty-state primal-card">
                            <div style="text-align: center; padding: 3rem; color: rgba(255, 255, 255, 0.6);">
                                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                                <h3 style="color: var(--primal-beige); margin-bottom: 1rem;">No Products Yet</h3>
                                <p>You haven't added any products to your collection.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($userListings, 0, 6) as $listing): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php 
                                    $imagePath = $listing['item_image'] ?? '';
                                    if (empty($imagePath) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                                        $imagePath = '/assets/images/example.png'; // fallback image
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" loading="lazy">
                                    <div class="product-status <?php echo $listing['is_active'] ? 'active' : 'inactive'; ?>"></div>
                                </div>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                                    <p class="product-category"><?php echo htmlspecialchars($listing['category']); ?></p>
                                    <p class="product-price">$<?php echo number_format($listing['price'], 2); ?></p>
                                    <p class="product-description"><?php echo htmlspecialchars(substr($listing['description'], 0, 100)) . (strlen($listing['description']) > 100 ? '...' : ''); ?></p>
                                    <p class="product-date">Added: <?php echo date('M j, Y', strtotime($listing['publish_date'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Member Purchases - Dynamic Loading -->
                <div class="purchases-section primal-card">
                    <div class="purchase-header">
                        <h3><i class="fas fa-history"></i> Purchase History</h3>
                        <button id="refresh-purchases" class="primal-btn-secondary">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    
                    <div id="purchase-loading" class="loading-spinner" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Loading purchases...
                    </div>
                    
                    <div id="purchase-list">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                    
                    <div id="no-purchases" class="no-purchases-state" style="display: none;">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>No Purchases Yet</h3>
                        <p>Explore the primal marketplace and discover unique items.</p>
                        <a href="/pages/shop" class="primal-btn-primary">
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
                            <?php 
                            $categories = require_once DUMMIES_PATH . '/categories.staticData.php';
                            foreach ($categories as $category): 
                                $categoryValue = strtolower(str_replace(' ', '-', $category['Name']));
                                $icon = match($category['Name']) {
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
                            ?>
                            <option value="<?php echo $categoryValue; ?>">
                                <?php echo $icon . ' ' . htmlspecialchars($category['Name']); ?>
                            </option>
                            <?php endforeach; ?>
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
                                    <?php 
                                    $imagePath = $listing['item_image'] ?? '';
                                    if (empty($imagePath) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                                        $imagePath = '/assets/images/example.png'; // fallback image
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" loading="lazy">
                                    <div class="product-status-indicator <?php echo $listing['is_active'] ? 'active' : 'inactive'; ?>"></div>
                                </div>
                                <div class="product-details">
                                    <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                                    <p class="product-category"><?php echo htmlspecialchars($listing['category']); ?></p>
                                    <p class="product-price">$<?php echo number_format($listing['price'], 2); ?></p>
                                    <p class="product-stock">Stock: <?php echo $listing['quantity']; ?></p>
                                    <p class="product-date">Added: <?php echo date('M j, Y', strtotime($listing['publish_date'])); ?></p>
                                    <p class="product-status-text">Status: <span class="<?php echo $listing['is_active'] ? 'text-success' : 'text-muted'; ?>"><?php echo $listing['is_active'] ? 'Active' : 'Inactive'; ?></span></p>
                                </div>
                                <div class="product-management-actions">
                                    <button class="action-btn edit-seller-product" data-product-id="<?php echo $listing['listing_id']; ?>" title="Edit Product">
                                        <i class="fas fa-edit"></i> Edit
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

        <?php if ($isVendor): ?>
            <!-- Sales History Tab (Only for Vendors) -->
            <div id="sales-history-content" class="tab-content">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-chart-line"></i> Sales History
                    </h2>
                    <div class="history-filters">
                        <select id="sales-status-filter" class="filter-select">
                            <option value="all">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="processed">Processed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select id="sales-date-filter" class="filter-select">
                            <option value="all">All Time</option>
                            <option value="week">Last Week</option>
                            <option value="month">Last Month</option>
                            <option value="year">Last Year</option>
                        </select>
                        <button id="refresh-sales" class="primal-btn-secondary">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="sales-history-container primal-card">
                    <div class="sales-summary">
                        <h3><i class="fas fa-analytics"></i> Sales Summary</h3>
                        <div class="sales-stats-grid">
                            <div class="stat-item">
                                <span class="stat-number" id="total-sales-count">0</span>
                                <span class="stat-label">Total Sales</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="total-sales-revenue">$0.00</span>
                                <span class="stat-label">Total Revenue</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="avg-order-value">$0.00</span>
                                <span class="stat-label">Avg Order Value</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="recent-sales-count">0</span>
                                <span class="stat-label">This Month</span>
                            </div>
                        </div>
                    </div>

                    <div class="sales-history-list">
                        <div class="sales-header">
                            <h3><i class="fas fa-history"></i> Recent Sales</h3>
                        </div>
                        
                        <div id="sales-loading" class="loading-spinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading sales history...
                        </div>
                        
                        <div id="sales-list">
                            <!-- Dynamic sales content will be loaded here -->
                        </div>
                        
                        <div id="no-sales" class="no-sales-state" style="display: none;">
                            <i class="fas fa-chart-line"></i>
                            <h3>No Sales Yet</h3>
                            <p>Your sales history will appear here once customers purchase your products.</p>
                            <a href="/pages/shop" class="primal-btn-primary">
                                <i class="fas fa-store"></i> View My Products in Shop
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

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
    </div>
</main>

<!-- Basic Product Modal removed - using sellerProductModal.component.php instead -->

<?php
// Include seller product modal for vendors
if ($isVendor) {
    include COMPONENTS_PATH . '/sellerProductModal.component.php';
}
?>

<!-- Pass PHP data to JavaScript -->
<script>
window.accountPageData = {
    isVendor: <?php echo $isVendor ? 'true' : 'false'; ?>,
    isAdmin: <?php echo $isAdmin ? 'true' : 'false'; ?>
};
</script>

<script src="/assets/js/primal-account.js"></script>
<?php require_once LAYOUTS_PATH . '/footer.php'; ?>