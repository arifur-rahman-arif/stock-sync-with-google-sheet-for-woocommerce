<?php
namespace WSMGS\classes\ajax\callbacks;

use WSMGS\classes\GlobalClass;

class SaveOptions {
    /**
     * @var array
     */
    public $output = [];

    /**
     * @var mixed
     */
    private $methods;

    public function saveOptions() {
        try {
            if (sanitize_text_field($_POST['action']) !== 'wsmgs_save_options') {
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

            $sheetUrlUpdated = update_option('sheetUrl', $this->reqData['sheetUrl']);
            $tabNameUpdated = update_option('tabName', $this->reqData['tabName']);
            $configureModeUpdated = delete_option('configureMode');

            if ($sheetUrlUpdated && $tabNameUpdated && $configureModeUpdated) {
                $this->output['status'] = 'success';
                $this->output['redirectUrl'] = admin_url('edit.php?post_type=product');
                $this->output['message'] = esc_html__('Thanks for passing all of these process. Your products will be synchronized from now on', WSMGS_TEXT_DOMAIN);
                wp_send_json_success($this->output, 200);
                wp_die();
            }

            $this->output['status'] = 'error';
            $this->output['message'] = esc_html__('Input data can\'t be saved. Try again', WSMGS_TEXT_DOMAIN);
            wp_send_json_error($this->output, 400);
            wp_die();

        } catch (\Throwable $error) {
            $this->output['message'] = $error->getMessage();
            wp_send_json_error($this->output, $error->getCode());
            wp_die();
        }
    }
}