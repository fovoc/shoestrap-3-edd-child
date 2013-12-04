<?php

/*
 * Extra functions in case EDD Simple Shipping is installed
 */
if ( class_exists( 'EDD_Simple_Shipping' ) ) :

	function shoestrap_edd_needs_shipping_fields() {
		return shoestrap_edd_cart_needs_shipping();
	}
	

	function shoestrap_edd_cart_needs_shipping() {
		$cart_contents = edd_get_cart_contents();
		$ret = false;
		if( is_array( $cart_contents ) ) {
			foreach( $cart_contents as $item ) {
				$price_id = isset( $item['options']['price_id'] ) ? (int) $item['options']['price_id'] : null;
				if( shoestrap_edd_item_has_shipping( $item['id'], $price_id ) ) {
					$ret = true;
					break;
				}
			}
		}
		return (bool) apply_filters( 'edd_simple_shipping_cart_needs_shipping', $ret );
	}


	function shoestrap_edd_item_has_shipping( $item_id = 0, $price_id = 0 ) {
		$enabled          = get_post_meta( $item_id, '_edd_enable_shipping', true );
		$variable_pricing = edd_has_variable_prices( $item_id );

		if( $variable_pricing && ! shoestrap_edd_price_has_shipping( $item_id, $price_id ) )
			$enabled = false;

		return (bool) apply_filters( 'edd_simple_shipping_item_has_shipping', $enabled, $item_id );
	}


	function shoestrap_edd_price_has_shipping( $item_id = 0, $price_id = 0 ) {
		$prices = edd_get_variable_prices( $item_id );
		$ret    = isset( $prices[ $price_id ]['shipping'] );
		return (bool) apply_filters( 'edd_simple_shipping_price_hasa_shipping', $ret, $item_id, $price_id );
	}


	function shoestrap_edd_has_billing_fields() {

		$did_action = did_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );
		if( ! $did_action && edd_use_taxes() )
			$did_action = did_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields' );

		// Have to assume all gateways are using the default CC fields (they should be)
		return ( $did_action || isset( $_POST['card_address'] ) );

	}


	if ( !function_exists( 'shoestrap_edd_simple_shipping_address_fields' ) ) :
	function shoestrap_edd_simple_shipping_address_fields() {
		global $EDD_Simple_Shipping;
		if( ! shoestrap_edd_needs_shipping_fields() )
			return;

		$display = shoestrap_edd_has_billing_fields() ? ' style="display:none;"' : '';

		ob_start(); ?>

		<div id="edd_simple_shipping">
			<?php if( shoestrap_edd_has_billing_fields() ) : ?>
				<fieldset id="edd_simple_shipping_diff_address">
					<label for="shoestrap_edd_simple_shipping_show">
						<input type="checkbox" id="shoestrap_edd_simple_shipping_show" name="edd_use_different_shipping" value="1"/>
						<?php _e( 'Ship to Different Address?', 'edd-simple-shipping' ); ?>
					</label>
				</fieldset>
			<?php endif; ?>
			<div id="shoestrap_edd_simple_shipping_fields_wrap"<?php echo $display; ?>>
				<fieldset id="edd_simple_shipping_fields">
					<?php do_action( 'edd_shipping_address_top' ); ?>
					<legend><?php _e( 'Shipping Details', 'edd-simple-shipping' ); ?></legend>

					<div class="form-group" id="edd-shipping-address-wrap">
						<label class="control-label col-md-3 edd-label"><?php _e( 'Shipping Address', 'edd-simple-shipping' ); ?></label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The address to ship your purchase to.', 'edd-simple-shipping' ); ?></small>
							<input type="text" name="shipping_address" class="form-control shipping-address edd-input" placeholder="<?php _e( 'Address line 1', 'edd-simple-shipping' ); ?>"/>
						</div>
					</div>

					<div class="form-group" id="edd-shipping-address-2-wrap">
						<label class="control-label col-md-3 edd-label"><?php _e( 'Shipping Address Line 2', 'edd-simple-shipping' ); ?></label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The suite, apt no, PO box, etc, associated with your shipping address.', 'edd-simple-shipping' ); ?></small>
							<input type="text" name="shipping_address_2" class="form-control shipping-address-2 edd-input" placeholder="<?php _e( 'Address line 2', 'edd-simple-shipping' ); ?>"/>
						</div>
					</div>

					<div class="form-group" id="edd-shipping-city-wrap">
						<label class="control-label col-md-3 edd-label"><?php _e( 'Shipping City', 'edd-simple-shipping' ); ?></label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The city for your shipping address.', 'edd-simple-shipping' ); ?></small>
							<input type="text" name="shipping_city" class="form-control shipping-city edd-input" placeholder="<?php _e( 'City', 'edd-simple-shipping' ); ?>"/>
						</div>
					</div>

					<div class="form-group" id="edd-shipping-country-wrap">
						<label class="control-label col-md-3 edd-label"><?php _e( 'Shipping Country', 'edd-simple-shipping' ); ?></label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The country for your shipping address.', 'edd-simple-shipping' ); ?></small>
							<select name="shipping_country" class="form-control shipping-country edd-select">
								<?php
								$countries = edd_get_country_list();
								foreach( $countries as $country_code => $country ) {
								  echo '<option value="' . $country_code . '">' . $country . '</option>';
								}
								?>
							</select>
						</div>
					</div>

					<div class="form-group" id="edd-shipping-state-wrap">
						<label class="control-label col-md-3 edd-label"><?php _e( 'Shipping State / Province', 'edd-simple-shipping' ); ?></label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The state / province for your shipping address.', 'edd-simple-shipping' ); ?></small>
							<input type="text" size="6" name="shipping_state_other" id="shipping_state_other" class="form-control shipping-state edd-input" placeholder="<?php _e( 'State / Province', 'edd-simple-shipping' ); ?>" style="display:none;"/>
							<select name="shipping_state_us" id="shipping_state_us" class="form-control shipping-state edd-select">
								<?php
									$states = edd_get_states_list();
									foreach( $states as $state_code => $state ) {
										echo '<option value="' . $state_code . '">' . $state . '</option>';
									}
								?>
							</select>
							<select name="shipping_state_ca" id="shipping_state_ca" class="form-control shipping-state edd-select" style="display: none;">
								<?php
									$provinces = edd_get_provinces_list();
									foreach( $provinces as $province_code => $province ) {
										echo '<option value="' . $province_code . '">' . $province . '</option>';
									}
								?>
							</select>
						</div>
					</div>

					<div class="form-group" id="edd-shipping-zip-wrap">
						<label class="control-label col-md-3 edd-label"><?php _e( 'Shipping Zip / Postal Code', 'edd-simple-shipping' ); ?></label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The zip / postal code for your shipping address.', 'edd-simple-shipping' ); ?></small>
							<input type="text" size="4" name="shipping_zip" class="form-control shipping-zip edd-input" placeholder="<?php _e( 'Zip / Postal code', 'edd-simple-shipping' ); ?>"/>
						</div>
					</div>

					<?php do_action( 'edd_shipping_address_bottom' ); ?>
				</fieldset>
			</div>
		</div>
<?php 	echo ob_get_clean();
	}
	endif;

remove_all_actions( 'edd_purchase_form_after_cc_form', 999 );
add_action( 'edd_purchase_form_after_cc_form', 'shoestrap_edd_simple_shipping_address_fields', 998 );
endif;


function shoestrap_edd_simple_shipping_address_fields_assets() {
	echo '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/assets/js/shipping.js' . '"></script>';
}
add_action( 'edd_purchase_form_after_cc_form', 'shoestrap_edd_simple_shipping_address_fields_assets' );
