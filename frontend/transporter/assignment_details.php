<?php
// frontend/transporter/assignment_details.php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_transporter.php';

$requestId = $_GET['id'] ?? 0;
if ($requestId && !is_numeric($requestId)) {
    $requestId = Security::decryptId($requestId);
}
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    /* Map styles moved to transporter.css */
</style>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>Assignment Details #CT-<?= str_pad($requestId, 4, '0', STR_PAD_LEFT) ?></h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Transporter') ?></span>
            </div>
        </header>


        <div class="content">
            <div class="recent-activity mb-20" id="statusUpdateSection">
                <h3>Update Status</h3>
                <div class="flex gap-10 mt-15">
                    <button onclick="updateStatus('in-transit')" class="btn bg-primary">Start / In Transit</button>
                    <button onclick="updateStatus('delivered')" class="btn bg-success">Mark Delivered</button>
                </div>
            </div>


            <!-- GPS / Map Section -->
            <div class="recent-activity mb-20">
                <div class="flex justify-between items-center mb-15">
                    <h3>Shipment Location (GPS)</h3>
                    <button id="shareLocationBtn" onclick="toggleLocationSharing()" class="btn bg-secondary">
                        <i data-feather="navigation" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                        Share Live Location
                    </button>
                </div>
                <div id="map" class="map-container"></div>
                <p class="mt-10 text-muted" style="font-size: 14px;">
                    <i data-feather="map-pin" style="width: 16px; height: 16px; vertical-align: text-bottom;"></i>
                    Current Location: <span id="locationText">Loading...</span>
                </p>
            </div>

            <div class="table-wrapper">
                <h3>Cargo Items</h3>
                <table class="table-modern mt-15">
                    <thead>
                        <tr>
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
            
            <div class="mt-20">
                <a href="assignments.php" class="btn btn-secondary">Back to Assignments</a>
            </div>
            
        </div>
    </main>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
const requestId = '<?= htmlspecialchars($_GET['id'] ?? '') ?>';
if (!requestId) {
    alert('Invalid Request ID');
    window.location.href = 'assignments.php';
}
const API_URL = '/cargo-project/backend/api/cargo_items/index.php';


/* MAP INITIALIZATION */
let map = null;
let pickupMarker = null;
let dropoffMarker = null;
let routeLine = null;

function initMap() {
    // Default center (Addis Ababa) - matching new_request.php
    map = L.map('map').setView([9.03, 38.74], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);
    
    // Fetch request details to get coordinates
    fetchRequestDetails();
}

