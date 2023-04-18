<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/**
 * Wpgetapi_Woocommerce_Import_License_Handler Class
 */
class Wpgetapi_Pro_License_Handler {

	public $name = 'pro';
	public $version = WPGETAPIEXTRASVERSION;
	public $item_id = 23;
	public $item_name = 'Pro Plugin';

	/**
     * Main constructor
     * @since 1.0.0
     */
    public function __construct() {
    	$this->includes();
    	$this->hooks();
    }


    /**
     * Includes
     * @since  1.0.0
     */
    public function includes() {
    	if ( ! class_exists( 'Wpgetapi_Plugin_Updater' ) ) {
			// load our custom updater
			include dirname( __FILE__ ) . '/Wpgetapi_Plugin_Updater.php';
		}
    }

    /**
     * Hooks
     * @since  1.0.0
     */
    public function hooks() {
    	add_action( 'init', array( $this, 'plugin_updater' ) );
    	add_action( 'admin_init', array( $this, 'register_settings'));
    	add_action( 'admin_init', array( $this, 'register_option' ) );
    	add_action( 'admin_init', array( $this, 'activate_license' ) );
    	add_action( 'admin_init', array( $this, 'deactivate_license' ) );
    }

	
	/**
	 * Initialize the updater. Hooked into `init` to work with the
	 * wp_version_check cron job, which allows auto-updates.
	 */
	public function plugin_updater() {

		// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}

		// retrieve our license key from the DB
		$license_key = trim( get_option( 'wpgetapi_' . $this->name . '_license_key' ) );

		// setup the updater
		$edd_updater = new Wpgetapi_Plugin_Updater(
			WPGETAPISTOREURL,
			WPGETAPIEXTRASFILE,
			array(
				'version' => $this->version, // current version number
				'license' => $license_key, // license key (used get_option above to retrieve from DB)
				'item_id' => $this->item_id, // ID of the product
				'author'  => 'WPGetAPI', // author of this plugin
				'beta'    => false,
			)
		);

	}
	
	function register_settings() {
		add_settings_field(
			'wpgetapi_' . $this->name . '_license_key',
			'<label for="wpgetapi_' . $this->name . '_license_key">' . $this->item_name . '</label>',
			array( $this, 'license_key_settings_field' ),
			WPGETAPILICENSEPAGE,
			'wpgetapi_licenses_section',
		);
	}

	/**
	 * Outputs the license key settings field.
	 *
	 * @return void
	 */
	function license_key_settings_field() {
		$license = get_option( 'wpgetapi_' . $this->name . '_license_key' );
		$status  = get_option( 'wpgetapi_' . $this->name . '_license_status' );

		?>
		<p class="description"><?php esc_html_e( 'Enter your license key.' ); ?></p>
		<?php
		printf(
			'<input type="text" class="regular-text" id="wpgetapi_' . $this->name . '_license_key" name="wpgetapi_' . $this->name . '_license_key" value="%s" />',
			esc_attr( $license )
		);
		$button = array(
			'name'  => 'edd_' . $this->name . '_license_deactivate',
			'label' => __( 'Deactivate License' ),
		);
		if ( 'valid' !== $status ) {
			$button = array(
				'name'  => 'edd_' . $this->name . '_license_activate',
				'label' => __( 'Activate License' ),
			);
		}
		wp_nonce_field( 'wpgetapi_' . $this->name . '_nonce', 'wpgetapi_' . $this->name . '_nonce' );
		?>
		<input type="submit" class="button-secondary" name="<?php echo esc_attr( $button['name'] ); ?>" value="<?php echo esc_attr( $button['label'] ); ?>"/>
		<?php
	}

	/**
	 * Registers the license key setting in the options table.
	 *
	 * @return void
	 */
	function register_option() {
		register_setting( 'wpgetapi_licenses_section', 'wpgetapi_' . $this->name . '_license_key', 'wpgetapi_' . $this->name . '_sanitize_license' );
	}
	

	/**
	 * Sanitizes the license key.
	 *
	 * @param string  $new The license key.
	 * @return string
	 */
	function sanitize_license( $new ) {
		$old = get_option( 'wpgetapi_' . $this->name . '_license_key' );
		if ( $old && $old !== $new ) {
			delete_option( 'wpgetapi_' . $this->name . '_license_status' ); // new license has been entered, so must reactivate
		}

		return sanitize_text_field( $new );
	}

