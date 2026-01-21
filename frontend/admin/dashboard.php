<?php
require_once __DIR__ . '/../../backend/config/session.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<!-- Sidebar -->
<?php include __DIR__ . '/sidebar.php'; ?>

<!-- Main content -->
<main class="main-content">
    <!-- Topbar -->
    <header class="topbar">
        <h2><?= __('welcome') ?>, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h2>
        <div class="user-info">
            <span><?= ucfirst($_SESSION['role']); ?></span>
        </div>
    </header>

    <!-- Dashboard content -->
    <div class="content">

        <!-- Stats grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= __('total_requests') ?></h3>
                <p class="stat-number" id="totalRequests">--</p>
                <div class="stat-trend up"><?= __('all_requests') ?></div>
            </div>

            <div class="stat-card">
                <h3><?= __('approved') ?></h3>
                <p class="stat-number" id="approvedRequests">--</p>
                <div class="stat-trend up"><?= __('successfully_approved') ?></div>
            </div>

            <div class="stat-card">
                <h3><?= __('pending') ?></h3>
                <p class="stat-number" id="pendingRequests">--</p>
                <div class="stat-trend down"><?= __('awaiting_action') ?></div>
            </div>

            <div class="stat-card">
                <h3><?= __('revenue') ?></h3>
                <p class="stat-number" id="revenue">--</p>
                <div class="stat-trend up"><?= __('estimated_revenue') ?></div>
            </div>
        </div>

        <!-- Recent activity -->
        <div class="recent-activity">
            <h3><?= __('recent_activity') ?></h3>
            <div class="activity-list" id="activityList">
                <div class="activity-item">
                    <div class="activity-content">
                        <p class="text-muted"><?= __('loading_activity') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="requests.php" class="btn btn-primary">
                <i data-feather="file-text"></i> <?= __('view_all_requests') ?>
            </a>
            <a href="reports.php" class="btn btn-secondary">
                <i data-feather="bar-chart"></i> <?= __('generate_reports') ?>
            </a>
        </div>

    </div>
</main>

<!-- Footer -->


<!-- Dashboard JS -->
<script>
async function loadDashboardStats() {
    try {
        const response = await fetch('/cargo-project/backend/api/admin/get_dashboard_stats.php');
        const result = await response.json();

        if (!result.success) {
            console.error(result.error);
            return;
        }

        const data = result.data;

        document.getElementById('totalRequests').textContent = data.totalRequests;
        document.getElementById('approvedRequests').textContent = data.approvedRequests;
        document.getElementById('pendingRequests').textContent = data.pendingRequests;
        document.getElementById('revenue').textContent =
            '$' + Number(data.revenue).toLocaleString();

        const activityList = document.getElementById('activityList');

        if (data.recentActivity.length === 0) {
            activityList.innerHTML = `
                <div class="activity-item">
                    <div class="activity-content">
                        <p class="text-muted"><?= __('no_recent_activity') ?></p>
                    </div>
                </div>`;
            return;
        }

        activityList.innerHTML = data.recentActivity.map(activity => `
            <div class="activity-item">
                <div class="activity-icon">
                    <i data-feather="${activity.icon}"></i>
                </div>
                <div class="activity-content">
                    <p>${activity.message}</p>
                    <span class="activity-time">${formatTimeAgo(activity.time)}</span>
                </div>
            </div>
        `).join('');

        feather.replace();

    } catch (err) {
        console.error('Dashboard load failed:', err);
    }
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const seconds = Math.floor((new Date() - date) / 1000);

    if (seconds < 60) return 'Just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    const days = Math.floor(hours / 24);
    if (days < 30) return `${days} day${days > 1 ? 's' : ''} ago`;
    const months = Math.floor(days / 30);
    return `${months} month${months > 1 ? 's' : ''} ago`;
}

loadDashboardStats();
feather.replace();
</script>