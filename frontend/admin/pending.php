<?php 
session_start();
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <h2>Pending Requests</h2>
        </header>

        <div class="content">
            <div class="table-controls">
                <input type="text" id="searchInput" placeholder="Search requests..." class="search-box">
            </div>

            <div class="table-wrapper">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
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

            <div class="pagination">
                <button class="page-btn" id="prevPage">Prev</button>
                <span id="pageInfo"></span>
                <button class="page-btn" id="nextPage">Next</button>
            </div>
            
        </div>
    </main>
</div>

<style>
.table-controls { display: flex; gap: 15px; margin-bottom: 20px; }
.search-box { padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; width: 300px; font-size: 14px; }
.pagination { display: flex; align-items: center; justify-content: flex-end; gap: 15px; margin-top: 20px; }
.page-btn { padding: 8px 16px; border: 1px solid #e2e8f0; background: #fff; border-radius: 6px; cursor: pointer; }
.page-btn:hover { background: #f1f5f9; }
</style>

<script>
let data = [];
let rowsPerPage = 10;
let currentPage = 1;
let filteredData = [];

const API_URL = '/cargo-project/backend/api/requests/index.php';

async function fetchRequests() {
    try {
        const response = await fetch(`${API_URL}?status=pending`);
        const result = await response.json();
        if (result.success) {
            data = result.data;
            filteredData = data;
            renderTable();
        }
    } catch (error) {
        console.error('Error fetching requests:', error);
    }
}

function renderTable() {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (filteredData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 20px;">No pending requests found</td></tr>`;
        document.getElementById("pageInfo").textContent = "Page 0 of 0";
        return;
    }

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let paginated = filteredData.slice(start, end);

    paginated.forEach(row => {
        body.innerHTML += `
        <tr>
            <td>#CT-${String(row.id).padStart(4, '0')}</td>
            <td>${row.customer_name}<br><small style="color:#64748b">${row.phone}</small></td>
            <td>${row.pickup_location}</td>
            <td>${row.dropoff_location}</td>
            <td>${row.pickup_date}</td>
            <td><span class="badge ${row.status}">${row.status}</span></td>
            <td class="row-action">
            <td class="row-action">
                <a href="order_items.php?id=${row.eid}" class="btn-small btn-view">View Details</a>
            </td>
        </tr>`;
    });

    document.getElementById("pageInfo").textContent = `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage) || 1}`;
}

async function updateStatus(id, status) {
    if (confirm(`Are you sure you want to ${status} this request?`)) {
        try {
            const response = await fetch(`${API_URL}?id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: status })
            });
            const result = await response.json();
            if (result.success) fetchRequests();
            else showError("<?= __('error_label') ?>: " + result.error);
        } catch (error) { console.error("Error updating status:", error); }
    }
}

document.getElementById("searchInput").addEventListener("input", (e) => {
    let keyword = e.target.value.toLowerCase();
    filteredData = data.filter(d => 
        d.customer_name.toLowerCase().includes(keyword) || 
        d.pickup_location.toLowerCase().includes(keyword) ||
        d.dropoff_location.toLowerCase().includes(keyword)
    );
    currentPage = 1;
    renderTable();
});

document.getElementById("prevPage").onclick = () => { if (currentPage > 1) { currentPage--; renderTable(); } };
document.getElementById("nextPage").onclick = () => { if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) { currentPage++; renderTable(); } };

fetchRequests();
feather.replace();
</script>


