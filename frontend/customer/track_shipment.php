<?php
require_once __DIR__ . '/../../backend/config/session.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_customer.php';

$requestId = $_GET['id'] ?? 0;
if ($requestId && !is_numeric($requestId)) {
    $requestId = Security::decryptId($requestId);
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
/* ===== Page Width (MATCH My Requests) ===== */
.track-main {
    padding: 30px 5%;
    max-width: 1200px;
    margin: 0 auto;
}

/* ===== Layout ===== */
.tracking-wrapper {
    display: flex;
    gap: 24px;
    align-items: flex-start;
}

/* LEFT PANEL */
.left-panel {
    width: 320px;
    background: #fff;
    padding: 20px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

/* RIGHT PANEL */
.right-panel {
    flex: 1;
}

/* MAP */
#map {
    width: 100%;
    height: 420px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

/* ===== Timeline ===== */
.timeline-item {
    position: relative;
    padding-left: 28px;
    margin-bottom: 24px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #cbd5e1;
    z-index: 2;
}

.timeline-item::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 18px;
    width: 2px;
    height: calc(100% + 8px);
    background: #e5e7eb;
}

.timeline-item:last-child::after {
    display: none;
}

.timeline-item.completed::before,
.timeline-item.completed::after {
    background: #22c55e;
}

.timeline-item h4 {
    margin: 0;
    font-size: 15px;
    color: #0f172a;
}

.timeline-item p {
    margin: 4px 0 0;
    font-size: 13px;
    color: #64748b;
}
/* Form Message styles */
.form-message {
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    display: none;
    line-height: 1.5;
    margin-bottom: 20px;
    width: 100%;
}
.form-message.error {
    background-color: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
    display: block;
}
.form-message.success {
    background-color: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
    display: block;
}
</style>

<div class="customer-layout">
    <?php include __DIR__ . '/../layout/navbar_customer.php'; ?>

    <main class="main-content track-main">
        <header class="topbar" style="margin-bottom: 24px;">
            <h2><?= __('track_shipment_title') ?>#CT-<?= str_pad($requestId, 4, '0', STR_PAD_LEFT) ?></h2>
        </header>

        <div id="trackMessage" class="form-message"></div>

        <div class="tracking-wrapper">
            <!-- LEFT: STATUS -->
            <div class="left-panel">
                <h3 style="margin-bottom: 20px;"><?= __('shipment_status') ?></h3>

                <div class="timeline-item" id="step-submitted">
                    <h4><?= __('request_submitted') ?></h4>
                    <p><?= __('request_received_msg') ?></p>
                </div>

                <div class="timeline-item" id="step-approved">
                    <h4><?= __('request_approved') ?></h4>
                    <p><?= __('admin_approved_msg') ?></p>
                </div>

                <div class="timeline-item" id="step-assigned">
                    <h4><?= __('transporter_assigned') ?></h4>
                    <p id="transporterName"><?= __('pending') ?></p>
                </div>

                <div class="timeline-item" id="step-transit">
                    <h4><?= __('in_transit') ?></h4>
                    <p><?= __('in_transit_msg') ?></p>
                </div>

                <div class="timeline-item" id="step-delivered">
                    <h4><?= __('completed') ?></h4>
                    <p><?= __('delivered_msg') ?></p>
                </div>
            </div>

            <!-- RIGHT: MAP -->
            <div class="right-panel">
                <div id="map"></div>
            </div>
        </div>
    </main>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const requestId = '<?= htmlspecialchars($_GET['id'] ?? '') ?>';

function showTrackMessage(msg, type = 'error') {
    const el = document.getElementById('trackMessage');
    el.textContent = msg;
    el.className = 'form-message ' + type;
    el.style.display = 'block';
}

function showError(msg) { showTrackMessage(msg, 'error'); }

if (!requestId) {
    showError('Invalid Request ID');
    setTimeout(() => {
        window.location.href = 'my_requests.php';
    }, 2000);
}
let map;

function initMap() {
    map = L.map('map').setView([9.03, 38.74], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    loadShipment();
}

async function loadShipment() {
    const res = await fetch(`/cargo-project/backend/api/requests/index.php?id=${requestId}`);
    const json = await res.json();

    if (!json.success) return;

    updateTimeline(json.data);
    drawRoute(json.data);
}

function updateTimeline(d) {
    document.getElementById('step-submitted').classList.add('completed');

    if (d.status === 'approved')
        document.getElementById('step-approved').classList.add('completed');

    if (['assigned','in-transit','delivered','completed'].includes(d.shipment_status)) {
        document.getElementById('step-approved').classList.add('completed');
        document.getElementById('step-assigned').classList.add('completed');
        document.getElementById('transporterName').innerText = d.transporter_name || '<?= __('assigned') ?>';
    }

    if (['in-transit','delivered','completed'].includes(d.shipment_status))
        document.getElementById('step-transit').classList.add('completed');

    if (['delivered', 'completed'].includes(d.shipment_status) || d.status === 'completed')
        document.getElementById('step-delivered').classList.add('completed');
}

async function drawRoute(d) {
    const start = [parseFloat(d.pickup_lat), parseFloat(d.pickup_lng)];
    const end   = [parseFloat(d.dropoff_lat), parseFloat(d.dropoff_lng)];

    const icon = (c) => L.icon({
        iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${c}.png`,
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41]
    });

    L.marker(start, { icon: icon('green') }).addTo(map);
    L.marker(end, { icon: icon('red') }).addTo(map);

    const url = `https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full&geometries=geojson`;
    const r = await fetch(url);
    const data = await r.json();

    if (data.code === 'Ok' && data.routes.length > 0) {
        const route = data.routes[0];
        const points = route.geometry.coordinates.map(c => [c[1], c[0]]);
        const line = L.polyline(points, { color: '#2563eb', weight: 4 }).addTo(map);
        map.fitBounds(line.getBounds(), { padding: [40, 40] });

        // Display ETA
        const duration = route.duration; // Total duration in seconds
        const minutes = Math.round(duration / 60);
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        const etaText = hours > 0 ? `${hours}h ${remainingMinutes}m` : `${minutes}m`;
        
        const infoDiv = document.querySelector('.tracking-info') || document.querySelector('.left-panel'); // Fallback selector
        
        const existingBadge = document.getElementById('eta-badge');
        if(existingBadge) existingBadge.remove();

        const etaBadge = document.createElement('div');
        etaBadge.id = 'eta-badge';
        etaBadge.style.cssText = "margin-top: 10px; padding: 10px; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe; color: #1e40af; font-size: 14px; display: flex; align-items: center; gap: 8px;";
        etaBadge.innerHTML = `<i data-feather="clock" style="width: 16px; height: 16px;"></i> <b>Est. Duration:</b> ${etaText}`;
        infoDiv.appendChild(etaBadge);
        if (typeof feather !== 'undefined') feather.replace();

        // Car Animation if in-transit
        if (d.shipment_status === 'in-transit' && d.picked_up_at) {
            // Calculate progress based on Real Time
            const pickupTime = new Date(d.picked_up_at).getTime();
            const now = new Date().getTime();
            
            // Allow for a slight visual delay or speedup for demo purposes if needed, 
            // but user asked for "real" movement based on "ratio".
            // So we use strict ratio: (Now - Start) / Duration
            
            // To make it visible for very long trips in this demo, usually we might speed it up.
            // But the user requested "real... don't stop... continue".
            // Implementation: We animate from CURRENT calculated position.
            
            startRealTimeAnimation(points, pickupTime, duration);
        } else if (d.shipment_status === 'delivered' || d.shipment_status === 'completed') {
             // Place car at end
             const carIcon = L.divIcon({
                html: '<i data-feather="check-circle" style="color: #16a34a; fill: white; width: 24px; height: 24px;"></i>',
                className: 'car-icon',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            L.marker(points[points.length-1], { icon: carIcon }).addTo(map);
            if (typeof feather !== 'undefined') feather.replace();
        }
    }
}

let carMarker = null;
let animationFrame = null;

function startRealTimeAnimation(latLngs, startTimeMs, totalDurationSec) {
    if (carMarker) map.removeLayer(carMarker);
    if (animationFrame) cancelAnimationFrame(animationFrame);

    const carIcon = L.divIcon({
        html: '<i data-feather="truck" style="color: #2563eb; fill: white; width: 24px; height: 24px;"></i>',
        className: 'car-icon', // Ensure this class has no background in CSS or add inline style
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    // Add marker initially at start (will jump immediately in loop)
    carMarker = L.marker(latLngs[0], { icon: carIcon }).addTo(map);
    if (typeof feather !== 'undefined') feather.replace();

    function animate() {
        const now = Date.now();
        const elapsedSec = (now - startTimeMs) / 1000;
        let progress = elapsedSec / totalDurationSec;

        if (progress < 0) progress = 0;
        if (progress > 1) progress = 1;

        // Find index
        // latLngs array length e.g. 500
        // index = 500 * 0.5 = 250
        const indexFloat = (latLngs.length - 1) * progress;
        const index = Math.floor(indexFloat);
        
        // Exact position interpolation could be better but nearest point is fine for high density paths
        carMarker.setLatLng(latLngs[index]);

        // Rotate Car
        if (index + 1 < latLngs.length) {
             const curr = latLngs[index];
             const next = latLngs[index + 1];
             // Simple bearing calculation
             // lat/lng are in degrees. For visualization relative bearing is enough using screen coords or mercator
             // but simpler: just standard atan2 on lat/lng works reasonably for short segments
             const y = next[0] - curr[0];
             const x = next[1] - curr[1];
             const angle = Math.atan2(y, x) * 180 / Math.PI;
             
             const iconEl = carMarker.getElement().querySelector('i');
             if(iconEl) iconEl.style.transform = `rotate(${angle}deg)`; // Adjust rotation offset if needed (usually truck icon points right or up)
             // Feather truck icon usually points left/right? Let's assume standard right 0deg.
             // If icon points left, verify rotation.
        }

        if (progress < 1) {
            animationFrame = requestAnimationFrame(animate);
        } else {
            console.log("Arrived");
        }
    }
    
    animate();
}

initMap();
</script>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>
