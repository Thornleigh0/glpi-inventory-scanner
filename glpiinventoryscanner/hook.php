<?php
function plugin_init_inventoryscanner() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['inventoryscanner'] = true;

    if (Session::haveRight("config", UPDATE)) {
        $PLUGIN_HOOKS['config_page']['inventoryscanner'] = 'front/config.php';
    }

    $PLUGIN_HOOKS['menu_toadd']['inventoryscanner'] = ['tools' => 'front/inventoryscanner_page.php'];

    // Permissions
    $PLUGIN_HOOKS['add_javascript']['inventoryscanner'] = 'scripts/upc_scanner.js';
}

function plugin_version_inventoryscanner() {
    return [
        'name'           => "Inventory Scanner",
        'version'        => '1.0.0',
        'author'         => 'Your Name',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/Thornleigh0/glpi-inventory-scanner',
        'minGlpiVersion' => '9.5'
    ];
}

function plugin_inventoryscanner_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '9.5', 'lt')) {
        echo "This plugin requires GLPI 9.5 or later";
        return false;
    }
    return true;
}

function plugin_inventoryscanner_check_config() {
    return true;
}