
<?php
// Product card component
function renderProductCard($title, $id, $price, $imgSrc, $isNew = false) {
    ?>
    <div class="product-card" data-title="<?php echo htmlspecialchars($title); ?>" data-price="<?php echo $price; ?>" data-image="<?php echo htmlspecialchars($imgSrc); ?>" data-id="<?php echo htmlspecialchars($id); ?>">
        <?php if ($isNew): ?>
            <span class="product-badge">NEW</span>
        <?php endif; ?>
        <div class="product-image">
            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($title); ?>">
        </div>
        <div class="product-info">
            <h3 class="product-title"><?php echo htmlspecialchars($title); ?></h3>
            <span class="product-price">&euro;<?php echo $price; ?></span>
        </div>
        <button class="add-to-cart-btn">Add to Cart</button>
    </div>
    <?php
}
