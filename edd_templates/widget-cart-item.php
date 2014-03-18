<?php
/**
 * Cart Item Template
 *
 * @package      Shoestrap Easy Digital Downloads Child Theme
 * @author       Shoestrap - http://shoestrap.org
 * @link         http://www.shoestrap.org
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/
?>
<?php global $ss_framework; ?>
<?php echo $ss_framework->open_row( 'li' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 7 ), null, 'edd-cart-item-title' ); ?>
		{item_title}
	<?php echo $ss_framework->close_col( 'span' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 3 ), null, 'edd-cart-item-price' ); ?>
		{item_amount}&nbsp;
	<?php echo $ss_framework->close_col( 'span' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 2 ), null, 'edd-cart-item-remove' ); ?>
		<a href="{remove_url}" data-cart-item="{cart_item_id}" data-download-id="{item_id}" data-action="edd_remove_from_cart" class="<?php echo $ss_framework->button_classes( 'danger', 'extra-small', null, 'edd-remove-from-cart' ); ?> <?php echo $ss_framework->float_class( 'right' ); ?>"><i class="el-icon-remove"></i></a>
	<?php echo $ss_framework->close_col( 'span' ); ?>
<?php echo $ss_framework->close_row( 'li' ); ?>
<hr>