<?php
/**
 * Plugin Name: Woo Paid Order Email for WooCommerce
 * Plugin URI: 
 * Description: WooPoe sends a notification e-mail to the buyer once the order is payed
 * Author: ffd-web.com
 * Author URI: http://www.ffd-web.com
 * Version: 1.0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 1.0
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function add_paid_order_woocommerce_email( $email_classes ) {
 
    // include custom email class
    require( 'includes/class-woopoe-paid-order-email.php' );
 
    // adds it to the woocommerce email class
    $wpoe_class['WC_Paid_Order_Email'] = new WC_Paid_Order_Email();
 
    return $wpoe_class;
 
}
add_filter( 'woocommerce_email_classes', 'add_paid_order_woocommerce_email' );