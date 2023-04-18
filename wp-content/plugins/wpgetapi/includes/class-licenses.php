<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/**
 * Wpgetapi_License_Handler Class
 */
class Wpgetapi_License_Handler {

	/**
     * Main constructor
     * @since 1.0.0
     */
    public function __construct() {
    	$this->hooks();
    }

    /**
     * Hooks
     * @since  1.0.0
     */
    public function hooks() {
    	add_action( 'admin_menu', array( $this, 'license_menu' ) );
    	add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

	/**
	 * Adds the plugin license page to the admin menu.
	 *
	 * @return void
	 */
	function license_menu() {
		if ( 
			is_plugin_active('wpgetapi-woocommerce/wpgetapi-woocommerce.php' ) || 
			is_plugin_active('wpgetapi-post-import/wpgetapi-post-import.php' ) || 
			is_plugin_active('wpgetapi-oauth/wpgetapi-oauth.php' ) || 
			is_plugin_active('wpgetapi-extras/wpgetapi-extras.php' ) 
		) {
			add_submenu_page(
				'wpgetapi_setup',
				__( 'Plugin Licenses' ),
				__( 'Licenses' ),
				'manage_options',
				WPGETAPILICENSEPAGE,
				array( $this, 'license_page' )
			);
		}

	}
	

	function license_page() {

		add_settings_section(
			'wpgetapi_licenses_section',
			__( '' ),
			array( $this, 'license_key_settings_section' ),
			WPGETAPILICENSEPAGE
		);

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WPGetAPI Extension Licenses' ); ?></h2>
			<form method="post" action="options.php">

				<?php
				do_settings_sections( WPGETAPILICENSEPAGE );
				settings_fields( 'wpgetapi_licenses_section' );
				submit_button();
				?>

			</form>
		<?php
	}


	/**
	 * Adds content to the settings section.
	 *
	 * @return void
	 */
	function license_key_settings_section() {

		?>
			<div class="intro">
				Enter your extension license keys here. This allows you to receive updates for your extensions as well as our premium support.
			</div>

		<?php

	}


	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	function admin_notices() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch ( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo wp_kses_post( $message ); ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					?>
					<div class="error">
						<p>Activated successfully.</p>
					</div>
					<?php
					// Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}
	


}

return new Wpgetapi_License_Handler();

