// Primal Black Market Cart Functionality
// Uses localStorage for demo; ready for backend integration

document.addEventListener('DOMContentLoaded', function() {
    renderCart();
    document.getElementById('checkout-btn').addEventListener('click', checkoutCart);
});

function getCart() {
    return JSON.parse(localStorage.getItem('pbm_cart') || '[]');
}

function setCart(cart) {
    localStorage.setItem('pbm_cart', JSON.stringify(cart));
}

function renderCart() {
    const cart = getCart();
    const cartItemsDiv = document.getElementById('cart-items');
    const cartTotalSpan = document.getElementById('cart-total');
    if (!cart.length) {
        cartItemsDiv.innerHTML = '<p>Your cart is empty.</p>';
        cartTotalSpan.textContent = '';
        return;
    }
    let total = 0;
    cartItemsDiv.innerHTML = cart.map((item, idx) => {
        total += item.price * item.qty;
        return `<div class="cart-item">
            <span class="cart-title">${item.title}</span>
            <span class="cart-qty">x${item.qty}</span>
            <span class="cart-price">$${item.price.toFixed(2)}</span>
            <button class="btn btn-accent" onclick="removeCartItem(${idx})">Remove</button>
        </div>`;
    }).join('');
    cartTotalSpan.textContent = `Total: $${total.toFixed(2)}`;
}

function removeCartItem(idx) {
    let cart = getCart();
    cart.splice(idx, 1);
    setCart(cart);
    renderCart();
    updateCartCount();
}

function checkoutCart() {
    alert('Checkout is not implemented. Connect to backend for real orders.');
}

function updateCartCount() {
    const cart = getCart();
    const count = cart.reduce((sum, item) => sum + item.qty, 0);
    const el = document.getElementById('cart-count');
    if (el) el.textContent = count;
}

// Update cart count on all pages
updateCartCount();
