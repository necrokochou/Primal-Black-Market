<?php
require_once __DIR__ . '/../../layouts/header.php';
?>
<main class="login-main primal-theme">
    <h1>Login</h1>
    <form id="login-form" method="POST">
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p>Don't have an account? <a href="/pages/register/index.php">Register</a></p>
</main>
<script src="/assets/js/auth.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
