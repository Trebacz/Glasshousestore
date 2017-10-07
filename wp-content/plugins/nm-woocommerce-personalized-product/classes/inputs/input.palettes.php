<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Palettes_wooproduct extends NM_Inputs_wooproduct{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = get_plugin_meta_wooproduct();
		
		$this -> title 		= __ ( 'Color Palettes', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'color boxes', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
					'title' => array (
							'type' => 'text',
							'title' => __ ( 'Title', 'nm-personalizedproduct' ),
							'desc' => __ ( 'It will be shown as field label', 'nm-personalizedproduct' ) 
					),
					'data_name' => array (
							'type' => 'text',
							'title' => __ ( 'Data name', 'nm-personalizedproduct' ),
							'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'nm-personalizedproduct' ) 
					),
					'description' => array (
							'type' => 'text',
							'title' => __ ( 'Description', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Small description, it will be diplay near name title.', 'nm-personalizedproduct' ) 
					),
					'error_message' => array (
							'type' => 'text',
							'title' => __ ( 'Error message', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Insert the error message for validation.', 'nm-personalizedproduct' ) 
					),
					
					'options' => array (
								'type' => 'paired',
								'title' => __ ( 'Add colors', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Type color code with price (optionally). To write label, use #colorcode - White', 'nm-personalizedproduct' )
					),
					'onetime' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Fixed Fee', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Add one time fee to cart total.', 'nm-personalizedproduct' ) 
						),
						
					'onetime_taxable' => array (
							'type' => 'checkbox',
							'title' => __ ( 'Fixed Fee Taxable?', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Calculate Tax for Fixed Fee', 'nm-personalizedproduct' ) 
					),
					'selected' => array (
							'type' => 'text',
							'title' => __ ( 'Selected color', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Type color code (given above) if you want already selected.', 'nm-personalizedproduct' ) 
					),
					
					'disabletooltip' => array (
							'type' => 'checkbox',
							'title' => __ ( 'Disable Tooltips', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Select this if you want to display labels in colorboxes.', 'nm-personalizedproduct' ) 
					),
					
					'required' => array (
							'type' => 'checkbox',
							'title' => __ ( 'Required', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Select this if it must be required.', 'nm-personalizedproduct' ) 
					),
					
					'class' => array (
							'type' => 'text',
							'title' => __ ( 'Class', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'nm-personalizedproduct' ) 
					),
					'width' => array (
							'type' => 'text',
							'title' => __ ( 'Width', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Type field width in % e.g: 50%', 'nm-personalizedproduct' ) 
					),
					'logic' => array (
							'type' => 'checkbox',
							'title' => __ ( 'Enable conditional logic', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Tick it to turn conditional logic to work below', 'nm-personalizedproduct' )
					),
					'conditions' => array (
							'type' => 'html-conditions',
							'title' => __ ( 'Conditions', 'nm-personalizedproduct' ),
							'desc' => __ ( 'Tick it to turn conditional logic to work below', 'nm-personalizedproduct' )
					),
				);
	}
	
	
	/*
	 * @params: $options
	*/
	function render_input($args, $options="", $default=""){
		
		$_html = '';
		foreach($options as $opt)
		{
			// First Separate color code and label
			$color_label_arr = explode('-', $opt['option']);
			$color_code = trim($color_label_arr[0]);
			$color_label = '';
			if(isset($color_label_arr[1])){
				$color_label = trim($color_label_arr[1]);
			}

			if($opt['price']){
				$output	=  wc_price($opt['price']);
			}else{
				$output	= '';
			}
			
			$field_id = $args['name'] . '-meta-'.strtolower ( preg_replace ( "![^a-z0-9]+!i", "_", $opt['option'] ) );
			
			$_html .= '<label for="'.$field_id.'"> <input id="'.$field_id.'" data-price="'.$opt['price'].'" type="radio" ';
			
			foreach ($args as $attr => $value){
					
				$_html .= $attr.'="'.stripslashes( $value ).'"';
			}
		
			$_html .= ' value="'.$opt['option'].'" '.checked($default, $opt['option'], false).'>';
			
			if(isset($args['disabletooltip']) && $args['disabletooltip'] != 'on'){
				$_html .= '<div class="palette-box" title="'.$color_label.'" style="background-color:'.trim($color_code).';">'.$output.'</div>';
			} else {
				$_html .= '<div class="palette-box" title="'.$color_label.'" style="background-color:'.trim($color_code).';">'.$color_label.'<br>'.$output.'</div>';
			}
			
		
			$_html .= '</label>';
		}
		if(isset($args['disabletooltip']) && $args['disabletooltip'] != 'on'){
			$_html .= '<script>
				jQuery( document ).ready(function(){
				    jQuery( ".nm-color-palette " ).tooltip({
				      position: {
				        my: "center bottom-20",
				        at: "center top",
				        using: function( position, feedback ) {
				          jQuery( this ).css( position );
				          jQuery( "<div>" )
				            .addClass( "arrow" )
				            .addClass( feedback.vertical )
				            .addClass( feedback.horizontal )
				            .appendTo( this );
				        }
				      }
				    });
				});
			</script>';
		}		
		
		echo $_html;
	}
}