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
                    <p class="stat-number" id="totalRequests">--</p>
                    <div class="stat-trend up">All requests</div>
                </div>
                <div class="stat-card">
                    <h3>Approved</h3>
                    <p class="stat-number" id="approvedRequests">--</p>
                    <div class="stat-trend up">Successfully approved</div>
                </div>
                <div class="stat-card">
                    <h3>Pending</h3>
                    <p class="stat-number" id="pendingRequests">--</p>
                    <div class="stat-trend down">Awaiting action</div>
                </div>
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <p class="stat-number" id="revenue">--</p>
                    <div class="stat-trend up">Estimated revenue</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <div class="activity-list" id="activityList">
                    <div class="activity-item">
                        <div class="activity-content">
                            <p style="color: #94a3b8;">Loading activity...</p>
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
// Load dashboard stats
async function loadDashboardStats() {
    try {
        const response = await fetch('/cargo-project/backend/api/admin/get_dashboard_stats.php');
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            // Update stats
            document.getElementById('totalRequests').textContent = data.totalRequests;
            document.getElementById('approvedRequests').textContent = data.approvedRequests;
            document.getElementById('pendingRequests').textContent = data.pendingRequests;
            document.getElementById('revenue').textContent = '$' + data.revenue.toLocaleString();
            
            // Update activity list
            const activityList = document.getElementById('activityList');
            if (data.recentActivity.length > 0) {
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
            } else {
                activityList.innerHTML = '<div class="activity-item"><div class="activity-content"><p style="color: #94a3b8;">No recent activity</p></div></div>';
            }
        } else {
            console.error('Failed to load stats:', result.error);
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
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

// Load stats on page load
loadDashboardStats();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>