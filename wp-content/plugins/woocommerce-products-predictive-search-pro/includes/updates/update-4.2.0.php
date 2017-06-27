<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

// Add an index to the field comment_type to improve the response time of the query
$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_posts WHERE column_name = 'post_type'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_posts ADD INDEX post_type (post_type)" );
}

$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_product_categories WHERE column_name = 'term_taxonomy_id'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_product_categories ADD INDEX term_taxonomy_id (term_taxonomy_id)" );
}

$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_product_categories WHERE column_name = 'name'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_product_categories ADD INDEX name (name)" );
}

$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_product_sku WHERE column_name = 'post_parent'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_product_sku ADD INDEX post_parent (post_parent)" );
}

$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_product_tags WHERE column_name = 'term_taxonomy_id'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_product_tags ADD INDEX term_taxonomy_id (term_taxonomy_id)" );
}

$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_product_tags WHERE column_name = 'name'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_product_tags ADD INDEX name (name)" );
}