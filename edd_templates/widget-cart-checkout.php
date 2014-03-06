<?php global $ss_framework; ?>
<?php echo $ss_framework->open_row( 'li', null, 'cart_item edd_subtotal' ); ?>
	<h4><?php echo __( 'Subtotal:', 'edd' ). " <span class='subtotal'>" . edd_currency_filter( edd_format_amount( edd_get_cart_subtotal() ) ); ?></span></h4>
<?php echo $ss_framework->close_row( 'li' ); ?>
<?php echo $ss_framework->open_row( 'li', null, 'cart_item edd_checkout' ); ?>
	<a class="<?php echo $ss_framework->button_classes( 'success', 'medium', null, 'btn-block expand' ); ?>" href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Checkout', 'edd' ); ?></a>
<?php echo $ss_framework->close_row( 'li' );