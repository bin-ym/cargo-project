<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_customer.php';
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    .tracking-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        align-items: start;
    }
    @media (max-width: 992px) {
        .tracking-grid {
            grid-template-columns: 1fr;
        }
    }
    #map {
        height: 500px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
    .map-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
</style>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>Track Shipment</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="tracking-grid">
                <!-- Left Column: Input & Timeline -->
                <div class="left-col">
                    <!-- Search Box -->
                    <div class="recent-activity">
                        <h3>Enter Tracking Number</h3>
                        <div style="margin-top: 20px; margin-bottom: 30px;">
                            <input type="text" id="trackingInput" placeholder="e.g., CT-2847" 
                                style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 16px;">
                            <button class="btn btn-primary" style="margin-top: 15px; width: 100%;" id="trackButton">Track Shipment</button>
                        </div>
                    </div>

                    <!-- Tracking Results -->
                    <div class="recent-activity" style="margin-top: 25px; display:none;" id="trackingResult">
                        <h3 id="trackingTitle"></h3>
                        <div style="margin-top: 25px;">
                            <div style="position: relative; padding-left: 40px;" id="timeline">
                                <!-- Timeline items injected here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Map -->
                <div class="right-col">
                    <div class="map-card">
                        <h3 style="margin-bottom: 15px;">Live Location</h3>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
/* MAP INITIALIZATION */
let map = null;
let routeLine = null;
let markers = [];

function initMap() {
    // Default center (Addis Ababa) - matching new_request.php
    map = L.map('map').setView([9.03, 38.74], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);
}

/* READ URL PARAM ID */
const urlParams = new URLSearchParams(window.location.search);
const requestId = urlParams.get('id');

// Initialize map on load
initMap();

if (requestId) {
    document.getElementById('trackingInput').value = requestId;
    trackShipment(requestId);
}

/* BUTTON CLICK */
document.getElementById('trackButton').addEventListener('click', () => {
    const val = document.getElementById('trackingInput').value.trim();
    if (val) trackShipment(val);
});

/* ENTER KEY SUPPORT */
document.getElementById('trackingInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        const val = document.getElementById('trackingInput').value.trim();
        if (val) trackShipment(val);
    }
});

/* FETCH SHIPMENT DATA */
async function trackShipment(id) {
    try {
        const res = await fetch(`/cargo-project/backend/api/customer/get_request_details.php?id=${id}`);
        const result = await res.json();
        
        if (result.success) {
            renderTimeline(result.data);
            updateMap(result.data);
        } else {
            alert(result.error || 'Request not found');
        }
    } catch (err) {
        console.error(err);
        alert("Error fetching tracking details");
    }
}

/* UPDATE MAP WITH REAL DATA */
function updateMap(data) {
    // Clear existing layers
    if (routeLine) map.removeLayer(routeLine);
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    // Use real coordinates from database
    const pickupLat = parseFloat(data.pickup_lat);
    const pickupLng = parseFloat(data.pickup_lng);
    const dropoffLat = parseFloat(data.dropoff_lat);
    const dropoffLng = parseFloat(data.dropoff_lng);
    
    // Validate coordinates
    if (!pickupLat || !pickupLng || !dropoffLat || !dropoffLng) {
        console.warn('Invalid coordinates, using default view');
        return;
    }
    
    const start = [pickupLat, pickupLng];
    const end = [dropoffLat, dropoffLng];

    // Create custom icons matching new_request.php style
    const createIcon = (color) => {
        return L.icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    };

    // Add Markers
    const startMarker = L.marker(start, {icon: createIcon('green')}).addTo(map)
        .bindPopup(`<b>Pickup:</b><br>${data.pickup_location}`);
    const endMarker = L.marker(end, {icon: createIcon('red')}).addTo(map)
        .bindPopup(`<b>Dropoff:</b><br>${data.dropoff_location}`);
    markers.push(startMarker, endMarker);

    // Draw Route
    routeLine = L.polyline([start, end], {color: 'blue', weight: 4, opacity: 0.7}).addTo(map);

    // Fit bounds
    map.fitBounds(routeLine.getBounds(), {padding: [50, 50]});

    // If In Transit, show a truck marker somewhere on the route (midpoint for now)
    if (data.shipment_status === 'in-transit') {
        const midLat = (pickupLat + dropoffLat) / 2;
        const midLng = (pickupLng + dropoffLng) / 2;
        const currentLoc = [midLat, midLng];
        
        const truckIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/713/713311.png',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });
        
        const truck = L.marker(currentLoc, {icon: truckIcon}).addTo(map)
            .bindPopup("Current Location (Estimated)").openPopup();
        markers.push(truck);
    }
}

/* TIMELINE RENDERING */
function renderTimeline(data) {
    const container = document.getElementById("trackingResult");
    const timeline = document.getElementById("timeline");
    const title = document.getElementById("trackingTitle");
    
    container.style.display = "block";
    title.innerText = `Shipment Status: #CT-${String(data.id).padStart(4, '0')}`;
    timeline.innerHTML = `<div style="position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #e2e8f0;"></div>`;

    const events = [
        { status: 'pending', label: 'Request Submitted', desc: 'Request created successfully', time: data.created_at },
        { status: 'approved', label: 'Request Approved', desc: 'Your request has been approved', time: null },
        { status: 'assigned', label: 'Transporter Assigned', desc: data.transporter_name ? `Assigned to ${data.transporter_name}` : 'Transporter assigned', time: null },
        { status: 'in-transit', label: 'In Transit', desc: 'Your package is on the way', time: null },
        { status: 'delivered', label: 'Delivered', desc: 'Package delivered successfully', time: null }
    ];

    /* Determine current step */
    let currentStep = 0;
    if (data.status === 'approved') currentStep = 1;
    if (data.shipment_status === 'assigned') currentStep = 2;
    if (data.shipment_status === 'in-transit') currentStep = 3;
    if (data.shipment_status === 'delivered') currentStep = 4;

    if (data.status === 'rejected') {
        timeline.innerHTML += createTimelineItem('Rejected', `Reason: ${data.rejection_reason}`, true);
        return;
    }

    events.forEach((event, index) => {
        if (index <= currentStep) {
            timeline.innerHTML += createTimelineItem(event.label, event.desc, false, index === currentStep);
        }
    });
}

function createTimelineItem(title, desc, isError = false, isActive = false) {
    const dotColor = isError ? '#dc2626' : '#16a34a';

    return `
        <div style="margin-bottom: 30px; position: relative;">
            <div style="position: absolute; left: -32px; width: 12px; height: 12px; 
                        background: ${dotColor}; border-radius: 50%; 
                        border: 3px solid white; box-shadow: 0 0 0 2px ${dotColor};">
            </div>
            <h4 style="color: #0f172a; margin-bottom: 5px;">${title}</h4>
            <p style="color: #64748b; font-size: 14px;">${desc}</p>
        </div>`;
}

feather.replace();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>