<?php
session_start();
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

$transporterId = $_GET['id'] ?? null;
if (!$transporterId) {
    header("Location: transporters.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <div class="header-left">
                <a href="transporters.php" class="btn-back">
                    <i data-feather="arrow-left"></i>
                </a>
                <h2>Transporter Details</h2>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="window.print()">
                    <i data-feather="printer"></i> Print
                </button>
            </div>
        </header>

        <div class="content" id="detailContent">
            <div class="loading-state">
                <div class="spinner"></div>
                <p>Loading transporter details...</p>
            </div>
        </div>
    </main>
</div>

<style>
.topbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 20;
}
.header-left { display: flex; align-items: center; gap: 15px; }
.content {
    padding: 24px;
}
.btn-back { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; color: #64748b; transition: all 0.2s; }
.btn-back:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }

.detail-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }

.profile-card { background: #fff; border-radius: 16px; padding: 30px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); height: fit-content; }
.profile-header { text-align: center; margin-bottom: 25px; }
.profile-avatar { width: 80px; height: 80px; background: var(--primary-100); color: var(--primary-600); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; font-weight: 700; }
.profile-header h3 { font-size: 20px; color: #0f172a; margin-bottom: 5px; }
.profile-header .badge { font-size: 12px; }

.info-list { display: flex; flex-direction: column; gap: 20px; }
.info-item { display: flex; flex-direction: column; gap: 5px; }
.info-item label { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.info-item span { font-size: 15px; color: #334155; font-weight: 500; }

.detail-main { display: flex; flex-direction: column; gap: 30px; }

.section-card { background: #fff; border-radius: 16px; padding: 25px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.section-card h3 { font-size: 18px; color: #0f172a; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.section-card h3 i { color: var(--primary-600); width: 20px; }

.license-preview { width: 100%; max-height: 400px; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; background: #f8fafc; display: flex; align-items: center; justify-content: center; }
.license-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
.license-preview .no-file { color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 10px; }

.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 100px 0; gap: 20px; color: #64748b; }
.spinner { width: 40px; height: 40px; border: 4px solid #f1f5f9; border-top-color: var(--primary-600); border-radius: 50%; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 1024px) {
    .detail-grid { grid-template-columns: 1fr; }
}
</style>

<script>
const transporterId = <?= json_encode($transporterId) ?>;
const API_URL = `/cargo-project/backend/api/transporters/index.php?id=${transporterId}&details=true`;

async function fetchDetails() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        
        if (result.success) {
            renderDetails(result.data);
        } else {
            document.getElementById('detailContent').innerHTML = `
                <div class="section-card text-center p-4">
                    <p class="text-danger">Error: ${result.error || 'Transporter not found'}</p>
                    <a href="transporters.php" class="btn btn-primary mt-4">Back to List</a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderDetails(data) {
    const container = document.getElementById('detailContent');
    
    let requestsHtml = '';
    if (data.recent_requests && data.recent_requests.length > 0) {
        requestsHtml = `
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.recent_requests.map(r => `
                            <tr>
                                <td>#CT-${String(r.id).padStart(4, '0')}</td>
                                <td>${r.customer_name}</td>
                                <td>${r.pickup_location} → ${r.dropoff_location}</td>
                                <td><span class="badge ${r.status}">${r.status}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    } else {
        requestsHtml = '<p class="text-muted text-center p-4">No recent activity found.</p>';
    }

    container.innerHTML = `
        <div class="detail-grid">
            <aside class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">${data.name.charAt(0)}</div>
                    <h3>${data.name}</h3>
                    <span class="badge ${data.status}">${data.status}</span>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <label>Username</label>
                        <span>@${data.username}</span>
                    </div>
                    <div class="info-item">
                        <label>Email Address</label>
                        <span>${data.email}</span>
                    </div>
                    <div class="info-item">
                        <label>Phone Number</label>
                        <span>${data.phone}</span>
                    </div>
                    <div class="info-item">
                        <label>Member Since</label>
                        <span>${new Date(data.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
            </aside>

            <div class="detail-main">
                <section class="section-card">
                    <h3><i data-feather="file-text"></i> License Documentation</h3>
                    <div class="license-preview">
                        ${data.license_copy ? 
                            (data.license_copy.toLowerCase().endsWith('.pdf') ? 
                                `<div class="no-file">
                                    <i data-feather="file" style="width: 48px; height: 48px;"></i>
                                    <span>PDF Document</span>
                                    <a href="/cargo-project/${data.license_copy}" target="_blank" class="btn btn-secondary btn-small">View PDF</a>
                                </div>` : 
                                `<img src="/cargo-project/${data.license_copy}" alt="License Copy">`
                            ) : 
                            `<div class="no-file">
                                <i data-feather="alert-circle" style="width: 48px; height: 48px;"></i>
                                <span>No license copy uploaded</span>
                            </div>`
                        }
                    </div>
                </section>

                <section class="section-card">
                    <h3><i data-feather="activity"></i> Recent Activity</h3>
                    ${requestsHtml}
                </section>
            </div>
        </div>
    `;
    
    feather.replace();
}

fetchDetails();
</script>

<!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->