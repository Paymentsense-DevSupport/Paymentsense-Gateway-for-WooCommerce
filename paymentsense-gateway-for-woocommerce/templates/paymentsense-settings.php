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
<div id="ps_diagnostic_messages"></div>
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
		jQuery( function() {
			jQuery.get(
				"<?php echo esc_url_raw( $module_info_url ); ?>", {}, function( result ) {
					jQuery.each( result, function( name, data ) {
						if ( data.hasOwnProperty( "text" ) && data.hasOwnProperty( "class" ) ) {
							let div_id = "ps_diagnostic_message_" + name;
							jQuery( "<div>" ).attr( "id", div_id ).appendTo( "#ps_diagnostic_messages" );
							jQuery( "<p>" ).html( data.text ).appendTo( "#" + div_id );
							jQuery( "#" + div_id ).addClass( data.class );
						}
					});
				}
			);
		});
	} else {
		let err_text = document.createTextNode("jQuery not found. Please enable jQuery.");
		let err_para = document.createElement("p");
		let err_div = document.createElement("div");
		err_para.appendChild(err_text);
		err_div.appendChild(err_para);
		err_div.className = "<?php echo esc_html( $this_::ERROR_CLASS_NAME ); ?>";
		document.getElementById("ps_diagnostic_messages").appendChild(err_div);
	}
</script>
