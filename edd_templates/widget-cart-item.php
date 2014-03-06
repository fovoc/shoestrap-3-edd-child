<?php global $ss_framework; ?>
<li class="edd-cart-item list-group-item">
	<span class="edd-cart-item-title">{item_title}</span>
	<span class="edd-cart-item-separator">-</span><span class="edd-cart-item-price">&nbsp;{item_amount}&nbsp;</span>
	<a href="{remove_url}" data-cart-item="{cart_item_id}" data-download-id="{item_id}" data-action="edd_remove_from_cart" class="<?php echo $ss_framework->button_classes( 'danger', 'extra-small', null, 'right pull-right edd-remove-from-cart' ); ?>"><i class="el-icon-remove"></i></a>
</li>