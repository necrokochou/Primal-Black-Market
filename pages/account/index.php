<?php
// Check if user is logged in
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /pages/login/index.php');
    exit;
}

require_once __DIR__ . '/../../layouts/header.php';

// Get user data from session
$username = $_SESSION['user'];
$alias = $_SESSION['user_alias'] ?? $username;
$email = $_SESSION['user_email'] ?? '';
$trustLevel = $_SESSION['user_trust_level'] ?? 0;
$isVendor = $_SESSION['is_vendor'] ?? false;
$isAdmin = $_SESSION['is_admin'] ?? false;

// Get user's listings from database (if they are a vendor)
$userListings = [];
if ($isVendor && isset($_SESSION['user_id'])) {
    try {
        require_once BASE_PATH . '/bootsrap.php';
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
?>

<!-- Account Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-account.css">

<main class="primal-account-bg min-vh-100">
    <div class="account-container">
        <!-- Account Header -->
        <div class="account-header primal-card">
            <div class="account-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="account-info">
                <h1 class="account-name"><?php echo htmlspecialchars($alias); ?></h1>
                <p class="account-type">
                    <?php
                    if ($isAdmin) {
                        echo 'Administrator Account';
                    } elseif ($isVendor) {
                        echo 'Vendor Account';
                    } else {
                        echo 'Member Account';
                    }
                    ?>
                </p>
                <span class="account-status active">Active</span>
                <p class="account-trust">Trust Level: <?php echo number_format($trustLevel, 1); ?></p>
            </div>
            <div class="account-actions">
                <?php if ($isAdmin): ?>
                    <a href="/pages/admin" class="primal-btn-secondary">
                        <i class="fas fa-shield-alt"></i> Admin Dashboard
                    </a>
                <?php endif; ?>
                <button class="primal-btn-danger" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>

        <!-- Account Navigation -->
        <div class="account-nav primal-card">
            <button class="nav-tab active" data-tab="products">
                <i class="fas fa-box"></i> My Products
            </button>
            <button class="nav-tab" data-tab="history">
                <i class="fas fa-history"></i> Purchase History
            </button>
            <button class="nav-tab" data-tab="settings">
                <i class="fas fa-cog"></i> Settings
            </button>
        </div>

        <!-- Products Tab -->
        <div id="products-content" class="tab-content active">
            <div class="section-header">
                <h2>My Products</h2>
                <button class="primal-btn-primary" id="add-product-btn">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>

            <div class="my-products-grid">
                <?php foreach (array_slice($userListings, 0, 6) as $listing): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($listing['Image']); ?>" alt="<?php echo htmlspecialchars($listing['Name']); ?>" loading="lazy">
                            <div class="product-status active"></div>
                        </div>
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($listing['Name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($listing['Category']); ?></p>
                            <p class="product-price">$<?php echo number_format($listing['Price'], 2); ?></p>
                            <p class="product-description"><?php echo htmlspecialchars($listing['Description']); ?></p>
                        </div>
                        <div class="product-actions">
                            <button class="primal-btn-secondary edit-product" data-product-id="<?php echo $listing['ID']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="primal-btn-danger remove-product" data-product-id="<?php echo $listing['ID']; ?>">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Purchase History Tab -->
        <div id="history-content" class="tab-content">
            <div class="section-header">
                <h2>Purchase History</h2>
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

            <div class="purchase-history-list">
                <!-- History items will be populated by JavaScript -->
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings-content" class="tab-content">
            <div class="settings-grid">
                <div class="settings-section primal-card">
                    <h3><i class="fas fa-user"></i> Change Alias</h3>
                    <form id="alias-form">
                        <input type="text" id="new-alias" placeholder="Enter new alias" value="<?php echo htmlspecialchars($alias); ?>">
                        <button type="submit" class="primal-btn-primary">Update Alias</button>
                    </form>
                </div>

                <div class="settings-section primal-card">
                    <h3><i class="fas fa-key"></i> Change Password</h3>
                    <form id="password-form">
                        <input type="password" placeholder="Current Password" required>
                        <input type="password" placeholder="New Password" required>
                        <input type="password" placeholder="Confirm New Password" required>
                        <button type="submit" class="primal-btn-primary">Update Password</button>
                    </form>
                </div>

                <div class="settings-section primal-card">
                    <h3><i class="fas fa-envelope"></i> Email Settings</h3>
                    <p class="current-email">Current: <?php echo htmlspecialchars($email); ?></p>
                    <form id="email-form">
                        <input type="email" placeholder="New Email Address" required>
                        <button type="submit" class="primal-btn-primary">Update Email</button>
                    </form>
                </div>

                <div class="settings-section primal-card danger-zone">
                    <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    <button class="primal-btn-danger" id="delete-account-btn">
                        <i class="fas fa-trash"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Product Modal -->
<div class="modal-overlay" id="product-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Add New Product</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="product-form">
                <div class="form-group">
                    <label for="product-name">Product Name</label>
                    <input type="text" id="product-name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="product-category">Category</label>
                    <select id="product-category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="weapons">Weapons</option>
                        <option value="armor">Armor</option>
                        <option value="potions">Potions</option>
                        <option value="accessories">Accessories</option>
                        <option value="forging-materials">Forging Materials</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product-price">Price ($)</label>
                    <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="product-description">Description</label>
                    <textarea id="product-description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="product-image">Image URL</label>
                    <input type="url" id="product-image" name="image" placeholder="https://...">
                </div>

                <div class="form-actions">
                    <button type="submit" class="primal-btn-primary">Save Product</button>
                    <button type="button" class="primal-btn-secondary modal-close">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/primal-account.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>