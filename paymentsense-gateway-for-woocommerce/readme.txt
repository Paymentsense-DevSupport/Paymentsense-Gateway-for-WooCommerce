=== WooCommerce Paymentsense Gateway ===

Contributors: paymentsense, alexanderkaltchev
Tags: paymentsense, payments, checkout, credit card
Requires at least: 4.4
Tested up to: 5.0
Stable tag: 3.0.12
Requires PHP: 5.6.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Extends WooCommerce, allowing you to take payments via Paymentsense. Provides integration with Paymentsense Hosted and Direct.

== Description ==

Paymentsense is a plugin that extends WooCommerce, allowing you to take payments via Paymentsense. The plugin provides integration with the Paymentsense Hosted and Paymentsense Direct payment methods.

In regards to taking payments online through your website, you have two options Hosted and Direct. 

Hosted means that during the checkout process the user is redirected to a page hosted by Paymentsense in order to enter their card details and finalise the payment and then is redirected back to your site for the order confirmation. This also has a lower PCI compliance level and has less technical requirements from the web server.

Direct on the other hand means that during the checkout process the customer enters their card details directly onto your website. This has a higher level of PCI compliance and also requires port 4430 to be open as well as an active SSL certificate.

= Support =

<a href="mailto:devsupport@paymentsense.com">devsupport@paymentsense.com</a>


== Installation ==

= Minimum Requirements =

* PHP version 5.6.0 or greater
* WordPress 4.4+ (tested up to 4.9.8)
* WooCommerce 3.x (tested up to 3.5.2). Warning: WooCommerce 3.5.x contains a bug that affects the Hosted payment method of the Paymentsense plugin.
* PCI-certified server using SSL/TLS in order to use the Direct Method

= Plugin Installation =

The Paymentsense plugin can be installed by any of the following three ways:

= Installation through the WordPress plugin directory =

1. Login into the admin area of your WordPress website.

2. Go to "Plugins" -> "Add New".

3. Type "Paymentsense" into the search box.

4. The plugin should appear in the search results.

5. Click the "Install Now" button.

6. Wait while the plugin is retrieved.

7. Click the "Activate" link associated with "Paymentsense Gateway for WooCommerce" plugin.


= Installation by uploading of the plugin's zip file through the admin area =

1. Login into the admin area of your WordPress website.

2. Go to "Plugins" -> "Add New".

3. Click "Upload Plugin" link at the top of the page.

4. Click "Browse" and choose to the plugin's zip file.

5. Click the "Install Now" button.

6. Wait while the plugin is uploaded to your server.

7. Click the "Activate" link associated with "Paymentsense Gateway for WooCommerce" plugin.


= Installation by copying the content of the plugin's zip file =

1. Copy the content of the plugin's zip file to the `/wp-content/plugins` directory.

2. Login into the admin area of your WordPress website.

3. Go to "Plugins".

4. Click the "Activate" link associated with "Paymentsense Gateway for WooCommerce" plugin.

= Activation and configuration of the Paymentsense Hosted Gateway =

1. Go to "WooCommerce" -> "Settings" -> "Checkout".

2. Click on the Paymentsense Hosted Gateway.

3. Check the "Enable Paymentsense Hosted" checkbox.

4. Set the values for the "Gateway MerchantID", "Gateway Password", "Gateway PreSharedKey" and "Gateway Hash Method" settings according to their respective values in the Paymentsense Merchant Management System (MMS).

5. Optionally, set the rest of the settings as per your needs.

6. Click the "Save changes" button.


= Activation and configuration of the Paymentsense Direct Gateway =

1. Go to "WooCommerce" -> "Settings" -> "Checkout".

2. Click on the Paymentsense Direct Gateway.

3. Check the "Enable Paymentsense Direct" checkbox.

4. Set the values for the "Gateway MerchantID" and "Gateway Password" settings according to their respective values in the Paymentsense Merchant Management System (MMS).

5. Optionally, set the rest of the settings as per your needs.

6. Click the "Save changes" button.


== Changelog ==

## [3.0.12] - 2018-12-20
### Added
- Dynamic gateway entry points configuration
- Extended module information feature
