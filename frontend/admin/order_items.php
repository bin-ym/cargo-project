<?php 
session_start();
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';

$requestId = $_GET['id'] ?? 0;
?>

<div class="dashboard">
    <main class="main-content">
        <header class="topbar">
            <h2>Items for Request #CT-<?= str_pad($requestId, 4, '0', STR_PAD_LEFT) ?></h2>
            <div style="display: flex; gap: 10px;">
                <button class="btn" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;" onclick="openAssignModal()">Approve & Assign</button>
                <button class="btn" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;" onclick="openRejectModal()">Reject</button>
            </div>
        </header>

        <div class="content">
            <!-- Request Details Section -->
            <div class="details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="card">
                    <h3>Customer Details</h3>
                    <p><strong>Name:</strong> <span id="custName">-</span></p>
                    <p><strong>Phone:</strong> <span id="custPhone">-</span></p>
                    <p><strong>Email:</strong> <span id="custEmail">-</span></p>
                </div>
                <div class="card">
                    <h3>Request Details</h3>
                    <p><strong>Pickup:</strong> <span id="reqPickup">-</span></p>
                    <p><strong>Dropoff:</strong> <span id="reqDropoff">-</span></p>
                    <p><strong>Date:</strong> <span id="reqDate">-</span></p>
                    <p><strong>Vehicle Type:</strong> <span id="reqVehicleType">-</span></p>
                    <p><strong>Total Amount:</strong> <span id="reqPrice" style="color: #16a34a; font-weight: 700;">-</span></p>
                    <p><strong>Status:</strong> <span id="reqStatus" class="badge">-</span></p>
                </div>
            </div>

            <!-- Status Specific Info -->
            <div id="statusInfo" style="margin-bottom: 30px; display: none;">
                <div class="card" style="border-left: 4px solid #3b82f6;">
                    <h3 id="statusTitle">Info</h3>
                    <div id="statusContent"></div>
                </div>
            </div>

            <div class="table-wrapper">
                <h3>Cargo Items</h3>
                <table class="table-modern" id="dataTable" style="margin-top: 15px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Weight</th>
                            <th>Category</th>
                            <th>Description</th>
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

<!-- Assign Modal -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Approve & Assign Transporter</h3>
            <span class="close" onclick="closeAssignModal()">&times;</span>
        </div>
        <form id="assignForm">
            <div style="background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 13px; color: #64748b;">Requested Vehicle Type:</p>
                <p id="requestedVehicleTypeDisplay" style="margin: 5px 0 0; font-size: 16px; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="truck" style="width: 18px;"></i>
                    <span>-</span>
                </p>
            </div>
            <div class="form-group">
                <label for="transporterSelect">
                    <i data-feather="user" style="width: 16px;"></i>
                    Select Transporter
                </label>
                <select id="transporterSelect" required>
                    <option value="">Select Transporter</option>
                </select>
            </div>
            <div class="form-group">
                <label for="vehicleSelect">
                    <i data-feather="truck" style="width: 16px;"></i>
                    Select Vehicle
                </label>
                <select id="vehicleSelect" required>
                    <option value="">Select Vehicle</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAssignModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Approve & Assign</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Request</h3>
            <span class="close" onclick="closeRejectModal()">&times;</span>
        </div>
        <form id="rejectForm">
            <div class="form-group">
                <label for="rejectReason">Rejection Reason</label>
                <textarea id="rejectReason" rows="4" placeholder="Please provide a reason for rejection..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn" style="background:#ef4444; color:white;">Reject</button>
            </div>
        </form>
    </div>
</div>

