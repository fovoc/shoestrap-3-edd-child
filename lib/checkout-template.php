<?php

/**
The functions in this file are modified versions of those found in
easy-digital-downloads/includes/checkout/template.php

What is mostly modified here is the addition of some framework classes so that the CSS framework works without any issues.
*/

/**
 * Outputs the default credit card address fields
 *
 * @since 1.0
 * @return void
 */
function ss_edd_default_cc_address_fields() {
	global $ss_framework;

	$input_class = $ss_framework->form_input_classes();
	if ( ! empty( $input_class ) ) {
		$input_class = $input_class . ' ';
	} else {
		$input_class = null;
	}

	$logged_in = is_user_logged_in();

	if( $logged_in ) {
		$user_address = get_user_meta( get_current_user_id(), '_edd_user_address', true );
	}
	$line1 = $logged_in && ! empty( $user_address['line1'] ) ? $user_address['line1'] : '';
	$line2 = $logged_in && ! empty( $user_address['line2'] ) ? $user_address['line2'] : '';
	$city  = $logged_in && ! empty( $user_address['city']  ) ? $user_address['city']  : '';
	$zip   = $logged_in && ! empty( $user_address['zip']   ) ? $user_address['zip']   : '';
	ob_start(); ?>
	<fieldset id="edd_cc_address" class="cc-address">
		<span><legend><?php _e( 'Billing Details', 'edd' ); ?></legend></span>
		<?php do_action( 'edd_cc_billing_top' ); ?>
		<p id="edd-card-address-wrap">
			<label for="card_address" class="edd-label">
				<?php _e( 'Billing Address', 'edd' ); ?>
				<?php if( edd_field_is_required( 'card_address' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The primary billing address for your credit card.', 'edd' ); ?></span>
			<input type="text" id="card_address" name="card_address" class="<?php echo $input_class; ?>card-address edd-input<?php if( edd_field_is_required( 'card_address' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line 1', 'edd' ); ?>" value="<?php echo $line1; ?>"/>
		</p>
		<p id="edd-card-address-2-wrap">
			<label for="card_address_2" class="edd-label">
				<?php _e( 'Billing Address Line 2 (optional)', 'edd' ); ?>
				<?php if( edd_field_is_required( 'card_address_2' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The suite, apt no, PO box, etc, associated with your billing address.', 'edd' ); ?></span>
			<input type="text" id="card_address_2" name="card_address_2" class="<?php echo $input_class; ?>card-address-2 edd-input<?php if( edd_field_is_required( 'card_address_2' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line 2', 'edd' ); ?>" value="<?php echo $line2; ?>"/>
		</p>
		<p id="edd-card-city-wrap">
			<label for="card_city" class="edd-label">
				<?php _e( 'Billing City', 'edd' ); ?>
				<?php if( edd_field_is_required( 'card_city' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The city for your billing address.', 'edd' ); ?></span>
			<input type="text" id="card_city" name="card_city" class="<?php echo $input_class; ?>card-city edd-input<?php if( edd_field_is_required( 'card_city' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'City', 'edd' ); ?>" value="<?php echo $city; ?>"/>
		</p>
		<p id="edd-card-zip-wrap">
			<label for="card_zip" class="edd-label">
				<?php _e( 'Billing Zip / Postal Code', 'edd' ); ?>
				<?php if( edd_field_is_required( 'card_zip' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'edd' ); ?></span>
			<input type="text" size="4" name="card_zip" class="<?php echo $input_class; ?>card-zip edd-input<?php if( edd_field_is_required( 'card_zip' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Zip / Postal code', 'edd' ); ?>" value="<?php echo $zip; ?>"/>
		</p>
		<p id="edd-card-country-wrap">
			<label for="billing_country" class="edd-label">
				<?php _e( 'Billing Country', 'edd' ); ?>
				<?php if( edd_field_is_required( 'billing_country' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The country for your billing address.', 'edd' ); ?></span>
			<select id="billing_country" name="billing_country" id="billing_country" class="<?php echo $input_class; ?>billing_country edd-select<?php if( edd_field_is_required( 'billing_country' ) ) { echo ' required'; } ?>">
				<?php

				$selected_country = edd_get_shop_country();

				if( $logged_in && ! empty( $user_address['country'] ) && '*' !== $user_address['country'] ) {
					$selected_country = $user_address['country'];
				}

				$countries = edd_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . $country_code . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>
		<p id="edd-card-state-wrap">
			<label for="card_state" class="edd-label">
				<?php _e( 'Billing State / Province', 'edd' ); ?>
				<?php if( edd_field_is_required( 'card_state' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The state or province for your billing address.', 'edd' ); ?></span>
            <?php
            $selected_state = edd_get_shop_state();
            $states         = edd_get_shop_states( $selected_country );

            if( $logged_in && ! empty( $user_address['state'] ) ) {
				$selected_state = $user_address['state'];
			}

            if( ! empty( $states ) ) : ?>
            <select id="card_state" name="card_state" id="card_state" class="<?php echo $input_class; ?>card_state edd-select<?php if( edd_field_is_required( 'card_state' ) ) { echo ' required'; } ?>">
                <?php
                    foreach( $states as $state_code => $state ) {
                        echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
                    }
                ?>
            </select>
        	<?php else : ?>
			<input type="text" size="6" name="card_state" id="card_state" class="<?php echo $input_class; ?>card_state edd-input" placeholder="<?php _e( 'State / Province', 'edd' ); ?>"/>
			<?php endif; ?>
		</p>
		<?php do_action( 'edd_cc_billing_bottom' ); ?>
	</fieldset>
	<?php
	echo ob_get_clean();
}
remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );
add_action( 'edd_after_cc_fields', 'ss_edd_default_cc_address_fields' );

/**
 * Renders the billing address fields for cart taxation
 *
 * @since 1.6
 * @return void
 */
function ss_edd_checkout_tax_fields() {
	if( edd_cart_needs_tax_address_fields() && edd_get_cart_total() )
		ss_edd_default_cc_address_fields();
}
remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields', 999 );
add_action( 'edd_purchase_form_after_cc_form', 'ss_edd_checkout_tax_fields', 999 );

/**
 * Renders the payment mode form by getting all the enabled payment gateways and
 * outputting them as radio buttons for the user to choose the payment gateway. If
 * a default payment gateway has been chosen from the EDD Settings, it will be
 * automatically selected.
 *
 * @since 1.2.2
 * @return void
 */
function edd_payment_mode_select() {
	global $ss_framework;

	$input_class = $ss_framework->form_input_classes();
	if ( ! empty( $input_class ) ) {
		$input_class = $input_class . ' ';
	} else {
		$input_class = null;
	}

	$gateways = edd_get_enabled_payment_gateways();
	$page_URL = edd_get_current_page_url();
	do_action('edd_payment_mode_top'); ?>
	<?php if( ! edd_is_ajax_enabled() ) { ?>
	<form id="edd_payment_mode" action="<?php echo $page_URL; ?>" method="GET">
	<?php } ?>
		<fieldset id="edd_payment_mode_select">
			<?php do_action( 'edd_payment_mode_before_gateways_wrap' ); ?>
			<div id="edd-payment-mode-wrap">
				<span class="edd-payment-mode-label"><?php _e( 'Select Payment Method', 'edd' ); ?></span><br/>
				<?php

				do_action( 'edd_payment_mode_before_gateways' );

				foreach ( $gateways as $gateway_id => $gateway ) :
					$checked = checked( $gateway_id, edd_get_default_gateway(), false );
					$checked_class = $checked ? ' edd-gateway-option-selected' : '';
					echo '<label for="edd-gateway-' . esc_attr( $gateway_id ) . '" class="edd-gateway-option' . $checked_class . '" id="edd-gateway-option-' . esc_attr( $gateway_id ) . '">';
						echo '<input type="radio" name="payment-mode" class="' . $input_class . 'edd-gateway" id="edd-gateway-' . esc_attr( $gateway_id ) . '" value="' . esc_attr( $gateway_id ) . '"' . $checked . '>' . esc_html( $gateway['checkout_label'] );
					echo '</label>';
				endforeach;

				do_action( 'edd_payment_mode_after_gateways' );

				?>
			</div>
			<?php do_action( 'edd_payment_mode_after_gateways_wrap' ); ?>
		</fieldset>
		<fieldset id="edd_payment_mode_submit" class="edd-no-js">
			<p id="edd-next-submit-wrap">
				<?php echo edd_checkout_button_next(); ?>
			</p>
		</fieldset>
	<?php if( ! edd_is_ajax_enabled() ) { ?>
	</form>
	<?php } ?>
	<div id="edd_purchase_form_wrap"></div><!-- the checkout fields are loaded into this-->
	<?php do_action('edd_payment_mode_bottom');
}
remove_action( 'edd_payment_mode_select', 'edd_payment_mode_select' );
add_action( 'edd_payment_mode_select', 'ss_edd_payment_mode_select' );

/**
 * Shows the User Info fields in the Personal Info box, more fields can be added
 * via the hooks provided.
 *
 * @since 1.3.3
 * @return void
 */
function ss_edd_user_info_fields() {
	global $ss_framework;

	$input_class = $ss_framework->form_input_classes();
	if ( ! empty( $input_class ) ) {
		$input_class = $input_class . ' ';
	} else {
		$input_class = null;
	}

	if ( is_user_logged_in() ) :
		$user_data = get_userdata( get_current_user_id() );
	endif;
	?>
	<fieldset id="edd_checkout_user_info">
		<span><legend><?php echo apply_filters( 'edd_checkout_personal_info_text', __( 'Personal Info', 'edd' ) ); ?></legend></span>
		<?php do_action( 'edd_purchase_form_before_email' ); ?>
		<p id="edd-email-wrap">
			<label class="edd-label" for="edd-email">
				<?php _e( 'Email Address', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_email' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'We will send the purchase receipt to this address.', 'edd' ); ?></span>
			<input class="<?php echo $input_class; ?>edd-input required" type="email" name="edd_email" placeholder="<?php _e( 'Email address', 'edd' ); ?>" id="edd-email" value="<?php echo is_user_logged_in() ? $user_data->user_email : ''; ?>"/>
		</p>
		<?php do_action( 'edd_purchase_form_after_email' ); ?>
		<p id="edd-first-name-wrap">
			<label class="edd-label" for="edd-first">
				<?php _e( 'First Name', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_first' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'We will use this to personalize your account experience.', 'edd' ); ?></span>
			<input class="<?php echo $input_class; ?>edd-input required" type="text" name="edd_first" placeholder="<?php _e( 'First name', 'edd' ); ?>" id="edd-first" value="<?php echo is_user_logged_in() ? $user_data->first_name : ''; ?>"/>
		</p>
		<p id="edd-last-name-wrap">
			<label class="edd-label" for="edd-last">
				<?php _e( 'Last Name', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_last' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'We will use this as well to personalize your account experience.', 'edd' ); ?></span>
			<input class="<?php echo $input_class; ?>edd-input<?php if( edd_field_is_required( 'edd_last' ) ) { echo ' required'; } ?>" type="text" name="edd_last" id="edd-last" placeholder="<?php _e( 'Last name', 'edd' ); ?>" value="<?php echo is_user_logged_in() ? $user_data->last_name : ''; ?>"/>
		</p>
		<?php do_action( 'edd_purchase_form_user_info' ); ?>
	</fieldset>
	<?php
}
remove_action( 'edd_purchase_form_after_user_info', 'edd_user_info_fields' );
add_action( 'edd_purchase_form_after_user_info', 'ss_edd_user_info_fields' );
remove_action( 'edd_register_fields_before', 'edd_user_info_fields' );
add_action( 'edd_register_fields_before', 'ss_edd_user_info_fields' );

/**
 * Renders the credit card info form.
 *
 * @since 1.0
 * @return void
 */
function ss_edd_get_cc_form() {
	global $ss_framework;

	$input_class = $ss_framework->form_input_classes();
	if ( ! empty( $input_class ) ) {
		$input_class = $input_class . ' ';
	} else {
		$input_class = null;
	}

	ob_start(); ?>

	<?php do_action( 'edd_before_cc_fields' ); ?>

	<fieldset id="edd_cc_fields" class="edd-do-validate">
		<span><legend><?php _e( 'Credit Card Info', 'edd' ); ?></legend></span>
		<?php if( is_ssl() ) : ?>
			<div id="edd_secure_site_wrapper">
				<?php echo $ss_framework->clearfix(); ?>
				<?php echo $ss_framework->alert( 'info', __( 'This is a secure SSL encrypted payment.', 'edd' ) ); ?>
			</div>
		<?php endif; ?>
		<p id="edd-card-number-wrap">
			<label for="card_number" class="edd-label">
				<?php _e( 'Card Number', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
				<span class="card-type"></span>
			</label>
			<span class="edd-description"><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'edd' ); ?></span>
			<input type="text" autocomplete="off" name="card_number" id="card_number" class="<?php echo $input_class; ?>card-number edd-input required" placeholder="<?php _e( 'Card number', 'edd' ); ?>" />
		</p>
		<p id="edd-card-cvc-wrap">
			<label for="card_cvc" class="edd-label">
				<?php _e( 'CVC', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'edd' ); ?></span>
			<input type="text" size="4" autocomplete="off" name="card_cvc" id="card_cvc" class="<?php echo $input_class; ?>card-cvc edd-input required" placeholder="<?php _e( 'Security code', 'edd' ); ?>" />
		</p>
		<p id="edd-card-name-wrap">
			<label for="card_name" class="edd-label">
				<?php _e( 'Name on the Card', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The name printed on the front of your credit card.', 'edd' ); ?></span>
			<input type="text" autocomplete="off" name="card_name" id="card_name" class="<?php echo $input_class; ?>card-name edd-input required" placeholder="<?php _e( 'Card name', 'edd' ); ?>" />
		</p>
		<?php do_action( 'edd_before_cc_expiration' ); ?>
		<p class="card-expiration">
			<label for="card_exp_month" class="edd-label">
				<?php _e( 'Expiration (MM/YY)', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The date your credit card expires, typically on the front of the card.', 'edd' ); ?></span>
			<select id="card_exp_month" name="card_exp_month" class="<?php echo $input_class; ?>card-expiry-month edd-select edd-select-small required">
				<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
			</select>
			<span class="exp-divider"> / </span>
			<select id="card_exp_year" name="card_exp_year" class="<?php echo $input_class; ?>card-expiry-year edd-select edd-select-small required">
				<?php for( $i = date('Y'); $i <= date('Y') + 10; $i++ ) { echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>'; } ?>
			</select>
		</p>
		<?php do_action( 'edd_after_cc_expiration' ); ?>

	</fieldset>
	<?php
	do_action( 'edd_after_cc_fields' );

	echo ob_get_clean();
}
remove_action( 'edd_cc_form', 'edd_get_cc_form' );
add_action( 'edd_cc_form', 'ss_edd_get_cc_form' );
