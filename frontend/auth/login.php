<?php
session_start();
$simpleNavbar = true;
require_once __DIR__ . '/../layout/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login • CargoConnect</title>
<script src="https://unpkg.com/feather-icons"></script>
<link rel="stylesheet" href="../css/public.css">
<style>
    /* Login page overrides */
    .login-hero {
        min-height: 70vh;
        display: flex;
        align-items: flex-start; /* so we can add margin-top */
        justify-content: center;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        padding-top: 200px; /* space from navbar */
    }
    .login-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        max-width: 400px;
        width: 100%;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        text-align: center;
    }
    .login-card h1 {
        font-size: 2rem;
        color: var(--secondary);
        margin-bottom: 10px;
    }
    .login-card p {
        color: #64748b;
        margin-bottom: 25px;
    }
    .login-card input {
        width: 100%;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 20px;
        outline: none;
        font-size: 1rem;
        transition: all 0.2s;
    }
    .login-card input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .input-password-wrapper {
    position: relative;
    display: flex;
    align-items: center; /* vertically centers the icon */
    margin-bottom: 20px;
}

.input-password-wrapper input {
    flex: 1;
    padding-right: 40px; /* space for icon */
    height: 44px; /* match your register page input height */
    border-radius: 12px;
    margin-bottom: 0;
}

.input-password-wrapper span {
    position: absolute;
    right: 12px;
    cursor: pointer;
    color: #94a3b8;
    display: flex;
    align-items: center; /* vertically center the SVG */
    height: 100%;
}

    .login-card button {
        width: 100%;
        padding: 14px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
    }
    .login-card button:hover {
        background: var(--primary-dark);
    }
    .login-links {
        margin-top: 20px;
        font-size: 0.95rem;
    }
    .login-links a {
        color: var(--primary);
        font-weight: 500;
        text-decoration: none;
    }
    .login-links a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<section class="login-hero">
    <div class="login-card">
        <h1><?= __('welcome_back') ?></h1>
        <p><?= __('sign_in_to_account') ?></p>
        <form id="loginForm" method="POST" action="/cargo-project/backend/api/auth/login.php">
            <input type="text" name="username" placeholder="<?= __('username_or_email') ?>" required autofocus>
            
            <div class="input-password-wrapper" style="position:relative;">
                <input type="password" name="password" id="password" placeholder="<?= __('password') ?>" required style="padding-right:40px;">
                <span onclick="togglePassword('password', 'eye')" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#94a3b8;">
                    <svg id="eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </span>
            </div>
            <div style="text-align: right; margin-top: -15px; margin-bottom: 20px;">
                <a href="forgot_password.php" style="font-size: 0.9rem; color: var(--primary); text-decoration: none;"><?= __('forgot_password') ?></a>
            </div>

            <button type="submit"><?= __('sign_in') ?></button>
        </form>
        <div class="login-links">
            <p><?= __('dont_have_account') ?> <a href="register.php"><?= __('create_account') ?></a></p>
        </div>
    </div>
</section>

<script>
if (typeof feather !== 'undefined') {
    feather.replace();
}

function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const btn = e.target.querySelector('button');
    const originalBtnText = btn.textContent;
    btn.textContent = 'Signing in...';
    btn.disabled = true;

    try {
        const res = await fetch('/cargo-project/backend/api/auth/login.php', {
            method: 'POST',
            body: formData
        });

        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        const data = await res.json();

        if (data.success) {
            if (data.must_change_password) {
                alert('Please change your password to continue.');
                window.location.href = '/cargo-project/frontend/auth/change_password.php';
            } else {
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
        btn.textContent = originalBtnText;
        btn.disabled = false;
    }
});
</script>
</body>
</html>