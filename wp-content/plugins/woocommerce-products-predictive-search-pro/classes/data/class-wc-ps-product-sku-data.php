<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class WC_PS_Product_SKU_Data
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_product_sku = $wpdb->prefix. "ps_product_sku";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_product_sku'") != $table_ps_product_sku) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_product_sku}` (
					post_id bigint(20) NOT NULL,
					sku text NULL,
					post_parent bigint(20) NOT NULL DEFAULT 0,
					PRIMARY KEY  (post_id),
					KEY post_parent (post_parent)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Product SKU Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_product_sku';

		$wpdb->ps_product_sku = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_product_sku';
	}

	/**
	 * Predictive Search Product SKU Table - return sql
	 *
	 * @return void
	 */
	public function get_sql( $search_keyword = '', $search_keyword_nospecial = '', $number_row, $start = 0, $check_existed = false ) {
		if ( '' == $search_keyword && '' == $search_keyword_nospecial ) {
			return false;
		}

		global $wpdb;
		global $wc_ps_exclude_data;

		$sql     = array();
		$join    = array();
		$where   = array();
		$groupby = array();
		$orderby = array();

		$items_excluded = $wc_ps_exclude_data->get_array_items( 'product' );

		$woocommerce_search_exclude_out_stock = get_option('woocommerce_search_exclude_out_stock');
		if ( 'yes' == $woocommerce_search_exclude_out_stock ) {
			global $wc_ps_postmeta_data;
			$items_out_of_stock = $wc_ps_postmeta_data->get_array_products_out_of_stock();
			$items_excluded = array_merge( $items_out_of_stock, $items_excluded );
		}

		$id_excluded    = '';
		if ( ! empty( $items_excluded ) ) {
			$id_excluded = implode( ',', $items_excluded );
		}

		$sql['select']   = array();
		if ( $check_existed ) {
			$sql['select'][] = " 1 ";
		} else {
			$sql['select'][] = " pp.* ";
		}

		$sql['from']   = array();
		$sql['from'][] = " {$wpdb->ps_product_sku} AS pp ";

		$sql['join']   = $join;

		$where[] = " 1=1 ";

		if ( '' != trim( $id_excluded ) ) {
			$where[] = " AND pp.post_id NOT IN ({$id_excluded}) AND pp.post_parent NOT IN ({$id_excluded}) AND pp.sku != '' ";
		}

		$where_title = ' ( ';
		$where_title .= WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'pp.sku', $search_keyword );
		if ( '' != $search_keyword_nospecial ) {
			$where_title .= " OR ". WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'pp.sku', $search_keyword_nospecial );
		}
		$where_title .= ' ) ';

		$where['search']   = array();
		$where['search'][] = ' ( ' . $where_title . ' ) ';

		$sql['where']      = $where;

		$sql['groupby']    = array();
		$sql['groupby'][]  = ' pp.post_id ';

		$sql['orderby']    = array();
		if ( $check_existed ) {
			$sql['limit']      = " 0 , 1 ";
		} else {
			global $predictive_search_mode;

			$multi_keywords = explode( ' ', trim( $search_keyword ) );
			if ( 'broad' != $predictive_search_mode ) {
				$sql['orderby'][]  = $wpdb->prepare( " pp.sku NOT LIKE '%s' ASC, pp.sku ASC ", $search_keyword.'%' );
				foreach ( $multi_keywords as $single_keyword ) {
					$sql['orderby'][]  = $wpdb->prepare( " pp.sku NOT LIKE '%s' ASC, pp.sku ASC ", $single_keyword.'%' );
				}
			} else {
				$sql['orderby'][]  = $wpdb->prepare( " pp.sku NOT LIKE '%s' ASC, pp.sku NOT LIKE '%s' ASC, pp.sku ASC ", $search_keyword.'%', '% '.$search_keyword.'%' );
				foreach ( $multi_keywords as $single_keyword ) {
					$sql['orderby'][]  = $wpdb->prepare( " pp.sku NOT LIKE '%s' ASC, pp.sku NOT LIKE '%s' ASC, pp.sku ASC ", $single_keyword.'%', '% '.$single_keyword.'%' );
				}
			}

			$sql['limit']      = " {$start} , {$number_row} ";
		}

		return $sql;
	}

	/**
	 * Insert Predictive Search Product SKU
	 */
	public function insert_item( $post_id, $sku = '', $post_parent = 0 ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_product_sku} VALUES(%d, %s, %d)", $post_id, stripslashes( $sku ), $post_parent ) );
	}

	/**
	 * Update Predictive Search Product SKU
	 */
	public function update_item( $post_id, $sku = '', $post_parent = 0 ) {
		global $wpdb;

		$value = $this->is_item_existed( $post_id );
		if ( '0' == $value ) {
			return $this->insert_item( $post_id, $sku, $post_parent );
		} else {
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ps_product_sku} SET sku = %s WHERE post_id = %d ", stripslashes( $sku ), $post_id ) );
		}
	}

	/**
	 * Get Predictive Search Product SKU
	 */
	public function get_item( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT sku FROM {$wpdb->ps_product_sku} WHERE post_id = %d LIMIT 0,1 ", $post_id ) );
	}

	/**
	 * Check Predictive Search Product SKU Existed
	 */
	public function is_item_existed( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM {$wpdb->ps_product_sku} WHERE post_id = %d LIMIT 0,1 )", $post_id ) );
	}

	/**
	 * Get Predictive Search Latest Post ID
	 */
	public function get_latest_post_id() {
		global $wpdb;

		return $wpdb->get_var( "SELECT post_id FROM {$wpdb->ps_product_sku} ORDER BY post_id DESC LIMIT 0,1" );
	}

	/**
	 * Check Latest Post ID is newest from WP database
	 */
	public function is_newest_id() {
		global $wpdb;

		$post_types = array( 'product', 'product_variation' );

		$latest_id = $this->get_latest_post_id();
		if ( empty( $latest_id ) || is_null( $latest_id ) ) {
			$latest_id = 0;
		}

		$is_not_newest = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT EXISTS( SELECT 1 FROM {$wpdb->posts} WHERE ID > %d AND post_type IN ('". implode("','", $post_types ) ."') AND post_status = %s LIMIT 0,1 )",
				$latest_id,
				'publish'
			)
		);

		if ( '1' != $is_not_newest ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Total Items Synched
	 */
	public function get_total_items_synched() {
		global $wpdb;

		return $wpdb->get_var( "SELECT COUNT(post_id) FROM {$wpdb->ps_product_sku} " );
	}

	/**
	 * Get Total Items Need to Sync
	 */
	public function get_total_items_need_sync() {
		global $wpdb;

		//return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON (p.ID=pm.post_id) WHERE p.post_type IN ('". implode("','", array( 'product', 'product_variation' ) ) ."') AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value NOT LIKE '' ", 'publish', '_sku' ) );
		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p WHERE p.post_type IN ('". implode("','", array( 'product', 'product_variation' ) ) ."') AND p.post_status = %s ", 'publish' ) );
	}

	/**
	 * Delete Predictive Search Product SKU
	 */
	public function delete_item( $post_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_product_sku} WHERE post_id = %d ", $post_id ) );
	}

	/**
	 * Empty Predictive Search Product SKU
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_product_sku}" );
	}

	/**
	 * Check if post_arent field is not existed then add it to ps_product_sku table
	 */
	public function check_post_parent_field_existed() {
		global $wpdb;

		$column = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
			DB_NAME, $wpdb->ps_product_sku, 'post_parent' ) );

		if ( empty( $column ) ) {
			$wpdb->query( "ALTER TABLE {$wpdb->ps_product_sku} ADD post_parent BIGINT NOT NULL DEFAULT 0" );
			$wpdb->query( "ALTER TABLE {$wpdb->ps_product_sku} ADD INDEX post_parent (post_parent)" );
		}
	}
}

global $wc_ps_product_sku_data;
$wc_ps_product_sku_data = new WC_PS_Product_SKU_Data();
?>