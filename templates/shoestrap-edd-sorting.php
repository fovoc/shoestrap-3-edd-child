<?php

global $ss_framework;

echo '<div class="pull-left left sort">';


	$content  = '<li class="default"><a href="#"><i class="el-icon-remove"></i> ' . __( 'Default', 'shoestrap_edd' ) . '</a></li>';
	$content .= '<li class="false"><a href="#name"><i class="el-icon-chevron-down"></i> ' . __( 'Descending', 'shoestrap_edd' ) . '</a></li>';
	$content .= '<li class="true"><a href="#name"><i class="el-icon-chevron-up"></i> ' . __( 'Ascending', 'shoestrap_edd' ) . '</a></li>';

	echo $ss_framework->make_dropdown_button( 'default', 'medium', null, 'left btn-name', __( 'Name', 'shoestrap_edd' ), $content );

	echo $ss_framework->make_dropdown_button( 'default', 'medium', null, 'left btn-price', __( 'Price', 'shoestrap_edd' ), $content );

echo '</div>';