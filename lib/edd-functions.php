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
				add_action( 'shoestrap_content_override', 'shoestrap_edd_helper_actions_content_override' );
			}

			add_filter( 'edd_purchase_link_defaults', array( $this, 'purchase_link_defaults' ) );

			remove_action( 'edd_cc_form', 'edd_get_cc_form' );
			add_action( 'edd_cc_form', array( $this, 'get_cc_form' ) );

			remove_action( 'edd_purchase_form_register_fields', 'edd_get_register_fields' );
			add_action( 'edd_purchase_form_register_fields', array( $this, 'get_register_fields' ) );

			remove_action( 'edd_purchase_form_login_fields', 'edd_get_login_fields' );
			add_action( 'edd_purchase_form_login_fields', array( $this, 'get_login_fields' ) );

			remove_action( 'edd_checkout_form_top', 'edd_discount_field', -1 );
			add_action( 'edd_checkout_form_top', array( $this, 'discount_field' ), -1 );

			remove_action( 'edd_purchase_form_before_submit', 'edd_checkout_final_total', 999 );
			add_action( 'edd_purchase_form_before_submit', array( $this, 'checkout_final_total' ), 999 );

			remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_submit', 9999 );
			add_action( 'edd_purchase_form_after_cc_form', array( $this, 'checkout_submit' ), 9999 );

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

		/**
		 * Renders the credit card info form.
		 *
		 * @since 1.0
		 * @return void
		 */
		function get_cc_form() {
			ob_start(); ?>

			<?php do_action( 'edd_before_cc_fields' ); ?>

			<fieldset id="edd_cc_fields" class="edd-do-validate">
				<legend><?php _e( 'Credit Card Info', 'edd' ); ?></legend>
				<?php if( is_ssl() ) : ?>
					<div class="alert alert-success" id="edd_secure_site_wrapper">
						<h3><i class="el-icon-lock"></i><?php _e( 'This is a secure SSL encrypted payment.', 'edd' ); ?></h3>>
					</div>
				<?php endif; ?>
				<div class="form-group" id="edd-card-number-wrap">
					<label class="col-md-3 control-label edd-label">
						<?php _e( 'Card Number', 'edd' ); ?>
						<span class="edd-required-indicator">*</span>
						<span class="card-type"></span>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'edd' ); ?></small>
						<input type="text" autocomplete="off" name="card_number" class="form-control card-number edd-input required" placeholder="<?php _e( 'Card number', 'edd' ); ?>" />
					</div>
				</div>

				<div class="form-group" id="edd-card-cvc-wrap">
					<label class="edd-label col-md-3 control-label">
						<?php _e( 'CVC', 'edd' ); ?>
						<span class="edd-required-indicator">*</span>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'edd' ); ?></small>
						<input type="text" size="4" autocomplete="off" name="card_cvc" class="form-control card-cvc edd-input required" placeholder="<?php _e( 'Security code', 'edd' ); ?>" />
					</div>
				</div>

				<div class="form-group" id="edd-card-name-wrap">
					<label class="edd-label col-md-3 control-label">
						<?php _e( 'Name on the Card', 'edd' ); ?>
						<span class="edd-required-indicator">*</span>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'The name printed on the front of your credit card.', 'edd' ); ?></small>
						<input type="text" autocomplete="off" name="card_name" class="form-control card-name edd-input required" placeholder="<?php _e( 'Card name', 'edd' ); ?>" />
					</div>
				</div>

				<?php do_action( 'edd_before_cc_expiration' ); ?>

				<div class="form-group card-expiration">
					<label class="col-md-3 control-label edd-label">
						<?php _e( 'Expiration (MM/YY)', 'edd' ); ?>
						<span class="edd-required-indicator">*</span>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'The date your credit card expires, typically on the front of the card.', 'edd' ); ?></small>
						<div class="row">
							<div class="col-md-6">
								<small class="edd-description">Month</small>
								<select name="card_exp_month" class="form-control card-expiry-month edd-select edd-select-small required">
									<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
								</select>
							</div>
							<div class="col-md-6">
								<small class="edd-description">Year</small>
								<select name="card_exp_year" class="form-control card-expiry-year edd-select edd-select-small required">
									<?php for( $i = date('Y'); $i <= date('Y') + 10; $i++ ) { echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>'; } ?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<?php do_action( 'edd_after_cc_expiration' ); ?>
			</fieldset>
			<?php
			do_action( 'edd_after_cc_fields' );

			echo ob_get_clean();
		}

		/**
		 * Renders the user registration fields. If the user is logged in, a login
		 * form is displayed other a registration form is provided for the user to
		 * create an account.
		 *
		 * @since 1.0
		 * @return string
		 */
		function get_register_fields() {
			global $edd_options;
			global $user_ID;

			if ( is_user_logged_in() )
			$user_data = get_userdata( $user_ID );

			ob_start(); ?>
			<fieldset id="edd_register_fields">
				<div class="form-group" id="edd-login-account-wrap">
					<a href="<?php echo add_query_arg('login', 1); ?>" class="edd_checkout_register_login btn btn-success btn-lg btn-block" data-action="checkout_login">
						<?php _e( 'Already have an account?', 'edd' ); ?><?php _e( 'Login', 'edd' ); ?>
					</a>
				</div>
				<?php do_action('edd_register_fields_before'); ?>

				<div class="form-group" id="edd-user-email-wrap">
					<label for="edd-email" class="col-md-3 control-label">
						<?php _e( 'Email', 'edd' ); ?>
						<?php if( edd_field_is_required( 'edd_email' ) ) : ?>
							<span class="edd-required-indicator">*</span>
						<?php endif; ?>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'We will send the purchase receipt to this address.', 'edd' ); ?></small>
						<input name="edd_email" id="edd-email" class="required edd-input form-control" type="email" placeholder="<?php _e( 'Email', 'edd' ); ?>" title="<?php _e( 'Email', 'edd' ); ?>"/>
					</div>
				</div>

				<div class="form-group" id="edd-user-first-name-wrap">
					<label class="edd-label col-md-3 control-label" for="edd-first">
						<?php _e( 'First Name', 'edd' ); ?>
						<?php if( edd_field_is_required( 'edd_first' ) ) : ?>
							<span class="edd-required-indicator">*</span>
						<?php endif; ?>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'We will use this to personalize your account experience.', 'edd' ); ?></small>
						<input class="edd-input required form-control" type="text" name="edd_first" placeholder="<?php _e( 'First Name', 'edd' ); ?>" id="edd-first" value="<?php echo is_user_logged_in() ? $user_data->user_firstname : ''; ?>"/>
					</div>
				</div>

				<div class="form-group" id="edd-user-last-name-wrap">
					<label class="edd-label col-md-3 control-label" for="edd-last">
						<?php _e( 'Last Name', 'edd' ); ?>
						<?php if( edd_field_is_required( 'edd_last' ) ) : ?>
							<span class="edd-required-indicator">*</span>
						<?php endif; ?>
					</label>
					<div class="col-md-9">
						<small class="edd-description"><?php _e( 'We will use this as well to personalize your account experience.', 'edd' ); ?></small>
						<input class="edd-input form-control" type="text" name="edd_last" id="edd-last" placeholder="<?php _e( 'Last name', 'edd' ); ?>" value="<?php echo is_user_logged_in() ? $user_data->user_lastname : ''; ?>"/>
					</div>
				</div>

				<?php do_action('edd_register_fields_after'); ?>

				<fieldset id="edd_register_account_fields">
					<legend><?php _e( 'Create an account', 'edd' ); if( !edd_no_guest_checkout() ) { echo ' ' . __( '(optional)', 'edd' ); } ?></legend>
					<?php do_action('edd_register_account_fields_before'); ?>

					<div class="form-group" id="edd-user-login-wrap">
						<label class="col-md-3 control-label" for="edd_user_login">
							<?php _e( 'Username', 'edd' ); ?>
							<?php if( edd_no_guest_checkout() ) : ?>
								<span class="edd-required-indicator">*</span>
							<?php endif; ?>
						</label>

						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The username you will use to log into your account.', 'edd' ); ?></small>
							<input name="edd_user_login" id="edd_user_login" class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" type="text" placeholder="<?php _e( 'Username', 'edd' ); ?>" title="<?php _e( 'Username', 'edd' ); ?>"/>
						</div>
					</div>

					<div class="form-group" id="edd-user-pass-wrap">
						<label class="col-md-3 control-label" for="password">
							<?php _e( 'Password', 'edd' ); ?>
							<?php if( edd_no_guest_checkout() ) : ?>
								<span class="edd-required-indicator">*</span>
							<?php endif; ?>
						</label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'The password used to access your account.', 'edd' ); ?></small>
							<input name="edd_user_pass" id="edd_user_pass" class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" placeholder="<?php _e( 'Password', 'edd' ); ?>" type="password"/>
						</div>
					</div>

					<div id="edd-user-pass-confirm-wrap" class="edd_register_password form-group">
						<label class="col-md-3 control-label" for="password_again">
							<?php _e( 'Password Again', 'edd' ); ?>
							<?php if( edd_no_guest_checkout() ) : ?>
								<span class="edd-required-indicator">*</span>
							<?php endif; ?>
						</label>
						<div class="col-md-9">
							<small class="edd-description"><?php _e( 'Confirm your password.', 'edd' ); ?></small>
							<input name="edd_user_pass_confirm" id="edd_user_pass_confirm" class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" placeholder="<?php _e( 'Confirm password', 'edd' ); ?>" type="password"/>
						</div>
					</div>

					<?php do_action( 'edd_register_account_fields_after' ); ?>

				</fieldset>
				<input type="hidden" name="edd-purchase-var" value="needs-to-register"/>

				<?php do_action( 'edd_purchase_form_user_info' ); ?>
			</fieldset>
			<?php
			echo ob_get_clean();
		}

		/**
		 * Gets the login fields for the login form on the checkout. This function hooks
		 * on the edd_purchase_form_login_fields to display the login form if a user already
		 * had an account.
		 *
		 * @since 1.0
		 * @return string
		 */
		function get_login_fields() {
			ob_start(); ?>

			<fieldset id="edd_login_fields">
				<div class="form-group" id="edd-new-account-wrap">
					<a href="<?php echo remove_query_arg('login'); ?>" class="edd_checkout_register_login btn btn-success btn-lg btn-block" data-action="checkout_register">
						<?php _e( 'Need to create an account?', 'edd' ); ?>
						<?php _e( 'Register', 'edd' ); if(!edd_no_guest_checkout()) { echo ' ' . __( 'or checkout as a guest.', 'edd' ); } ?>
					</a>
				</div>
				<?php do_action('edd_checkout_login_fields_before'); ?>

				<div class="form-group" id="edd-user-login-wrap">
					<label class="edd-label control-label col-md-3" for="edd-username"><?php _e( 'Username', 'edd' ); ?></label>
					<div class="col-md-9">
						<input class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" type="text" name="edd_user_login" id="edd_user_login" value="" placeholder="<?php _e( 'Your username', 'edd' ); ?>"/>
					</div>
				</div>

				<div id="edd-user-pass-wrap" class="edd_login_password form-group">
					<label class="edd-label control-label col-md-3" for="edd-password"><?php _e( 'Password', 'edd' ); ?></label>
					<div class="col-md-9">
						<input class="form-control <?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input" type="password" name="edd_user_pass" id="edd_user_pass" placeholder="<?php _e( 'Your password', 'edd' ); ?>"/>
						<input type="hidden" name="edd-purchase-var" value="needs-to-login"/>
					</div>
				</div>

				<?php do_action('edd_checkout_login_fields_after'); ?>
			</fieldset><!--end #edd_login_fields-->
			<?php echo ob_get_clean();
		}

		/**
		 * Renders the Discount Code field which allows users to enter a discount code.
		 * This field is only displayed if there are any active discounts on the site else
		 * it's not displayed.
		 *
		 * @since 1.2.2
		 * @return void
		*/
		function discount_field() {
			if( ! isset( $_GET['payment-mode'] ) && count( edd_get_enabled_payment_gateways() ) > 1 && ! edd_is_ajax_enabled() )
				return; // Only show once a payment method has been selected if ajax is disabled

			if ( edd_has_active_discounts() && edd_get_cart_total() ) : ?>
				<fieldset id="edd_discount_code">
					<p id="edd_show_discount" style="display:none;">
						<?php _e( 'Have a discount code?', 'edd' ); ?> <a href="#" class="edd_discount_link"><?php echo _x( 'Click to enter it', 'Entering a discount code', 'edd' ); ?></a>
					</p>
					<p id="edd-discount-code-wrap">
						<label class="edd-label" for="edd-discount">
							<?php _e( 'Discount', 'edd' ); ?>
							<img src="<?php echo EDD_PLUGIN_URL; ?>assets/images/loading.gif" id="edd-discount-loader" style="display:none;"/>
						</label>
						<small class="edd-description"><?php _e( 'Enter a coupon code if you have one.', 'edd' ); ?></span>
						<input class="edd-input form-control" type="text" id="edd-discount" name="edd-discount" placeholder="<?php _e( 'Enter discount', 'edd' ); ?>"/>
					</p>
				</fieldset>
				<?php
			endif;
		}

		/**
		 * Shows the final purchase total at the bottom of the checkout page
		 *
		 * @since 1.5
		 * @return void
		 */
		function checkout_final_total() { ?>
			<h2 class="text-center">
				<?php _e( 'Purchase Total:', 'edd' ); ?>
				<span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_amount( false ); ?>" data-total="<?php echo edd_get_cart_amount( true, true ); ?>">
				<?php edd_cart_total(); ?>
			</h2>
			<?php
		}

		/**
		 * Renders the Checkout Submit section
		 *
		 * @since 1.3.3
		 * @return void
		 */
		function checkout_submit() { ?>
			<fieldset id="edd_purchase_submit">
				<?php do_action( 'edd_purchase_form_before_submit' ); ?>
				<?php edd_checkout_hidden_fields(); ?>
				<?php echo $this->checkout_button_purchase(); ?>
				<?php do_action( 'edd_purchase_form_after_submit' ); ?>
				<?php if ( ! edd_is_ajax_enabled() ) { ?>
					<p class="edd-cancel"><a href="javascript:history.go(-1)"><?php _e( 'Go back', 'edd' ); ?></a></p>
				<?php } ?>
			</fieldset>
		<?php
		}

		/**
		 * Renders the Next button on the Checkout
		 *
		 * @since 1.2
		 * @global $edd_options Array of all the EDD Options
		 * @return string
		 */
		function checkout_button_next() {
			global $edd_options;

			ob_start(); ?>

			<input type="hidden" name="edd_action" value="gateway_select" />
			<input type="hidden" name="page_id" value="<?php echo absint( $edd_options['purchase_page'] ); ?>"/>
			<input type="submit" name="gateway_submit" id="edd_next_button" class="edd-submit btn btn-primary" value="<?php _e( 'Next', 'edd' ); ?>"/>
			<?php return apply_filters( 'edd_checkout_button_next', ob_get_clean() );
		}

		/**
		 * Renders the Purchase button on the Checkout
		 *
		 * @since 1.2
		 * @global $edd_options Array of all the EDD Options
		 * @return string
		 */
		function checkout_button_purchase() {
			global $edd_options;
			$color = isset( $edd_options[ 'checkout_color' ] ) ? $edd_options[ 'checkout_color' ] : 'gray';

			if ( edd_get_cart_total() )
				$complete_purchase = ! empty( $edd_options['checkout_label'] ) ? $edd_options['checkout_label'] : __( 'Purchase', 'edd' );
			else
				$complete_purchase = ! empty( $edd_options['checkout_label'] ) ? $edd_options['checkout_label'] : __( 'Free Download', 'edd' );

			ob_start(); ?>
			<input type="submit" class="edd-submit btn btn-primary btn-block btn-lg" id="edd-purchase-button" name="edd-purchase" value="<?php echo $complete_purchase; ?>"/>
			<?php return apply_filters( 'edd_checkout_button_purchase', ob_get_clean() );
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
	}
	global $ss_edd;
	$ss_edd = new Shoestrap_EDD();
}


function shoestrap_edd_helper_actions_content_override() { get_template_part( 'templates/content-download' ); }