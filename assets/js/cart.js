// // Render cart items in the new table layout for cart page
// function renderCart() {
//   const cart = getCart();
//   const cartItemsDiv = document.getElementById('cart-items');
//   if (!cartItemsDiv) return;
//   if (!cart.length) {
//     cartItemsDiv.innerHTML = '<div class="cart-empty-message">Your cart is empty.</div>';
//     const ids = ['cart-subtotal','cart-discount','cart-delivery','cart-total'];
//     ids.forEach(id => { const el = document.getElementById(id); if (el) el.textContent = id==='cart-discount' ? '-0.00 USD' : '0.00 USD'; });
//     return;
//   }
//   let subtotal = 0;
//   cartItemsDiv.innerHTML = cart.map((item, idx) => {
//     const itemTotal = item.price * item.qty;
//     subtotal += itemTotal;
//     return `
//       <div class="cart-row">
//         <div class="cart-row-product">
//           <img src="/assets/images/example.png" alt="${item.title}" class="cart-row-img">
//           <div class="cart-row-info">
//             <div class="cart-row-title">${item.title}</div>
//             <div class="cart-row-color">Set : Colour: <span>${item.color || 'N/A'}</span></div>
//           </div>
//         </div>
//         <div class="cart-row-qty">
//           <button class="qty-btn" onclick="updateQty(${idx},-1)">-</button>
//           <span class="cart-qty">${item.qty}</span>
//           <button class="qty-btn" onclick="updateQty(${idx},1)">+</button>
//         </div>
//         <div class="cart-row-total">$${itemTotal.toLocaleString()}</div>
//         <div class="cart-row-action">
//           <button class="remove-btn" onclick="removeCartItem(${idx})"><i class="fa fa-trash"></i></button>
//         </div>
//       </div>
//     `;
//   }).join('');
//   // Summary
//   const subtotalEl = document.getElementById('cart-subtotal');
//   const discountEl = document.getElementById('cart-discount');
//   const deliveryEl = document.getElementById('cart-delivery');
//   const totalEl = document.getElementById('cart-total');
//   if (subtotalEl) subtotalEl.textContent = subtotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
//   const discount = subtotal > 0 ? subtotal * 0.10 : 0;
//   if (discountEl) discountEl.textContent = '-' + discount.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
//   const delivery = subtotal > 0 ? 50 : 0;
//   if (deliveryEl) deliveryEl.textContent = delivery.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
//   const total = subtotal - discount + delivery;
//   if (totalEl) totalEl.textContent = total.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
// }
// function updateQty(idx, delta) {
//   let cart = getCart();
//   if (!cart[idx]) return;
//   cart[idx].qty = Math.max(1, cart[idx].qty + delta);
//   setCart(cart);
//   renderCart();
//   if (typeof updateCartCount === 'function') updateCartCount();
// }
// function removeCartItem(idx) {
//   let cart = getCart();
//   cart.splice(idx, 1);
//   setCart(cart);
//   renderCart();
//   if (typeof updateCartCount === 'function') updateCartCount();
// }
// if (typeof window !== 'undefined') {
//   window.renderCart = renderCart;
//   window.updateQty = updateQty;
//   window.removeCartItem = removeCartItem;
// }
// // Render cart items in the new table layout for cart page
// function renderCart() {
//   const cart = JSON.parse(localStorage.getItem('pbm_cart') || '[]');
//   const cartItemsDiv = document.getElementById('cart-items');
//   if (!cartItemsDiv) return;
//   if (!cart.length) {
//     cartItemsDiv.innerHTML = '<div class="cart-empty-message">Your cart is empty.</div>';
//     const subtotalEl = document.getElementById('cart-subtotal');
//     const discountEl = document.getElementById('cart-discount');
//     const deliveryEl = document.getElementById('cart-delivery');
//     const totalEl = document.getElementById('cart-total');
//     if (subtotalEl) subtotalEl.textContent = '0.00 USD';
//     if (discountEl) discountEl.textContent = '-0.00 USD';
//     if (deliveryEl) deliveryEl.textContent = '0.00 USD';
//     if (totalEl) totalEl.textContent = '0.00 USD';
//     return;
//   }
//   let subtotal = 0;
//   cartItemsDiv.innerHTML = cart.map((item, idx) => {
//     const itemTotal = item.price * item.qty;
//     subtotal += itemTotal;
//     return `
//       <div class="cart-row">
//         <div class="cart-row-product">
//           <img src="/assets/images/example.png" alt="${item.title}" class="cart-row-img">
//           <div class="cart-row-info">
//             <div class="cart-row-title">${item.title}</div>
//             <div class="cart-row-color">Set : Colour: <span>${item.color || 'N/A'}</span></div>
//           </div>
//         </div>
//         <div class="cart-row-qty">
//           <button class="qty-btn" onclick="updateQty(${idx},-1)">-</button>
//           <span class="cart-qty">${item.qty}</span>
//           <button class="qty-btn" onclick="updateQty(${idx},1)">+</button>
//         </div>
//         <div class="cart-row-total">$${itemTotal.toLocaleString()}</div>
//         <div class="cart-row-action">
//           <button class="remove-btn" onclick="removeCartItem(${idx})"><i class="fa fa-trash"></i></button>
//         </div>
//       </div>
//     `;
//   }).join('');
//   // Summary
//   const subtotalEl = document.getElementById('cart-subtotal');
//   const discountEl = document.getElementById('cart-discount');
//   const deliveryEl = document.getElementById('cart-delivery');
//   const totalEl = document.getElementById('cart-total');
//   if (subtotalEl) subtotalEl.textContent = subtotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
//   const discount = subtotal > 0 ? subtotal * 0.10 : 0;
//   if (discountEl) discountEl.textContent = '-' + discount.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
//   const delivery = subtotal > 0 ? 50 : 0;
//   if (deliveryEl) deliveryEl.textContent = delivery.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
//   const total = subtotal - discount + delivery;
//   if (totalEl) totalEl.textContent = total.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
// }

