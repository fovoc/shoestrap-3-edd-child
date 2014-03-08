<?php

global $ss_framework;

echo '<div class="pull-left left sort">';


	$content_name  = '<li class="default"><a href="#"><i class="el-icon-remove"></i> ' . __( 'Default', 'shoestrap_edd' ) . '</a></li>';
	$content_name .= '<li class="false"><a href="#name"><i class="el-icon-chevron-down"></i> ' . __( 'Descending', 'shoestrap_edd' ) . '</a></li>';
	$content_name .= '<li class="true"><a href="#name"><i class="el-icon-chevron-up"></i> ' . __( 'Ascending', 'shoestrap_edd' ) . '</a></li>';

	$content_price  = '<li class="default"><a href="#"><i class="el-icon-remove"></i> ' . __( 'Default', 'shoestrap_edd' ) . '</a></li>';
	$content_price .= '<li class="false"><a href="#price"><i class="el-icon-chevron-down"></i> ' . __( 'Descending', 'shoestrap_edd' ) . '</a></li>';
	$content_price .= '<li class="true"><a href="#price"><i class="el-icon-chevron-up"></i> ' . __( 'Ascending', 'shoestrap_edd' ) . '</a></li>';

	echo $ss_framework->make_dropdown_button( 'default', 'medium', 'left btn-name', null, '<span class="name">' . __( 'Name', 'shoestrap_edd' ) . '</span>', $content_name );

	echo $ss_framework->make_dropdown_button( 'default', 'medium', 'left btn-price', null, '<span class="name">' . __( 'Price', 'shoestrap_edd' ) . '</span>', $content_price );

echo '</div>';