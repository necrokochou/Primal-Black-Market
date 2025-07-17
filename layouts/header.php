
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
?>
<header class="site-header primal-header-aaa">
    <link rel="stylesheet" href="/assets/css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <div class="top-bar primal-top-bar">
        <span><i class="fa fa-crown"></i> Welcome to the <b>Primal Black Market</b>!</span>
        <div class="top-bar-right">
            <span class="lang-select">English <i class="fa fa-chevron-down"></i></span>
            <?php if (!$user): ?>
                <a href="/pages/login/index.php" class="login-link">Login</a> /
                <a href="/pages/register/index.php" class="login-link">Register</a>
            <?php else: ?>
                <span class="user-welcome"><i class="fa fa-user"></i> <?= htmlspecialchars($user) ?></span> /
                <a href="/pages/logout/index.php" class="login-link">Logout</a>
            <?php endif; ?>
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
        </div>
    </div>
    <div class="main-nav primal-theme-nav primal-nav-aaa">
        <div class="logo">
            <a href="/"><img src="/assets/images/example.png" alt="Primal Black Market Logo" style="height:44px;"></a>
        </div>
        <nav>
            <ul class="nav-list">
                <li><a href="/index.php">Home</a></li>
                <li><a href="/pages/shop/index.php">Shop</a></li>
                <li><a href="/pages/cart/index.php">Cart</a></li>
                <li><a href="/pages/about/index.php">About</a></li>
            </ul>
        </nav>
        <div class="header-actions">
            <a href="/pages/cart/index.php" class="cart-link" id="cartBtn">
                <i class="fa fa-shopping-cart"></i> <span id="cart-count">0</span>
            </a>
            <?php if (!$user): ?>
                <a href="/pages/login/index.php" class="login-link" id="loginBtn">Login</a> /
                <a href="/pages/register/index.php" class="login-link" id="registerBtn">Register</a>
            <?php else: ?>
                <span class="user-welcome"><i class="fa fa-user"></i> <?= htmlspecialchars($user) ?></span> /
                <a href="/pages/logout/index.php" class="login-link">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <script>
    // Animate cart count
    document.addEventListener('DOMContentLoaded', function() {
        function updateCartCount() {
            let count = 0;
            try {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                count = cart.length;
            } catch {}
            document.getElementById('cart-count').textContent = count;
        }
        updateCartCount();
        window.addEventListener('cartUpdated', updateCartCount);
    });
    </script>
</header>
