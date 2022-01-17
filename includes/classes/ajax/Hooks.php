<?php

namespace WSMGS\classes\ajax;

use WSMGS\classes\ajax\callbacks\ExportProducts;

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
    }

    // Define the ajax action hooks for this plugin
    public function ajaxActionHooks() {

        // Ajax hook wsmgs_authorize_sheet
        add_action('wp_ajax_wsmgs_export_product', [$this->sheetAuth, 'exportProducts']);
    }
}