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
            <h2><?= __('earnings') ?></h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?= __('total_earnings') ?></h3>
                    <p class="stat-number" id="totalEarnings">--</p>
                    <div class="stat-trend up"><?= __('lifetime_earnings') ?></div>
                </div>
                <div class="stat-card">
                    <h3><?= __('completed_trips') ?></h3>
                    <p class="stat-number" id="completedTrips">--</p>
                    <div class="stat-trend"><?= __('successfully_delivered') ?></div>
                </div>
                <div class="stat-card">
                    <h3><?= __('avg_per_delivery') ?></h3>
                    <p class="stat-number" id="avgEarning">--</p>
                    <div class="stat-trend"><?= __('average_payout') ?></div>
                </div>
                <div class="stat-card">
                    <h3><?= __('rating') ?></h3>
                    <p class="stat-number" id="rating">--</p>
                    <div class="stat-trend" id="ratingTrend"><?= __('out_of_5') ?></div>
                </div>
            </div>

            <div class="recent-activity">
                <h3><?= __('earnings_breakdown_7days') ?></h3>
                <canvas id="earningsChart" class="p-20" style="max-height: 300px;"></canvas>
            </div>

            <div class="recent-activity mt-20" style="margin-top: 30px;">
                <h3><?= __('recent_payments') ?></h3>
                <div class="activity-list" id="recentPayments">
                    <p class="text-center p-20 text-muted"><?= __('loading_payments') ?></p>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadEarnings() {
    try {
        const response = await fetch('/cargo-project/backend/api/transporter/get_transporter_stats.php');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            
            // Update Stats
            document.getElementById('totalEarnings').innerText = data.totalEarnings + ' ETB';
            document.getElementById('completedTrips').innerText = data.completedTrips;
            
            const totalEarningsVal = parseFloat(data.totalEarnings.replace(/,/g, ''));
            const avgEarning = data.completedTrips > 0 ? (totalEarningsVal / data.completedTrips).toFixed(2) : '0.00';
            document.getElementById('avgEarning').innerText = avgEarning + ' ETB';
            
            document.getElementById('rating').innerText = data.rating + ' ★';
            const ratingCount = data.ratingCount || 0;
            const ratingLabel = ratingCount === 1 ? "<?= __('rating_singular') ?>" : "<?= __('rating_plural') ?>";
            document.getElementById('ratingTrend').innerText = "<?= __('based_on') ?> " + ratingCount + " " + ratingLabel;

            // Create Chart
            const ctx = document.getElementById('earningsChart').getContext('2d');
            
            const labels = [];
            const chartData = [];
            
            if (Array.isArray(data.earningsChart)) {
                data.earningsChart.forEach(item => {
                    labels.push(new Date(item.date).toLocaleDateString('en-US', { weekday: 'short' }));
                    chartData.push(item.earnings);
                });
            } else {
                for(let i=0; i<7; i++) { labels.push(''); chartData.push(0); }
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "<?= __('earnings_etb') ?>",
                        data: chartData,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Update Recent Payments
            const paymentsList = document.getElementById('recentPayments');
            paymentsList.innerHTML = '';

            if (!data.recentDeliveries || data.recentDeliveries.length === 0) {
                paymentsList.innerHTML = '<p class="text-center p-20 text-muted"><?= __('no_recent_payments') ?></p>';
            } else {
                data.recentDeliveries.forEach(item => {
                    if (item.status === 'delivered') {
                        paymentsList.innerHTML += `
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i data-feather="dollar-sign"></i>
                                </div>
                                <div class="activity-content">
                                    <p><?= __('payment_received_for_request') ?> <strong>#${item.id}</strong></p>
                                    <span class="activity-time">${item.date}</span>
                                </div>
                                <strong class="text-success">+${item.earning} ETB</strong>
                            </div>
                        `;
                    }
                });
                feather.replace();
            }
        }
    } catch (error) {
        console.error('Error loading earnings:', error);
    }
}

loadEarnings();
feather.replace();
</script>

