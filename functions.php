<?php

// Prioritize loading of some necessary core modules


// Load the EDD-Specific functions

function shoestrap_edd_include_files() {
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		require_once dirname( __FILE__ ) . '/lib/class-Shoestrap_EDD.php';
		require_once dirname( __FILE__ ) . '/lib/checkout-template.php';
		require_once dirname( __FILE__ ) . '/lib/login-register.php';
		require_once dirname( __FILE__ ) . '/lib/edd-shortcodes.php';
		require_once dirname( __FILE__ ) . '/lib/edd-widgets.php';
		require_once dirname( __FILE__ ) . '/lib/addons/edd-simple-shipping.php';
		require_once dirname( __FILE__ ) . '/lib/addons/edd-variable-pricing-switcher.php';
	}
}
add_action( 'shoestrap_include_files', 'shoestrap_edd_include_files' );


function shoestrap_edd_updater() {

	$args = array(
		'remote_api_url' => 'http://shoestrap.org',
		'item_name'      => 'Shoestrap 3 EDD Child',
		'version'        => '1.1',
		'author'         => 'aristath, fovoc',
		'mode'           => 'theme',
		'title'          => 'Shoestrap 3 EDD Child Theme License',
		'field_name'     => 'shoestrap_edd_license',
		'description'    => 'The licence key provided with Shoestrap 3 EDD Child Theme.',
		'single_license' => false
	);

	if ( class_exists( 'SS_EDD_SL_Updater' ) ) {
		$updater = new SS_EDD_SL_Updater( $args );
	}

}
add_action( 'admin_init', 'shoestrap_edd_updater' );