async function fetchRequestDetails() {
    try {
        const response = await fetch(`/cargo-project/backend/api/requests/index.php?id=${requestId}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            updateMapWithRoute(result.data);
            
            // Handle Button Visibility
            const statusSection = document.getElementById('statusUpdateSection');
            const startBtn = document.querySelector("button[onclick=\"updateStatus('in-transit')\"]");
            const deliverBtn = document.querySelector("button[onclick=\"updateStatus('delivered')\"]");

            if (result.data.shipment_status === 'assigned') {
                // Show Start, Hide Delivered
                if(startBtn) startBtn.style.display = 'inline-block';
                if(deliverBtn) deliverBtn.style.display = 'none';
            } else if (result.data.shipment_status === 'in-transit') {
                // Hide Start, Show Delivered
                if(startBtn) startBtn.style.display = 'none';
                if(deliverBtn) deliverBtn.style.display = 'inline-block';
            } else if (result.data.shipment_status === 'delivered' || result.data.shipment_status === 'completed') {
                // Hide Section
                if (statusSection) statusSection.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error fetching request details:', error);
    }
}

async function updateMapWithRoute(data) {
    const pickupLat = parseFloat(data.pickup_lat);
    const pickupLng = parseFloat(data.pickup_lng);
    const dropoffLat = parseFloat(data.dropoff_lat);
    const dropoffLng = parseFloat(data.dropoff_lng);
    
    if (!pickupLat || !pickupLng || !dropoffLat || !dropoffLng) {
        console.warn('Invalid coordinates');
        document.getElementById('locationText').innerText = 'Coordinates not available';
        return;
    }
    
    const start = [pickupLat, pickupLng];
    const end = [dropoffLat, dropoffLng];
    
    // Create custom icons
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
    
    // Add markers
    pickupMarker = L.marker(start, {icon: createIcon('green')}).addTo(map)
        .bindPopup(`<b>Pickup:</b><br>${data.pickup_location}`);
    dropoffMarker = L.marker(end, {icon: createIcon('red')}).addTo(map)
        .bindPopup(`<b>Dropoff:</b><br>${data.dropoff_location}`);
    
    // OSRM Routing
    const url = `https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${dropoffLng},${dropoffLat}?overview=full&geometries=geojson`;

    try {
        const response = await fetch(url);
        const routeData = await response.json();

        if (routeData.code === 'Ok' && routeData.routes.length > 0) {
            const route = routeData.routes[0];
            // Flip coordinates for Leaflet
            const latLngs = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            
            if (routeLine) map.removeLayer(routeLine);
            routeLine = L.polyline(latLngs, {color: 'blue', weight: 4, opacity: 0.7}).addTo(map);
            map.fitBounds(routeLine.getBounds(), {padding: [50, 50]});

            // Display ETA
            const duration = route.duration; // in seconds
            const minutes = Math.round(duration / 60);
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            const etaText = hours > 0 ? `${hours}h ${remainingMinutes}m` : `${minutes}m`;
            
            document.getElementById('locationText').innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <span>${data.pickup_location} → ${data.dropoff_location}</span>
                    <span class="badge badge-info" style="font-size: 12px; width: fit-content;">
                        <i data-feather="clock" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;"></i>
                        Est. Travel Time: ${etaText}
                    </span>
                </div>
            `;
            feather.replace();

            // Car Animation if in-transit
            if (data.shipment_status === 'in-transit') {
                const animDuration = Math.min(60, Math.max(15, duration / 60));
                startCarAnimation(latLngs, animDuration);
            }
        } else {
            // Fallback to straight line
            if (routeLine) map.removeLayer(routeLine);
            routeLine = L.polyline([start, end], {color: 'blue', weight: 4, opacity: 0.7, dashArray: '10, 10'}).addTo(map);
            map.fitBounds(routeLine.getBounds(), {padding: [50, 50]});
        }
    } catch (e) {
        console.error("Routing error:", e);
        // Fallback to straight line
        if (routeLine) map.removeLayer(routeLine);
        routeLine = L.polyline([start, end], {color: 'blue', weight: 4, opacity: 0.7, dashArray: '10, 10'}).addTo(map);
        map.fitBounds(routeLine.getBounds(), {padding: [50, 50]});
    }
    
    document.getElementById('locationText').innerText = `${data.pickup_location} → ${data.dropoff_location}`;
}


async function fetchItems() {
    try {
        const response = await fetch(`${API_URL}?request_id=${requestId}`);
        const result = await response.json();
        if (result.success) {
            renderTable(result.data);
        }
    } catch (error) {
        console.error('Error fetching items:', error);
    }
}

function renderTable(data) {
    const body = document.getElementById("tableBody");
    body.innerHTML = "";

    if (data.length === 0) {
        body.innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 20px;">No items found</td></tr>`;
        return;
    }

    data.forEach(row => {
        body.innerHTML += `
        <tr>
            <td>${row.item_name}</td>
            <td>${row.quantity}</td>
            <td>${row.weight}</td>
            <td>${row.category}</td>
            <td>${row.description || '-'}</td>
        </tr>`;
    });
}

let carMarker = null;
let animationFrame = null;

