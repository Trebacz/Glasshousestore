<?php
/*
 * Followig class handling file input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_File_wooproduct extends NM_Inputs_wooproduct{
	
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
		
		$this -> title 		= __ ( 'File Input', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'regular file input', 'nm-personalizedproduct' );
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
				
				'file_cost' => array (
						'type' => 'text',
						'title' => __ ( 'File cost/price', 'nm-personalizedproduct' ),
						'desc' => __ ( 'This will be added into cart', 'nm-personalizedproduct' )
				),
				'onetime_taxable' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Fee Taxable?', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Calculate Tax for Fixed Fee', 'nm-personalizedproduct' ) 
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
				
				'dragdrop' => array (
						'type' => 'checkbox',
						'title' => __ ( 'Drag & Drop', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Turn drag & drop on/eff.', 'nm-personalizedproduct' )
				),
						
				'popup_width' => array (
						'type' => 'text',
						'title' => __ ( 'Popup width', 'nm-personalizedproduct' ),
						'desc' => __ ( '(if image) Popup window width in px e.g: 750', 'nm-personalizedproduct' )
				),
				
				'popup_height' => array (
						'type' => 'text',
						'title' => __ ( 'Popup height', 'nm-personalizedproduct' ),
						'desc' => __ ( '(if image) Popup window height in px e.g: 550', 'nm-personalizedproduct' )
				),
				
				'button_label_select' => array (
						'type' => 'text',
						'title' => __ ( 'Button label (select files)', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Type button label e.g: Select Photos', 'nm-personalizedproduct' ) 
				),
				
				
				'button_class' => array (
						'type' => 'text',
						'title' => __ ( 'Button class', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Type class for both (select, upload) buttons', 'nm-personalizedproduct' ) 
				),
				
				'files_allowed' => array (
						'type' => 'text',
						'title' => __ ( 'Files allowed', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Type number of files allowed per upload by user, e.g: 3', 'nm-personalizedproduct' ) 
				),
				'file_types' => array (
						'type' => 'text',
						'title' => __ ( 'File types', 'nm-personalizedproduct' ),
						'desc' => __ ( 'File types allowed seperated by comma, e.g: jpg,pdf,zip', 'nm-personalizedproduct' ) 
				),
				
				'file_size' => array (
						'type' => 'text',
						'title' => __ ( 'File size', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Type size with units in kb|mb per file uploaded by user, e.g: 3mb', 'nm-personalizedproduct' ) 
				),
				
				'cropping_ratio' => array (
						'type' => 'textarea',
						'title' => __ ( 'Cropping Ratio (each ratio/line)', 'nm-personalizedproduct' ),
						'desc' => __ ( 'It will enable cropping after image upload e.g: 800/600 <a href="http://najeebmedia.com/front-end-image-cropping-in-wordpress/" target="blank">See</a>', 'nm-personalizedproduct' ) 
				),
				'photo_editing' => array (
						'type' => 'checkbox',
						'title' => __ ( 'Enable photo editing', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Allow users to edit photos by Aviary API, make sure that Aviary API Key is set in previous tab.', 'nm-personalizedproduct' ) 
				),
				
				'editing_tools' => array (
						'type' => 'checkbox',
						'title' => __ ( 'Editing Options', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Select editing options', 'nm-personalizedproduct' ),
						'options' => array (
								'enhance' => 'Enhancements',
								'effects' => 'Filters',
								'frames' => 'Frames',
								'stickers' => 'Stickers',
								'orientation' => 'Orientation',
								'focus' => 'Focus',
								'resize' => 'Resize',
								'crop' => 'Crop',
								'warmth' => 'Warmth',
								'brightness' => 'Brightness',
								'contrast' => 'Contrast',
								'saturation' => 'Saturation',
								'sharpness' => 'Sharpness',
								'colorsplash' => 'Colorsplash',
								'draw' => 'Draw',
								'text' => 'Text',
								'redeye' => 'Red-Eye',
								'whiten' => 'Whiten teeth',
								'blemish' => 'Remove skin blemishes' 
						) 
				),
				'language_opt' => array (
						'type' => 'select',
						'title' => __ ( 'Select Language', 'nm-personalizedproduct' ),
						'desc' => __ ( 'Select language for uploader alerts and messages.', 'nm-personalizedproduct' ),
						'options' => array (
								'' => 'Default',
								'ar' => 'AR',
								'az' => 'AZ',
								'bs' => 'BS',
								'cs' => 'CS',
								'cy' => 'CY',
								'da' => 'DA',
								'de' => 'DE',
								'el' => 'EL',
								'en' => 'EN',
								'es' => 'ES',
								'et' => 'ET',
								'fa' => 'FA',
								'fi' => 'FI',
								'fr' => 'FR',
								'he' => 'HE',
								'hr' => 'HR',
								'hu' => 'HU',
								'hy' => 'HY',
								'id' => 'ID',
								'it' => 'IT',
								'ja' => 'JA',
								'ka' => 'KA',
								'kk' => 'KK',
								'km' => 'KM',
								'ko' => 'KO',
								'lt' => 'LT',
								'lv' => 'LV',
								'mn' => 'MN',
								'ms' => 'MS',
								'nl' => 'NL',
								'pl' => 'PL',
								'pt_BR' => 'PT_BR',
								'ro' => 'RO',
								'ru' => 'RU',
								'sk' => 'SK',
								'sq' => 'SQ',
								'sr' => 'SR',
								'sr_RS' => 'SR_RS',
								'sv' => 'SV',
								'th_TH' => 'TH_TH',
								'tr' => 'TR',
								'uk_UA' => 'UK_UA',
								'zh_CN' => 'ZH_CN',
								'zh_TW' => 'ZH_TW',
						) 
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
	 * @params: args
	*/
	function render_input($args, $content=""){

		// Setting pluploader language for alerts (added in 6.6)
		if ($args['language'] != '') {
			$plugin_meta = get_plugin_meta_wooproduct();
			wp_enqueue_script( 'pluploader-language', $plugin_meta['url'].'/js/plupload-2.1.2/js/i18n/'.$args['language'].'.js');
		}
		$container_height = ($args['dragdrop']) ? 'auto' : '30px' ;
		$_html = '<div class="container_buttons" style="height: '.$container_height.' ;">';
			$_html .= '<div class="btn_center">';
			$_html .= '<a id="selectfiles-'.$args['id'].'" href="javascript:;" class="select_button '.$args['button-class'].'">' . $args['button-label-select'] . '</a>';
			$_html .= '</div>';
			
			
		$_html .= '</div>';		//container_buttons

		if($args['dragdrop']){
			
			$_html .= '<div class="droptext">';
				if($this -> if_browser_is_ie())
					$_html .= __('Drag file(s) in this box', 'nm-personalizedproduct');
				else 
					$_html .= __('Drag file(s) or directory in this box', 'nm-personalizedproduct');
			$_html .= '</div>';
		}
    	
    	$_html .= '<div id="filelist-'.$args['id'].'" class="filelist"></div>';
    	
    	
    	echo $_html;
    	
    	$this -> get_input_js($args);
	}
	
	
	/*
	 * following function is rendering JS needed for input
	 */
	function get_input_js($args){
		
		// nothing since 8.4
	}
}