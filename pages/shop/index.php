<?php
// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login/index.php');
    exit;
}

require_once BASE_PATH . '/bootstrap.php';
require_once LAYOUTS_PATH . '/header.php';
require_once COMPONENTS_PATH . '/productCard.component.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

// // Get user data from session
// $username = $_SESSION['user_username'];
// $alias = $_SESSION['user_alias'] ?? $username;
// $email = $_SESSION['user_email'] ?? '';
// $trustLevel = $_SESSION['user_trust_level'] ?? 0;
// $isVendor = $_SESSION['is_vendor'] ?? false;
// $isAdmin = $_SESSION['is_admin'] ?? false;

try {
    $db = DatabaseService::getInstance();
    $categories = $db->getCategories();
    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : null;
    
    if ($searchTerm) {
        $filteredListings = $db->searchListings($searchTerm, $selectedCategory);
    } else {
        $filteredListings = $db->getListings($selectedCategory);
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    // Set empty arrays if database fails
    $categories = [];
    $filteredListings = [];
}
?>

<!-- Shop Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-shop.css">
<main class="shop-main shop-dark-theme">
    
   
    <!-- Removed shop-filters-bar for decluttered design -->
    <div class="shop-content-row">
        <aside class="shop-sidebar">
            <h3>Categories</h3>
            <ul>
                <li><a href="/pages/shop/index.php"<?= !$selectedCategory ? ' class="active"' : '' ?> data-category="all">All</a></li>
                <?php foreach ($categories as $cat): ?>
                    <?php 
                    $catName = $cat['name'] ?? $cat['Name'] ?? '';
                    ?>
                    <li><a href="/pages/shop/index.php?category=<?= urlencode($catName) ?>"<?= $selectedCategory === $catName ? ' class="active"' : '' ?> data-category="<?= htmlspecialchars($catName) ?>"><?= htmlspecialchars($catName) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </aside>
        <section class="shop-products">
            <h2>Best Products for you</h2>
            <div class="products-grid shop-products-grid">
                <?php foreach ($filteredListings as $product): ?>
                    <?php
                        // Handle both database format and static format
                        $id = $product['listing_id'];
                        $title = $product['title'] ?? $product['name'] ?? $product['Title'] ?? 'Unknown Product';
                        $price = $product['price'] ?? $product['Price'] ?? 0;
                        $image = $product['item_image'] ?? $product['image_path'] ?? $product['Item_Image'] ?? $product['Image'] ?? null;
                        $publishDate = $product['publish_date'] ?? $product['created_at'] ?? $product['PublishDate'] ?? date('Y-m-d');
                        
                        $isNew = (strtotime($publishDate) > strtotime('-14 days'));
                        $imagePath = $image ? $image : '/assets/images/example.png';
                        
                        renderProductCard(
                            $title, 
                            $id,
                            number_format($price, 2), 
                            $imagePath, 
                            $isNew
                        );
                    ?>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<!-- Shop Page JavaScript -->
<script src="/assets/js/primal-shop.js"></script>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
