<?php

namespace WSMGS\classes;

defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

class GlobalClass {

    /**
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

}