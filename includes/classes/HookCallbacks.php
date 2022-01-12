<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class HookCallbacks {

    public function loadBackendAssets() {
        $this->loadBackendScripts();

        // If the plugin is in production mode than include the style files
        if (WSMGS_PlUGIN_MODE === 'prod') {
            $this->loadBackendStyles();
        }
    }

    // Load all the scripts for admin panal
    public function loadBackendScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('WSMGS_admin', WSMGS_BASE_URL . 'assets/public/scripts/backend.min.js', ['jquery'], WSMGS_VERSION, true);
    }

    // Load all the style files for admin panal
    public function loadBackendStyles() {
        # code...
    }

    // Load all the scripts for frontend
    public function loadFrontendScripts() {
        # code...
    }

    // Load all the styles for frontend
    public function loadFrontendStyles() {
        # code...
    }
}