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

// Add the custom variables pricing dropdown before the purchase link
// and remove the default radio boxes.
remove_action( 'edd_purchase_link_top', 'edd_purchase_variable_pricing', 10, 1 );
add_action( 'edd_purchase_link_top', 'shoestrap_edd_purchase_variable_pricing', 10, 1 );


function shoestrap_edd_assets() {
	// Register && Enqueue MixItUp
	wp_register_script('shoestrap_edd_mixitup', get_stylesheet_directory_uri() . '/assets/js/jquery.mixitup.min.js', false, null, true);
	wp_enqueue_script('shoestrap_edd_mixitup');

	// Register && Enqueue jQuery EqualHeights
	wp_register_script('shoestrap_edd_equalheights', get_stylesheet_directory_uri() . '/assets/js/jquery.equalheights.min.js', false, null, true);
	wp_enqueue_script('shoestrap_edd_equalheights');

	if ( is_post_type_archive( 'download' ) || ( shoestrap_getVariable( 'shoestrap_edd_frontpage' ) == 1 && is_front_page() ) ) :
		// Here triggers the MixItiUp && EqualHeights
		add_action( 'wp_footer', function() { echo '<script>$(function(){$(".product-list").mixitup();$(".product-list .equal").equalHeights();});</script>'; }, 99 );
	elseif ( isset( $wp_query->query_vars['taxonomy'] ) && ( $wp_query->query_vars['taxonomy'] == 'download_category' ) ) :
		// Here triggers the MixItiUp && EqualHeights
		add_action( 'wp_footer', function() { echo '<script>$(function(){$(".product-list").mixitup();$(".product-list .equal").equalHeights();});</script>'; }, 99 );
	elseif ( isset( $wp_query->query_vars['taxonomy'] ) && ( $wp_query->query_vars['taxonomy'] == 'download_tag' ) ) :
		// Here triggers the MixItiUp && EqualHeights
		add_action( 'wp_footer', function() { echo '<script>$(function(){$(".product-list").mixitup();$(".product-list .equal").equalHeights();});</script>'; }, 99 );
	endif;

	add_action( 'wp_head', function() { echo '<style>.product-list .mix{opacity: 0;display: none;}</style>'; });
}
add_action( 'wp_head', 'shoestrap_edd_assets', 99 );


// Script to increase the total cart quantity in navbar-cart
function shoestrap_edd_increase_navbar_cart_quantity(){
	echo '<script type="text/javascript">jQuery(document).ready(function(){$(".edd-add-to-cart").click(function(){$("#nav-cart-quantity").html(function(i, val){ return val*1+1 });});});</script>';
}
add_action('wp_head','shoestrap_edd_increase_navbar_cart_quantity');