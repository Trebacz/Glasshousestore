<?php
/**
 * Plugin Name: WooCommerce Advanced Review Reminder
 * Plugin URI: http://kodemann.com
 * Description: Sends out customized emails asking your customers for leaving a review of their purchase. Increase conversions with social proof - Get reviews from your customers.
 * Author: kodemann.com
 * Author URI: http://kodemann.com
 * Version: 2.2
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

/*
Changelog


= 2.2 =
* Introducing filtering by category. Only send out review requests for products in specific category/categories.

= 2.1 =
* WooCommerce 3.0 compability - minor fixes.
* Danish translation updated
* .POT file for translations

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

View the full list in CHANGELOG.md

 */

if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly

class advanced_review_rem {

	public static function init() {
		$class = __CLASS__;
		if (empty($GLOBALS[$class])) {
			$GLOBALS[$class] = new $class;
		}
	}

	public function __construct() {
		add_action('init', array(&$this, "on_init"));
		add_action('admin_menu', array(&$this, 'add_menu_link'));
		add_action('arr_send_email', array(&$this, "send_email_reminder"), 1, 3);
		add_action('woocommerce_order_status_changed', array(&$this, 'woocommerce_order_status_changed'), 1, 3);
		add_action('add_meta_boxes', array(&$this, 'add_review_request_metabox'));
		add_action('save_post', array(&$this, 'post_save_send_request_immediately'), 1, 2);
		add_action('admin_footer', array($this, 'bulk_admin_footer'), 10);
		add_action('load-edit.php', array($this, 'bulk_action'));
		add_action('admin_notices', array($this, 'bulk_admin_notices'));
		add_filter('plugin_action_links', array(&$this, 'add_settings_link'), 10, 5);
	} // __construct()

	/**
	 * Add the menu item under WooCommerce page
	 * @author larsk
	 */
	function add_menu_link() {
		add_submenu_page('woocommerce',
			__('Review Reminder', 'wc-review-reminder'),
			__('Review Reminder', 'wc-review-reminder'),
			'manage_options',
			'wc-settings&tab=email&section=wc_review_reminder',
			array(&$this, '_dashboard_page')
		);
	}

/**
 * Adds a direct link to settings from plugin overview page.
 * @author larsk
 * @param  [type] $actions     [description]
 * @param  [type] $plugin_file [description]
 * @since  1.9.4
 * @return void
 */
function add_settings_link($actions, $plugin_file) {
	static $plugin;

	if (!isset($plugin)) {
		$plugin = plugin_basename(__FILE__);
	}

	if ($plugin == $plugin_file) {
		$settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=email&section=wc_review_reminder">' . __('Settings', 'General') . '</a>');
		$actions  = array_merge($settings, $actions);
	}
	return $actions;
}

	/**
	 * Add extra bulk action options to mark orders as complete or processing
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
	 */
	public function bulk_admin_footer() {
		global $post_type;
		if ('shop_order' == $post_type) {
			?>
			<script type="text/javascript">
			jQuery(function() {
				jQuery('<option>').val('send_review_reminder').text('<?php _e('Send Review Email', 'wc-review-reminder')?>').appendTo('select[name="action"]');
				jQuery('<option>').val('send_review_reminder').text('<?php _e('Send Review Email', 'wc-review-reminder')?>').appendTo('select[name="action2"]');
			});
		</script>
		<?php
	}
}

	/**
	 * Process the new bulk actions for changing order status
	 */
	public function bulk_action() {
		$wp_list_table = _get_list_table('WP_Posts_List_Table');
		$action        = $wp_list_table->current_action();

		if ('send_review_reminder' != $action) {
			// Not sending review email, so lets
			return;
		}
		$post_ids   = array_map('absint', (array) $_REQUEST['post']);
		$sentemails = 0;
		foreach ($post_ids as $post_id) {
			$customeremail = get_post_meta($post_id, '_billing_email', true);
			$this->send_email_reminder($post_id, 0, $customeremail); // $days = 1 hardcoded to avoid sending dummy data. rework..
			$sentemails++;
		}
		$sendback = add_query_arg(array('post_type' => 'shop_order', 'sent_emails' => $sentemails, 'ids' => join(',', $post_ids)), '');
		wp_redirect(esc_url_raw($sendback));
		exit();

	}

