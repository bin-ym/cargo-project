<?php
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('reset_password_title') ?> - Cargo Connect</title>
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
        .btn {
            width: 100%; background: #2563eb; color: white; padding: 12px;
            border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
        }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }
        .back-link {
            display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none;
        }
        /* Form Message styles */
        .form-message {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            line-height: 1.5;
            margin-top: 15px;
            width: 100%;
            text-align: center;
        }
        .form-message.error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            display: block;
        }
        .form-message.success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            display: block;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <h2 style="text-align: center; margin-bottom: 10px;"><?= __('reset_password_title') ?></h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 30px;"><?= __('enter_email_reset') ?></p>

        <form id="forgotForm">
            <div class="input-group">
                <label><?= __('email') ?></label>
                <input type="email" id="email" required placeholder="<?= __('email_placeholder') ?>">
            </div>
            <button type="submit" class="btn"><?= __('send_reset_link') ?></button>
            <div id="message" class="form-message"></div>
        </form>

        <a href="login.php" class="back-link"><?= __('back_to_login') ?></a>
    </div>
</div>

<script>
const translations = {
    sending: "<?= __('sending') ?>",
    sendLink: "<?= __('send_reset_link') ?>",
    serverError: "<?= __('server_error_try_again') ?>"
};

document.getElementById('forgotForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    const msg = document.getElementById('message');
    const email = document.getElementById('email').value;

    btn.disabled = true;
    btn.innerText = translations.sending;
    msg.innerText = '';

    try {
        const res = await fetch('/cargo-project/backend/api/auth/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        });
        const data = await res.json();

        if (data.success) {
            msg.className = 'form-message success';
            msg.textContent = data.message;
            setTimeout(() => {
                location.href = `reset_password.php?email=${encodeURIComponent(email)}`;
            }, 1500);
        } else {
            msg.className = 'form-message error';
            msg.textContent = data.error || 'Failed to send email';
        }
    } catch (err) {
        msg.className = 'form-message error';
        msg.textContent = translations.serverError;
    }

    btn.disabled = false;
    btn.innerText = translations.sendLink;
});
</script>

</body>
</html>
