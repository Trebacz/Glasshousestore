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

/**
 * check wheather ppom setting allow to send file as attachment
 * @since 8.1
 **/
function ppom_send_file_in_attachment($product_id) {
	
	$product_meta = get_post_meta ( $product_id, '_product_meta_id', true );
	
	if ($product_meta == 0 || $product_meta == 'None')
		return false;
	
	$single_form = PPOM()-> get_product_meta ( $product_meta );
	if( $single_form -> send_file_attachment == 'yes'){
		return true;
	} else {
		return false;
	}
	
}

/**
 * adding cart items to order
 * @since 8.2
 **/
function ppom_make_meta_data( $cart_item ){
	
	// nm_personalizedproduct_pa($cart_item); exit;
		
	if ( ! isset ( $cart_item ['product_meta'] )) {
		return;
	}
	 // removing the _File(s) attached key
	 if (isset( $cart_item ['product_meta'] ['meta_data']['_File(s) attached'] )) {
	 	unset( $cart_item ['product_meta'] ['meta_data']['_File(s) attached']);
	 }
	 
	
	$order_val = '';
	$order_data = array();
	
	foreach ( $cart_item ['product_meta'] ['meta_data'] as $key => $val ) {
		
		if (is_array($val) ) {
			
			if(isset($val['type']) && ($val['type'] == 'image' || $val['type'] == 'audio') ){
					
				// if selected designs are more then one
				
				$order_val = '';
				
				if(is_array($val['selected'])){
		
					$_v = '';
					foreach ($val['selected'] as $selected){
						$selecte_image_meta = json_decode(stripslashes( $selected ));
						$_v .= $selecte_image_meta -> title.',';
					}
					
					$order_val = $_v;
				}else{
					$selecte_image_meta = json_decode(stripslashes( $val['selected'] ));
					$order_val = $selecte_image_meta -> title;
				}
				
				
			}elseif(isset($val['type']) && $val['type'] == 'bulkquantity'){
					
				// if selected designs are more then one
				
				$order_val = '';
				$data = $val['data'];
				$val = sprintf(__('%s (%d)', 'nm-personalizedproduct'), $data['option'], intval($data['qty']));
				
				$order_val = $val;
				
			}else{
				
				$order_val = implode(',', $val);
			}
		}else{
		
			$order_val = stripslashes( $val );
		}
		
		$order_data[$key] = $order_val;
	}
	
	return $order_data;
}

/**
* hiding prices for variable product
* only when priced options are used
* 
* @since 8.2
**/
function ppom_meta_priced_options( $the_meta ) {
	
	$has_priced_option = false;
	foreach ( $the_meta as $key => $meta ) {
	
		$options		= ( isset($meta['options'] ) ? $meta['options'] : array());
		foreach($options as $opt)
		{
				
			if( isset($opt['price']) && $opt['price'] != '') {
				$has_priced_option = true;
			}
		}
	}
	
	return apply_filters('ppom_meta_priced_options', $has_priced_option, $the_meta);
}

/**
 * check if browser is IE
 **/
function ppom_if_browser_is_ie()
{
	//print_r($_SERVER['HTTP_USER_AGENT']);
	
	if(!(isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))){
		return false;
	}else{
		return true;
	}
}

// parsing viary tools to array notation
function ppom_get_editing_tools( $editing_tools ){

	parse_str ( $editing_tools, $tools );
	if (isset( $tools['editing_tools'] ) && $tools['editing_tools'])
		return implode(',', $tools['editing_tools']);
}

