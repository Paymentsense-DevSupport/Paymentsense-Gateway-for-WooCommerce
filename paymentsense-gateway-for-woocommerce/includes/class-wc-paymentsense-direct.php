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
				'enabled'                  => array(
					'title'   => __( 'Enable/Disable:', 'woocommerce-paymentsense' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable ', 'woocommerce-paymentsense' ) . $this->method_title,
					'default' => 'yes',
				),

				'module_options'           => array(
					'title'       => __( 'Module Options', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'The following options affect how the ', 'woocommerce-paymentsense' ) . $this->method_title . __( ' Module is displayed on the frontend.', 'woocommerce-paymentsense' ),
				),

				'title'                    => array(
					'title'       => __( 'Title:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the customer sees during checkout.', 'woocommerce-paymentsense' ),
					'default'     => $this->method_title,
					'desc_tip'    => true,
				),

				'description'              => array(
					'title'       => __( 'Description:', 'woocommerce-paymentsense' ),
					'type'        => 'textarea',
					'description' => __( 'This controls the description which the customer sees during checkout.', 'woocommerce-paymentsense' ),
					'default'     => __( 'Pay securely by Credit or Debit card through ', 'woocommerce-paymentsense' ) . $this->method_title . '.',
					'desc_tip'    => true,
				),

				'order_prefix'             => array(
					'title'       => __( 'Order Prefix:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the order prefix that you will see in the MMS.', 'woocommerce-paymentsense' ),
					'default'     => 'WC-',
					'desc_tip'    => true,
				),

				'gateway_settings'         => array(
					'title'       => __( 'Gateway Settings', 'woocommerce-paymentsense' ),
					'type'        => 'title',
					'description' => __( 'These are the gateway settings to allow you to connect with the Paymentsense gateway. (These are not the details used to login to the MMS)', 'woocommerce-paymentsense' ),
				),

				'gateway_merchant_id'      => array(
					'title'       => __( 'Gateway MerchantID:', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'This is the gateway MerchantID not used with the MMS login. The Format should match the following ABCDEF-1234567', 'woocommerce-paymentsense' ),
					'default'     => '',
					'desc_tip'    => true,
				),

				'gateway_password'         => array(
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

				'amex_accepted'            => array(
					'title'       => __( 'Accept American Express?', 'woocommerce-paymentsense' ),
					'type'        => 'checkbox',
					'description' => __( 'Tick only if you have an American Express MID associated with your Paymentsense gateway account.', 'woocommerce-paymentsense' ),
					'label'       => 'Enable American Express',
					'default'     => 'no',
					'desc_tip'    => true,
				),

				/*
				 * // TODO: Implementation of removal of this code
				 * Duplicate Delay Seetings. Started but postponed.
				'gateway_delay_initial'    => array(
					'title'       => __( 'Duplicate Delay (Initial):', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'The amount of time in seconds that the initial (card details) transactions with same OrderID and CardNumber should be rejected.', 'woocommerce-paymentsense' ),
					'default'     => 60,
					'desc_tip'    => true,
				),

				'gateway_delay_crossref'   => array(
					'title'       => __( 'Duplicate Delay (Refunds):', 'woocommerce-paymentsense' ),
					'type'        => 'text',
					'description' => __( 'The amount of time in seconds that the cross-reference (refund) transactions on same CrossReference should be rejected.', 'woocommerce-paymentsense' ),
					'default'     => 60,
					'desc_tip'    => true,
				),
				*/
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
					'direct-payment-form.php',
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
					'psense_ccname',
					'psense_ccnum',
					'psense_cv2',
					'psense_expmonth',
					'psense_expyear',
				);
				foreach ( $required_card_fields as $field ) {
					if ( empty( wc_get_post_data_by_key( $field ) ) ) {
						wc_add_notice( 'Required variable (' . $field . ') is missing', 'error' );
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

			try {
				$xml_data = array(
					'MerchantID'       => $this->gateway_merchant_id,
					'Password'         => $this->gateway_password,
					'Amount'           => $this->get_order_property( $order, 'order_total' ) * 100,
					'CurrencyCode'     => get_currency_iso_code( get_woocommerce_currency() ),
					'TransactionType'  => $this->gateway_transaction_type,
					'OrderID'          => $order_id,
					'OrderDescription' => $this->order_prefix . (string) $order_id,
					'CardName'         => $this->strip_invalid_chars( wc_get_post_data_by_key( 'psense_ccname' ) ),
					'CardNumber'       => wc_get_post_data_by_key( 'psense_ccnum' ),
					'ExpMonth'         => wc_get_post_data_by_key( 'psense_expmonth' ),
					'ExpYear'          => wc_get_post_data_by_key( 'psense_expyear' ),
					'CV2'              => wc_get_post_data_by_key( 'psense_cv2' ),
					'IssueNumber'      => wc_get_post_data_by_key( 'psense_issueno' ),
					'Address1'         => $this->get_order_property( $order, 'billing_address_1', true ),
					'Address2'         => $this->get_order_property( $order, 'billing_address_2', true ),
					'Address3'         => '',
					'Address4'         => '',
					'City'             => $this->get_order_property( $order, 'billing_city', true ),
					'State'            => $this->get_order_property( $order, 'billing_state', true ),
					'Postcode'         => $this->get_order_property( $order, 'billing_postcode', true ),
					'CountryCode'      => get_country_iso_code(
						$this->get_order_property( $order, 'billing_country', true )
					),
					'EmailAddress'     => $this->get_order_property( $order, 'billing_email', true ),
					'PhoneNumber'      => $this->get_order_property( $order, 'billing_phone', true ),
					// @codingStandardsIgnoreLine
					'IPAddress'        => $_SERVER['REMOTE_ADDR'],
				);

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

				$gateway_id         = 1;
				$transattempt       = 1;
				$soap_success       = false;
				$transaction_status = 'failed';
				$trx_message        = '';

				while ( ! $soap_success && $gateway_id <= 3 && $transattempt <= 3 ) {

					$data = array(
						'url'     => $this->get_gateway_url( $gateway_id ),
						'headers' => $headers,
						'xml'     => $xml,
					);

					if ( 0 === $this->send_transaction( $data, $response ) ) {
						$trx_status_code = $this->get_xml_value( 'StatusCode', $response, '[0-9]+' );
						$trx_message     = $this->get_xml_value( 'Message', $response, '.+' );

						if ( is_numeric( $trx_status_code ) ) {
							// request was processed correctly.
							if ( PS_TRX_RESULT_FAILED !== $trx_status_code ) {
								// set success flag so it will not run the request again.
								$soap_success = true;

								$crossref = $this->get_xml_cross_reference( $response );
								update_post_meta( (int) $order_id, 'CrossRef', $crossref );

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
												'crossref' => $crossref,
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

					if ( $transattempt <= 2 ) {
						$transattempt++;
					} else {
						$transattempt = 1;
						$gateway_id++;
					}
				}

				if ( 'success' === $transaction_status ) {
					$order->payment_complete();
					$order->add_order_note( 'Payment Successful: ' . $trx_message, 0 );
					$result = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				} elseif ( 'failed' === $transaction_status ) {
					$order->update_status( 'failed', __( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . strtolower( $trx_message ) );
					wc_add_notice(
						__( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . $trx_message . '<br />' .
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
					'key'       => $this->get_order_property( $order, 'order_key', true ),
					'order-pay' => $order_id,
				),
				WC()->api_request_url( get_class( $this ), is_ssl() )
			);

			$args = array(
				'pay_url'    => $paymentsense_sess['url'],
				'target'     => 'ACSFrame',
				'term_url'   => $term_url,
				'pareq'      => $paymentsense_sess['pareq'],
				'crossref'   => $paymentsense_sess['crossref'],
				'cancel_url' => $order->get_cancel_order_url(),
				'spinner'    => PS_IMG_SPINNER,
			);

			$this->show_output(
				'direct-acs-redirect.php',
				$args
			);
		}

		/**
		 * Processes the 3D secure response
		 */
		public function process_3dsecure_response() {
			$pares = wc_get_post_data_by_key( 'PaRes' );
			$md    = wc_get_post_data_by_key( 'MD' );

			if ( ! empty( $pares ) && ! empty( $md ) ) {
				$pay_url = add_query_arg(
					array(
						// @codingStandardsIgnoreStart
						'key'            => sanitize_text_field( $_GET['key'] ),
						'order-received' => sanitize_text_field( $_GET['order-pay'] ),
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
					'direct-return-redirect.php',
					$args
				);

				exit;
			}

			// @codingStandardsIgnoreLine
			$order_id = (int) sanitize_text_field( $_GET['order-received'] );
			$order    = new WC_Order( $order_id );

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

			$gateway_id   = 1;
			$transattempt = 1;
			$soap_success = false;

			while ( ! $soap_success && $gateway_id <= 3 && $transattempt <= 3 ) {

				$data = array(
					'url'     => $this->get_gateway_url( $gateway_id ),
					'headers' => $headers,
					'xml'     => $xml,
				);

				if ( 0 === $this->send_transaction( $data, $response ) ) {
					$trx_status_code = $this->get_xml_value( 'StatusCode', $response, '[0-9]+' );

					if ( is_numeric( $trx_status_code ) ) {
						// request was processed correctly.
						if ( PS_TRX_RESULT_FAILED !== $trx_status_code ) {
							// set success flag so it will not run the request again.
							$soap_success = true;

							$trx_message = $this->get_xml_value( 'Message', $response, '.+' );
							$auth_code   = $this->get_xml_value( 'AuthCode', $response, '.+' );
							$crossref    = $this->get_xml_cross_reference( $response );

							switch ( $trx_status_code ) {
								case PS_TRX_RESULT_SUCCESS:
									if ( ! empty( $auth_code ) ) {
										update_post_meta( (int) $order_id, 'AuthCode', $auth_code );
									}
									if ( ! empty( $crossref ) ) {
										update_post_meta( (int) $order_id, 'CrossRef', $crossref );
									}

									$order->add_order_note( __( 'Paymentsense Direct 3D payment completed', 'woocommerce-paymentsense' ) );
									$order->payment_complete();
									WC()->cart->empty_cart();
									$location = wc_get_endpoint_url(
										'order-received', $order_id, $order->get_checkout_order_received_url()
									);
									break;
								case PS_TRX_RESULT_DECLINED:
									$order->update_status( 'failed', __( 'Paymentsense Direct 3D Secure Password Check Failed. ', 'woocommerce-paymentsense' ) . $trx_message );
									wc_add_notice(
										__( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . $trx_message . '<br />' .
										__( 'Please check your card details and try again.', 'woocommerce-paymentsense' ),
										'error'
									);
									$location = wc_get_endpoint_url(
										'order-pay', $order_id, $order->get_checkout_payment_url( false )
									);
									break;
								default:
									$order->update_status( 'failed', __( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . $trx_message );
									wc_add_notice(
										__( 'Payment Failed due to: ', 'woocommerce-paymentsense' ) . $trx_message . '<br />' .
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

				if ( $transattempt <= 2 ) {
					$transattempt++;
				} else {
					$transattempt = 1;
					$gateway_id++;
				}
			}

			$order->update_status( 'failed', __( 'UnAn unexpected error has occurred. ', 'woocommerce-paymentsense' ) );
			wc_add_notice(
				__( 'An unexpected error has occurred. ', 'woocommerce-paymentsense' ),
				'error'
			);
			wp_safe_redirect( wc_get_endpoint_url( 'order-pay', $order_id, $order->get_checkout_payment_url( false ) ) );
		}
	}
}
