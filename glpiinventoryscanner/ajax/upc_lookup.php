<?php
include ('../../../inc/includes.php');

Session::checkLoginUser();

if (!isset($_GET['upc']) || empty($_GET['upc'])) {
    echo json_encode(['success' => false, 'error' => 'No UPC provided.']);
    exit;
}

$upc = $_GET['upc'];

// Retrieve API key from the plugin config table
global $DB;
$query = "SELECT api_key FROM glpi_plugin_inventoryscanner_config LIMIT 1";
$result = $DB->query($query);
$config = $DB->fetch_assoc($result);
$apiKey = $config['api_key'] ?? null;

if (!$apiKey) {
    echo json_encode(['success' => false, 'error' => 'API key not configured.']);
    exit;
}

// Query the UPC Database API
$apiUrl = "https://api.upcdatabase.org/product/$upc?apikey=$apiKey";
$response = file_get_contents($apiUrl);
if (!$response) {
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve data from UPC API.']);
    exit;
}

$data = json_decode($response, true);

if (!isset($data['success']) || !$data['success']) {
    echo json_encode(['success' => false, 'error' => 'UPC not found in database.']);
    exit;
}

// Extract relevant fields
$itemName = $data['title'] ?? '';
$category = $data['category'] ?? '';
$brand = $data['brand'] ?? '';
$productNumber = $data['mpn'] ?? '';
$description = $data['description'] ?? '';

// API usage limits
$apiLimits = [
    'lookups_remaining' => $data['requests_remaining'] ?? null,
    'reset_time' => $data['reset_time'] ?? null,
];

echo json_encode([
    'success' => true,
    'name' => $itemName,
    'category' => $category,
    'brand' => $brand,
    'mpn' => $productNumber,
    'description' => $description,
    'api_limits' => $apiLimits,
]);
exit;
