
<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<link rel="stylesheet" href="/assets/css/primal-register.css">

<main class="primal-auth-bg">
    <div class="primal-card primal-auth-card">
        <h1 class="primal-title">Join Primal</h1>
        <p class="primal-subtitle">Create your account and unlock exclusive access to the finest marketplace experience</p>
        
        <form id="register-form" class="primal-form" method="POST">
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
            
            <div class="form-options">
                <label class="checkbox-group">
                    <input type="checkbox" name="terms" required>
                    <div class="checkmark"></div>
                    <span class="label-text">I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
                </label>
                
                <label class="checkbox-group">
                    <input type="checkbox" name="marketing">
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
