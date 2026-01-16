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
            <h2>Vehicles Management</h2>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i data-feather="plus"></i> Add Vehicle
            </button>
        </header>

        <div class="content">
            <div class="table-wrapper">
                <table class="table-modern">
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
                            <td colspan="6" class="loading-cell">Loading…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal -->
<div id="vehicleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Vehicle</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>

        <form id="vehicleForm">
            <input type="hidden" id="vehicle_id">

            <div class="form-group">
                <label>Plate Number</label>
                <input type="text" id="plate_number" required>
            </div>

            <div class="form-group">
                <label>Vehicle Type</label>
                <select id="vehicle_type" required>
                    <option value="">Select</option>
                    <option value="pickup">Pickup (Small)</option>
                    <option value="isuzu">Isuzu (Medium)</option>
                    <option value="trailer">Trailer (Large)</option>
                </select>
            </div>

            <div class="form-group hidden" id="statusGroup">
                <label>Status</label>
                <select id="status">
                    <option value="available">Available</option>
                    <option value="in-use">In Use</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const API_URL = '/cargo-project/backend/api/admin';
    let vehicles = [];

    // ================= LOAD VEHICLES =================
    async function loadVehicles() {
        try {
            const res = await fetch(`${API_URL}/get_vehicles.php`);
            const json = await res.json();

            if (!json.success) {
                alert(json.error || 'Failed to load vehicles');
                return;
            }

            vehicles = json.data;
            renderTable();
        } catch (err) {
            console.error("Load error:", err);
        }
    }

    // ================= RENDER TABLE =================
    function renderTable() {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (!vehicles.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="empty-cell">No vehicles found</td></tr>`;
            return;
        }

        vehicles.forEach(v => {
            const badgeMap = {
                'available': 'approved',
                'in-use': 'pending',
                'maintenance': 'rejected'
            };

            tbody.innerHTML += `
                <tr>
                    <td>#${v.id}</td>
                    <td><strong>${v.plate_number}</strong></td>
                    <td>${formatType(v.vehicle_type)}</td>
                    <td><span class="badge ${badgeMap[v.status] || 'secondary'}">${v.status}</span></td>
                    <td>${new Date(v.created_at).toLocaleDateString()}</td>
                    <td class="row-action">
                        <button class="btn-small btn-view" onclick="editVehicle(${v.id})">Edit</button>
                        <button class="btn-small btn-delete" onclick="deleteVehicle(${v.id})">Delete</button>
                    </td>
                </tr>
            `;
        });

        feather.replace();
    }

    function formatType(type) {
        return {
            pickup: 'Pickup (Small)',
            isuzu: 'Isuzu (Medium)',
            trailer: 'Trailer (Large)'
        }[type] || type;
    }

    // ================= MODAL =================
    window.openAddModal = function () {
        document.getElementById('vehicleForm').reset();
        document.getElementById('vehicle_id').value = '';
        document.getElementById('statusGroup').classList.add('hidden');
        document.getElementById('modalTitle').innerText = 'Add Vehicle';
        document.getElementById('submitBtn').innerText = 'Add';
        document.getElementById('vehicleModal').style.display = 'flex';
    }

    window.editVehicle = function (id) {
        const v = vehicles.find(x => x.id == id);
        if (!v) return;

        document.getElementById('vehicle_id').value = v.id;
        document.getElementById('plate_number').value = v.plate_number;
        document.getElementById('vehicle_type').value = v.vehicle_type;
        document.getElementById('status').value = v.status;
        document.getElementById('statusGroup').classList.remove('hidden');

        document.getElementById('modalTitle').innerText = 'Edit Vehicle';
        document.getElementById('submitBtn').innerText = 'Update';
        document.getElementById('vehicleModal').style.display = 'flex';
    }

    window.closeModal = function () {
        document.getElementById('vehicleModal').style.display = 'none';
    }

    // ================= SAVE =================
    document.getElementById('vehicleForm').addEventListener('submit', async e => {
        e.preventDefault();

        const id = document.getElementById('vehicle_id').value;

        const payload = {
            id,
            plate_number: document.getElementById('plate_number').value.trim(),
            vehicle_type: document.getElementById('vehicle_type').value,
            status: document.getElementById('status').value
        };

        const url = id
            ? `${API_URL}/update_vehicle.php`
            : `${API_URL}/add_vehicle.php`;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(payload)
            });

            const json = await res.json();
            if (!json.success) {
                alert(json.error || 'Operation failed');
                return;
            }

            closeModal();
            loadVehicles();
        } catch (err) {
            console.error("Save error:", err);
        }
    });

    // ================= DELETE =================
    window.deleteVehicle = async function (id) {
        if (!confirm('Delete this vehicle?')) return;

        try {
            const res = await fetch(`${API_URL}/delete_vehicle.php`, {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ id })
            });

            const json = await res.json();
            if (json.success) loadVehicles();
            else alert(json.error || 'Delete failed');
        } catch (err) {
            console.error("Delete error:", err);
        }
    }

    // Close modal on backdrop click
    window.onclick = function(e) {
        if (e.target === document.getElementById('vehicleModal')) {
            closeModal();
        }
    }

    // INIT
    loadVehicles();
    feather.replace();
});
</script>

<!-- <?php require_once __DIR__ . '/../layout/footer_dashboard.php'; ?> -->
