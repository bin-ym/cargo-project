<?php
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../../backend/config/languages.php';

if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') header("Location: ../admin/dashboard.php");
    elseif ($role === 'customer') header("Location: ../customer/dashboard.php");
    elseif ($role === 'transporter') header("Location: ../transporter/dashboard.php");
    else header("Location: ../../index.php");
    exit();
}

$simpleNavbar = true;
require_once __DIR__ . '/../layout/navbar.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= __('register') ?> • <?= __('app_name') ?></title>

<script src="https://unpkg.com/feather-icons"></script>
<link rel="stylesheet" href="../css/public.css">

<style>
/* === Hero Section === */
/* Unifying with Login Page Styles */
    .register-hero {
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 200px;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        font-family: 'Inter', sans-serif;
    }

    .register-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        max-width: 700px;
        width: 100%;
        margin-bottom: 80px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    }

    .register-header {
        text-align: center;
        margin-bottom: 30px;
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b; /* --secondary */
        letter-spacing: -0.5px;
    }

    .register-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .full-width {
        grid-column: 1 / -1;
        margin-bottom: 0;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
        color: #475569;
    }

    .input-group input,
    .input-group select {
        width: 100%;
        padding: 12px 42px 12px 12px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .input-group input:focus,
    .input-group select:focus {
        outline: none;
        border-color: #2563eb; /* --primary */
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }

    .btn {
        display: inline-block;
        width: 100%;
        background: #2563eb; /* --primary */
        color: white;
        font-weight: 600;
        border-radius: 12px;
        padding: 14px;
        border: none;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        font-size: 1rem;
        margin-top: 10px;
    }

    .btn:hover {
        background: #1d4ed8; /* --primary-dark */
        transform: translateY(-1px);
    }

    /* Form Message styles */
    .form-message {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        display: none;
        line-height: 1.5;
        margin-bottom: 20px;
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

    /* Password strength & toggles */
    .password-wrapper { position: relative; }
    .password-wrapper span {
        position: absolute;
        right: 12px;
        top: 38px; /* Adjusted for label height */
        cursor: pointer;
        color: #94a3b8;
    }
    .password-wrapper span:hover { color: #2563eb; }
    .password-strength { font-size: 0.8rem; color: #64748b; margin-top: 5px; }
    .hidden { display: none; }

    /* Links */
    .login-link {
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
    }
    .login-link:hover { text-decoration: underline; }

    @media (max-width: 640px) {
        .register-grid { grid-template-columns: 1fr; }
        .register-card { padding: 24px; }
    }
</style>
</head>

<body>

<section class="register-hero">
    <div class="register-card">
        <div class="register-header"><?= __('create_your_account') ?></div>

        <form id="registerForm" enctype="multipart/form-data">
            <div class="register-grid">
                <div class="input-group">
                    <label><?= __('full_name') ?></label>
                    <input type="text" name="name" placeholder="<?= __('enter_full_name') ?>" required>
                </div>
                <div class="input-group">
                    <label><?= __('username') ?></label>
                    <input type="text" name="username" placeholder="<?= __('choose_username') ?>" required>
                </div>
                <div class="input-group">
                    <label><?= __('email') ?></label>
                    <input type="email" name="email" placeholder="<?= __('email_placeholder') ?>" required>
                </div>
                <div class="input-group">
                    <label><?= __('phone_number') ?></label>
                    <input type="tel" name="phone" placeholder="<?= __('phone_placeholder') ?>" required>
                </div>
                <div class="input-group password-wrapper">
                    <label><?= __('password') ?></label>
                    <input type="password" id="regPassword" name="password" placeholder="<?= __('enter_password') ?>" required minlength="6">
                    <span onclick="togglePassword('regPassword')"><i data-feather="eye"></i></span>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>
                <div class="input-group password-wrapper">
                    <label><?= __('confirm_password') ?></label>
                    <input type="password" id="confirmPassword" placeholder="<?= __('confirm_password_placeholder') ?>" required minlength="6">
                    <span onclick="togglePassword('confirmPassword')"><i data-feather="eye"></i></span>
                </div>

                <div class="input-group full-width">
                    <label><?= __('role') ?></label>
                    <select name="role" id="roleSelect" required onchange="toggleFields()">
                        <option value=""><?= __('select_role') ?></option>
                        <option value="customer"><?= __('customer') ?></option>
                        <option value="transporter"><?= __('transporter') ?></option>
                    </select>
                </div>
            </div>

            <!-- Customer Fields -->
            <div id="customerFields" class="full-width hidden">
                <div class="register-grid">
                    <div class="input-group">
                        <label><?= __('address') ?></label>
                        <input type="text" name="address" placeholder="<?= __('enter_address') ?>">
                    </div>
                    <div class="input-group">
                        <label><?= __('city') ?></label>
                        <input type="text" name="city" placeholder="<?= __('enter_city') ?>">
                    </div>
                </div>
            </div>

            <!-- Transporter Fields -->
            <div id="transporterFields" class="full-width hidden">
                <div class="input-group">
                    <label><?= __('license_copy') ?></label>
                    <input type="file" name="license_copy" accept="image/*,.pdf">
                </div>
            </div>

            <div class="full-width">
                <button class="btn" type="submit"><?= __('create_account') ?></button>
            </div>

            <div id="registerMessage" class="form-message"></div>
        </form>

        <div class="full-width" style="text-align:center; margin-top:10px;">
            <?= __('already_have_account') ?> <a href="login.php"><?= __('sign_in') ?></a>
        </div>
    </div>
</section>

<script>
feather.replace();

const registerForm = document.getElementById('registerForm');
const regPassword = document.getElementById('regPassword');
const confirmPassword = document.getElementById('confirmPassword');
const passwordStrength = document.getElementById('passwordStrength');
const roleSelect = document.getElementById('roleSelect');
const customerFields = document.getElementById('customerFields');
const transporterFields = document.getElementById('transporterFields');
const registerMessage = document.getElementById('registerMessage');

// Translations for JS
const translations = {
    too_short: "<?= __('too_short') ?>",
    medium_strength: "<?= __('medium_strength') ?>",
    strong_password: "<?= __('strong_password') ?>",
    passwords_not_match: "<?= __('passwords_not_match') ?>",
    creating: "<?= __('creating') ?>",
    create_account: "<?= __('create_account') ?>",
    server_error: "<?= __('server_error_try_again') ?>"
};

function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function toggleFields() {
    customerFields.classList.add('hidden');
    transporterFields.classList.add('hidden');

    if (roleSelect.value === 'customer') customerFields.classList.remove('hidden');
    if (roleSelect.value === 'transporter') transporterFields.classList.remove('hidden');
}

// Password Strength
regPassword.addEventListener('input', () => {
    const v = regPassword.value;
    passwordStrength.textContent =
        v.length < 6 ? translations.too_short :
        v.length < 10 ? translations.medium_strength :
        translations.strong_password;
});

// Form Submission
registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (regPassword.value !== confirmPassword.value) {
        registerMessage.style.color = 'red';
        registerMessage.textContent = translations.passwords_not_match;
        return;
    }

    const btn = registerForm.querySelector('button');
    btn.disabled = true;
    btn.textContent = translations.creating;
    registerMessage.textContent = '';

    try {
        const res = await fetch('/cargo-project/backend/api/auth/register.php', {
            method: 'POST',
            body: new FormData(registerForm)
        });

        const data = await res.json();
        registerMessage.className = 'form-message ' + (data.success ? 'success' : 'error');
        registerMessage.textContent = data.success ? data.message : data.error;

        if (data.success) {
            setTimeout(() => {
                location.href = `verify_email.php?email=${encodeURIComponent(data.email)}`;
            }, 1500);
        }

    } catch (err) {
        registerMessage.className = 'form-message error';
        registerMessage.textContent = translations.server_error;
    }

    btn.disabled = false;
    btn.textContent = translations.create_account;
});
</script>

</body>
</html>