<?php
require_once __DIR__ . '/../../layouts/header.php';
?>
<main class="register-main primal-theme">
    <h1>Register</h1>
    <form id="register-form" method="POST">
        <input type="text" name="username" placeholder="Username" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p>Already have an account? <a href="/pages/login/index.php">Login</a></p>
</main>
<script src="/assets/js/auth.js"></script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
