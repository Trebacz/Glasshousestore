<?php 
/*
Plugin Name: LH Archived Post Status
Plugin URI: https://lhero.org/plugins/lh-archived-post-status/
Description: Creates an archived post status. Content can be excluded from the main loop and feed (but visible with a message), or hidden entirely
Version: 2.16
Author: Peter Shaw
Author URI: https://shawfactor.com/
License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2017  Peter Shaw  (email : pete@localhero.biz)
*/

class LH_archived_post_status_plugin {

var $newstatusname = 'archive';
var $newstatuslabel = 'archived';
var $newstatuslabel_count = 'Archived <span class="count">(%s)</span>';
var $title_label_field_name = 'lh_archive_post_status-title_label';
var $message_field_name = 'lh_archive_post_status_message';
var $hidden_field_name = 'lh_archive_post_status-submit_hidden';
var $publicly_available = 'public';
var $opt_name = 'lh_archive_post_status_options';
var $namespace = 'lh_archive_post_status';

var $filename;
var $options;

private function make_status_consistent_with_expiration($post_object, $expiration) { 

//check to see if is currently archived but expiration is in the future, if so publish it

if((strtotime($expiration) > strtotime('today midnight')) and ($post_object->post_status == $this->newstatusname)) {

$my_post = array(
      'ID'           => $post_object->ID,
      'post_status'   => 'publish'
  );

// Update the post into the database
  wp_update_post( $my_post );


//check to see if is currently published but expiration is in the past, if so archive it

} elseif ((strtotime($expiration) < strtotime('today midnight')) and ($post_object->post_status == 'publish')) {


$my_post = array(
      'ID'           => $post_object->ID,
      'post_status'   => $this->newstatusname
  );

// Update the post into the database
  wp_update_post( $my_post );


}


}




private function archive_post_by_id($postid) { 

$update = array( 'ID' => $postid, 'post_status' => $this->newstatusname );

wp_update_post($update);

}


private function dateFormatTojQueryUIDatePickerFormat($dateFormat) { 

    $chars = array( 
        // Day
        'd' => 'dd', 'j' => 'd', 'l' => 'DD', 'D' => 'D',
        // Month 
        'm' => 'mm', 'n' => 'm', 'F' => 'MM', 'M' => 'M', 
        // Year 
        'Y' => 'yy', 'y' => 'y', 
    ); 

    return strtr((string)$dateFormat, $chars); 
} 


private function process_expired_posts(){



$timestamp = date("Y-m-d H:i:s", strtotime('today midnight'));

//$timestamp = date("Y-m-d H:i:s", strtotime(current_time('mysql')) + 3600);

//$timestamp = current_time('mysql');

$types = get_post_types( array('public'   => true ), 'names' );

$args = array (
    'post_type' => $types,
    'post_status' => array('publish'),
    'posts_per_page' => '5',
    'ignore_sticky_posts' => 1,
     'meta_query'    => array(
      'relation'  => 'AND',
    array(
        'key'       => '_lh_archive_post_status-post_expires',
        'value'     => $timestamp,
        'compare'   => '<',
    )
  )
);

// The Query
$query = new WP_Query( $args );

$posts = $query->get_posts();

foreach($posts as $post) {

$this->archive_post_by_id($post->ID);

}



}


public function add_expiration_field() {
	
	global $post;

	if( ! empty( $post->ID ) ) {
		$expires = get_post_meta( $post->ID, '_lh_archive_post_status-post_expires', true );
	}

	//$label_val = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : __( 'never', $this->namespace );
	//$date  = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : '';

if ($post->post_status == $this->newstatusname){

$desc = ucfirst($this->newstatuslabel).' on';

} else {


$desc = ucfirst($this->newstatusname);


}


	$label_val = ! empty( $expires ) ? date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) : __( 'never', $this->namespace );

$js_format = $this->dateFormatTojQueryUIDatePickerFormat(get_option( 'date_format' ));

