<?php
namespace WSMGS\classes\ajax\callbacks;

use WSMGS\classes\GlobalClass;

class ExportProducts {

    /**
     * @var array
     */
    public $output = [
        'status'  => 'invalid',
        'message' => "Server error"
    ];

    /**
     * @var mixed
     */
    public $reqData;

    /**
     * @var mixed
     */
    public $methods;

    public function exportProducts() {

        if (sanitize_text_field($_POST['action']) !== 'wsmgs_export_product') {
            $output['status'] = 'invalid';
            $output['message'] = 'Action is not valid';
            wp_send_json_error($output, 400);
            wp_die();
        }

        $this->reqData = $this->sanitizeData($_POST);

        if (!wp_verify_nonce($this->reqData['wpNonce'], 'wsmgs_nonce')) {
            $output['status'] = 'invalid';
            $output['message'] = 'Invalid nonce';
            wp_send_json_error($output, 403);
            wp_die();
        };

        // Assigne the global class to use its methods
        $this->methods = new GlobalClass;

        wp_send_json_error($this->output, 400);
        wp_die();
    }

    /**
     * @param  array   $nonSanitzedData
     * @return mixed
     */
    public function sanitizeData(array $nonSanitzedData) {
        $sanitizedData = null;

        $sanitizedData = array_map(function ($data) {
            if (gettype($data) == 'array') {
                return $this->sanitizeData($data);
            } else {
                return sanitize_text_field($data);
            }
        }, $nonSanitzedData);

        return $sanitizedData;
    }

    public function getProducts() {

    }

    /**
     * @param $products
     * @return mixed
     */
    public function organizeInsertionValues($products) {
        $organizedData = [];

        if (!$products) {
            return $organizedData;
        }

        foreach ($products as $key => $product) {

            $wcProduct = wc_get_product($product->ID);

            array_push($organizedData, [
                $product->ID, /* Product ID */
                $product->post_title, /*  Product Name */
                $product->post_content, /* Description */
                $wcProduct->get_regular_price(), /* Price */
                $wcProduct->get_sale_price(), /* Discount Price */
                $wcProduct->get_sku(), /* SKU */
                $wcProduct->get_stock_quantity() /* Stock Quantity */
            ]);
        }

        return $organizedData;
    }
}