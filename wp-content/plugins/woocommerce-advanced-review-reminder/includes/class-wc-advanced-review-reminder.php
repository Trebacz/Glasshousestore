<?php
if (!defined('ABSPATH')) {
	exit;
}
class WC_Review_Reminder extends WC_Email {
	public function __construct() {
		// WC 3+ ?
		$this->customer_email = true;
		//$this->template_html  = 'emails/customer-new-account.php'; // TODO - SSSS
		//$this->template_plain = 'emails/plain/customer-new-account.php'; // TODO - SSSS

//http://wc3.dev/wp-admin/admin.php?page=wc-settings&tab=email&section=wc_email_customer_reset_password
//http://wc3.dev/wp-admin/admin.php?page=wc-settings&tab=email&section=wc_review_reminder


		$this->id          = 'wc_advanced_review';
		$this->title       = 'Advanced Review Reminder';
		$this->description = __('Reminders to review purchases can be sent out automatically.', 'wc-review-reminder');
		$this->heading     = __('We would love your feedback', 'wc-review-reminder');
		$this->subject     = __('Please help us by reviewing', 'wc-review-reminder');


		//add_action('woocommerce_order_status_changed', array(&$this, 'trigger'));
		// We cannot do like this. A catchall has been set up instead, that also checks if an email is scheduled to send, and then remove it as necessary. See main plugin file for the general trigger.

		parent::__construct();
	}


	/**
	 * get_content_html function.
	 *
	 * @since 0.1
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		woocommerce_get_template($this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
		));
		return ob_get_clean();
	}

	/**
	 * Custom process admin options - sends test email if inputfield is not empty
	 * @author larsk
	 * @return void
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		/*
			Registers with WPML
		*/
			if ((isset($_POST['woocommerce_wc_advanced_review_subject'])) && (function_exists('icl_register_string'))) {
				icl_register_string('WC Advanced Review Reminder', 'Email subject', $_POST['woocommerce_wc_advanced_review_subject']);
			}

			if ((isset($_POST['woocommerce_wc_advanced_review_email'])) && (function_exists('icl_register_string'))) {
				icl_register_string('WC Advanced Review Reminder', 'Email content', $_POST['woocommerce_wc_advanced_review_email']);
			}

			if ((isset($_POST['woocommerce_wc_advanced_review_unsubscribetext'])) && (function_exists('icl_register_string'))) {
				icl_register_string('WC Advanced Review Reminder', 'Unsubscribe text', $_POST['woocommerce_wc_advanced_review_unsubscribetext']);
			}
			if ((isset($_POST['woocommerce_wc_advanced_review_unsubscribesubjectline'])) && (function_exists('icl_register_string'))) {
				icl_register_string('WC Advanced Review Reminder', 'Unsubscribe confirmation email subject', $_POST['woocommerce_wc_advanced_review_unsubscribesubjectline']);
			}
			if ((isset($_POST['woocommerce_wc_advanced_review_stoptext'])) && (function_exists('icl_register_string'))) {
				icl_register_string('WC Advanced Review Reminder', 'Unsubscribe from review emails anchor', $_POST['woocommerce_wc_advanced_review_stoptext']);
			}

			if ((isset($_POST['woocommerce_wc_advanced_review_buttontext'])) && (function_exists('icl_register_string'))) {
				icl_register_string('WC Advanced Review Reminder', 'Please Review Button text', $_POST['woocommerce_wc_advanced_review_buttontext']);
			}

