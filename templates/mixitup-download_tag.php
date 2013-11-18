<?php

/*
 * MixItUp controls for filtering t
 */
$terms = get_terms( 'download_tag' );
$count = count( $terms );
if ( $count > 0 ) : ?>
	<div class="btn-group btn-group-sm pull-right">
		<li class="filter btn btn-default active" data-filter="all"><?php _e( 'All Tags', 'shoestrap_edd' ); ?></li>
		<?php foreach ( $terms as $term ) : ?>
			<li class="filter btn btn-default" data-filter="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></li>
		<?php endforeach; ?>
	</div>
<?php endif;