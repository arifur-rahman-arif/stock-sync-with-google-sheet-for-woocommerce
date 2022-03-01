<?php

namespace WSMGS\classes\ajax\callbacks;

use WSMGS\classes\GlobalClass;

class CopyScript {
    /**
     * @var array
     */
    public $output = [];

    /**
     * @var mixed
     */
    private $methods;

    /**
     * Get the script code ajax response
     * @return mixed
     */
    public function getScript() {
        try {
            if (sanitize_text_field($_POST['action']) !== 'wsmgs_copy_script') {
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

            $tabName = get_option('tabName');

            if (!$tabName || $tabName == "") {
                $this->output['status'] = 'error';
                $this->output['message'] = esc_html__('Google Sheet tab name is not saved correctly. Try to save tab name again', WSMGS_TEXT_DOMAIN);
                wp_send_json_error($this->output, 400);
                wp_die();
            }

            $rawScript = $this->methods->rawAppScript();

            if ($rawScript) {
                $this->output['status'] = 'success';
                $this->output['message'] = esc_html__('Script Code copied to your clipboard. Please paste it in your Apps Script file', WSMGS_TEXT_DOMAIN);
                $this->output['scriptCode'] = $rawScript;
                wp_send_json_success($this->output, 200);
                wp_die();
            }

            $this->output['status'] = 'error';
            $this->output['message'] = esc_html__('Unable to copy script code. Try again or re-save Google Sheet URL and Tab Name', WSMGS_TEXT_DOMAIN);
            wp_send_json_error($this->output, 400);
            wp_die();

        } catch (\Throwable $error) {
            $this->output['message'] = $error->getMessage();
            wp_send_json_error($this->output, $error->getCode());
            wp_die();
        }
    }
}