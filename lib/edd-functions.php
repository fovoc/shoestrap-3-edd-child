<?php

/*
 * Add the mixitup template parts and an extra wrapper div
 */
function shoestrap_edd_mixitup_templates() {
	if ( is_post_type_archive( 'download' ) ) :
		get_template_part( 'templates/mixitup', 'sorting' );
		if ( !is_tax( 'download_category' ) ) :
			get_template_part( 'templates/mixitup', 'download_category' );
		elseif ( !is_tax( 'download_tag' ) ) :
			get_template_part( 'templates/mixitup', 'download_tag' );
		else :
			get_template_part( 'templates/mixitup', 'download_category' );
			get_template_part( 'templates/mixitup', 'download_tag' );
		endif;
	endif;
}
add_action( 'shoestrap_index_begin', 'shoestrap_edd_mixitup_templates', 12 );

/*
 * some necessary hacks to make templating work properly
 */
function shoestrap_edd_helper_actions() {
	if ( is_post_type_archive( 'download' ) ) :
		add_action( 'shoestrap_index_begin', function() { echo '<div class="clearfix"></div><div class="row product-list">'; } );
		add_action( 'shoestrap_index_end', function() { echo '</div>'; } );
		add_action( 'shoestrap_content_override', function() { get_template_part( 'templates/content-download' ); } );
	endif;
}
add_action( 'shoestrap_index_begin', 'shoestrap_edd_helper_actions', 13 );

function shoestrap_edd_append_purchase_link( $download_id ) {
	if ( ! get_post_meta( $download_id, '_edd_hide_purchase_link', true ) ) :
		echo edd_get_purchase_link( array( 'download_id' => $download_id, 'class' => 'btn btn-block', 'style' => 'btn-primary' ) );
	endif;
}


function shoestrap_edd_element_class() {
	$style = shoestrap_getVariable( 'shoestrap_edd_box_style' );

	if ( $style == 'well' ) :
		$maindivclass = 'well well-sm';
	elseif ( $style == 'panel' ) :
		$maindivclass = 'panel panel-default';
	else :
		$maindivclass = 'thumbnail';
	endif;

	return $maindivclass;	
}


/*
 * The Original price variables for EDD downloads is displayed as radio input.
 * The below function replaces that with a dropdown.
 */
function shoestrap_edd_purchase_variable_pricing( $download_id ) {
	$variable_pricing = edd_has_variable_prices( $download_id );
	if ( ! $variable_pricing ) :
		return;
	endif;

	$prices = apply_filters( 'edd_purchase_variable_prices', edd_get_variable_prices( $download_id ), $download_id );
	$type   = edd_single_price_option_mode( $download_id ) ? 'checkbox' : 'radio';

	do_action( 'edd_before_price_options', $download_id ); ?>
	
	<div class="edd_price_options col-lg-6">
		<?php if ( $prices ) :
			echo '<select class="form-control" name="edd_options[price_id][]">';

			foreach ( $prices as $key => $price ) :
				printf(
					'<option for="%3$s" name="edd_options[price_id][]" id="%3$s" class="%4$s" value="%5$s" %7$s> %6$s</option>',
					checked( 0, $key, false ),
					$type,
					esc_attr( 'edd_price_option_' . $download_id . '_' . $key ),
					esc_attr( 'edd_price_option_' . $download_id ),
					esc_attr( $key ),
					esc_html( $price['name'] . ' - ' . edd_currency_filter( edd_format_amount( $price[ 'amount' ] ) ) ),
					selected( isset( $_GET['price_option'] ), $key, false )
				);
			do_action( 'edd_after_price_option', $key, $price, $download_id );
			endforeach;
			echo '</select>';
		endif;

		do_action( 'edd_after_price_options_list', $download_id, $prices, $type );
		?>
	</div>
	<?php do_action( 'edd_after_price_options', $download_id );
}








/**
BELOW THIS POINT, EVERYTHING IS A MESS
THESE MUST BE SORTED OUT, MOVED TO THEIR OWN TEMPLATE FILES ETC.
*/









