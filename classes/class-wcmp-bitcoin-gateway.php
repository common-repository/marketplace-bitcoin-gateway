<?php
if (!defined('ABSPATH')) {
    exit;
}
class WCMp_Bitcoin_Gateway {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $library;
    public $shortcode;
    public $admin;
    public $frontend;
    public $template;
    public $ajax;
    private $file;
    public $settings;
    public $dc_wp_fields;
    public $saved_cards;
    public $payment;
    public $connect_vendor;
    public $transfer;
    public $reset_cards_obj;

    public function __construct($file) {
        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WCMp_BITCOIN_GATEWAY_PLUGIN_TOKEN;
        $this->text_domain = WCMp_BITCOIN_GATEWAY_TEXT_DOMAIN;
        $this->version = WCMp_BITCOIN_GATEWAY_PLUGIN_VERSION;
		add_filter('vendor_commission_amount', array($this,'bitcoin_commission_amount'),1);
        add_action('init', array(&$this, 'init'), 5);
 
    }

    /**
     * initilize plugin on WP init
     */
    function init() {

        // Init Text Domain
        $this->load_plugin_textdomain();

        // Init library
        $this->load_class('library');
        $this->library = new WCMp_Bitcoin_Gateway_Library();
       // $this->library->blockchain_library();

        // Init ajax
        if (defined('DOING_AJAX')) {
            $this->load_class('ajax');
            $this->ajax = new WCMp_Bitcoin_Gateway_Ajax();
        }

        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new WCMp_Bitcoin_Gateway_Admin();
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new WCMp_Bitcoin_Gateway_Frontend();

            // init templates
            $this->load_class('template');
            $this->template = new WCMp_Bitcoin_Gateway_Template();
        }
        /* Adding function to work with WCMp */
        if (class_exists('WCMp')) {
            $this->load_vendor_class('connect-vendor', 'wcmp');
            $this->connect_vendor = new WCMp_Bitcoin_Gateway_Connect_Vendor();
            $this->load_vendor_class('connect', 'wcmp');
            add_filter('wcmp_payment_gateways', array(&$this, 'add_wcmp_bitcoin_payment_gateway'));
        }

        // WCMp Wp Fields
        $this->dc_wp_fields = $this->library->load_wp_fields();
    }

    /**
     * Add WCMp stripe payment gateway
     * @param array $load_gateways
     * @return string
     */
    public function add_wcmp_bitcoin_payment_gateway($load_gateways) {
        $load_gateways[] = 'WC_Product_Vendors_Bitcoin_Connect';
        return $load_gateways;
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present
     *
     * @access public
     * @return void
     */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'marketplace-bitcoin-gateway');
        load_textdomain('marketplace-bitcoin-gateway', WP_LANG_DIR . '/marketplace-bitcoin-gateway/marketplace-bitcoin-gateway-' . $locale . '.mo');
        load_plugin_textdomain('marketplace-bitcoin-gateway', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
    }
    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

    public function load_vendor_class($class_name = '', $vendor = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('vendor/'.$vendor.'/class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    /**
     * Install upon activation.
     *
     * @access public
     * @return void
     */
    function activate_WCMp_Bitcoin_Gateway() {
        global $WCMp_Bitcoin_Gateway;
    }

    /**
     * UnInstall upon deactivation.
     *
     * @access public
     * @return void
     */
    function deactivate_WCMp_Bitcoin_Gateway() {
        global $WCMp_Bitcoin_Gateway;
        delete_option('WCMp_Bitcoin_Gateway_installed');
    }

    /** Cache Helpers ******************************************************** */

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE'))
            define("DONOTCACHEPAGE", "true");
        // WP Super Cache constant
    }

}