<?php
include ('../../../inc/includes.php');

header('Content-Type: application/json');
Session::checkLoginUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['name'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

$name = $data['name'];
$category = strtolower($data['category'] ?? '');
$serialNumbers = is_array($data['serialNumbers']) ? array_filter($data['serialNumbers']) : [$data['serialNumbers']];
$userId = $_SESSION['glpiID'];
$upc = $data['upc'] ?? '';
$description = $data['description'] ?? '';
$brand = $data['brand'] ?? '';
$productNumber = $data['mpn'] ?? '';

// Ensure a category is selected
if (empty($category)) {
    echo json_encode(['success' => false, 'error' => 'Category is required. Please select a category from the dropdown.']);
    exit;
}

// Determine the correct model table based on asset type
$modelTableMap = [
    'computer' => 'glpi_computermodels',
    'monitor' => 'glpi_monitormodels',
    'printer' => 'glpi_printermodels',
    'network' => 'glpi_networkequipmentmodels',
    'peripheral' => 'glpi_peripheralmodels',
    'phone' => 'glpi_phonemodels',
    'software' => 'glpi_softwaremodels'
];

$modelTable = $modelTableMap[$category] ?? null;
if (!$modelTable) {
    echo json_encode(['success' => false, 'error' => 'Invalid category.']);
    exit;
}

// Check if manufacturer exists and get its ID
$manufacturerId = 0;
if (!empty($brand)) {
    $query = "SELECT id FROM glpi_manufacturers WHERE name = '" . $DB->escape($brand) . "' LIMIT 1";
    $result = $DB->query($query);
    if ($DB->numrows($result) > 0) {
        $manufacturer = $DB->fetch_assoc($result);
        $manufacturerId = $manufacturer['id'];
    } else {
        $DB->query("INSERT INTO glpi_manufacturers (name) VALUES ('" . $DB->escape($brand) . "')");
        $manufacturerId = $DB->insert_id();
    }
}

// Check if a model with the same UPC already exists
$query = "SELECT id, name, upc, comment, product_number, manufacturers_id FROM `$modelTable` WHERE upc = '" . $DB->escape($upc) . "' LIMIT 1";
$result = $DB->query($query);
if ($DB->numrows($result) > 0) {
    $existingModel = $DB->fetch_assoc($result);
    $modelId = $existingModel['id'];
} else {
    $DB->query("INSERT INTO `$modelTable` (name, upc, comment, product_number, manufacturers_id) VALUES ('" . $DB->escape($name) . "', '" . $DB->escape($upc) . "', '" . $DB->escape($description) . "', '" . $DB->escape($productNumber) . "', '$manufacturerId')");
    $modelId = $DB->insert_id();
}

// Now link multiple assets to the model
$assetTableMap = [
    'computer' => 'glpi_computers',
    'monitor' => 'glpi_monitors',
    'printer' => 'glpi_printers',
    'network' => 'glpi_networkequipments',
    'peripheral' => 'glpi_peripherals',
    'phone' => 'glpi_phones',
    'software' => 'glpi_software'
];

$assetTable = $assetTableMap[$category] ?? null;
if (!$assetTable) {
    echo json_encode(['success' => false, 'error' => 'Invalid asset type.']);
    exit;
}

$assetIds = [];
foreach ($serialNumbers as $serialNumber) {
    $DB->query("INSERT INTO `$assetTable` (name, serial, users_id, groups_id, models_id) 
                 VALUES ('" . $DB->escape($name) . "', '" . $DB->escape($serialNumber) . "', '$userId', '0', '$modelId')");
    $assetIds[] = $DB->insert_id();
}

echo json_encode(['success' => true, 'model_id' => $modelId, 'asset_ids' => $assetIds]);
exit;
