<?php

$udinra_img_pluginurl = plugins_url();

if ( preg_match( '/^https/', $udinra_img_pluginurl ) && !preg_match( '/^https/', get_bloginfo('url') ) )
	$udinra_img_pluginurl = preg_replace( '/^https/', 'http', $udinra_img_pluginurl );

define( 'UDINRA_IMG_FRONT_URL', $udinra_img_pluginurl.'/' );
global $wpdb;

$udinra_index_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$udinra_index_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_IMG_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-index-sitemap.xsl'. '"'.'?>' .PHP_EOL;
$udinra_index_xml  .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

$udinra_index_sitemap_url = ABSPATH . '/sitemap-index-image.xml'; 
$udinra_date = Date(DATE_W3C);
$udinra_sitemap_response = '';
$udinra_sitemap_length = get_option(udinra_image_sitemap_url_count);
$udinra_sitemap_count = 0;
$udinra_image_multisite = get_option('udinra_image_sitemap_multisite');
$udinra_upload_dir = wp_upload_dir();
$udinra_upload_dir_url = $udinra_upload_dir['baseurl'] . '/';
$udinra_image_sitemap_cdn = get_option('udinra_image_sitemap_cdn');

if ($udinra_image_sitemap_cdn != '') {
	if(stristr($udinra_upload_dir_url , 'http://')) {
		$udinra_image_sitemap_cdn      = $udinra_image_sitemap_cdn . '.' ;
		$udinra_upload_dir_url = substr_replace($udinra_upload_dir_url , $udinra_image_sitemap_cdn ,7 , 0);
	}
	if(stristr($udinra_upload_dir_url , 'https://')) {
		$udinra_image_sitemap_cdn      = $udinra_image_sitemap_cdn . '.' ;
		$udinra_upload_dir_url = substr_replace($udinra_upload_dir_url , $udinra_image_sitemap_cdn ,8 , 0);
	}
}
$udinra_sitemap_post_str   = get_option( 'udinra_image_sitemap_post_type'  ) ;
$udinra_sitemap_post_array = explode( ',' , $udinra_sitemap_post_str );
$udinra_sitemap_post_type  = '';
foreach ( $udinra_sitemap_post_array AS $udinra_sitemap_post_element ){
	$udinra_sitemap_post_type .= "'" . $udinra_sitemap_post_element . "'," ;
}
$udinra_sitemap_post_type     = substr( $udinra_sitemap_post_type , 0 , -1) ;    

$udinra_image_sitemap_exclude = get_option('udinra_image_sitemap_exclude');

?>