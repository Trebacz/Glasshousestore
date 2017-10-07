<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" value="Y">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="<?php echo $this->publicly_available; ?>"><?php _e("Can Archived Posts be read publicly:", $this->namespace); ?></label></th>
<td><select name="<?php echo $this->publicly_available; ?>" id="<?php echo $this->publicly_available; ?>">
<option value="1" <?php  if ($this->options[$this->publicly_available] == 1){ echo 'selected="selected"'; }  ?>>Yes - But not in the, main loop, frontpage, or feed</option>
<option value="0" <?php  if ($this->options[$this->publicly_available] == 0){ echo 'selected="selected"';}  ?>>No - only logged in users can view archived posts</option>
</select>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="<?php echo $this->title_label_field_name; ?>"><?php _e("Title Label:", $this->namespace); ?></label>
</th>
<td><input type="text" name="<?php echo $this->title_label_field_name; ?>" id="<?php echo $this->title_label_field_name; ?>" value="<?php echo $this->options[$this->title_label_field_name] ; ?>" size="20" /><br/><?php _e("This label will appear after the title for archived posts on the front end of your website", $this->namespace); ?>
</td>
</tr>


<tr valign="top">
<th scope="row"><label for="<?php echo $this->message_field_name; ?>"><?php _e("Archive Message:", $this->namespace); ?></label></th>
<td>
<?php
$settings = array( 'media_buttons' => false );
wp_editor( $this->options[$this->message_field_name], $this->message_field_name, $settings);
?>
</td>
</tr>
</table>



<?php submit_button( 'Save Changes' ); ?>
</form>