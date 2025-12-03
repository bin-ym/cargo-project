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
            <h2>My Requests</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Pickup Location</th>
                            <th>Dropoff Location</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="row-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#CT-2847</td>
                            <td>Addis Ababa</td>
                            <td>Bahir Dar</td>
                            <td>2025-11-28</td>
                            <td><span class="badge in-transit">In Transit</span></td>
                            <td class="row-action">
                                <button class="btn-small btn-view">Track</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#CT-2845</td>
                            <td>Gondar</td>
                            <td>Addis Ababa</td>
                            <td>2025-11-27</td>
                            <td><span class="badge approved">Delivered</span></td>
                            <td class="row-action">
                                <button class="btn-small btn-view">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#CT-2840</td>
                            <td>Hawassa</td>
                            <td>Adama</td>
                            <td>2025-11-25</td>
                            <td><span class="badge pending">Pending</span></td>
                            <td class="row-action">
                                <button class="btn-small btn-view">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
