<?php

/*
 * Isotope controls for filtering
 */
global $ss_edd, $ss_framework;

$terms = get_terms( 'download_category' );
$count = count( $terms );

$content  = '<li>';
$content  = '<select multiple="multiple" style="display: none;">';
$content .= '<option value="multiselect-all" selected="selected"> ' . __( 'All', 'shoestrap_edd' ) . '</option>';
$content .= '<optgroup label="' . __( 'Categories', 'shoestrap_edd' ) . '">' . $ss_edd->downloads_terms_filters( 'download_category', false ) . '</optgroup>';
$content .= '<optgroup label="' . __( 'Tags', 'shoestrap_edd' ) . '">' . $ss_edd->downloads_terms_filters( 'download_tag', false ) . '</optgroup>';
$content .= '</select>';
$content .= '</li>';

if ( $count > 0 ) {
	echo '<div class="filter pull-right right">';
	echo $ss_framework->make_dropdown_button( 'default', 'medium', null, 'btn-name', __( 'Filters', 'shoestrap_edd' ), $content );
	echo '</div>';
}