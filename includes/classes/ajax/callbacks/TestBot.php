<?php

namespace WSMGS\classes\ajax\callbacks;

use WSMGS\classes\GlobalClass;

class TestBot {
    /**
     * @var array
     */
    public $output = [];

    /**
     * @var mixed
     */
    private $methods;

    /**
     * @return mixed
     */
    public function testBotID() {
        try {
            if (sanitize_text_field($_POST['action']) !== 'wsmgs_check_bot_access') {
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

            $args = [
                'sheetID' => $this->methods->getSheetId(get_option('sheetUrl')),
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

                if ($response) {
                    $this->output['status'] = 'success';
                    $this->output['message'] = esc_html__('Successful', WSMGS_TEXT_DOMAIN);
                    wp_send_json_success($this->output, 200);
                    wp_die();
                };
            }

            if ($sheetColumn) {
                $this->output['status'] = 'success';
                $this->output['message'] = esc_html__('Successful', WSMGS_TEXT_DOMAIN);
                wp_send_json_success($this->output, 200);
                wp_die();
            }

            $this->output['status'] = 'error';
            $this->output['message'] = esc_html__('Bot do not have access. Check your sheet URL and and name is Ok or check if bot has editor access', WSMGS_TEXT_DOMAIN);
            wp_send_json_error($this->output, 400);
            wp_die();

        } catch (\Throwable $error) {
            $this->output['message'] = esc_html__('Bot do not have access. Check your sheet URL and and name is Ok or check if bot has editor access', WSMGS_TEXT_DOMAIN);
            wp_send_json_error($this->output, $error->getCode());
            wp_die();
        }
    }
}