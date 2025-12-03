<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register • Cargo Transport System</title>
    <link rel="stylesheet" href="/cargo-project/frontend/assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">CT</div>
                <h1>Create Account</h1>
                <p>Join the future of cargo transport</p>
            </div>
            <div class="form-body">
                <form id="registerForm">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label>Phone</label>
                        <input type="text" name="phone" required>
                    </div>
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" required minlength="6">
                    </div>
                    <div class="input-group">
                        <label>Role</label>
                        <select name="role" required style="width:100%;padding:14px 16px;border:2px solid #e1e5e9;border-radius:12px;font-size:16px;">
                            <option value="customer">Customer</option>
                            <option value="transporter">Transporter</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Create Account</button>
                </form>
                <div class="links">
                    <p>Already have an account? <a href="login.php">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const btn = e.target.querySelector('.btn');
            btn.textContent = 'Creating Account...';
            btn.disabled = true;

            try {
                const res = await fetch('/cargo-project/backend/api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });

                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }

                const data = await res.json();
                
                if (data.success) {
                    alert('Registration successful! Redirecting to login...');
                    window.location.href = 'login.php';
                } else {
                    alert(data.error || 'Registration failed');
                }
            } catch (err) {
                console.error('Registration error:', err);
                alert('Network error: ' + err.message);
            } finally {
                btn.textContent = 'Create Account';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>