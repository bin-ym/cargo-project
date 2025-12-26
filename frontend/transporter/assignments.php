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
            <h2>My Assignments</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="row-action">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
async function fetchAssignments() {
    try {
        const response = await fetch('/cargo-project/backend/api/transporter/get_assignments.php');
        const result = await response.json();
        
        if (result.success) {
            renderTable(result.data);
        } else {
            console.error(result.error);
        }
    } catch (error) {
        console.error('Error fetching assignments:', error);
    }
}

function renderTable(data) {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (data.length === 0) {
        body.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 20px;">No assignments found</td></tr>`;
        return;
    }

    data.forEach(row => {
        let status = row.shipment_status || 'assigned';
        let badgeClass = status === 'delivered' ? 'approved' : (status === 'in-transit' ? 'in-transit' : 'pending');
        
        body.innerHTML += `
        <tr>
            <td>#CT-${String(row.id).padStart(4, '0')}</td>
            <td>${row.customer_name}<br><small style="color:#64748b">${row.phone}</small></td>
            <td>${row.pickup_location}</td>
            <td>${row.dropoff_location}</td>
            <td>${row.pickup_date}</td>
            <td><span class="badge ${badgeClass}">${status.replace('-', ' ')}</span></td>
            <td class="row-action">
                <a href="assignment_details.php?id=${row.id}" class="btn-small btn-view">View Details</a>
            </td>
        </tr>`;
    });
}

fetchAssignments();
feather.replace();
</script>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
