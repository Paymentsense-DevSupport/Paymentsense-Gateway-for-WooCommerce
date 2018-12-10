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
$incompatible_plugins = $this_->output_incompatible_plugins();
if ( ! empty( $incompatible_plugins ) ) {
	?>
<div id="message" class="updated woocommerce-message">
<p>
	<?php
	echo wp_kses(
		sprintf(
			'<strong class="red">%1$s</strong> %2$s',
			__( 'Incompatibility Warning:', 'woocommerce' ),
			$incompatible_plugins
		),
		array(
			'strong' => array(),
			'br'     => array(),
		)
	);
	?>
</p>
</div>
	<?php
}
echo '<h2>' . esc_html( $title ) . '</h2>';
echo wp_kses_post( wpautop( $description ) );
?>
<p>
	<a href="https://mms.paymentsensegateway.com/" target="_blank">Paymentsense Merchant Management System (MMS)</a>
</p>
<table class="form-table">
	<?php $this_->generate_settings_html(); ?>
</table>
