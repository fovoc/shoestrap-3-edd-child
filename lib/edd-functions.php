<?php

if ( ! class_exists( 'Shoestrap_EDD' ) ) {
	/**
	* The main Shoestrap_EDD class
	*/
	class Shoestrap_EDD {

		function __construct() {
			global $ss_settings;
			// Dequeue default EDD styles
			remove_action( 'wp_enqueue_scripts', 'edd_register_styles' );

			// Add the custom variables pricing dropdown before the purchase link
			// and remove the default radio boxes.
			remove_action( 'edd_purchase_link_top', 'edd_purchase_variable_pricing', 10, 1 );
			add_action( 'edd_purchase_link_top', array( $this, 'purchase_variable_pricing' ), 10, 1 );

			if ( ( $ss_settings['shoestrap_edd_frontpage'] == 1 && is_front_page() ) ) {
				add_action( 'shoestrap_page_header_override', 'shoestrap_blank' );
			}

			add_action( 'wp_head', array( $this, 'assets' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ), 99 );
			add_action( 'shoestrap_index_begin', array( $this, 'isotope_templates' ), 9 );

			if ( is_post_type_archive( 'download' ) || is_tax( 'download_category' ) || is_tax( 'download_tag' ) || ( $ss_settings['shoestrap_edd_frontpage'] == 1 && is_front_page() ) ) {
				add_action( 'shoestrap_index_begin', array( $this, 'helper_actions_index_begin' ), 30 );
				add_action( 'shoestrap_index_end', array( $this, 'helper_actions_index_end' ) );
			}

			add_filter( 'edd_purchase_link_defaults', array( $this, 'purchase_link_defaults' ) );

			add_filter( 'pre_get_posts', array( $this, 'downloads_on_homepage' ) );
			add_action( 'shoestrap_inside_nav_begin', array( $this, 'add_minicart_to_navbar' ) );
			/*
			 * Remove EDD Specs for the bottom of the content.
			 * This only applied when the "EDD Software Specs" is installed.
			 * We are removing the default version because we're adding these in the meta widget.
			 */
			if ( class_exists( 'EDD_Software_Specs' ) ) {
				global $EDD_Software_Specs;
				remove_action( 'edd_after_download_content', array( $EDD_Software_Specs, 'specs' ), 30 );
			}
		}

		function assets() {
			global $ss_settings;

			$infinitescroll = $ss_settings['shoestrap_edd_infinite_scroll'];
			$equalheights 	= $ss_settings['shoestrap_edd_equalheights'];

			// Register && Enqueue Bootstrap Multiselect
			wp_register_script( 'shoestrap_multiselect', get_stylesheet_directory_uri() . '/assets/js/bootstrap-multiselect.js', false, null, true );
			wp_enqueue_script( 'shoestrap_multiselect' );

			// Bootstrap Multiselect stylesheet
			wp_enqueue_style( 'shoestrap_multiselect_css', get_stylesheet_directory_uri(). '/assets/css/bootstrap-multiselect.css', false, null );

			// Register && Enqueue Isotope
			wp_register_script( 'shoestrap_isotope', get_stylesheet_directory_uri() . '/assets/js/jquery.isotope.min.js', false, null, true );
			wp_enqueue_script( 'shoestrap_isotope' );

			// Register && Enqueue Isotope-Sloppy-Masonry
			wp_register_script( 'shoestrap_isotope_sloppy_masonry', get_stylesheet_directory_uri() . '/assets/js/jquery.isotope.sloppy-masonry.min.js', false, null, true );
			wp_enqueue_script( 'shoestrap_isotope_sloppy_masonry' );

			if ( $equalheights == 1 ) {
				// Register && Enqueue jQuery EqualHeights
				wp_register_script( 'shoestrap_edd_equalheights', get_stylesheet_directory_uri() . '/assets/js/jquery.equalheights.min.js', false, null, true );
				wp_enqueue_script( 'shoestrap_edd_equalheights' );
			}

			if ( $infinitescroll == 1 ) {
				// Register && Enqueue Infinite Scroll
				wp_register_script( 'shoestrap_edd_infinitescroll', get_stylesheet_directory_uri() . '/assets/js/jquery.infinitescroll.min.js', false, null, true );
				wp_register_script( 'shoestrap_edd_imagesloaded', get_stylesheet_directory_uri() . '/assets/js/imagesloaded.pkgd.min.js', false, null, true );
				wp_enqueue_script( 'shoestrap_edd_imagesloaded' );
				wp_enqueue_script( 'shoestrap_edd_infinitescroll' );
			}
		}

		/*
		 * Load our custom scripts
		 */
		function load_scripts() {
			wp_enqueue_script( 'shoestrap_script', get_stylesheet_directory_uri() . '/assets/js/script.js' );
			wp_localize_script( 'shoestrap_script', 'shoestrap_script_vars', array(
					'equalheights'   => shoestrap_getVariable( 'shoestrap_edd_equalheights' ),
					'infinitescroll' => shoestrap_getVariable( 'shoestrap_edd_infinite_scroll' ),
					'no_filters'     =>  __( 'No filters', 'shoestrap_edd' ),
					'msgText'        => "<div class='progress progress-striped active' style='width:220px;margin-bottom:0px;'><div class='progress-bar progress-bar-" . __( shoestrap_getVariable( 'shoestrap_edd_loading_color' ) ) . "' style='width: 100%;'><span class='edd_bar_text'>" . __( shoestrap_getVariable( 'shoestrap_edd_loading_text' ) ) . "<span></div></div>",
					'finishedMsg'    => "<div class='progress progress-striped active' style='width:220px;margin-bottom:0px;'><div class='progress-bar progress-bar-" . __( shoestrap_getVariable( 'shoestrap_edd_end_color' ) ) . "' style='width: 100%;'><span class='edd_bar_text'>" . __( shoestrap_getVariable( 'shoestrap_edd_end_text' ) ) . "<span></div></div>"
				)
			);
		}

		/*
		 * Load our custom styles
		 */
		function load_styles() {
			wp_register_style('shoestrap_styles', get_stylesheet_directory_uri() . '/assets/css/styles.css');
			wp_enqueue_style( 'shoestrap_styles' );  
		}

		/*
		 * Add template parts for sorting && filtering and an extra wrapper div.
		 */
		function isotope_templates() {
			if ( is_post_type_archive( 'download' ) || is_tax( 'download_category' ) || is_tax( 'download_tag' ) ) {
				get_template_part( 'templates/shoestrap-edd', 'sorting' );
				get_template_part( 'templates/shoestrap-edd', 'filtering' );
			}
		}

		function helper_actions_index_begin() { 
			global $ss_framework;

			echo $ss_framework->clearfix();
			echo $ss_framework->open_row( 'div', null, 'product-list' );
		}

		function helper_actions_index_end() { 
			global $ss_framework;

			echo $ss_framework->clearfix();
			echo $ss_framework->close_row( 'div' );
		}

		/*
		 * This function is a mini loop that will go through all the items currently displayed
		 * Retrieve their terms, and then return the list items required by isotope
		 * to be properly displayed inside the filters.
		 */
		function downloads_terms_filters( $vocabulary, $echo = false ) {
			global $post;

			$tags   = array();
			$output = '';

			while ( have_posts() ) {
				the_post();
				$terms = wp_get_post_terms( $post->ID, $vocabulary );

				foreach ( $terms as $term ) {
					$tags[] = $term->term_id;
				}
			}

			$tags = array_unique( $tags );

			foreach ( $tags as $tagid ) {
				$tag = get_term( $tagid, $vocabulary );
				$tagname = $tag->name;
				$tagslug = $tag->slug;
				$output .= '<option value=".' . $tagslug . '">' . $tagname . '</option>';
			}

			if ( $echo ) {
				echo $output;
			} else {
				return $output;
			}
		}

		/*
		 * Specify the defaults for purchase links.
		 * We use the 'edd_purchase_link_defaults' filter for this.
		 */
		function purchase_link_defaults( $args ) {
			global $ss_framework; 

			$args['class'] = $ss_framework->button_classes( 'primary' );
			$args['style'] = $ss_framework->button_classes( 'block', 'large' );
			return $args;
		}

		function element_class() {
			global $ss_settings;

			$style = $ss_settings['shoestrap_edd_box_style'];

			if ( $style == 'well' ) {
				$maindivclass = 'well well-sm';
			} elseif ( $style == 'panel' ) {
				$maindivclass = 'panel panel-default';
			} else {
				$maindivclass = 'thumbnail';
			}

			return $maindivclass;	
		}

		/*
		 * The Original price variables for EDD downloads is displayed as radio input.
		 * The below function replaces that with a dropdown.
		 */
		function purchase_variable_pricing( $download_id ) {
			$variable_pricing = edd_has_variable_prices( $download_id );

			if ( ! $variable_pricing ) {
				return;
			}

			$prices = apply_filters( 'edd_purchase_variable_prices', edd_get_variable_prices( $download_id ), $download_id );
			$type   = edd_single_price_option_mode( $download_id ) ? 'checkbox' : 'radio';

			do_action( 'edd_before_price_options', $download_id ); ?>
			
			<div class="edd_price_options">
				<?php if ( $prices ) :
					echo '<select class="form-control" name="edd_options[price_id][]">';

					foreach ( $prices as $key => $price ) :
						printf(
							'<option for="%3$s" name="edd_options[price_id][]" id="%3$s" class="%4$s" value="%5$s" %7$s> %6$s</option>',
							checked( 0, $key, false ),
							$type,
							esc_attr( 'edd_price_option_' . $download_id . '_' . $key ),
							esc_attr( 'edd_price_option_' . $download_id ),
							esc_attr( $key ),
							esc_html( $price['name'] . ' - ' . edd_currency_filter( edd_format_amount( $price[ 'amount' ] ) ) ),
							selected( isset( $_GET['price_option'] ), $key, false )
						);
					do_action( 'edd_after_price_option', $key, $price, $download_id );
					endforeach;
					echo '</select>';
				endif;

				do_action( 'edd_after_price_options_list', $download_id, $prices, $type );
				?>
			</div>
			<?php do_action( 'edd_after_price_options', $download_id );
		}

		/*
		 * A mini cart. Simply displays number of products and a link.
		 */
		function mini_shopping_cart( $global_btn_class = 'button', $size_class = 'small', $btn_class = 'primary', $price_class = 'danger', $dropdown = true ) {
			global $edd_options, $ss_framework;

			$label = shoestrap_getVariable( 'shoestrap_edd_minicart_label' );

			ob_start();

			$display = 'style="display:none;"';
			$cart_quantity = edd_get_cart_quantity();

			if ( $cart_quantity > 0 ) {
				$display = "";
			}

			$btn_classes = $global_btn_class . ' ' . $price_class . ' ' . $size_class;
			$a_classes   = $global_btn_class . ' ' . $btn_class . ' ' . $size_class;
			?>

			<div class="<?php echo $ss_framework->button_group_classes(); ?>">
				<button type="button" disabled="disabled" class="<?php echo $btn_classes; ?> nav-cart-quantity"><?php echo $cart_quantity; ?></button>
				<a class="<?php echo $a_classes; ?>" href="<?php echo edd_get_checkout_uri(); ?>">
					<i class="el-icon-shopping-cart"></i>
					<?php echo $label; ?>
				</a>
			</div>
			<?php echo ob_get_clean();
		}

		/*
		 * Display Products on the Homepage.
		 * This will simply alter the query so that EDD Downloads are shown
		 * on the Frontpage instead of the list of posts.
		 */
		function downloads_on_homepage( $query ) {
		    if ( shoestrap_getVariable( 'shoestrap_edd_frontpage' ) == 1 && $query->is_home() && $query->is_main_query() ) {
		        $query->set( 'post_type', array( 'download' ) );
		    }
		}

		/*
		 * Calculate the classes of the downloads in archives
		 * based on the settings in the admin panel
		 * and the content width.
		 *
		 * This function also calculates some additional classes
		 * that must be added so that the grid works properly
		 * using some clear-left declarations.
		 */
		function get_download_class( $download_size = 'normal' ) {
			$content_width 	= shoestrap_content_width_px();
			$breakpoint 	= shoestrap_getVariable( 'screen_tablet' );

			$class = 'col-sm-6 col-md-4';

			if ( $content_width < $breakpoint ) {
				if ( $download_size == 'wide' ) {
					$class = 'col-sm-12 col-md-6';
				}
			} else {
				if ( $download_size == 'narrow' ) {
					$class = 'col-sm-6 col-md-3';
				} elseif ( $download_size == 'wide' ) {
					$class = 'col-sm-6';
				}
			}

			return $class;
		}

		function add_minicart_to_navbar() {
			if ( shoestrap_getVariable( 'shoestrap_edd_navbar_cart' ) == 1 ) :
				global $ss_framework;
			?>
				<div class="pull-right">
					<?php $this->mini_shopping_cart( $ss_framework->button_classes( null, null, null, 'navbar-btn' ), null, $ss_framework->button_classes( 'success' ), $ss_framework->button_classes( 'default' ), null ); ?>
				</div>
			<?php
			endif;
		}

		/*
		 * Custom function to display prices
		 */
		function price( $el = 'h2' ) {
			// find if there's a ZERO price in variable pricing
			$zero_price = 0;
			if ( edd_has_variable_prices( get_the_ID() ) == 1 ) {
				foreach ( edd_get_variable_prices( get_the_ID() ) as $key => $price ) {
					if ( esc_html( $price[ 'amount' ] ) == '0' ) {
						$zero_price = 1;
					}
				}
			}
			
			$coming_soon = isset( $post->ID ) ? get_post_meta( $post->ID, 'edd_coming_soon', true ) : '';
			$coming_soon_text = isset( $post->ID ) ? get_post_meta( $post->ID, 'edd_coming_soon_text', true ) : '';
			$element = '<' . $el . ' itemprop="price" class="edd_price">';

			echo $element;

			if ( $coming_soon ) :
				if ( $coming_soon_text )
					echo $coming_soon_text;
				else
					echo __( 'Coming soon', 'shoestrap-edd' );

			elseif ( '0' == edd_get_download_price( get_the_ID() ) && !edd_has_variable_prices( get_the_ID() ) ) :
				echo __( 'Free', 'shoestrap-edd' );
				echo '<span class="hidden price">0</span>';

			elseif ( edd_has_variable_prices( get_the_ID() ) && $zero_price == 1 ) :
				_e( 'From Free', 'shoestrap_edd' );
				echo '<span class="hidden price">0</span>';

			elseif ( edd_has_variable_prices( get_the_ID() ) ) :
				_e( 'From ', 'shoestrap_edd' );
				edd_price( get_the_ID() );

				$prices = edd_get_variable_prices( get_the_ID() );
				// Return the lowest price
				$price_float = 0;
		      foreach ($prices as $key => $value)
		        if ( ( ( (float)$prices[ $key ]['amount'] ) < $price_float ) or ( $price_float == 0 ) ) 
		          $price_float = (float)$prices[ $key ]['amount'];
		          $price = edd_sanitize_amount( $price_float );
				echo '<span class="hidden price">'; echo $price; echo '</span>';

			else :
				edd_price( get_the_ID() );
				echo '<span class="hidden price">'; echo edd_get_download_price( get_the_ID() ); echo '</span>';

			endif;

			echo '</' . $el . '>';
		}
	}
	global $ss_edd;
	$ss_edd = new Shoestrap_EDD();
}


function shoestrap_edd_helper_actions_content_override() { get_template_part( 'templates/content-download' ); }