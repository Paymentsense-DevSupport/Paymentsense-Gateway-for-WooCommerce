<?php
/**
 * Paymentsense Gateway Library
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

if ( ! class_exists( 'Paymentsense_Base' ) ) {

	/**
	 * Paymentsense_Base class.
	 *
	 * @extends WC_Payment_Gateway
	 */
	class Paymentsense_Base extends WC_Payment_Gateway {

		/**
		 * Request Types
		 */
		const REQ_NOTIFICATION      = '0';
		const REQ_CUSTOMER_REDIRECT = '1';

		/**
		 * Default Payment Gateway Entry Points
		 *
		 * @var array
		 */
		protected static $default_gateway_entry_points = array(
			'https://gw1.paymentsensegateway.com:4430',
			'https://gw2.paymentsensegateway.com:4430',
		);

		/**
		 * Payment Gateway Entry Points
		 *
		 * @var array
		 */
		protected static $gateway_entry_points = array();

		/**
		 * Payment Gateway Merchant ID
		 *
		 * @var string
		 */
		protected $gateway_merchant_id;

		/**
		 * Payment Gateway Password
		 *
		 * @var string
		 */
		protected $gateway_password;

		/**
		 * Payment Gateway Pre-shared Key
		 *
		 * @var string
		 */
		protected $gateway_presharedkey;

		/**
		 * Payment Gateway Hash Method
		 *
		 * @var string
		 */
		protected $gateway_hashmethod;

		/**
		 * Payment Gateway Transaction Type
		 *
		 * @var string
		 */
		protected $gateway_transaction_type;

		/**
		 * American Express Cards Accepted
		 *
		 * @var string
		 */
		protected $amex_accepted;

		/**
		 * Order Prefix
		 *
		 * @var string
		 */
		protected $order_prefix;

		/**
		 * Incompatible Plugins
		 *
		 * @var array
		 */
		protected $incompatible_plugins = array(
			'woocommerce-sequential-order-numbers' => 'WooCommerce Sequential Order Numbers',
		);

		/**
		 * Plugin Data
		 *
		 * @var array
		 */
		protected $plugin_data = array();

		/**
		 * Supported content types of the output of the module information
		 *
		 * @var array
		 */
		protected $content_types = array(
			'json' => TYPE_APPLICATION_JSON,
			'text' => TYPE_TEXT_PLAIN,
		);

		/**
		 * Paymentsense Gateway Class Constructor
		 */
		public function __construct() {
			$this->init_form_fields();

			// Sets WC_Payment_Gateway class variables.
			$this->enabled     = $this->get_option( 'enabled' );
			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );

			// Sets Paymentsense class variables.
			$this->order_prefix             = $this->get_option( 'order_prefix' );
			$this->gateway_merchant_id      = $this->get_option( 'gateway_merchant_id' );
			$this->gateway_password         = $this->get_option( 'gateway_password' );
			$this->gateway_presharedkey     = $this->get_option( 'gateway_presharedkey' );
			$this->gateway_hashmethod       = $this->get_option( 'gateway_hashmethod' );
			$this->gateway_transaction_type = $this->get_option( 'gateway_transaction_type' );
			$this->amex_accepted            = 'yes' === $this->settings['amex_accepted'] ? 'TRUE' : 'FALSE';

			// Sets the icon (logo).
			$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', PS_IMG_LOGO );

			// Initialises gateway entry points.
			$this->init_gateway_entry_points();

			// Adds refunds support.
			if ( 'true' !== $this->settings['disable_comm_on_port_4430'] ) {
				array_push( $this->supports, 'refunds' );
			}
		}

		/**
		 * Gets payment form URL
		 *
		 * @return  string
		 */
		protected function get_payment_form_url() {
			return 'https://mms.paymentsensegateway.com/Pages/PublicPages/PaymentForm.aspx';
		}

		/**
		 * Initialises payment gateway entry points by performing the GetGatewayEntryPoints request if needed
		 */
		protected function init_gateway_entry_points() {
			if ( empty( self::$gateway_entry_points ) ) {
				$this->set_gateway_entry_points();
			}
		}

		/**
		 * Sets payment gateway entry points
		 */
		protected function set_gateway_entry_points() {
			$gateway_entry_points       = $this->retrieve_gateway_entry_points();
			self::$gateway_entry_points = is_array( $gateway_entry_points ) && ! empty( $gateway_entry_points )
				? $gateway_entry_points
				: self::$default_gateway_entry_points;
		}

		/**
		 * Gets payment gateway entry points
		 */
		protected function get_gateway_entry_points() {
			return is_array( self::$gateway_entry_points ) && ! empty( self::$gateway_entry_points )
				? self::$gateway_entry_points
				: self::$default_gateway_entry_points;
		}

		/**
		 * Outputs the payment method settings in the admin panel
		 *
		 * Overrides wc payment gateway class method
		 */
		public function admin_options() {
			$args = array(
				'this_'       => $this,
				'title'       => $this->get_method_title(),
				'description' => $this->get_method_description(),
			);

			$this->show_output(
				'settings.php',
				$args
			);

		}

		/**
		 * Determines whether the store is configured to use a secure connection
		 *
		 * @return bool
		 */
		public function is_connection_secure() {
			return is_ssl();
		}

		/**
		 * Builds a string containing the variables for calculating the hash digest
		 *
		 * @param string $request_type Type of the request (notification or customer redirect).
		 *
		 * @return bool
		 */
		public function build_variables_string( $request_type ) {
			$result = false;
			$fields = array(
				// Variables for hash digest calculation for notification requests (excluding configuration variables).
				self::REQ_NOTIFICATION      => array(
					'StatusCode',
					'Message',
					'PreviousStatusCode',
					'PreviousMessage',
					'CrossReference',
					'Amount',
					'CurrencyCode',
					'OrderID',
					'TransactionType',
					'TransactionDateTime',
					'OrderDescription',
					'CustomerName',
					'Address1',
					'Address2',
					'Address3',
					'Address4',
					'City',
					'State',
					'PostCode',
					'CountryCode',
					'EmailAddress',
					'PhoneNumber',
				),
				// Variables for hash digest calculation for customer redirects (excluding configuration variables).
				self::REQ_CUSTOMER_REDIRECT => array(
					'CrossReference',
					'OrderID',
				),
			);

			if ( array_key_exists( $request_type, $fields ) ) {
				$result = 'MerchantID=' . $this->gateway_merchant_id .
					'&Password=' . $this->gateway_password;
				foreach ( $fields[ $request_type ] as $field ) {
					$result .= '&' . $field . '=' . $this->get_http_var( $field );
				}
			}
			return $result;
		}

		/**
		 * Gets order property
		 *
		 * @param  WC_Order $order WooCommerce order object.
		 * @param  string   $field_name WooCommerce order field name.
		 * @param  bool     $strip_invalid_chars Flag indicating whether the invalid chars should be stripped.
		 *
		 * @return string
		 */
		protected function get_order_property( $order, $field_name, $strip_invalid_chars = false ) {
			switch ( $field_name ) {
				case 'completed_date':
					$result = $order->get_date_completed() ? gmdate( 'Y-m-d H:i:s', $order->get_date_completed()->getOffsetTimestamp() ) : '';
					break;
				case 'paid_date':
					$result = $order->get_date_paid() ? gmdate( 'Y-m-d H:i:s', $order->get_date_paid()->getOffsetTimestamp() ) : '';
					break;
				case 'modified_date':
					$result = $order->get_date_modified() ? gmdate( 'Y-m-d H:i:s', $order->get_date_modified()->getOffsetTimestamp() ) : '';
					break;
				case 'order_date':
					$result = $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '';
					break;
				case 'id':
					$result = $order->get_id();
					break;
				case 'post':
					$result = get_post( $order->get_id() );
					break;
				case 'status':
					$result = $order->get_status();
					break;
				case 'post_status':
					$result = get_post_status( $order->get_id() );
					break;
				case 'customer_message':
				case 'customer_note':
					$result = $order->get_customer_note();
					break;
				case 'user_id':
				case 'customer_user':
					$result = $order->get_customer_id();
					break;
				case 'tax_display_cart':
					$result = get_option( 'woocommerce_tax_display_cart' );
					break;
				case 'display_totals_ex_tax':
					$result = 'excl' === get_option( 'woocommerce_tax_display_cart' );
					break;
				case 'display_cart_ex_tax':
					$result = 'excl' === get_option( 'woocommerce_tax_display_cart' );
					break;
				case 'cart_discount':
					$result = $order->get_total_discount();
					break;
				case 'cart_discount_tax':
					$result = $order->get_discount_tax();
					break;
				case 'order_tax':
					$result = $order->get_cart_tax();
					break;
				case 'order_shipping_tax':
					$result = $order->get_shipping_tax();
					break;
				case 'order_shipping':
					$result = $order->get_shipping_total();
					break;
				case 'order_total':
					$result = $order->get_total();
					break;
				case 'order_type':
					$result = $order->get_type();
					break;
				case 'order_currency':
					$result = $order->get_currency();
					break;
				case 'order_version':
					$result = $order->get_version();
					break;
				case 'order_key':
					$result = $order->get_order_key();
					break;
				default:
					if ( is_callable( array( $order, "get_{$field_name}" ) ) ) {
						$result = $order->{"get_{$field_name}"}();
					} else {
						$result = get_post_meta( $order->get_id(), '_' . $field_name, true );
					}
					break;
			}

			if ( $strip_invalid_chars ) {
				$result = $this->strip_invalid_chars( $result );
			}

			return $result;
		}

		/**
		 * Gets the value of an XML element from an XML document
		 *
		 * @param string $xml_element XML element.
		 * @param string $xml XML document.
		 * @param string $pattern Regular expression pattern.
		 *
		 * @return string
		 */
		protected function get_xml_value( $xml_element, $xml, $pattern ) {
			$result = $xml_element . ' Not Found';
			if ( preg_match( '#<' . $xml_element . '>(' . $pattern . ')</' . $xml_element . '>#iU', $xml, $matches ) ) {
				$result = $matches[1];
			}
			return $result;
		}

		/**
		 * Gets the value of the CrossReference element from an XML document
		 *
		 * @param string $xml XML document.
		 *
		 * @return string
		 */
		protected function get_xml_cross_reference( $xml ) {
			$result = 'No Data Found';
			if ( preg_match( '#<TransactionOutputData CrossReference="(.+)">#iU', $xml, $matches ) ) {
				$result = $matches[1];
			}
			return $result;
		}

		/**
		 * Gets the value of the GatewayEntryPoint elements from an XML document
		 *
		 * @param string $xml XML document.
		 *
		 * @return array
		 */
		protected function get_xml_gateway_entry_points( $xml ) {
			$result = array();
			if ( preg_match_all( '#<GatewayEntryPoint EntryPointURL="(.+)" Metric="(.+)" />#iU', $xml, $matches ) ) {
				$result = $matches[1];
			}
			return $result;
		}

		/**
		 * Builds the fields for the hosted form as an associative array
		 *
		 * @param  WC_Order $order WooCommerce order object.
		 * @return array An associative array containing the Required Input Variables for the API of the payment form
		 */
		protected function build_form_fields( $order ) {

			$fields = array(
				'Amount'                    => $this->get_order_property( $order, 'order_total' ) * 100,
				'CurrencyCode'              => get_currency_iso_code( get_woocommerce_currency() ),
				'OrderID'                   => $order->get_order_number(),
				'TransactionType'           => $this->gateway_transaction_type,
				'TransactionDateTime'       => date( 'Y-m-d H:i:s P' ),
				'CallbackURL'               => WC()->api_request_url( get_class( $this ), is_ssl() ),
				'OrderDescription'          => $this->order_prefix . $order->get_order_number(),
				'CustomerName'              => $this->get_order_property( $order, 'billing_first_name', true ) . ' ' .
					$this->get_order_property( $order, 'billing_last_name', true ),
				'Address1'                  => $this->get_order_property( $order, 'billing_address_1', true ),
				'Address2'                  => $this->get_order_property( $order, 'billing_address_2', true ),
				'Address3'                  => '',
				'Address4'                  => '',
				'City'                      => $this->get_order_property( $order, 'billing_city', true ),
				'State'                     => $this->get_order_property( $order, 'billing_state', true ),
				'PostCode'                  => $this->get_order_property( $order, 'billing_postcode', true ),
				'CountryCode'               => get_country_iso_code(
					$this->get_order_property( $order, 'billing_country', true )
				),
				'EmailAddress'              => $this->get_order_property( $order, 'billing_email', true ),
				'PhoneNumber'               => $this->get_order_property( $order, 'billing_phone', true ),
				'EmailAddressEditable'      => $this->get_option( 'email_address_editable' ),
				'PhoneNumberEditable'       => $this->get_option( 'phone_number_editable' ),
				'CV2Mandatory'              => 'true',
				'Address1Mandatory'         => $this->get_option( 'address1_mandatory' ),
				'CityMandatory'             => $this->get_option( 'city_mandatory' ),
				'PostCodeMandatory'         => $this->get_option( 'postcode_mandatory' ),
				'StateMandatory'            => $this->get_option( 'state_mandatory' ),
				'CountryMandatory'          => $this->get_option( 'country_mandatory' ),
				'ResultDeliveryMethod'      => $this->get_option( 'gateway_result_delivery' ),
				'ServerResultURL'           => ( 'SERVER' === $this->get_option( 'gateway_result_delivery' ) ) ? WC()->api_request_url( get_class( $this ), is_ssl() ) : '',
				'PaymentFormDisplaysResult' => 'false',
			);

			$data  = 'MerchantID=' . $this->gateway_merchant_id;
			$data .= '&Password=' . $this->gateway_password;

			foreach ( $fields as $key => $value ) {
				$data .= '&' . $key . '=' . $value;
			};

			$additional_fields = array(
				'HashDigest' => $this->calculate_hash_digest( $data, $this->gateway_hashmethod, $this->gateway_presharedkey ),
				'MerchantID' => $this->gateway_merchant_id,
			);

			$fields = array_merge( $additional_fields, $fields );
			$fields = array_map(
				function( $value ) {
					return str_replace( '"', '\"', $value );
				},
				$fields
			);

			return $fields;
		}

		/**
		 * Performs cURL requests to the Paymentsense gateway
		 *
		 * @param array  $data cURL data.
		 * @param mixed  $response the result or false on failure.
		 * @param mixed  $info last transfer information.
		 * @param string $err_msg last transfer error message.
		 *
		 * @return int the error number or 0 if no error occurred
		 */
		protected function send_transaction( $data, &$response, &$info = array(), &$err_msg = '' ) {
			if ( 'true' === $this->settings['disable_comm_on_port_4430'] ) {
				$err_no  = CURLE_ABORTED_BY_CALLBACK;
				$err_msg = 'Communication Aborted. The communication on port 4430 is disabled at the plugin configuration settings.';
			} else {
				// @codingStandardsIgnoreStart
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $data['headers']);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_URL, $data['url']);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data['xml']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($ch, CURLOPT_TIMEOUT, 12);
				$response = curl_exec($ch);
				$err_no = curl_errno($ch);
				$err_msg = curl_error($ch);
				$info = curl_getinfo($ch);
				curl_close($ch);
				// @codingStandardsIgnoreEnd
			}
			return $err_no;
		}

		/**
		 * Retrieves the gateway entry points
		 *
		 *  @param array $diagnostic_info Last transfer diagnostic information.
		 *
		 * @return array|WP_Error
		 */
		public function retrieve_gateway_entry_points( &$diagnostic_info = array() ) {
			$xml_data = array(
				'MerchantID' => $this->gateway_merchant_id,
				'Password'   => $this->gateway_password,
			);
			$headers  = array(
				'SOAPAction:https://www.thepaymentgateway.net/GetGatewayEntryPoints',
				'Content-Type: text/xml; charset = utf-8',
				'Connection: close',
			);
			$xml      = '<?xml version="1.0" encoding="utf-8"?>
                             <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                 <soap:Body>
                                     <GetGatewayEntryPoints xmlns="https://www.thepaymentgateway.net/">
                                         <GetGatewayEntryPointsMessage>
                                             <MerchantAuthentication MerchantID="' . $xml_data['MerchantID'] . '" Password="' . $xml_data['Password'] . '" />
                                         </GetGatewayEntryPointsMessage>
                                     </GetGatewayEntryPoints>
                                 </soap:Body>
                             </soap:Envelope>';

			$gateway_id      = 0;
			$trans_attempt   = 1;
			$attempt_no      = 0;
			$max_attempts    = 2;
			$soap_success    = false;
			$result          = array();
			$diagnostic_info = array();

			$gateways       = $this->get_gateway_entry_points();
			$gateways_count = count( $gateways );

			while ( ! $soap_success && $gateway_id < $gateways_count && $trans_attempt <= $max_attempts ) {
				$data = array(
					'url'     => $gateways[ $gateway_id ],
					'headers' => $headers,
					'xml'     => $xml,
				);

				$curl_errno = $this->send_transaction( $data, $response, $info, $curl_errmsg );
				if ( 0 === $curl_errno ) {
					$trx_status_code = $this->get_xml_value( 'StatusCode', $response, '[0-9]+' );

					if ( is_numeric( $trx_status_code ) ) {
						// request was processed correctly.
						if ( PS_TRX_RESULT_FAILED !== $trx_status_code ) {
							// set success flag so it will not run the request again.
							$soap_success = true;
							$result       = $this->get_xml_gateway_entry_points( $response );

						}
					}
				}

				$diagnostic_info[ 'Connection attempt ' . ( ++$attempt_no ) ] = array(
					'curl_errno'  => $curl_errno,
					'curl_errmsg' => $curl_errmsg,
					'response'    => $response,
					'info'        => $info,
				);

				if ( $trans_attempt < $max_attempts ) {
					$trans_attempt++;
				} else {
					$trans_attempt = 1;
					$gateway_id++;
				}
			}
			return $result;
		}

		/**
		 * Processes refunds.
		 *
		 * @param  int    $order_id Order ID.
		 * @param  float  $amount Refund amount.
		 * @param  string $reason Refund Reason.
		 * @return bool|WP_Error
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {

			if ( '' === $reason ) {
				$reason = 'Refund';
			}

			$xml_data = array(
				'MerchantID'       => $this->gateway_merchant_id,
				'Password'         => $this->gateway_password,
				'Amount'           => $amount * 100,
				'CurrencyCode'     => get_currency_iso_code( get_woocommerce_currency() ),
				'TransactionType'  => 'REFUND',
				'CrossReference'   => get_post_meta( $order_id, 'CrossRef', true ),
				'OrderID'          => $order_id,
				'OrderDescription' => $this->order_prefix . (string) $order_id . ': ' . $reason,
			);
			$headers  = array(
				'SOAPAction:https://www.thepaymentgateway.net/CrossReferenceTransaction',
				'Content-Type: text/xml; charset = utf-8',
				'Connection: close',
			);
			$xml      = '<?xml version="1.0" encoding="utf-8"?>
                             <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                                 <soap:Body>
                                     <CrossReferenceTransaction xmlns="https://www.thepaymentgateway.net/">
                                         <PaymentMessage>
                                             <MerchantAuthentication MerchantID="' . $xml_data['MerchantID'] . '" Password="' . $xml_data['Password'] . '" />
                                             <TransactionDetails Amount="' . $xml_data['Amount'] . '" CurrencyCode="' . $xml_data['CurrencyCode'] . '">
                                                 <MessageDetails TransactionType="' . $xml_data['TransactionType'] . '" NewTransaction="FALSE" CrossReference="' . $xml_data['CrossReference'] . '" />
                                                 <OrderID>' . $xml_data['OrderID'] . '</OrderID>
                                                 <OrderDescription>' . $xml_data['OrderDescription'] . '</OrderDescription>
                                                 <TransactionControl>
                                                     <EchoCardType>FALSE</EchoCardType>
                                                     <EchoAVSCheckResult>FALSE</EchoAVSCheckResult>
                                                     <EchoCV2CheckResult>FALSE</EchoCV2CheckResult>
                                                     <EchoAmountReceived>FALSE</EchoAmountReceived>
                                                     <DuplicateDelay>10</DuplicateDelay>
                                                     <AVSOverridePolicy>BPPF</AVSOverridePolicy>
                                                     <ThreeDSecureOverridePolicy>FALSE</ThreeDSecureOverridePolicy>
                                                 </TransactionControl>
                                             </TransactionDetails>
                                         </PaymentMessage>
                                     </CrossReferenceTransaction>
                                 </soap:Body>
                             </soap:Envelope>';

			$gateway_id         = 0;
			$trans_attempt      = 1;
			$max_attempts       = 3;
			$soap_success       = false;
			$transaction_status = 'failed';
			$trx_message        = '';

			$gateways       = $this->get_gateway_entry_points();
			$gateways_count = count( $gateways );

			while ( ! $soap_success && $gateway_id < $gateways_count && $trans_attempt <= $max_attempts ) {
				$data = array(
					'url'     => $gateways[ $gateway_id ],
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
							$soap_success       = true;
							$transaction_status = ( PS_TRX_RESULT_SUCCESS === $trx_status_code ) ? 'success' : 'failed';
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

			if ( 'success' !== $transaction_status ) {
				return new WP_Error( 'refund_error', __( 'Refund was declined. ', 'woocommerce-paymentsense' ) . $trx_message );
			}

			return true;
		}

		/**
		 * Strips invalid chars from a string
		 *
		 * @param string $subject The string.
		 * @return string
		 */
		protected function strip_invalid_chars( $subject ) {
			$search  = array( '<', '&' );
			$replace = array( '', '&amp;' );
			return str_replace( $search, $replace, $subject );
		}

		/**
		 * Generates output using template
		 *
		 * @param string $template_name Template filename.
		 * @param array  $args Template arguments.
		 */
		protected function show_output( $template_name, $args = array() ) {
			$templates_path = dirname( plugin_dir_path( __FILE__ ) ) . '/templates/';
			wc_get_template( $template_name, $args, '', $templates_path );
		}

		/**
		 * Outputs message when the module is unavailable for use
		 *
		 * @param string $message The message.
		 */
		protected function output_message( $message ) {
			$this->show_output(
				'message.php',
				array(
					'message' => $message,
				)
			);
		}

		/**
		 * Calculates the hash digest.
		 * Supported hash methods: MD5, SHA1, HMACMD5, HMACSHA1
		 *
		 * @param string $data Data to be hashed.
		 * @param string $hash_method Hash method.
		 * @param string $key Secret key to use for generating the hash.
		 *
		 * @return string
		 */
		protected function calculate_hash_digest( $data, $hash_method, $key ) {
			$result = '';

			$include_key = in_array( $hash_method, array( 'MD5', 'SHA1' ), true );
			if ( $include_key ) {
				$data = 'PreSharedKey=' . $key . '&' . $data;
			}

			switch ( $hash_method ) {
				case 'MD5':
					$result = md5( $data );
					break;
				case 'SHA1':
					$result = sha1( $data );
					break;
				case 'HMACMD5':
					$result = hash_hmac( 'md5', $data, $key );
					break;
				case 'HMACSHA1':
					$result = hash_hmac( 'sha1', $data, $key );
					break;
			}

			return $result;
		}

		/**
		 * Checks whether the hash digest received from the gateway is valid
		 *
		 * @param string $request_type Type of the request (notification or customer redirect).
		 *
		 * @return bool
		 */
		protected function is_hash_digest_valid( $request_type ) {
			$result = false;
			$data   = $this->build_variables_string( $request_type );
			if ( $data ) {
				$hash_digest_received   = $this->get_http_var( 'HashDigest' );
				$hash_digest_calculated = $this->calculate_hash_digest( $data, $this->gateway_hashmethod, $this->gateway_presharedkey );
				$result                 = strToUpper( $hash_digest_received ) === strToUpper( $hash_digest_calculated );
			}
			return $result;
		}

		/**
		 * Gets the value of an HTTP variable based on the requested method or the default value if the variable does not exist
		 *
		 * @param string $field HTTP POST/GET variable.
		 * @param string $default Default value.
		 * @return string
		 */
		protected function get_http_var( $field, $default = '' ) {
			// @codingStandardsIgnoreStart
			switch ( $_SERVER['REQUEST_METHOD'] ) {
				case 'GET':
					return array_key_exists( $field, $_GET )
						? $_GET[ $field ]
						: $default;
				case 'POST':
					return array_key_exists( $field, $_POST )
						? $_POST[ $field ]
						: $default;
				default:
					return $default;
			}
			// @codingStandardsIgnoreEnd
		}

		/**
		 * Gets a warning message, if applicable
		 *
		 * @return  string
		 */
		public static function get_warning_message() {
			return ( version_compare( WC()->version, '3.5.0', '>=' ) &&
					version_compare( WC()->version, '3.5.1', '<=' ) )
				? __( 'Warning: WooCommerce 3.5.0 and 3.5.1 contain a bug that affects the Hosted payment method of the Paymentsense plugin. Please consider updating WooCommerce.', 'woocommerce-paymentsense' )
				: '';
		}

		/**
		 * Checks whether the request is for plugin information
		 *
		 * @return  bool
		 */
		protected function is_info_request() {
			return 'info' === $this->get_http_var( 'action', '' );
		}

		/**
		 * Processes the request for plugin information
		 */
		protected function process_info_request() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				if ( is_file( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
			}

			if ( function_exists( 'get_plugin_data' ) ) {
				$ps_plugin_file    = '/paymentsense-gateway-for-woocommerce/paymentsense-gateway-for-woocommerce.php';
				$this->plugin_data = get_plugin_data( WP_PLUGIN_DIR . $ps_plugin_file );
			}

			$info = array(
				'Module Name'              => $this->get_module_name(),
				'Module Installed Version' => $this->get_module_installed_version(),
			);

			if ( ( 'true' === $this->get_http_var( 'extended_info', '' ) ) &&
				( 'true' === $this->get_option( 'extended_plugin_info' ) ) ) {
				$extended_info = array(
					'Module Latest Version' => $this->get_module_latest_version(),
					'WordPress Version'     => $this->get_wp_version(),
					'WooCommerce Version'   => $this->get_wc_version(),
					'PHP Version'           => $this->get_php_version(),
					'Connectivity'          => $this->check_gateway_connection(),
				);
				$info          = array_merge( $info, $extended_info );
			}

			$this->output_info( $info );
		}

		/**
		 * Outputs plugin information
		 *
		 * @param array $info Module information.
		 */
		private function output_info( $info ) {
			$output       = $this->get_http_var( 'output', 'text' );
			$content_type = array_key_exists( $output, $this->content_types )
				? $this->content_types[ $output ]
				: TYPE_TEXT_PLAIN;

			switch ( $content_type ) {
				case TYPE_APPLICATION_JSON:
					// @codingStandardsIgnoreLine
					$body = json_encode( $info );
					break;
				case TYPE_TEXT_PLAIN:
				default:
					$body = $this->convert_array_to_string( $info );
					break;
			}

			// @codingStandardsIgnoreStart
			@header( 'Cache-Control: max-age=0, must-revalidate, no-cache, no-store', true );
			@header( 'Pragma: no-cache', true );
			@header( 'Content-Type: '. $content_type, true );
			echo $body;
			// @codingStandardsIgnoreEnd
			exit;
		}

		/**
		 * Converts an array to string
		 *
		 * @param array  $arr An associative array.
		 * @param string $ident Identation.
		 * @return string
		 */
		private function convert_array_to_string( $arr, $ident = '' ) {
			$result        = '';
			$ident_pattern = '  ';
			foreach ( $arr as $key => $value ) {
				if ( '' !== $result ) {
					$result .= PHP_EOL;
				}
				if ( is_array( $value ) ) {
					$value = PHP_EOL . $this->convert_array_to_string( $value, $ident . $ident_pattern );
				}
				$result .= $ident . $key . ': ' . $value;
			}
			return $result;
		}

		/**
		 * Gets module name
		 *
		 * @return string
		 */
		private function get_module_name() {
			return array_key_exists( 'Name', $this->plugin_data )
				? $this->plugin_data['Name']
				: '';
		}

		/**
		 * Gets module installed version
		 *
		 * @return string
		 */
		private function get_module_installed_version() {
			return array_key_exists( 'Version', $this->plugin_data )
				? $this->plugin_data['Version']
				: '';
		}

		/**
		 * Gets module latest version
		 *
		 * @return string
		 */
		private function get_module_latest_version() {
			$result = 'N/A';

			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			if ( function_exists( 'plugins_api' ) ) {
				$args = array(
					'slug'   => 'paymentsense-gateway-for-woocommerce',
					'fields' => array(
						'version' => true,
					),
				);

				$latest_plugin_info = plugins_api( 'plugin_information', $args );

				if ( ! is_wp_error( $latest_plugin_info ) &&
					property_exists( $latest_plugin_info, 'version' ) ) {
					$result = $latest_plugin_info->version;
				}
			}

			return $result;
		}

		/**
		 * Gets WordPress version
		 *
		 * @return string
		 */
		private function get_wp_version() {
			return get_bloginfo( 'version' );
		}

		/**
		 * Gets WooCommerce version
		 *
		 * @return string
		 */
		private function get_wc_version() {
			return WC()->version;
		}

		/**
		 * Gets PHP version
		 *
		 * @return string
		 */
		private function get_php_version() {
			return phpversion();
		}

		/**
		 * Checks the connection with the Paymentsense entry points
		 *
		 * @return array
		 */
		private function check_gateway_connection() {
			$result = $this->retrieve_gateway_entry_points( $diagnostic_info );
			return array(
				'Gateway entry points' => $result,
				'Connection info'      => $diagnostic_info,
			);
		}
	}
}
