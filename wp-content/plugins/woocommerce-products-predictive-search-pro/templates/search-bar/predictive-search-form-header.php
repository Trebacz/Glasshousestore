<?php
/**
 * The Template for Predictive Search plugin
 *
 * Override this template by copying it to yourtheme/woocommerce/predictive-search-form-header.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_search_page_id;

$search_results_page = str_replace( array( 'http:', 'https:' ), '', get_permalink( $woocommerce_search_page_id ) );
?>

<?php do_action( 'wc_ps_search_form_before' ); ?>

<div class="wc_ps_container wc_ps_header_container" id="wc_ps_container_<?php echo $ps_id; ?>">
	<form
		class="wc_ps_form"
		id="wc_ps_form_<?php echo $ps_id; ?>"
		autocomplete="off"
		action="<?php echo esc_url( $search_results_page ); ?>"
		method="get"

		data-ps-id="<?php echo $ps_id; ?>"
		data-ps-cat_align="<?php echo $ps_args['cat_align']; ?>"
		data-ps-cat_max_wide="<?php echo $ps_args['cat_max_wide']; ?>"
		data-ps-popup_wide="<?php echo $ps_args['popup_wide']; ?>"
		data-ps-widget_template="<?php echo $ps_widget_template; ?>"
	>

		<?php
		if ( 1 == $ps_args['show_catdropdown'] && false !== $product_categories = wc_ps_get_product_categories() ) {
			$default_cat       = '';
			$default_cat_label = wc_ps_ict_t__( 'All', __('All', 'woocommerce-predictive-search' ) );
			if ( isset( $ps_args['default_cat'] ) && ! empty( $ps_args['default_cat'] ) ) {
				foreach ( $product_categories as $category_data ) {
					if ( $ps_args['default_cat'] == $category_data['slug'] ) {
						$default_cat       = $category_data['slug'];
						$default_cat_label = esc_html( $category_data['name'] );
						break;
					}
				}
			}
		?>
		<div class="wc_ps_nav_<?php echo $ps_args['cat_align']; ?>">
			<div class="wc_ps_nav_scope">
				<div class="wc_ps_nav_facade">
					<i class="fa fa-angle-down wc_ps_nav_down_icon" aria-hidden="true"></i>
					<span class="wc_ps_nav_facade_label"><?php echo $default_cat_label; ?></span>
				</div>
				<select class="wc_ps_category_selector" name="cat_in">
					<option value="" selected="selected"><?php wc_ps_ict_t_e( 'All', __('All', 'woocommerce-predictive-search' ) ); ?></option>
				<?php if ( $product_categories !== false ) { ?>
					<?php foreach ( $product_categories as $category_data ) { ?>
					<option <?php selected( $default_cat, $category_data['slug'], true ); ?> data-href="<?php echo esc_url( $category_data['url'] ); ?>" value="<?php echo esc_attr( $category_data['slug'] ); ?>"><?php echo esc_html( $category_data['name'] ); ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</div>
		</div>
		<?php } else { ?>
		<input type="hidden" class="wc_ps_category_selector" name="cat_in" value="" >
		<?php } ?>

		<div class="wc_ps_nav_<?php echo ( 'left' === $ps_args['cat_align'] ? 'right' : 'left' ); ?>">
			<div class="wc_ps_nav_submit">
				<i class="fa fa-search wc_ps_nav_submit_icon" aria-hidden="true"></i>
				<input data-ps-id="<?php echo $ps_id; ?>" class="wc_ps_nav_submit_bt" type="button" value="<?php echo __( 'Go', 'woocommerce-predictive-search' ); ?>">
			</div>
		</div>

		<div class="wc_ps_nav_fill">
			<div class="wc_ps_nav_field">
				<input type="text" name="rs" class="wc_ps_search_keyword" id="wc_ps_search_keyword_<?php echo $ps_id; ?>"
					onblur="if( this.value == '' ){ this.value = '<?php echo esc_js( $ps_args['search_box_text'] ); ?>'; }"
					onfocus="if( this.value == '<?php echo esc_js( $ps_args['search_box_text'] ); ?>' ){ this.value = ''; }"
					value="<?php echo esc_attr( $ps_args['search_box_text'] ); ?>"
					data-ps-id="<?php echo $ps_id; ?>"
					data-ps-default_text="<?php echo esc_attr( $ps_args['search_box_text'] ); ?>"
					data-ps-row="<?php echo esc_attr( $ps_args['row'] ); ?>"
					data-ps-text_lenght="<?php echo esc_attr( $ps_args['text_lenght'] ); ?>"

					<?php if ( class_exists('SitePress') ) { ?>
					data-ps-lang="<?php echo ICL_LANGUAGE_CODE; ?>"
					<?php } ?>

					<?php if ( $ps_args['search_in'] != '' ) { ?>
					data-ps-popup_search_in="<?php echo esc_attr( $ps_args['search_in'] ); ?>"
					<?php } ?>

					<?php if ( count( $ps_args['search_list'] ) > 0 ) { ?>
					data-ps-search_in="<?php echo esc_attr( $ps_args['search_list'][0] ); ?>"
					data-ps-search_other="<?php echo esc_attr( implode( ',', $ps_args['search_list'] ) ); ?>"
					<?php } ?>

					data-ps-show_price="<?php echo $ps_args['show_price']; ?>"
					data-ps-show_in_cat="<?php echo $ps_args['show_in_cat']; ?>"
				/>
				<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw wc_ps_searching_icon" style="display: none;"></i>
			</div>
		</div>

	<?php if ( '' == get_option('permalink_structure') ) { ?>
		<input type="hidden" name="page_id" value="<?php echo esc_attr( $woocommerce_search_page_id ); ?>"  />

		<?php if ( class_exists('SitePress') ) { ?>
			<input type="hidden" name="lang" value="<?php echo ICL_LANGUAGE_CODE; ?>"  />
		<?php } ?>

	<?php } ?>

	<?php if ( count( $ps_args['search_list'] ) > 0 ) { ?>
		<input type="hidden" name="search_in" value="<?php echo esc_attr( $ps_args['search_list'][0] ); ?>"  />
		<input type="hidden" name="search_other" value="<?php echo esc_attr( implode( ',', $ps_args['search_list'] ) ); ?>"  />
	<?php } ?>

		<?php do_action( 'wc_ps_search_form_inside' ); ?>
	</form>
</div>

<?php do_action( 'wc_ps_search_form_after' ); ?>

