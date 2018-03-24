<?php
/*
 * Followig class handling price matrix based on quantity provied in range
 * like 1-25
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_PriceMatrix_wooproduct extends NM_Inputs_wooproduct{
	
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
		
		$this -> title 		= __ ( 'Price Matrix', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'Price/Quantity', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
				
						'title' => array (
								'type' => 'text',
								'title' => __ ( 'Title', 'nm-personalizedproduct' ),
								'desc' => __ ( 'It will as section heading wrapped in h2', 'nm-personalizedproduct' )
						),
						'description' => array (
								'type' => 'textarea',
								'title' => __ ( 'Description', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Type description, it will be diplay under section heading.', 'nm-personalizedproduct' )
						),
						'option_added' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Option Added', 'nm-personalizedproduct' ),
								'desc' => __ ( 'It will apply discount on Base+Options Price, otherwis apply only Base Price', 'nm-personalizedproduct' ) 
						),
						'options' => array (
								'type' => 'paired',
								'title' => __ ( 'Price matrix', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Type quantity range with price', 'nm-personalizedproduct' )
						),
						
						
						
				);
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $ranges){

		$ranges = ppom_handle_price_matrix( $ranges, $args['product_price'] );
		
		$_html = '<input name="_pricematrix" id="_pricematrix" type="hidden" value="'.esc_attr( json_encode($ranges)).'" />';
		$_html .= '<input name="_pricematrix_option_added" id="_pricematrix_option_added" type="hidden" value="'.esc_attr( $args['option_added'] ).'" />';

		$_html .= '<p>'. stripslashes( $args['description']).'</p>';
		
		foreach($ranges as $opt)
		{
			$price = isset( $opt['price'] ) ? trim($opt['price']) : 0;
			if(isset($opt['percent'])){
				
				$percent = $opt['percent'];
				$price = "-{$percent} (".wc_price( $price ).")";
			}else {
				$price = wc_price( $price );	
			}
			
			$_html .= '<div style="clear:both;border-bottom:1px #ccc dashed;">';
			$_html .= '<span>'.stripslashes(trim($opt['option'])).'</span>';
			$_html .= '<span style="float:right">'.$price.'</span>';
			$_html .= '</div>';
		}

		echo $_html;
	}
	
}