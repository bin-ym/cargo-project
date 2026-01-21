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
        <header class="topbar" style="margin-bottom: 30px;">
            <h2><?= __('my_profile') ?></h2>
            <div class="user-info">
                <span class="badge badge-primary">
                    <?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?>
                </span>
            </div>
        </header>

        <!-- Profile Form -->
        <div class="recent-activity-card">
            <h3><?= __('personal_information') ?></h3>

            <form id="profileForm" class="profile-form" style="margin-top: 20px;">

    <div class="form-item">
        <label for="name"><?= __('full_name') ?></label>
        <input type="text" id="name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
    </div>

    <div class="form-item">
        <label for="username"><?= __('username') ?></label>
        <input type="text" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
    </div>

    <div class="form-item">
        <label for="email"><?= __('email') ?></label>
        <input type="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
    </div>

    <div class="form-item">
        <label for="phone"><?= __('phone_number') ?></label>
        <input type="tel" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
    </div>

    <div class="form-item">
        <label for="city"><?= __('city') ?></label>
        <input type="text" id="city" value="<?= htmlspecialchars($customer['city'] ?? '') ?>" placeholder="<?= __('enter_city') ?>">
    </div>

    <div class="form-item">
        <label for="address"><?= __('address') ?></label>
        <input type="text" id="address" value="<?= htmlspecialchars($customer['address'] ?? '') ?>" placeholder="<?= __('enter_address') ?>">
    </div>

    <!-- Password full width -->
    <div class="form-item full-row">
        <label for="password"><?= __('new_password') ?></label>
        <input type="password" id="password" placeholder="<?= __('password_blank_msg') ?>">
    </div>

    <!-- Submit full width -->
    <div class="actions full-row">
        <button type="submit" class="btn btn-primary"><?= __('update_profile') ?></button>
    </div>

</form>
        </div>
    </main>
</div>

<script>
document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = e.target.querySelector('button');
    const originalText = btn.innerText;

    btn.innerText = "<?= __('updating') ?>";
    btn.disabled = true;

    const payload = {
        username: document.getElementById('username').value,
        name: document.getElementById('name').value,
        phone: document.getElementById('phone').value,
        address: document.getElementById('address').value,
        city: document.getElementById('city').value,
        password: document.getElementById('password').value
    };

    try {
        const response = await fetch('/cargo-project/backend/api/customer/update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            alert("<?= __('profile_updated_success') ?>");
            location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error(error);
        alert("<?= __('profile_update_error') ?>");
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>
