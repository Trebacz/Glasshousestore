<?php
if ( $udinra_image_multisite == 1) {
	$udinra_first_time = 0;
	$udinra_url_count = 0;
}
else{
	
	$udinra_xml_image .= "</urlset>"; 
	$udinra_sitemap_count = $udinra_sitemap_count + 1;
	$udinra_image_sitemap_url = ABSPATH . '/sitemap-image-'.$udinra_sitemap_count.'.xml'; 

	if (file_put_contents ($udinra_image_sitemap_url, $udinra_xml_image)) {
		$udinra_tempurl = get_bloginfo('url').'/sitemap-image-'.$udinra_sitemap_count.'.xml'; 
		$udinra_index_xml .="\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
							  "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
	}	
	
	$udinra_first_time = 0;
	$udinra_url_count = 0;
	$udinra_xml_image = '';
}
?>