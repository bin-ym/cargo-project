<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_customer.php';
?>

<div class="customer-layout">
    <?php include __DIR__ . '/../layout/navbar_customer.php'; ?>

    <main class="main-content" style="padding: 30px 5%; max-width: 1200px; margin: 0 auto;">
        <header class="topbar" style="margin-bottom: 30px;">
            <h2><?= __('my_requests') ?></h2>
        </header>

        <div class="content">

            <!-- Status Cards -->
            <div class="status-cards">
                <div class="card card-total">
                    <h3><?= __('total_requests') ?></h3>
                    <p id="count-total">0</p>
                </div>
                <div class="card card-pending">
                    <h3><?= __('pending') ?></h3>
                    <p id="count-pending">0</p>
                </div>
                <div class="card card-approved">
                    <h3><?= __('approved') ?></h3>
                    <p id="count-approved">0</p>
                </div>
                <div class="card card-intransit">
                    <h3><?= __('in_transit') ?></h3>
                    <p id="count-intransit">0</p>
                </div>
                <div class="card card-completed">
                    <h3><?= __('completed') ?></h3>
                    <p id="count-completed">0</p>
                </div>
            </div>

            <!-- Tabs & Search -->
            <div class="table-controls">
                <div class="tabs">
                    <button class="tab-btn active" onclick="filterByTab('all', this)"><?= __('all') ?></button>
                    <button class="tab-btn" onclick="filterByTab('pending', this)"><?= __('pending') ?></button>
                    <button class="tab-btn" onclick="filterByTab('approved', this)"><?= __('approved') ?></button>
                    <button class="tab-btn" onclick="filterByTab('in-transit', this)"><?= __('in_transit') ?></button>
                    <button class="tab-btn" onclick="filterByTab('delivered', this)"><?= __('completed') ?></button>
                </div>
                <input type="text" id="searchInput" class="search-box" placeholder="<?= __('search_requests') ?>">
            </div>

            <!-- Table -->
            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th><?= __('id') ?></th>
                            <th><?= __('pickup') ?></th>
                            <th><?= __('dropoff') ?></th>
                            <th><?= __('date') ?></th>
                            <th><?= __('price') ?></th>
                            <th><?= __('status') ?></th>
                            <th><?= __('action') ?></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<!-- Rating Modal -->
<div id="ratingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?= __('rate_transporter') ?></h3>
            <span class="close" onclick="closeRatingModal()">&times;</span>
        </div>

        <form id="ratingForm">
            <div class="form-group">
                <label><?= __('rating') ?></label>
                <div class="star-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-rating="<?= $i ?>">★</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="ratingValue" required>
            </div>

            <div class="form-group">
                <label><?= __('comment_optional') ?></label>
                <textarea id="comment" rows="4"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRatingModal()">
                    <?= __('cancel') ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?= __('submit_rating') ?>
                </button>
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
.card-rejected { border-left: 4px solid #ef4444; }
.card-completed { border-left: 4px solid #6366f1; }

/* Tabs */
.table-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.tabs {
    display: flex;
    gap: 5px;
    background: #f1f5f9;
    padding: 5px;
    border-radius: 8px;
}
.tab-btn {
    padding: 8px 16px;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    background: transparent;
}
.tab-btn.active {
    background: #fff;
}

/* Search */
.search-box {
    padding: 10px 16px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    width: 300px;
}

/* Action Buttons FIX */
.row-action {
    white-space: normal;
}
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.action-buttons .btn-small {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    font-size: 13px;
    cursor: pointer;
    white-space: nowrap;
}
.action-buttons a.btn-small {
    text-decoration: none;
    color: #fff;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
}
.modal-content {
    background: #fff;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    margin: 0;
}
.modal-header h3 {
    margin: 0;
    font-size: 18px;
    color: #0f172a;
    font-weight: 600;
}
.close {
    font-size: 24px;
    cursor: pointer;
    color: #64748b;
    line-height: 1;
}
.close:hover {
    color: #0f172a;
}
#ratingForm {
    padding: 24px;
}
#ratingForm .form-group {
    margin-bottom: 20px;
}
#ratingForm .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #334155;
    font-size: 14px;
}
#ratingForm textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    color: #0f172a;
    resize: vertical;
}
#ratingForm textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin: 0;
}

/* Stars */
.star-rating {
    font-size: 40px;
}
.star {
    cursor: pointer;
    color: #e2e8f0;
}
.star.active {
    color: #fbbf24;
}
</style>

<script>
const API_URL = '/cargo-project/backend/api/customer/get_my_requests.php';
let allRequests = [];
let currentRequestId = null;

