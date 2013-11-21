<?php

/*
 * MixItUp controls for filtering tags
 */
$terms = get_terms( 'download_tag' );
$count = count( $terms );
if ( $count > 0 ) : ?>
<div class="btn-group mix-filter-tag pull-right">
	<button type="button" class="btn btn-default filter active" data-filter="all"><?php _e( 'All Tags', 'shoestrap_edd' ); ?></button>
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php shoestrap_edd_downloads_terms_query_helper( 'download_tag', true ); ?>
	</ul>
</div><?php endif;