			if ((isset($_POST['arr_email_recipient'])) && (is_email($_POST['arr_email_recipient']))) {
				global $advanced_review_rem, $wpdb;

			// try to detect earliest set day interval
				$intervals = explode(',', $_POST['woocommerce_wc_advanced_review_interval']);

				if (isset($intervals[0])) {
				// pick up the first
					$testinterval = intval($intervals[0]);
				} else {
				$testinterval = 7; // set default to 7 days after if not properly detected
			}
			$advanced_review_rem->send_email_reminder(0, $testinterval, $_POST['arr_email_recipient']);
			unset($_POST['arr_email_recipient']); // unset so test email only gets sent once.
		}

	}

	/**
	 * Adds custom HTML to settings page.
	 * @author larsk
	 * @return void
	 */
	public function admin_options() {
		?>
		<h2><?php _e('WooCommerce Advanced Review Reminder', 'wc-review-reminder');?></h2>
		<p><a href="<?php echo plugin_dir_url(dirname(__FILE__)) . 'documentation/index.html'; ?>" target="_blank"><?php _e('Click here to read the documentation', 'wc-review-reminder');?></a></p>
		<?php
		global $advanced_review_rem;

		$woocommerce_enable_review_rating = get_option('woocommerce_enable_review_rating');
		?>
		<?php
		if ('no' === $woocommerce_enable_review_rating) {
			?>
			<div style="border:2px solid #ff0000; padding:10px;">
				<h3><?php _e('Reviews are not turned on. Customers will not be able to rate products with the star rating.', 'wc-review-reminder');?></h3>
				<p><a href="<?php echo admin_url('admin.php?page=wc-settings&tab=products'); ?>"><?php _e('Click here to enable ratings.', 'wc-review-reminder');?></a></p>
			</div>
			<?php
		}
		?>

		<style>
		.logtable .shortcol {
			min-width: 150px;
		}
		.logtable .shortcol.prio-0 {
			color:#afafaf !important;
		}
	</style>

	<table class="form-table">
		<?php $this->generate_settings_html();?>
	</table>
	<h3><?php _e('Send Test Email', 'wc-review-reminder');?></h3>
	<?php wp_nonce_field('arr_nonce');?>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="arr_email_recipient"><?php _e('Email Recipient', 'wc-review-reminder');?></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e('Email Recipient', 'wc-review-reminder');?></span></legend>
						<input class="input-text regular-input" type="text" name="arr_email_recipient" id="arr_email_recipient"  value="">
						<p class="description"><?php _e('Enter a valid email to send a test email.', 'wc-review-reminder');?><br />
						</p>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	global $wpdb;
	$logtable = $wpdb->prefix . "woocommerce_arr_log";
	$query    = "SELECT * FROM $logtable order by `time` DESC LIMIT 50;";
	$logs     = $wpdb->get_results($query, ARRAY_A);
	$time     = date('Y-m-d H:i:s ', time());
	if ($logs) {
		?>
		<h3><?php _e('Logs', 'wc-review-reminder');?></h3>
		<table class="wp-list-table widefat logtable">
			<thead>
				<tr>
					<th scope="shortcol" class="shortcol"><?php _e('Time', 'wc-review-reminder');?></th>
					<th scope="col"><?php _e('Event', 'wc-review-reminder');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($logs as $log) {
					echo "<tr><td class='shortcol prio-" . $log['prio'] . "'>" . $log['time'] . "</td><td class='prio-" . $log['prio'] . "'>" . stripslashes($log['note']) . "</td></tr>";
				}
				?>

			</tbody>
			<tfoot>
				<tr>
					<th scope="shortcol" class="shortcol"><?php _e('Time', 'wc-review-reminder');?></th>
					<th scope="col"><?php _e('Event', 'wc-review-reminder');?></th>
				</tr>
			</tfoot>
		</table>
		<?php
} // if ($logs)

$crons = _get_cron_array();

$hook = 'arr_send_email';
if ($crons) {
	echo "<h3>" . __('Scheduled Emails', 'wc-review-reminder') . "</h3>";
	echo "<ul class='scheduledemails'>";
	$totalscheduled = 0;
	foreach ($crons as $timestamp => $cron) {
		if ((isset($cron[$hook])) AND (is_array($cron[$hook]))) {
			$details = $cron[$hook];
			foreach ($details as $key => $detail) {
				$days = $detail['args'][1];
				$user = $detail['args'][2];
			}
			echo "<li>Day " . $days . " reminder to be sent to " . $user . " at " . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $timestamp) . "</li>";
			$totalscheduled++;
		}
	}
	echo "</ul>";
	echo "<h5>" . $totalscheduled . ' reminders scheduled</h5>';
		} // if ($crons)
	}

/**
 * get_content_plain function.
 *
 * @since 0.1
 * @return string
 */
