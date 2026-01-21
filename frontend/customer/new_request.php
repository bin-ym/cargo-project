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
    .spinner-small { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; margin-right: 8px; vertical-align: middle; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="customer-layout">
    <?php include __DIR__ . '/../layout/navbar_customer.php'; ?>
    <main class="main-content" style="padding: 30px 5%; max-width: 1200px; margin: 0 auto;">
        <header class="topbar" style="margin-bottom: 30px;">
            <h2><?= __('new_cargo_request') ?></h2>
            <div class="user-info">
                <span class="badge badge-primary"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 1200px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
                    
                    <!-- Left: Form -->
                    <div>
                        <h3><?= __('request_details') ?></h3>
                        <form id="requestForm" style="margin-top: 20px;">
                            <!-- Hidden Fields for Coordinates -->
                            <input type="hidden" id="pickup_lat" name="pickup_lat">
                            <input type="hidden" id="pickup_lng" name="pickup_lng">
                            <input type="hidden" id="dropoff_lat" name="dropoff_lat">
                            <input type="hidden" id="dropoff_lng" name="dropoff_lng">
                            <input type="hidden" id="distance_km" name="distance_km">
                            <input type="hidden" id="calculated_price" name="price">

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('pickup_location') ?></label>
                                <div class="input-group">
                                    <input type="text" id="pickup_location" placeholder="<?= __('type_address_click_map') ?>" 
                                        onchange="geocodeAddress(this.value, 'pickup')"
                                        style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <i data-feather="search" class="input-icon" onclick="geocodeAddress(document.getElementById('pickup_location').value, 'pickup')"></i>
                                </div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('dropoff_location') ?></label>
                                <div class="input-group">
                                    <input type="text" id="dropoff_location" placeholder="<?= __('type_address_click_map') ?>" 
                                        onchange="geocodeAddress(this.value, 'dropoff')"
                                        style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <i data-feather="search" class="input-icon" onclick="geocodeAddress(document.getElementById('dropoff_location').value, 'dropoff')"></i>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('pickup_date') ?></label>
                                <input type="date" id="pickup_date" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                            </div>
                            
                            <h4 style="margin: 30px 0 15px; color: #0f172a;"><?= __('cargo_items') ?></h4>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('item_name') ?></label>
                                <input type="text" id="item_name" placeholder="<?= __('electronics_placeholder') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('quantity') ?></label>
                                    <input type="number" id="quantity" value="1" min="1" onchange="calculatePrice()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('weight_kg') ?></label>
                                    <input type="number" id="weight" placeholder="<?= __('weight_placeholder') ?>" min="0.1" step="0.1" onchange="calculatePrice()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                                </div>
                            </div>
                            
                            <!-- Vehicle Type Selection -->
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('vehicle_type') ?></label>
                                <select id="vehicle_type" onchange="calculatePrice()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <option value="pickup"><?= __('pickup_small') ?></option>
                                    <option value="isuzu"><?= __('isuzu_medium') ?></option>
                                    <option value="trailer"><?= __('trailer_large') ?></option>
                                </select>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('category') ?></label>
                                <select id="category" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                    <option value="Electronics"><?= __('electronics') ?></option>
                                    <option value="Furniture"><?= __('furniture') ?></option>
                                    <option value="Hardware"><?= __('hardware') ?></option>
                                    <option value="Other"><?= __('other') ?></option>
                                </select>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;"><?= __('description') ?></label>
                                <textarea id="description" rows="3" placeholder="<?= __('additional_details') ?>" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Right: Map & Price -->
                    <div style="position: sticky; top: 20px;">
                        <div id="map"></div>
                        <div style="text-align: center; margin-bottom: 10px; color: #64748b; font-size: 0.9rem;">
                            <?= __('map_instruction') ?>
                        </div>

                        <div class="price-card">
                            <h4><?= __('estimated_cost') ?></h4>
                            <div class="price-row">
                                <span><?= __('distance') ?></span>
                                <span id="disp_distance">0 km</span>
                            </div>
                            <div class="price-row">
                                <span><?= __('weight') ?></span>
                                <span id="disp_weight">0 kg</span>
                            </div>
                            <div class="price-row price-total">
                                <span><?= __('total') ?></span>
                                <span id="disp_price">0 ETB</span>
                            </div>
                            
                            <button type="button" id="submitBtn" onclick="submitRequest()" class="btn btn-primary" style="width: 100%; background: #16a34a; border-color: #16a34a; margin-top: 20px;">
                                <?= __('submit_request') ?>
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
                        alert("<?= __('location_not_found') ?>");
                    }
                } catch (err) {
                    console.error(err);
                    alert("<?= __('location_search_failed') ?>");
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
                pickupMarker = L.marker(latlng, {icon: createIcon('green')}).addTo(map).bindPopup("<?= __('pickup_location_popup') ?>").openPopup();
                
                document.getElementById('pickup_lat').value = latlng.lat;
                document.getElementById('pickup_lng').value = latlng.lng;
                
                if (fetchAddress) {
                    document.getElementById('pickup_location').value = "<?= __('loading') ?>";
                    reverseGeocode(latlng.lat, latlng.lng, 'pickup');
                }
                
                calculateDistance();
            }

            function setDropoff(latlng, fetchAddress = false) {
                if (dropoffMarker) map.removeLayer(dropoffMarker);
                dropoffMarker = L.marker(latlng, {icon: createIcon('red')}).addTo(map).bindPopup("<?= __('dropoff_location_popup') ?>").openPopup();
                
                document.getElementById('dropoff_lat').value = latlng.lat;
                document.getElementById('dropoff_lng').value = latlng.lng;
                
                if (fetchAddress) {
                    document.getElementById('dropoff_location').value = "<?= __('loading') ?>";
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

            async function calculateDistance() {
                if (!pickupMarker || !dropoffMarker) return;

                const p = pickupMarker.getLatLng();
                const d = dropoffMarker.getLatLng();
                
                // OSRM API (Open Source Routing Machine)
                // Using the public demo server (Note: For production, host your own OSRM or use a paid service)
                const url = `https://router.project-osrm.org/route/v1/driving/${p.lng},${p.lat};${d.lng},${d.lat}?overview=full&geometries=geojson`;

                try {
                    document.getElementById('disp_distance').innerText = "<?= __('calculating') ?>";
                    
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.code === 'Ok' && data.routes.length > 0) {
                        const route = data.routes[0];
                        const distKm = (route.distance / 1000).toFixed(2); // Convert meters to km
                        
                        document.getElementById('distance_km').value = distKm;
                        document.getElementById('disp_distance').innerText = distKm + " km";

                        // Draw Route
                        if (routeLine) map.removeLayer(routeLine);
                        
                        // Flip coordinates for Leaflet (GeoJSON is Lng,Lat; Leaflet is Lat,Lng)
                        const latLngs = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                        
                        routeLine = L.polyline(latLngs, {color: 'blue', weight: 5, opacity: 0.7}).addTo(map);
                        map.fitBounds(routeLine.getBounds(), {padding: [50, 50]});
                        
                        calculatePrice();
                    } else {
                        alert("<?= __('no_road_route') ?>");
                    }
                } catch (error) {
                    console.error("Routing error:", error);
                    alert("<?= __('error_calculating_route') ?>");
                }
            }

            async function calculatePrice() {
    const distance = parseFloat(document.getElementById('distance_km').value) || 0;
    const weight = parseFloat(document.getElementById('weight').value) || 0;
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const vehicleType = document.getElementById('vehicle_type').value;
    const pickupDate = document.getElementById('pickup_date').value;

    if (distance <= 0 || weight <= 0 || !pickupDate) return;

    document.getElementById('disp_price').innerText = "<?= __('calculating') ?>";

    try {
        const res = await fetch('/cargo-project/backend/api/customer/calculate_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                distance_km: distance,
                weight: weight,
                quantity: quantity,
                vehicle_type: vehicleType,
                pickup_date: pickupDate
            })
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById('calculated_price').value = data.price;
            document.getElementById('disp_weight').innerText = weight + " kg";
            document.getElementById('disp_price').innerText = data.price + " ETB";
        } else {
            console.error(data.error);
            document.getElementById('disp_price').innerText = "Error";
        }
    } catch (err) {
        console.error(err);
        document.getElementById('disp_price').innerText = "Error";
    }
}

            async function submitRequest() {
                // 1. Validate Form
                const required = ['pickup_lat', 'dropoff_lat', 'pickup_date', 'item_name', 'weight'];
                for (let id of required) {
                    if (!document.getElementById(id).value) {
                        alert("<?= __('fill_all_fields') ?>");
                        return;
                    }
                }

                const btn = document.getElementById('submitBtn');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="spinner-small"></i> <?= __('processing') ?>';

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
                        btn.innerHTML = '<i class="spinner-small"></i> <?= __('redirecting_to_payment') ?>';
                        // Redirect to Chapa Payment Page
                        window.location.href = result.payment_url;
                    } else {
                        alert('Error creating request: ' + (result.error || 'Unknown error'));
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }

            // Init
            initMap();
            feather.replace();
        </script>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer_customer.php'; ?>