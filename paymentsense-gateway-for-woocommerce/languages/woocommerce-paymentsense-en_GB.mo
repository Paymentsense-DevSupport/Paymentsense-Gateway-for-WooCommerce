��    s      �      L      L  %   M     s     �     �     �  $   �  B   �  t   3  �   �  "   �	  H   �	     �	     
  	   +
     5
  9   E
  *   
     �
     �
  �   �
  9   �  <   �  �   �  A   �     �  -   �     %     -     =     M     ]     n  �   ~       2   %     X  i   w  7   �  ]        w     �  N   �     �  N   	  <   X     �     �  �   �  �  B     9  T   F     �     �     �  �   �     ;     N  -   \     �  &   �     �     �  M        Y      q     �  ,   �  -   �      �       �   5  E   �     �               )     ?     W  B   \  �   �     ,  v   D  l   �  %   (  6   N  �   �  b     �   s  F     @   `  S   �  p   �  �   f  m   �  6   Z  A   �  .   �  �     �   �  `   �     �     �        #      �   C   f   �   �   .!  �   �!  �   d"  �   �"  �   �#  D   &$     k$     p$      t$  %   u$     �$     �$     �$     �$  $   �$  B   %  t   [%  �   �%  "   �&  H   �&     $'     3'  	   S'     ]'  9   m'  *   �'     �'     �'  �   �'  9   �(  <   �(  �   ")  A   �)     *  -   *     M*     U*     e*     u*     �*     �*  �   �*     8+  2   M+     �+  i   �+  7   	,  ]   A,     �,     �,  N   �,     -  N   1-  <   �-     �-     �-  �   �-  �  j.     a0  T   n0     �0     �0     �0  �   �0     c1     v1  -   �1     �1  &   �1     �1     2  M   32     �2      �2     �2  ,   �2  -   �2      %3     F3  �   ]3  E   �3     $4     94     B4     Q4     g4     4  B   �4  �   �4     T5  v   l5  l   �5  %   P6  6   v6  �   �6  b   87  �   �7  F   A8  @   �8  S   �8  p   9  �   �9  m   :  6   �:  A   �:  .   �:  �   *;  �   �;  `   �<     =     =     .=  #   G=  �   k=  f   �=  �   V>  �   �>  �   �?  �   @  �   �@  D   NA     �A     �A    Module is displayed on the frontend. &laquo; Go Back (Maestro/Solo only). Accept American Express? Address Line 1 Mandatory: Allow extended information requests: An error occurred while processing order#%1$s. Error message: %2$s An error occurred while processing your payment. Payment status is unknown. Please contact support. Payment Status:  An unexpected callback notification has been received. This normally happens when the customer clicks on the "Back" button on their web browser or/and attempts to perform further payment transactions after a successful one is made. An unexpected error has occurred.  As a result of that Paymentsense Gateway for WooCommerce is deactivated. CVV/CV2 Number Cancel order &amp; restore cart Card Name City Mandatory: Click here if you are not redirected within 10 seconds... Connection to Paymentsense was successful. Country Mandatory: Credit Card Number Define the Address Line 1 as a Mandatory field on the Payment form. This is used for the Address Verification System (AVS) check on the customers card. Recommended Setting "Yes". Define the City as a Mandatory field on the Payment form. Define the Country as a Mandatory field on the Payment form. Define the Post Code as a Mandatory field on the Payment form. This is used for the Address Verification System (AVS) check on the customers card. Recommended Setting "Yes". Define the State/County as a Mandatory field on the Payment form. Description: Email Address can be altered on payment form: Enable  Enable/Disable: Error message:  Expiration date Expiration month Expiration year FYI: The Paymentsense Hosted method is configured to run in safe mode. You can still take payments, but refunds will need to be done via the MMS. Gateway Hash Method: Gateway MerchantID and Gateway Password are valid. Gateway MerchantID is invalid. Gateway MerchantID is invalid. Please make sure the Gateway MerchantID matches the ABCDEF-1234567 format. Gateway MerchantID or/and Gateway Password are invalid. Gateway MerchantID, Gateway Password, Gateway PreSharedKey and Gateway Hash Method are valid. Gateway MerchantID: Gateway Password is invalid. Gateway Password, Gateway PreSharedKey or/and Gateway Hash Method are invalid. Gateway Password: Gateway PreSharedKey and Gateway Hash Method cannot be validated at this time. Gateway PreSharedKey or/and Gateway Hash Method are invalid. Gateway PreSharedKey: Gateway Settings If you wish to obtain authorisation for the payment only, as you intend to manually collect the payment via the MMS, choose Pre-auth. In order to function normally the Paymentsense plugin performs outgoing connections to the Paymentsense gateway on port 4430 which is required to be open. In the case port 4430 on your server is closed you can still use the Paymentsense Hosted method with a limited functionality. Please note that by disabling the communication on port 4430 the online refund functionality will be disabled too. Recommended Setting "No". Please set to "Yes" only as a last resort when your server has port 4430 closed. Issue Number It seems you already have paid for this order. In case of doubts, please contact us. Module Options Month No Once the transaction status and authentication code are confirmed set the order status to "Processing" and process the order normally.  Order ID is empty. Order Prefix: Pay securely by Credit or Debit card through  Payment (3DS) failed due to:  Payment (3DS) processed successfully.  Payment Form Additional Field Payment Form Mandatory Fields Payment failed due to unknown or unsupported payment status. Payment Status:  Payment failed due to:  Payment processed successfully.  Pending payment Phone Number can be altered on payment form: Please check your card details and try again. Please contact Customer Support. Please enable SSL/TLS. Please log into your account at the MMS and check that transaction %1$s is processed with status SUCCESS and the message: %2$s.  Port 4430 is NOT open on my server (safe mode with refunds disabled): Post Code Mandatory: Pre-Auth Redirecting... Refund was declined.  Result Delivery Method: Sale Settings related to troubleshooting and diagnostics of the plugin. Specifies whether requests for extended plugin information are allowed. Used for troubleshooting and diagnostics. Recommended Setting "Yes". State/County Mandatory: Thank you - your order is now pending payment. You should be automatically redirected to Paymentsense to make payment. The Server Result Method determines how the transaction results are delivered back to the WooCommerce store. The following options affect how the  The gateway settings cannot be validated at this time. These are the gateway settings to allow you to connect with the Paymentsense gateway. (These are not the details used to login to the MMS) These options allow the customer to change the email address and phone number on the payment form. These options allow you to change what fields are mandatory for the customers to complete on the payment form. (The default settings are recommended by Paymentsense) This controls the description which the customer sees during checkout. This controls the title which the customer sees during checkout. This is located within the MMS under "Account Admin Settings" > "Account Settings". This is the gateway MerchantID not used with the MMS login. The Format should match the following ABCDEF-1234567 This is the gateway Password not used with the MMS login. The Password should use lower case and uppercase letters, and numbers only. This is the hash method set in MMS under "Account Admin" > "Account Settings". By default, this will be SHA1. This is the order prefix that you will see in the MMS. This module is not configured. Please configure gateway settings. This module requires an encrypted connection.  This option allows the customer to change the email address that entered during checkout. By default the Paymentsense module will pass the customers email address that they entered during checkout. This option allows the customer to change the phone number that entered during checkout. By default the Paymentsense module will pass the customers phone number that they entered during checkout. Tick only if you have an American Express MID associated with your Paymentsense gateway account. Title: Transaction Type: Troubleshooting Settings Unsupported Result Delivery Method. WARNING: The authenticity of the status of this transaction cannot be confirmed automatically! Please check the status at the MMS.  Warning: Paymentsense Gateway for WooCommerce plugin is incompatible with the following plugin(s): %s. Warning: Port 4430 seems to be closed on your server. Paymentsense Direct can NOT be used in this case. Please use Paymentsense Hosted or open port 4430. Warning: Port 4430 seems to be closed on your server. Please open port 4430 or set the "Port 4430 is NOT open on my server" configuration setting to "Yes". Warning: The Paymentsense plugin cannot connect to the Paymentsense gateway. Please contact support providing the information below: <pre>%s</pre> Warning: The Paymentsense plugin cannot resolve any of the Paymentsense gateway entry points. Please check your DNS resolution or contact support. Warning: WooCommerce 3.5.0 and 3.5.1 contain a bug that affects the Hosted payment method of the Paymentsense plugin. Please consider updating WooCommerce. We are now redirecting you to Paymentsense to complete your payment. Year Yes   Module is displayed on the frontend. &laquo; Go Back (Maestro/Solo only). Accept American Express? Address Line 1 Mandatory: Allow extended information requests: An error occurred while processing order#%1$s. Error message: %2$s An error occurred while processing your payment. Payment status is unknown. Please contact support. Payment Status:  An unexpected callback notification has been received. This normally happens when the customer clicks on the "Back" button on their web browser or/and attempts to perform further payment transactions after a successful one is made. An unexpected error has occurred.  As a result of that Paymentsense Gateway for WooCommerce is deactivated. CVV/CV2 Number Cancel order &amp; restore cart Card Name City Mandatory: Click here if you are not redirected within 10 seconds... Connection to Paymentsense was successful. Country Mandatory: Credit Card Number Define the Address Line 1 as a Mandatory field on the Payment form. This is used for the Address Verification System (AVS) check on the customers card. Recommended Setting "Yes". Define the City as a Mandatory field on the Payment form. Define the Country as a Mandatory field on the Payment form. Define the Post Code as a Mandatory field on the Payment form. This is used for the Address Verification System (AVS) check on the customers card. Recommended Setting "Yes". Define the State/County as a Mandatory field on the Payment form. Description: Email Address can be altered on payment form: Enable  Enable/Disable: Error message:  Expiration date Expiration month Expiration year FYI: The Paymentsense Hosted method is configured to run in safe mode. You can still take payments, but refunds will need to be done via the MMS. Gateway Hash Method: Gateway MerchantID and Gateway Password are valid. Gateway MerchantID is invalid. Gateway MerchantID is invalid. Please make sure the Gateway MerchantID matches the ABCDEF-1234567 format. Gateway MerchantID or/and Gateway Password are invalid. Gateway MerchantID, Gateway Password, Gateway PreSharedKey and Gateway Hash Method are valid. Gateway MerchantID: Gateway Password is invalid. Gateway Password, Gateway PreSharedKey or/and Gateway Hash Method are invalid. Gateway Password: Gateway PreSharedKey and Gateway Hash Method cannot be validated at this time. Gateway PreSharedKey or/and Gateway Hash Method are invalid. Gateway PreSharedKey: Gateway Settings If you wish to obtain authorisation for the payment only, as you intend to manually collect the payment via the MMS, choose Pre-auth. In order to function normally the Paymentsense plugin performs outgoing connections to the Paymentsense gateway on port 4430 which is required to be open. In the case port 4430 on your server is closed you can still use the Paymentsense Hosted method with a limited functionality. Please note that by disabling the communication on port 4430 the online refund functionality will be disabled too. Recommended Setting "No". Please set to "Yes" only as a last resort when your server has port 4430 closed. Issue Number It seems you already have paid for this order. In case of doubts, please contact us. Module Options Month No Once the transaction status and authentication code are confirmed set the order status to "Processing" and process the order normally.  Order ID is empty. Order Prefix: Pay securely by Credit or Debit card through  Payment (3DS) failed due to:  Payment (3DS) processed successfully.  Payment Form Additional Field Payment Form Mandatory Fields Payment failed due to unknown or unsupported payment status. Payment Status:  Payment failed due to:  Payment processed successfully.  Pending payment Phone Number can be altered on payment form: Please check your card details and try again. Please contact Customer Support. Please enable SSL/TLS. Please log into your account at the MMS and check that transaction %1$s is processed with status SUCCESS and the message: %2$s.  Port 4430 is NOT open on my server (safe mode with refunds disabled): Post Code Mandatory: Pre-Auth Redirecting... Refund was declined.  Result Delivery Method: Sale Settings related to troubleshooting and diagnostics of the plugin. Specifies whether requests for extended plugin information are allowed. Used for troubleshooting and diagnostics. Recommended Setting "Yes". State/County Mandatory: Thank you - your order is now pending payment. You should be automatically redirected to Paymentsense to make payment. The Server Result Method determines how the transaction results are delivered back to the WooCommerce store. The following options affect how the  The gateway settings cannot be validated at this time. These are the gateway settings to allow you to connect with the Paymentsense gateway. (These are not the details used to login to the MMS) These options allow the customer to change the email address and phone number on the payment form. These options allow you to change what fields are mandatory for the customers to complete on the payment form. (The default settings are recommended by Paymentsense) This controls the description which the customer sees during checkout. This controls the title which the customer sees during checkout. This is located within the MMS under "Account Admin Settings" > "Account Settings". This is the gateway MerchantID not used with the MMS login. The Format should match the following ABCDEF-1234567 This is the gateway Password not used with the MMS login. The Password should use lower case and uppercase letters, and numbers only. This is the hash method set in MMS under "Account Admin" > "Account Settings". By default, this will be SHA1. This is the order prefix that you will see in the MMS. This module is not configured. Please configure gateway settings. This module requires an encrypted connection.  This option allows the customer to change the email address that entered during checkout. By default the Paymentsense module will pass the customers email address that they entered during checkout. This option allows the customer to change the phone number that entered during checkout. By default the Paymentsense module will pass the customers phone number that they entered during checkout. Tick only if you have an American Express MID associated with your Paymentsense gateway account. Title: Transaction Type: Troubleshooting Settings Unsupported Result Delivery Method. WARNING: The authenticity of the status of this transaction cannot be confirmed automatically! Please check the status at the MMS.  Warning: Paymentsense Gateway for WooCommerce plugin is incompatible with the following plugin(s): %s. Warning: Port 4430 seems to be closed on your server. Paymentsense Direct can NOT be used in this case. Please use Paymentsense Hosted or open port 4430. Warning: Port 4430 seems to be closed on your server. Please open port 4430 or set the "Port 4430 is NOT open on my server" configuration setting to "Yes". Warning: The Paymentsense plugin cannot connect to the Paymentsense gateway. Please contact support providing the information below: <pre>%s</pre> Warning: The Paymentsense plugin cannot resolve any of the Paymentsense gateway entry points. Please check your DNS resolution or contact support. Warning: WooCommerce 3.5.0 and 3.5.1 contain a bug that affects the Hosted payment method of the Paymentsense plugin. Please consider updating WooCommerce. We are now redirecting you to Paymentsense to complete your payment. Year Yes 