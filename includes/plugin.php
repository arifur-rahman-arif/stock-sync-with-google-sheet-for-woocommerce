<?php

namespace WSMGS;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

use WSMGS\classes\Filters;
use WSMGS\classes\Hooks;

class Plugin {
    public function __construct() {

        $this->includeGlobalObjects();

        $this->includeHooks();

        $this->includeFilters();

    }

    // Include the global class so its methods will be used across the plugin
    public function includeGlobalObjects() {
    }

    // Include all the hooks for this plugin
    public function includeHooks() {
        new Hooks;
    }

    // Include all the filters for this plugin
    public function includeFilters() {
        new Filters;
    }

}