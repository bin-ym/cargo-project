<?php
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../../backend/config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_transporter.php';
require_once __DIR__ . '/../../backend/controllers/RequestController.php';

$db = Database::getConnection();
$controller = new RequestController();
$requests = $controller->getByTransporterId($_SESSION['user_id']);
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
                            <th>Request ID</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Date</th>
                            <th>Earnings</th>
                            <th>Rating</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                    No delivery history found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $req): ?>
                                <?php
                                $statusClass = $req['shipment_status'] === 'delivered' ? 'approved' : 
                                               ($req['shipment_status'] === 'in-transit' ? 'pending' : 'pending');
                                $earnings = number_format($req['price'] * 0.8, 2); // 80% commission
                                
                                // Get rating for this request
                                $ratingStmt = $db->prepare("SELECT rating FROM ratings WHERE request_id = ? AND transporter_id = ?");
                                $ratingStmt->execute([$req['id'], $_SESSION['user_id']]);
                                $ratingRow = $ratingStmt->fetch();
                                $rating = $ratingRow ? $ratingRow['rating'] . ' ★' : '-';
                                ?>
                                <tr>
                                    <td>#<?= $req['id'] ?></td>
                                    <td><?= htmlspecialchars($req['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($req['pickup_location']) ?> → <?= htmlspecialchars($req['dropoff_location']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($req['pickup_date'])) ?></td>
                                    <td><?= $earnings ?> ETB</td>
                                    <td><?= $rating ?></td>
                                    <td><span class="badge <?= $statusClass ?>"><?= ucfirst($req['shipment_status'] ?? 'assigned') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
