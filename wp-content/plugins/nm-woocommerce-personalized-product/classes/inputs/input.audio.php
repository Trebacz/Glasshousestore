<?php
/*
 * Followig class handling pre-uploaded image control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Audio_wooproduct extends NM_Inputs_wooproduct{
	
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
		
		$this -> title 		= __ ( 'Audio / Video', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'Audio File Selection', 'nm-personalizedproduct' );
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
		
		'required' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Required', 'nm-personalizedproduct' ),
				'desc' => __ ( 'Select this if it must be required.', 'nm-personalizedproduct' ) 
		),
				
		'audio' => array (
				'type' => 'pre-audios',
				'title' => __ ( 'Select Audio/Video', 'nm-personalizedproduct' ),
				'desc' => __ ( 'Select audio or video from media library', 'nm-personalizedproduct' )
		),
				
		'multiple_allowed' => array (
				'type' => 'checkbox',
				'title' => __ ( 'Multiple selection?', 'nm-personalizedproduct' ),
				'desc' => __ ( 'Allow users to select more then one videos or audios?.', 'nm-personalizedproduct' )
		),
				
		'selected' => array (
				'type' => 'text',
				'title' => __ ( 'Selected image', 'nm-personalizedproduct' ),
				'desc' => __ ( 'Type option title (given above) if you want it already selected.', 'nm-personalizedproduct' )
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
	function render_input($args, $images = "", $default_selected = ""){
		
		// nm_personalizedproduct_pa($images);
		
		echo '<div class="pre_audio_box">';
			
		$img_index = 0;
		// $popup_width	= $args['popup-width'] == '' ? 600 : $args['popup-width'];
		// $popup_height	= $args['popup-height'] == '' ? 450 : $args['popup-height'];
		
		if ($images) {
			echo '<table width="100%">';
			
			foreach ($images as $image){

				// Getting Audio URL
				$audio_url = '';
				if($image['id'] != ''){
					$audio_url = wp_get_attachment_url( $image['id'] );	
				} else {
					$audio_url = $image['link'];
				}

				?>
					<tr>
						<td>
						<label>
						<?php
							if ($args['multiple-allowed'] == 'on') {
								echo '<input type="checkbox" data-price="'.$image['price'].'" data-title="'.stripslashes( $image['title'] ).'" name="'.$args['name'].'[]" value="'.esc_attr(json_encode($image)).'" /> ';
							}else{
								//default selected
								$checked = ($image['title'] == $default_selected ? 'checked = "checked"' : '' );
								echo '<input type="radio" data-price="'.$image['price'].'" data-title="'.stripslashes( $image['title'] ).'" data-type="'.stripslashes( $args['data-type'] ).'" name="'.$args['name'].'" value="'.esc_attr(json_encode($image)).'" '.$checked.' /> ';
							}

							$price = '';
							if(function_exists('wc_price') && $image['price'] > 0)
								$price = wc_price( $image['price'] );							
							echo '<b>'.stripslashes( $image['title'] ) . ' ' . $price .'</b>';
						?>
						</label>
						<br>
							<?php
								echo apply_filters( 'the_content', $audio_url );
							?>
						<br>
						</td>
					</tr>
				
				<?php 
					
				$img_index++;
			}
			echo '</table>';
		}
		
		echo '<div style="clear:both"></div>';
			
		echo '</div>';		//container_buttons
		
		$this -> get_input_js($args);
	}
	
	
	/*
	 * following function is rendering JS needed for input
	*/
	function get_input_js($args){
		?>
			
					<script type="text/javascript">	
					<!--
					jQuery(function($){
						$('.wp-video').css('width', '100%');
						$('.wp-video-shortcode').css('width', '100%');
	
						
					});
					
					//--></script>
					<?php
			}
}