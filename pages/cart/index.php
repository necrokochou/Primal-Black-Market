

<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<main class="primal-cart-bg min-vh-100">
  <div class="cart-container">
    <div class="cart-table primal-card">
      <h1 class="primal-title text-left mb-xxl">Shopping Cart</h1>
      <div class="cart-table-header">
        <div class="cart-th-product">Product Code</div>
        <div class="cart-th-qty">Quantity</div>
        <div class="cart-th-total">Total</div>
        <div class="cart-th-action">Action</div>
      </div>
      <div id="cart-items"></div>
      <button class="primal-btn-primary cart-update-btn" id="update-cart-btn">Update Cart</button>
    </div>
    <aside class="cart-summary primal-card">
      <h2 class="cart-summary-title">Order Summary</h2>
      <div class="cart-summary-row">
        <input type="text" id="cart-voucher" class="cart-voucher-input" placeholder="Discount voucher">
        <button class="primal-btn-primary cart-voucher-btn">Apply</button>
      </div>
      <div class="cart-summary-list">
        <div class="cart-summary-item"><span>Sub Total</span><span id="cart-subtotal">0.00 USD</span></div>
        <div class="cart-summary-item"><span>Discount (10%)</span><span id="cart-discount">-0.00 USD</span></div>
        <div class="cart-summary-item"><span>Delivery fee</span><span id="cart-delivery">0.00 USD</span></div>
      </div>
      <div class="cart-summary-total"><span>Total</span><span id="cart-total">0.00 USD</span></div>
      <div class="cart-summary-warranty">
        <i class="fa fa-shield"></i> 90 Day Limited Warranty against manufacturer's defects <a href="#" class="primal-link">Details</a>
      </div>
      <button class="primal-btn-primary cart-checkout-btn" id="checkout-btn">Checkout Now</button>
    </aside>
  </div>
</main>
<script src="/assets/js/cart.js"></script>
<script>renderCart();</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
