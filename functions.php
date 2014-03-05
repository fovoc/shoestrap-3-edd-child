<?php

// Prioritize loading of some necessary core modules


// Load the EDD-Specific functions

function shoestrap_edd_include_files() {
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		require_once dirname( __FILE__ ) . '/lib/admin.php';
		require_once dirname( __FILE__ ) . '/lib/edd-functions.php';
		require_once dirname( __FILE__ ) . '/lib/checkout-template.php';
		require_once dirname( __FILE__ ) . '/lib/login-register.php';
		require_once dirname( __FILE__ ) . '/lib/edd-shortcodes.php';
		require_once dirname( __FILE__ ) . '/lib/edd-widgets.php';
		require_once dirname( __FILE__ ) . '/lib/addons/edd-simple-shipping.php';
		require_once dirname( __FILE__ ) . '/lib/addons/edd-variable-pricing-switcher.php';
	}
}
add_action( 'shoestrap_include_files', 'shoestrap_edd_include_files' );