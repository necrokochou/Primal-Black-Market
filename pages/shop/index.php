
<?php
require_once LAYOUTS_PATH . '/header.php';
require_once COMPONENTS_PATH . '/productCard.component.php';
require_once UTILS_PATH . '/DatabaseService.util.php';

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
                        $title = $product['title'] ?? $product['name'] ?? $product['Title'] ?? 'Unknown Product';
                        $price = $product['price'] ?? $product['Price'] ?? 0;
                        $image = $product['item_image'] ?? $product['image_path'] ?? $product['Item_Image'] ?? $product['Image'] ?? null;
                        $publishDate = $product['publish_date'] ?? $product['created_at'] ?? $product['PublishDate'] ?? date('Y-m-d');
                        
                        $isNew = (strtotime($publishDate) > strtotime('-14 days'));
                        $imagePath = $image ? '/' . $image : '/assets/images/example.png';
                        
                        renderProductCard(
                            $title, 
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
