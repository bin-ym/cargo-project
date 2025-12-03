<?php
require_once __DIR__ . '/../../backend/config/session.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h2>
            <div class="user-info">
                <span><?= ucfirst($_SESSION['role']) ?></span>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Requests</h3>
                    <p class="stat-number" id="totalRequests">47</p>
                    <div class="stat-trend up">+12% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Approved</h3>
                    <p class="stat-number" id="approvedRequests">38</p>
                    <div class="stat-trend up">+8% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Pending</h3>
                    <p class="stat-number" id="pendingRequests">9</p>
                    <div class="stat-trend down">-3% from last month</div>
                </div>
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <p class="stat-number">$12,450</p>
                    <div class="stat-trend up">+15% from last month</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="package"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>New request</strong> from John Doe</p>
                            <span class="activity-time">2 minutes ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div class="activity-content">
                            <p>Request <strong>#CT-2847</strong> was approved</p>
                            <span class="activity-time">1 hour ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="truck"></i>
                        </div>
                        <div class="activity-content">
                            <p>Shipment <strong>#SH-8932</strong> is in transit</p>
                            <span class="activity-time">3 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="requests.php" class="btn btn-primary">
                    <i data-feather="file-text"></i>
                    View All Requests
                </a>
                <a href="reports.php" class="btn btn-secondary">
                    <i data-feather="bar-chart"></i>
                    Generate Reports
                </a>
            </div>
        </div>
    </main>
</div>

<script>
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>