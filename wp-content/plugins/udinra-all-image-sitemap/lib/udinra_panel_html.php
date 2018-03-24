<?php

?>
<div class="w3-card-2" style="width:100%;">
	<div class="w3-container">
		<h2 class="w3-center w3-blue">Udinra Sitemap Configuration</h2>
		<div class="w3-show w3-border" id="commonSitemap" ><form action="" method="post">
			<ul class="w3-ul">
				<li><input <?php if (get_option('udinra_image_sitemap_cat') == 1) echo "checked"; ?> class="w3-check" type="checkbox" id="udscat" value="udscat" name="udscat">
					<label>Create Category Sitemap (Not Recommended if creating tag sitemap)</label>
				</li>
				<li><input <?php if (get_option('udinra_image_sitemap_tag') == 1) echo "checked"; ?> class="w3-check" type="checkbox" id="udstag" value="udstag" name="udstag">
					<label>Create Tag Sitemap      (Not Recommended if creating category sitemap)</label>
				</li>
				<li><input <?php if (get_option('udinra_image_sitemap_auth') == 1) echo "checked"; ?> class="w3-check" type="checkbox" id="udsauth" value="udsauth" name="udsauth">
					<label>Create Author Sitemap   (Not Recommended for single author sites)</label>
				</li>
				<?php
					foreach ( get_post_types( '', 'names' ) as $post_type ) {
						if( $post_type == 'attachment'          || $post_type == 'revision'         || $post_type == 'custom_css'         ||
							$post_type == 'nav_menu_item'       || $post_type == 'oembed_cache'     || $post_type == 'envira'             ||
							$post_type == 'foogallery'          || $post_type == 'bws-gallery'      || $post_type == 'ngg_gallery'        ||
							$post_type == 'ngg_pictures'        || $post_type == 'lightbox_library' || $post_type == 'dlm_download'       ||    
							$post_type == 'ml-slider'           || $post_type == 'ml-slide'         || $post_type == 'amn_envira-lite'    ||  
							$post_type == 'displayed_gallery'   || $post_type == 'display_type'     || $post_type == 'gal_display_source' ||  
							$post_type == 'customize_changeset' || $post_type == 'ngg_album'        || $post_type == 'dlm_download_version' ||
							$post_type == 'edd_payment'			|| $post_type == 'edd_discount'     || $post_type == 'edd_log'            ||
							$post_type == 'shop_order'			|| $post_type == 'shop_coupon'      || $post_type == 'shop_order_refund'  ||
							$post_type == 'shop_webhook'		|| $post_type == 'product_variation') {
						}
						else {
							$udinra_image_sitemap_post_type = 'udinra_image_sitemap_' . $post_type ;
							echo '<li>'              . '<input class="w3-check" type="checkbox" '  . 
								  udinra_image_sitemap_check_if_post_type_selected($post_type)           .
								 ' id=udinra_image_sitemap_post_type"'                                   . '" '   .
						 	 	 ' name=udinra_image_sitemap_post_type[]"'                               . '" '   .
							 	 ' value="'           . $post_type                                 . '" >'  .
	  					 	 	 ' <label> Include  ' . $post_type . ' in Sitemap</label>'         .  '</li>';
						}
					}
				?>
				<li><input id="udscount" name="udscount" class="w3-input w3-border" type="text" placeholder="Enter Number of URL per Sitemap (default is 1000, Upper Limit 20,000)" value="<?php echo get_option('udinra_image_sitemap_url_count'); ?>"></li>
				<li><input id="udsexclude" name="udsexclude" class="w3-input w3-border" type="text" placeholder="Enter ID of post or page to exclude from Sitemap (separated by ,)" value="<?php echo get_option('udinra_image_sitemap_exclude'); ?>"></li>
				<li><select id="udsfreq" name="udsfreq" class="UdinraSelect" style="background-color: lightyellow;">
					<option value="dailyno" selected disabled ?> >How frequently Sitemap should be generated?</option>
					<option value="daily"   <?php if (get_option('udinra_image_sitemap_freq') == 1) echo "selected"; ?> >Daily (Best if you have large website)    </option>
					<option value="always"  <?php if (get_option('udinra_image_sitemap_freq') == 2) echo "selected"; ?> >After page or post is changed / published </option>			
					<option value="both"    <?php if (get_option('udinra_image_sitemap_freq') == 3) echo "selected"; ?> >Both of above (default)                   </option>						
				</select></li>
			</ul>
			<input id="udscdn" name="udscdn" class="w3-input w3-border" type="text" placeholder="Enter CDN Name example cdn" value="<?php echo get_option('udinra_image_Sitemap_cdn'); ?>">						
			<input name="udswebsave"   id="udswebsave" value="Save Common Configuration Options"  type="submit" class="w3-button w3-ripple w3-block w3-blue w3-border w3-border-black" />
		</form></div>
		<form action="" method="post">
			<input name="udscreate" id="udscreate"  value="Create Sitemap Manually" type="submit" class="w3-button w3-ripple w3-block w3-orange w3-border w3-border-black" />			
		</form>
		<a href="http://wordpress.org/extend/plugins/udinra-all-image-sitemap/" class="w3-button w3-ripple w3-sand">Please rate this plugin on WordPress.org</a>
	</div>
	<footer class="w3-display-container w3-blue">
		<p><?php echo "<h3>" . get_option('udinra_image_sitemap_response') . "</h3>" ; ?></p>	
	</footer>	
</div>
<div class="w3-card-4" style="width:100%;">
	<div class="w3-container">
	<h2 class="w3-center w3-blue">Udinra Sitemap Pro Features</h2>
		<ul class="w3-ul w3-light-blue">
			<li>Get 25% discount on Pro version. Use coupon code FREE25 <a class="w3-large" href="https://udinra.com/downloads/sitemap-pro">Click Here</a></li>
			<li>Pro Plugin supports Web Sitemap, Image Sitemap, Video Sitemap and HTML Sitemap.</li>
			<li>Support for popular Gallery (e.g. NextGen) ,eCommerce (e.g. WooCommerce) and Slider (e.g. Meta Slider) plugins</li>
			<li>Support for WooCommerce Videos, YouTube, Dailymotion and Vimeo</li>
			<li>No need to use multiple Sitemap plugins. Use one for all sitemaps.</li>
		</ul>	
	</div>
</div>
<?php
/*********************************************************************************************************/
/* These set of functions are called by HTML section of the plugin                                       */
/*********************************************************************************************************/
/*
* This function shows the Post Types which will be included in the Sitemap
*/
function udinra_image_sitemap_check_if_post_type_selected($post_type){
	if (get_option('udinra_image_sitemap_post_type')) {
		$temp_post_type = get_option('udinra_image_sitemap_post_type')  ;
		if (strpos($temp_post_type , $post_type) !== false) {
			return " checked ";
		}
	} 
}

?>