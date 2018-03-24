=== Google Customer Reviews for WooCommerce ===
Contributors: ecreationsllc, natekinkead
Tags: WooCommerce, Google Customer Reviews, Google Merchant, Google Trusted Sites, Opt-in, Badge, Google Rating Badge, GCR
Requires at least: 3.0.0
Tested up to: 4.9.4
Stable tag: 2.6
License: GPLv3
License URI: http://www.gnu.org/licenses/quick-guide-gplv3.html

Integrates Google Merchant Center's Google Customer Reviews survey opt-in and badge into your WooCommerce store.

== Description ==

This is THE WordPress plugin to integrate Google Merchant Center's "Google Customer Reviews" into your WooCommerce store.

It allows the Survey Opt-in code onto your Thank You (Order Received) page with the option to pick which location the popup will appear.  It can also send GTIN data to Google for each product purchase which enabled product reviews.

It also integrates the Google Customer Reviews Badge onto your site.  You can choose to show it in the bottom left or bottom right.  You can also choose to only show the badge when users are in the WooCommerce area of your website.

To enable the functionality of this plugin, you need to paste your Google Merchant ID in the plugin settings page.

Made with love by <a href="https://www.ecreations.net" target="_blank">eCreations</a>, the <a href="https://www.ecreations.net/arizona-woocommerce-expert/" target="_blank"><strong>ONLY</strong> Certified WooCommerce Expert in Arizona!</a>

<strong>Check Out our Premium Plugins</strong>

<a href="https://www.ecreations.net/shop/woocommerce-transaction-central/" target="_blank" title="WooCommerce Extension / Plugin for Transaction Central">WooCommerce Extension / Plugin for Transaction Central</a><br />
<a href="https://www.ecreations.net/shop/woocommerce-extension-for-paid-calendar-events/" target="_blank" title="WooCommerce Extension for Paid Calendar Events">WooCommerce Extension for Paid Calendar Events</a><br>
<a href="https://www.ecreations.net/shop/woocommerce-extension-coupon-for-product-attributes/" target="_blank" title="WooCommerce Extension for Product Attribute Coupon">WooCommerce Extension for Product Attribute Coupon</a><br>
<a href="https://www.ecreations.net/shop/woocommerce-extension-export-orders-shopworks/" target="_blank" title="WooCommerce Extension to Export Orders to ShopWorks">WooCommerce Extension to Export Orders to ShopWorks</a><br>
<a href="https://www.ecreations.net/shop/woocommerce-extension-to-sponsor-calendar-events/" target="_blank" title="WooCommerce Extension to Sponsor Calendar Events">WooCommerce Extension to Sponsor Calendar Events</a>

= Features: =

* Integrates Google Customer Reviews on your WooCommerce store
* Adds the Survey Opt-in code onto your Thank You (Order Received) page
* Integrates the Google Customer Reviews Badge onto your site
* Options to choose language, survey opt-in popup position, estimated delivery (days), rating badge position, only show badge in shop
* Supports the latest version of WooCommerce.  Yes, we're WooCommerce 3.0 compatible!
* Extend this plugin using a hook to conditionally hide the Google Customer Reviews Badge on certain pages

== Installation ==

To install Google Customer Reviews for WooCommerce, follow these steps:

1. Download and unzip the plugin

2. Upload the entire ecr-google-customer-reviews/ directory to the /wp-content/plugins/ directory

3. Activate the plugin through the Plugins menu in WordPress

== Screenshots ==

1. The Survey Opt-in popup on the Thank You (Order Received) Page
2. The Google Customer Reviews Rating Badge
3. The settings page for Google Customer Reviews for WooCommerce plugin

== Frequently Asked Questions ==

= How do I add GTINs (Global Trade Item Numbers) to my products to enable Product Reviews? =

Edit each product in WooCommerce.  In the "Product data" panel, click on the "Inventory" tab.  You should see a new field labeled "GTIN".  Enter your UPC, EAN, or ISBN for the product and click "Update".

= Why is the survey opt-in not showing up? =

There are many reasons why the survey opt-in is not coming up, most of them out of our control.  This plugin integrates the code according to Google's guidelines, but it's up to Google and your Merchant Center account to actually display something.

* Make sure you have an active Google Merchant account and you've activated the Google Customer Reviews program within your account.
* Make sure you've visited the setting page ( Settings > Google Customer Reviews ) and configured your settings with your correct Google Merchant ID number.

= Does the survey opt-in show on mobile devices? =

Yes, it does now!  Originally it didn't and at that time, according to Google, the GCR opt-in survey would not show on mobile devices.  They had said "Thanks for your feedback though and we will take it onboard for future reference."

Well, I'm happy to report that it seems Google really has taken this feedback and implemented the survey popup on mobile devices.  No update of this plugin is necessary for this new feature.

If you find it's not working for you, please let us know in the support forum.  Thanks.

= Why does the survey opt-in not show on some browsers =

I don't have a complete answer for this yet, but I've heard reports of users not able to see the survey opt-in on some browsers.

In one case, simply going to that same (order received) URL in Incognito Mode resolved the issue, so there must be some kind of browser extension or recognized session that prevents the survey opt-in.

But, rest assured, if it works in Incognito Mode, then it should work for your customers.

= Why have I not received the survey email yet? =

There is a setting for this plugin that allows you to choose the number of days that Google should wait before sending the email. This setting is called “Estimated Delivery (days)”. This is to make sure the survey goes out after the customer has received the item.

Please be aware that Google often will send the email 3-4 days after the amount of days that you provide in this setting.  I think that is to give the customer ample time to use the product in order to be able to give a review.

= Why does the email address appear html encoded on the survey opt-in? =

In the rare case you are using another WordPress plugin that obfuscates email addresses, that can cause this issue with this plugin.  The email on the survey opt-in will appear with a bunch of seemingly random characters.

One such plugin that causes this issue is WP-Spamshield.  If using that plugin, check the option called "Disable email harvester protection" and that will fix the issue.

= How do I use a hook to conditionally hide the Google Customer Reviews Badge on certain pages? =

You can use the filter hook called 'ecr_show_gcr_badge'.  Return false inside of a condition to prevent it from displaying for that condition.  Here is an example that hides the badge for product ID 1280.

`function my_gcr_badge_function($show) {
	if(get_the_ID() == 1280) {
		return false;
	}
	return $show;
}
add_filter('ecr_show_gcr_badge', 'my_gcr_badge_function');`

== Changelog ==

= 2.6 =
* Bug fix

= 2.5 =
* Bug fix to only show GTIN if populated

= 2.4 =
* Bug fix
* Added option to display the GTIN field in the product meta on the front-end

= 2.3 =
* Removed an action from a common manually-implemented duplicate GTIN field on simple products.

= 2.2 =
* Added GTIN support for Product Variations
* GTIN field now shows a sample value of each meta key
* Added ability to disable GTIN by selecting NONE in the dropdown
* Added filter hook to conditionally hide the badge on certain pages
* Removed the transient cache feature from the product post meta field on the plugin settings page

= 2.1 =
* Added the ability to select an existing product meta field that contains GTIN data.

= 2.0 =
* NEW support for GTIN integration for product reviews
* Updated the list of available languages

= 1.3 =
* Updated to use WooCommerce 3.x getter methods with WooCommerce 2.x backward compatibility.

= 1.0.3 =
* Reverted unnecessary fix and updated FAQs.

= 1.0.2 =
* Fixed some cases where email needed html_entity_decode.

= 1.0.1 =
* Added option to enable or disable the rating badge.

= 1.0 =
* Initial Release