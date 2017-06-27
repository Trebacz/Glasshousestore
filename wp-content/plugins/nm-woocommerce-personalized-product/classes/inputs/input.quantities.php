<?php
/*
 * Followig class handling select input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Quantities_wooproduct extends NM_Inputs_wooproduct{
	
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
		
		$this -> title 		= __ ( 'Variation Quantity', 'nm-personalizedproduct' );
		$this -> desc		= __ ( 'regular select-box input', 'nm-personalizedproduct' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		$how_link = '<a href="https://najeebmedia.com/2016/09/29/add-quantity-fields-variations-woocommerce/" target="_blank">How to use?</a>';
		return array (
						'title' => array (
								'type' => 'text',
								'title' => __ ( 'Title', 'nm-personalizedproduct' ),
								'desc' => __ ( 'It will be shown as field label. '.$how_link, 'nm-personalizedproduct' ) 
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
								'type' => 'paired-quantity',
								'title' => __ ( 'Add options', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Type option with price (optionally)', 'nm-personalizedproduct' )
						),
						
						/*'onetime' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Fixed Fee', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Add one time fee to cart total.', 'nm-personalizedproduct' ) 
						),
						
						'onetime_taxable' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Fixed Fee Taxable?', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Calculate Tax for Fixed Fee', 'nm-personalizedproduct' ) 
						),*/
				
						
						'required' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Required', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Select this if it must be required.', 'nm-personalizedproduct' ) 
						),
						
						'horizontal' => array (
								'type' => 'checkbox',
								'title' => __ ( 'Horizontal Layout', 'nm-personalizedproduct' ),
								'desc' => __ ( 'Check to enable horizontal layout for variations.', 'nm-personalizedproduct' ) 
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
		if (isset($args['horizontal_layout']) && $args['horizontal_layout'] == 'on') { ?>
		<div class="nm-horizontal-layout">
			<table class="shop_table cart sizes-input">
			    <tr>
			        <th><?php _e('Options', 'nm-personalizedproduct');?></th>
	            <?php foreach($options as $opt){ ?>
                	<th>
            			<label class="quantities-lable"> <?php echo stripslashes(trim($opt['option'])); ?>
                		
            			<?php if( $opt['price'] ){
            				echo ' <span>'.wc_price($opt['price']).'</span>';
            			} ?>

            			</label>
                	</th>
	            <?php } ?>
			    </tr>
			    <tr>
			        <th><?php _e('Quantity', 'woocommerce');?></th>
	            <?php foreach($options as $opt){ ?>
                	<td>
                		<?php
	            			$name = $args['id'].'['.$opt['option'].']';
	            			$min = (isset($opt['min']) ? $opt['min'] : 0 );
	            			$max = (isset($opt['max']) ? $opt['max'] : 10000 );
	            			
	            			$required = ($args['data-req'] == 'on' ? 'required' : '');
            				echo '<input style="width:50px;text-align:center" '.$required.' min="'.$min.'" max="'.$max.'" data-option="'.$opt['option'].'" min="0" name="'.$name.'" type="number" class="quantity" value="0" data-price="'.$opt['price'].'">';
                		?>
                	</td>
	            <?php } ?>
			    </tr>
			</table>
		</div>
		<?php } else { ?>
			<table class="shop_table cart sizes-input">
			    <tr>
			        <th><?php _e('Options', 'nm-personalizedproduct');?></th>
			        <th><?php _e('Quantity', 'woocommerce');?></th>
			    </tr>
	            <?php foreach($options as $opt){ ?>
				    <tr>
		                	<th>
		            			<label class="quantities-lable"> <?php echo stripslashes(trim($opt['option'])); ?>
		                		
		            			<?php if( $opt['price'] ){
		            				echo ' <span>'.wc_price($opt['price']).'</span>';
		            			} ?>

		            			</label>
		                	</th>
		                	<th>
		                		<?php
			            			$name = $args['id'].'['.$opt['option'].']';
			            			$min = (isset($opt['min']) ? $opt['min'] : 0 );
			            			$max = (isset($opt['max']) ? $opt['max'] : 10000 );
			            			
			            			$required = ($args['data-req'] == 'on' ? 'required' : '');
		            				echo '<input style="width:50px;text-align:center" '.$required.' min="'.$min.'" max="'.$max.'" data-option="'.$opt['option'].'" min="0" name="'.$name.'" type="number" class="quantity" value="0" data-price="'.$opt['price'].'">';
		                		?>
		                	</th>
				    </tr>
	            <?php } ?>
			</table>

		<?php } ?>
		
		<div class="display-total-price"></div>
		
		<?php
	}
}