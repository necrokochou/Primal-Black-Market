

<?php
require_once BASE_PATH . '/bootstrap.php';
require_once UTILS_PATH . '/envSetter.util.php';
require_once HANDLERS_PATH . '/mongodbChecker.handler.php';
require_once HANDLERS_PATH . '/postgresChecker.handler.php';
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/components/productCard.component.php';

$categories = require __DIR__ . '/staticData/dummies/categories.staticData.php';
$listings = require __DIR__ . '/staticData/dummies/listings.staticData.php';
$groupedListings = [];
foreach ($listings as $listing) {
    if (!isset($groupedListings[$listing['Category']])) {
        $groupedListings[$listing['Category']] = [];
    }
    $groupedListings[$listing['Category']][] = $listing;
}
?>

<!-- Primal Body Styling -->
<link rel="stylesheet" href="/assets/css/primal-body.css">

<main class="homepage-main">
    <section class="hero-section hero-modern">
        <div class="hero-bg-image">
            <img src="/assets/images/heroimg.jpg" alt="primal img" />
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content hero-content-modern">
            <div class="hero-label-row">
                <span class="hero-genre-label">PRIMAL GOODS</span>
                <span class="hero-badge">EXOTIC</span>
            </div>
            <h1 class="hero-title-modern">UNLEASH THE PRIMAL<br>OWN THE FORBIDDEN</h1>
            <div class="hero-desc">ARGGHH ARGHH ARGHHH<br>
                Step beyond the civilized world. We deal in rare, primal goods and exotic artifacts items whispered about in the shadows. From tribal relics to untamed luxuries, our marketplace is where instinct meets indulgence. Discreet. Dangerous. Decidedly raw.
            </div>
            <div class="hero-actions hero-actions-modern">
                <a href="./pages/shop/index.php" class="btn btn-primary hero-btn">SHOP NOW</a>
            </div>
        </div>
    </section>

    <section class="featured-products">
        <h2>Featured Products</h2>
        <div class="featured-products-row">
            <?php
            $allProducts = $listings;
            shuffle($allProducts);
            $carouselProducts = array_slice($allProducts, 0, 3);
            foreach ($carouselProducts as $product) {
                $isNew = (strtotime($product['PublishDate']) > strtotime('-14 days'));
                renderProductCard(
                    $product['Title'],
                    number_format($product['Price'], 2),
                    '/assets/images/example.png',
                    $isNew
                );
            }
            ?>
        </div>
    </section>



    <section class="promo-video-section">
        <div class="promo-banner">
            <span class="promo-label">EXTRA 75% OFF</span>
            <span class="promo-code">USE PROMO CODE: <b>HGARJFA</b></span>
            <a href="#" class="btn">SHOP NOW</a>
        </div>
        <div class="promo-video">
            <iframe width="100%" height="200" src="https://www.youtube.com/embed/8ZK_S-46KwE" title="Timelapse - Lighthouse (Oct 2012)" frameborder="0" allowfullscreen></iframe>
        </div>
    </section>

    <section class="weekly-top-selling">
        <h2>Weekly Top Selling</h2>
        <div class="products-grid">
           <?php
            $allProducts = $listings;
            shuffle($allProducts);
            $carouselProducts = array_slice($allProducts, 0, 4);
            foreach ($carouselProducts as $product) {
                $isNew = (strtotime($product['PublishDate']) > strtotime('-14 days'));
                renderProductCard(
                    $product['Title'],
                    number_format($product['Price'], 2),
                    '/assets/images/example.png',
                    $isNew
                );
            }
            ?>
        </div>
    </section>

    <div class="delivery-info">
        <span>FREE DELIVERY FOR EVERY FIRST PURCHASE + RETURN OVER $59.00 | COLLECT FROM STORE</span>
    </div>
</main>

<!-- Primal Body Interactions -->
<script src="/assets/js/primal-body.js"></script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>

