<?php
/**
 * License Settings pAge
 */
class QCLD_str_License_Settings_page
{
	
	function __construct()
	{
		add_action( 'admin_init', array($this, 'register_license_key_settings') );
	}

	function register_license_key_settings(){
		register_setting( 'qcld_str_license', 'qcld_str_enter_license_key', array($this, 'qcld_callback_quantum_license_key') );
		register_setting( 'qcld_str_license', 'qcld_str_enter_envato_key', array($this, 'qcld_callback_envato_license_key') );
		register_setting( 'qcld_str_license', 'qcld_str_buy_from_where', array( 'default' => '' ) );
		register_setting( 'qcld_str_license', 'qcld_str_site_type', array( 'default' => '' ) );
		//start new-update-for-codecanyon
		register_setting( 'qcld_str_license', 'qcld_str_enter_license_or_purchase_key', array( 'default' => '' ) );
		//end new-update-for-codecanyon
	}

	function qcld_callback_quantum_license_key($posted_option){
		if(isset($_POST['submit'])){
			$license_key = sanitize_text_field($_POST['qcld_str_enter_license_key']);
			$buy_from = sanitize_text_field($_POST['qcld_str_buy_from_where']);
			$server_type = sanitize_text_field($_POST['qcld_str_site_type']);
			$plugin_name = str_LICENSING_PLUGIN_NAME;

			$request = wp_remote_get(
				str_LICENSING_PRODUCT_DEV_URL."wp-json/qcld-sumu-subscription-checker/v1/checkplugin/?license_key=".$license_key."&plugin_to_check=".$plugin_name, array('timeout' => 300)
			);
			$current_domain_url =  site_url() ;
			$current_domain = str_replace(
									array( 'https://', 'http://', 'www.' ),
									array('', '', ''),
									$current_domain_url
								);
			if( $buy_from == 'quantumcloud'){
				if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				
					$result = json_decode($request['body']);
					if( $result){
						$requested_plugin_name = $result->plugin_name;
						$plugin_max_domain = $result->plugin_max_domain;
						if($requested_plugin_name == $plugin_name){

							delete_str_update_transient();
							delete_str_renew_transient();

							delete_str_invalid_license();
							set_str_valid_license();
							$track_domain_request = wp_remote_get(str_LICENSING_PRODUCT_DEV_URL."wp-json/qc-domain-tracker/v1/insertdomain/?license_key=".$license_key."&plugin_name=".$plugin_name."&current_domain=".$current_domain."&plugin_max_domain=".$plugin_max_domain."&buy_from=".$buy_from.'&server_type='.$server_type, array('timeout' => 300));

							if( !is_wp_error( $track_domain_request ) || wp_remote_retrieve_response_code( $track_domain_request ) === 200 ){
								//error_log($track_domain_request['body']);
								//Success and nothing to do
							}else{
								//error_log('wrong');
							}
						}
					}else{
						set_str_invalid_license();
						delete_str_valid_license();
					}
				}else{
					//error_log( json_encode($request) );
				}
			}
		}
		return $posted_option;
	}

	function qcld_callback_envato_license_key($posted_option){
		if(isset($_POST['submit'])){
			$purchase_code = sanitize_text_field($_POST['qcld_str_enter_envato_key']);
			$buy_from = sanitize_text_field($_POST['qcld_str_buy_from_where']);
			$server_type = sanitize_text_field($_POST['qcld_str_site_type']);
			$plugin_name = str_LICENSING_PLUGIN_NAME;
			$current_domain_url =  site_url() ;
			$current_domain = str_replace(
									array( 'https://', 'http://', 'www.' ),
									array('', '', ''),
									$current_domain_url
								);

			if( $buy_from == 'codecanyon'){
				if (!preg_match("/^(\w{8})-((\w{4})-){3}(\w{12})$/", $purchase_code) ){
	   				//throw new Exception("Invalid code");
	   				set_str_invalid_license();
	   				delete_str_valid_license();
	   			}else{

					$verify_purchase_code = wp_remote_get(str_LICENSING_PRODUCT_DEV_URL."wp-json/qc-envato/v1/checklicense/?license_key=".$purchase_code);

					if ( !is_wp_error( $verify_purchase_code ) || wp_remote_retrieve_response_code( $verify_purchase_code ) === 200 ) {
				
						$verify_purchase_result = json_decode($verify_purchase_code['body'], true);
						$item_details = $verify_purchase_result['item'];

						if( $item_details['id'] == str_ENVATO_PLUGIN_ID ){
			   				$track_domain_request = wp_remote_get(str_LICENSING_PRODUCT_DEV_URL."wp-json/qc-domain-tracker/v1/insertdomain/?license_key=".$purchase_code."&plugin_name=".$plugin_name."&current_domain=".$current_domain."&plugin_max_domain=1&buy_from=".$buy_from.'&server_type='.$server_type);
			   				
			   				delete_str_update_transient();
							delete_str_renew_transient();

			   				delete_str_invalid_license();
			   				set_str_valid_license();
						}else{
							set_str_invalid_license();
	   						delete_str_valid_license();
						}

					}else{
						set_str_invalid_license();
	   					delete_str_valid_license();
					}

	   			}
	   		}
		}
		return $posted_option;
	}
}
