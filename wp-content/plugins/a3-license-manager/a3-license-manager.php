<?php
/*
Plugin Name: a3 License Manager
Description: Install a3 License Manager to activate auto maintenance updates and feature upgrades for all of your Licensed a3rev Premium Plugins.
Version: 1.0.2
Author: A3 Revolution
Author URI: http://www.a3rev.com/
Text Domain: a3-license-manager
Domain Path: /languages
License: This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007

	Copyright © 2011 a3THEMES

	a3THEMES
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'A3_LICENSE_MANAGER_PATH', dirname(__FILE__) );
define( 'A3_LICENSE_MANAGER_FOLDER', dirname( plugin_basename( __FILE__ ) ) );
define( 'A3_LICENSE_MANAGER_NAME', plugin_basename( __FILE__ ) );
define( 'A3_LICENSE_MANAGER_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'A3_LICENSE_MANAGER_IMAGES_URL', A3_LICENSE_MANAGER_URL . '/assets/images' );
define( 'A3_LICENSE_MANAGER_JS_URL', A3_LICENSE_MANAGER_URL . '/assets/js' );
define( 'A3_LICENSE_MANAGER_CSS_URL', A3_LICENSE_MANAGER_URL . '/assets/css' );
define( 'A3_LICENSE_MANAGER_FRAMEWORK_URL', A3_LICENSE_MANAGER_URL . '/includes' );

if ( ! defined( 'A3_LICENSE_MANAGER_API' ) )
	define( 'A3_LICENSE_MANAGER_API', 'http://a3api.com/a3_license_manager' );

register_activation_hook( __FILE__, 'a3_license_manager_activate' );
register_deactivation_hook( __FILE__, 'a3_license_manager_deactivate' );

/**
 * Register Activation Hook
 */
function a3_license_manager_activate() {
	update_option( 'a3_license_manager_version', '1.0.2' );

	update_option( 'a3_license_manager_installed', true );
}

update_option( 'a3_license_manager_plugin', 'a3_license_manager' );

include( 'includes/plugin-tracking.php' );
include( 'upgrade/plugin_upgrade.php' );

require_once( 'admin/plugin-init.php' );
require_once( 'admin/plugin-page.php' );
require_once( 'includes/plugins-manager.php' );

?>