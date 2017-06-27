<?php
/**
 * Register Deactivation Hook
 */
function a3_license_manager_deactivate(){
	delete_transient( 'a3_license_manager_update_info' );
	delete_transient( 'a3_license_manager_plugins_info' );
}

/**
 * Load languages file
 */
function a3_license_manager_load_plugin_textdomain() {
	if ( get_option( 'a3_license_manager_installed' ) ) {
		delete_option( 'a3_license_manager_installed' );

		if ( ! function_exists( 'responsi_premium_pack_check_pin' ) || ! responsi_premium_pack_check_pin() ) {
			wp_redirect( admin_url( 'index.php?page=a3-license-manager', 'relative' ) );
			exit();
		}
	}
	$locale = apply_filters( 'plugin_locale', get_locale(), 'a3-license-manager' );

	load_textdomain( 'a3-license-manager', WP_LANG_DIR . '/a3-license-manager/a3-license-manager-' . $locale . '.mo' );
	load_plugin_textdomain( 'a3-license-manager', false, A3_LICENSE_MANAGER_FOLDER . '/languages/' );
}

// Add language
add_action( 'init', 'a3_license_manager_load_plugin_textdomain' );

// Check upgrade functions
add_action( 'init', 'a3_license_manager_upgrade_version' );
function a3_license_manager_upgrade_version () {

	update_option( 'a3_license_manager_version', '1.0.2' );
}

// Manually clear version cached
add_action('init', 'a3_license_manager_manual_clear_version_cached' );
function a3_license_manager_manual_clear_version_cached() {
	if ( isset( $_POST['a3_license_manager_clear_version_cached'] ) && 1 == $_POST['a3_license_manager_clear_version_cached'] ) {
		delete_transient( 'a3_license_manager_update_info' );
		delete_transient( 'a3_license_manager_plugins_info' );
	}
}

?>