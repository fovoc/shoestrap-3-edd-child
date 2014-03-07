<?php
/**
 * Empty Cart Template
 *
 * @package      Shoestrap Easy Digital Downloads Child Theme
 * @author       Shoestrap - http://shoestrap.org
 * @link         http://www.shoestrap.org
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/
?>
<li class="cart_item empty"><?php echo edd_empty_cart_message(); ?></li>
<li class="cart_item edd_subtotal" style="display:none;"><?php echo __( 'Subtotal:', 'edd' ). " <span class='subtotal'>" . edd_currency_filter( edd_get_cart_subtotal() ); ?></span></li>
<li class="cart_item edd_checkout" style="display:none;"><a href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Checkout', 'edd' ); ?></a></li>
