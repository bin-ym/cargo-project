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
        <header class="topbar" style="display: flex; justify-content: space-between; align-items: center;">
            <h2><?= __('my_profile') ?></h2>
            <div style="display: flex; gap: 15px; align-items: center;">
                <button type="button" class="btn btn-outline" id="editBtn" onclick="toggleEdit()">
                    <i data-feather="edit-2" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 5px;"></i>
                    <?= __('edit') ?>
                </button>
                <div class="user-info">
                    <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 600px;">
                <h3><?= __('personal_information') ?></h3>
                <form class="mt-20" id="profileForm">
                    <div class="form-group">
                        <label class="form-label"><?= __('full_name') ?></label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-control" pattern="[A-Za-z\s]+" title="Letters and spaces only" required disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('email') ?></label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('phone') ?></label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control" pattern="[0-9]+" title="Numbers only" required disabled>
                    </div>
                    <p class="info-box">
                        <?= __('transporter_profile_note') ?>
                    </p>
                    <div id="formActions" style="display: none; gap: 10px;">
                        <button type="submit" class="btn btn-primary" id="saveBtn"><?= __('update_profile') ?></button>
                        <button type="button" class="btn btn-secondary" onclick="toggleEdit()"><?= __('cancel') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function toggleEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input:not([readonly])');
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


