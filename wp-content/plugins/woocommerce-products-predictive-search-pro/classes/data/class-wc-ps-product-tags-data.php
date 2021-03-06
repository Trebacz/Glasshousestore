<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class WC_PS_Product_Tags_Data
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_product_tags = $wpdb->prefix. "ps_product_tags";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_product_tags'") != $table_ps_product_tags) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_product_tags}` (
					term_id bigint(20) NOT NULL,
					term_taxonomy_id bigint(20) NOT NULL,
					name varchar(200) NOT NULL,
					PRIMARY KEY  (term_id),
					KEY term_taxonomy_id (term_taxonomy_id),
					KEY name (name)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Product Tags Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_product_tags';

		$wpdb->ps_product_tags = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_product_tags';
	}

	/**
	 * Predictive Search Product Tags Table - return sql
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

		$items_excluded = $wc_ps_exclude_data->get_array_items( 'product_tag' );
		$id_excluded    = '';
		if ( ! empty( $items_excluded ) ) {
			$id_excluded = implode( ',', $items_excluded );
		}

		$sql['select']   = array();
		if ( $check_existed ) {
			$sql['select'][] = " 1 ";
		} else {
			$sql['select'][] = " ppt.* ";
		}

		$sql['from']   = array();
		$sql['from'][] = " {$wpdb->ps_product_tags} AS ppt ";

		$sql['join']   = $join;

		$where[] = " 1=1 ";

		if ( '' != trim( $id_excluded ) ) {
			$where[] = " AND ppt.term_id NOT IN ({$id_excluded}) ";
		}

		$where_title = ' ( ';
		$where_title .= WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name', $search_keyword );
		if ( '' != $search_keyword_nospecial ) {
			$where_title .= " OR ". WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name', $search_keyword_nospecial );
		}
		$search_keyword_no_s_letter = WC_Predictive_Search_Functions::remove_s_letter_at_end_word( $search_keyword );
		if ( $search_keyword_no_s_letter != false ) {
			$where_title .= " OR ". WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name', $search_keyword_no_s_letter );
		}
		$where_title .= ' ) ';

		$where['search']   = array();
		$where['search'][] = ' ( ' . $where_title . ' ) ';

		$sql['where']      = $where;

		$sql['groupby']    = array();
		$sql['groupby'][]  = ' ppt.term_id ';

		$sql['orderby']    = array();
		if ( $check_existed ) {
			$sql['limit']      = " 0 , 1 ";
		} else {
			global $predictive_search_mode;

			$multi_keywords = explode( ' ', trim( $search_keyword ) );
			if ( 'broad' != $predictive_search_mode ) {
				$sql['orderby'][]  = $wpdb->prepare( " ppt.name NOT LIKE '%s' ASC, ppt.name ASC ", $search_keyword.'%' );
				foreach ( $multi_keywords as $single_keyword ) {
					$sql['orderby'][]  = $wpdb->prepare( " ppt.name NOT LIKE '%s' ASC, ppt.name ASC ", $single_keyword.'%' );
				}
			} else {
				$sql['orderby'][]  = $wpdb->prepare( " ppt.name NOT LIKE '%s' ASC, ppt.name NOT LIKE '%s' ASC, ppt.name ASC ", $search_keyword.'%', '% '.$search_keyword.'%' );
				foreach ( $multi_keywords as $single_keyword ) {
					$sql['orderby'][]  = $wpdb->prepare( " ppt.name NOT LIKE '%s' ASC, ppt.name NOT LIKE '%s' ASC, ppt.name ASC ", $single_keyword.'%', '% '.$single_keyword.'%' );
				}
			}

			$sql['limit']      = " {$start} , {$number_row} ";
		}

		return $sql;
	}

	/**
	 * Insert Predictive Search Product Category
	 */
	public function insert_item( $term_id, $term_taxonomy_id, $name = '' ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_product_tags} VALUES(%d, %d, %s)", $term_id, $term_taxonomy_id, stripslashes( $name ) ) );
	}

	/**
	 * Update Predictive Search Product Tag
	 */
	public function update_item( $term_id, $term_taxonomy_id, $name = '' ) {
		global $wpdb;

		$value = $this->is_item_existed( $term_id );
		if ( '0' == $value ) {
			return $this->insert_item( $term_id, $term_taxonomy_id, $name );
		} else {
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ps_product_tags} SET name = %s WHERE term_id = %d ", stripslashes( $name ), $term_id ) );
		}
	}

	/**
	 * Get Predictive Search Product Tag
	 */
	public function get_item( $term_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->ps_product_tags} WHERE term_id = %d LIMIT 0,1 ", $term_id ) );
	}

	/**
	 * Check Predictive Search Product Tag Existed
	 */
	public function is_item_existed( $term_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM {$wpdb->ps_product_tags} WHERE term_id = %d LIMIT 0,1 )", $term_id ) );
	}

	/**
	 * Get Predictive Search Latest Post ID
	 */
	public function get_latest_post_id() {
		global $wpdb;

		return $wpdb->get_var( "SELECT term_id FROM {$wpdb->ps_product_tags} ORDER BY term_id DESC LIMIT 0,1" );
	}

	/**
	 * Check Latest Post ID is newest from WP database
	 */
	public function is_newest_id() {
		global $wpdb;

		$latest_id = $this->get_latest_post_id();
		if ( empty( $latest_id ) || is_null( $latest_id ) ) {
			$latest_id = 0;
		}

		$is_not_newest = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT EXISTS( SELECT 1 FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE t.term_id > %d AND tt.taxonomy = %s LIMIT 0,1 )",
				$latest_id,
				'product_tag'
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

		return $wpdb->get_var( "SELECT COUNT(term_id) FROM {$wpdb->ps_product_tags} " );
	}

	/**
	 * Get Total Items Need to Sync
	 */
	public function get_total_items_need_sync() {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(t.term_id) FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy = %s ", 'product_tag' ) );
	}

	/**
	 * Delete Predictive Search Product Tag
	 */
	public function delete_item( $term_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_product_tags} WHERE term_id = %d ", $term_id ) );
	}

	/**
	 * Empty Predictive Search Product Tags
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_product_tags}" );
	}
}

global $wc_ps_product_tags_data;
$wc_ps_product_tags_data = new WC_PS_Product_Tags_Data();
?>