<?php
/**
 * Cart Widget Template
 *
 * @package      Shoestrap Easy Digital Downloads Child Theme
 * @author       Shoestrap - http://shoestrap.org
 * @link         http://www.shoestrap.org
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/
?>
<?php global $ss_framework; ?>
<?php echo $ss_framework->open_row( 'li', null, 'cart_item edd_subtotal' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 12 ) ); ?>
		<h4><?php echo __( 'Subtotal:', 'edd' ). " <span class='subtotal'>" . edd_currency_filter( edd_format_amount( edd_get_cart_subtotal() ) ); ?></span></h4>
	<?php echo $ss_framework->close_col( 'span' ); ?>
<?php echo $ss_framework->close_row( 'li' ); ?>
<?php echo $ss_framework->open_row( 'li', null, 'cart_item edd_checkout' ); ?>
	<?php echo $ss_framework->open_col( 'span', array( 'tablet' => 12 ) ); ?>
		<a class="<?php echo $ss_framework->button_classes( 'success', 'medium', null, 'btn-block expand' ); ?>" href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Checkout', 'edd' ); ?></a>
	<?php echo $ss_framework->close_col( 'span' ); ?>
<?php echo $ss_framework->close_row( 'li' );