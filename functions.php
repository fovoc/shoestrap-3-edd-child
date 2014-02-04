<?php

if ( !defined( 'REDUX_OPT_NAME' ) )
	define( 'REDUX_OPT_NAME', 'shoestrap' );

// Prioritize loading of some necessary core modules
if ( file_exists( get_template_directory() . '/lib/modules/redux/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/redux/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/layout/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/layout/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/blog/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/blog/module.php';
endif;

// Load the EDD admin options
require_once get_stylesheet_directory() . '/lib/admin.php';

// Load the EDD-Specific functions
if ( class_exists( 'Easy_Digital_Downloads' ) ) :
	require_once get_stylesheet_directory() . '/lib/edd-functions.php';
	require_once get_stylesheet_directory() . '/lib/edd-widgets.php';
	require_once get_stylesheet_directory() . '/lib/addons/edd-simple-shipping.php';
	require_once get_stylesheet_directory() . '/lib/addons/edd-variable-pricing-switcher.php';
endif;