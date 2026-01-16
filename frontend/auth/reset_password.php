<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Cargo Connect</title>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            padding: 20px;
        }
        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; }
        .input-group input {
            width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;
        }
        .otp-input {
            letter-spacing: 5px;
            font-size: 18px;
            text-align: center;
            font-weight: bold;
        }
        .btn {
            width: 100%; background: #2563eb; color: white; padding: 12px;
            border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
        }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2 style="text-align: center; margin-bottom: 30px;">Reset Password</h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 20px; font-size: 0.9rem;">
            Enter the code sent to your email.
        </p>

        <form id="resetForm">
            <div class="input-group">
                <label>Verification Code</label>
                <input type="text" id="otp" class="otp-input" required placeholder="000000" maxlength="6" pattern="\d{6}">
            </div>
            <div class="input-group">
                <label>New Password</label>
                <input type="password" id="password" required minlength="6" placeholder="Enter new password">
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" id="confirmPassword" required minlength="6" placeholder="Confirm new password">
            </div>
            <button type="submit" class="btn">Reset Password</button>
            <div id="message" style="margin-top: 15px; text-align: center; font-size: 14px;"></div>
        </form>
    </div>
</div>

<script>
document.getElementById('resetForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    const msg = document.getElementById('message');
    
    const otp = document.getElementById('otp').value;
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;
    
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email');

    if (password !== confirm) {
        msg.style.color = 'red';
        msg.innerText = 'Passwords do not match';
        return;
    }

    if (!email) {
        msg.style.color = 'red';
        msg.innerText = 'Email missing. Please restart the process.';
        return;
    }

    btn.disabled = true;
    btn.innerText = 'Resetting...';
    msg.innerText = '';

    try {
        const res = await fetch('/cargo-project/backend/api/auth/reset_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, token: otp, password })
        });
        const data = await res.json();

        if (data.success) {
            msg.style.color = 'green';
            msg.innerText = 'Password reset successful! Redirecting...';
            setTimeout(() => location.href = 'login.php', 2000);
        } else {
            msg.style.color = 'red';
            msg.innerText = data.error || 'Reset failed';
            btn.disabled = false;
            btn.innerText = 'Reset Password';
        }
    } catch (err) {
        msg.style.color = 'red';
        msg.innerText = 'Server error. Please try again.';
        btn.disabled = false;
        btn.innerText = 'Reset Password';
    }
});
</script>

</body>
</html>
