<?php

$udinra_img_pluginurl = plugins_url();

if ( preg_match( '/^https/', $udinra_img_pluginurl ) && !preg_match( '/^https/', get_bloginfo('url') ) )
	$udinra_img_pluginurl = preg_replace( '/^https/', 'http', $udinra_img_pluginurl );

define( 'UDINRA_IMG_FRONT_URL', $udinra_img_pluginurl.'/' );
global $wpdb;

$udinra_index_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$udinra_index_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_IMG_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-index-sitemap.xsl'. '"'.'?>' .PHP_EOL;
$udinra_index_xml  .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
$udinra_index_flag = get_option('udinra_image_sitemap_index');

if($udinra_index_flag == 1){
	$udinra_index_sitemap_url = ABSPATH . '/sitemap-image.xml'; 
}
else {
	$udinra_index_sitemap_url = ABSPATH . '/sitemap-index-image.xml'; 
}

$udinra_date = Date(DATE_W3C);
$udinra_sitemap_response = '';
$udinra_temp_length = get_option('udinra_image_sitemap_count');

if($udinra_temp_length == 0){
	$udinra_sitemap_length = 1000;
}
else {
	$udinra_sitemap_length = $udinra_temp_length;
}
$udinra_sitemap_count = 0;
$udinra_image_multisite = get_option('udinra_image_sitemap_multisite');
$udinra_upload_dir = wp_upload_dir();
$udinra_upload_dir_url = $udinra_upload_dir['baseurl'] . '/';


?>