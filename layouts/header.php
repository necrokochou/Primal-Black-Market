<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
?>

<header class="site-header modern-clean-header">
    <link rel="stylesheet" href="/assets/css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="header-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <a href="/" class="logo-link">
                <img src="/assets/images/Logo.png" alt="Primal Black Market Logo" class="logo-img">
            </a>
        </div>

        <!-- Navigation Section -->
        <nav class="nav-section">
            <ul class="nav-menu">
                <li><a href="/index.php" class="nav-link">Home</a></li>
                <li><a href="/pages/shop/index.php" class="nav-link">Shop</a></li>
                <li><a href="/pages/cart/index.php" class="nav-link">Cart</a></li>
                <li><a href="/pages/about/index.php" class="nav-link">About</a></li>
            </ul>
        </nav>

        <!-- Search Section -->
        <div class="search-container">
            <input type="text" placeholder="Search products..." class="search-input">
            <button class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- Actions Section -->
        <div class="header-actions">
            <a href="/pages/cart/index.php" class="icon-link cart-link" id="cartBtn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cart-count">0</span>
            </a>

            <?php if (!$user): ?>
                <button class="icon-link user-link" id="loginBtn" aria-label="Login">
                    <i class="fas fa-user"></i>
                </button>

                <script>
                    document.getElementById('loginBtn').addEventListener('click', () => {
                        window.location.href = '/pages/login/index.php';
                    });
                </script>
            <?php else: ?>
                <div class="user-dropdown">
                    <span class="user-welcome">
                        <i class="fas fa-user"></i>
                        <?= htmlspecialchars($user['alias'] ?? $user['username']) ?>
                    </span>
                    <div class="dropdown-content">
                        <a href="/pages/account/index.php">Profile</a>
                        <a href="/handlers/logout.handler.php">Logout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateCartCount() {
                fetch('/handlers/cart.handler.php?action=count', {
                    credentials: 'include', // ✅ This sends PHPSESSID with request
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const el = document.getElementById('cart-count');
                        if (el) el.textContent = data.count;
                    } else {
                        console.warn('Cart count error:', data.error);
                    }
                })
                .catch(err => console.error('Failed to fetch cart count', err));
            }

            updateCartCount();
            window.addEventListener('cartUpdated', updateCartCount);
        });
    </script>
</header>