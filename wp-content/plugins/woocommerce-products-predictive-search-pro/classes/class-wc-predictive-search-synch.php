<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_Predictive_Search_Synch
{
	public function __construct() {

		// Synch for post
		add_action( 'init', array( $this, 'sync_process_post' ), 1 );

		// Synch for Product Category
		add_action( 'created_product_cat', array( $this, 'synch_save_product_cat' ), 10, 2 );
		add_action( 'edited_product_cat', array( $this, 'synch_save_product_cat' ), 10, 2 );
		add_action( 'delete_product_cat', array( $this, 'synch_delete_product_cat' ), 10, 3 );

		// Synch for Product Tag
		add_action( 'created_product_tag', array( $this, 'synch_save_product_tag' ), 10, 2 );
		add_action( 'edited_product_tag', array( $this, 'synch_save_product_tag' ), 10, 2 );
		add_action( 'delete_product_tag', array( $this, 'synch_delete_product_tag' ), 10, 3 );

		// Synch for Term Relationships
		add_action( 'delete_term', array( $this, 'synch_delete_term_relationships' ), 10, 4 );

		add_action( 'admin_notices', array( $this, 'start_sync_data_notice' ), 11 );

		/*
		 *
		 * Synch for custom mysql query from 3rd party plugin
		 * Call below code on 3rd party plugin when create post by mysql query
		 * do_action( 'mysql_inserted_post', $post_id );
		 */
		add_action( 'mysql_inserted_post', array( $this, 'synch_mysql_inserted_post' ) );

		if ( is_admin() ) {
			// AJAX sync data
			add_action('wp_ajax_wc_predictive_search_sync_products', array( $this, 'wc_predictive_search_sync_products_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_products', array( $this, 'wc_predictive_search_sync_products_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_product_skus', array( $this, 'wc_predictive_search_sync_product_skus_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_product_skus', array( $this, 'wc_predictive_search_sync_product_skus_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_categories', array( $this, 'wc_predictive_search_sync_categories_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_categories', array( $this, 'wc_predictive_search_sync_categories_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_tags', array( $this, 'wc_predictive_search_sync_tags_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_tags', array( $this, 'wc_predictive_search_sync_tags_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_relationships', array( $this, 'wc_predictive_search_sync_relationships_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_relationships', array( $this, 'wc_predictive_search_sync_relationships_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_posts', array( $this, 'wc_predictive_search_sync_posts_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_posts', array( $this, 'wc_predictive_search_sync_posts_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_pages', array( $this, 'wc_predictive_search_sync_pages_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_pages', array( $this, 'wc_predictive_search_sync_pages_ajax' ) );

			add_action('wp_ajax_wc_predictive_search_sync_end', array( $this, 'wc_predictive_search_sync_end_ajax' ) );
			add_action('wp_ajax_nopriv_wc_predictive_search_sync_end', array( $this, 'wc_predictive_search_sync_end_ajax' ) );
		}
	}

	public function start_sync_data_notice() {
		$had_sync_posts_data = get_option( 'wc_predictive_search_had_sync_posts_data', 0 );
		$is_upgraded_new_sync_data = get_option( 'wc_ps_upgraded_to_new_sync_data', 0 );
		$is_upgrade_from_free_version = get_option( 'wc_predictive_search_lite_version', false );

		if ( 0 != $had_sync_posts_data && 0 != $is_upgraded_new_sync_data ) return;

		if ( 0 == $is_upgraded_new_sync_data ) {
			$heading_text = __( 'Thanks for upgrading to latest version of WooCommerce Predictive Search' , 'woocommerce-predictive-search' );
			$warning_text = __( 'The setup is almost done. Just one more step and you are ready to go. Please run database Sync to populate your Search engine database.' , 'woocommerce-predictive-search' );
		} elseif ( false === $is_upgrade_from_free_version ) {
			$heading_text = __( 'Thanks for installing WooCommerce Predictive Search' , 'woocommerce-predictive-search' );
			$warning_text = __( 'The setup is almost done. Just one more step and you are ready to go. Please run database Sync to populate your Search engine database.' , 'woocommerce-predictive-search' );
		} else {
			$heading_text = __( 'Thanks for upgrading to WooCommerce Predictive Search Premium' , 'woocommerce-predictive-search' );
			$warning_text = __( 'Now you need to run a full database sync to complete the upgrade.' , 'woocommerce-predictive-search' );
		}

		$sync_data_url = admin_url( 'admin.php?page=woo-predictive-search&tab=performance-settings&box_open=predictive_search_synch_data#predictive_search_synch_data', 'relative' );
	?>
		<div class="message error wc_ps_sync_data_warning">
    		<p>
    			<strong><?php echo $heading_text; ?></strong>
    			- <?php echo $warning_text; ?>
    		</p>
    		<p>
    			<a class="button button-primary" href="<?php echo $sync_data_url; ?>" target="_parent"><?php echo __( 'Sync Now' , 'woocommerce-predictive-search' ); ?></a>
    		</p>
    	</div>
	<?php
	}

	public function get_sync_posts_statistic( $post_type = 'product' ) {
		$status = 'completed';

		global $wc_ps_posts_data;
		$current_items = $wc_ps_posts_data->get_total_items_synched( $post_type );

		$all_items      = wp_count_posts( $post_type );
		$total_items    = isset( $all_items->publish ) ? $all_items->publish : 0;

		if ( $total_items > $current_items ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_items, 'total_items' => $total_items );
	}

	public function get_sync_product_skus_statistic() {
		$status = 'completed';

		global $wc_ps_product_sku_data;
		$current_skus = $wc_ps_product_sku_data->get_total_items_synched();

		$total_skus = $wc_ps_product_sku_data->get_total_items_need_sync();
		$total_skus = ! empty( $total_skus ) ? $total_skus : 0;

		if ( $total_skus > $current_skus ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_skus, 'total_items' => $total_skus );
	}

	public function get_sync_categories_statistic() {
		$status = 'completed';

		global $wc_ps_product_categories_data;
		$current_categories = $wc_ps_product_categories_data->get_total_items_synched();

		$total_categories = $wc_ps_product_categories_data->get_total_items_need_sync();
		$total_categories = ! empty( $total_categories ) ? $total_categories : 0;

		if ( $total_categories > $current_categories ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_categories, 'total_items' => $total_categories );
	}

	public function get_sync_tags_statistic() {
		$status = 'completed';

		global $wc_ps_product_tags_data;
		$current_tags = $wc_ps_product_tags_data->get_total_items_synched();

		$total_tags = $wc_ps_product_tags_data->get_total_items_need_sync();
		$total_tags = ! empty( $total_tags ) ? $total_tags : 0;

		if ( $total_tags > $current_tags ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_tags, 'total_items' => $total_tags );
	}

	public function get_sync_relationships_statistic() {
		$status = 'completed';

		global $wc_ps_term_relationships_data;
		$current_items = $wc_ps_term_relationships_data->get_total_items_synched();
		$total_items   = $wc_ps_term_relationships_data->get_total_items_need_sync();

		if ( $total_items > $current_items ) {
			$status = 'continue';
		}

		return array( 'status' => $status, 'current_items' => $current_items, 'total_items' => $total_items );
	}

	public function wc_predictive_search_sync_posts( $post_type = 'product' ) {
		$end_time = time() + 16;

		$this->migrate_posts( $post_type, $end_time );

		return $this->get_sync_posts_statistic( $post_type );
	}

	public function wc_predictive_search_sync_product_skus() {
		$end_time = time() + 16;

		$this->migrate_skus( $end_time );

		return $this->get_sync_product_skus_statistic();
	}

	public function wc_predictive_search_sync_categories() {
		$end_time = time() + 16;

		$this->migrate_product_categories( $end_time );

		return $this->get_sync_categories_statistic();
	}

	public function wc_predictive_search_sync_tags() {
		$end_time = time() + 16;

		$this->migrate_product_tags( $end_time );

		return $this->get_sync_tags_statistic();
	}

	public function wc_predictive_search_sync_relationships() {
		$end_time = time() + 16;

		$this->migrate_term_relationships( $end_time );

		return $this->get_sync_relationships_statistic();
	}

	public function wc_predictive_search_sync_products_ajax() {
		$result = $this->wc_predictive_search_sync_posts( 'product' );

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_product_skus_ajax() {
		$result = $this->wc_predictive_search_sync_product_skus();

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_categories_ajax() {
		$result = $this->wc_predictive_search_sync_categories();

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_tags_ajax() {
		$result = $this->wc_predictive_search_sync_tags();

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_relationships_ajax() {
		$result = $this->wc_predictive_search_sync_relationships();

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_posts_ajax() {
		$result = $this->wc_predictive_search_sync_posts( 'post' );

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_pages_ajax() {
		$result = $this->wc_predictive_search_sync_posts( 'page' );

		echo json_encode( $result );

		die();
	}

	public function wc_predictive_search_sync_end_ajax() {
		update_option( 'wc_predictive_search_synced_posts_data', 1 );
		update_option( 'wc_predictive_search_manual_synced_completed_time', current_time( 'timestamp' ) );

		wp_send_json( array( 'status' => 'OK', 'date' => date_i18n( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) ) ) );

		die();
	}

	public function sync_process_post() {
		add_action( 'save_post', array( $this, 'synch_save_post' ), 12, 2 );
		add_action( 'delete_post', array( $this, 'synch_delete_post' ) );

		add_action( 'woocommerce_save_product_variation', array( $this, 'sync_save_variation' ), 12, 2 );
	}

	public function empty_posts() {
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_product_categories_data;
		global $wc_ps_product_tags_data;
		global $wc_ps_term_relationships_data;

		// Empty all tables
		$wc_ps_posts_data->empty_table();
		$wc_ps_postmeta_data->empty_table();
		$wc_ps_product_sku_data->empty_table();
		$wc_ps_product_categories_data->empty_table();
		$wc_ps_product_tags_data->empty_table();
		$wc_ps_term_relationships_data->empty_table();

		update_option( 'wc_predictive_search_synced_posts_data', 0 );
	}

	public function update_sync_status() {
		update_option( 'wc_predictive_search_had_sync_posts_data', 1 );
		delete_option( 'wc_predictive_search_lite_version' );
		update_option( 'wc_ps_upgraded_to_new_sync_data', 1 );
	}

	public function migrate_posts( $post_types = array( 'product' ), $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;

		$this->update_sync_status();

		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		$post_types = apply_filters( 'predictive_search_post_types_support', $post_types );

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_posts_data->get_latest_post_id( $post_types );
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			$this->empty_posts();
			$stopped_ID = 0;
		}

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_posts_data->is_newest_id( $post_types ) ) {
			$all_posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_title, p.post_type FROM {$wpdb->posts} AS p WHERE p.post_status = %s AND p.post_type IN ('". implode("','", $post_types ) ."') AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_posts} AS pp WHERE p.ID = pp.post_id ) ORDER BY p.ID ASC LIMIT 0, 500" ,
					'publish'
				)
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_title, p.post_type FROM {$wpdb->posts} AS p WHERE p.ID > %d AND p.post_status = %s AND p.post_type IN ('". implode("','", $post_types ) ."') ORDER BY p.ID ASC LIMIT 0, 500" ,
					$stopped_ID,
					'publish'
				)
			);
		}

		if ( $all_posts && is_array( $all_posts ) && count( $all_posts ) > 0 ) {

			$woocommerce_search_focus_enable = get_option( 'woocommerce_search_focus_enable', 'no' );
			$woocommerce_search_focus_plugin = get_option( 'woocommerce_search_focus_plugin', 'none' );

			foreach ( $all_posts as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$post_id       = $item->ID;

				$item_existed = $wc_ps_posts_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$post_title = $item->post_title;
					if ( in_array( $item->post_type, array( 'product_variation' ) ) ) {
						$post_title = WC_Predictive_Search_Functions::get_product_variation_name( $post_id );
					}
					$wc_ps_posts_data->insert_item( $post_id, $post_title, $item->post_type );
				}

				if ( 'yes' == $woocommerce_search_focus_enable && 'none' != $woocommerce_search_focus_plugin ) {

					if ( 'yoast_seo_plugin' == $woocommerce_search_focus_plugin ) {
						$yoast_keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
						if ( ! empty( $yoast_keyword ) && '' != trim( $yoast_keyword ) ) {
							$wc_ps_postmeta_data->add_item_meta( $post_id, '_yoast_wpseo_focuskw', $yoast_keyword );
						}
					}

					if ( 'all_in_one_seo_plugin' == $woocommerce_search_focus_plugin ) {
						$wpseo_keyword = get_post_meta( $post_id, '_aioseop_keywords', true );
						if ( ! empty( $wpseo_keyword ) && '' != trim( $wpseo_keyword ) ) {
							$wc_ps_postmeta_data->add_item_meta( $post_id, '_aioseop_keywords', $wpseo_keyword );
						}
					}
				}
			}
		}
	}

	public function migrate_skus( $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		$this->update_sync_status();

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_product_sku_data->get_latest_post_id();
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			$wc_ps_product_sku_data->empty_table();
			$stopped_ID = 0;
		}

		/*$all_skus = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_parent, pm.meta_value FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON (p.ID=pm.post_id) WHERE p.ID > %d AND p.post_type IN ('". implode("','", array( 'product', 'product_variation' ) ) ."') AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value NOT LIKE '' ORDER BY p.ID ASC LIMIT 0, 500",
				$stopped_ID,
				'publish',
				'_sku'
			)
		);*/

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_product_sku_data->is_newest_id() ) {
			$all_skus = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_parent FROM {$wpdb->posts} AS p WHERE p.post_type IN ('". implode("','", array( 'product', 'product_variation' ) ) ."') AND p.post_status = %s AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_product_sku} AS ps WHERE p.ID = ps.post_id ) ORDER BY p.ID ASC LIMIT 0, 500" ,
					'publish'
				)
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_skus = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_parent FROM {$wpdb->posts} AS p WHERE p.ID > %d AND p.post_type IN ('". implode("','", array( 'product', 'product_variation' ) ) ."') AND p.post_status = %s ORDER BY p.ID ASC LIMIT 0, 500",
					$stopped_ID,
					'publish'
				)
			);
		}

		if ( $all_skus && is_array( $all_skus ) && count( $all_skus ) > 0 ) {

			foreach ( $all_skus as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$post_id     = $item->ID;
				$post_parent = $item->post_parent;
				$sku         = get_post_meta( $post_id, '_sku', true );

				// Get SKU of parent product if variation has empty SKU
				if ( ( empty( $sku ) || '' == trim( $sku ) ) && $post_parent > 0 ) {
					$sku = get_post_meta( $post_parent, '_sku', true );
				}

				if ( empty( $sku ) || '' == trim( $sku ) ) {
					$sku = '';
				}

				$item_existed = $wc_ps_product_sku_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$wc_ps_product_sku_data->insert_item( $post_id, $sku, $post_parent );
				}

				// Migrate Product Out of Stock
				$terms      = get_the_terms( $post_id, 'product_visibility' );
				$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
				$outofstock = in_array( 'outofstock', $term_names );

				if ( ! $outofstock ) {
					$stock_status = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $stock_status ) && 'outofstock' == trim( $stock_status ) ) {
						$outofstock = true;
					} else {
						$outofstock = false;
					}
				}

				if ( $outofstock ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}
		}
	}

	// This function just for auto update to version 3.2.0
	public function migrate_products_out_of_stock() {
		global $wpdb;
		global $wc_ps_postmeta_data;

		$all_out_of_stock = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
				'_stock_status',
				'outofstock'
			)
		);

		if ( $all_out_of_stock ) {
			foreach ( $all_out_of_stock as $item ) {
				$wc_ps_postmeta_data->update_item_meta( $item->post_id, '_stock_status', 'outofstock' );
			}
		}
	}

	public function migrate_product_categories( $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_product_categories_data;

		$this->update_sync_status();

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_product_categories_data->get_latest_post_id();
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			$wc_ps_product_categories_data->empty_table();
			$stopped_ID = 0;
		}

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_product_categories_data->is_newest_id() ) {
			$all_categories = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT t.term_id, t.name, tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy = %s AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_product_categories} AS pc WHERE t.term_id = pc.term_id ) ORDER BY t.term_id ASC LIMIT 0, 100",
					'product_cat'
				)
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_categories = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT t.term_id, t.name, tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE t.term_id > %d AND tt.taxonomy = %s ORDER BY t.term_id ASC LIMIT 0, 100",
					$stopped_ID,
					'product_cat'
				)
			);
		}

		if ( $all_categories && is_array( $all_categories ) && count( $all_categories ) > 0 ) {
			foreach ( $all_categories as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$item_existed = $wc_ps_product_categories_data->is_item_existed( $item->term_id );
				if ( '0' == $item_existed ) {
					$wc_ps_product_categories_data->insert_item( $item->term_id, $item->term_taxonomy_id, $item->name );
				}
			}
		}
	}

	public function migrate_product_tags( $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_product_tags_data;

		$this->update_sync_status();

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_product_tags_data->get_latest_post_id();
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			$wc_ps_product_tags_data->empty_table();
			$stopped_ID = 0;
		}

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_product_tags_data->is_newest_id() ) {
			$all_tags = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT t.term_id, t.name, tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy = %s AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_product_tags} AS pt WHERE t.term_id = pt.term_id ) ORDER BY t.term_id ASC LIMIT 0, 100",
					'product_tag'
				)
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_tags = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT t.term_id, t.name, tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE t.term_id > %d AND tt.taxonomy = %s ORDER BY t.term_id ASC LIMIT 0, 100",
					$stopped_ID,
					'product_tag'
				)
			);
		}

		if ( $all_tags && is_array( $all_tags ) && count( $all_tags ) > 0 ) {
			foreach ( $all_tags as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$item_existed = $wc_ps_product_tags_data->is_item_existed( $item->term_id );
				if ( '0' == $item_existed ) {
					$wc_ps_product_tags_data->insert_item( $item->term_id, $item->term_taxonomy_id, $item->name );
				}
			}
		}
	}

	public function migrate_term_relationships( $end_time = 0 ) {
		global $wpdb;
		global $wc_ps_term_relationships_data;

		$this->update_sync_status();

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synced_data = get_option( 'wc_predictive_search_synced_posts_data', 0 );

		if ( 0 == $synced_data ) {
			// continue synch data from stopped post ID
			$latest_data = $wc_ps_term_relationships_data->get_latest_post_id();
			if ( ! empty( $latest_data ) && ! is_null( $latest_data ) ) {
				$stopped_ID      = $latest_data->object_id;
				$stopped_term_ID = $latest_data->term_id;
			} else {
				$stopped_ID      = 0;
				$stopped_term_ID = 0;
			}
		} else {
			// Empty table
			$wc_ps_term_relationships_data->empty_table();
			$stopped_ID      = 0;
			$stopped_term_ID = 0;
		}

		// If it's newest ID then fetch missed old ID to sync
		if ( $wc_ps_term_relationships_data->is_newest_id() ) {
			$all_relationships = $wpdb->get_results(
				"SELECT tr.object_id, tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy IN ('category', 'post_tag', 'product_cat', 'product_tag') AND NOT EXISTS ( SELECT 1 FROM {$wpdb->ps_term_relationships} AS ptr WHERE tr.object_id = ptr.object_id AND tt.term_id = ptr.term_id ) ORDER BY tr.object_id ASC, tt.term_id ASC LIMIT 0, 5000"
			);

		// Or continue sync based latest ID have synced
		} else {
			$all_relationships = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT tr.object_id, tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE ( ( tr.object_id = %d AND tt.term_id > %d ) OR tr.object_id > %d ) AND tt.taxonomy IN ('category', 'post_tag', 'product_cat', 'product_tag') ORDER BY tr.object_id ASC, tt.term_id ASC LIMIT 0, 5000",
					$stopped_ID,
					$stopped_term_ID,
					$stopped_ID
				)
			);
		}

		if ( $all_relationships && is_array( $all_relationships ) && count( $all_relationships ) > 0 ) {
			foreach ( $all_relationships as $item ) {

				// Stop command after timeout is set
				if ( $end_time > 0 && $end_time <= time() ) {
					break;
				}

				$wc_ps_term_relationships_data->insert_item( $item->object_id, $item->term_id );
			}
		}
	}

	public function synch_full_database() {
		$this->migrate_posts();
		$this->migrate_product_categories();
		$this->migrate_product_tags();
		$this->migrate_term_relationships();
	}

	public function delete_post_data( $post_id ) {
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		$wc_ps_posts_data->delete_item( $post_id );
		$wc_ps_postmeta_data->delete_item_metas( $post_id );
		$wc_ps_product_sku_data->delete_item( $post_id );
	}

	public function synch_save_post( $post_id, $post ) {
		global $wpdb;
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_term_relationships_data;

		$this->delete_post_data( $post_id );

		$post_types = apply_filters( 'predictive_search_post_types_support', array( 'post', 'page', 'product' ) );

		if ( 'publish' == $post->post_status && in_array( $post->post_type, $post_types ) ) {
			$yoast_keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
			// For Yoast SEO need to check if $_POST['yoast_wpseo_focuskw_text_input'] is existed then use it instead of use post meta
			if ( isset( $_POST['yoast_wpseo_focuskw_text_input'] ) ) {
				$yoast_keyword = trim( $_POST['yoast_wpseo_focuskw_text_input'] );
			}
			$wpseo_keyword = get_post_meta( $post_id, '_aioseop_keywords', true );

			$wc_ps_posts_data->update_item( $post_id, $post->post_title, $post->post_type );

			if ( ! empty( $yoast_keyword ) && '' != trim( $yoast_keyword ) ) {
				$wc_ps_postmeta_data->update_item_meta( $post_id, '_yoast_wpseo_focuskw', $yoast_keyword );
			}

			if ( ! empty( $wpseo_keyword ) && '' != trim( $wpseo_keyword ) ) {
				$wc_ps_postmeta_data->update_item_meta( $post_id, '_aioseop_keywords', $wpseo_keyword );
			}

			$wc_ps_term_relationships_data->delete_object( $post_id );

			if ( 'post' == $post->post_type ) {
				$all_relationships = $wpdb->get_results( "SELECT tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy IN ('category', 'post_tag') AND tr.object_id = {$post_id} ORDER BY tr.object_id ASC" );
				if ( is_array( $all_relationships)  && count( $all_relationships ) > 0 ) {
					foreach ( $all_relationships as $item ) {
						$wc_ps_term_relationships_data->insert_item( $post_id, $item->term_id );
					}
				}
			} elseif ( 'product' == $post->post_type ) {
				$sku = get_post_meta( $post_id, '_sku', true );
				if ( empty( $sku ) || '' == trim( $sku ) ) {
					$sku = '';
				}
				$wc_ps_product_sku_data->update_item( $post_id, $sku, 0 );

				// Update SKU for all variation of this product have empty SKU
				$all_variations = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT p.ID FROM {$wpdb->posts} AS p WHERE p.post_parent = %d AND p.post_type IN ('". implode("','", array( 'product_variation' ) ) ."') AND p.post_status = %s",
						$post_id,
						'publish'
					)
				);

				if ( $all_variations && is_array( $all_variations ) && count( $all_variations ) > 0 ) {
					foreach ( $all_variations as $item ) {
						$variation_sku = get_post_meta( $item->ID, '_sku', true );
						if ( empty( $variation_sku ) || '' == trim( $variation_sku ) ) {
							$wc_ps_product_sku_data->update_item( $item->ID, $sku, $post_id );
						}
					}
				}

				$all_relationships = $wpdb->get_results( "SELECT tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy IN ('product_cat', 'product_tag') AND tr.object_id = {$post_id} ORDER BY tr.object_id ASC" );
				if ( is_array( $all_relationships)  && count( $all_relationships ) > 0 ) {
					foreach ( $all_relationships as $item ) {
						$wc_ps_term_relationships_data->insert_item( $post_id, $item->term_id );
					}
				}

				// Migrate Product Out of Stock
				$terms      = get_the_terms( $post_id, 'product_visibility' );
				$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
				$outofstock = in_array( 'outofstock', $term_names );

				if ( ! $outofstock ) {
					$stock_status = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $stock_status ) && 'outofstock' == trim( $stock_status ) ) {
						$outofstock = true;
					} else {
						$outofstock = false;
					}
				}

				if ( $outofstock ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}

			if ( 'page' == $post->post_type ) {
				global $woocommerce_search_page_id;

				// flush rewrite rules if page is editing is WooCommerce Search Result page
				if ( $post_id == $woocommerce_search_page_id ) {
					flush_rewrite_rules();
				}
			}

		}
	}

	public function sync_save_variation( $variation_id, $i ) {
		global $wc_ps_product_sku_data;
		global $wc_ps_postmeta_data;

		$this->delete_post_data( $variation_id );

		$sku         = get_post_meta( $variation_id, '_sku', true );
		$post_parent = wp_get_post_parent_id( $variation_id );

		if ( ( empty( $sku ) || '' == trim( $sku ) ) && $post_parent > 0 ) {
			$sku = get_post_meta( $post_parent, '_sku', true );
		}

		if ( empty( $sku ) || '' == trim( $sku ) ) {
			$sku = '';
		}

		$wc_ps_product_sku_data->update_item( $variation_id, $sku, $post_parent );

		// Migrate Product Out of Stock
		$terms      = get_the_terms( $variation_id, 'product_visibility' );
		$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
		$outofstock = in_array( 'outofstock', $term_names );

		if ( ! $outofstock ) {
			$stock_status = get_post_meta( $variation_id, '_stock_status', true );
			if ( ! empty( $stock_status ) && 'outofstock' == trim( $stock_status ) ) {
				$outofstock = true;
			} else {
				$outofstock = false;
			}
		}

		if ( $outofstock ) {
			$wc_ps_postmeta_data->update_item_meta( $variation_id, '_stock_status', 'outofstock' );
		} else {
			$wc_ps_postmeta_data->delete_item_meta( $variation_id, '_stock_status' );
		}
	}

	public function synch_delete_post( $post_id ) {
		global $wc_ps_keyword_data;
		global $wc_ps_exclude_data;
		global $wc_ps_term_relationships_data;

		$this->delete_post_data( $post_id );

		$post_type = get_post_type( $post_id );

		$wc_ps_keyword_data->delete_item( $post_id );
		$wc_ps_exclude_data->delete_item( $post_id, $post_type );

		$wc_ps_term_relationships_data->delete_object( $post_id );
	}

	public function synch_save_product_cat( $term_id, $tt_id ) {
		global $wc_ps_product_categories_data;

		$term = get_term( $term_id, 'product_cat' );
		$wc_ps_product_categories_data->update_item( $term_id, $tt_id, $term->name );
	}

	public function synch_save_product_tag( $term_id, $tt_id ) {
		global $wc_ps_product_tags_data;

		$term = get_term( $term_id, 'product_tag' );
		$wc_ps_product_tags_data->update_item( $term_id, $tt_id, $term->name );
	}

	public function synch_delete_product_cat( $term_id, $tt_id, $deleted_term ) {
		global $wc_ps_product_categories_data;
		global $wc_ps_exclude_data;

		$wc_ps_product_categories_data->delete_item( $term_id );
		$wc_ps_exclude_data->delete_item( $term_id, 'product_cat' );
	}

	public function synch_delete_product_tag( $term_id, $tt_id, $deleted_term ) {
		global $wc_ps_product_tags_data;
		global $wc_ps_exclude_data;

		$wc_ps_product_tags_data->delete_item( $term_id );
		$wc_ps_exclude_data->delete_item( $term_id, 'product_tag' );
	}

	public function synch_delete_term_relationships( $term_id, $tt_id, $taxonomy, $deleted_term ) {
		global $wc_ps_term_relationships_data;
		$wc_ps_term_relationships_data->delete_term( $term_id );
	}

	public function synch_mysql_inserted_post( $post_id = 0 ) {
		if ( $post_id < 1 ) return;

		global $wpdb;
		$post_types = apply_filters( 'predictive_search_post_types_support', array( 'post', 'page', 'product', 'product_variation' ) );

		$item = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ID, post_title, post_type, post_parent FROM {$wpdb->posts} WHERE ID = %d AND post_status = %s AND post_type IN ('". implode("','", $post_types ) ."')" ,
				$post_id,
				'publish'
			)
		);

		if ( $item ) {
			global $wc_ps_posts_data;
			global $wc_ps_postmeta_data;
			global $wc_ps_product_sku_data;

			if ( $item->post_type != 'product_variation' ) {
				$yoast_keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
				$wpseo_keyword = get_post_meta( $post_id, '_aioseop_keywords', true );

				$item_existed = $wc_ps_posts_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$wc_ps_posts_data->insert_item( $post_id, $item->post_title, $item->post_type );
				}

				if ( ! empty( $yoast_keyword ) && '' != trim( $yoast_keyword ) ) {
					$wc_ps_postmeta_data->add_item_meta( $post_id, '_yoast_wpseo_focuskw', $yoast_keyword );
				}

				if ( ! empty( $wpseo_keyword ) && '' != trim( $wpseo_keyword ) ) {
					$wc_ps_postmeta_data->add_item_meta( $post_id, '_aioseop_keywords', $wpseo_keyword );
				}
			}

			if ( in_array( $item->post_type, array( 'product', 'product_variation' ) ) ) {
				$sku         = get_post_meta( $post_id, '_sku', true );
				$post_parent = $item->post_parent;

				if ( ( empty( $sku ) || '' == trim( $sku ) ) && $post_parent > 0 ) {
					$sku = get_post_meta( $post_parent, '_sku', true );
				}

				if ( empty( $sku ) || '' == trim( $sku ) ) {
					$sku = '';
				}

				$item_existed = $wc_ps_product_sku_data->is_item_existed( $post_id );
				if ( '0' == $item_existed ) {
					$wc_ps_product_sku_data->insert_item( $post_id, $sku, $post_parent );
				}

				// Migrate Product Out of Stock
				$terms      = get_the_terms( $post_id, 'product_visibility' );
				$term_names = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
				$outofstock = in_array( 'outofstock', $term_names );

				if ( ! $outofstock ) {
					$stock_status = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $stock_status ) && 'outofstock' == trim( $stock_status ) ) {
						$outofstock = true;
					} else {
						$outofstock = false;
					}
				}

				if ( $outofstock ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}
		}
	}
}

global $wc_ps_synch;
$wc_ps_synch = new WC_Predictive_Search_Synch();
?>