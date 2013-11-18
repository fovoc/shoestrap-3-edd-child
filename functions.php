<?php

// Prioritize loading of some necessary core modules
require_once get_template_directory() . '/lib/modules/core.redux/module.php';
require_once get_template_directory() . '/lib/modules/core/module.php';
require_once get_template_directory() . '/lib/modules/core.layout/module.php';
require_once get_template_directory() . '/lib/modules/core.images/module.php';

// Load the EDD admin options
require_once get_stylesheet_directory() . '/lib/modules/edd/module.php';

// Load the EDD-Specific functions
require_once get_stylesheet_directory() . '/lib/edd-functions.php';
require_once get_stylesheet_directory() . '/lib/edd-widgets.php';

// Dequeue default EDD styles
remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

// Remove the default purchase link from the end of single downloads
// and add our custom one.
remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );
add_action( 'edd_after_download_content', 'shoestrap_edd_append_purchase_link', 30 );

// Add the custom variables pricing dropdown before the purchase link
// and remove the default radio boxes.
remove_action( 'edd_purchase_link_top', 'edd_purchase_variable_pricing', 10, 1 );
add_action( 'edd_purchase_link_top', 'shoestrap_edd_purchase_variable_pricing', 10, 1 );
