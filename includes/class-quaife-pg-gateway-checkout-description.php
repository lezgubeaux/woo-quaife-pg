<?php

/**
 * Called as needed
 *
 * @link       https://framework.tech
 * @since      1.0.0
 *
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 */

/**
 * Add Description fields to Checkout with Quaife Payments (as WC_Payment_Gateway extension)
 *
 * @since      1.1.0
 * @package    Quaife_Pg
 * @subpackage Quaife_Pg/includes
 * @author     Vladimir Eric <Vladimir@framework.tech>
 */

add_filter('woocommerce_gateway_description', 'quaife_pg_description', 20, 2);

function quaife_pg_description($description, $payment_id)
{
    if ($payment_id !== 'woo-quaife-pg') {
        return $description;
    }

    ob_start();

    $description .= ob_get_clean();

    /**
     * iframe of PP 
     * (js source: #quaife_payment_iframe -> target: .payment_box.payment_method_woo-quaife-pg)
     */

    $content = '';

    // get saved url (grabbed from api check)
    $iframe_src = get_option('quaife_pg_api_iframe');

    if (!$iframe_src === false) {
        $content .= '
		<div id="quaife_payment_iframe">
            <iframe src="' . $iframe_src . '" title="Quaife Payment Gateway"></iframe>
        </div>';
    } else {
        // dummy example, remove on release
        $content .= '
        <div id="quaife_payment_iframe">
            <iframe class="quaife_pg_iframe" src="https://paymentpage.quaife.net/payment-page/ddqsy1oq2gywxqzxxj07ipbppd31afxycj620bmv/info" title="Quaife Payment Gateway"></iframe>
        </div>';
    }

    return $description . ' Here comes detailed description of the payment.' . $content;
}
