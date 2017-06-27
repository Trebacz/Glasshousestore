<?php


$udinra_sql = "SELECT max(out1.post_parent)	FROM $wpdb->posts out1
			 			WHERE out1.post_type = 'attachment'
						AND out1.post_mime_type like 'image%'
						AND EXISTS (
							SELECT 1 FROM $wpdb->posts in1
							WHERE in1.ID = out1.post_parent
							AND in1.post_status = 'publish'
							AND post_type NOT IN ('product_variation','dlm_download','dlm_download_version','bws-gallery','envira','foogallery'))
						ORDER BY out1.post_parent,out1.post_date asc";
$udinra_max_limit = $wpdb->get_var($udinra_sql);

$udinra_sql = "SELECT min(out1.post_parent)	FROM $wpdb->posts out1
			 			WHERE out1.post_type = 'attachment'
						AND out1.post_mime_type like 'image%'
						AND EXISTS (
							SELECT 1 FROM $wpdb->posts in1
							WHERE in1.ID = out1.post_parent
							AND in1.post_status = 'publish' 
							AND post_type NOT IN ('product_variation','dlm_download','dlm_download_version','bws-gallery','envira','foogallery'))
						ORDER BY out1.post_parent,out1.post_date asc";
$udinra_max_id = $wpdb->get_var($udinra_sql);


$result_length = 0;
$udinra_min_id = 0;
$udinra_max_id = $udinra_max_id - 1;
$udinra_limit_flag = 0;
$udinra_url_count = 0;
$udinra_cur_post_id = 0;
$udinra_prev_post_id = 0;		
$udinra_first_time = 0;
if ($udinra_image_multisite == 0) {
	$udinra_xml_image = '';
}

do {
	if ($result_length == 0) {
		$udinra_min_id = $udinra_max_id + 1;
		$udinra_max_id = $udinra_max_id + 100;
		if ($udinra_max_id > $udinra_max_limit && $udinra_limit_flag == 0) {
			$udinra_max_id = $udinra_max_limit;
			$udinra_limit_flag = 1;
		}
	}
	else {
		foreach ($udinra_posts as $udinra_post) { 
			if ($udinra_url_count == 0 && $udinra_image_multisite == 0) {
				$udinra_xml_image   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
				$udinra_xml_image  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_IMG_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-image-sitemap.xsl'. '"'.'?>' . PHP_EOL;
				$udinra_xml_image  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
			}
			include 'udinra_img_loop.php';
		}
		if ($udinra_url_count >= $udinra_sitemap_length) {
			include 'udinra_img_write.php';
		}
		$udinra_min_id = $udinra_max_id + 1;
		$udinra_max_id = $udinra_max_id + 100;
		if ($udinra_max_id > $udinra_max_limit && $udinra_limit_flag == 0) {
			$udinra_max_id = $udinra_max_limit;
			$udinra_limit_flag = 1;
		}
	}
	if ( $udinra_max_id <= $udinra_max_limit) {
		$udinra_sql = "SELECT out1.post_title,out1.post_excerpt,out1.post_parent,out1.guid,out1.id,pm.meta_value	
							FROM $wpdb->posts out1
							INNER JOIN $wpdb->postmeta pm
							ON out1.id = pm.post_id
				 			WHERE out1.post_type = 'attachment'
							AND out1.post_mime_type like 'image%'
							AND out1.post_parent BETWEEN $udinra_min_id AND $udinra_max_id
							AND pm.meta_key = '_wp_attached_file'
							AND EXISTS (
								SELECT 1 FROM $wpdb->posts in1
								WHERE in1.ID = out1.post_parent
								AND in1.post_status = 'publish' 
								AND post_type NOT IN ('product_variation','dlm_download','dlm_download_version','bws-gallery','envira','foogallery'))
							ORDER BY out1.post_parent,out1.post_date asc";
		$udinra_posts = $wpdb->get_results($udinra_sql);
		$result_length = count($udinra_posts);
	}
}While($udinra_max_id <= $udinra_max_limit);
if ($udinra_url_count > 0) {
	include 'udinra_img_write.php';
}
?>