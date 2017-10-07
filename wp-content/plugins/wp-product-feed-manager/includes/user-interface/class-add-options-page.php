<?php

/* * ******************************************************************
 * Version 1.3
 * Modified: 25-05-2017
 * Copyright 2017 Accentio. All rights reserved.
 * License: None
 * By: Michel Jongbloed
 * ****************************************************************** */

// Prevent direct access
if ( !defined( 'ABSPATH' ) ) {
	echo 'Hi!  I\'m just a plugin, there\'s not much I can do when called directly.';
	exit;
}

if ( !class_exists( 'WPPFM_Add_Options_Page' ) ) :

	class WPPFM_Add_Options_Page extends WPPFM_Admin_Page {

		private $_options_form;
		// @private storage of scripts version
		private $_version_stamp;
		// @private register minified scripts
		private $_js_min;

		public function __construct() {

			parent::__construct();

			$premium_version_nr		 = EDD_SL_ITEM_NAME === 'WP Product Feed Manager' ? 'fr-' : 'pr-'; // prefix for version stamp depending on premium or free version
			$this->_version_stamp	 = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : $premium_version_nr . MYPLUGIN_VERSION_NUM;
			$this->_js_min			 = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

			$this->prepare_options_form();
		}

		private function prepare_options_form() {
			$this->_options_form = new WPPFM_Options_Form ();
			$this->includes();
		}

		public function show() {
			echo $this->options_page_header();

			echo $this->message_field();

			if ( 'valid' === wppfm_validate() ) { // ref HWOTBERH

				echo $this->options_page_body();
			} else {
				
				wp_redirect( admin_url( '/admin.php?page=wp-product-feed-manager' ) );
			}
			
			echo $this->options_page_footer();
		}

		private function options_page_header() {
			return 
			'
		<div class="wrap">
		<div class="feed-spinner" id="feed-spinner" style="display:none;">
			<img id="img-spinner" src="' . $this->spinner_gif . '" alt="Loading" />
		</div>
		<div class="main-wrapper header-wrapper" id="header-wrapper">
		<div class="header-text"><h1>' . __( 'Feed Manager Settings', 'wp-product-feed-manager' ) . '</h1></div>
		<div class="logo"></div>
		</div>
		';
		}
		
		private function options_page_body() { $this->_options_form->display(); }
		
		private function options_page_footer() { }
		
		private function includes() {
			wp_register_style( 'wp-product-feed-manager-setting', esc_url( MYPLUGIN_PLUGIN_URL . '/css/wppfm_setting-page' . $this->_js_min . '.css' ),
				'', $this->_version_stamp, 'screen' );
			wp_enqueue_style( 'wp-product-feed-manager-setting' );

			wp_localize_script( 'wppfm_data-handling-script', 'MyAjax', array(
				// URL to wp-admin/admin-ajax.php to process the request
				'ajaxurl'				=> admin_url( 'admin-ajax.php' ),

				// generate the required nonces
				'setFTPModeNonce'				=> wp_create_nonce( 'myajax-ftp-mode-nonce' ),
				'setAutoFeedFixNonce'			=> wp_create_nonce( 'myajax-auto-feed-fix-nonce' ),
				'setThirdPartyKeywordsNonce'	=> wp_create_nonce( 'myajax-set-third-party-keywords-nonce' ),
				'backupNonce'					=> wp_create_nonce( 'myajax-backup-nonce' ),
				'deleteBackupNonce'				=> wp_create_nonce( 'myajax-delete-backup-nonce' ),
				'restoreBackupNonce'			=> wp_create_nonce( 'myajax-restore-backup-nonce' ),
				'duplicateBackupNonce'			=> wp_create_nonce( 'myajax-duplicate-backup-nonce' ),
				'postBackupListNonce'			=> wp_create_nonce( 'myajax-backups-list-nonce' ),
				'postSetupOptionsNonce'			=> wp_create_nonce( 'myajax-setting-options-nonce' ),
				'setReInitiateNonce'			=> wp_create_nonce( 'myajax-reinitiate-nonce' )
			));
			
			wp_enqueue_script( 'wppfm_data-handling-script', esc_url( MYPLUGIN_PLUGIN_URL . '/includes/data/js/wppfm_ajaxdatahandling' . $this->_js_min . '.js' ), array( 'jquery' ), $this->_version_stamp, true );
			wp_enqueue_script( 'wppfm_setting-form-script', esc_url( MYPLUGIN_PLUGIN_URL . '/includes/user-interface/js/wppfm_setting-form' . $this->_js_min . '.js' ), array( 'jquery' ), $this->_version_stamp, true );
			wp_enqueue_script( 'wppfm_event-listener-script', esc_url( MYPLUGIN_PLUGIN_URL . '/includes/user-interface/js/wppfm_feed-form-events' . $this->_js_min . '.js' ), array( 'jquery' ), $this->_version_stamp, true );
			wp_enqueue_script( 'wppfm_form-support-script', esc_url( MYPLUGIN_PLUGIN_URL . '/includes/user-interface/js/wppfm_support' . $this->_js_min . '.js' ), array( 'jquery' ), $this->_version_stamp, true );
			wp_enqueue_script( 'wppfm_backup-list-script', esc_url( MYPLUGIN_PLUGIN_URL . '/includes/user-interface/js/wppfm_backup-list' . $this->_js_min . '.js' ), array( 'jquery' ), $this->_version_stamp, true );
		}
	}	

     // end of WPPFM_Add_Options_Page class

endif;