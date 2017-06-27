<?php

$udinra_cur_post_id = $udinra_post->post_parent;
if($udinra_cur_post_id != 0) {
	if($udinra_cur_post_id != $udinra_prev_post_id) {
		$udinra_post_url = get_permalink($udinra_cur_post_id);
		if($udinra_first_time == 1) {
			$udinra_xml_image .= "\t"."</url>"."\n"; 
			$udinra_first_time = 0;
		}
		$udinra_xml_image .= "\t"."<url>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."<loc>".htmlspecialchars($udinra_post_url)."</loc>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."<lastmod>".get_post_modified_time('c',false,$udinra_cur_post_id)."</lastmod>".PHP_EOL;

		if ( get_post_type($udinra_cur_post_id) == 'page') {
			$udinra_xml_image .= "\t\t"."<priority>"."0.8"."</priority>".PHP_EOL;
		}
		elseif (get_post_type($udinra_cur_post_id) == 'post') {
			$udinra_xml_image .= "\t\t"."<priority>"."0.6"."</priority>".PHP_EOL;
		}
		else {
			$udinra_xml_image .= "\t\t"."<priority>"."0.70"."</priority>".PHP_EOL;
		}
					
		$udinra_xml_image .= "\t\t"."<image:image>".PHP_EOL;
		$udinra_post->post_title = preg_replace("/\.[^$]*/","",$udinra_post->post_title);
		$udinra_xml_image .= "\t\t\t"."<image:loc>".htmlspecialchars($udinra_upload_dir_url . $udinra_post->meta_value)."</image:loc>".PHP_EOL;
		if ( ctype_space($udinra_post->post_excerpt) || $udinra_post->post_excerpt == '' ) {
			$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_post->post_title)."</image:caption>".PHP_EOL;
		}
		else {
			$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_post->post_excerpt)."</image:caption>".PHP_EOL;
		}
		$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_post->post_title)."</image:title>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."</image:image>".PHP_EOL;
			
		$udinra_alt_text_value = get_post_meta($udinra_post->id,'_wp_attachment_image_alt',true);
		if ( ctype_space($udinra_alt_text_value) || $udinra_alt_text_value == '' ) {
			add_post_meta($udinra_post->id,'_wp_attachment_image_alt',$udinra_post->post_title,true);
		}
		$udinra_first_time = 1;
		$udinra_prev_post_id = $udinra_cur_post_id;
		$udinra_url_count = $udinra_url_count + 1;
	}
	else {
		$udinra_xml_image .= "\t\t"."<image:image>".PHP_EOL;
		$udinra_post->post_title = preg_replace("/\.[^$]*/","",$udinra_post->post_title);
		$udinra_xml_image .= "\t\t\t"."<image:loc>".htmlspecialchars($udinra_upload_dir_url . $udinra_post->meta_value)."</image:loc>".PHP_EOL;
		if ( ctype_space($udinra_post->post_excerpt) || $udinra_post->post_excerpt == '' ) {
			$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_post->post_title)."</image:caption>".PHP_EOL;
		}
		else {
			$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_post->post_excerpt)."</image:caption>".PHP_EOL;
		}
		$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_post->post_title)."</image:title>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."</image:image>".PHP_EOL;
					
		$udinra_alt_text_value = get_post_meta($udinra_post->id,'_wp_attachment_image_alt',true);
		if ( ctype_space($udinra_alt_text_value) || $udinra_alt_text_value == '' ) {
			add_post_meta($udinra_post->id,'_wp_attachment_image_alt',$udinra_post->post_title,true);
		}	
	}
} 


?>