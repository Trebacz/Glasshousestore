<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="market-box table-box-main">
    <div class="orderimpexp-premium-features">
        <center><a href="https://www.xadapter.com/product/order-import-export-plugin-for-woocommerce/" target="_blank" class="button button-primary button-go-pro"><?php _e('Upgrade to Premium Version', 'wf_csv_import_export'); ?></a></center>
        <span>
            <ul>
                <li><?php _e('Import and Export Subscriptions along with Order and Coupon.', 'wf_order_import_export'); ?></li>
                <li><?php _e('Filtering options while Export using Order Status, Date, Coupon Type etc.', 'wf_order_import_export'); ?></li>
                <li><?php _e('Change values while import using Evaluation Field feature.', 'wf_order_import_export'); ?></li>
                <li><?php _e('A number of third party plugins supported.', 'wf_order_import_export'); ?> </li>
                <li><?php _e('Column Mapping Feature to Import from any CSV format ( Magento, Shopify, OpenCart etc. ).', 'wf_order_import_export'); ?></li>
                <li><?php _e('Import and Export via FTP.', 'wf_order_import_export'); ?></li>
                <li><?php _e('Choice to update or skip existing orderswhile importing.', 'wf_order_import_export'); ?></li>
                <li><?php _e('WPML supported. French and German support out of the box.', 'wf_order_import_export'); ?></li>
            </ul>
        </span>
        <div id="show_more" style="display: none">
            <span>

                <ul>
                    <li><?php _e('Schedule automatic import and export using Cron Job Feature.', 'wf_order_import_export'); ?></li>
                    <li><?php _e('Automatic scheduled import and export.', 'wf_order_import_export'); ?></li>
                    <li><?php _e('XML Export/Import supports Stamps.com desktop application, UPS WorldShip, Endicia and FedEx.', 'wf_order_import_export'); ?></li>
                    <li><?php _e('30 Days Money Back Guarantee.', 'wf_order_import_export'); ?></li>
                    <li><?php _e('More frequent plugin updates.', 'wf_order_import_export'); ?></li>
                    <li><?php _e('Excellent Support for setting it up!', 'wf_order_import_export'); ?></li>
                </ul>

            </span>
            <center> 
                <a href="http://orderimportexport.hikeforce.com/wp-admin/admin.php?page=wf_woocommerce_order_im_ex" target="_blank" class="button button-doc-demo"><?php _e('Live Demo', 'wf_order_import_export'); ?></a>
                <a href="https://www.xadapter.com/setting-up-order-import-export-plugin-for-woocommerce/" target="_blank" class="button button-doc-demo"><?php _e('Documentation', 'wf_order_import_export'); ?></a>
            </center>
            <center>
                <a href="<?php echo plugins_url('Sample_Order.csv', WF_OrderImpExpCsv_FILE); ?>" target="_blank" class="button button-doc-demo"><?php _e('Sample Order CSV', 'wf_order_import_export'); ?></a>
                <a href="<?php echo plugins_url('Sample_Coupon.csv', WF_OrderImpExpCsv_FILE); ?>" target="_blank" class="button button-doc-demo"><?php _e('Sample Coupon CSV', 'wf_order_import_export'); ?></a>
            </center>
        </div>
        <div class="button-show-more">
            <button class="button" onclick="showMoreFeatures()" id="showMoreButton" >Show more
                <i class="dashicons dashicons-arrow-down-alt2"></i>
            </button>
        </div>
    </div>
    <div class="orderimpexp-review-widget">
        <?php
        echo sprintf(__('<div class=""><p><i>If you like the plugin please leave us a %1$s review!</i><p></div>', 'wf_csv_import_export'), '<a href="https://wordpress.org/support/plugin/order-import-export-for-woocommerce/reviews?rate=5#new-post" target="_blank" class="xa-orderimpexp-rating-link" data-reviewed="' . esc_attr__('Thanks for the review.', 'wf_csv_import_export') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>');
        ?>
    </div>
</div>
<script>
    function showMoreFeatures() {
        var moreFeatures = document.getElementById("show_more");
        moreFeatures.style.display = "block";
        document.getElementById("showMoreButton").style.display = "none";
    }
</script>