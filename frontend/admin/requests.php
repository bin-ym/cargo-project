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
            <h2>All Requests</h2>
        </header>

        <div class="content">
            <!-- Status Cards -->
            <div class="status-cards">
                <div class="card card-total">
                    <h3>Total Requests</h3>
                    <p id="count-total">0</p>
                </div>
                <div class="card card-pending">
                    <h3>Pending</h3>
                    <p id="count-pending">0</p>
                </div>
                <div class="card card-approved">
                    <h3>Approved</h3>
                    <p id="count-approved">0</p>
                </div>
                <div class="card card-rejected">
                    <h3>Rejected</h3>
                    <p id="count-rejected">0</p>
                </div>
                <div class="card card-completed">
                    <h3>Completed</h3>
                    <p id="count-completed">0</p>
                </div>
            </div>

            <!-- Tabs & Search -->
            <div class="table-controls">
                <div class="tabs">
                    <button class="tab-btn active" onclick="filterByTab('all')">All</button>
                    <button class="tab-btn" onclick="filterByTab('pending')">Pending</button>
                    <button class="tab-btn" onclick="filterByTab('approved')">Approved</button>
                    <button class="tab-btn" onclick="filterByTab('rejected')">Rejected</button>
                    <button class="tab-btn" onclick="filterByTab('completed')">Completed</button>
                </div>
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
/* Status Cards */
.status-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
.card h3 { font-size: 14px; color: #64748b; margin-bottom: 10px; font-weight: 500; }
.card p { font-size: 28px; font-weight: 700; color: #0f172a; margin: 0; }
.card-total { border-left: 4px solid #3b82f6; }
.card-pending { border-left: 4px solid #f59e0b; }
.card-approved { border-left: 4px solid #10b981; }
.card-rejected { border-left: 4px solid #ef4444; }
.card-completed { border-left: 4px solid #6366f1; }

/* Tabs */
.table-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.tabs { display: flex; gap: 5px; background: #f1f5f9; padding: 5px; border-radius: 8px; }
.tab-btn { padding: 8px 16px; border: none; background: transparent; border-radius: 6px; cursor: pointer; font-size: 14px; color: #64748b; font-weight: 500; transition: all 0.2s; }
.tab-btn.active { background: #fff; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.tab-btn:hover:not(.active) { background: #e2e8f0; }

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
let currentTab = 'all';

const API_URL = '/cargo-project/backend/api/requests/index.php';

async function fetchRequests() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        if (result.success) {
            data = result.data;
            updateStats();
            filterByTab(currentTab); // Initial render
        }
    } catch (error) {
        console.error('Error fetching requests:', error);
    }
}

function updateStats() {
    const counts = { total: data.length, pending: 0, approved: 0, rejected: 0, completed: 0 };
    data.forEach(d => {
        if (counts[d.status] !== undefined) counts[d.status]++;
    });

    document.getElementById('count-total').innerText = counts.total;
    document.getElementById('count-pending').innerText = counts.pending;
    document.getElementById('count-approved').innerText = counts.approved;
    document.getElementById('count-rejected').innerText = counts.rejected;
    document.getElementById('count-completed').innerText = counts.completed;
}

function filterByTab(tab) {
    currentTab = tab;
    
    // Update active tab UI
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.innerText.toLowerCase() === tab || (tab === 'all' && btn.innerText === 'All')) {
            btn.classList.add('active');
        }
    });

    // Filter data
    if (tab === 'all') {
        filteredData = data;
    } else {
        filteredData = data.filter(d => d.status === tab);
    }
    
    // Apply search if exists
    const keyword = document.getElementById("searchInput").value.toLowerCase();
    if (keyword) {
        filteredData = filteredData.filter(d => 
            d.customer_name.toLowerCase().includes(keyword) || 
            d.pickup_location.toLowerCase().includes(keyword) ||
            d.dropoff_location.toLowerCase().includes(keyword)
        );
    }

    currentPage = 1;
    renderTable();
}

function renderTable() {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (filteredData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 20px;">No requests found</td></tr>`;
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
                <a href="order_items.php?id=${row.id}" class="btn-small btn-view" style="margin-right: 5px;">View</a>
            </td>
        </tr>`;
    });

    document.getElementById("pageInfo").textContent = `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage) || 1}`;
}

document.getElementById("searchInput").addEventListener("input", () => filterByTab(currentTab));
document.getElementById("prevPage").onclick = () => { if (currentPage > 1) { currentPage--; renderTable(); } };
document.getElementById("nextPage").onclick = () => { if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) { currentPage++; renderTable(); } };

fetchRequests();
feather.replace();
</script>

<!-- Assign Modal -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Assign Transporter</h3>
            <span class="close" onclick="closeAssignModal()">&times;</span>
        </div>
        <form id="assignForm">
            <input type="hidden" id="assignRequestId">
            <div class="form-group">
                <label>Select Transporter</label>
                <select id="transporterSelect" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <option value="">Select Transporter</option>
                </select>
            </div>
            <div class="modal-footer" style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeAssignModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
.modal-content { background-color: #fff; padding: 25px; border-radius: 16px; width: 400px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.close { font-size: 24px; cursor: pointer; }
</style>

<!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->