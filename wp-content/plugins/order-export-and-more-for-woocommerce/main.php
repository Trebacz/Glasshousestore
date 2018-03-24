<?php
/*
Plugin Name: WooCommerce Order Export and More
Plugin URI: http://www.jem-products.com
Description: Export your woocommerce orders and more with this free plugin
Version: 2.0.2
Author: JEM Plugins
Author URI: http://www.jem-products.com
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define ( 'JEM_EXP_PLUGIN_PATH' , plugin_dir_path( __FILE__ ) );
define('JEM_EXP_DOMAIN', 'jem-woocommerce-exporter');
define( 'JEM_EXP_URL', plugin_dir_url( __FILE__ ) );

//only proceed if we are in admin mode!
if( ! is_admin() ){
	return;
}

//Globals
global $jem_export_globals;

// add option while plugin acivated
function order_export_more_plugin_activation() {
    //add options for different tab
    $product_option_array = Array('Product ID' => 1,'Product SKU' => 2,'Parent ID' => 3,'Parent SKU' => 4,'Product Name' => 5,'Product Type' => 6,'Shipping Class' => 7,'Width' => 8,'Length' => 9,'Height' => 10,'Managing Stock' => 11,'In Stock' => 12,'Qty In Stock' => 13,'Downloadable' => 14,'Tax Status' => 15,'Tax Class' => 16,'Featured Product' => 17,'Price' => 18,'Sale Price' => 19,'Sale Start Date' => 20,'Sale End Date' => 21);
    $order_option_array =  Array('Order ID' => 1,'Order Date' => 2,'Order Status' => 3,'Customer Name' => 4,'Customer Email' => 5,'Order Total' => 6,'Order Shipping' => 7,'Order Shipping Tax' => 8,'Shipping Address Line 1' => 9,'Shipping Address Line 2' => 10,'Shipping City' => 11,'Shipping State' => 12,'Shipping Zip/Postcode' => 13,'Shipping Country' => 14,'Product Name' => 15,'Quantity of items purchased' => 16,'Item price EXCL. tax' => 17,'Item tax' => 18,'Item price INCL. tax' => 19,'Product Variations' => 20,'Order Currency' => 21,'Order Discount' => 22,'Coupon Code' => 23,'Payment Gateway' => 24,'Shipping Method' => 25,'Shipping Weight' => 26,'Customer Message' => 27,'Billing Address Line 1' => 28,'Billing Address Line 2' => 29,'Billing City' => 30,'Billing State' => 31,'Billing Zip/Postcode' => 32,'Billing Country' => 33,'Billing Phone Number' => 34);
    $customer_option_array =  Array('User ID' => 1,'User Name' => 2,'Billing First Name' => 3,'Billing Last Name' => 4,'Billing Company' => 5,'Billing Address 1' => 6,'Billing Address 2' => 7,'Billing City' => 8,'Billing State' => 9,'Billing Zipcode/Postcode' => 10,'Billing Country' => 11,'Billing Phone Number' => 12,'Billing Email Address' => 13,'Shipping First Name' => 14,'Shipping Last Name' => 15,'Shipping Company' => 16,'Shipping Address 1' => 17,'Shipping Address 2' => 18,'Shipping City' => 19,'Shipping State' => 20,'Shipping Zipcode/Postcode' => 21,'Shipping Country' => 22,'# Orders Placed' => 23,'Total Spent' => 24);
    $shipping_option_array = Array('Shipping Class ID' => 1,'Shipping Class Name' => 2,'Shipping Class Description' => 3,'Shipping Class Slug' => 4,'# Times Used' => 5);
    $coupons_option_array = Array('Coupon Code' => 1,'Coupon Description' => 2,'Discount Type' => 3,'Coupon Amount' => 4,'Allow Free Shipping' => 5,'Coupon Expiry Date' => 6,'Minimum Spend' => 7,'Maximum Spend' => 8,'Individual Use Only' => 9,'Eclude Sale Items' => 10,'Products' => 11,'Exclude Products' => 12,'Product categories' => 13,'Exclude Product categories' => 14,'Email Restrictions' => 15,'Usage Limit per Coupon' => 16,'Usage Limit per User' => 17);
    $categories_option_array = Array('Term ID' => 1,'Category Name' => 2,'Category Description' => 3,'Category Slug' => 4,'Parent ID' => 5,'Display Type' => 6,'Thumbnail Image' => 7);
    $tags_option_array = Array('Term ID' => 1,'Tag Name' => 2,'Tag Description' => 3,'Tag Slug' => 4);
    //encode different option array
    $encoded_product_option = json_encode($product_option_array);
    $encoded_order_option = json_encode($order_option_array);
    $encoded_customer_option = json_encode($customer_option_array);
    $encoded_shipping_option = json_encode($shipping_option_array);
    $encoded_coupons_option = json_encode($coupons_option_array);
    $encoded_categories_option = json_encode($categories_option_array);
    $encoded_tags_option = json_encode($tags_option_array);
    //update different option array
    update_option('product_option', $encoded_product_option);
    update_option('order_option', $encoded_order_option);
    update_option('customer_option', $encoded_customer_option);
    update_option('shipping_option', $encoded_shipping_option);
    update_option('coupons_option', $encoded_coupons_option);
    update_option('categories_option', $encoded_categories_option);
    update_option('tags_option', $encoded_tags_option);
}
register_activation_hook( __FILE__, 'order_export_more_plugin_activation' );

// add option while plugin upgraded
function order_export_more_upgrade_process_completed($upgrader_object, $options) {
    // Get the path of our plugin's main file
    $our_plugin = plugin_basename(__FILE__);
    // If an update has taken place and the updated type is plugins and the plugins element exists
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
        // Iterate through the plugins being updated and check if ours is there
        foreach ($options['plugins'] as $plugin) {
            if ($plugin == $our_plugin) {
                $product_option_array = Array('Product ID' => 1, 'Product SKU' => 2, 'Parent ID' => 3, 'Parent SKU' => 4, 'Product Name' => 5, 'Product Type' => 6, 'Shipping Class' => 7, 'Width' => 8, 'Length' => 9, 'Height' => 10, 'Managing Stock' => 11, 'In Stock' => 12, 'Qty In Stock' => 13, 'Downloadable' => 14, 'Tax Status' => 15, 'Tax Class' => 16, 'Featured Product' => 17, 'Price' => 18, 'Sale Price' => 19, 'Sale Start Date' => 20, 'Sale End Date' => 21);
                $order_option_array = Array('Order ID' => 1, 'Order Date' => 2, 'Order Status' => 3, 'Customer Name' => 4, 'Customer Email' => 5, 'Order Total' => 6, 'Order Shipping' => 7, 'Order Shipping Tax' => 8, 'Shipping Address Line 1' => 9, 'Shipping Address Line 2' => 10, 'Shipping City' => 11, 'Shipping State' => 12, 'Shipping Zip/Postcode' => 13, 'Shipping Country' => 14, 'Product Name' => 15, 'Quantity of items purchased' => 16, 'Item price EXCL. tax' => 17, 'Item tax' => 18, 'Item price INCL. tax' => 19, 'Product Variations' => 20, 'Order Currency' => 21, 'Order Discount' => 22, 'Coupon Code' => 23, 'Payment Gateway' => 24, 'Shipping Method' => 25, 'Shipping Weight' => 26, 'Customer Message' => 27, 'Billing Address Line 1' => 28, 'Billing Address Line 2' => 29, 'Billing City' => 30, 'Billing State' => 31, 'Billing Zip/Postcode' => 32, 'Billing Country' => 33, 'Billing Phone Number' => 34);
                $customer_option_array = Array('User ID' => 1, 'User Name' => 2, 'Billing First Name' => 3, 'Billing Last Name' => 4, 'Billing Company' => 5, 'Billing Address 1' => 6, 'Billing Address 2' => 7, 'Billing City' => 8, 'Billing State' => 9, 'Billing Zipcode/Postcode' => 10, 'Billing Country' => 11, 'Billing Phone Number' => 12, 'Billing Email Address' => 13, 'Shipping First Name' => 14, 'Shipping Last Name' => 15, 'Shipping Company' => 16, 'Shipping Address 1' => 17, 'Shipping Address 2' => 18, 'Shipping City' => 19, 'Shipping State' => 20, 'Shipping Zipcode/Postcode' => 21, 'Shipping Country' => 22, '# Orders Placed' => 23, 'Total Spent' => 24);
                $shipping_option_array = Array('Shipping Class ID' => 1, 'Shipping Class Name' => 2, 'Shipping Class Description' => 3, 'Shipping Class Slug' => 4, '# Times Used' => 5);
                $coupons_option_array = Array('Coupon Code' => 1, 'Coupon Description' => 2, 'Discount Type' => 3, 'Coupon Amount' => 4, 'Allow Free Shipping' => 5, 'Coupon Expiry Date' => 6, 'Minimum Spend' => 7, 'Maximum Spend' => 8, 'Individual Use Only' => 9, 'Eclude Sale Items' => 10, 'Products' => 11, 'Exclude Products' => 12, 'Product categories' => 13, 'Exclude Product categories' => 14, 'Email Restrictions' => 15, 'Usage Limit per Coupon' => 16, 'Usage Limit per User' => 17);
                $categories_option_array = Array('Term ID' => 1, 'Category Name' => 2, 'Category Description' => 3, 'Category Slug' => 4, 'Parent ID' => 5, 'Display Type' => 6, 'Thumbnail Image' => 7);
                $tags_option_array = Array('Term ID' => 1, 'Tag Name' => 2, 'Tag Description' => 3, 'Tag Slug' => 4);
                //encode different option array
                $encoded_product_option = json_encode($product_option_array);
                $encoded_order_option = json_encode($order_option_array);
                $encoded_customer_option = json_encode($customer_option_array);
                $encoded_shipping_option = json_encode($shipping_option_array);
                $encoded_coupons_option = json_encode($coupons_option_array);
                $encoded_categories_option = json_encode($categories_option_array);
                $encoded_tags_option = json_encode($tags_option_array);
                //update different option array
                update_option('product_option', $encoded_product_option);
                update_option('order_option', $encoded_order_option);
                update_option('customer_option', $encoded_customer_option);
                update_option('shipping_option', $encoded_shipping_option);
                update_option('coupons_option', $encoded_coupons_option);
                update_option('categories_option', $encoded_categories_option);
                update_option('tags_option', $encoded_tags_option); //                
            }
        }
    }
}

add_action('upgrader_process_complete', 'order_export_more_upgrade_process_completed', 10, 2);

function order_export_more_plugin_deactivation() {
  delete_option( 'product_option' );
  delete_option( 'order_option' );
  delete_option( 'customer_option' );
  delete_option( 'shipping_option' );
  delete_option( 'coupons_option' );
  delete_option( 'categories_option' );
  delete_option( 'tags_option' );
}
register_deactivation_hook( __FILE__, 'order_export_more_plugin_deactivation' );

$entities = array();
$entities[] = "Product";
$entities[] = "Order";
$entities[] = "Customer";
$entities[] = "Shipping";
$entities[] = "Coupon";
$entities[] = "Categories";
$entities[] = "Tags";

//Create an array of which entities are active
$active = array();
$active["Product"] = true;
$active["Order"] = true;

$jem_export_globals['entities'] = $entities;
$jem_export_globals['active'] = $active;

//Include the basic stuff
include_once(JEM_EXP_PLUGIN_PATH . 'inc/jem-exporter.php');
include_once(JEM_EXP_PLUGIN_PATH . 'inc/BaseEntity.php');

//include the entities
foreach($jem_export_globals['entities'] as $entity){
	include_once(JEM_EXP_PLUGIN_PATH . 'inc/' . $entity . '.php');

}

/**
 * Loads the right js & css assets
*/
function load_jem_exp_scripts(){

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');


 	//Need the jquery CSS files
	global $wp_scripts;
	$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
	// Admin styles for WC pages only
	wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
	wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
	
	
	wp_enqueue_style('dashicons');
		
	wp_enqueue_script( 'jem-css',  plugin_dir_url( __FILE__ ). 'js/main.js' );
	wp_enqueue_style( 'jem-css',  plugin_dir_url( __FILE__ ). 'css/jem-export-lite.css' );
}