function startCarAnimation(latLngs, durationSeconds) {
    if (carMarker) map.removeLayer(carMarker);
    if (animationFrame) cancelAnimationFrame(animationFrame);

    const carIcon = L.divIcon({
        html: '<i data-feather="truck" style="color: #2563eb; fill: white; width: 24px; height: 24px;"></i>',
        className: 'car-icon',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    carMarker = L.marker(latLngs[0], { icon: carIcon }).addTo(map);
    feather.replace();

    let step = 0;
    const totalSteps = latLngs.length;
    
    // We want the animation to take 'durationSeconds'
    // requestAnimationFrame runs at ~60fps
    // totalFrames = durationSeconds * 60
    // stepIncrement = totalSteps / totalFrames
    const totalFrames = (durationSeconds || 30) * 60; 
    const stepIncrement = totalSteps / totalFrames;

    function animate() {
        if (step >= totalSteps) {
            step = 0; 
        }

        carMarker.setLatLng(latLngs[Math.floor(step)]);
        
        if (step + 1 < totalSteps) {
            const nextPoint = latLngs[Math.floor(step) + 1];
            const currPoint = latLngs[Math.floor(step)];
            const angle = Math.atan2(nextPoint[0] - currPoint[0], nextPoint[1] - currPoint[1]) * 180 / Math.PI;
            const iconElement = carMarker.getElement().querySelector('i');
            if (iconElement) {
                iconElement.style.transform = `rotate(${angle + 90}deg)`;
            }
        }

        step += stepIncrement;
        animationFrame = requestAnimationFrame(animate);
    }

    animate();
}

async function updateStatus(status) {
    if (!confirm(`Are you sure you want to update status to ${status}?`)) return;

    try {
        const res = await fetch('/cargo-project/backend/api/transporter/update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId, status: status })
        });
        
        const result = await res.json();
        if (result.success) {
            alert("Status updated successfully!");
            window.location.reload();
        } else {
            alert("Error: " + result.error);
        }
    } catch (err) {
        console.error(err);
        alert("Update failed");
    }
}

/* GPS SHARING LOGIC */
let watchId = null;
let isSharing = false;

function toggleLocationSharing() {
    const btn = document.getElementById('shareLocationBtn');
    
    if (!isSharing) {
        if (!navigator.geolocation) {
            alert("Geolocation is not supported by your browser");
            return;
        }

        isSharing = true;
        btn.style.background = "#dc2626"; // Red
        btn.innerHTML = `<i data-feather="stop-circle" style="width: 16px; height: 16px; vertical-align: middle;"></i> Stop Sharing`;
        feather.replace();

        watchId = navigator.geolocation.watchPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Update UI
                updateMapMarker(lat, lng);
                
                // Send to Backend
                sendLocationUpdate(lat, lng);
            },
            (error) => {
                console.error("GPS Error:", error);
                let errorMsg = "Unable to retrieve location. ";
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg += "Please allow location access in your browser settings.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg += "Location information is unavailable.";
                        break;
                    case error.TIMEOUT:
                        errorMsg += "The request to get your location timed out.";
                        break;
                    default:
                        errorMsg += "An unknown error occurred.";
                }
                
                alert(errorMsg);
                toggleLocationSharing(); // Stop
            },
            { 
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );

    } else {
        isSharing = false;
        btn.style.background = "#64748b"; // Gray
        btn.innerHTML = `<i data-feather="navigation" style="width: 16px; height: 16px; vertical-align: middle;"></i> Share Live Location`;
        feather.replace();

        if (watchId) navigator.geolocation.clearWatch(watchId);
    }
}

function updateMapMarker(lat, lng) {
    // Create or update live location marker
    const liveIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', // Blue dot for live location
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    if (window.liveMarker) {
        window.liveMarker.setLatLng([lat, lng]);
    } else {
        window.liveMarker = L.marker([lat, lng], {icon: liveIcon}).addTo(map)
            .bindPopup("Your Live Location").openPopup();
    }
    
    map.setView([lat, lng], 15);
    document.getElementById('locationText').innerText = `Live: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

async function sendLocationUpdate(lat, lng) {
    try {
        await fetch('/cargo-project/backend/api/transporter/update_location.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ request_id: requestId, lat: lat, lng: lng })
        });
    } catch (err) {
        console.error("Failed to sync location", err);
    }
}

// Initialize
initMap();
fetchItems();
feather.replace();
</script>
