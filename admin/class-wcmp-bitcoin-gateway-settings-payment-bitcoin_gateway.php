<?php

class WCMp_Payment_Bitcoin_Gateway_Settings_Gneral {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp, $WCMp_Bitcoin_Gateway;

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "default_settings_section" => array("title" => __('', 'marketplace-bitcoin-gateway'), // Section one
                    "fields" => array(
                        "is_enable_bitcoin" => array('title' => __('Enable Bitcoin Gateway', 'marketplace-bitcoin-gateway'), 'type' => 'checkbox', 'value' => 'Enable'), // Checkbox
                        "bitaps_redeam_code" => array('title' => __('Bitaps Redeem Code', 'marketplace-bitcoin-gateway'), 'type' => 'text', 'id' => 'bitaps_redeam_code', 'label_for' => 'bitaps_redeam_code', 'name' => 'bitaps_redeam_code', 'hints' => __('Get your Bitaps Redeem Code from https://bitaps.com/api/#Create_Redeem_Code', 'marketplace-bitcoin-gateway'), 'placeholder' => __('Bitaps Redeem Code', 'marketplace-bitcoin-gateway')),
						 "bitaps_invoice_code" => array('title' => __('Bitaps Invoice Code', 'marketplace-bitcoin-gateway'), 'type' => 'text', 'id' => 'bitaps_invoice_code', 'label_for' => 'bitaps_invoice_code', 'name' => 'bitaps_invoice_code', 'hints' => __('Get your Bitaps Invoice Code from https://bitaps.com/api/#Create_Redeem_Code', 'marketplace-bitcoin-gateway'), 'placeholder' => __('Bitaps Invoice Code', 'marketplace-bitcoin-gateway')),                    ),
                )
            )
        );
		 
        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_payment_bitcoin_gateway_settings_sanitize($input) {
        global $WCMp_Bitcoin_Gateway;
        $new_input = array();

        $hasError = false;

        if (isset($input['is_enable_bitcoin'])) {
            $new_input['is_enable_bitcoin'] = sanitize_text_field($input['is_enable_bitcoin']);
        }
        
        if(isset($input['bitaps_redeam_code'])){
            $new_input['bitaps_redeam_code'] = sanitize_text_field($input['bitaps_redeam_code']);
        }
		if(isset($input['bitaps_invoice_code'])){
            $new_input['bitaps_invoice_code'] = sanitize_text_field($input['bitaps_invoice_code']);
        }


        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Bitcoin Gateway Settings Updated', 'marketplace-bitcoin-gateway'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
    public function default_settings_section_info() {
        global $WCMp_Bitcoin_Gateway;
        printf(__('', 'marketplace-bitcoin-gateway'));
    }

    /**
     * Print the Section text
     */
    public function WCMp_Bitcoin_Gateway_store_policies_admin_details_section_info() {
        global $WCMp_Bitcoin_Gateway;
    }

}
