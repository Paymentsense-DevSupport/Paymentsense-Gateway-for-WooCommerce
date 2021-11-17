<?php
/**
 * Paymentsense Direct Payment Method
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

if ( ! class_exists( 'WC_Paymentsense_Direct' ) ) {
	/**
	 * WC_Paymentsense_Direct class.
	 *
	 * @extends Paymentsense_Base
	 */
	class WC_Paymentsense_Direct extends Paymentsense_Base {
		/**
		 * Payment method ID
		 *
		 * @var string
		 */
		public $id = 'paymentsense_direct';

		/**
		 * Payment method title
		 *
		 * @var string
		 */
		public $method_title = 'Paymentsense Direct';

		/**
		 * Payment method description
		 *
		 * @var string
		 */
		public $method_description = 'Accept payments from Credit/Debit cards through Paymentsense Direct';

		/**
		 * Specifies whether the payment method shows fields on the checkout
		 *
		 * @var bool
		 */
		public $has_fields = true;

		/**
		 * Paymentsense Direct Class Constructor
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
				array( $this, 'process_3dsecure_request' )
			);
			add_action(
				'woocommerce_api_wc_' . $this->id,
				array( $this, 'process_3dsecure_response' )
			);
		}

		/**
		 * Initialises settings form fields
		 *
		 * Overrides wc settings api class method
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable:', 'woocommerce-paymentsense' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable ', 'woocommerce-paymentsense' ) . $this->method_title,
					'default' => 'yes',
				),

				'module_options' => array(
					'title'       => __( 'Module Options', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'The following options affect how the ', 'woocommerce-paymentsense' ) . $this->method_title . __( ' Module is displayed on the frontend.', 'woocommerce-paymentsense' ),
				),

				'title' => array(
					'title'       => __( 'Title:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the customer sees during checkout.', 'woocommerce-paymentsense' ),
					'default'     => $this->method_title,
					'desc_tip'    => true,
				),

				'description' => array(
					'title'       => __( 'Description:', 'woocommerce-paymentsense' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description which the customer sees during checkout.', 'woocommerce-paymentsense' ),
					'default'     => __( 'Pay securely by Credit or Debit card through ', 'woocommerce-paymentsense' ) . $this->method_title . '.',
					'desc_tip'    => true,
				),

				'order_prefix' => array(
					'title'       => __( 'Order Prefix:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the order prefix that you will see in the MMS.', 'woocommerce-paymentsense' ),
					'default'     => 'WC-',
					'desc_tip'    => true,
				),

				'gateway_settings' => array(
					'title'       => __( 'Gateway Settings', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'These are the gateway settings to allow you to connect with the Paymentsense gateway. (These are not the details used to login to the MMS)', 'woocommerce-paymentsense' ),
				),

				'gateway_merchant_id' => array(
					'title'       => __( 'Gateway MerchantID:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the gateway MerchantID not used with the MMS login. The Format should match the following ABCDEF-1234567', 'woocommerce-paymentsense' ),
					'default'     => '',
					'desc_tip'    => true,
				),

				'gateway_password' => array(
					'title'       => __( 'Gateway Password:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the gateway Password not used with the MMS login. The Password should use lower case and uppercase letters, and numbers only.', 'woocommerce-paymentsense' ),
					'default'     => '',
					'desc_tip'    => true,
				),

				'gateway_transaction_type' => array(
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

				'amex_accepted' => array(
					'title'       => __( 'Accept American Express?', 'woocommerce-paymentsense' ),
					'type'        => 'checkbox',
					'description' => __( 'Tick only if you have an American Express MID associated with your Paymentsense gateway account.', 'woocommerce-paymentsense' ),
					'label'       => 'Enable American Express',
					'default'     => 'no',
					'desc_tip'    => true,
				),

				'troubleshooting_settings' => array(
					'title'       => __( 'Troubleshooting Settings', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'Settings related to troubleshooting and diagnostics of the plugin.', 'woocommerce-paymentsense' ),
				),

				'extended_plugin_info' => array(
					'title'       => __( 'Allow extended information requests:', 'woocommerce-paymentsense' ),
					'type'        => 'select',
					'description' => __( 'Specifies whether requests for extended plugin information are allowed. Used for troubleshooting and diagnostics. Recommended Setting "Yes".', 'woocommerce-paymentsense' ),
					'default'     => 'true',
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
		 * Checks whether the SSL is enabled and the gateway merchant ID and password are set
		 *
		 * @return  bool
		 */
		public function is_valid_for_use() {
			return (
				$this->is_connection_secure() &&
				! empty( $this->get_option( 'gateway_merchant_id' ) ) &&
				! empty( $this->get_option( 'gateway_password' ) )
			);
		}

		/**
		 * Outputs the payment form on the checkout page
		 *
		 * Overrides wc payment gateway class method
		 *
		 * @return void
		 */
		public function payment_fields() {
			if ( $this->is_valid_for_use() ) {
				$this->show_output(
					'paymentsense-direct-payment-form.php',
					array(
						'description' => $this->description,
					)
				);
			} elseif ( ! $this->is_connection_secure() ) {
				$this->output_message(
					__( 'This module requires an encrypted connection. ', 'woocommerce-paymentsense' ) .
					__( 'Please enable SSL/TLS.', 'woocommerce-paymentsense' )
				);
			} else {
				$this->output_message(
					__( 'This module is not configured. Please configure gateway settings.', 'woocommerce-paymentsense' )
				);
			}
		}

		/**
		 * Validates payment fields on the frontend.
		 *
		 * Overrides parent wc payment gateway class method
		 *
		 * @return bool
		 */
		public function validate_fields() {
			if ( $this->is_connection_secure() ) {
				$result               = true;
				$required_card_fields = array(
					'psense_ccname'   => __( 'Cardholder’s Name', 'woocommerce-paymentsense' ),
					'psense_ccnum'    => __( 'Card Number', 'woocommerce-paymentsense' ),
					'psense_cv2'      => __( 'CVV/CV2 Number', 'woocommerce-paymentsense' ),
					'psense_expmonth' => __( 'Expiration month', 'woocommerce-paymentsense' ),
					'psense_expyear'  => __( 'Expiration year', 'woocommerce-paymentsense' ),
				);
				foreach ( $required_card_fields as $key => $value ) {
					if ( empty( wc_get_post_data_by_key( $key ) ) ) {
						wc_add_notice( '"' . $value . '" form field is empty.', 'error' );
						$result = false;
					}
				}
				return $result;
			} else {
				wc_add_notice( __( 'This module requires an encrypted connection. ', 'woocommerce-paymentsense' ), 'error' );
				return false;
			}
		}

		/**
		 * Process Payment
		 *
		 * Overrides parent wc payment gateway class method
		 *
		 * Process the payment. Override this in your gateway. When implemented, this should.
		 * return the success and redirect in an array. e.g:
		 *
		 *        return array(
		 *            'result'   => 'success',
		 *            'redirect' => $this->get_return_url( $order )
		 *        );
		 *
		 * @param int $order_id WooCommerce OrderId.
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$result = array(
				'result'   => 'fail',
				'redirect' => '',
			);

			$order = new WC_Order( $order_id );

			$order->update_status(
				'pending',
				__( 'Pending payment', 'woocommerce-paymentsense' )
			);

			try {
				$xml_data = array(
					'MerchantID'       => $this->gateway_merchant_id,
					'Password'         => $this->gateway_password,
					'Amount'           => $this->get_order_property( $order, 'order_total' ) * 100,
					'CurrencyCode'     => get_currency_iso_code( get_woocommerce_currency() ),
					'TransactionType'  => $this->gateway_transaction_type,
					'OrderID'          => $order_id,
					'OrderDescription' => $this->order_prefix . (string) $order_id,
					'CardName'         => wc_get_post_data_by_key( 'psense_ccname' ),
					'CardNumber'       => wc_get_post_data_by_key( 'psense_ccnum' ),
					'ExpMonth'         => wc_get_post_data_by_key( 'psense_expmonth' ),
					'ExpYear'          => wc_get_post_data_by_key( 'psense_expyear' ),
					'CV2'              => wc_get_post_data_by_key( 'psense_cv2' ),
					'IssueNumber'      => '',
					'Address1'         => $this->get_order_property( $order, 'billing_address_1' ),
					'Address2'         => $this->get_order_property( $order, 'billing_address_2' ),
					'Address3'         => '',
					'Address4'         => '',
					'City'             => $this->get_order_property( $order, 'billing_city' ),
					'State'            => $this->get_order_property( $order, 'billing_state' ),
					'Postcode'         => $this->get_order_property( $order, 'billing_postcode' ),
					'CountryCode'      => get_country_iso_code(
						$this->get_order_property( $order, 'billing_country' )
					),
					'EmailAddress'     => $this->get_order_property( $order, 'billing_email' ),
					'PhoneNumber'      => $this->get_order_property( $order, 'billing_phone' ),
					// @codingStandardsIgnoreLine
					'IPAddress'        => $_SERVER['REMOTE_ADDR'],
				);

				$xml_data = array_map(
					function ( $value ) {
						return null === $value ? '' : $this->filter_unsupported_chars( $value, true );
					},
					$xml_data
				);

				$xml_data = $this->apply_length_restrictions( $xml_data );

				$headers = array(
					'SOAPAction:https://www.thepaymentgateway.net/CardDetailsTransaction',
					'Content-Type: text/xml; charset = utf-8',
					'Connection: close',
				);
				$xml     = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <CardDetailsTransaction xmlns="https://www.thepaymentgateway.net/">
                                    <PaymentMessage>
                                        <MerchantAuthentication MerchantID="' . $xml_data['MerchantID'] . '" Password="' . $xml_data['Password'] . '" />
                                        <TransactionDetails Amount="' . $xml_data['Amount'] . '" CurrencyCode="' . $xml_data['CurrencyCode'] . '">
                                            <MessageDetails TransactionType="' . $xml_data['TransactionType'] . '" />
                                            <OrderID>' . $xml_data['OrderID'] . '</OrderID>
                                            <OrderDescription>' . $xml_data['OrderDescription'] . '</OrderDescription>
                                            <TransactionControl>
                                                <EchoCardType>TRUE</EchoCardType>
                                                <EchoAVSCheckResult>TRUE</EchoAVSCheckResult>
                                                <EchoCV2CheckResult>TRUE</EchoCV2CheckResult>
                                                <EchoAmountReceived>TRUE</EchoAmountReceived>
                                                <DuplicateDelay>20</DuplicateDelay>
                                            </TransactionControl>
                                        </TransactionDetails>
                                        <CardDetails>
                                            <CardName>' . $xml_data['CardName'] . '</CardName>
                                            <CardNumber>' . $xml_data['CardNumber'] . '</CardNumber>
                                            <StartDate Month="" Year="" />
                                            <ExpiryDate Month="' . $xml_data['ExpMonth'] . '" Year="' . $xml_data['ExpYear'] . '" />
                                            <CV2>' . $xml_data['CV2'] . '</CV2>
                                            <IssueNumber>' . $xml_data['IssueNumber'] . '</IssueNumber>
                                        </CardDetails>
                                        <CustomerDetails>
                                            <BillingAddress>
                                                <Address1>' . $xml_data['Address1'] . '</Address1>
                                                <Address2>' . $xml_data['Address2'] . '</Address2>
                                                <Address3>' . $xml_data['Address3'] . '</Address3>
                                                <Address4>' . $xml_data['Address4'] . '</Address4>
                                                <City>' . $xml_data['City'] . '</City>
                                                <State>' . $xml_data['State'] . '</State>
                                                <PostCode>' . $xml_data['Postcode'] . '</PostCode>
                                                <CountryCode>' . $xml_data['CountryCode'] . '</CountryCode>
                                            </BillingAddress>
                                            <EmailAddress>' . $xml_data['EmailAddress'] . '</EmailAddress>
                                            <PhoneNumber>' . $xml_data['PhoneNumber'] . '</PhoneNumber>
                                            <CustomerIPAddress>' . $xml_data['IPAddress'] . '</CustomerIPAddress>
                                        </CustomerDetails>
                                    </PaymentMessage>
                                </CardDetailsTransaction>
                            </soap:Body>
                        </soap:Envelope>';

				$gateway_id         = 0;
				$trans_attempt      = 1;
				$max_attempts       = 3;
				$valid_response     = false;
				$transaction_status = 'failed';
				$trx_message        = '';

				$gateways       = $this->get_gateway_entry_points();
				$gateways_count = count( $gateways );

				while ( ! $valid_response && $gateway_id < $gateways_count && $trans_attempt <= $max_attempts ) {
					$data = array(
						'url'     => $gateways[ $gateway_id ],
						'headers' => $headers,
						'xml'     => $xml,
					);

					if ( 0 === $this->send_transaction( $data, $response ) ) {
						$trx_status_code = $this->get_xml_value( 'StatusCode', $response, '[0-9]+' );
						$trx_message     = $this->get_xml_value( 'Message', $response, '.+' );

						if ( is_numeric( $trx_status_code ) ) {
							if ( PS_TRX_RESULT_FAILED !== $trx_status_code ) {
								$valid_response = true;

								$cross_ref = $this->get_xml_cross_reference( $response );
								update_post_meta( (int) $order_id, 'CrossRef', $cross_ref );

								switch ( $trx_status_code ) {
									case PS_TRX_RESULT_SUCCESS:
										$transaction_status = 'success';
										break;
									case PS_TRX_RESULT_INCOMPLETE:
										// 3D Secure Auth required.
										$pareq = $this->get_xml_value( 'PaREQ', $response, '.+' );
										$url   = $this->get_xml_value( 'ACSURL', $response, '.+' );
										WC()->session->set(
											'paymentsense',
											array(
												'pareq'    => $pareq,
												'crossref' => $cross_ref,
												'url'      => $url,
											)
										);
										return array(
											'result'   => 'success',
											'redirect' => $order->get_checkout_payment_url( true ),
										);
									case PS_TRX_RESULT_DUPLICATE:
										$transaction_status = 'failed';
										if ( preg_match( '#<PreviousTransactionResult>(.+)</PreviousTransactionResult>#iU', $response, $matches ) ) {
											$prev_trx_result      = $matches[1];
											$trx_message          = $this->get_xml_value( 'Message', $prev_trx_result, '.+' );
											$prev_trx_status_code = $this->get_xml_value( 'StatusCode', $prev_trx_result, '.+' );
											if ( '0' === $prev_trx_status_code ) {
												$transaction_status = 'success';
											}
										}
										break;
									case PS_TRX_RESULT_REFERRED:
									case PS_TRX_RESULT_DECLINED:
									default:
										$transaction_status = 'failed';
										break;
								}
							}
							if ( 'failed' === $transaction_status ) {
								$trx_message .= '<br />' . $this->get_xml_value( 'Detail', $response, '.+' );
							}
						}
					}

					if ( $trans_attempt < $max_attempts ) {
						$trans_attempt++;
					} else {
						$trans_attempt = 1;
						$gateway_id++;
					}
				}

				if ( 'success' === $transaction_status ) {
					$order->payment_complete();
					$order->add_order_note( __( 'Payment processed successfully. ', 'woocommerce-paymentsense' ) . $trx_message );
					$result = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				} elseif ( 'failed' === $transaction_status ) {
					$order->update_status( 'failed', __( 'Payment failed due to: ', 'woocommerce-paymentsense' ) . strtolower( $trx_message ) );
					wc_add_notice(
						__( 'Payment failed due to: ', 'woocommerce-paymentsense' ) . $trx_message . '<br />' .
						__( 'Please check your card details and try again.', 'woocommerce-paymentsense' ),
						'error'
					);
				}
			} catch ( Exception $exception ) {
				$order->update_status(
					'failed',
					__( 'An unexpected error has occurred. ', 'woocommerce-paymentsense' ) .
					__( 'Error message: ', 'woocommerce-paymentsense' ) . $exception->getMessage()
				);
				wc_add_notice(
					__( 'An unexpected error has occurred. ', 'woocommerce-paymentsense' ) .
					__( 'Please contact Customer Support.', 'woocommerce-paymentsense' ),
					'error'
				);
			}

			return $result;
		}

		/**
		 * Processes the 3D secure request
		 *
		 * @param  int $order_id Order ID.
		 * @return void
		 */
		public function process_3dsecure_request( $order_id ) {
			$paymentsense_sess = WC()->session->get( 'paymentsense' );
			if ( empty( $paymentsense_sess ) ) {
				return;
			}

			$order = new WC_Order( $order_id );

			$term_url = add_query_arg(
				array(
					'key'       => $this->get_order_property( $order, 'order_key' ),
					'order-pay' => $order_id,
				),
				WC()->api_request_url( get_class( $this ), is_ssl() )
			);

			$args = array(
				'acs_url'    => $paymentsense_sess['url'],
				'target'     => 'ACSFrame',
				'term_url'   => $term_url,
				'pareq'      => $paymentsense_sess['pareq'],
				'crossref'   => $paymentsense_sess['crossref'],
				'cancel_url' => $order->get_cancel_order_url(),
				'spinner'    => PS_IMG_SPINNER,
			);

			$this->show_output(
				'paymentsense-direct-acs-redirect.php',
				$args
			);
		}

		/**
		 * Processes the 3D secure response
		 */
		public function process_3dsecure_response() {
			if ( $this->is_info_request() ) {
				$this->process_info_request();
			}

			if ( $this->is_checksums_request() ) {
				$this->process_checksums_request();
			}

			if ( $this->is_connection_info_request() ) {
				$this->process_connection_info_request();
			}

			$pares = wc_get_post_data_by_key( 'PaRes' );
			$md    = wc_get_post_data_by_key( 'MD' );

			if ( ! empty( $pares ) && ! empty( $md ) ) {
				$pay_url = add_query_arg(
					array(
						// @codingStandardsIgnoreStart
						'key'       => sanitize_text_field( $_GET['key'] ),
						'order-pay' => sanitize_text_field( $_GET['order-pay'] ),
						// @codingStandardsIgnoreEnd
					)
				);

				$args = array(
					'pay_url'    => $pay_url,
					'target'     => '_parent',
					'term_url'   => '',
					'pares'      => $pares,
					'crossref'   => $md,
					'cancel_url' => '',
					'spinner'    => PS_IMG_SPINNER,
				);

				$this->show_output(
					'paymentsense-direct-return-redirect.php',
					$args
				);

				exit;
			}

			// @codingStandardsIgnoreLine
			$order_id = (int) sanitize_text_field( $_GET['order-pay'] );
			$order    = new WC_Order( $order_id );

			if ( 'processing' === $order->get_status() ) {
				$order->add_order_note(
					__(
						'An unexpected callback notification has been received. This normally happens when the customer clicks on the "Back" button on their web browser or/and attempts to perform further payment transactions after a successful one is made.',
						'woocommerce-paymentsense'
					)
				);
				wc_add_notice(
					__( 'It seems you already have paid for this order. In case of doubts, please contact us.', 'woocommerce-paymentsense' ),
					'error'
				);
				$location = $order->get_checkout_payment_url();
				wp_safe_redirect( $location );
				return;
			}

			$xml_data = array(
				'MerchantID'     => $this->gateway_merchant_id,
				'Password'       => $this->gateway_password,
				'CrossReference' => wc_get_post_data_by_key( 'CrossReference' ),
				'PaRES'          => wc_get_post_data_by_key( 'PaRes' ),
			);

			$headers = array(
				'SOAPAction:https://www.thepaymentgateway.net/ThreeDSecureAuthentication',
				'Content-Type: text/xml; charset = utf-8',
				'Connection: close',
			);
			$xml     = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                            <ThreeDSecureAuthentication xmlns="https://www.thepaymentgateway.net/">
                                <ThreeDSecureMessage>
                                    <MerchantAuthentication MerchantID="' . $xml_data['MerchantID'] . '" Password="' . $xml_data['Password'] . '" />
                                    <ThreeDSecureInputData CrossReference="' . $xml_data['CrossReference'] . '">
                                        <PaRES>' . $xml_data['PaRES'] . '</PaRES>
                                    </ThreeDSecureInputData>
                                    <PassOutData>Some data to be passed out</PassOutData>
                                </ThreeDSecureMessage>
                            </ThreeDSecureAuthentication>
                        </soap:Body>
                    </soap:Envelope>';

			$gateway_id    = 0;
			$trans_attempt = 1;
			$max_attempts  = 3;

			$gateways       = $this->get_gateway_entry_points();
			$gateways_count = count( $gateways );

			while ( $gateway_id < $gateways_count && $trans_attempt <= $max_attempts ) {
				$data = array(
					'url'     => $gateways[ $gateway_id ],
					'headers' => $headers,
					'xml'     => $xml,
				);

				if ( 0 === $this->send_transaction( $data, $response ) ) {
					$trx_status_code = $this->get_xml_value( 'StatusCode', $response, '[0-9]+' );

					if ( is_numeric( $trx_status_code ) ) {
						if ( PS_TRX_RESULT_FAILED !== $trx_status_code ) {
							if ( ( PS_TRX_RESULT_DUPLICATE === $trx_status_code ) &&
								( preg_match( '#<PreviousTransactionResult>(.+)</PreviousTransactionResult>#iU', $response, $matches ) ) ) {
								$prev_trx_result = $matches[1];
								$trx_status_code = $this->get_xml_value( 'StatusCode', $prev_trx_result, '.+' );
								$trx_message     = $this->get_xml_value( 'Message', $prev_trx_result, '.+' );
							} else {
								$trx_message = $this->get_xml_value( 'Message', $response, '.+' );
							}

							switch ( $trx_status_code ) {
								case PS_TRX_RESULT_SUCCESS:
									$auth_code = $this->get_xml_value( 'AuthCode', $response, '.+' );
									update_post_meta( (int) $order_id, 'AuthCode', $auth_code );
									$cross_ref = $this->get_xml_cross_reference( $response );
									update_post_meta( (int) $order_id, 'CrossRef', $cross_ref );
									$order->payment_complete();
									$order->add_order_note(
										__( 'Payment (3DS) processed successfully. ', 'woocommerce-paymentsense' ) .
										$trx_message
									);
									WC()->cart->empty_cart();
									$location = $order->get_checkout_order_received_url();
									break;
								case PS_TRX_RESULT_DECLINED:
									$order->update_status(
										'failed',
										__( 'Payment (3DS) failed due to: ', 'woocommerce-paymentsense' ) .
										$trx_message
									);
									wc_add_notice(
										__( 'Payment failed due to: ', 'woocommerce-paymentsense' ) . $trx_message . '<br />' .
										__( 'Please check your card details and try again.', 'woocommerce-paymentsense' ),
										'error'
									);
									$location = wc_get_endpoint_url(
										'order-pay', $order_id, $order->get_checkout_payment_url( false )
									);
									break;
								default:
									$order->update_status(
										'failed',
										__( 'Payment (3DS) failed due to: ', 'woocommerce-paymentsense' ) .
										$trx_message
									);
									wc_add_notice(
										__( 'Payment failed due to: ', 'woocommerce-paymentsense' ) . $trx_message . '<br />' .
										__( 'Please check your card details and try again.', 'woocommerce-paymentsense' ),
										'error'
									);
									$location = wc_get_endpoint_url(
										'order-pay', $order_id, $order->get_checkout_payment_url( false )
									);
									break;
							}

							wp_safe_redirect( $location );
							return;
						}
					}
				}

				if ( $trans_attempt < $max_attempts ) {
					$trans_attempt++;
				} else {
					$trans_attempt = 1;
					$gateway_id++;
				}
			}

			$order->update_status(
				'failed',
				__( 'An unexpected error has occurred. ', 'woocommerce-paymentsense' )
			);
			wc_add_notice(
				__( 'An unexpected error has occurred. ', 'woocommerce-paymentsense' ),
				'error'
			);
			wp_safe_redirect( wc_get_endpoint_url( 'order-pay', $order_id, $order->get_checkout_payment_url( false ) ) );
		}

		/**
		 * Gets the message about the connection settings.
		 *
		 * @param bool $text_format Specifies whether the format of the message is text.
		 *
		 * @return array
		 */
		public function get_connection_settings_message( $text_format ) {
			$result = array();
			if ( ! $this->merchant_id_format_valid() ) {
				$result = $this->build_error_settings_message(
					__(
						'Gateway MerchantID is invalid. Please make sure the Gateway MerchantID matches the ABCDEF-1234567 format.',
						'woocommerce-paymentsense'
					)
				);
			} else {
				$merchant_credentials_valid = null;
				foreach ( $this->connection_info[ self::CONN_INFO_GGEP ] as $connection_info ) {
					if ( CURLE_OK === $connection_info['curl_errno'] ) {
						$trx_status_code = $this->get_xml_value( 'StatusCode', $connection_info['response'], '[0-9]+' );
						if ( PS_TRX_RESULT_SUCCESS === $trx_status_code ) {
							$merchant_credentials_valid = true;
							break;
						} elseif ( PS_TRX_RESULT_FAILED === $trx_status_code ) {
							$trx_message = $this->get_xml_value( 'Message', $connection_info['response'], '.+' );
							if ( $this->merchant_credentials_invalid( $trx_message ) ) {
								$merchant_credentials_valid = false;
								break;
							}
						}
					}
				}
				if ( true === $merchant_credentials_valid ) {
					$result = $this->build_success_settings_message(
						__(
							'Gateway MerchantID and Gateway Password are valid.',
							'woocommerce-paymentsense'
						)
					);
				} else {
					$gateway_settings_response = $this->check_gateway_settings();
					switch ( $gateway_settings_response ) {
						case self::HPF_RESP_MID_MISSING:
						case self::HPF_RESP_MID_NOT_EXISTS:
							$result = $this->build_error_settings_message(
								__(
									'Gateway MerchantID is invalid.',
									'woocommerce-paymentsense'
								)
							);
							break;
						case self::HPF_RESP_HASH_INVALID:
							if ( false === $merchant_credentials_valid ) {
								$result = $this->build_error_settings_message(
									__(
										'Gateway Password is invalid.',
										'woocommerce-paymentsense'
									)
								);
							} else {
								$result = $this->build_warning_settings_message(
									__(
										'The gateway settings cannot be validated at this time.',
										'woocommerce-paymentsense'
									)
								);
							}
							break;
						case self::HPF_RESP_NO_RESPONSE:
							if ( false === $merchant_credentials_valid ) {
								$result = $this->build_error_settings_message(
									__(
										'Gateway MerchantID or/and Gateway Password are invalid.',
										'woocommerce-paymentsense'
									)
								);
							} else {
								$result = $this->build_warning_settings_message(
									__(
										'The gateway settings cannot be validated at this time.',
										'woocommerce-paymentsense'
									)
								);
							}
							break;
					}
				}
			}

			if ( $text_format ) {
				$result = $this->getSettingsTextMessage( $result );
			}

			return $result;
		}
	}
}
