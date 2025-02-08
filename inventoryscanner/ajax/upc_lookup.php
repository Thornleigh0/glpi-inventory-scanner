<?php
include ('../../../inc/includes.php');

header('Content-Type: application/json');

if (!isset($_GET['upc']) || empty($_GET['upc'])) {
    echo json_encode(['success' => false, 'error' => 'No UPC provided.']);
    exit;
}

$upc = trim($_GET['upc']);
$apiKey = 'YOUR_UPCDATABASE_API_KEY';
$apiUrl = "https://api.upcdatabase.org/product/$upc?apikey=$apiKey";

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: GLPI-InventoryScanner\r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($apiUrl, false, $context);
$data = json_decode($response, true);

// Extract API limit headers
$apiLimits = [
    'remaining' => null,
    'reset' => null
];
foreach ($http_response_header as $header) {
    if (stripos($header, 'X-RateLimit-Remaining:') !== false) {
        $apiLimits['remaining'] = (int)trim(explode(':', $header)[1]);
    } elseif (stripos($header, 'X-RateLimit-Reset:') !== false) {
        $apiLimits['reset'] = (int)trim(explode(':', $header)[1]);
    }
}

if (!$data || !isset($data['title'])) {
    echo json_encode([
        'success' => false,
        'error' => 'UPC not found.',
        'api_limits' => $apiLimits
    ]);
    exit;
}

$result = [
    'success' => true,
    'name' => $data['title'] ?? '',
    'category' => $data['category'] ?? '',
    'api_limits' => $apiLimits
];

echo json_encode($result);
exit;
