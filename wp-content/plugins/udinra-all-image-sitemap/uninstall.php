<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

udinra_uninstall_image_plugin();

function udinra_uninstall_image_plugin () {
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
        if ( false == is_super_admin() ) {
            return;
        }
        $blogs = wp_get_sites();
        foreach ( $blogs as $blog ) {
            switch_to_blog( $blog[ 'blog_id' ] );
            udinra_delete_image_options();
            restore_current_blog();
        }
    } else {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }
		udinra_delete_image_options();
	}
}

function udinra_delete_image_options () {
	delete_option('udinra_image_sitemap_cat');
	delete_option('udinra_image_sitemap_tag');
	delete_option('udinra_image_sitemap_auth');
	delete_option('udinra_image_sitemap_post_type');	
	delete_option('udinra_image_sitemap_exclude');
	delete_option('udinra_image_sitemap_url_count');
	delete_option('udinra_image_sitemap_freq');
	delete_option('udinra_image_sitemap_response');
    delete_option('udinra_image_Sitemap_cdn');
    delete_option('udinra_image_sitemap_multisite');
}

?>