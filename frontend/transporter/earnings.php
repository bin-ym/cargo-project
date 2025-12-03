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
            <h2>Earnings</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>This Month</h3>
                    <p class="stat-number">45,000 ETB</p>
                    <div class="stat-trend up">+15% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Last Month</h3>
                    <p class="stat-number">39,000 ETB</p>
                    <div class="stat-trend">November 2025</div>
                </div>
                <div class="stat-card">
                    <h3>Total Earnings</h3>
                    <p class="stat-number">285,000 ETB</p>
                    <div class="stat-trend up">All time</div>
                </div>
                <div class="stat-card">
                    <h3>Avg per Delivery</h3>
                    <p class="stat-number">2,200 ETB</p>
                    <div class="stat-trend">Based on 28 deliveries</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Payments</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div class="activity-content">
                            <p>Payment received for Order <strong>#89</strong></p>
                            <span class="activity-time">2 days ago</span>
                        </div>
                        <strong style="color: #16a34a;">+2,500 ETB</strong>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div class="activity-content">
                            <p>Payment received for Order <strong>#85</strong></p>
                            <span class="activity-time">4 days ago</span>
                        </div>
                        <strong style="color: #16a34a;">+1,800 ETB</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
