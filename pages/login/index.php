<?php
session_start();



require_once __DIR__ . '/../../layouts/header.php';
?>
<link rel="stylesheet" href="/assets/css/primal-login.css">

<main class="primal-auth-bg">
    <div class="primal-card primal-auth-card">
        <h1 class="primal-title">Login</h1>
        <p class="primal-subtitle">Access your Primal Black Market account</p>
        
        <form id="login-form" class="primal-form" method="POST" action="/handlers/auth.handler.php" novalidate> <!-- TODO: add action -->
            <input type="hidden" name="action" value="login"> <!-- TODO: add hidden value "login" -->
            <div class="input-group">
                <input type="text" name="username" class="primal-input" placeholder="Username" required 
                       autocomplete="username" aria-label="Username" />
                <div class="input-highlight"></div>
            </div>
            
            <div class="input-group">
                <input type="password" name="password" class="primal-input" placeholder="Password" required 
                       autocomplete="current-password" aria-label="Password" />
                <div class="input-highlight"></div>
            </div>
            
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <span class="checkmark"></span>
                    <span class="label-text">Remember me</span>
                </label>
                <a href="/pages/forgot-password" class="forgot-password">Forgot password?</a>
            </div>
            
            <button type="submit" class="primal-btn-primary">Login</button>
        </form>
        
        <div class="primal-auth-link">
            Don't have an account? <a class="primal-link" href="/pages/register/index.php">Create Account</a>
        </div>
        
        <div class="login-features">
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Authentication</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-user-shield"></i>
                <span>Privacy Protected</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span>24/7 Access</span>
            </div>
        </div>
    </div>
</main>

<script src="/assets/js/primal-login.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
