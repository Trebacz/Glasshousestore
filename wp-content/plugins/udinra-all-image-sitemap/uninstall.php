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
	delete_option('udinra_image_sitemap_freq');
	delete_option('udinra_image_sitemap_count');
	delete_option('udinra_image_sitemap_index');
	delete_option('udinra_image_sitemap_ping');
	delete_option('udinra_image_sitemap_multisite');
}

?>