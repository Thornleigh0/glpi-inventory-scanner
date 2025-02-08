<?php
include ('../../../inc/includes.php');

Session::checkLoginUser();

// Ensure user has at least READ rights
if (!Session::haveRight("plugin_inventoryscanner", READ)) {
    Html::displayRightError();
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="inventoryscanner_log.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['User ID', 'UPC', 'Item Name', 'Category', 'Serial Number', 'Timestamp']);

$query = "SELECT user_id, upc, item_name, category, serial_number, timestamp FROM glpi_plugin_inventoryscanner_logs";
$result = $DB->query($query);

while ($row = $DB->fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
