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
                    <h3>Total Trips</h3>
                    <p class="stat-number" id="totalTrips">--</p>
                    <div class="stat-trend up">All assignments</div>
                </div>
                <div class="stat-card">
                    <h3>Completed Trips</h3>
                    <p class="stat-number" id="completedTrips">--</p>
                    <div class="stat-trend up">Successfully delivered</div>
                </div>
                <div class="stat-card">
                    <h3>Total Earnings</h3>
                    <p class="stat-number" id="totalEarnings">--</p>
                    <div class="stat-trend up">Lifetime earnings</div>
                </div>
                <div class="stat-card">
                    <h3>Rating</h3>
                    <p class="stat-number" id="rating">--</p>
                    <div class="stat-trend">Out of 5.0</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Deliveries</h3>
                <div class="activity-list" id="recentDeliveries">
                    <p style="color: #64748b; padding: 20px; text-align: center;">Loading deliveries...</p>
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
async function loadDashboardStats() {
    try {
        const response = await fetch('/cargo-project/backend/api/transporter/get_transporter_stats.php');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            
            // Update Stats
            document.getElementById('totalTrips').innerText = data.totalTrips;
            document.getElementById('completedTrips').innerText = data.completedTrips;
            document.getElementById('totalEarnings').innerText = data.totalEarnings + ' ETB';
            document.getElementById('rating').innerText = data.rating + ' ★';

            // Update Recent Deliveries
            const deliveriesList = document.getElementById('recentDeliveries');
            deliveriesList.innerHTML = '';

            if (data.recentDeliveries.length === 0) {
                deliveriesList.innerHTML = '<p style="color: #64748b; padding: 20px; text-align: center;">No recent deliveries</p>';
            } else {
                data.recentDeliveries.forEach(item => {
                    const statusClass = item.status === 'delivered' ? 'approved' : 
                                        item.status === 'in-transit' ? 'pending' : 'pending';
                    deliveriesList.innerHTML += `
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i data-feather="package"></i>
                            </div>
                            <div class="activity-content">
                                <p><strong>Request #${item.id}</strong> - ${item.customer}</p>
                                <small style="color: #64748b;">${item.pickup} → ${item.dropoff}</small>
                                <div style="margin-top: 5px;">
                                    <span class="badge ${statusClass}">${item.status}</span>
                                    <span style="margin-left: 10px; color: #16a34a; font-weight: 600;">${item.earning} ETB</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                feather.replace();
            }
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

loadDashboardStats();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>