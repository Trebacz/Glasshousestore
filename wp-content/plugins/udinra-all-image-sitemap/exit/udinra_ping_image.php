<?php

if ( $udinra_image_multisite == 1) {
	update_option('udinra_image_sitemap_response','<a href='.get_bloginfo('url'). '/sitemap-index-image.xml'.' target="_blank" title="Image Sitemap URL">View Image Sitemap</a> <br />Submit this sitemap to Google Search Console (Google Webmasters) and others Bing Webmasters.');
	$udinra_xml_image .= "</urlset>"; 
}
else {
	$udinra_index_xml .= "</sitemapindex>";	
	if (UdinraImageWritable(ABSPATH) || UdinraImageWritable($udinra_image_sitemap_url)) {
		file_put_contents ($udinra_index_sitemap_url, $udinra_index_xml);
		update_option('udinra_image_sitemap_response','<a href='.get_bloginfo('url'). '/sitemap-index-image.xml'.' target="_blank" title="Image Sitemap URL">View Image Sitemap</a> <br />Submit this sitemap to Google Search Console (Google Webmasters) and others Bing Webmasters.');
	}
	else {
		update_option('udinra_image_sitemap_response','<a href="https://udinra.com/blog/docs/image-sitemap-plugin" target="_blank" title="Plugin Documentation">Documentation Link</a>. Please read the documentation for solution.');
	}	
	if (is_wp_error(wp_remote_get( "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($udinra_index_sitemap_url) ))) {
	}
	if (is_wp_error(wp_remote_get( "http://www.bing.com/webmaster/ping.aspx?sitemap=" . urlencode($udinra_index_sitemap_url) ))) {
	}
}

?>