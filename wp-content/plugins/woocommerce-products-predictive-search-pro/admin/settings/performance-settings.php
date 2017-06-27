<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search Performance Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WC_Predictive_Search_Performance_Settings extends WC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'performance-settings';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = '';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_predictive_search_performance_settings';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	public function custom_types() {
		$custom_type = array( 'min_characters_yellow_message', 'time_delay_yellow_message', 'cache_timeout_yellow_message' );
		
		return $custom_type;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {

		// add custom type
		foreach ( $this->custom_types() as $custom_type ) {
			add_action( $this->plugin_name . '_admin_field_' . $custom_type, array( $this, $custom_type ) );
		}
		
		add_action( 'plugins_loaded', array( $this, 'init_form_fields' ), 1 );
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Performance Settings successfully saved.', 'woocommerce-predictive-search' ),
				'error_message'		=> __( 'Error: Performance Settings can not save.', 'woocommerce-predictive-search' ),
				'reset_message'		=> __( 'Performance Settings successfully reseted.', 'woocommerce-predictive-search' ),
			);

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wc_predictive_search_admin_interface;
		
		$wc_predictive_search_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}

	/*-----------------------------------------------------------------------------------*/
	/* after_save_settings()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function after_save_settings() {

	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wc_predictive_search_admin_interface;
		
		$wc_predictive_search_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'performance-settings',
			'label'				=> __( 'Performance', 'woocommerce-predictive-search' ),
			'callback_function'	=> 'wc_predictive_search_performance_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wc_predictive_search_admin_interface;
		
		$output = '';
		$output .= $wc_predictive_search_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {

		$sync_button_text = __( 'Start Sync', 'woocommerce-predictive-search' );
		$synced_full_data = false;
		if ( isset( $_GET['page'] ) && 'woo-predictive-search' == $_GET['page'] && isset( $_GET['tab'] ) && $this->parent_tab == $_GET['tab'] ) {
			if ( ! isset( $_SESSION ) ) {
				@session_start();
			}

			global $wpdb, $wc_ps_product_sku_data;
			$total_products = $wpdb->get_var( $wpdb->prepare( 'SELECT count(id) FROM '.$wpdb->posts.' WHERE post_type=%s AND post_status=%s', 'product', 'publish' ) );
			$total_products = ! empty( $total_products ) ? $total_products : 0;

			$total_skus = $wc_ps_product_sku_data->get_total_items_need_sync();
			$total_skus = ! empty( $total_skus ) ? $total_skus : 0;

			$all_posts      = wp_count_posts( 'post' );
			$total_posts    = isset( $all_posts->publish ) ? $all_posts->publish : 0;

			$all_pages      = wp_count_posts( 'page' );
			$total_pages    = isset( $all_pages->publish ) ? $all_pages->publish : 0;

			global $wc_ps_posts_data;
			$current_products = $wc_ps_posts_data->get_total_items_synched( 'product' );
			$current_skus     = $wc_ps_product_sku_data->get_total_items_synched();
			$current_posts    = $wc_ps_posts_data->get_total_items_synched( 'post' );
			$current_pages    = $wc_ps_posts_data->get_total_items_synched( 'page' );

			global $wc_ps_term_relationships_data;
			$current_relationships = $wc_ps_term_relationships_data->get_total_items_synched();
			$total_relationships   = $wc_ps_term_relationships_data->get_total_items_need_sync();

			global $wc_ps_product_categories_data;
			$current_categories = $wc_ps_product_categories_data->get_total_items_synched();
			$total_categories   = $wc_ps_product_categories_data->get_total_items_need_sync();

			global $wc_ps_product_tags_data;
			$current_tags = $wc_ps_product_tags_data->get_total_items_synched();
			$total_tags   = $wc_ps_product_tags_data->get_total_items_need_sync();

			$current_items = $current_products + $current_skus + $current_categories + $current_tags + $current_relationships + $current_posts + $current_pages;
			$total_items   = $total_products + $total_skus + $total_categories + $total_tags + $total_relationships + $total_posts + $total_pages;

			$had_sync_posts_data = get_option( 'wc_predictive_search_had_sync_posts_data', 0 );

			if ( 0 == $had_sync_posts_data ) {
				$synced_full_data = true;
				update_option( 'wc_predictive_search_synced_posts_data', 1 );
			} elseif ( $current_items > 0 && $current_items < $total_items ) {
				update_option( 'wc_predictive_search_synced_posts_data', 0 );
				$sync_button_text = __( 'Continue Sync', 'woocommerce-predictive-search' );
			} elseif ( $current_items >= $total_items ) {
				$synced_full_data = true;
				update_option( 'wc_predictive_search_synced_posts_data', 1 );
				$sync_button_text = __( 'Re Sync', 'woocommerce-predictive-search' );
			}
		}

  		// Define settings
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(

			array(
            	'name' 		=> __( 'Manual Database Sync', 'woocommerce-predictive-search' ),
            	'desc'		=> __( 'Predictive Search database is auto updated whenever a product or post is published or updated. Please run a Manual database sync if you upload products by csv or feel that Predictive Search results are showing old data.  Will sync the Predictive Search database with your current WooCommerce and WordPress databases', 'woocommerce-predictive-search' ),
            	'id'		=> 'predictive_search_synch_data',
                'type' 		=> 'heading',
				'is_box'	=> true,
           	),
           	array(
				'name'             => __( 'Sync Search Data', 'woocommerce-predictive-search' ),
				'id'               => 'woocommerce_search_sync_data',
				'type'             => 'ajax_multi_submit',
				'statistic_column' => 2,
				'multi_submit' => array(
					array(
						'item_id'          => 'sync_products',
						'item_name'        => __( 'Products Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_products ) ) ? (int) $current_products : 0,
						'total_items'      => ( ! empty( $total_products ) ) ? (int) $total_products : 0,
						'progressing_text' => __( 'Syncing Products...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Products', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_products',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_product_skus',
						'item_name'        => __( 'Product SKUs Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_skus ) ) ? (int) $current_skus : 0,
						'total_items'      => ( ! empty( $total_skus ) ) ? (int) $total_skus : 0,
						'progressing_text' => __( 'Syncing Product SKUs...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Product SKUs', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_product_skus',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_categories',
						'item_name'        => __( 'Product Categories Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_categories ) ) ? (int) $current_categories : 0,
						'total_items'      => ( ! empty( $total_categories ) ) ? (int) $total_categories : 0,
						'progressing_text' => __( 'Syncing Product Categories...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Product Categories', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_categories',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_tags',
						'item_name'        => __( 'Product Tags Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_tags ) ) ? (int) $current_tags : 0,
						'total_items'      => ( ! empty( $total_tags ) ) ? (int) $total_tags : 0,
						'progressing_text' => __( 'Syncing Product Tags...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Product Tags', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_tags',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_posts',
						'item_name'        => __( 'Posts Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_posts ) ) ? (int) $current_posts : 0,
						'total_items'      => ( ! empty( $total_posts ) ) ? (int) $total_posts : 0,
						'progressing_text' => __( 'Syncing Posts...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Posts', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_posts',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#7ad03a',
						)
					),
					array(
						'item_id'          => 'sync_pages',
						'item_name'        => __( 'Pages Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_pages ) ) ? (int) $current_pages : 0,
						'total_items'      => ( ! empty( $total_pages ) ) ? (int) $total_pages : 0,
						'progressing_text' => __( 'Syncing Pages...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Pages', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_pages',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#0073aa',
						)
					),
					array(
						'item_id'          => 'sync_relationships',
						'item_name'        => __( 'Term Relationships Synced', 'woocommerce-predictive-search' ),
						'current_items'    => ( ! empty( $current_relationships ) ) ? (int) $current_relationships : 0,
						'total_items'      => ( ! empty( $total_relationships ) ) ? (int) $total_relationships : 0,
						'progressing_text' => __( 'Syncing Term Relationships...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Term Relationships', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_relationships',
							)
						),
						'show_statistic'       => false,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
				),
				'separate_option'   => true,
				'button_name'       => $sync_button_text,
				'resubmit'			=> $synced_full_data,
				'progressing_text'  => __( 'Syncing Data...', 'woocommerce-predictive-search' ),
				'completed_text'    => __( 'Synced Data', 'woocommerce-predictive-search' ),
				'successed_text'    => __( 'Synced Data', 'woocommerce-predictive-search' ),
			),

			array(
            	'name' 		=> __( 'Search Performance Settings', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
				'desc'		=> __( "If you have a large site with 1,000's of products or an underpowered server use the settings below to tweak the search performance.", 'woocommerce-predictive-search' ),
				'id'		=> 'predictive_search_performance_settings',
				'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "Charaters Before Query", 'woocommerce-predictive-search' ),
				'desc' 		=> __("characters", 'woocommerce-predictive-search' ). '. ' .__( 'Number of Characters min 1, max 6', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_min_characters',
				'type' 		=> 'slider',
				'default'	=> 1,
				'min'		=> 1,
				'max'		=> 6,
				'increment'	=> 1
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container min_characters_yellow_message_container',
           	),
			array(
                'type' 		=> 'min_characters_yellow_message',
           	),
			
			array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Query Time Delay', 'woocommerce-predictive-search' ),
				'desc' 		=> __( 'milli seconds', 'woocommerce-predictive-search' ). '. ' .__( 'min 500, max 1,500', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_delay_time',
				'type' 		=> 'slider',
				'default'	=> 600,
				'min'		=> 500,
				'max'		=> 1500,
				'increment'	=> 100
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container time_delay_yellow_message_container',
           	),
			array(
                'type' 		=> 'time_delay_yellow_message',
           	),

           	array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Cache Timeout', 'woocommerce-predictive-search' ),
				'desc' 		=> __( 'hours', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_cache_timeout',
				'type' 		=> 'slider',
				'default'	=> 24,
				'min'		=> 1,
				'max'		=> 72,
				'increment'	=> 1
			),

			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container cache_timeout_yellow_message_container',
           	),
			array(
                'type' 		=> 'cache_timeout_yellow_message',
           	),
		
        ));
	}

	public function include_script() {
	?>
	<style type="text/css">
		.a3-ps-synched-products {
			color: #96587d;
		}
		.a3-ps-synched-posts {
			color: #7ad03a;
		}
		.a3-ps-synched-pages {
			color: #0073aa;
		}
	</style>
<script>
(function($) {

	$(document).ready(function() {

		$(document).on( 'a3rev-ui-ajax_multi_submit-end', '#woocommerce_search_sync_data', function( event, bt_ajax_submit, multi_ajax ) {
			bt_ajax_submit.html('<?php echo __( 'Re Sync', 'woocommerce-predictive-search' ); ?>');
			$('body').find('.wc_ps_sync_data_warning').slideUp('slow');
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
				data: { action: 'wc_predictive_search_sync_end' },
				success: function ( response ) {
				}
			});
		});

	});

})(jQuery);
</script>
    <?php
	}
		
	public function min_characters_yellow_message( $value ) {
	?>
    	<tr valign="top" class="min_characters_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$min_characters_yellow_message = '<div>'. __( 'Number of characters that must be typed before the first search query. Setting 6 will decrease the number of queries  on your database by a factor of ~5 over a setting of 1.' , 'woocommerce-predictive-search' ) .'</div><div>&nbsp;</div>
				<div style="clear:both"></div>
                <a class="min_characters_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'woocommerce-predictive-search' ).'</a>
                <a class="min_characters_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'woocommerce-predictive-search' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $min_characters_yellow_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .min_characters_yellow_message_container {
<?php if ( get_option( 'wc_ps_min_characters_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ps_min_characters_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".min_characters_yellow_message_dontshow", function(){
		$(".min_characters_yellow_message_tr").slideUp();
		$(".min_characters_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dontshow",
				option_name: 	"wc_ps_min_characters_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".min_characters_yellow_message_dismiss", function(){
		$(".min_characters_yellow_message_tr").slideUp();
		$(".min_characters_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dismiss",
				session_name: 	"wc_ps_min_characters_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
	
	public function time_delay_yellow_message( $value ) {
	?>
    	<tr valign="top" class="time_delay_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$time_delay_yellow_message = '<div>'. __( 'Time delay after a character is entered and query begins. Example setting 1,000 is 1 second after that last charcter is typed. If speed type a 10 letter word then first query is whole word not 1 query for each character. Reducing queries  to database by a factor of ~10.' , 'woocommerce-predictive-search' ) .'</div><div>&nbsp;</div>
				<div style="clear:both"></div>
                <a class="time_delay_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'woocommerce-predictive-search' ).'</a>
                <a class="time_delay_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'woocommerce-predictive-search' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $time_delay_yellow_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .time_delay_yellow_message_container {
<?php if ( get_option( 'wc_ps_time_delay_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ps_time_delay_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".time_delay_yellow_message_dontshow", function(){
		$(".time_delay_yellow_message_tr").slideUp();
		$(".time_delay_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dontshow",
				option_name: 	"wc_ps_time_delay_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".time_delay_yellow_message_dismiss", function(){
		$(".time_delay_yellow_message_tr").slideUp();
		$(".time_delay_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dismiss",
				session_name: 	"wc_ps_time_delay_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}

	public function cache_timeout_yellow_message( $value ) {
	?>
    	<tr valign="top" class="cache_timeout_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$cache_timeout_yellow_message = '<div>'. __( 'How long should cached popup result remain fresh? Use low value if your site have add or update many products daily. A good starting point is 24 hours.' , 'woocommerce-predictive-search' ) .'</div><div>&nbsp;</div>
				<div style="clear:both"></div>
                <a class="cache_timeout_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'woocommerce-predictive-search' ).'</a>
                <a class="cache_timeout_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'woocommerce-predictive-search' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $cache_timeout_yellow_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .cache_timeout_yellow_message_container {
<?php if ( get_option( 'wc_ps_cache_timeout_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ps_cache_timeout_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".cache_timeout_yellow_message_dontshow", function(){
		$(".cache_timeout_yellow_message_tr").slideUp();
		$(".cache_timeout_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dontshow",
				option_name: 	"wc_ps_cache_timeout_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".cache_timeout_yellow_message_dismiss", function(){
		$(".cache_timeout_yellow_message_tr").slideUp();
		$(".cache_timeout_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ps_yellow_message_dismiss",
				session_name: 	"wc_ps_cache_timeout_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ps_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
}

global $wc_predictive_search_performance_settings;
$wc_predictive_search_performance_settings = new WC_Predictive_Search_Performance_Settings();

/** 
 * wc_predictive_search_performance_settings_form()
 * Define the callback function to show subtab content
 */
function wc_predictive_search_performance_settings_form() {
	global $wc_predictive_search_performance_settings;
	$wc_predictive_search_performance_settings->settings_form();
}

?>