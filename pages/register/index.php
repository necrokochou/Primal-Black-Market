
<?php require_once __DIR__ . '/../../layouts/header.php'; ?>
<main class="primal-auth-bg">
    <div class="primal-card primal-auth-card">
        <h1 class="primal-title">Register</h1>
        <form id="register-form" class="primal-form" method="POST">
            <input type="text" name="username" class="primal-input" placeholder="Username" required />
            <input type="email" name="email" class="primal-input" placeholder="Email" required />
            <input type="password" name="password" class="primal-input" placeholder="Password" required />
            <button type="submit" class="primal-btn-primary">Register</button>
        </form>
        <div class="primal-auth-link">Already have an account? <a class="primal-link" href="/pages/login/index.php">Login</a></div>
    </div>
</main>
<script src="/assets/js/auth.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
