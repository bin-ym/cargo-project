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
            <h2>My Profile</h2>
            <div class="user-info">
                <span class="badge badge-primary">
                    <?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?>
                </span>
            </div>
        </header>

        <!-- Profile Form -->
        <div class="recent-activity-card">
            <h3>Personal Information</h3>

            <form id="profileForm" class="profile-form" style="margin-top: 20px;">

    <div class="form-item">
        <label for="name">Full Name</label>
        <input type="text" id="name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
    </div>

    <div class="form-item">
        <label for="username">Username</label>
        <input type="text" id="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
    </div>

    <div class="form-item">
        <label for="email">Email</label>
        <input type="email" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
    </div>

    <div class="form-item">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
    </div>

    <div class="form-item">
        <label for="city">City</label>
        <input type="text" id="city" value="<?= htmlspecialchars($customer['city'] ?? '') ?>" placeholder="Enter your city">
    </div>

    <div class="form-item">
        <label for="address">Address</label>
        <input type="text" id="address" value="<?= htmlspecialchars($customer['address'] ?? '') ?>" placeholder="Enter your address">
    </div>

    <!-- Password full width -->
    <div class="form-item full-row">
        <label for="password">New Password</label>
        <input type="password" id="password" placeholder="Leave blank to keep current password">
    </div>

    <!-- Submit full width -->
    <div class="actions full-row">
        <button type="submit" class="btn btn-primary">Update Profile</button>
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

    btn.innerText = 'Updating...';
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
            alert('Profile updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error(error);
        alert('An error occurred while updating profile.');
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>
