<?php
require_once __DIR__ . '/../../backend/config/session.php';

// Customer only
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
            <h2>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Customer'); ?> 👋</h2>
            <div class="user-info">
                <span><?= ucfirst($_SESSION['role']) ?></span>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>My Requests</h3>
                    <p class="stat-number" id="totalRequests">12</p>
                    <div class="stat-trend up">+3 this month</div>
                </div>
                <div class="stat-card">
                    <h3>Completed</h3>
                    <p class="stat-number" id="completedRequests">8</p>
                    <div class="stat-trend up">+2 this month</div>
                </div>
                <div class="stat-card">
                    <h3>Pending</h3>
                    <p class="stat-number" id="pendingRequests">3</p>
                    <div class="stat-trend">Awaiting approval</div>
                </div>
                <div class="stat-card">
                    <h3>In Transit</h3>
                    <p class="stat-number">1</p>
                    <div class="stat-trend up">On the way</div>
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
                            <p><strong>Shipment #CT-2847</strong> is in transit</p>
                            <span class="activity-time">2 hours ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div class="activity-content">
                            <p>Request <strong>#CT-2845</strong> was delivered</p>
                            <span class="activity-time">1 day ago</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i data-feather="package"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>New request</strong> submitted successfully</p>
                            <span class="activity-time">3 days ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="new_request.php" class="btn btn-primary">
                    <i data-feather="plus-circle"></i>
                    New Request
                </a>
                <a href="my_requests.php" class="btn btn-secondary">
                    <i data-feather="list"></i>
                    View My Requests
                </a>
            </div>
        </div>
    </main>
</div>

<script>
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>