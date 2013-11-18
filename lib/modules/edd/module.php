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

  $section['fields'] = $fields;

  $section = apply_filters( 'shoestrap_module_edd_options_modifier', $section );
  
  $sections[] = $section;
  return $sections;
}
add_filter( 'redux-sections-' . REDUX_OPT_NAME, 'shoestrap_module_edd_options', 1 );   
endif;