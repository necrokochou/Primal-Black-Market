<?php
session_start();

<<<<<<< HEAD
<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<main class="primal-auth-bg">
    <div class="primal-card primal-auth-card">
        <h1 class="primal-title">Login</h1>
        <form id="login-form" class="primal-form" method="POST">
            <input type="text" name="username" class="primal-input" placeholder="Username" required />
            <input type="password" name="password" class="primal-input" placeholder="Password" required />
=======
if (isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

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
            
>>>>>>> 6d0a871561d783c75721bdda7e382102b1748681
            <button type="submit" class="primal-btn-primary">Login</button>
        </form>
        <div class="primal-auth-link">Don't have an account? <a class="primal-link" href="/pages/register/index.php">Register</a></div>
    </div>
</main>
<<<<<<< HEAD
<script src="/assets/js/auth.js"></script>
=======

<script src="/assets/js/auth.js"></script> <!-- TODO: primal-login.js to auth.js -->
>>>>>>> 6d0a871561d783c75721bdda7e382102b1748681
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
