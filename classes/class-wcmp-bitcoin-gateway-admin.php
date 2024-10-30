<?php

class WCMp_Bitcoin_Gateway_Admin {

    public $settings;

    public function __construct() {
        //admin script and style
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
        add_action('WCMp_Bitcoin_Gateway_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_WCMp_Bitcoin_Gateway'));
        if (class_exists('WCMp')) {
            add_filter('wcmp_tabsection_payment', array(&$this, 'wcmp_tabsection_payment_callback'));
            add_action('settings_page_payment_bitcoin_gateway_tab_init', array(&$this, 'payment_bitcoin_gateway_tab_init'), 10, 5);
            add_action('admin_menu', array(&$this, 'register_bitcoin_vendor_menus'), 999);

        }
    }


    /**
         * Add Bitcoin Gateway menu page
         */
        public function register_bitcoin_vendor_menus() {

               add_menu_page(__('Bitaps Gateway', 'marketplace-bitcoin-gateway'), __('Bitcoin Gateway', 'marketplace-bitcoin-gateway'), 'manage_options', 'wcpv-vendor-bitcoin', array($this, 'render_bitcoin_page'), 'dashicons-paperclip', 70);
        }
		public function post_admin_api($url, $postfields) {
				$data = wp_remote_post( $url, array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => $postfields,
					'cookies' => array()
					)
				);
					if( is_wp_error( $data ) ) {
						return false;
					}

			$results = wp_remote_retrieve_body( $data );	

			return $results;
		}
        /**
         * Render Bitcoin Gateway account
         */
        public function render_bitcoin_page() {
			$bitcoin_settings = get_option('wcmp_payment_bitcoin_gateway_settings_name');
			 $redeemcode = $bitcoin_settings['bitaps_redeam_code'];		
			if (!empty($redeemcode)) {
			  $postfields = json_encode(array('redeemcode'=> $redeemcode));
			  $data = $this->post_admin_api("https://bitaps.com/api/get/redeemcode/info", $postfields);
			  $respond = json_decode($data,true);
			  $address = $respond["address"]; // Redeem code receiver address
			  $balance = $respond["balance"]; // Current balance
			  $pending_balance = $respond["pending_balance"]; // Pending balance (unconfirmed)
			  $paid_out = $respond["paid_out"]; // Paid out amount
				if (isset($_POST['withdraw'])){
					$amount = sanitize_text_field($_POST['admin_btc_balance']);
					$bitcoin_wallet = sanitize_text_field($_POST['admin_btc_wallet']);
					$amount_to_pay = ($amount)*(pow(10, 8)); //Returns 5000
					$transfer_args = array(
					   'redeemcode'=> $redeemcode,
						'address' => $bitcoin_wallet,
						'amount' => $amount_to_pay			
					);
				  $postfields = json_encode($transfer_args);
				  $data = $this->post_admin_api("https://bitaps.com/api/use/redeemcode", $postfields);
				  $respond = json_decode($data,true);
				 // print_r($respond);
				  $tx_hash = $respond["tx_hash"]; //Transfer transaction hash
				  if ($tx_hash){
					  $response = '<h1 style="color:blue;">Payment Sent to '. $bitcoin_wallet.'</h1>';
					   $response .= '<h1 style="color:blue;">TX Hash:'. $tx_hash.'</h1>';
				  }else{
					 $response = '<h1 style="color:red;">'.$respond["error"].'</h1>';
				  }
   
				}
			  ?>
            <div class="wrap">
				<h2>Here your account informations.</h21>
				<h2>Redeem Code: <?php echo $redeemcode; ?></h2>
				<h2>Bitcoin Wallet: <?php echo $address; ?></h2>
				<h2>Bitcoin Wallet Balance: <?php echo $balance; ?></h2>
				<h2>Bitcoin Wallet Pending Balance: <?php echo $pending_balance; ?></h2>
				<h2>Bitcoin Wallet Paid Balance: <?php echo $paid_out; ?></h2>
				 <form action="" method="post">
						Bitcoin Wallet to Withdraw<input type="text" name="admin_btc_wallet" value="" /><br />
						Bitcoin Balance<input type="text" name="admin_btc_balance" value="<?php echo $balance; ?>" /><br />
                        <button type="submit" name="withdraw" class="button button-primary"><?php _e('Withdraw Balance', 'marketplace-bitcoin-gateway'); ?></button>
				</form>
				<?php echo  $response ; ?>
			</div>
            <?php
			}else{
			  $confirmations = 3; // the desired number of confirmations
			  $data = file_get_contents("https://bitaps.com/api/create/redeemcode?confirmations=". $confirmations);
			  $respond = json_decode($data,true);
			  $address = $respond["address"]; // Bitcoin address to receive payments
			  $redeem_code = $respond["redeem_code"]; //Redeem Code for sending payments
			  $invoice = $respond["invoice"]; // Invoice to view payments and transactions
			  ?>
            <div class="wrap">
				<h1>You have to save redeem code at <a href="/wp-admin/admin.php?page=wcmp-setting-admin&tab=payment&tab_section=bitcoin_gateway">Setting Page</a></h1>
				<h2>Redeem Code: <?php echo $redeem_code; ?></h2>
				<h2>Invoice: <?php echo $invoice; ?> </h2>
				<h2>Bitcoin Wallet: <?php echo $address; ?></h2>
            </div>
            <?php
			}
           
          
        }
    /**
     * Add setting option to WCMp for Bitcoin
     * @param array $submenue_tab
     * @return array
     */
    function wcmp_tabsection_payment_callback($submenue_tab) {
        $submenue_tab['bitcoin_gateway'] = __('Bitcoin Gateway', 'marketplace-bitcoin-gateway');
        return $submenue_tab;
    }
    /**
     * Create Bitcoin option in WCMp setting page
     * @global type $WCMp_Bitcoin_Gateway
     * @param string $tab
     * @param string $subsection
     */
    function payment_bitcoin_gateway_tab_init($tab, $subsection) {
        global $WCMp_Bitcoin_Gateway;
        $this->load_class("settings-{$tab}-{$subsection}", $WCMp_Bitcoin_Gateway->plugin_path, $WCMp_Bitcoin_Gateway->token);
        new WCMp_Payment_Bitcoin_Gateway_Settings_Gneral($tab, $subsection);
    }

    function load_class($class_name = '') {
        global $WCMp_Bitcoin_Gateway;

        if ('' != $class_name) {
            require_once ($WCMp_Bitcoin_Gateway->plugin_path . '/admin/class-' . esc_attr($WCMp_Bitcoin_Gateway->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }

// End load_class()

    function dualcube_admin_footer_for_WCMp_Bitcoin_Gateway() {
        global $WCMp_Bitcoin_Gateway;
        ?>
        <div style="clear: both"></div>
        <div id="dc_admin_footer">
  
        </div>
        <?php
    }

    /**
     * Admin Scripts
     */
    public function enqueue_admin_script() {
        global $WCMp_Bitcoin_Gateway;
        $screen = get_current_screen();

        // Enqueue admin script and stylesheet from here
        if (in_array($screen->id, array('toplevel_page_wcmp-bitcoin-gateway-setting-admin'))) :
            $WCMp_Bitcoin_Gateway->library->load_qtip_lib();
            wp_enqueue_script('admin_js', $WCMp_Bitcoin_Gateway->plugin_url . 'assets/admin/js/admin.js', array('jquery'), $WCMp_Bitcoin_Gateway->version, true);
            wp_enqueue_style('admin_css', $WCMp_Bitcoin_Gateway->plugin_url . 'assets/admin/css/admin.css', array(), $WCMp_Bitcoin_Gateway->version);
        endif;
    }

}