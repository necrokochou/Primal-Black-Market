/**
 * PRIMAL BLACK MARKET SHOP PAGE
 * Advanced JavaScript for Shop Interactions and Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // SHOP INITIALIZATION
    // ================================
    
    console.log('🛍️ Initializing Primal Black Market Shop...');
    
    // Get shop elements
    const shopMain = document.querySelector('.shop-main');
    const sidebar = document.querySelector('.shop-sidebar');
    const productsGrid = document.querySelector('.shop-products-grid');
    const categoryLinks = document.querySelectorAll('.shop-sidebar a');
    const productCards = document.querySelectorAll('.product-card');
    
    // ================================
    // CATEGORY FILTERING ENHANCEMENTS
    // ================================
    
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading effect
            if (!this.classList.contains('active')) {
                showLoadingState();
                
                // Remove active class from all links
                categoryLinks.forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Add click effect
                this.style.transform = 'translateX(8px) scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'translateX(5px) scale(1)';
                }, 150);
                
                // Simulate loading (since this is a demo)
                setTimeout(() => {
                    hideLoadingState();
                    animateProductsIn();
                }, 800);
            }
        });
        
        // Enhanced hover effects
        link.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateX(3px)';
            }
        });
        
        link.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateX(0)';
            } else {
                this.style.transform = 'translateX(5px)';
            }
        });
    });
    
    // ================================
    // PRODUCT CARD ENHANCED INTERACTIONS
    // ================================
    
    productCards.forEach((card, index) => {
        // 3D hover effect with mouse tracking
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 8;
            const rotateY = (centerX - x) / 8;
            
            this.style.transform = `
                translateY(-8px) scale(1.02) 
                perspective(1000px) 
                rotateX(${rotateX}deg) 
                rotateY(${rotateY}deg)
            `;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });
        
        // Product quick view on double click
        card.addEventListener('dblclick', function() {
            showProductQuickView(this);
        });
        
        // Stagger animation delay
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // ================================
    // ADD TO CART ENHANCED FUNCTIONALITY (MATCHING HOMEPAGE)
    // ================================
    
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const card = this.closest('.product-card');
            const listingID = card.getAttribute('data-id');
            const title = card.getAttribute('data-title');
            const price = parseFloat(card.getAttribute('data-price'));
            const image = card.getAttribute('data-image');
            
            // Create floating animation (matching homepage)
            const rect = this.getBoundingClientRect();
            const floating = document.createElement('div');
            floating.textContent = '+1';
            floating.style.cssText = `
                position: fixed;
                left: ${rect.left + rect.width / 2}px;
                top: ${rect.top}px;
                color: var(--primal-orange);
                font-weight: bold;
                font-size: 1.2rem;
                pointer-events: none;
                z-index: 9999;
                transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            `;
            
            document.body.appendChild(floating);
            
            // Animate floating number
            requestAnimationFrame(() => {
                floating.style.transform = 'translateY(-50px)';
                floating.style.opacity = '0';
            });
            
            // Remove element after animation
            setTimeout(() => {
                if (document.body.contains(floating)) {
                    document.body.removeChild(floating);
                }
            }, 800);
            
            // Button state changes
            const originalText = this.textContent;
            this.textContent = 'Added!';
            this.style.background = 'linear-gradient(135deg, var(--primal-green), #28a745)';
            this.style.transform = 'scale(0.95)';
            
            // Add to cart logic (matching homepage)
            fetch('/handlers/cart.handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'add',
                    listing_id: listingID,
                    quantity: 1
                })
            })
            .then(res => {
                if (!res.ok) throw new Error(`Server returned ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.error);
                console.log('Cart updated:', data);
            })
            .catch(err => {
                console.error('Network/cart error:', err);
            });
            
            // Trigger cart update event
            window.dispatchEvent(new Event('cartUpdated'));
            
            // Create success ripple effect
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                top: 50%; left: 50%;
                width: 0; height: 0;
                background: rgba(255, 140, 0, 0.3);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                pointer-events: none;
                animation: rippleEffect 0.6s ease-out;
            `;
            
            this.style.position = 'relative';
            this.appendChild(ripple);
            
            // Reset button after delay
            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = 'linear-gradient(135deg, var(--primal-brown) 0%, var(--primal-brown-dark) 100%)';
                this.style.transform = 'scale(1)';
                if (this.contains(ripple)) {
                    this.removeChild(ripple);
                }
            }, 1500);
        });
        
        // Enhanced button hover effects (matching homepage)
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
            this.style.boxShadow = '0 8px 25px rgba(255, 140, 0, 0.4)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 4px 15px rgba(127, 79, 36, 0.3)';
        });
    });
    
    // ================================
    // SHOP STATISTICS
    // ================================
    
    function updateShopStats() {
        const totalProducts = productCards.length;
        const activeCategory = document.querySelector('.shop-sidebar a.active')?.textContent || 'All';
        
        // Create or update stats bar
        let statsBar = document.querySelector('.shop-stats');
        if (!statsBar) {
            statsBar = document.createElement('div');
            statsBar.className = 'shop-stats';
            statsBar.innerHTML = `
                <div class="shop-stats-item">
                    <i class="fas fa-boxes"></i>
                    <span>Products: <span class="shop-stats-count">${totalProducts}</span></span>
                </div>
                <div class="shop-stats-item">
                    <i class="fas fa-tag"></i>
                    <span>Category: <span class="shop-stats-count">${activeCategory}</span></span>
                </div>
                <div class="shop-stats-item">
                    <i class="fas fa-fire"></i>
                    <span>Status: <span class="shop-stats-count">Active</span></span>
                </div>
            `;
            
            const shopProducts = document.querySelector('.shop-products');
            shopProducts.insertBefore(statsBar, shopProducts.querySelector('h2').nextSibling);
        }
    }
    
    // ================================
    // SEARCH FUNCTIONALITY
    // ================================
    
    function initializeShopSearch() {
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.toLowerCase().trim();
                
                searchTimeout = setTimeout(() => {
                    filterProductsBySearch(query);
                }, 300);
            });
        }
    }
    
    function filterProductsBySearch(query) {
        productCards.forEach(card => {
            const title = card.getAttribute('data-title')?.toLowerCase() || '';
            const isVisible = !query || title.includes(query);
            
            if (isVisible) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.4s ease-out both';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update stats
        const visibleCount = Array.from(productCards).filter(card => 
            card.style.display !== 'none'
        ).length;
        
        const statsCount = document.querySelector('.shop-stats-count');
        if (statsCount) {
            statsCount.textContent = visibleCount;
        }
    }
    
    // ================================
    // UTILITY FUNCTIONS
    // ================================
    
    function showLoadingState() {
        let loadingDiv = document.querySelector('.shop-loading');
        if (!loadingDiv) {
            loadingDiv = document.createElement('div');
            loadingDiv.className = 'shop-loading';
            loadingDiv.innerHTML = 'Loading products...';
            productsGrid.appendChild(loadingDiv);
        }
        
        // Hide product cards
        productCards.forEach(card => {
            card.style.opacity = '0.3';
            card.style.pointerEvents = 'none';
        });
    }
    
    function hideLoadingState() {
        const loadingDiv = document.querySelector('.shop-loading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
        
        // Show product cards
        productCards.forEach(card => {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
        });
    }
    
    function animateProductsIn() {
        productCards.forEach((card, index) => {
            card.style.animation = 'none';
            card.offsetHeight; // Trigger reflow
            card.style.animation = `fadeInUp 0.6s ease-out ${index * 0.1}s both`;
        });
    }
    
    function showProductQuickView(card) {
        const title = card.getAttribute('data-title');
        const price = card.getAttribute('data-price');
        
        // Create quick view modal (simplified)
        console.log(`Quick view for: ${title} - $${price}`);
        
        // Add visual feedback
        card.style.transform = 'scale(1.05)';
        setTimeout(() => {
            card.style.transform = 'scale(1)';
        }, 200);
    }
    
    // ================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // ================================
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe animated elements
    const animatedElements = document.querySelectorAll('.shop-sidebar, .shop-products, .product-card');
    animatedElements.forEach(el => animationObserver.observe(el));
    
    // ================================
    // SCROLL EFFECTS
    // ================================
    
    let ticking = false;
    function updateScrollEffects() {
        const scrollY = window.scrollY;
        
        // Parallax effect for sidebar
        if (sidebar && window.innerWidth > 992) {
            const offset = scrollY * 0.1;
            sidebar.style.transform = `translateY(${offset}px)`;
        }
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    });
    
    // ================================
    // INITIALIZATION
    // ================================
    
    // Initialize all features
    updateShopStats();
    initializeShopSearch();
    
    // Set initial active state
    const currentCategory = new URLSearchParams(window.location.search).get('category');
    if (currentCategory) {
        categoryLinks.forEach(link => {
            if (link.href.includes(`category=${encodeURIComponent(currentCategory)}`)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
    
    console.log('✅ Primal Black Market Shop initialized successfully!');
});

// ================================
// CSS ANIMATIONS FOR JAVASCRIPT
// ================================

const shopStyles = document.createElement('style');
shopStyles.textContent = `
    @keyframes rippleEffect {
        to {
            width: 300px;
            height: 300px;
            opacity: 0;
        }
    }
    
    .animate-in {
        animation-play-state: running !important;
    }
    
    .shop-loading {
        display: flex !important;
    }
`;

document.head.appendChild(shopStyles);
