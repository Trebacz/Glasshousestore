<?php

$post_id = get_post($udinra_post->ID);
if(get_option('udinra_image_sitemap_visual') == 1 ){
	$udinra_post->post_content = apply_filters( 'the_content_export', $post_id->post_content);
}

$udinra_image_found = 0;

$udinra_xml_image .= "\t"."<url>".PHP_EOL;
$udinra_xml_image .= "\t\t"."<loc>".htmlspecialchars(get_permalink($udinra_post->ID))."</loc>".PHP_EOL;
$udinra_xml_image .= "\t\t"."<lastmod>".get_post_modified_time('c',false,$udinra_post->ID)."</lastmod>".PHP_EOL;
if ( $udinra_post->post_type == 'page') {
	$udinra_xml_image .= "\t\t"."<priority>"."0.8"."</priority>".PHP_EOL;
}
elseif ($udinra_post->post_type == 'post') {
	$udinra_xml_image .= "\t\t"."<priority>"."0.6"."</priority>".PHP_EOL;
}
elseif ($udinra_post->post_type == 'product' or  $udinra_post->post_type == 'download' or  $udinra_post->post_type =='wpsc-product') {
	$udinra_xml_image .= "\t\t"."<priority>"."0.75"."</priority>".PHP_EOL;
}
else {
	$udinra_xml_image .= "\t\t"."<priority>"."0.70"."</priority>".PHP_EOL;
}

if (preg_match_all ("/<img(.*?)[>]/ui",$udinra_post->post_content, $udinra_matches, PREG_SET_ORDER)) {

	for ( $udinra_i = 0; $udinra_i < count($udinra_matches); $udinra_i++) {
		$udinra_ret_code  = preg_match_all ("/alt=[\'\"](.*?)[\'\"]/ui",$udinra_matches[$udinra_i][0], $udinra_matches_alt, PREG_SET_ORDER);
		$udinra_ret_code = preg_match_all ("/title=[\'\"](.*?)[\'\"]/ui",$udinra_matches[$udinra_i][0], $udinra_matches_title, PREG_SET_ORDER);
		$udinra_ret_code  = preg_match_all ("/src=[\'\"](.*?)[\'\"]/ui",$udinra_matches[$udinra_i][0], $udinra_matches_src, PREG_SET_ORDER);
		if(strrpos($udinra_matches_src[0][1] ,'x')) {
			$udinra_ret_code = strrpos($udinra_matches_src[0][1] ,'-');
			$udinra_ret_code1 = strrpos($udinra_matches_src[0][1] ,'.'); 
			$udinra_matches_src[0][1] = substr($udinra_matches_src[0][1] , 0 , $udinra_ret_code) . substr($udinra_matches_src[0][1] , $udinra_ret_code1);
			$udinra_ret_code = strrpos($udinra_matches_src[0][1] ,'/'); 
			$udinra_file_name = substr($udinra_matches_src[0][1],$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code);
		}
  		$udinra_xml_image .= "\t\t"."<image:image>".PHP_EOL;
		if ($udinra_image_sitemap_cdn != '') {
			if(stristr($udinra_matches_src[0][1] , 'http://')) {
				$udinra_matches_src[0][1] = substr_replace($udinra_matches_src[0][1] , $udinra_image_sitemap_cdn ,7 , 0);
			}
			if(stristr($udinra_matches_src[0][1] , 'https://')) {
				$udinra_matches_src[0][1] = substr_replace($udinra_matches_src[0][1] , $udinra_image_sitemap_cdn ,8 , 0);
			}
		}
		$udinra_xml_image .= "\t\t\t"."<image:loc>".htmlspecialchars(trim($udinra_matches_src[0][1]))."</image:loc>".PHP_EOL;
		if ( ctype_space($udinra_matches_alt[0][1]) || $udinra_matches_alt[0][1] == '' ) {
			$udinra_ret_code = strrpos($udinra_matches_src[0][1] ,'/'); 
			$udinra_ret_code1 = strrpos($udinra_matches_src[0][1] ,'.'); 
			$udinra_file_name = substr($udinra_matches_src[0][1],$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code);			
			$udinra_matches_alt[0][1] = $udinra_file_name;
		}
		else {
			$udinra_matches_alt[0][1] = preg_replace("/\.[^$]*/","",$udinra_matches_alt[0][1]);
		}
		$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_matches_alt[0][1])."</image:caption>".PHP_EOL;
		if ( ctype_space($udinra_matches_title[0][1]) || $udinra_matches_title[0][1] == '' ) {
			$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_matches_alt[0][1])."</image:title>".PHP_EOL;
		}
		else {
			$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_matches_title[0][1])."</image:title>".PHP_EOL;
		}
		$udinra_xml_image .= "\t\t"."</image:image>".PHP_EOL;
	}

	$udinra_post_thumbnail_url = get_the_post_thumbnail_url($udinra_post->ID);
	if($udinra_post_thumbnail_url){
		$udinra_post_thumbnail_alt = get_post_meta( get_post_thumbnail_id($udinra_post->ID), '_wp_attachment_image_alt', true );
		if ( ctype_space($udinra_post_thumbnail_alt) || $udinra_post_thumbnail_alt == '' ) {
			$udinra_ret_code1 = strrpos($udinra_post_thumbnail_url ,'.');
			$udinra_ret_code = strrpos($udinra_post_thumbnail_url ,'/'); 
			$udinra_file_name = substr($udinra_post_thumbnail_url,$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code);
			$udinra_post_thumbnail_alt = $udinra_file_name;
		}
		$udinra_xml_image .= "\t\t"."<image:image>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:loc>".htmlspecialchars(trim($udinra_post_thumbnail_url))."</image:loc>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_post_thumbnail_alt)."</image:caption>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_post_thumbnail_alt)."</image:title>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."</image:image>".PHP_EOL;
	}
	$udinra_image_found = 1;
}
if($udinra_image_found == 0) {
	$udinra_post_thumbnail_url = get_the_post_thumbnail_url($udinra_post->ID);
	if($udinra_post_thumbnail_url){
		$udinra_post_thumbnail_alt = get_post_meta( get_post_thumbnail_id($udinra_post->ID), '_wp_attachment_image_alt', true );
		if ( ctype_space($udinra_post_thumbnail_alt) || $udinra_post_thumbnail_alt == '' ) {
			$udinra_ret_code1 = strrpos($udinra_post_thumbnail_url ,'.');
			$udinra_ret_code = strrpos($udinra_post_thumbnail_url ,'/'); 
			$udinra_file_name = substr($udinra_post_thumbnail_url,$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code);
			$udinra_post_thumbnail_alt = $udinra_file_name;
		}
		$udinra_xml_image .= "\t\t"."<image:image>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:loc>".htmlspecialchars(trim($udinra_post_thumbnail_url))."</image:loc>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_post_thumbnail_alt)."</image:caption>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_post_thumbnail_alt)."</image:title>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."</image:image>".PHP_EOL;
	}
}
$udinra_xml_image .= "</url>\n"; 
$udinra_url_count = $udinra_url_count + 1;

?>