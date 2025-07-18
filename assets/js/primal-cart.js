/**
 * PRIMAL BLACK MARKET CART PAGE
 * Enhanced JavaScript for Premium Cart Experience
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // CART INITIALIZATION
    // ================================
    
    console.log('🛒 Initializing Primal Black Market Cart...');
    
    // Get cart elements
    const cartItemsDiv = document.getElementById('cart-items');
    const updateCartBtn = document.getElementById('update-cart-btn');
    const checkoutBtn = document.getElementById('checkout-btn');
    const voucherInput = document.getElementById('cart-voucher');
    const voucherBtn = document.querySelector('.cart-voucher-btn');
    
    // ================================
    // ENHANCED CART RENDERING
    // ================================
    
    function renderCartEnhanced() {
        const cart = getCart();
        
        if (!cartItemsDiv) return;
        
        if (!cart.length) {
            cartItemsDiv.innerHTML = `
                <div class="cart-empty-message">
                    <i class="fas fa-shopping-cart" style="font-size: 4rem; color: rgba(127, 79, 36, 0.3); margin-bottom: 2rem;"></i>
                    <h3 style="font-family: 'Cinzel', serif; font-size: 1.8rem; color: var(--primal-beige-light); margin-bottom: 1rem;">Your Cart is Empty</h3>
                    <p style="font-size: 1.1rem; margin-bottom: 2rem; color: rgba(255, 255, 255, 0.6);">Discover our primal collection and add some wild items to your cart.</p>
                    <a href="/pages/shop/index.php" class="primal-btn-primary" style="display: inline-block; padding: 1rem 2rem; background: linear-gradient(135deg, var(--primal-orange), var(--primal-brown)); color: white; text-decoration: none; border-radius: 15px; font-weight: 600; transition: all 0.3s ease;">
                        <i class="fas fa-shopping-bag" style="margin-right: 0.5rem;"></i>
                        Continue Shopping
                    </a>
                </div>
            `;
            updateCartSummary(0, 0, 0, 0);
            return;
        }
        
        let subtotal = 0;
        
        cartItemsDiv.innerHTML = cart.map((item, idx) => {
            const itemTotal = item.price * item.qty;
            subtotal += itemTotal;
            
            return `
                <div class="cart-row" data-item-index="${idx}">
                    <div class="cart-row-product">
                        <img src="/assets/images/example.png" alt="${item.title}" class="cart-row-img">
                        <div class="cart-row-info">
                            <div class="cart-row-title">${item.title}</div>
                            <div class="cart-row-color">Set : Colour: <span>${item.color || 'Default'}</span></div>
                            <div class="cart-row-price" style="font-size: 0.9rem; color: var(--primal-orange); margin-top: 0.5rem;">
                                $${item.price.toFixed(2)} each
                            </div>
                        </div>
                    </div>
                    <div class="cart-row-qty">
                        <button class="qty-btn qty-decrease" data-index="${idx}">-</button>
                        <span class="cart-qty">${item.qty}</span>
                        <button class="qty-btn qty-increase" data-index="${idx}">+</button>
                    </div>
                    <div class="cart-row-total">$${itemTotal.toFixed(2)}</div>
                    <div class="cart-row-action">
                        <button class="remove-btn" data-index="${idx}" title="Remove item">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
        
        // Update summary
        const discount = subtotal > 0 ? subtotal * 0.10 : 0;
        const delivery = subtotal > 0 ? 50 : 0;
        const total = subtotal - discount + delivery;
        
        updateCartSummary(subtotal, discount, delivery, total);
        
        // Add event listeners to new elements
        addCartEventListeners();
        
        // Trigger entrance animations
        setTimeout(() => {
            const cartRows = document.querySelectorAll('.cart-row');
            cartRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
                row.classList.add('animate-in');
            });
        }, 100);
    }
    
    // ================================
    // CART SUMMARY UPDATES
    // ================================
    
    function updateCartSummary(subtotal, discount, delivery, total) {
        const subtotalEl = document.getElementById('cart-subtotal');
        const discountEl = document.getElementById('cart-discount');
        const deliveryEl = document.getElementById('cart-delivery');
        const totalEl = document.getElementById('cart-total');
        
        if (subtotalEl) {
            subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
            animateValue(subtotalEl, subtotal);
        }
        
        if (discountEl) {
            discountEl.textContent = `-$${discount.toFixed(2)}`;
            animateValue(discountEl, discount);
        }
        
        if (deliveryEl) {
            deliveryEl.textContent = `$${delivery.toFixed(2)}`;
            animateValue(deliveryEl, delivery);
        }
        
        if (totalEl) {
            totalEl.textContent = `$${total.toFixed(2)}`;
            animateValue(totalEl, total);
        }
    }
    
    // ================================
    // ENHANCED CART INTERACTIONS
    // ================================
    
    function addCartEventListeners() {
        // Quantity buttons
        document.querySelectorAll('.qty-decrease').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                updateQuantityWithAnimation(index, -1);
            });
        });
        
        document.querySelectorAll('.qty-increase').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                updateQuantityWithAnimation(index, 1);
            });
        });
        
        // Remove buttons
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                removeItemWithAnimation(index);
            });
        });
        
        // Add hover effects to cart rows
        document.querySelectorAll('.cart-row').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    function updateQuantityWithAnimation(index, delta) {
        const cart = getCart();
        const item = cart[index];
        
        if (!item) return;
        
        const newQty = item.qty + delta;
        
        if (newQty <= 0) {
            removeItemWithAnimation(index);
            return;
        }
        
        // Update quantity with visual feedback
        item.qty = newQty;
        localStorage.setItem('pbm_cart', JSON.stringify(cart));
        
        // Find the quantity display and animate it
        const qtyDisplay = document.querySelector(`[data-item-index="${index}"] .cart-qty`);
        if (qtyDisplay) {
            qtyDisplay.style.transform = 'scale(1.2)';
            qtyDisplay.style.color = 'var(--primal-orange)';
            
            setTimeout(() => {
                qtyDisplay.textContent = newQty;
                qtyDisplay.style.transform = 'scale(1)';
                qtyDisplay.style.color = 'var(--primal-beige-light)';
            }, 150);
        }
        
        // Update totals
        renderCartEnhanced();
        
        // Trigger cart update event
        window.dispatchEvent(new Event('cartUpdated'));
        
        // Show update notification
        showUpdateNotification(`Quantity updated to ${newQty}`);
    }
    
    function removeItemWithAnimation(index) {
        const cart = getCart();
        const item = cart[index];
        
        if (!item) return;
        
        // Get the cart row element
        const cartRow = document.querySelector(`[data-item-index="${index}"]`);
        
        if (cartRow) {
            // Animate removal
            cartRow.style.transform = 'translateX(-100%)';
            cartRow.style.opacity = '0';
            
            setTimeout(() => {
                // Remove from cart
                cart.splice(index, 1);
                localStorage.setItem('pbm_cart', JSON.stringify(cart));
                
                // Re-render cart
                renderCartEnhanced();
                
                // Trigger cart update event
                window.dispatchEvent(new Event('cartUpdated'));
                
                // Show removal notification
                showUpdateNotification(`${item.title} removed from cart`, 'warning');
            }, 300);
        }
    }
    
    // ================================
    // VOUCHER FUNCTIONALITY
    // ================================
    
    let appliedVoucher = null;
    
    function initializeVoucherSystem() {
        if (voucherBtn) {
            voucherBtn.addEventListener('click', function() {
                const voucherCode = voucherInput.value.trim().toLowerCase();
                
                if (!voucherCode) {
                    showUpdateNotification('Please enter a voucher code', 'error');
                    return;
                }
                
                applyVoucher(voucherCode);
            });
        }
        
        if (voucherInput) {
            voucherInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    voucherBtn.click();
                }
            });
        }
    }
    
    function applyVoucher(code) {
        const validVouchers = {
            'primal10': { discount: 0.10, description: '10% off' },
            'wild20': { discount: 0.20, description: '20% off' },
            'savage15': { discount: 0.15, description: '15% off' },
            'beast25': { discount: 0.25, description: '25% off' }
        };
        
        if (validVouchers[code]) {
            appliedVoucher = validVouchers[code];
            
            // Visual feedback
            voucherInput.style.borderColor = 'var(--primal-green)';
            voucherBtn.textContent = 'Applied!';
            voucherBtn.style.background = 'linear-gradient(135deg, var(--primal-green), #28a745)';
            
            // Update cart summary
            renderCartEnhanced();
            
            showUpdateNotification(`Voucher applied: ${appliedVoucher.description}`, 'success');
            
            // Reset button after delay
            setTimeout(() => {
                voucherBtn.textContent = 'Apply';
                voucherBtn.style.background = 'linear-gradient(135deg, var(--primal-green), #4a5a3a)';
            }, 3000);
        } else {
            // Invalid voucher
            voucherInput.style.borderColor = '#dc3545';
            voucherBtn.textContent = 'Invalid';
            voucherBtn.style.background = 'linear-gradient(135deg, #dc3545, #b02a37)';
            
            showUpdateNotification('Invalid voucher code', 'error');
            
            // Reset after delay
            setTimeout(() => {
                voucherInput.style.borderColor = 'rgba(127, 79, 36, 0.3)';
                voucherBtn.textContent = 'Apply';
                voucherBtn.style.background = 'linear-gradient(135deg, var(--primal-green), #4a5a3a)';
            }, 3000);
        }
    }
    
    // ================================
    // CHECKOUT FUNCTIONALITY
    // ================================
    
    function initializeCheckout() {
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                const cart = getCart();
                
                if (!cart.length) {
                    showUpdateNotification('Your cart is empty', 'error');
                    return;
                }
                
                // Checkout animation
                this.style.transform = 'scale(0.95)';
                this.textContent = 'Processing...';
                
                setTimeout(() => {
                    // Simulate checkout process
                    showCheckoutModal();
                    
                    // Reset button
                    this.style.transform = 'scale(1)';
                    this.textContent = 'Checkout Now';
                }, 1000);
            });
        }
    }
    
    function showCheckoutModal() {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        `;
        
        modal.innerHTML = `
            <div style="
                background: var(--cart-card-bg);
                border-radius: 20px;
                padding: 3rem;
                max-width: 500px;
                width: 90%;
                text-align: center;
                border: 1px solid var(--cart-border);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            ">
                <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--primal-green); margin-bottom: 1rem;"></i>
                <h2 style="font-family: 'Cinzel', serif; font-size: 2rem; color: var(--primal-beige-light); margin-bottom: 1rem;">Checkout Successful!</h2>
                <p style="font-family: 'Inter', sans-serif; color: rgba(255, 255, 255, 0.8); margin-bottom: 2rem;">
                    Your order has been processed. You will receive a confirmation email shortly.
                </p>
                <button id="closeModal" style="
                    background: linear-gradient(135deg, var(--primal-orange), var(--primal-brown));
                    color: white;
                    border: none;
                    padding: 1rem 2rem;
                    border-radius: 12px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                ">Close</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal functionality
        const closeBtn = modal.querySelector('#closeModal');
        closeBtn.addEventListener('click', function() {
            modal.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                document.body.removeChild(modal);
                
                // Clear cart after successful checkout
                localStorage.removeItem('pbm_cart');
                renderCartEnhanced();
                window.dispatchEvent(new Event('cartUpdated'));
            }, 300);
        });
        
        // Close on background click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeBtn.click();
            }
        });
    }
    
    // ================================
    // UTILITY FUNCTIONS
    // ================================
    
    function getCart() {
        return JSON.parse(localStorage.getItem('pbm_cart') || '[]');
    }
    
    function animateValue(element, value) {
        element.style.transform = 'scale(1.1)';
        element.style.color = 'var(--primal-orange)';
        
        setTimeout(() => {
            element.style.transform = 'scale(1)';
            element.style.color = 'var(--primal-beige-light)';
        }, 200);
    }
    
    function showUpdateNotification(message, type = 'info') {
        const notification = document.createElement('div');
        
        let bgColor, icon;
        switch (type) {
            case 'success':
                bgColor = 'linear-gradient(135deg, var(--primal-green), #28a745)';
                icon = 'fas fa-check-circle';
                break;
            case 'error':
                bgColor = 'linear-gradient(135deg, #dc3545, #b02a37)';
                icon = 'fas fa-exclamation-circle';
                break;
            case 'warning':
                bgColor = 'linear-gradient(135deg, #ffc107, #e0a800)';
                icon = 'fas fa-exclamation-triangle';
                break;
            default:
                bgColor = 'linear-gradient(135deg, var(--primal-orange), var(--primal-brown))';
                icon = 'fas fa-info-circle';
        }
        
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        `;
        
        notification.innerHTML = `
            <i class="${icon}" style="margin-right: 0.5rem;"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Animate out
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 400);
        }, 3000);
    }
    
    // ================================
    // UPDATE CART BUTTON FUNCTIONALITY
    // ================================
    
    function initializeUpdateButton() {
        if (updateCartBtn) {
            updateCartBtn.addEventListener('click', function() {
                // Visual feedback
                this.style.transform = 'scale(0.95)';
                this.textContent = 'Updating...';
                
                setTimeout(() => {
                    renderCartEnhanced();
                    
                    // Reset button
                    this.style.transform = 'scale(1)';
                    this.textContent = 'Update Cart';
                    
                    showUpdateNotification('Cart updated successfully!', 'success');
                }, 500);
            });
        }
    }
    
    // ================================
    // INITIALIZATION
    // ================================
    
    // Initialize all features
    initializeVoucherSystem();
    initializeCheckout();
    initializeUpdateButton();
    
    // Render cart on page load
    renderCartEnhanced();
    
    // Listen for cart updates from other pages
    window.addEventListener('cartUpdated', function() {
        renderCartEnhanced();
    });
    
    console.log('✅ Primal Black Market Cart initialized successfully!');
});

// ================================
// GLOBAL FUNCTIONS (for backward compatibility)
// ================================

function renderCart() {
    // Trigger the enhanced render function
    const event = new CustomEvent('cartNeedsRender');
    window.dispatchEvent(event);
}

function updateQty(index, delta) {
    const cart = JSON.parse(localStorage.getItem('pbm_cart') || '[]');
    const item = cart[index];
    
    if (!item) return;
    
    const newQty = item.qty + delta;
    
    if (newQty <= 0) {
        removeCartItem(index);
        return;
    }
    
    item.qty = newQty;
    localStorage.setItem('pbm_cart', JSON.stringify(cart));
    
    // Trigger re-render
    renderCart();
    window.dispatchEvent(new Event('cartUpdated'));
}

function removeCartItem(index) {
    const cart = JSON.parse(localStorage.getItem('pbm_cart') || '[]');
    cart.splice(index, 1);
    localStorage.setItem('pbm_cart', JSON.stringify(cart));
    
    // Trigger re-render
    renderCart();
    window.dispatchEvent(new Event('cartUpdated'));
}

// ================================
// CSS ANIMATIONS
// ================================

const cartStyles = document.createElement('style');
cartStyles.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
    
    .animate-in {
        animation: fadeInUp 0.6s ease-out both;
    }
`;

document.head.appendChild(cartStyles);
