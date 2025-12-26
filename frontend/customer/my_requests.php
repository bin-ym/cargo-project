<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_customer.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <h2>My Requests</h2>
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
                <div class="card card-intransit">
                    <h3>In Transit</h3>
                    <p id="count-intransit">0</p>
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
                    <button class="tab-btn" onclick="filterByTab('in-transit')">In Transit</button>
                    <button class="tab-btn" onclick="filterByTab('delivered')">Completed</button>
                </div>
                <input type="text" id="searchInput" placeholder="Search requests..." class="search-box">
            </div>

            <div class="table-wrapper">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Date</th>
                            <th>Price</th>
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

<!-- Rating Modal -->
<div id="ratingModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Rate Transporter</h3>
            <span class="close" onclick="closeRatingModal()">&times;</span>
        </div>
        <form id="ratingForm">
            <div class="form-group">
                <label>Rating</label>
                <div class="star-rating">
                    <span class="star" data-rating="1">★</span>
                    <span class="star" data-rating="2">★</span>
                    <span class="star" data-rating="3">★</span>
                    <span class="star" data-rating="4">★</span>
                    <span class="star" data-rating="5">★</span>
                </div>
                <input type="hidden" id="ratingValue" required>
            </div>
            <div class="form-group">
                <label for="comment">Comment (Optional)</label>
                <textarea id="comment" rows="4" style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:8px;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRatingModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Rating</button>
            </div>
        </form>
    </div>
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
.card-intransit { border-left: 4px solid #8b5cf6; }
.card-completed { border-left: 4px solid #6366f1; }

/* Tabs */
.table-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.tabs { display: flex; gap: 5px; background: #f1f5f9; padding: 5px; border-radius: 8px; }
.tab-btn { padding: 8px 16px; border: none; background: transparent; border-radius: 6px; cursor: pointer; font-size: 14px; color: #64748b; font-weight: 500; transition: all 0.2s; }
.tab-btn.active { background: #fff; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.tab-btn:hover:not(.active) { background: #e2e8f0; }


.search-box { padding: 10px 16px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; width: 300px; }
.search-box:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

/* Rating Modal */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
.modal-content { background: #fff; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; }
.modal-header h3 { margin: 0; color: #0f172a; font-size: 18px; }
.modal-header .close { color: #64748b; font-size: 28px; cursor: pointer; line-height: 1; }
.modal form { padding: 24px; }
.modal .form-group { margin-bottom: 20px; }
.modal .form-group label { display: block; margin-bottom: 8px; color: #334155; font-weight: 500; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 20px; border-top: 1px solid #e2e8f0; }

/* Star Rating */
.star-rating { font-size: 40px; cursor: pointer; }
.star { color: #e2e8f0; transition: color 0.2s; }
.star.active, .star:hover { color: #fbbf24; }

</style>

<script>
const API_URL = '/cargo-project/backend/api/customer/get_my_requests.php';
let allRequests = [];
let currentFilter = 'all';

async function loadRequests() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();

        if (result.success) {
            allRequests = result.data.requests || [];
            
            // Update counts
            const counts = result.data.counts;
            document.getElementById('count-total').innerText = counts.total || 0;
            document.getElementById('count-pending').innerText = counts.pending || 0;
            document.getElementById('count-approved').innerText = counts.approved || 0;
            document.getElementById('count-intransit').innerText = counts.inTransit || 0;
            document.getElementById('count-completed').innerText = counts.completed || 0;
            
            renderTable(allRequests);
        }
    } catch (error) {
        console.error('Error loading requests:', error);
    }
}

function renderTable(requests) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    if (requests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">No requests found</td></tr>';
        return;
    }

    requests.forEach(async (req) => {
        const statusClass = getStatusClass(req.shipment_status || req.status);
        const isCompleted = (req.shipment_status === 'delivered');
        const isPending = (req.status === 'pending');
        
        // Check if already rated
        let actionButton = `<a href="track_shipment.php?id=${req.id}" class="btn-small" style="background:#3b82f6; color:white; text-decoration:none; padding:6px 12px; border-radius:6px;">Track</a>`;
        
        if (isCompleted) {
            try {
                const ratingRes = await fetch(`/cargo-project/backend/api/customer/check_rating.php?request_id=${req.id}`);
                const ratingData = await ratingRes.json();
                
                if (ratingData.success && !ratingData.hasRated) {
                    actionButton += ` <button onclick="openRatingModal(${req.id})" class="btn-small" style="background:#16a34a; color:white; border:none; padding:6px 12px; border-radius:6px; margin-left:5px; cursor:pointer;">Rate</button>`;
                } else if (ratingData.hasRated) {
                    actionButton += ` <span style="color:#16a34a; margin-left:10px; font-size:12px;">★ Rated</span>`;
                }
            } catch (e) {
                console.error('Error checking rating:', e);
            }
        }
        
        // Add delete button for pending requests
        if (isPending) {
            actionButton += ` <button onclick="deleteRequest(${req.id})" class="btn-small" style="background:#dc2626; color:white; border:none; padding:6px 12px; border-radius:6px; margin-left:5px; cursor:pointer;">Delete</button>`;
        }
        
        tbody.innerHTML += `
            <tr>
                <td>#${req.id}</td>
                <td>${req.pickup_location || 'N/A'}</td>
                <td>${req.dropoff_location || 'N/A'}</td>
                <td>${req.pickup_date ? new Date(req.pickup_date).toLocaleDateString() : 'N/A'}</td>
                <td>${req.price ? parseFloat(req.price).toFixed(2) + ' ETB' : 'N/A'}</td>
                <td><span class="badge ${statusClass}">${req.shipment_status || req.status || 'pending'}</span></td>
                <td class="row-action">${actionButton}</td>
            </tr>
        `;
    });
}

function getStatusClass(status) {
    const statusMap = {
        'pending': 'pending',
        'approved': 'approved',
        'in-transit': 'pending',
        'delivered': 'approved',
        'rejected': 'rejected'
    };
    return statusMap[status] || 'pending';
}

function filterByTab(filter) {
    currentFilter = filter;
    
    // Update active tab
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Filter requests
    let filtered = allRequests;
    if (filter !== 'all') {
        filtered = allRequests.filter(req => {
            // Match backend logic
            if (filter === 'pending') {
                return req.status === 'pending';
            }
            if (filter === 'approved') {
                return req.status === 'approved' && req.shipment_status !== 'in-transit' && req.shipment_status !== 'delivered';
            }
            if (filter === 'in-transit') {
                return req.shipment_status === 'in-transit';
            }
            if (filter === 'delivered') { // 'delivered' matches 'Completed' tab
                return req.shipment_status === 'delivered' || req.status === 'completed';
            }
            return false;
        });
    }
    
    renderTable(filtered);
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase();
    const filtered = allRequests.filter(req => {
        return (req.pickup_location && req.pickup_location.toLowerCase().includes(query)) ||
               (req.dropoff_location && req.dropoff_location.toLowerCase().includes(query)) ||
               (req.id && req.id.toString().includes(query));
    });
    renderTable(filtered);
});

// Rating Modal Functions
let currentRequestId = null;

function openRatingModal(requestId) {
    currentRequestId = requestId;
    document.getElementById('ratingModal').style.display = 'flex';
    document.getElementById('ratingValue').value = '';
    document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
}

function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
    document.getElementById('ratingForm').reset();
}

// Star rating interaction
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-rating');
        document.getElementById('ratingValue').value = rating;
        
        document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
        for (let i = 0; i < rating; i++) {
            document.querySelectorAll('.star')[i].classList.add('active');
        }
    });
});

// Submit rating
document.getElementById('ratingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const rating = document.getElementById('ratingValue').value;
    const comment = document.getElementById('comment').value;
    
    if (!rating) {
        alert('Please select a rating');
        return;
    }
    
    try {
        const res = await fetch('/cargo-project/backend/api/customer/rate_transporter.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                request_id: currentRequestId,
                rating: parseInt(rating),
                comment: comment
            })
        });
        
        const result = await res.json();
        
        if (result.success) {
            alert('Thank you for your rating!');
            closeRatingModal();
            loadRequests(); // Reload to update UI
        } else {
            alert('Error: ' + result.error);
        }
    } catch (err) {
        console.error(err);
        alert('Failed to submit rating');
    }
});

// Delete Request Function
async function deleteRequest(requestId) {
    if (!confirm('Are you sure you want to delete this request? This action cannot be undone.')) {
        return;
    }
    
    try {
        const res = await fetch('/cargo-project/backend/api/customer/delete_request.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId })
        });
        
        const result = await res.json();
        
        if (result.success) {
            alert('Request deleted successfully!');
            loadRequests(); // Reload table
        } else {
            alert('Error: ' + result.error);
        }
    } catch (err) {
        console.error(err);
        alert('Failed to delete request');
    }
}

loadRequests();
feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
