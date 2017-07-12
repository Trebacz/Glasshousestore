<?php
/**
 * Plugin Name: WooCommerce Test Extension
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Test extension.
 * Version: 1.0.0
 * Author: David Trebacz
 * Author URI: https://www.trebacz.com/
 * Developer: David Trebacz
 * Developer URI: https://www.trebacz.com/
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

 /**
 * Check if WooCommerce is active
 **/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option(	 'active_plugins' ) ) ) ) {
    // Onlyt run if plugin doesn't exist
    if ( ! class_exists('WC_Example')){
    	class WC_Example{
    		public function __construct(){
    			// Print an admin notice on the screen
    			add_action( 'admin_notices', array($this , 'trebacz_admin_notice'));
    		}

    		// print admin notice
    		public function trebacz_admin_notice(){
    				?>
    			    <div class="notice notice-success is-dismissible">
    			    	<p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
    				</div>
    				<?php
    		}
    	}
    	$GLOBALS['wc_example'] = new WC_Example();
    }
}