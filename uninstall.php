<?php

defined('WP_UNINSTALL_PLUGIN') || wp_die(__('You can\'t access this page', 'wsmgs'));

class WooCommerceStockManagementWithSheet {
    public function __construct() {
        $this->deleteOptions();
    }

    public function deleteOptions() {
        $savedOptions = [
            'wsmgsToken',
            'configureMode',
            'sheetUrl',
            'tabName'
        ];

        foreach ($savedOptions as $option) {
            delete_option($option);
        }
    }
}

if (!class_exists('WooCommerceStockManagementWithSheet')) {
    return;
}

new WooCommerceStockManagementWithSheet();