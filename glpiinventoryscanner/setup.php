<?php
include ('../../../inc/includes.php');

function plugin_inventoryscanner_install() {
    global $DB;

    // Create configuration table if it does not exist
    if (!$DB->tableExists('glpi_plugin_inventoryscanner_config')) {
        $query = "CREATE TABLE `glpi_plugin_inventoryscanner_config` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `api_key` VARCHAR(255) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->query($query) or die("Error creating configuration table: " . $DB->error());
    }
    
    // Create logging table if it does not exist
    if (!$DB->tableExists('glpi_plugin_inventoryscanner_logs')) {
        $query = "CREATE TABLE `glpi_plugin_inventoryscanner_logs` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `user_id` INT(11) NOT NULL,
                    `upc` VARCHAR(50) NOT NULL,
                    `item_name` VARCHAR(255) NOT NULL,
                    `category` VARCHAR(255) NOT NULL,
                    `serial_number` VARCHAR(255) DEFAULT NULL,
                    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $DB->query($query) or die("Error creating logging table: " . $DB->error());
    }
    
    return true;
}

function plugin_inventoryscanner_uninstall() {
    global $DB;
    
    // Remove configuration table
    $DB->query("DROP TABLE IF EXISTS `glpi_plugin_inventoryscanner_config`;")
        or die("Error removing configuration table: " . $DB->error());
    
    // Remove logging table
    $DB->query("DROP TABLE IF EXISTS `glpi_plugin_inventoryscanner_logs`;")
        or die("Error removing logging table: " . $DB->error());

    return true;
}