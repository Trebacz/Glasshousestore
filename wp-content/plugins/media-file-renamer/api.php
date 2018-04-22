<?php

/**
 *
 * FUNCTIONS
 *
 */

// Rename the media automatically based on the settings
function mfrh_rename( $mediaId ) {
  global $mfrh_core;
  return $mfrh_core->rename( $mediaId );
}

/**
 *
 * ACTIONS AND FILTERS
 *
 * Available actions are:
 * mfrh_path_renamed
 * mfrh_url_renamed
 * mfrh_media_renamed
 *
 * Please have a look at the custom.php file for examples.
 *
 */

?>
