<?php
// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: /pages/login/index.php');
    exit;
}

require_once __DIR__ . '/../../layouts/header.php';
?>

<!-- Cart Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-cart.css">

<main class="primal-cart-bg min-vh-100">
  <div class="cart-container">
    <div class="cart-table primal-card">
      <h1 class="primal-title text-left mb-xxl">Shopping Cart</h1>
      <div class="cart-table-header">
        <div class="cart-th-product">Product Details</div>
        <div class="cart-th-qty">Quantity</div>
        <div class="cart-th-total">Total Price</div>
        <div class="cart-th-action">Remove</div>
      </div>
      <div id="cart-items"></div>
      <button class="primal-btn-primary cart-update-btn" id="update-cart-btn">
        <i class="fas fa-sync-alt" style="margin-right: 0.5rem;"></i>
        Update Cart
      </button>
    </div>
    <aside class="cart-summary primal-card">
      <h2 class="cart-summary-title">Order Summary</h2>
      <div class="cart-summary-row">
        <input type="text" id="cart-voucher" class="cart-voucher-input" placeholder="Enter discount code">
        <button class="primal-btn-primary cart-voucher-btn">
          <i class="fas fa-tag" style="margin-right: 0.3rem;"></i>
          Apply
        </button>
      </div>
      <div class="cart-summary-list">
        <div class="cart-summary-item">
          <span><i class="fas fa-calculator" style="margin-right: 0.5rem; color: var(--primal-orange);"></i>Sub Total</span>
          <span id="cart-subtotal">$0.00</span>
        </div>
        <div class="cart-summary-item">
          <span><i class="fas fa-percent" style="margin-right: 0.5rem; color: var(--primal-green);"></i>Discount (10%)</span>
          <span id="cart-discount">-$0.00</span>
        </div>
        <div class="cart-summary-item">
          <span><i class="fas fa-truck" style="margin-right: 0.5rem; color: var(--primal-brown);"></i>Delivery Fee</span>
          <span id="cart-delivery">$0.00</span>
        </div>
      </div>
      <div class="cart-summary-total">
        <span><i class="fas fa-coins" style="margin-right: 0.5rem;"></i>Total</span>
        <span id="cart-total">$0.00</span>
      </div>
      <div class="cart-summary-warranty">
        <i class="fas fa-shield-alt"></i> 90 Day Limited Warranty against manufacturer's defects 
        <a href="#" class="primal-link">View Details</a>
      </div>
      <button class="primal-btn-primary cart-checkout-btn" id="checkout-btn">
        <i class="fas fa-credit-card" style="margin-right: 0.5rem;"></i>
        Checkout Now
      </button>
    </aside>
  </div>
</main>

<!-- Cart Page JavaScript -->
<script src="/assets/js/primal-cart.js"></script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
