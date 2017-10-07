<?php
/*
 * this file contains pluing meta information and then shared
 * between pluging and admin classes
 * * [1]
 */



if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	define('NM_DIR_SEPERATOR', '\\');
} else {
	define('NM_DIR_SEPERATOR', '/');
}

function get_plugin_meta_wooproduct(){
	
	
	return array('name'			=> 'Personalized Product',
							'dir_name'		=> '',
							'shortname'		=> 'nm_personalizedproduct',
							'path'			=> untrailingslashit(plugin_dir_path( __FILE__ )),
							'url'			=> untrailingslashit(plugin_dir_url( __FILE__ )),
							'db_version'	=> 3.12,
							'logo'			=> plugin_dir_url( __FILE__ ) . 'images/logo.png',
							'menu_position'	=> 90,
							'ppom_bulkquantity'	=> plugin_dir_path( __DIR__ ) . 'ppom-addon-bulkquantity/classes/input.bulkquantity.php',
							'ppom_eventcalendar'	=> plugin_dir_path( __DIR__ ) . 'ppom-addon-eventcalendar/classes/input.eventcalendar.php'
	);
}


function nm_personalizedproduct_pa($arr){
	
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}


function nm_translation_options($option) {
	
	$option['option'] = nm_wpml_translate($option['option'], 'PPOM');
	return $option;
}

/**
 * some WC functions wrapper
 * */
 

if( !function_exists('nm_wc_add_notice')){
function nm_wc_add_notice($string, $type="error"){
 	
 	global $woocommerce;
 	if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
 		wc_add_notice( $string, $type );
	    // Use new, updated functions
	} else {
	   $woocommerce->add_error ( $string );
	}
 }
}

if( !function_exists('nm_wc_add_order_item_meta') ){
	
	function nm_wc_add_order_item_meta($item_id, $key, $val){
		global $woocommerce;
		if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
			wc_add_order_item_meta( $item_id, $key, $val );
			// Use new, updated functions
		} else {
		   woocommerce_add_order_item_meta($item_id, $key, $val);
		}
	}
}

/**
 * WPML
 * registering and translating strings input by users
 */
if( ! function_exists('nm_wpml_register') ) {
	

	function nm_wpml_register($field_value, $domain) {
		
		$field_name = $domain . ' - ' . sanitize_key($field_value);
		//WMPL
	    /**
	     * register strings for translation
	     * source: https://wpml.org/wpml-hook/wpml_register_single_string/
	     */
	     
	     do_action( 'wpml_register_single_string', $domain, $field_name, $field_value );
	     
	    //WMPL
		}
}

if( ! function_exists('nm_wpml_translate') ) {
	

	function nm_wpml_translate($field_value, $domain) {
		
		$field_name = $domain . ' - ' . sanitize_key($field_value);
		//WMPL
	    /**
	     * register strings for translation
	     * source: https://wpml.org/wpml-hook/wpml_translate_single_string/
	     */
	    
		return apply_filters('wpml_translate_single_string', $field_value, $domain, $field_name );
		//WMPL
	}
}

/**
 * returning order id 
 * 
 * @since 7.9
 */
if ( ! function_exists('nm_get_order_id') ) {
	function nm_get_order_id( $order ) {
		
		$class_name = get_class ($order);
		if( $class_name != 'WC_Order' ) 
			return $order -> ID;
		
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {  
		
			// vesion less then 2.7
			return $order -> id;
		} else {
			
			return $order -> get_id();
		}
	}
}

/**
 * returning product id 
 * 
 * @since 7.9
 */

if( ! function_exists('nm_get_product_id') ) {
	
	function nm_get_product_id( $product ) {
		
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {  
		
			// vesion less then 2.7
			return $product -> ID;
		} else {
			
			return $product -> get_id();
		}
	}
}
