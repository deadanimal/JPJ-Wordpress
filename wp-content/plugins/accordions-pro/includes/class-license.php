<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 



class class_accordions_pro_license{
	
	public function __construct(){


        add_action( 'init', array( $this, 'check_plugin_update' ), 12 );
				
	}


	public function check_plugin_update() {

        $accordions_settings = get_option( 'accordions_settings' );
		$license_key = isset($accordions_settings['license_key']) ? $accordions_settings['license_key'] : '';

        if(is_multisite()){
            $domain = site_url();
        }else{
            $domain = $_SERVER['SERVER_NAME'];
        }

		require_once ( 'class-wp-autoupdate.php' );

		$plugin_current_version = accordions_pro_plugin_version;
		$plugin_remote_path = accordions_pro_server_url;
		$plugin_slug = accordions_pro_plugin_basename;

		new WP_AutoUpdate ( $plugin_current_version, $plugin_remote_path, $plugin_slug, $license_key, $domain );
	}


	

	
	
	public function check_license_on_server($license_key){

	    $return_data = array();
        $domain = (is_multisite()) ? site_url() : $_SERVER['SERVER_NAME'];;
		
		// API query parameters
		$api_params = array(
			'license_manager_action' => '_activate',
			'license_key' => $license_key,
			'registered_domain' => $domain,
		);
	
		// Send query to the license manager server
		$response = wp_remote_get(add_query_arg($api_params, accordions_pro_server_url), array('timeout' => 20, 'sslverify' => false));
	
		// Check for error in the response
		if (is_wp_error($response)){

            $return_data['mgs'] = __("Unexpected Error! The query returned with an error.", 'accordions-pro');
		}
		else{

            $license_data = json_decode(wp_remote_retrieve_body($response));

			$license_key = isset($license_data->license_key) ? sanitize_text_field($license_data->license_key) : '';
			$date_created = isset($license_data->date_created) ? sanitize_text_field($license_data->date_created) : '';
			$date_expiry = isset($license_data->date_expiry) ? sanitize_text_field($license_data->date_expiry) : '';
			$license_status = isset($license_data->license_status) ? sanitize_text_field($license_data->license_status) : '';
			$license_found = isset($license_data->license_found) ? sanitize_text_field($license_data->license_found) : '';
			$mgs = isset($license_data->mgs) ? sanitize_text_field($license_data->mgs) : '';
			$days_remaining = isset($license_data->days_remaining) ? sanitize_text_field($license_data->days_remaining) : '';

            $return_data['date_created'] = $date_created;
            $return_data['date_expiry'] = $date_expiry;
            $return_data['license_status'] = $license_status;
            $return_data['license_found'] = $license_found;
            $return_data['mgs'] = $mgs;
            $return_data['days_remaining'] = $days_remaining;

		}


        return $return_data;

	}	

}

new class_accordions_pro_license();