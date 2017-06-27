<?php
/*
Plugin Name: Robo Gallery Key
Plugin URI: http://robosoft.co
Description: A responsive, easy and elegant way to show gallery
Version: 1.4
Author: RoboSoft
Author URI: http://robosoft.co
License: GPL2
*/

if ( ! defined( 'WPINC' ) )  die;

define('ROBO_GALLERY_KEY', 1); 
define('ROBO_GALLERY_KEY_VERSION', '1.4'); 

if (!class_exists('roboGalleryParent')) {

	class roboGalleryParent{
		public $pro = 1 ;

		public function getTagsMenu($class='', $style=''){
			$retHtml = '';
			for ($i=0; $i < count($this->selectImages->tags); $i++) {
 				$retHtml .= '<a href="#" data-filter=".tag_id'.$i.'"'
						.' class="button '.$class.'"'
						.' '.($style?'style="'.$style.'"':'')
					.'>'.esc_attr($this->selectImages->tags[$i]).'</a>';
 			}
 			return $retHtml;
		}

		public function getOrderBy($size){
			if( count($size) && isset($size['orderby']) && $size['orderby'] ) 
				$this->orderby = $size['orderby'];
		}

		public function getSource($size){
			if( count($size) && isset($size['source']) && $size['source'] ) 
				$this->thumbsource = $size['source'];
		}

		public function getHoverBorder(){
			return $this->addBorder('hover-border-options');
		}

		public function getHoverShadown(){
			return $this->addShadow('hover-shadow-options');
		}

		public function setHoverType(){
			$this->hover = get_post_meta( $this->id,  ROBO_GALLERY_PREFIX.'hover', true );
			if($this->hover==2){
				$this->templateHover= get_post_meta( $this->id,  ROBO_GALLERY_PREFIX.'desc_template', true );
			}
		}

		public function getOverlayBg(){
			$background = get_post_meta( $this->id,  ROBO_GALLERY_PREFIX.'background', true );
			return 'background:'.$background.';';
		}

		public function setDescHover(){
			$this->descHover = $this->getTemplateItem( get_post_meta( $this->id,  ROBO_GALLERY_PREFIX.'showDesc', true ), 'rbsDesc', '@DESC@' );
		}

		public function	getMenuButton($optionName){
			$class = '';
			switch ( get_post_meta( $this->id,  ROBO_GALLERY_PREFIX.$optionName.'Fill', true ) ) {
	 			case 'flat': 	$class .= 'button-flat';	break;
	 			case '3d': 		$class .= 'button-3d'; 		break;
	 			case 'border': 	$class .= 'button-border'; 	break;
	 			case 'normal': default: $class .= 'button'; break;
	 		}

	 		switch ( get_post_meta( $this->id,  ROBO_GALLERY_PREFIX.$optionName.'Color', true ) ) {
	 			case 'blue': 	$class .= '-primary '; 	break;
	 			case 'green': 	$class .= '-action '; 	break;
	 			case 'orange': 	$class .= '-highlight '; break;
	 			case 'red': 	$class .= '-caution '; 	break;
	 			case 'purple': 	$class .= '-royal '; 	break;
	 			case 'gray': default: $class .= ' '; 	break;
	 		}
	 		return $class;
		}

		public function	setCCL(){
			$this->selectImages->lazyLoad = 999;
		}

		public function	setColumns(){
			$colums = get_post_meta( $this->id, ROBO_GALLERY_PREFIX.'colums', true );
			if(count($colums)){
				if( isset($colums['autowidth']) ){
					if($colums['colums']) $this->helper->setValue( 'columns',  $colums['colums'], 'int' );
					$this->helper->setValue( 'columnWidth', 'auto' );
				} elseif( isset($colums['width']) ) { 
					$this->helper->setValue( 'columnWidth',  $colums['width'], 'int' );
				}
				$resolutions=array( $this->addWidth($colums, 1), $this->addWidth($colums, 2), $this->addWidth($colums, 3) );
				if( count($resolutions) ){
					$this->helper->setValue( 'resolutions',  '['.implode( ' , ', $resolutions ).']', 'raw' );
				}
			}
		}

		public function setLightboxBg(){
			$lightboxBackground = get_post_meta( $this->id, ROBO_GALLERY_PREFIX.'lightboxBackground', true );
			if($lightboxBackground) $this->rbsLightboxStyle .=  'background-color: '.$lightboxBackground.';';
			$this->addJavaScriptStyle('rbsLightbox','body .mfp-ready.mfp-bg',2);
		}
		
	}
}

if(!function_exists('rbs_pro_widget')){
	function rbs_pro_widget($id){
		if( !empty( $id ) )  echo do_shortcode('[robo-gallery id="'.$id.'"]');
	}	
}
