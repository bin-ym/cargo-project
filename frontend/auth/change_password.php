<?php
require_once __DIR__ . '/../../backend/config/session.php';

// If not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password • Cargo Transport</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">CT</div>
                <h1>Change Password</h1>
                <p>Please update your password to continue.</p>
            </div>

            <div class="form-body">
                <div id="errorAlert" class="alert alert-error" style="display:none; color:red; margin-bottom:10px;"></div>
                <div id="successAlert" class="alert alert-success" style="display:none; color:green; margin-bottom:10px;"></div>

                <form id="changePasswordForm">
                    <div class="input-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    
                    <div class="input-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required minlength="6">
                    </div>

                    <div class="input-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="6">
                    </div>

                    <button type="submit" class="btn">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');
        const btn = e.target.querySelector('.btn');
        
        // Reset alerts
        errorAlert.style.display = 'none';
        successAlert.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Updating...';

        try {
            const response = await fetch('/cargo-project/backend/api/auth/update_password.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                successAlert.textContent = 'Password updated successfully! Redirecting...';
                successAlert.style.display = 'block';
                e.target.reset();
                
                setTimeout(() => {
                    // Redirect based on role
                    const role = '<?= $_SESSION['role'] ?>';
                    if (role === 'admin') window.location.href = '/cargo-project/frontend/admin/dashboard.php';
                    else if (role === 'customer') window.location.href = '/cargo-project/frontend/customer/dashboard.php';
                    else if (role === 'transporter') window.location.href = '/cargo-project/frontend/transporter/dashboard.php';
                    else window.location.href = '/cargo-project/frontend/auth/login.php';
                }, 1500);
            } else {
                errorAlert.textContent = data.error || 'Failed to update password';
                errorAlert.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Update Password';
            }
        } catch (error) {
            errorAlert.textContent = 'An error occurred. Please try again.';
            errorAlert.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Update Password';
        }
    });
    </script>
</body>
</html>
