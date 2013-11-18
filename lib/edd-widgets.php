<?php

class Shoestrap_EDD_Download_Meta extends WP_Widget {
	private $fields = array(
		'title'          => 'Title (optional)',
	);

	function __construct() {
		$widget_ops = array(
			'classname' 	=> 'widget_shoestrap_edd',
			'description'	=> __( 'Use this widget to add meta details for single products', 'shoestrap_edd' )
		);

		$this->WP_Widget( 'widget_shoestrap_edd', __( 'Shoestrap EDD: Download meta', 'shoestrap_edd' ), $widget_ops );
		$this->alt_option_name = 'widget_shoestrap_edd';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_shoestrap_edd', 'widget' );
		if ( !is_array( $cache ) ) :
			$cache = array();
		endif;

		if ( !isset( $args['widget_id'] ) ) :
			$args['widget_id'] = null;
		endif;

		if ( isset( $cache[$args['widget_id']] ) ) :
			echo $cache[$args['widget_id']];
			return;
		endif;

		ob_start();
		extract($args, EXTR_SKIP);

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Product Details', 'shoestrap_edd' ) : $instance['title'], $instance, $this->id_base );
		foreach( $this->fields as $name => $label ) :
			if ( !isset( $instance[$name] ) ) :
				$instance[$name] = '';
			endif;
		endforeach;

		echo $before_widget;

		if ( $title ) :
			echo $before_title, $title, $after_title;
		endif;

		global $post;

		$button_args = array(
			'download_id' => $post->ID,
			'price'       => (bool) false,
			'direct'      => edd_get_download_button_behavior( $post->ID ) == 'direct' ? true : false,
			'text'        => !empty( $edd_options[ 'add_to_cart_text' ] )  ? $edd_options[ 'add_to_cart_text' ] : __( 'Purchase', 'edd' ),
			'style'       => isset( $edd_options[ 'button_style' ] ) 	   ? $edd_options[ 'button_style' ]     : 'btn',
			'color'       => isset( $edd_options[ 'checkout_color' ] ) 	   ? $edd_options[ 'checkout_color' ] 	: 'blue',
			'class'       => 'btn btn-primary btn-lg btn-block edd-submit'
		);

		shoestrap_edd_price();	

		if ( edd_has_variable_prices( $post->ID ) == 1 ) {
			echo shoestrap_edd_get_purchase_link( $button_args );
		} else {
			echo edd_get_purchase_link( $button_args );
		}
		?>
		<table class="table table-striped table-bordered" style="margin-top: 2em;">
			<?php
			// Number of Downloads
			?>
			<tr>
				<td><i class="elusive icon-shopping-cart"></i> <?php _e( 'Downloads', 'shoestrap_edd' ); ?></td>
				<td><?php echo edd_get_download_sales_stats( $post->ID ); ?></td>
			</tr>

			<tr>
				<td><i class="elusive icon-user"> <?php _e( 'Author', 'shoestrap_edd' ); ?></td>
				<td><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta( 'display_name' ); ?></a></td>
			</tr>

			<?php if ( !class_exists( 'EDD_Software_Specs' ) ) : ?>
				<?php
				// Created Date
				?>
				<tr>
					<td><i class="elusive icon-calendar-sign"></i> <?php _e( 'Created', 'shoestrap_edd' ); ?></td>
					<td><?php echo get_the_date(); ?></td>
				</tr>
			
				<?php
				// Updated Date
				?>
				<?php if ( get_the_date() != get_the_modified_date() ) : ?>
					<tr>
						<td><i class="elusive icon-calendar-sign"></i> <?php _e( 'Last Modified', 'shoestrap_edd' ); ?></td>
						<td><?php echo get_the_modified_date(); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>
			<?php
			// Software Specs
			?>
			<?php if ( class_exists( 'EDD_Software_Specs' ) ) :
				$isa_curr = empty($pc) ? 'USD' : $pc;
				$eddchangelog_version = get_post_meta( $post->ID, '_edd_sl_version', TRUE );

				if ( empty( $eddchangelog_version ) )
					$vKey = '_smartest_currentversion';
				else
					$vKey = '_edd_sl_version';

		
				$sVersion = get_post_meta($post->ID, $vKey, true);
				$appt = get_post_meta($post->ID, '_smartest_apptype', true);
				$filt = get_post_meta($post->ID, '_smartest_filetype', true);
				$fils = get_post_meta($post->ID, '_smartest_filesize', true);
				$reqs = get_post_meta($post->ID, '_smartest_requirements', true);
				
				$moddate = ($dm) ? date('Y-m-d', $dm) : '';$moddatenice = ($dm) ? date('F j, Y', $dm) : '';
				?>

				<tr>
					<td><i class="elusive icon-calendar-sign"></i> <?php _e( 'Release date:', 'edd-specs' ); ?></td>
					<td>
						<meta itemprop="datePublished" content="<?php echo get_post_time('Y-m-d', false, $post->ID); ?>">
						<?php echo get_post_time('F j, Y', false, $post->ID, true); ?>
					</td>
				</tr>

				<tr>
					<td><i class="elusive icon-calendar-sign"></i> <?php _e( 'Last updated:', 'edd-specs' ); ?></td>
					<td><meta itemprop="dateModified" content="<?php echo $moddate; ?>"><?php echo $moddatenice; ?></td>
				</tr>
			
				<?php if ( $sVersion ) : ?>
					<tr>
						<td><i class="elusive icon-laptop"></i> <?php _e( 'Current version:', 'edd-specs' ); ?></td>
						<td itemprop="softwareVersion"><?php echo $sVersion; ?></td>
					</tr>
				<?php endif; ?>

				<?php if ( $appt ) : ?>
					<tr>
						<td><i class="elusive icon-laptop"></i> <?php _e( 'Software application type:', 'edd-specs' ); ?></td>
						<td itemprop="applicationCategory"><?php echo $appt; ?></td>
					</tr>
				<?php endif; ?>

				<?php if ( $filt ) : ?>
					<tr>
						<td><i class="elusive icon-file"></i> <?php _e( 'File format:', 'edd-specs' ); ?></td>
						<td itemprop="fileFormat"><?php echo $filt; ?></td>
					</tr>
				<?php endif; ?>

				<?php if ( $fils ) : ?>
					<tr>
						<td><i class="elusive icon-file"></i> <?php _e( 'File size:', 'edd-specs' ); ?></td>
						<td itemprop="fileSize"><?php echo $fils; ?></td>
					</tr>
				<?php endif; ?>

				<?php if ( $reqs ) : ?>
					<tr>
						<td><i class="elusive icon-tasks"></i> <?php _e( 'Requirements:', 'edd-specs' ); ?></td>
						<td itemprop="requirements"><?php echo $reqs; ?></td>
					</tr>
				<?php endif; ?>

				<?php if ( $pric ) : ?>
					<tr itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<td><i class="elusive icon-credit-card"></i> <?php _e( 'Price:', 'edd-specs' ); ?></td>
						<td>
							<span><?php echo $pric; ?></span>
							<span itemprop="priceCurrency"><?php echo $isa_curr; ?></span>
						</td>
					</tr>
					
					<?php do_action( 'eddss_add_specs_table_row' ); ?>
				<?php endif; ?>

			<?php endif; ?>

		</table>
		<?php
		echo $after_widget;
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_shoestrap_edd', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance = array_map( 'strip_tags', $new_instance );
		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_shoestrap_edd'] ) ) :
			delete_option('widget_shoestrap_edd');
		endif;
		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_shoestrap_edd', 'widget' );
	}
}

function shoestrap_edd_widgets_init() {
	register_widget('Shoestrap_EDD_Download_Meta');
}
add_action('widgets_init', 'shoestrap_edd_widgets_init');
