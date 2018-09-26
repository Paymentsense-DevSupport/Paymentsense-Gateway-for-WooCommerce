<?php
/**
 * Paymentsense Defines
 *
 * @package WooCommerce_Paymentsense_Gateway
 * @subpackage WooCommerce_Paymentsense_Gateway/includes
 * @author Paymentsense
 * @link http://www.paymentsense.co.uk/
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Transaction Result Codes
 */
define( 'PS_TRX_RESULT_SUCCESS', '0' );
define( 'PS_TRX_RESULT_INCOMPLETE', '3' );
define( 'PS_TRX_RESULT_REFERRED', '4' );
define( 'PS_TRX_RESULT_DECLINED', '5' );
define( 'PS_TRX_RESULT_DUPLICATE', '20' );
define( 'PS_TRX_RESULT_FAILED', '30' );

/**
 * Images
 */
define( 'PS_IMG_LOGO', plugins_url( 'assets/images/paymentsense.gif', dirname( __FILE__ ) ) );
define( 'PS_IMG_CARDS_WITH_AMEX', plugins_url( 'assets/images/paymentsense-logos-with-amex.png', dirname( __FILE__ ) ) );
define( 'PS_IMG_CARDS_WITHOUT_AMEX', plugins_url( 'assets/images/paymentsense-logos-no-amex.png', dirname( __FILE__ ) ) );
define( 'PS_IMG_SPINNER', plugins_url( 'assets/images/AJAXSpinner.gif', dirname( __FILE__ ) ) );
