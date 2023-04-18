<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.miniorange.com
 * @since      1.0.0
 *
 * @package    Custom_Api_For_Wordpress
 * @subpackage Custom_Api_For_Wordpress/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Custom_Api_For_Wordpress
 * @subpackage Custom_Api_For_Wordpress/includes
 * @author     miniOrange <info@xecurify.com>
 */
class Custom_Api_For_Wordpress_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'custom-api-for-wordpress',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
