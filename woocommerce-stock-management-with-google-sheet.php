<?php

/**
 * Plugin Name:       WooCommerce Stock Management with Google Sheet
 * Plugin URI:        https://wppool.dev/woocommerce-stock-management-with-google-sheet/
 * Description:       Manage your WooCommerce products with google sheet. Activate and setup for once and manage your products with less efforts.
 * Version:           1.0.0
 * Requires at least: 5.4
 * Requires PHP:      5.6
 * Author:            WPPOOL
 * Author URI:        https://wppool.dev/
 * Text Domain:       wsmgs
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/* if accessed directly exit from plugin */
defined('ABSPATH') || wp_die(__('You can\'t access this page', 'wsmgs'));

if (!defined('WSMGS_VERSION')) {
    define('WSMGS_VERSION', '1.0.0');
    // define('WSMGS_VERSION', time());
}

if (!defined('WSMGS_TEXT_DOMAIN')) {
    define('WSMGS_TEXT_DOMAIN', 'wsmgs');
}

if (!defined('WSMGS_BASE_PATH')) {
    define('WSMGS_BASE_PATH', plugin_dir_path(__FILE__));
}

if (!defined('WSMGS_BASE_URL')) {
    define('WSMGS_BASE_URL', plugin_dir_url(__FILE__));
}

if (!defined('WSMGS_PlUGIN_NAME')) {
    define('WSMGS_PlUGIN_NAME', 'WooCommerce Stock Management with Google Sheet');
}

if (!defined('WSMGS_PlUGIN_MODE')) {
    $development = false;

    if ($development) {
        define('WSMGS_PlUGIN_MODE', 'dev');
    } else {
        define('WSMGS_PlUGIN_MODE', 'prod');
    }
}

if (!file_exists(WSMGS_BASE_PATH . 'vendor/autoload.php')) {
    return;
}

require_once WSMGS_BASE_PATH . 'vendor/autoload.php';

final class WSMGS {

    /**
     * @var string
     */
    public $noticeMessage = 'This is a notice message';

    /**
     * @return null
     */
    public function __construct() {

        if ($this->versionCheck() == 'version_low') {
            return;
        }

        if (!$this->pluginsCheck()) {
            return;
        }

        $this->initiatePlugin();
    }

    /**
     * Initialte the plugin after all check is complete
     * @return null
     */
    public function initiatePlugin() {

        if (get_option('wsmgsRedirect')) {
            delete_option('wsmgsRedirect');
            wp_redirect(admin_url('admin.php?page=wsmgs-page'));
        }

        // Include the base file of this plugin
        require_once WSMGS_BASE_PATH . 'includes/plugin.php';

        new WSMGS\Plugin;

    }

    public function pluginsCheck() {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        if (!is_plugin_active(plugin_basename(__FILE__)) || !class_exists('WooCommerce')) {
            $this->noticeMessage = "<b>" . WSMGS_PlUGIN_NAME . "</b>" . "&nbsp" . __("deactivated beacasue <b>WooCommerce</b> plugin is not active.", WSMGS_TEXT_DOMAIN);
            $this->deactivatePlugin();
            return false;
        }

        return true;
    }

    /**
     * @return null
     */
    public function versionCheck() {

        if (version_compare(PHP_VERSION, '5.4') < 0) {

            if (is_plugin_active(plugin_basename(__FILE__))) {

                $this->noticeMessage = "<b>" . WSMGS_PlUGIN_NAME . "</b>" . "&nbsp" . __("cannot be activated. Requires at least PHP 5.4. Plugin automatically deactivated.", WSMGS_TEXT_DOMAIN);
                $this->deactivatePlugin();
                return 'version_low';
            }
        }
    }

    /**
     * @param  $noticeMessage
     * @return null
     */
    public function deactivatePlugin() {

        deactivate_plugins(plugin_basename(__FILE__));

        add_action('admin_notices', function () {
            printf('<div class="notice notice-error is-dismissible"><p>%s</p></div>', $this->noticeMessage);
            return;
        });
    }

}

if (!class_exists('WSMGS')) {
    return;
}

add_action('plugins_loaded', 'woocommerceStockManagementWithSheet');

if (!function_exists('woocommerceStockManagementWithSheet')) {

    function woocommerceStockManagementWithSheet() {
        return new WSMGS();
    }
}

register_activation_hook(__FILE__, function () {
    if (!get_option('wsmgsToken')) {
        add_option('wsmgsToken', wp_generate_uuid4());
    }
    add_option('wsmgsRedirect', true);
    add_option('configureMode', true);
});