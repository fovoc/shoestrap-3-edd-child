<?php

/*
 * Isotope controls for filtering categories
 */
$terms = get_terms( 'download_category' );
$count = count( $terms );
if ( $count > 0 ) : ?>
<div class="filter-cats pull-right">	
	<select id="download-cats" multiple="multiple" style="display: none;">
		<option value="multiselect-all" selected="selected"> <?php _e( 'All Categories', 'shoestrap_edd' ); ?></option>
  	<?php shoestrap_edd_downloads_terms_filters( 'download_category', true ); ?>
	</select>
</div>
<?php endif;