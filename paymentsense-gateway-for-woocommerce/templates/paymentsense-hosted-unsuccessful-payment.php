<?php
/**
 * Paymentsense Gateway Template
 *
 * @package WooCommerce_Paymentsense_Gateway
 * @subpackage WooCommerce_Paymentsense_Gateway/templates
 * @author Paymentsense
 * @link http://www.paymentsense.co.uk/
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<style>
	.woocommerce-thankyou-order-failed, .woocommerce-thankyou-order-failed-actions {
		display: none;
	}
	.paymentsense_payment_buttons {
		padding-bottom: 30px;
	}
</style>
<ul class="woocommerce-error" role="alert">
	<li><?php echo esc_html( $error_message ); ?></li>
</ul>
<div class="paymentsense_payment_buttons">
	<a class="button button-primary" href="<?php echo esc_url( $checkout_payment_url ); ?>"><?php echo esc_attr( $retry_payment_button ); ?></a>
</div>
