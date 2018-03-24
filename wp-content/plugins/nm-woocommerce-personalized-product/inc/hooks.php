<?php
/**
 * handling all hooks callbacks in future
 * @since 8.0
 **/


// adding column in product list
function ppom_show_product_meta( $columns ){
    
    $columns['product_meta'] = __( 'Fields Meta', 'nm-personalizedproduct' );
    
    return $columns;
}
function ppom_product_meta_column( $column, $post_id ) {
    
    switch ( $column ) {

      case 'product_meta' :
          
            $product_meta_id = get_post_meta ( $post_id, '_product_meta_id', true );
            $ppom_settings_url = admin_url( 'options-general.php?page=nm-personalizedproduct');
            
            if ($product_meta_id != "" && $product_meta_id != 'None'){
                $product_meta = PPOM() -> get_product_meta($product_meta_id);
                $url_edit = add_query_arg(array('productmeta_id'=> $product_meta_id, 'do_meta'=>'edit'), $ppom_settings_url);
                echo sprintf(__('<a href="%s">%s</a>', 'nm-personalizedproduct'), $url_edit, $product_meta -> productmeta_name);
            }else{
                
                echo sprintf(__('<a class="btn button" href="%s">%s</a>', 'nm-personalizedproduct'), $ppom_settings_url, "Add Fields");
            }
            break;

    }
}

// since 8.1 - sending files in email as attachment
function ppom_send_files_in_email($files_moved, $order_id) {
    
    // if( ! $files_moved ) return;
    $attachments = array();
    foreach($files_moved as $files) {
    
        foreach($files as $product_id => $file_path) {
    
            if( ppom_send_file_in_attachment($product_id) ) {
                $attachments[] = $file_path;
            }
        }
            
    }
    
    if( ! $attachments ) return;
    
    $subject = sprintf( __("Files uploaded - Order %d", 'nm-personalizedproduct'), $order_id);
    $message = __("Following file(s) have been uploaded against this order", 'nm-personalizedproduct');
    $message = apply_filters('ppom_message_file_attachment', $message);
    
    $site_name = get_bloginfo('name');
    $site_admin = get_bloginfo('admin_email');
    
    $headers = "From: {$site_name} <$site_admin> \r\n";
    
    wp_mail($site_admin, $subject, $message, $headers, $attachments);
}