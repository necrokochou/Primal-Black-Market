
<?php
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../components/productCard.component.php';
$categories = require __DIR__ . '/../../staticData/dummies/categories.staticData.php';
$listings = require __DIR__ . '/../../staticData/dummies/listings.staticData.php';
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$filteredListings = array_filter($listings, function($item) use ($selectedCategory) {
    return !$selectedCategory || $item['Category'] === $selectedCategory;
});
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
                    <li><a href="/pages/shop/index.php?category=<?= urlencode($cat['Name']) ?>"<?= $selectedCategory === $cat['Name'] ? ' class="active"' : '' ?> data-category="<?= htmlspecialchars($cat['Name']) ?>"><?= htmlspecialchars($cat['Name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </aside>
        <section class="shop-products">
            <h2>Best Products for you</h2>
            <div class="products-grid shop-products-grid">
                <?php foreach ($filteredListings as $product): ?>
                    <?php
                        $isNew = (strtotime($product['PublishDate']) > strtotime('-14 days'));
                        renderProductCard(
                            $product['Title'], 
                            number_format($product['Price'], 2), 
                            '/assets/images/example.png', 
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

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
