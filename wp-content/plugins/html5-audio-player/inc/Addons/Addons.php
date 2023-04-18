<?php
namespace H5AP\Addons;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

final class Addons {

	const VERSION = '1.0.0';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '7.0';
	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct(){

	}

	public function register() {
		//Register Frontend Script
		add_action( "elementor/frontend/after_register_scripts", [ $this, 'frontend_assets_scripts' ] );

		// Add Plugin actions
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );

	}


	/**
	 * Frontend script
	 */
	public function frontend_assets_scripts(){

		if(!wp_script_is('h5ap-public', 'registered')){
			wp_register_script( 'bplugins-plyrio', H5AP_PLUGIN_DIR . 'js/plyr-v3.7.2.js' , array(), H5AP_PLUGIN_VERSION );
			wp_register_style( 'bplugins-plyrio', H5AP_PLUGIN_DIR . 'style/player-style.css', array(), H5AP_PLUGIN_VERSION , 'all'  );
        	wp_register_script( 'h5ap-public', H5AP_PLUGIN_DIR . 'dist/public.js' , array('bplugins-plyrio'), H5AP_PLUGIN_VERSION  );
		}
	
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {
		// Include Widget files
		require_once( __DIR__ . '/Simple.php' );

		// Register widget
		\Elementor\Plugin::instance()->widgets_manager->register( new Simple() );
	}

}
