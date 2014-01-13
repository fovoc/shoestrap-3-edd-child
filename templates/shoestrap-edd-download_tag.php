<?php

/*
 * Isotope controls for filtering tags
 */
$terms = get_terms( 'download_tag' );
$count = count( $terms );
if ( $count > 0 ) : ?>
<div class="filter-tags pull-right">	
	<select id="download-tags" multiple="multiple" style="display: none;">
		<option value="multiselect-all" selected="selected"> <?php _e( 'All Tags', 'shoestrap_edd' ); ?></option>
  	<?php shoestrap_edd_downloads_terms_filters( 'download_tag', true ); ?>
	</select>
</div>
<?php endif;