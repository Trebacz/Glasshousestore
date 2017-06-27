<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class a3_License_Manager_Plugins
{

	public function __construct() {
		add_action( 'update-custom_upgrade-a3-plugin', array( $this, 'upgrade_a3_plugin' ) );
		add_filter( 'update_plugin_complete_actions', array( $this, 'replace_actions_url_after_updated_addon' ), 11, 2 );

		add_action( 'init', array( $this, 'process_actions' ), 0 );
	}

	public function process_actions() {
		global $a3_license_manager_tracking;
		if ( is_admin() && current_user_can( 'manage_options' ) && isset( $_POST['a3-activate-license-key'] ) ) {
			$addon_slug = $_POST['addon_slug'];
			$license_key = $_POST['license_key'];
			$a3_license_manager_tracking->process_confirm_pin( $addon_slug, $license_key );
		}

		if ( is_admin() && current_user_can( 'manage_options' ) && isset( $_GET['a3-action'] ) && 'remove-license' == $_GET['a3-action'] ) {
			$addon_slug = $_GET['plugin'];
			$a3_license_manager_tracking->process_remove_license( $addon_slug );
		}
	}

	private function get_installed_plugins() {
		global $a3_license_manager_tracking;

		$list_plugins   = $a3_license_manager_tracking->get_all_plugins_info();

		$installed_plugins = array();

		if ( is_array( $list_plugins ) && isset( $list_plugins['status'] ) && 'valid' == $list_plugins['status'] && isset( $list_plugins['plugins'] ) ) {
			foreach( $list_plugins['plugins'] as $addon_slug => $addon ) {
				if ( ! isset( $addon['slug'] ) ) $addon['slug'] = $addon_slug;

				if ( file_exists( WP_PLUGIN_DIR . '/' . $addon['slug'] ) || is_dir( WP_PLUGIN_DIR . '/' . $addon['slug'] ) ) {
					$installed_plugin = get_plugins('/' . $addon['slug']);
					$key = array_keys( $installed_plugin );
					$key = array_shift( $key ); 		//Use the first plugin regardless of the name, Could have issues for multiple-plugins in one directory if they share different version numbers
					$plugin_slug = $addon['slug'].'/'.$key;

					$is_actived = is_plugin_active( $plugin_slug );
					if ( $is_actived ) {
						$installed_plugins[$addon_slug] = array_merge( $installed_plugin[$key], $addon );
						$installed_plugins[$addon_slug]['plugin_path'] = $plugin_slug;

						if ( $a3_license_manager_tracking->check_license( $addon_slug ) ) {
							$installed_plugins[$addon_slug]['activated_license'] = 1;
							$license_info = $a3_license_manager_tracking->get_license_info( $addon_slug, $installed_plugin[$key]['Version'], true );
							$date_expired_time = strtotime( $license_info['date_expired'] );
							if ( $date_expired_time < time() ) {
								$installed_plugins[$addon_slug]['expired'] = 1;
							} else {
								$installed_plugins[$addon_slug]['expired'] = 0;
							}
							if ( $date_expired_time > strtotime( '2029-12-01 12:12:12' ) || $date_expired_time < strtotime( '2000-12-01 12:12:12' ) ) {
								$date_expired = __( 'Life Time', 'a3-license-manager' );
								$installed_plugins[$addon_slug]['expired'] = 0;
							} else {
								$date_expired = date( 'Y/m/d', strtotime( '-3 days', $date_expired_time ) );
							}
							$installed_plugins[$addon_slug]['expiry_date'] = $date_expired;
						} else {
							$installed_plugins[$addon_slug]['activated_license'] = 0;
						}

						if ( version_compare( $installed_plugin[$key]['Version'], $addon['new_version'], '<' ) ) {
							$installed_plugins[$addon_slug]['need_to_update'] = 1;
						} else {
							$installed_plugins[$addon_slug]['need_to_update'] = 0;
						}
					}
				}
			}
		}

		return $installed_plugins;
	}

	public function admin_screen() {
		global $wp_version;
		global $a3_license_manager_tracking;

		$a3rev_url = 'http://a3rev.com';

		$have_new_version = false;
		$current_update_plugins = get_site_transient( 'update_plugins' );
		$installed_plugins = $this->get_installed_plugins();

		add_thickbox();

		if ( isset( $_POST['a3-activate-license-key'] ) ) {
			$a3_license_manager_message = get_option( 'a3_license_manager_message', '' );
			if ( 1 == get_option( 'a3_license_activated_plugin_sucessful', 0 ) ) {
				echo '<div class="updated below-h2" style="display: block !important;"><p>' . $a3_license_manager_message . '</p></div>';
			} else {
				echo '<div class="error below-h2" style="display: block !important;"><p>' . $a3_license_manager_message . '</p></div>';
			}
			delete_option( 'a3_license_activated_plugin_sucessful' );
		}
	?>
	<div id="col-container" class="about-wrap">
		<div>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th class="manage-column column-plugin_name column-primary" id="plugin_name" scope="col"><?php echo __( 'Plugin', 'a3-license-manager' ); ?></th>
						<th class="manage-column column-plugin_version" id="plugin_version" scope="col"><?php echo __( 'Version', 'a3-license-manager' ); ?></th>
						<th class="manage-column column-plugin_expiry" id="plugin_expiry" scope="col"><?php echo __( 'Renews On', 'a3-license-manager' ); ?></th>
						<th class="manage-column column-plugin_status" id="plugin_status" scope="col"><?php echo __( 'License Key', 'a3-license-manager' ); ?></th>
					</tr>
				</thead>

				<tbody id="the-list">
				<?php
				if ( is_array( $installed_plugins ) && count( $installed_plugins ) > 0 ) {
					foreach ( $installed_plugins as $addon_slug => $installed_plugin ) {
						$plugin_path = $installed_plugin['plugin_path'];
				?>
					<tr>
						<td><?php echo $installed_plugin['name']; ?></td>
						<td>
							<?php echo __( 'Your Version', 'a3-license-manager' ); ?>: <?php echo $installed_plugin['Version']; ?>
							<?php if ( 1 == $installed_plugin['need_to_update'] ) { ?>
							<br />
							<?php echo __( 'New Version', 'a3-license-manager' ); ?>: <?php echo $installed_plugin['new_version']; ?>
							-
							<a class="thickbox" href="<?php echo $installed_plugin['log']; ?>?TB_iframe=true&width=600&height=550" title="<?php echo $installed_plugin['name']; ?> v<?php echo $installed_plugin['new_version']; ?>"><?php echo __( 'Changelog', 'a3-license-manager' ); ?></a>
							<?php } ?>
						</td>
						<td>
						<?php if ( 1 == $installed_plugin['activated_license'] && isset( $installed_plugin['expiry_date'] ) ) {
							if ( 1 == $installed_plugin['expired'] ) {
								echo '<span class="expired-status">' . $installed_plugin['expiry_date'] . '</span>';
								echo '<br /><span class="expired-status">' . __( 'Expired', 'a3-license-manager' ) . '</span>';
							} else {
								echo $installed_plugin['expiry_date'];
							}
						} ?>
						</td>
						<td>
						<?php if ( 1 == $installed_plugin['activated_license'] ) { ?>
							<?php
								$remove_url = add_query_arg( array(
									'a3-action'	=> 'remove-license',
									'plugin'	=> $addon_slug,
								), self_admin_url( 'index.php?page=a3-license-manager' ) );
							?>
							<a href="<?php echo $remove_url; ?>" class="button remove-license"><?php echo __( 'Remove License', 'a3-license-manager' ); ?></a>
							<?php if ( 1 == $installed_plugin['need_to_update'] && 0 == $installed_plugin['expired'] ) { ?>
							<?php
								if ( isset( $current_update_plugins->response ) ) {
									$download_url = $a3_license_manager_tracking->get_download_url( $addon_slug, $installed_plugin['Version'] );

									$current_update_plugins->response[ $plugin_path ] = new stdClass();
									$current_update_plugins->response[ $plugin_path ]->url = $a3rev_url;
									$current_update_plugins->response[ $plugin_path ]->slug = $addon_slug;
									$current_update_plugins->response[ $plugin_path ]->package = $download_url;
									$current_update_plugins->response[ $plugin_path ]->new_version = $installed_plugin['new_version'];
									$current_update_plugins->response[ $plugin_path ]->upgrade_notice = $installed_plugin['upgrade_notice'];
									$current_update_plugins->response[ $plugin_path ]->id = "0";

									$have_new_version = true;
								}

								$update_url = add_query_arg( array(
									'action' 		=> 'upgrade-a3-plugin',
									'plugin'		=> $plugin_path,
									'a3-plugin'	=> 1,
								), self_admin_url( 'update.php' ) );
								$update_url = esc_url( wp_nonce_url( $update_url, 'upgrade-a3-plugin_' . $plugin_path ) ) ;
							?>
							<a href="<?php echo $update_url; ?>" class="update-now button"><?php echo __( 'Update Now', 'a3-license-manager' ); ?></a>
							<?php } elseif ( 1 == $installed_plugin['need_to_update'] ) { ?>
							<a href="https://a3rev.com/my-account/" target="_blank" class="renew-button button"><?php echo __( 'Renew License', 'a3-license-manager' ); ?></a>
							<?php } ?>
						<?php } else { ?>
						<form method="post" action="<?php echo self_admin_url('index.php?page=a3-license-manager'); ?>" class="validate">
							<input type="hidden" name="addon_slug" value="<?php echo $addon_slug; ?>" />
							<input type="text" value="" name="license_key" /> <input class="button button-primary" type="submit" name="a3-activate-license-key" value="<?php echo __( 'Activate License', 'a3-license-manager' ); ?>" />
						</form>
						<?php } ?>
						</td>
					</tr>
				<?php
					}
				} else {
				?>
					<tr class="no-items">
						<td colspan="4" class="colspanchange">
							<p><?php echo __( 'No plugins found.', 'a3-license-manager' ); ?></p>
						</td>
					</tr>
				<?php } ?>
				</tbody>

				<tfoot>
					<tr>
						<th class="manage-column column-plugin_name column-primary" id="plugin_name" scope="col"><?php echo __( 'Plugin', 'a3-license-manager' ); ?></th>
						<th class="manage-column column-plugin_version" id="plugin_version" scope="col"><?php echo __( 'Version', 'a3-license-manager' ); ?></th>
						<th class="manage-column column-plugin_expiry" id="plugin_expiry" scope="col"><?php echo __( 'Renews On', 'a3-license-manager' ); ?></th>
						<th class="manage-column column-plugin_status" id="plugin_status" scope="col"><?php echo __( 'License Key', 'a3-license-manager' ); ?></th>
					</tr>
				</tfoot>

			</table>
		</div>
	</div>
	<?php
		if ( $have_new_version ) {
			set_site_transient( 'update_plugins', $current_update_plugins );
		}
	}

	public function upgrade_a3_plugin() {
		$plugin = isset($_REQUEST['plugin']) ? trim($_REQUEST['plugin']) : '';
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

		if ( ! current_user_can('update_plugins') )
			wp_die(__('You do not have sufficient permissions to update plugins for this site.', 'a3-license-manager'));

		check_admin_referer('upgrade-a3-plugin_' . $plugin);

		$title = __('Update Plugin', 'a3-license-manager');
		$parent_file = 'plugins.php';
		$submenu_file = 'plugins.php';
		load_template(ABSPATH . 'wp-admin/admin-header.php');

		$nonce = 'upgrade-plugin_' . $plugin;
		$url = 'update.php?action=upgrade-plugin&plugin=' . urlencode( $plugin ).'&a3-plugin=1';

		$upgrader = new Plugin_Upgrader( new Plugin_Upgrader_Skin( compact('title', 'nonce', 'url', 'plugin') ) );
		$upgrader->upgrade($plugin);

		load_template(ABSPATH . 'wp-admin/admin-footer.php', false);
	}

	public function replace_actions_url_after_updated_addon( $install_actions, $plugin ) {
		$have_replace_actions_url = false;

		if ( isset( $_GET['a3-plugin'] ) || ( isset( $_GET['action'] ) && $_GET['action'] == 'upgrade-a3-plugin' ) ) {
			$have_replace_actions_url = true;
		}

		if ( $have_replace_actions_url ) {
			if ( ! is_array( $install_actions ) ) $install_actions = array();
			$install_actions['plugins_page'] = '<a href="' . self_admin_url('index.php?page=a3-license-manager') . '" title="' . esc_attr__( 'Return to a3 License Manager', 'a3-license-manager' ) . '" target="_parent">' . __('Return to a3 License Manager', 'a3-license-manager' ) . '</a>';
		}

		return $install_actions;
	}
}

global $a3_license_manager_plugins;
$a3_license_manager_plugins = new a3_License_Manager_Plugins();

?>