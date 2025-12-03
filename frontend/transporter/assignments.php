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
            <h2>My Assignments</h2>
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
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="row-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#102</td>
                            <td>Abebe Kebede</td>
                            <td>Addis Ababa</td>
                            <td>Bahir Dar</td>
                            <td>2025-11-28</td>
                            <td><span class="badge pending">Pending Pickup</span></td>
                            <td class="row-action">
                                <button class="btn-small btn-view">Start Delivery</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#98</td>
                            <td>Sara Daniel</td>
                            <td>Gondar</td>
                            <td>Addis Ababa</td>
                            <td>2025-11-29</td>
                            <td><span class="badge approved">Assigned</span></td>
                            <td class="row-action">
                                <button class="btn-small btn-view">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