	$date  = ! empty( $expires ) ? date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) : '';


?>

	<div id=="pw-spe-expiration-wrap" class="misc-pub-section">
		<span>
			<span class="wp-media-buttons-icon dashicons dashicons-calendar"></span>&nbsp;
			<?php _e( $desc.':', $this->namespace ); ?>
			<b id="pw-spe-expiration-label"><?php echo $label_val; ?></b>
		</span>
		<a href="#" id="pw-spe-edit-expiration" class="pw-spe-edit-expiration hide-if-no-js">
		<span id="lh_archived_post_status-js-date_format" data-value="<?php echo 
$js_format; ?>"></span>
			<span aria-hidden="true"><?php _e( 'Edit', $this->namespace ); ?></span>&nbsp;
			<span class="screen-reader-text"><?php _e( 'Edit date and time', $this->namespace ); ?></span>
		</a>
		<div id="pw-spe-expiration-field" class="hide-if-js">
			<p>
				<input type="text" name="lh_archive_post_status-post_expires" id="lh_archive_post_status-post_expires" value="<?php echo esc_attr( $date ); ?>" placeholder="yyyy-mm-dd"/>
			</p>
			<p>
			<a href="#" class="pw-spe-hide-expiration button secondary"><?php _e( 'OK', $this->namespace ); ?></a>
			<a href="#" class="pw-spe-hide-expiration cancel"><?php _e( 'Cancel', $this->namespace ); ?></a>
			</p>
		</div>
		<?php wp_nonce_field( 'pw_spe_edit_expiration', 'pw_spe_expiration_nonce' ); ?>
	</div>

<?php

}


public function add_post_scripts() {

  global $post_type;

if( 'attachment' != $post_type ){

wp_enqueue_style('jquery-ui-css',plugins_url( '/styles/jquery-ui-fresh.min.css' , __FILE__ ));

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_script( 'jquery-ui-slider' );
wp_enqueue_script('pw-spe-expiration', plugins_url( '/scripts/edit.js' , __FILE__ ), array(), '1.00', true  );

}

}

public function save_expiration( $post_id = 0 ) {

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return;
	}

	if( ! empty( $_POST['pw_spe_edit_expiration'] ) ) {
		return;
	}

	if( !isset($_POST['pw_spe_expiration_nonce']) or ! wp_verify_nonce( $_POST['pw_spe_expiration_nonce'], 'pw_spe_edit_expiration' ) ) {
		return;
	}

	if( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$expiration = ! empty( $_POST['lh_archive_post_status-post_expires'] ) ? sanitize_text_field( $_POST['lh_archive_post_status-post_expires'] ) : false;

	if( $expiration ) {

$expiration = date("Y-m-d H:i:s", strtotime($expiration));

		// Save the expiration
		update_post_meta( $post_id, '_lh_archive_post_status-post_expires', $expiration );

$post_object = get_post($post_id);


$this->make_status_consistent_with_expiration($post_object, $expiration);


	} else {


$post_object = get_post($post_id);

//It is already archived but missing an archive date
if ($post_object->post_status == $this->newstatusname){

$expiration = current_time('mysql');

update_post_meta( $post_id, '_lh_archive_post_status-post_expires', $expiration );

} else {

// Remove any existing expiration date
delete_post_meta( $post_id, '_lh_archive_post_status-post_expires' );


}

	}

}

	public function admin_edit_columns( $columns ) {
		$columns['lh_archive_post_status-post_expires'] = __( 'Archive Date', $this->namespace );

		return $columns;
	}

	public function admin_edit_column_values( $column, $post_id ) {
		global $post;
		if ( 'lh_archive_post_status-post_expires' == $column ) {

$date = get_post_meta( $post->ID, '_lh_archive_post_status-post_expires', true );

if (!$date){
			echo 'Never';

} else {

echo date(get_option( 'date_format' ), strtotime($date));

}
		}
	}


