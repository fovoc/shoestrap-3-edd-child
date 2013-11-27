<?php

// Prioritize loading of some necessary core modules
if ( file_exists( get_template_directory() . '/lib/modules/core.redux/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core.redux/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core.layout/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core.layout/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core.images/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core.images/module.php';
endif;

// Load the EDD admin options
require_once get_stylesheet_directory() . '/lib/admin.php';

// Load the EDD-Specific functions
if ( class_exists( 'Easy_Digital_Downloads' ) ) :
	require_once get_stylesheet_directory() . '/lib/edd-functions.php';
	require_once get_stylesheet_directory() . '/lib/edd-widgets.php';
endif;