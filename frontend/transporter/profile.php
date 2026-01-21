<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_transporter.php';
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
                    <i data-feather="edit-2" style="width: 16px; height: 16px; margin-right: 5px;"></i>
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
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('email') ?></label>
                        <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('phone') ?></label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control" disabled>
                    </div>

                    <p class="info-box"><?= __('transporter_profile_note') ?></p>

                    <!-- Inline message -->
                    <div id="transporterMessage" class="form-message"></div>

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

    const messageDiv = document.getElementById('transporterMessage');
    messageDiv.textContent = "";
    messageDiv.classList.remove('success');

    const btn = document.getElementById('saveBtn');
    const originalText = btn.innerText;
    btn.innerText = "<?= __('saving') ?>";
    btn.disabled = true;

    const full_name = document.getElementById('full_name').value.trim();
    const phone = document.getElementById('phone').value.trim();

    // Validation
    if (!full_name || !phone) {
        messageDiv.textContent = "<?= __('all_fields_required') ?>";
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    // Full name must be letters and spaces only
    if (!/^[A-Za-z\s]+$/.test(full_name)) {
        messageDiv.textContent = "<?= __('invalid_full_name') ?>";
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    // Phone must be digits
    if (!/^\d+$/.test(phone)) {
        messageDiv.textContent = "<?= __('invalid_phone_number') ?>";
        btn.innerText = originalText;
        btn.disabled = false;
        return;
    }

    const payload = { full_name, phone };

    try {
        const res = await fetch('/cargo-project/backend/api/transporter/update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (result.success) {
            messageDiv.textContent = "<?= __('profile_updated_success') ?>";
            messageDiv.classList.add('success');
            setTimeout(() => location.reload(), 1000);
        } else {
            messageDiv.textContent = result.error || "<?= __('update_failed') ?>";
        }
    } catch (err) {
        console.error(err);
        messageDiv.textContent = "<?= __('error_occurred') ?>";
    } finally {
        btn.innerText = originalText;
        btn.disabled = false;
    }
});

feather.replace();
</script>

<style>
.form-message {
    margin-top: 10px;
    font-size: 14px;
    color: red;
}
.form-message.success {
    color: green;
}
</style>