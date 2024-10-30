<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Product_Vendors_Bitcoin_Connect extends WCMp_Payment_Gateway {

    public $id;
    public $message = array();
	private $vendor_bitcoin_address;
    private $bitcoin_settings;
    private $bitaps_redeam_code;

    public function __construct() {
        $this->id = 'bitcoin_masspay';
        $this->payment_gateway = $this->id;
        $this->enabled = get_wcmp_vendor_settings('payment_method_bitcoin_masspay', 'payment');
    }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'auto') {
        $this->vendor = $vendor;
        $this->commissions = $commissions;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        $this->vendor_bitcoin_address = get_user_meta($this->vendor->id, '_vendor_bitoin_address', true);
        $this->bitcoin_settings = get_option('wcmp_payment_bitcoin_gateway_settings_name');
        $this->bitaps_redeam_code = $this->bitcoin_settings['bitaps_redeam_code'];
        if ($this->validate_request()) {
            if($this->process_bitcoin_payment()){
                $this->record_transaction();
                if ($this->transaction_id) {
                    return array('message' => __('New transaction has been initiated', 'marketplace-bitcoin-gateway'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
                }
            } else{
                return $this->message;
            }
        } else{
            return $this->message;
        }
    }
	public function post_api($url, $postfields) {
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

    public function validate_request() {
        global $WCMp;
        if ($this->enabled != 'Enable') {
            $this->message[] = array('message' => __('Invalid payment method', 'marketplace-bitcoin-gateway'), 'type' => 'error');
            return false;
        }
        if ($this->transaction_mode != 'admin') {
            /* handel thesold time */
            $threshold_time = isset($WCMp->vendor_caps->payment_cap['commission_threshold_time']) && !empty($WCMp->vendor_caps->payment_cap['commission_threshold_time']) ? $WCMp->vendor_caps->payment_cap['commission_threshold_time'] : 0;
            if ($threshold_time > 0) {
                foreach ($this->commissions as $index => $commission) {
                    if (intval((date('U') - get_the_date('U', $commission)) / (3600 * 24)) < $threshold_time) {
                        unset($this->commissions[$index]);
                    }
                }
            }
            /* handel thesold amount */
            $thesold_amount = isset($WCMp->vendor_caps->payment_cap['commission_threshold']) && !empty($WCMp->vendor_caps->payment_cap['commission_threshold']) ? $WCMp->vendor_caps->payment_cap['commission_threshold'] : 0;
            if ($this->get_transaction_total() > $thesold_amount) {
                return true;
            } else {
                $this->message[] = array('message' => __('Minimum thesold amount to withdrawal commission is ' . $thesold_amount, 'marketplace-bitcoin-gateway'), 'type' => 'error');
                return false;
            }
        }
        return parent::validate_request();
    }

    private function process_bitcoin_payment() {
     	   $transfer_args = array(
			   'redeemcode'=> $this->bitaps_redeam_code,
                'address' => $this->vendor_bitcoin_address,
				'amount' => $this->get_bitcoin_amount()				
            );
			
		  //print_r($transfer_args);
		  $postfields = json_encode($transfer_args);
		  $data = $this->post_api("https://bitaps.com/api/use/redeemcode", $postfields);
		  $respond = json_decode($data,true);
		  $commissions = $_POST['commissions'];

		  foreach ($commissions as $commission_id){
			$commission = get_post($commission_id);
		    $orderid = get_post_meta($commission->ID, '_commission_order_id',true); 
			$is_shipped = get_post_meta($orderid, 'dc_pv_shipped', true);
			if (!$is_shipped) {
			 $this->message[] = array('message' => 'Please ship this Order:'.$orderid.' then you can process withdraw...', 'type' => 'error');
			}else{
			  $tx_hash = $respond["tx_hash"]; //Transfer transaction hash
			  if ($tx_hash){
				 $this->message[] = array('message' => $tx_hash);
				  return true;
			  }else{
				 $this->message[] = array('message' => $respond["error"], 'type' => 'error');
				  return false;
			  }
			}
		  }
	}
    
    private function get_bitcoin_amount(){
        $amount_to_pay = $this->get_transaction_total() - $this->transfer_charge($this->transaction_mode) - $this->gateway_charge();
		$amount_to_pay = ($amount_to_pay)*(pow(10, 8)); //Returns 5000
        return $amount_to_pay;
    }

}