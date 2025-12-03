<?php 
session_start();
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';

$requestId = $_GET['id'] ?? 0;
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <h2>Items for Request #<?= htmlspecialchars($requestId) ?></h2>
            <button class="btn btn-primary" onclick="openModal()">
                <i data-feather="plus"></i> Add Item
            </button>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Weight</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="row-action">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="requests.php" class="btn btn-secondary">Back to Requests</a>
            </div>
        </div>
    </main>
</div>

<!-- Modal -->
<div id="itemModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Item</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="itemForm">
            <input type="hidden" id="itemId">
            <input type="hidden" id="requestId" value="<?= $requestId ?>">
            
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" id="item_name" required>
            </div>
            
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" id="quantity" value="1" required>
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
</style>

<script>
let data = [];
const requestId = <?= $requestId ?>;
const API_URL = '/cargo-project/backend/api/cargo_items/index.php';

async function fetchItems() {
    try {
        const response = await fetch(`${API_URL}?request_id=${requestId}`);
        const result = await response.json();
        if (result.success) {
            data = result.data;
            renderTable();
        }
    } catch (error) {
        console.error('Error fetching items:', error);
    }
}

function renderTable() {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (data.length === 0) {
        body.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 20px;">No items found for this request</td></tr>`;
        return;
    }

    data.forEach(row => {
        body.innerHTML += `
        <tr>
            <td>#${row.id}</td>
            <td>${row.item_name}</td>
            <td>${row.quantity}</td>
            <td>${row.weight}</td>
            <td>${row.category}</td>
            <td>${row.description || '-'}</td>
            <td class="row-action">
                <button onclick="editItem(${row.id})" class="btn-small btn-view" style="margin-right:5px;">Edit</button>
                <button onclick="deleteItem(${row.id})" class="btn-small" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;">Delete</button>
            </td>
        </tr>`;
    });
}

// Modal Logic
const modal = document.getElementById("itemModal");
const form = document.getElementById("itemForm");

function openModal() {
    document.getElementById("modalTitle").innerText = "Add Item";
    document.getElementById("itemId").value = "";
    form.reset();
    document.getElementById("requestId").value = requestId;
    modal.style.display = "flex";
}

function closeModal() { modal.style.display = "none"; }

function editItem(id) {
    const item = data.find(d => d.id == id);
    if (item) {
        document.getElementById("modalTitle").innerText = "Edit Item";
        document.getElementById("itemId").value = item.id;
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
        request_id: requestId,
        item_name: document.getElementById("item_name").value,
        quantity: document.getElementById("quantity").value,
        weight: document.getElementById("weight").value,
        category: document.getElementById("category").value,
        description: document.getElementById("description").value,
    };

    const method = id ? 'PUT' : 'POST';
    const url = id ? `${API_URL}?id=${id}` : API_URL;

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.success) { closeModal(); fetchItems(); }
        else alert("Error: " + result.error);
    } catch (error) { console.error("Error saving:", error); }
});

fetchItems();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
