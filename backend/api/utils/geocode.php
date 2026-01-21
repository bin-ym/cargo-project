<?php
header('Content-Type: application/json');

$query = $_GET['q'] ?? null;
$type  = $_GET['type'] ?? 'search';

if (!$query) {
    echo json_encode(['success' => false, 'error' => 'Missing query']);
    exit;
}

$email = 'your-email@example.com'; // REQUIRED by Nominatim
$base  = 'https://nominatim.openstreetmap.org/';

$url = $type === 'reverse'
    ? $base . "reverse?format=json&lat={$query['lat']}&lon={$query['lng']}&email=$email&addressdetails=1"
    : $base . "search?format=json&q=" . urlencode($query) . "&countrycodes=et&limit=5&email=$email&addressdetails=1";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => 'CargoSystem/1.0'
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($type === 'reverse') {
    // Reverse Geocoding returns a single object
    if (isset($data['address']['country_code']) && strtolower($data['address']['country_code']) !== 'et') {
        // Not in Ethiopia
        echo json_encode(['error' => 'Location outside Ethiopia']);
    } else {
        echo json_encode($data);
    }
} else {
    // Search returns an array
    if (is_array($data)) {
        $filtered = array_filter($data, function($item) {
            return isset($item['address']['country_code']) && strtolower($item['address']['country_code']) === 'et';
        });
        echo json_encode(array_values($filtered));
    } else {
        echo json_encode([]);
    }
}
