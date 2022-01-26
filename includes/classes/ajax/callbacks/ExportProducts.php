<?php
namespace WSMGS\classes\ajax\callbacks;

use WSMGS\classes\GlobalClass;

class ExportProducts {

    /**
     * @var array
     */
    public $output = [
        'status'  => 'error',
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
                $this->output['status'] = 'error';
                $this->output['message'] = esc_html__('Action is not valid', WSMGS_TEXT_DOMAIN);
                wp_send_json_error($this->output, 400);
                wp_die();
            }

            // Assigne the global class to use its methods
            $this->methods = new GlobalClass();

            $this->reqData = $this->methods->sanitizeData($_POST);

            if (!wp_verify_nonce($this->reqData['wpNonce'], 'wsmgs_nonce')) {
                $this->output['status'] = 'error';
                $this->output['message'] = esc_html__('Invalid nonce', WSMGS_TEXT_DOMAIN);
                wp_send_json_error($this->output, 403);
                wp_die();
            };

            $this->initExport();

        } catch (\Throwable $error) {
            $this->output['message'] = $error->getMessage();
            wp_send_json_error($this->output, $error->getCode());
            wp_die();
        }

    }

    // Initialize the export of products in google sheet
    public function initExport() {
        $this->addSheetColumn();
        $this->insertProducts();
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
        $columns = array_values($columns);

        $sheetColumn = $this->methods->getSheetColumn($args);

        if (is_array($sheetColumn)) {
            $sheetColumn = array_merge(...$sheetColumn);
        }

        // if column is empty or column value is less then 8 then update the column
        if (empty($sheetColumn) || count($sheetColumn) < count($columns)) {

            $args['values'] = [$columns];

            $response = $this->methods->updateColumn($args);

            return $response;
        }

    }

    public function insertProducts() {

        $products = $this->getProducts();

        $insertionValues = $this->methods->organizeInsertionValues($products);

        $productCount = count($insertionValues);

        $args = [
            'sheetId' => $this->methods->getSheetId(get_option('sheetUrl')),
            'tabName' => get_option('tabName'),
            'values'  => $insertionValues
        ];

        try {

            $response = $this->methods->insertData($args);

            if ($response) {
                $this->output['status'] = 'success';

                $this->output['message'] = esc_html__($productCount . ' products exported in google sheet', WSMGS_TEXT_DOMAIN);

                if ($productCount === 0) {
                    $this->output['message'] = esc_html__('All products are already found in google sheet.', WSMGS_TEXT_DOMAIN);
                }

                wp_send_json_success($this->output, 201);
                wp_die();
            } else {
                $this->output['status'] = 'error';
                $this->output['message'] = esc_html__('Products could not be inserted. Try again', WSMGS_TEXT_DOMAIN);
                wp_send_json_error($this->output, 400);
                wp_die();
            }

        } catch (\Throwable $error) {
            $this->output['message'] = $error->getMessage();
            wp_send_json_error($this->output, $error->getCode());
        }
    }

    /**
     * @return mixed
     */
    public function getProducts() {

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => -1
        ];

        $products = get_posts($args);

        return $products;
    }

}