<form method="post" action="<?php echo admin_url( "admin-post.php" ); ?>">
<h3>Meta Viewer</h3>
<p class="instructions">You can view meta data that is associated with a Product or Order.</p>
<p><a href="http://jem-products.com/blog/knowledgebase/using-meta-data-viewer/" target='_blank'>See the documentation here </a></p>
<p>To export this data you will need the Pro version. <a href="http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress">Clickhere for more details</a></p>
<div>
	<label for="meta_id">Product/Order ID</label>
	<input type="text" size="25" name="meta_id">
	<input type="radio" name="meta_type" value="product" checked>Product &nbsp;&nbsp;
	<input type="radio" name="meta_type" value="order">Order

</div>
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="View Meta">
    </p>
    <input type="hidden" name="action" value="update_meta">
    <input type="hidden" name="_wp_http_referer" value="<?php echo urlencode( $_SERVER['REQUEST_URI'] ); ?>">
<?php
    if($this->message != ""){
        echo $this->message;
    }

?>
    <TABLE class="jemxp-meta-table" style="font-family:monospace; text-align:left; width:100%;">
    <?php echo $html; ?>

    </TABLE>
    <TABLE class="jemxp-meta-table" style="">
        <?php echo $line_item_html; ?>

    </TABLE>
</form>




