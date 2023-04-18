<?php
/**
 * Plugin Name: WPGetAPI Pro
 * Description: Pro plugin that extends WPGetAPI with enhancements and extra features.
 * Author: WPGetAPI
 * Author URI: https://wpgetapi.com/
 * Version: 2.1.0
 * Text Domain: 'wpgetapi-extras'
 * Domain Path: languages
 * Network: true
 * 
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 *
 * @since 1.0.0
 */
final class WpGetApi_Extras {

	/**
	 * @var The one true instance
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	public $version = '2.1.0';
	public $cache_key = 'wpgetapi_extras_update';

	/**
	 * Main Instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpgetapi-extras' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpgetapi-extras' ), '1.0.0' );
	}

	/**
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->define_constants();
		$this->includes();

		do_action( 'wpgetapi_extras_loaded' );

	}

	/**
	 * Define Constants.
	 * @since  1.0.0
	 */
	private function define_constants() {
		$this->define( 'WPGETAPIEXTRASFILE', __FILE__ );
		$this->define( 'WPGETAPIEXTRASDIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'WPGETAPIEXTRASURL',plugin_dir_url( __FILE__ ) );
		$this->define( 'WPGETAPIEXTRASSLUG', plugin_basename( __DIR__ ) );
		$this->define( 'WPGETAPIEXTRASBASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WPGETAPIEXTRASVERSION', $this->version );
	}


	/**
	 * Define constant if not already set.
	 * @since  1.0.0
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}


	/**
	 * Include required files.
	 * @since  1.0.0
	 */
	public function includes() {
		
		include_once ( WPGETAPIEXTRASDIR . 'includes/updater/class-license-handler.php' );

		include_once ( WPGETAPIEXTRASDIR . 'includes/class-extend.php' );
		include_once ( WPGETAPIEXTRASDIR . 'includes/class-tokens.php' );
		
	}


}


/**
 * Run the plugin.
 */
function WpGetApi_Extras() {
	return WpGetApi_Extras::instance();
}
add_action( 'wpgetapi_loaded', 'WpGetApi_Extras' );