// window.updateQty = function(idx, delta) {
//   let cart = JSON.parse(localStorage.getItem('pbm_cart') || '[]');
//   if (!cart[idx]) return;
//   cart[idx].qty = Math.max(1, cart[idx].qty + delta);
//   localStorage.setItem('pbm_cart', JSON.stringify(cart));
//   renderCart();
//   if (typeof updateCartCount === 'function') updateCartCount();
// }

// window.removeCartItem = function(idx) {
//   let cart = JSON.parse(localStorage.getItem('pbm_cart') || '[]');
//   cart.splice(idx, 1);
//   localStorage.setItem('pbm_cart', JSON.stringify(cart));
//   renderCart();
//   if (typeof updateCartCount === 'function') updateCartCount();
// }

// document.addEventListener('DOMContentLoaded', function() {
//   const updateBtn = document.getElementById('update-cart-btn');
//   if (updateBtn) updateBtn.onclick = function() { renderCart(); };
// });
// // Primal Black Market Cart Functionality
// // Uses localStorage for demo; ready for backend integration

// document.addEventListener('DOMContentLoaded', function() {
//     renderCart();
//     document.getElementById('checkout-btn').addEventListener('click', checkoutCart);
// });

// function getCart() {
//     return JSON.parse(localStorage.getItem('pbm_cart') || '[]');
// }

// function setCart(cart) {
//     localStorage.setItem('pbm_cart', JSON.stringify(cart));
// }

// function renderCart() {
//     const cart = getCart();
//     const cartItemsDiv = document.getElementById('cart-items');
//     const cartTotalSpan = document.getElementById('cart-total');
//     if (!cart.length) {
//         cartItemsDiv.innerHTML = '<p>Your cart is empty.</p>';
//         cartTotalSpan.textContent = '';
//         return;
//     }
//     let total = 0;
//     cartItemsDiv.innerHTML = cart.map((item, idx) => {
//         total += item.price * item.qty;
//         return `<div class="primal-card-secondary" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.2rem;padding:1.2rem 1.5rem;">
//             <span class="cart-title" style="font-weight:700;">${item.title}</span>
//             <span class="cart-qty" style="margin:0 1.2rem;">x${item.qty}</span>
//             <span class="cart-price" style="color:var(--primal-green-dark);font-weight:600;">$${item.price.toFixed(2)}</span>
//             <button class="primal-btn-primary" style="margin-left:1.2rem;" onclick="removeCartItem(${idx})">Remove</button>
//         </div>`;
//     }).join('');
//     cartTotalSpan.textContent = `Total: $${total.toFixed(2)}`;
// }

// function removeCartItem(idx) {
//     let cart = getCart();
//     cart.splice(idx, 1);
//     setCart(cart);
//     renderCart();
//     updateCartCount();
// }

// function checkoutCart() {
//     alert('Checkout is not implemented. Connect to backend for real orders.');
// }

// function updateCartCount() {
//     const cart = getCart();
//     const count = cart.reduce((sum, item) => sum + item.qty, 0);
//     const el = document.getElementById('cart-count');
//     if (el) el.textContent = count;
// }

// // Update cart count on all pages
// updateCartCount();
