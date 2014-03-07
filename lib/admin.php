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
    'title'     => __( 'NavBar Cart Label', 'shoestrap_edd' ),
    'desc'      => __( 'Label of Cart in NavBar.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_minicart_label',
    'default'   => __( 'Checkout', 'edd' ),
    'type'      => 'text',
    'required'  => array( 'shoestrap_edd_navbar_cart','=',array( '1' ) ),
  );

  $fields[] = array( 
    'title'     => __( 'Enable EqualHeights', 'shoestrap' ),
    'desc'      => __( 'Default: Off.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_equalheights',
    'default'   => 0,
    'type'      => 'switch'
  );

  $fields[] = array( 
    'title'     => __( 'Enable Infinite Scroll', 'shoestrap' ),
    'desc'      => __( 'Default: Off.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_infinite_scroll',
    'default'   => 0,
    'type'      => 'switch'
  );

  $fields[] = array( 
    'title'     => __( 'Loading text', 'shoestrap' ),
    'desc'      => __( 'The text inside the progress bar as next set is loading.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_loading_text',
    'default'   => 'Loading...',
    'type'      => 'text',
    'required'  => array( 'shoestrap_edd_infinite_scroll','=',array( '1' ) ),
  );

  $fields[] = array( 
    'title'     => __( 'End text', 'shoestrap' ),
    'desc'      => __( 'The text inside the progress bar when no more posts are available.', 'shoestrap' ),
    'id'        => 'shoestrap_edd_end_text',
    'default'   => 'End of list',
    'type'      => 'text',
    'required'  => array( 'shoestrap_edd_infinite_scroll','=',array( '1' ) ),
  );

  $fields[] = array( 
    'title'     => __( 'Loading progress bar color', 'shoestrap' ),
    'desc'      => __( 'Select between standard Bootstrap\'s progress bars classes', 'shoestrap' ),
    'id'        => 'shoestrap_edd_loading_color',
    'default'   => ' ',
    'type'      => 'select',
    'customizer'=> array(),
    'options'   => array( 
      'default' => 'Default',
      'info'    => 'Info',
      'success' => 'Success',
      'warning' => 'Warning',
      'danger'  => 'Danger'
    ),
    'required'  => array( 'shoestrap_edd_infinite_scroll','=',array( '1' ) ),
  );

  $fields[] = array( 
    'title'     => __( 'End progress bar color', 'shoestrap' ),
    'desc'      => __( 'Select between standard Bootstrap\'s progress bars classes', 'shoestrap' ),
    'id'        => 'shoestrap_edd_end_color',
    'default'   => ' ',
    'type'      => 'select',
    'customizer'=> array(),
    'options'   => array( 
      'default' => 'Default',
      'info'    => 'Info',
      'success' => 'Success',
      'warning' => 'Warning',
      'danger'  => 'Danger'
    ),
    'required'  => array( 'shoestrap_edd_infinite_scroll','=',array( '1' ) ),
  );

  $section['fields'] = $fields;

  $section = apply_filters( 'shoestrap_module_edd_options_modifier', $section );
  
  $sections[] = $section;
  return $sections;
}
// add_filter( 'redux-sections-' . REDUX_OPT_NAME, 'shoestrap_module_edd_options', 1 );   
add_filter( 'redux/options/' . REDUX_OPT_NAME . '/sections', 'shoestrap_module_edd_options', 1 );   
endif;