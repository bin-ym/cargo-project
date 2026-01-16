<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';

$requestId = $_GET['id'] ?? 'Unknown';
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <h2>Request Details #<?= htmlspecialchars($requestId) ?></h2>
        </header>

        <div class="content">
            <div class="details-box">
                <h3>Customer: John Doe</h3>
                <p><strong>From:</strong> Addis Ababa</p>
                <p><strong>To:</strong> Bahir Dar</p>
                <p><strong>Weight:</strong> 120 kg</p>
                <p><strong>Status:</strong> <span class="badge pending">Pending</span></p>

                <div class="actions">
                    <button class="btn btn-primary">Approve Request</button>
                    <button class="btn btn-danger">Reject</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->
