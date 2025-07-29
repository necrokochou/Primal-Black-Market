<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/primal-register.css">
<link rel="stylesheet" href="/assets/css/primal-registration.css">

<main class="primal-auth-bg">
    <div class="primal-card primal-auth-card">
        <h1 class="primal-title">Join Primal</h1>
        <p class="primal-subtitle">Create your account and unlock exclusive access to the finest marketplace experience</p>

        <form id="register-form" class="primal-form" method="POST" novalidate>
            <div class="input-group">
                <input type="text" name="username" class="primal-input" placeholder="Choose your username" required aria-label="Username" />
            </div>

            <div class="input-group">
                <input type="email" name="email" class="primal-input" placeholder="Enter your email address" required aria-label="Email Address" />
            </div>

            <div class="input-group">
                <input type="password" name="password" class="primal-input" placeholder="Create a strong password" required aria-label="Password" />
            </div>

            <div class="input-group">
                <input type="password" name="confirm_password" class="primal-input" placeholder="Confirm your password" required aria-label="Confirm Password" />
            </div>

            <!-- Account Type Selection -->
            <div class="account-type-section">
                <h3 class="account-type-title">Choose Your Account Type</h3>
                <p class="account-type-subtitle">Select how you want to use the Primal Black Market</p>
                
                <div class="account-type-cards">
                    <label class="account-type-card buyer-card">
                        <input type="radio" name="account_type" value="buyer" checked>
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="card-title">Buyer Account</h4>
                            <p class="card-description">Browse and purchase unique primal treasures from verified sellers</p>
                            <ul class="card-features">
                                <li><i class="fas fa-check"></i> Access to exclusive marketplace</li>
                                <li><i class="fas fa-check"></i> Secure payment processing</li>
                                <li><i class="fas fa-check"></i> Purchase history & tracking</li>
                            </ul>
                        </div>
                        <div class="card-badge">Most Popular</div>
                    </label>
                    
                    <label class="account-type-card seller-card">
                        <input type="radio" name="account_type" value="seller">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4 class="card-title">Seller Account</h4>
                            <p class="card-description">List and sell your primal goods to a community of collectors</p>
                            <ul class="card-features">
                                <li><i class="fas fa-check"></i> Create product listings</li>
                                <li><i class="fas fa-check"></i> Sales analytics dashboard</li>
                                <li><i class="fas fa-check"></i> Direct customer communication</li>
                            </ul>
                        </div>
                        <div class="card-badge seller-badge">Premium</div>
                    </label>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox-group">
                    <input type="checkbox" name="terms" required tabindex="0">
                    <div class="checkmark"></div>
                    <span class="label-text">I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
                </label>

                <label class="checkbox-group">
                    <input type="checkbox" name="marketing" tabindex="0">
                    <div class="checkmark"></div>
                    <span class="label-text">I want to receive exclusive offers and updates</span>
                </label>
            </div>

            <button type="submit" class="primal-btn-primary" aria-describedby="register-form-description">Create Account</button>
        </form>

        <div class="primal-auth-link">
            Already have an account? <a class="primal-link" href="/pages/login/index.php">Sign In</a>
        </div>
    </div>
</main>

<script src="/assets/js/primal-register.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>