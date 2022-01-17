<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class GlobalClass {

    /**
     * Organize the WooCommerce products for exporting to google sheet
     * @param  $products
     * @return mixed
     */
    public function organizeInsertionValues(array $products) {
        $organizedData = [];

        if (!$products) {
            return $organizedData;
        }

        foreach ($products as $key => $product) {

            $wcProduct = wc_get_product($product->ID);

            array_push($organizedData, [
                $product->ID, /* Column A*/

                $wcProduct->get_type(), /* Column B*/

                $wcProduct->get_sku(), /* Column C*/

                $product->post_title, /* Column D*/

                $product->post_status, /*  Column E*/

                $wcProduct->get_stock_quantity(), /* Column F*/

                $wcProduct->get_sale_price(), /* Column G*/

                $wcProduct->get_regular_price() /* Column H*/

            ]);

            if ($wcProduct->get_type() === 'variable') {
                $variations = $wcProduct->get_available_variations();

                if (is_array($variations) && count($variations) > 0) {
                    foreach ($variations as $key => $value) {
                        //get values HERE
                    }
                }
            }
        }

        return $organizedData;
    }

    /**
     * Get the google client to operate on google sheet
     * @return mixed
     */
    public function getClient() {

        try {
            $client = new \Google_Client();
            $client->setApplicationName('Woocommerce To Sheet');
            $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
            $client->setAuthConfig(WSMGS_BASE_PATH . 'client.json');
            $client->setAccessType('offline');
            return $client;
        } catch (\Throwable $error) {
            return false;
        }

    }

    /**
     * Define the sheet columns
     * @return mixed
     */
    public function sheetColumns() {

        $columns = [
            'ID',
            'Type',
            'SKU',
            'Name',
            'Published',
            'Stock',
            'Sale price',
            'Regular price'
        ];

        return $columns;
    }

    /**
     * Get the sheet column row (the 1st row) from sheet
     * @param  array   $args
     * @return mixed
     */
    public function getSheetColumn(array $args) {

        if (!isset($args['sheetId']) || !$args['sheetId']) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['tabName']) || !$args['tabName']) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        $client = $this->getClient();
        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $args['sheetId'];
        $range = '' . $args['tabName'] . '!A1:H1';
        $requestBody = new \Google_Service_Sheets_ValueRange();
        $requestBody->setMajorDimension('COLUMNS');
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        return $values;
    }

    /**
     * Update the google sheet column names
     * @param  $args
     * @return mixed
     */
    public function updateColumn($args) {

        if (!isset($args['sheetId']) || !$args['sheetId']) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['tabName']) || !$args['tabName']) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        $client = $this->getClient();

        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $args['sheetId'];
        $range = '' . $args['tabName'] . '!A1:H1';
        $requestBody = new \Google_Service_Sheets_ValueRange();
        $requestBody->setMajorDimension('ROWS');
        $requestBody->setValues(
            $args['values']
        );

        $response = $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $requestBody,
            [
                'valueInputOption'          => 'USER_ENTERED',
                'responseValueRenderOption' => 'FORMATTED_VALUE'
            ]
        );

        return $response;
    }

    /**
     * Extract the sheet ID from sheet url
     * @param  string  $url
     * @return mixed
     */
    public function getSheetId(string $url) {
        $pattern = "/\//";
        $components = preg_split($pattern, $url);
        if ($components) {
            if (array_key_exists(5, $components)) {
                return $components[5];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}