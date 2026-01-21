<?php
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('reset_password') ?> - Cargo Connect</title>
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
        <h2 style="text-align: center; margin-bottom: 30px;"><?= __('reset_password') ?></h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 20px; font-size: 0.9rem;">
            <?= __('enter_code_email') ?>
        </p>

        <form id="resetForm">
            <div class="input-group">
                <label><?= __('verification_code') ?></label>
                <input type="text" id="otp" class="otp-input" required placeholder="000000" maxlength="6" pattern="\d{6}">
            </div>
            <div class="input-group">
                <label><?= __('new_password') ?></label>
                <input type="password" id="password" required minlength="6" placeholder="<?= __('enter_password') ?>">
            </div>
            <div class="input-group">
                <label><?= __('confirm_password') ?></label>
                <input type="password" id="confirmPassword" required minlength="6" placeholder="<?= __('confirm_password_placeholder') ?>">
            </div>
            <button type="submit" class="btn"><?= __('reset_password') ?></button>
            <div id="message" style="margin-top: 15px; text-align: center; font-size: 14px;"></div>
        </form>
    </div>
</div>

<script>
const translations = {
    passwordsNotMatch: "<?= __('passwords_not_match') ?>",
    resetting: "<?= __('resetting') ?>",
    successMsg: "<?= __('reset_success_redirect') ?>",
    serverError: "<?= __('server_error_try_again') ?>",
    resetBtn: "<?= __('reset_password') ?>"
};

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
        msg.innerText = translations.passwordsNotMatch;
        return;
    }

    if (!email) {
        msg.style.color = 'red';
        msg.innerText = 'Email missing. Please restart the process.';
        return;
    }

    btn.disabled = true;
    btn.innerText = translations.resetting;
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
            msg.innerText = translations.successMsg;
            setTimeout(() => location.href = 'login.php', 2000);
        } else {
            msg.style.color = 'red';
            msg.innerText = data.error || 'Reset failed';
            btn.disabled = false;
            btn.innerText = translations.resetBtn;
        }
    } catch (err) {
        msg.style.color = 'red';
        msg.innerText = translations.serverError;
        btn.disabled = false;
        btn.innerText = translations.resetBtn;
    }
});
</script>

</body>
</html>