<style>
.details-grid .card { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }
.details-grid h3 { margin-bottom: 15px; color: #64748b; font-size: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }
.details-grid p { margin-bottom: 8px; color: #0f172a; font-size: 14px; }
.details-grid strong { color: #64748b; width: 80px; display: inline-block; }

/* Modal Styles */
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
    backdrop-filter: blur(4px);
}

.modal-content {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal .form-group select,
.modal .form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    color: #0f172a;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.modal .form-group select:focus,
.modal .form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal .form-group textarea {
    resize: vertical;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    margin-top: 20px;
}

.btn-small {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-small:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>

<script>
let data = [];
const requestId = <?= $requestId ?>;
const API_URL = '/cargo-project/backend/api/requests/index.php'; // Use requests API to get full details

function capitalizeVehicleType(type) {
    const types = {
        'pickup': 'Pickup (Small)',
        'isuzu': 'Isuzu (Medium)',
        'trailer': 'Trailer (Large)'
    };
    return types[type] || type;
}

async function fetchDetails() {
    try {
        const response = await fetch(`${API_URL}?id=${requestId}`);
        const result = await response.json();
        if (result.success) {
            const r = result.data;
            
            
            // Render Customer & Request Details
            document.getElementById('custName').innerText = r.customer_name;
            document.getElementById('custPhone').innerText = r.phone;
            document.getElementById('custEmail').innerText = r.email;
            
            document.getElementById('reqPickup').innerText = r.pickup_location;
            document.getElementById('reqDropoff').innerText = r.dropoff_location;
            document.getElementById('reqDate').innerText = r.pickup_date;
            document.getElementById('reqVehicleType').innerText = capitalizeVehicleType(r.vehicle_type || 'N/A');
            document.getElementById('reqPrice').innerText = (r.price ? parseFloat(r.price).toFixed(2) + ' ETB' : 'N/A');
            document.getElementById('reqStatus').innerText = r.status;
            document.getElementById('reqStatus').className = `badge ${r.status}`;
            
            // Store vehicle type for assignment modal
            currentRequestVehicleType = r.vehicle_type;
            document.querySelector('#requestedVehicleTypeDisplay span').innerText = capitalizeVehicleType(r.vehicle_type);

            // Render Items
            renderItems(r.items || []);

            // Render Status Info & Actions
            renderStatusInfo(r);
        } else {
            alert("Request not found or not paid.");
            window.location.href = 'requests.php';
        }
    } catch (error) {
        console.error('Error fetching details:', error);
    }
}

function renderItems(items) {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (items.length === 0) {
        body.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 20px;">No items found for this request</td></tr>`;
        return;
    }

    items.forEach(row => {
        body.innerHTML += `
        <tr>
            <td>#${row.id}</td>
            <td>${row.item_name}</td>
            <td>${row.quantity}</td>
            <td>${row.weight}</td>
            <td>${row.category}</td>
            <td>${row.description || '-'}</td>
        </tr>`;
    });
}

function renderStatusInfo(r) {
    const statusInfo = document.getElementById('statusInfo');
    const statusContent = document.getElementById('statusContent');
    const statusTitle = document.getElementById('statusTitle');
    const actionButtons = document.querySelector('.topbar div'); // The div containing buttons

    // Hide buttons by default
    if(actionButtons) actionButtons.style.display = 'none';
    statusInfo.style.display = 'none';

    if (r.status === 'pending') {
        // Show Action Buttons
        if(actionButtons) actionButtons.style.display = 'flex';
    } else if (r.status === 'approved' || r.status === 'completed') {
        statusInfo.style.display = 'block';
        statusTitle.innerText = "Transporter Assignment";
        
        // Determine badge style based on shipment status
        let badgeClass = 'pending';
        if (r.shipment_status === 'delivered') badgeClass = 'approved';
        else if (r.shipment_status === 'in-transit') badgeClass = 'pending';
        
        // Format status display
        let statusDisplay = r.shipment_status || 'assigned';
        if (statusDisplay === 'in-transit') statusDisplay = 'In Transit';
        else statusDisplay = statusDisplay.charAt(0).toUpperCase() + statusDisplay.slice(1);
        
        let content = `
            <p><strong>Transporter:</strong> ${r.transporter_name || 'N/A'}</p>
            <p><strong>Shipment Status:</strong> <span class="badge ${badgeClass}">${statusDisplay}</span></p>
        `;

        if (r.shipment_status === 'delivered' && r.delivered_at) {
            content += `<p><strong>Delivered At:</strong> ${r.delivered_at}</p>`;
        }

        // Only allow changing transporter if shipment hasn't started yet
        if (r.shipment_status === 'assigned') {
            content += `<button onclick="openAssignModal()" class="btn-small" style="margin-top: 10px; background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd;">Change Transporter</button>`;
        } else if (r.shipment_status === 'in-transit') {
            content += `<p style="margin-top: 10px; padding: 10px; background:#fef3c7; border-left: 3px solid #f59e0b; border-radius: 6px; font-size: 13px; color:#92400e;"><strong>Note:</strong> Transporter cannot be changed once the journey has started.</p>`;
        }

        statusContent.innerHTML = content;
    } else if (r.status === 'rejected') {
        statusInfo.style.display = 'block';
        statusTitle.innerText = "Rejection Details";
        document.querySelector('#statusInfo .card').style.borderLeftColor = '#ef4444';
        statusContent.innerHTML = `<p style="color: #dc2626;"><strong>Reason:</strong> ${r.rejection_reason || 'No reason provided'}</p>`;
    }
}

// ASSIGNMENT LOGIC
const assignModal = document.getElementById("assignModal");

let currentRequestVehicleType = null;

async function openAssignModal() {
    const transporterSelect = document.getElementById("transporterSelect");
    const vehicleSelect = document.getElementById("vehicleSelect");
    
    transporterSelect.innerHTML = '<option value="">Loading...</option>';
    vehicleSelect.innerHTML = '<option value="">Loading...</option>';
    
    try {
        // Load transporters
        const transRes = await fetch('/cargo-project/backend/api/admin/get_transporters.php');
        const transResult = await transRes.json();
        
        // Load vehicles filtered by request vehicle type
        const vehicleRes = await fetch(`/cargo-project/backend/api/admin/get_vehicles.php?vehicle_type=${currentRequestVehicleType}&status=available`);
        const vehicleResult = await vehicleRes.json();
        
        if (transResult.success && vehicleResult.success) {
            transporterSelect.innerHTML = '<option value="">Select Transporter</option>';
            transResult.data.forEach(t => {
                transporterSelect.innerHTML += `<option value="${t.id}">${t.full_name}</option>`;
            });
            
            vehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';
            vehicleResult.data.forEach(v => {
                vehicleSelect.innerHTML += `<option value="${v.id}">${v.plate_number} (${capitalizeVehicleType(v.vehicle_type)})</option>`;
            });
            
            assignModal.style.display = "flex";
            feather.replace();
        } else {
            alert("Failed to load transporters or vehicles");
        }
    } catch (err) {
        console.error(err);
        alert("Error loading data");
    }
}

function closeAssignModal() { assignModal.style.display = "none"; }

document.getElementById("assignForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const transporterId = document.getElementById("transporterSelect").value;
    const vehicleId = document.getElementById("vehicleSelect").value;
    
    if (!transporterId) { alert("Please select a transporter"); return; }
    if (!vehicleId) { alert("Please select a vehicle"); return; }

    try {
        const res = await fetch('/cargo-project/backend/api/admin/assign_transporter.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                request_id: requestId, 
                transporter_id: transporterId,
                vehicle_id: vehicleId 
            })
        });
        const result = await res.json();
        if (result.success) {
            alert("Transporter and vehicle assigned successfully!");
            closeAssignModal();
            window.location.reload();
        } else {
            alert("Error: " + result.error);
        }
    } catch (err) { console.error(err); alert("Assignment failed"); }
});

// REJECTION LOGIC
const rejectModal = document.getElementById("rejectModal");

function openRejectModal() { rejectModal.style.display = "flex"; }
function closeRejectModal() { rejectModal.style.display = "none"; }

document.getElementById("rejectForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const reason = document.getElementById("rejectReason").value;
    
    if (!reason) { alert("Please provide a reason"); return; }

    try {
        const res = await fetch('/cargo-project/backend/api/admin/reject_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId, reason: reason })
        });
        const result = await res.json();
        if (result.success) {
            alert("Request rejected successfully!");
            closeRejectModal();
            window.location.reload();
        } else {
            alert("Error: " + result.error);
        }
    } catch (err) { console.error(err); alert("Rejection failed"); }
});

fetchDetails(); // Call new fetch function
feather.replace();
</script>


