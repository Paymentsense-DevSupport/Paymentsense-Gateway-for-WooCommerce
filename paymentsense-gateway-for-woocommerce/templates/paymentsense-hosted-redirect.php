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
<form action="<?php echo esc_url( $hpf_url ); ?>" method="post" id="pms_payment_form" target="_top">
	<?php foreach ( $hpf_arguments as $name => $value ) { ?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
	<?php } ?>
	<div class="payment_buttons">
		<input type="submit" class="button alt" id="pms_submit_button" value="<?php echo esc_attr( $hpf_submit_button ); ?>" />
	</div>
</form>
<script type="text/javascript">
	if ( typeof jQuery !== 'undefined' ) {
		jQuery( function() {
			jQuery( "body" ).block(
				{
					message: "<?php echo esc_js( $hpf_redirect_message ); ?>",
					overlayCSS: {
						background: "#eee",
						opacity: 0.6
					},
					css: {
						padding: 20,
						textAlign: "center",
						color: "#444",
						border: "2px solid #aaa",
						backgroundColor: "#fff",
						cursor: "wait",
						lineHeight: "32px"
					}
				}
			);
			jQuery( "#pms_payment_form" ).submit();
		});
	} else {
		document.getElementById( 'pms_payment_form' ).submit();
	}
</script>
