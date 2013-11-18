<?php

if ( !is_tax( 'download_category' ) ) :
	get_template_part( 'templates/mixitup', 'download_category' );
elseif ( !is_tax( 'download_tag' ) ) :
	get_template_part( 'templates/mixitup', 'download_tag' );
endif;