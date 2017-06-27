<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class a3_License_Manager_Upgrade
{
	public $version_transient = 'a3_license_manager_update_info';

	public function __construct() {
		// Show Notice on top of Dashboard when have new version of Responsi Premium Pack plugin
		add_action( 'admin_notices', array( $this, 'new_version_notice' ), 8 );

		add_action( 'install_plugins_pre_plugin-information', array( $this, 'display_changelog' ) );

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		add_filter( 'plugins_api_result', array( $this, 'make_compatibility' ), 11, 3);

		add_filter( 'http_request_args', array( $this, 'disable_ssl_verify' ), 100, 2 );

		// Defined this plugin as external so that WordPress don't call to the WordPress.org Plugin Install API
		add_filter( 'plugins_api', array( $this, 'is_external' ), 11, 3 );

	}

	public function new_version_notice() {
		$new_version = $this->get_version_info();

		if ( is_array( $new_version ) && isset( $new_version['version'] ) && version_compare( get_option('a3_license_manager_version'), $new_version['version'], '<' ) ) {
			$update_url = add_query_arg( array(
				'action' 		=> 'upgrade-plugin',
				'plugin'  		=> A3_LICENSE_MANAGER_NAME,
			), self_admin_url( 'update.php' ) );
			$update_url = esc_url( wp_nonce_url( $update_url, 'upgrade-plugin_' . A3_LICENSE_MANAGER_NAME ) );

			add_thickbox();
			$changelog = get_option( 'a3_license_manager_changelog', '' );
		?>
    		<div class="error below-h2" style="display:block !important; margin-left:2px;">
    			<p><?php echo sprintf( __( "a3 License Manager %s is available. <a title='a3 License Manager' class='thickbox' href='#TB_inline?width=640&amp;height=346&inlineId=a3-license-manager-changelog'>See what's new</a> or <a href='%s' target='_parent'>update now.</a>" , 'a3-license-manager' ), $new_version['version'], $update_url ); ?></p>
    			<div style="display: none;"><div id="a3-license-manager-changelog"><div><?php echo $changelog; ?></div></div></div>
    		</div>
    	<?php
		}
	}

	public function get_version_info( $cache=true ) {

		//Getting version number
		$respone_api = get_transient( $this->version_transient );

		if ( ! $cache ) {
            $respone_api = false;
		}

        // Fixed for work compatibility WP 4.3 when transient_timeout is deleted
        if ( false !== $respone_api ) {
			$transient_timeout = '_transient_timeout_' . $this->version_transient;
			$timeout = get_option( $transient_timeout, false );
			if ( false === $timeout ) {
				$respone_api = false;
			}
		}

		if ( ! $respone_api ) {

				// set caching first before call to server to solve server timeout issue and make cron run again everytime
				set_transient( $this->version_transient, 'cannot_connect_api', 86400); //caching for 24 hours

				$options = array(
					'method' 	=> 'POST',
					'timeout' 	=> 8,
					'body' 		=> array(
									'plugin' 		=> get_option('a3_license_manager_plugin'),
									'domain_name'	=> $_SERVER['SERVER_NAME'],
									'address_ip'	=> $_SERVER['SERVER_ADDR'],
									'v'				=> get_option('a3_license_manager_version'),
									'owner'			=> base64_encode(get_bloginfo('admin_email'))
								)
				);

				$raw_response = wp_remote_request(A3_LICENSE_MANAGER_API. "/version.php", $options);
				if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']){
					$respone_api = $raw_response['body'];
				} else {
					$respone_api = 'cannot_connect_api';
				}

			//caching responses.
			set_transient( $this->version_transient, $respone_api, 86400); //caching for 24 hours
		}

		$version_info = explode('||', $respone_api);
		if ( FALSE !== stristr( $respone_api, '||' ) && is_array( $version_info ) ) {
			$info = array("is_valid_key" => $version_info[1], "version" => $version_info[0] , "upgrade_notice" => $version_info[2]);
			return $info;
		} else {
			set_transient( $this->version_transient, $respone_api, 7200); //change caching for 2 hours
			return '';
		}
    }

	public function check_update($update_plugins_option){
        $new_version = $this->get_version_info();
        if (!is_array($new_version))
            return $update_plugins_option;

        $plugin_name = A3_LICENSE_MANAGER_NAME;
        if(empty($update_plugins_option->response[$plugin_name]))
            $update_plugins_option->response[$plugin_name] = new stdClass();

        //Empty response means that the key is invalid. Do not queue for upgrade
        if ( version_compare(get_option('a3_license_manager_version'), $new_version['version'], '<' ) ) {
			$update_plugins_option->response[$plugin_name]->url            = "http://www.a3rev.com";
			$update_plugins_option->response[$plugin_name]->slug           = get_option('a3_license_manager_plugin');
			$update_plugins_option->response[$plugin_name]->package        = $this->get_url_download();
			$update_plugins_option->response[$plugin_name]->new_version    = $new_version['version'];
			$update_plugins_option->response[$plugin_name]->upgrade_notice = $new_version['upgrade_notice'];
			$update_plugins_option->response[$plugin_name]->id             = "0";

			$page_text = $this->get_changelog();
        	update_option( 'a3_license_manager_changelog', $page_text );
        } else {
        	unset($update_plugins_option->response[$plugin_name]);
        }

        return $update_plugins_option;

    }

	//Displays current version details on Plugin's page
   	public function display_changelog(){
        if( $_REQUEST["plugin"] != get_option('a3_license_manager_plugin') )
            return;

        $changelog = get_option( 'a3_license_manager_changelog', '' );
        echo $changelog;

        exit();
    }

    private function get_changelog(){
		$options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 8,
			'body' 		=> array(
							'plugin' 		=> get_option('a3_license_manager_plugin'),
							'domain_name'	=> $_SERVER['SERVER_NAME'],
							'address_ip'	=> $_SERVER['SERVER_ADDR'],
						)
				);

        $raw_response = wp_remote_request(A3_LICENSE_MANAGER_API . "/changelog.php", $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            $page_text = __('Error: ', 'a3-license-manager' ).' '.$raw_response->get_error_message();
        }else{
            $page_text = $raw_response['body'];
        }
        return stripslashes($page_text);
    }

	public function get_url_download(){
        $download_url = A3_LICENSE_MANAGER_API . "/download.php?plugin=".get_option('a3_license_manager_plugin')."&domain_name=".$_SERVER['SERVER_NAME']."&address_ip=" . $_SERVER['SERVER_ADDR']."&v=".get_option('a3_license_manager_version')."&owner=".base64_encode(get_bloginfo('admin_email'));

        return $download_url;
	}

	public function make_compatibility( $info, $action, $args ) {
		global $wp_version;
		$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		$our_plugin_name = get_option('a3_license_manager_plugin');
		if ( $action == 'plugin_information' ) {
			if ( version_compare( $wp_version, '3.7', '<=' ) ) {
				if ( is_object( $args ) && isset( $args->slug ) && $args->slug == $our_plugin_name ) {
					$info->tested = $wp_version;
				}
			} elseif ( version_compare( $wp_version, '3.7', '>' ) && is_array( $args ) && isset( $args['body']['request'] ) ) {
				$request = unserialize( $args['body']['request'] );
				if ( $request->slug == $our_plugin_name ) {
					$info->tested = $wp_version;
				}
			}
		}
		return $info;
	}

	public function disable_ssl_verify($args, $url) {
		if ( stristr($url, A3_LICENSE_MANAGER_API . "/download.php" ) !== false ) {
			$args['timeout'] = 60;
			$args['sslverify'] = false;
		} elseif ( stristr($url, A3_LICENSE_MANAGER_API) !== false ) {
			$args['timeout'] = 8;
			$args['sslverify'] = false;
		}

		return $args;
	}

	public function is_external( $external, $action, $args ) {
		if ( 'plugin_information' == $action ) {
			if ( is_object( $args ) && isset( $args->slug ) &&  get_option('a3_license_manager_plugin') == $args->slug ) {
				global $wp_version;
				$external = array(
					'tested'  => $wp_version
				);
				$external = (object) $external;
			}
		}
		return $external;
	}
}

global $a3_license_manager_upgrade;
$a3_license_manager_upgrade = new a3_License_Manager_Upgrade();

?>