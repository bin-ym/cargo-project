<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_transporter.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>Delivery History</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Delivered Date</th>
                            <th>Earnings</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#89</td>
                            <td>Sara Daniel</td>
                            <td>Gondar → Addis Ababa</td>
                            <td>2025-11-27</td>
                            <td>2,500 ETB</td>
                            <td><span class="badge approved">Delivered</span></td>
                        </tr>
                        <tr>
                            <td>#85</td>
                            <td>Abebe Kebede</td>
                            <td>Hawassa → Adama</td>
                            <td>2025-11-25</td>
                            <td>1,800 ETB</td>
                            <td><span class="badge approved">Delivered</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
