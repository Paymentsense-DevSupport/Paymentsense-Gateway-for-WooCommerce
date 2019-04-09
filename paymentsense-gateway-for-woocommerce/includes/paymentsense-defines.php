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
 * Content Types for Module Information
 */
define( 'TYPE_APPLICATION_JSON', 'application/json' );
define( 'TYPE_TEXT_PLAIN', 'text/plain' );

/**
 * Images
 */
define( 'PS_IMG_LOGO', plugins_url( 'assets/images/paymentsense-logo.png', dirname( __FILE__ ) ) );
define( 'PS_IMG_SPINNER', plugins_url( 'assets/images/spinner.gif', dirname( __FILE__ ) ) );