public function plugin_menu() {
add_options_page('Archive options', 'Archiving', 'manage_options', $this->filename, array($this,"plugin_options"));
}


function current_user_can_view() {
	/**
	 * Default capability to grant ability to view Archived content (if the status is set to non public)
	 *
	 * @since 0.3.0
	 *
	 * @return string
	 */
	$capability = 'read_private_posts';

	return current_user_can( $capability );
}



function plugin_options() {

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

    // variables for the field and option names 
   

 // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $this->hidden_field_name ]) && $_POST[ $this->hidden_field_name ] == 'Y' ) {
        // Read their posted value

if (($_POST[ $this->publicly_available ] == "0") || ($_POST[ $this->publicly_available ] == "1")){	  

$options[ $this->publicly_available ] = $_POST[ $this->publicly_available ];

}

if( isset($_POST[ $this->title_label_field_name ])){
$options[$this->title_label_field_name] = sanitize_text_field($_POST[ $this->title_label_field_name ]);
}

if( isset($_POST[ $this->message_field_name ])){
$allowed_tags = wp_kses_allowed_html( 'post' );
$options[ $this->message_field_name ] = wp_kses($_POST[ $this->message_field_name ], $allowed_tags);
}



if (update_option( $this->opt_name, $options )){

$this->options = get_option($this->opt_name);


        // Put an settings updated message on the screen



?>
<div class="updated"><p><strong><?php _e('settings saved.', $this->namespace ); ?></strong></p></div>
<?php

}

    } 

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h1>" . __( 'LH Archive Post Type Settings', $this->namespace ) . "</h1>";

    // settings form

    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" value="Y">

<p><label for="<?php echo $this->publicly_available; ?>"><?php _e("Can Archived Posts be read publicly:", $this->namespace); ?></label>
<select name="<?php echo $this->publicly_available; ?>" id="<?php echo $this->publicly_available; ?>">
<option value="1" <?php  if ($this->options[$this->publicly_available] == 1){ echo 'selected="selected"'; }  ?>>Yes - But not in the, main loop, frontpage, or feed</option>
<option value="0" <?php  if ($this->options[$this->publicly_available] == 0){ echo 'selected="selected"';}  ?>>No - only logged in users can view archived posts</option>
</select>
</p>



<p><strong><label for="<?php echo $this->title_label_field_name; ?>"><?php _e("Title Label:", $this->namespace); ?></label></strong><br/>
<?php _e("This label will appear after the title for archived posts on the front end of your website", $this->namespace); ?><br/>
<input type="text" name="<?php echo $this->title_label_field_name; ?>" id="<?php echo $this->title_label_field_name; ?>" value="<?php echo $this->options[$this->title_label_field_name] ; ?>" size="20" />
</p>

<p><label for="<?php echo $this->message_field_name; ?>"><?php _e("Archive Message:", $this->namespace); ?></label><br/>
<?php
$settings = array( 'media_buttons' => false );
wp_editor( $this->options[$this->message_field_name], $this->message_field_name, $settings);
?>
</p>



<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>



</div>

<?php
}




function create_archived_custom_post_status(){


if ($this->options[ $this->publicly_available ]){

$public = $this->options[ $this->publicly_available ];

} else {

$public = $this->current_user_can_view();

}



foreach ( get_post_types( array('public'   => true ), 'names' ) as $posttype ) {

     register_post_status( $this->newstatusname, array(
          'label'                     => _x( $this->newstatuslabel, $posttype ),
          'public'                    => $public,
          'show_in_admin_all_list'    => true,
          'show_in_admin_status_list' => true,
          'label_count'               => _n_noop( $this->newstatuslabel_count, $this->newstatuslabel_count) ) );

}

}

function add_archived_message($content){

global $post;

if (is_singular()){

if (isset($post->post_status) and ($post->post_status == $this->newstatusname)){


$content = $this->options[$this->message_field_name].$content;

}

}

return $content;

}

