<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class a3_License_Manager_Page
{

	public function __construct() {
		$menu_hook = is_multisite() ? 'network_admin_menu' : 'admin_menu';
		add_action( $menu_hook, array( $this, 'register_settings_screen' ) );
	}

	public function register_settings_screen() {
		// Don't show admin page if Responsi Premium Pack is acttivated
		if ( function_exists( 'responsi_premium_pack_check_pin' ) && responsi_premium_pack_check_pin() ) return;

		$page_name = add_dashboard_page( __( 'a3 License Manager', 'a3-license-manager' ), __( 'a3 License Manager', 'a3-license-manager' ), 'manage_options', 'a3-license-manager', array( $this, 'settings_screen' ) );

		add_action( 'admin_print_scripts-' . $page_name, array( $this, 'enqueue_styles' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'a3-license-manager-admin', A3_LICENSE_MANAGER_CSS_URL . '/admin.css', array(), '1.0.0' );
	}

	public function current_screen() {
		$screen = 'license';
		if ( isset( $_GET['screen'] ) ) {
			$screen = $_GET['screen'];
		}

		return $screen;
	}

	public function settings_screen() {
		$screen = $this->current_screen();
		?>
		<div class="wrap about-wrap a3-license-manager-wrap">
			<h1><?php echo __( 'a3 License Manager', 'a3-license-manager' ); ?></h1>

			<div class="about-text a3-license-manager-about-text">
				<?php
					echo __( 'This is your one-stop-spot for activating your subscriptions.', 'a3-license-manager' );
				?>
			</div>
			<div class="short-description a3-license-manager-short-description">
			</div>

			<div class="wp-filter">
				<ul class="filter-links">
					<?php
						$license_screen_page = add_query_arg( array(
							'screen' => 'license',
							), admin_url( 'index.php?page=a3-license-manager' ) );

						$help_screen_page = add_query_arg( array(
							'screen' => 'help',
							), admin_url( 'index.php?page=a3-license-manager' ) );
					?>
					<li>
						<a class="<?php if ( 'help' != $screen ) echo 'current'; ?>" href="<?php echo esc_url( $license_screen_page ); ?>"><?php echo __( 'Subscriptions', 'a3-license-manager' ); ?></a>
					</li>
					<!--
					<li>
						<a class="<?php if ( 'help' == $screen ) echo 'current'; ?>" href="<?php echo esc_url( $help_screen_page ); ?>"><?php echo __( 'Help', 'a3-license-manager' ); ?></a>
					</li>
					-->
				</ul>
			</div>

			<br class="clear">
		<?php

		switch ( $screen ) {
			case 'help':
				require_once( A3_LICENSE_MANAGER_PATH . '/includes/screen-help.php' );
			break;

			case 'license':
			default:
				global $a3_license_manager_plugins;
				$a3_license_manager_plugins->admin_screen();
			break;
		}
		?>
		</div>
		<?php
	}

}

new a3_License_Manager_Page();

?>