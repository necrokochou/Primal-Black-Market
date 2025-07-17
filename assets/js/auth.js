// Advanced login/register AJAX for Primal Black Market

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(loginForm);
            formData.append('action', 'login');
            fetch('/handlers/auth.handler.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/';
                } else {
                    alert(data.error || 'Login failed.');
                }
            })
            .catch(() => alert('Login error.'));
        });
    }

    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(registerForm);
            formData.append('action', 'register');
            fetch('/handlers/auth.handler.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/';
                } else {
                    alert(data.error || 'Registration failed.');
                }
            })
            .catch(() => alert('Registration error.'));
        });
    }
});
