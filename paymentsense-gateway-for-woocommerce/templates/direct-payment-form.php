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
<table style="border: none;">
	<tr style="border: none;">
		<td style="border: none; width: 40%">
			<label for="psense_ccname"><?php esc_html_e( 'Card Name:', 'woocommerce-paymentsense' ); ?> <span
						class="required">*</span></label>
		</td>
		<td colspan="2" style="border: none;">
			<input type="text" class="input-text" id="psense_ccname"
						name="psense_ccname" autocomplete="off" />
		</td>
	</tr>
	<tr style="border: none;">
		<td style="border: none;">
			<label for="psense_ccnum"><?php esc_html_e( 'Credit Card Number:', 'woocommerce-paymentsense' ); ?> <span
						class="required">*</span></label>
		</td>
		<td colspan="2" style="border: none;">
			<input type="text" class="input-text" id="psense_ccnum"
						name="psense_ccnum" autocomplete="off" />
		</td>
	</tr>
	<tr>
		<td style="border: none;">
			<label for="psense_cv2"><?php esc_html_e( 'CVV/CV2 Number:', 'woocommerce-paymentsense' ); ?> <span
						class="required">*</span></label>
		</td>
		<td style="border: none;">
			<input type="text" class="input-text" id="psense_cv2" name="psense_cv2"
						maxlength="4" style="width:60px" autocomplete="off" />
		</td>
		<td style="border: none;">
				<span class="help"><a href="https://www.cvvnumber.com/cvv.html" target="_blank"
						style="font-size:11px">What is my CVV code?</a></span>
		</td>
	</tr>
	<tr>
		<td style="border: none;">
			<label for="psense_issueno"><?php esc_html_e( 'Issue Number:', 'woocommerce-paymentsense' ); ?></label>
		</td>
		<td style="border: none;">
			<input type="text" class="input-text" id="psense_issueno" name="psense_issueno"
						style="width:60px" autocomplete="off" />
		</td>
		<td style="border: none;">
			<span class="help"><?php esc_html_e( '(Maestro/Solo only).', 'woocommerce-paymentsense' ); ?></span>
		</td>
	</tr>
	<tr>
		<td style="border: none;">
			<label for="psense_expmonth"><?php esc_html_e( 'Expiration date:', 'woocommerce-paymentsense' ); ?> <span
						class="required">*</span></label>
		</td>
		<td style="border: none;">
			<select name="psense_expmonth" id="psense_expmonth"
					class="woocommerce-select woocommerce-cc-month">
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
		</td>
		<td style="border: none;">
			<select name="psense_expyear" id="psense_expyear"
					class="woocommerce-select woocommerce-cc-year">
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
