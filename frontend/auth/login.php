<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login • Cargo Transport System</title>
    <link rel="stylesheet" href="/cargo-project/frontend/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">CT</div>
                <h1>Cargo Transport</h1>
                <p>Mediation System</p>
            </div>
            <div class="form-body">
                <form id="loginForm">
                    <div class="input-group">
                        <label>Username or Email</label>
                        <input type="text" name="username" required autofocus>
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn">Sign In</button>
                </form>
                <div class="links">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const btn = e.target.querySelector('.btn');
            btn.textContent = 'Signing in...';
            btn.disabled = true;

            try {
                const res = await fetch('/cargo-project/backend/api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });

                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                const data = await res.json();
                
                if (data.success) {
                    if (data.must_change_password) {
                        alert('Please change your password to continue.');
                        window.location.href = '/cargo-project/frontend/auth/change_password.php';
                    } else {
                        // Redirect based on role
                        if (data.user.role === 'admin') window.location.href = '/cargo-project/frontend/admin/dashboard.php';
                        else if (data.user.role === 'customer') window.location.href = '/cargo-project/frontend/customer/dashboard.php';
                        else if (data.user.role === 'transporter') window.location.href = '/cargo-project/frontend/transporter/dashboard.php';
                        else window.location.href = '/cargo-project/index.php';
                    }
                } else {
                    alert(data.error || 'Login failed');
                }
            } catch (err) {
                console.error('Login error:', err);
                alert('Network error: ' + err.message);
            } finally {
                btn.textContent = 'Sign In';
                btn.disabled = false;
            }
        });

        // Add enter key support for better UX
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    document.getElementById('loginForm').dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>