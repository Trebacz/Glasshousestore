<?php

$udinra_sql = "SELECT max(ID) FROM $wpdb->posts out1 WHERE out1.post_status = 'publish' AND out1.post_type IN ($udinra_sitemap_post_type)";
$udinra_max_limit = $wpdb->get_var($udinra_sql);

$udinra_sql = "SELECT min(ID) FROM $wpdb->posts out1 WHERE out1.post_status = 'publish' AND out1.post_type IN ($udinra_sitemap_post_type)";
$udinra_max_id = $wpdb->get_var($udinra_sql);
$result_length = 0;
$udinra_min_id = 0;
$udinra_max_id = $udinra_max_id - 1;
$udinra_limit_flag = 0;
$udinra_url_count = 0;
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
			include 'udinra_imgimages.php';
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
		$udinra_sql = "SELECT ID,post_content,post_title,post_type FROM $wpdb->posts out1 WHERE out1.post_status = 'publish' " . 
						" AND out1.post_type IN ($udinra_sitemap_post_type)" .
						" AND out1.id NOT IN ($udinra_image_sitemap_exclude) " .
						" AND out1.ID BETWEEN $udinra_min_id AND $udinra_max_id";
		$udinra_posts = $wpdb->get_results($udinra_sql);
		$result_length = count($udinra_posts);
	}
}While($udinra_max_id <= $udinra_max_limit);
if ($udinra_url_count > 0) {
	include 'udinra_img_write.php';
}
?>