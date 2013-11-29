<?php

/*
 * Shoestrap EDD Addon options
 */
if ( !function_exists( 'shoestrap_module_edd_options' ) ) :
function shoestrap_module_edd_options( $sections ) {

  $section = array(
    'title' => __( 'Easy Digital Downloads', 'shoestrap' ),
    'icon'  => 'el-icon-shopping-cart icon-large'
  );

  $fields[] = array( 
    'title'     => __( 'Display products on the frontpage instead of archive of posts.', 'shoestrap_edd' ),
    'desc'      => __( 'Default: On.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_frontpage',
    'default'   => 1,
    'customizer'=> array(),
    'type'      => 'switch'
  );

  $fields[] = array( 
    'title'     => __( 'Products width on lists.', 'shoestrap_edd' ),
    'desc'      => '',
    'id'        => 'shoestrap_edd_products_width',
    'default'   => 'default',
    'type'      => 'select',
    'options'   => array(
      'narrow'  => 'Narrow',
      'normal'  => 'Normal',
      'wide'    => 'Wide',
    ),
  );

  $fields[] = array( 
    'title'     => __( 'Select between box styles', 'shoestrap' ),
    'desc'      => __( 'Select between box styles.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_box_style',
    'type'      => 'button_set',
    'default'   => 'default',
    'options'   => array(
      'default' => 'Default',
      'well'    => 'Well',
      'panel'   => 'Panel'
    ),
  );

  $fields[] = array( 
    'title'     => __( 'Show Product description in lists', 'shoestrap_edd' ),
    'desc'      => __( 'Show the excerpt of the products body on product archives.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_show_text_in_lists',
    'default'   => 1,
    'type'      => 'switch',
  );

  $fields[] = array( 
    'title'     => __( 'Show the cart on the NavBar', 'shoestrap_edd' ),
    'desc'      => __( 'Show a link to the cart with totals in the navbar.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_navbar_cart',
    'default'   => 1,
    'type'      => 'switch',
  );

  $fields[] = array( 
    'title'     => __( 'Show the cart on the NavBar', 'shoestrap_edd' ),
    'desc'      => __( 'Show a link to the cart with totals in the navbar.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_minicart_label',
    'default'   => __( 'Checkout', 'edd' ),
    'type'      => 'text',
    'required'  => array( 'shoestrap_edd_navbar_cart','=',array( '1' ) ),
  );

  $section['fields'] = $fields;

  $section = apply_filters( 'shoestrap_module_edd_options_modifier', $section );
  
  $sections[] = $section;
  return $sections;
}
add_filter( 'redux-sections-' . REDUX_OPT_NAME, 'shoestrap_module_edd_options', 1 );   
endif;

if ( !function_exists( 'shoestrap_edd_child_licensing' ) ) :
function shoestrap_edd_child_licensing($section) {
  $section['fields'][] = array( 
    'title'           => __( 'Shoestrap EDD Child-Theme Licence', 'shoestrap' ),
    'id'              => 'shoestrap_edd_child_license_key',
    'default'         => '',
    'type'            => 'edd_license',
    'mode'            => 'theme', // theme|plugin
    'path'            => '', // Path to the plugin/template main file
    'remote_api_url'  => 'http://shoestrap.org',    // our store URL that is running EDD
    'version'         => '1.0.2', // current version number
    'item_name'       => 'Shoestrap 3 EDD Child', // name of this theme
    'author'          => 'Aristeides Stathopoulos (@aristath), Dimitris Kalliris (@fovoc)', // author of this theme
    'field_id'        => "shoestrap_edd_child_license_key", // ID of the field used by EDD
  ); 
  return $section;
}
endif;
add_filter( 'shoestrap_module_licencing_options_modifier', 'shoestrap_edd_child_licensing' );