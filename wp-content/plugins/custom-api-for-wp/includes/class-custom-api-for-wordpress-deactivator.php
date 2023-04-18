<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.miniorange.com
 * @since      1.0.0
 *
 * @package    Custom_Api_For_Wordpress
 * @subpackage Custom_Api_For_Wordpress/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Custom_Api_For_Wordpress
 * @subpackage Custom_Api_For_Wordpress/includes
 * @author     miniOrange <info@xecurify.com>
 */
class Custom_Api_For_Wordpress_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		
		delete_option( 'cutom_api_wp_host_name' );

		delete_option( 'custom_api_authentication_verify_customer' );
		delete_option( 'custom_api_authentication_admin_customer_key' );
		delete_option( 'custom_api_authentication_admin_api_key' );
		delete_option( 'custom_api_authentication_new_customer' );
		// delete_option('CUSTOM_API_WP_LIST');
		delete_option( 'custom_api_authentication_customer_token' );
	}

}
