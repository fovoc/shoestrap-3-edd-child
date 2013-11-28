<?php

if ( class_exists( 'EDD_Variable_Pricing_Switcher' ) ) :

	global $EDD_Variable_Pricing_Switcher;
	remove_action( 'edd_before_purchase_form', array( $EDD_Variable_Pricing_Switcher, 'checkout_addition' ), 10 );
	add_action( 'edd_before_purchase_form', 'shoestrap_edd_variable_pricing_switcher_checkout_addition', 10 );

	if ( !function_exists( 'shoestrap_edd_variable_pricing_switcher_checkout_addition' ) ) :
	function shoestrap_edd_variable_pricing_switcher_checkout_addition() {
		global $edd_options, $user_ID, $post;

		$cart = edd_get_cart_contents();

		$pricing_switchers = '';
		foreach( $cart as $cart_item ) {

			// Check if variable pricing switcher is enabled for this download
			$enabled = get_post_meta( $cart_item[ 'id' ], '_edd_vps_enabled', true ) ? true : false;
			if( ! $enabled ) {
				continue;
			}

			// Check if the product has variable prices
			if( !edd_has_variable_prices( $cart_item[ 'id' ] ) ) {
				continue;
			}

			// Fix the price_id option if it doesn't exists
			if( ! isset( $cart_item[ 'options' ][ 'price_id' ] ) ) {
				$cart_item[ 'options' ][ 'price_id' ] = 0;
			}

			// Get pricing options
			$pricing_options = edd_get_variable_prices( $cart_item[ 'id' ] );

			// We need more than one pricing option
			if( count( $pricing_options ) < 2 ) {
				return;
			}

			$item_title = get_the_title( $cart_item[ 'id' ] );

			// Add select box
			$pricing_switchers .= "<select name='edd-variable-pricing-switcher[{$cart_item[ 'id' ]}]' class='form-control edd-variable-pricing-switcher'>\n";
				foreach( $pricing_options as $pricing_id => $pricing_option ) {
					$pricing_switchers .= "<option value='{$pricing_id}'" . ( ( $pricing_id == $cart_item[ 'options' ][ 'price_id' ] ) ? " selected='selected'" : "" ) . ">{$item_title} | {$pricing_option[ 'name' ]} - " . edd_currency_filter( edd_format_amount( $pricing_option[ 'amount' ] ) ) . "</option>\n";
				}
			$pricing_switchers .= "</select>\n";

		}

		if( $pricing_switchers == '' ) {
			return;
		}

		// Get label
		$vps_label = ( ( isset( $edd_options[ 'vps_label' ] ) ) ? $edd_options[ 'vps_label' ] : 'License' );

	?>
	<form name="edd_variable_pricing_switcher" action="<?php echo edd_get_checkout_uri(); ?>" method="post">
		<fieldset id="edd_variable_pricing_switcher-fieldset">
			<span><legend><?php echo $vps_label; ?></legend></span>
			<?php echo $pricing_switchers; ?>
		</fieldset>
	</form>

	<?php
		// Only show discount fieldset if the normal cart is disabled
		if( isset( $edd_options[ 'vps_disable_cart' ] ) && $edd_options[ 'vps_disable_cart' ] == '1' ) {
	?>
		<fieldset id="edd_variable_pricing_switcher_discounts"<?php if( ! edd_cart_has_discounts() )  echo ' style="display:none;"'; ?>>
			<span><legend><?php _e( 'DISCOUNT', 'edd' ); ?></legend></span>
			<div>
			<?php
			if( edd_cart_has_discounts() ) {
				echo edd_get_cart_discounts_html();
			}
			?>
			</div>
		</fieldset>
	<?php
		}
	?>

	<?php
	}
	endif;
endif;