<?php
/**
 * Paymentsense Hosted Payment Method
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

if ( ! class_exists( 'WC_Paymentsense_Hosted' ) ) {
	/**
	 * WC_Paymentsense_Hosted class.
	 *
	 * @extends Paymentsense_Lib
	 */
	class WC_Paymentsense_Hosted extends Paymentsense_Lib {
		/**
		 * Payment method ID
		 *
		 * @var string
		 */
		public $id = 'paymentsense_hosted';

		/**
		 * Payment method title
		 *
		 * @var string
		 */
		public $method_title = 'Paymentsense Hosted';

		/**
		 * Payment method description
		 *
		 * @var string
		 */
		public $method_description = 'Accept payments from Credit/Debit cards through Paymentsense Hosted';

		/**
		 * Specifies whether the payment method shows fields on the checkout
		 *
		 * @var bool
		 */
		public $has_fields = false;

		/**
		 * Paymentsense Hosted Class Constructor
		 */
		public function __construct() {
			parent::__construct();

			// Hooks actions.
			add_action(
				'woocommerce_update_options_payment_gateways_' . $this->id,
				array( $this, 'process_admin_options' )
			);
			add_action(
				'woocommerce_receipt_' . $this->id,
				array( $this, 'receipt_page' )
			);
			add_action(
				'woocommerce_api_wc_' . $this->id,
				array( $this, 'process_gateway_response' )
			);
		}

		/**
		 * Initialises settings form fields
		 *
		 * Overrides wc settings api class method
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'                              => array(
					'title'   => __( 'Enable/Disable:', 'woocommerce-paymentsense' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable ', 'woocommerce-paymentsense' ) . $this->method_title,
					'default' => 'yes',
				),

				'module_options'                       => array(
					'title'       => __( 'Module Options', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'The following options affect how the ', 'woocommerce-paymentsense' ) . $this->method_title . __( ' Module is displayed on the frontend.', 'woocommerce-paymentsense' ),
				),

				'title'                                => array(
					'title'       => __( 'Title:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the customer sees during checkout.', 'woocommerce-paymentsense' ),
					'default'     => $this->method_title,
					'desc_tip'    => true,
				),

				'description'                          => array(
					'title'       => __( 'Description:', 'woocommerce-paymentsense' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description which the customer sees during checkout.', 'woocommerce-paymentsense' ),
					'default'     => __( 'Pay securely by Credit or Debit card through ', 'woocommerce-paymentsense' ) . $this->method_title . '.',
					'desc_tip'    => true,
				),

				'order_prefix'                         => array(
					'title'       => __( 'Order Prefix:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the order prefix that you will see in the MMS.', 'woocommerce-paymentsense' ),
					'default'     => 'WC-',
					'desc_tip'    => true,
				),

				'gateway_settings'                     => array(
					'title'       => __( 'Gateway Settings', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'These are the gateway settings to allow you to connect with the Paymentsense gateway. (These are not the details used to login to the MMS)', 'woocommerce-paymentsense' ),
				),

				'gateway_merchant_id'                  => array(
					'title'       => __( 'Gateway MerchantID:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the gateway MerchantID not used with the MMS login. The Format should match the following ABCDEF-1234567', 'woocommerce-paymentsense' ),
					'default'     => '',
					'desc_tip'    => true,
				),

				'gateway_password'                     => array(
					'title'       => __( 'Gateway Password:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the gateway Password not used with the MMS login. The Password should use lower case and uppercase letters, and numbers only.', 'woocommerce-paymentsense' ),
					'default'     => '',
					'desc_tip'    => true,
				),

				'gateway_presharedkey'                 => array(
					'title'       => __( 'Gateway PreSharedKey:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is located within the MMS under "Account Admin Settings" > "Account Settings".', 'woocommerce-paymentsense' ),
					'default'     => '',
					'desc_tip'    => true,
				),

				'gateway_hashmethod'                   => array(
					'title'       => __( 'Gateway Hash Method:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'This is the hash method set in MMS under "Account Admin" > "Account Settings". By default, this will be SHA1.', 'woocommerce-paymentsense' ),
					'default'     => 'SHA1',
					'desc_tip'    => true,
					'options'     => array(
						'SHA1'     => 'SHA1',
						'MD5'      => 'MD5',
						'HMACSHA1' => 'HMACSHA1',
						'HMACMD5'  => 'HMACMD5',
					),
				),

				'gateway_transaction_type'             => array(
					'title'       => __( 'Transaction Type:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'If you wish to obtain authorisation for the payment only, as you intend to manually collect the payment via the MMS, choose Pre-auth.', 'woocommerce-paymentsense' ),
					'default'     => 'SALE',
					'desc_tip'    => true,
					'options'     => array(
						'SALE' => __( 'Sale', 'woocommerce-paymentsense' ),
						// TODO: Implementation of the pre-authorisation support
						// @codingStandardsIgnoreLine
						// 'PREAUTH' => __( 'Pre-Auth', 'woocommerce-paymentsense' ),
					),
				),

				'amex_accepted'                        => array(
					'title'       => __( 'Accept American Express?', 'woocommerce-paymentsense' ),
					'type'        => 'checkbox',
					'description' => __( 'Tick only if you have an American Express MID associated with your Paymentsense gateway account.', 'woocommerce-paymentsense' ),
					'label'       => 'Enable American Express',
					'default'     => 'no',
					'desc_tip'    => true,
				),

				'hosted_payment_form_additional_field' => array(
					'title'       => __( 'Payment Form Additional Field', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'These options allow the customer to change the email address and phone number on the payment form.', 'woocommerce-paymentsense' ),
				),

				'email_address_editable'               => array(
					'title'       => __( 'Email Address can be altered on payment form:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'This option allows the customer to change the email address that entered during checkout. By default the Paymentsense module will pass the customers email address that they entered during checkout.', 'woocommerce-paymentsense' ),
					'default'     => 'false',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),

				'phone_number_editable'                => array(
					'title'       => __( 'Phone Number can be altered on payment form:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'This option allows the customer to change the phone number that entered during checkout. By default the Paymentsense module will pass the customers phone number that they entered during checkout.', 'woocommerce-paymentsense' ),
					'default'     => 'false',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),

				'hosted_payment_form_mandatory_field'  => array(
					'title'       => __( 'Payment Form Mandatory Fields', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'These options allow you to change what fields are mandatory for the customers to complete on the payment form. (The default settings are recommended by Paymentsense)', 'woocommerce-paymentsense' ),
				),

				'address1_mandatory'                   => array(
					'title'       => __( 'Address Line 1 Mandatory:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'Define the Address Line 1 as a Mandatory field on the Payment form. This is used for the Address Verification System (AVS) check on the customers card. Recommended Setting "Yes".', 'woocommerce-paymentsense' ),
					'default'     => 'true',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),

				'city_mandatory'                       => array(
					'title'       => __( 'City Mandatory:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'Define the City as a Mandatory field on the Payment form.', 'woocommerce-paymentsense' ),
					'default'     => 'false',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),

				'state_mandatory'                      => array(
					'title'       => __( 'State/County Mandatory:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'Define the State/County as a Mandatory field on the Payment form.', 'woocommerce-paymentsense' ),
					'default'     => 'false',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),

				'postcode_mandatory'                   => array(
					'title'       => __( 'Post Code Mandatory:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'Define the Post Code as a Mandatory field on the Payment form. This is used for the Address Verification System (AVS) check on the customers card. Recommended Setting "Yes".', 'woocommerce-paymentsense' ),
					'default'     => 'true',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),

				'country_mandatory'                    => array(
					'title'       => __( 'Country Mandatory:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'Define the Country as a Mandatory field on the Payment form.', 'woocommerce-paymentsense' ),
					'default'     => 'false',
					'desc_tip'    => true,
					'options'     => array(
						'true'  => __( 'Yes', 'woocommerce-paymentsense' ),
						'false' => __( 'No', 'woocommerce-paymentsense' ),
					),
				),
			);
		}

		/**
		 * Determines if the payment method is available
		 *
		 * Checks whether the gateway merchant ID, password and pre-shared key are set
		 *
		 * @return  bool
		 */
		public function is_valid_for_use() {
			return (
				! empty( $this->gateway_merchant_id ) &&
				! empty( $this->gateway_password ) &&
				! empty( $this->gateway_presharedkey )
			);
		}

		/**
		 * Receipt page
		 *
		 * @param  int $order_id Order ID.
		 */
		public function receipt_page( $order_id ) {
			if ( $this->is_valid_for_use() ) {
				$this->output_redirect_form( $order_id );
			} else {
				$this->output_message(
					__( 'This module is not configured. Please configure gateway settings.', 'woocommerce-paymentsense' )
				);
			}
		}

		/**
		 * Outputs the redirecting form to the hosted page
		 *
		 * @param  int $order_id WooCommerce OrderID.
		 */
		private function output_redirect_form( $order_id ) {
			$order = new WC_Order( $order_id );
			$this->show_output(
				'hosted-redirect.php',
				array(
					'title'            => __( 'Thank you - your order is now pending payment. You should be automatically redirected to Paymentsense to make payment.', 'woocommerce-paymentsense' ),
					'logo'             => PS_IMG_LOGO,
					'paymentsense_adr' => $this->get_payment_form_url(),
					'arguments'        => $this->build_form_fields( $order ),
					'cancel_url'       => $order->get_cancel_order_url(),
				)
			);
		}

		/**
		 * Redirects to Redirect form (receipt page)
		 *
		 * Overrides  wc payment gateway class method
		 *
		 * @param int $order_id WooCommerce OrderId.
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order = new WC_Order( $order_id );
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		}

		/**
		 * Processes the payment gateway response
		 */
		public function process_gateway_response() {
			$location           = null;
			$transaction_status = 'failed';

			$order_id = wc_get_post_data_by_key( 'OrderID' );
			$message  = wc_get_post_data_by_key( 'Message' );

			if ( empty( $order_id ) ) {
				return;
			}

			$order = new WC_Order( $order_id );

			if ( ! $this->is_hash_digest_valid() ) {
				$message = 'Invalid HashDigest';
			} else {
				update_post_meta( (int) $order_id, 'CrossRef', wc_get_post_data_by_key( 'CrossReference' ) );
				switch ( wc_get_post_data_by_key( 'StatusCode' ) ) {
					case PS_TRX_RESULT_SUCCESS:
						$transaction_status = 'success';
						break;
					case PS_TRX_RESULT_REFERRED:
						$transaction_status = 'failed';
						break;
					case PS_TRX_RESULT_DECLINED:
						$transaction_status = 'failed';
						break;
					case PS_TRX_RESULT_DUPLICATE:
						if ( PS_TRX_RESULT_SUCCESS === wc_get_post_data_by_key( 'PreviousStatusCode' ) ) {
							$transaction_status = 'success';
						} else {
							$transaction_status = 'failed';
						}
						break;
					case PS_TRX_RESULT_FAILED:
						$transaction_status = 'failed';
						break;
				}
			}
			switch ( $transaction_status ) {
				case 'success':
					$order->payment_complete();
					$order->add_order_note( __( 'Payment Successful: ', 'woocommerce-paymentsense' ) . $message, 0 );
					$location = wc_get_endpoint_url( 'order-received', $order_id, $order->get_checkout_order_received_url() );
					break;
				case 'failed':
					$order->update_status( 'failed', __( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . $message );
					wc_add_notice(
						__( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . $message . '<br />' .
						__( 'Please check your card details and try again.', 'woocommerce-paymentsense' ),
						'error'
					);
					$location = wc_get_endpoint_url( 'order-received', $order_id, $order->get_checkout_payment_url( false ) );
					break;
			}
			wp_safe_redirect( $location );
		}
	}
}
