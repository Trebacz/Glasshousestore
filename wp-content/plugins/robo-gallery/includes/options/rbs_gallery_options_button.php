<?php 
/*
*      Robo Gallery     
*      Version: 1.0
*      By Robosoft
*
*      Contact: https://robosoft.co/robogallery/ 
*      Created: 2015
*      Licensed under the GPLv2 license - http://opensource.org/licenses/gpl-2.0.php
*
*      Copyright (c) 2014-2016, Robosoft. All rights reserved.
*      Available only in  https://robosoft.co/robogallery/ 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

$button_group = new_cmb2_box( array(
    'id' 			=> ROBO_GALLERY_PREFIX . 'button_metabox',
    'title' 		=> '<span class="dashicons dashicons-format-gallery"></span> '.__( 'Menu Options', 'robo-gallery' ),
    'object_types' 	=> array( ROBO_GALLERY_TYPE_POST ),
    'show_names' 	=> false,
    'context' 		=> 'normal',
));

$button_group->add_field( array(
	'name' 			=> __('Menu', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'menu',
	'type' 			=> 'switch',
	'level'			=> !ROBO_GALLERY_PRO,
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(1),
	'bootstrap_style'=> 1,
	'showhide'		=> 1,
	'depends' 		=> 	'.rbs_menu_options',
	'before_row'	=> '
	<a id="rbs_menu_options_link"></a>
<div class="rbs_block"><br/>',
	'after_row'		=> '
	<div class="rbs_menu_options">',
));

$button_group->add_field( array(
	'name' 			=> __('Menu mode', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'menuTag',
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(0),
	'type' 			=> 'switch',
	'level'			   => !ROBO_GALLERY_PRO,
	'update'		=> '1.4',
	
	'depends' 		=> 	'.menuTagOptions',

	'onText'		=> __('Tags', 'robo-gallery' ),
	'offText'		=> __('Categories', 'robo-gallery' ),
	'onStyle'		=> __('primary', 'robo-gallery' ),
	'offStyle'		=> __('info', 'robo-gallery' ),
	'bootstrap_style'=> 1,
	'after_row'		=> '
	<div class="menuTagOptions">'
));

$button_group->add_field( array(
	'name'             => __('Tags Ordering', 'robo-gallery' ),
	'id'               => ROBO_GALLERY_PREFIX . 'menuTagSort',
	'type'             => 'rbsradiobutton',
	'show_option_none' => false,
	'update'		=> '1.5',
	'default'          => '',
	'options'          => array(
		'' 		=> __( 'No ordering' , 		'robo-gallery' ),
		'asc' 	=> __( 'Alphabetical asc' , 'robo-gallery' ),
		'desc' 	=> __( 'Alphabetical desc' ,'robo-gallery' ),
	),
	'after_row'		=> '
	</div>',
));

$button_group->add_field( array(
	'name' 			=> __('Self Images', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'menuSelfImages',
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(1),
	'type' 			=> 'switch',
	'showhide'		=> 1,
	'bootstrap_style'=> 1,
));




$button_group->add_field( array(
	'name' 			=> __('Root Label', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'menuRoot',
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(1),
	'type' 			=> 'switch',
	'bootstrap_style'=> 1,
	'depends' 		=> 	'.rbs_menu_root_text',
	'showhide'		=> 1,
	'before_row'	=>'
	<div role="tabpanel">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#menu_label" aria-controls="menu_label" role="tab" data-toggle="tab">'.__('Menu Labels', 'robo-gallery' ).'</a></li>
				<li role="presentation"><a href="#menu_render" aria-controls="menu_render" role="tab" data-toggle="tab">'.__('Menu Style', 'robo-gallery' ).'</a></li>
				<li role="presentation"><a href="#menu_search" aria-controls="menu_search" role="tab" data-toggle="tab">'.__('Search', 'robo-gallery' ).'</a></li>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="menu_label"><br/>',
	'after_row'		=>'
					<div class="rbs_menu_root_text">',
));

$button_group->add_field( array(
    'name'    => __('Root Label Text','robo-gallery'),
    'default' => __('All', 'robo-gallery' ),
    'id'	  => ROBO_GALLERY_PREFIX .'menuRootLabel',
    'type'    => 'rbstext',
    'after_row'		=> '
					</div>',
));



$button_group->add_field( array(
	'name' 			=> __('Self Label', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'menuSelf',
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(1),
	'type' 			=> 'switch',
	'showhide'		=> 1,
	'bootstrap_style'=> 1,
));







$button_group->add_field( array(
	'name'             => __( 'Style', 'robo-gallery' ),
	'id'               => ROBO_GALLERY_PREFIX . 'buttonFill',
	'type'             => 'rbsselect',
	'show_option_none' => false,
	'level'			   => !ROBO_GALLERY_PRO,
	'default'          => 'border',
	'options'          => array(
		 'normal' 	=> __( 'Normal' , 	'cmb' ),
		 'flat' 	=> __( 'flat' , 	'cmb' ),
		 '3d'		=> __( '3d' , 		'cmb' ),
		 'border' 	=> __( 'Border' , 	'cmb' ),
	),
	 'before_row'=> 	'
	   			</div>
	        	<div role="tabpanel" class="tab-pane" id="menu_render"><br/>',
	
));

$button_group->add_field( array(
	'name'             => __( 'Color', 'robo-gallery' ),
	'id'               => ROBO_GALLERY_PREFIX . 'buttonColor',
	'type'             => 'rbsselect',
	'show_option_none' => false,
	'level'			   => !ROBO_GALLERY_PRO,
	'default'          => 'red',
	'options'          => array(
		'gray' 		=> __( 'gray' , 'cmb' ),
		'blue' 		=> __( 'blue' , 'cmb' ),
		'green' 	=> __( 'green' , 'cmb' ),
		'orange' 	=> __( 'orange' , 'cmb' ),
		'red' 		=> __( 'red' , 'cmb' ),
		'purple' 	=> __( 'purple' , 'cmb' ),
	),
));

$button_group->add_field( array(
	'name'             => __( 'Rounds', 'robo-gallery' ),
	'id'               => ROBO_GALLERY_PREFIX . 'buttonType',
	'type'             => 'rbsselect',
	'show_option_none' => false,
	'default'          => 'normal',
	'options'          => array(
		'normal' 	=> __( 'Normal' , 	'cmb' ),
		'rounded' 	=> __( 'Rounded' , 	'cmb' ),
		'pill' 		=> __( 'Pill' , 	'cmb' ),
		'circle' 	=> __( 'Circle ' , 	'cmb' ),
	),
));

$button_group->add_field( array(
	'name'             => __( 'Size', 'robo-gallery' ),
	'id'               => ROBO_GALLERY_PREFIX . 'buttonSize',
	'type'             => 'rbsselect',
	'show_option_none' => false,
	'default'          => 'normal',
	'options'          => array(
		'jumbo' 	=> __( 'Jumbo' , 	'cmb' ),
		'large' 	=> __( 'Large' , 	'cmb' ),
		'normal' 	=> __( 'Normal' , 	'cmb' ),
		'small' 	=> __( 'Small' , 	'cmb' ),
		'tiny' 		=> __( 'Tiny ' , 	'cmb' ),
	),
));

$button_group->add_field( array(
	'name'             => __( 'Align', 'robo-gallery' ),
	'id'               => ROBO_GALLERY_PREFIX . 'buttonAlign',
	'type'             => 'rbsselect',
	'show_option_none' => false,
	'default'          => 'left',
	'options'          => array(
		'left' 	=> __( 'Left' , 	'cmb' ),
		'center'=> __( 'Center' , 	'cmb' ),
		'right' => __( 'Right' , 	'cmb' ),
	),
));

$button_group->add_field( array(
	'name' 			=> __( 'Left Padding', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'paddingLeft',
	'type' 			=> 'slider',
	'bootstrap_style'=> 1,
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(5),
	'min'			=> 0,
	'addons'		=> 'px',
	'max'			=> 100,
));

$button_group->add_field( array(
	'name' 			=> __( 'Bottom Padding', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'paddingBottom',
	'type' 			=> 'slider',
	'bootstrap_style'=> 1,
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(10),
	'min'			=> 0,
	'max'			=> 100,
	'addons'		=> 'px',
	'after_row'		=> '
				</div>
				<div role="tabpanel" class="tab-pane" id="menu_search"><br/>'
));	


$button_group->add_field( array(
	'name' 			=> __('Search', 'robo-gallery' ),
	'id' 			=> ROBO_GALLERY_PREFIX . 'searchEnable',
	'default'		=> rbs_gallery_set_checkbox_default_for_new_post(0),
	'type' 			=> 'switch',
	'showhide'		=> 1,
	'bootstrap_style'=> 1,
	
));
$button_group->add_field( array(
    'name'    => __('Search Text','robo-gallery'),
    'default' => __('search', 'robo-gallery' ),
    'id'	  => ROBO_GALLERY_PREFIX .'searchLabel',
    'type'    => 'rbstext',
    'after_row'		=> '
				</div>
			</div>
		</div>
    </div>
</div>'
));