	/**
	 * Shows confirmation message if a bulk review email has been sent.
	 */
	public function bulk_admin_notices() {
		global $post_type, $pagenow;

		// Bail out if not on shop order list page
		if ('edit.php' !== $pagenow || 'shop_order' !== $post_type) {
			return;
		}

		if (isset($_REQUEST['sent_emails'])) {

			$sent_emails = isset($_REQUEST['sent_emails']) ? absint($_REQUEST['sent_emails']) : 0;

			$message = '<p>' . sprintf(_n('Review email sent.', '%s review emails sent.', $sent_emails, 'wc-review-reminder'), number_format_i18n($sent_emails)) . '</p>';

			if (isset($_REQUEST['extra_message'])) {
				$message .= '<p>' . $_REQUEST['extra_message'] . '</p>';
			}

			echo '<div class="updated">' . $message . '</div>';

		}
	}

	/**
	 * Monitors any change to WooCommerce orders and schedules or unschedules accordingly.
	 * @author larsk
	 * @param $order_id 		- Affected order id
	 * @param $old_status 	- Previous status of the order
	 * @param $new_status 	- Current status of the order
	 * @since  1.8
	 * @return void
	 */
	function woocommerce_order_status_changed($order_id, $old_status, $new_status) {

		$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');
		$orderstatusname          = $advanced_review_settings['orderstatus'];
		$guestscanrate						= $advanced_review_settings['guestscanrate'];

		$wc_get_order_statuses = wc_get_order_statuses();

		foreach ($wc_get_order_statuses as $key => $status) {
			if ($key == 'wc-' . $old_status) {
				$oldstatuskey = $key;
			}
			if ($key == 'wc-' . $new_status) {
				$newstatuskey = $key;
			}
		}


		if ($newstatuskey == $orderstatusname) {
			// first check if guest reviews are allowed

			$order = new WC_Order($order_id);
			$_billing_email = get_post_meta($order_id, '_billing_email', true); // for {customer_email}

			if ( ( !email_exists( $_billing_email ) ) && ( ( !$guestscanrate ) OR ( $guestscanrate == 'no' ) ) ) {
				$this->log(sprintf(__('#%s Customer ordered by a guest and reviews by guests not allowed. No emails will be sent.', 'wc-review-reminder'), $order_id ));
				return; // Nevermind then...
			}

			if ( ( !email_exists( $_billing_email ) ) && ( $guestscanrate ) ) {
				// Do anything?
			}

			$this->log(sprintf(__('#%s marked as %s. Scheduling emails.', 'wc-review-reminder'), $order_id, wc_get_order_status_name($orderstatusname)));
			$this->action_woocommerce_order_status_completed($order_id);
		} else {
			$this->woocommerce_order_status_changed_remove_schedule($order_id, $new_status);
		}
	}

	public static function timerstart($watchname) {
		set_transient('_wcarg_' . $watchname, microtime(true), 60 * 1); // set the transient to be deleted soon.
	}

	public static function timerstop($watchname, $digits = 4) {
		$return = round(microtime(true) - get_transient('_wcarg_' . $watchname), $digits);
		delete_transient('_wcarg_' . $watchname);
		return $return;

	}

	/**
	 * Add review reminder meta box
	 * @author larsk
	 * @since  1.4
	 * @return null
	 */
	function add_review_request_metabox() {
		add_meta_box('wcarg_send_request_metabox', __('Email Review Request', 'wc-review-reminder'), array(&$this, 'wcarg_send_request_metabox'), 'shop_order', 'side', 'low');
	}

	/**
	 * Output meta box on edit order in admin
	 * @author larsk
	 * @since  1.4
	 * @return null
	 */
	function wcarg_send_request_metabox() {
		global $post;
		echo '<p>' . __('Send a request to review this order immediately. Any scheduled emails will still be sent.', 'wc-review-reminder') . '</p>';

		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="wcarr_send_immediately" id="wcarr_send_immediately" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
		?>
		<input type="submit" class="button send_reminder button-primary" name="send_reminder_now" value="<?php _e('Send reminder now', 'wc-review-reminder');?>">
		<?php
	}

	/**
	 * Checks if an email is already on the blocklist, returns true if email is blocked.
	 * @author larsk
	 * @since  1.9.3
	 * @return null
	 */
	function is_email_blocked($emailtotest) {
		if (!is_email($emailtotest)) {
			return false;
			// this is not an email, so return false
		}

		$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');
		$blocklist                = $advanced_review_settings['blocklist'];
		$blocklist_arr            = explode(',', $blocklist);

		if (in_array($emailtotest, $blocklist_arr)) {
			// email found, so return true
			return true;
		}

	}