/**
 * Cart Widget
 *
 * Downloads cart widget class.
 *
 * @since 1.0
 * @return void
*/
class shoestrap_edd_cart_widget extends WP_Widget {
	/** Constructor */
	function shoestrap_edd_cart_widget() {
		parent::WP_Widget( false, __( 'Downloads Cart', 'edd' ), array( 'description' => __( 'Display the downloads shopping cart', 'edd' ) ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );

		global $post, $edd_options;

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		do_action( 'edd_before_cart_widget' );
		shoestrap_edd_shopping_cart( true );
		do_action( 'edd_after_cart_widget' );
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quantity'] = isset( $new_instance['quantity'] ) ? strip_tags( $new_instance['quantity'] ) : '';
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : '';
		?>
		<p>
       		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd' ); ?></label>
     		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
          	 name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
    		</p>
    
   		 <?php
	}
}

/**
 * Purchase History Widget
 *
 * Displays a user's purchase history.
 *
 * @since 1.2
 * @return void
 */
class shoestrap_edd_purchase_history_widget extends WP_Widget {
	/** Constructor */
	function shoestrap_edd_purchase_history_widget() {
		parent::WP_Widget( false, __( 'Purchase History', 'edd' ), array( 'description' => __( 'Display a user\'s purchase history', 'edd' ) ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		global $user_ID, $edd_options;

		if ( is_user_logged_in() ) {
			$purchases = edd_get_users_purchases( $user_ID );

			if ( $purchases ) {
				echo $before_widget;
				if ( $title ) {
					echo $before_title . $title . $after_title;
				}

				foreach ( $purchases as $purchase ) {
					$purchase_data = edd_get_payment_meta( $purchase->ID );
					$downloads = edd_get_payment_meta_downloads( $purchase->ID );

					if ( $downloads ) {
						echo '<ul class="list-group">';
						foreach ( $downloads as $download ) {
							$id = isset( $purchase_data['cart_details'] ) ? $download['id'] : $download;
							$price_id = isset( $download['options']['price_id'] ) ? $download['options']['price_id'] : null;
							$download_files = edd_get_download_files( $id, $price_id );
							echo '<li class="list-group-item edd-purchased-widget-purchase edd-purchased-widget-purchase-' . $purchase->ID . '" id="edd-purchased-widget-purchase-' . $id . '">';
								echo '<div class="edd-purchased-widget-purchase-name">' . get_the_title( $id ) . '</div>';
								echo '<ul class="edd-purchased-widget-file-list">';

								if ( ! edd_no_redownload() ) {
									if ( $download_files ) {
										foreach ( $download_files as $filekey => $file ) {
											$download_url = edd_get_download_file_url( $purchase_data['key'], $purchase_data['email'], $filekey, $id, $price_id );
											echo '<li class="edd-purchased-widget-file"><a href="' . $download_url . '" class="edd-purchased-widget-file-link">' .  $file['name'] . '</a></li>';
										}
									} else {
										echo '<li class="edd-purchased-widget-no-file">' . __( 'No downloadable files found.', 'edd' );
									}
								}

								echo '</ul>';
							echo '</li>';
						}
						echo '</ul>';
					}

				}
				echo $after_widget;
			}
		}
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'edd' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
	<?php
	}
}

/**
 * Mini Cart Widget
 *
 * Downloads cart widget class.
 *
 * @since 1.0
 * @return void
*/
class shoestrap_edd_mini_cart_widget extends WP_Widget {
	/** Constructor */
	function shoestrap_edd_mini_cart_widget() {
		parent::WP_Widget( false, __( 'Mini Downloads Cart', 'edd' ), array( 'description' => __( 'Display the downloads shopping cart in a minimal format', 'edd' ) ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );

		global $post, $edd_options;

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		shoestrap_edd_mini_shopping_cart( true );
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['quantity'] = isset( $new_instance['quantity'] ) ? strip_tags( $new_instance['quantity'] ) : '';
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : '';
		?>
		<p>
       		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd' ); ?></label>
     		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
          	 name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
    		</p>
    
   		 <?php
	}
}

function sshoestrap_edd_widgets_init() {
	unregister_widget( 'edd_cart_widget' );
	register_widget( 'shoestrap_edd_cart_widget' );
	register_widget( 'shoestrap_edd_mini_cart_widget' );
	unregister_widget( 'edd_purchase_history_widget' );
	register_widget( 'shoestrap_edd_purchase_history_widget' );
}
add_action('widgets_init', 'sshoestrap_edd_widgets_init');

/*
 * Display Products on the Homepage.
 * This will simply alter the query so that EDD Downloads are shown
 * on the Frontpage instead of the list of posts.
 */
function shoestrap_edd_downloads_on_homepage( $query ) {
	$option = get_option( 'shoestrap' );
    if ( $option['shoestrap_edd_frontpage'] == 1 && $query->is_home() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'download' ) );
        add_action( 'shoestrap_page_header_override', function(){} );
		return $query;
    }
}
add_action( 'pre_get_posts', 'shoestrap_edd_downloads_on_homepage' );



/*
 * Calculate the classes of the downloads in archives
 * based on the settings in the admin panel
 * and the content width.
 *
 * This function also calculates some additional classes
 * that must be added so that the grid works properly
 * using some clear-left declarations.
 */
function shoestrap_edd_get_download_class( $download_size = 'normal' ) {
	$option = get_option( 'shoestrap' );
	$content_width 	= shoestrap_content_width_px();
	$breakpoint 	= $option['screen_tablet'];

	$class = 'col-sm-6 col-md-4 mix';

	if ( $content_width < $breakpoint ) :
		if ( $download_size != 'wide' ) :
		else :
			$class = 'col-sm-12 col-md-6';
		endif;
	else :
		if ( $download_size == 'narrow' ) :
			$class = 'col-sm-6 col-md-3';
		elseif ( $download_size == 'wide' ) :
			$class = 'col-sm-6';
		endif;
	endif;

	return $class;
}

/*
 * Custom function to get minimum price as plain number
 */
function shoestrap_min_price_plain( $download_id, $echo = true ) {
	if ( edd_has_variable_prices( $download_id ) ) {
		$prices = edd_get_variable_prices( $download_id );
		// Return the lowest price
		$price_float = 0;
      foreach ($prices as $key => $value)
        if ( ( ( (float)$prices[ $key ]['amount'] ) < $price_float ) or ( $price_float == 0 ) ) 
          $price_float = (float)$prices[ $key ]['amount'];
          $price = edd_sanitize_amount( $price_float );
	} else {
		$price = edd_get_download_price( $download_id );
	}
	if ( $echo == false ) :
		return $price;
	else :
		echo $price;
	endif;
}


/*
 * The sub-template for the grid.
 * This depends on the mode that the user has selected in the admin panel.
 */

function shoestrap_edd_subtemplate( $thumbnails = true, $excerpt = true, $full_content = false, $price = true, $buy_button = true ) {
	global $post;
	$option = get_option( 'shoestrap' );

	$button_args = array(
		'download_id' => $post->ID,
		'price'       => (bool) true,
		'direct'      => edd_get_download_button_behavior( $post->ID ) == 'direct' ? true : false,
		'text'        => ! empty( $edd_options[ 'add_to_cart_text' ] ) ? $edd_options[ 'add_to_cart_text' ] : __( 'Purchase', 'edd' ),
		'style'       => isset( $edd_options[ 'button_style' ] ) 	   ? $edd_options[ 'button_style' ]     : 'btn',
		'color'       => isset( $edd_options[ 'checkout_color' ] ) 	   ? $edd_options[ 'checkout_color' ] 	: 'blue',
		'class'       => 'btn btn-primary edd-submit'
	);

	$style = $option['shoestrap_edd_box_style'];

	echo '<div class="equal"><div class="' . shoestrap_edd_element_class() . '">';

	if ( $style != 'panel' ) :

		if ( $price == false ) :
			$button_args['price'] = false;
		endif;

		if ( $thumbnails != false ) :
			$thumb_url = wp_get_attachment_url( get_post_thumbnail_id() );
			if ( $thumb_url == '' ) :
				$thumb_url = plugins_url( '../assets/img/empty.png', __FILE__ );
			endif;
			$args = array(
				"url"       => $thumb_url,
				"width"     => 691,
				"height"    => 424,
				"crop"      => true,
				"retina"    => "",
				"resize"    => true,
			);
			$image = shoestrap_image_resize( $args );
			echo '<div class="download-image">';
			echo '<a href="' . get_permalink() . '"><img src="' . $image['url'] . '" /></a>';

			if ( $buy_button != false ) :
				echo '<div class="overlay">';

				if ( edd_has_variable_prices( $post->ID ) ) :
					echo '<a href="' . get_permalink() . '" class="btn btn-primary">' . __( 'Choose Option', 'shoestrap_edd' ) . '</a>';
				else :
					echo edd_get_purchase_link( $button_args );
				endif;

				echo '</div>';
			endif;

			echo '</div>';
		endif;
		echo '<div class="caption">
				<a itemprop="url" href="' . get_permalink() . '">
					<h3 itemprop="name">' . get_the_title() . '</h3>
				</a>';
		if ( $price != false ) :
			shoestrap_edd_price( 'h4' );
		endif;

		if ( $full_content == true ) :
			the_content();
		elseif ( $excerpt != false ) :
			the_excerpt();
		endif;

		echo '</div>';

	else :
		echo '<div class="panel-heading">
				<a itemprop="url" href="' . get_permalink() . '">
					<h4 itemprop="name">' . get_the_title() . '</h4>
				</a>
			</div>';

		if ( $price == false ) :
			$button_args['price'] = false;
		endif;

		if ( $thumbnails != false ) :
			$thumb_url = wp_get_attachment_url( get_post_thumbnail_id() );
			if ( $thumb_url == '' ) :
				$thumb_url = plugins_url( '../assets/img/empty.png', __FILE__ );
			endif;
			$args = array(
				"url"       => $thumb_url,
				"width"     => 691,
				"height"    => 424,
				"crop"      => true,
				"retina"    => "",
				"resize"    => true,
			);
			$image = shoestrap_image_resize( $args );
			echo '<div class="download-image">';
			echo '<a href="' . get_permalink() . '"><img src="' . $image['url'] . '" /></a>';

			if ( $buy_button != false ) :
				echo '<div class="overlay">';

				if ( edd_has_variable_prices( $post->ID ) ) :
					echo '<a href="' . get_permalink() . '" class="btn btn-primary">' . __( 'Choose Option', 'shoestrap_edd' ) . '</a>';
				else :
					echo edd_get_purchase_link( $button_args );
				endif;

				echo '</div>';
			endif;

			echo '</div>';
		endif;

		echo '<div class="panel-body">';

		if ( $price != false ) :
			shoestrap_edd_price( 'h4' );
		endif;

		if ( $full_content == true ) :
			the_content();
		elseif ( $excerpt != false ) :
			the_excerpt();
		endif;

		echo '</div>';
	endif;
	echo '</div></div>';
}

/*
 * Remove EDD Specs for the bottom of the content.
 * This only applied when the "EDD Software Specs" is installed.
 * We are removing the default version so that we may add our custom version later on.
 */
if ( class_exists( 'EDD_Software_Specs' ) ) :
function remove_edd_software_specs_from_content() {
	global $EDD_Software_Specs;
	remove_action( 'edd_after_download_content', array( $EDD_Software_Specs, 'specs' ), 30 );
}
add_action( 'edd_after_download_content', 'remove_edd_software_specs_from_content', 10 );
endif;


/*
 * Custom function to display prices
 */
function shoestrap_edd_price( $el = 'h2' ) {
	// find if there's a ZERO price in variable pricing
	$zero_price = 0;
	if ( edd_has_variable_prices( get_the_ID() ) == 1 ) {
		foreach ( edd_get_variable_prices( get_the_ID() ) as $key => $price ) {
			if ( esc_html( $price[ 'amount' ] ) == '0' ) {
				$zero_price = 1;
			}
		}
	}
	
	$coming_soon = isset( $post->ID ) ? get_post_meta( $post->ID, 'edd_coming_soon', true ) : '';
	$coming_soon_text = isset( $post->ID ) ? get_post_meta( $post->ID, 'edd_coming_soon_text', true ) : '';
	$element = '<' . $el . ' itemprop="price" class="edd_price">';

	echo $element;

	if ( $coming_soon ) :
		if ( $coming_soon_text )
			echo $coming_soon_text;
		else
			echo __( 'Coming soon', 'shoestrap-edd' );

	elseif ( '0' == edd_get_download_price( get_the_ID() ) && !edd_has_variable_prices( get_the_ID() ) ) :
		echo __( 'Free', 'shoestrap-edd' );

	elseif ( edd_has_variable_prices( get_the_ID() ) && $zero_price == 1 ) :
		_e( 'From Free', 'shoestrap_edd' );

	elseif ( edd_has_variable_prices( get_the_ID() ) ) :
		_e( 'From ', 'shoestrap_edd' );
		edd_price( get_the_ID() );

	else :
		edd_price( get_the_ID() );

	endif;

	echo '</' . $el . '>';
}


function custom_excerpt_length( $length ) {
	return 20;
}

/*
 * Some additional CSS rules that must be added for this plugin
 */
function shoestrap_edd_header_css() {
	$option = get_option( 'shoestrap' );
	$screen_tablet         = filter_var( $option['screen_tablet'], FILTER_SANITIZE_NUMBER_INT );
	$screen_desktop        = filter_var( $option['screen_desktop'], FILTER_SANITIZE_NUMBER_INT );
	$screen_large_desktop  = filter_var( $option['screen_large_desktop'], FILTER_SANITIZE_NUMBER_INT );
	?>
	<style>
	.row.product-list .download { margin-bottom: 2em; }
	<?php if ( !is_singular( 'download' ) ) : ?>
		.widget_shoestrap_edd { display: none; }
	<?php endif; ?>
	.download-image {
		position: relative;
	}
	.download-image:hover .overlay {
		bottom: 0;
		visibility: visible;
	}
	.download-image .overlay {
		display: block;
		position: absolute;
		right: 0;
		visibility: hidden;
		background: rgba(0,0,0,0.6);
		width: 100%;
		padding: 15px;
	}
	</style>
	<?php
}


/**
 * Get Checkout Form
 *
 * @since 1.0
 * @global $edd_options Array of all the EDD options
 * @global $user_ID ID of current logged in user
 * @global $post Current Post Object
 * @return string
 */
function shoestrap_edd_checkout_form() {
	global $edd_options, $user_ID, $post;

	$payment_mode = edd_get_chosen_gateway();
	$form_action  = esc_url( edd_get_checkout_uri( 'payment-mode=' . $payment_mode ) );

	ob_start();

	echo '<div id="edd_checkout_wrap">';

	if ( edd_get_cart_contents() ) :
		shoestrap_edd_checkout_cart(); ?>

		<div id="edd_checkout_form_wrap" class="edd_clearfix">
			<?php do_action( 'edd_before_purchase_form' ); ?>
			<form id="edd_purchase_form" class="form-horizontal" action="<?php echo $form_action; ?>" method="POST">
				<?php
				do_action( 'edd_checkout_form_top' );

				if ( edd_show_gateways() )
					do_action( 'edd_payment_mode_select'  );
				else
					do_action( 'edd_purchase_form' );

				do_action( 'edd_checkout_form_bottom' ); ?>
			</form>
			<?php do_action( 'edd_after_purchase_form' ); ?>
		</div><!--end #edd_checkout_form_wrap-->
	<?php else:
		do_action( 'edd_cart_empty' );
	endif;

	echo '</div><!--end #edd_checkout_wrap-->';
	return ob_get_clean();
}

/**
 * Renders the Purchase Form, hooks are provided to add to the purchase form.
 * The default Purchase Form rendered deisplays a list of the enabled payment
 * gateways, a user registration form (if enable) and a credit card info form
 * if credit cards are enabled
 *
 * @since 1.4
 * @global $edd_options Array of all the EDD options
 * @return string
 */
function shoestrap_edd_show_purchase_form() {
	global $edd_options;

	$payment_mode = edd_get_chosen_gateway();
	do_action( 'edd_purchase_form_top' );

	if ( edd_can_checkout() ) :
		do_action( 'edd_purchase_form_before_register_login' );

		if( isset( $edd_options['show_register_form'] ) && ! is_user_logged_in() && ! isset( $_GET['login'] ) ) : ?>
			<div id="edd_checkout_login_register">
				<?php do_action( 'edd_purchase_form_register_fields' ); ?>
			</div>
		<?php elseif ( isset( $edd_options['show_register_form'] ) && ! is_user_logged_in() && isset( $_GET['login'] ) ) : ?>
			<div id="edd_checkout_login_register">
				<?php do_action( 'edd_purchase_form_login_fields' ); ?>
			</div>
		<?php endif; ?>

		<?php if( ( !isset( $_GET['login'] ) && is_user_logged_in() ) || !isset( $edd_options['show_register_form'] ) ) :
			do_action( 'edd_purchase_form_after_user_info' );
		endif;

		do_action( 'edd_purchase_form_before_cc_form' );

		// Load the credit card form and allow gateways to load their own if they wish
		if ( has_action( 'edd_' . $payment_mode . '_cc_form' ) )
			do_action( 'edd_' . $payment_mode . '_cc_form' );
		else
			do_action( 'edd_cc_form' );

		do_action( 'edd_purchase_form_after_cc_form' );
	else :
		// Can't checkout
		do_action( 'edd_purchase_form_no_access' );
	endif;

	do_action( 'edd_purchase_form_bottom' );
}
remove_action( 'edd_purchase_form', 'edd_show_purchase_form' );
add_action( 'edd_purchase_form', 'shoestrap_edd_show_purchase_form' );

/**
 * Shows the User Info fields in the Personal Info box, more fields can be added
 * via the hooks provided.
 *
 * @since 1.3.3
 * @return void
 */
function shoestrap_edd_user_info_fields() {
	if ( is_user_logged_in() )
		$user_data = get_userdata( get_current_user_id() );
	?>
	<fieldset id="edd_checkout_user_info">
		<span><legend><?php echo apply_filters( 'edd_checkout_personal_info_text', __( 'Personal Info', 'edd' ) ); ?></legend></span>
		<?php do_action( 'edd_purchase_form_before_email' ); ?>

		<div class="form-group" id="edd-email-wrap">
			<label class="edd-label col-md-3 control-label" for="edd-email">
				<?php _e( 'Email Address', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_email' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'We will send the purchase receipt to this address.', 'edd' ); ?></span>
				<input class="form-control edd-input required" type="email" name="edd_email" placeholder="<?php _e( 'Email address', 'edd' ); ?>" id="edd-email" value="<?php echo is_user_logged_in() ? $user_data->user_email : ''; ?>"/>
			</div>
		</div>

		<?php do_action( 'edd_purchase_form_after_email' ); ?>

		<div class="form-group" id="edd-first-name-wrap">
			<label class="col-md-3 control-label edd-label" for="edd-first">
				<?php _e( 'First Name', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_first' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'We will use this to personalize your account experience.', 'edd' ); ?></span>
				<input class="form-control edd-input required" type="text" name="edd_first" placeholder="<?php _e( 'First Name', 'edd' ); ?>" id="edd-first" value="<?php echo is_user_logged_in() ? $user_data->first_name : ''; ?>"/>
			</div>
		</div>
		<div class="form-group" id="edd-last-name-wrap">
			<label class="col-md-3 control-label edd-label" for="edd-last">
				<?php _e( 'Last Name', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_last' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'We will use this as well to personalize your account experience.', 'edd' ); ?></span>
				<input class="form-control edd-input" type="text" name="edd_last" id="edd-last" placeholder="<?php _e( 'Last name', 'edd' ); ?>" value="<?php echo is_user_logged_in() ? $user_data->last_name : ''; ?>"/>
			</div>
		</div>

		<?php do_action( 'edd_purchase_form_user_info' ); ?>
	</fieldset>
	<?php
}
remove_action( 'edd_purchase_form_after_user_info', 'edd_user_info_fields' );
add_action( 'edd_purchase_form_after_user_info', 'shoestrap_edd_user_info_fields' );

/**
 * Renders the credit card info form.
 *
 * @since 1.0
 * @return void
 */
function shoestrap_edd_get_cc_form() {
	ob_start(); ?>

	<?php do_action( 'edd_before_cc_fields' ); ?>

	<fieldset id="edd_cc_fields" class="edd-do-validate">
		<span><legend><?php _e( 'Credit Card Info', 'edd' ); ?></legend></span>
		<?php if( is_ssl() ) : ?>
			<div class="alert alert-success" id="edd_secure_site_wrapper">
				<h3><i class="elusive icon-lock"></i><?php _e( 'This is a secure SSL encrypted payment.', 'edd' ); ?></h3>>
			</div>
		<?php endif; ?>
		<div class="form-group" id="edd-card-number-wrap">
			<label class="col-md-3 control-label edd-label">
				<?php _e( 'Card Number', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
				<span class="card-type"></span>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'edd' ); ?></span>
				<input type="text" autocomplete="off" name="card_number" class="form-control card-number edd-input required" placeholder="<?php _e( 'Card number', 'edd' ); ?>" />
			</div>
		</div>

		<div class="form-group" id="edd-card-cvc-wrap">
			<label class="edd-label col-md-3 control-label">
				<?php _e( 'CVC', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'edd' ); ?></span>
				<input type="text" size="4" autocomplete="off" name="card_cvc" class="form-control card-cvc edd-input required" placeholder="<?php _e( 'Security code', 'edd' ); ?>" />
			</div>
		</div>

		<div class="form-group" id="edd-card-name-wrap">
			<label class="edd-label col-md-3 control-label">
				<?php _e( 'Name on the Card', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The name printed on the front of your credit card.', 'edd' ); ?></span>
				<input type="text" autocomplete="off" name="card_name" class="form-control card-name edd-input required" placeholder="<?php _e( 'Card name', 'edd' ); ?>" />
			</div>
		</div>

		<?php do_action( 'edd_before_cc_expiration' ); ?>

		<div class="form-group card-expiration">
			<label class="col-md-3 control-label edd-label">
				<?php _e( 'Expiration (MM/YY)', 'edd' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The date your credit card expires, typically on the front of the card.', 'edd' ); ?></span>
				<select name="card_exp_month" class="form-control card-expiry-month edd-select edd-select-small required">
					<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
				</select>
				<span class="exp-divider"> / </span>
				<select name="card_exp_year" class="card-expiry-year edd-select edd-select-small required">
					<?php for( $i = date('Y'); $i <= date('Y') + 10; $i++ ) { echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>'; } ?>
				</select>
			</div>
		</div>
		<?php do_action( 'edd_after_cc_expiration' ); ?>
	</fieldset>
	<?php
	do_action( 'edd_after_cc_fields' );

	echo ob_get_clean();
}
remove_action( 'edd_cc_form', 'edd_get_cc_form' );
add_action( 'edd_cc_form', 'shoestrap_edd_get_cc_form' );

/**
 * Outputs the default credit card address fields
 *
 * @since 1.0
 * @return void
 */
function shoestrap_edd_default_cc_address_fields() {
	$logged_in = is_user_logged_in();

	if( $logged_in )
		$user_address = get_user_meta( get_current_user_id(), '_edd_user_address', true );

	$line1 = $logged_in && ! empty( $user_address['line1'] ) ? $user_address['line1'] : '';
	$line2 = $logged_in && ! empty( $user_address['line2'] ) ? $user_address['line2'] : '';
	$city  = $logged_in && ! empty( $user_address['city']  ) ? $user_address['city']  : '';
	$zip   = $logged_in && ! empty( $user_address['zip']   ) ? $user_address['zip']   : '';
	ob_start(); ?>

	<fieldset id="edd_cc_address" class="cc-address">
		<span><legend><?php _e( 'Billing Details', 'edd' ); ?></legend></span>
		<?php do_action( 'edd_cc_billing_top' ); ?>
		<div class="form-group" id="edd-card-address-wrap">
			<label class="edd-label col-md-3 control-label"><?php _e( 'Billing Address', 'edd' ); ?></label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The primary billing address for your credit card.', 'edd' ); ?></span>
				<input type="text" name="card_address" class="form-control card-address edd-input required" placeholder="<?php _e( 'Address line 1', 'edd' ); ?>" value="<?php echo $line1; ?>"/>
			</div>
		</div>

		<div class="form-group" id="edd-card-address-2-wrap">
			<label class="col-md-3 control-label edd-label"><?php _e( 'Billing Address Line 2 (optional)', 'edd' ); ?></label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The suite, apt no, PO box, etc, associated with your billing address.', 'edd' ); ?></span>
				<input type="text" name="card_address_2" class="form-control card-address-2 edd-input" placeholder="<?php _e( 'Address line 2', 'edd' ); ?>" value="<?php echo $line2; ?>"/>
			</div>
		</div>

		<div class="form-group" id="edd-card-city-wrap">
			<label class="edd-label col-md-3 control-label"><?php _e( 'Billing City', 'edd' ); ?></label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The city for your billing address.', 'edd' ); ?></span>
				<input type="text" name="card_city" class="form-control card-city edd-input required" placeholder="<?php _e( 'City', 'edd' ); ?>" value="<?php echo $city; ?>"/>
			</div>
		</div>

		<div class="form-group" id="edd-card-zip-wrap">
			<label class="col-md-3 control-label edd-label"><?php _e( 'Billing Zip / Postal Code', 'edd' ); ?></label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'edd' ); ?></span>
				<input type="text" size="4" name="card_zip" class="form-control card-zip edd-input required" placeholder="<?php _e( 'Zip / Postal code', 'edd' ); ?>"/>
			</div>
		</div>

		<div class="form-group" id="edd-card-country-wrap">
			<label class="col-md-3 control-label edd-label"><?php _e( 'Billing Country', 'edd' ); ?></label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The country for your billing address.', 'edd' ); ?></span>
				<select name="billing_country" id="billing_country" class="form-control billing_country edd-select required">
					<?php

					$selected_country = edd_get_shop_country();

					if( $logged_in && ! empty( $user_address['country'] ) )
						$selected_country = $user_address['country'];

					$countries = edd_get_country_list();
					foreach( $countries as $country_code => $country ) {
					  echo '<option value="' . $country_code . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
					}
					?>
				</select>
			</div>
		</div>

		<div class="form-group" id="edd-card-state-wrap">
			<label class="edd-label col-md-3 control-label"><?php _e( 'Billing State / Province', 'edd' ); ?></label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'The state or province for your billing address.', 'edd' ); ?></span>
				<?php
				$selected_state = edd_get_shop_state();
				$states         = edd_get_shop_states();

				if( $logged_in && ! empty( $user_address['state'] ) )
					$selected_state = $user_address['state'];

				if( ! empty( $states ) ) : ?>
					<select name="card_state" id="card_state" class="card_state edd-select required">
						<?php foreach( $states as $state_code => $state ) {
							echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
						} ?>
					</select>
				<?php else : ?>
					<input type="text" size="6" name="card_state" id="card_state" class="card_state edd-input" placeholder="<?php _e( 'State / Province', 'edd' ); ?>"/>
				<?php endif; ?>
			</div>
		</div>

		<?php do_action( 'edd_cc_billing_bottom' ); ?>

	</fieldset>
	<?php echo ob_get_clean();
}
remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );
add_action( 'edd_after_cc_fields', 'shoestrap_edd_default_cc_address_fields' );


/**
 * Renders the billing address fields for cart taxation
 *
 * @since 1.6
 * @return void
 */
function shoestrap_edd_checkout_tax_fields() {
	if( edd_cart_needs_tax_address_fields() && edd_get_cart_total() )
		shoestrap_edd_default_cc_address_fields();
}
remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields', 999 );
add_action( 'edd_purchase_form_after_cc_form', 'shoestrap_edd_checkout_tax_fields', 999 );


/**
 * Renders the user registration fields. If the user is logged in, a login
 * form is displayed other a registration form is provided for the user to
 * create an account.
 *
 * @since 1.0
 * @return string
 */
function shoestrap_edd_get_register_fields() {
	global $edd_options;
	global $user_ID;

	if ( is_user_logged_in() )
	$user_data = get_userdata( $user_ID );

	ob_start(); ?>
	<fieldset id="edd_register_fields">
		<div class="form-group" id="edd-login-account-wrap">
			<a href="<?php echo add_query_arg('login', 1); ?>" class="edd_checkout_register_login btn btn-success btn-lg btn-block" data-action="checkout_login">
				<?php _e( 'Already have an account?', 'edd' ); ?><?php _e( 'Login', 'edd' ); ?>
			</a>
		</div>
		<?php do_action('edd_register_fields_before'); ?>

		<div class="form-group" id="edd-user-email-wrap">
			<label for="edd-email" class="col-md-3 control-label">
				<?php _e( 'Email', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_email' ) ) : ?>
					<span class="edd-required-indicator">*</span>
				<?php endif; ?>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'We will send the purchase receipt to this address.', 'edd' ); ?></span>
				<input name="edd_email" id="edd-email" class="required edd-input form-control" type="email" placeholder="<?php _e( 'Email', 'edd' ); ?>" title="<?php _e( 'Email', 'edd' ); ?>"/>
			</div>
		</div>

		<div class="form-group" id="edd-user-first-name-wrap">
			<label class="edd-label col-md-3 control-label" for="edd-first">
				<?php _e( 'First Name', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_first' ) ) : ?>
					<span class="edd-required-indicator">*</span>
				<?php endif; ?>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'We will use this to personalize your account experience.', 'edd' ); ?></span>
				<input class="edd-input required form-control" type="text" name="edd_first" placeholder="<?php _e( 'First Name', 'edd' ); ?>" id="edd-first" value="<?php echo is_user_logged_in() ? $user_data->user_firstname : ''; ?>"/>
			</div>
		</div>

		<div class="form-group" id="edd-user-last-name-wrap">
			<label class="edd-label col-md-3 control-label" for="edd-last">
				<?php _e( 'Last Name', 'edd' ); ?>
				<?php if( edd_field_is_required( 'edd_last' ) ) : ?>
					<span class="edd-required-indicator">*</span>
				<?php endif; ?>
			</label>
			<div class="col-md-9">
				<span class="edd-description"><?php _e( 'We will use this as well to personalize your account experience.', 'edd' ); ?></span>
				<input class="edd-input form-control" type="text" name="edd_last" id="edd-last" placeholder="<?php _e( 'Last name', 'edd' ); ?>" value="<?php echo is_user_logged_in() ? $user_data->user_lastname : ''; ?>"/>
			</div>
		</div>

		<?php do_action('edd_register_fields_after'); ?>

		<fieldset id="edd_register_account_fields">
			<span><legend><?php _e( 'Create an account', 'edd' ); if( !edd_no_guest_checkout() ) { echo ' ' . __( '(optional)', 'edd' ); } ?></legend></span>
			<?php do_action('edd_register_account_fields_before'); ?>

			<div class="form-group" id="edd-user-login-wrap">
				<label class="col-md-3 control-label" for="edd_user_login">
					<?php _e( 'Username', 'edd' ); ?>
					<?php if( edd_no_guest_checkout() ) : ?>
						<span class="edd-required-indicator">*</span>
					<?php endif; ?>
				</label>

				<div class="col-md-9">
					<span class="edd-description"><?php _e( 'The username you will use to log into your account.', 'edd' ); ?></span>
					<input name="edd_user_login" id="edd_user_login" class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" type="text" placeholder="<?php _e( 'Username', 'edd' ); ?>" title="<?php _e( 'Username', 'edd' ); ?>"/>
				</div>
			</div>

			<div class="form-group" id="edd-user-pass-wrap">
				<label class="col-md-3 control-label" for="password">
					<?php _e( 'Password', 'edd' ); ?>
					<?php if( edd_no_guest_checkout() ) : ?>
						<span class="edd-required-indicator">*</span>
					<?php endif; ?>
				</label>
				<div class="col-md-9">
					<span class="edd-description"><?php _e( 'The password used to access your account.', 'edd' ); ?></span>
					<input name="edd_user_pass" id="edd_user_pass" class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" placeholder="<?php _e( 'Password', 'edd' ); ?>" type="password"/>
				</div>
			</div>

			<div id="edd-user-pass-confirm-wrap" class="edd_register_password form-group">
				<label class="col-md-3 control-label" for="password_again">
					<?php _e( 'Password Again', 'edd' ); ?>
					<?php if( edd_no_guest_checkout() ) : ?>
						<span class="edd-required-indicator">*</span>
					<?php endif; ?>
				</label>
				<div class="col-md-9">
					<span class="edd-description"><?php _e( 'Confirm your password.', 'edd' ); ?></span>
					<input name="edd_user_pass_confirm" id="edd_user_pass_confirm" class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" placeholder="<?php _e( 'Confirm password', 'edd' ); ?>" type="password"/>
				</div>
			</div>

			<?php do_action( 'edd_register_account_fields_after' ); ?>

		</fieldset>
		<input type="hidden" name="edd-purchase-var" value="needs-to-register"/>

		<?php do_action( 'edd_purchase_form_user_info' ); ?>
	</fieldset>
	<?php
	echo ob_get_clean();
}
remove_action( 'edd_purchase_form_register_fields', 'edd_get_register_fields' );
add_action( 'edd_purchase_form_register_fields', 'shoestrap_edd_get_register_fields' );

/**
 * Gets the login fields for the login form on the checkout. This function hooks
 * on the edd_purchase_form_login_fields to display the login form if a user already
 * had an account.
 *
 * @since 1.0
 * @return string
 */
function shoestrap_edd_get_login_fields() {
	ob_start(); ?>

	<fieldset id="edd_login_fields">
		<div class="form-group" id="edd-new-account-wrap">
			<a href="<?php echo remove_query_arg('login'); ?>" class="edd_checkout_register_login btn btn-success btn-lg btn-block" data-action="checkout_register">
				<?php _e( 'Need to create an account?', 'edd' ); ?>
				<?php _e( 'Register', 'edd' ); if(!edd_no_guest_checkout()) { echo ' ' . __( 'or checkout as a guest.', 'edd' ); } ?>
			</a>
		</div>
		<?php do_action('edd_checkout_login_fields_before'); ?>

		<div class="form-group" id="edd-user-login-wrap">
			<label class="edd-label control-label col-md-3" for="edd-username"><?php _e( 'Username', 'edd' ); ?></label>
			<div class="col-md-9">
				<input class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" type="text" name="edd_user_login" id="edd_user_login" value="" placeholder="<?php _e( 'Your username', 'edd' ); ?>"/>
			</div>
		</div>

		<div id="edd-user-pass-wrap" class="edd_login_password form-group">
			<label class="edd-label control-label col-md-3" for="edd-password"><?php _e( 'Password', 'edd' ); ?></label>
			<div class="col-md-9">
				<input class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" type="password" name="edd_user_pass" id="edd_user_pass" placeholder="<?php _e( 'Your password', 'edd' ); ?>"/>
				<input type="hidden" name="edd-purchase-var" value="needs-to-login"/>
			</div>
		</div>

		<?php do_action('edd_checkout_login_fields_after'); ?>
	</fieldset><!--end #edd_login_fields-->
	<?php echo ob_get_clean();
}
remove_action( 'edd_purchase_form_login_fields', 'edd_get_login_fields' );
add_action( 'edd_purchase_form_login_fields', 'shoestrap_edd_get_login_fields' );

/**
 * Renders the payment mode form by getting all the enabled payment gateways and
 * outputting them as radio buttons for the user to choose the payment gateway. If
 * a default payment gateway has been chosen from the EDD Settings, it will be
 * automatically selected.
 *
 * @since 1.2.2
 * @return void
 */
function shoestrap_edd_payment_mode_select() {
	$gateways = edd_get_enabled_payment_gateways();
	$page_URL = edd_get_current_page_url();
	do_action('edd_payment_mode_top');

	if( !edd_is_ajax_enabled() ) echo '<form class="form-horizontal" role="form" id="edd_payment_mode" action="' . $page_URL . '" method="GET">'; ?>
		<fieldset id="edd_payment_mode_select">
			<?php do_action( 'edd_payment_mode_before_gateways_wrap' ); ?>
			<div id="edd-payment-mode-wrap">
				<span class="edd-payment-mode-label">
					<?php _e( 'Select Payment Method', 'edd' ); ?>
				</span>
				<br/>
				<?php do_action( 'edd_payment_mode_before_gateways' );

				foreach ( $gateways as $gateway_id => $gateway ) :
					$checked = checked( $gateway_id, edd_get_default_gateway(), false );
					echo '<label for="edd-gateway-' . esc_attr( $gateway_id ) . '" class="edd-gateway-option" id="edd-gateway-option-' . esc_attr( $gateway_id ) . '">';
					echo '<input type="radio" name="payment-mode" class="edd-gateway" id="edd-gateway-' . esc_attr( $gateway_id ) . '" value="' . esc_attr( $gateway_id ) . '"' . $checked . '>' . esc_html( $gateway['checkout_label'] ) . '</option>';
					echo '</label>';
				endforeach;

				do_action( 'edd_payment_mode_after_gateways' ); ?>
			</div>
			<?php do_action( 'edd_payment_mode_after_gateways_wrap' ); ?>
		</fieldset>

		<fieldset id="edd_payment_mode_submit" class="edd-no-js">
			<p id="edd-next-submit-wrap"><?php echo shoestrap_edd_checkout_button_next(); ?></p>
		</fieldset>
	<?php if( !edd_is_ajax_enabled() ) echo '</form>'; ?>

	<div id="edd_purchase_form_wrap"></div><!-- the checkout fields are loaded into this-->
	<?php do_action('edd_payment_mode_bottom');
}
remove_action( 'edd_payment_mode_select', 'edd_payment_mode_select' );
add_action( 'edd_payment_mode_select', 'shoestrap_edd_payment_mode_select' );

/**
 * Renders the Discount Code field which allows users to enter a discount code.
 * This field is only displayed if there are any active discounts on the site else
 * it's not displayed.
 *
 * @since 1.2.2
 * @return void
*/
function shoestrap_edd_discount_field() {
	if( ! isset( $_GET['payment-mode'] ) && count( edd_get_enabled_payment_gateways() ) > 1 && ! edd_is_ajax_enabled() )
		return; // Only show once a payment method has been selected if ajax is disabled

	if ( edd_has_active_discounts() && edd_get_cart_total() ) : ?>
		<fieldset id="edd_discount_code">
			<p id="edd_show_discount" style="display:none;">
				<?php _e( 'Have a discount code?', 'edd' ); ?> <a href="#" class="edd_discount_link"><?php echo _x( 'Click to enter it', 'Entering a discount code', 'edd' ); ?></a>
			</p>
			<p id="edd-discount-code-wrap">
				<label class="edd-label" for="edd-discount">
					<?php _e( 'Discount', 'edd' ); ?>
					<img src="<?php echo EDD_PLUGIN_URL; ?>assets/images/loading.gif" id="edd-discount-loader" style="display:none;"/>
				</label>
				<span class="edd-description"><?php _e( 'Enter a coupon code if you have one.', 'edd' ); ?></span>
				<input class="edd-input form-control" type="text" id="edd-discount" name="edd-discount" placeholder="<?php _e( 'Enter discount', 'edd' ); ?>"/>
			</p>
		</fieldset>
		<?php
	endif;
}
remove_action( 'edd_checkout_form_top', 'edd_discount_field', -1 );
add_action( 'edd_checkout_form_top', 'shoestrap_edd_discount_field', -1 );

/**
 * Shows the final purchase total at the bottom of the checkout page
 *
 * @since 1.5
 * @return void
 */
function shoestrap_edd_checkout_final_total() { ?>
	<h2 class="text-center">
		<?php _e( 'Purchase Total:', 'edd' ); ?>
		<span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_amount( false ); ?>" data-total="<?php echo edd_get_cart_amount( true, true ); ?>">
		<?php edd_cart_total(); ?>
	</h2>
	<?php
}
remove_action( 'edd_purchase_form_before_submit', 'edd_checkout_final_total', 999 );
add_action( 'edd_purchase_form_before_submit', 'shoestrap_edd_checkout_final_total', 999 );


/**
 * Renders the Checkout Submit section
 *
 * @since 1.3.3
 * @return void
 */
function shoestrap_edd_checkout_submit() { ?>
	<fieldset id="edd_purchase_submit">
		<?php do_action( 'edd_purchase_form_before_submit' ); ?>
		<?php edd_checkout_hidden_fields(); ?>
		<?php echo shoestrap_edd_checkout_button_purchase(); ?>
		<?php do_action( 'edd_purchase_form_after_submit' ); ?>
		<?php if ( ! edd_is_ajax_enabled() ) { ?>
			<p class="edd-cancel"><a href="javascript:history.go(-1)"><?php _e( 'Go back', 'edd' ); ?></a></p>
		<?php } ?>
	</fieldset>
<?php
}
remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_submit', 9999 );
add_action( 'edd_purchase_form_after_cc_form', 'shoestrap_edd_checkout_submit', 9999 );

/**
 * Renders the Next button on the Checkout
 *
 * @since 1.2
 * @global $edd_options Array of all the EDD Options
 * @return string
 */
function shoestrap_edd_checkout_button_next() {
	global $edd_options;

	ob_start(); ?>

	<input type="hidden" name="edd_action" value="gateway_select" />
	<input type="hidden" name="page_id" value="<?php echo absint( $edd_options['purchase_page'] ); ?>"/>
	<input type="submit" name="gateway_submit" id="edd_next_button" class="edd-submit btn btn-primary" value="<?php _e( 'Next', 'edd' ); ?>"/>
	<?php return apply_filters( 'edd_checkout_button_next', ob_get_clean() );
}

/**
 * Renders the Purchase button on the Checkout
 *
 * @since 1.2
 * @global $edd_options Array of all the EDD Options
 * @return string
 */
function shoestrap_edd_checkout_button_purchase() {
	global $edd_options;
	$color = isset( $edd_options[ 'checkout_color' ] ) ? $edd_options[ 'checkout_color' ] : 'gray';

	if ( edd_get_cart_total() )
		$complete_purchase = ! empty( $edd_options['checkout_label'] ) ? $edd_options['checkout_label'] : __( 'Purchase', 'edd' );
	else
		$complete_purchase = ! empty( $edd_options['checkout_label'] ) ? $edd_options['checkout_label'] : __( 'Free Download', 'edd' );

	ob_start(); ?>
	<input type="submit" class="edd-submit btn btn-primary btn-block btn-lg" id="edd-purchase-button" name="edd-purchase" value="<?php echo $complete_purchase; ?>"/>
	<?php return apply_filters( 'edd_checkout_button_purchase', ob_get_clean() );
}


/**
 * Login Form
 *
 * @global $edd_options
 * @global $post
 * @param string $redirect Redirect page URL
 * @return string Login form
*/
function shoestrap_edd_login_form( $redirect = '' ) {
	global $edd_options, $post;

	if ( $redirect == '' )
		$redirect = edd_get_current_page_url();

	ob_start();

	if ( ! is_user_logged_in() ) {
		// Show any error messages after form submission
		edd_print_errors(); ?>
		<form id="edd_login_form" class="edd_form form-horizontal" action="" method="post" role="form">
			<fieldset>
				<legend><?php _e( 'Log into Your Account', 'edd' ); ?></legend>
				<?php do_action('edd_checkout_login_fields_before');?>
				<div class="form-group">
					<label class="col-md-3 control-label" for="edd_user_Login"><?php _e( 'Username', 'edd' ); ?></label>
					<div class="col-md-9">
						<input name="edd_user_login" id="edd_user_login" class="form-control required" type="email" title="<?php _e( 'Username', 'edd' ); ?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label" for="edd_user_pass"><?php _e( 'Password', 'edd' ); ?></label>
					<div class="col-md-9">
						<input name="edd_user_pass" id="edd_user_pass" class="form-control password required" type="password"/>
					</div>
				</div>
				<input type="hidden" name="edd_redirect" value="<?php echo $redirect; ?>"/>
				<input type="hidden" name="edd_login_nonce" value="<?php echo wp_create_nonce( 'edd-login-nonce' ); ?>"/>
				<input type="hidden" name="edd_action" value="user_login"/>
				<input id="edd_login_submit" type="submit" class="edd_submit btn btn-primary btn-lg btn-block" value="<?php _e( 'Login', 'edd' ); ?>"/>
				<a class="btn btn-link btn-sm btn-block" href="<?php echo wp_lostpassword_url(); ?>" title="<?php _e( 'Lost Password', 'edd' ); ?>">
					<?php _e( 'Lost Password?', 'edd' ); ?>
				</a>
				<?php do_action( 'edd_checkout_login_fields_after' );?>
			</fieldset>
		</form>
	<?php
	} else {
		echo '<p class="edd-logged-in">' . __( 'You are already logged in', 'edd' ) . '</p>';
	}
	return ob_get_clean();
}


function shoestrap_edd_mini_shopping_cart_echo() {
	echo '<div class="pull-right">';
	shoestrap_edd_mini_shopping_cart( true, 'btn', 'navbar-btn', 'btn-primary', 'btn-default disabled', false );
	echo '</div>';
}
/**
 * Renders the Shopping Cart
 *
 * @return string Fully formatted cart
*/
function shoestrap_edd_mini_shopping_cart( $echo = false, $global_btn_class = 'btn', $size_class = 'btn-sm', $btn_class = 'btn-primary', $price_class = 'btn-danger', $dropdown = true ) {
	global $edd_options;
	ob_start();

	$display = 'style="display:none;"';
	$cart_quantity = edd_get_cart_quantity();
	if ( $cart_quantity > 0 )
		$display = "";
	?>

	<div class="btn-group">
		<?php
		echo '<button id="nav-cart-quantity" type="button" class="' . $global_btn_class . ' ' . $price_class . ' ' . $size_class . '">' . $cart_quantity. '</button>';
		echo '<a class="' . $global_btn_class . ' ' . $btn_class . ' ' . $size_class . '" href="' . edd_get_checkout_uri() . '"><i class="elusive icon-shopping-cart"></i> ' . __( 'Checkout', 'edd' ) . '</a>';
	if ( $echo )
		echo ob_get_clean();
	else
		return ob_get_clean();
}

function shoestrap_edd_get_mini_cart_item_template( $key, $item, $ajax = false ) {
	global $post;

	$id = is_array( $item ) ? $item['id'] : $item;
	$title      = get_the_title( $id );
	$options    = !empty( $item['options'] ) ? $item['options'] : array();
	$price      = edd_get_cart_item_price( $id, $options );

	if ( ! empty( $options ) )
		$title .= ( edd_has_variable_prices( $item['id'] ) ) ? ' <span class="edd-cart-item-separator">-</span> ' . edd_get_price_name( $id, $item['options'] ) : edd_get_price_name( $id, $item['options'] );

	ob_start();
	?>
	<span class="edd-cart-item-title">{item_title}</span>
	<span class="edd-cart-item-separator">-</span><span class="edd-cart-item-price">&nbsp;{item_amount}&nbsp;</span>
	<?php

	$item = ob_get_clean();
	$item = str_replace( '{item_title}', $title, $item );
	$item = str_replace( '{item_amount}', edd_currency_filter( edd_format_amount( $price ) ), $item );
	$item = str_replace( '{cart_item_id}', absint( $key ), $item );
	$item = str_replace( '{item_id}', absint( $id ), $item );
	$subtotal = '';

	if ( $ajax )
		$subtotal = edd_currency_filter( edd_format_amount( edd_get_cart_amount( false ) ) ) ;

	$item = str_replace( '{subtotal}', $subtotal, $item );

	return apply_filters( 'edd_cart_item', $item, $id );
}

// Script to increase the total cart quantity in navbar-cart
function shoestrap_edd_increase_navbar_cart_quantity(){
	echo '<script type="text/javascript">jQuery(document).ready(function(){$(".edd-add-to-cart").click(function(){$("#nav-cart-quantity").html(function(i, val){ return val*1+1 });});});</script>';
}
add_action('wp_head','shoestrap_edd_increase_navbar_cart_quantity');

/**
 * Purchase History Shortcode
 *
 * Displays a user's purchsae history.
 */
function shoestrap_edd_purchase_history() {
	if ( is_user_logged_in() ) {
		ob_start();
		edd_get_template_part( 'history', 'purchases' );
		return ob_get_clean();
	}
}
remove_shortcode( 'purchase_history', 'edd_purchase_history' );
add_shortcode( 'purchase_history', 'shoestrap_edd_purchase_history' );

/**
 * Checkout Form Shortcode
 *
 * Show the checkout form.
 */
function shoestrap_edd_checkout_form_shortcode( $atts, $content = null ) {
	return shoestrap_edd_checkout_form();
}
remove_shortcode( 'download_checkout', 'edd_checkout_form_shortcode' );
add_shortcode( 'download_checkout', 'shoestrap_edd_checkout_form_shortcode' );

/**
 * Download Cart Shortcode
 *
 * Show the shopping cart.
 *
 * @param array $atts Shortcode attributes
 * @param string $content
 * @return string
 */
function shoestrap_edd_cart_shortcode( $atts, $content = null ) {
	return shoestrap_edd_shopping_cart();
}
remove_shortcode( 'download_cart', 'edd_cart_shortcode' );
add_shortcode( 'download_cart', 'shoestrap_edd_cart_shortcode' );

/**
 * Login Shortcode
 *
 * Shows a login form allowing users to users to log in. This function simply
 * calls the edd_login_form function to display the login form.
 *
 * @param array $atts Shortcode attributes
 * @param string $content
 * @uses edd_login_form()
 * @return string
 */
function shoestrap_edd_login_form_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'redirect' => '',
		), $atts, 'edd_login' )
	);
	return shoestrap_edd_login_form( $redirect );
}
remove_shortcode( 'edd_login', 'edd_login_form_shortcode' );
add_shortcode( 'edd_login', 'shoestrap_edd_login_form_shortcode' );

/**
 * Discounts short code
 *
 * Displays a list of all the active discounts. The active discounts can be configured
 * from the Discount Codes admin screen.
 *
 * @param array $atts Shortcode attributes
 * @param string $content
 * @uses edd_get_discounts()
 * @return string $discounts_lists List of all the active discount codes
 */
function shoestrap_edd_discounts_shortcode( $atts, $content = null ) {
	$discounts = edd_get_discounts();

	$discounts_list = '<ul id="edd_discounts_list" class="list-group">';

	if ( ! empty( $discounts ) && edd_has_active_discounts() ) {

		foreach ( $discounts as $discount ) {

			if ( edd_is_discount_active( $discount->ID ) ) {

				$discounts_list .= '<li class="edd_discount list-group-item">';

					$discounts_list .= '<span class="edd_discount_name">' . edd_get_discount_code( $discount->ID ) . '</span>';
					$discounts_list .= '<span class="edd_discount_amount pull-right label label-success">' . edd_format_discount_rate( edd_get_discount_type( $discount->ID ), edd_get_discount_amount( $discount->ID ) ) . '</span>';

				$discounts_list .= '</li>';
			}
		}
	} else {
		$discounts_list .= '<li class="edd_discount list-group-item">' . __( 'No discounts found', 'edd' ) . '</li>';
	}

	$discounts_list .= '</ul>';

	return $discounts_list;
}
remove_shortcode( 'download_discounts', 'edd_discounts_shortcode' );
add_shortcode( 'download_discounts', 'shoestrap_edd_discounts_shortcode' );

/**
 * Downloads Shortcode
 *
 * This shortcodes uses the WordPress Query API to get downloads with the
 * arguments specified when using the shortcode. A list of the arguments
 * can be found from the EDD Dccumentation. The shortcode will take all the
 * parameters and display the downloads queried in a valid HTML <div> tags.
 *
 * @since 1.0.6
 * @internal Incomplete shortcode
 * @param array $atts Shortcode attributes
 * @param string $content
 * @return string $display Output generated from the downloads queried
 */
function shoestrap_edd_downloads_query( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'category'         => '',
			'exclude_category' => '',
			'tags'             => '',
			'exclude_tags'     => '',
			'relation'         => 'AND',
			'number'           => 10,
			'price'            => 'no',
			'excerpt'          => 'yes',
			'full_content'     => 'no',
			'buy_button'       => 'yes',
			'columns'          => 3,
			'thumbnails'       => 'true',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'ids'              => ''
		), $atts, 'downloads' )
	);

	$query = array(
		'post_type'      => 'download',
		'posts_per_page' => absint( $number ),
		'orderby'        => $orderby,
		'order'          => $order
	);

	switch ( $orderby ) {
		case 'price':
			$orderby           = 'meta_value';
			$query['meta_key'] = 'edd_price';
			$query['orderby']  = 'meta_value_num';
		break;

		case 'title':
			$query['orderby'] = 'title';
		break;

		case 'id':
			$query['orderby'] = 'ID';
		break;

		case 'random':
			$query['orderby'] = 'rand';
		break;

		default:
			$query['orderby'] = 'post_date';
		break;
	}

	if ( $tags || $category || $exclude_category || $exclude_tags ) {
		$query['tax_query'] = array(
			'relation'     => $relation
		);

		if ( $tags ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_tag',
				'terms'    => explode( ',', $tags ),
				'field'    => 'slug'
			);
		}

		if ( $category ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_category',
				'terms'    => explode( ',', $category ),
				'field'    => 'slug'
			);
		}

		if ( $exclude_category ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_category',
				'terms'    => explode( ',', $exclude_category ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			);
		}

		if ( $exclude_tags ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_tag',
				'terms'    => explode( ',', $exclude_tags ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			);
		}
	}

	if( ! empty( $ids ) )
		$query['post__in'] = explode( ',', $ids );

	if ( get_query_var( 'paged' ) )
		$query['paged'] = get_query_var('paged');
	else if ( get_query_var( 'page' ) )
		$query['paged'] = get_query_var( 'page' );
	else
		$query['paged'] = 1;

	switch( intval( $columns ) ) :
		case 1:
			$column_width = 'wide'; break;
		case 2:
			$column_width = 'wide'; break;
		case 3:
			$column_width = 'normal'; break;
		case 4:
			$column_width = 'narrow'; break;
		case 5:
			$column_width = 'narrow'; break;
		case 6:
			$column_width = 'narrow'; break;
	endswitch;

	// Allow the query to be manipulated by other plugins
	$query = apply_filters( 'edd_downloads_query', $query, $atts );

	$downloads = new WP_Query( $query );
	if ( $downloads->have_posts() ) :
		$i = 1;
		$wrapper_class = 'edd_download_columns_' . $columns;
		ob_start(); ?>
		<div class="edd_downloads_list row <?php echo apply_filters( 'edd_downloads_list_wrapper_class', $wrapper_class, $atts ); ?>">
			<?php while ( $downloads->have_posts() ) : $downloads->the_post(); ?>
				<div itemscope itemtype="http://schema.org/Product" class="<?php echo shoestrap_edd_get_download_class( $i, $column_width ); ?> <?php echo apply_filters( 'edd_download_class', 'edd_download', get_the_ID(), $atts ); ?>" id="edd_download_<?php echo get_the_ID(); ?>">
					<div class="edd_download_inner">
						<?php

						do_action( 'edd_download_before' );

						if ( 'false' != $thumbnails )
							$thumbnails = true;
						else
							$thumbnails = false;

						if ( $excerpt == 'yes' )
							$excerpt = true;
						else
							$excerpt = false;

						if ( $full_content == 'yes' )
							$full_content = true;
						else
							$full_content = false;

						if ( $price != 'no' || $price != 0 || $price != false )
							$price = true;
						else
							$price = no;

						if ( $buy_button != 'no' || $buy_button != 0 || $buy_button != false )
							$buy_button = true;
						else
							$buy_button = false;

						shoestrap_edd_subtemplate( $thumbnails, $excerpt, $full_content, $price, $buy_button );
						do_action( 'edd_download_after' );

						?>
					</div>
				</div>
			<?php $i++; endwhile; ?>

			<div style="clear:both;"></div>

			<?php wp_reset_postdata(); ?>

			<div id="edd_download_pagination" class="navigation">
				<?php
				if ( is_single() ) {
					echo paginate_links( array(
						'base'    => get_permalink() . '%#%',
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					) );
				} else {
					$big = 999999;
					echo paginate_links( array(
						'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					) );
				}
				?>
			</div>

		</div>
		<?php
		$display = ob_get_clean();
	else:
		$display = sprintf( _x( 'No %s found', 'download post type name', 'edd' ), edd_get_label_plural() );
	endif;

	return apply_filters( 'downloads_shortcode', $display, $atts, $buy_button, $columns, $column_width, $downloads, $excerpt, $full_content, $price, $thumbnails, $query );
}
remove_shortcode( 'downloads', 'edd_downloads_query' );
add_shortcode( 'downloads', 'shoestrap_edd_downloads_query' );

$options = get_option( 'shoestrap' );

if ( $options['shoestrap_edd_navbar_cart'] == 1 ) :
	add_action( 'shoestrap_inside_nav_begin', 'shoestrap_edd_mini_shopping_cart_echo', 20 );
endif;