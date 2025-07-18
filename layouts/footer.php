
<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-brand">
            <div class="logo">
                <a href="/"><img src="/assets/images/CP Logo(ARCL).png" alt="Primal Black Market Logo"></a>
            </div>
            <p><b>Primal Black Market</b> &mdash; The wildest deals, the rarest finds. Shop the primal way.</p>
            <div class="footer-social">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-news">
            <h4>Latest News</h4>
            <ul>
                <li>New primal products just dropped!</li>
                <li>Exclusive: Members-only deals</li>
                <li>Top 10 Survival Tips</li>
                <li>Limited Edition Collections</li>
                <li>VIP Member Benefits</li>
            </ul>
        </div>
        <div class="footer-info">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="/pages/about/index.php">About Us</a></li>
                <li><a href="/pages/cart/index.php">Shopping Cart</a></li>
                <li><a href="/pages/shop/index.php">Shop Now</a></li>
                <li><a href="#">Order Status</a></li>
                <li><a href="#">Customer Support</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>
        <div class="footer-newsletter">
            <h4>Stay Connected</h4>
            <p>Subscribe for exclusive deals and primal updates.</p>
            <form onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                <input type="email" placeholder="Enter your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Primal Black Market. All rights reserved.</p>
        <div class="footer-payments">
            <img src="/assets/images/paypal.png" alt="PayPal">
            <img src="/assets/images/visa.png" alt="Visa">
            <img src="/assets/images/mastercard.png" alt="Mastercard">
            <img src="/assets/images/bitcoin.png" alt="Bitcoin">
        </div>
    </div>
    <script src="/assets/js/main.js"></script>
</footer>
