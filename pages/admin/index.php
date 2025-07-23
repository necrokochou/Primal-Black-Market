<?php


// Get data from database
try {
    $db = DatabaseService::getInstance();
    $userCount = $db->getUserCount();
    $listingCount = $db->getListingCount(false); // Total listings
    $activeListingCount = $db->getListingCount(true); // Active listings only
    $users = $db->getAllUsers();
    $listings = $db->getListings(null, null, 0, true);
    echo '<pre>';
    echo "✅ Users loaded: " . count($users) . "\n";
    echo "✅ Listings loaded: " . count($listings) . "\n";
    echo "Host: " . ($_ENV['PG_HOST'] ?? 'not set') . "\n";
    echo "Port: " . ($_ENV['PG_PORT'] ?? 'not set') . "\n";
    echo "DB: " . ($_ENV['PG_DB'] ?? 'not set') . "\n";
    echo "User: " . ($_ENV['PG_USER'] ?? 'not set') . "\n";
    echo '</pre>';
    echo '</pre>'; // Include inactive listings for admin
} catch (Exception $e) {
    error_log("Database error in admin dashboard: " . $e->getMessage());
    // Set default values if database fails
    $userCount = 0;
    $listingCount = 0;
    $activeListingCount = 0;
    $users = [];
    $listings = [];

    echo '<pre>';
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "✅ Users fallback: " . count($users) . "\n";
    echo "✅ Listings fallback: " . count($listings) . "\n";
    echo '</pre>';
}
?>


<!-- Admin Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-admin.css">

