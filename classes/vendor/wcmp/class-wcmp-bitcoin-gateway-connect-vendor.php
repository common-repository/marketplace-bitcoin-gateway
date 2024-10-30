<?php
if (!defined('ABSPATH')) {
    exit;
}

class WCMp_Bitcoin_Gateway_Connect_Vendor {

    public function __construct() {
        $is_enable_bitcoin = mbg_get_WCMp_Bitcoin_Gateway_settings('is_enable_bitcoin', 'payment', 'bitcoin_gateway');
        if ($is_enable_bitcoin == 'Enable') {
            // Connect Button Vendor Shop Page
            if (get_user_meta(get_current_user_id(), '_vendor_payment_mode', true) == 'bitcoin_masspay' && get_wcmp_vendor_settings('payment_method_bitcoin_masspay', 'payment') == 'Enable') {
                if (WCMp_PLUGIN_VERSION <= '2.7.5') {
                    add_action('other_exta_field_dcmv', array($this, 'vendor_bitcoin_address'));
                } else {
                    add_action('wcmp_after_vendor_billing', array($this, 'vendor_bitcoin_address'));
                }
				 
				 add_filter('wcfm_wcmarketplace_settings_fields_billing', array($this, 'wcfm_vendor_bitcoin_address'), 10,1);
                 add_action('wcfm_wcmarketplace_settings_update', array($this, 'wcfm_save_bitcoin_address'), 10,2);

				
            }
            // Add stripe in the payment mode list
            add_filter('automatic_payment_method', array($this, 'admin_bitcoin_payment_mode'), 10);

            $this->payment_admin_settings = get_option('wcmp_payment_settings_name');
            add_filter('wcmp_vendor_payment_mode', array($this, 'vendor_bitcoin_payment_mode'), 10);
        }
        // Save Bitcoin Address
        add_action('before_wcmp_vendor_dashboard', array($this, 'save_bitcoin_address'));
    }
    function wcfm_save_bitcoin_address($user_id, $formfields) {
        if (isset($formfields['bitcoin_address'])) {
			$btc_address = $formfields['bitcoin_address'];
			update_user_meta( $user_id, '_vendor_bitoin_address', $btc_address );	
		}
	}
    function wcfm_vendor_bitcoin_address($arg) {
		//print_r($arg); exit;
		if (empty($user)) {
            $user = wp_get_current_user();
        }
        $user_id = $user->ID;
		$bitcoin_address = get_user_meta($user_id, '_vendor_bitoin_address', true);
		
        $vendor_btc_address = array_merge($arg, array("bitcoin_address" => array('label' => __('Enter Bitcoin Adress', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele paymode_field paymode_bitcoin_payout paymode_bitcoin_masspay paymode_bitcoin_adaptive', 'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_bitcoin_masspay paymode_bitcoin_masspay paymode_bitcoin_adaptive', 'value' => $bitcoin_address )));
        return $vendor_btc_address;
    }

    function admin_bitcoin_payment_mode($arg) {
        $admin_payment_mode_select = array_merge($arg, array('bitcoin_masspay' => __('Bitcoin Gateway', 'saved-cards')));
        return $admin_payment_mode_select;
    }

    function vendor_bitcoin_payment_mode($arg) {
        $payment_mode = array();
        if (isset($this->payment_admin_settings['payment_method_bitcoin_masspay']) && $this->payment_admin_settings['payment_method_bitcoin_masspay'] = 'Enable') {
            $payment_mode['bitcoin_masspay'] = __('Bitcoin Gateway', 'saved-cards');
        }
        $vendor_payment_mode_select = array_merge($arg, $payment_mode);
        return $vendor_payment_mode_select;
    }

    /**
     * This will add bitcoin address field at marketplace
     */
    function vendor_bitcoin_address($user = '') {
        if (empty($user)) {
            $user = wp_get_current_user();
        }
        $user_id = $user->ID;
        $vendor = get_wcmp_vendor($user_id);
        if ($vendor) {
            $bitcoin_settings = get_option('wcmp_payment_bitcoin_gateway_settings_name');
            if (isset($bitcoin_settings) && !empty($bitcoin_settings)) {
                if (isset($bitcoin_settings['enabled']) && $bitcoin_settings['enabled'] == 'no') {
                    return;
                }
				//print_r($bitcoin_settings);
                //$secret_key = $bitcoin_settings['bitaps_redeam_code'];
				
				 $btc_address = get_user_meta($user_id, '_vendor_bitoin_address', true);

				?>
			<div class="wcmp_headding2"><?php _e('Bitcoin', 'dc-woocommerce-multi-vendor'); ?></div>
            <p><?php _e('Enter your Bitcoin Address', 'dc-woocommerce-multi-vendor'); ?></p>
            <input  class="long no_input" readonly type="text" name="bitcoin_address" value="<?php echo $btc_address; ?>"  placeholder="<?php _e('Enter Bitcoin Address', 'dc-woocommerce-multi-vendor'); ?>">
            <?php
            }
        } else {
            ?>
            <div><?php _e('You are not a Vendor. Please Login as a Vendor.', 'saved-cards'); ?></div>
            <?php
        }
    }

    function save_bitcoin_address() {
		if (empty($user)) {
            $user = wp_get_current_user();
        }
        $user_id = $user->ID;
        if (isset($_POST['bitcoin_address'])) {
			$btc_address = sanitize_text_field($_POST['bitcoin_address']);
			update_user_meta( $user_id, '_vendor_bitoin_address', $btc_address );	
		}
    }

}
?>