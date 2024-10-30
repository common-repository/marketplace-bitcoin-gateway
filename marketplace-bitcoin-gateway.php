<?php
/**
 * Plugin Name: Marketplace Bitcoin Gateway
 * Plugin URI: https://wc-marketplace.com/
 * Description: Bitcoin Payment Gateway ( WooCommerce MarketPlace Compatible )
 * Author: Mohammad Umer Shaikh
 * Version: 1.0.0
 * Author URI: 
 * 
 * Text Domain: marketplace-bitcoin-gateway
 * Domain Path: /languages/
 */

if (!class_exists('WCMp_Dependencies_bitcoin_gateway')) {
    require_once trailingslashit(dirname(__FILE__)) . 'includes/class-wcmp-bitcoin-dependencies.php';
}
require_once trailingslashit(dirname(__FILE__)) . 'includes/wcmp-bitcoin-gateway-core-functions.php';
require_once trailingslashit(dirname(__FILE__)) . 'marketplace-bitcoin-gateway-config.php';
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
} 
if (!defined('WCMp_BITCOIN_GATEWAY_PLUGIN_TOKEN')) {
    exit;
}
if (!defined('WCMp_BITCOIN_GATEWAY_TEXT_DOMAIN')) {
    exit;
}

if (!WCMp_Dependencies_bitcoin_gateway::woocommerce_plugin_active_check()) {
    add_action('admin_notices', 'mbg_woocommerce_inactive_notice_bitcoin');
}



if (WCMp_Dependencies_bitcoin_gateway::woocommerce_plugin_active_check()) {
    if (!class_exists('WCMp_Bitcoin_Gateway')) {
        require_once( trailingslashit(dirname(__FILE__)) . 'classes/class-wcmp-bitcoin-gateway.php' );
        global $WCMp_Bitcoin_Gateway;
        $WCMp_Bitcoin_Gateway = new WCMp_Bitcoin_Gateway(__FILE__);
        $GLOBALS['WCMp_Bitcoin_Gateway'] = $WCMp_Bitcoin_Gateway;
        // Activation Hooks
        register_activation_hook(__FILE__, array($WCMp_Bitcoin_Gateway, 'activate_WCMp_Bitcoin_Gateway'));
        register_activation_hook(__FILE__, 'flush_rewrite_rules');
        // Deactivation Hooks
        register_deactivation_hook(__FILE__, array($WCMp_Bitcoin_Gateway, 'deactivate_WCMp_Bitcoin_Gateway'));
    }
}
?>