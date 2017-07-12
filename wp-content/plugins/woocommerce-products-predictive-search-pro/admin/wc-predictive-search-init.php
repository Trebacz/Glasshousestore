<?php
/**
 * Register Activation Hook
 */
update_option('wc_predictive_search_plugin', 'woo_predictive_search');
function wc_predictive_install(){
	global $wpdb;
	$woocommerce_search_page_id = WC_Predictive_Search_Functions::create_page( _x('woocommerce-search', 'page_slug', 'woocommerce-predictive-search' ), 'woocommerce_search_page_id', __('Woocommerce Predictive Search', 'woocommerce-predictive-search' ), '[woocommerce_search]' );
	WC_Predictive_Search_Functions::auto_create_page_for_wpml( $woocommerce_search_page_id, _x('woocommerce-search', 'page_slug', 'woocommerce-predictive-search' ), __('Woocommerce Predictive Search', 'woocommerce-predictive-search' ), '[woocommerce_search]' );

	global $wc_predictive_search;
	$wc_predictive_search->install_databases();

	delete_option('woocommerce_search_lite_clean_on_deletion');

	update_option('wc_predictive_search_version', '4.4.0');

	global $wc_predictive_search_admin_init;
	delete_metadata( 'user', 0, $wc_predictive_search_admin_init->plugin_name . '-' . 'plugin_framework_global_box' . '-' . 'opened', '', true );

	delete_transient( $wc_predictive_search_admin_init->version_transient );
	flush_rewrite_rules();

	update_option( 'wc_predictive_search_had_sync_posts_data', 0 );

	update_option('wc_predictive_search_just_installed', true);

	// registered event for auto preload data cache
	$enable_cache_value = get_option( 'predictive_search_category_cache', 'yes' );
	if ( 'yes' == $enable_cache_value && ! wp_next_scheduled( 'wc_predictive_search_auto_preload_cache_event' ) ) {
		wp_schedule_event( time() + 120, 'hourly', 'wc_predictive_search_auto_preload_cache_event' );
	}
}

function wc_predictive_deactivate(){
	global $wc_predictive_search_admin_init;
	delete_transient( $wc_predictive_search_admin_init->version_transient );

	wp_clear_scheduled_hook( 'wc_predictive_search_auto_preload_cache_event' );

	flush_rewrite_rules();
}

function woops_init() {
	if ( get_option('wc_predictive_search_just_installed') ) {
		delete_option('wc_predictive_search_just_installed');

		// Set Settings Default from Admin Init
		global $wc_predictive_search_admin_init;
		$wc_predictive_search_admin_init->set_default_settings();

		// Build sass
		global $wc_predictive_search_less;
		$wc_predictive_search_less->plugin_build_sass();

		update_option( 'wc_predictive_search_just_confirm', 1 );
	}

	wc_predictive_search_plugin_textdomain();
}

// Add language
add_action('init', 'woops_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'WC_Predictive_Search_Hook_Filter', 'a3_wp_admin' ) );

add_action( 'plugins_loaded', array( 'WC_Predictive_Search_Hook_Filter', 'plugins_loaded' ), 8 );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('WC_Predictive_Search_Hook_Filter', 'plugin_extra_links'), 10, 2 );

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_' . WOOPS_NAME, array( 'WC_Predictive_Search_Hook_Filter', 'settings_plugin_links' ) );

function register_widget_woops_predictive_search() {
	register_widget('WC_Predictive_Search_Widgets');
}

// Need to call Admin Init to show Admin UI
global $wc_predictive_search_admin_init;
$wc_predictive_search_admin_init->init();

// Add upgrade notice to Dashboard pages
add_filter( $wc_predictive_search_admin_init->plugin_name . '_plugin_extension_boxes', array( 'WC_Predictive_Search_Hook_Filter', 'plugin_extension_box' ) );

// Custom Rewrite Rules
add_filter( 'query_vars', array( 'WC_Predictive_Search_Functions', 'add_query_vars' ) );
add_filter( 'rewrite_rules_array', array( 'WC_Predictive_Search_Functions', 'add_rewrite_rules' ) );

// Registry widget
add_action('widgets_init', 'register_widget_woops_predictive_search');

