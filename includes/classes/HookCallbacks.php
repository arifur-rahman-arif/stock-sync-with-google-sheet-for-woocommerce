<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class HookCallbacks {

    public function loadBackendAssets() {
        $this->loadBackendScripts();
        $this->localizeFile();

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

    // Localize javascript files
    public function localizeFile() {
        wp_localize_script('WSMGS_admin', 'wsmgsLocal', [
            'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
            'wpNonce' => wp_create_nonce('wsmgs_nonce')
        ]);
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

    // Add admin menus to worpdress backend
    public function addAdminMenus() {
        add_menu_page(
            __('WooCommerce To Google Sheet', WSMGS_TEXT_DOMAIN),
            __('WooCommerce To Google Sheet', WSMGS_TEXT_DOMAIN),
            'manage_options',
            'wsmgs_page',
            [$this, 'dashboardTemplate'],
            'dashicons-database-export'
        );
    }

    public function dashboardTemplate() {
        load_template(WSMGS_BASE_PATH . 'templates/dashboard.php', true);
    }

    /**
     * @return mixed
     */
    public function settingsOption(): array{

        $settingsOption = [
            'sheetUrl',
            'tabName'
        ];

        return $settingsOption;
    }

    public function addMenuSettings() {

        $settinsOptions = $this->settingsOption();

        foreach ($settinsOptions as $setting) {
            register_setting(
                'wsmgs_general_setting',
                $setting
            );
        }

        $this->addSettingsFields();
    }

    public function addSettingsFields() {
        add_settings_section(
            'wsmgs_section_id',
            '',
            null,
            'wsmgs_page'
        );
        add_settings_field(
            'wsmgs_settings_field',
            "",
            [$this, 'loadFieldHTML'],
            'wsmgs_page',
            'wsmgs_section_id'
        );
    }

    public function loadFieldHTML() {
        load_template(WSMGS_BASE_PATH . 'templates/dashboard_settings.php', true);
    }

    // Register the rest route in wordpress to receive the sheet request and act accordingly
    public function registerRoute() {
        register_rest_route('wsmgs/v1', 'update-product', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handleRequest'],
            'permission_callback' => function () {return '';},
        ), true);
    }

    // Handle the post request from sheet
    /**
     * @param $request
     */
    public function handleRequest($request) {

        $requestBody = $request->get_body();

        $requestBody = json_decode($requestBody);

        wp_console_log($requestBody);

        return json_encode([
            'status'   => 200,
            'response' => 'Order created'
        ]);

    }

}