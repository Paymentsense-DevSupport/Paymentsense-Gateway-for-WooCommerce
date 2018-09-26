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
<html>
<head>
	<title>Processing Payment...</title>
	<?php wp_print_scripts(); ?>
</head>
<body style="max-height: 400px; overflow: hidden;">
<img src="<?php echo esc_url( $args['spinner'] ); ?>"
	alt="<?php esc_attr_e( 'Redirecting...', 'woocommerce-paymentsense' ); ?>"/>
<form name="pms<?php echo esc_attr( $args['target'] ); ?>" action="<?php echo esc_url( $args['pay_url'] ); ?>"
	method="post" target="<?php echo esc_attr( $args['target'] ); ?>" id="pms<?php echo esc_attr( $args['target'] ); ?>">
	<input name="CrossReference" type="hidden" value="<?php echo esc_attr( $args['crossref'] ); ?>"/>
	<input name="PaRes" type="hidden" value="<?php echo esc_attr( $args['pares'] ); ?>"/>
	<script type="text/javascript">
		window.onload = function () {
			var auto_refresh = setInterval(function () {
				submitform();
			}, 1000);
			function submitform() {
				document.getElementById("pms<?php echo esc_attr( $args['target'] ); ?>").submit();
				clearTimeout(auto_refresh);
			}
		}
	</script>
</form>
</body>
</html>
