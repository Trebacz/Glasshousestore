<?php

if(isset($_POST['udswebsave'])){
	if(isset($_POST['udscat'])){
		update_option( 'udinra_image_sitemap_cat'    , 1 );
	}	
	else {
		update_option( 'udinra_image_sitemap_cat'    , 0 );
	}
	if(isset($_POST['udstag'])){
		update_option( 'udinra_image_sitemap_tag'    , 1 );
	}	
	else {
		update_option( 'udinra_image_sitemap_tag'    , 0 );
	}
	if(isset($_POST['udsauth'])){
		update_option( 'udinra_image_sitemap_auth'   , 1 );
	}	
	else {
		update_option( 'udinra_image_sitemap_auth'   , 0 );
	}

	if(isset($_POST['udinra_image_sitemap_post_type'])){
		update_option('udinra_image_sitemap_post_type' , implode("," , $_POST['udinra_image_sitemap_post_type']));
	}
	else {
		update_option('udinra_image_sitemap_post_type' , 'post,page');
	}

	if(isset($_POST['udinra_image_sitemap_exclude'])){
		update_option('udinra_image_sitemap_exclude' , $_POST['udinra_image_sitemap_exclude']);
	}
	else {
		update_option('udinra_image_sitemap_exclude' , '0');
	}
	
	if(isset($_POST['udscount'])) {
		update_option('udinra_image_sitemap_url_count' , $_POST['udscount']  );
	}
	else {
		update_option('udinra_image_sitemap_url_count' , 1000  );
	}
	switch ($_POST['udsfreq']) {
		case "dailyno":
			update_option( 'udinra_image_sitemap_freq' , 0 );
			break;
		case "daily":
			update_option( 'udinra_image_sitemap_freq' , 1 );
			break;
		case "always":
			update_option( 'udinra_image_sitemap_freq' , 2 );
			break;
		default:
			update_option( 'udinra_image_sitemap_freq' , 3 );
	}
	if (isset($_REQUEST['udscdn'])) {
		update_option('udinra_image_Sitemap_cdn',$_REQUEST['udscdn']);
	}
	else {
		update_option('udinra_image_sitemap_cdn',' ');
	}
	update_option('udinra_image_sitemap_response','Options Saved Successfully');
}

?>