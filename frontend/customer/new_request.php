<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_customer.php';

// Generate unique transaction reference
$txRef = "TX-" . uniqid() . "-" . time();
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
#map { height: 600px; width: 100%; border-radius: 12px; margin-bottom: 20px; z-index: 1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .price-card { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 20px; }
    .price-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .price-total { font-size: 1.25rem; font-weight: 700; color: #0f172a; border-top: 1px solid #cbd5e1; padding-top: 10px; }
    .input-group { position: relative; }
    .input-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; cursor: pointer; }
</style>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>New Cargo Request</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 1200px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
                    
                    <!-- Left: Form -->
                    <div>
                        <h3>Request Details</h3>
                        <form id="requestForm" style="margin-top: 20px;">
                            <!-- Hidden Fields for Coordinates -->
                            <input type="hidden" id="pickup_lat" name="pickup_lat">
                            <input type="hidden" id="pickup_lng" name="pickup_lng">
                            <input type="hidden" id="dropoff_lat" name="dropoff_lat">
                            <input type="hidden" id="dropoff_lng" name="dropoff_lng">
                            <input type="hidden" id="distance_km" name="distance_km">
                            <input type="hidden" id="calculated_price" name="price">

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Pickup Location</label>
                                <div class="input-group">
                                    <input type="text" id="pickup_location" placeholder="Type address or click on map..." 
                                        onchange="geocodeAddress(this.value, 'pickup')"
                                        style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <i data-feather="search" class="input-icon" onclick="geocodeAddress(document.getElementById('pickup_location').value, 'pickup')"></i>
                                </div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Dropoff Location</label>
                                <div class="input-group">
                                    <input type="text" id="dropoff_location" placeholder="Type address or click on map..." 
                                        onchange="geocodeAddress(this.value, 'dropoff')"
                                        style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <i data-feather="search" class="input-icon" onclick="geocodeAddress(document.getElementById('dropoff_location').value, 'dropoff')"></i>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Pickup Date</label>
                                <input type="date" id="pickup_date" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                            </div>
                            
                            <h4 style="margin: 30px 0 15px; color: #0f172a;">Cargo Items</h4>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Item Name</label>
                                <input type="text" id="item_name" placeholder="e.g., Electronics" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Quantity</label>
                                    <input type="number" id="quantity" value="1" min="1" onchange="calculatePrice()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Weight (kg)</label>
                                    <input type="number" id="weight" placeholder="e.g., 10" min="0.1" step="0.1" onchange="calculatePrice()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                                </div>
                            </div>
                            
                            <!-- Vehicle Type Selection -->
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Vehicle Type</label>
                                <select id="vehicle_type" onchange="calculatePrice()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <option value="pickup">Pickup (Small)</option>
                                    <option value="isuzu">Isuzu (Medium)</option>
                                    <option value="trailer">Trailer (Large)</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Category</label>
                                <select id="category" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <option>Electronics</option>
                                    <option>Furniture</option>
                                    <option>Hardware</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Description</label>
                                <textarea id="description" rows="3" placeholder="Additional details..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Right: Map & Price -->
                    <div style="position: sticky; top: 20px;">
                        <div id="map"></div>
                        <div style="text-align: center; margin-bottom: 10px; color: #64748b; font-size: 0.9rem;">
                            Click map to set <b>Pickup</b> (Green) then <b>Dropoff</b> (Red)
                        </div>

                        <div class="price-card">
                            <h4>Estimated Cost</h4>
                            <div class="price-row">
                                <span>Distance:</span>
                                <span id="disp_distance">0 km</span>
                            </div>
                            <div class="price-row">
                                <span>Weight:</span>
                                <span id="disp_weight">0 kg</span>
                            </div>
                            <div class="price-row price-total">
                                <span>Total:</span>
                                <span id="disp_price">0 ETB</span>
                            </div>
                            
                            <button type="button" onclick="submitRequest()" class="btn btn-primary" style="width: 100%; background: #16a34a; border-color: #16a34a; margin-top: 20px;">
                                Submit Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <!-- Feather Icons for search -->
        <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

        <script>
            // Set minimum date
            document.getElementById('pickup_date').min = new Date().toISOString().split('T')[0];

            /* MAP LOGIC */
            let map, pickupMarker, dropoffMarker, routeLine;
            let selectionMode = 'pickup'; // pickup or dropoff

            function initMap() {
                // Default center (Addis Ababa)
                map = L.map('map').setView([9.03, 38.74], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

                map.on('click', function(e) {
                    if (selectionMode === 'pickup') {
                        setPickup(e.latlng, true);
                        selectionMode = 'dropoff';
                    } else {
                        setDropoff(e.latlng, true);
                        selectionMode = 'pickup'; // Reset cycle
                    }
                });
            }

            // Geocoding (Address -> Coords)
            async function geocodeAddress(query, type) {
                if (!query) return;

                try {
                    const res = await fetch(`/cargo-project/backend/api/utils/geocode.php?q=${encodeURIComponent(query)}`);
                    const data = await res.json();

                    if (data && data.length > 0) {
                        const latlng = {
                            lat: parseFloat(data[0].lat),
                            lng: parseFloat(data[0].lon)
                        };

                        if (type === 'pickup') {
                            setPickup(latlng, false);
                            document.getElementById('pickup_location').value = data[0].display_name;
                        } else {
                            setDropoff(latlng, false);
                            document.getElementById('dropoff_location').value = data[0].display_name;
                        }

                        map.setView(latlng, 14);
                    } else {
                        alert("Location not found");
                    }
                } catch (err) {
                    console.error(err);
                    alert("Location search failed");
                }
            }

            // Reverse Geocoding (Coords -> Address)
            async function reverseGeocode(lat, lng, type) {
                try {
                    const res = await fetch(`/cargo-project/backend/api/utils/geocode.php?type=reverse&q[lat]=${lat}&q[lng]=${lng}`);
                    const data = await res.json();

                    const name = data.display_name ?? `${lat.toFixed(4)}, ${lng.toFixed(4)}`;

                    if (type === 'pickup') {
                        document.getElementById('pickup_location').value = name;
                    } else {
                        document.getElementById('dropoff_location').value = name;
                    }
                } catch {
                    const fallback = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                    document.getElementById(type + '_location').value = fallback;
                }
            }

            function setPickup(latlng, fetchAddress = false) {
                if (pickupMarker) map.removeLayer(pickupMarker);
                pickupMarker = L.marker(latlng, {icon: createIcon('green')}).addTo(map).bindPopup("Pickup Location").openPopup();
                
                document.getElementById('pickup_lat').value = latlng.lat;
                document.getElementById('pickup_lng').value = latlng.lng;
                
                if (fetchAddress) {
                    document.getElementById('pickup_location').value = "Loading address...";
                    reverseGeocode(latlng.lat, latlng.lng, 'pickup');
                }
                
                calculateDistance();
            }

            function setDropoff(latlng, fetchAddress = false) {
                if (dropoffMarker) map.removeLayer(dropoffMarker);
                dropoffMarker = L.marker(latlng, {icon: createIcon('red')}).addTo(map).bindPopup("Dropoff Location").openPopup();
                
                document.getElementById('dropoff_lat').value = latlng.lat;
                document.getElementById('dropoff_lng').value = latlng.lng;
                
                if (fetchAddress) {
                    document.getElementById('dropoff_location').value = "Loading address...";
                    reverseGeocode(latlng.lat, latlng.lng, 'dropoff');
                }
                
                calculateDistance();
            }

            function createIcon(color) {
                return L.icon({
                    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
            }

            function calculateDistance() {
                if (!pickupMarker || !dropoffMarker) return;

                const p = pickupMarker.getLatLng();
                const d = dropoffMarker.getLatLng();
                
                // Simple Haversine distance for now (straight line)
                const R = 6371; // Radius of earth in km
                const dLat = deg2rad(d.lat - p.lat);
                const dLon = deg2rad(d.lng - p.lng);
                const a = 
                    Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(deg2rad(p.lat)) * Math.cos(deg2rad(d.lat)) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2); 
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                const dist = R * c; // Distance in km

                document.getElementById('distance_km').value = dist.toFixed(2);
                document.getElementById('disp_distance').innerText = dist.toFixed(2) + " km";

                // Draw line
                if (routeLine) map.removeLayer(routeLine);
                routeLine = L.polyline([p, d], {color: 'blue'}).addTo(map);
                map.fitBounds(routeLine.getBounds(), {padding: [20, 20]});

                calculatePrice();
            }

            function deg2rad(deg) {
                return deg * (Math.PI/180);
            }

            async function calculatePrice() {
                const distance = parseFloat(document.getElementById('distance_km').value) || 0;
                const weight = parseFloat(document.getElementById('weight').value) || 0;
                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const vehicleType = document.getElementById('vehicle_type').value;
                
                if (distance <= 0 || weight <= 0) return;

                document.getElementById('disp_price').innerText = "Calculating...";

                try {
                    const res = await fetch('/cargo-project/backend/api/customer/calculate_price.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            distance_km: distance, 
                            weight: weight, 
                            quantity: quantity,
                            vehicle_type: vehicleType 
                        })
                    });
                    const data = await res.json();
                    
                    if (data.success) {
                        document.getElementById('calculated_price').value = data.price;
                        document.getElementById('disp_weight').innerText = weight + " kg";
                        document.getElementById('disp_price').innerText = data.price + " ETB";
                    } else {
                        console.error("Price calc error:", data.error);
                        document.getElementById('disp_price').innerText = "Error";
                    }
                } catch (err) {
                    console.error("Price fetch error:", err);
                }
            }

            async function submitRequest() {
                // 1. Validate Form
                const required = ['pickup_lat', 'dropoff_lat', 'pickup_date', 'item_name', 'weight'];
                for (let id of required) {
                    if (!document.getElementById(id).value) {
                        alert("Please fill all fields and select locations on map.");
                        return;
                    }
                }

                // 2. Submit to Backend First
                const payload = {
                    pickup_location: document.getElementById('pickup_location').value,
                    dropoff_location: document.getElementById('dropoff_location').value,
                    pickup_lat: document.getElementById('pickup_lat').value,
                    pickup_lng: document.getElementById('pickup_lng').value,
                    dropoff_lat: document.getElementById('dropoff_lat').value,
                    dropoff_lng: document.getElementById('dropoff_lng').value,
                    distance_km: document.getElementById('distance_km').value,
                    price: document.getElementById('calculated_price').value,
                    vehicle_type: document.getElementById('vehicle_type').value,
                    pickup_date: document.getElementById('pickup_date').value,
                    items: [{
                        item_name: document.getElementById('item_name').value,
                        quantity: document.getElementById('quantity').value,
                        weight: document.getElementById('weight').value,
                        category: document.getElementById('category').value,
                        description: document.getElementById('description').value
                    }]
                };

                try {
                    const response = await fetch('/cargo-project/backend/api/customer/create_request.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json();

                    if (result.success && result.payment_url) {
                        // Redirect to Chapa Payment Page
                        window.location.href = result.payment_url;
                    } else {
                        alert('Error creating request: ' + (result.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred.');
                }
            }

            // Init
            initMap();
            feather.replace();
        </script>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>