async function loadRequests() {
    const res = await fetch(API_URL);
    const json = await res.json();

    if (!json.success) return;

    allRequests = json.data.requests || [];
    const c = json.data.counts;

    count('total', c.total);
    count('pending', c.pending);
    count('approved', c.approved);
    count('intransit', c.inTransit);
    count('completed', c.completed);

    renderTable(allRequests);
}

function count(id, val) {
    document.getElementById(`count-${id}`).innerText = val || 0;
}

async function renderTable(requests) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    if (!requests.length) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;">
            <?= __('no_requests_found') ?>
        </td></tr>`;
        return;
    }

    for (const req of requests) {
        tbody.insertAdjacentHTML('beforeend', await buildRow(req));
    }
}

async function buildRow(req) {
    const status = req.shipment_status || req.status || 'pending';
    const actions = await buildActions(req);

    return `
        <tr>
            <td>#${req.id}</td>
            <td>${req.pickup_location ?? 'N/A'}</td>
            <td>${req.dropoff_location ?? 'N/A'}</td>
            <td>${req.pickup_date ? new Date(req.pickup_date).toLocaleDateString() : 'N/A'}</td>
            <td>${req.price ? Number(req.price).toFixed(2) + ' ETB' : 'N/A'}</td>
            <td>${status}</td>
            <td class="row-action">${actions}</td>
        </tr>
    `;
}

async function buildActions(req) {
    let html = `
        <div class="action-buttons">
            <a href="track_shipment.php?id=${req.eid}" class="btn-small" style="background:#3b82f6;">
                <?= __('track') ?>
            </a>
    `;

    if (req.shipment_status === 'delivered') {
        try {
            const r = await fetch(
                `/cargo-project/backend/api/customer/check_rating.php?request_id=${req.eid}`
            );
            const j = await r.json();

            if (j.success && !j.hasRated) {
                html += `
                    <button class="btn-small" style="background:#16a34a"
                            onclick="openRatingModal('${req.eid}')">
                        <?= __('rate') ?>
                    </button>`;
            } else if (j.success && j.hasRated) {
                html += `
                    <span style="color:#16a34a;font-size:12px;">
                        ★ <?= __('rated') ?>
                    </span>`;
            }
        } catch (e) {
            console.error("Rating check failed", e);
        }
    }
    if (req.status === 'pending') {
        html += `
            <button class="btn-small" style="background:#dc2626"
                    onclick="deleteRequest('${req.eid}')">
                <?= __('delete') ?>
            </button>`;
    }

    return html + `</div>`;
}

function filterByTab(filter, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    let filtered = allRequests;

    if (filter !== 'all') {
        filtered = allRequests.filter(r => {
            if (filter === 'pending') return r.status === 'pending';

            if (filter === 'approved')
                return r.status === 'approved' &&
                       !['in-transit', 'delivered', 'completed'].includes(r.shipment_status);

            if (filter === 'in-transit')
                return r.shipment_status === 'in-transit';

            if (filter === 'delivered')
                return ['delivered', 'completed'].includes(r.shipment_status) || r.status === 'completed';
        });
    }

    renderTable(filtered);
}

document.getElementById('searchInput').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    renderTable(allRequests.filter(r =>
        r.id.toString().includes(q) ||
        r.pickup_location?.toLowerCase().includes(q) ||
        r.dropoff_location?.toLowerCase().includes(q)
    ));
});

function openRatingModal(id) {
    currentRequestId = id;
    document.getElementById('ratingModal').style.display = 'flex';
}

function closeRatingModal() {
    document.getElementById('ratingModal').style.display = 'none';
    document.getElementById('ratingForm').reset();
}

document.querySelectorAll('.star').forEach((s, i) => {
    s.onclick = () => {
        document.getElementById('ratingValue').value = i + 1;
        document.querySelectorAll('.star').forEach((x, j) =>
            x.classList.toggle('active', j <= i)
        );
    };
});

document.getElementById('ratingForm').onsubmit = async e => {
    e.preventDefault();

    const rating = document.getElementById('ratingValue').value;
    if (!rating) return showError("<?= __('select_rating_error') ?>");

    await fetch('/cargo-project/backend/api/customer/rate_transporter.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            request_id: currentRequestId,
            rating: Number(rating),
            comment: comment.value
        })
    });

    showSuccess("<?= __('thank_you_rating') ?>");
    closeRatingModal();
    loadRequests();
};

async function deleteRequest(id) {
    if (!confirm("<?= __('delete_request_confirm') ?>")) return;

    try {
        const response = await fetch('/cargo-project/backend/api/customer/delete_request.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({request_id: id})
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess("<?= __('request_deleted_success') ?>");
            await loadRequests();
        } else {
            showError(result.error || 'Delete failed');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showError('An error occurred while deleting the request');
    }
}

loadRequests();
</script>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>
