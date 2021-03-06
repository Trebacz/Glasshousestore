<?php
/*
 * Followig class handling price matrix based on quantity provied in range
 * like 1-25
* dependencies. Do not make changes in code
* Create on: 10 February, 2017
*/

class NM_EventCalendar_wooproduct extends NM_Inputs_wooproduct{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings, $url_buy;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> plugin_meta = get_plugin_meta_wooproduct();
		
		$this -> title 		= __ ( 'Event Calendar', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'Event Booking Calendar', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
		$this -> url_buy	= 'https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/';
		
	}
	
	
	
	
	private function get_settings(){
		
		return array (
				
						'info' => array (
								'type' => 'info',
								'title' => __ ( 'It is a Paid Addon. You can Buy this from <a href="'.esc_url($this -> url_buy).'">Here</a>', 'nm-personalizedproduct' ),
						),
						
						
						
				);
	}
	
	
	/*
	 * @params: args
	*/
	function render_input($args, $options){

		_e('It is a Paid Addon. You can Buy this from <a href="'.esc_url($this -> url_buy).'">Here</a>', 'nm-personalizedproduct');

		
	}

}