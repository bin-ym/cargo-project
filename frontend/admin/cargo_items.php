<?php 
session_start();
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <h2>All Cargo Items</h2>
            <!-- Optional: Add Item button here if we want to allow adding items without context of a request -->
        </header>

        <div class="content">
            <div class="table-controls">
                <input type="text" id="searchInput" placeholder="Search items..." class="search-box">
            </div>

            <div class="table-wrapper">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Request ID</th>
                            <th>Weight</th>
                            <th>Category</th>
                            <th>Description</th>
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

<!-- Modal -->
<div id="itemModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Edit Item</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="itemForm">
            <input type="hidden" id="itemId">
            <input type="hidden" id="requestId"> <!-- Hidden request ID -->
            
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" id="item_name" required>
            </div>
            
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" id="quantity" required>
            </div>

            <div class="form-group">
                <label>Weight</label>
                <input type="text" id="weight" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" id="category" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea id="description" rows="3" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
.modal-content { background-color: #fff; padding: 25px; border-radius: 16px; width: 500px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h3 { margin: 0; font-size: 18px; color: #0f172a; }
.close { font-size: 24px; cursor: pointer; color: #94a3b8; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-size: 14px; font-weight: 500; color: #64748b; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
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

const API_URL = '/cargo-project/backend/api/cargo_items/index.php';

async function fetchItems() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        if (result.success) {
            data = result.data;
            filteredData = data;
            renderTable();
        }
    } catch (error) {
        console.error('Error fetching items:', error);
    }
}

function renderTable() {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (filteredData.length === 0) {
        body.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 20px;">No items found</td></tr>`;
        document.getElementById("pageInfo").textContent = "Page 0 of 0";
        return;
    }

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;
    let paginated = filteredData.slice(start, end);

    paginated.forEach(row => {
        body.innerHTML += `
        <tr>
            <td>#${row.id}</td>
            <td>${row.item_name}</td>
            <td><a href="order_items.php?id=${row.request_id}">#${row.request_id}</a></td>
            <td>${row.weight}</td>
            <td>${row.category}</td>
            <td>${row.description || '-'}</td>
            <td class="row-action">
                <button onclick="editItem(${row.id})" class="btn-small btn-view" style="margin-right:5px;">Edit</button>
                <button onclick="deleteItem(${row.id})" class="btn-small" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;">Delete</button>
            </td>
        </tr>`;
    });

    document.getElementById("pageInfo").textContent = `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage) || 1}`;
}

// Modal Logic
const modal = document.getElementById("itemModal");
const form = document.getElementById("itemForm");

function closeModal() { modal.style.display = "none"; }

function editItem(id) {
    const item = data.find(d => d.id == id);
    if (item) {
        document.getElementById("modalTitle").innerText = "Edit Item";
        document.getElementById("itemId").value = item.id;
        document.getElementById("requestId").value = item.request_id;
        document.getElementById("item_name").value = item.item_name;
        document.getElementById("quantity").value = item.quantity;
        document.getElementById("weight").value = item.weight;
        document.getElementById("category").value = item.category;
        document.getElementById("description").value = item.description;
        modal.style.display = "flex";
    }
}

async function deleteItem(id) {
    if (confirm("Are you sure you want to delete this item?")) {
        try {
            const response = await fetch(`${API_URL}?id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            if (result.success) fetchItems();
            else alert("Error: " + result.error);
        } catch (error) { console.error("Error deleting:", error); }
    }
}

form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = document.getElementById("itemId").value;
    const payload = {
        item_name: document.getElementById("item_name").value,
        quantity: document.getElementById("quantity").value,
        weight: document.getElementById("weight").value,
        category: document.getElementById("category").value,
        description: document.getElementById("description").value,
    };

    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.success) { closeModal(); fetchItems(); }
        else alert("Error: " + result.error);
    } catch (error) { console.error("Error saving:", error); }
});

document.getElementById("searchInput").addEventListener("input", (e) => {
    let keyword = e.target.value.toLowerCase();
    filteredData = data.filter(d => d.item_name.toLowerCase().includes(keyword) || d.category.toLowerCase().includes(keyword));
    currentPage = 1;
    renderTable();
});

document.getElementById("prevPage").onclick = () => { if (currentPage > 1) { currentPage--; renderTable(); } };
document.getElementById("nextPage").onclick = () => { if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) { currentPage++; renderTable(); } };

fetchItems();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
