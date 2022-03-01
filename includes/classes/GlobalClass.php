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

        // Service account credentials for accessing user sheet
        $credentials = (array) json_decode('{
            "type": "service_account",
            "project_id": "wc-stock-management-with-sheet",
            "private_key_id": "93153d9a15fefa33ee8c277bcbe8709bc77f5704",
            "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCa+xKwCBtAlNX0\nNxXLk4yezKgwth4KdPhbOFaWvCSQN2vsKqj8Pw2dAXNtuB2qpTLNEENJaAFL6Uqv\ncGAllZmq3XS0N+Vec3ZgXkERuWPRuGFo84+EzL5Uxk00UH0ul73S5L4naDww4M2w\nc+G9iBXLhJk/JuRo6JDqGAHPwl3+dfcnQYc4QYln7/9iUQew0VoBSOa2k/wB0hkI\nxmVWZqoFJD63Jh/21PQ/oNRpjDGP2mCVyDZOsUe335Oj2nxS1FGAHo+Jw0od+Tnh\ntADlVQAyvP6I3GZAu4HwnWGFzUM9gCCt11wtq6zU9GPcaWpRic0yagktVCLvR0tV\n/B5Reb9JAgMBAAECggEABBP8O2aTNQFVme4DJE63IgyXlmSMaNEIIfpbCO9ddPAI\nZnf+51hPZuS9YVM5cJ6VNiEp7BoRAqraXMBzvzlvCDh4lC9uWSxnnoSMaMd8kW2t\nHoJeMevjTD7FlE1T5fB3Pb/oIwhSoSShNPXvqDmW/Tp5f9hppTsNaSOFlK50I7hz\ncx1/gMni2fIAwzaNX0a3vfymruaqbp0HI+1uDAEl/Slp5Nwkv1JOt3zgog/48PO4\nsiv36Yos1X8+ny9omUXerJ9VITwEIAwwSi5dW6Hg8hYd71UA+cU60w807NvwzkMA\nKQGo1vDHDQ6ii3H9G3+DZBWVWEs13a0P0+wOeKIW+wKBgQDKDtAJ0ZGrHcS10fdT\nOnNmpa2tagVCjWTRrpedORP7vPtsibPfcEgfhiCsMwUBmnXWROkGe5w9+iQBTMtv\nAbjAzBGCxcVZIH12uxYCklTPyGOxeD2OJKE4A27yHunPkIOmz80cheeHdMQq5z9c\nckuUL69tlMMwX9VOG9u1J8Op9wKBgQDEWuHGGTaQmg5iGZ5skHsLOkuhR5teCwi9\nvzEWIldbn8I8Dh42zrAn9efBpM9VOv23rtDGWbZYsLtU0YZJ22R2IdPvEN3lstdh\nHqb4A/2nZv/bgldlBW8J+fF/y7SEfp/uBwnmfzjm+kAqZdT8NWZSAoKUMRbTGyV2\nlhuaH3WQvwKBgQDA44EgTmqUAvf9ZnKHhl4SGImC6ZoZ3WbocJaV5Z45DECsbYxD\n+ikvxtg76vyVekGRifk2UhxmYfurLgdqcidQDb+NqoTpv4VmKdUqIU1Lig35pkKF\nBlzNXyGzi2VC27+CKA+zcfUDr6lxF466DvYtgZQjtQbckC/Nv/RurIYyIwKBgQCS\nrHy4WL8stxeFajOwTyDKm0pBeFbzofRuoPIO4ao11BnoeHdTY4Os5gCv4ufJd95s\nplZnxD309FeLHVRduVfK8qv4ibXnzncaoiYBYCHLOaJoG24jFTMD1cPzPXxj+J6I\n1JMtrd0YlNj3ksfNeiormppmDmFJYp7SfYCy2UsdywKBgQDBkPARltD2QDwTAomB\nPMHE+ZHtgkEqKktisj3U+PHPWQajY0DM9OXntKOb9unzHNyJYr7Rb9EzWrwUql+q\nYsH9YUIQktrpskUYaPU6ujrr6hIfh5RpDCosz37wE99oDJlooZWURQhOY/HrQ2OC\nm/S3m3hRs5sP3yyMZ8c91FXYbw==\n-----END PRIVATE KEY-----\n",
            "client_email": "wcsmgs@wc-stock-management-with-sheet.iam.gserviceaccount.com",
            "client_id": "106303069307998867945",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/wcsmgs%40wc-stock-management-with-sheet.iam.gserviceaccount.com"
          }
        ');

        try {
            $client = new \Google_Client();
            $client->setApplicationName('Woocommerce To Sheet');
            $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
            $client->setAuthConfig($credentials);
            $client->setAccessType('offline');
            return $client;
        } catch (\Throwable $error) {
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