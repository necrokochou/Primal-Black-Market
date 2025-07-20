
<footer class="site-footer primal-footer-aaa">
    <div class="footer-main primal-footer-main">
        <div class="footer-brand">
            <div class="logo">
                <a href="/"><img src="/assets/images/example.png" alt="Primal Black Market Logo"></a>
            </div>
            <p><b>Primal Black Market</b> &mdash; The wildest deals, the rarest finds. Shop the primal way.</p>
            <div class="footer-socials">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-discord"></i></a>
            </div>
        </div>
        <div class="footer-news">
            <h4>Latest News</h4>
            <ul>
                <li>New primal products just dropped!</li>
                <li>Exclusive: Members-only deals</li>
                <li>Top 10 Survival Tips</li>
            </ul>
        </div>
        <div class="footer-info">
            <h4>Information</h4>
            <ul>
                <li><a href="/pages/about/index.php">About</a></li>
                <li><a href="/pages/cart/index.php">Cart</a></li>
                <li><a href="/pages/shop/index.php">Shop</a></li>
                <li><a href="#">Order Status</a></li>
                <li><a href="#">Help</a></li>
                <li><a href="#">Site Policies</a></li>
            </ul>
        </div>
        <div class="footer-newsletter">
            <h4>Our Newsletter</h4>
            <p>Sign up to get primal deals and secret drops.</p>
            <form onsubmit="event.preventDefault(); alert('Thank you for subscribing!');">
                <input type="email" placeholder="Your email here" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom primal-footer-bottom">
        <span>&copy; <?= date('Y') ?> Primal Black Market. All rights reserved.</span>
        <span class="footer-payments">
            <img src="/assets/images/paypal.png" alt="PayPal">
            <img src="/assets/images/visa.png" alt="Visa">
            <img src="/assets/images/mastercard.png" alt="Mastercard">
            <img src="/assets/images/bitcoin.png" alt="Bitcoin">
        </span>
    </div>
    <script src="/assets/js/main.js"></script>
</footer>
