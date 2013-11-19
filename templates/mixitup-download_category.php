<?php

/*
 * MixItUp controls for filtering categories
 */
$terms = get_terms( 'download_category' );
$count = count( $terms );
if ( $count > 0 ) : ?>
	<div class="btn-group btn-group-sm pull-right mix-filter-category">
		<li class="filter btn btn-default active" data-filter="all"><?php _e( 'All Categories', 'shoestrap_edd' ); ?></li>
		<?php foreach ( $terms as $term ) : ?>
			<li class="filter btn btn-default" data-filter="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></li>
		<?php endforeach; ?>
	</div>
<?php endif;