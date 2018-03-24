<?php

$udinra_gallery_id = 0;
$udinra_first_time = 0;

if (preg_match_all ("/ids=[\"](.*)[\" ]/U",$udinra_post->post_content, $udinra_matches_img, PREG_SET_ORDER)) {
	if ($udinra_first_time == 0) {
		$udinra_xml_image .= "\t"."<url>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."<loc>".htmlspecialchars(get_permalink($udinra_post->ID))."</loc>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."<lastmod>".get_post_modified_time('c',false,$udinra_post->ID)."</lastmod>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
		$udinra_first_time = 1;
	}
	$udinra_image_gallery_list = explode(',' , $udinra_matches_img[0][1]);
	foreach ($udinra_image_gallery_list as $udinra_image_gallery_image) { 
		$udinra_gallery_id = filter_var($udinra_image_gallery_image , FILTER_SANITIZE_NUMBER_INT); 
		$udinra_sql = "SELECT out1.post_title,out1.post_excerpt,pm.meta_value ".	
						" FROM $wpdb->posts out1 ".
						" INNER JOIN $wpdb->postmeta pm ".
						" ON out1.id = pm.post_id ".					
						" WHERE out1.id = $udinra_gallery_id " .
						" AND pm.meta_key = '_wp_attached_file' " ;
		$udinra_image_detail = $wpdb->get_results($udinra_sql);
		$udinra_xml_image .= "\t\t"."<image:image>".PHP_EOL;
		$udinra_xml_image .= "\t\t\t"."<image:loc>".htmlspecialchars($udinra_upload_dir_url . $udinra_image_detail[0]->meta_value)."</image:loc>".PHP_EOL;
		$udinra_image_detail[0]->post_title  = preg_replace("/\.[^$]*/","",$udinra_image_detail[0]->post_title);
		$udinra_image_detail[0]->description = preg_replace("/\.[^$]*/","",$udinra_image_detail[0]->post_excerpt);
		if ( ctype_space($udinra_image_detail[0]->post_excerpt) || $udinra_image_detail[0]->post_excerpt == '' ) {
			$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_image_detail[0]->post_title)."</image:caption>".PHP_EOL;
		}
		else {
			$udinra_xml_image .= "\t\t\t"."<image:caption>".htmlspecialchars($udinra_image_detail[0]->post_excerpt)."</image:caption>".PHP_EOL;
		}
		$udinra_xml_image .= "\t\t\t"."<image:title>".htmlspecialchars($udinra_image_detail[0]->post_title)."</image:title>".PHP_EOL;
		$udinra_xml_image .= "\t\t"."</image:image>".PHP_EOL;
			
		$udinra_alt_text_value = get_post_meta($udinra_gallery_id,'_wp_attachment_image_alt',true);
		if ( ctype_space($udinra_alt_text_value) || $udinra_alt_text_value == '' ) {
			add_post_meta($udinra_gallery_id,'_wp_attachment_image_alt',$udinra_image_detail[0]->post_title,true);
		}
	}
}
if ($udinra_first_time == 1) {
	$udinra_xml_image .= "\t"."</url>".PHP_EOL;
	$udinra_url_count = $udinra_url_count + 1;
}
?>