add_action('admin_enqueue_scripts', 'load_jem_exp_scripts');

$jem_exporter_lite = new JEM_export_lite();

//=========   function for ajax call
function ajax_call_for_save_sorting(){
?>
<script type="text/javascript">

        jQuery(".sortable_table").sortable({
            update: function (ev, tbody) {
            var current_table_name = jQuery('.checkbox-class:checked').val();
            var obj = {};
            var counter = 0;
            jQuery('.'+current_table_name+' > tbody  > tr').each(function() {
                    if(jQuery(this).attr('data-key') != ""){
                    var get_place_holder = jQuery(this).attr('data-key');}
//                   console.log(get_place_holder);
                    if(get_place_holder != "undefined"){
                        counter++;
                        obj[get_place_holder] = counter;
                    }
                    
            });
            var form_data = {
                         action : 'savefieldorder',
                         pass_obj : obj,
                         pass_current_table_name : current_table_name,
                 };
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: form_data,
                    success: function(data) {
                    }
                });
            }
        });
        
</script>
<?php
}
//add_action('admin_footer','ajax_call_for_save_sorting');

function saving_field_order()
{
    global $post;
    $get_obj = $_POST['pass_obj'];
    $obj_product = json_encode($get_obj);
    $get_current_table_name = $_POST['pass_current_table_name'];
    if($get_current_table_name == 'Product'){
        update_option('product_option', $obj_product);
    }
    if($get_current_table_name == 'Order'){
        update_option('order_option', $obj_product);
    }
    if($get_current_table_name == 'Customer'){
        update_option('customer_option', $obj_product);
    }
    if($get_current_table_name == 'Shipping'){
        update_option('shipping_option', $obj_product);
    }
    if($get_current_table_name == 'Coupons'){
        update_option('coupons_option', $obj_product);
    }
    if($get_current_table_name == 'Categories'){
        update_option('categories_option', $obj_product);
    }
    if($get_current_table_name == 'Tags'){
        update_option('tags_option', $obj_product);
    }
    exit;
}
add_action('wp_ajax_savefieldorder', 'saving_field_order');
add_action('wp_ajax_nopriv_savefieldorder', 'saving_field_order');

?>