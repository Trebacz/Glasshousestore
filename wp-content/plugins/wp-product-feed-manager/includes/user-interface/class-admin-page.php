<?php

/* * ******************************************************************
 * Version 1.0
 * Modified: 29-08-2015
 * Copyright 2015 Accentio. All rights reserved.
 * License: None
 * By: Michel Jongbloed
 * ****************************************************************** */

// Prevent direct access
if ( !defined( 'ABSPATH' ) ) {
	echo 'Hi!  I\'m just a plugin, there\'s not much I can do when called directly.';
	exit;
}

if ( !class_exists( 'WPPFM_Admin_Page' ) ) :

	/**
	 * 
	 */
	class WPPFM_Admin_Page {

		protected $spinner_gif;

		/**
		 * Class constructor
		 */
		protected function __construct() {

			// link to the spinner gif file
			$this->spinner_gif = MYPLUGIN_PLUGIN_URL . '/images/ajax-loader.gif';
		}

		/**
		 * Returns a string containing the standard header for an admin page.
		 * 
		 * @return string
		 */
		protected function admin_page_header( $header_text = "WP Product Feed Manager" ) {

			global $wpdb;

			return
			'
         <div class="wrap">
         <div class="feed-spinner" id="feed-spinner" style="display:none;">
            <img id="img-spinner" src="' . $this->spinner_gif . '" alt="Loading" />
         </div>
         <div class="data" id="wp-product-feed-manager-data" style="display:none;"><div id="wp-plugin-url">' . WPPFM_UPLOADS_URL . '</div><div id="wp-table-prefix">' . $wpdb->prefix . '</div></div>
         <div class="main-wrapper header-wrapper" id="header-wrapper">
         <div class="header-text"><h1>' . $header_text . '</h1></div>
         <div class="sub-header-text"><h3>' . __( 'Manage your feeds with ease', 'wp-product-feed-manager' ) . '</h3></div>
         <div class="logo"></div>
         </div>
      ';
		}

		/**
		 * Returns a string containing the standard footer for an admin page.
		 * 
		 * @return string
		 */
		protected function admin_page_footer() {

			return
			'
         <div class="main-wrapper footer-wrapper" id="footer-wrapper">
         <div class="links-wrapper" id="footer-links"><a href="' . EDD_SL_STORE_URL . '" target="_blank">About Us</a> 
		 | <a href="' . EDD_SL_STORE_URL . '/support/" target="_blank">Contact Us</a> 
		 | <a href="' . EDD_SL_STORE_URL . '/terms/" target="_blank">Terms and Conditions</a></div>
         </div></div>
      ';
		}

		protected function message_field( $alert = '' ) {
			$display_alert = !empty( $alert ) ? 'block' : 'none';
		
			return
			'
         <div class="message-field notice notice-error" id="error-message" style="display:none;"></div>
         <div class="message-field notice notice-success" id="success-message" style="display:none;"></div>
         <div class="message-field notice notice-warning" id="disposible-warning-message" style="display:' . $display_alert . ';"><p>' . $alert . '</p>
		<button type="button" id="disposible-notice-button" class="notice-dismiss"></button>
		</div>
      ';
		}

		protected function licensing_field( $stat )  {

			$license = get_option( 'wppfm_lic_key' );
			
			$license_exp = $stat === 'expired' ? '<p>Your key seems to be expired. If you are sure you have a valid key, please open a ticket at wpmarketingrobot.com/support/</p>' : '';

			$html = '<div class="edd-wrap notice-success notice below-h2"><h2>' . __( 'Plugin License Options', 'wp-product-feed-manager' ) . '</h2>';
			$html .= '<form method="post">';
			$html .= settings_fields( 'wppfm_lic_group' );
			$html .= '<table class="form-table">';
			$html .= '<tbody><tr valign="top">';
			$html .= '<th scope="row" valign="top">';
			$html .= __( 'License Key', 'wp-product-feed-manager' );
			$html .= '</th><td><input id="wppfm_license_key" name="wppfm_license_key" type="text" class="regular-text"';
			$html .= ' value="' . esc_attr__( $license ) . '" />';
			$html .= $license_exp . '</td></tr>';
			$html .= '<tr valign="top"><th scope="row" valign="top">&nbsp;</th>';
			$html .= '<td><span>Click <a href="//www.wpmarketingrobot.com/terms/" target="_blank">here to read our Terms and Conditions</a> before using WP Product Feed Manager.</span>';
			$html .= '</td></tr>';
			$html .= '<tr valign="top"><th scope="row" valign="top">';
			$html .= __( 'I accept your Terms and Conditions', 'wp-product-feed-manager' );
			$html .= '</th><td><input id="accept_eula" name="user_accepts_eula" type="checkbox" />';
			$html .= '</td></tr>';
			$html .= '<tr valign="top"><th scope="row" valign="top">';
			$html .= __( 'Activate License', 'wp-product-feed-manager' );
			$html .= '</th><td>';
			$html .= wp_nonce_field( 'wppfm_lic_nonce', 'wppfm_lic_nonce' );
			$html .= '<input type="submit" class="button-secondary" name="wppfm_license_activate" value="';
			$html .= __( 'Activate License', 'wp-product-feed-manager' ) . '" id="wppfm_license_activate" disabled />';
			$html .= '</td></tr>';
			$html .= '</tbody></table>';
			$html .= '</form></div>';

			return $html;
		}

	}

	

     // end of WPPFM_Admin_Page class

endif;