<?php
/*
Plugin Name: Srizon Facebook Album
Plugin URI: http://www.srizon.com/srizon-facebook-album
Description: Show your Facebook Albums/Galleries on your WordPress Site
Text Domain: srizon-facebook-album
Domain Path: /languages
Version: 3.3
Author: Afzal
Author URI: http://www.srizon.com/contact
*/

function srizon_fb_album_load_textdomain() {
	load_plugin_textdomain( 'srizon-facebook-album', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'srizon_fb_album_load_textdomain' );

// libraries
require_once 'lib/srizon_functions.php';
require_once 'lib/srizon_fb_album.php';
require_once 'lib/srizon_pagination.php';
require_once 'lib/srizon-fb-ui.php';
require_once 'lib/srizon-fb-db.php';

// font end files
if(!is_admin()) {
	require_once 'site/srizon-fb-front.php';
	require_once 'site/srizon-fb-album-front.php';
	require_once 'site/srizon-fb-gallery-front.php';
}

// backend files
if(is_admin()) {
	require_once 'admin/index.php';
}

register_activation_hook( __FILE__, 'srz_fb_install' );
register_uninstall_hook( __FILE__, 'srz_fb_uninstall' );
add_action( 'wpmu_new_blog', 'srz_on_create_blog', 10, 6 );

function srz_fb_install($network_wide) {
	global $wpdb;
	if ( is_multisite() && $network_wide ) {
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			SrizonFBDB::CreateDBTables();
			restore_current_blog();
		}
	} else {
		SrizonFBDB::CreateDBTables();
	}
}

function srz_fb_uninstall() {
	//SrizonFBDB::DeleteDBTables();
	//delete_option('srzfbcomm');
}

function srz_on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	// need to remove -pro from free version
	if ( is_plugin_active_for_network( 'srizon-facebook-album/srizon-fb-album.php' ) ) {
		switch_to_blog( $blog_id );
		SrizonFBDB::CreateDBTables();
		restore_current_blog();
	}
}
function srz_fb_get_resource_url( $relativePath ) {
	return plugins_url( $relativePath, plugin_basename( __FILE__ ) );
}
