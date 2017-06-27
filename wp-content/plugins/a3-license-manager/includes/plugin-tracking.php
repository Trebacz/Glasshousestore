<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class a3_License_Manager_Tracking
{

	public $plugins_transient = 'a3_license_manager_plugins_info';

	public function __construct() {
	}

	public function process_confirm_pin( $plugin_slug, $license_key ) {
		return $this->confirm_pin( $plugin_slug, $license_key );
	}

	public function check_license( $plugin_slug ) {
		return $this->check_pin( $plugin_slug );
	}

	public function process_remove_license( $plugin_slug ){
		return $this->remove_activated_license( $plugin_slug );
	}

	public function get_download_url( $plugin_slug, $version ) {
		$license_key = get_option( 'a3_' . $plugin_slug . '_license' );
		$download_url = A3_LICENSE_MANAGER_API . "/download.php?plugin=".$plugin_slug."&key=".$license_key."&domain_name=".$_SERVER['SERVER_NAME']."&address_ip=" . $_SERVER['SERVER_ADDR']."&v=".$version."&owner=".base64_encode(get_bloginfo('admin_email'));

		return $download_url;
	}

	private function confirm_pin( $plugin_slug, $license_key ) {

		/**
		* Check pin for confirm plugin
		*/
		$respone_api = __('Connection Error! Could not reach the a3API on Amazon Cloud, the network may be busy. Please try again in a few minutes.', 'a3-license-manager');
		$license_key = md5( $license_key );
		$options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 20,
			'sslverify'	=> false,
			'body' 		=> array(
				'act'			=> 'activate',
				'ssl'			=> $license_key,
				'plugin' 		=> $plugin_slug,
				'domain_name'	=> $_SERVER['SERVER_NAME'],
				'address_ip'	=> $_SERVER['SERVER_ADDR'],
			)
		);
		$raw_response = wp_remote_request( A3_LICENSE_MANAGER_API . '/index.php' , $options);
		if ( !is_wp_error( $raw_response ) && $raw_response['response']['code'] >= 200 && $raw_response['response']['code'] < 300) {
			$respone_api = $raw_response['body'];
		} elseif ( is_wp_error( $raw_response ) ) {
			$respone_api = __('Error: ', 'a3-license-manager').' '.$raw_response->get_error_message();
		}

		if ( $respone_api == md5( 'valid' ) ) {
			update_option( 'a3_' . $plugin_slug . '_pin', sha1(md5('a3rev.com_'.str_replace( array( 'www.', 'http://', 'https://' ), '', get_option('siteurl') ).'_'.$plugin_slug)));
			update_option( 'a3_' . $plugin_slug . '_license', $license_key );
			update_option( 'a3_license_manager_message', __('Thank you. Your License Key is valid.', 'a3-license-manager') );
			update_option( 'a3_license_activated_plugin_sucessful', 1 );
		} else {
			delete_option( 'a3_' . $plugin_slug . '_pin' );
			delete_option( 'a3_' . $plugin_slug . '_license' );
			update_option( 'a3_license_manager_message', $respone_api );
		}
		delete_transient( $plugin_slug . '_licinfo' );
	}

	private function check_pin( $plugin_slug ) {
		$domain_name = get_option('siteurl');
		$a3rev_auth_key = get_option( 'a3_' . $plugin_slug . '_license' );
		$a3rev_pin_key = get_option( 'a3_' . $plugin_slug . '_pin' );
		if (function_exists('is_multisite')){
			if (is_multisite()) {
				global $wpdb;
				$domain_name = $wpdb->get_var("SELECT option_value FROM ".$wpdb->options." WHERE option_name = 'siteurl'");
				if ( substr($domain_name, -1) == '/') {
					$domain_name = substr( $domain_name, 0 , -1 );
				}
			}
		}
		$nonwww_domain_name = str_replace( 'www.', '', $domain_name );
		$nonhttp_domain_name = str_replace( array( 'http://', 'https://' ), '', $nonwww_domain_name );
		$www_domain_name = str_replace( 'https://', 'https://www.', str_replace( 'http://', 'http://www.', $nonwww_domain_name ) );
		if ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$nonwww_domain_name.'_'.$plugin_slug))) return true;
		elseif ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$nonhttp_domain_name.'_'.$plugin_slug))) return true;
		elseif ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$www_domain_name.'_'.$plugin_slug))) return true;
		else return false;
	}

	private function remove_activated_license( $plugin_slug ) {
		$license_key = get_option( 'a3_' . $plugin_slug . '_license' );

		$options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 20,
			'body' 		=> array(
				'act'			=> 'deactivate',
				'ssl'			=> $license_key,
				'plugin' 		=> $plugin_slug,
				'domain_name'	=> $_SERVER['SERVER_NAME'],
				'address_ip'	=> $_SERVER['SERVER_ADDR'],
			)
		);
		$raw_response = wp_remote_request( A3_LICENSE_MANAGER_API . '/index.php' , $options);
		if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']) {
			$respone_api = $raw_response['body'];
		}

		delete_option( 'a3_' . $plugin_slug . '_license' );
		delete_option( 'a3_' . $plugin_slug . '_pin' );
		delete_transient( $plugin_slug . '_licinfo' );
	}

	public function get_all_plugins_info($cache=true){
		//Getting version number
		$respone_api = get_transient( $this->plugins_transient );

		if ( ! $cache ) {
            $respone_api = false;
		}

        // Fixed for work compatibility WP 4.3 when transient_timeout is deleted
        if ( false !== $respone_api ) {
			$transient_timeout = '_transient_timeout_' . $this->plugins_transient;
			$timeout = get_option( $transient_timeout, false );
			if ( false === $timeout ) {
				$respone_api = false;
			}
		}

		if ( ! $respone_api ){

			// set caching first before call to server to solve server timeout issue and make cron run again everytime
			set_transient( $this->plugins_transient, 'cannot_connect_api', 86400); //caching for 24 hours

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

			$raw_response = wp_remote_request(A3_LICENSE_MANAGER_API. "/plugins.php", $options);
			if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']){
				$respone_api = $raw_response['body'];
				$respone_api = (array) maybe_unserialize($respone_api);

				//caching responses.
            	set_transient( $this->plugins_transient, $respone_api, 86400); //caching for 24 hours
			} else {
				$respone_api = 'cannot_connect_api';
				//caching responses.
            	set_transient( $this->plugins_transient, $respone_api, 7200); //caching for 2 hours
			}
		}

		return $respone_api;
    }

    public function get_license_info( $plugin_slug, $current_version, $cache=true ) {

    	$transient_name = $plugin_slug . '_licinfo';
    	$license_key = get_option( 'a3_' . $plugin_slug . '_license' );

		//Getting version number
		$respone_api = get_transient( $transient_name );

		if ( ! $cache ) {
            $respone_api = false;
		}

        // Fixed for work compatibility WP 4.3 when transient_timeout is deleted
        if ( false !== $respone_api ) {
			$transient_timeout = '_transient_timeout_' . $transient_name;
			$timeout = get_option( $transient_timeout, false );
			if ( false === $timeout ) {
				$respone_api = false;
			}
		}

		if ( ! $respone_api ) {

				// set caching first before call to server to solve server timeout issue and make cron run again everytime
				set_transient( $transient_name, 'cannot_connect_api', 86400); //caching for 24 hours

				$options = array(
					'method' 	=> 'POST',
					'timeout' 	=> 8,
					'body' 		=> array(
									'plugin' 		=> $plugin_slug,
									'key'			=> $license_key,
									'domain_name'	=> $_SERVER['SERVER_NAME'],
									'address_ip'	=> $_SERVER['SERVER_ADDR'],
									'v'				=> $current_version,
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
			set_transient( $transient_name, $respone_api, 86400); //caching for 24 hours
		}

		$version_info = explode('||', $respone_api);
		if ( FALSE !== stristr( $respone_api, '||' ) && is_array( $version_info ) ) {
			$info = array("is_valid_key" => $version_info[1], "version" => $version_info[0] , "upgrade_notice" => $version_info[2]);
			$info['url'] = $this->get_download_url( $plugin_slug, $current_version );
			if ( isset( $version_info[3] ) && strlen( trim( $version_info[3] ) ) ) $info['date_registered'] = trim( $version_info[3] );
			if ( isset( $version_info[4] ) && strlen( trim( $version_info[4] ) ) ) $info['date_expired'] = trim( $version_info[4] );
			if ( isset( $version_info[5] ) ) $info['number_licenses'] = $version_info[5];
			if ( isset( $version_info[6] ) ) $info['order_id'] = $version_info[6];
			return $info;
		} else {
			set_transient( $transient_name, $respone_api, 7200); //change caching for 2 hours
			return '';
		}
    }
}

global $a3_license_manager_tracking;
$a3_license_manager_tracking = new a3_License_Manager_Tracking();

?>