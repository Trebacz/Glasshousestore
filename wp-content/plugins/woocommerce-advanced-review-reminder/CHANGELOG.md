= 2.1 =
* WooCommerce 3.0 compability - minor fixes.

= 2.0 =
* New: Setting - Do not send review requests to guest shoppers (does not have account).

= 1.9.7.1 =
* Deprecated WooCommerce functions replaced with new ones. wp_get_attachment_image_src().
* Blurry thumbnail image fix.

= 1.9.7 =
 * New: Unsubscribe url. Enter url in settings on your website to send customers who unsubscribe.
 * Fix: Customer who added themselves to blocklist sometimes still got an email. These are now properly blocked.
 * Fix: Bugs in unsubscribe process, sometime unsubscribe email was not properly sent.
 * Fix: Some PHP notices in code.

= 1.9.6 =
 * Rewrote email sending code.
 * Minor code cleaning
 * Moved older changelog notes to changelog.md

= 1.9.5 =
 * Fix for shortcodes showing up in product table in emails. The text used in the outgoing emails come from the Product Short Description for each product.
 * Cleaned up code and removed unneeded files.
 * Updated language files.

= 1.9.4 =
 * Added product exclusion in settings. Do not send reminder emails for specific products. - Thank you for suggestion and sample code, isaachg :-)
 * Direct link to settings page under "WooCommerce" in admin navigation.

= 1.9.3 =
 * Do not schedule emails for customers who have unsubscribed.
 * Improved handling of unsubscribed customers.
 * Danish translation updated.

= 1.9.2 =
 * Fix problem with multiple emails sent for unsubscribers.
 * Emails can no longer be sent to customers who have unsubscribed, even when trying manually.

= 1.9.1 =
 * Fixed wrong parameter count for order status change.

= 1.9 =
 * NEW: Bulk email sending from order overview page.
 * NEW: Link to settings page from plugin overview page.
 * NEW: You now get a warning on the settings page if product ratings are turned off in WooCommerce.
 * NEW: Checks if an individual product has reviews enabled or not before sending emails.

= 1.8.1 =
 * Cleaned up code and improved logic, minor fixes.
 * Danish translation now 100% completed.
 * Spanish translation improved, not completed.

= 1.8 =
 * NEW: You can choose any "order status", even custom ones for when to schedule sending emails.
 * NEW: You can now use  macros in the unsubscribe email template. (Not all macros are supported, some do not make sense to use).
 * FIX: Unsubscribe email is now sent using WooCommerce email template system also (before only plaintext version was sent).
 * FIX: Some cases when customers unsubscribed not all scheduled emails were removed.

= 1.7.1 =
 * To remove scheduled emails and remove log, you have to check a setting. Before emails could get removed during upgrades.

= 1.7 =
 * Automatically adds #reviews to end of product urls in emails. If you have turned on reviews and your theme follows WooCommerce CSS markup, the link will lead directly to the review tab/area.
 * Added option to change default "#reviews" - You can also attach tracking parameters here if you want to keep track of how many click a product link from these emails.
 * Updated documentation included with this plugin - See top of page WooCommerce -> Settings -> Emails -> WooCommerce Advanced Review Reminder.

= 1.6.4 =
 * FIX: Compability with WooCommerce Sequential Order Numbers.
 * Updated Danish translations.

= 1.6.3 =
 * Better WPML integration.
 * New HTML buttons for better compability.

= 1.6.2 =
 * NEW: Customize colors for Review Now buttons in settings page.
 * NEW: Customize button text for the Review Now button.
 * Updated some Spanish and Danish translations.

= 1.6.1 =
 * Fixing the button color in emails.

= 1.6 =
 * Default text now uses {order_table}

= 1.5.5 =
 * Missing {order_table} dummy data in test email - thanks emielm.
 * Better styling on review button in emails - thanks emielm.

= 1.5.4 =
 * NEW: {order_table} shortcode to show images and big button of purchased products.

= 1.5.3 =
 * Fix for using mb_encode_mimeheader() on PHP installations without mb_ extension installed. (http://php.net/manual/en/function.mb-encode-mimeheader.php - Install instructions here: http://php.net/manual/en/mbstring.installation.php)

= 1.5.2 =
 * Minor PHP Notice fix for using esc_sql() instead of mysql_real_escape_string()

= 1.5.1 =
 * Fix: When setting 'Day(s) after order' to empty '', no emails will be sent, only by clicking the manual button on the order page.

= 1.5 =
 * Unsubscribe confirmation - Users now get a confirmation email they have unsubscribed from further emails.

= 1.4.3 =
 * Minor fix for PHP undefined index, 'send_reminder_now'

= 1.4.2 =
 * Fix for UTF8 encoding problem in database.

= 1.4.1 =
 * Fix: Bug in the scheduling and immediate order sending fixed.

= 1.4 =
 * New: See email sending log notes directly on order page in admin
 * New: Send review request immediately via button on order page in admin

= 1.3.1 =
 * Fix for UTF-8 characters in subject line.
 * Minor fix for a PHP warning.

= 1.3 =
 * Added {customer_firstname} and {customer_lastname} macros by customer request.

= 1.2.1 =
 * Fix: Compability with WooCommerce Custom Order Statuses & Actions plugin.
 * Code cleanup/refactoring.
 * Danish translation updated.
 * Updated documentation, added instructional video and added link to documentation from settings page.

= 1.2 =
 * New: Introducing logging, so you can see what is going on.

= 1.1.1 =
 * Fix: Save THEN send test email. No need to first save and then afterwards send test email.
 * Fix: Changed link in documentation to new CodeCanyon link: http://codecanyon.net/user/kodemann

= 1.1 =
 * You can now send a test email out from the settings page.

= 1.0.1 =
 * Removed buggy update script.

= 1.0 =
 * First release.