<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

use WSMGS\classes\HookCallbacks;

class Hooks {

    /**
     * @var mixed
     */
    public $hookCallbacks;

    public function __construct() {
        $this->assignClasses();
        $this->enqueueHooks();
        $this->actionHooks();
    }

    // Assign required classes/methods in its properties
    public function assignClasses() {
        $this->hookCallbacks = new HookCallbacks;
    }

    public function actionHooks() {

        // Add the admin menu for plugin in wp dashborad
        add_action('admin_menu', [$this->hookCallbacks, 'addAdminMenus']);
        add_action('admin_init', [$this->hookCallbacks, 'addMenuSettings']);

        // Register the rest route to recive webhook request from the sheet
        add_action('rest_api_init', [$this->hookCallbacks, 'registerRoute']);

        // Update product in google sheet when user update products in WordPress
        add_action('save_post_product', [$this->hookCallbacks, 'updateSheetProduct']);
    }

    public function enqueueHooks() {
        add_action('admin_enqueue_scripts', [$this->hookCallbacks, 'loadBackendAssets']);
    }
}