// AJAX hide yellow message dontshow
add_action('wp_ajax_wc_ps_yellow_message_dontshow', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dontshow') );
add_action('wp_ajax_nopriv_wc_ps_yellow_message_dontshow', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dontshow') );

// AJAX hide yellow message dismiss
add_action('wp_ajax_wc_ps_yellow_message_dismiss', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dismiss') );
add_action('wp_ajax_nopriv_wc_ps_yellow_message_dismiss', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dismiss') );

// Add shortcode [woocommerce_search]
add_shortcode('woocommerce_search', array('WC_Predictive_Search_Shortcodes', 'parse_shortcode_search_result'));

// Add shortcode [woocommerce_widget_search]
add_shortcode('woocommerce_search_widget', array('WC_Predictive_Search_Shortcodes', 'parse_shortcode_search_widget'));

// Add Predictive Search Meta Box to all post type
add_action( 'add_meta_boxes', array('WC_Predictive_Search_Meta','create_custombox'), 9 );

// Save Predictive Search Meta Box to all post type
if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
	add_action( 'save_post', array('WC_Predictive_Search_Meta','save_custombox' ), 11 );
}

// Add search widget icon to Page Editor
if (in_array (basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php') ) ) {
	add_action('media_buttons_context', array('WC_Predictive_Search_Shortcodes', 'add_search_widget_icon') );
	add_action('admin_footer', array('WC_Predictive_Search_Shortcodes', 'add_search_widget_mce_popup'));
}

function woo_predictive_search_widget( $ps_echo = true ) {

	$product_items    = get_option('woocommerce_search_product_items', 6 );
	$p_sku_items      = get_option('woocommerce_search_p_sku_items', 0 );
	$p_cat_items      = get_option('woocommerce_search_p_cat_items', 0 );
	$p_tag_items      = get_option('woocommerce_search_p_tag_items', 0 );
	$post_items       = get_option('woocommerce_search_post_items', 0 );
	$page_items       = get_option('woocommerce_search_page_items', 0 );
	$widget_template  = get_option('woocommerce_search_widget_template', 'sidebar' );
	$show_catdropdown = get_option('woocommerce_search_show_catdropdown', 'yes' );
	$show_image       = get_option('woocommerce_search_show_image', 'yes' );
	$show_price       = get_option('woocommerce_search_show_price', 'yes' );
	$show_desc        = get_option('woocommerce_search_show_desc', 'yes' );
	$text_lenght      = get_option('woocommerce_search_character_max', 100 );
	$show_in_cat      = get_option('woocommerce_search_show_in_cat', 'yes' );

	if ( class_exists('SitePress') ) {
		$current_lang = ICL_LANGUAGE_CODE;
		$search_box_texts = get_option('woocommerce_search_box_text', array() );
		if ( is_array($search_box_texts) && isset($search_box_texts[$current_lang]) ) $search_box_text = esc_attr( stripslashes( trim( $search_box_texts[$current_lang] ) ) );
		else $search_box_text = '';
	} else {
		$search_box_text = get_option('woocommerce_search_box_text', '' );
		if ( is_array($search_box_text) ) $search_box_text = '';
	}

	$ps_id = rand(100, 10000);

	if ( 'yes' == $show_image ) $show_image = 1;
	else $show_image = 0;

	if ( 'yes' == $show_price ) $show_price = 1;
	else $show_price = 0;

	if ( 'yes' == $show_desc ) $show_desc = 1;
	else $show_desc = 0;

	if ( 'yes' == $show_in_cat ) $show_in_cat = 1;
	else $show_in_cat = 0;

	if ( 'yes' == $show_catdropdown ) $show_catdropdown = 1;
	else $show_catdropdown = 0;

	global $wc_predictive_search_cache;
	if ( ! $wc_predictive_search_cache->enable_cat_cache() || ! $wc_predictive_search_cache->cat_cache_is_built() ) {
		$show_catdropdown = 0;
	}

	$row                  = 0;
	$search_list          = array();
	$number_items         = array();
	$items_search_default = WC_Predictive_Search_Widgets::get_items_search();

	foreach ($items_search_default as $key => $data) {
		if ( isset(${$key.'_items'}) && ${$key.'_items'} > 0 ) {
			$number_items[$key] = ${$key.'_items'};
			$row += ${$key.'_items'};
			$row++;
			$search_list[] = $key;
		} elseif ( $data['number'] > 0 ) {
			$number_items[$key] = $data['number'];
			$row += ${$key.'_items'};
			$row++;
			$search_list[] = $key;
		}
	}

	$search_in = json_encode($number_items);

	$ps_args = array(
		'search_box_text'  => $search_box_text,
		'row'              => $row,
		'text_lenght'      => $text_lenght,
		'show_catdropdown' => $show_catdropdown,
		'widget_template'  => $widget_template,
		'show_image'       => $show_image,
		'show_price'       => $show_price,
		'show_desc'        => $show_desc,
		'show_in_cat'      => $show_in_cat,
		'search_in'        => $search_in,
		'search_list'      => $search_list,
	);
	$search_form = wc_ps_search_form( $ps_id, $widget_template, $ps_args, false );

	if ( $ps_echo ) {
		echo $search_form;
	} else {
		return $search_form;
	}
}

// Check upgrade functions
add_action( 'init', 'woo_predictive_search_pro_upgrade_plugin' );
function woo_predictive_search_pro_upgrade_plugin () {
	global $wc_predictive_search_less, $wc_predictive_search_admin_init;

	// Upgrade to 2.0
	if(version_compare(get_option('wc_predictive_search_version'), '2.0') === -1){
		update_option('wc_predictive_search_version', '2.0');

		include( WOOPS_DIR. '/includes/updates/update-2.0.php' );
	}

	// Upgrade to 3.0
	if(version_compare(get_option('wc_predictive_search_version'), '3.0.0') === -1){
		update_option('wc_predictive_search_version', '3.0.0');

		include( WOOPS_DIR. '/includes/updates/update-3.0.php' );
	}

	// Upgrade to 3.2.0
	if(version_compare(get_option('wc_predictive_search_version'), '3.2.0') === -1){
		update_option('wc_predictive_search_version', '3.2.0');

		include( WOOPS_DIR. '/includes/updates/update-3.2.0.php' );
	}

	// Upgrade to 3.6.0
	if ( version_compare( get_option('wc_predictive_search_version' ), '3.6.0' ) === -1 ) {
		update_option('wc_predictive_search_version', '3.6.0');

		update_option( 'a3_' . $wc_predictive_search_admin_init->plugin_name . '_pin', get_option( 'a3rev_pin_woo_predictive_search', '' ) );
		update_option( 'a3_' . $wc_predictive_search_admin_init->plugin_name . '_license', get_option( 'a3rev_auth_woo_predictive_search', '' ) );
		delete_transient( $wc_predictive_search_admin_init->version_transient );
	}

	// Upgrade to 3.7.0
	if( version_compare(get_option('wc_predictive_search_version'), '3.7.0') === -1 ){
		update_option('wc_predictive_search_version', '3.7.0');

		// Set Settings Default from Admin Init
		$wc_predictive_search_admin_init->set_default_settings();

		// Build sass
		$wc_predictive_search_less->plugin_build_sass();
	}

	// Upgrade to 3.7.2
	if( version_compare(get_option('wc_predictive_search_version'), '3.8.0') === -1 ){
		update_option('wc_predictive_search_version', '3.8.0');

		// registered event for auto preload data cache
		if ( ! wp_next_scheduled( 'wc_predictive_search_auto_preload_cache_event' ) ) {
			wp_schedule_event( time() + 120, 'hourly', 'wc_predictive_search_auto_preload_cache_event' );
		}
	}

	// Upgrade to 3.9.0
	if( version_compare(get_option('wc_predictive_search_version'), '3.9.0') === -1 ){
		update_option('wc_predictive_search_version', '3.9.0');

		// Set Settings Default from Admin Init
		$wc_predictive_search_admin_init->set_default_settings();

		// Build sass
		$wc_predictive_search_less->plugin_build_sass();
	}

	// Upgrade to 3.9.2
	if( version_compare(get_option('wc_predictive_search_version'), '3.9.2') === -1 ){
		update_option('wc_predictive_search_version', '3.9.2');

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = apply_filters( 'wpml_current_language', NULL );
		}
		if ( '' != trim( $current_lang ) ) {
			$current_lang = '_' . $current_lang;
		}

		// Generate transient name
		$transient_name = 'ps_cat_dropdown';
		$transient_name .= $current_lang;

		// Get cached
		$data_cached = get_transient( $transient_name );
		if ( false !== $data_cached ) {
			update_option( 'predictive_search_have_cat_cache' . $current_lang, 'yes' );
		}
	}

	// Upgrade to 3.9.3
	if( version_compare(get_option('wc_predictive_search_version'), '3.9.3') === -1 ){
		update_option('wc_predictive_search_version', '3.9.3');

		delete_option( 'wc_predictive_search_lite_version' );
		update_option( 'wc_ps_upgraded_to_new_sync_data', 0 );
	}

	// Upgrade to 4.0.0
	if( version_compare(get_option('wc_predictive_search_version'), '4.0.0') === -1 ){
		update_option('wc_predictive_search_version', '4.0.0');

		include( WOOPS_DIR. '/includes/updates/update-4.0.php' );

		// Build sass
		$wc_predictive_search_less->plugin_build_sass();

		delete_option( 'wc_predictive_search_lite_version' );
		update_option( 'wc_ps_upgraded_to_new_sync_data', 0 );
	}

	if( version_compare( get_option('wc_predictive_search_version'), '4.1.0') === -1 ){
		// Build sass
		$wc_predictive_search_less->plugin_build_sass();
	}

	if ( version_compare( get_option('wc_predictive_search_version'), '4.2.0', '<' ) ) {
		include( WOOPS_DIR. '/includes/updates/update-4.2.0.php' );
	}

	update_option('wc_predictive_search_version', '4.4.0');
}

function woo_predictive_search_check_pin() {
	return true;
}

?>