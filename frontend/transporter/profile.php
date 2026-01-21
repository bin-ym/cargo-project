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
            <h2><?= __('my_profile') ?></h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 600px;">
                <h3><?= __('personal_information') ?></h3>
                <form class="mt-20" id="profileForm">
                    <div class="form-group">
                        <label class="form-label"><?= __('full_name') ?></label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-control" pattern="[A-Za-z\s]+" title="Letters and spaces only" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('email') ?></label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('phone') ?></label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control" pattern="[0-9]+" title="Numbers only" required>
                    </div>
                    <p class="info-box">
                        <?= __('transporter_profile_note') ?>
                    </p>
                    <button type="submit" class="btn btn-primary" id="saveBtn"><?= __('update_profile') ?></button>
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
    btn.innerText = "<?= __('saving') ?>";
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
            alert("<?= __('profile_updated_success') ?>");
            location.reload();
        } else {
            alert(result.error || "<?= __('update_failed') ?>");
        }
    } catch (err) {
        console.error(err);
        alert("<?= __('error_occurred') ?>");
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?>
