<?php
/*
 Plugin Name: N-Media WooCommerce Personalized Product Meta Manager
Plugin URI: http://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/
Description: This plugin allow WooCommerce Store Admin to create unlimited input fields and files to attach with Product Page
Version: 7.7
Author: Najeeb Ahmad
Text Domain: nm-personalizedproduct
Author URI: http://www.najeebmedia.com/
*/

// @since 6.1
if( ! defined('ABSPATH' ) ){
	exit;
}

//Auto Udate Checker
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    // 'http://wordpresspoets.com/wp-update-server/?action=get_metadata&slug=nm-woocommerce-personalized-product',
    'http://www.wordpresspoets.com/release-json/ppom.json',
    __FILE__
);

/*
 * Lets start from here
*/

/*
 * loading plugin config file
 */
$_config = dirname(__FILE__).'/config.php';
if( file_exists($_config))
	include_once($_config);
else
	die('Reen, Reen, BUMP! not found '.$_config);


/* ======= the plugin main class =========== */
$_plugin = dirname(__FILE__).'/classes/plugin.class.php';
if( file_exists($_plugin))
	include_once($_plugin);
else
	die('Reen, Reen, BUMP! not found '.$_plugin);

/*
 * [1]
 */

$nmpersonalizedproduct = NM_PersonalizedProduct::get_instance();
NM_PersonalizedProduct::init();
//nm_personalizedproduct_pa($nmpersonalizedproduct);

if( is_admin() ){

	$_admin = dirname(__FILE__).'/classes/admin.class.php';
	if( file_exists($_admin))
		include_once($_admin );
	else
		die('file not found! '.$_admin);

	$nmpersonalizedproduct_admin = new NM_PersonalizedProduct_Admin();
}


/*
 * activation/install the plugin data
*/
register_activation_hook( __FILE__, array('NM_PersonalizedProduct', 'activate_plugin'));
register_deactivation_hook( __FILE__, array('NM_PersonalizedProduct', 'deactivate_plugin'));

// plugin settings link
if( is_admin() ) {
	
	$plugin_basename = plugin_basename( __FILE__ );
	add_filter( "plugin_action_links_$plugin_basename", 'ppom_settings_link');
}
function ppom_settings_link($links) {
		
	$settings_link = '<a href="'.admin_url( 'options-general.php?page=nm-personalizedproduct').'">Add Fields</a>';
  	array_push( $links, $settings_link );
  	return $links;
}