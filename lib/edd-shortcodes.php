<?php

/**
 * Discounts short code
 *
 * Displays a list of all the active discounts. The active discounts can be configured
 * from the Discount Codes admin screen.
 *
 * @since 1.0.8.2
 * @param array $atts Shortcode attributes
 * @param string $content
 * @uses edd_get_discounts()
 * @return string $discounts_lists List of all the active discount codes
 */
function ss_edd_discounts_shortcode( $atts, $content = null ) {
	$discounts = edd_get_discounts();

	$discounts_list = '<ul id="edd_discounts_list" class="list-group">';

	if ( ! empty( $discounts ) && edd_has_active_discounts() ) {

		foreach ( $discounts as $discount ) {

			if ( edd_is_discount_active( $discount->ID ) ) {

				$discounts_list .= '<li class="edd_discount list-group-item">';

					$discounts_list .= '<span class="edd_discount_name">' . edd_get_discount_code( $discount->ID ) . '</span>';
					$discounts_list .= '<span class="edd_discount_amount pull-right label label-success">' . edd_format_discount_rate( edd_get_discount_type( $discount->ID ), edd_get_discount_amount( $discount->ID ) ) . '</span>';

				$discounts_list .= '</li>';

			}

		}

	} else {
		$discounts_list .= '<li class="edd_discount list-group-item">' . __( 'No discounts found', 'edd' ) . '</li>';
	}

	$discounts_list .= '</ul>';

	return $discounts_list;
}
remove_shortcode( 'download_discounts', 'edd_discounts_shortcode' );
add_shortcode( 'download_discounts', 'ss_edd_discounts_shortcode' );

/**
 * Login Shortcode
 *
 * Shows a login form allowing users to users to log in. This function simply
 * calls the edd_login_form function to display the login form.
 *
 * @since 1.0
 * @param array $atts Shortcode attributes
 * @param string $content
 * @uses edd_login_form()
 * @return string
 */
function ss_edd_login_form_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'redirect' => '',
		), $atts, 'edd_login' )
	);
	return ss_edd_login_form( $redirect );
}
remove_shortcode( 'edd_login', 'edd_login_form_shortcode' );
add_shortcode( 'edd_login', 'ss_edd_login_form_shortcode' );