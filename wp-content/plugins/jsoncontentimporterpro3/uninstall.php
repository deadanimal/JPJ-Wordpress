<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

UNINSTALL_jci_pro_plugin_options();

function UNINSTALL_jci_pro_plugin_options() {
    global $wpdb;
    if (function_exists('is_multisite') && is_multisite()) {
      $blogIdCurrent = $wpdb->blogid;  // retrieve blogIds
      $blogIdArr = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
      foreach ($blogIdArr as $blogid) {
        switch_to_blog($blogid);
        UNINSTALL_jci_options_and_db();
      }
      switch_to_blog($blogIdCurrent);
      return;
    }
    UNINSTALL_jci_options_and_db();
}

function UNINSTALL_jci_options_and_db() {
  if (get_option('jci_pro_uninstall_deleteall')==1) {
    delete_option( "jci_pro_json_url" );
	  delete_option( "jci_pro_json_fileload_basepath" );
    delete_option( "jci_pro_enable_cache" );
    delete_option( "jci_pro_cache_time" );
    delete_option( "jci_pro_cache_time_format" );
    delete_option( "jci_pro_json_basenode" );
    delete_option( "jci_pro_json_urlgettimeout" );
    delete_option( "jci_pro_errormessage" );
    delete_option( "jci_pro_uninstall_deleteall" );

    delete_option( "jci_pro_allow_urlparam" );
    delete_option( "jci_pro_allow_urldirdyn" );
    delete_option( "jci_pro_allow_regexp" );
    delete_option( "jci_pro_allow_oauth_code" );
    delete_option( "jci_pro_http_header_accept" );
    delete_option( "jci_pro_http_header_useragent" );
  	delete_option( 'jci-pro-options', 'jci_pro_http_body' );
    delete_option( "jci_pro_delimiter" );
    delete_option( "jci_pro_use_wpautop" );
    delete_option( "jci_pro_debugmode" );
    
   	delete_option( "jci_pro_order_of_shortcodeeval" );
    delete_option( "jci_pro_custom_post_types" );

	  delete_option( "jci_pro_curl_optionlist" );
	  delete_option( "jci_pro_curl_usernamepassword" );
	  delete_option( "jci_pro_curl_authmethod" );

	  delete_option( "edd_jcipro_license_key" );
	  delete_option( "edd_jcipro_license_status" );
	  delete_option( "edd_jcipro_license_lifetime" );
	  delete_option( "edd_jcipro_license_lc" );
	  delete_option( "edd_jcipro_license_lv" );
	  delete_option( "edd_jcipro_license_errormsg" );
	  delete_option( "edd_jcipro_license_errormsgacdeac" );
	  
	  delete_option( "jci_pro_api_errorhandling" );
	  delete_option( "jci_pro_cp_fastdelete" );
	  delete_option( "jci_pro_api_access_items" );
	  
	  delete_option( "jci_pro_load_jqueryui" );
	  delete_option( "jci_pro_load_jqueryuicss" );
	  delete_option( "jci_pro_load_jqueryuitouchpunch" );
	  delete_option( "jci_pro_load_jquerymobilejs" );
	  delete_option( "jci_pro_load_jquerymobilecss" );
	  delete_option( "jci_pro_load_foundationfloatmincss" );
	  delete_option( "jci_pro_load_libs_pageids" );
	  delete_option( "jci_pro_use_nestedlevel" );
	  
	  delete_option( "jci_pro_selected_editor" );
	  delete_option( "jci_pro_hide_deprecated" );
	  
	  delete_option( "jci_pro_api_use_items" );
	  delete_option( "jci_pro_templatelistsettings" );
	  
    UNINSTALL_jci_database();
  }
}

function UNINSTALL_jci_database() {
  global $wpdb;
  $table_name = $wpdb->prefix.'plugin_jci_pro_templates';
  if ($wpdb->get_var( "SHOW TABLES LIKE '$table_name'") == $table_name) {
    $sql = "DROP TABLE IF EXISTS {$table_name}";
    $wpdb->query($sql);
  }
}
?>