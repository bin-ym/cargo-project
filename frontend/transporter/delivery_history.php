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
            <h2><?= __('delivery_history_title') ?></h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th><?= __('order_id') ?></th>
                            <th><?= __('customer') ?></th>
                            <th><?= __('pickup') ?></th>
                            <th><?= __('dropoff') ?></th>
                            <th><?= __('date') ?></th>
                            <th><?= __('status') ?></th>
                            <th class="row-action"><?= __('action') ?></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="7" class="text-center p-20 text-muted"><?= __('loading_history') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
const API_URL = '/cargo-project/backend/api/transporter/get_assignments.php';

async function fetchHistory() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        
        if (result.success) {
            // Filter only delivered deliveries
            const history = result.data.filter(r => r.shipment_status === 'delivered');
            renderTable(history);
        } else {
            showError("<?= __('failed_load_history') ?>");
        }
    } catch (error) {
        console.error('Error fetching history:', error);
        showError("<?= __('error_load_history') ?>");
    }
}

function renderTable(data) {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (data.length === 0) {
        body.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    <div class="text-muted">
                        <i data-feather="clock" class="empty-state-icon"></i>
                        <p class="empty-state-title"><?= __('no_delivery_history_found') ?></p>
                        <p class="empty-state-subtitle"><?= __('completed_deliveries_appear_here') ?></p>
                    </div>
                </td>
            </tr>`;
        feather.replace();
        return;
    }

    data.forEach(row => {
        const requestIdFormatted = `#CT-${String(row.id).padStart(4, '0')}`;
        
        body.innerHTML += `
        <tr>
            <td><strong>${requestIdFormatted}</strong></td>
            <td>${row.customer_name}</td>
            <td>${row.pickup_location}</td>
            <td>${row.dropoff_location}</td>
            <td>${row.pickup_date}</td>
            <td><span class="badge approved"><?= __('completed') ?></span></td>
            <td class="row-action">
                <a href="assignment_details.php?id=${row.id}" class="btn-small btn-view"><?= __('view_details') ?></a>
            </td>
        </tr>`;
    });
    
    feather.replace();
}

function showError(message) {
    const body = document.getElementById("tableBody");
    body.innerHTML = `
        <tr>
            <td colspan="7" class="text-center p-20 text-danger">
                ${message}
            </td>
        </tr>`;
}

fetchHistory();
feather.replace();
</script>
<?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?>