	/**
	 * Process on save post (order) - send email requests immediately
	 * @author larsk
	 * @since  1.4
	 * @return null
	 */
	function post_save_send_request_immediately($post_id, $post) {
		if (!isset($_POST['send_reminder_now'])) {
			// we are not going to do anything, since the send button has not been clicked.
			return $post_id;
		}
		if (!wp_verify_nonce($_POST['wcarr_send_immediately'], plugin_basename(__FILE__))) {
			return $post_id;
		}
		if (!current_user_can('edit_post', $post->ID)) {
			return $post_id;
		}

		// Look up billing email
		if ($_POST['send_reminder_now']) {
			$_billing_email = get_post_meta($post_id, '_billing_email', true);

			if ($this->is_email_blocked($_billing_email)) {
				// user already on blocklist
				$this->log(sprintf(__('Manual review reminder - %s has requested not to get any more review emails. Email was not sent.', 'wc-review-reminder'), $post_id, $_billing_email));

				$order = new WC_Order($post_id);

				$order->add_order_note(sprintf(__('Manual review reminder - customer - %s - has requested not to get any more review emails. Email was not sent.'), $_billing_email));

				return $post_id;
			} else {
				$this->send_email_reminder($post_id, 0, $_billing_email);
			}

		}
	}

	/**
	 * Runs on several woocommerce do_action() to remove any orders
	 * @author larsk
	 * @param  int $order_id The unique order id
	 * @return null
	 */
	function woocommerce_order_status_changed_remove_schedule($order_id, $new_status) {
		$crons = _get_cron_array();
		$hook  = 'arr_send_email';

		foreach ($crons as $timestamp => $cron) {
			if (isset($cron[$hook])) {
				if (is_array($cron[$hook])) {
					foreach ($cron[$hook] as $details) {
						$target = $details['args'][0]; // order id from paramaters
						if ($target == $order_id) {
							// unset this scheduled event
							unset($crons[$timestamp][$hook]);
						}
					}
				}
			}
		}

		$this->log(sprintf(__('#%s has changed status to %s, no review e-mails scheduled.', 'wc-review-reminder'), $order_id, wc_get_order_status_name($new_status)));
		_set_cron_array($crons);
	}

	/**
	 * Runs when an order is marked as complete
	 * @author larsk
	 * @param  int $order_id The id of the order
	 * @return null
	 */
	function action_woocommerce_order_status_completed($order_id) {
		$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');
		// Check if plugin is enabled
		if ($advanced_review_settings['enabled'] != 'yes') {
			return $order_id;
		}

		$advanced_review_settings['excludeproducts'];

		$nosend_items = explode(',', $advanced_review_settings['excludeproducts']);


		if (is_array($nosend_items)) {
			//	$nosend_items = array('####', '####'); // replace hashes with product IDs to exclude
			$order = new WC_Order($order_id);
			$items = $order->get_items();
			foreach ($items as $item) {
				if (in_array($item['product_id'], $nosend_items)) {
					$this->log(sprintf(__('#%s No scheduled emails to be sent due to exclusion of product ID # %s', 'wc-review-reminder'), $order_id, $item['product_id']));
					return;
				}
			}

		}

		$order = new WC_Order($order_id);

		if (isset($order->billing_email)) $_billing_email = $order->billing_email;

		if (!$_billing_email) $_billing_email = get_post_meta($order_id, '_billing_email', true); // for {customer_email}

		if (!$_billing_email) $_billing_email = $order->get_billing_email(); // $order->billing_email;


		// TODO - only v3?
		//$_shipping_last_name  = $order->get_shipping_last_name();
		//$_shipping_first_name = $order->get_shipping_first_name();

		if ($this->is_email_blocked($_billing_email)) {
			// user already on blocklist
			$this->log(sprintf(__('#%s has changed status, but %s has requested not to get any more review emails. Nothing scheduled.', 'wc-review-reminder'),$order_id, $_billing_email));

			$order = new WC_Order($order_id);

			$order->add_order_note(sprintf(__('This order has changed, but %s has requested not to get any more review emails. Nothing scheduled.', 'wc-review-reminder'), $_billing_email));

			return $order_id;
		}






		$reminderdays = explode(',', $advanced_review_settings['interval']);
		if ($reminderdays) {
			$scheduleddays = '';
			if ((is_array($reminderdays)) && ($advanced_review_settings['interval'] != '')) {
				foreach ($reminderdays as $rd) {

					$args           = array($order_id, $rd, $_billing_email);
					$futuretime     = time() + ($rd * 86400);
					$scheduleresult = wp_schedule_single_event($futuretime, 'arr_send_email', $args);
					$scheduleddays .= date_i18n(get_option('date_format'), $futuretime) . ', ';

				}
			}
			if ($scheduleddays) {
				$this->log(sprintf(__('#%s Scheduled emails to be sent on following dates: %s', 'wc-review-reminder'), $order_id, $_billing_email, $scheduleddays));
			}
		}
	}

