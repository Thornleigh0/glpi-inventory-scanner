<?php
/**
 * Plugin Name: GLPI Inventory Scanner
 * Author: Brian Cook
 * Description: A plugin to quickly scan and add inventory using UPC codes.
 * Version: 1.0
 */

// Security check to prevent direct access
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");

// Plugin initialization
function plugin_init_inventoryscanner() {
    global $PLUGIN_HOOKS;
    
    // Add menu entry
    $PLUGIN_HOOKS['menu_entry']['inventoryscanner'] = 'front/inventoryscanner_page.php';
    
    // Add permissions
    $PLUGIN_HOOKS['csrf_compliant']['inventoryscanner'] = true;
    $PLUGIN_HOOKS['add_javascript']['inventoryscanner'] = 'scripts/upc_scanner.js';
}

// Declare plugin version and dependencies
function plugin_version_inventoryscanner() {
    return [
        'name'           => 'GLPI Inventory Scanner',
        'version'        => '1.0.0',
        'author'         => 'Brian Cook',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/Thornleigh0/glpi-inventory-scanner',
        'minGlpiVersion' => '10.0',
    ];
}

// Define the main plugin class
class PluginInventoryscanner extends Plugin {
    public static function getMenuContent() {
        return [
            'title' => __('Inventory Scanner', 'inventoryscanner'),
            'page'  => '/plugins/inventoryscanner/front/inventoryscanner_page.php',
            'icon'  => 'fas fa-barcode',
        ];
    }
}
?>
