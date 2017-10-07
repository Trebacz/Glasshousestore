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
 * @version     1.2
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
		'manage_options', 'wp-product-feed-manager', 
		'wppfm_main_admin_page', 
		esc_url( MYPLUGIN_PLUGIN_URL . '/images/app-rss-plus-xml-icon.png' ) 
	);

	add_submenu_page(
		'wp-product-feed-manager',
		__( 'Add Feed', 'wp-product-feed-manager' ), 
		__('Add Feed', 'wp-product-feed-manager' ), 
		'manage_options', 
		'wp-product-feed-manager-add-new-feed', 
		'wppfm_add_feed_page' 
	);

	// add the settings 
	add_submenu_page(
	'wp-product-feed-manager', __( 'Settings', 'wp-product-feed-manager' ),  __( 'Settings', 'wp-product-feed-manager' ), 'manage_options', 'wppfm-options-page', 'wppfm_options_page' );

	if( !wppfm_check_backup_status() ) {
		echo wppfm_show_wp_warning( __( "Due to the latest update your Feed Manager backups are no longer valid! 
			Please open the Feed Manager Settings page, remove all your backups in and make a new one.", 'wp-product-feed-manager' ), true );
	}
	
// Obsolete 170217
//	add_options_page(
//	__( 'WP Feed Manage Options', 'wp-product-feed-manager' ), __( 'Feed Manager', 'wp-product-feed-manager' ), 'manage_options', 'wppfm_options_page', 'wppfm_options_page' );
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

// obsolete 050317
//function wppfm_license_menu() {
//	add_plugins_page( 'Plugin License', 'Plugin License', 'manage_options', 'pluginname-license', 'edd_sample_license_page' );
//}
//add_action('admin_menu', 'wppfm_license_menu');

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
			wppfm_handle_wp_errors_response( $response, "Error 2121. Activating your license failed. Please contact support@wpmarketingrobot.com for support on this issue." );
			return false; 
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		if ( $license_data ) {
			// $license_data->license will be either "active" or "inactive"
			update_option( 'wppfm_lic_status', $license_data->license );
			update_option( 'wppfm_lic_key', $license );
			update_option( 'wppfm_lic_status_date', date( 'Ymd' ) );
		} elseif ( strpos( $response['body'], 'Fatal error' ) ) {
			echo wppfm_show_wp_error( sprintf( __( "An error has occured. It is possible that the wpmarketingrobot website is down. 
				Please check if wpmarketingrobot.com is still active. If not, or if this error consist, please contact
				auke@wpmarketingrobot.com. Error Message: %s" , 'wp-product-feed-manager'), $response['body'] ) );
		}
	}
}

add_action('admin_init', 'wppfm_activate_license');

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
		wppfm_handle_wp_errors_response( $response, "Error 2122. Checking your license failed. Please contact support@wpmarketingrobot.com for support on this issue." );
		return false; 
	}
	
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	
	if( $license_data && $license_data->license == 'valid' ) {
		return 'valid'; // this license is still valid
	} else {
		return $license_data->license; // this license is no longer valid
	}
}