function ppom_load_file_upload_js($product) {
	
	$product_meta_id = get_post_meta ( $product -> ID, '_product_meta_id', true );
	
	if ($product_meta_id == "" || $product_meta_id == 'None')
		return;
		
	$product_meta = PPOM() -> get_product_meta($product_meta_id);
	
	$ppom_fields = json_decode ( $product_meta->the_meta );
	
	$file_inputs = array();
	
	if( empty($ppom_fields) && !is_array($ppom_fields) ) return;
	
	foreach($ppom_fields as $field){
		
		if( $field->type == 'file' ){
			
			$file_cost = $field -> file_cost == '' ? array('') : array(sprintf(__("File Charges (%s)", "nm_personalizedproduct"), $field->title) => array('fee' => $field -> file_cost, 'taxable' => $field->onetime_taxable));
			$file_cost = json_encode($file_cost);
			
			$field -> editing_tools = ppom_get_editing_tools($field -> editing_tools);
			$field -> file_cost = $file_cost;
			$field -> cropping_ratio = $field ->cropping_ratio == '' ? NULL : explode("\n", $field ->cropping_ratio);
			
			// aviary crop preset
			if( $field -> cropping_ratio != '') {
				
				$crop_preset = $field -> cropping_ratio;
				$js_crop_preset = '';
				if($crop_preset){
					$js_crop_preset = '[';
					foreach($crop_preset as $preset){
						$js_crop_preset .= "'".str_replace('/', 'x', $preset)."',";
					}
					$js_crop_preset = rtrim($js_crop_preset, ',');
					$js_crop_preset .= ']';
				}
				
				$field -> aviary_crop_preset = $js_crop_preset;
			}
			
			$file_inputs[] = $field;
		}
	}
	
	if( empty( $file_inputs ) ) return;
	
	// nm_personalizedproduct_pa($file_inputs);
	
	wp_enqueue_script( 'ppom-file-upload', PPOM()->plugin_meta['url'].'/js/file-upload.js', array('jquery', 'plupload'), '8.4', true);
	$ppom_file_vars = array('file_inputs'		=> $file_inputs,
							'delete_file_msg'	=> __("Are you sure?", "nm-personalizedproduct"),
							'aviary_api_key'	=> $product_meta -> aviary_api_key,
							'plupload_runtime'	=> (ppom_if_browser_is_ie()) ? 'html5,html4' : 'html5,silverlight,html4,browserplus,gear');
	wp_localize_script( 'ppom-file-upload', 'ppom_file_vars', $ppom_file_vars);
	
	// if Aviary Editor is used
	if($product_meta -> aviary_api_key != ''){
		
		if(is_ssl()){
			wp_enqueue_script( 'aviary-api', '//dme0ih8comzn4.cloudfront.net/js/feather.js');	
		}else{
			wp_enqueue_script( 'aviary-api', '//feather.aviary.com/imaging/v1/editor.js');	
		}
	}
}

/**
 * if ranges in % then get it's value
 * 
 * @since 8.5
 **/
function ppom_handle_price_matrix( $ranges, $base_price ) {
	
	// nm_personalizedproduct_pa($ranges);
	
	$new_range = array();
	foreach ($ranges as $range) {
		
		$price = isset( $range['price'] ) ? trim($range['price']) : 0;
		if(strpos($price,'%') !== false){
			
			$the_range['percent'] = $price;
			
			$percent		= wc_format_decimal( substr( $price, 0, -1 ) );
			$percent_cut	= ($percent / 100) * $base_price;
			// var_dump($percent_cut);
			$price			= wc_format_decimal(($base_price - $percent_cut), 2);
			/*$wc_price		= wc_price($price);
			echo "{$base_price} - {$percent_cut} = {$price} (wc:{$wc_price})|| ";*/
			
			$the_range['option']	= $range['option'];
			$the_range['price']		= $price;
			
			$new_range[] = $the_range;
		}
	}
	
	// nm_personalizedproduct_pa($new_range); exit;
	return $new_range;
}

function ppom_adjust_price_matrix_for_option_price($price_matrix, $post_data, $product_id){
	
	if( ! isset($post_data['woo_option_price'] ) ) return $price_matrix;
	
	if( ! isset($post_data['_pricematrix_option_added'] ) ) return $price_matrix;
	
	
	$product = new WC_Product($product_id);
	
	$price_matrix = json_decode( stripslashes($price_matrix), true);
	
	$product_price = wc_format_decimal($product -> get_price() + $post_data['woo_option_price']);
	$quantity	   = $post_data['quantity'];
	
	/*nm_personalizedproduct_pa($post_data);
	nm_personalizedproduct_pa($price_matrix);*/
	
	$new_range = array();
	foreach ($price_matrix as $range) {
		
		$percent = isset( $range['percent'] ) ? trim($range['percent']) : 0;
		$price = isset( $range['price'] ) ? trim($range['price']) : 0;
		if(strpos($percent,'%') !== false){
			
			$the_range['percent'] = $percent;
			
			$percent		= wc_format_decimal( substr( $percent, 0, -1 ) );
			$percent_cut	= ($percent / 100) * $product_price;
			
			$price			= wc_format_decimal(($product_price - $percent_cut), 2);
			/*$wc_price		= wc_price($price);
			echo "{$base_price} - {$percent_cut} = {$price} (wc:{$wc_price})|| ";*/
			
			$the_range['option']	= $range['option'];
			$the_range['price']		= $price;
			
			$new_range[] = $the_range;
		}
	}
	
	// nm_personalizedproduct_pa($new_range);
	return json_encode($new_range);
	
	
}