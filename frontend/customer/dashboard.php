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
                    <p class="stat-number" id="totalRequests">--</p>
                    <div class="stat-trend up">Total requests</div>
                </div>
                <div class="stat-card">
                    <h3>Pending</h3>
                    <p class="stat-number" id="pendingRequests">--</p>
                    <div class="stat-trend">Awaiting approval</div>
                </div>
                <div class="stat-card">
                    <h3>Approved</h3>
                    <p class="stat-number" id="approvedRequests">--</p>
                    <div class="stat-trend">Awaiting transporter</div>
                </div>
                <div class="stat-card">
                    <h3>In Transit</h3>
                    <p class="stat-number" id="inTransitCount">--</p>
                    <div class="stat-trend">Currently active</div>
                </div>
                <div class="stat-card">
                    <h3>Completed</h3>
                    <p class="stat-number" id="completedRequests">--</p>
                    <div class="stat-trend up">Successfully delivered</div>
                </div>
            </div>

            <!-- Pending Payments Section -->
            <div id="pendingPaymentsSection" style="display: none; margin-top: 30px;">
                <h3 style="margin-bottom: 15px; color: #b91c1c;">⚠️ Pending Payments</h3>
                <div class="card" style="border-left: 4px solid #ef4444;">
                    <div id="pendingPaymentsList"></div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <div class="activity-list" id="activityList">
                    <p style="color: #64748b; padding: 20px; text-align: center;">Loading activity...</p>
                </div>
            </div>

            <div class="quick-actions">
                <a href="new_request.php" class="btn btn-primary">
                    <i data-feather="plus"></i>
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
// Load dashboard stats
async function loadDashboardStats() {
    try {
        const response = await fetch('/cargo-project/backend/api/customer/get_dashboard_stats.php');
        const result = await response.json();

        if (result.success) {
            const data = result.data;
            
            // Update Stats
            document.getElementById('totalRequests').innerText = data.totalRequests;
            document.getElementById('completedRequests').innerText = data.completedRequests;
            document.getElementById('pendingRequests').innerText = data.pendingRequests;
            document.getElementById('approvedRequests').innerText = data.approvedRequests || 0;
            document.getElementById('inTransitCount').innerText = data.inTransitCount;

            // Update Activity
            const activityList = document.getElementById('activityList');
            activityList.innerHTML = '';

            if (data.recentActivity.length === 0) {
                activityList.innerHTML = '<p style="color: #64748b; padding: 20px; text-align: center;">No recent activity</p>';
            } else {
                data.recentActivity.forEach(item => {
                    activityList.innerHTML += `
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i data-feather="${item.icon}"></i>
                            </div>
                            <div class="activity-details">
                                <p>${item.message}</p>
                                <span class="activity-time">${formatTimeAgo(item.time)}</span>
                            </div>
                        </div>
                    `;
                });
                feather.replace();
            }

            // Update Pending Payments
            const pendingSection = document.getElementById('pendingPaymentsSection');
            const pendingList = document.getElementById('pendingPaymentsList');
            
            if (data.pendingPayments && data.pendingPayments.length > 0) {
                pendingSection.style.display = 'block';
                pendingList.innerHTML = '';
                
                data.pendingPayments.forEach(p => {
                    pendingList.innerHTML += `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #f1f5f9;">
                            <div>
                                <strong style="color: #0f172a;">Request #${p.id}</strong>
                                <div style="color: #64748b; font-size: 0.9rem;">${p.pickup_location} -> ${p.dropoff_location}</div>
                                <div style="color: #0f172a; font-weight: 600; margin-top: 4px;">${p.price} ETB</div>
                            </div>
                            <button class="btn btn-primary" onclick="initiatePayment(${p.id})" style="background: #16a34a; border-color: #16a34a;">Pay Now</button>
                        </div>
                    `;
                });
            } else {
                pendingSection.style.display = 'none';
            }

        }
    } catch (error) {
        console.error('Error loading stats:', error);
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

async function initiatePayment(requestId) {
    if (!confirm("Proceed to payment for Request #" + requestId + "?")) return;

    try {
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = "Processing...";
        btn.disabled = true;

        const res = await fetch('/cargo-project/backend/api/customer/initiate_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId })
        });
        const data = await res.json();

        if (data.success) {
            window.location.href = data.payment_url;
        } else {
            alert("Payment Error: " + data.error);
            btn.innerText = originalText;
            btn.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert("An error occurred.");
    }
}

// Check for Payment Verification on Load
const urlParams = new URLSearchParams(window.location.search);
const txRef = urlParams.get('tx_ref');
const status = urlParams.get('status');

if (txRef || status === 'success') {
    verifyPayment(txRef);
}

async function verifyPayment(ref) {
    console.log("Verifying payment...", ref);
    
    try {
        const response = await fetch(`/cargo-project/backend/api/payment/verify.php?tx_ref=${ref}`);
        const result = await response.json();

        if (result.success) {
            alert("Payment Successful! Your request has been approved.");
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
            // Reload to update stats
            window.location.reload();
        } else {
            console.error(result.error);
            alert("Payment verification failed: " + (result.error || "Unknown error"));
        }
    } catch (err) {
        console.error("Verification error:", err);
        alert("An error occurred while verifying payment.");
    }
}

// Load stats on page load
loadDashboardStats();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>