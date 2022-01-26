<?php

namespace WSMGS;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

use WSMGS\classes\ajax\Hooks as AjaxHooks;
use WSMGS\classes\Filters;
use WSMGS\classes\Hooks;

class Plugin {
    public function __construct() {

        $this->includeHooks();

        $this->includeFilters();
    }

    // Include all the hooks for this plugin
    public function includeHooks() {
        new Hooks;
        new AjaxHooks;
    }

    // Include all the filters for this plugin
    public function includeFilters() {
        new Filters;
    }

}