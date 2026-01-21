<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_customer.php';
require_once __DIR__ . '/../../backend/config/database.php';

// Fetch user data
$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch customer data
$stmtCust = $db->prepare("SELECT * FROM customers WHERE user_id = ?");
$stmtCust->execute([$_SESSION['user_id']]);
$customer = $stmtCust->fetch();
?>

<div class="customer-layout">
    <?php include __DIR__ . '/../layout/navbar_customer.php'; ?>

    <main class="main-content" style="padding: 30px 5%; max-width: 1200px; margin: 0 auto;">
        <header class="topbar" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <h2><?= __('my_profile') ?></h2>
            <div style="display: flex; gap: 15px; align-items: center;">
                <button type="button" class="btn btn-outline" id="editBtn" onclick="toggleEdit()">
                    <i data-feather="edit-2" style="width: 16px; height: 16px; margin-right: 5px;"></i>
                    <?= __('edit') ?>
                </button>
                <div class="user-info">
                    <span class="badge badge-primary"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
                </div>
            </div>
        </header>

        <!-- Profile Form -->
        <div class="recent-activity-card">
            <h3><?= __('personal_information') ?></h3>

            <form id="profileForm" class="profile-form" style="margin-top: 20px;">

                <div class="form-item">
                    <label for="name"><?= __('full_name') ?></label>
                    <input type="text" id="name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" disabled>
                </div>
                <div class="form-item">
                    <label for="username"><?= __('username') ?></label>
                    <input type="text" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                </div>
                <div class="form-item">
                    <label for="email"><?= __('email') ?></label>
                    <input type="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly disabled>
                </div>
                <div class="form-item">
                    <label for="phone"><?= __('phone_number') ?></label>
                    <input type="tel" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" disabled>
                </div>
                <div class="form-item">
                    <label for="city"><?= __('city') ?></label>
                    <input type="text" id="city" value="<?= htmlspecialchars($customer['city'] ?? '') ?>" placeholder="<?= __('enter_city') ?>" disabled>
                </div>
                <div class="form-item">
                    <label for="address"><?= __('address') ?></label>
                    <input type="text" id="address" value="<?= htmlspecialchars($customer['address'] ?? '') ?>" placeholder="<?= __('enter_address') ?>" disabled>
                </div>
                <div class="form-item full-row">
                    <label for="password"><?= __('new_password') ?></label>
                    <input type="password" id="password" placeholder="<?= __('password_blank_msg') ?>" disabled>
                </div>

                <div id="customerMessage" class="form-message"></div>

                <div class="actions full-row" id="formActions" style="display: none;">
                    <button type="submit" class="btn btn-primary"><?= __('update_profile') ?></button>
                    <button type="button" class="btn btn-secondary" onclick="toggleEdit()"><?= __('cancel') ?></button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function toggleEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input:not(#email)');
    const actions = document.getElementById('formActions');
    const editBtn = document.getElementById('editBtn');

    const isEditing = actions.style.display !== 'none';
    if (isEditing) {
        inputs.forEach(input => input.disabled = true);
        actions.style.display = 'none';
        editBtn.style.display = 'inline-block';
    } else {
        inputs.forEach(input => input.disabled = false);
        actions.style.display = 'flex';
        editBtn.style.display = 'none';
    }
}

document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const customerMessage = document.getElementById('customerMessage');
    customerMessage.textContent = "";
    customerMessage.classList.remove('success');

    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerText;
    btn.innerText = "<?= __('updating') ?>";
    btn.disabled = true;

    const name = document.getElementById('name').value.trim();
    const username = document.getElementById('username').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const city = document.getElementById('city').value.trim();
    const address = document.getElementById('address').value.trim();
    const password = document.getElementById('password').value;

    // Validation
    // Validation
    const nameRegex = /^[a-zA-Z\s]+$/;
    const usernameRegex = /^[a-zA-Z\s]+$/;
    // Phone: Starts with 09 (10 digits total) OR +251 (13 chars total)
    const phoneRegex = /^(09\d{8}|\+251\d{9})$/;

    if (!name || !username || !phone || !city || !address) {
        customerMessage.textContent = "<?= __('all_fields_required') ?>";
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    if (!nameRegex.test(name)) {
        customerMessage.textContent = "<?= __('invalid_full_name') ?>"; // Ensure this key exists or use English fallback
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }
    
    // Allow username to be just letters? User said "only the letter".
    if (!usernameRegex.test(username)) {
         // Re-using invalid_full_name key or similar if 'invalid_username' is missing, 
         // but let's assume 'invalid_full_name' is close enough or use plain text if translations irrelevant for this error
        customerMessage.textContent = "Username must contain only letters"; 
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    if (!phoneRegex.test(phone)) {
        customerMessage.textContent = "<?= __('invalid_phone_number') ?>";
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    if (password && password.length < 6) {
        customerMessage.textContent = "<?= __('password_min_length') ?>";
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    const payload = { name, username, phone, city, address, password };

    try {
        const response = await fetch('/cargo-project/backend/api/customer/update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            customerMessage.textContent = "<?= __('profile_updated_success') ?>";
            customerMessage.classList.add('success');
            setTimeout(() => location.reload(), 1000);
        } else {
            customerMessage.textContent = result.error || "<?= __('profile_update_error') ?>";
        }
    } catch (error) {
        console.error(error);
        customerMessage.textContent = "<?= __('profile_update_error') ?>";
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});

if (typeof feather !== 'undefined') feather.replace();
</script>

<style>
.form-message {
    margin-top: 10px;
    color: red;
    font-size: 14px;
}
.form-message.success {
    color: green;
}
</style>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>