function add_posts_rows($actions,$post) {

if ($post->post_status == "publish"){

if ( current_user_can('edit_post', $post->ID) ) {

if ( current_user_can('publish_posts') ) {


$url = add_query_arg( 'lh_archived_post_status-archive_post', $post->ID );

$url = add_query_arg( 'lh_archived_post_status-archive_nonce', wp_create_nonce( 'lh_archived_post_status-archive_post'.$post->ID ), $url );


$actions['archive_link']  = '<a href="'.$url.'" title="' . esc_attr( __( 'Archive this post' ) ) . '">' . __( 'Archive' ) . '</a>';



}

return $actions;

}

} elseif ($post->post_status == $this->newstatusname){

unset($actions['edit']);

unset($actions['trash']);


}

return $actions;

}



function exclude_archive_post_status_from_main_query( $query ) {
	if ( $query->is_main_query() && !is_admin() && !is_singular() && !$query->is_search()) {

if ( current_user_can('read_private_posts') ) {

$post_status = array( 'publish', 'private' );

} else {

$post_status = array( 'publish');

}
		$query->set( 'post_status', $post_status );
	}
}


function exclude_archive_post_status_from_feed( $query ) {
	if ($query->is_feed){

if ( current_user_can('read_private_posts') ) {

$post_status = array( 'publish', 'private' );

} else {

$post_status = array( 'publish');

}
		$query->set( 'post_status', $post_status );
	}
}



function display_archive_state( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if($arg != $this->newstatusname){
          if($post->post_status == $this->newstatusname){
               return array(ucwords($this->newstatuslabel));
          }
     }
    return $states;
}

public function handle_archiving(){

if(isset($_GET['lh_archived_post_status-archive_post'])){

if ( current_user_can('publish_posts') ) { 

if (wp_verify_nonce( $_GET['lh_archived_post_status-archive_nonce'], 'lh_archived_post_status-archive_post'.$_GET['lh_archived_post_status-archive_post'])){

$this->archive_post_by_id($_GET['lh_archived_post_status-archive_post']);


}


}

}


}


function append_post_status_list(){
     global $post;
     $complete = '';
     $label = '';


          if($post->post_status == $this->newstatusname){
          echo '
          <script>
          jQuery(document).ready(function($){
$("#post-status-display" ).text("'.ucwords($this->newstatuslabel).'");
$("select#post_status").append("<option value=\"'.$this->newstatusname.'\" selected=\"selected\">'.ucwords($this->newstatuslabel).'</option>");
$(".misc-pub-post-status label").append("<span id=\"post-status-display\"> '.ucwords($this->newstatuslabel).'</span>");
          });
          </script>
          ';
          } elseif ($post->post_status == "publish"){


          echo '
          <script>
          jQuery(document).ready(function($){
$("select#post_status").append("<option value=\"'.$this->newstatusname.'\" >'.ucwords($this->newstatuslabel).'</option>");
          });
          </script>
          ';

}

     
} 

// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){
		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';
	}
	return $links;
}

public function on_activate($network_wide) {
    if ( is_multisite() && $network_wide ) { 

        global $wpdb;

        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);
wp_clear_scheduled_hook( 'lh_archived_post_status_run' ); 
wp_clear_scheduled_hook( 'lh_archived_post_status_initial' ); 
wp_schedule_event( time(), 'hourly', 'lh_archived_post_status_run' );
wp_schedule_single_event(time(), 'lh_archived_post_status_initial');

            restore_current_blog();
        } 

    } else {

wp_clear_scheduled_hook( 'lh_archived_post_status_run' ); 
wp_clear_scheduled_hook( 'lh_archived_post_status_initial' ); 
wp_schedule_event( time(), 'hourly', 'lh_archived_post_status_run' );
wp_schedule_single_event(time(), 'lh_archived_post_status_initial');

}



}


public function deactivate_hook() {

wp_clear_scheduled_hook( 'lh_archived_post_status_run' ); 


}

public function run_processes(){


$this->process_expired_posts();

}


