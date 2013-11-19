<?php

global $post;
$style          = shoestrap_getVariable( 'shoestrap_edd_box_style' );
$download_size  = shoestrap_getVariable( 'shoestrap_edd_products_width' );
$show_excerpt   = shoestrap_getVariable( 'shoestrap_edd_show_text_in_lists' );
$content_width 	= shoestrap_content_width_px();
$breakpoint     = shoestrap_getVariable( 'screen_tablet' );
$show_excerpt   = shoestrap_getVariable( 'shoestrap_edd_show_text_in_lists' );

// get the layout classes
$mix_class      = 'mix';
$sm_class       = ( $content_width < $breakpoint ) ? 'col-sm-12' : 'col-sm-6';
$md_class       = 'col-md-4';

if ( $content_width < $breakpoint ) :
	$md_class = 'col-md-6';
else :
	if ( $download_size == 'narrow' ) :
		$md_class = 'col-md-3';
	elseif ( $download_size == 'wide' ) :
		$md_class = 'col-md-6';
	endif;
endif;

// get the thumbnail URL
$thumb_url = wp_get_attachment_url( get_post_thumbnail_id() );
if ( $thumb_url == '' ) :
	$thumb_url = get_stylesheet_uri() . '/assets/img/empty.png';
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

// The in-cart class
$in_cart = ( function_exists( 'edd_item_in_cart' ) && edd_item_in_cart( $id ) && !edd_has_variable_prices( $id ) ) ? 'in-cart' : '';
// The variable-priced class
$variable_priced = ( function_exists( 'edd_has_variable_prices' ) && edd_has_variable_prices( $id ) ) ? 'variable-priced' : '';

// Get a list with categories of each download (MixitUp!)
$terms = get_the_terms( $id, 'download_category' );
if ( $terms && ! is_wp_error( $terms ) ) :
	foreach ( $terms as $term ) :
		$download_categories[] = $term->term_id;
	endforeach;
	$categories = join( ' ', $download_categories );
endif;

// Get a list with tags of each download (MixitUp!)
$terms = get_the_terms( $id, 'download_tag' );
if ( $terms && ! is_wp_error( $terms ) ) :
	foreach ( $terms as $term ) :
		$download_tags[] = $term->term_id;
	endforeach;
	$tags = join(" ", $download_tags );
else :
	$tags = '';
endif;

$button_args = array(
	'download_id' => $post->ID,
	'price'       => (bool) true,
	'direct'      => edd_get_download_button_behavior( $post->ID ) == 'direct' ? true : false,
	'text'        => ! empty( $edd_options[ 'add_to_cart_text' ] ) ? $edd_options[ 'add_to_cart_text' ] : __( 'Purchase', 'edd' ),
	'style'       => isset( $edd_options[ 'button_style' ] ) 	   ? $edd_options[ 'button_style' ]     : 'btn',
	'color'       => isset( $edd_options[ 'checkout_color' ] ) 	   ? $edd_options[ 'checkout_color' ] 	: 'blue',
	'class'       => 'btn btn-primary edd-submit'
);
?>

<article itemscope itemtype="http://schema.org/Product" id="edd_download_<?php echo $post->ID; ?>" <?php post_class( array( $in_cart, $variable_priced, $mix_class, $sm_class, $md_class, $categories, $tags ) ); ?> data-name="<?php echo get_the_title( $post->ID ); ?>" data-price="<?php shoestrap_min_price_plain( $post->ID ); ?>">
	<div class="equal">
		<div class="<?php echo shoestrap_edd_element_class(); ?>">
			<?php
				if ( $style != 'panel' ) : ?>
					<div class="download-image">
						<a href="<?php echo get_permalink(); ?>"><img src="<?php echo $image['url']; ?>" /></a>
						<div class="overlay">
							<?php if ( edd_has_variable_prices( $post->ID ) ) : ?>
								<a href="<?php echo get_permalink(); ?>" class="btn btn-primary"><?php _e( 'Choose Option', 'shoestrap_edd' ); ?></a>
							<?php else : ?>
								<?php echo shoestrap_edd_get_purchase_link( $button_args ); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="caption">
						<a itemprop="url" href="<?php echo get_permalink(); ?>">
							<h3 itemprop="name"><?php echo get_the_title(); ?></h3>
						</a>
						<?php shoestrap_edd_price( 'h4' ); ?>
						<?php if ( $show_excerpt == 1 ) : ?>
							<?php the_excerpt(); ?>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="panel-heading">
						<a itemprop="url" href="<?php echo get_permalink(); ?>">
							<h4 itemprop="name"><?php echo get_the_title(); ?></h4>
						</a>
					</div>
					<div class="download-image">
						<a href="<?php echo get_permalink(); ?>"><img src="<?php echo $image['url']; ?>" /></a>
						<div class="overlay">
							<?php if ( edd_has_variable_prices( $post->ID ) ) : ?>
								<a href="<?php echo get_permalink(); ?>" class="btn btn-primary"><?php _e( 'Choose Option', 'shoestrap_edd' ); ?></a>
							<?php else : ?>
								<?php shoestrap_edd_get_purchase_link( $button_args ); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="panel-body">
						<?php shoestrap_edd_price( 'h4' ); ?>
						<?php if ( $show_excerpt == 1 ) : ?>
							<?php the_excerpt(); ?>
						<?php endif; ?>
					</div>
			<?php endif; ?>
		</div>
	</div>
</article>