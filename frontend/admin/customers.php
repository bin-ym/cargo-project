<?php 
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <h2><?= __('customers') ?></h2>
            <button class="btn btn-primary" onclick="openModal()">
                <i data-feather="plus"></i> <?= __('add_customer') ?>
            </button>
        </header>

        <div class="content">

            <div class="table-controls">
                <input type="text" id="searchInput" placeholder="<?= __('search_customers') ?>" class="search-box">
                <button id="exportCSV" class="btn btn-secondary"><?= __('export_csv') ?></button>
            </div>

            <div class="table-wrapper">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th><?= __('no') ?></th>
                            <th><?= __('full_name') ?></th>
                            <th><?= __('email') ?></th>
                            <th><?= __('phone') ?></th>
                            <th><?= __('city') ?></th>
                            <th class="row-action"><?= __('action') ?></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>

            <div class="pagination">
                <button class="page-btn" id="prevPage"><?= __('prev') ?></button>
                <span id="pageInfo"></span>
                <button class="page-btn" id="nextPage"><?= __('next') ?></button>
            </div>

        </div>
    </main>
</div>

<!-- Modal -->
<div id="customerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle"><?= __('add_customer') ?></h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="customerForm">
            <input type="hidden" id="customerId">
            
            <div class="form-group">
                <label><?= __('full_name') ?></label>
                <input type="text" id="name" required>
            </div>
            
            <div class="form-group">
                <label><?= __('email') ?></label>
                <input type="email" id="email" required>
            </div>

            <div class="form-group">
                <label><?= __('phone') ?></label>
                <input type="text" id="phone" required>
            </div>

            <div class="form-group">
                <label><?= __('address') ?></label>
                <input type="text" id="address" required>
            </div>

            <div class="form-group">
                <label><?= __('city') ?></label>
                <input type="text" id="city" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><?= __('save') ?></button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none; 
    position: fixed; 
    z-index: 2000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    background-color: rgba(0,0,0,0.5); 
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #fff;
    padding: 25px;
    border-radius: 16px;
    width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    color: #0f172a;
}

.close {
    font-size: 24px;
    cursor: pointer;
    color: #94a3b8;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 500;
    color: #64748b;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 25px;
}

/* Controls */
.table-controls {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.search-box {
    padding: 10px 15px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    width: 300px;
    font-size: 14px;
}

.pagination {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 20px;
}

.page-btn {
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.page-btn:hover {
    background: #f1f5f9;
}
</style>

<script>
let data = [];
let filteredData = [];
let rowsPerPage = 5;
let currentPage = 1;

// API URL
const API_URL = '/cargo-project/backend/api/customers/index.php';

// Fetch Customers
async function fetchCustomers() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();

        if (result.success) {
            data = result.data;
            filteredData = data;
            currentPage = 1;
            renderTable();
        } else {
            console.error("Load error:", result.error);
        }
    } catch (err) {
        console.error("Fetch error:", err);
    }
}

// Render Table
function renderTable() {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (filteredData.length === 0) {
        body.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:20px;"><?= __('no_customers_found') ?></td></tr>`;
        document.getElementById("pageInfo").textContent = "Page 0 of 0";
        return;
    }

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let paginated = filteredData.slice(start, end);

    paginated.forEach((row, index) => {
        body.innerHTML += `
            <tr>
                <td>${start + index + 1}</td>
                <td>${row.name}</td>
                <td>${row.email}</td>
                <td>${row.phone}</td>
                <td>${row.city}</td>
                <td class="row-action">
                    <button onclick="editCustomer(${row.id})" class="btn-small btn-view" style="margin-right:5px;"><?= __('edit') ?></button>
                    <button onclick="deleteCustomer(${row.id})" class="btn-small" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;"><?= __('delete') ?></button>
                </td>
            </tr>
        `;
    });

    document.getElementById("pageInfo").textContent =
        `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage)}`;
}

// ========== MODAL LOGIC ==========
const modal = document.getElementById("customerModal");
const form = document.getElementById("customerForm");

function openModal() {
    document.getElementById("modalTitle").innerText = "<?= __('add_customer') ?>";
    document.getElementById("customerId").value = "";
    form.reset();
    modal.style.display = "flex";
}

function closeModal() {
    modal.style.display = "none";
}

function editCustomer(id) {
    const item = data.find(d => d.id == id);
    if (!item) return;

    document.getElementById("modalTitle").innerText = "<?= __('edit_customer') ?>";
    document.getElementById("customerId").value = item.id;
    document.getElementById("name").value = item.name;
    document.getElementById("email").value = item.email;
    document.getElementById("phone").value = item.phone;
    document.getElementById("address").value = item.address;
    document.getElementById("city").value = item.city;

    modal.style.display = "flex";
}

// Delete
async function deleteCustomer(id) {
    if (!confirm("<?= __('delete_confirm') ?>")) return;

    try {
        const response = await fetch(`${API_URL}?id=${id}`, { method: "DELETE" });
        const result = await response.json();

        if (result.success) {
            fetchCustomers();
        } else {
            alert("Error: " + result.error);
        }
    } catch (err) {
        console.error("Delete error:", err);
    }
}

// Save (Add/Update)
form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = document.getElementById("customerId").value;
    const payload = {
        name: document.getElementById("name").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value,
        address: document.getElementById("address").value,
        city: document.getElementById("city").value,
    };

    const method = id ? "PUT" : "POST";
    const url = id ? `${API_URL}?id=${id}` : API_URL;

    try {
        const resp = await fetch(url, {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await resp.json();

        if (result.success) {
            closeModal();
            fetchCustomers();
        } else {
            alert("Error: " + result.error);
        }
    } catch (err) {
        console.error("Save error:", err);
    }
});

// SEARCH
document.getElementById("searchInput").addEventListener("input", (e) => {
    const keyword = e.target.value.toLowerCase();

    filteredData = data.filter(d =>
        d.name.toLowerCase().includes(keyword) ||
        d.email.toLowerCase().includes(keyword) ||
        d.phone.includes(keyword) ||
        d.city.toLowerCase().includes(keyword)
    );

    currentPage = 1;
    renderTable();
});

// Pagination
document.getElementById("prevPage").onclick = () => {
    if (currentPage > 1) {
        currentPage--;  
        renderTable();
    }
};

document.getElementById("nextPage").onclick = () => {
    if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) {
        currentPage++;
        renderTable();
    }
};

// CSV Export
document.getElementById("exportCSV").onclick = () => {
    let csv = "ID,Name,Email,Phone,City\n";
    filteredData.forEach(r => {
        csv += `${r.id},${r.name},${r.email},${r.phone},${r.city}\n`;
    });

    const a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob([csv], { type: "text/csv" }));
    a.download = "customers.csv";
    a.click();
};

// Load on start
fetchCustomers();
feather.replace();
</script>

