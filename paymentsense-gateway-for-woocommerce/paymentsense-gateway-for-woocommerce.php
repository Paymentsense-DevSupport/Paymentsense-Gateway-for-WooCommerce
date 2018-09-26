<?php
/**
 * Paymentsense Gateway for WooCommerce.
 *
 * Plugin Name:          Paymentsense Gateway for WooCommerce
 * Description:          Extends WooCommerce by taking payments via Paymentsense
 * Version:              3.0.0
 * Author:               Paymentsense
 * Author URI:           http://www.paymentsense.co.uk/
 * License:              GNU General Public License v3.0
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:          woocommerce-paymentsense
 * Requires at least:    4.4
 * Tested up to:         4.9.4
 * WC requires at least: 3.0.9
 * WC tested up to:      3.3.3
 *
 * @package WooCommerce_Paymentsense_Gateway
 * @wordpress-plugin
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
 * Hooks Paymentsense on the plugins_loaded action if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

	if ( ! function_exists( 'woocommerce_paymentsense_init' ) ) {
		/**
		 * Paymentsense Init function
		 */
		function woocommerce_paymentsense_init() {
			if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
				return;
			}

			load_plugin_textdomain(
				'woocommerce-paymentsense', false, basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'languages'
			);

			require_once plugin_dir_path( __FILE__ ) . 'includes/helper-iso-codes.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/paymentsense-defines.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-paymentsense-lib.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-paymentsense-hosted.php';
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-paymentsense-direct.php';

			if ( ! function_exists( 'woocommerce_add_paymentsense_gateways' ) ) {
				/**
				 * Adds Paymentsense payments into the WooCommerce payment gateways
				 *
				 * @param array $methods WooCommerce payment gateways.
				 * @return array WooCommerce payment gateways
				 */
				function woocommerce_add_paymentsense_gateways( $methods ) {
					$methods[] = 'WC_Paymentsense_Hosted';
					$methods[] = 'WC_Paymentsense_Direct';
					return $methods;
				}
				add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_paymentsense_gateways' );
			}
		}
	}
	add_action( 'plugins_loaded', 'woocommerce_paymentsense_init', 0 );
}
