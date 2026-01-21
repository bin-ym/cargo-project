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
    max-width: 700px;
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

.input-group input,
.input-group select {
    width: 100%;
    padding: 12px 42px 12px 12px; /* space for eye icon */
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.95rem;
}

.input-group input::placeholder {
    color: #94a3b8;
    font-size: 0.9rem;
}

.input-group input:focus,
.input-group select:focus {
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
    padding-right: 40px; /* space for the eye icon */
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

.password-strength {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 4px;
}

/* Hide by default */
.hidden {
    display: none;
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

/* Success/Error Message */
#registerMessage {
    margin-top: 10px;
    font-size: 0.9rem;
    text-align: center;
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

            <div id="registerMessage"></div>
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
        registerMessage.style.color = data.success ? 'green' : 'red';
        registerMessage.textContent = data.success ? data.message : data.error;

        if (data.success) {
            setTimeout(() => {
                location.href = `verify_email.php?email=${encodeURIComponent(data.email)}`;
            }, 1500);
        }

    } catch (err) {
        registerMessage.style.color = 'red';
        registerMessage.textContent = translations.server_error;
    }

    btn.disabled = false;
    btn.textContent = translations.create_account;
});
</script>

</body>
</html>