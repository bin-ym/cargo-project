<?php
require_once __DIR__ . '/../../backend/config/session.php';

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
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register • CargoConnect</title>

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
        <div class="register-header">Create Your Account</div>

        <form id="registerForm" enctype="multipart/form-data">
            <div class="register-grid">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="+251 9xx xxx xxx" required>
                </div>
                <div class="input-group password-wrapper">
                    <label>Password</label>
                    <input type="password" id="regPassword" name="password" placeholder="Enter password" required minlength="6">
                    <span onclick="togglePassword('regPassword')"><i data-feather="eye"></i></span>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>
                <div class="input-group password-wrapper">
                    <label>Confirm Password</label>
                    <input type="password" id="confirmPassword" placeholder="Confirm password" required minlength="6">
                    <span onclick="togglePassword('confirmPassword')"><i data-feather="eye"></i></span>
                </div>

                <div class="input-group full-width">
                    <label>Role</label>
                    <select name="role" id="roleSelect" required onchange="toggleFields()">
                        <option value="">Select Role</option>
                        <option value="customer">Customer</option>
                        <option value="transporter">Transporter</option>
                    </select>
                </div>
            </div>

            <!-- Customer Fields -->
            <div id="customerFields" class="full-width hidden">
                <div class="register-grid">
                    <div class="input-group">
                        <label>Address</label>
                        <input type="text" name="address" placeholder="Enter your address">
                    </div>
                    <div class="input-group">
                        <label>City</label>
                        <input type="text" name="city" placeholder="Enter your city">
                    </div>
                </div>
            </div>

            <!-- Transporter Fields -->
            <div id="transporterFields" class="full-width hidden">
                <div class="input-group">
                    <label>License Copy</label>
                    <input type="file" name="license_copy" accept="image/*,.pdf">
                </div>
            </div>

            <div class="full-width">
                <button class="btn" type="submit">Create Account</button>
            </div>

            <div id="registerMessage"></div>
        </form>

        <div class="full-width" style="text-align:center; margin-top:10px;">
            Already have an account? <a href="login.php">Sign in</a>
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
        v.length < 6 ? 'Too short' :
        v.length < 10 ? 'Medium strength' :
        'Strong password';
});

// Form Submission
registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    if (regPassword.value !== confirmPassword.value) {
        registerMessage.style.color = 'red';
        registerMessage.textContent = 'Passwords do not match';
        return;
    }

    const btn = registerForm.querySelector('button');
    btn.disabled = true;
    btn.textContent = 'Creating...';
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
        registerMessage.textContent = 'Server error, please try again.';
    }

    btn.disabled = false;
    btn.textContent = 'Create Account';
});
</script>

</body>
</html>