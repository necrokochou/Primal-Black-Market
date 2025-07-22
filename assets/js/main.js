// // Featured products carousel navigation
// const carousel = document.getElementById("featured-carousel");
// if (carousel) {
//   document
//     .querySelector(".carousel-prev")
//     ?.addEventListener("click", function () {
//       carousel.scrollBy({
//         left: -carousel.offsetWidth / 1.2,
//         behavior: "smooth",
//       });
//     });
//   document
//     .querySelector(".carousel-next")
//     ?.addEventListener("click", function () {
//       carousel.scrollBy({
//         left: carousel.offsetWidth / 1.2,
//         behavior: "smooth",
//       });
//     });
// }
// // Advanced JS for enhanced interactivity and cart/login/register
// document.addEventListener("DOMContentLoaded", function () {
//   // Smooth scroll for anchor links
//   document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
//     anchor.addEventListener("click", function (e) {
//       const target = document.querySelector(this.getAttribute("href"));
//       if (target) {
//         e.preventDefault();
//         target.scrollIntoView({ behavior: "smooth" });
//       }
//     });
//   });
// });

// // Header sticky effect
// const header = document.querySelector(".site-header");
// let lastScroll = 0;
// window.addEventListener("scroll", function () {
//   if (window.scrollY > 60) {
//     header.classList.add("scrolled");
//   } else {
//     header.classList.remove("scrolled");
//   }
//   lastScroll = window.scrollY;
// });

// // Product card hover effect (add shadow and scale)
// document.querySelectorAll(".product-card").forEach((card) => {
//   card.addEventListener("mouseenter", function () {
//     this.classList.add("active");
//   });
//   card.addEventListener("mouseleave", function () {
//     this.classList.remove("active");
//   });
// });

// // Newsletter form UX
// const newsletterForm = document.querySelector(".footer-newsletter form");
// if (newsletterForm) {
//   newsletterForm.addEventListener("submit", function (e) {
//     e.preventDefault();
//     const input = this.querySelector('input[type="email"]');
//     input.value = "";
//     input.placeholder = "Thank you for subscribing!";
//     setTimeout(() => {
//       input.placeholder = "Your email here";
//     }, 2000);
//   });
// }

// // Add-to-cart functionality for Primal Black Market
// document.querySelectorAll(".add-to-cart-btn").forEach(function (btn) {
//   btn.addEventListener("click", function () {
//     const card = btn.closest(".product-card");
//     const title = card.getAttribute("data-title");
//     function getCart() {
//       return JSON.parse(localStorage.getItem("pbm_cart") || "[]");
//     }
//     function setCart(cart) {
//       localStorage.setItem("pbm_cart", JSON.stringify(cart));
//       updateCartCount();
//     }
//     function addToCart(item) {
//       let cart = getCart();
//       let found = cart.find(
//         (i) => i.title === item.title && i.price === item.price
//       );
//       if (found) {
//         found.qty += 1;
//       } else {
//         cart.push(item);
//       }
//       setCart(cart);
//     }
//     function updateCartCount() {
//       const cart = getCart();
//       const count = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
//       const el = document.getElementById("cart-count");
//       if (el) el.textContent = count;
//     }
//     updateCartCount();
//     document.querySelectorAll(".add-to-cart-btn").forEach((btn) => {
//       btn.addEventListener("click", function () {
//         const card = this.closest(".product-card");
//         const title = card.getAttribute("data-title");
//         const price = parseFloat(card.getAttribute("data-price"));
//         const image = card.getAttribute("data-image");
//         addToCart({ title, price, image, qty: 1 });
//         this.textContent = "Added!";
//         setTimeout(() => {
//           this.textContent = "Add to Cart";
//         }, 1200);
//       });
//     });
//     if (idx > -1) {
//       cart[idx].qty += 1;
//       const card = this.closest(".product-card");
//       const title = card.getAttribute("data-title");
//       const price = parseFloat(card.getAttribute("data-price"));
//       let cart = getCart();
//       let found = cart.find((item) => item.title === title);
//       if (found) {
//         found.qty += 1;
//       } else {
//         cart.push({ title, price, qty: 1 });
//       }
//       setCart(cart);
//       this.textContent = "Added!";
//       setTimeout(() => {
//         this.textContent = "Add to Cart";
//       }, 1200);
//     }
//   });

//   // Login/Register modal (frontend only)
//   function showModal(type) {
//     let modal = document.getElementById("auth-modal");
//     if (!modal) {
//       modal = document.createElement("div");
//       modal.id = "auth-modal";
//       modal.innerHTML = `
//                 <div class="modal-bg"></div>
//                 <div class="modal-content">
//                     <span class="modal-close">&times;</span>
//                     <h2 id="modal-title"></h2>
//                     <form id="auth-form">
//                         <input type="text" placeholder="Username" required><br>
//                         <input type="password" placeholder="Password" required><br>
//                         <button type="submit">Submit</button>
//                     </form>
//                 </div>
//             `;
//       document.body.appendChild(modal);
//       modal.querySelector(".modal-bg").onclick = closeModal;
//       modal.querySelector(".modal-close").onclick = closeModal;
//       modal.querySelector("#auth-form").onsubmit = function (e) {
//         e.preventDefault();
//         closeModal();
//       };
//     }
//     modal.style.display = "block";
//     document.getElementById("modal-title").textContent =
//       type === "login" ? "Login" : "Register";
//   }
//   function closeModal() {
//     let modal = document.getElementById("auth-modal");
//     if (modal) modal.style.display = "none";
//   }
//   document.getElementById("loginBtn")?.addEventListener("click", function (e) {
//     e.preventDefault();
//     showModal("login");
//   });
//   document
//     .getElementById("registerBtn")
//     ?.addEventListener("click", function (e) {
//       e.preventDefault();
//       showModal("register");
//     });

//   // Add-to-cart functionality for Primal Black Market
//   document.querySelectorAll(".add-to-cart-btn").forEach(function (btn) {
//     btn.addEventListener("click", function () {
//       const card = btn.closest(".product-card");
//       const title = card.getAttribute("data-title");
//       const price = parseFloat(card.getAttribute("data-price"));
//       addToCart({ title, price, qty: 1 });
//     });
//   });

//   function addToCart(item) {
//     let cart = JSON.parse(localStorage.getItem("pbm_cart") || "[]");
//     const idx = cart.findIndex(
//       (i) => i.title === item.title && i.price === item.price
//     );
//     if (idx > -1) {
//       cart[idx].qty += 1;
//     } else {
//       cart.push(item);
//     }
//     localStorage.setItem("pbm_cart", JSON.stringify(cart));
//     updateCartCount();
//     alert("Added to cart!");
//   }

//   function updateCartCount() {
//     const cart = JSON.parse(localStorage.getItem("pbm_cart") || "[]");
//     const count = cart.reduce((sum, item) => sum + item.qty, 0);
//     const el = document.getElementById("cart-count");
//     if (el) el.textContent = count;
//   }
// });
