<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search Exclude Content Settings

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

class WC_PS_Search_Function_Settings extends WC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'search-function';
	
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
	public $form_key = 'wc_ps_search_function_settings';
	
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
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Search Function Settings successfully saved.', 'woocommerce-predictive-search' ),
				'error_message'		=> __( 'Error: Search Function Settings can not save.', 'woocommerce-predictive-search' ),
				'reset_message'		=> __( 'Search Function Settings successfully reseted.', 'woocommerce-predictive-search' ),
			);

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		
		add_action( $this->plugin_name . '_settings_' . 'predictive_search_code' . '_start', array( $this, 'predictive_search_code_start' ) );
		
		//add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
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
			'name'				=> 'search-function',
			'label'				=> __( 'Search Function', 'woocommerce-predictive-search' ),
			'callback_function'	=> 'wc_ps_search_function_settings_form',
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
		$disabled_cat_dropdown = false;
		if ( is_admin() ) {
			global $wc_predictive_search_cache;
			if ( ! $wc_predictive_search_cache->enable_cat_cache() || ! $wc_predictive_search_cache->cat_cache_is_built() ) {
				$disabled_cat_dropdown = true;
			}
		}

  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Predictive Search Function', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
          		'id' 		=> 'predictive_search_code',
          		'is_box'	=> true,
           	),
			
			array(
            	'name' 		=> __( 'Customize Search Function values :', 'woocommerce-predictive-search' ),
				'desc'		=> __("The values you set here will be shown when you add the global search function to your header.php file. After adding the global function to your header.php file you can change the values here and 'Update' and they will be auto updated in the function.", 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
                'id'		=> 'predictive_search_customize_function_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'Product name', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of Product Name to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_product_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Product SKU', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of Product SKU to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_p_sku_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Product category', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of Product Categories to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_p_cat_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Product tag', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of Product Tags to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_p_tag_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Post', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of Posts to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_post_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(  
				'name' 		=> __( 'Page', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of Pages to show in search field drop-down. Leave &lt;empty&gt; for not activated', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_page_items',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),
			array(
				'name' 		=> __( 'Select Template', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_widget_template',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'sidebar',
				'checked_value'		=> 'sidebar',
				'unchecked_value'	=> 'header',
				'checked_label'		=> __( 'WIDGET', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'HEADER', 'woocommerce-predictive-search' ),
			),
			array(
				'name' 		=> __( 'Category Dropdown', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to search in Product Category.', 'woocommerce-predictive-search' )
				. ( ( $disabled_cat_dropdown ) ? '</span><div style="clear: both;">'.sprintf( __( 'Activate and build <a href="%s">Category Cache</a> to activate this feature', 'woocommerce-predictive-search' ), admin_url( 'admin.php?page=woo-predictive-search&tab=search-box-settings&box_open=predictive_search_category_cache_box#predictive_search_category_cache_box', 'relative' ) ).'</div><span>' : '' ),
				'id' 		=> 'woocommerce_search_show_catdropdown',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
				'custom_attributes' => ( $disabled_cat_dropdown ) ? array( 'disabled' => 'disabled' ) : array(),
			),
			array(  
				'name' 		=> __( 'Image', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show Results Images', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_show_image',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Price', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show Results Prices', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_show_price',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(  
				'name' 		=> __( 'Description', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show Results Description', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_show_desc',
				'class'		=> 'woocommerce_search_show_desc',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			array(
            	'name' 		=> '',
                'type' 		=> 'heading',
                'class'		=> 'woocommerce_search_show_desc_container',
           	),
			array(  
				'name' 		=> __( 'Character Count', 'woocommerce-predictive-search' ),
				'desc' 		=> __('Number of characters from results description to show in search field drop-down. Default value is "100".', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_character_max',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
			),

			array(
            	'name' 		=> '',
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'Product Categories', 'woocommerce-predictive-search' ),
				'desc' 		=> __('On to show Categories that Product assigned to', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_show_in_cat',
				'class'		=> 'woocommerce_search_show_in_cat',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
			),
			
		
        ));
	}
	
	public function predictive_search_code_start() {
		echo '<tr valign="top"><td class="forminp" colspan="2">';
		?>
        <?php _e('Copy and paste this global function into your themes header.php file to replace any existing search function. (Be sure to delete the existing WordPress, WooCommerce or Theme search function)', 'woocommerce-predictive-search' );?>
            <br /><code>&lt;?php<br />
            $ps_echo = true ; <br /> 
            if ( function_exists( 'woo_predictive_search_widget' ) ) woo_predictive_search_widget( $ps_echo ); <br /> 
            ?&gt;</code>
		<?php echo '</td></tr>';
	}

	public function include_script() {
	?>
<script>
(function($) {

	$(document).ready(function() {

		if ( $("input.woocommerce_search_show_desc:checked").val() != 'yes') {
			$('.woocommerce_search_show_desc_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px' } );
		}

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.woocommerce_search_show_desc', function( event, value, status ) {
			$('.woocommerce_search_show_desc_container').attr('style','display:none;');
			if ( status == 'true' ) {
				$(".woocommerce_search_show_desc_container").slideDown();
			} else {
				$(".woocommerce_search_show_desc_container").slideUp();
			}
		});

	});

})(jQuery);
</script>
    <?php
	}
	
}

global $wc_ps_search_function_settings;
$wc_ps_search_function_settings = new WC_PS_Search_Function_Settings();

/** 
 * wc_ps_search_function_settings_form()
 * Define the callback function to show subtab content
 */
function wc_ps_search_function_settings_form() {
	global $wc_ps_search_function_settings;
	$wc_ps_search_function_settings->settings_form();
}

?>