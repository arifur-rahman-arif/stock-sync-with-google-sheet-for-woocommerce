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

        try {
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
            $this->methods = new GlobalClass();

            $products = $this->getProducts();

            $insertionValues = $this->methods->organizeInsertionValues($products);

            // wp_console_log($insertionValues);

            $this->initExport();

        } catch (\Throwable $error) {
            $output['message'] = $error->getMessage();
            wp_send_json_error($this->output, $error->getMessage());
            wp_die();
        }

    }

    // Initialize the export of products in google sheet
    public function initExport() {
        $this->addSheetColumn();
        // $this->insertProducts();
    }

    /**
     * Add the defined column in google sheet if first row is not found
     */
    public function addSheetColumn() {

        $args = [
            'sheetId' => $this->methods->getSheetId(get_option('sheetURL')),
            'tabName' => get_option('tabName')
        ];

        $columns = $this->methods->sheetColumns();

        $sheetColumn = $this->methods->getSheetColumn($args);

        if (is_array($sheetColumn)) {
            $sheetColumn = array_merge(...$sheetColumn);
        }

        // if column is empty or column value is less then 8 then update the column
        if (empty($sheetColumn) || count($sheetColumn) < count($columns)) {

            $args['values'] = [$columns];

            $this->methods->updateColumn($args);
        }

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

    /**
     * @return mixed
     */
    public function getProducts() {

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => -1
            // 'post_status'    => 'publish'
        ];

        $products = get_posts($args);

        return $products;
    }

}