	/**
	 * Runs on 'init' action - checks for &okey= to add customer to blocklist
	 * @author larsk
	 */
	function on_init() {
		load_plugin_textdomain('wc-review-reminder', false, dirname(plugin_basename(__FILE__)) . '/languages');
		if (is_admin()) {
			return;
		}

		if (isset($_GET['okey'])) {
			if (!is_email($_GET['okey'])) {
				$okey = sanitize_key($_GET['okey']);
			} else {
				$okey = $_GET['okey'];
			}
		}
		// Look for the parsing of "?okey=". If parsed, look up billing email from unique order key and add to blocklist.
		if (isset($okey)) {
			$this->timerstart('processing_blacklisting');
			global $wpdb, $woocommerce;
			$post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '%s' AND meta_key='_order_key';", $okey));
			if ((isset($post_id)) OR (is_email($okey))) {
				$customer_email = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '%d' AND meta_key='_billing_email';", $post_id));

				// checking if the customer has already blocked him/herself
				if (is_email($customer_email)) {
					// $advanced_review_settings = get_option( 'woocommerce_wc_advanced_review_settings');
					// $blocklist = $advanced_review_settings['blocklist'];
					// $blocklist_arr = explode(',',$blocklist);
					if ($this->is_email_blocked($customer_email)) {
						// user already on blocklist
						return;
					}
				}
				if (($customer_email) or (is_email($okey))) {

					$order_id = get_post_meta($post_id, '_order_number', true);

					if (!$order_id) {
						$order_id = $post_id;
					}

					$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');
					$blocklist                = $advanced_review_settings['blocklist'];
					$blocklist_arr            = explode(',', $blocklist);
					if (is_array($blocklist_arr)) {

						if ($customer_email) {
							$blocklist_arr[]                       = $customer_email; // add to the blocked list
							$blocklist_arr                         = array_unique($blocklist_arr); // remove duplicates
							$advanced_review_settings['blocklist'] = implode(',', $blocklist_arr);
							update_option('woocommerce_wc_advanced_review_settings', $advanced_review_settings);
						}

						if (!$customer_email) {
							$customer_email = $okey; // set the customer email to the $okey (the test email) to test the unsubscribe email gets sent
						}

						$unsubscribetext        = $advanced_review_settings['unsubscribetext'];
						$unsubscribesubjectline = $advanced_review_settings['unsubscribesubjectline'];

						$order_date 						= new WC_Order($order_id); // ???

						$order_date           	= $order->order_date; // for {order_date}
						$_completed_date      	= get_post_meta($order_id, '_completed_date', true); // for {order_date_completed}
						$_billing_email     	 	 = get_post_meta($order_id, '_billing_email', true); // for {customer_email}
						$_order_key     	      = get_post_meta($order_id, '_order_key', true); // unique order key - used for blacklisting emails link
						$blacklist_link   	    = add_query_arg( array('okey'=>$_order_key)  ,$this->_return_stop_link()); // for {blacklist_link}

						$stoplink             = '<a href="' . $blacklist_link . '?okey=' . $_order_key . '">' . $advanced_review_settings['stoptext'] . '</a>';
						$_shipping_last_name  = get_post_meta($order_id, '_shipping_last_name', true);
						$_shipping_first_name = get_post_meta($order_id, '_shipping_first_name', true);
						$_order_list          = '';

						$_completed_date_time = strtotime($_completed_date); // completed date in UNIX format
						$now                  = current_time("mysql"); // this gets the proper local time to compare with
						$_now_time            = strtotime($now);

						$replace_list                         = array();
						$replace_list['{customer_name}']      = $_shipping_first_name . ' ' . $_shipping_last_name;
						$replace_list['{customer_firstname}'] = $_shipping_first_name;
						$replace_list['{customer_lastname}']  = $_shipping_last_name;
						$replace_list['{order_id}']           = $order_id;

						$saved_order_id = get_post_meta($order_id, '_order_number', true); // look up the "real" internal order id
						if ($saved_order_id != $order_id) {
							$replace_list['{order_id}'] = $saved_order_id;
						}

						$replace_list['{customer_email}']       = $customer_email;
						$replace_list['{order_date}']           = $order_date;
						$replace_list['{order_date_completed}'] = $_completed_date;
						$replace_list['{stop_emails_link}']     = $stoplink;
						$replace_list['{blacklist_link}']       = $blacklist_link;
						$replace_list['{order_list}']           = $_order_list;
						$replace_list['{site_title}']           = get_bloginfo('name'); // from parsed paramater

						if (function_exists('icl_translate')) {
							$unsubscribesubjectline = icl_translate('WC Advanced Review Reminder', 'Unsubscribe email subject', $unsubscribesubjectline);
							$unsubscribetext        = icl_translate('WC Advanced Review Reminder', 'Unsubscribe email content', $unsubscribetext);
						}

						foreach ($replace_list as $searchfor => $replacewith) {
							$unsubscribetext        = str_replace($searchfor, stripslashes($replacewith), $unsubscribetext);
							$unsubscribesubjectline = str_replace($searchfor, stripslashes($replacewith), $unsubscribesubjectline);
						}

						$mailer = $woocommerce->mailer();

						$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";

						$full_message = $mailer->wrap_message(
							$unsubscribesubjectline, $unsubscribetext
						);

						$mailer->send($customer_email, $unsubscribesubjectline, $full_message, $headers);

						// unsetting scheduled emails for this order.
						$crons = _get_cron_array();
						$hook  = 'arr_send_email';

						$newcronlist = array();
						// look up schedules crons and delete
						foreach ($crons as $timestamp => $cron) {
							if (isset($cron[$hook])) {
								if (is_array($cron[$hook])) {
									foreach ($cron[$hook] as $details) {
										$target = $details['args'][0]; // order id from paramaters
										if ($target != $order_id) {
											// No match, add to the cron we will return...
											$newcronlist[] = $cron;
										}
									}
								}
							}
						}

						$processing_blacklisting = $this->timerstop('processing_blacklisting', 2);

						$this->log(sprintf(__('%s added him/herself to the blocklist and an email confirmation was sent. Process took %s sec', 'wc-review-reminder'), $customer_email, $processing_blacklisting));

					}
				}
			}
		}

	} // on_init()




