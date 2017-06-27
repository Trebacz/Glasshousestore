<?php
/*
 * rendering product meta on product page
 */
global $nmpersonalizedproduct, $product;

$single_form = $nmpersonalizedproduct -> get_product_meta ( $nmpersonalizedproduct -> productmeta_id );
//nm_personalizedproduct_pa( $single_form );

$existing_meta = json_decode ( $single_form -> the_meta, true );

if ($existing_meta) {
//pasting the custom css if used in form settings	

echo '<style>';
	echo '.related.products .amount-options { display:none; }';

    //added on September 2, 2014
    echo '.upsells .amount-options { display:none; }';
    if ( $single_form -> productmeta_style != '') {
		echo stripslashes(strip_tags( $single_form -> productmeta_style ));
    }
	
	/**
	 * hiding prices for variable product
	 * 
	 * @since 6.8
	 **/
	 echo '.single_variation_wrap .price {display: none; !important}';
echo '</style>';


	echo '<div id="nm-productmeta-box-' . $nmpersonalizedproduct -> productmeta_id . '" class="nm-productmeta-box">';
	echo '<input type="hidden" name="woo_option_price">';	// it will be populated while dynamic prices set in script.js
	echo '<input type="hidden" id="_product_price" value="'.$product->get_price().'">';	// it is setting price to be used for dymanic prices in script.js
	echo '<input type="hidden" id="_productmeta_id" value="'.$nmpersonalizedproduct -> productmeta_id.'">';
	echo '<input type="hidden" id="_product_id" value="'.$product->get_id().'">';
	
	echo '<input type="hidden" name="woo_onetime_fee">';	// it will be populated while dynamic prices set in script.js
	echo '<input type="hidden" name="woo_file_cost">';	// to hold the file cost
	
	echo '<input type="hidden" name="add-to-cart" value="'.$product->get_id().'">';
	
	$row_size = 0;
	
	$started_section = '';
	
	foreach ( $existing_meta as $key => $meta ) {
		
		$type 			= ( isset($meta['type']) ? $meta ['type'] : '');
		$data_name		= ( isset($meta['data_name']) ? $meta ['data_name'] : '');
		$title			= ( isset($meta['title']) ? $meta ['title'] : '');
		$width			= ( isset($meta['width']) ? $meta ['width'] : '');
		$required		= ( isset($meta['required'] ) ? $meta['required'] : '' );
		$error_message 	= ( isset($meta['error_message'] ) ? $meta['error_message'] : '' );
		$description	= ( isset($meta['description'] ) ? $meta['description'] : '' );
		$condition		= ( isset($meta['conditions'] ) ? $meta['conditions'] : '' );
		$options		= ( isset($meta['options'] ) ? $meta['options'] : array());
		
		
		//WPML
		
		$title			= nm_wpml_translate($title, 'PPOM');
		$description	= nm_wpml_translate($description, 'PPOM');
		$error_message	= nm_wpml_translate($error_message, 'PPOM');
		if(is_array($options)){
			$options		= array_map("nm_translation_options", $options);
		}
		
	
		$name = strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $data_name ) );
		
		// conditioned elements
		$visibility = '';
		$conditions_data = '';
		if (isset( $meta['logic'] ) && $meta['logic'] == 'on') {
		
			if($meta['conditions']['visibility'] == 'Show')
				$visibility = 'display: none';
		
			$conditions_data	= 'data-rules="'.esc_attr( json_encode( $condition )).'"';
		}
		
		if (($row_size + intval ( $width)) > 100 || $type == 'section') {
			
			echo '<div style="clear:both; margin: 0;"></div>';
			
			if ($type == 'section') {
				$row_size = 100;
			} else {
				
				$row_size = intval ( $width );
			}
		} else {
			
			$row_size += intval ( $width );
		}
		
		$show_asterisk = (isset( $meta ['required'] ) && $meta ['required']) ? '<span class="show_required"> *</span>' : '';
		$show_description = ($description) ? '<span class="show_description"> ' . stripslashes ( $description ) . '</span>' : '';
		
		$the_width = intval ( $width );
		$the_width = ($the_width > 0 ? $the_width - 1 . '%' : '99%');
		
		$the_margin = '1%';
		
		$field_label = stripslashes( $title ) . $show_asterisk . $show_description;
		
		
		$args = '';
		
		$validate_name = '_'.$name.'_';
		if($conditions_data == '')
			echo '<input type="hidden" name="'.$validate_name.'" value="showing">';
			
		switch ($type) {

			case 'text':
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'maxlength'	=> $meta['max_length'],
									);
									
					$args = apply_filters('ppom_input_args', $args, $type);
					
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$data_default = ( isset( $meta['default_value'] ) ? $meta['default_value'] : '');
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $data_default);					
					
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
					
				case 'number':
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'max'	=> $meta['max_value'],
									'min'	=> $meta['min_value'],
									'step'	=> $meta['step'],
									);
					$args = apply_filters('ppom_input_args', $args, $type);				
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$data_default = ( isset( $meta['default_value'] ) ? $meta['default_value'] : '');
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $data_default);					
					
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
					
				case 'masked':
						
					$args = array(	'name'			=> $name,
					'id'			=> $name,
					'data-type'		=> $type,
					'data-req'		=> $required,
					'data-mask'		=> $meta['mask'],
					'data-ismask'	=> "no",
					'data-message'	=> $error_message);
					
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
						
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
						
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
				
				case 'hidden':

					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
								);
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);	
					break;
					
			
				case 'date':
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'data-format'	=> $meta['date_formats'],
								);
			
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
			
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
					
				case 'color':
						
					$args = array(	'name'			=> $name,
					'id'			=> $name,
					'data-type'		=> $type,
					'data-req'		=> $required,
					'data-message'	=> $error_message,
					'default-color'	=> $meta['default_color'],
					'show-onload'	=> $meta['show_onload'],
					'show-palletes'	=> $meta['show_palletes']);
						
					$args = apply_filters('ppom_input_args', $args, $type);	
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
						
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
		
				case 'email':
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'	=> $error_message,
									'data-sendemail'=> $meta['send_email']);

					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
					
				
				case 'textarea':
				
					$args = array(	'name'			=> $name,
							'id'			=> $name,
							'data-type'		=> $type,
							'data-req'		=> $required,
							'data-message'	=> $error_message,
							'maxlength'	=> $meta['max_length'],
							'minlength'	=> $meta['min_length']);
					
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$data_default = ( isset( $meta['default_value'] ) ? $meta['default_value'] : '');
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $data_default);				
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
					
					
				case 'select':
				
					$default_selected = (isset( $meta['selected'] ) ? $meta['selected'] : '' );
					$data_onetime = (isset( $meta['onetime'] ) ? $meta['onetime'] : '' );
					$data_onetime_taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					
					$args = array(	'name'				=> $name,
									'id'				=> $name,
									'data-type'			=> $type,
									'data-req'			=> $required,
									'data-message'		=> $error_message,
									'data-onetime'		=> $data_onetime,
									'data-onetime-taxable'	=> $data_onetime_taxable,
									'field_label'		=> $title);
				
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options, $default_selected);
				
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
						
				case 'radio':
					$default_selected = $meta['selected'];
					$data_onetime = (isset( $meta['onetime'] ) ? $meta['onetime'] : '' );
					$data_onetime_taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					
					$args = array(	'name'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'		=> $error_message,
									'data-onetime'		=> $data_onetime,
									'data-onetime-taxable'	=> $data_onetime_taxable,
									'field_label'		=> $title);
				
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options, $default_selected);
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
						
				case 'palettes':
					$default_selected = $meta['selected'];
					$data_onetime = (isset( $meta['onetime'] ) ? $meta['onetime'] : '' );
					$data_onetime_taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					
					$args = array(	'name'			=> $name,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'		=> $error_message,
									'data-onetime'		=> $data_onetime,
									'data-onetime-taxable'	=> $data_onetime_taxable);
				
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" class="nm-color-palette" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options, $default_selected);
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
		
				case 'checkbox':
			
					$defaul_checked = explode("\n", $meta['checked']);
					$data_onetime = (isset( $meta['onetime'] ) ? $meta['onetime'] : '' );
					$data_onetime_taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
		
					$args = array(	'name'			=> $name,
							'data-type'		=> $type,
							'data-req'		=> $required,
							'data-dataname' => $name,
							'data-message'	=> $error_message,
							'data-onetime'		=> $data_onetime,
							'data-onetime-taxable'	=> $data_onetime_taxable,);
					
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options, $defaul_checked);
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					
					break;
					
				case 'file':
					
					$label_select = ($meta['button_label_select'] == '' ? __('Select files', 'nm-personalizedproduct') : $meta['button_label_select']);
					$files_allowed = ($meta['files_allowed'] == '' ? 1 : $meta['files_allowed']);
					$file_types = ($meta['file_types'] == '' ? 'jpg,png,gif' : $meta['file_types']);
					$file_size = ($meta['file_size'] == '' ? '10mb' : $meta['file_size']);
					$cropping_ratio = ($meta['cropping_ratio'] == '' ? NULL : explode("\n", $meta['cropping_ratio']));
					$chunk_size = '1mb';
					
					$drag_drop		= (isset( $meta ['dragdrop'] ) ? $meta ['dragdrop'] : '' );
					$button_class	= (isset( $meta ['button_class'] ) ? $meta ['button_class'] : '' );
					$photo_editing	= (isset( $meta ['photo_editing'] ) ? $meta ['photo_editing'] : '' );
					$editing_tools	= (isset( $meta ['editing_tools'] ) ? $meta ['editing_tools'] : '' );
					$popup_width	= (isset( $meta ['popup_width'] ) ? $meta ['popup_width'] : '500' );
					$popup_height	= (isset( $meta ['popup_height'] ) ? $meta ['popup_height'] : '400' );
					$file_cost	= (isset( $meta ['file_cost'] ) ? $meta ['file_cost'] : '' );
					$taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					$language = (isset( $meta['language_opt'] ) ? $meta['language_opt'] : '' );
					
					
					$args = array(	'name'					=> $name,
									'id'					=> $name,
									'data-type'				=> $type,
									'data-req'				=> $required,
									'dragdrop'				=> $drag_drop,
									'data-message'			=> $error_message,
									'button-label-select'	=> $label_select,
									'files-allowed'			=> $files_allowed,
									'file-types'			=> $file_types,
									'file-size'				=> $file_size,
									'chunk-size'			=> $chunk_size,
									'button-class'			=> $button_class,
									'photo-editing'			=> $photo_editing,
									'editing-tools'			=> $editing_tools,
									'aviary-api-key'		=> $single_form -> aviary_api_key,
									'popup-width'			=> $popup_width,
									'popup-height'			=> $popup_height,
									'file-cost'				=> $file_cost,
									'cropping-ratio'		=> $cropping_ratio,
									'taxable'			=> $taxable,
									'language'			=> $language,
									);
									
					$field_label = ($file_cost == '') ? $field_label : $field_label . ' - ' . wc_price($file_cost);
					
					$args = apply_filters('ppom_input_args', $args, $type);
					echo '<div data-dataname="'.$name.'" id="box-'.$name.'" class="fileupload-box" style="float:left; width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					echo '<label for="'.$name.'">'. $field_label.'</label>';
					echo '<div id="nm-uploader-area-'. $name.'" class="nm-uploader-area">';
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input( $args );
				
					echo '<span class="errors"></span>';
				
					echo '</div>';		//.nm-uploader-area
					echo '</div>';
				
					// adding thickbox support
					add_thickbox();
					break;
					
				case 'quantities':
				
					$default_selected = (isset( $meta['selected'] ) ? $meta['selected'] : '' );
					$data_onetime = (isset( $meta['onetime'] ) ? $meta['onetime'] : '' );
					$data_onetime_taxable = (isset( $meta['onetime_taxable'] ) ? $meta['onetime_taxable'] : '' );
					$horizontal_layout = (isset( $meta['horizontal'] ) ? $meta['horizontal'] : '' );
					
					$args = array(	'name'			=> $name,
									'id'			=> $name,
									'horizontal_layout' => $horizontal_layout,
									'data-type'		=> $type,
									'data-req'		=> $required,
									'data-message'		=> $error_message,
									'data-onetime'		=> $data_onetime,
									'data-onetime-taxable'	=> $data_onetime_taxable);
				
					echo '<div id="input-quantities" data-dataname="'.$name.'" id="box-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
					printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
					
					$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options, $default_selected);
				
					//for validtion message
					echo '<span class="errors"></span>';
					echo '</div>';
					break;
					
					
				case 'image':
					
						$default_selected = $meta['selected'];
						$args = array(	'name'			=> $name,
								'id'			=> $name,
								'data-type'		=> $type,
								'data-req'		=> $required,
								'data-message'	=> $error_message,
								'popup-width'	=> $meta['popup_width'],
								'popup-height'	=> $meta['popup_height'],
								'multiple-allowed' => $meta['multiple_allowed']);
					
						$args = apply_filters('ppom_input_args', $args, $type);
						echo '<div data-dataname="'.$name.'" id="pre-uploaded-images-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
							
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $meta['images'], $default_selected);
					
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</div>';
						
						// adding thickbox support
						add_thickbox();
					break;
					
				case 'audio':
					
						// nm_personalizedproduct_pa($meta);
						$default_selected = $meta['selected'];
						$args = array(	'name'			=> $name,
								'id'			=> $name,
								'data-type'		=> $type,
								'data-req'		=> $required,
								'data-message'	=> $error_message,
								// 'popup-width'	=> $meta['popup_width'],
								// 'popup-height'	=> $meta['popup_height'],
								'multiple-allowed' => $meta['multiple_allowed']);
					
						$args = apply_filters('ppom_input_args', $args, $type);
						echo '<div data-dataname="'.$name.'" id="pre-uploaded-images-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
							
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $meta['audio'], $default_selected);
					
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</div>';
						
						// adding thickbox support
						add_thickbox();
					break;
					
					
					case 'instagram':
							
						$args = array(	'name'			=> $name,
						'id'			=> $name,
						'data-type'		=> $type,
						'data-req'		=> $required,
						'data-message'	=> $error_message,
						'button-class'			=> $meta['button_class'],
						'button-label-import'	=> $meta['button_label_import'],
						'client-id'		=> $meta['client_id'],
						'client-secret'	=> $meta['client_secret'],
						);
							
						$args = apply_filters('ppom_input_args', $args, $type);	
						echo '<div data-dataname="'.$name.'" id="instagram-photos-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
							
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
							
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</div>';
						break;
			
				case 'facebook':
							
						$args = array(	'name'			=> $name,
						'id'			=> $name,
						'data-type'		=> $type,
						'button-class'			=> $meta['button_class'],
						'button-label-import'	=> $meta['button_label_import'],
						'app-id'		=> $meta['app_id'],
						'app-secret'	=> $meta['app_secret'],
						'redirect-uri'	=> $meta['redirect_uri'],
						);
							
						$args = apply_filters('ppom_input_args', $args, $type);	
						echo '<div data-dataname="'.$name.'" id="facebook-photos-'.$name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $name, $field_label );
							
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
							
						//for validtion message
						echo '<span class="errors"></span>';
						echo '</div>';
						break;
					
					
					case 'section':
						
						if($started_section)		//if section already started then close it first
							echo '</section>';
						
						$section_title 		= strtolower(preg_replace("![^a-z0-9]+!i", "_", $title)); 
						$started_section 	= 'webcontact-section-'.$section_title;
						
						$args = array(	'id'			=> $started_section,
								'data-type'		=> $type,
								'title'			=> $title,
								'description'			=> $description,
								);
						$args = apply_filters('ppom_input_args', $args, $type);
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args);
						
					break;
					
					case 'pricematrix':
						
						$pm_name = sanitize_key($title);
						echo '<div id="price-matrix-'.$pm_name.'" style="width: '. $the_width.'; margin-right: '. $the_margin.';'.$visibility.'" '.$conditions_data.'>';
						printf( __('<label for="%1$s">%2$s</label><br />', 'nm-personalizedproduct'), $pm_name, $title );
						
						$args = array(	'id'			=> $name,
								'data-type'		=> $type,
								'title'			=> $title,
								'description'			=> $description,
						);
						$args = apply_filters('ppom_input_args', $args, $type);
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options);
						
						echo '</div>';
					break;
					
					case 'bulkquantity':
						
						if( ! class_exists('NM_BulkQuantity_wooproduct') ) 
							return;
							
						$pm_name = sanitize_key($title);
						$data_fixed_prices = (isset( $meta['fixed_prices'] ) ? $meta['fixed_prices'] : '' );
						$data_label_quantity = (isset( $meta['label_quantity'] ) ? $meta['label_quantity'] : '' );
						$data_label_baseprice = (isset( $meta['label_baseprice'] ) ? $meta['label_baseprice'] : '' );
						$data_label_total = (isset( $meta['label_total'] ) ? $meta['label_total'] : '' );
						$data_label_fixed = (isset( $meta['label_fixed'] ) ? $meta['label_fixed'] : '' );
						
						echo '<div class="bulk-qty-wrap" style="'.$visibility.'" '.$conditions_data.'>';
						
						$args = array(
								'id'			=> $name,
								'data-type'		=> $type,
								'title'			=> $title,
								'description'	=> $description,
								'fixed_prices'	=> $data_fixed_prices,
								'label_quantity'	=> $data_label_quantity,
								'label_baseprice'	=> $data_label_baseprice,
								'label_total'	=> $data_label_total,
								'label_fixed'	=> $data_label_fixed,
						);
						$args = apply_filters('ppom_input_args', $args, $type);
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options);
						
						echo '</div>';
					break;
					
					
					case 'eventcalendar':
						
						if( ! function_exists('PPOM_EC') )
							return;
							
						$eventcalendar_theme = $meta['skin'].'.datepicker.css';
						wp_enqueue_style('ppom-eventcalendar', PPOM_EC()->url .'/lib/jquery-datepicker-skins/css/'.$eventcalendar_theme);						
						
						$pm_name = sanitize_key($title);
						
						echo '<div class="bulk-qty-wrap" style="'.$visibility.'" '.$conditions_data.'>';
						
						$args = array(
								'id'			=> $name,
								'data-type'		=> $type,
								'title'			=> $title,
								'description'	=> $description,
								'skin'			=> $meta['skin'],
								'cal_addon_disable_days'			=> $meta['cal_addon_disable_days'],
								'disable_dates'			=> $meta['disable_dates'],
								'disable_past_dates'			=> $meta['disable_past_dates'],
						);
						
						$args = apply_filters('ppom_input_args', $args, $type);
						$nmpersonalizedproduct -> inputs[$type]	-> render_input($args, $options);
						
						echo '</div>';
					break;
		}
	}
	
	echo '<div style="clear: both"></div>';
	
	echo '</div>'; // ends nm-productmeta-box
}

