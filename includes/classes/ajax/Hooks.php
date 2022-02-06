<?php

namespace WSMGS\classes\ajax;

use WSMGS\classes\ajax\callbacks\ExportProducts;
use WSMGS\classes\ajax\callbacks\SaveOptions;
use WSMGS\classes\ajax\callbacks\TestBot;

class Hooks {
    /**
     * @var mixed
     */
    public $sheetAuth;

    public function __construct() {
        $this->assignClass();
        $this->ajaxActionHooks();
    }

    public function assignClass() {
        $this->sheetAuth = new ExportProducts;
        $this->testBot = new TestBot;
        $this->saveOptions = new SaveOptions;
    }

    // Define the ajax action hooks for this plugin
    public function ajaxActionHooks() {

        // Ajax wsmgs_export_product hook for product export
        add_action('wp_ajax_wsmgs_export_product', [$this->sheetAuth, 'exportProducts']);

        // Ajax wsmgs_check_bot_access hook for bot testing
        add_action('wp_ajax_wsmgs_check_bot_access', [$this->testBot, 'testBotID']);

        // Ajax wsmgs_save_options hook for bot testing
        add_action('wp_ajax_wsmgs_save_options', [$this->saveOptions, 'saveOptions']);
    }
}