<main class="primal-admin-bg min-vh-100">
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header primal-card">
            <div class="admin-info">
                <h1><i class="fas fa-shield-alt"></i> Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($alias); ?></p>
            </div>
            <div class="admin-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $userCount; ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $listingCount; ?></span>
                    <span class="stat-label">Total Products</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $activeListingCount; ?></span>
                    <span class="stat-label">Active Products</span>
                </div>
            </div>
            <div class="admin-actions">
                <a href="/pages/account" class="primal-btn-secondary">
                    <i class="fas fa-user"></i> My Account
                </a>
                <a href="/handlers/logout.handler.php" class="primal-btn-danger" id="admin-logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Admin Navigation -->
        <div class="admin-nav primal-card">
            <button class="nav-tab active" data-tab="users">
                <i class="fas fa-users"></i> User Management
            </button>
            <button class="nav-tab" data-tab="products">
                <i class="fas fa-box"></i> Product Management
            </button>
            <button class="nav-tab" data-tab="analytics">
                <i class="fas fa-chart-bar"></i> Analytics
            </button>
        </div>

        <!-- Users Management Section -->
        <div id="users-section" class="admin-section">
            <div class="section-header">
                <h2>User Management</h2>
                <div class="section-filters">
                    <input type="text" id="user-search" class="search-input" placeholder="Search users...">
                    <select id="user-status-filter" class="filter-select">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="banned">Banned</option>
                    </select>
                    <select id="user-role-filter" class="filter-select">
                        <option value="all">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </div>

            <div class="users-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Join Date</th>
                            <th>Trust Level</th>
                            <th>Vendor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                            <tr data-user-id="<?php echo htmlspecialchars($user['user_id']); ?>">
                                <td>
                                    <div class="user-info">
                                        <i class="fas fa-user-circle user-avatar"></i>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                            <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                            <div class="user-alias">Alias: <?php echo htmlspecialchars($user['alias']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="role-badge <?php echo $user['is_admin'] ? 'admin' : 'user'; ?>"><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></span></td>
                                <td><span class="status-badge active">Active</span></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo number_format($user['trustlevel'], 1); ?></td>
                                <td><?php echo $user['is_vendor'] ? 'Yes' : 'No'; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view-user" data-user-id="<?php echo $user['user_id']; ?>" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn ban-user" data-user-id="<?php echo $user['user_id']; ?>" title="Ban User">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <button class="action-btn delete-user" data-user-id="<?php echo htmlspecialchars($user['user_id']); ?>" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Products Management Section -->
        <div id="products-section" class="admin-section" style="display: none;">
            <div class="section-header">
                <h2>Product Management</h2>
                <div class="section-filters">
                    <input type="text" id="product-search" class="search-input" placeholder="Search products...">
                    <select id="product-category-filter" class="filter-select">
                        <option value="all">All Categories</option>
                        <option value="weapons">Weapons</option>
                        <option value="armor">Armor</option>
                        <option value="potions">Potions</option>
                        <option value="accessories">Accessories</option>
                        <option value="forging-materials">Forging Materials</option>
                    </select>
                </div>
            </div>

            <div class="admin-products-grid">
                <?php foreach (array_slice($listings, 0, 12) as $listing): ?>
                    <div class="admin-product-item" data-product-id="<?php echo $listing['listing_id']; ?>">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($listing['item_image'] ?? '/assets/images/default-product.png'); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" loading="lazy">
                            <div class="product-status-indicator <?php echo $listing['is_active'] ? 'active' : 'inactive'; ?>"></div>
                        </div>
                        <div class="product-details">
                            <h3><?php echo htmlspecialchars($listing['title']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($listing['category_name'] ?? $listing['category']); ?></p>
                            <p class="product-price">$<?php echo number_format($listing['price'], 2); ?></p>
                            <p class="product-stock">Stock: <?php echo $listing['quantity']; ?></p>
                            <p class="product-seller">Seller: User</p>
                        </div>
                        <div class="product-actions">
                            <button class="action-btn edit-product-admin" data-product-id="<?php echo $listing['listing_id']; ?>" title="Edit Product">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn toggle-product-status" data-product-id="<?php echo $listing['listing_id']; ?>" title="Toggle Status">
                                <i class="fas fa-<?php echo $listing['is_active'] ? 'pause' : 'play'; ?>"></i>
                            </button>
                            <button class="action-btn delete-product-admin" data-product-id="<?php echo $listing['listing_id']; ?>" title="Delete Product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Analytics Section -->
        <div id="analytics-section" class="admin-section" style="display: none;">
            <div class="analytics-grid">
                <div class="analytics-card">
                    <h3><i class="fas fa-users"></i> User Statistics</h3>
                    <div class="metric">
                        <span class="metric-value total-users-metric"><?php echo count($users); ?></span>
                        <span class="metric-label">Total Users</span>
                        <div class="metric-change positive users-growth">
                            <i class="fas fa-arrow-up"></i> 12.5%
                        </div>
                    </div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-box"></i> Product Statistics</h3>
                    <div class="metric">
                        <span class="metric-value total-products-metric"><?php echo count($listings); ?></span>
                        <span class="metric-label">Total Products</span>
                        <div class="metric-change positive products-growth">
                            <i class="fas fa-arrow-up"></i> 15.7%
                        </div>
                    </div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-dollar-sign"></i> Revenue</h3>
                    <div class="metric">
                        <span class="metric-value total-revenue-metric">$45,670.50</span>
                        <span class="metric-label">Total Revenue</span>
                        <div class="metric-change positive revenue-growth">
                            <i class="fas fa-arrow-up"></i> 8.3%
                        </div>
                    </div>
                </div>

                <div class="analytics-card">
                    <h3><i class="fas fa-chart-pie"></i> Top Categories</h3>
                    <div class="category-list">
                        <div class="category-item">
                            <span class="category-name">Weapons</span>
                            <span class="category-percentage">35%</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Armor</span>
                            <span class="category-percentage">28%</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Potions</span>
                            <span class="category-percentage">20%</span>
                        </div>
                        <div class="category-item">
                            <span class="category-name">Accessories</span>
                            <span class="category-percentage">17%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/assets/js/primal-admin.js"></script>
