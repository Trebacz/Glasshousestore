<?php
/**
 * WooCommerce Predictive Search Hook Filter
 *
 * Hook anf Filter into woocommerce plugin
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * a3_wp_admin()
 * yellow_message_dontshow()
 * yellow_message_dismiss()
 * plugin_extra_links()
 */
class WC_Predictive_Search_Hook_Filter
{

	public static function plugins_loaded() {
		global $woocommerce_search_page_id;
		global $predictive_search_mode;
		global $predictive_search_description_source;

		$woocommerce_search_page_id = WC_Predictive_Search_Functions::get_page_id_from_shortcode( 'woocommerce_search', 'woocommerce_search_page_id');

		$predictive_search_mode               = get_option( 'predictive_search_mode', 'strict' );
		$predictive_search_description_source = get_option( 'predictive_search_description_source', 'content' );
	}

	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WOOPS_CSS_URL . '/a3_wp_admin.css' );
	}

	public static function yellow_message_dontshow() {
		check_ajax_referer( 'wc_ps_yellow_message_dontshow', 'security' );
		$option_name   = $_REQUEST['option_name'];
		update_option( $option_name, 1 );
		die();
	}

	public static function yellow_message_dismiss() {
		check_ajax_referer( 'wc_ps_yellow_message_dismiss', 'security' );
		$session_name   = $_REQUEST['session_name'];
		if ( !isset($_SESSION) ) { @session_start(); }
		$_SESSION[$session_name] = 1 ;
		die();
	}

	public static function plugin_extra_links($links, $plugin_name) {
		global $wc_predictive_search_admin_init;

		if ( $plugin_name != WOOPS_NAME) {
			return $links;
		}
		$links[] = '<a href="'.WOO_PREDICTIVE_SEARCH_DOCS_URI.'" target="_blank">'.__('Documentation', 'woocommerce-predictive-search' ).'</a>';
		$links[] = '<a href="'.$wc_predictive_search_admin_init->support_url.'" target="_blank">'.__('Support', 'woocommerce-predictive-search' ).'</a>';
		return $links;
	}

	public static function settings_plugin_links($actions) {
		$actions = array_merge( array( 'settings' => '<a href="admin.php?page=woo-predictive-search">' . __( 'Settings', 'woocommerce-predictive-search' ) . '</a>' ), $actions );

		return $actions;
	}

	public static function plugin_extension_box( $boxes = array() ) {
		global $wc_predictive_search_admin_init;

		$support_box = '<a href="'.$wc_predictive_search_admin_init->support_url.'" target="_blank" alt="'.__('Go to Support Forum', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/go-to-support-forum.png" /></a>';
		$boxes[] = array(
			'content' => $support_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$docs_box = '<a href="'.WOO_PREDICTIVE_SEARCH_DOCS_URI.'" target="_blank" alt="'.__('View Plugin Docs', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/view-plugin-docs.png" /></a>';

		$boxes[] = array(
			'content' => $docs_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		$connect_box = '<div style="margin-bottom: 5px;">' . __('Connect with us via','woocommerce-predictive-search' ) . '</div>';
		$connect_box .= '<a href="https://www.facebook.com/a3rev" target="_blank" alt="'.__('a3rev Facebook', 'woocommerce-predictive-search' ).'" style="margin-right: 5px;"><img src="'.WOOPS_IMAGES_URL.'/follow-facebook.png" /></a> ';
		$connect_box .= '<a href="https://twitter.com/a3rev" target="_blank" alt="'.__('a3rev Twitter', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/follow-twitter.png" /></a>';

		$boxes[] = array(
			'content' => $connect_box,
			'css' => 'border-color: #3a5795;'
		);

		$woocommerce_box = '<a href="http://a3rev.com/product-category/woocommerce/?display=products" target="_blank" alt="'.__('WooCommerce Plugins', 'woocommerce-predictive-search' ).'"><img src="'.WOOPS_IMAGES_URL.'/woocommerce-plugins.png" /></a>';

		$boxes[] = array(
			'content' => $woocommerce_box,
			'css' => 'border: none; padding: 0; background: none;'
		);

		return $boxes;
	}
}
?>