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
    ? $base . "reverse?format=json&lat={$query['lat']}&lon={$query['lng']}&email=$email"
    : $base . "search?format=json&q=" . urlencode($query) . "&limit=1&email=$email";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => 'CargoSystem/1.0'
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
