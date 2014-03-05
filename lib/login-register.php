<?php

/**
The functions in this file are modified versions of those found in
easy-digital-downloads/includes/login-register.php

What is mostly modified here is the addition of some framework classes so that the CSS framework works without any issues.
*/

/**
 * Login Form
 *
 * @since 1.0
 * @global $edd_options
 * @global $post
 * @param string $redirect Redirect page URL
 * @return string Login form
*/
function ss_edd_login_form( $redirect = '' ) {
	global $edd_options, $post, $ss_framework;

	$input_class = $ss_framework->form_input_classes();
	if ( ! empty( $input_class ) ) {
		$input_class = ' ' . $input_class;
	} else {
		$input_class = null;
	}

	if ( empty( $redirect ) ) {
		$redirect = edd_get_current_page_url();
	}

	ob_start();

	if ( ! is_user_logged_in() ) {

		// Show any error messages after form submission
		edd_print_errors(); ?>
		<form id="edd_login_form" class="edd_form" action="" method="post">
			<fieldset>
				<span><legend><?php _e( 'Log into Your Account', 'edd' ); ?></legend></span>
				<?php do_action( 'edd_login_fields_before' ); ?>
				<p>
					<label for="edd_user_Login"><?php _e( 'Username', 'edd' ); ?></label>
					<input name="edd_user_login" id="edd_user_login" class="<?php echo $input_class; ?>required" type="text" title="<?php _e( 'Username', 'edd' ); ?>"/>
				</p>
				<p>
					<label for="edd_user_pass"><?php _e( 'Password', 'edd' ); ?></label>
					<input name="edd_user_pass" id="edd_user_pass" class="<?php echo $input_class; ?>password required" type="password"/>
				</p>
				<p>
					<input type="hidden" name="edd_redirect" value="<?php echo $redirect; ?>"/>
					<input type="hidden" name="edd_login_nonce" value="<?php echo wp_create_nonce( 'edd-login-nonce' ); ?>"/>
					<input type="hidden" name="edd_action" value="user_login"/>
					<input id="edd_login_submit" type="submit" class="<?php echo $ss_framework->button_classes( 'primary', 'medium', null, 'edd_submit' ); ?>" value="<?php _e( 'Login', 'edd' ); ?>"/>
				</p>
				<p class="edd-lost-password">
					<a class="<?php echo $ss_framework->button_classes( 'default', 'medium', null, 'edd_submit' ); ?>" href="<?php echo wp_lostpassword_url(); ?>" title="<?php _e( 'Lost Password', 'edd' ); ?>">
						<?php _e( 'Lost Password?', 'edd' ); ?>
					</a>
				</p>
				<?php do_action( 'edd_login_fields_after' ); ?>
			</fieldset>
		</form>
	<?php
	} else {
		echo $ss_framework->alert( 'success', __( 'You are already logged in', 'edd' ), null, 'edd-logged-in' );
	}
	return apply_filters( 'edd_login_form', ob_get_clean() );
}
