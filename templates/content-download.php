<?php

global $post, $ss_framework, $ss_settings, $ss_edd, $ss_layout;

$style          = $ss_settings['shoestrap_edd_box_style'];
$download_size  = $ss_settings['shoestrap_edd_products_width'];
$show_excerpt   = $ss_settings['shoestrap_edd_show_text_in_lists'];
$content_width 	= $ss_layout->content_width_px( false );
if ( isset( $ss_settings['screen_tablet'] ) ) {
	$breakpoint = $ss_settings['screen_tablet'];
} else {
	$breakpoint = 1600;
}
$show_excerpt   = $ss_settings['shoestrap_edd_show_text_in_lists'];
$in_cart        = '';
$categories     = '';
$tags           = '';

// get the layout classes
if ( $content_width < $breakpoint ) {
	$sm_class = $ss_framework->column_classes( array( 'tablet' => 12 ) );
} else {
	$sm_class = $ss_framework->column_classes( array( 'tablet' => 6 ) );
}

$md_class       = $ss_framework->column_classes( array( 'medium' => 4 ) );

if ( $content_width < $breakpoint ) {
	$md_class = $ss_framework->column_classes( array( 'medium' => 6 ) );
} else {
	if ( $download_size == 'narrow' ) {
		$md_class = $ss_framework->column_classes( array( 'medium' => 3 ) );
	} elseif ( $download_size == 'wide' ) {
		$md_class = $ss_framework->column_classes( array( 'medium' => 6 ) );
	}
}

// get the thumbnail URL
$thumb_url = wp_get_attachment_url( get_post_thumbnail_id() );
if ( $thumb_url == '' ) {
	$thumb_url = get_stylesheet_directory_uri() . '/assets/img/empty.png';
}

$args = array(
	"url"       => $thumb_url,
	"width"     => 691,
	"height"    => 424,
	"crop"      => true,
	"retina"    => "",
	"resize"    => true,
);
$image = Shoestrap_Image::image_resize( $args );

// The in-cart class
$in_cart = ( function_exists( 'edd_item_in_cart' ) && edd_item_in_cart( $id ) && !edd_has_variable_prices( $id ) ) ? 'in-cart' : '';

// The variable-priced class
$variable_priced = ( function_exists( 'edd_has_variable_prices' ) && edd_has_variable_prices( $id ) ) ? 'variable-priced' : '';

// Get a list with categories of each download (MixitUp!)
$terms = get_the_terms( $id, 'download_category' );
if ( $terms && ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) {
		$download_categories[] = $term->slug;
	}

	$categories = join( ' ', $download_categories );
} else {
	$categories = '';
}

// Get a list with tags of each download (MixitUp!)
$terms = get_the_terms( $id, 'download_tag' );

if ( $terms && ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) {
		$download_tags[] = $term->slug;
	}

	$tags = join(" ", $download_tags );
} else {
	$tags = '';
} ?>

<article itemscope itemtype="http://schema.org/Product" id="edd_download_<?php echo $post->ID; ?>" <?php post_class( array( $in_cart, $variable_priced, $sm_class, $md_class, $categories, $tags ) ); ?> >
	<div class="equal">
		<div class="<?php echo $ss_edd->element_class(); ?>">
			<?php
				if ( $style != 'panel' ) : ?>
					<div class="download-image">
						<a href="<?php echo get_permalink(); ?>"><img src="<?php echo $image['url']; ?>" /></a>
						<div class="overlay">
							<?php if ( edd_has_variable_prices( $post->ID ) ) : ?>
								<a href="<?php echo get_permalink(); ?>" class="btn btn-primary"><?php _e( 'Choose Option', 'shoestrap_edd' ); ?></a>
							<?php else : ?>
								<?php echo edd_get_purchase_link(); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="caption">
						<a itemprop="url" href="<?php echo get_permalink(); ?>">
							<h3 itemprop="name" class="name"><?php echo get_the_title(); ?></h3>
						</a>
						<?php $ss_edd->price( 'h4' ); ?>
						<?php if ( $show_excerpt == 1 ) : ?>
							<?php the_excerpt(); ?>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="panel-heading">
						<a itemprop="url" href="<?php echo get_permalink(); ?>">
							<h4 itemprop="name" class="name"><?php echo get_the_title(); ?></h4>
						</a>
					</div>
					<div class="download-image">
						<a href="<?php echo get_permalink(); ?>"><img src="<?php echo $image['url']; ?>" /></a>
						<div class="overlay">
							<?php if ( edd_has_variable_prices( $post->ID ) ) : ?>
								<a href="<?php echo get_permalink(); ?>" class="btn btn-primary"><?php _e( 'Choose Option', 'shoestrap_edd' ); ?></a>
							<?php else : ?>
								<?php echo edd_get_purchase_link(); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="panel-body">
						<?php $ss_edd->price( 'h4' ); ?>
						<?php if ( $show_excerpt == 1 ) : ?>
							<?php the_excerpt(); ?>
						<?php endif; ?>
					</div>
			<?php endif; ?>
		</div>
	</div>
</article>