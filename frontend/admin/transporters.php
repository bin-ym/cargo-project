<?php 
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <h2><?= __('transporters') ?></h2>
            <button class="btn btn-primary" onclick="openModal()">
                <i data-feather="plus"></i> <?= __('add_transporter') ?>
            </button>
        </header>

        <div class="content">

            <div class="table-controls">
                <input type="text" id="searchInput" placeholder="<?= __('search_transporters') ?>" class="search-box">
                
                <select id="statusFilter" class="filter-select">
                    <option value=""><?= __('all_status') ?></option>
                    <option value="approved"><?= __('active') ?></option>
                    <option value="suspended"><?= __('suspended') ?></option>
                    <option value="pending"><?= __('pending') ?></option>
                </select>

                <button id="exportCSV" class="btn btn-secondary"><?= __('export_csv') ?></button>
            </div>

            <div class="table-wrapper">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th><?= __('no') ?></th>
                            <th><?= __('company_name') ?></th>
                            <th><?= __('email') ?></th>
                            <th><?= __('phone') ?></th>
                            <th><?= __('status') ?></th>
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
<div id="transporterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle"><?= __('add_transporter') ?></h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="transporterForm">
            <input type="hidden" id="transporterId">
            
            <div class="form-group">
                <label><?= __('full_name_company') ?></label>
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
                <label><?= __('username') ?></label>
                <input type="text" id="username" required>
            </div>

            <div id="passwordFields">
                <div class="form-group">
                    <label><?= __('password') ?></label>
                    <input type="password" id="password">
                </div>
                <div class="form-group">
                    <label><?= __('confirm_password') ?></label>
                    <input type="password" id="confirmPassword">
                </div>
            </div>

            <div class="form-group">
                <label><?= __('license_copy') ?></label>
                <input type="file" id="license_copy" accept="image/*,.pdf">
            </div>

            <div class="form-group">
                <label><?= __('status') ?></label>
                <select id="status">
                    <option value="pending"><?= __('pending') ?></option>
                    <option value="approved"><?= __('approved') ?></option>
                    <option value="suspended"><?= __('suspended') ?></option>
                </select>
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

.filter-select {
    padding: 10px 15px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: #fff;
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
let rowsPerPage = 5;
let currentPage = 1;
let filteredData = [];

// API URL
const API_URL = '/cargo-project/backend/api/transporters/index.php';

async function fetchTransporters() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        
        if (result.success) {
            data = result.data;
            filteredData = data;
            renderTable();
        } else {
            console.error('Failed to load transporters:', result.error);
        }
    } catch (error) {
        console.error('Error fetching transporters:', error);
    }
}

function renderTable() {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (filteredData.length === 0) {
        body.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 20px;"><?= __('no_transporters_found') ?></td></tr>`;
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
            <td><span class="badge ${row.status}">${row.status}</span></td>
            <td class="row-action">
                <a href="transporter_view.php?id=${row.id}" class="btn-small btn-view" style="margin-right:5px;">
                    <i data-feather="eye"></i> <?= __('view') ?>
                </a>
                <button onclick="editTransporter(${row.id})" class="btn-small btn-view" style="margin-right:5px;"><?= __('edit') ?></button>
                <button onclick="deleteTransporter(${row.id})" class="btn-small" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;"><?= __('delete') ?></button>
            </td>
        </tr>`;
    });

    document.getElementById("pageInfo").textContent =
        `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage) || 1}`;
}

// MODAL LOGIC
const modal = document.getElementById("transporterModal");
const form = document.getElementById("transporterForm");

function openModal() {
    document.getElementById("modalTitle").innerText = "<?= __('add_transporter') ?>";
    document.getElementById("transporterId").value = "";
    document.getElementById("passwordFields").style.display = "block";
    document.getElementById("password").required = true;
    document.getElementById("confirmPassword").required = true;
    document.getElementById("username").disabled = false;
    form.reset();
    modal.style.display = "flex";
}

function closeModal() {
    modal.style.display = "none";
}

function editTransporter(id) {
    const item = data.find(d => d.id == id);
    if (item) {
        document.getElementById("modalTitle").innerText = "<?= __('edit_transporter') ?>";
        document.getElementById("transporterId").value = item.id;
        document.getElementById("name").value = item.name;
        document.getElementById("email").value = item.email;
        document.getElementById("phone").value = item.phone;
        document.getElementById("status").value = item.status;
        
        // Hide password fields when editing
        document.getElementById("passwordFields").style.display = "none";
        document.getElementById("password").required = false;
        document.getElementById("confirmPassword").required = false;
        
        // Disable username editing if it exists in the data (need to check if API returns it)
        if (item.username) {
            document.getElementById("username").value = item.username;
            document.getElementById("username").disabled = true;
        } else {
            document.getElementById("username").value = "";
            document.getElementById("username").disabled = false;
        }

        modal.style.display = "flex";
    }
}

async function deleteTransporter(id) {
    if (confirm("<?= __('delete_transporter_confirm') ?>")) {
        try {
            const response = await fetch(`${API_URL}?id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            if (result.success) {
                fetchTransporters();
            } else {
                alert("Error: " + result.error);
            }
        } catch (error) {
            console.error("Error deleting:", error);
        }
    }
}

form.addEventListener("submit", async (e) => {
    e.preventDefault();
    
    const id = document.getElementById("transporterId").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (!id && password !== confirmPassword) {
        alert("<?= __('passwords_dont_match') ?>");
        return;
    }

    const formData = new FormData();
    formData.append('name', document.getElementById("name").value);
    formData.append('email', document.getElementById("email").value);
    formData.append('phone', document.getElementById("phone").value);
    formData.append('username', document.getElementById("username").value);
    formData.append('status', document.getElementById("status").value);

    if (!id) {
        formData.append('password', password);
    }

    const fileInput = document.getElementById("license_copy");
    if (fileInput.files.length > 0) {
        formData.append('license_copy', fileInput.files[0]);
    }

    const method = id ? 'POST' : 'POST'; // We'll use POST for both to support file uploads easily in PHP
    let url = API_URL;
    if (id) {
        url += `?id=${id}&_method=PUT`; // Method spoofing if needed, or just handle in controller
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        if (result.success) {
            closeModal();
            fetchTransporters();
            alert(id ? "<?= __('transporter_updated') ?>" : "<?= __('transporter_created') ?>");
        } else {
            alert("Error: " + result.error);
        }
    } catch (error) {
        console.error("Error saving:", error);
        alert("An error occurred while saving.");
    }
});

// SEARCH
document.getElementById("searchInput").addEventListener("input", () => {
    let keyword = searchInput.value.toLowerCase();
    filteredData = data.filter(d =>
        d.name.toLowerCase().includes(keyword) ||
        d.email.toLowerCase().includes(keyword) ||
        d.phone.includes(keyword)
    );
    currentPage = 1;
    renderTable();
});

// FILTER
document.getElementById("statusFilter").addEventListener("change", () => {
    let val = statusFilter.value;
    filteredData = val ? data.filter(d => d.status === val) : data;
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

// CSV export
document.getElementById("exportCSV").onclick = () => {
    let csv = "ID,Name,Email,Phone,Status\n";
    filteredData.forEach(r => {
        csv += `${r.id},${r.name},${r.email},${r.phone},${r.status}\n`;
    });

    let a = document.createElement("a");
    a.href = URL.createObjectURL(new Blob([csv], { type: "text/csv" }));
    a.download = "transporters.csv";
    a.click();
};

// Initial load
fetchTransporters();
feather.replace();
</script>

<!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->