<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_customer.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>My Profile</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 600px;">
<?php
require_once __DIR__ . '/../../backend/config/database.php';
$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch customer specific data
$stmtCust = $db->prepare("SELECT * FROM customers WHERE user_id = ?");
$stmtCust->execute([$_SESSION['user_id']]);
$customer = $stmtCust->fetch();
?>
                <h3>Personal Information</h3>
                <form style="margin-top: 20px;" method="POST" action="/cargo-project/backend/api/customer/update_profile.php">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Email</label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #f1f5f9; cursor: not-allowed;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Phone</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Address</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($customer['address'] ?? '') ?>" placeholder="Enter your address" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">City</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($customer['city'] ?? '') ?>" placeholder="Enter your city" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