	/**
	 * Returns the url set to direct unsubscribers to
	 * @author larsk
	 * @param  none
	 * @return null
	 */
	function _return_stop_link() {
		$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');
		if ( $advanced_review_settings['stoplink'] ) {
			return $advanced_review_settings['stoplink'];
		}
		return site_url(); // Default if email is not set.
	}




	/**
	 * Logging function
	 * @author larsk
	 * @param  string  $text Text to be logged.
	 * @param  integer $prio Prioritization of log. Can set different style via CSS.
	 * @return null
	 */
	function log($text, $prio = 0) {
		global $wpdb;
		$woocommerce_arr_log = $wpdb->prefix . "woocommerce_arr_log";
		$time                = current_time("mysql");
		$text                = esc_sql($text);
		$daquery             = "INSERT INTO `$woocommerce_arr_log` (time,prio,note) VALUES ('$time','$prio','$text');"; //todo - set up with $wpdb->prepare()
		$result              = $wpdb->query($daquery);
		$total               = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$woocommerce_arr_log`;");
		if ($total > 1000) {
			$targettime = $wpdb->get_var("SELECT `time` from `$woocommerce_arr_log` order by `time` DESC limit 500,1;");
			$query      = "DELETE from `$woocommerce_arr_log`  where `time` < '$targettime';";
			$success    = $wpdb->query($query);
		}
	}

	/**
	 * Sends email reminder
	 * @author larsk
	 * @param  int    $order_id 	Order ID to process - Use 0 as value to send dummy email and data
	 * @param  int    $days		Number of days reminder
	 * @param  string $email		Customers email
	 * @return null
	 */
	function send_email_reminder($order_id, $days, $email) {
		global $wpdb, $woocommerce;
		$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');

		$this->timerstart('email_reminder_timer');

		// Check if plugin is enabled
		if (($advanced_review_settings['enabled'] != 'yes') AND ($order_id != 0)) {
			// skip if set to send test email
			wp_clear_scheduled_hook('arr_send_email', array($order_id, $days, $email)); // Remove the current cron if not enabled.
			return;
		}

		if ($this->is_email_blocked($email)) {
			$this->log(sprintf(__('#%s - %s has requested not to get any more review emails. Email was not sent.', 'wc-review-reminder'), $order_id, $email));
				wp_clear_scheduled_hook('arr_send_email', array($order_id, $days, $email)); // Remove the current cron.

				$order = new WC_Order($order_id);
				$order->add_order_note(sprintf(__('Customer - %s - has requested not to get any more review emails. Email was not sent.'), $email));
				return;
			}

			$button_bg_color = $advanced_review_settings['buttonbg'];
			$buttoncolor     = $advanced_review_settings['buttoncolor'];
			$buttontext      		= $advanced_review_settings['buttontext'];
			$productcategories  	= $advanced_review_settings['productcategories'];

			if (function_exists('icl_translate')) {
				$buttontext = icl_translate('WC Advanced Review Reminder', 'Please Review Now text', $advanced_review_settings['buttontext']);
			}

			$order                = new WC_Order($order_id);

			$_completed_date      = get_post_meta($order_id, '_completed_date', true);

			if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
			$_billing_email       = $order->get_billing_email(); // $order->billing_email;
			$_shipping_last_name  = $order->get_shipping_last_name();
			$_shipping_first_name = $order->get_shipping_first_name();
			$_order_key           = $order->get_order_key();
			$order_date = $order->get_date_created(); // for {order_date}
		}
		else {
			$_billing_email       = $order->billing_email;
			$_shipping_last_name  = $order->shipping_last_name;
			$_shipping_first_name = $order->shipping_first_name;
			$_order_key = get_post_meta($order_id, '_order_key', true);
				$order_date           = $order->order_date; // for {order_date}
			}

		$blacklist_link   	  = add_query_arg( array('okey'=>$_order_key)  ,$this->_return_stop_link()); // for {blacklist_link}
		$stoplink      			  = '<a href="' . $blacklist_link . '">' . $advanced_review_settings['stoptext'] . '</a>'; // for {stop_emails_link}

		$_order_list          = '';
		$items                = $order->get_items();
		$items_for_review     = 0; // counter for items in review list.
		$product_table        = '<table>';

		if ($items) {
			$_order_list .= '<ul>';
			foreach ($items as $item) {
				$order->get_product_from_item($item);
				$product_id = $item['product_id'];

				// only do the following if we have any whitelisted categories from settings
				if ( (is_array($productcategories)) && (!empty($productcategories)) )  {
					$categories = array(); // reset
					$terms = wp_get_post_terms( $product_id, 'product_cat' );
					foreach ( $terms as $term ) $categories[] = $term->slug;
					$matches = array_intersect($productcategories,$categories);
					if ( (is_array($matches)) && (!empty($matches)) ) {
						$product_ok_to_use = true;
					}
					else {
						$product_ok_to_use = false;
					}
				} else {
					$product_ok_to_use = true; // no whitelisting by category going on...
				}

				$comments_open = comments_open($product_id);

				if ($comments_open && $product_ok_to_use) {

					$matched_to_review = false;
					$args              = array('post_id' => $product_id);
					$comments          = get_comments($args);
					if ($comments) {
						foreach ($comments as $comment) {
							if ($comment->comment_author_email == $_billing_email) {
								$matched_to_review = true; // a match. the customer being email has already made a review!
							}
						}
					}
					if ($matched_to_review) {
						// We can do stuff here. If we want.
					} else {
						$_order_list .= "<li><a href='" . get_permalink($product_id) . "'>" . $item['name'] . "</a></li>";
						$items_for_review++;
					}

					// generate html table for products
					$_product       = $item['product_id'];
					$product        = new WC_Product($item['product_id']);
					$button         = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td><a href="' . get_permalink($item['product_id']) . $advanced_review_settings['urlappend'] . '" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: ' . $buttoncolor . '; text-decoration: none; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; background-color: ' . $button_bg_color . '; border-top: 12px solid ' . $button_bg_color . '; border-bottom: 12px solid ' . $button_bg_color . '; border-right: 18px solid ' . $button_bg_color . '; border-left: 18px solid ' . $button_bg_color . '; display: inline-block;">' . $buttontext . '</a></td></tr></table></td></tr></table>';
					$_style_tdstyle = 'text-align:left; vertical-align:top; word-wrap:break-word;';
					$src            = wp_get_attachment_image_src(get_post_thumbnail_id($item['product_id']), array(150, 150));
					$image          = '';
					$image          = get_the_post_thumbnail($product->get_id(), apply_filters('single_product_large_thumbnail_size', array(150, 150)));
					$image_title    = esc_attr(get_the_title(get_post_thumbnail_id()));
					$image_link     = wp_get_attachment_url(get_post_thumbnail_id());
					if (is_array($src)) {
						$imagehtml = apply_filters('woocommerce_order_product_image', '<img src="' . $src[0] . '" alt="' . $product->get_title() . '" height="150" width="150" style="vertical-align:middle; margin-right: 10px;" />', $_product);
					} else {
						$imagehtml = apply_filters('woocommerce_single_product_image_html', sprintf('<img src="%s" alt="' . $product->get_title() . '" height="150" width="150" style="vertical-align:middle; margin-right: 10px;" />', wc_placeholder_img_src()), $product->ID);
					}
					$product_table .= '<tr>';
					$product_table .= '<td style="' . $_style_tdstyle . '">' . $imagehtml . '</td><td style="' . $_style_tdstyle . '"><h3>' . $product->get_title() . '</h3>';


					if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '3.0', '>=' ) ) {
						$product_table .= wp_trim_words(apply_filters('the_content', $product->get_short_description()), 25); // todo - find content SSSS
					}
					// Backwards compability
					else {
						$product_table .= wp_trim_words(apply_filters('the_content', $product->post->post_excerpt), 25); // todo - find content SSSS
					}

					$product_table .= '<p>' . $product->get_price_html() . '</p>';
					$product_table .= $button;
					$product_table .= '</td></tr>';
				} // if ($comments_open)
				else {
					$this->log(sprintf(__('#%s - %s product does not have comments/reviews enabled. Skipped.', 'wc-review-reminder'), $order_id, $item['name']));

					wp_clear_scheduled_hook('arr_send_email', array($order_id, $days, $_billing_email));
				}
			} // foreach ($items as $item)

			$_order_list .= '</ul>';

		}

		$product_table .= '</table><!-- end product_table -->';

		$_completed_date_time = strtotime($_completed_date); // completed date in UNIX format
		$now                  = current_time("mysql"); // this gets the proper local time to compare with
		$_now_time            = strtotime($now);

		$replace_list                         = array();
		$replace_list['{customer_name}']      = $_shipping_first_name . ' ' . $_shipping_last_name;
		$replace_list['{customer_firstname}'] = $_shipping_first_name;
		$replace_list['{customer_lastname}']  = $_shipping_last_name;
		$replace_list['{order_id}']           = $order_id;
		$saved_order_id                       = get_post_meta($order_id, '_order_number', true);

		if ($saved_order_id != $order_id) {
			$replace_list['{order_id}'] = $saved_order_id;
		}

		$replace_list['{customer_email}']       = $email;
		$replace_list['{order_date}']           = $order_date;
		$replace_list['{order_date_completed}'] = $_completed_date;
		$replace_list['{stop_emails_link}']     = $stoplink;
		$replace_list['{blacklist_link}']       = $blacklist_link;
		$replace_list['{order_list}']           = $_order_list;
		$replace_list['{order_table}']          = $product_table;
		$replace_list['{days_ago}']             = $days; // from parsed paramater
		$replace_list['{site_title}']           = get_bloginfo('name'); // from parsed paramater

		if ($order_id == 0) {
			// use dummy data for test email

			$button = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td><a href="' . site_url('/') . $advanced_review_settings['urlappend'] . '" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: ' . $buttoncolor . '; text-decoration: none; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; background-color: ' . $button_bg_color . '; border-top: 12px solid ' . $button_bg_color . '; border-bottom: 12px solid ' . $button_bg_color . '; border-right: 18px solid ' . $button_bg_color . '; border-left: 18px solid ' . $button_bg_color . '; display: inline-block;">' . $buttontext . '</a></td></tr></table></td></tr></table>';

			$_style_tdstyle = 'text-align:left; vertical-align:top; word-wrap:break-word;';

			$product_table = '<table><tbody><tr><td style="' . $_style_tdstyle . '"><img src="' . wc_placeholder_img_src() . '" alt="Alt text" height="150" width="150" style="vertical-align:middle; margin-right: 10px;"></td><td style="text-align:left; vertical-align:top; word-wrap:break-word;"><h3>Example Product #1</h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus mi erat, ultrices ut erat eget, fermentum malesuada massa. Praesent tempor blandit massa, quis auctor augue. Ut id urna in nunc maximus tincidunt non sit amet velit. Maecenas gravida laoreet tempor.<p><del><span class="amount">$100</span></del> <ins><span class="amount">$85</span></ins></p>' . $button . '</td></tr><tr><td style="' . $_style_tdstyle . '"><img src="' . wc_placeholder_img_src() . '" alt="Alt text" height="150" width="150" style="vertical-align:middle; margin-right: 10px;"></td><td style="text-align:left; vertical-align:top; word-wrap:break-word;"><h3>Example Product #2</h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus mi erat, ultrices ut erat eget, fermentum malesuada massa. Praesent tempor blandit massa, quis auctor augue.<p><del><span class="amount">$100</span></del> <ins><span class="amount">$85</span></ins></p>' . $button . '</td></tr></tbody></table>';

			$replace_list['{customer_name}']        = 'John Doe';
			$replace_list['{customer_firstname}']   = 'John';
			$replace_list['{customer_lastname}']    = 'Doe';
			$replace_list['{order_id}']             = '1';
			$replace_list['{customer_email}']       = $email;
			$replace_list['{order_date}']           = $now;
			$replace_list['{order_date_completed}'] = $now;
			$replace_list['{blacklist_link}']       = $blacklist_link;
			$replace_list['{stop_emails_link}']     = '<a href="' . $blacklist_link . '">' . $advanced_review_settings['stoptext'] . '</a>';
			$replace_list['{order_list}']           = '<ul><li><a href="' . site_url() . '">' . __('Example Product Review Link #1', 'wc-review-reminder') . '</a></li><li><a href="' . site_url() . '">' . __('Example Product Review Link #2', 'wc-review-reminder') . '</a></li></ul>';
			$replace_list['{order_table}']          = $product_table;
			$items_for_review                       = 1; // to trigger generation
		} // if ($order_id == 0)  DUMMY DATA

		$message      = stripslashes($advanced_review_settings['email']);
		$subject_line = $advanced_review_settings['subject'];

		// integrating in to WPML translations
		if (function_exists('icl_translate')) {
			$subject_line                       = icl_translate('WC Advanced Review Reminder', 'Email subject', $subject_line);
			$message                            = icl_translate('WC Advanced Review Reminder', 'Email content', $message);
			$replace_list['{stop_emails_link}'] = '<a href="' . $blacklist_link . '">' . icl_translate('WC Advanced Review Reminder', 'Unsubscribe from review emails anchor', $advanced_review_settings['stoptext']) . '</a>';
		}

		foreach ($replace_list as $searchfor => $replacewith) {
			$message      = str_replace($searchfor, stripslashes($replacewith), $message);
			$subject_line = str_replace($searchfor, stripslashes($replacewith), $subject_line);
		}

		if ($items_for_review > 0) {
			$mailer = $woocommerce->mailer();

			$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";

			$full_message = $mailer->wrap_message(
				$subject_line, $message
			);

			$mailer->send($email, $subject_line, $full_message, $headers);

			$email_reminder_timer = $this->timerstop('email_reminder_timer', 2);

			if ($days != 0) {
				// Add log entry to own log system
				$this->log(sprintf(__('#%s E-mail sent to %s - Day %s reminder. Took %s sec.', 'wc-review-reminder'), $order_id, $email, $days, $email_reminder_timer));
				// Add note to the order
				$order->add_order_note(sprintf(__('Review reminder sent by Advanced Review Reminder - Day %s reminder. Took %s sec', 'wc-review-reminder'), $days, $email_reminder_timer));
			}
			else {
				$this->log(sprintf(__('#%s E-mail sent to %s - Manual request. Took %s sec.', 'wc-review-reminder'), $order_id, $email, $days, $email_reminder_timer));
				$order->add_order_note(sprintf(__('Manual request for review sent by Advanced Review Reminder. Took %s sec.', 'wc-review-reminder'), $email_reminder_timer));
			}
		}
		wp_clear_scheduled_hook('arr_send_email', array($order_id, $days, $email));
	}
} // advanced_review_rem class
add_action('plugins_loaded', array('advanced_review_rem', 'init'));

register_activation_hook(__FILE__, '_activate_routines');

/**
 * Activation routines
 * @author larsk
 * @since    1.2
 */
function _activate_routines() {

	global $wpdb;
	require_once ABSPATH . '/wp-admin/includes/upgrade.php';

	$table_name = $wpdb->prefix . "woocommerce_arr_log";
	if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		prio tinyint(1) NOT NULL,
		note tinytext NOT NULL,
		PRIMARY KEY (id)
	) CHARACTER SET utf8 COLLATE utf8_general_ci";
	dbDelta($sql);
}
}

register_deactivation_hook(__FILE__, '_deactivate_routines');
/**
 * Deactivation routines
 * @author larsk
 * @since  1.0
 * @return null
 */
function _deactivate_routines() {

	$advanced_review_settings = get_option('woocommerce_wc_advanced_review_settings');

	$removeondeactivate = $advanced_review_settings['removeondeactivate'];

	if ($removeondeactivate != 'yes') {
		return;
	}
	// Dont clean emails and database unless user set this setting.

	wp_clear_scheduled_hook('arr_send_email');
	// loop through all crons and remove any scheduled emails.
	$crons = _get_cron_array();
	$hook  = 'arr_send_email';
	foreach ($crons as $timestamp => $cron) {
		if ((isset($cron[$hook])) AND (is_array($cron[$hook]))) {
			$target = $details['args'][0]; // order id from paramaters
			unset($crons[$timestamp][$hook]);
		}
	}
	_set_cron_array($crons);
	global $wpdb;
	$table_name = $wpdb->prefix . "woocommerce_arr_log";
	$wpdb->query("DROP TABLE $table_name;");

}

add_filter('woocommerce_email_classes', "woocommerce_review_gatherer_email");

/**
 * Includes the email class for WooCommerce - Moved outside main class to fix problem with other plugins overruling.
 * @author larsk
 * @param  array $email_classes WooCommerce incoming $email_classes
 */
function woocommerce_review_gatherer_email($email_classes) {
	require_once 'includes/class-wc-advanced-review-reminder.php';
	$email_classes['WC_Review_Reminder'] = new WC_Review_Reminder();
	return $email_classes;
}