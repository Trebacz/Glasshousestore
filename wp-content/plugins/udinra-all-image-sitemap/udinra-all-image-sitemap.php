<?php
/*
Plugin Name: Udinra All Image Sitemap 
Plugin URI: https://udinra.com/downloads/udinra-image-sitemap-pro
Description: Automatically generates XML Image Sitemap and submits it to Google,Bing and Ask.com.
Author: Udinra
Version: 3.6.1
Author URI: https://udinra.com
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

function Udinra_Image() {
	switch (true) {
		case isset($_POST['udswebsave']):
		case isset($_POST['udsimgsave']):
			include 'lib/udinra_save_options.php';
			break;		
		case isset($_POST['udscreate']):
			udinra_image_sitemap_loop();
			break;		
		default:
			update_option('udinra_image_sitemap_response','Select Options and Click Create Sitemap');
			break;
	}
	include 'lib/udinra_panel_html.php';
}

function udinra_image_sitemap_loop() {
	include 'init/udinra_init_image.php';
	include 'img/udinra_imgtags.php';
	include 'gallery/wpgallery/udinra_gallery.php';	

	if(get_option('udinra_image_sitemap_cat') == 1) {
		include 'web/udinra_sitemap_cat.php';
	}
		
	if(get_option('udinra_image_sitemap_tag') == 1) {
		include 'web/udinra_sitemap_tag.php';
	}
		
	if(get_option('udinra_image_sitemap_auth') == 1) {
		include 'web/udinra_sitemap_author.php';
	}				
	include 'exit/udinra_ping_image.php';
}

function udinra_image_sitemap_admin() {
	if (function_exists('add_options_page')) {
		add_options_page('Udinra Image Sitemap', 'Udinra Image Sitemap', 'manage_options', basename(__FILE__), 'Udinra_Image');
	}
}

function udinra_image_free_admin_style($hook) {
	if($hook == 'settings_page_udinra-all-image-sitemap') {
		wp_enqueue_style( 'udinra_image_free_pure_style', plugins_url('css/udstyle.css', __FILE__) );	
		wp_enqueue_script( 'udinra_sitemap_script', plugins_url('js/udscript.js', __FILE__),null,null,false );	
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
add_action( 'admin_enqueue_scripts', 'udinra_image_free_admin_style' );
add_filter( 'plugin_action_links', 'udinra_image_settings_plugin_link', 10, 2 );

?>
