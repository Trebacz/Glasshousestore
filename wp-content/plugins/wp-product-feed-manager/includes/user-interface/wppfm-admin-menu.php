<?php
/**
 * WP Product Feed Manager Admin Menu functions
 *
 * Functions for handling admin pages
 *
 * @since 1.0.0
 * 
 * @author 		Michel Jongbloed
 * @category 	Menus
 * @package 	User-interface
 * @version     1.3
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Add the feed manager menu in the Admin page
 */
function wppfm_add_feed_manager_menu( $channel_updated = false ) {

	// defines the feed manager menu
	add_menu_page(
		__( 'WP Feed Manager', 'wp-product-feed-manager' ), 
		__( 'Feed Manager', 'wp-product-feed-manager' ), 
		'manage_woocommerce', 'wp-product-feed-manager', 
		'wppfm_main_admin_page', 
		esc_url( WPPFM_PLUGIN_URL . '/images/app-rss-plus-xml-icon.png' ) 
	);

	add_submenu_page(
		'wp-product-feed-manager',
		__( 'Add Feed', 'wp-product-feed-manager' ), 
		__('Add Feed', 'wp-product-feed-manager' ), 
		'manage_woocommerce', 
		'wp-product-feed-manager-add-new-feed', 
		'wppfm_add_feed_page' 
	);

	// add the settings 
	add_submenu_page(
	'wp-product-feed-manager', __( 'Settings', 'wp-product-feed-manager' ),  __( 'Settings', 'wp-product-feed-manager' ), 'manage_woocommerce', 'wppfm-options-page', 'wppfm_options_page' );
}

add_action( 'admin_menu', 'wppfm_add_feed_manager_menu' );

/**
 * starts the main admin page
 */
function wppfm_main_admin_page() {
	$start = new WPPFM_Main_Admin_Page ();
	// now let's get things going
	$start->show();
}

function wppfm_add_feed_page() {
	$add_new_feed_page = new WPPFM_Add_Feed_Page ();
	$add_new_feed_page->show();
}

/**
 * options page
 */
function wppfm_options_page() {
	$add_options_page = new WPPFM_Add_Options_Page ();
	$add_options_page->show();
}

/**
 * Checks if the backups are valid for the current database version and warns the user if not
 * 
 * @since 1.9.6
 */
function wppfm_check_backups() {
	if( !wppfm_check_backup_status() ) {
		$msg = __( "Due to the latest update your Feed Manager backups are no longer valid! Please open the Feed Manager Settings page, remove all your backups in and make a new one.", 'wp-product-feed-manager' )
			?><div class="notice notice-warning is-dismissible">
			<p><?php echo $msg; ?></p>
		</div><?php
	}
}

add_action( 'admin_notices', 'wppfm_check_backups' );

// ref HWOTBERH
function wppfm_validate() {
	if ( get_option( 'wppfm_lic_status' ) === 'valid' ) {
		if ( date( 'Ymd' ) === get_option( 'wppfm_lic_status_date' ) ) {
			return 'valid';
		} else {
			return wppfm_edd_status();
		}
	} else {
		return wppfm_edd_status();
	}
}

add_action('admin_init', 'wppfm_validate');

function wppfm_edd_status() {
	$edd_status = wppfm_check_license( get_option( 'wppfm_lic_key' ) );

	update_option( 'wppfm_lic_status', $edd_status );

	if ( $edd_status === 'valid' ) {
		update_option( 'wppfm_lic_status_date', date( 'Ymd' ) );
		return 'valid';
	} else {
		return $edd_status;
	}
}

function wppfm_register_option() {
	// creates our settings in the options table
	register_setting('wppfm_lic_group', 'wppfm_lic_key', 'wppfm_sanitize_license' );
}

add_action('admin_init', 'wppfm_register_option');

function wppfm_sanitize_license( $new ) {
	$old = get_option( 'wppfm_lic_key' );

	if( $old && $old != $new ) {
		delete_option( 'wppfm_lic_status' ); // new license has been entered, so must reactivate
	}
	
	return $new;
}

