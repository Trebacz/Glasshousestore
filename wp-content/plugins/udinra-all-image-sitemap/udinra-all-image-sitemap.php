<?php
/*
Plugin Name: Udinra All Image Sitemap 
Plugin URI: https://udinra.com/downloads/udinra-image-sitemap-pro
Description: Automatically generates Google Image Sitemap and submits it to Google,Bing and Ask.com.
Author: Udinra
Version: 3.5.2
Author URI: https://udinra.com
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

function Udinra_Image() {
	$udinra_sitemap_response = '';
	if(isset($_POST['save_option'])){
		include 'lib/udinra_save_options.php';
		$udinra_sitemap_response = "Options saved successfully";
	}
	if(isset($_POST['create_sitemap'])) {
		udinra_image_sitemap_loop($udinra_sitemap_response);
	}
	include 'lib/udinra_panel_html.php';
}
 
function udinra_image_sitemap_loop(&$udinra_sitemap_response) {
	include 'init/udinra_init_image.php';
	include 'img/udinra_img.php';
	include 'exit/udinra_ping_image.php';
}

function udinra_image_sitemap_admin() {
	if (function_exists('add_options_page')) {
		add_options_page('Udinra Image Sitemap', 'Udinra Image Sitemap', 'manage_options', basename(__FILE__), 'Udinra_Image');
	}
}

function udinra_image_admin_style($hook) {
	
	if($hook == 'settings_page_udinra-all-image-sitemap') {
		wp_enqueue_style( 'udinra_image_pure_style', plugins_url('css/udstyle.css', __FILE__) );	
		wp_enqueue_script( 'udinra_image_pure_js', plugins_url('js/udinra_slideshow.js', __FILE__),array(), '1.0.0', true );
    }
}

function udinra_image_settings_plugin_link( $links, $file ) 
{
    if ( $file == plugin_basename(dirname(__FILE__) . '/udinra-all-image-sitemap.php') ) 
    {
        $in = '<a href="options-general.php?page=udinra-all-image-sitemap">' . __('Settings','udimage') . '</a>';
        array_unshift($links, $in);
   }
    return $links;
}

function load_sitemap_index_image() {
	load_template( dirname( __FILE__ ) . '/feed-sitemap-image.php' );
}

include 'lib/udinra_init_func.php';
include 'lib/udinra_multisite_func.php';

register_activation_hook(__FILE__, 'udinra_image_act');
register_deactivation_hook(__FILE__, 'udinra_image_deact');

add_action( 'transition_post_status', 'udinra_image_post_unpublished', 10, 3 );
add_action('admin_menu','udinra_image_sitemap_admin');	
add_action('admin_notices', 'udinra_image_admin_notice');
add_action('admin_init', 'udinra_image_admin_ignore');
add_action( 'do_feed_sitemap-index-image','load_sitemap_index_image',10,1 );
add_action( 'wpmu_new_blog', 'udinra_image_new_blog', 10, 6);        
add_action( 'admin_enqueue_scripts', 'udinra_image_admin_style' );
add_filter( 'plugin_action_links', 'udinra_image_settings_plugin_link', 10, 2 );

?>
