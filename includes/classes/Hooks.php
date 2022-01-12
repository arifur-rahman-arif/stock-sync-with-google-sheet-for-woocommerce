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
    }

    // Assign required classes/methods in its properties
    public function assignClasses() {
        $this->hookCallbacks = new HookCallbacks;
    }

    public function enqueueHooks() {
        add_action('admin_enqueue_scripts', [$this->hookCallbacks, 'loadBackendAssets']);
    }
}