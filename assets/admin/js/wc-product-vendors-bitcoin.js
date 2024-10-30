jQuery(document).ready(function ($) {
    $('#wcpv_vendor_settings_payment_gateway').change(function () {
               var BitcoinAddress = $('#wcpv_vendor_settings_bitcoin_address').parents('tr').eq(0);
            BitcoinAddress.show();
    }).change();
});