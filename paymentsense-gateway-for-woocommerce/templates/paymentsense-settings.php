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
if ( $this_ instanceof WC_Paymentsense_Hosted ) {
	$warning = $this_::get_warning_message();
	if ( ! empty( $warning ) ) {
		?>
		<div id="message" class="updated woocommerce-message">
			<p>
			<?php
			echo wp_kses(
				sprintf( '<strong>%s</strong>', $warning ),
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
}
?>
<div id="gateway_connectivity_info_div">
	<p id="gateway_connectivity_info_text"></p>
</div>
<?php
echo '<h2>' . esc_html( $title ) . '</h2>';
echo wp_kses_post( wpautop( $description ) );
?>
<p>
	<a href="https://mms.paymentsensegateway.com/" target="_blank">Paymentsense Merchant Management System (MMS)</a>
</p>
<table class="form-table">
	<?php $this_->generate_settings_html(); ?>
</table>
<script type="text/javascript">
	if ( typeof jQuery !== 'undefined' ) {
		function checkGatewayConnectivity() {
			jQuery.get(
				"<?php echo esc_url_raw( $module_info_url ); ?>", {}, function( result ) {
					if ( result.hasOwnProperty( "msg" ) && result.hasOwnProperty( "class" ) ) {
						jQuery( "#gateway_connectivity_info_text" ).html( result.msg );
						jQuery( "#gateway_connectivity_info_div" ).addClass( result.class );
					}
				}
			);
		}
		jQuery( function() {
			checkGatewayConnectivity();
		});
	} else {
		document.getElementById( 'gateway_connectivity_info_text' ).innerText = 'jQuery not found. Please enable jQuery.';
		document.getElementById( 'gateway_connectivity_info_div' ).className = 'notice notice-error';
	}
</script>