<?php
require_once __DIR__ . '/../../backend/config/session.php';

// Transporter only
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
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Transporter'); ?> 👋</h2>
            <div class="user-info">
                <span><?= ucfirst($_SESSION['role']) ?></span>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Active Assignments</h3>
                    <p class="stat-number" id="activeAssignments">3</p>
                    <div class="stat-trend up">On the road</div>
                </div>
                <div class="stat-card">
                    <h3>Completed Assignments</h3>
                    <p class="stat-number" id="completed Assignments">28</p>
                    <div class="stat-trend up">+5 this month</div>
                </div>
                <div class="stat-card">
                    <h3>Pending Assignments</h3>
                    <p class="stat-number" id="pending Assignments">5</p>
                    <div class="stat-trend">Awaiting pickup</div>
                </div>
                <div class="stat-card">
                    <h3>Earnings</h3>
                    <p class="stat-number">45,000 ETB</p>
                    <div class="stat-trend up">+15% this month</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="truck"></i>
                        </div>
                        <div class="activity-content">
                            <p>Shipment <strong>#SH-8932</strong> is in transit</p>
                            <span class="activity-time">1 hour ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Delivery #DL-7821</strong> completed successfully</p>
                            <span class="activity-time">4 hours ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="package"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>New assignment</strong> received</p>
                            <span class="activity-time">1 day ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="assignments.php" class="btn btn-primary">
                    <i data-feather="truck"></i>
                    View Assignments
                </a>
                <a href="earnings.php" class="btn btn-secondary">
                    <i data-feather="dollar-sign"></i>
                    View Earnings
                </a>
            </div>
        </div>
    </main>
</div>

<script>
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>