<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class GlobalClass {

    /**
     * Sanitize the data to use safely
     * @param  array   $nonSanitizedData
     * @return mixed
     */
    public function sanitizeData(array $nonSanitizedData) {
        $sanitizedData = null;

        $sanitizedData = array_map(function ($data) {
            if (gettype($data) == 'array') {
                return $this->sanitizeData($data);
            } else {
                return sanitize_text_field($data);
            }
        }, $nonSanitizedData);

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

                $wcProduct->get_stock_quantity() ? $wcProduct->get_stock_quantity() : $wcProduct->get_stock_status(), /* Column F*/

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

        // Service account credentials for accessing user sheet
        $credentials = (array) json_decode('{
            "type": "service_account",
            "project_id": "ssgsw-344416",
            "private_key_id": "5d7bd36605c71b3b731be836f2c814b94ad4974b",
            "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDGGi5JsGv4yyu2\nboz/keOPK2tnE1roFU0kGxVqX+BDu5vsTrjXmkyvDt2g+O2TiLhkzmvrgmIJrdyu\nlexqg9nKtklnIdFX11tAVeY0Pri2qELc0mPtDpc6fVUmlLDOh+ZKe+oRE+RhRNgb\nTKt1DbDyWUE6PfCMGimKBrgDyXiHl3zw9nBfPBFu0jhRnlw1Gk8b9TgcXgid9Hgc\nqKs+j9nNo4X2ubdSfQw8qwWexWStBkMIqhsIFH+Bkg58SwbjbkN227eJ7ykspfau\n3wglwaeDoBc91XlJ0aDysDvaFxnGapnbow5gDYRo8EuL3Fn//HPecH1BiHInVRlQ\nWsj51snRAgMBAAECggEAAuSyH0AHFK45ekezMh6iLGazwfC0vcHlY3kMixYhRTX7\n0xPLg/UgSHiC7Mlj9WkcWdcQuRLpfgcSu39LsZrRQxJeYDXsjAAUiGwUwZe2rI7I\nsrdVL6q8U1WuKSMH8c1G9BirGJKh/dhywFPSZ4JSnriGYQyTa0VszMP4OzBtrOvH\nOiwDF/78YSpCrx8iYY0vKH4fEVbaW9s+Qz7CLhp2Gc6+ki1TUSJ8bOZVty6JT4u2\nnLHOH1U42Ij6a8J1c/a7ZhMUSGdU2crumw/+MVuTMp7IYGD+fvhFaIYAaXkHW02E\nZkgNZAev2Sn9E3pkFCiqLmvbiypo/6QzwRacKXEPAQKBgQDmporNHYlpJuWoZMA+\n896LleH51xqkCQ8GJpenFnExSJ3qdnOctfuJmQ8TX29Q+35iiY2WPBsSlaaVuOVn\nbFyzWZ85uugZBQ+ZhinVjoPXe0xCNFm4s/+u1kgxSQFO2l8WLEAkdMgvRM04Qbgw\nX1wMjMh3hNvFY+9Bd36IAC1pAQKBgQDb3+C6ASM0Hfa7c21MukmQoKDgGu1SPRyk\nS4tpUsHIH7MdYsgeqszhu0JR0q8fhdzzZKOlkpkDE4QdLnngJFOtN7yJtv5NjSjW\nNxLWU7Sc0FGPSiQDDmbyt69zdtc4tIyjg/BIMJ610Gsn5Gbo390S7p9bOzUhfdLj\n9ILKqjQQ0QKBgQCGUVQhcOL1lk5Fiy6dOn1OAmHPkeGUxyW1qkHEbwBJ+ATmZkab\nayrpjXXHDVuQ6HRIQ9xtMeF+KCITrNIDMYqmczmTb/H7qAPWnte74ruKf+X/4+af\n9kqecXEnKmGVnvuiuPA9iriNNl7gNP1jRhUfxdh0Ka2mPLU3BbRs1NWdAQKBgHP8\nDiSwcuyMHdpWEkMWRbYoNZTiP2xRVmmIfDsL77LsneSTdjNPMBKnYF2fQMflKl+u\nJ/Ewesy3n8pw+NN29jj6nNcck9s+6lrN5H7w2uaZsVxf5M1D7KR+puvGtdNw0ntz\nHu5Fz4LjNZNulaakaI8TFzbd7bIEGJotIk1cKWjxAoGAVs/pBdZTKd2M+/2dFuJA\n6PWLnN5iTQXJmMSZGhdo+/EBnigG7f/T+rBBhxkWkNQegz4cHhB7ONLIFA+l5Tgz\n8iXF/ResPa+pFz5JdWJI7plqmaaV5QQloLzwkgTea5dUa/gWoI0rrpx8Vf7sRmzQ\nJvRGIVJJ7w86eU2OrAraHII=\n-----END PRIVATE KEY-----\n",
            "client_email": "ssgsw-service@ssgsw-344416.iam.gserviceaccount.com",
            "client_id": "113205165664956048920",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/ssgsw-service%40ssgsw-344416.iam.gserviceaccount.com"
        }');

        try {
            $client = new \Google_Client();
            $client->setApplicationName('Woocommerce To Sheet');
            $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
            $client->setAuthConfig($credentials);
            $client->setAccessType('offline');
            return $client;
        } catch (\Throwable$error) {
            throw $error;
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

    /**
     * Defining the raw app script to use in Google Sheet
     * @return mixed
     */
    public function rawAppScript() {

        $tabName = get_option('tabName');

        if (!$tabName || $tabName == "") {
            return null;
        }

        $rawScript = '
        // Run on user edit
        function atEdit(e) {
            let sheet = e.range.getSheet();

            let currentTab = sheet.getName();

            if (currentTab !== "' . get_option('tabName') . '") return "INVALID_SHEET";

            let data = collectData(e);

            updateProduct({
                data,
            });
        }

        // Collect the data from sheet and organize those based on their column position.
        function collectData(e) {
            let sheet = e.range.getSheet();

            let column = e.range.getColumn();

            if (column > 8) return "INVALID_COLUMN";

            let range = sheet.getActiveRange();

            let editedValues = range.getValues();

            if (!editedValues) return "NO_VALUES";

            let data = [];

            editedValues.forEach((editedValue, rowIndex) => {
                let row = e.range.getRow() + rowIndex;

                let aToBValues = sheet.getRange(`A${row}:B${row}`).getValues()[0];

                // The start index of the user column selection in the row
                let startIndex = column;
                // The end index of user current selection in the row
                let endIndex = column + (editedValue.length - 1);

                // The colum place
                let columns = [
                    "id", // Col A
                    "type", // Col B
                    "sku", // Col C
                    "name", // Col D
                    "published", // Col E
                    "stock", // Col F
                    "salePrice", // Col G
                    "regularPrice", // Col H
                ];

                let organizedData = {};

                let tempIndex = 0;

                for (let i = startIndex; i <= endIndex; i++) {

                    // If there is no value than just simply break the loop and return;
                    if(!editedValue[tempIndex]) continue;

                    if (!organizedData[columns[i - 1]]) {
                        organizedData[columns[i - 1]] = editedValue[tempIndex];
                    }

                    if (organizedData.id) {
                    // Set the old ID value to that cell
                    if(e.oldValue){
                        sheet.getRange(`A${row}`).setValue(e.oldValue);
                    }
                    showAlert("ID column cannot be changed");
                    data.length = 0;
                    return;
                    }

                    if (organizedData.type) {
                        // Set the old Type value to that cell
                        if(e.oldValue){
                        sheet.getRange(`B${row}`).setValue(e.oldValue);
                        }
                        showAlert("Type column cannot be changed");
                        data.length = 0;
                        return;
                    }
                    tempIndex++;
                }


                // If there are no data inside the object skip this current loop
                if(Object.keys(organizedData).length < 1){
                return;
                }

                // Insert the id & type of that product
                organizedData[columns[0]] = aToBValues[0];
                organizedData[columns[1]] = aToBValues[1];

                data.push(organizedData);
            });

            return data;
        }

        // Update the product on wordpress when there is a new change in sheet
        function updateProduct(args) {
            if (!Array.isArray(args.data) || args.data.length < 1) {
                SpreadsheetApp.getActiveSpreadsheet().toast("Data is not valid to send to WordPress");
                return "INVALID_DATA";
            }

            //Request body
            let data = {
                token: "' . get_option('wsmgsToken') . '",
                reqData: args.data,
            };

            // Request options
            let options = {
                method: "POST",
                contentType: "application/json",
                muteHttpExceptions: true,
                payload: JSON.stringify(data),
            };

            try {

                let url = "' . site_url() . '/wp-json/wsmgs/v1/update-product";
                let result = UrlFetchApp.fetch(url, options);
                let response = JSON.parse(result.getContentText());

                SpreadsheetApp.getActiveSpreadsheet().toast(response.data.message);

            } catch (error) {
                showAlert(error.message)
                Logger.log(error.message)
            }
        }

        // Show a popup alert on spreadsheet
        function showAlert(message){
            SpreadsheetApp.getUi().alert(message);
        }
        ';

        return $rawScript ? $rawScript : null;
    }

}