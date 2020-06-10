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
	<?php echo wp_kses_post( wpautop( $description ) ); ?>
</p>
<style type="text/css">
	.paymentsense-card-form tr td {
		border: none;
	}
	.paymentsense-help {
		font-size: 0.8em;
	}
	.paymentsense-small {
		font-size: 0.9em;
	}
	.paymentsense-reduced-width{
		max-width: 6em;
	}
</style>
<table class="paymentsense-card-form">
	<tr>
		<td>
			<label for="psense_ccname"><?php esc_html_e( 'Cardholder’s Name', 'woocommerce-paymentsense' ); ?>: <span class="required">*</span></label>
		</td>
		<td>
			<input type="text" class="input-text" id="psense_ccname" name="psense_ccname" autocomplete="off" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="psense_ccnum"><?php esc_html_e( 'Card Number', 'woocommerce-paymentsense' ); ?>: <span class="required">*</span></label>
		</td>
		<td>
			<input type="text" class="input-text" id="psense_ccnum" name="psense_ccnum" autocomplete="off" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="psense_cv2"><?php esc_html_e( 'CVV/CV2 Number', 'woocommerce-paymentsense' ); ?>: <span class="required">*</span></label>
		</td>
		<td>
			<input type="text" class="input-text paymentsense-reduced-width" id="psense_cv2" name="psense_cv2" maxlength="4" autocomplete="off" />
			<span><a class="paymentsense-help" href="https://www.cvvnumber.com/cvv.html" target="_blank">What is my CVV code?</a></span>
		</td>
	</tr>
	<tr>
		<td>
			<label for="psense_expmonth"><?php esc_html_e( 'Expiration date', 'woocommerce-paymentsense' ); ?>: <span class="required">*</span></label>
			<label for="psense_expyear"></label>
		</td>
		<td>
			<select name="psense_expmonth" id="psense_expmonth" class="select2-container">
				<option value=""><?php esc_html_e( 'Month', 'woocommerce-paymentsense' ); ?></option>
				<?php
				for ( $i = 1; $i <= 12; $i++ ) {
					$timestamp = mktime( 0, 0, 0, $i, 1 );
				?>
				<option value="<?php echo esc_html( date( 'n', $timestamp ) ); ?>"><?php echo esc_html( date( 'F', $timestamp ) ); ?></option>
				<?php
				}
				?>
			</select>
			<select name="psense_expyear" id="psense_expyear" class="select2-container">
				<option value=""><?php esc_html_e( 'Year', 'woocommerce-paymentsense' ); ?></option>
				<?php
				for ( $y = 0; $y <= 10; $y++ ) {
				?>
				<option value="<?php echo esc_html( date( 'y' ) + $y ); ?>"><?php echo esc_html( date( 'Y' ) + $y ); ?></option>
				<?php
				}
				?>
			</select>
		</td>
	</tr>
</table>
