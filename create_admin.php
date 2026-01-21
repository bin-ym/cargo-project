<?php
require_once __DIR__ . '/backend/config/session.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') header("Location: frontend/admin/dashboard.php");
    elseif ($role === 'customer') header("Location: frontend/customer/dashboard.php");
    elseif ($role === 'transporter') header("Location: frontend/transporter/dashboard.php");
    exit();
}

$simpleNavbar = true;
require_once __DIR__ . '/frontend/layout/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Admin • CargoConnect</title>

<script src="https://unpkg.com/feather-icons"></script>
<link rel="stylesheet" href="frontend/css/public.css">

<style>
/* === Hero Section === */
.register-hero {
    min-height: 80vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 100px;
    background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
}

/* === Card === */
.register-card {
    background: white;
    border-radius: 24px;
    padding: 36px;
    max-width: 600px;
    width: 100%;
    margin-bottom: 80px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1),
                0 10px 10px -5px rgba(0,0,0,0.04);
}

/* === Header === */
.register-header {
    text-align: center;
    margin-bottom: 25px;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
}

/* === Grid 2-column === */
.register-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

/* Full-width elements span 2 columns */
.full-width {
    grid-column: 1 / -1;
    margin-bottom: 15px;
}

/* Input Styling */
.input-group label {
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
    color: #475569;
}

.input-group input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.95rem;
}

.input-group input::placeholder {
    color: #94a3b8;
    font-size: 0.9rem;
}

.input-group input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    background: white;
}

/* Password toggle */
.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 40px;
}

.password-wrapper span {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #94a3b8;
}

.password-wrapper span:hover {
    color: #2563eb;
}

.btn {
    display: inline-block;
    width: 100%;
    background: #2563eb;
    color: white;
    font-weight: 600;
    border-radius: 12px;
    padding: 12px;
    border: none;
    cursor: pointer;
    transition: background 0.3s;
}

.btn:hover {
    background: #1d4ed8;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Success/Error Message */
#message {
    margin-top: 10px;
    font-size: 0.9rem;
    text-align: center;
}

.success {
    color: #16a34a;
}

.error {
    color: #dc2626;
}

.note {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    padding: 12px;
    border-radius: 12px;
    font-size: 13px;
    margin-top: 20px;
}

/* Mobile Responsive */
@media (max-width: 640px) {
    .register-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<section class="register-hero">
    <div class="register-card">
        <div class="register-header">Create Admin Account</div>
        
        <div class="note" style="margin-bottom: 20px;">
            <strong>⚠️ Important:</strong> This script creates the system administrator. Only one admin account can exist. After setup, please remove or restrict access to this file.
        </div>

        <form id="adminForm">
            <div class="register-grid">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="Enter your full name" required>
                </div>
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="admin@example.com" required>
                </div>
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="+251 9xx xxx xxx" required>
                </div>
                <div class="input-group password-wrapper">
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required minlength="6">
                    <span onclick="togglePassword('password')"><i data-feather="eye"></i></span>
                </div>
                <div class="input-group password-wrapper">
                    <label>Confirm Password</label>
                    <input type="password" id="confirmPassword" placeholder="Confirm password" required minlength="6">
                    <span onclick="togglePassword('confirmPassword')"><i data-feather="eye"></i></span>
                </div>
            </div>

            <div class="full-width">
                <button class="btn" type="submit">Create Admin Account</button>
            </div>

            <div id="message"></div>
        </form>

        <div class="full-width" style="text-align:center; margin-top:10px;">
            Already have an account? <a href="frontend/auth/login.php">Sign in</a>
        </div>
    </div>
</section>

<script>
feather.replace();

function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

document.getElementById('adminForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const messageDiv = document.getElementById('message');
    const btn = e.target.querySelector('button');
    
    messageDiv.innerHTML = '';
    
    if (password !== confirmPassword) {
        messageDiv.innerHTML = '<div class="error">Passwords do not match!</div>';
        return;
    }
    
    if (password.length < 6) {
        messageDiv.innerHTML = '<div class="error">Password must be at least 6 characters long!</div>';
        return;
    }
    
    btn.disabled = true;
    btn.textContent = 'Creating Admin...';
    
    try {
        const formData = new FormData(e.target);
        
        const response = await fetch('/cargo-project/backend/api/admin/create_admin.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageDiv.innerHTML = '<div class="success">' + data.message + '</div>';
            e.target.reset();
            
            setTimeout(() => {
                window.location.href = '/cargo-project/frontend/auth/login.php';
            }, 2000);
        } else {
            messageDiv.innerHTML = '<div class="error">' + (data.error || 'Failed to create admin account') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Create Admin Account';
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="error">Server error. Please try again.</div>';
        btn.disabled = false;
        btn.textContent = 'Create Admin Account';
    }
});
</script>

</body>
</html>