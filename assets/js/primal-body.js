/**
 * PRIMAL BLACK MARKET BODY INTERACTIONS
 * Advanced JavaScript for Featured Products, Weekly Top Selling, and Promo Sections
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // ================================
    
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe all animated sections
    const animatedSections = document.querySelectorAll('.featured-products, .weekly-top-selling, .promo-video-section, .delivery-info');
    animatedSections.forEach(section => {
        section.style.animationPlayState = 'paused';
        animationObserver.observe(section);
    });
    
    // ================================
    // PRODUCT CARD ENHANCED INTERACTIONS
    // ================================
    
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach((card, index) => {
        // Enhanced hover effect with 3D tilt
        card.addEventListener('mouseenter', function(e) {
            this.style.transform = 'translateY(-8px) scale(1.02) perspective(1000px) rotateX(5deg)';
            this.style.transition = 'all 0.4s cubic-bezier(0.23, 1, 0.32, 1)';
            
            // Add glow effect
            this.style.boxShadow = `
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 8px 16px rgba(127, 79, 36, 0.2),
                0 0 20px rgba(255, 140, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1)
            `;
        });
        
        card.addEventListener('mouseleave', function(e) {
            this.style.transform = 'translateY(0) scale(1) perspective(1000px) rotateX(0deg)';
            this.style.boxShadow = `
                0 8px 32px rgba(0, 0, 0, 0.3),
                0 2px 8px rgba(127, 79, 36, 0.1)
            `;
        });
        
        // Mouse move parallax effect
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `
                translateY(-8px) scale(1.02) 
                perspective(1000px) 
                rotateX(${rotateX}deg) 
                rotateY(${rotateY}deg)
            `;
        });
        
        // Stagger animation on page load
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // ================================
    // ADD TO CART ENHANCED FUNCTIONALITY
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
        
        // Enhanced button hover effects
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
    // PROMO SECTION INTERACTIONS
    // ================================
    
    const promoCode = document.querySelector('.promo-code b');
    if (promoCode) {
        promoCode.addEventListener('click', function() {
            // Copy to clipboard
            navigator.clipboard.writeText(this.textContent).then(() => {
                const originalText = this.textContent;
                this.textContent = 'COPIED!';
                this.style.color = 'var(--primal-green)';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.color = 'var(--primal-orange)';
                }, 2000);
            });
            
            // Add click effect
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
        
        // Add cursor pointer and tooltip
        promoCode.style.cursor = 'pointer';
        promoCode.title = 'Click to copy promo code';
    }
    
    const promoButton = document.querySelector('.promo-banner .btn');
    if (promoButton) {
        promoButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Create sparkle effect
            for (let i = 0; i < 6; i++) {
                createSparkle(this);
            }
            
            // Redirect to shop
            setTimeout(() => {
                window.location.href = '/pages/shop/index.php';
            }, 300);
        });
    }
    
    // ================================
    // DELIVERY INFO INTERACTION
    // ================================
    
    const deliveryInfo = document.querySelector('.delivery-info');
    if (deliveryInfo) {
        deliveryInfo.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = `
                0 12px 35px rgba(0, 0, 0, 0.4),
                0 0 25px rgba(127, 79, 36, 0.15)
            `;
        });
        
        deliveryInfo.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = `
                0 8px 25px rgba(0, 0, 0, 0.3),
                0 0 15px rgba(127, 79, 36, 0.1)
            `;
        });
    }
    
    // ================================
    // UTILITY FUNCTIONS
    // ================================
    
    function createSparkle(element) {
        const sparkle = document.createElement('div');
        const rect = element.getBoundingClientRect();
        
        sparkle.style.cssText = `
            position: fixed;
            left: ${rect.left + Math.random() * rect.width}px;
            top: ${rect.top + Math.random() * rect.height}px;
            width: 4px;
            height: 4px;
            background: var(--primal-orange);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            animation: sparkleAnimation 1s ease-out forwards;
        `;
        
        document.body.appendChild(sparkle);
        
        setTimeout(() => {
            if (document.body.contains(sparkle)) {
                document.body.removeChild(sparkle);
            }
        }, 1000);
    }
    
    // ================================
    // PERFORMANCE OPTIMIZATIONS
    // ================================
    
    // Throttle scroll events
    let ticking = false;
    function updateScrollEffects() {
        // Add scroll-based parallax effects here if needed
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    });
    
    // Preload next section images
    const images = document.querySelectorAll('.product-image img');
    images.forEach(img => {
        const imageLoader = new Image();
        imageLoader.src = img.src;
    });
    
    console.log('🔥 Primal Black Market body interactions loaded successfully!');
});

// ================================
// CSS KEYFRAMES FOR JAVASCRIPT ANIMATIONS
// ================================

const style = document.createElement('style');
style.textContent = `
    @keyframes rippleEffect {
        to {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }
    
    @keyframes sparkleAnimation {
        0% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translateY(-50px) scale(0);
            opacity: 0;
        }
    }
    
    .animate-in {
        animation-play-state: running !important;
    }
`;

document.head.appendChild(style);