function wppfm_activate_license() {
	// listen for our activate button to be clicked
	if( isset( $_POST['wppfm_license_activate'] ) ) {

		// run a quick security check 
	 	if( !check_admin_referer( 'wppfm_lic_nonce', 'wppfm_lic_nonce' ) ) {
			return false; // get out if we didn't click the Activate button
		}

		// retrieve the license from the form
		$license = trim( $_POST[ 'wppfm_license_key'] );
			
		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license'   => $license, 
			'item_name' => urlencode( EDD_SL_ITEM_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_post( EDD_SL_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) { 
			echo wppfm_handle_wp_errors_response( $response, "Error 2121. Activating your license failed. Please contact support@wpmarketingrobot.com for support on this issue." );
			return false; 
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		if ( $license_data ) {
			// $license_data->license will be either "active" or "inactive"
			update_option( 'wppfm_lic_status', $license_data->license );
			update_option( 'wppfm_lic_key', $license );
			update_option( 'wppfm_lic_expires', $license_data->expires );
			update_option( 'wppfm_lic_status_date', date( 'Ymd' ) );
		} elseif ( strpos( $response['body'], 'Fatal error' ) ) {
			echo wppfm_show_wp_error( sprintf( __( "An error has occured. It is possible that the wpmarketingrobot website is down. 
				Please check if wpmarketingrobot.com is still active. If not, or if this error consist, please contact
				auke@wpmarketingrobot.com. Error Message: %s" , 'wp-product-feed-manager'), $response['body'] ) );
		}
	}
}

add_action('admin_init', 'wppfm_activate_license');

/**
 * Shows a message on all wp admin pages when the license needs to be renewed
 * 
 * @since 1.9.5
 */
function wppfm_check_license_expiration() {
	$lic_key = get_option( 'wppfm_lic_key' );
	$expires = get_option( 'wppfm_lic_expires' );
	
	if ( $expires === 'lifetime' ) { return; }
	
	if ( !$lic_key || !$expires ) { return; }
	
	$today = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
	$one_week_date = date_add( date_create( $today ), date_interval_create_from_date_string( '1 week') );
	$almost_expires_date = date_format( $one_week_date, 'Y-m-d H:i:s' );
	$plugin_name = get_plugin_data( WPPFM_PLUGIN_DIR.'wp-product-feed-manager.php' );
	$msg = '';
	$is_dismissible = 'is-dismissible';
	
	if ( $expires < $almost_expires_date ) { 
		wppfm_check_license( $lic_key ); // make sure the status is correct
		$expires = get_option( 'wppfm_lic_expires' );
		$almost_expires_date = date_format( $one_week_date, 'Y-m-d H:i:s' );
	}

	if( $expires <= $today ) {
		$msg = sprintf( __("You have an invalid or expired license key for your %s plugin. 
			Please <a href='%scheckout/?edd_license_key=%s&utm_source=client-admin&utm_medium=renewal_link&utm_campaign=license_expired'>
			click here to go to the Licenses renewal page</a> to keep the plugin working and your product feeds valid.", 
			'wp-product-feed-manager'), $plugin_name['Name'], EDD_SL_STORE_URL, $lic_key );
		$msg_id = 'wppfm-licence-expired';
		$msg_type = 'error';
		$is_dismissible = '';
	} else if ( $expires < $almost_expires_date  ) {
		$msg = !get_option( 'wppfm_license_notice_surpressed' ) ? sprintf( __("The license key for your %s plugin is expiring soon. 
			If you wish you can <a href='%scheckout/?edd_license_key=%s&utm_source=client-admin&utm_medium=renewal_link&utm_campaign=license_renewal'>
			click here</a> to renew your license key directly so you can be sure your product feeds keep working.", 'wp-product-feed-manager'), 
			$plugin_name['Name'], EDD_SL_STORE_URL, $lic_key ) : '';
		$msg_id = 'wppfm-licence-almost-expired';
		$msg_type = 'info';
	} else {
		update_option( 'wppfm_license_notice_surpressed', false );
	}

	if( $msg ) { 
		delete_option( 'wppfm_lic_status_date' ); // keep checking if there is an update on the license status
		
		?><div data-dismissible="notice-one-forever" class="notice notice-<?php echo $msg_type; ?> <?php echo $is_dismissible; ?>" id="<?php echo $msg_id; ?>">
			<p><?php echo $msg; ?></p>
		</div><?php
	}
}

add_action('admin_notices', 'wppfm_check_license_expiration');

function wppfm_check_license( $license ) {

	// return false if no license is given
	if ( !$license ) { return false; }
	
	$item_name = urlencode( EDD_SL_ITEM_NAME );
	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => $item_name
	);

	$response = wp_remote_get( add_query_arg( $api_params, EDD_SL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
	
	if ( is_wp_error( $response ) ) {
		echo wppfm_handle_wp_errors_response( $response, "Error 2122. Checking your license failed. Please contact support@wpmarketingrobot.com for support on this issue." );
		return false; 
	}
	
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	
	update_option( 'wppfm_lic_expires', $license_data->expires );
	
	if( $license_data && $license_data->license == 'valid' ) {
		return 'valid'; // this license is still valid
	} else {
		delete_option( 'wppfm_check_license_expiration' ); // reset the expiration messages
		return $license_data->license; // this license is no longer valid
	}
}