public function get_content_plain() {
	ob_start();
	woocommerce_get_template($this->template_plain, array(
		'order'         => $this->object,
		'email_heading' => $this->get_heading(),
	));
	return ob_get_clean();
}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 2.0
	 */
	public function init_form_fields() {

		$macrolisttable = '<p class="description">You can use these macros to customize the content of the email:</p>';

		$macrolisttable .= '<table id="macrolist">';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{customer_name}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the customers name.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{customer_firstname}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the customers name.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{customer_lastname}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the customers name.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{customer_email}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the customer email.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{site_title}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the site title.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{order_id}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the order id.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{order_date}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the date and time of the order.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{order_date_completed}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the date the order was marked completed.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{stop_emails_link}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with a link to stop recieving email review reminders.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{order_list}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with a list of products purchased but not reviewed.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{order_table}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with a nice looking table with product images and short description.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '<tr><td style="padding-top:3px;padding-bottom:3px;"><code>{days_ago}</code></td><td style="padding-top:3px;padding-bottom:3px;">' . __('Replaced with the number of days ago the order was made.', 'wc-review-reminder') . '</td></tr>';

		$macrolisttable .= '</table>';

		if (function_exists('wc_get_order_statuses')) {
			$wc_get_order_statuses = wc_get_order_statuses();
			if ($wc_get_order_statuses) {
				$status_list = array();
				foreach ($wc_get_order_statuses as $key => $wc_status) {
					$status_list[$key] = $wc_status . ' (' . $key . ')';
				}
			}
		}

		// Get list of product categories.
		$all_categories = get_categories( array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 0,
			'hide_empty'   => false
		) );

		// Convert list of product categories to array
		if ($all_categories) {
			$catlist = array();
			foreach ($all_categories as $ac) {
				$catlist[$ac->slug] = $ac->name;
			}
		}

		$this->form_fields = array(
			'enabled'	=> array(
				'title'   => __('Enable/Disable', 'wc-review-reminder'),
				'type'    => 'checkbox',
				'label'   => __('Enable this email notification', 'wc-review-reminder'),
				'default' => 'yes',
			),
			'interval'	=> array(
				'title'       => __('Day(s) after order', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('You can choose how many days after a order has been completed before a reminder email is sent.', 'wc-review-reminder'),
				'placeholder' => '7,14',
				'default'     => '7,14',
			),
			'orderstatus'	=> array(
				'title'       => __('Order status to schedule', 'wc-review-reminder'),
				'type'        => 'select',
				'description' => __('When an order reaches a specific status, the reminder is scheduled to be sent. Choose which status. Default status is <code>Completed</code>.', 'wc-review-reminder'),
				'default'     => 'wc-completed',
				'options'     => $status_list,
			),

			'productcategories' => array(
				'title'         => __( 'Product Categories', 'wc-review-reminder' ),
				'type'          => 'multiselect',
				'description'   => __( 'Email will only be sent if products are in the following categories. Disable by choosing none or all categories.', 'wc-review-reminder' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $catlist,
				'select_buttons' => true,
				'placeholder' => __('Select one or more categories','wc-review-reminder')
			),

			'excludeproducts'	 => array(
				'title'       => __('Exclude products', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('Ignore emails with these product ids. Comma-seperated list of product ids. No reminder emails with these product ids will be sent.', 'wc-review-reminder'),
				'default'     => '',
			),

			'guestscanrate'	=> array(
				'title'   => __('Allow guests to review', 'wc-review-reminder'),
				'type'    => 'checkbox',
				'label'   => __('Allow orders by guests (not logged in) to review purchases.', 'wc-review-reminder'),
				'description' => __('Depending on your website configuration it might not be possible for guests to leave reviews. Disabled by default.', 'wc-review-reminder'),
				'default' => '',
			),

			'customizesection'       => array(
				'title'       => ' ',
				'type'        => 'title',
				'description' => '<h3>' . __('Customize Email Content', 'wc-review-reminder') . '</h3><hr>',
			),

			'subject'                => array(
				'title'       => __('Email subject', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('The email subject line.', 'wc-review-reminder'),
				'placeholder' => '',
				'default'     => __('[{site_title}] Review recently purchased products', 'wc-review-reminder'),
			),
			'email'                  => array(
				'title'       => __('Email Content', 'wc-review-reminder'),
				'type'        => 'textarea',
				'description' => __('This is the email template', 'wc-review-reminder') . $macrolisttable,
				'placeholder' => '',
				'default'     => __("Hello {customer_name},\n\nThank you for purchasing items from the {site_title} shop!\n\nWe would love if you could help us and other customers by reviewing the products you recently purchased.  It only takes a minute and it would really help others by giving them an idea of your experience.  Click the link below for each product and review the product under the 'Reviews' tab.\n\n{order_table}\n\nMuch appreciated,\n\n{site_title}.\n\n{stop_emails_link}", 'wc-review-reminder')
			),

			'buttonbg'               => array(
				'title'       => __('Button Background Color', 'wc-review-reminder'),
				'type'        => 'text',
				'css'         => 'width:6em;height:2em;',
				'description' => __('Background color for Review Now buttons in email. Default <code>#ad74a2</code>.', 'wc-review-reminder'),
				'default'     => '#ad74a2',
				'class'       => 'colorpick',
			),
			'buttoncolor'            => array(
				'title'       => __('Button Text Color', 'wc-review-reminder'),
				'type'        => 'text',
				'css'         => 'width:6em;height:2em;',
				'description' => __('Font color for Review Now buttons in email. Default <code>#ffffff</code>.', 'wc-review-reminder'),
				'default'     => '#ffffff',
				'class'       => 'colorpick',
			),
			'buttontext'             => array(
				'title'       => __('Text on button', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('If you use the <code>{product_table}</code> macro you can change the text on the button here. Default <code>Review Now</code>.', 'wc-review-reminder'),
				'default'     => __('Review Now', 'wc-review-reminder'),
			),
			'urlappend'              => array(
				'title'       => __('Append to link', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('<code>#reviews</code> normally links to the Reviews tab. Change the link here or add tracking parameters', 'wc-review-reminder'),
				'default'     => '#reviews',
			),
			'unsubscribesection'     => array(
				'title'       => ' ',
				'type'        => 'title',
				'description' => '<h3>' . __('Unsubscribe Options', 'wc-review-reminder') . '</h3><hr>',
			),
			'stoptext'               => array(
				'title'       => __('Stop Receiving Emails Text', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('This text will be made in to a clickable link you can use with the <code>{stop_emails_link}</code> macro.', 'wc-review-reminder'),
				'placeholder' => __('Unsubscribe from review emails', 'wc-review-reminder'),
				'default'     => __('Unsubscribe from review emails', 'wc-review-reminder'),
			),
			'stoplink'               => array(
				'title'       => __('Unsubscribe page', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('Enter url for page to send customers who unsubscribes. This is the link that will be used in emails. If empty, the frontpage of your website will be used.', 'wc-review-reminder'),
//				'placeholder' => __('Enter full url', 'wc-review-reminder'),
				'placeholder' => 'http://',
				'default'     => ''
			),
			'unsubscribesubjectline' => array(
				'title'       => __('Stop Receiving Emails Confirmation', 'wc-review-reminder'),
				'type'        => 'text',
				'description' => __('This will be the subject line in the unsubscribe confirmation email. Note: You can use these macros: <code>{customer_name}</code>, <code>{customer_firstname}</code>, <code>{customer_lastname}</code>, <code>{customer_email}</code>, <code>{site_title}</code>, <code>{order_id}</code>, <code>{order_date}</code> and <code>{order_date_completed}</code>.', 'wc-review-reminder'),
				'placeholder' => __('You are now unsubscribed', 'wc-review-reminder'),
				'default'     => __('You are now unsubscribed', 'wc-review-reminder'),
			),
			'unsubscribetext'        => array(
				'title'       => __('Unsubscribe message', 'wc-review-reminder'),
				'type'        => 'textarea',
				'description' => __('This is the email template that will be sent to users who unsubscribe.', 'wc-review-reminder'),
				'placeholder' => '',
				'default'     => __("Hello {customer_name}\n\nYou are now unsubscribed from further emails requesting product reviews.\n\n{site_title}", 'wc-review-reminder'),
			),
			'blocklist'              => array(
				'title'       => __('Email blocklist', 'wc-review-reminder'),
				'type'        => 'textarea',
				'description' => __('Comma separated list of emails that have asked not to recieve any more review reminders.', 'wc-review-reminder'),
				'placeholder' => '',
				'default'     => '',
			),
			'removeondeactivate'     => array(
				'title' => __('Remove scheduled emails and log database', 'wc-review-reminder'),
				'type'  => 'checkbox',
				'label' => __('If this is set, all scheduled emails and tables in the database will be removed upon deactivating the plugin.', 'wc-review-reminder'),
			),
		);
}
} // end WC_Review_Reminder class