if( $single_form -> productmeta_validation == 'yes'){	//enable ajax based validation
?>
<script type="text/javascript">
	<!--
	jQuery(function($){
		
		//updating nm_personalizedproduct_vars.settings
		$(".nm-productmeta-box").closest('form').find('button').click(function(event)
		  {
		    event.preventDefault(); // cancel default behavior
		
		    if( validate_cart_data() ){
		    	$(this).closest('form').submit();
		    }
		  });
	});
	
	function validate_cart_data(){
	
	var form_data = jQuery.parseJSON( '<?php echo stripslashes($single_form -> the_meta);?>' );
	var has_error = true;
	var error_in = '';
	
	jQuery.each( form_data, function( key, meta ) {
		
		var type = meta['type'];
		var error_message	= stripslashes( meta['error_message'] );
		//console.log('err message '+error_message+' id '+meta['data_name']);
		
		error_message = (error_message === '') ? nm_personalizedproduct_vars.default_error_message : error_message;
		
		if(type === 'text' || type === 'textarea' || type === 'select' || type === 'email' || type === 'date'){
			
			var input_control = jQuery('#'+meta['data_name']);
			
			if(meta['required'] === "on" && jQuery(input_control).val() === '' && jQuery(input_control).closest('div').css('display') != 'none'){
				jQuery(input_control).closest('div').find('span.errors').html(error_message).css('color', 'red');
				has_error = false;
				error_in = meta['data_name']
			}else{
				jQuery(input_control).closest('div').find('span.errors').html('').css({'border' : '','padding' : '0'});
			}
		}else if(type === 'checkbox'){
			
			if(meta['required'] === "on" && jQuery('input:checkbox[name="'+meta['data_name']+'[]"]:checked').length === 0 && jQuery('input:checkbox[name="'+meta['data_name']+'[]"]').closest('div').css('display') != 'none'){
				
				jQuery('input:checkbox[name="'+meta['data_name']+'[]"]').closest('div').find('span.errors').html(error_message).css('color', 'red');
				has_error = false;
			}else if(meta['min_checked'] != '' && jQuery('input:checkbox[name="'+meta['data_name']+'[]"]:checked').length < meta['min_checked']){
				jQuery('input:checkbox[name="'+meta['data_name']+'[]"]').closest('div').find('span.errors').html(error_message).css('color', 'red');
				has_error = false;
			}else if(meta['max_checked'] != '' && jQuery('input:checkbox[name="'+meta['data_name']+'[]"]:checked').length > meta['max_checked']){
				jQuery('input:checkbox[name="'+meta['data_name']+'[]"]').closest('div').find('span.errors').html(error_message).css('color', 'red');
				has_error = false;
			}else{
				
				jQuery('input:checkbox[name="'+meta['data_name']+'[]"]').closest('div').find('span.errors').html('').css({'border' : '','padding' : '0'});
				
				}
		}else if(type === 'radio'){
				
				if(meta['required'] === "on" && jQuery('input:radio[name="'+meta['data_name']+'"]:checked').length === 0 && jQuery('input:radio[name="'+meta['data_name']+'"]').closest('div').css('display') != 'none'){
					jQuery('input:radio[name="'+meta['data_name']+'"]').closest('div').find('span.errors').html(error_message).css('color', 'red');
					has_error = false;
					error_in = meta['data_name']
				}else{
					jQuery('input:radio[name="'+meta['data_name']+'"]').closest('div').find('span.errors').html('').css({'border' : '','padding' : '0'});
				}
		}else if(type === 'file'){
			
				var $upload_box = jQuery('#nm-uploader-area-'+meta['data_name']);
				var $uploaded_files = $upload_box.find('input:checkbox:checked');
				if(meta['required'] === "on" && $uploaded_files.length === 0 && $upload_box.css('display') != 'none'){
					$upload_box.find('span.errors').html(error_message).css('color', 'red');
					has_error = false;
					error_in = meta['data_name']
				}else{
					$upload_box.find('span.errors').html('').css({'border' : '','padding' : '0'});
				}
		}else if(type === 'image'){
			
			var $image_box = jQuery('#pre-uploaded-images-'+meta['data_name']);
			if(meta['required'] === "on" && jQuery('input:radio[name="'+meta['data_name']+'"]:checked').length === 0 && jQuery('input:checkbox[name="'+meta['data_name']+'[]"]:checked').length === 0 && $image_box.css('display') != 'none'){
				$image_box.find('span.errors').html(error_message).css('color', 'red');
				has_error = false;
				error_in = meta['data_name']
			}else{
				$image_box.find('span.errors').html('').css({'border' : '','padding' : '0'});
			}
		}else if(type === 'masked'){
			
			var input_control = jQuery('#'+meta['data_name']);
			
			if(meta['required'] === "on" && (jQuery(input_control).val() === '' || jQuery(input_control).attr('data-ismask') === 'no') && jQuery(input_control).closest('div').css('display') != 'none'){
				jQuery(input_control).closest('div').find('span.errors').html(error_message).css('color', 'red');
				has_error = false;
				error_in = meta['data_name'];
			}else{
				jQuery(input_control).closest('div').find('span.errors').html('').css({'border' : '','padding' : '0'});
			}
		}
		
	});
	
	//console.log( error_in ); return false;
	return has_error;
}
	//-->
</script>

<?php
}	//ending if() ajax based validation
