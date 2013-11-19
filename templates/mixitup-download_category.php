<?php

/*
 * MixItUp controls for filtering categories
 */
$terms = get_terms( 'download_category' );
$count = count( $terms );
if ( $count > 0 ) : ?>
<div class="btn-group mix-filter-category pull-right">
	<button type="button" class="btn btn-default filter active" data-filter="all"><?php _e( 'All Categories', 'shoestrap_edd' ); ?></button>
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php foreach ( $terms as $term ) : ?>
			<li class="filter" data-filter="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></li>
		<?php endforeach; ?>
	</ul>
</div><?php endif;