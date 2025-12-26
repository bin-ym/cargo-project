<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <h2>Vehicles Management</h2>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i data-feather="plus"></i> Add Vehicle
            </button>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern" id="vehiclesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Plate Number</th>
                            <th>Vehicle Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="row-action">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Add/Edit Vehicle Modal -->
<div id="vehicleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Vehicle</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="vehicleForm">
            <input type="hidden" id="vehicle_id">
            <div class="form-group">
                <label for="plate_number">Plate Number</label>
                <input type="text" id="plate_number" required placeholder="e.g., ET-001-AA">
            </div>
            <div class="form-group">
                <label for="vehicle_type">Vehicle Type</label>
                <select id="vehicle_type" required>
                    <option value="">Select Type</option>
                    <option value="pickup">Pickup (Small)</option>
                    <option value="isuzu">Isuzu (Medium)</option>
                    <option value="trailer">Trailer (Large)</option>
                </select>
            </div>
            <div id="statusGroup" class="form-group" style="display: none;">
                <label for="status">Status</label>
                <select id="status">
                    <option value="available">Available</option>
                    <option value="in-use">In Use</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" id="submitBtn" class="btn btn-primary">Add Vehicle</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 500px;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
    margin: 0;
    color: #0f172a;
    font-size: 18px;
    font-weight: 600;
}

.modal-header .close {
    color: #64748b;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
    transition: color 0.2s;
}

.modal-header .close:hover {
    color: #0f172a;
}

.modal form {
    padding: 24px;
}

.modal .form-group {
    margin-bottom: 20px;
}

.modal .form-group label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-weight: 500;
    font-size: 14px;
}

.modal .form-group input,
.modal .form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    color: #0f172a;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.modal .form-group input:focus,
.modal .form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    margin-top: 20px;
}
</style>

<script>
const API_URL = '/cargo-project/backend/api/admin';
let allVehicles = [];

async function loadVehicles() {
    try {
        const response = await fetch(`${API_URL}/get_vehicles.php`);
        const result = await response.json();
        
        if (result.success) {
            allVehicles = result.data;
            renderTable(allVehicles);
        }
    } catch (error) {
        console.error('Error loading vehicles:', error);
    }
}

function renderTable(vehicles) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';
    
    if (vehicles.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">No vehicles found</td></tr>';
        return;
    }
    
    vehicles.forEach(v => {
        const statusClass = v.status === 'available' ? 'approved' : 
                           v.status === 'in-use' ? 'pending' : 'rejected';
        tbody.innerHTML += `
            <tr>
                <td>#${v.id}</td>
                <td><strong>${v.plate_number}</strong></td>
                <td>${capitalizeType(v.vehicle_type)}</td>
                <td><span class="badge ${statusClass}">${v.status}</span></td>
                <td>${new Date(v.created_at).toLocaleDateString()}</td>
                <td class="row-action">
                    <button class="btn-small btn-view" onclick="editVehicle(${v.id})" style="margin-right:5px;">
                        <i data-feather="edit-2" style="width:14px; height:14px;"></i>
                    </button>
                    <button class="btn-small btn-delete" onclick="deleteVehicle(${v.id})" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;">
                        <i data-feather="trash-2" style="width:14px; height:14px;"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    feather.replace();
}

function capitalizeType(type) {
    const types = {
        'pickup': 'Pickup (Small)',
        'isuzu': 'Isuzu (Medium)',
        'trailer': 'Trailer (Large)'
    };
    return types[type] || type;
}

function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Add New Vehicle';
    document.getElementById('submitBtn').innerText = 'Add Vehicle';
    document.getElementById('vehicle_id').value = '';
    document.getElementById('statusGroup').style.display = 'none';
    document.getElementById('vehicleForm').reset();
    document.getElementById('vehicleModal').style.display = 'flex';
}

function editVehicle(id) {
    const vehicle = allVehicles.find(v => v.id == id);
    if (!vehicle) return;

    document.getElementById('modalTitle').innerText = 'Edit Vehicle';
    document.getElementById('submitBtn').innerText = 'Update Vehicle';
    document.getElementById('vehicle_id').value = vehicle.id;
    document.getElementById('plate_number').value = vehicle.plate_number;
    document.getElementById('vehicle_type').value = vehicle.vehicle_type;
    document.getElementById('status').value = vehicle.status;
    document.getElementById('statusGroup').style.display = 'block';
    document.getElementById('vehicleModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('vehicleModal').style.display = 'none';
    document.getElementById('vehicleForm').reset();
}

document.getElementById('vehicleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('vehicle_id').value;
    const data = {
        plate_number: document.getElementById('plate_number').value,
        vehicle_type: document.getElementById('vehicle_type').value
    };

    let url = `${API_URL}/add_vehicle.php`;
    let method = 'POST';

    if (id) {
        data.id = id;
        data.status = document.getElementById('status').value;
        url = `${API_URL}/update_vehicle.php`;
        // We use POST for simplicity as the backend handles it, or we could use PUT if we want to be RESTful
    }
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(id ? 'Vehicle updated successfully!' : 'Vehicle added successfully!');
            closeModal();
            loadVehicles();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    }
});

async function deleteVehicle(id) {
    if (!confirm('Are you sure you want to delete this vehicle?')) return;
    
    try {
        const response = await fetch(`${API_URL}/delete_vehicle.php?id=${id}`);
        const result = await response.json();
        
        if (result.success) {
            alert('Vehicle deleted successfully!');
            loadVehicles();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred');
    }
}

loadVehicles();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
