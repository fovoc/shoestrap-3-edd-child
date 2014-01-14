<?php

/*
 * Isotope controls for filtering
 */
$terms = get_terms( 'download_category' );
$count = count( $terms );
if ( $count > 0 ) : ?>
<div class="filter pull-right">	
	<select multiple="multiple" style="display: none;">
		<option value="multiselect-all" selected="selected"> <?php _e( 'All', 'shoestrap_edd' ); ?></option>
  		<optgroup label="<?php _e( 'Categories', 'shoestrap_edd' ); ?>">
  			<?php shoestrap_edd_downloads_terms_filters( 'download_category', true ); ?>
  		</optgroup>
  		<optgroup label="<?php _e( 'Tags', 'shoestrap_edd' ); ?>">
  			<?php shoestrap_edd_downloads_terms_filters( 'download_tag', true ); ?>
  		</optgroup>
	</select>
</div>
<?php endif;