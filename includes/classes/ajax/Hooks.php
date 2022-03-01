<?php

namespace WSMGS\classes\ajax;

use WSMGS\classes\ajax\callbacks\CopyScript;
use WSMGS\classes\ajax\callbacks\ExportProducts;
use WSMGS\classes\ajax\callbacks\SaveOptions;
use WSMGS\classes\ajax\callbacks\TestBot;
use WSMGS\classes\ajax\callbacks\WizardMode;

class Hooks {
    public function __construct() {
        $this->assignClass();
        $this->ajaxActionHooks();
    }

    public function assignClass() {
        $this->sheetAuth = new ExportProducts;
        $this->testBot = new TestBot;
        $this->saveOptions = new SaveOptions;
        $this->copyScript = new CopyScript;
        $this->wizardMode = new WizardMode;
    }

    // Define the ajax action hooks for this plugin
    public function ajaxActionHooks() {

        // Ajax wsmgs_export_product hook for product export
        add_action('wp_ajax_wsmgs_export_product', [$this->sheetAuth, 'exportProducts']);

        // Ajax wsmgs_check_bot_access hook for bot testing
        add_action('wp_ajax_wsmgs_check_bot_access', [$this->testBot, 'testBotID']);

        // Ajax wsmgs_save_options hook for bot testing
        add_action('wp_ajax_wsmgs_save_options', [$this->saveOptions, 'saveOptions']);

        // Ajax wsmgs_copy_script hook for getting the App Script code
        add_action('wp_ajax_wsmgs_copy_script', [$this->copyScript, 'getScript']);

        // Ajax wsmgs_copy_script hook for getting the App Script code
        add_action('wp_ajax_wsmgs_exit_wizard_mode', [$this->wizardMode, 'exitWizardMode']);
    }
}