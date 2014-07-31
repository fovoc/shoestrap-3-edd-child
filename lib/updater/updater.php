<?php

if ( ! class_exists( 'EDD_SL_Theme_Updater' ) ) {
	// Load our custom theme updater
	include( dirname( __FILE__ ) . '/EDD_SL_Theme_Updater.php' );
}

function shoestrap_edd_theme_updater() {

	$license = trim( get_option( 'shoestrap_edd_license_key' ) );

	$edd_updater = new EDD_SL_Theme_Updater( array(
			'remote_api_url' 	=> 'http://shoestrap.org',
			'version' 			=> '1.2',
			'license' 			=> $license,
			'item_name' 		=> 'Shoestrap 3 EDD Child',
			'author'			=> 'aristath, fovoc'
		)
	);
}
add_action( 'admin_init', 'shoestrap_edd_theme_updater' );

/**
 * Add page to menu under "Settings"
 */
if ( ! function_exists( 'ss_updater_add_menus' ) ) {
	function ss_updater_add_menus() {
		add_theme_page( 'Shoestrap Extensions & Addons', 'Shoestrap Extensions', 'manage_options', 'ss-updater', 'ss_updater_settings_page' );
	}

	add_action( 'admin_menu', 'ss_updater_add_menus' );
}

/**
 * Build our Options page
 */
if ( ! function_exists( 'ss_updater_settings_page' ) ) {
	function ss_updater_settings_page() {

		echo '<div class="wrap">';

			echo '<h2>' . __( 'Shoestrap Licensing' ) . '</h2><hr><br>';

			// If there are no products that need licensing, display a message
			if ( ! has_action( 'shoestrap_updater_form_content' ) ) {
				_e( 'No products require a license key.' );
			}

			?>

			<table class="wp-list-table widefat plugins">
				<thead>
					<tr>
						<th scope="col" id="name" class="manage-column column-name" style="">Product name</th>
						<th scope="col" id="license" class="manage-column column-license" style="">License</th>
						<th scope="col" id="save" class="manage-column column-save" style=""></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php
						// Include our custom licensing fields for all our plugins & themes.
						do_action( 'shoestrap_updater_form_content' );
					?>
				</tbody>
			</table>

			<?php

			// Display the addons section.
			do_action( 'shoestrap_installer_form_content' );

		echo '</div>';
	}
}

/*
 * The license form that is added in the admin page.
 */
if ( ! function_exists( 'shoestrap_edd_license_form' ) ) {
	function shoestrap_edd_license_form() {
		$field_name = 'shoestrap_edd_license';

		$license 	= get_option( 'shoestrap_edd_license_key' );
		$status 	= get_option( 'shoestrap_edd_license_key_status' );
		?>
		<tr id="shoestrap-edd" class="<?php echo $status; ?>">
			<form method="post" action="options.php">
				<?php settings_fields( 'shoestrap_edd_license' ); ?>
				<td class="plugin-title">
					<strong><?php _e( 'Shoestrap 3 EDD Child', 'shoestrap-edd' ); ?></strong>
					<div class="row-actions visible">
						<?php wp_nonce_field( 'shoestrap_edd_nonce', 'shoestrap_edd_nonce' ); ?>

						<?php if ( false !== $license ) : ?>
							<?php if ( $status !== false && $status == 'valid' ) : ?>
								<input type="submit" class="button-secondary" name="shoestrap_edd_license_deactivate" value="<?php _e( 'Deactivate License', 'shoestrap_edd' ); ?>"/>
							<?php else : ?>
								<input type="submit" class="button-secondary" name="shoestrap_edd_license_activate" value="<?php _e( 'Activate License', 'shoestrap_edd' ); ?>"/>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</td>

				<td>
					<input id="shoestrap_edd_license_key" name="shoestrap_edd_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
					<label class="description" for="shoestrap_edd_license_key"><?php _e( 'Enter your license key', 'shoestrap_edd' ); ?></label>
				</td>

				<td>
					<?php submit_button(); ?>
				</td>
			</form>
		</tr>
		<?php
	}
}
add_action( 'shoestrap_updater_form_content', 'shoestrap_edd_license_form' );

/**
 * Register the option
 */
if ( ! function_exists( 'shoestrap_edd_licensing_register_option' ) ) {
	function shoestrap_edd_licensing_register_option() {
		// creates our settings in the options table
		register_setting( 'shoestrap_edd_license', 'shoestrap_edd_license_key', 'shoestrap_edd_license_sanitize' );
	}
	add_action('admin_init', 'shoestrap_edd_licensing_register_option');
}

/*
 * Gets rid of the local license status option when adding a new one
 */
if ( ! function_exists( 'shoestrap_edd_license_sanitize' ) ) {
	function shoestrap_edd_license_sanitize( $new ) {
		$old = get_option( 'shoestrap_edd_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'shoestrap_edd_license_key_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}
}

/*
 * Activate the license
 */
function shoestrap_edd_activate_license() {

	if ( isset( $_POST['shoestrap_edd_license_activate'] ) ) {
		if( ! check_admin_referer( 'shoestrap_edd_nonce', 'shoestrap_edd_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		global $wp_version;

		$license = trim( get_option( 'shoestrap_edd_license_key' ) );

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( 'Shoestrap 3 EDD Child' )
		);

		$response = wp_remote_get( add_query_arg( $api_params, 'http://shoestrap.org' ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'shoestrap_edd_license_key_status', $license_data->license );

	}

}
add_action( 'admin_init', 'shoestrap_edd_activate_license' );

/*
 * De-activate the license
 */
function shoestrap_edd_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['shoestrap_edd_license_deactivate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'shoestrap_edd_nonce', 'shoestrap_edd_nonce' ) ) {
			return; // get out if we didn't click the deactivate button
		}

		// retrieve the license from the database
		$license = trim( get_option( 'shoestrap_edd_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name'  => urlencode( 'Shoestrap 3 EDD Child' )
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, 'http://shoestrap.org' ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'shoestrap_edd_license_key_status' );
		}

	}

}
add_action('admin_init', 'shoestrap_edd_deactivate_license');
