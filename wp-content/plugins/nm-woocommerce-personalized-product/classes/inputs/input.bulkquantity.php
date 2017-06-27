<?php
/*
 * Followig class handling price matrix based on quantity provied in range
 * like 1-25
* dependencies. Do not make changes in code
* Create on: 10 February, 2017
*/

class NM_BulkQuantity_wooproduct extends NM_Inputs_wooproduct{
	
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
		
		$this -> title 		= __ ( 'Bulk Quantity', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'Price/Quantity', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
				
						'info' => array (
								'type' => 'info',
								'title' => __ ( 'Please Get Bulk Quantity Addon to Enable this feature.', 'nm-personalizedproduct' ),
						),
						
						
						
				);
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $options){

		_e('Sorry please upgrade to Pro version', 'nm-personalizedproduct');

		
	}

}