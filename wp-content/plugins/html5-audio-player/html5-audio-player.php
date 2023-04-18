<?php 
/*
 * Plugin Name: Html5 Audio Player
 * Plugin URI:  http://audioplayerwp.com/
 * Description: You can easily integrate html5 audio player in your wordress website using this plugin.
 * Version: 2.1.12
 * Author: bPlugins LLC
 * Author URI: http://bplugins.com
 * License: GPLv3
 * Text Domain:  html5-audio-player
 * Domain Path:  /languages
 */

// load text domain
function h5ap_load_textdomain() {
    load_plugin_textdomain( 'html5-audio-player', false, dirname( __FILE__ ) . "/languages" );
}

add_action( "plugins_loaded", 'h5ap_load_textdomain' );

/*Some Set-up*/
define('H5AP_PLUGIN_DIR', plugin_dir_url(__FILE__) );
define('H5AP_PLUGIN_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() :'2.1.12' );
define('H5AP_VER', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() :'2.1.12' );
defined('H5AP_PATH') or define( 'H5AP_PATH', plugin_dir_path( __FILE__ ) );


if(!class_exists('H5AP')){
class H5AP{
	private $plugin ;
	function __construct(){
		$this->plugin = plugin_basename(__FILE__);
		add_action('plugins_loaded', [$this, 'plugins_loaded']);
		add_filter("plugin_action_links_$this->plugin", [$this, 'your_plugin_settings_link'] );
	}

	function plugins_loaded(){
		require_once(__DIR__.'/admin/framework/codestar-framework.php');
		require_once(__DIR__.'/admin/inc/metabox-free.php');
	}

	// Add settings link on plugin page
	function your_plugin_settings_link($links) {
		$settings_link = '<a href="#" class="h5ap_import_data">Import Data</a>';
		array_unshift($links, $settings_link); 
		return $links; 
	}

}

new H5AP();
}

include_once('blocks/init.php');
require_once(__DIR__.'/inc/Init.php');