public function initial_processes(){

if (!get_option($this->opt_name)){

$options[ $this->publicly_available ] = true;
$options[ $this->message_field_name ] = "<p>This content has been archived. It may no longer be relevant</p>";

update_option( $this->opt_name, $options );


}

wp_clear_scheduled_hook( 'lh_archived_post_status_initial' );

}



public function modify_title($title,$post_id){


if (in_the_loop() && is_singular()){


$post_object = get_post($post_id);


if (isset($post_object->post_status) and ($post_object->post_status == $this->newstatusname)){


  if (!empty($this->options[$this->title_label_field_name])){


$title .= ' - '.$this->options[$this->title_label_field_name];
	
  }

}

}

return $title;


}

public function append_post_status_bulk_edit() {

echo '

<script>

jQuery(document).ready(function($){

$(".inline-edit-status select ").append("<option value=\"archive\">Archive</option>");

});

</script>

';

}


public function add_expiry_if_missing( $new_status, $old_status, $post ){

if (($new_status == 'publish') and ($old_status == 'archive')){

delete_post_meta( $post->ID, '_lh_archive_post_status-post_expires');

} elseif ( 'archive' == $new_status ){

$expiry = get_post_meta($post->ID , '_lh_archive_post_status-post_expires', true );

if (empty($expiry)){

$expiry = current_time('mysql');

update_post_meta( $post->ID, '_lh_archive_post_status-post_expires', $expiry );


}


} else {

return;

}

}


public function __construct() {

$this->filename = plugin_basename( __FILE__ );
$this->options = get_option($this->opt_name);

add_action( 'init', array($this,"create_archived_custom_post_status"));
add_filter( 'the_content', array($this,"add_archived_message"));
add_action('admin_menu', array($this,"plugin_menu"));
add_filter('page_row_actions',array($this,"add_posts_rows"),10,2);
add_filter('post_row_actions',array($this,"add_posts_rows"),10,2);
add_action( 'pre_get_posts', array($this,"exclude_archive_post_status_from_main_query"));
add_action( 'pre_get_posts', array($this,"exclude_archive_post_status_from_feed"));
add_filter( 'display_post_states', array($this,"display_archive_state"));
add_action( 'plugins_loaded', array($this,"handle_archiving"));
add_action('admin_footer-post.php', array($this,"append_post_status_list"));
add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);

add_action( 'post_submitbox_misc_actions', array($this,"add_expiration_field"));
add_action( 'admin_print_scripts-post-new.php', array($this,"add_post_scripts"));
add_action( 'admin_print_scripts-post.php', array($this,"add_post_scripts"));
add_action( 'save_post', array($this,"save_expiration"));
add_filter( 'manage_posts_columns', array( $this, 'admin_edit_columns' ) );
add_action( 'manage_posts_custom_column', array( $this, 'admin_edit_column_values' ), 10, 2 );

add_action( 'lh_archived_post_status_run', array($this,"run_processes"));
add_action( 'lh_archived_post_status_initial', array($this,"initial_processes"));

add_filter('the_title', array( $this, 'modify_title' ),10,2);

//Javascript for bulk edit support
add_action( 'admin_footer-edit.php', array( $this, 'append_post_status_bulk_edit' ));

//add an expiry to newly archived post objects that don't have one already, remove it if it has been republished
add_action( 'transition_post_status', array( $this, 'add_expiry_if_missing' ),10,3);


}


}


$lh_archived_post_status_instance = new LH_archived_post_status_plugin();
register_activation_hook(__FILE__, array($lh_archived_post_status_instance,'on_activate'), 10, 1);
register_deactivation_hook( __FILE__, array($lh_archived_post_status_instance,'deactivate_hook') );

function lh_archived_post_status_uninstall(){

$option_name = 'lh_archive_post_status_options';

delete_option( $option_name );

}


register_uninstall_hook( __FILE__, 'lh_archived_post_status_uninstall' );


?>