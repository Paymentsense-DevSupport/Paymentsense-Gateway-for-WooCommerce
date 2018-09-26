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
<p>
	<?php echo esc_html( $title ); ?>
</p>
<p>
	<img src="<?php echo esc_url( $logo ); ?>" />
</p>
<form action="<?php echo esc_url( $paymentsense_adr ); ?>" method="post" id="paymentsense_payment_form" target="_top">
	<?php
	foreach ( $arguments as $key => $value ) {
		?>
		<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}
	?>
        <!-- Button Fallback -->
        <div class="payment_buttons">
            <input type="submit" class="button alt" id="submit_paymentsense_payment_form" value="<?php echo __( 'Pay via Paymentsense', 'woocommerce-paymentsense' ); ?>" />
            <a class="button cancel" href="<?php esc_url( $cancel_url ); ?>"><?php echo __( 'Cancel order &amp; restore cart', 'woocommerce-paymentsense' ); ?></a>
        </div>
</form>
<script type="text/javascript">
	jQuery(function(){
		jQuery("body").block(
			{
				message: "<?php echo esc_js( __( 'We are now redirecting you to Paymentsense to complete your payment.', 'woocommerce-paymentsense' ) ); ?>",
				overlayCSS: {
					background: "#fff",
					opacity: 0.6
				},
				css: {
					padding: 20,
					textAlign: "center",
					color: "#555",
					border: "3px solid #aaa",
					backgroundColor: "#fff",
					cursor: "wait",
					lineHeight: "32px"
				}
			}
		);
	jQuery("#submit_paymentsense_payment_form").click();});
</script>
