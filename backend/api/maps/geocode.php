<?php
header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'search';

if (!$query && $type === 'search') {
    echo json_encode(['error' => 'Missing query']);
    exit;
}

$email = "your-email@example.com"; // REQUIRED by Nominatim

if ($type === 'search') {
    $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q="
         . urlencode($query)
         . "&email=" . urlencode($email);
} else {
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon";
}

$opts = [
    "http" => [
        "header" => "User-Agent: CargoSystem/1.0\r\n"
    ]
];

$context = stream_context_create($opts);
$response = file_get_contents($url, false, $context);

echo $response ?: json_encode(['error' => 'Failed']);
