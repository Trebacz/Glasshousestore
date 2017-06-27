<?php

if ( $udinra_image_multisite == 1) {
	$udinra_sitemap_response = '<a href='.get_bloginfo('url'). '/sitemap-index-image.xml'.' target="_blank" title="Image Sitemap URL">View Image Sitemap</a> <br />Submit this sitemap to Google Search Console (Google Webmasters) and others Bing Webmasters.';
	$udinra_xml_image .= "</urlset>"; 
}
else {
	$udinra_index_xml .= "</sitemapindex>";	
	if (UdinraImageWritable(ABSPATH) || UdinraImageWritable($udinra_image_sitemap_url)) {
		file_put_contents ($udinra_index_sitemap_url, $udinra_index_xml);
		if($udinra_index_flag == 1){
			$udinra_sitemap_response = '<a href='.get_bloginfo('url'). '/sitemap-image.xml'.' target="_blank" title="Image Sitemap URL">View Image Sitemap</a> <br />Submit this sitemap to Google Search Console (Google Webmasters) and others Bing Webmasters.';			
		}
		else {
			$udinra_sitemap_response = '<a href='.get_bloginfo('url'). '/sitemap-index-image.xml'.' target="_blank" title="Image Sitemap URL">View Image Sitemap</a> <br />Submit this sitemap to Google Search Console (Google Webmasters) and others Bing Webmasters.';
		}
	}
	else {
		$udinra_sitemap_response = 'The file sitemap-index-image.xml is not writable please check permission of the file for more details visit <a href="https://udinra.com/docs/category/image-sitemap-pro">Plugin FAQ</a>';
	}
	if (get_option('udinra_image_sitemap_ping') == 1) {
		if (is_wp_error(wp_remote_get( "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($udinra_index_sitemap_url) ))) {
		}
		if (is_wp_error(wp_remote_get( "http://www.bing.com/webmaster/ping.aspx?sitemap=" . urlencode($udinra_index_sitemap_url) ))) {
		}
	}
	return $udinra_sitemap_response;
}
?>