<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class HookCallbacks {

    public function loadBackendAssets() {
        $this->loadExternalCss();
        $this->loadBackendScripts();
        $this->localizeFile();

        // If the plugin is in production mode than include the style files
        if (WSMGS_PlUGIN_MODE === 'prod') {
            $this->loadBackendStyles();
        }
    }

    public function loadExternalCss() {
        wp_enqueue_style('WSMGS_prism', WSMGS_BASE_URL . 'assets/public/syntex-highlight/prism.css', [], WSMGS_VERSION, 'all');
    }

    // Load all the scripts for admin panal
    public function loadBackendScripts() {
        wp_enqueue_script('jquery');

        wp_enqueue_script('WSMGS_prism', WSMGS_BASE_URL . 'assets/public/syntex-highlight/prism.js', [], WSMGS_VERSION, true);

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
            'wsmgs-page',
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
            'wsmgs-page'
        );
        add_settings_field(
            'wsmgs_settings_field',
            "",
            [$this, 'loadFieldHTML'],
            'wsmgs-page',
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

    /**
     * Handle the post request from sheet
     * @param $request
     */
    public function handleRequest($request) {

        try {

            $requestBody = $request->get_body();

            $requestBody = json_decode($requestBody);

            $token = $requestBody->token;
            $reqData = $requestBody->reqData;

            if ($token !== get_option('wsmgsToken')) {
                return wp_send_json_error([
                    'status'  => 'error',
                    'message' => esc_html__('Token is not valid', WSMGS_TEXT_DOMAIN)
                ], 401);
            }

            return $this->updateProducts($reqData);

        } catch (\Throwable $error) {
            return wp_send_json_error([
                'status'  => 'error',
                'message' => esc_html__($error->getMessage(), WSMGS_TEXT_DOMAIN)
            ], $error->getCode());
        }
    }

    /**
     * Update woocommerce products based on their id & data
     * @param array $reqData
     */
    public function updateProducts(array $reqData) {

        if (count($reqData) < 1 || !$reqData) {
            return wp_send_json_error([
                'status'  => 'error',
                'message' => esc_html__('No data found to update', WSMGS_TEXT_DOMAIN)
            ], 404);
        }

        $updatedProducts = [];

        foreach ($reqData as $key => $data) {

            $isProductUpdated = false;

            $productID = property_exists($data, 'id') ? $data->id : null;
            $type = property_exists($data, 'type') ? $data->type : null;
            $sku = property_exists($data, 'sku') ? $data->sku : null;
            $name = property_exists($data, 'name') ? $data->name : null;
            $published = property_exists($data, 'published') ? $data->published : null;
            $stock = property_exists($data, 'stock') ? $data->stock : null;
            $salePrice = property_exists($data, 'salePrice') ? $data->salePrice : null;
            $regularPrice = property_exists($data, 'regularPrice') ? $data->regularPrice : null;

            if (!$productID) {
                continue;
            }

            $woocmmerceInstance = wc_get_product($productID);

            if (!$woocmmerceInstance) {
                continue;
            }

            // Update the product sku
            if ($sku) {
                if (update_post_meta($productID, '_sku', $sku)) {
                    $isProductUpdated = true;
                };
            }

            // Update the product name
            if ($name) {
                $args = [
                    'ID'         => $productID,
                    'post_type'  => 'product',
                    'post_title' => $name
                ];

                if (!is_wp_error(wp_update_post($args))) {
                    $isProductUpdated = true;
                };
            }

            $postStatus = ['publish', 'draft'];

            // Update the product status
            if (in_array($published, $postStatus)) {
                $args = [
                    'ID'          => $productID,
                    'post_type'   => 'product',
                    'post_status' => $published
                ];

                if (!is_wp_error(wp_update_post($args))) {
                    $isProductUpdated = true;
                };
            }

            // Update the product stock quantity
            if ($stock) {
                $quantity = $stock;

                $woocmmerceInstance->set_stock_quantity($quantity);

                if ($quantity == 0) {
                    $woocmmerceInstance->set_stock_status('outofstock');
                }

                $woocmmerceInstance->save();

                $isProductUpdated = true;
            }

            // Update the product sale price
            if ($salePrice) {
                $woocmmerceInstance->set_sale_price($salePrice);
                if ($woocmmerceInstance->save()) {
                    $isProductUpdated = true;
                };
            }

            // Update the product reguler price
            if ($regularPrice) {
                $woocmmerceInstance->set_regular_price($regularPrice);
                if ($woocmmerceInstance->save()) {
                    $isProductUpdated = true;
                };
            }

            // If any of the field is updated than push that product ID to return value
            if ($isProductUpdated) {
                array_push($updatedProducts, $productID);
            }

        }

        // If there are no product id found in array that means no product updated
        if (count($updatedProducts) < 1) {
            return wp_send_json_error([
                'status'  => 'error',
                'message' => esc_html__('No data updated', WSMGS_TEXT_DOMAIN)
            ], 400);
        }

        return wp_send_json_success([
            'status'  => 'success',
            'message' => esc_html__(count($updatedProducts) . ' product updated', WSMGS_TEXT_DOMAIN)
        ], 200);
    }

}