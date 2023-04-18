<?php
/*
Plugin Name: Accordions by PickPlugins - Pro
Plugin URI: https://www.pickplugins.com/item/accordions-html-css3-responsive-accordion-grid-for-wordpress/?ref=dashboard
Description: Fully responsive and mobile ready accordion grid for wordpress.
Version: 1.0.1
WC requires at least: 3.0.0
WC tested up to: 4.1
Author: PickPlugins
Author URI: http://pickplugins.com
Text Domain: accordions-pro
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class AccordionsPro{
	
	public function __construct(){

		define('accordions_pro_plugin_url', plugins_url('/', __FILE__)  );
		define('accordions_pro_plugin_dir', plugin_dir_path( __FILE__ ) );
        define('accordions_pro_plugin_name', 'Accordions pro' );
        define('accordions_pro_plugin_version', '1.0.1' );
        define('accordions_pro_plugin_basename', plugin_basename( __FILE__ ) );
        define('accordions_pro_server_url', 'https://www.pickplugins.com' );


        require_once( accordions_pro_plugin_dir . 'includes/functions-accordions-meta-hook.php');
        require_once( accordions_pro_plugin_dir . 'includes/functions-settings-hook.php');
        require_once( accordions_pro_plugin_dir . 'includes/class-license.php');
        require_once( accordions_pro_plugin_dir . 'includes/functions.php');
        require_once( accordions_pro_plugin_dir . 'includes/functions-accordions-hook.php');
        require_once( accordions_pro_plugin_dir . 'includes/class-admin-notices.php');


        add_action( 'wp_enqueue_scripts', array( $this, '_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
		
		add_action( 'plugins_loaded', array( $this, '_textdomain' ));

	}
	

	
	public function _textdomain() {

        $locale = apply_filters( 'plugin_locale', get_locale(), 'accordions-pro' );
        load_textdomain('accordions-pro', WP_LANG_DIR .'/accordions/accordions-'. $locale .'.mo' );

        load_plugin_textdomain( 'accordions-pro', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	
	public function accordions_install(){
		
		do_action( 'accordions_action_install' );
	}
		
	public function _uninstall(){
		
		do_action( 'accordions_action_uninstall' );
	}
		
	public function _deactivation(){
		
		do_action( 'accordions_action_deactivation' );
	}
	
	
	public function _front_scripts(){


	}

	public function _admin_scripts(){


	}




}

new AccordionsPro();
