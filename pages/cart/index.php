<?php
require_once __DIR__ . '/../../layouts/header.php';
?>
<main class="cart-main primal-theme">
    <h1>Your Cart</h1>
    <div id="cart-items"></div>
    <div class="cart-summary">
        <span id="cart-total"></span>
        <button class="btn btn-primary" id="checkout-btn">Checkout</button>
    </div>
</main>
<script src="/assets/js/cart.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
