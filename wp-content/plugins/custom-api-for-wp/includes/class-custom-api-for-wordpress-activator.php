<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.miniorange.com
 * @since      1.0.0
 *
 * @package    Custom_Api_For_Wordpress
 * @subpackage Custom_Api_For_Wordpress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Custom_Api_For_Wordpress
 * @subpackage Custom_Api_For_Wordpress/includes
 * @author     miniOrange <info@xecurify.com>
 */
class Custom_Api_For_Wordpress_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option('custom_api_authentication_new_customer','yes');
	}

}
