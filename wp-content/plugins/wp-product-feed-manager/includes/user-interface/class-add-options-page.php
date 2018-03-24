<?php

/* * ******************************************************************
 * Version 1.4
 * Modified: 15-10-2017
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
			$this->_version_stamp	 = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : $premium_version_nr . WPPFM_VERSION_NUM;
			$this->_js_min			 = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

			$this->prepare_options_form();
		}

		private function prepare_options_form() {
			$this->_options_form = new WPPFM_Options_Form ();
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
	}	

     // end of WPPFM_Add_Options_Page class

endif;