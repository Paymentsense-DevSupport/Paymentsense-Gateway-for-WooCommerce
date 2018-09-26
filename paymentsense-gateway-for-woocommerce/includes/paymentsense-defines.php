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
define( 'PS_IMG_LOGO', '../assets/images/paymentsense.gif' );
define( 'PS_IMG_CARDS_WITH_AMEX', '../assets/images/paymentsense-logos-with-amex.png' );
define( 'PS_IMG_CARDS_WITHOUT_AMEX', '../assets/images/paymentsense-logos-no-amex.png' );
define( 'PS_IMG_SPINNER', '../assets/images/AJAXSpinner.gif' );
