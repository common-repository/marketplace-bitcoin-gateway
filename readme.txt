=== Marketplace Bitcoin Gateway ===
Contributors:   umershaikh
Tags: Marketplace Bitcoin gateway, wcmp Bitcoin gateway, Bitcoin payment, Bitcoin commission, wc marketplace, wc market place, WooCommerce vendors, woocommerce marketplace, vendor, vendors, vendor system, woocommerce market place, WooCommerce multivendor, WooCommerce multi vendor, woocommerce vendors, woo vendors, WooCommerce vendors, Woocommerce Shipping,wc marketplace shipping, wcmp shipping, multivendor, multi vendor, multi vendors,  multi seller
Donate link: https://blockchain.info/payment_request?address=15WT142AfgsXyqc9jStcCb5NwpM5sUryfb&amount=0.005&message=Donation
Requires at least: 4.2
Tested up to: 4.8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Free Payment Gateway for Marketplace allowing you to Pay Your Vendors Using Bitcoin.


== Description ==

Marketplace Bitcoin Gateway allows you, the marketplace owner, to mass pay your vendors' commission using Bitcoins. It supports both manual and schedule disbursement of payments. Make sure you have any [WooCommerce Bitcoin Payment Gateway] installed on your site, to activate Marketplace Bitcoin Gateway. Currently supported multi vendor plugin is [WC Marketplace](https://wordpress.org/plugins/dc-woocommerce-multi-vendor/). Note we are using bitaps api (https://bitaps.com/api) to send and recieve bitcoins. 

<strong>Admin can</strong>

Pay the vendor their commission via Bitcoin by activating the plugin.

<strong>Vendor can</strong>

Vendor just need put his wallet address to accept the commission via Bitcoin.

<strong>Bitcoin Advantages</strong>
- Easy to set up, funds go directly into your wallet and the vendors receives their part of the commission in their wallet after the schedule.
- Admins pay no extra fee to receive admins portion of the sale, vendor or customer will pays all fees.

== Installation ==

NOTE:  Marketplace Bitcoin Gateway plugin is an extension of WooCommerce and WooCommerce Bitcoin Payment Gateway. As such, WooCommerce and any WooCommerce Bitcoin Payment Gateway plugin needs to be installed and activated on your WordPress site for this plugin to work properly.


1. Download and install Woocommerce
2. Download and install any WooCommerce Bitcoin Payment Gateway Plugin
3. Download and install WC Marketplace Plugin.

4. Download and install Marketplace Bitcoin Gateway plugin using the built-in Word Press plugin installer. If you download Marketplace Bitcoin Gateway plugin 
   manually, make sure that it's uploaded to /WP-content/plugins/ and activate the plug in from the Plugin menu from your WordPress dashboard.      
   Alternatively, follow these steps below and install the addon: 
   Plugins > Add new > Upload plugin > Upload marketplace-bitcoin-gateway.zip > Install Now > Activate Plug in.
5. Active marketplace features from Woocommerce > WCMp > Payment tab > Bitcoin Gateway sub tab.




== Frequently Asked Questions ==
= Which API Used to Send and Receive Bitcoins?
Ans. https://bitaps.com/api, Go through this url and see how it works. It is Open Restfull Api you don't ever need to register on their site. They used Redeem code to send bitcoin.
= What is Redeemcode in bitaps api?
Ans. Redeem code like an api key, Like an complete wallt and account where you can send and receive bitcoins, We can get redeem code with php,  Every redeem code (When we create) contain wallet address and invoice. See on there https://bitaps.com/api/#Create_Redeem_Code, So we just created an plugin page which will create redeem code. Here is plugin page /wp-admin/admin.php?page=wcpv-vendor-bitcoin, So you can collect Redeem code, Wallet address, invoice code from plugin page. and save them to gateways.
= How Gateway Work?
Ans. From plugin page you can get wallet address. So when user checkout you can set wallet address to collect bitcoins. Now Vendor's could be paid from redeemcode.
= When Vendor can withdraw his comission?
Ans. Vendor can withdraw his comission in bitcoins when he shipped the item otherewise he connot able to withdraw bitcoins.
= Does this plugin work with newest WP version and also older versions? =
Ans. Yes, this plugin works fine with WordPress 4.8! It is also compatible for older WordPress versions from 4.2
= Up to which version of WooCommerce this plugin compatible with? =
Ans. This plugin is compatible with WooCommerce 3.0.
= Up to which version of php this plugin is compatible with? =
Ans. This plugin is tested with php version 5.6.
= What Will be the plug in requirements for working with this plugin? =
Ans. Woocommerce, Any WooCommerce Bitcoin Payment Gateway, WC Marketplace Plugin must be installed in your system.


== Screenshots ==
1. Marketplace Settings: Enable or disable marketplace Bitcoin gateway.
2. Vendor Setting: Add vendor's wallet address.
3. Bitaps Plugin Page. User can get Redeem Code and Can Withdraw their balance.