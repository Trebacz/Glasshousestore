<?php

if ( $udinra_image_multisite == 0) {
    $udinra_xml_image   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $udinra_xml_image  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_IMG_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-image-sitemap.xsl'. '"'.'?>' . PHP_EOL;
    $udinra_xml_image  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;        
}

$udinra_sitemap_all_users = get_users('orderby=post_count&order=DESC');
$udinra_sitemap_users = array();

foreach($udinra_sitemap_all_users as $udinra_sitemap_currentUser) {
    if(!in_array( 'subscriber', $udinra_sitemap_currentUser->roles )) {
            $udinra_sitemap_users[] = $udinra_sitemap_currentUser;
    }
}
     
foreach( $udinra_sitemap_users as $udinra_sitemap_user ) {
	$udinra_sql =   " SELECT MAX(p.post_modified_gmt) AS lastmod " .
					" FROM	$wpdb->posts AS p " .
					" INNER JOIN $wpdb->users AS u " .
					" ON p.post_author = u.ID " .
					" AND u.ID = $udinra_author_id " .
					" WHERE	p.post_status IN ('publish','inherit') " .
					" AND p.post_password = '' ";
	
	$udinra_update_time = mysql2date('c',$wpdb->get_var($udinra_sql),false);	    
    $udinra_xml_image .= "\t"."<url>".PHP_EOL;
	$udinra_xml_image .= "\t\t"."<loc>".htmlspecialchars(get_author_posts_url( $udinra_sitemap_user->ID ))."</loc>".PHP_EOL;
	$udinra_xml_image .= "\t\t"."<lastmod>".$udinra_update_time."</lastmod>".PHP_EOL;
	$udinra_xml_image .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
    $udinra_xml_image .= "\t"."</url>".PHP_EOL;
}     
if ( $udinra_image_multisite == 0) {
    $udinra_xml_image .= "</urlset>"; 
    $udinra_image_sitemap_url = ABSPATH . '/sitemap-image-author.xml'; 
    
    if (file_put_contents ($udinra_image_sitemap_url, $udinra_xml_image)) {
        $udinra_tempurl    = get_bloginfo('url').'/sitemap-image-author.xml'; 
        $udinra_index_xml .=  "\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
                              "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
    } 	        
}


?>