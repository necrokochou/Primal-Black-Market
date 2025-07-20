<?php require_once LAYOUTS_PATH . '/header.php'; ?>

<!-- 404 Error Page Specific Styles -->
<link rel="stylesheet" href="/assets/css/primal-404.css">

<main class="primal-404-bg">
    <div class="error-container">
        
        <!-- Error Hero Section -->
        <section class="error-hero">
            <div class="error-number">
                <span class="four">4</span>
                <span class="zero">0</span>
                <span class="four">4</span>
            </div>
            <div class="error-skull">
                <i class="fas fa-skull"></i>
            </div>
            <h1 class="error-title">Lost in the Primal Void</h1>
            <p class="error-subtitle">
                The path you seek has vanished into the shadows of the black market. 
                Even our most experienced trackers couldn't find this treasure.
            </p>
        </section>
        
        <!-- Error Message Section -->
        <section class="error-message">
            <div class="error-card">
                <div class="error-icon">
                    <i class="fas fa-compass"></i>
                </div>
                <h2 class="error-card-title">Page Not Found</h2>
                <p class="error-card-description">
                    The rare artifact you're looking for seems to have disappeared from our vaults. 
                    Perhaps it was claimed by another collector, or maybe it never existed in this realm.
                </p>
            </div>
        </section>
        
        <!-- Navigation Options -->
        <section class="error-navigation">
            <h3 class="nav-title">Find Your Way Back</h3>
            <div class="nav-options">
                <a href="/" class="nav-option">
                    <div class="nav-option-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="nav-option-content">
                        <h4>Return Home</h4>
                        <p>Go back to the main marketplace</p>
                    </div>
                </a>
                
                <a href="/pages/shop" class="nav-option">
                    <div class="nav-option-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="nav-option-content">
                        <h4>Browse Shop</h4>
                        <p>Explore our collection of rare items</p>
                    </div>
                </a>
                
                <a href="/pages/about" class="nav-option">
                    <div class="nav-option-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="nav-option-content">
                        <h4>About Us</h4>
                        <p>Learn about our primal marketplace</p>
                    </div>
                </a>
                
                <a href="javascript:history.back()" class="nav-option">
                    <div class="nav-option-icon">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <div class="nav-option-content">
                        <h4>Go Back</h4>
                        <p>Return to your previous location</p>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- Search Section -->
        <section class="error-search">
            <div class="search-card">
                <h3 class="search-title">
                    <i class="fas fa-search"></i>
                    Search for Treasures
                </h3>
                <p class="search-description">
                    Can't find what you're looking for? Use our search to discover hidden gems in our collection.
                </p>
                <form class="search-form" action="/pages/shop" method="GET">
                    <div class="search-input-group">
                        <input 
                            type="text" 
                            name="search" 
                            class="search-input" 
                            placeholder="Search for items, categories, or sellers..."
                            required
                        >
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                            Hunt
                        </button>
                    </div>
                </form>
            </div>
        </section>
        
        <!-- Featured Categories -->
        <section class="error-categories">
            <h3 class="categories-title">Popular Categories</h3>
            <div class="categories-grid">
                <a href="/pages/shop?category=weapons" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-sword"></i>
                    </div>
                    <span>Weapons</span>
                </a>
                
                <a href="/pages/shop?category=armor" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span>Armor</span>
                </a>
                
                <a href="/pages/shop?category=potions" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <span>Potions</span>
                </a>
                
                <a href="/pages/shop?category=artifacts" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <span>Artifacts</span>
                </a>
                
                <a href="/pages/shop?category=materials" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <span>Materials</span>
                </a>
                
                <a href="/pages/shop?category=pets" class="category-item">
                    <div class="category-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <span>Pets</span>
                </a>
            </div>
        </section>
        
        <!-- Contact Support -->
        <section class="error-support">
            <div class="support-card">
                <div class="support-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="support-title">Need Assistance?</h3>
                <p class="support-description">
                    Our primal guides are always ready to help you navigate the marketplace. 
                    Reach out if you need assistance finding what you're looking for.
                </p>
                <div class="support-actions">
                    <a href="mailto:support@primalblackmarket.com" class="support-btn primary">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                    <a href="tel:+1-800-PRIMAL-1" class="support-btn secondary">
                        <i class="fas fa-phone"></i>
                        Call Us
                    </a>
                </div>
            </div>
        </section>
        
    </div>
</main>

<!-- 404 Page JavaScript -->
<script src="/assets/js/primal-404.js"></script>

<?php require_once LAYOUTS_PATH . '/footer.php'; ?>
 