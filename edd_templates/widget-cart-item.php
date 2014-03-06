<?php global $ss_framework; ?>
<?php echo $ss_framework->open_row( 'li' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 7 ), null, 'edd-cart-item-title' ); ?>
		{item_title}
	<?php echo $ss_framework->close_col( 'span' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 3 ), null, 'edd-cart-item-price' ); ?>
		{item_amount}&nbsp;
	<?php echo $ss_framework->close_col( 'span' ); ?>
	<a href="{remove_url}" data-cart-item="{cart_item_id}" data-download-id="{item_id}" data-action="edd_remove_from_cart" class="<?php echo $ss_framework->column_classes( array( 'tablet' => 2 ), 'string' ); ?> <?php echo $ss_framework->button_classes( 'danger', 'extra-small', null, 'right pull-right edd-remove-from-cart' ); ?>"><i class="el-icon-remove"></i></a>
<?php echo $ss_framework->close_row( 'li' );