	/**
	 * Activates the license key.
	 *
	 * @return void
	 */
	function activate_license() {

		// listen for our activate button to be clicked
		if ( ! isset( $_POST[ 'edd_' . $this->name . '_license_activate' ] ) ) {
			return;
		}

		// retrieve the license from the database
		$license = trim( get_option( 'wpgetapi_' . $this->name . '_license_key' ) );

		if ( ! $license ) {
			$license = ! empty( $_POST['wpgetapi_' . $this->name . '_license_key'] ) ? sanitize_text_field( $_POST['wpgetapi_' . $this->name . '_license_key'] ) : '';
		}
		if ( ! $license ) {
			return;
		}


		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'activate_license',
			'license'     => $license,
			'item_id'     => $this->item_id,
			'item_name'   => rawurlencode( $this->item_name ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			WPGETAPISTOREURL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

			// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

					case 'expired':
						$message = sprintf(
							/* translators: the license key expiration date */
							__( 'Your license key expired on %s.', 'edd-sample-plugin' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'edd-sample-plugin' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'edd-sample-plugin' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'edd-sample-plugin' );
						break;

					case 'item_name_mismatch':
						/* translators: the plugin name */
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'edd-sample-plugin' ), $this->item_name );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'edd-sample-plugin' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'edd-sample-plugin' );
						break;
				}
			}
		}

			// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$redirect = add_query_arg(
				array(
					'page'          => WPGETAPILICENSEPAGE,
					'sl_activation' => 'false',
					'message'       => rawurlencode( $message ),
				),
				admin_url( 'admin.php' )
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		if ( 'valid' === $license_data->license ) {
			update_option( 'wpgetapi_' . $this->name . '_license_key', $license );
		}
		update_option( 'wpgetapi_' . $this->name . '_license_status', $license_data->license );
		wp_safe_redirect( admin_url( 'admin.php?page=' . WPGETAPILICENSEPAGE ) );
		exit();
	}
	

	/**
	 * Deactivates the license key.
	 * This will decrease the site count.
	 *
	 * @return void
	 */
	function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['edd_' . $this->name . '_license_deactivate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'wpgetapi_' . $this->name . '_nonce', 'wpgetapi_' . $this->name . '_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( 'wpgetapi_' . $this->name . '_license_key' ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'  => 'deactivate_license',
				'license'     => $license,
				'item_id'     => $this->item_id,
				'item_name'   => rawurlencode( $this->item_name ), // the name of our product in EDD
				'url'         => home_url(),
				'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
			);

			// Call the custom API.
			$response = wp_remote_post(
				WPGETAPISTOREURL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

				$redirect = add_query_arg(
					array(
						'page'          => WPGETAPILICENSEPAGE,
						'sl_activation' => 'false',
						'message'       => rawurlencode( $message ),
					),
					admin_url( 'plugins.php' )
				);

				wp_safe_redirect( $redirect );
				exit();
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( 'deactivated' === $license_data->license ) {
				delete_option( 'wpgetapi_' . $this->name . '_license_status' );
			}

			wp_safe_redirect( admin_url( 'admin.php?page=' . WPGETAPILICENSEPAGE ) );
			exit();

		}
	}
	

	/**
	 * Checks if a license key is still valid.
	 * The updater does this for you, so this is only needed if you want
	 * to do somemthing custom.
	 *
	 * @return void
	 */
	function check_license() {

		$license = trim( get_option( 'wpgetapi_' . $this->name . '_license_key' ) );

		$api_params = array(
			'edd_action'  => 'check_license',
			'license'     => $license,
			'item_id'     => $this->item_id,
			'item_name'   => rawurlencode( $this->item_name ),
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			WPGETAPISTOREURL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 'valid' === $license_data->license ) {
			echo 'valid';
			exit;
			// this license is still valid
		} else {
			echo 'invalid';
			exit;
			// this license is no longer valid
		}
	}
	

}

return new Wpgetapi_Pro_License_Handler();

