<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_transporter.php';

// Fetch user data from database
require_once __DIR__ . '/../../backend/config/database.php';
$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>My Profile</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 600px;">
                <h3>Personal Information</h3>
                <form class="mt-20" id="profileForm">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-control" pattern="[A-Za-z\s]+" title="Letters and spaces only" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control" pattern="[0-9]+" title="Numbers only" required>
                    </div>
                    <p class="info-box">
                        <strong>Note:</strong> Plate Number and Vehicle Type will be assigned by the admin when you receive delivery assignments.
                    </p>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Update Profile</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('saveBtn');
    const originalText = btn.innerText;
    btn.innerText = 'Saving...';
    btn.disabled = true;

    const payload = {
        full_name: document.getElementById('full_name').value,
        phone: document.getElementById('phone').value
    };

    try {
        const res = await fetch('/cargo-project/backend/api/transporter/update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success) {
            alert('Profile updated successfully');
            location.reload();
        } else {
            alert(result.error || 'Update failed');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred');
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});
feather.replace();
</script>

<!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->
