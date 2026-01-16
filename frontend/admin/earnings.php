<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <h2>Earnings Overview</h2>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Revenue (Gross)</h3>
                    <p class="stat-number" id="totalRevenue">--</p>
                    <div class="stat-trend up">From customers</div>
                </div>
                <div class="stat-card">
                    <h3>Net Profit (20%)</h3>
                    <p class="stat-number" id="netProfit">--</p>
                    <div class="stat-trend up">After transporter commission</div>
                </div>
                <div class="stat-card">
                    <h3>Total Requests</h3>
                    <p class="stat-number" id="totalRequests">--</p>
                    <div class="stat-trend">Paid requests</div>
                </div>
            </div>

            <div class="recent-activity" style="margin-top: 30px;">
                <h3>Revenue by Month (Last 6 Months)</h3>
                <canvas id="earningsChart" style="max-height: 300px; padding: 20px;"></canvas>
            </div>

            <div class="table-wrapper" style="margin-top: 30px;">
                <h3>Recent Transactions</h3>
                <table class="table-modern" style="margin-top: 15px;">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsBody">
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            <!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadEarnings() {
    try {
        const response = await fetch('/cargo-project/backend/api/admin/get_earnings.php');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            
            // Update Stats
            document.getElementById('totalRevenue').innerText = data.totalRevenue + ' ETB';
            document.getElementById('netProfit').innerText = data.netProfit + ' ETB';
            document.getElementById('totalRequests').innerText = data.totalRequests;

            // Create Chart
            const months = data.byMonth.map(m => m.month).reverse();
            const revenues = data.byMonth.map(m => parseFloat(m.revenue)).reverse();
            
            const ctx = document.getElementById('earningsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Revenue (ETB)',
                        data: revenues,
                        backgroundColor: 'rgba(37, 99, 235, 0.8)',
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Update Recent Transactions
            const tbody = document.getElementById('transactionsBody');
            tbody.innerHTML = '';

            if (data.recentTransactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">No transactions found</td></tr>';
            } else {
                data.recentTransactions.forEach(t => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${t.id}</td>
                            <td>${t.customer_name}</td>
                            <td><strong>${parseFloat(t.price).toFixed(2)} ETB</strong></td>
                            <td>${new Date(t.created_at).toLocaleDateString()}</td>
                        </tr>
                    `;
                });
            }
        }
    } catch (error) {
        console.error('Error loading earnings:', error);
    }
}

loadEarnings();
feather.replace();
</script>


