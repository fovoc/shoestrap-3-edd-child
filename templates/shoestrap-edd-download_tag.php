<?php

/*
 * MixItUp controls for filtering tags
 */
$terms = get_terms( 'download_tag' );
$count = count( $terms );
if ( $count > 0 ) : ?>
<div class="btn-group filter-tag pull-right">
	<a class="btn btn-default btn-tag" data-filter="*" title="<?php _e( 'Click to reset', 'shoestrap_edd' ); ?>"><?php _e( 'All Tags', 'shoestrap_edd' ); ?></a>
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	<ul class="dropdown-menu" role="menu">
		<?php shoestrap_edd_downloads_terms_filters( 'download_tag', true ); ?>
	</ul>
</div><?php endif;