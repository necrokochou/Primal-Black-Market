
<?php

$user = $_SESSION['user'] ?? null;
?>
<header class="site-header modern-clean-header">
    <link rel="stylesheet" href="/assets/css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <div class="header-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <a href="/" class="logo-link">
                <img src="/assets/images/CP Logo(ARCL).png" alt="Primal Black Market Logo" class="logo-img">
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
                <a href="/pages/login/index.php" class="icon-link user-link" id="loginBtn">
                    <i class="fas fa-user"></i>
                </a>
            <?php else: ?>
                <div class="user-dropdown">
                    <span class="user-welcome">
                        <i class="fas fa-user"></i>
                        <?= htmlspecialchars($user) ?>
                    </span>
                    <div class="dropdown-content">
                        <a href="/pages/profile/index.php">Profile</a>
                        <a href="/pages/logout/index.php">Logout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
    <script>
    // Animate cart count (use pbm_cart and sum qty)
    document.addEventListener('DOMContentLoaded', function() {
        function updateCartCount() {
            let count = 0;
            try {
                const cart = JSON.parse(localStorage.getItem('pbm_cart') || '[]');
                count = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
            } catch {}
            const el = document.getElementById('cart-count');
            if (el) el.textContent = count;
        }
        updateCartCount();
        window.addEventListener('cartUpdated', updateCartCount);
    });
    </script>
</header>
