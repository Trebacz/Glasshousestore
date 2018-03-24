<?php

function udinra_image_admin_notice() {
	global $current_user ;
	$user_id = $current_user->ID;
	if ( ! get_user_meta($user_id, 'udinra_image_admin_notice') ) {
		echo '<div class="notice notice-info"><p>'; 
		printf(__('<b>Best Sitemap plugin with XML, Image, Video, HTML Sitemap support.</b> <a href="https://udinra.com/downloads/sitemap-pro">Know More</a> | <a href="%1$s">Hide Notice</a>'), '?udinra_image_admin_ignore=0');
		echo "</p></div>";
	}
}

function udinra_image_admin_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	if ( isset($_GET['udinra_image_admin_ignore']) && '0' == $_GET['udinra_image_admin_ignore'] ) {
		add_user_meta($user_id, 'udinra_image_admin_notice', 'true', true);
	}
}

function UdinraImageWritable($udinra_filename) {
	if(!is_writable($udinra_filename)) {
		return false;
	}
	return true;
}

function udinra_image_post_unpublished( $new_status, $old_status,$post) {
	if (get_option('udinra_image_sitemap_post_type')) {
		$temp_post_type = get_option('udinra_image_sitemap_post_type');
		if(strpos($temp_post_type , $post->post_type) !== false){
			if(get_option('udinra-image-sitemap-freq') != 1) {
				if ( $old_status !== 'publish'  &&  $new_status == 'publish')  {	
					udinra_image_sitemap_loop($udinra_sitemap_response);
				}
				if ( $old_status == 'publish'  &&  $new_status == 'publish') {
					udinra_image_sitemap_loop($udinra_sitemap_response);
				}
			}
		}
	}
}

function udinra_image_event() {
	if(get_option('udinra-image-sitemap-freq') != 0) {
		udinra_image_sitemap_loop($udinra_sitemap_response);
	}
}

?>