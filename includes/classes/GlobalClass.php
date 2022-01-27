<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class GlobalClass {

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
     * Organize the WooCommerce products for exporting to google sheet
     * @param  $products
     * @return mixed
     */
    public function organizeInsertionValues(array $products, bool $skipExportCheck = false) {
        $organizedData = [];

        if (!is_array($products) || count($products) < 1) {
            return $organizedData;
        }

        foreach ($products as $key => $product) {

            $wcProduct = wc_get_product($product->ID);

            if ($skipExportCheck == false) {

                $isProductExported = $this->isProductExported($product->ID);

                if ($isProductExported) {
                    continue;
                }
            }

            array_push($organizedData, [
                $product->ID ? $product->ID : '', /* Column A*/

                $wcProduct->get_type() ? $wcProduct->get_type() : '', /* Column B*/

                $wcProduct->get_sku() ? $wcProduct->get_sku() : '', /* Column C*/

                $product->post_title ? $product->post_title : '', /* Column D*/

                $product->post_status ? $product->post_status : '', /*  Column E*/

                $wcProduct->get_stock_quantity() ? $wcProduct->get_stock_quantity() : '', /* Column F*/

                $wcProduct->get_sale_price() ? $wcProduct->get_sale_price() : '', /* Column G*/

                $wcProduct->get_regular_price() ? $wcProduct->get_regular_price() : '' /* Column H*/

            ]);

            // If the current product is a variable product than push all its variations data afte the variable product
            if ($wcProduct->get_type() === 'variable') {

                $variations = $wcProduct->get_available_variations();

                if (is_array($variations) && count($variations) > 0) {

                    foreach ($variations as $key => $variation) {

                        array_push($organizedData, [
                            $variation['variation_id'] ? $variation['variation_id'] : '', /* Column A*/

                            'variation', /* Column B*/

                            $variation['sku'] ? $variation['sku'] : '', /* Column C*/

                            $this->variationProductName($variation['attributes'], $product->post_title), /* Column D*/

                            $product->post_status ? $product->post_status : '', /*  Column E*/

                            $variation['max_qty'] ? $variation['max_qty'] : '', /* Column F*/

                            $variation['display_price'] ? $variation['display_price'] : '', /* Column G*/

                            $variation['display_regular_price'] ? $variation['display_regular_price'] : '' /* Column H*/

                        ]);

                    }
                }
            }
        }

        return $organizedData;
    }

    /**
     * Add meta value for each product if its exported
     * @param $productID
     */
    public function isProductExported($productID) {
        $sheetProductIDs = $this->getProductIDFromSheet();

        if (!is_array($sheetProductIDs)) {
            return false;
        }

        $sheetProductIDs = array_merge(...$sheetProductIDs);

        return in_array($productID, $sheetProductIDs);
    }

    /**
     * Get only the first column from sheet which is product id column
     * @return mixed
     */
    public function getProductIDFromSheet() {

        $sheetID = $this->getSheetId(get_option('sheetUrl'));
        $tabName = get_option('tabName');

        if (!$sheetID) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!$tabName) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        $client = $this->getClient();
        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $sheetID;
        $range = '' . $tabName . '!A2:A';
        $requestBody = new \Google_Service_Sheets_ValueRange();
        $requestBody->setMajorDimension('ROWS');
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        return $values;

    }

    /**
     * @param  array   $attributes
     * @param  string  $productName
     * @return mixed
     */
    public function variationProductName(array $attributes, string $productName) {

        $variationName = '';

        if (!is_array($attributes)) {
            return $variationName;
        }

        if (count($attributes) < 1) {
            return;
        }

        $capitalizedArray = [];

        foreach ($attributes as $key => $value) {
            $capitalizedArray[] = ucfirst($value);
        }

        $variationName = $productName . ' - ' . implode(", ", $capitalizedArray);

        return $variationName;
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
            'id'           => 'ID',
            'type'         => 'Type',
            'sku'          => 'SKU',
            'name'         => 'Name',
            'published'    => 'Published',
            'stock'        => 'Stock',
            'salePrice'    => 'Sale price',
            'regularPrice' => 'Regular price'
        ];

        return $columns;
    }

    /**
     * Get the sheet column row (the 1st row) from sheet
     * @param  array   $args
     * @return mixed
     */
    public function getSheetColumn(array $args) {

        if (!isset($args['sheetID']) || !$args['sheetID']) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['tabName']) || !$args['tabName']) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        $client = $this->getClient();
        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $args['sheetID'];
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

        if (!isset($args['sheetID']) || !$args['sheetID']) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['tabName']) || !$args['tabName']) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        $client = $this->getClient();

        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $args['sheetID'];
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
     * Append data after first row in google sheet
     * @param  $args
     * @return mixed
     */
    public function insertData($args) {

        if (!isset($args['sheetID']) || !$args['sheetID']) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['tabName']) || !$args['tabName']) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        if (count($args['values']) < 1) {
            return;
        }

        $client = $this->getClient();

        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $args['sheetID'];
        $range = '' . $args['tabName'] . '!A2';
        $requestBody = new \Google_Service_Sheets_ValueRange();
        $requestBody->setMajorDimension('ROWS');
        $requestBody->setValues(
            $args['values']
        );

        $response = $service->spreadsheets_values->append(
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
     * @param  $args
     * @return mixed
     */
    public function updateProducts($args) {

        if (!isset($args['sheetID']) || !$args['sheetID']) {
            trigger_error("Sheet ID is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['tabName']) || !$args['tabName']) {
            trigger_error("Sheet tab name is not found", E_USER_WARNING);
            return;
        }

        if (!isset($args['rowIndex']) || !$args['rowIndex']) {
            trigger_error("Row index is empty or not found", E_USER_WARNING);
            return;
        }

        $client = $this->getClient();

        $service = new \Google_Service_Sheets($client);

        $spreadsheetId = $args['sheetID'];
        $range = '' . $args['tabName'] . '!A' . $args['rowIndex'] . ':H' . $args['rowIndex'] . '';
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