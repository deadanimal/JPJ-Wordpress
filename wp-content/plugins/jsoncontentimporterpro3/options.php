<?php
global $wpdb;
add_action('admin_menu', 'register_jci_pro_create_menu');

function register_jci_pro_create_menu() {
	//create new top-level menu
	add_menu_page('JSON Content Importer Pro', 'JSON Content Importer Pro', 'administrator', 'unique_jcipro_menu_slug', 'jci_pro_settings_page',plugins_url('/images/jci-pro-loadcircle-16x16.png', __FILE__));
	add_submenu_page('unique_jcipro_menu_slug', 'Options', 'Options', 'administrator', 'unique_jcipro_menu_slug', 'register_jci_pro_settings');
	add_submenu_page('unique_jcipro_menu_slug', 'Way 1, Step 1: Get JSON', 'Way 1, Step 1: Get JSON', 'administrator', 'jciprostep1getjsonslug', 'register_jci_pro_step1getjson');
	add_submenu_page('unique_jcipro_menu_slug', 'Way 1, Step 2: Use JSON', 'Way 1, Step 2: Use JSON', 'administrator', 'jciprostep2usejsonslug', 'register_jci_pro_step2usejson');
	#add_submenu_page('unique_jcipro_menu_slug', 'Show stored APIs', 'Show stored APIs', 'administrator', 'jciproshowapis', 'register_jci_pro_showapis');
	
	$smh = add_submenu_page('unique_jcipro_menu_slug', 'Way 2: Template-Manager', 'Way 2: Template-Manager', 'administrator', 'jciprotemplateslug', 'register_jci_pro_templates');
	add_action( "load-$smh", 'add_options' );		
	if (isset($_POST["wp_screen_options"]["value"]) && (!empty($_POST["wp_screen_options"]["value"]))) {
		$tm_settings = json_decode(get_option("jci_pro_templatelistsettings"), TRUE);
		$tm_settings["per_page"] = $_POST["wp_screen_options"]["value"]; 
		update_option("jci_pro_templatelistsettings", json_encode($tm_settings) );
	}
	
	
	
	add_submenu_page('unique_jcipro_menu_slug', 'Way 2: Add Template', 'Way 2: Add Template', 'administrator', 'jciproaddtemplateslug', 'register_jci_pro_add_templates');
	add_submenu_page('unique_jcipro_menu_slug', 'JCI pro Licence', 'JCI pro Licence', 'administrator', 'jciprolicence', 'edd_jcipro_license_page');
	add_action( 'admin_init', 'register_jci_pro_settings' );//call register settings function
}

	# screen options for template-table
	function add_options() {
		$per_page = 10;
		$tm_settings = json_decode(get_option("jci_pro_templatelistsettings"), TRUE);
		if (!empty($tm_settings["per_page"])) {
			$per_page = $tm_settings["per_page"];
		}
		$args = array(
			'label' => 'Number of displayed JCI-Templates',
			'default' => $per_page,
			'option' => 'jcitemplates_per_page'
			);
		$option = 'per_page';
		add_screen_option( $option, $args );
	}	


/* options BEGIN */
function register_jci_pro_settings() {
	//register our settings
#	register_setting( 'jci-pro-options', 'jci_pro_json_url' );
	register_setting( 'jci-pro-options', 'jci_pro_json_fileload_basepath' );
	register_setting( 'jci-pro-options', 'jci_pro_php_timeout' );
	register_setting( 'jci-pro-options', 'jci_pro_enable_cache' );
	register_setting( 'jci-pro-options', 'jci_pro_enable_twigcache' );
	register_setting( 'jci-pro-options', 'jci_pro_cache_time' );
	register_setting( 'jci-pro-options', 'jci_pro_cache_time_format' );
	register_setting( 'jci-pro-options', 'jci_pro_cache_path' );
	register_setting( 'jci-pro-options', 'jci_pro_errormessage' );
	register_setting( 'jci-pro-options', 'jci_pro_uninstall_deleteall' );
	register_setting( 'jci-pro-options', 'jci_pro_allow_urlparam' );
	register_setting( 'jci-pro-options', 'jci_pro_allow_urldirdyn' );
	register_setting( 'jci-pro-options', 'jci_pro_allow_regexp' );
	register_setting( 'jci-pro-options', 'jci_pro_allow_oauth_code' );
	register_setting( 'jci-pro-options', 'jci_pro_http_header_accept' );
	register_setting( 'jci-pro-options', 'jci_pro_http_header_useragent' );
	register_setting( 'jci-pro-options', 'jci_pro_http_body' );
	register_setting( 'jci-pro-options', 'jci_pro_delimiter' );
	register_setting( 'jci-pro-options', 'jci_pro_use_wpautop' );
	register_setting( 'jci-pro-options', 'jci_pro_order_of_shortcodeeval' );
	register_setting( 'jci-pro-options', 'jci_pro_debugmode' );
	register_setting( 'jci-pro-options', 'jci_pro_custom_post_types' );

	register_setting( 'jci-pro-options', 'jci_pro_curl_optionlist' );
	register_setting( 'jci-pro-options', 'jci_pro_curl_usernamepassword' );
	register_setting( 'jci-pro-options', 'jci_pro_curl_authmethod' );

	register_setting( 'jci-pro-options', 'edd_jcipro_license_key' );
	register_setting( 'jci-pro-options', 'edd_jcipro_license_status' );
	register_setting( 'jci-pro-options', 'edd_jcipro_license_lifetime' );
	register_setting( 'jci-pro-options', 'edd_jcipro_license_lc' );
	register_setting( 'jci-pro-options', 'edd_jcipro_license_lv' );
	register_setting( 'jci-pro-options', 'edd_jcipro_license_errormsg' );
	register_setting( 'jci-pro-options', 'edd_jcipro_license_errormsgacdeac' );
	
	register_setting( 'jci-pro-options', 'jci_pro_api_errorhandling' );
	register_setting( 'jci-pro-options', 'jci_pro_cp_fastdelete' );
	register_setting( 'jci-pro-options', 'jci_pro_api_access_items' );
	register_setting( 'jci-pro-options', 'jci_pro_api_use_items' );
	register_setting( 'jci-pro-options', 'jci_pro_generating_set' );

	register_setting( 'jci-pro-options', 'jci_pro_load_jquery' );
	register_setting( 'jci-pro-options', 'jci_pro_load_jqueryui' );
	register_setting( 'jci-pro-options', 'jci_pro_load_jqueryuicss' );
	register_setting( 'jci-pro-options', 'jci_pro_load_jqueryuitouchpunch' );
	register_setting( 'jci-pro-options', 'jci_pro_load_jquerymobilejs' );
	register_setting( 'jci-pro-options', 'jci_pro_load_jquerymobilecss' );
	register_setting( 'jci-pro-options', 'jci_pro_load_foundationfloatmincss' );
	register_setting( 'jci-pro-options', 'jci_pro_load_libs_pageids' );
	register_setting( 'jci-pro-options', 'jci_pro_use_nestedlevel' );
	register_setting( 'jci-pro-options', 'jci_pro_selected_editor' ); 
	register_setting( 'jci-pro-options', 'jci_pro_hide_deprecated' ); 
	register_setting( 'jci-pro-options', 'jci_pro_templatelistsettings' ); 
}

function jci_pro_settings_page() {
  $errorLevelSaveOptions = jci_pro_save_settings(); # save new settings if needed
?>
<div class="wrap">
<h2>JSON Content Importer: Global Settings</h2>
  <?php
  global $pagenow;
  if ( $pagenow == 'admin.php' && $_GET['page'] == 'unique_jcipro_menu_slug' ){
    # define tabs for plugin-admin-menu
    $currenttab = 'syntax';
    if ( isset ( $_GET['tab'] ) ) {
      $currenttab = $_GET['tab'];
    }
    jci_pro_admin_tabs($currenttab);
  ?>

<form method="post" action="admin.php?page=unique_jcipro_menu_slug&tab=<?php echo $currenttab; ?>">
    <?php settings_fields( 'jci-pro-options' ); ?>
    <?php do_settings_sections( 'jci-pro-options' ); ?>

	<table class="widefat striped">
    <?php
      # save: failed, no changes or changes-saved?
      if ($errorLevelSaveOptions == -5 ) {
        echo '<tr><td bgcolor=red><b>Saving of URL-Timeout failed: must be a number</b></td></tr>';
      } else if ($errorLevelSaveOptions == -6) {  # there were changes
        echo '<tr><td bgcolor=red><b>Saving of Cachetime failed: must be a number</b></td></tr>';
      } else if ($errorLevelSaveOptions<0) {  # there were changes
        echo '<tr><td bgcolor=red><b>Saving failed, errorcode: '.$errorLevelSaveOptions.'</b></td></tr>';
      } else if ($errorLevelSaveOptions==2) {  # there were changes
         echo '<tr><td bgcolor=#ccff33><b>Saving successful: Changed values saved</b></td></tr>';
      } else if ($errorLevelSaveOptions==1) {
         echo '<tr><td bgcolor=#ccff33><b>Nothing changed, nothing saved</b></td></tr>';
      }
      wp_nonce_field( "jci-pro-set-page" );

		if (empty(get_option('jci_pro_hide_deprecated'))) {
			$jci_pro_hide_deprecated_value = "no";
		} else {
			$jci_pro_hide_deprecated_value = get_option('jci_pro_hide_deprecated');
		}
		
      switch ( $currenttab ){
        case 'settings' :
    ?>
    <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
        </td></tr>
<?php
  break;
  case 'header' :
?>
       <tr>
        <td>
           <h1>Retrieve JSON-data from API: Add data to header</h1>
		   The global settings here are used for all usages of the JCI-Plugin. All parameter can also be set individually in the JCI-Template or the Shortcode.
		   Set parameters here if you want to have it for all:
           <h2>Add OAuth-Key (leave blank if API works without it):</h2>Authorization: Bearer &lt;xxxxx-xxx-xxx-xxxx-xxxxxx&gt;<br>
          If "Basic TOKEN" is required, use "Basic TOKEN" (Bearer will not used in this case)
           <br>
           <?php
              $val_jci_pro_allow_oauth_code = get_option('jci_pro_allow_oauth_code');
           ?>
           <input type="text" name="jci_pro_allow_oauth_code" placeholder="OAuth Bearerkey: xxxxx-xxx-xxx-xxxx-xxxxxx" value="<?php echo $val_jci_pro_allow_oauth_code; ?>" size="100">
                  </td></tr><tr><td>
           <h2>Add Useragent (leave blank if API works without it):</h2>
           <br>
           Useragent: When surfing via Browser the Browser adds his Browser-Signature (=Useragent, e.g. "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0"), so requests can handled Browser specific.
           <br>
           <?php
              $val_jci_pro_http_header_useragent = get_option('jci_pro_http_header_useragent');
           ?>
           <input type="text" name="jci_pro_http_header_useragent" placeholder="Example: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0" value="<?php echo $val_jci_pro_http_header_useragent; ?>" size="100">
                    </td></tr><tr><td>
           <h2>Accept-Header:</h2>Some API-server need to know what kind of request should be done (leave blank if API works without it):<br>
           E.g. setting it to "application/json" the server answers with JSON. Or "application/xml" gives XML-data.
           <br>
           <?php
              $val_jci_pro_http_header_accept = get_option('jci_pro_http_header_accept');
           ?>
           <input type="text" name="jci_pro_http_header_accept" placeholder="JSON: application/json" value="<?php echo $val_jci_pro_http_header_accept; ?>" size="100">
                    </td></tr><tr><td>
           <h2>HTTP-Body:</h2>Some API-server, esp. with POST-requests, require some input in the HTTP-body which follows the HTTP-header.
           <br> E.g. some authentication-data. This is the place to set a HTTP-body for all Plugin-Requests.
	   <br>Overwritten by Shortcode-Parameter "postbody".
           <br>
           <?php
              $val_jci_pro_http_body = get_option_and_prepare_for_form('jci_pro_http_body');
           ?>
           <input type="text" name="jci_pro_http_body" placeholder="Whatever the API expects, e.g. some JSON" value="<?php echo $val_jci_pro_http_body; ?>" size="100">

        </td>
      </tr>
      <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
        </td></tr>

<?php
  break;
  case 'syntaxtwig' :
?>
        <tr>
        	<td>
          ....
          </td>
        </tr>

<?php
  break;
  case 'syntax' :
?>
        <tr><td>
            <h1>Initial help:</h1>
			<h2>Step 1: Check the JCI-Plugin settings</h2>
			<ul>
			<li>&bull; Tab "Check install": Is your server ok for the minimal requirements of the JCI-Plugin?</li>
			<li>&bull; Tab "Admin-Settings": Settings for the Backend</li>
			<li>&bull; Tab "Cache": Switch it on if you want to have less API-requests and work with locally stored JSON for some time or in case of errors</li>
			<li>&bull; Tab "Shortcode-Settings": Check the "General settings for the JCI-Shortcodes" and there esp. "Text of errormessage". All other settings are optional and helpful in special situations</li>
			<li>&bull; Advanced - Tabs "HTTP: Header, Body" and "CURL": Needed only if you want to set things globally. Usually you set those settings in a JCI-Template.</li>
			<li>&bull; Advanced - Tab "Custom Post Types": Needed if you want to create Custom Posts out of Custom Post Types and JSON</li>
			<li>&bull; Advanced - Tab "GDPR": Infos about your and your users privacy</li>
			<li>&bull; Advanced - Tab "Uninstall": For complete uninstalling the plugin activate the checkbox there</li>
			</ul>
			<h2>Step 2: Select the way: Either use JSON-Access- and JSON-Use-Sets. Or create a JCI-Template</h2>
			<ul>
			<li>&bull; Either use JSON-Access- and JSON-Use-Sets -&gt; Way 1</li>
			<li>&bull; JCI-Templates: Very flexible, but a bit tricky when the API expects special settings -&gt; Way 2</li>
			</li>
			</ul>
			<h2>Way 1: Create a JSON-Access-Set and a JSON-Use-Set</h2>
			<ul>
			<li>&bull; Build an API-request with URL and additional info: JSON-Access-Set</li>
			<li>&bull; Select parts of the JSON, get auto-created twig-sourcecode and see the result: JSON-Use-Set</li>
			<li>&bull; Use s JSON-Use-Set with an Shortcode or Gutenberg-Block</li>
			</li>
			</ul>

			<h2>Way 2: JCI-Template: Get JSON and use it</h2>
			Create a JCI-Template: 
			<ul>
			<li>&bull; Insert at "URL of Template" the URL to the API you want to use.</li>
			<li>&bull; Check the http-Method: The default Method is GET done by CURL. Switch to POST or PUT if the API expects that. Some APIs are sensitive: Msybe the CURL-/WP-/RAW-Way for GET / POST / PUT works.
			<li>&bull; Set the debugmode to "debugmode ON (display more debug-infos AND: test the following URL with it's settings like curloptions, timeout etc)".</li>
			<li>&bull; At "Curloptions" you can set the details for the http-Request depending on the Expectations if the API.</li>
			<li>&bull; Use "Timeout for URL-retrieving" if the API is slow.</li>
			<li>&bull; Store that and you get a Number for that Template.</li>
			<li>&bull; Try this on a Wordpress-Page with [jsoncontentimporterpro id=NO_OF_TEMPLATE]  (no [/jsoncontentimporterpro] !!):
			<br>
			Previewing this page should show you several debug info and the JSON received from the API. If not, check the method, Curloptions... (most of the time autentication is required, which can be done in many ways).
			</li>
			</ul>
			<strong>Use JSON:</strong>
			<ul>
			<li>&bull; Edit the JCI-Template at "Template-Text": You can use the JCI-Parser-Syntax used in <a href="https://wordpress.org/plugins/json-content-importer/" target="_blank">free JCI-Plugin</a>. 
			This is not recommended: The main advantage of the PRO plugin is the <a href="https://twig.symfony.com/doc/3.x/templates.html" target="_blank">twig-Templateengine</a>.</li>
			<li>&bull;The default twig-Code of a brandnew JCI-Template has "{{ _context | json_encode }}" which shows you all JSON we got from the API.</li>
			<li>&bull;	<a href="https://json-content-importer.com/support/twig-templateengine/" target="_blank">At https://json-content-importer.com/support/twig-templateengine/ you find more details on twig and JCI</a></li>
			<li>&bull; see some <a href="https://json-content-importer.com/support/videos-on-json-content-importer/" target="_blank">HowTo-Videos on Youtube</a></li>
            <li>&bull; <a href="http://api.json-content-importer.com/" target="_blank">Examples for using the twig-Templateengine</a> and <a href="https://twig.symfony.com/doc/3.x/templates.html" target="_blank">the twig-manual</a></li>
			</ul>
           </td>
        </tr>
<?php
  break;
  case 'examples' :
?>
       <tr>
        <td>
           <h1>Some help:</h1>
          <a href="https://json-content-importer.com/examples/very-basic-example/" target="_blank">see www.json-content-importer.com/examples/very-basic-example/</a>

        </td>
      </tr>


<?php
  break;
  case 'shortcodeatt' :
?>
        <tr><td>
		<h1>Global settings for the JSON Content Importer-Shortcode:</h1>
		By using the Shortcode [jsoncontentimporterpro ...] you evoke the JCI-Plugin.
		Here you find some settings, which might help in case of Problems:
		     </td>
      </tr>
	   <tr><td>
			<h2>Set PHP-timeout (<a href="https://www.php.net/manual/en/function.set-time-limit.php" target="_blank">done with set_time_limit</a>): </h2>
If the execution of a Shortcode takes longer than the default maximum execution time you can set the maximum time in second here (e. g. generating CPT): 
           <?php
			  $met = ini_get("max_execution_time");
              $val_jci_pro_php_timeout = get_option('jci_pro_php_timeout');
              if (!($val_jci_pro_php_timeout>0)) {
                $val_jci_pro_php_timeout = $met;
              }
           ?>
<br>        <input type="text" name="jci_pro_php_timeout" value="<?php echo $val_jci_pro_php_timeout; ?>" size="5"> seconds (on this Server the default PHP-timeout is <?php echo $met; ?> seconds)
        </td>
      </tr>
       <tr>
        <td>
           <h2>Usage of Shortcodes (JCI or other) in the JCI-twig-Template:</h2>
           You can place Shortcodes of other plugins or another JCI-Shortcode in the JCI-template. In this way you can use the JSON-data with plugins like <a href="https://wordpress.org/plugins/tablepress/" target="_blank">TablePress</a> or <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a>. 
		   <br>
           <?php
              $val_jci_pro_order_of_shortcodeeval = get_option('jci_pro_order_of_shortcodeeval');
              if ($val_jci_pro_order_of_shortcodeeval=="") {
                $val_jci_pro_order_of_shortcodeeval = 1;
              }
          ?>
           <input type="radio" name="jci_pro_order_of_shortcodeeval" value="1" <?php echo ($val_jci_pro_order_of_shortcodeeval == 1)?"checked=checked":""; ?> /> First option (default) is to evaluate the twig-code in the JSONContentImporter-Shortcode and execute after that remaining Shortcodes in the template.
           <br>
		   Use this default setting if you want to execute a Shortcode by the twig-extension "doshortcode" () {{ [shortcode..] | doshortcode }}
		   <p>
           <input type="radio" name="jci_pro_order_of_shortcodeeval" value="2" <?php echo ($val_jci_pro_order_of_shortcodeeval == 2)?"checked=checked":""; ?> /> Or the other way round: Executing Shortcodes in the template first and then evaluate the twig-code: use this for TablePress, Contact Form 7 etc.
<br>Example: If you want to fill JSON-data into a "Contact Form 7"-Form, you have to evaluate the "Contact Form 7" Shortcode [contact-form-7 id="..." title="..."] first. And you have to place the JSON-placeholders into the "Contact Form 7"-Template! Eval the [contact-form-7 id="..." title="..."] gives you the HTML of that with the JSON-placeholders: Those are replaced by eval the JSONContentImporter-Shortcode. As this is not the default way, you have to set this here!<br>
        </td>
      </tr>
       <tr>
        <td>
		<h1>Nested JCI-Shortcodes: Use child-settings in parent-shortcode?</h1>
         <?php
              $val_jci_pro_use_nestedlevel = get_option('jci_pro_use_nestedlevel');
              if ($val_jci_pro_use_nestedlevel=="") {
                $val_jci_pro_use_nestedlevel = 1;
              }
          ?>
		  This setting is importaint in this situation: You have a JCI-shortcode (parent) with a twig-template. In this twig-template is another, different JCI-Shortcode (child, e. g. load data from a 2nd source etc.).
			When executing the plugin it starts with executing the parent-shortcode and sets plugin-parameters to the parent-values. 
			Next the child-shortcode is executed and the overwrites the plugin-settings with the child-values. 
			After finishing the child-shortcode the plugin returns to the parent-shortcode but with the child-settings.
			This can be ok but not in all situations.
           <br>
		   <input type="radio" name="jci_pro_use_nestedlevel" value="1" <?php echo ($val_jci_pro_use_nestedlevel == 1)?"checked=checked":""; ?> /> Default: use parent-shortcode with child-settings
		   
		   <br>
           <input type="radio" name="jci_pro_use_nestedlevel" value="2" <?php echo ($val_jci_pro_use_nestedlevel == 2)?"checked=checked":""; ?> /> Separate parent- and child-settings
		   
	   
        </td>
      </tr>
       <tr>
        <td>
		<h2>General settings for the JCI-Shortcodes:</h2>
           <b>Text of errormessage, displayed e.g. if the JSON-API is unavailable</b>
				You can use HTML<br> 
		   <?php
              $errormessage = get_option('jci_pro_errormessage');
              if ($errormessage=="") {
                $errormessage = ""; #sorry - data is unavailabe, try again later, please.";
              }
			  $errormessage = stripslashes($errormessage);
           ?>
           <input type="text" name="jci_pro_errormessage" placeholder="define custom errormessage here: Ordinary text or HTML..." value="<?php echo esc_html($errormessage); ?>" size="100">
       <p>&nbsp;<br>
           <b>Load JSON-data from server filesystem:</b>
           You can access JSON-datafiles via server-filesystem (and not via URL): Then you have to set the two Shortcode-parameter "feedsource=file" and "feedfilename=DIR/NAME_OF_FILE".
          <br> Where "DIR" is a one or more directories and "NAME_OF_FILE" the filename.
           The following option sets the base path to the file, hence the plugin tires to get "OPTIONVALUE/DIR/NAME_OF_FILE" ("../" and such in DIR is filtered!)<br>
            <?php
              $val_jci_pro_json_fileload_basepath = get_option('jci_pro_json_fileload_basepath');
              if ($val_jci_pro_json_fileload_basepath=="") {
                $val_jci_pro_json_fileload_basepath = WP_CONTENT_DIR;
                if (!preg_match("/\/$/", $val_jci_pro_json_fileload_basepath)) {
                  $val_jci_pro_json_fileload_basepath .= "/";
                }
              }
			  $val_jci_pro_json_fileload_basepath = stripslashes($val_jci_pro_json_fileload_basepath); 
           ?>
           <input type="text" name="jci_pro_json_fileload_basepath" placeholder="base directory where JSON-files are stored" value="<?php echo esc_html($val_jci_pro_json_fileload_basepath); ?>" size="100">
       <p>&nbsp;<br>
     <b>Global switch debug mode off / on:</b><br>
           In case of problems, the debug mode gives you some hints what's going wrong.
           <br>
           If switched on, use the shortcode and check the created page. On the page some debug-messages will occur (if not, see html-sourcecode - sometimes css-themes overlap...)
           <br>
           <?php
              $val_jci_pro_debugmode = get_option('jci_pro_debugmode');
              if ($val_jci_pro_debugmode=="") {
                $val_jci_pro_debugmode = 1;
              }
          ?>
           <input type="radio" name="jci_pro_debugmode" value="1" <?php echo ($val_jci_pro_debugmode == 1)?"checked=checked":""; ?> /> debugmode off
           <br>
           <input type="radio" name="jci_pro_debugmode" value="2" <?php echo ($val_jci_pro_debugmode == 2)?"checked=checked":""; ?> /> debugmode ON (display some debug-infos)
           <br>
           <input type="radio" name="jci_pro_debugmode" value="10" <?php echo ($val_jci_pro_debugmode == 10)?"checked=checked":""; ?> /> debugmode ON (display more debug-infos)       </td>
      </tr>

       <tr>
        <td>
           <h2>Settings when not using the JCI-template (you should use it!)</h2>
		   <b>Use wpautop or not:</b><br>
           The single- or double-linefeeds of the text between [jsoncontentimporterpro] and [/jsoncontentimporterpro] can be handled in different ways:
           <br>Converted into HTML-linefeeds or ignored.
           <br>
           If you have trouble with linefeeds, try using <a href="https://codex.wordpress.org/Function_Reference/wpautop" target="_blank">"wpautop"</a> by switching the following radio-button to "use wpautop".
           <br>
           <?php
              $val_jci_pro_use_wpautop = get_option('jci_pro_use_wpautop');
              if ($val_jci_pro_use_wpautop=="") {
                $val_jci_pro_use_wpautop = 2;
              }
          ?>
           <input type="radio" name="jci_pro_use_wpautop" value="1" <?php echo ($val_jci_pro_use_wpautop == 1)?"checked=checked":""; ?> /> use wpautop
           <br>
           <input type="radio" name="jci_pro_use_wpautop" value="2" <?php echo ($val_jci_pro_use_wpautop == 2)?"checked=checked":""; ?> /> do NOT use wpautop (default)
           <br>
           <input type="radio" name="jci_pro_use_wpautop" value="3" <?php echo ($val_jci_pro_use_wpautop == 3)?"checked=checked":""; ?> /> remove wpautop ("do NOT use wpautop" does not work in all situations)

		<?PHP
			$jci_pro_hide_deprecated_value = get_option('jci_pro_hide_deprecated');
			if ($jci_pro_hide_deprecated_value=="no") {
		?>
			<p>&nbsp;<br>
			<b>Shortcode-Attributes "filterresultsin" or/and "filterresultsnotin" (better way: the twig-Templateengine can filter too...)</b><br>
           With the <b>Shortcode-Attributes "filterresultsin" or/and "filterresultsnotin"</b> you can define parameter which <b>filter the JSON-data</b>.
           E.g. "...page/?f=3" would be made useful by "filterresultsin=f". Then the JSON-data is filtered for value "3" of field "f".
           <br><b>By default the matching of the fields value is done by a regular expression.</b> This means: "3" would match "33", "136" etc..
           If you really want "3" you have to use <b>^3$"</b> as this is the regular expression for that.
           <br>Or, you can switch the following radio-button to off, for using exact match instead of regular-expression-match:
           <br>
           <?php
            $val_jci_pro_allow_regexp = get_option('jci_pro_allow_regexp');
            if ($val_jci_pro_allow_regexp=="") {
              $val_jci_pro_allow_regexp = 2;
            }
           ?>
           <input type="radio" name="jci_pro_allow_regexp" value="1" <?php echo ($val_jci_pro_allow_regexp == 1)?"checked=checked":""; ?> /> use exact match, no regular-expression-match
           <br>
           <input type="radio" name="jci_pro_allow_regexp" value="2" <?php echo ($val_jci_pro_allow_regexp == 2)?"checked=checked":""; ?> /> use regular-expression-match, no exact match
  	    <p>&nbsp;<br>
           <b>Allow Shortcode-Parameter "urlparam", "pathparam", "fileext" (better way: Use "urlparam4twig" in the JCI-Template:</b>
           <br>
            The URL of the JSON-Feed is defined either by the Shortcode-Parameter "url" or by together with the Template at the Plugin-settings.
            By default the "url" is static and always the same. But sometimes it should be dynamic: When calling the page GET-parameter should be passed into the plugin for setting
            the URL.
            <p>
            E.g. a Wordpress-page like "http://...displayDomain/example.php?test=5&exa=7" should use a JSON-Feed like "http://...JSONdomain/data.json?test=5&exa=7".
            Without "urlparam" you can define the Shortcode like this: [jsoncontentimporterpro url="http://...JSONdomain/data.json"], there is no way to pass the value of "test" to the JSON-Templateengine.
            <p>
            By "urlparam" you can do this. [jsoncontentimporterpro url="http://...JSONdomain/data.json" urlparam="test#exa"] will put together the URL of the JSON-Feed out of "url" and the "#" separated parameter.
            <p>
            <b>Attention:</b><br>By using "urlparam" <b>anyone</b> can pass any value via browser to "http://...JSONdomain/...". Although the values are sanitized this is a bit of risk you should be aware of.
            You should know, how "http://...JSONdomain/..." reacts when someone evil tries to manipulate the URL!
            Hence this feature is switched off by default.
            <p><b>By switching it on you should know what you do!</b>
           <p>
           <b>"urlparam" is</b>
           <br>
           <?php
            $val_gjci_pro_allow_urlparam = get_option('jci_pro_allow_urlparam');
            if ($val_gjci_pro_allow_urlparam=="") {
              $val_gjci_pro_allow_urlparam = 1;
            }
           ?>
           <input type="radio" name="jci_pro_allow_urlparam" value="1" <?php echo ($val_gjci_pro_allow_urlparam == 1)?"checked=checked":""; ?> /> off
           <br>
           <input type="radio" name="jci_pro_allow_urlparam" value="2" <?php echo ($val_gjci_pro_allow_urlparam == 2)?"checked=checked":""; ?> /> on
  	    <p>&nbsp;<br>
           <b>"pathparam", "fileext":</b>
           <br>
            If the URL itself should be dynamic, the Shortcode-Parameter "pathparam" and "fileext" can be used: With that you can define what Input-GET-Value is used to put together the JSON-Feedurl.
            <br>
            E.g. Wordpress-page like "http://...displayDomain/example.php?dir1=a&dir2=b" should use a JSON-Feed like "http://...JSONdomain/a/b.php".
            Then you have to set the two Shortcode-Parameter as following:
            <br>pathparam="dir1#dir2" and fileext="php"
            <p>
            <b>Attention:</b><br>
            Whereas "fileext" is not dynamic and fixed in the Shortcode, "pathparam" is dynamic: <b>anyone</b> can pass any value via browser to "http://...JSONdomain/...".
            Although the value of "pathparam" is sanitized this adds some risk you should be aware of. You should know, how "http://...JSONdomain/..." reacts when someone evil tries to manipulate the URL!
            Hence this feature is switched off by default.
            <p><b>By switching it on you should know what you do! I exclude any liability!</b>
           <p>
           <b>"pathparam", "fileext" are</b>
           <br>
           <?php
            $val_jci_pro_allow_urldirdyn = get_option('jci_pro_allow_urldirdyn');
            if ($val_jci_pro_allow_urldirdyn=="") {
              $val_jci_pro_allow_urldirdyn = 1;
            }
           ?>
           <input type="radio" name="jci_pro_allow_urldirdyn" value="1" <?php echo ($val_jci_pro_allow_urldirdyn == 1)?"checked=checked":""; ?> /> off
           <br>
           <input type="radio" name="jci_pro_allow_urldirdyn" value="2" <?php echo ($val_jci_pro_allow_urldirdyn == 2)?"checked=checked":""; ?> /> on
		   <?PHP
			} else {
				echo '<input type="hidden" name="jci_pro_allow_regexp" value="1" />';
				echo '<input type="hidden" name="jci_pro_allow_urlparam" value="1" />';
				echo '<input type="hidden" name="jci_pro_allow_urldirdyn" value="1" />';
			}
		   ?>
		   
		   
                     </td></tr>
		<?PHP
			if ($jci_pro_hide_deprecated_value=="no") {
		?>
					 <tr><td>
<h2>Settings when not using the twig-Templateengine (don't do that with the PRO-Plugin!)</h2>
If you still use JCI-Templates from the free plugin (you have the PRO-plugin, switch to twig - it's way better and the main reason for buying thre PRO pluign):
<br>
           <b>Delimiter:</b><br>
           JSON-value manipulators like "ifNotEmptyAddLeftRight" or the "jcix-syntax for unnamed arrays" require an delimiter (e.g. {KEY:ifNotEmptyAddLeftRight:left##right##no##})<br>
           If another delimiter instead of "##" should be used (if "##" is part of left or right...), set another delimiter here and use it in the template:
           <br>
           <?php
              $val_jci_pro_delimiter = get_option('jci_pro_delimiter');
              if (empty($val_jci_pro_delimiter)) {
                $val_jci_pro_delimiter = "##";
              }
           ?>
<br>        <input type="text" name="jci_pro_delimiter" value="<?php echo $val_jci_pro_delimiter; ?>" size="5">
        </td>
      </tr>
		<?PHP
			}
		?>
	  
      <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
        </td></tr>

<?php
  break;
  case 'asettings' :
?>
    <tr><td>
         <h1>Here are the setting for your backend</h1>
	</td></tr>
	<tr><td>
		<?PHP
			if (empty(get_option('jci_pro_selected_editor'))) {
				$jciproseled = "puretext";  #"ace";  # set default
			} else {
				$jciproseled = get_option('jci_pro_selected_editor');
			}
		?>
        <h2>Editor for the Template in the JCI-Template:</h2>
            <input type="radio" name="jci_pro_selected_editor" value="puretext" <?php echo ($jciproseled == "puretext")?"checked=checked":""; ?> /> pure Texteditor (default)<br>
            <input type="radio" name="jci_pro_selected_editor" value="ace" <?php echo ($jciproseled == "ace")?"checked=checked":""; ?> /> <a href="https://ace.c9.io/" target="_blank">Ace-Editor</a><br>
	</td></tr>
<!--
	<tr><td>
        <h2>Hide deprecated and some global options:</h2>
            <input type="radio" name="jci_pro_hide_deprecated" value="yes" <?php echo ($jci_pro_hide_deprecated_value == "yes")?"checked=checked":""; ?> /> yes<br>
            <input type="radio" name="jci_pro_hide_deprecated" value="no" <?php echo ($jci_pro_hide_deprecated_value == "no")?"checked=checked":""; ?> /> no<p>
			In Detail:
			<ul>
				<li>Reduce JCI-Template: hide old twig-Versions, hide post-paypload fields - use curloptions here</li>
				<li>Hide Tabs "HTTP: Header, Body" and "CURL": There you can define global settings for that. Usually you should set this at each JCI-Template</li>
			</ul>
	</td></tr>
-->	
	<tr><td>
		<input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
		<input type="submit" name="Submit"  class="button-primary" value="Update Admin-Settings" />
    </td></tr>

<?PHP
  break;
  case 'curlsettings' :
?>
           <tr>
        <td>
           <h1>Global settings if you use the Shortcode-Attribute "method=curlget" or selected CURL-GET in the JCI-Template:</h1>
		   The global settings here are used for all usages of the JCI-Plugin. All parameter can also be set individually in the JCI-Template or the Shortcode.
		   Set parameters here if you want to have it for all:
           <h2>Username and Password used for <a href="http://php.net/manual/en/function.curl-setopt.php" target="_blank">CURLOPT_USERPWD</a>, separated by ":"</h2>
           Set some CURL-Options here, leave empty if authentication is not needed!
           <?php
              $curlusernamepassword = get_option('jci_pro_curl_usernamepassword');
           ?>
           <input type="text" name="jci_pro_curl_usernamepassword" placeholder="USERNAME:PASSWORD" value="<?php echo $curlusernamepassword; ?>" size="80">
          </td></tr><tr><td>
          <h2>HTTP authentication method(s) to use, see <a href="http://php.net/manual/en/function.curl-setopt.php" target="_blank">CURLOPT_HTTPAUTH</a></h2>
            Valid strings are: CURLAUTH_BASIC, CURLAUTH_DIGEST, CURLAUTH_GSSNEGOTIATE, CURLAUTH_NTLM, CURLAUTH_ANY or CURLAUTH_ANYSAFE.
           <br>
           <?php
              $curlauthmethod = get_option('jci_pro_curl_authmethod');
           ?>
           <input type="text" name="jci_pro_curl_authmethod" placeholder="CURLAUTH_BASIC, CURLAUTH_DIGEST, CURLAUTH_GSSNEGOTIATE, CURLAUTH_NTLM, CURLAUTH_ANY or CURLAUTH_ANYSAFE" value="<?php echo $curlauthmethod; ?>" size="80">
             </td></tr><tr><td>
          <h2>Set <a href="http://php.net/manual/en/function.curl-setopt.php" target=_blank>CURL-Options as you like:</a></h2>
           Syntax: OPTIONNAME1=OPTIONVALUE1;OPTIONNAME2=OPTIONVALUE2 where "true" is 1 and "false" is 0.
           <br>
           Example: "CURLOPT_SSL_VERIFYPEER=0;CURLOPT_SSL_VERIFYHOST=0" switches off https-verification (in case the https-verification fails due to missing certificates)
           <br>
           <?php
              $curloptionlist = get_option('jci_pro_curl_optionlist');
           ?>
           <input type="text" name="jci_pro_curl_optionlist" placeholder="OPTIONNAME1=OPTIONVALUE1;OPTIONNAME2=OPTIONVALUE2" value="<?php echo $curloptionlist; ?>" size="80">
             </td></tr><tr><td>
           more http/curl-settings: <br>
           HTTP-Timeout: Set Shortcode-Attribute "urlgettimeout=NO_OF_SECONDS"<br>
           User-Agents: Set value at Plugin-Option Tab "HTTP: Header, Body"
        </td>
      </tr>
      <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Update CURL-Settings" />
        </td></tr>
<?php
  break;
  case 'cacher' :
?>
           <tr><td>
           <h1>Store the API-answer on the Server: This speeds up the execution time and limits the http-API-requests</h1>
		   If the JCI-cacher is switched off, every time a Shortcode is executed the API is requested by http.<br>With active JCI-caching you can store the API-answer on the server and use it for speeding up or as fallback when the API is not available.
		   <br>Set the caching time depending on how often the data changes. Therefore: Not recommended for realtime data like a live-ticker. 
           </td></tr>
      <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Store and update the following Settings" />
        </td></tr>
		   <tr><td>
        <h2>JCI-Cacher: Active or not?</h2>
            Enable JCI-Cache: <input type="checkbox" name="jci_pro_enable_cache" value="1" <?php echo (get_option('jci_pro_enable_cache') == 1)?"checked=checked":""; ?> />
			<?PHP
				$jciprocachetime = get_option('jci_pro_cache_time');
				if (empty($jciprocachetime)) {
					$jciprocachetime = 1;
				}
			?>
        	 &nbsp;&nbsp;&nbsp; reload JSON from Web-API, if cachefile is older than <input type="text" name="jci_pro_cache_time" size="2" value="<?php echo $jciprocachetime; ?>" />
           <select name="jci_pro_cache_time_format">
           			<option value="minutes" <?php echo (get_option('jci_pro_cache_time_format') == 'minutes')?"selected=selected":""; ?>>Minutes</option>
                    <option value="days" <?php echo (get_option('jci_pro_cache_time_format') == 'days')?"selected=selected":""; ?>>Days</option>
                    <option value="month" <?php echo (get_option('jci_pro_cache_time_format') == 'month')?"selected=selected":""; ?>>Months</option>
                    <option value="year" <?php echo (get_option('jci_pro_cache_time_format') == 'year')?"selected=selected":""; ?>>Years</option>
           </select>
           <br>
			<?php
				$isTwigChacheEnabled = (get_option('jci_pro_enable_twigcache') == 1);
			?>
            Enable twig-Cache (speed up the execution of the template engine): <input type="checkbox" name="jci_pro_enable_twigcache" value="1" <?php echo ($isTwigChacheEnabled)?"checked=checked":""; ?> />
			 </td></tr>
		   <tr><td>
        <h2>JCI-Cacher: Where to store the data?</h2>
			<?php
				$defaultcachepath = WP_CONTENT_DIR . "/cache/jsoncontentimporterpro/";
				$cachepath = get_option('jci_pro_cache_path');
				if (empty($cachepath)) {
					$cachepath = $defaultcachepath;
					update_option('jci_pro_cache_path', $cachepath);
				}
				
				$cachepath = stripslashes($cachepath);
				echo 'Used Path for Cachefiles <br><input type="text" placeholder="leave empty to use defaultpath: '.$defaultcachepath.'" name="jci_pro_cache_path" value="'.esc_html($cachepath).'" size="200" />';
				echo '<table>';
				echo "<tr><td>Check the above Path:<td><td>";
				
				require_once plugin_dir_path( __FILE__ ) . '/lib/cache.php';
				$jci_Cache = new jci_Cache();
				if ($jci_Cache->get_open_basedir_error()) {
					$cacheFolderOptions = get_option('jci_pro_cache_path');
					echo '<span style="color:#f00;">The cacheFolder '.$cacheFolderOptions.' can\'t be used!<br>';
					echo 'The Webserver-Settings do allow only dir\'s above the open_basedir-dir: '.($jci_Cache->get_open_basedir()).'<br>';
					echo 'Change the cacheFolder to a dir matching these requirements!';
					echo '</span>';
				} else {
					if (!preg_match("/\/$/", $cachepath)) {
						$cachepath .= "/";
					}
					if ($isTwigChacheEnabled) {
						$cachepath .= 'twigcache';
					}
					if (is_dir($cachepath) && is_writeable($cachepath)) {
						echo "<font color=green>ok and writable</font>";
					} else {
						$mkdirError = @mkdir($cachepath, 0777 , TRUE);
						if ($mkdirError) {
							echo "<font color=green>created: $cachepath</font>";
						} else {
							echo "<font color=red>FAILED to create $cachepath - caching will not work with this path</font>";
						}
					}
				}
				echo '</td></tr>';
				echo '<tr><td>Default Path:<td><td>'.$defaultcachepath.'</td></tr>';
				$plugincachepath = plugin_dir_path(__FILE__) . "cache/";
				echo '<tr><td>An alternative is the Plugin Path:<td><td>'.$plugincachepath.'</td></tr>';
				echo '</table>';
			?>
<?php
			echo "</td></tr>";
			echo "<tr><td>";
			require_once plugin_dir_path( __FILE__ ) . '/lib/cache.php';
			$jci_Cache = new jci_Cache();
		
			$cacheFolder = $jci_Cache->getCacheFolder();
			
			$delmsg = "";
			if ( isset ( $_GET['clearcache'] ) && $_GET['clearcache']=="y") {
				$dcwpn = wp_verify_nonce( $_REQUEST['_wpnonce'], 'jcipro_clearcache' );
				if (!$dcwpn) {
					$delmsg = "<font color=red>deleting of cache failed because security check failed</font><br>";
				} else {
					$delmsg = $jci_Cache->clearCacheFolder();
				}
			}
		
			$filecount = 0;
			$files = glob($cacheFolder . "*.cgi");
			//var_Dump($files);
			if ($files){
				$filecount = count($files);
			}
			echo "<h2>JCI-Cacher: What is already in the Cache?</h2>";
			echo "Number of JSON-Cachefiles in $cacheFolder: $filecount<br>";

			if (class_exists('RecursiveDirectoryIterator')) { 
				if (is_dir($cacheFolder)) {
					$ret = $jci_Cache->get_dir_size($cacheFolder);
					$sizecachedir = $ret["size"];
					$cacheTwigCacheFolder = $cacheFolder.'/twigcache';
					$twigcache = "";
					$sizetwigcachedir = 0;
					$rettwigcache = array();
					$rettwigcache["nooffiles"] = 0;
					if (is_dir($cacheTwigCacheFolder)) {
						$rettwigcache = $jci_Cache->get_dir_size($cacheTwigCacheFolder);
						$sizetwigcachedir = $rettwigcache["size"];
						$twigcache = "Size of twig-Cache: ".$jci_Cache->format_dir_size($sizetwigcachedir)." in ".$rettwigcache["nooffiles"]." Files<br>";
					}
					echo "Total size of Cache: ".$jci_Cache->format_dir_size($sizecachedir)." in ".$ret["nooffiles"]." Files<br>";
					echo "Size of JSON-Cache: ".$jci_Cache->format_dir_size($sizecachedir-$sizetwigcachedir)." in ".($ret["nooffiles"]-$rettwigcache["nooffiles"])." Files<br>";
					echo $twigcache;
				}
			} else {
				echo "Calc of Cachefolder-Size failed due to missing PHP-Class (PHP7 or higher required)";
			}

			echo "</td></tr>";
?>
       <tr>
        	<td colspan="2">
          <h2>Use the cached JSON in case an API is unavailable:</h2> 
		  Sometimes an API is not available and a request fails. If there is a locally cached API-answer, this can be used, even if the cachetime has expired.
		  With the following settings you can define how this should be:
		  <br>
		  <input type="radio" name="jci_pro_api_errorhandling" value="0" <?php echo (get_option('jci_pro_api_errorhandling') == 0)?"checked=checked":""; ?> />
		  do not try to use cached JSON<br>
		  <input type="radio" name="jci_pro_api_errorhandling" value="1" <?php echo (get_option('jci_pro_api_errorhandling') == 1)?"checked=checked":""; ?> />
		  If the API-http-answercode is not 200: try to use cached JSON (do not use this, if your API sends valid data but an http-answercode other than 200)<br>
		  <input type="radio" name="jci_pro_api_errorhandling" value="2" <?php echo (get_option('jci_pro_api_errorhandling') == 2)?"checked=checked":""; ?> />
		  If the API sends invalid JSON: try to use cached JSON<br>
		  <input type="radio" name="jci_pro_api_errorhandling" value="3" <?php echo (get_option('jci_pro_api_errorhandling') == 3)?"checked=checked":""; ?> />
		  <font color=green>Recommended</font> (not switched on due to backwards-compatibility) - If the API-http-answercode is not 200 OR sends invalid JSON: try to use cached JSON<br>
            </td>
        </tr>

<?php	
		echo '<tr><td bgcolor="yellow">';
		echo "<h1>JCI-Cacher: Empty the Cache!</h1>";
		echo "<h2>$delmsg</h2>";
        $clearCacheUrl = "admin.php?page=unique_jcipro_menu_slug&tab=cacher&clearcache=y";
        $wpn_cc_url = wp_nonce_url( $clearCacheUrl, 'jcipro_clearcache' );
?>
           <a href="<?php echo $wpn_cc_url; ?>">Click here to CLEAR CACHE</a>
           </td>
        </tr>  
<?php
  break;
  case 'uninstall' :
?>
      <tr>
        <td>
           <h1>Uninstall:</h1>
           <br>
           On default, not all data of this plugin is deleted: 
           If the following checkbox is NOT activated (default settings) you can deactivate and delete the JSON Content Importer PRO Plugin without risk. After reinstalling all data is still there.
		   <br>
		   Only if the following checkbox is activated, also templates, settings etc. are deleted.
           <br>
           <input type="checkbox" name="jci_pro_uninstall_deleteall" value="1" <?php echo (get_option('jci_pro_uninstall_deleteall') == 1)?"checked=checked":""; ?> /> delete all, incl. templates and above options
        </td>
      </tr>
      <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Update Settings" />
        </td></tr>
<?php
  break;
  case 'gdpr' :
?>
      <tr>
        <td>
           <h1>General Data Protection Regulation (GDPR):</h1>
           <br>
           The General Data Protection Regulation <a href="https://eur-lex.europa.eu/eli/reg/2016/679/oj" target="_blank">(EU) 2016/679</a> ("GDPR") is a regulation in EU law on data protection and privacy for all individuals within the European Union (EU) and the European Economic Area (EEA) 
           (<a href="https://en.wikipedia.org/wiki/General_Data_Protection_Regulation" target="_blank">see more on that at Wikipedia</a>).
           For this plugin this is relevant in two ways:
          <ol>
          <li>You use the plugin to get data from APIs, transform it and display it on a website: If the data contains personal data you have to check the GDPR. Then the plugin is piece of software working with that data. Add then the plugin to your <a href="https://gdpr-info.eu/art-30-gdpr/" target="_blank">GDPR-"Records of processing activities"</a>.</li>
          <li>When you install and activate the plugin with your licencekey: The licencekey is frequently validated by www.json-content-importer.com.
          As the licencekey is connected to the buyer of the plugin, this is the automatic usage of pseudonym personal data by www.json-content-importer.com. When buying the plugin the customer aggreed to that, by accepting the terms of service and purchase.
          </li>
          </ol>
           
           <br>
        </td>
      </tr>
<?php
break;
  case 'customposttypes' :
?>
      <tr>
        <td>
           <h1>Custom Post Types:</h1>
           <br>
           Custom Post Types (CPT) like the Wordpress-defaults Posts and Pages are the Blueprint for Custom Pages (CP) with a defined set of Information like Title, Content, Taxonomies, CustomPostFields (CPF) etc.<br>
		   This plugin can create CPs out of CPT and JSON.<br>
		   For that you need a CPT-Blueprint at first.
		   </td></tr>
		   <tr><td>
		   <h2>Create Custom Post Types:</h2>
		   How to create CPT with CPF and Taxonomies: 
		   <ul>
		   <li>&bull; Use the Wordpress-buildt-in Posts / Pages,</li>
			<li>&bull; Use the JCI-own way (limited, see below - but out of the box).</li>
			<li>&bull; Use plugins <b><a href="https://toolset.com/" rel="noopener noreferrer" target="_blank">Toolset</a></b> or <b><a href="https://de.wordpress.org/plugins/pods/" rel="noopener noreferrer" target="_blank">Pods</a></b> 
			(very powerful and easy, see also <a href="https://www.youtube.com/watch?v=fQsiJj_Aozw" target="_blank">this video</a>)</li>
			<ul>

</td></tr><tr><td>
		   <h2>JCI-own way: Create JCI-Custom Post Types</h2>
			is like this:</strong><br>You have to define four parameters for a new Custom Post Type:
<ul>
<li>&bull; type: Singluar name of the Custom Type Page. The Plugin adds automatic "jci_" as prefix to avoid trouble with other Plugins and CPT</li>
<li>&bull; ptredirect: Path for the URL of the created Custom Pages</li>
<li>&bull; ptname: Menuname in the Wordpress-Dashboard</li>
<li>&bull; key: Unique, random string to connect all pages created by this JSON (when deleting, all pages with that key are deleted). You can use twig here, e.g.: 'date{{"now"|date("mdY")}}' sets the key to the current date</li>
</ul>
The keys and values have to be separated by "=" and the pairs by ";". If you want to define more than one Custom Type Page separate by "##".
<hr><strong>Example:</strong><br>
type=mynewposttype;ptredirect=herewego;ptname=MyCreatedPost;key=34tdssg54<br>

           <?php
              $val_jci_pro_custom_post_types = get_option('jci_pro_custom_post_types');
              $val_jci_pro_custom_post_types = stripslashes($val_jci_pro_custom_post_types);
              $val_jci_pro_custom_post_types = htmlentities($val_jci_pro_custom_post_types);
           ?>
          <br>
		  <input type="text" name="jci_pro_custom_post_types" value="<?php echo $val_jci_pro_custom_post_types; ?>" size="150">

        </td>
      </tr>
     <tr><td>
		
        <h2>Enable "CustomPostPages-Fast-Delete":</h2>
		If you want to update many CPs (connected by the same key), all previous created CPs are deleted. This might take some time. To speed up, you can try this switch:<br>
		<input type="checkbox" name="jci_pro_cp_fastdelete" value="1" <?php echo (get_option('jci_pro_cp_fastdelete') == 1)?"checked=checked":""; ?> />
		Delete generated CPs fast (direct in Database instead of WP-methods). 
        </td></tr>
		
     <tr><td>
    <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
    <input type="submit" name="Submit"  class="button-primary" value="Store and update the above Settings" />
        </td></tr>
<?php
break;
  case 'jciextra' :
?>
      <tr>
        <td>
           <h1>Load JS-/CSS-Libraries:</h1>
  <input type="hidden" name="jci-pro-settings-submit" value="savesettings" />
           <br>
           In case you need Libraries like <a href="https://jqueryui.com/" target="_blank">jQuery-UI</a> for displaying your data and your Wordpress-template does not load these libs you can invoke it here (on all pages of this Wordpress Installation!):
<p>
            <input type="checkbox" name="jci_pro_load_jquery" value="1" <?php echo (get_option('jci_pro_load_jquery') == 1)?"checked=checked":""; ?> /> Load "jquery-3.5.1.min.js"<br>
            <input type="checkbox" name="jci_pro_load_jqueryui" value="1" <?php echo (get_option('jci_pro_load_jqueryui') == 1)?"checked=checked":""; ?> /> Load "jquery-ui.min.js 1.12.1"<br>
            <input type="checkbox" name="jci_pro_load_jqueryuicss" value="1" <?php echo (get_option('jci_pro_load_jqueryuicss') == 1)?"checked=checked":""; ?> /> Load "jquery-ui.min.css 1.12.1"<br>
            <input type="checkbox" name="jci_pro_load_jqueryuitouchpunch" value="1" <?php echo (get_option('jci_pro_load_jqueryuitouchpunch') == 1)?"checked=checked":""; ?> /> Load <a href="https://github.com/furf/jquery-ui-touch-punch" target="_blank">jQuery-UI Touch Punch "jqueryui-touch-punch-0.2.3.min.js" (e. g. for using sliders on mobile devices)</a><br>
            <input type="checkbox" name="jci_pro_load_jquerymobilejs" value="1" <?php echo (get_option('jci_pro_load_jquerymobilejs') == 1)?"checked=checked":""; ?> /> Load <a href="https://jquerymobile.com/download/" target="_blank">jQuery mobile JS</a><br>
            <input type="checkbox" name="jci_pro_load_jquerymobilecss" value="1" <?php echo (get_option('jci_pro_load_jquerymobilecss') == 1)?"checked=checked":""; ?> /> Load <a href="https://jquerymobile.com/download/" target="_blank">jQuery mobile CSS</a><br>
            <input type="checkbox" name="jci_pro_load_foundationfloatmincss" value="1" <?php echo (get_option('jci_pro_load_foundationfloatmincss') == 1)?"checked=checked":""; ?> /> Load <a href="https://get.foundation/sites/docs/grid.html" target="_blank">foundation-float.min.css for CSS-grids</a><p>

			If the Libraries should be only on some pages, set the PageIDs (the number post=NUMBER when editing a Post...) here, separated by comma.<br>
			If the following is empty, the Libraries are loaded on all pages.<br>
<?php
              $val_jci_pro_load_libs_pageids = get_option('jci_pro_load_libs_pageids');
              $val_jci_pro_load_libs_pageids = stripslashes($val_jci_pro_load_libs_pageids);
              $val_jci_pro_load_libs_pageids = htmlentities($val_jci_pro_load_libs_pageids);
?>
			<input type="text" name="jci_pro_load_libs_pageids" value="<?php echo $val_jci_pro_load_libs_pageids; ?>" size="150">
			
<p>    <input type="submit" name="Submit"  class="button-primary" value="Store and update the following Settings" />
<p>
			Alternative: If you need a Library only on some pages, add HTML-Loading to the JCI-twig-template like this:
<?php 
	$jsuicode = '
<script type="text/javascript" src="/wp-content/plugins/jsoncontentimporterpro3/js/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/jsoncontentimporterpro3/js/jquery/jquery-ui.js"></script>
<script type="text/javascript" src="/wp-content/plugins/jsoncontentimporterpro3/js/jquery/jqueryui-touch-punch-0.2.3.min.js"></script>
<link rel=stylesheet" href="/wp-content/plugins/jsoncontentimporterpro3/js/jquery/jquery-ui.css">

';
			
	echo "<br><code>".nl2br(htmlentities($jsuicode))."</code>";
			
?>			
    </td></tr>
<?php
break;
  case 'check' :
?>
      <tr>
        <td>
           <h1>Check if installation is ok:</h1>
<?php
        $phpvers = phpversion();
        echo "<h2>PHP</h2>";
        echo "PHP-Version: Installed: $phpvers<br>";
        if (version_compare(PHP_VERSION,'8')>0) {
          echo '<b><span style="color:#f00;">PHP Version 8 works with Twig 3.3.2 only! Not with Twig 3!</span></b><br>';
        }
		
        echo "<a href=\"https://www.php.net/manual/en/features.remote-files.php\" target=\"_blank\">PHP allow_url_fopen check</a>: ";
		if( ini_get('allow_url_fopen') ) {
          echo '<b><span style="color:#4CC417;">ok</span></b>';
        } else {
          echo '<b><span style="color:#f00;">NOT ok, allow_url_fopen NOT active: Maybe even the free JCI-Plugin does not work on this server. Also you might get timeout-errors when licencing or using this  plugin, as the server can\'t read remote URLs.</span></b>';
        }
		
        echo "<br>JCI-parser needs at least PHP 5.3.0 for using <a href=\"http://php.net/manual/de/functions.anonymous.php\" target=\"_blank\">Anonymous PHP-functions</a>: ";
        if (version_compare('5.3.0', $phpvers)==1) {
          echo '<b><span style="color:#f00;">PHP NOT ok for using JCI-parser</span></b>';
        } else {
          echo '<b><span style="color:#4CC417;">PHP ok for using JCI-parser</span></b>';
        }
		
        echo '<br>Twig 1.X Parser <a href="https://twig.symfony.com/doc/1.x/templates.html" target="_blank">needs at least PHP 5.5.0</a>: ';
        if (version_compare('5.5.0', $phpvers)==1) {
          echo '<b><span style="color:#f00;">PHP NOT ok for using twig 1.X parser</span></b>';
        } else {
          echo '<b><span style="color:#4CC417;">PHP ok for using twig 1.X parser</span></b>';
        }
        echo '<br>Twig 2.X Parser <a href="https://twig.symfony.com/doc/2.x/templates.html" target="_blank">needs at least PHP 7.0.0</a>: ';
        if (version_compare('7.0.0', $phpvers)==1) {
          echo '<b><span style="color:#f00;">PHP NOT ok for using twig 2.X parser</span></b>';
        } else {
          echo '<b><span style="color:#4CC417;">PHP ok for using twig 2.X parser</span></b>';
        }
        echo '<br>Twig 3.X Parser <a href="https://twig.symfony.com/doc/3.x/templates.html" target="_blank">needs at least PHP 7.2.0</a>: ';
        if (version_compare('7.2.0', $phpvers)==1) {
          echo '<b><span style="color:#f00;">PHP NOT ok for using twig 3.X parser</span></b>';
        } else {
          echo '<b><span style="color:#4CC417;">PHP ok for using twig 3.X parser</span></b>';
        }
		
		$placeoftwiglib = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
        echo '<br>Check JCI-twig-Library in filesystem at '.$placeoftwiglib.': ';
		if (file_exists($placeoftwiglib)) {
          echo '<b><span style="color:#4CC417;">ok</span></b>';
		} else {
          echo '<b><span style="color:#f00;">NOT found</span></b>';
		}
		
		
		if (function_exists('curl_version')) {
			$curlVersionArr = curl_version();
			echo '<br><b><span style="color:#4CC417;">CURL: Needed for default http(s)-requests</span></b> - <a href="https://curl.haxx.se/docs/releases.html" target="_blank">CURL-Version '.$curlVersionArr["version"].' is installed</a>';
		} else {
			echo '<br><b><span style="color:#f00;">CURL is not installed!</span></b> Use PHP-RAWGET or PHP-RAWPOST as method for http(s)-requests (see Plugin-Template) or install CURL (which is very helpful)';
		}
		
		
		
        echo "</td></tr><tr><td>";

        // check multisite BEGIN
        if (function_exists('is_multisite') && is_multisite()) {
          echo "<h2>Multisite Installation:</h2>";
          echo "This is a wordpress multisite installation!";
          echo "<br>If a plugins is 'networkwide activated' it's available for all multisite websites.<br>";
          echo "If it's 'networkwide deactivated' it is not available.<br>";
          // network activation? yes: create db for each blog id
          if ($networkwide) {
            echo '<b><span style="color:#f00;">Plugin is NOT activated networkwide: Activate via "network admin dashboard &gt; plugins", please.</span></b>';
          } else {
            echo '<b><span style="color:#4CC417;">Plugin is activated networkwide.</span></b>';
          }
		   echo "</td></tr><tr><td>";
        }
        // check multisite END


      // check if twig is working BEGIN
      /*
	  echo "<h2>Twig</h2>";

      $twigOk = FALSE;
      if (class_exists( 'Twig_Autoloader' ) ) {
        # there is a twig from another plugin
        $foundTwigVersion = Twig_Environment::VERSION;
        echo '<b><span style="color:#f00;">Twig Version '.$foundTwigVersion.' found, but loaded from another plugin (e.g. "Timber")!</span></b><br>';
        echo "Rarely this causes problems - you might use another main version 1.X, 2.X or 3.X instead.<br>";
        echo "If you deactivate other plugins you can find the one who is using twig too.<br>";
        echo "The JCI-plugin uses Twig 1.24.0 (parser=twig), 2.4.3 (parser=twig243) or 3.0.3 (parser=twig3).";
        $twigOk = TRUE;
      }	else {
		if (version_compare('7.2.0', $phpvers, '<=')==-1) {
			# check twig 3.X
			$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig3/vendor/autoload.php';
			$twigOk = checkTwig("3.X", $phpvers, "twig3");
        } else if (version_compare('7.0.0', $phpvers, '<=')==-1) {
			# check twig 2.X
			$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig243/vendor/autoload.php';
			$twigOk = checkTwig("2.X", $phpvers, "twig243");
        } else if (version_compare('5.5.0', $phpvers, '<=')==-1) {
			# check twig 1.X
			$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig1/Twig/Autoloader.php';
			$twigOk = checkTwig("1.X", $phpvers, "twig");
        } else {
			echo "<font color=red>To use twig: Upgrade your PHP-Version at least to Version 5.5.0, better 7.2.0</font>";
		}
      }
      if (!$twigOk) {
        echo "<br><font color=red>twig is not avaliable</font>";
      }
	  */
       // check if twig is working END

      // check if cache is working BEGIN
		
		echo "</td></tr><tr><td>";
      echo "<h2>Cache</h2>";
		echo "<b>Check JSON-cacher and cachefolder (directory where JSON-feeds are stored to reduce API-requests):</b><br>";
		require_once plugin_dir_path( __FILE__ ) . '/lib/cache.php';
		$jci_Cache = new jci_Cache();

        $cacheEnabledOption = get_option('jci_pro_enable_cache');
        if ($cacheEnabledOption==1) {
          echo "Cache is active (see Tab 'Cache')<br>";
        } else {
          echo "Cache is NOT active (see Tab 'Cache')<br>";
        }
		if ($jci_Cache->get_open_basedir_error()) {
			$cacheFolderOptions = get_option('jci_pro_cache_path');
			echo '<span style="color:#f00;">The cacheFolder '.$cacheFolderOptions.' can\'t be used!<br>';
			echo 'The Webserver-Settings do allow only dir\'s above the open_basedir-dir: '.($jci_Cache->get_open_basedir()).'<br>';
			echo 'Change the cacheFolder to a dir matching these requirements!';
			echo '</span>';
			
		} else {
			$cacheFolder = $jci_Cache->getCacheFolder();
			if (is_dir($cacheFolder)) {
				# cachedir is there
				if (is_writeable($cacheFolder)) {
					echo '<span style="color:#4CC417;">cacheFolder '.$cacheFolder.' is there and writeable</span>';
				} else {
					echo '<span style="color:#f00;">cacheFolder '.$cacheFolder.' is there but NOT writeable</span>';
				}
			} else {
				# cachedir is NOT there
				echo '<span style="color:#f00;">cacheFolder '.$cacheFolder.' is NOT there</span>';
				echo "<br>don't panic: this is ok if cache was never active on this wordpress installation.";
				echo "<br>the directory is created the first time the cache is switched on and used!";
			}
		}
		
		echo "<br>";
		$clearCacheUrl = "admin.php?page=unique_jcipro_menu_slug&tab=cacher";
		echo "<a href=\"".$clearCacheUrl."\">CACHE-Details</a>";
		echo "</td></tr>";
		echo "<tr><td>";
      // check if cache is working END


      echo "<h2>Database for template-manager</h2>";
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
          $blogIdCurrent = $wpdb->blogid;
          echo "Wordpress-multisite installation, current blog id: ".$blogIdCurrent."<br>";
        } else {
          echo "Wordpress-singlesite installation<br>";
        }
        $table_name = $wpdb->prefix.'plugin_jci_pro_templates';
        if ($wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'") == $table_name) {
          echo '<b><span style="color:#4CC417;">'.$table_name.' ok</span></b><br>';
        } else {
          echo '<b><span style="color:#f00;">'.$table_name.' MISSING</span></b><br>';
          echo 'When installing this plugin, Wordpress tries to create a table in the wordpress-database named '.$table_name.'.<br>';
          echo 'This failed at this installation, very unusual behaviour! Try this:<br>';
          echo 'Please uninstall this plugin, deactivate all other plugins and reinstall this plugin.<br>If that does not solve the problem: ';
          echo '<a href="https://json-content-importer.com/legal-contact/" target="_blank">Contact the plugin-developer</a>';
        }
		echo "</td></tr>";

		/*
		echo "<tr><td>";

        echo "<h2>Check other plugins</h2>";
        echo "Some other plugins also use the twig-templateengine. This may cause problems as severals twig-libraries are arround:<br>";
        echo "In this case, wordpress might not use the twig-libraray which comes whith this plugin. But a newer or older version.<br>";
        echo "If you have problems here: Try to use twig1, twig2 or twig3 to check if one of these is working. If not: report to the <a href=\"https://json-content-importer.com/legal-contact/\" target=\"_blank\">pluginauthor</a>, please.<br>Plugins to watch:<br>";
        $arr_pluginlist = get_plugins();
        if (count($arr_pluginlist)==0) {
          echo '<span style="color:#4CC417;">no other plugins installed - no such problems ;-)</span><br>';
        } else {
          $listOfProblemPlugins["timber-library/timber.php"] = 1;
          $listOfProblemPlugins["all-in-one-event-calendar/all-in-one-event-calendar.php"] = 1;
          $listOfProblemPlugins["publishpress/publishpress.php"] = 1;
          $listOfProblemPlugins["publishpress-authors/publishpress-authors.php"] = 1;
          $listOfProblemPlugins["contact-form-7/wp-contact-form-7.php"] = 2;
          $listOfProblemPlugins["hello.php"] = 2;
          $listOfProblemPlugins["akismet/akismet.php"] = 2;
          $listOfProblemPlugins["json-content-importer/json-content-importer.php"] = 2;
          $listOfProblemPlugins["jsoncontentimporterpro3/jsoncontentimporterpro.php"] = 3;
          $listOfProblemPlugins["si-captcha-for-wordpress/si-captcha.php"] = 2;
          $listOfProblemPlugins["admin-menu-editor/menu-editor.php"] = 2;
          $listOfProblemPlugins["automatic-post-tagger/automatic-post-tagger.php"] = 2;
          $listOfProblemPlugins["contest-gallery/index.php"] = 2;
          $listOfProblemPlugins["email-address-encoder/email-address-encoder.php"] = 2;
          $listOfProblemPlugins["jetpack/jetpack.php"] = 2;
          $listOfProblemPlugins["json-content-importer-widget/json-content-importer-widget.php"] = 2;
          $listOfProblemPlugins["jm-twitter-cards/jm-twitter-cards.php"] = 2;
          $listOfProblemPlugins["google-sitemap-generator/sitemap.php"] = 2;
          $listOfProblemPlugins["newyorktimes-api-jci/newyorktimes-api-jci.php"] = 2;
          $listOfProblemPlugins["seo-image/seo-friendly-images.php"] = 2;
          $listOfProblemPlugins["updraftplus/updraftplus.php"] = 2;
          $listOfProblemPlugins["wordpress-popular-posts/wordpress-popular-posts.php"] = 2;
          $listOfProblemPlugins["wordfence/wordfence.php"] = 2;
          $listOfProblemPlugins["wp-google-analytics/wp-google-analytics.php"] = 2;
          $listOfProblemPlugins["wp-super-cache/wp-cache.php"] = 2;
          $listOfProblemPlugins["wordpress-seo/wp-seo.php"] = 2;
          $listOfProblemPlugins["yet-another-related-posts-plugin/yarpp.php"] = 2;
          $listOfProblemPlugins["insert-php/insert_php.php"] = 2;
          $listOfProblemPlugins["syntaxhighlighter/syntaxhighlighter.php"] = 2;
          $listOfProblemPlugins["wp-global-variable/my-global-variable.php"] = 2;
          $listOfProblemPlugins["Classic Editor"] = 2;
          $listOfProblemPlugins["Gutenberg"] = 2;
          $listOfProblemPlugins["wp-file-upload/wordpress_file_upload.php"] = 2;
          $listOfProblemPlugins["wp-memory-usage/wp-memory-usage.php"] = 2;
          $listOfProblemPlugins["wp-crontrol/wp-crontrol.php"] = 2;
          $listOfProblemPlugins["wp-google-maps/wpGoogleMaps.php"] = 2;
          $listOfProblemPlugins["rest-api/plugin.php"] = 2;
          $listOfProblemPlugins["wpseo/wpseo.php"] = 2;

          $listOfProblemPlugins["mailpoet/mailpoet.php"] = 2;
          $listOfProblemPlugins["woocommerce/woocommerce.php"] = 2;
          $listOfProblemPlugins["woocommerce-payments/woocommerce-payments.php"] = 2;
          $listOfProblemPlugins["woocommerce-services/woocommerce-services.php"] = 2;
          $listOfProblemPlugins["wp-mail-smtp/wp_mail_smtp.php"] = 2;
          $listOfProblemPlugins["antispam-bee/antispam_bee.php"] = 2;
          $listOfProblemPlugins["wedocs/wedocs.php"] = 2;

          
        $listOfProblemPlugins["advanced-custom-fields/acf.php"] = 2;
		$listOfProblemPlugins["auto-refresh-api-ajax/auto-refresh-api-ajax.php"] = 2;
		$listOfProblemPlugins["build-relationships-toolset-plugin/build_relationships_cpt_toolset.php"] = 2;
		$listOfProblemPlugins["classic-editor/classic-editor.php"] = 2;
		$listOfProblemPlugins["elementor/elementor.php"] = 2;
		$listOfProblemPlugins["get-url-cron/geturlcron.php"] = 2;
		$listOfProblemPlugins["jsoncontentimporterpro3/jsoncontentimporterpro.php"] = 2;
		$listOfProblemPlugins["pods/init.php"] = 2;
		$listOfProblemPlugins["toolset-blocks/wp-views.php"] = 2;
		$listOfProblemPlugins["types/wpcf.php"] = 2;
		$listOfProblemPlugins["wp-views/wp-views.php"] = 2;

          #var_Dump($arr_pluginlist);
          $foundProblemPlugins = 0;
          $show2admin = FALSE; # TRUE;
          echo "<ol>";
          foreach ($arr_pluginlist as $key => $pl) {
            if ( isset($listOfProblemPlugins[$key]) && $listOfProblemPlugins[$key]==1) {
              # problem plugins
              $foundProblemPlugins++;
              if (is_plugin_active($key)) {
                if (!$show2admin) {
                  echo '<li><span style="color:#f00;">'.$pl["Name"]." (plugin set active: this may cause problems)</span></li>";
                }
              } else {
                if (!$show2admin) {
                  echo '<li><span style="color:#f00;">'.$pl["Name"]." (plugin is set inactive, when active this may work but also may cause problems)</span></li>";
                }
              }
            } else if ( isset($listOfProblemPlugins[$key]) && $listOfProblemPlugins[$key]==2) {
              # ok plugins
                if (!$show2admin) {
                  echo '<li><span style="color:#4CC417;">'.$pl["Name"]." (plugin ok with JCI-plugin)</span></li>";
                }
            } else if ( isset($listOfProblemPlugins[$key]) && $listOfProblemPlugins[$key]==3) {
                # this is the plugin itself
            } else {
              # unknown status plugins
              if (is_plugin_active($key)) {
                if ($show2admin) {
                  echo '$listOfProblemPlugins{"'.$pl["Name"].'"} = 2;<br>';
                } else {
                  echo '<li><span style="color:f00;">'.$pl["Name"]." (active plugin, unknown status regarding this twig-problem: $key)</span></li>";
                }
              } else {
                if ($show2admin) {
                  #echo '$listOfProblemPlugins{"'.$pl["Name"].'"} = 2;<br>';
                } else {
                  echo '<li><span style="color:f00;">'.$pl["Name"]." (inactive plugin, unknown status regarding this twig-problem: $key)</span></li>";
                }
              }
            }
          }
          echo "</ol>";
          echo "<h2>".count($arr_pluginlist)." plugins found: $foundProblemPlugins may cause problems</h2>";
        }
		echo "</td></tr>";
		*/

break;
}
}
?>
    </table>
</form>
</div>
<?php
}
/* options END */

function checkTwig($twigVersion, $phpvers, $shortcodeForTwig) {
	if (!class_exists('doTwig')) {
		$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
		require_once $inc;
		$twigHandler = new doJCITwig($shortcodeForTwig, TRUE);
    }
	$contin = $twigVersion.time();
	$json4twig = array("twigversionandtime" => $contin);
	$twigtesttemplate = "received JSON:<br>{{_context | json_encode }}";

	#$needresult = $timest."Array ( [twigversionl] => twigversion: $twigVersion )";

	$result = $twigHandler->executeTwig($json4twig, $twigtesttemplate, $shortcodeForTwig, TRUE);
  	echo "You use PHP $phpvers, with that twig $twigVersion should be used.";
	if (preg_match("/$contin/", $result)) {
			echo "<font color=green>twig-test successful. installed twig-version: ".$twigHandler->jci_getSelectedTwigVersion()."</font>";
	} else {
			echo "<font color=red>test failed</font><br>";
			$twigDebugMsg = $twigHandler->getTwigDebug();
			echo "test not successful:<br>$twigDebugMsg";
	}
	return TRUE;
}

function get_option_and_prepare_for_form($txt) {
  $txtoption = get_option($txt);
  $txtoption = preg_replace("/\"/", "&quot;", $txtoption);
  return stripslashes($txtoption);
}


/* define tabs for plugin-admin-menu BEGIN*/
function jci_pro_admin_tabs( $current = 'syntax' ) {
    $tabs = array(
          'syntax' => 'Initial help',
          'check' => 'Check Install',
          'asettings' => 'Admin-Settings',
          'cacher' => 'Cache',
          #'syntaxtwig' => 'Twig-Parser',
          #'examples' => 'Examples',
          'shortcodeatt' => 'Shortcode-Settings',
          'header' => 'HTTP: Header, Body',
          'curlsettings' => 'CURL',
          #'shortcodeval' => 'Shortcode-Values',
          #'settings' => 'Settings',
          'customposttypes' => 'Custom Post Types',
          'jciextra' => 'Extras',
          'gdpr' => 'GDPR',
          'uninstall' => 'Uninstall',
          );

		$jci_pro_hide_deprecated_value = get_option('jci_pro_hide_deprecated');
		if ($jci_pro_hide_deprecated_value=="yes") {
			unset($tabs['header']);
			unset($tabs['curlsettings']);
		}


    echo '<h2 class="nav-tab-wrapper">';
	echo "<style>.nav-tab-active, .nav-tab-active:hover {background-color: #0071a1;color: #FFF;}</style>";
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=unique_jcipro_menu_slug&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}
/* define tabs for plugin-admin-menu END*/

/* save settings BEGIN*/
function jci_pro_save_check_value($val, $changefound) {
  $areThereChanges = $changefound;
  $inputValPost = trim((@$_POST[$val])); # remove spaces at begin / end
#  $inputValPost = trim(strip_tags(@$_POST[$val])); # remove tags and spaces at begin / end: not good as aa<bbb would be stored as aa
  if (!($inputValPost == get_option($val))) {
    update_option( $val, $inputValPost );
    $areThereChanges = TRUE;
  }
  return $areThereChanges;
}
/* save settings END*/


/* save settings BEGIN*/
function jci_pro_save_settings() {
  # check if call is ok
  if (!isset($_POST["jci-pro-settings-submit"]) || ($_POST["jci-pro-settings-submit"] != 'savesettings') ) {
    # invalid savecall
    return 0;
  }

  #$nonce = $_REQUEST['_wpnonce'];
  isset($_REQUEST['_wpnonce']) ? $nonce = $_REQUEST['_wpnonce'] : $nonce = NULL;

  $nonceCheck = wp_verify_nonce( $nonce, "jci-pro-set-page" );
  if (!$nonceCheck) {
    # invalid nonce, hence invalid call
    return -2;
  }

   global $pagenow;
   if ( $pagenow == 'admin.php' && $_GET['page'] == 'unique_jcipro_menu_slug' ){
      if ( isset ( $_GET['tab'] ) ) {
        $tab = $_GET['tab'];
      } else {
        $tab = 'syntax';
      }

      $areThereChanges = FALSE;
      switch ( $tab ){
      case 'header' :
        $areThereChanges = jci_pro_save_check_value("jci_pro_allow_oauth_code", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_http_header_useragent", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_http_header_accept", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_http_body", $areThereChanges);
        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'asettings' :
        $areThereChanges = jci_pro_save_check_value("jci_pro_selected_editor", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_hide_deprecated", $areThereChanges);
        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'curlsettings' :
        $areThereChanges = jci_pro_save_check_value("jci_pro_curl_usernamepassword", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_curl_authmethod", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_curl_optionlist", $areThereChanges);
        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'cacher' :
        $areThereChanges = jci_pro_save_check_value("jci_pro_api_errorhandling", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_enable_cache", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_enable_twigcache", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_cache_time_format", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_cache_path", $areThereChanges);
        if (!is_numeric($_POST['jci_pro_cache_time'] )) {
          return -6;
        } else {
          $areThereChanges = jci_pro_save_check_value("jci_pro_cache_time", $areThereChanges);
        }
        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'syntax' :
         return 1;
      break;
      case 'uninstall' :
         # no settings yet $settings['....'] = ....
        $areThereChanges = jci_pro_save_check_value("jci_pro_uninstall_deleteall", $areThereChanges);
        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'customposttypes' :
         # no settings yet $settings['....'] = ....
        $areThereChanges = jci_pro_save_check_value("jci_pro_custom_post_types", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_cp_fastdelete", $areThereChanges);
        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'jciextra' :
        # no settings yet $settings['....'] = ....
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_jquery", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_jqueryui", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_jqueryuicss", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_jqueryuitouchpunch", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_jquerymobilejs", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_jquerymobilecss", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_foundationfloatmincss", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_load_libs_pageids", $areThereChanges);

        if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      case 'shortcodeatt' :
        $areThereChanges = jci_pro_save_check_value("jci_pro_allow_urlparam", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_allow_regexp", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_allow_urldirdyn", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_delimiter", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_use_wpautop", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_order_of_shortcodeeval", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_php_timeout", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_debugmode", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_errormessage", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_json_fileload_basepath", $areThereChanges);
        $areThereChanges = jci_pro_save_check_value("jci_pro_use_nestedlevel", $areThereChanges);
		if ($areThereChanges) {
          return 2;
        } else {
          return 1;
        }
      break;
      }
   }
   return -3;
}
/* save settings END*/

/* templates BEGIN */
function remove_param_quotes() {
	$_GET    = stripslashes_deep($_GET);
	$_POST   = stripslashes_deep($_POST);
	$_COOKIE = stripslashes_deep($_COOKIE);
	$_REQUEST = stripslashes_deep($_REQUEST);
}

function create_jci_pro_plugin_db($networkwide) {
  handle_jci_pro_plugin_db('_activate_jci_database', $networkwide);
}
function deactivate_jci_pro_plugin_db($networkwide) {
  handle_jci_pro_plugin_db('_deactivate_jci_database', $networkwide);
}


function handle_jci_pro_plugin_db($typefunction, $networkwide) {
    global $wpdb;
    if (function_exists('is_multisite') && is_multisite()) {
        // network activation? yes: create db for each blog id
        if ($networkwide) {
          $blogIdCurrent = $wpdb->blogid;
          // retrieve blogIds
          $blogIdArr = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
          foreach ($blogIdArr as $blogid) {
            switch_to_blog($blogid);
            call_user_func($typefunction, $networkwide);
          }
          switch_to_blog($blogIdCurrent);
          return;
        }
    } else {
      if ( false == current_user_can( 'activate_plugins' ) ) {
        return;
      }
      call_user_func($typefunction, $networkwide);
    }
}


function _activate_jci_database() {
    global $wpdb;
    $table_name = $wpdb->prefix.'plugin_jci_pro_templates';
    _create_jci_database($table_name);
}

function _deactivate_jci_database() {
    global $wpdb;
    $table_name = $wpdb->prefix.'plugin_jci_pro_templates';
    if ($wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'") != $table_name) {
      _delete_jci_database($table_name, $type);
    }
}

function _delete_jci_database() {
  global $wpdb;
	$table_name = $wpdb->prefix."plugin_jci_pro_templates";
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name )	{
		$sql = "DROP TABLE IF EXISTS {$table_name}";
		$wpdb->query($sql);
	}
}


function _create_jci_database($table_name) {
  global $wpdb;
    $charset_collate = "";
    if (!empty ($wpdb->charset)) $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    if (!empty ($wpdb->collate)) $charset_collate .= " COLLATE {$wpdb->collate}";
	$sql = "CREATE TABLE {$table_name} (
		id INTEGER(10) AUTO_INCREMENT,
		nameoftemplate TEXT CHARACTER SET utf8 NOT NULL,
		template TEXT CHARACTER SET utf8 NOT NULL,
		urloftemplate TEXT CHARACTER SET utf8,
		basenode TEXT CHARACTER SET utf8,
		method TEXT CHARACTER SET utf8,
		parser TEXT CHARACTER SET utf8,
		postpayload TEXT CHARACTER SET utf8,
		postbody TEXT CHARACTER SET utf8,
		curloptions TEXT CHARACTER SET utf8,
		cachetime TEXT CHARACTER SET utf8,
		urlgettimeout TEXT CHARACTER SET utf8,
		urlparam4twig  TEXT CHARACTER SET utf8,
		debugmode TEXT CHARACTER SET utf8,
		PRIMARY KEY  (id),
    KEY nameoftemplateindex1 (nameoftemplate(40))
     ) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$resultdbDelta = dbDelta( $sql );
    ###update_option('plugin_jci_pro_templates_version','..'); # not here!!! set only if "jcipro_isDBok" was successful!!!
    $nooflinesindb = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
  	if ( $nooflinesindb==0 )	{
      $defaultitem = "{% set pageprop = wp_get_page_properties() %}\nThis post was modified at {{pageprop.get_post.post_modified | date(\"d.m.Y, H:i:s\") }}<br>\nExample-Template:<br>{{level1.start}}<hr>\n{% for i in  level1.level2 %}\n<strong>{{i.key}}:</strong> {{i.data.type}}, {{i.data.id}}<br>\n{% endfor %}\n<hr>";
  	  $wpdb->insert( $table_name, array( 'template' => $defaultitem, 'parser' => 'twig3', 'nameoftemplate' => 'example', 'method' => 'curlget', 'urloftemplate' => plugin_dir_url( __FILE__ ).'json/example1.json' ) );
    }

}

function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
    if (is_plugin_active_for_network('jsoncontentimporterpro3/jsoncontentimporterpro.php')) {
      $blogIdCurrent = $wpdb->blogid;
      switch_to_blog($blog_id);
      _activate_jci_database();
      switch_to_blog($blogIdCurrent);
    }
}

register_deactivation_hook( __FILE__, 'deactivate_jci_pro_plugin_db' );

if( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Templates_WP_List_Table extends WP_List_Table {
	#private $numRows = 0;
	private $tm_data = 0;
	private $tm_settings = array();

    function __construct($data){
        global $status, $page;
		#var_Dump($_POST);
		
		$this->tm_data = $data;
		array_walk_recursive($this->tm_data, array($this, 'filter'));
		$this->tm_settings = json_decode(get_option("jci_pro_templatelistsettings"), TRUE);
		if (empty($this->tm_settings["per_page"])) {
			$this->tm_settings["per_page"] = 10; 
		}
        parent::__construct( array(
            'singular'  => 'jcitemplate',    
            'plural'    => 'jcitemplates',   
            'ajax'      => false       
        ) );
    }

	function column_default( $item, $column_name ) {
		if ('debugmode'==$column_name) {
			if ($item[$column_name]<=1) {
				$outval = "off";
			} else {
				$outval = "on (".$item[$column_name].")";
			}
		}
		switch( $column_name ) {
			case 'id':
		    case 'template':
            return $this->column_overview_display($item[ $column_name ]);
		    case 'nameoftemplate':
            return $item[ $column_name ];
		    case 'urloftemplate':
            return $this->column_overview_display($item[ $column_name ]);
            #return $item[ $column_name ];
		    case 'basenode':
            return $item[ $column_name ];
		    case 'method':
            return $item[ $column_name ];
		    case 'parser':
            return $item[ $column_name ];
		    case 'postpayload':
            #return $item[ $column_name ];
            return $this->column_overview_display($item[ $column_name ]);
		    case 'postbody':
            return $this->column_overview_display($item[ $column_name ]);
            #return $item[ $column_name ];
		    case 'curloptions':
            #return $item[ $column_name ];
            return $this->column_overview_display($item[ $column_name ]);
		    case 'cachetime':
            return $item[ $column_name ];
		    case 'urlgettimeout':
            return $item[ $column_name ];
		    case 'urlparam4twig':
            return $this->column_overview_display($item[ $column_name ]);
            #return $item[ $column_name ];
		    case 'debugmode':
            return $outval;
			default:
            return print_r( $item, true ) ; //show the whole array for troubleshooting
		}
	}

	function column_id($item) {
		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">change</a>',$_REQUEST['page'],'edit',$item['id']),
			'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s" onclick = "if (! confirm(\'Really deleting template?\')) { return false; }">delete</a>',$_REQUEST['page'],'delete',$item['id']),
			'copy'    	=> sprintf('<a href="?page=%s&action=%s&id=%s">copy</a>',$_REQUEST['page'],'copy',$item['id']),
		);
		return sprintf('%1$s %2$s', $item["id"], $this->row_actions($actions, TRUE) );
	}	
	
	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  
			/*$2%s*/ $item['id']               
        );
	}	
	
	function get_columns(){
		# name of the columns: if active create column
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'id' => __( 'ID (for Shortcode)' ),
			'nameoftemplate'  => __( 'Templatename' ),
			'template'  => __( 'Templatecode' ),
			'urloftemplate'  => __( 'URL of Template' ),
			'urlparam4twig'  => __( 'URL-Param4twig' ),
			#'basenode'  => __( 'JSON basenode' ),
			'method'  => __( 'http Method' ),
			'curloptions'  => __( 'Curloptions' ),
			'parser'  => __( 'Parser' ),
			#'postpayload'  => __( 'postpayload' ),
			#'postbody'  => __( 'postbody' ),
			#'cachetime'  => __( 'cachetime' ),
			#'urlparam'  => __( 'urlparam' ),
			#'urlgettimeout'  => __( 'urlgettimeout' ),
			'debugmode'  => __( 'debugmode' ),
		);
		return $columns;
	}	
	
    function get_sortable_columns() {
        $sortable_columns = array(
            'id'     => array('id',TRUE),    
            'nameoftemplate'    => array('nameoftemplate',false),		
            'urloftemplate'    => array('urloftemplate',false),
            'urlparam4twig'    => array('urlparam4twig',false),
            'method'    => array('method',false),
            'curloptions'    => array('curloptions',false),
            'parser'    => array('parser',false),
            'debugmode'    => array('debugmode',false),
        );
        return $sortable_columns;
    }

	function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
			if (!empty($_POST["jcitemplate"])) {
				#echo "DELETE: ".json_encode($_POST["jcitemplate"]);
				global $wpdb;
				foreach ($_POST["jcitemplate"] as &$idlist) {
					$deleteErrorLevel = $wpdb->delete( $wpdb->prefix . "plugin_jci_pro_templates", array( 'id' => $idlist ) );
				}
				$this->tm_data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates ORDER BY id DESC', ARRAY_A );
				array_walk_recursive($this->tm_data, array($this, 'filter'));
			}
        }
    }
	
	function filter(&$value) {
		$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
	
	function prepare_items() {
        $this->process_bulk_action();
        $per_page = 10;
		echo "Screen Options: Change number of JCI-Templates per page";
		if (!empty($this->tm_settings["per_page"])) {
			$per_page = $this->tm_settings["per_page"];
		}
		if (isset($_POST['page']) && isset($_POST['s']) && (!empty($_POST['s']))) {
			$per_page = 10000;
		}	
	    $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
		$data = $this->tm_data;

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to id
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strnatcmp($b[$orderby], $a[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                 
            'per_page'    => $per_page,                    
            'total_pages' => ceil($total_items/$per_page)   
        ) );
	}

	private function column_overview_display( $column_content ) {
		$lenofitem = 20;
		if (strlen($column_content)>$lenofitem) {
			return substr( $column_content, 0, $lenofitem)."...";
		}
		return $column_content;
	}
}


function register_jci_pro_add_templates($tm_data) {
	add_template($tm_data);
}


function add_template($tm_data, $showonlyurlsettings=FALSE) {
#function register_jci_pro_add_templates($tm_data) {
  global $wpdb;
  $errormsg = "";
  $msg = "";
  if ( isset($_POST['submit']) ) {
  	remove_param_quotes();
    $template = $_POST['template'];
    if (""==trim($template)) {
		if (!$showonlyurlsettings) {
			echo '<h1><span style="color:#f00;">Template-Code missing! Add new template:</span></h1>';
		}
      showTemplateItem(NULL, "", "add", $showonlyurlsettings);
      return "";
    } else {
      if (!isset($_POST['postpayload'])) { $_POST['postpayload']=''; }
      if (!isset($_POST['postbody'])) { $_POST['postbody']=''; }
      if (!isset($_POST['cachetime'])) { $_POST['cachetime']=0; }
      if (!isset($_POST['urlgettimeout'])) { $_POST['urlgettimeout']=0; }
      if (!isset($_POST['debugmode'])) { $_POST['debugmode']=0; }
      
	  
	  ## if ace-editor: POST gives urlencoded template
		if ("ace" == get_option('jci_pro_selected_editor')) {
			$template = urldecode($template);
		}
	  #echo "INSERTtemplate:  $template<br>";
	  
	   $insertErrorlevel = $wpdb->insert( $wpdb->prefix. 'plugin_jci_pro_templates', 
       array( 
          'template' => $template
          , 'nameoftemplate' => $_POST['nameoftemplate']
          , 'urloftemplate' => $_POST['urloftemplate']
          , 'basenode' => $_POST['basenode']
          , 'method' => $_POST['method']
          , 'parser' => $_POST['parser']
          , 'postpayload' => $_POST['postpayload']
          , 'postbody' => $_POST['postbody']
          , 'curloptions' => $_POST['curloptions']
          , 'cachetime' => $_POST['cachetime']
          #, 'urlparam' => $_POST['urlparam']
          , 'urlgettimeout' => $_POST['urlgettimeout']
          , 'urlparam4twig' => $_POST['urlparam4twig']
          , 'debugmode' => $_POST['debugmode']
          ) 
        );
      if ($insertErrorlevel) {
        $errormsg = '<span style="color:#4CC417;">Success: Template saved</span>';
      } else {
        $errormsg = '<span style="color:#f00;">Failed: Template NOT saved</span>';
      }
      $insertedid = $wpdb->insert_id;
      if (is_int($insertedid) && ($insertedid>0)) {
        $table = $wpdb->get_row( "SELECT * from " . $wpdb->prefix . "plugin_jci_pro_templates WHERE id = " . sanitize_text_field($insertedid) . "" );
      }  
	echo "<h1>Template $insertedid saved. Any changes needed?</h1>";
      showTemplateItem($table, $errormsg, "change", $showonlyurlsettings);
      return "";
    }
  }

		if (!$showonlyurlsettings) {
			echo "<h1>Add new template:</h1>";
		}
  showTemplateItem(NULL, "", "add", $showonlyurlsettings);
  return "";
}

function showTemplateItem($table, $errormsg, $type, $showonlyurlsettings=FALSE, $editandthencopytemplate=FALSE) {
?>
<style> ul#jcieditor li {  display:inline; } </style>
	<ul id=jcieditor><li>
    <div style = "font-weight:bold;color:#4CC417;font-size:14px;padding:5px;"><?php echo $errormsg; ?></div>

    <?php 
		if ($editandthencopytemplate) {
			$table->id = NULL;
		}
		$tmpformurl = "";
		if ($showonlyurlsettings) {
			$tmpformurl = "jciprostep1getjsonslug";
			if (isset($table->id)) {
				$tmpformurl = $tmpformurl."&action=edit&id=".$table->id;
			} else { 
				$tmpformurl = $tmpformurl;
			}   
		} else {
			if (isset($table->id)) {
				$tmpformurl = "jciprotemplateslug&action=edit&id=".$table->id;
			} else { 
				$tmpformurl = "jciproaddtemplateslug";
			}   
		}
    ?>

    <form action="admin.php?page=<?php echo $tmpformurl; ?>" method="post">
	<?php 	button_save_update($type, $table, $showonlyurlsettings); 
	
	if ($showonlyurlsettings) {
		echo '<input type="hidden" name="template" id="template" value="stored only URL-settings, template is created otherwise">';
	} else {
	?>
 	<p>
    <b><a href="https://twig.symfony.com/doc/3.x/templates.html" target="_blank">Twig-Syntax</a> for the Template using the JSON:</b><br>
    <?php
        if (isset($table->template)) {
            $tmp = $table->template;
          } else {
            $tmp = "Twig-syntax for showing the complete JSON-data:<br>\n{{ _context | json_encode  }}";
        }
		
		if (empty(get_option('jci_pro_selected_editor'))) {
			$jciproseled = "puretext"; # "ace";
		} else {
			$jciproseled = get_option('jci_pro_selected_editor');
		}
		if ($jciproseled=="ace") {
			echo 'Editor: This is the <a href="https://ace.c9.io" target="_blank">ace Editor</a>, <a href="https://github.com/ajaxorg/ace/wiki/Default-Keyboard-Shortcuts" target="_blank">see here for Keyboard-Shortcuts</a> - In case of problems: switch to "pure Texteditor" in JCI-Options, Tab "Admin-Settings"';
			echo '<br>Try in the editor "for" or "if". Importaint: "&lt;/script&gt;" is replaced by "&lt;\/script\&gt;" as otherwise the Ace-Editor crashes. In action "&lt;/script&gt;" is re-replaced<br>';
			#echo '<textarea style="display:none;"  placeholder="" name="template" id="template" ></textarea>';
			########## editor ace begin
			require_once plugin_dir_path( __FILE__ ) . '/editor/jcieditor.php';
			$aceEditor = new JCIeditor("template", $tmp, '', '', '', "1000px", "500px");
			$aceEditor->showAceEditor();
			########## editor ace end
		} else {
			echo 'Editor: This is the simple pure Text-Editor, you might change it at the JCI-Settings, Tab "Admin-Settings"<br>';
			echo '<textarea style = "display:block;width:800px;height:500px;margin-bottom: 10px;" placeholder="Insert the twig- and HTML-code here" name="template" id="template" >'.$tmp.'</textarea>';
		}
	}
	?>
    </li><li><b>Unique template-name: You may replace the random number by a unique and better name for the template:</b><br>
	<?php
		$tableNameoftemplate = time()."-".rand();
		if (isset($table->nameoftemplate) && (!empty($table->nameoftemplate))) {
			$tableNameoftemplate = $table->nameoftemplate;
		}
		if ($editandthencopytemplate) {
			$tableNameoftemplate .= "-".rand();
		}
	?>
    <input type="text" name="nameoftemplate" placeholder="set a unique (!!!) templatename..." value = "<?php echo $tableNameoftemplate; ?>" size="35" /><br>
	Shortcode: <code>[jsoncontentimporterpro nameoftemplate="<?php echo $tableNameoftemplate; ?>"]</code>
	<hr>
	<?php
	if ($showonlyurlsettings) {
		echo '<input type="hidden" name="debugmode" value="10" />';
	} else {
	?>
    <b>Debugmode:</b><br>
    <?php isset($table->debugmode) ? $tmp = $table->debugmode : $tmp = 1; ?>
    <input type="radio" name="debugmode" value="1" <?php echo ($tmp == 1)?"checked=checked":""; ?> /> debugmode off
    <br>
    <input type="radio" name="debugmode" value="2" <?php echo ($tmp == 2)?"checked=checked":""; ?> /> debugmode ON (display some debug-infos)
    <br>
    <input type="radio" name="debugmode" value="10" <?php echo ($tmp == 10)?"checked=checked":""; ?> /> debugmode ON (display more debug-infos AND: test the following URL with it's settings like curloptions, timeout etc)
    <hr>
<?php
	}
?>	  
    <b>URL of Template:</b> The plugin is searching the Shortcode for "url". <br>If there's no url, but an url is defined with the connected template then this is the url.  You can put twig into the URL. The variables defined at urlparam4twig are available as urlparam.VARIABLE in the twig here.
	<br>
	If you define a relative URL (starting with "/") here <a href="https://developer.wordpress.org/reference/functions/home_url/" target="_blank">home_url</a> is added. This helps when switching domains (e. g. from dev- to live-server).
    <br>
	Using "dummyrequest": Then no http-request is done but a JSON with the page properties is returned.
	<br>
     <?php 
        if (isset($table->urloftemplate)) {
            $tmp = $table->urloftemplate;
            $tmp = htmlspecialchars($tmp, ENT_QUOTES);
          } else {
            $tmp = "";
        }
     ?>
    <textarea style="display:block;width:800px;height:100px;margin-bottom: 10px;" placeholder="Insert URL here. Linefeeds are removed when URL is used. {{urlparam.VAR1}} is the value of the GET/POST-Variable defined by urlparam4twig" name="urloftemplate" id="urloftemplate" ><?php echo $tmp; ?></textarea>
     urlparam4twig (separated by #):<br>
    <input type="text" name="urlparam4twig" placeholder="urlparam4twig" value = "<?php isset($table->urlparam4twig) ? $tmp = $table->urlparam4twig : $tmp = ""; echo $tmp; ?>" size="250" /><br><br>
    <b>Method of API-request (best choice in almost any case: curlget / curlpost / curlput):</b><br>
    <?php
      if ("add"==$type) {
        $method = "curlget"; 
      } else  {
        $method = "get"; 
      }
      if (isset($table->method)) { $method = $table->method; } 
    ?>
    <table border="0" width="50%">
    <tr><td>
    <input type="radio" name="method" value="curlget" <?php ($method=="curlget") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?> /> <a href="https://curl.haxx.se/docs/httpscripting.html" title="default setting: together with the curloptions this should work in almost any cases" target="_blank">CURL-GET</a>
    <br><input type="radio" name="method" <?php ($method=="curlpost") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?> value="curlpost" /> CURL-POST
	<br><input type="radio" name="method" <?php ($method=="curlput") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?> value="curlput" /> <a href="https://restfulapi.net/rest-put-vs-post/" target="_blank">CURL-PUT</a>
    </td><td>
    <input type="radio" name="method" <?php ($method=="get") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?>  value="get" /> <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get" target="_blank">WP-GET</a>
    <br><input type="radio" name="method" <?php ($method=="post") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?> value="post" /> <a href="https://codex.wordpress.org/Function_Reference/wp_remote_post" target="_blank">WP-POST</a>
    </td><td>
    <input type="radio" name="method" <?php ($method=="rawget") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?> value="rawget" /> <a href="http://php.net/manual/de/function.file-get-contents.php" target="_blank">PHP-RAWGET</a>
    <br><input type="radio" name="method" <?php ($method=="rawpost") ? $tmp = " checked " : $tmp = ""; echo $tmp; ?> value="rawpost" /> <a href="http://php.net/manual/de/function.stream-context-create.php" target="_blank">PHP-RAWPOST</a>
    </td></tr></table>
    <hr><b>Curloptions:</b><br>
    <a href="https://json-content-importer.com/support/simple-example-of-wordpress-shortcode/" target="_blank">Example</a>: CURLOPT_HTTPAUTH=CURLAUTH_BASIC;CURLOPT_TIMEOUT=30;CURLOPT_HTTPHEADER=a:{{urlparam.VAR1}}##c:d;CURLOPT_POSTFIELDS=e:f##{"g":"h"}##i:j<br>
     <?php
      if (isset($table->curloptions) ){
        $tmp = $table->curloptions;
        $tmp = htmlspecialchars($tmp, ENT_QUOTES);
      } else {
        $tmp = "";
      }
     ?>
    <input type="text" name="curloptions" placeholder="CURLOPT_HTTPAUTH=CURLAUTH_BASIC;CURLOPT_TIMEOUT=30;CURLOPT_HTTPHEADER=a:b##c:d;CURLOPT_POSTFIELDS=e:f##{&quot;g&quot;:&quot;h&quot;}##i:j" value = "<?php echo $tmp; ?>" size="250" />

<?PHP 
			$jci_pro_hide_deprecated_value = get_option('jci_pro_hide_deprecated');
			if ($jci_pro_hide_deprecated_value=="no") {
?>
    <hr><b>Postpayload (can be done by curloptions CURLOPT_POSTFIELDS or direct through this field if curl is not used):</b><br>
    Add data to header: Some POST-APIs need inputdata like that. "JSON_PAYLOAD" must contain valid JSON! If "JSON_PAYLOAD" contains strings like "POSTGET_something" where something is a letter or number this is replaced by the value of the "something" GET / POST parameter. If "JSON_PAYLOAD" must contain ] or [ use #BRO# ("bracket-open") and #BRC# ("bracket-close") instead, otherwise wordpress gets confused.
    <br>
     <?php
      if (isset($table->postpayload) ){
        $tmp = $table->postpayload;
        $tmp = htmlspecialchars($tmp, ENT_QUOTES);
      } else {
        $tmp = "";
      }
     ?>   
    <input type="text" name="postpayload" placeholder="valid JSON string" value = "<?php echo $tmp; ?>" size="250" />
    <hr><b>Postbody (used only if WP-POST is the selected method!):</b><br>
    Add data to the http-body: Some POST-APIs need inputdata like that. If ] or [ is in the JSON use #BRO# ("bracket-open") and #BRC# ("bracket-close") instead, otherwise wordpress gets confused.
    <br>
     <?php
      if (isset($table->postbody) ){
        $tmp = $table->postbody;
        $tmp = htmlspecialchars($tmp, ENT_QUOTES);
      } else {
        $tmp = "";
      }
     ?>
    <input type="text" name="postbody" placeholder="valid JSON string" value = "<?php echo $tmp; ?>" size="250" />
<?PHP 
			}
?>
    <hr><b>Cachetime:</b> Set the cachetime for this URL to n seconds, regardless of the settings in the plugin-option, even when caching is switched off there.
    <br>
    <input type="text" name="cachetime" placeholder="Number of seconds" value = "<?php isset($table->cachetime) ? $tmp = $table->cachetime : $tmp = ""; echo $tmp; ?>" size="20" />

    <hr><b>Timeout for URL-retrieving (if possible do not use. use "CURLOPT_TIMEOUT=..." with CURL-GET or CURL-POST):</b> Who many seconds for loading url till timeout?
    <br>
    <input type="text" name="urlgettimeout" placeholder="Number of seconds" value = "<?php isset($table->urlgettimeout) ? $tmp = $table->urlgettimeout : $tmp = ""; echo $tmp; ?>" size="20" />
    <hr>
	<?php
	if ($showonlyurlsettings) {
		echo '<input type="hidden" name="parser" value="twig332adj">';
		echo '<input type="hidden" name="basenode" value="" />';
	} else {
	?>
    <b>Parser / Template-Engine:</b><br>
		<?PHP 
			$lastesttwigselected = FALSE;
			$tmp = "";
			if ($type=="add") {
				$tmp = " checked "; 
			} else {
				if (isset($table->parser) && ($table->parser=="twig332adj")) {
					$tmp = " checked "; 
					$lastesttwigselected = TRUE;
				}
			}
			echo '<input type="radio" name="parser" '.$tmp.' value="twig332adj" />JCI-twig 3.3.2  (adjusted version: if twig-lib from other Plugins causes conflicts) ';
			echo '<br>';

			if (isset($table->parser) && ($table->parser=="twig332")) {					$tmp = " checked "; 				} else { $tmp = "";  } 
			echo '<input type="radio" name="parser" '.$tmp.' value="twig332" />twig 3.3.2 (must if you use PHP Version 8) ';
			echo '<br>';
			
			$jci_pro_hide_deprecated_value = get_option('jci_pro_hide_deprecated');
			$showoldparser = TRUE;
			if ($jci_pro_hide_deprecated_value=="yes" && (!$lastesttwigselected)) {				$showoldparser = FALSE;			}
			if ($showoldparser) {
				$tmp = "";
				if (isset($table->parser) && ($table->parser=="twig3")) {					$tmp = " checked "; 				} else { $tmp = "";  } 
				echo '<input type="radio" name="parser" '.$tmp.' value="twig3" />twig 3 (DEPRECATED)';
				echo '<br>';
				$tmp = "";
				if (isset($table->parser) && ($table->parser=="twig243")) {				$tmp = " checked "; 			} else { $tmp = "";  } 
				echo '<input type="radio" name="parser" '.$tmp.' value="twig243" />twig 2.4.3 (DEPRECATED)';
				echo '<br>';
			
				$tmp = "";
				if (isset($table->parser) && ($table->parser=="twig")) {				$tmp = " checked "; 			} else { $tmp = "";  } 
				echo '<input type="radio" name="parser" '.$tmp.' value="twig" />twig 1 (DEPRECATED)';
				echo '<br>';

				$tmp = "";
				if ($type=="change") {
					if (isset($table->parser)) {
						if ($table->parser=="jci") {						$tmp = " checked ";					} else { $tmp = "";  } 
					} else {
						$tmp = " checked "; 
					}
				}
				echo '<input type="radio" name="parser" '.$tmp.' value="jci" />JCI (like free plugin, DEPRECATED with the PRO-Plugin!)';
			}
			if ($jci_pro_hide_deprecated_value=="no") {
				?>
				<hr>
				<b>JSON basenode (not needed with twig!):</b> If not defined in the Shortcode. Try this to display JSON-data structure: <a href="http://jsonviewer.stack.hu/" target="_blank">jsonviewer.stack.hu</a>:
				<br>
				<input type="text" name="basenode" placeholder="check JSON-data structure" value = "<?php isset($table->basenode) ? $tmp = $table->basenode : $tmp = ""; echo $tmp; ?>" size="50" /><br><br>
				<?PHP	
			} else {
				echo '<input type="hidden" name="basenode" value="" />';
			}
    echo "<hr>";
	}
	button_save_update($type, $table, $showonlyurlsettings); ?>  
    </form>
    <div style = "font-weight:bold;color:#4CC417;font-size:14px;padding:5px;"><?php echo $errormsg; ?></div>
	</li></ul>
<?PHP
  }


function button_save_update($type, $table, $showonlyurlsettings) {
    if ($type=="change") {
		echo '<input class="button-primary" type="submit" name="update" value="Save changed template" id="update" />';
		echo '&nbsp;&nbsp;<a class="button-primary" href="admin.php?page=jciprotemplateslug">Back to template list</a>';
		echo '<input type="hidden" name="update_id" value="';
		if (isset($table->id)) {
			$tmp = $table->id;
		} else {
			$tmp = ""; 
		}
		echo $tmp.'" />';
    }
    if ($type=="add") {
		echo '<input type="hidden" name="submit" />';
		echo '<input type="hidden" name="type" value="change" />';
		if ($showonlyurlsettings) {
			$butname = "Save new URL in template";
		} else {
			$butname = "Save new template";
		}
		echo '<input class="button-primary" type="submit" value="'.$butname.'" name="'.$butname.'" id="tb_add" />';
    }
}


function register_jci_pro_showapis() {
	echo "<h2>Stored JSON-Access-Sets - ";
	echo "<a href=?page=jciprostep1getjsonslug>Add or modify a JSON-Access-Set</a></h2><hr>";
	$apiitems = get_option( 'jci_pro_api_access_items' );
	$apiitemsArr = json_decode($apiitems, TRUE);
	
	
	$act = @$_GET["act"];
	$mid = @$_GET["mid"];
	if (isset($_GET["act"]) && isset($_GET["mid"])) {
		if ("del"==$act) {
			unset($apiitemsArr[$mid]);
		}
		if ("act"==$act) {
			$apiitemsArr[$mid]["status"] = "active";
		}
		if ("ina"==$act) {
			$apiitemsArr[$mid]["status"] = "inactive";
		}
	}
		$save_storeapirequestval_str = json_encode($apiitemsArr);
		update_option('jci_pro_api_access_items', $save_storeapirequestval_str);
		
	if (count($apiitemsArr)>0) {
		echo "<table border=1 cellpadding=10>";
		echo "<tr bgcolor=white><td>";
		echo "<b>Load</b>";
		echo "</td><td>";
		echo "<b>Status</b>";
		#echo "</td><td>";
		#echo "<b>Name</b>";
		echo "</td><td>";
		echo "<b>Date</b>";
		echo "</td><td>";
		echo "<b>URL</b>";
		echo "</td><td>";
		echo "<b>Timeout</b>";
		echo "</td><td>";
		echo "<b>method</b>";
		echo "</td><td>";
		echo "<b>technique</b>";
		echo "</td><td>";
		echo "<b>Header</b>";
	#	echo "</td><td>";
	#	echo "<b>JSON</b>";
		echo "</td><td>";
		echo "<b>Delete?</b>";
		echo "</td></tr>";
		$namechecker = Array();
		foreach($apiitemsArr as $i) {
			echo "<tr><td>";
			
			echo "<form action=admin.php?page=jciprostep1getjsonslug method=post>";
			echo '<input type=hidden name="accset" value="'.$i["md5id"].'">';
			echo '<input type=hidden name="type" value="loadaccset">';
			$submitButtonValue = $i["nameofjas"]." - Load this JSON-Access-Set";
			submit_button($submitButtonValue, 'primary', 'loadjas', FALSE); 
			@$namechecker[trim($i["nameofjas"])]++;
			if ($namechecker[$i["nameofjas"]]>1) {
				echo "<br><font color=red>Attention: Name already used - rename it please</font>";
			}
			echo "</form>";
			echo "</td><td>";
			
			$status = @$i["status"];
			if (empty($status)) {
				$status = "active";
			}
			echo "Show this Set on lists with  all JSON-Access-Sets:<br>";
			if ("inactive"==$status) {
				echo "<font color=red>no</font> - ";
				echo '<a href=?page=jciprostep1getjsonslug&act=act&mid='.$i["md5id"].'>switch to yes</a>';
			} else {
				echo "<font color=green>yes</font> - ";
				echo '<a href=?page=jciprostep1getjsonslug&act=ina&mid='.$i["md5id"].'  title="">switch to no</a>';
			}
			#echo "</td><td>";
			#echo $i["nameofjas"];
			echo "</td><td>";
			echo date("F d.Y, H:i:s", $i["time"]);
			echo "</td><td>";
			echo $i["set"]["jciurl"];
			echo "</td><td>";
			echo $i["set"]["timeout"];
			echo "</td><td>";
			echo $i["set"]["method"];
			echo "</td><td>";
			echo $i["set"]["methodtech"];
			echo "</td><td>";
			$hn = $i["set"]["noheader"];
			for ($j = 1; $j <= $hn ; $j++) {
				if (!empty(@$i["set"]["headerl".$j]) || !empty(@$i["set"]["headerr".$j])) {
					echo $i["set"]["headerl".$j]." : ".$i["set"]["headerr".$j]."<br>";
				}
			}
			echo "</td><td>";
	#		$json = json_encode($i["json"]);
	#		$jsonout = substr($json, 0, 80)."...";
	#		echo $jsonout;
	#		echo "</td><td>";
			# check if a Access-Set is connected to a Use-Set!
			$is_this_access_set_connected_to_an_use_set = FALSE;
			$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);
			if (count($jci_pro_api_use_items)>0) {
				foreach($jci_pro_api_use_items as $k => $v) {
						if ($i["md5id"]==$v["selectejas"]) {
							$is_this_access_set_connected_to_an_use_set = TRUE;
						}
						#var_Dump($v);
				}
			}
			if ("deleted"==$status) {
				echo "<font color=red>deleted</font>";
			} else {
				if ($is_this_access_set_connected_to_an_use_set) {
					echo 'This JSON-Access-Set can\'t be deleted:<br>It is used at a JSON-Access-Set<p>';
					jcipro_load_json_use_set($i["md5id"], $k, $v, "Load the connected JSON-Use-Set");
					 
				} else {
					echo '<a href=?page=jciprostep1getjsonslug&act=del&mid='.$i["md5id"].' onclick = "if (! confirm(\'Really deleting this Access-Set?\')) { return false; }" title="delete JSON-Access-Set permanently">delete this Access-Set</a>';
				}
			}
			echo "</td></tr>";
		}
	} else {
		echo "No JSON-Access-Sets defined";
	}
	echo "</table>";
}


function jcipro_clear_httpheaderkey($key) {
	return preg_replace("/[^a-z0-9\-]/i", "", $key);  #remove spaces, specialchars etc. 
}

function jcipro_divbox($text, $dohtmlentities = TRUE, $bgcolor = "black", $fontcolor= "white", $idofbox = "") {
	$out = "<div id=\"$idofbox\" style=\"background-color: $bgcolor; color: $fontcolor; padding: 10px;\">";
	if ($dohtmlentities) {
		$out .= htmlentities($text);
	} else {
		$out .= $text;
	}
	$out .= "</div>";
	return $out;
}

function register_jci_pro_step1getjson($tm_data) {

	echo "<h1>Step 1: Get JSON - Insert URL, Authentication etc. for the API and check response</h1>";
	#var_dump($_POST);# updatejas
	#echo "<hr>";


	###### 
	# manage JSON-Access-Sets: show, delete, activate, inactivate
	if (
		isset($_POST['manage'])
		|| isset($_GET['del'])
		|| isset($_GET['act'])
		|| isset($_GET['ina'])
	) { 
		register_jci_pro_showapis();
		return TRUE;
   }

   	###### 
	# update JSON-Access-Sets
	if (isset($_POST['updatejas'])) { 
		#echo json_encode($_POST);
		$accset = $_POST['accset'];
		$inp_nameofjas = $_POST['nameofjas'];
		if (empty($inp_nameofjas)) {
			#$inp_nameofjas = substr(md5(time()), 0, 15);
			$inp_nameofjas = jcipro_calc_unique_id(time());
		}
		#echo "update: ".$accset."<br>";
		#echo "nameofjas: ".$nameofjas."<br>";
		$inp_set = urldecode($_POST['storeapirequestval']);
		$inp_storeapirequestval = json_decode($inp_set, TRUE);
		$inp_storeapirequestjson = json_decode(urldecode($_POST['storeapirequestjson']), TRUE);
		#echo "storeapirequestval: ".json_encode($inp_storeapirequestval)."<br>";
		#echo "storeapirequestjson: ".json_encode($inp_storeapirequestjson)."<br>";

		$apiitems = get_option( 'jci_pro_api_access_items' );
		$apiitemsArr = json_decode($apiitems, TRUE);
		$apiitemsArrNew = Array();
		#echo "<hr>stored: ".json_encode($apiitemsArr)."<br>";
		foreach($apiitemsArr as $t) {
			#echo "md5id: ".$t['md5id']."<br>";
			if (isset($t['md5id']) && (""!=$t['md5id'])) {
				#echo "md5id: ".$t['md5id']."<br>";
				#$apiitemsArrNew[$t['md5id']]["json"] = $inp_storeapirequestjson;
				$apiitemsArrNew[$t['md5id']]["time"] = time();
				$apiitemsArrNew[$t['md5id']]["status"] = $apiitemsArr[$accset]["status"];
				$apiitemsArrNew[$t['md5id']]["md5id"] = $t['md5id'];
				if ($t['md5id']==$accset) {
					$apiitemsArrNew[$t['md5id']]["set"] = $inp_storeapirequestval;
					$apiitemsArrNew[$t['md5id']]["nameofjas"] = trim($inp_nameofjas);
				} else {
					$apiitemsArrNew[$t['md5id']]["set"] = $apiitemsArr[$t['md5id']]["set"] ;
					$apiitemsArrNew[$t['md5id']]["nameofjas"] = trim($apiitemsArr[$t['md5id']]["nameofjas"] );
					
				}
			}
		}

		$save_storeapirequestval_str = json_encode($apiitemsArrNew);
		update_option('jci_pro_api_access_items', $save_storeapirequestval_str);
		register_jci_pro_showapis();
		return TRUE;
	}

   	###### 
	# store JSON-Access-Set
	if (isset($_POST['storeapirequest']) && ("save"==$_POST['storeapirequest'])) { 
	
		$storeapirequest = $_POST['storeapirequest'];

		# get existing Sets
		$apiitems = get_option( 'jci_pro_api_access_items' );
		$apiitemsArr = json_decode($apiitems, TRUE);

		################## save new or update old
		#if ("save"==$storeapirequest) {
			#echo "<h2>Save JSON-Access-Set</h2>";
			$inp_set = urldecode($_POST['storeapirequestval']);
			$inp_storeapirequestval = json_decode($inp_set, TRUE);
			$inp_storeapirequestjson = json_decode(urldecode($_POST['storeapirequestjson']), TRUE);

			$inp_nameofjas = urldecode($_POST['nameofjas']);
			if (empty($inp_nameofjas)) {
				#$inp_nameofjas = substr(md5(time()), 0, 15);
				$inp_nameofjas = jcipro_calc_unique_id(time());
			}

			#$inp_id = md5($inp_set);
			$inp_id = jcipro_calc_unique_id($inp_set);
			$apiitemsArr[$inp_id]["set"] = $inp_storeapirequestval;
			$apiitemsArr[$inp_id]["json"] = $inp_storeapirequestjson;
			$apiitemsArr[$inp_id]["time"] = time();
			$apiitemsArr[$inp_id]["status"] = "active";
			$apiitemsArr[$inp_id]["md5id"] = $inp_id;
			$apiitemsArr[$inp_id]["nameofjas"] = $inp_nameofjas;
			#echo $inp_nameofjas;
		
			$save_storeapirequestval_str = json_encode($apiitemsArr);
			#echo urldecode($_POST['storeapirequestval'])."<hr>";
			#echo urldecode($_POST['storeapirequestjson'])."<hr>";
			#var_Dump( $save_storeapirequestval);
			update_option('jci_pro_api_access_items', $save_storeapirequestval_str);
		#}
		################## save end
		register_jci_pro_showapis();
		return TRUE;
	}
		
   	###### 
	# show JSON-Access-Set and the result of the request
	
		$meth["get"] = "GET";
		$meth["post"]= "POST";
		$meth["put"] = "PUT";
		$methtech["curl"] = "CURL";
		$methtech["php"]= "PHP";
		$methtech["wp"] = "Wordpress";

		$method["curlget"] = "curlget";
		$method["curlpost"] = "curlpost";
		$method["curlput"] = "curlput";
		$method["phpget"] = "rawget";
		$method["phppost"] = "rawpost";
		$method["wpget"] = "get";
		$method["wppost"] = "post";

		$dataformat["json"] = "JSON";
		$dataformatshortcode["json"] = "";
		$dataformat["xml"] = "XML";
		$dataformatshortcode["xml"] = " inputtype=xml ";
		$dataformat["csv"] = "CSV/TXT";
		$dataformatshortcode["csv"] = " inputtype=csv ";

	#################
	# set form with POST-input
		#var_Dump($_POST);
		$isnewjas = TRUE;
		if (isset($_POST['noheader'])) { 
			$noheader = $_POST['noheader']; 
		} else {
			$noheader = 3;
		}
		$formdata["nameofselectedjas"] = jcipro_calc_unique_id(time());
		if (isset($_POST['nameofselectedjas'])) {  		
			$formdata["nameofselectedjas"] = $_POST['nameofselectedjas']; 	
			$isnewjas = FALSE;
		}
		if (isset($_POST['accset'])) {  		
			$isnewjas = FALSE;
		}

		$formdata["noheader"] = $noheader;
		#$formdata["nameofselectedjas"] = substr(md5(time()), 0, 15); 
		
		$formdata["cbheadaccess"] = ""; if (isset($_POST['cbheadaccess'])) {  		$formdata["cbheadaccess"] = $_POST['cbheadaccess']; 	}
		$formdata["headaccesskey"] = "Access"; if (isset($_POST['headaccesskey'])) {  		$formdata["headaccesskey"] = $_POST['headaccesskey']; 	}
		$formdata["headaccessval"] = "json/application"; if (isset($_POST['headaccessval'])) {  		$formdata["headaccessval"] = $_POST['headaccessval']; 	}
		

		$formdata["cbheaduseragent"] = ""; if (isset($_POST['cbheaduseragent'])) {  		$formdata["cbheaduseragent"] = $_POST['cbheaduseragent']; 	}
		$formdata["headuseragentkey"] = "User-Agent"; if (isset($_POST['headuseragentkey'])) {  		$formdata["headuseragentkey"] = $_POST['headuseragentkey']; 	}
		$formdata["headuseragentval"] = "Mozilla"; if (isset($_POST['headuseragentval'])) {  		$formdata["headuseragentval"] = $_POST['headuseragentval']; 	}

		$formdata["cbheadoauth2"] = ""; if (isset($_POST['cbheadoauth2'])) {  		$formdata["cbheadoauth2"] = $_POST['cbheadoauth2']; 	}
		$formdata["headoauth2key"] = "Authentication"; if (isset($_POST['headoauth2key'])) {  		$formdata["headoauth2key"] = jcipro_clear_httpheaderkey($_POST['headoauth2key']); 	}
		$formdata["headoauth2val"] = "Bearer [jsoncontentimporterpro nameoftemplate=gettoken]"; if (isset($_POST['headoauth2val'])) {  		$formdata["headoauth2val"] = $_POST['headoauth2val']; 	}
		#echo "PP: ".$_POST['headoauth2val']."<br>"; #exit;
		
		$nooffilledheader = 0;
		for ($i = 1; $i <= $noheader; $i++) {
			if (!empty(@$_POST["headerl".$i]) || !empty(@$_POST["headerr".$i])) {
				$nooffilledheader++;
				@$formdata["headerl".$nooffilledheader] = $_POST['headerl'.$i];
				@$formdata["headerr".$nooffilledheader] = $_POST['headerr'.$i];
			}
		}
		if ($nooffilledheader==0) { 
			$nooffilledheader = 4; 
		}		
		@$formdata["headernooffilledheader"] = $nooffilledheader;
		if (isset($_POST['method'])) { 
			$methodTmp = $_POST['method']; 
		} else {
			$methodTmp = "get";
		}
	
		if (isset($_POST['methodtech'])) { 
			$methodtechTmp = $_POST['methodtech']; 
		} else {
			$methodtechTmp = "curl";
		}

		if (isset($_POST['indataformat'])) { 
			$indataformat = $_POST['indataformat']; 
		} else {
			$indataformat = "json";
		}
		if (isset($_POST['csvdelimiter'])) {  		$csvdelimiter = $_POST['csvdelimiter']; 	} else {		$csvdelimiter = ","; 	}
		if (isset($_POST['csvline'])) { 	$csvline = $_POST['csvline']; 	} else {		$csvline = "#LF#";	}
	
		if (isset($_POST['csvenclosure'])) { 	$csvenclosure = $_POST['csvenclosure']; 	} else {		$csvenclosure = '#QM#';	}
		if (isset($_POST['csvskipempty']) && ("y"==$_POST['csvskipempty'])) { 	$csvskipempty = "y"; 	} else {		$csvskipempty = "n";	}
		if (isset($_POST['csvescape'])) { 	$csvescape = $_POST['csvescape']; 	} else {		$csvescape = "#BS#";	}

		# put only with curl, not with wp and php
		$errormsg = "";
		if ( ("put"==$methodTmp) && ("wp"==$methodtechTmp || "php"==$methodtechTmp)) {
			$errormsg = "PUT can be done only with CURL: Set CURL instead of ".$methtech[$methodtechTmp];
			$methodtechTmp = "curl";
			$methodTmp = "put";
		}
		$formdata["method"] = $methodTmp;
		$formdata["methodtech"] = $methodtechTmp;
		$formdata["indataformat"] = $indataformat;
		$formdata["csvdelimiter"] = stripslashes($csvdelimiter);
		$formdata["csvline"] = stripslashes($csvline);
		$formdata["csvenclosure"] = stripslashes($csvenclosure);
		$formdata["csvskipempty"] = FALSE;
		if ($csvskipempty=="y") {
			$formdata["csvskipempty"] = TRUE;
		}
		$formdata["csvescape"] = stripslashes($csvescape);

		$postPayload = ""; 
		if (isset($_POST['payload'])) { 
			#$postPayload = stripslashes(htmlentities($_POST['payload'])); 
			$postPayload = stripslashes($_POST['payload']); 
		}
		$formdata["payload"] = $postPayload;
	
		$selectedmethod = $method[$methodtechTmp.$methodTmp];
		if (empty($selectedmethod)) {
			$selectedmethod = "curlget  ".$methodtechTmp.$methodTmp;
		}

		$httpsverify = 1;  # check!
		if (isset($_POST['httpsverify']) && 2 == $_POST['httpsverify']) { 
			#checkbox NOT active, no check
			$httpsverify = 2;
		}
		$formdata["httpsverify"] = $httpsverify;

		$ignorehttpcode = @$_POST["ignorehttpcode"];
		if (isset($_POST['jciurl'])) { 
			$jciurl = stripslashes($_POST['jciurl']); 
		} else {
			$jciurl = plugin_dir_url(__FILE__).'json/example1.json';
		}
		$formdata["jciurl"] = $jciurl;

		if (isset($_POST['timeout'])) { 
			$urlgettimeout = $_POST['timeout']; 
		} else {
			$urlgettimeout = 5;
		}
		$formdata["timeout"] = $urlgettimeout;
		#$postPayload = "";
	##}
	#var_Dump( $formdata);

		if (isset($_POST['accset'])) { 
			#$accset = $_POST['accset']; 
			$formdata["accset"] = $_POST['accset']; 
		}
	

	echo '<table border=0>';
	$jci_pro_api_access_items = json_decode(get_option('jci_pro_api_access_items'), TRUE);
	$thereisnojac = TRUE;
	$thereisnoactivejac = TRUE;
	if (is_array($jci_pro_api_access_items) && count($jci_pro_api_access_items)>0) {
		$thereisnojac = FALSE;
		foreach($jci_pro_api_access_items as $t) {
			if ($t["status"]=="active") {
				$thereisnoactivejac = FALSE;
			}
		}
	}
	
	#var_Dump($formdata);
	#$accset = "";
	if (isset($jci_pro_api_access_items)) {
		#################
		# show existing Sets
		echo '<tr><td style="padding: 10px" bgcolor=white>';
		if ($thereisnojac) {
			echo "<h2>There is no stored JSON-Access-Set yet. Create one below!</h2>";
		} else if ($thereisnoactivejac) {
			echo "<h2>There are stored, but no active JSON-Access-Sets. Activate one or create one below!</h2>";
			echo "<form action=admin.php?page=jciprostep1getjsonslug method=post>";
			$submitButtonValue = "Activate a stored JSON-Access-Set";
			submit_button($submitButtonValue, 'large', 'manage', FALSE); 
			echo "</form>";
		} else {
			echo "<form action=admin.php?page=jciprostep1getjsonslug method=post>";
			echo "Load stored JSON-Access-Sets: ";
			echo "<select name=accset>";
			$i = 1;
			$loadaccset = TRUE;
			if (isset($_POST['testrequest'])) {
				echo "acc: $accset";
				$loadaccset = FALSE; # do not load the set from the stored data
			#} else {
				#$accset = $_POST['accset']; 
				#$accset = 1;
			}
			foreach($jci_pro_api_access_items as $t) {
				$sel = "";
				if ($t['md5id']==$formdata["accset"]) { #$accset) {
					$sel = " selected ";
					if ($loadaccset) {
						# load existing data
						$jciurl = $t['set']['jciurl'];
						$formdata["jciurl"] = $jciurl;
						$formdata["method"] = $t['set']['method'];
						$formdata["methodtech"] = $t['set']['methodtech'];
						$postPayload = $t['set']['payload'];
						$formdata["payload"] = $postPayload;
						$formdata["headernooffilledheader"] = $t['set']['headernooffilledheader'];
						for ($i = 1; $i <= $formdata["headernooffilledheader"]; $i++) {
							$formdata["headerl".$i] = jcipro_clear_httpheaderkey($t['set']['headerl'.$i]);
							$formdata["headerr".$i] = $t['set']['headerr'.$i];
						}
						$formdata["cbheadoauth2"] = $t['set']['cbheadoauth2'];
						$formdata["headoauth2key"] = jcipro_clear_httpheaderkey($t['set']['headoauth2key']);
						$formdata["headoauth2val"] = $t['set']['headoauth2val'];
						$formdata["cbheadaccess"] = $t['set']['cbheadaccess'];
						$formdata["headaccesskey"] = jcipro_clear_httpheaderkey($t['set']['headaccesskey']);
						$formdata["headaccessval"] = $t['set']['headaccessval'];
						$formdata["cbheaduseragent"] = $t['set']['cbheaduseragent'];
						$formdata["headuseragentkey"] = jcipro_clear_httpheaderkey($t['set']['headuseragentkey']);
						$formdata["headuseragentval"] = $t['set']['headuseragentval'];
						$formdata["timeout"] = $t['set']['timeout'];
						$formdata["indataformat"] = $t['set']['indataformat'];
						$formdata["csvdelimiter"] =  $t['set']['csvdelimiter'];
						$formdata["csvline"] =  $t['set']['csvline'];
						$formdata["csvenclosure"] =  $t['set']['csvenclosure'];
						$formdata["csvskipempty"] = FALSE;
						$csvskipempty = "n";
						if ($t['set']['csvskipempty']) {
							$formdata["csvskipempty"] = TRUE;
							$csvskipempty = "y";
					}
						$formdata["csvescape"] = stripslashes($csvescape);
						$formdata["httpsverify"] = $t['set']['httpsverify'];
						$nameofselectedjas = $t['nameofjas'];
						$formdata["nameofselectedjas"] = $nameofselectedjas;
						#$formdata["accset"] = $accset;
					}
				}
				if ("active"==$t['status']) {
					# show only active sets
					echo "<option value=".$t['md5id']." $sel>";
					echo $t['nameofjas'];
					echo "</option>";
				}
			}
			echo "</select>";
			echo '<input type=hidden name="type" value="loadaccset">';
			$submitButtonValue = "Load JSON-Access-Set";
			submit_button($submitButtonValue, 'primary', 'load', FALSE); 
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$submitButtonValue = "Manage stored JSON-Access-Sets";
			submit_button($submitButtonValue, 'large', 'manage', FALSE); 
			echo "</form>";
		}
		echo "</td></tr>";
	}
	
	
	#######
	# show set
	echo '<tr><td style="padding: 10px" bgcolor=white>';
	if ($isnewjas) {
		echo "<h2>Create a new JSON-Access-Set: You might use the Test-URL or another URL</h2>";
		
	
		
		
	} else {
		echo "<h2>Change the JSON-Access-Set \"".$formdata["nameofselectedjas"]."\"</h2>";
	}
	
	if (!empty($errormsg)){
		echo '<tr><td style="padding: 10px" bgcolor=red>';
		echo "<font color=white>$errormsg</font>";
		echo "</td></tr>";
		echo '<tr><td style="padding: 10px" bgcolor=white>';
	}

	$httpcode  = -1;
	#$formdata["jciurl"] = $jciurl;
	
$httpr[200] = "ok";
$httpr[301] = "301 Moved Permanently";
$httpr[302] = "302 Found (Previously Moved temporarily)";
$httpr[400] = "400 Bad Request";
$httpr[401] = "401 Unauthorized (RFC 7235)";
$httpr[403] = "403 Forbidden";
$httpr[404] = "404 Not Found";
$httpr[405] = "405 Method Not Allowed";
$httpr[500] = "500 Internal Server Error";
$httpr["cache"] = "JSON loaded from local cache";

    #if (isset($_POST['payload'])) { 
	#	$postPayload = stripslashes(htmlentities($_POST['payload'])); 
	#}
	#$formdata["payload"] = $postPayload;
	
	$resu = "";

	
	if (isset($_POST['testrequest'])) {
		$nameofselectedjas = $_POST["nameofselectedjas"];
		$nameofjas =  $nameofselectedjas;
		if (empty($nameofjas)) {
			$nameofjas = jcipro_calc_unique_id(time());
		}

		#######
		# do request
		
		# BEGIN param
		$debugModeIsOn = FALSE;
		$debugLevel = 10;
		# END: param
		
		# BEGIN buildrequest
		require_once plugin_dir_path( __FILE__ ) . 'lib/lib_request.php';
		$jci_request_handler = new jci_request_prepare($formdata, $methodTmp, $formdata["methodtech"]);
		$curloptions = $jci_request_handler->getCurlOptionsString();
		$curloptions4Request = $jci_request_handler->getCurlOptions4Request($curloptions);
		# END buildrequest


		# BEGIN cache: The  retrieved JSON from the JSON-Access-Set is cached
		$cacheEnable = FALSE;#TRUE;
		if ($cacheEnable) {
			$defaultcachepath = WP_CONTENT_DIR . "/cache/jsoncontentimporterpro/";
			$cachepath = get_option('jci_pro_cache_path');
			if (empty($cachepath)) {
				$cachepath = $defaultcachepath;
				update_option('jci_pro_cache_path', $cachepath);
			}
			$cacheFileFingerPrint = $nameofjas."-".json_encode($formdata);
			$cacheFile = $cachepath.md5($cacheFileFingerPrint).".cgi";
			$cacheExpireTime = 86400; # caching for 24 hrs
		} else {
			$cacheFile = "";
			$cacheExpireTime = 0;
		}
		# END Cache 

		# BEGIN LOAD
		if (
			(!class_exists('FileLoadWithCachePro'))
			|| (!class_exists('JSONdecodePro'))
		) {
			require_once plugin_dir_path( __FILE__ ) . '/class-fileload-cache-pro.php';
		}
		$header = "";
		$urlencodepostpayload = "";
		$encodingofsource = "";
		$httpstatuscodemustbe200 = "no";
		$auth = "";
		$showapiresponse = FALSE;
		$followlocation = TRUE; # follow 301 etc.
		
        $fileLoadWithCacheObj = new FileLoadWithCachePro(
            $formdata["jciurl"], $formdata["timeout"], $cacheEnable, $cacheFile, $cacheExpireTime, $selectedmethod, NULL, '', '',
            $formdata["payload"], $header, $auth, $formdata["payload"],
            $debugLevel, $debugModeIsOn, $urlencodepostpayload, $curloptions4Request,
            $httpstatuscodemustbe200, $encodingofsource, $showapiresponse, $followlocation 
            );
        $fileLoadWithCacheObj->retrieveJsonData();
		$receivedData = $fileLoadWithCacheObj->getHttpResponse();
		$httpcode = $fileLoadWithCacheObj->getErrormsgHttpCode();
		#echo "testrequest selectedmethod: $selectedmethod<br>receivedData: $receivedData<br>httpcode: $httpcode<br>";
		# END LOAD
		
		$httplev = "";
		if (!empty($httpcode)) {
			$httplev = $httpr[$httpcode];
			if (empty($httplev)) {
				$httplev = "Error-Code: ".$httpcode;
			}
		}
		
		$shortcodeparam = "parser=\"twig332adj\" ";
		$errorcol = "black";
		$okcol = "#afa";
		if (200!=$httpcode && "cache"!=$httpcode) {
			$formdata["httpcode"] = 1;
			$formdata["ignorehttpcode"] = $ignorehttpcode;
			$shortcodeparam .= " httpstatuscodemustbe200=\"no\"";
			$errorcol = "red";
			$okcol = "#DDD";
		}
		if ("cache"==$httpcode) {
			$errorcol = "black";
		}
		if (!empty($httplev)) {
			$resu .= "<strong><font color=$errorcol>API-answer:</font></strong> ".$httplev;
			if (!preg_match("/$httpcode/", $resu)) {
				$resu .= " (http-Code: $httpcode)";
			}
		}

		jci_test_form($formdata, $meth, $methtech, $dataformat);
		echo '</td></tr>';
		echo '<tr><td style="padding: 10px" bgcolor='.$okcol.'>';
	
		$feedData = $fileLoadWithCacheObj->getFeeddataWithoutpayloadinputstr();
		
		#######
		# did we get JSON?
		$convertJsonNumbers2Strings = TRUE; # default!
        $jsonDecodeObj = new JSONdecodePro($feedData, TRUE, $debugLevel, $debugModeIsOn, $convertJsonNumbers2Strings, $cacheFile, $fileLoadWithCacheObj->getContentType(), 
			$formdata["indataformat"], $formdata["csvdelimiter"], $formdata["csvline"],
			$formdata["csvenclosure"], $formdata["csvskipempty"], $formdata["csvescape"]
			);

        $vals = $jsonDecodeObj->getJsondata();
		#echo "<textarea>".json_encode($vals)."</textarea>";
		
		$resu .= "<hr><strong>Valid JSON received?</strong> ";
		if ($jsonDecodeObj->getIsAllOk()) {
			if (is_null(@$vals["nojsonvalue"])) {
				$resu .= "decoding ok, we got JSON-data!";
			} else {
				$resu .= "decoding failed, API-answer was packed into nojsonvalue-JSON";
			}
		} else {
			$resu .= "decoding due to invalid JSON failed. Check structure and encoding of JSON-data";
		}
		echo $resu.' - <a href="#" id="showapianswer" status="off">Show API-Answer</a>';
?>
		<script>
		jQuery(function() {
			jQuery('a[id=showapianswer]').click(function() {
				var status = jQuery('a[id=showapianswer]').attr('status');
				if (status=='off') {
					jQuery('div[id=divapianswer]').show();
					jQuery('a[id=showapianswer]').text('Hide API-Answer');
					jQuery('a[id=showapianswer]').attr('status', 'on');
				} else {
					jQuery('div[id=divapianswer]').hide();
					jQuery('a[id=showapianswer]').text('Show API-Answer');
					jQuery('a[id=showapianswer]').attr('status', 'off');
				}
			});
		});
		</script>
<?PHP
		echo '<div id="divapianswer" style="display: none;"><textarea rows="7" cols="80">'.json_encode($vals).'</textarea><br><a href="https://jsoneditoronline.org" target="_blank">You might copypaste the JSON to jsoneditoronline.org to analyze the JSON in detail</a></div>';

		############
		# build shortcode
		$inputtypeparam = "";
		if ("csv"==$formdata["indataformat"]) {
			$inputtypeparam = " inputtypeparam='";
			$itpArr = array();
			if (","!=$formdata["csvdelimiter"]) {
				$itpArr["delimiter"] = $formdata["csvdelimiter"];
			}
			if ("#LF#"!=$formdata["csvline"]) {
				$itpArr["csvline"] = $formdata["csvline"];
			}
			if ("#QM#"!=$formdata["csvenclosure"]) {
				$itpArr["enclosure"] = $formdata["csvenclosure"];
			}
			if ("#BS#"!=$formdata["csvescape"]) {
				$itpArr["escape"] = $formdata["csvescape"];
			}
			if ($formdata["csvskipempty"]) {
				$itpArr["skipempty"] = "yes";
			}
			$inputtypeparam .= json_encode($itpArr)."' ";
		}
		$urlgettimeoutstr = '';
		if ($formdata["timeout"]!=5) {
			$urlgettimeoutstr = ' urlgettimeout="'.$formdata["timeout"].'" ';
		}

		if ($methodtechTmp!="curl" || empty($curloptions)) {
			$curloptions4Shortcode = '';
		} else {
			$curloptions4Shortcode = ' curloptions="'.$curloptions.'"';
		}
		

		$sc = '[jsoncontentimporterpro url="'.$formdata["jciurl"].'" debugmode="10" '.$dataformatshortcode[$formdata["indataformat"]].$inputtypeparam.$urlgettimeoutstr.' method="'.$selectedmethod.'"'.$curloptions4Shortcode.' '.$shortcodeparam.']{{_context | json_encode}}[/jsoncontentimporterpro]';
		
		$scOut = htmlentities($sc);
		# httpstatuscodemustbe200=no
		# trytohealjson=yes
		# convertjsonnumbers2strings=yes
		# displayapireturn=
		# encodingofsource=…
		
		#############
		# show JSON-tree
		if (is_array($vals)) {
			$vals4js = "<pre>".str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string) json_encode($vals)), "\0..\37'\\")))."</pre>"; # https://stackoverflow.com/questions/168214/pass-a-php-string-to-a-javascript-variable-and-escape-newlines
			#<script>		var win = window.open("", "Title", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=200,top="+(screen.height-400)+",left="+(screen.width-840));			win.document.body.innerHTML = " $vals4js;";			</script>
			echo "<hr><strong>JSON</strong><br>";
			jcipro_displayJSONpure($vals);
			echo "<hr>";
			
			#############
			# store, rename or copy set
			echo "<strong>Name and store JSON-Access-Set:</strong>";
			echo '<form method="post" action="admin.php?page=jciprostep1getjsonslug">';
			#echo '<input type=hidden name=storeapirequestjson value="'.urlencode($feedData).'">';
			$feedData = json_encode($vals);
			#echo $feedData; exit;
			echo '<input type=hidden name=storeapirequestjson value="'.urlencode($feedData).'">';
			$fdstr = json_encode($formdata);
			echo '<input type=hidden name=storeapirequestval value="'.urlencode($fdstr).'">';
			#var_Dump($fdstr);  # settings of the JSON-Access-Set
#			$nameofselectedjas = $_POST["nameofselectedjas"];
#			$nameofjas =  $nameofselectedjas;
#			if (empty($nameofjas)) {
#				$nameofjas = jcipro_calc_unique_id(time());
#			}
			echo '<input type=text name=nameofjas size=30 value="'.htmlentities($nameofjas).'">&nbsp;&nbsp;&nbsp;';
			if (empty($formdata["accset"])) {
				echo '<input type=hidden name=storeapirequest value="save">';
				$submitButtonValue = "If this is the JSON you need: Click here to store this JSON-Access-Set!";
				submit_button($submitButtonValue, 'primary', '', FALSE); 
			} else {
				echo '<input type=hidden name=storeapirequest value="save">';
				echo '<input type=hidden name=accset value="'.$formdata["accset"].'">';
				$submitButtonValue = "Update this JSON-Access-Set ";#.htmlentities($nameofjas);
				submit_button($submitButtonValue, 'primary', 'updatejas', FALSE);
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				$submitButtonValue = "Create new JSON-Access-Set";
				submit_button($submitButtonValue, 'large', 'createnewjas', FALSE); 
			}

			echo "</form>";
		}
		
		############
		# show Shortcode
		echo "<hr><strong>Shortcode:</strong> ";
		echo "<input type=text size=\"".strlen($scOut)."\" value=\"$scOut\">";

		echo '</td></tr>';
		echo '</table>';
		return "";
	} else {
		jci_test_form($formdata, $meth, $methtech, $dataformat);
		echo '</td></tr>';
		echo '</table>';
	}
	echo $resu;
}

function jci_test_form($formdata, $meth, $methtech, $dataformat) {
	$chk_ignorehttpcode = "";
	
	echo '<style>	label { padding: 0.7ex; }
	
	#jas { background: #eee; }
	</style>	';
	
	echo '<form method="post" action="admin.php?page=jciprostep1getjsonslug">';
	echo '<label><input type=text name=jciurl value="'.htmlentities($formdata["jciurl"]).'" placeholder="Insert URL to be checked for JSON" size="200"></label>';
		if (empty(@$formdata["method"])) {
			$formdata["method"] = "get";
		}
		if (empty(@$formdata["methodtech"])) {
			$formdata["methodtech"] = "curl";
		}
		echo "<p><strong>Optional settings, if needed:</atrong><br>";
		echo '<table border=0>';
		echo '<tr><td valign=top>';
		echo '<table border=0>';
		echo '<tr><td>';
		echo "HTTP-Method: ";
		echo '</td><td>';
		echo '<select name="method">';
		foreach($meth as $k => $v) {
			if (@$formdata["method"]==$k) { $chk_method  = " selected "; } else { $chk_method  = ""; }
			echo '<option value="'.$k.'"'.$chk_method.'>'.$v.'</option>';
		}
		echo '</select>';
		echo '</td></tr>';
		echo '<tr><td>';
		echo "Request-Technique:";
		echo '</td><td>';
		echo '<select name="methodtech">';
		foreach($methtech as $k => $v) {
			if (@$formdata["methodtech"]==$k) { $chk_method  = " selected "; } else { $chk_method  = " "; }
			echo '<option value="'.$k.'"'.$chk_method.'>'.$v.'</option>';
		}
		echo '</select>';
		echo '</td></tr>';
		echo '<tr><td>';
		if (empty(@$formdata["timeout"])) {
			$formdata["timeout"] = 5;
		}
		echo 'Timeout (sec waiting for answer):';
		echo '</td><td>';
		echo '<label><input type=number name=timeout value="'.htmlentities($formdata["timeout"]).'" size=3></label>';
		echo '</td></tr>';

		echo '<tr><td>';
		if (2==$formdata["httpsverify"]) {
			$chk_httpsverifyno = " checked ";
			$chk_httpsverifyyes = "";
		} else {
			$chk_httpsverifyno = "";
			$chk_httpsverifyyes = " checked ";
		}
		echo 'Check valid HTTPS-/TLS-certificate?';
		echo '</td><td>';
		echo '<div class="tooltip"><label><input type=radio name=httpsverify '.$chk_httpsverifyyes.' value="1">YES</label>';
		echo '<label><input type=radio name=httpsverify '.$chk_httpsverifyno.' value="2">NO</label>';
		echo '<span class="tooltiptext">Sometimes the https-TLS-certificate of the API-Server is not compatible to the Wordpress-Server. In case of that: switch check off.</span></div>';
		echo '</td></tr>';


		echo '<tr><td valign=top>';
		#echo '<div class="tooltip">';
		echo "Format of Data:";
		echo '<div id=csvsettings class="csvsettings">';
		echo "<br>Placeholders:<br>";
		echo "Tabulator: #TAB#<br>";
		echo "Linefeed (\\n): #LF#<br>";
		echo "Car. Return (\\r): #CR#<br>";
		echo 'Quot. Mark ("): #QM#<br>';
		echo "Backspace (\\): #BS#<br>";
		##echo '</div>';
		#echo '<span class="tooltiptext">switch Check off in case of access-problems</span></div>';
		echo '</td><td>';
		echo '<select name="indataformat">';
		foreach($dataformat as $k => $v) {
			if (@$formdata["indataformat"]==$k) { $chk_dataformat = " selected "; } else { $chk_dataformat  = " "; }
			echo '<option value="'.$k.'"'.$chk_dataformat.'>'.$v.'</option>';
		}
		echo '</select>';
		echo "<div id=csvsettings class=csvsettings>";
		echo 'CSV-Item-Delimiter (default: ,): <label><input type=text name=csvdelimiter  id=jas size=3 value='.htmlentities(@$formdata["csvdelimiter"]).'></label><br>';
		echo 'CSV-Line-Delimiter (default: #LF#): <label><input type=text name=csvline  id=jas size=3 value='.htmlentities(@$formdata["csvline"]).'></label><br>';

		echo 'CSV-Enclosure (default: #QM#): <label><input type=text name=csvenclosure  id=jas size=3 value='.htmlentities(@$formdata["csvenclosure"]).'></label><br>';
		echo 'CSV-Escape (default: #BS#): <label><input type=text name=csvescape  id=jas size=3 value='.htmlentities(@$formdata["csvescape"]).'></label><br>';
		echo 'Skip empty Lines? ';
		if (@$formdata["csvskipempty"]) {
			$checkcsvskiemptyline_y = " checked ";
			$checkcsvskiemptyline_n = "";
		} else {
			$checkcsvskiemptyline_n = " checked ";
			$checkcsvskiemptyline_y = "";
		}
		echo '<label><input type=radio id=jas name=csvskipempty '.$checkcsvskiemptyline_y.' value=y>YES</label>';
		echo '<label><input type=radio id=jas  name=csvskipempty '.$checkcsvskiemptyline_n.' value=n>NO</label>';

		echo '<input type=hidden name=nameofselectedjas value='.htmlentities(@$formdata["nameofselectedjas"]).'>';
		echo '<input type=hidden name=accset value='.htmlentities(@$formdata["accset"]).'>';

		
		echo "</div>";
		echo '</td></tr>';
		
		
		echo '</table>';
		echo '</td><td valign=top width=120>&nbsp;&nbsp;&nbsp;</td><td valign=top>';
	?>
		<style>
		div .playload {   display: none; }
		div .csvsettings {   display: none; }
		</style>
		<script>
		function showHidePayloadTextarea() {
			var selmethod = jQuery('select[name=method]').val();
			if ("post"==selmethod || "put"==selmethod) {
				jQuery("div[id=pay]").show();
			} else {
				jQuery("div[id=pay]").hide();
			}
		}
		function showHideHeaderSettings() {
			var selmethodtech = jQuery('select[name=methodtech]').val();
			if ("curl"==selmethodtech) {
				jQuery("div[id=httpheader]").show();
			} else {
				jQuery("div[id=httpheader]").hide();
			}
		}
		function showHideCSVSettings() {
			var selidf = jQuery('select[name=indataformat]').val();
			if ("csv"==selidf) {
				jQuery("div[id=csvsettings]").show();
			} else {
				jQuery("div[id=csvsettings]").hide();
			}
		}
		jQuery(function() {
			try {
				jQuery('select[name=methodtech]').change(function() {
					showHideHeaderSettings();
				});
				jQuery('select[name=method]').change(function() {
					showHidePayloadTextarea();
				});
				showHideHeaderSettings();
				showHidePayloadTextarea();
			} catch(e) {
				alert('error: '+e);
			}
			try {
				jQuery('select[name=indataformat]').change(function() {
					showHideCSVSettings();
				});
				showHideCSVSettings();
			} catch(e) {
				alert('error: '+e);
			}
		});
		</script>
<?php
		echo '<div id=pay class=payload>POST-Payload:<br><textarea name=payload rows=3 cols=80>'.@$formdata["payload"].'</textarea><br></div>';

		echo '<div id=httpheader><a href="https://en.wikipedia.org/wiki/List_of_HTTP_header_fields" target="_blank">HTTP-Header:</a><br>';
		if (empty(@$formdata["headernooffilledheader"])) {
			@$formdata["headernooffilledheader"] = 4;
		}
		@$formdata["headernooffilledheader"]++;
		
		#echo @$formdata["headernooffilledheader"]; exit;
		echo '<input type=hidden name=noheader value="'.@$formdata["headernooffilledheader"].'">';
		for ($i = 1; $i <= @$formdata["headernooffilledheader"]; $i++) {
			$hltmp = "";
			if (isset($formdata["headerl".$i])) {
				$hltmp = jcipro_clear_httpheaderkey($formdata["headerl".$i]);
			}
			echo '<label><input type=text id=jas name=headerl'.$i.' value="'.stripslashes(htmlspecialchars($hltmp)).'" size="10"></label> : ';
			echo '<label><input type=text id=jas name=headerr'.$i.' value="'.stripslashes(htmlspecialchars(@$formdata["headerr".$i])).'" size="10"></label><br>';
		}

		echo '</div></td><td valign=top><div id=httpheader>';
		echo 'Predefined common HTTP-Header:<br><div class="tooltip">';
		
		
			echo '<label><input type=text  id=jas name=headaccesskey value="'.stripslashes(htmlspecialchars($formdata["headaccesskey"])).'" size="10"></label> : ';
			echo '<label><input type=text  id=jas name=headaccessval value="'.stripslashes(htmlspecialchars($formdata["headaccessval"])).'"  size=30></label>   ';
			$cbheadaccess_checked = ""; if ($formdata["cbheadaccess"]=="y") {  	$cbheadaccess_checked = " checked "; 			}
			echo 'Use it: <label><input type=checkbox name=cbheadaccess value="y" '.$cbheadaccess_checked.'></label><br>';
			
			echo '<label><input type=text  id=jas name=headuseragentkey value="'.stripslashes(htmlspecialchars($formdata["headuseragentkey"])).'" size="10"></label> : ';
			echo '<label><input type=text  id=jas name=headuseragentval value="'.stripslashes(htmlspecialchars($formdata["headuseragentval"])).'" size=30></label>   ';
			$cbheaduseragent_checked = ""; if ($formdata["cbheaduseragent"]=="y") {  				$cbheaduseragent_checked = " checked "; 			}
			echo 'Use it: <label><input type=checkbox name=cbheaduseragent value="y"'.$cbheaduseragent_checked.'></label>';
			
			echo '<span class="tooltiptext">Here you find two often used HTTP-Header-Fields: Access and User-Agent</span></div><p>';
	

			$formdata["headoauth2key"] = jcipro_clear_httpheaderkey($formdata["headoauth2key"]);
			echo 'oAuth2-Authentication:<br>';
			echo '<div class="tooltip"><label><input type=text id=jas name=headoauth2key value="'.stripslashes(htmlspecialchars($formdata["headoauth2key"])).'" size="10"></label> : ';
			echo '<label><input type=text  id=jas  name=headoauth2val value="'.stripslashes(htmlspecialchars($formdata["headoauth2val"])).'" size=30></label>   ';
			$cbheadoauth2_checked = ""; if ($formdata["cbheadoauth2"]=="y") {  				$cbheadoauth2_checked = " checked "; 			}
			echo 'Use it: <label><input type=checkbox name=cbheadoauth2 value="y"'.$cbheadoauth2_checked.'></label><span class="tooltiptext">oAuth2 means usually sending "Authentication:Bearer TOKEN" in the HTTP-Header. ';
			echo 'The TOKEN you can retrieve from the API with an Shortcode you must define first!</span></div><br>';
			#echo '<div class="tooltip">Info<span class="tooltiptext">Tooltip text</span></div> ';			

		echo '</div></td></tr>';
		echo '</table>';

		#var_dump($formdata);


		echo '<input type=hidden name=nameofselectedjas value="'.stripslashes(htmlspecialchars(@$formdata["nameofselectedjas"])).'">';
		echo '<input type=hidden name=accset value="'.stripslashes(htmlspecialchars(@$formdata["accset"])).'">';

	$submitButtonValue = "Test Request ";#.$formdata["accset"];
	submit_button($submitButtonValue, 'primary', 'testrequest', FALSE); 
	echo "</form>";
?>
<style>
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 250px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {  visibility: visible;}
</style>
<?PHP	
}

function jcipro_jsonuseset_select_form() {
	$out = '<div id="juslist">';
	$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);
	if (is_null($jci_pro_api_use_items)) {
		return "First create a JSON-Use-Set: This will be used for a Generating-Set.";
	}
	$out .= "<select name=selectejus>";
	if (isset($jci_pro_api_use_items)) {
		foreach($jci_pro_api_use_items as $k =>$v) {
			#echo htmlentities(json_encode($v))."<hr>";
			$out .=  "<option value=".$k.">";
			$out .=  $v["savejusname"]." (";
			$out .=  date("F d Y, H:i", $v["lastchange"]).", ";
			$out .=  ")</option>";
		}
	}
	$out .=  "</select>";
	$out .= '</div>';
	return $out;
}

function jcipro_jsonaccessset_select_form() {
	$out = '<div id="jaclist">';
	$isthereastoredandactivejas = FALSE;
	$jci_pro_api_access_items = json_decode(get_option('jci_pro_api_access_items'), TRUE);
	$out .= "<select name=selectejas>";
	if (isset($jci_pro_api_access_items)) {
		foreach($jci_pro_api_access_items as $t) {
			if ("active"!=$t["status"]) { 
				continue; 
			}
			$isthereastoredandactivejas = TRUE;
			$out .=  "<option value=".$t["md5id"].">";
			$out .=  $t["nameofjas"]." (";
			$out .=  date("F d Y, H:i", $t["time"]).", ";
			$out .=  $t["set"]["jciurl"];
			$out .=  ")</option>";
		}
	}
	$out .=  "</select>";
	if (!$isthereastoredandactivejas) {
		return "";
	}
	$out .= '</div>';
	return $out;
}


function register_jci_pro_step2usejson($tm_data) {

	echo "<h1>Step 2: How the JSON from the API should be used?</h1>";

	##########
	# offer the JSON-Access-Sets
	if (!isset($_POST["selectejas"])) {	
		echo "<h2>What JSON? <a href=admin.php?page=jciprostep1getjsonslug>Manage JSON-Access-Sets</a></h2>";
		echo '<form method="post" id="selectjsonaccessset" action="admin.php?page=jciprostep2usejsonslug">';
		$jasselect = jcipro_jsonaccessset_select_form();
		if (empty($jasselect)) {
			echo "There is no active JSON-Access-Set. Click on \"Manage JSON-Access-Sets\".";
		} else {
			$jusselect =  jcipro_jsonuseset_select_form();
			echo "<table border=0 cellspacing=15><tr><td valign=top>";
			#echo $jasselect;
			#echo $jusselect;
			####################
			## 2nd: how the json should be used --> creating Custom Posts or display?
			$howjsnArr = array();
			$howjsnArr['single'] = "Use JSON on a page to display data: Select a JSON-Access-Set";
			$selectedflag['single'] = "";
			$selectedtype['single'] = "jas";

			$howjsnArr['cpt'] = "Create Custom Post Types out of JSON: Select a JSON-Use-Set";
			$selectedflag['cpt'] = "";
			$selectedtype['cpt'] = "jus";
	
			$howjson = "single";
			if (isset($_POST['howjson'])) { 		$howjson = $_POST['howjson'];	}
			$selectedflag[$howjson] = " checked ";
			if (empty($selectedflag[$howjson])) {
				$selectedflag["single"] = " checked ";
				$selectedflag["cpt"] = "";
			}
			echo "<input type=hidden name=select value=way>";
			echo "<h2>How we should use the JSON?</h2>";
		
			echo '<input type=radio name=howjson value="single" '.$selectedflag["single"].'> '.$howjsnArr['single'].' '.$jasselect.'<p>';
			#echo '<input type=radio name=howjson value="cpt" '.$selectedflag["cpt"].'> '.$howjsnArr['cpt'].' '.$jusselect.'<p>';

############
?>
		<script>
		jQuery(function() {
			try {
				var $selected = jQuery('input[type=radio][name=howjson]:checked');
				if($selected.length == 0) {
					jQuery('div[id=juslist]').hide();
					jQuery('div[id=jaclist]').show();
				} else {
					if ($selected.val()=="single") {
						jQuery('div[id=juslist]').hide();
						jQuery('div[id=jaclist]').show();
					} else {
						jQuery('div[id=juslist]').show();
						jQuery('div[id=jaclist]').hide();
					}
				}	
				jQuery('input[type=radio][name=howjson]').change(function() {
					if (this.value=="single") {
						jQuery('div[id=juslist]').hide();
						jQuery('div[id=jaclist]').show();
					} else {
						jQuery('div[id=juslist]').show();
						jQuery('div[id=jaclist]').hide();					
					}
					//alert(this.value);
				});
			} catch(e) {
				alert('error: '+e);
			}
		});
		</script>
<?PHP

############



			echo "<p>";
			submit_button("Let's use the above selected JSON-Access-Set!", 'primary', 'selectway', FALSE); 
			echo "</form><hr>";
			jcipro_show_saved_json_use_sets();
			echo "</td></tr></table>";
		}
		return TRUE;	
	}
	echo '<h1><a href="#" id="all">Show all Tabs</a> - <a href="#" id="jsonli">JSON only</a> - 
			<a href="#" id="twigmi">JSON & twig</a> - <a href="#" id="resre">twig & Result</a></h1><hr>';
	#echo json_encode($_POST);
?>	
		<script>
		jQuery(function() {
	<?PHP		
		if (isset($_POST["seljs"]) && (!isset($_POST["usejson"]))) {
	?>
			jQuery("div[id=jsonli]").show();
			jQuery("div[id=twigmi]").show();
			jQuery("div[id=resre]").hide();
	<?PHP		
		} else if (isset($_POST["usejson"]) && ("usejson" == $_POST["usejson"])) {
	?>		
			jQuery("div[id=jsonli]").hide();
			jQuery("div[id=twigmi]").show();
			jQuery("div[id=resre]").show();
	<?PHP		
		} else {
	?>		
			jQuery("div[id=jsonli]").show();
			jQuery("div[id=twigmi]").hide();
			jQuery("div[id=resre]").hide();
	<?PHP		
		}
	?>		
			try {
				jQuery('a[id=jsonli]').click(function() {
					jQuery("div[id=jsonli]").show();
					jQuery("div[id=twigmi]").hide();
					jQuery("div[id=resre]").hide();
				});
				jQuery('a[id=resre]').click(function() {
					jQuery("div[id=jsonli]").hide();
					jQuery("div[id=twigmi]").show();
					jQuery("div[id=resre]").show();
				});
				jQuery('a[id=twigmi]').click(function() {
					jQuery("div[id=jsonli]").show();
					jQuery("div[id=twigmi]").show();
					jQuery("div[id=resre]").hide();
				});
				jQuery('a[id=all]').click(function() {
					jQuery("div[id=jsonli]").show();
					jQuery("div[id=twigmi]").show();
					jQuery("div[id=resre]").show();
				});
			} catch(e) {
				alert('error: '+e);
			}
		});
		</script>	
<?PHP
	echo "<table border=0 cellspacing=15>";
	echo "<tr><td valign=top>";
	echo '<div id="jsonli">';
	#var_Dump($_POST);
	
	$howjson = $_POST["howjson"]; # show data OR generate CPT

	if ("single"==$howjson) {
		$jci_pro_api_access_items = json_decode(get_option('jci_pro_api_access_items'), TRUE);
		#var_dump($jci_pro_api_access_items[$_POST['selectejas']]);
		$selectedJAS = $jci_pro_api_access_items[$_POST['selectejas']];
		#var_dump($selectedJAS);
	
		#echo json_encode($selectedJAS);
		$formdata = $selectedJAS["set"];
		echo '<h2>Selected JSON-Access-Set: '.$selectedJAS["nameofjas"].'</h2>';	
	
	#return TRUE;
##############

		#######
		# do request
		
		# BEGIN param
		$debugModeIsOn = FALSE;
		$debugLevel = 10;
		$methodTmp = $formdata["method"];
		# END: param
		
		# BEGIN buildrequest
		require_once plugin_dir_path( __FILE__ ) . 'lib/lib_request.php';
		$jci_request_handler = new jci_request_prepare($formdata, $methodTmp, $formdata["methodtech"]);
		$curloptions = $jci_request_handler->getCurlOptionsString();
		$curloptions4Request = $jci_request_handler->getCurlOptions4Request($curloptions);
		$selectedmethod = $jci_request_handler->getSelectedmethod();
		# END buildrequest

		# BEGIN cache: The  retrieved JSON from the JSON-Access-Set is cached
		$checkbox_cachejson = "";
		$cacheEnable = FALSE;#TRUE;
		#$cacheEnable = TRUE;
		if (isset($_POST["cachejson"]) && ($_POST["cachejson"]=="docache")) {
			$checkbox_cachejson = " checked ";
			$cacheEnable = TRUE;
		}
		
		if ($cacheEnable) {
			$defaultcachepath = WP_CONTENT_DIR . "/cache/jsoncontentimporterpro/";
			$cachepath = get_option('jci_pro_cache_path');
			if (empty($cachepath)) {
				$cachepath = $defaultcachepath;
				update_option('jci_pro_cache_path', $cachepath);
			}
			$cacheFileFingerPrint = $selectedJAS["nameofjas"]."-".json_encode($formdata);
			$cacheFile = $cachepath.md5($cacheFileFingerPrint).".cgi";
			$cacheExpireTime = 86400; # caching for 24 hrs
		} else {
			$cacheFile = "";
			$cacheExpireTime = 0;
		}
		# END Cache 

		# BEGIN LOAD
		if (
			(!class_exists('FileLoadWithCachePro'))
			|| (!class_exists('JSONdecodePro'))
		) {
			require_once plugin_dir_path( __FILE__ ) . '/class-fileload-cache-pro.php';
		}
		$header = "";
		$urlencodepostpayload = "";
		$encodingofsource = "";
		$httpstatuscodemustbe200 = "no";
		$auth = "";
		$showapiresponse = FALSE;
		$followlocation = TRUE; # follow 301 etc.
		
        $fileLoadWithCacheObj = new FileLoadWithCachePro(
            $formdata["jciurl"], $formdata["timeout"], $cacheEnable, $cacheFile, $cacheExpireTime, $selectedmethod, NULL, '', '',
            $formdata["payload"], $header, $auth, $formdata["payload"],
            $debugLevel, $debugModeIsOn, $urlencodepostpayload, $curloptions4Request,
            $httpstatuscodemustbe200, $encodingofsource, $showapiresponse, $followlocation 
            );
        $fileLoadWithCacheObj->retrieveJsonData();
		$receivedData = $fileLoadWithCacheObj->getFeeddataWithoutpayloadinputstr();
		$httpcode = $fileLoadWithCacheObj->getErrormsgHttpCode();
		#echo "selectedmethod: $selectedmethod<br>receivedData:<hr>$receivedData<hr><br>httpcode: $httpcode<br>";
		# END LOAD
		######################

		## get JSON-Array
		$convertJsonNumbers2Strings = TRUE; # default
		$inputtype = $formdata["indataformat"];
		$csv_delimiter = $formdata["csvdelimiter"];
		$csv_csvline = $formdata["csvline"];
		$csv_enclosure = $formdata["csvenclosure"];
		$csv_skipempty = $formdata["csvskipempty"];
		$csv_escape = $formdata["csvescape"];
		
		#$json = json_decode($receivedData, TRUE);
        $jsonDecodeObj = new JSONdecodePro($receivedData, TRUE, $debugLevel, $debugModeIsOn, 
			$convertJsonNumbers2Strings, $cacheFile, $fileLoadWithCacheObj->getContentType(), 
			$inputtype, $csv_delimiter, $csv_csvline, $csv_enclosure, $csv_skipempty, $csv_escape);
        $json = $jsonDecodeObj->getJsondata();
		
		if (empty($json)) {
			echo "Error: No stored JSON in this JSON-Access-Set. Check this set, please!";
			echo "</div></td></tr></table>";
			return TRUE;
		}
	}
	
	if ("cpt"==$howjson) {
		$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);
		#echo "<hr>".htmlentities(json_encode(($jci_pro_api_use_items)))."<hr>";
	
		$selectedJUS = $jci_pro_api_use_items[$_POST['selectejus']];
		#var_dump($selectedJAS);
	
		#echo json_encode($selectedJAS);
		echo '<h2>Selected JSON-Use-Set: '.$selectedJUS["savejusname"].'</h2>';	

		$json = $selectedJUS['jsonStr'];
		if (empty($json)) {
			echo "No stored JSON in this JSON-Access-Set. Check this set, please!";
			echo "</div></td></tr></table>";
			return TRUE;
		}

		$selected_cpt_key = "";
		if (isset($_POST["selcpt"])) {
			$selected_cpt_key = $_POST["selcpt"];
		}
		require_once plugin_dir_path( __FILE__ ) . '/lib/lib_generate_cpt.php';

	#	$jci_gen_cpt = new jci_generate_cpt($json, $selected_cpt_key, $_POST["selectejas"], $_POST["select"], $_POST["howjson"], $_POST["selectway"]);
		$jci_gen_cpt = new jci_generate_cpt($json, $selected_cpt_key, $_POST["selectejus"], $_POST["select"], $_POST["howjson"], $_POST["selectway"]);
		if (empty($selected_cpt_key)) {
			$jci_gen_cpt->selectCPTForm();
			return "";
		}

		echo "<strong>Selected Custom Post Type: $selected_cpt_key</strong><hr>";
		
		$jci_gen_cpt->getCPF();
	
		echo "<strong>Selected JSON-Node: ".$selectedJUS["jsonbasenode"]."</strong><hr>";	
		$jsonArr = json_decode($json);
		$jci_gen_cpt->showJSONkeys($jsonArr, $selectedJUS["jsonbasenode"]);
		$json2workwithNode = $jci_gen_cpt->get_jsonKeysArr();
	
		$jci_gen_cpt->showformCPF2JSON($json2workwithNode,"");
		
		$jci_gen_cpt->showShortcode("");
		
		
		$jci_gen_cpt->showExistingGenSets();
		
		
		return "";
		#  END of if ("cpt"==$howjson) {
	}
	
	# load php-lib for working with JSON
	$addcheckboxes = TRUE;
	$openall = FALSE;
#	jcipro_displayJSONpure($json, $addcheckboxes, $openall );

	echo '<form method="post" id="target" action="?page=jciprostep2usejsonslug">';
	submit_button("Click here to create twig-code with selected JSON", 'primary', '', FALSE); 
	echo "<p>";
	
	echo 'Select the JSON-nodes you need. By this you create a "reduced" set of data out of the JSON-Feed.<br>'; #<br>The order you click is importaint at arrays: This  defines the order of the data in the output!<br>';
	

################# option: reduce JSON by basenode
	require_once plugin_dir_path( __FILE__ ) . '/lib/lib_jsonphp.php';
	
	$wwj = new workWithJSON($json);

	#$ja = new jci_json_analyzer($json, FALSE);
	$wwj->showJSONkeys4UseBasenode(FALSE);
	#var_dump($wwj->getJsonKeysArr());

	echo "Optional: Select a JSON-Node, where you want to use the JSON (e.g. for Generating-Sets)<br>";
	
	$selectedbasenode = "";
	if (isset($_POST["savejusname"])) {
		$savejusname = $_POST["savejusname"];
		#echo $savejusname."<hr>";
		$usesetArr = NULL;
		$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);
		foreach ($jci_pro_api_use_items as $k=>$v) { 
			if ($v["savejusname"]==$savejusname) {
				$usesetArr = $v;
			}
		}
		#echo json_encode($usesetArr)."<hr>";
		if (isset($usesetArr['jsonbasenode'])) {
			$selectedbasenode = $usesetArr['jsonbasenode'];
		}
	}
	if (isset($_POST["jsonbasenode"])) {
		$selectedbasenode = $_POST["jsonbasenode"];
	}
	
	if (!is_null($wwj->getJsonKeysArr4UseBasenode())) {
		echo '<select name="jsonbasenode">';
		echo '<option value="">No Basenode, use complete JSON</option>';
		foreach($wwj->getJsonKeysArr4UseBasenode() as $bnk => $bnv) {
			$anzitems = "";
			if (is_numeric($bnv)) {
				$anzitems = ' ('.$bnv.' Items)';	
				$jsonbnselect = "";
				if ($bnk==$selectedbasenode) {
					$jsonbnselect = " selected ";
				}
				echo '<option value="'.$bnk.'" '.$jsonbnselect.'>'.$bnk.$anzitems.'</option>';	
			}
		}
		echo '</select>';
	} else {
		echo '<input type=hidden name="jsonbasenode" value="">';
	}

	echo ' <input type=checkbox name="cachejson" value="docache" '.$checkbox_cachejson.'> Cache JSON (no API-requests while testing)';

	
	if (!empty($selectedbasenode)) {
		# $selectedbasenode
		 $wwj->selectJSONnode($json, $selectedbasenode);
		 $json = $wwj->getBasenodeSelectedJSON();
		# echo "<hr>";
		 #var_Dump($json);
	}
	
	#echo "POST: ".json_encode($_POST);
#################


	echo "<hr>";
	jcipro_displayJSONpure($json, $addcheckboxes, $openall );
	echo '<input type=hidden name=selecjs value="" />';
	echo '<input type=hidden name=selectejas value="'.$_POST["selectejas"].'" />';
	echo '<input type=hidden name=select value="'.$_POST["select"].'" />';
	echo '<input type=hidden name=howjson value="'.$_POST["howjson"].'" />';
	echo '<input type=hidden name=selectway value="'.stripslashes($_POST["selectway"]).'" />';
	echo '<input type=hidden id="seljs" name="seljs" value="" />';
	echo "</form>";
?>
	<hr>
	<a href="admin.php?page=jciprostep1getjsonslug">Other JSON-Access-Set?</a>

    <script type="text/javascript">
	$( "#target" ).submit(function( event ) {
		//alert( "submit" );
		var checked_nodes = []; 
		var selectedNodes = $('#jci_json_jstree').jstree("get_selected", true);
		$.each(selectedNodes, function() {
			checked_nodes.push(this.id);
		});
		$('#seljs').val(checked_nodes);
		//event.preventDefault(); // no forward
	});
	</script>
<?PHP	
	echo "</div>";
	if (isset($_POST["seljs"])) {
		echo "</td><td valign=top bgcolor=#fff>";
		echo '<div id="twigmi">';
		if (empty($_POST["seljs"])) {
			echo "<h2>Select some JSON in the left JSON-Tree, please!</h2>";
			echo "</div></td></tr></table>";
			return TRUE;
		}

		# create a reduced json only with the selected nodes and items
		$selnodesArr = explode(",", $_POST["seljs"]); # selektoren
		$jsonFullStr = json_encode($json);
		require_once plugin_dir_path( __FILE__ ) . '/lib/lib_jsonselector.php';
		$jsonSelector = new jsonSelector();
		$jsonStr = $jsonSelector->processJson($jsonFullStr, $_POST["seljs"]);

		echo '<script>';
		
		$jsonStr4newwin = "<pre>".str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$jsonStr), "\0..\37'\\")))."</pre>"; # https://stackoverflow.com/questions/168214/pass-a-php-string-to-a-javascript-variable-and-escape-newlines
		echo 'function nWin() { 
			var newWin = window.open(\'\', \'jsonreduced\');
			$(newWin.document.body).html(\''.$jsonStr4newwin.'\');
			}';
		echo '$(function() {    $("a#showreducedjson").click(nWin);});';
		echo '</script>';
		echo '<h2>twig-code, used with the JSON selected on the left side: <a href="javascript:;" id="showreducedjson">Show "reduced" JSON</a></h2>';

		## build twig-code
		#echo "<textarea>".preg_replace("/(\n|\r)/", "", $jsonStr)."</textarea>";

#		require_once plugin_dir_path( __FILE__ ) . '/lib/JsonToTwigConverter.php';
#		$j2t = new JsonToTwigConverter($jsonStr, "", "", 'nodefined'); # ($json = '', 'list',  'nokeys', 'nodefined')

		require_once plugin_dir_path( __FILE__ ) . '/lib/lib_json2twig.php';
		$j2t = new JsonToTwig($jsonStr);
		$res = $j2t->getTwig();
		$content = $res;
		if (isset($_POST["jcitwigcodeeditor"])) {
			$content = stripslashes(urldecode($_POST["jcitwigcodeeditor"]));
		}
		#var_Dump($_POST);

		echo '<form id="target" action="?page=jciprostep2usejsonslug" method=post>';
		echo '<input type=hidden name=selecjs value="" />';

		echo '<input type=hidden name=selectejas value="'.$_POST["selectejas"].'" />';
		echo '<input type=hidden name=select value="'.$_POST["select"].'" />';
		echo '<input type=hidden name=howjson value="'.$_POST["howjson"].'" />';
		echo '<input type=hidden name=selectway value="'.stripslashes($_POST["selectway"]).'" />';

		echo '<input type=hidden id="seljs" name="seljs" value="'.$_POST["seljs"].'" />';
		echo '<input type=hidden name="selectedjson" value="'.urlencode($jsonStr).'" />';
		$jsonbasenode4form = "";
		if (isset($usesetArr["jsonbasenode"])) {
			$jsonbasenode4form = $usesetArr["jsonbasenode"];
		}
		if (isset($_POST["jsonbasenode"])) {
			$jsonbasenode4form = $_POST["jsonbasenode"];
		}
		echo '<input type=hidden name="jsonbasenode" value="'.$jsonbasenode4form.'" />';
	

		submit_button("Click here to show the result of the below twig-code combined with the selected JSON", 'primary', '', FALSE); 
		
		########## editor ace begin
		echo '<p>Editor: This is the <a href="https://ace.c9.io" target="_blank">ace Editor</a>, <a href="https://github.com/ajaxorg/ace/wiki/Default-Keyboard-Shortcuts" target="_blank">see here for Ace-Keyboard-Shortcuts</a> - try "for" or "if"...';

		$editor_id = 'jcitwigcodeeditor';
		require_once plugin_dir_path( __FILE__ ) . '/editor/jcieditor.php';
		$aceEditor = new JCIeditor($editor_id, $content);
		
		
		$aceEditor->showAceEditor();
		
		$aceTwigSyntaxShorts = $aceEditor->getAceTwigSyntaxShorts();
		ksort($aceTwigSyntaxShorts);

		########## editor ace end
		#var_Dump($_POST);
		
		echo '<input type=hidden id="usejson" name="usejson" value="usejson" />';
		echo "</form>";

		echo "<br><strong>Autocomplete twig-code in the ace-editor:</strong><br>";
		echo "<table border=1>";
			echo "<tr bgcolor=yellow><td valign==top>";
			echo "Editor-Text";
			echo "</td><td valign==top width=150>";
			echo "Autocompleted twig-code";
			echo "</td></tr>";
		foreach($aceTwigSyntaxShorts as $k => $v) {
			echo "<tr><td valign==top>";
			echo trim($k);
			echo '</td><td valign==top>';
			echo htmlentities(trim($v));
			echo "</td></tr>";
		}		
		echo "</table>";

		
 
		echo "</div>";
		
		echo "</td><td valign=top>";
		echo '<div id="resre">';
		if (isset($_POST["usejson"]) && ("usejson" == $_POST["usejson"])) {
			$jsonstr = "";
			$twigResult = "Click left to show results";
			if (isset($_POST["selectedjson"])) {
				$jsonstr = urldecode($_POST["selectedjson"]);
				$reducedJsonArr = json_decode($jsonstr, TRUE);
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
				require_once $inc;
				$twigHandler = new doJCITwig("twig332adj", TRUE);
				$twigResult = $twigHandler->executeTwig($reducedJsonArr, $content, "twig332adj", TRUE);
			}


?>
		<script>
		jQuery(function() {
			jQuery("code[id=htmlcode]").hide();
			jQuery("code[id=exechtml]").show();
			try {
				jQuery('a[id=togglehtmlcode]').click(function() {
					var togglehtmlcodestatus = jQuery('a[id=togglehtmlcode]').attr("togval");
					if ("code"==togglehtmlcodestatus) {
						jQuery("code[id=htmlcode]").show();
						jQuery("code[id=exechtml]").hide();
						jQuery('a[id=togglehtmlcode]').text("Show executed HTML");
						jQuery('a[id=togglehtmlcode]').attr("togval", "execcode");
					} else {
						jQuery("code[id=htmlcode]").hide();
						jQuery("code[id=exechtml]").show();
						jQuery('a[id=togglehtmlcode]').text("Show HTML-Code");
						jQuery('a[id=togglehtmlcode]').attr("togval", "code");
					}
				});
				jQuery('a[id=hideboth]').click(function() {
					jQuery("code[id=htmlcode]").hide();
					jQuery("code[id=exechtml]").hide();
				});
			} catch(e) {
				alert('error: '+e);
			}
		});
		</script>
<?PHP
			echo "<h3>Result of twig-code and selected JSON: ";
			echo "<a href=# id=togglehtmlcode togval=code>Show HTML-Code</a> - ";
			echo "<a href=# id=hideboth togval=hideboth>Hide both</a>";
			echo "</h3>";

			echo '<form id="storejus" action="?page=jciprostep2usejsonslug" method=post>';
			echo '<input type=hidden name=selecjs value="" />';

			echo '<input type=hidden name=selectejas value="'.$_POST["selectejas"].'" />';
			echo '<input type=hidden name=select value="'.$_POST["select"].'" />';
			echo '<input type=hidden name=howjson value="'.$_POST["howjson"].'" />';
			echo '<input type=hidden name=selectway value="'.stripslashes($_POST["selectway"]).'" />';

			echo '<input type=hidden id="seljs" name="seljs" value="'.$_POST["seljs"].'" />';
			echo '<input type=hidden name="selectedjson" value="'.urlencode($jsonStr).'" />';
	
			echo '<input type=hidden name="'.$editor_id.'" value="'.urlencode($content).'" />';
			echo '<input type=hidden id="usejson" name="usejson" value="usejson" />';

			$savejusname = "use-".$selectedJAS["nameofjas"];##time();
			if (isset($_POST["savejusname"])) {
				$savejusname = $_POST["savejusname"];
			}
			echo '<input type=hidden name="savejus" value="savejus" />';
			if (isset($_POST["jsonbasenode"])) {
				echo '<input type=hidden name="jsonbasenode" value="'.$_POST["jsonbasenode"].'" />';
			}
			echo '<input type=text name="savejusname" value="'.$savejusname.'" />';
			submit_button("Click here to store this JSON-Use-Set with the left name", 'primary', '', FALSE); 
			echo "</form>";

			echo "<code id=htmlcode>";
			echo jcipro_divbox($twigResult,TRUE, "white", "black", "");
			echo "</code>";
			echo "<code id=exechtml>";
			echo jcipro_divbox($twigResult, FALSE);
			echo "</code><hr>";

			if (isset($_POST["savejus"]) && ("savejus" == $_POST["savejus"])) {
				echo "<h2>JSON-Use-Set Saved</h2>";
				echo "Name of saved JSON-Use-Set: $savejusname<br>";
				$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);
				
				$jusitemsArr = array();
				$savejusname_uid = jcipro_calc_unique_id($savejusname);
				
				$jci_pro_api_use_items[$savejusname_uid]["savejusname"] = $savejusname;
				$jci_pro_api_use_items[$savejusname_uid]["selectejas"] = $_POST["selectejas"];
				$jci_pro_api_use_items[$savejusname_uid]["howjson"] = $_POST["howjson"];
				$jci_pro_api_use_items[$savejusname_uid]["seljs"] = $_POST["seljs"];
				#$jci_pro_api_use_items[$savejusname_uid]["jsonStr"] = ...  # do not save jsonStr: this might get too big for json_decode 
				$jci_pro_api_use_items[$savejusname_uid]["content"] = $content;
				#$jci_pro_api_use_items[$savejusname_uid]["usejson"] = $_POST["usejson"];
				$jci_pro_api_use_items[$savejusname_uid]["lastchange"] = time();
				if (!isset($jci_pro_api_use_items[$savejusname_uid]["initalcreatetime"])) {
					$jci_pro_api_use_items[$savejusname_uid]["initalcreatetime"] = time();
				}
				$jci_pro_api_use_items[$savejusname_uid]["jsonbasenode"] = $_POST["jsonbasenode"];
				
				
#				var_Dump($jci_pro_api_use_items);
				$save_str = json_encode($jci_pro_api_use_items);
				update_option('jci_pro_api_use_items', $save_str);
				###only for tests!! update_option('jci_pro_api_use_items', "");
			}
			echo "<p><br><hr>";
		}
		jcipro_show_saved_json_use_sets(TRUE);
		echo "</div>";
	}	
	echo "</td></tr></table>";
}

function jcipro_show_saved_json_use_sets($short=FALSE) {

	$jci_pro_api_access_items = json_decode(get_option('jci_pro_api_access_items'), TRUE);
	$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);

	$act = @$_GET["act"];
	$mid = @$_GET["mid"];
	if (isset($_GET["act"]) && isset($_GET["mid"])) {
		if ("del"==$act) {
				unset($jci_pro_api_use_items[$mid]);
		}
		if ("act"==$act) {
			$jci_pro_api_use_items[$mid]["status"] = "active";
		}
		if ("ina"==$act) {
			$jci_pro_api_use_items[$mid]["status"] = "inactive";
		}
	}
		
		$save_storeapirequestval_str = json_encode($jci_pro_api_use_items);
		update_option('jci_pro_api_use_items', $save_storeapirequestval_str);

				echo "<h2>Saved JSON-Use-Sets";
				if ($short) {
					echo ' - <a href="?page=jciprostep2usejsonslug">Show list of JSON-Use-Sets in detail</a>';
				}
				echo "</h2>";
				
				if (count($jci_pro_api_use_items)>0) {
					echo "<table border=1 cellpadding=5>";
					echo "<tr bgcolor=#fff><td>";
					echo "<strong>Load this JSON-Use-Set</strong>";
					echo "</td><td><strong>Status</strong>";
				#	echo "</td><td>";
				#	echo "<strong>Name of Set</strong>";
					if (!$short) {
						echo "</td><td><strong>Shortcode</strong>";
						echo "</td><td><strong>Last change</strong>";
						echo "</td><td><strong>Created at</strong>";
						echo "</td><td><strong>JSON-Access-Set</strong>";
					}
					echo "</td><td><strong>Delete?</strong>";
					echo "</td><tr>";
	
					#var_Dump($jci_pro_api_use_items);
					foreach($jci_pro_api_use_items as $k => $v) {
						echo "<tr><td>";
						#jcipro_load_json_use_set($jci_pro_api_access_items[$v["selectejas"]], $k, $v);
						jcipro_load_json_use_set($v["selectejas"], $k, $v);
						
						echo "</td><td>";

						$status = "active";
						if (isset($v["status"])) {
							$status = $v["status"];
						}
						echo "Show this Set on lists with  all JSON-Use-Sets:<br>";
						if ("inactive"==$status) {
							echo "<font color=red>no</font> - ";
							echo '<a href=?page=jciprostep2usejsonslug&act=act&mid='.$k.'>switch to yes</a>';
						} else {
							echo "<font color=green>yes</font> - ";
							echo '<a href=?page=jciprostep2usejsonslug&act=ina&mid='.$k.'  title="">switch to no</a>';
						}
					#	echo "</td><td>";
					#	echo $v["savejusname"];
						if (!$short) {
							echo "</td><td>";
							$setshortcode = '[jsoncontentimporterpro jsonuseset="'.urlencode($v["savejusname"]).'"]';
							echo "<code>$setshortcode</code>";
							echo "</td><td>";
							echo date("F d Y, H:i", $v["lastchange"]);
							echo "</td><td>";
							echo date("F d Y, H:i", $v["initalcreatetime"]);
							echo "</td><td>";
							echo $jci_pro_api_access_items[$v["selectejas"]]["set"]["nameofselectedjas"].", ";
							echo date("F d Y, H:i", $jci_pro_api_access_items[$v["selectejas"]]["time"]).", ";
							echo $jci_pro_api_access_items[$v["selectejas"]]["set"]["jciurl"];
							#var_Dump($jci_pro_api_access_items[$v["selectejas"]]);
						}
						echo "</td><td>";
						if ("deleted"==$status) {
							echo "<font color=red>deleted</font>";
						} else {
							echo '<a href=?page=jciprostep2usejsonslug&act=del&mid='.$k.' title="delete JSON-Access-Set permanently">delete Set</a>';
						}
						echo "</td><tr>";
					}
				} else {
					echo "There is no JSON-Use-Set saved. Create one!";
				}
				echo "</table>";
}


function jcipro_load_json_use_set($idofaccessset, $k, $v, $buttontext = "Load this JSON-Use-Set") {
	echo '<form id="storejus" action="?page=jciprostep2usejsonslug" method=post>';
	echo '<input type=hidden name=selecjs value="" />';
#	echo '<input type=hidden name=selectejas value="'.$v["selectejas"].'" />';
	echo '<input type=hidden name=selectejas value="'.$idofaccessset.'" />';
	echo '<input type=hidden name=select value="select" />'; 
	echo '<input type=hidden name=howjson value="'.$v["howjson"].'" />';
	echo '<input type=hidden name=selectway value="selectway" />'; 
	echo '<input type=hidden id="seljs" name="seljs" value="'.$v["seljs"].'" />';
	#if (isset($v["jsonStr"])) {
#		echo '<input type=hidden name="selectedjson" value="'.urlencode($v["jsonStr"]).'" />';
		#echo '<input type=hidden name="selectedjson" value="" />';
	#}
	$content = $v["content"]; 
	echo '<input type=hidden name="jcitwigcodeeditor" value="'.urlencode($content).'" />';
	echo '<input type=hidden id="usejson" name="usejson" value="usejson" />';
	echo '<input type=hidden name="savejusname" value="'.$v["savejusname"].'" />';
	#echo '<input type=hidden name="savejus" value="savejus" />';
	submit_button($v["savejusname"]." - ".$buttontext, 'primary', '', FALSE); 
	echo "</form>";
	#echo htmlentities(json_encode($v));
}

function jcipro_calc_unique_id($idin) {
	return substr("jci".md5($idin), 0, 10);
}

function jcipro_maskspecialchars($strin) {
	$strin = preg_replace("/\,/", "#KOMM##", $strin);
	$strin = preg_replace("/\./", "#DOT##",  $strin);
	return $strin;
}
function jcipro_unmaskspecialchars($strin) {
	$strin = preg_replace("/#KOMM##/", ",", $strin);
	$strin = preg_replace("/#DOT##/", ".", $strin);
	return $strin;
}

function jcipro_buildJSONNode(&$array_in, $key, $value) {
  $keys = explode('.', $key);
  $last_key = array_pop($keys);
	$last_key = jcipro_unmaskspecialchars($last_key);
  while ($arr_key = array_shift($keys)) {
    if (!array_key_exists($arr_key, $array_in)) {
      $array_in[$arr_key] = array();
    }
    $array_in = &$array_in[$arr_key];
  }
  $array_in[$last_key] = $value;
}


function jcipro_getJSONValueOfNode($jsonArr, $node) {
	$nodeArr = explode(".", $node);
	$retval = "";
	foreach ($nodeArr as $i) {
		$i = jcipro_unmaskspecialchars($i);
		if (!isset($jsonArr[$i])) { return FALSE; }
		$jsonArr = $jsonArr[$i];
		$retval = $jsonArr;
	}
	return $retval;
}


function jcipro_displayJSONpure($jsonArr, $addcheckboxes=FALSE, $openall=TRUE) {
	jcipro_displayJSON($jsonArr, FALSE, $addcheckboxes, $openall);
}

function jcipro_displayJSON($jsonArr, $showSelectionForm = TRUE, $addcheckboxes=FALSE, $openall=TRUE) {
	require_once plugin_dir_path( __FILE__ ) . '/lib/lib_jsonphp.php';
	
#############
		echo '<a href="#" id="showapiansweruse" status="off">Show JSON</a>';
?>
		<script>
		jQuery(function() {
			jQuery('a[id=showapiansweruse]').click(function() {
				var status = jQuery('a[id=showapiansweruse]').attr('status');
				if (status=='off') {
					jQuery('div[id=divapiansweruse]').show();
					jQuery('a[id=showapiansweruse]').text('Hide JSON');
					jQuery('a[id=showapiansweruse]').attr('status', 'on');
				} else {
					jQuery('div[id=divapiansweruse]').hide();
					jQuery('a[id=showapiansweruse]').text('Show JSON');
					jQuery('a[id=showapiansweruse]').attr('status', 'off');
				}
			});
		});
		</script>
<?PHP
		echo '<div id="divapiansweruse" style="display: none;"><textarea rows="7" cols="80">'.json_encode($jsonArr).'</textarea><br><a href="https://jsoneditoronline.org" target="_blank">You might copypaste the JSON to jsoneditoronline.org to analyze the JSON in detail</a><hr></div>';
###############	
	
	$wwj = new workWithJSON($jsonArr);
	$noofshowedlistitems = 5;
	$jtz =  $wwj->showJSON($jsonArr, "", $noofshowedlistitems, 1, $openall);
	
	echo '<input type="text" id="plugins4_q" value="" placeholder="search JSON">';
	echo '<link rel="stylesheet" href="'.plugin_dir_url(__FILE__).'/js/jstree/dist/themes/default/style.min.css" />';
	echo '<script src="'.plugin_dir_url(__FILE__).'/js/jstree/jQuery/jquery.min.js"></script>';
	if ($addcheckboxes) {
		echo '&nbsp;<a href="#" id="chkSelectAll" >Check All</a>';
		echo '&nbsp;&nbsp;&nbsp;<a href="#" id="chkUnSelectAll" >Uncheck All</a><br />';
	}
	echo '<div id="jci_json_jstree">';
	echo $jtz;
	echo '</div>';
	echo '<script src="'.plugin_dir_url(__FILE__).'/js/jstree/dist/jstree.min.js"></script>';
?>
    <script type="text/javascript">	
		$(function () {
			$('#chkSelectAll').click(function() {          
				$("#jci_json_jstree").jstree().check_all(true);				   
			}); 
			$('#chkUnSelectAll').click(function() {          
				$("#jci_json_jstree").jstree().uncheck_all(true);
			}); 
			
			$('#jci_json_jstree').jstree( {
				"core" : {
					"check_callback" : false
				},
				
//  "dnd": {	
  // check_while_dragging: true,
//    use_html5: true,
  //  open_timeout: 750,
//    large_drop_target: true, // Comment out to see behavior change
    //touch: "selected"
  //},
				
				
				<?PHP if ($addcheckboxes) { echo ' "checkbox" : {  "keep_selected_style" : false    },	'; } ?>
				"plugins" : [ 
					<?PHP if ($addcheckboxes) { echo '"checkbox", '; } ?>
//					, "themes", "search", "dnd" ]
					, "themes", "search" ]
				}
				
				
				
				
				
				
				
				
				
			);
			
	/*		
$(document).on("dnd_start.vakata.jstree", function(event, data) {
	let draggedTmp = data.data.obj.selector;// + "WE" + data.data.obj.text();
	dragged = draggedTmp.replace(/^\#/, "");  
	dragged = dragged.replace(/\\./g, ".");  
});

$(".box").on("dragover", function(event) {
  // Make the box appear as though things can be dropped onto it.
  event.preventDefault();
  event.originalEvent.dataTransfer.dropEffect = "copy";
});

$(".box").on("drop", function(event) {
	alert(dragged);
  $(".box").append('{{ _context[\'' + dragged + '\'] }}');
});			
*/
			
			<?PHP
				if (isset($_POST["seljs"])) {
					$selnodes = $_POST["seljs"];
					$selnodesArr = explode(",", $selnodes);
					foreach ($selnodesArr as $selnode) {
						echo "$('#jci_json_jstree').jstree(true).check_node('".$selnode."');\n";
					}
				} else {
					//echo "$('#jci_json_jstree').jstree(true).check_all();\n";
				}
			?>
	
			// search
			var to = false;
			$('#plugins4_q').keyup(function () {
				if(to) { 
					clearTimeout(to); 
				}
				to = setTimeout(function () {
					var v = $('#plugins4_q').val();
					$('#jci_json_jstree').jstree(true).search(v);
				}, 250);
			});
		});
	</script>
<?PHP					
}

function getTemplateFromDB($templateId) {
	global $wpdb;
	$what = " id, nameoftemplate, urloftemplate ";
	if ($templateId>0) {
		$where = " WHERE id=$templateId ";
		$what = " * ";
	}
	$getTemplateNames = $wpdb->get_results( 'SELECT '.$what.' FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates' . $where);
	return $getTemplateNames;
}



function register_jci_pro_templates($tm_data) {
	edit_jci_pro_templates($tm_data);
}

function edit_jci_pro_templates($tm_data, $showonlyurlsettings=FALSE) {
	#var_Dump($_POST);
  global $wpdb;
  $errormsg = "";
  $msg = "";

  if (isset($_GET['action']) && $_GET['action'] == 'delete') {
	  global $wpdb;
	  $deleteErrorLevel = $wpdb->delete( $wpdb->prefix . "plugin_jci_pro_templates", array( 'id' => $_GET['id'] ) );
    if ($deleteErrorLevel) {
      $errormsg = '<span style="color:#4CC417;">Success deleting Template</span>';
    }
  }
  
	if (empty($_POST['nameoftemplate'])) {
  		$_POST['nameoftemplate'] = time()."-".rand();
	}

  if (isset($_POST['update']) && $_POST['update'] ) {
	   remove_param_quotes();
	   global $wpdb;

     $checkUniqueNameOfTemplate = $wpdb->get_results( 'SELECT COUNT(*) AS ANZ FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates
              WHERE nameoftemplate=\''.$_POST['nameoftemplate'].'\' AND NOT id='.$_POST['update_id']  ); #
     if ($checkUniqueNameOfTemplate[0]->{"ANZ"}>0) {
        #this name is given at another template: do not accept it!
  	   $errormsg = '<span style="color:#f00;">Changed template NOT saved: Set a unique (!!!) template-name, please!</span>';
     } else {
		if (!isset($_POST['postpayload'])) { $_POST['postpayload']=''; }
		if (!isset($_POST['postbody'])) { $_POST['postbody']=''; }
		if (!isset($_POST['cachetime'])) { $_POST['cachetime']=0; }
		if (!isset($_POST['urlgettimeout'])) { $_POST['urlgettimeout']=0; }
		if (!isset($_POST['debugmode'])) { $_POST['debugmode']=0; }
		$templ = $_POST['template'];
		if (empty(get_option('jci_pro_selected_editor'))) {
			$jciproseled = "ace";
		} else {
			$jciproseled = get_option('jci_pro_selected_editor');
		}
		if ($jciproseled == "ace") {
			$templ = preg_replace("/\+/", "%2B", $templ);
			$templ = urldecode($templ); # aceeditor gives urlencoded template
		}
		$tmp_template =  $templ; # aceeditor gives urlencoded template
	
		$wpdb->update(
	 	    $wpdb->prefix . "plugin_jci_pro_templates",
	 	    array( 
			'template' => $tmp_template
			, 'nameoftemplate' => $_POST['nameoftemplate']
			, 'urloftemplate' => $_POST['urloftemplate']
			, 'basenode' => $_POST['basenode'] 
			, 'method' => $_POST['method'] 
			, 'parser' => $_POST['parser'] 
			, 'postpayload' => $_POST['postpayload'] 
			, 'postbody' => $_POST['postbody'] 
			, 'curloptions' => $_POST['curloptions'] 
			, 'cachetime' => $_POST['cachetime'] 
			#  , 'urlparam' => $_POST['urlparam'] 
			, 'urlgettimeout' => $_POST['urlgettimeout'] 
			, 'urlparam4twig' => $_POST['urlparam4twig'] 
			, 'debugmode' => $_POST['debugmode'] 
			),
				array( 'id' => $_POST['update_id'] )
			);
			$errormsg = '<span style="color:#4CC417;">Changed template saved!</span>';
		}
	}

	if (isset($_GET['action']) && $_GET['action'] == 'copy') {
		$table = $wpdb->get_row( "SELECT * from " . $wpdb->prefix . "plugin_jci_pro_templates WHERE id = " . sanitize_text_field($_GET['id']) . "" );
		echo '<h1>Edit template '.sanitize_text_field($_GET['id']). ' and then store it as new template:</h1>';
		showTemplateItem($table, $errormsg, "add", $showonlyurlsettings, TRUE);
		return "";
	}


	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		$table = $wpdb->get_row( "SELECT * from " . $wpdb->prefix . "plugin_jci_pro_templates WHERE id = " . sanitize_text_field($_GET['id']) . "" );
		echo '<h1>Edit template '.sanitize_text_field($_GET['id']).'</h1>';
		showTemplateItem($table, $errormsg, "change", $showonlyurlsettings);
		return "";
	}



	// show templates in table BEGIN
	$sql_where = "";
	$searchstring = htmlentities($_POST["s"]);
	if (!empty($searchstring)) {
		$sql_where = " WHERE (template LIKE '%$searchstring%') OR (nameoftemplate LIKE '%$searchstring%') OR (urloftemplate LIKE '%$searchstring%')";
	}

	// get items
	$tb_items1 = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates '.$sql_where.' ORDER BY id DESC', ARRAY_A );
	# echo "<hr>".htmlentities(json_encode($tb_items1))."<hr>";
	# echo count($tb_items1);
	$TB_WP_List_Table_Obj = new Templates_WP_List_Table($tb_items1);
	echo '<h1>JSON Content Importer Plugin: Template-Manager</h1><div class="wrap">';
	echo '<form method="post" action="admin.php?page=jciprotemplateslug">';
	echo '<input type="hidden" name="page" value="jciprotemplateslug">';
	$TB_WP_List_Table_Obj->prepare_items();
    $TB_WP_List_Table_Obj->search_box( 'search Templatename, Templatecode, URL of Template', 'search_id' );
	$TB_WP_List_Table_Obj->display();
	echo '</form>';  
	echo '</div>';
}
// show templates in table END */


/* EDD BEGIN */
function edd_jcipro_license_page() {
	$license 	= get_option( 'edd_jcipro_license_key' );
	$status 	= get_option( 'edd_jcipro_license_status' );
	$wpml_in_use = FALSE;
	if (defined('ICL_SITEPRESS_VERSION')) {
		$wpml_in_use = TRUE;
	}

	?>
	<div class="wrap">
	<h2>JCI pro Plugin License Options</h2>
	<form method="post" action="options.php">
	<table class="form-table" border="1">
		<tr valign="top" bgcolor="#fff">
			<td scope="row" valign="top" colspan="2">
				Step 1: Enter your licence key and save it<br>
				Step 2: Activate your Licence at the JCI-Licencing Server<br>
				<a href="https://json-content-importer.com/your-downloads/" target="_blank">Manage all your licences at json-content-importer.com</a><br>
			</td></tr>
		<tr valign="top" bgcolor="#ccc">
			<td scope="row" valign="top">
				<?php settings_fields('edd_jcipro_license'); ?>
				<strong><?php _e('License Key'); ?></strong>
			</td><td>
				<input id="edd_jcipro_license_key" name="edd_jcipro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
				<?php submit_button('Enter your license key and save it'); ?>
			</td></tr>
				<?php 
					if(!empty($license) && (false !== $license) ) {
				?>
					<tr valign="top">
						<td scope="row" valign="top" width=10%>
							<strong><?php _e('Activate License'); ?></strong>
						</td>
						<td>
							<?php if( $status !== false && $status == 'valid' ) { ?>
								<span style="color:green;"><?php _e('active'); ?></span>
								<?php wp_nonce_field( 'edd_jcipro_nonce', 'edd_jcipro_nonce' ); ?>
								<input type="submit" class="button-secondary" name="edd_jcipro_license_deactivate" value="<?php _e('Deactivate License '.htmlentities ($license)); ?>"/>
							<?php } else { 
								wp_nonce_field( 'edd_jcipro_nonce', 'edd_jcipro_nonce' ); 
							?>
								<input type="submit" class="button-secondary" name="edd_jcipro_license_activate" value="<?php _e('Activate License '.htmlentities ($license)); ?>"/>
							<?php } ?>
						</td>
					</tr>
				<?php
					$license_lc = trim(get_option('edd_jcipro_license_lc')); # time of last licence check
					$license_lv = trim(get_option('edd_jcipro_license_lv')); #
					$license_errormsg = trim(get_option('edd_jcipro_license_errormsg')); #
					$license_errormsgacdeac = trim(get_option('edd_jcipro_license_errormsgacdeac')); #
					$license_lifetime = trim(get_option('edd_jcipro_license_lifetime')); #

					if (!empty($license_errormsgacdeac)) {
						echo '<tr valign="top" bgcolor="#ccc"><td scope="row" valign="top"><strong>';
						_e('Licencing Server');
						echo '</strong></td><td>';
						echo $license_errormsgacdeac;
						echo "</td></tr>";
					}

					echo '<tr valign="top"><td scope="row" valign="top"><strong>';
					_e('Licencing Status');
					echo '</strong></td><td>';

						if($license_lc>0) {
							#$license_lc_out = date_i18n( get_option( 'date_format' ), strtotime( $license_lc, current_time( 'timestamp' ) ) ); 
							$license_lc_out = date_i18n( get_option( 'date_format' ), $license_lc ); 
							$license_lc_out = date("Y-m-d H:i:s", $license_lc); 
							echo "Last licence check: ".$license_lc_out."<br>";
							if ($license_lv==-1) {
								echo "Result of last licence check: ok<br>";
								if ($license_lifetime!=-1) {
									$license_lifetime_out = $license_lifetime; 
									#$license_lifetime_out = date_i18n( get_option( 'date_format' ), strtotime( $license_lifetime, current_time( 'timestamp' ) ) ); 
									echo "Licence valid until: ".$license_lifetime_out."<br>";
								}
							#} else {
							#	echo "NOT ok: $license_lv";
							}
							if ($license_errormsg!="") {
								echo "Licencing-Errormessage: ".$license_errormsg."<br>";
							}
							if ($license_errormsgacdeac!="") {
							#	echo "Licence active / deactive: ".$license_errormsgacdeac;
							}
							echo "</td></tr>";
						#} else {
						#	echo '<tr valign="top"><th scope="row" valign="top">';
						#	_e('License Check');
						#	echo '</th><td>';
						#	echo "No licence check up to now. This will be done with the next usage of the Plugin.";
						#	echo "</td></tr>";
						} else {
							echo "Activate the licence with a valid licence key";
							echo "</td></tr>";
						}
				#	}
               ?>
				<tr valign="top"  bgcolor="#ccc">
					<td colspan="2">
In case it's not working:
<ul>
<li>* <a href="https://json-content-importer.com/your-downloads/" target="_blank">If all licences are in use: Manage, where the licences are connected to. Or add another licence</a></li>
<?PHP
	$requestingDomain = edd_jcipro_get_requestingDomain();
	if ($wpml_in_use) {
		echo "<li> * ";
		echo "<strong>The WPML-Plugin is in use - you do not need a licence for each language: In this case $requestingDomain is used for the JCI-Licence</strong>";  # $_SERVER['HTTP_HOST'] is sent by the request, $_SERVER['SERVER_NAME'] is from the server config
		#phpinfo();
		echo "</li>";
	} else {
		echo '<li>* For this Wordpress that Domain is used for connecting a JCI-licence: <strong>'.$requestingDomain.'</strong></li>';
		
	}
?>
<li>* If you get a "Are you sure?"-page when trying to activate the plugin: Deactivate all other plugins and try it again. Then reactivate the other plugins.</li>
</ul>


</td></tr>
<tr><td colspan="2" bgcolor="#fff">
          <h1>Software Licenses:</h1>
		  <h2>This plugin, the "JSON Content Importer PRO" Software</h2>
<pre>
Copyright (c) <?PHP echo date("Y"); ?>, Bernhard Kux, Munich, Germany
All rights reserved.
Redistribution and use in source and binary forms, with or without modification, is NOT PERMITTED.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

<hr>
<strong>Additional this Plugin is using third party software, released under the follwoing licences:</strong>
<h2>twig (template engine)</h2>
The BSD 3-License for the Twig-Software (many thanks to the Twig-Developement team):
Licencing notes see https://twig.symfony.com/license and here:
--begin of Twig-Licencing notes--
Copyright (c) 2009-2021 by the Twig Team, see AUTHORS for more details.

Some rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
- The names of the contributors may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
--end of Twig-Licencing notes--
<hr>
<h2>ace (editor)</h2>
<a href="https://ace.c9.io/" target="_blank">Ace</a> is a great Editor: Thanks to the people behind Ace. The Licence for Ace is at https://github.com/ajaxorg/ace/blob/master/LICENSE
--begin of Ace-Licencing notes--
Copyright (c) 2010, Ajax.org B.V.
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
- Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
- Neither the name of Ajax.org B.V. nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL AJAX.ORG B.V. BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
--end of Ace-Licencing notes--
</pre>
							</td>
						</tr>
					<?php } ?>
			</table>
		</form>
	</div>
	<?php
}

function edd_jcipro_register_option() {
	// creates our settings in the options table
	register_setting('edd_jcipro_license', 'edd_jcipro_license_key', 'edd_jcipro_sanitize_license' );
}
add_action('admin_init', 'edd_jcipro_register_option');

function edd_jcipro_sanitize_license( $new ) {
	$old = get_option( 'edd_jcipro_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_jcipro_license_status' ); // new license has been entered, so must reactivate

    update_option('edd_jcipro_license_lifetime', -1);
    update_option('edd_jcipro_license_lc', -1);
    update_option('edd_jcipro_license_lv', '');
    update_option('edd_jcipro_license_errormsg', '');
    update_option('edd_jcipro_license_errormsgacdeac', '');
	}
	return $new;
}

function edd_license_erroradd($errormsg) { # error when using the plugin
   update_option('edd_jcipro_license_errormsg', $errormsg);
}

function edd_license_erroradd_acdeac($errormsg) { #error when activating or deactivating
   update_option('edd_jcipro_license_errormsgacdeac', $errormsg);
}


function edd_jcipro_get_requestingDomain() {
	$requestingDomain = home_url();
	if (defined('ICL_SITEPRESS_VERSION')) {
		$requestingDomain = $_SERVER['SERVER_NAME'];
		if (""==$requestingDomain) {
			$requestingDomain = home_url();
		}
	}
	return $requestingDomain;
}


/* licencing */
function edd_jcipro_activate_license() {
	// listen for our activate button to be clicked
	if(! isset( $_POST['edd_jcipro_license_activate'] ) ) {
		return;
	}
edd_jcipro_log("start edd_jcipro_activate_license: input ok");
		edd_license_erroradd_acdeac('');
		edd_license_erroradd('');
		update_option('edd_jcipro_license_lv', '');
		// run a quick security check
	 	if( ! check_admin_referer( 'edd_jcipro_nonce', 'edd_jcipro_nonce' ) ) {
			edd_license_erroradd_acdeac("nonce failed trying to activate license");
			return FALSE; // get out if we didn't click the Activate button
		}
		// retrieve the license from the database
		$license = trim( get_option( 'edd_jcipro_license_key' ) );

		// data to send in our API request
		$requestingDomain = edd_jcipro_get_requestingDomain();
		$api_params = array(
			'edd_action'  => 'activate_license',
			'license'     => $license,
			'item_name'   => rawurlencode( EDD_JCIPRO_ITEM_NAME ), // the name of our product in EDD
			'url'         => $requestingDomain,
		);
		edd_jcipro_log("edd_jcipro_activate_license with ".$requestingDomain);

		// Call the custom API.
		$response = wp_remote_post( EDD_JCIPRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		
		// make sure the response came back okay
		$message = "";#.print_r($response, TRUE)."<BR>";
		$httpreturncode = wp_remote_retrieve_response_code( $response );
		if ( is_wp_error( $response ) || 200 !== $httpreturncode ) { 
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'Failed: Connecting Licencing-server '.EDD_JCIPRO_STORE_URL.' - Errornumber: '.$httpreturncode);
			}
			edd_license_erroradd_acdeac($message);
		edd_jcipro_log("result edd_jcipro_activate_license ($requestingDomain): $message");
			return FALSE;
		} else {
			$message .= __( 'Success: Connecting Licencing-server '.EDD_JCIPRO_STORE_URL.' for ' )."$requestingDomain at ".date("Y-m-d H:i:s")."<br>".__( 'Answer Licencing-server: ' );
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
edd_jcipro_log("result edd_jcipro_activate_license ($requestingDomain) answer: ".print_r($license_data, TRUE));
		if ( false === $license_data->success ) {
				switch( $license_data->error ) {
					case 'expired' :
						$message .= sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ),  $license_data->expires  )
							#date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked' :
						$message .= __( 'Your license key has been disabled.' );
						break;

					case 'missing' :
						$message .= __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :
						$message .= __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :
						$message .= __( 'This appears to be an invalid license key.' );
						break;

					case 'no_activations_left':
						$no_license_limit = $license_data->license_limit;
						$no_site_count = $license_data->site_count;
						$no_activations_left = $license_data->activations_left;
						if ($no_activations_left==0) {
							if ($no_license_limit==1) {
								#return "Your licencekey is ok. But: You activated this licencekey already for another Domain. Domain means the fully qualified domain name, e.g. www.xy.com and test.xy.com are different. First deactivate this licence and then activate it here, please. You can do this here: <a href=https://json-content-importer.com/your-downloads/ target=_blank>https://json-content-importer.com/your-downloads/</a>";
								$message .= __( 'Your licencekey is ok. But: You activated this licencekey already for another Domain. Domain means the fully qualified domain name, e.g. www.xy.com and test.xy.com are different. First deactivate the used licence and then activate it here, please. You can do this here: <a href=https://json-content-importer.com/your-downloads/ target=_blank>https://json-content-importer.com/your-downloads/</a>' );
							} else {
								#return "Your licencekey is ok. But: You activated all of your $no_license_limit licencekeys for other Domains. Domain means the fully qualified domain name, e.g. www.xy.com and test.xy.com are different. First deactivate one of these licences and then activate it here, please.You can do this here: <a href=https://json-content-importer.com/your-downloads/ target=_blank>https://json-content-importer.com/your-downloads/</a>";
								$message .= __( 'Your licencekey is ok. But: You activated all of your '.$no_license_limit.' licencekeys for other Domains. Domain means the fully qualified domain name, e.g. www.xy.com and test.xy.com are different. First deactivate one of these licences and then activate it here, please.You can do this here: <a href=https://json-content-importer.com/your-downloads/ target=_blank>https://json-content-importer.com/your-downloads/</a>' );
							}
						} else {
							#return "Your site licence is inactive: Check your licencekey, please.";
							$message .= __( 'Your site licence is inactive: Check your licencekey, please.' );
						}
						break;

					default :
						$message = __( 'An error occurred, please try again.' );
						break;
				}
			#$message .= "<br>".json_encode($license_data)."<br>";
			edd_license_erroradd_acdeac($message);
		} else {
			$message .= $license_data->license;
			#$message .= "<br>".json_encode($license_data)."<br>";
		}
		
			// $license_data->license will be either "valid" or "invalid"
			update_option( 'edd_jcipro_license_status', $license_data->license );

		if ($license_data->license=="valid") {
			update_option('edd_jcipro_license_lifetime', $license_data->expires );
			update_option('edd_jcipro_license_lc', time());
			update_option('edd_jcipro_license_lv', -1);
			if ($license_data->expires=="lifetime") {
				$message .= "<br>" . __( 'Licence: Lifetime' );
			} else {
				$message .= "<br>" . __( 'Licence expiration date: ' ) . $license_data->expires;
					#date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ); 
			}
		} else {
			# invalid licence
			update_option( 'edd_jcipro_license_lifetime', -1 );
			#edd_license_erroradd_acdeac("Licencing-Server: Invalid licence");
		}
			edd_license_erroradd_acdeac($message);
		#edd_license_erroradd_acdeac($message);

	#}
}

 add_action('admin_init', 'edd_jcipro_activate_license');



function edd_jcipro_check_license_showdebug($debugmsg) {
  echo "<i>DEBUG: ".$debugmsg."</i><br>";
}

function edd_jcipro_log($msg) {
	$doeddlicencinglog = FALSE;
	if (!$doeddlicencinglog) {
		return TRUE;
	}
	$msgcl = preg_replace("/(\n|\r|##)/", "", $msg);
	$msglog = date("Y.m.d, H:i:s")."##".$msgcl."\n";
	file_put_contents(plugin_dir_path(__FILE__)."eddlog.txt", $msglog, FILE_APPEND);
}


function edd_jcipro_check_license($inp="", $delaytime=86400) {
	#$delaytime= 0;
	$lviewer = "";
  if (isset($_GET["lv"])) {
    $lviewer = sanitize_text_field($_GET["lv"]);
  }
  $showldata = FALSE;

  if ($inp=="admininit") {
    $showldata = FALSE;
  } else {
    $val_jci_pro_debugmode = get_option('jci_pro_debugmode');
    if ($val_jci_pro_debugmode>1) {
      $showldata = TRUE;
    } else {
      $showldata = FALSE;
    }
  }
  #if ($inp=="") {
	$license_lc = trim(get_option('edd_jcipro_license_lc')); # time of last licence check
	
	if (empty($license_lc)) { $license_lc = -1; }
	
	$license_lv = trim(get_option('edd_jcipro_license_lv')); # status of last licence check
	$license_lifetime = trim(get_option('edd_jcipro_license_lifetime'));
	$timesincelastcheck = time()-$license_lc;
	if ($license_lv==-1 && ($timesincelastcheck<$delaytime)) {
		if ($showldata) {
			edd_jcipro_check_license_showdebug('valid licence cached (switch off this message in the plugin-option tab settings: "debugmode off")');
		}
		return -1;
	} else {
		if ($showldata) {
			edd_jcipro_check_license_showdebug('NO valid licence cached (switch off this message in the plugin-option tab settings: "debugmode off" )');
		}
	}
	update_option('edd_jcipro_license_lc', time());
	update_option('edd_jcipro_license_lv', "precheck");
  #}
  if ($showldata) {
    edd_jcipro_check_license_showdebug('start check of licence (switch off this message in the plugin-option tab settings: : "debugmode off")');
  }
	$license = trim( get_option( 'edd_jcipro_license_key' ) );
	$requestingDomain = edd_jcipro_get_requestingDomain();
	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => rawurlencode( EDD_JCIPRO_ITEM_NAME ),
		'url'       => $requestingDomain
	);
	if ($showldata) {
		edd_jcipro_check_license_showdebug("do licencecheck: ".substr($license,0,4)."...");
	}

	// Call the custom API.
	$response = wp_remote_post( EDD_JCIPRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
	if ( is_wp_error( $response ) ) {
		update_option('edd_jcipro_license_lc', time());
		update_option('edd_jcipro_license_lv', -1);
		if ($showldata) {
			edd_jcipro_check_license_showdebug("pass licencecheck");
		}
	edd_jcipro_log("VALID edd_jcipro_check_license $requestingDomain: error licencing server ".$response->get_error_message());
		return -1; # if licencing server is down: pass
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	if( $license_data->license == 'valid' ) {
		// this license is still valid: do nothing besides caching update
		update_option('edd_jcipro_license_lc', time());
		update_option('edd_jcipro_license_lv', -1);
		if ($showldata) {
		edd_jcipro_check_license_showdebug("valid licence found");
		}
	edd_jcipro_log("VALID: edd_jcipro_check_license $requestingDomain: ".json_encode($response));
		return -1;
	} else if (
		$license_data->license == 'site_inactive' ||
		@$license_data->error == 'no_activations_left'
	) {
		$no_license_limit = $license_data->license_limit;
		$no_site_count = $license_data->site_count;
		$no_activations_left = $license_data->activations_left;
		if ($no_activations_left==0) {
			if ($no_license_limit==1) {
				$retval = "Your licencekey is ok. But: You activated this licencekey (valid for 1 domain) already for another Domain. First deactivate this licence and then activate it here, please.";
			} else {
				$retval = "Your licencekey is ok. But: You activated this licencekey (valid for $no_license_limit domains) already for another Domain. First deactivate this licence and then activate it here, please.";
			}
			update_option('edd_jcipro_license_lv', $retval);
			edd_license_erroradd($retval);
edd_jcipro_log("VALID but all in use: edd_jcipro_check_license $requestingDomain: ".$retval);
			return $retval;
		}
	};
  $plugin_buy = '<a href="https://json-content-importer.com">buy Plugin</a>';
  $contact_developer = '<a href=https://json-content-importer.com/your-downloads/ target=_blank>https://json-content-importer.com/your-downloads/</a>';#<a href="https://json-content-importer.com" target="_blank">contact developer</a>';
  $retval = "unknown error - $contact_developer";
edd_jcipro_log("INVALID: edd_jcipro_check_license $requestingDomain: ".$license_data->license);
  if( $license_data->license == 'site_inactive' ) {
    $retval = "Licence of Plugin JSON Content Importer Pro not activated for $requestingDomain: Press 'Activate Licence' please.";
	} else if( $license_data->license == 'inactive' ) {
    $retval = "Licence of Plugin JSON Content Importer Pro inactive for $requestingDomain: Check your licence status at $contact_developer please.";
	} else if( $license_data->license == 'invalid' ) {
    $retval = "Invalid licencekey: Check your licencekey at $contact_developer please.";
	} else {
    $retval = "Problems with Licence of Plugin JSON Content Importer Pro (".$license_data->license.")<br>Check your licence status at $contact_developer please.";
	}
  update_option('edd_jcipro_license_status', $license_data->license );
  update_option('edd_jcipro_license_lc', time());
  update_option('edd_jcipro_license_lv', $retval);
  edd_license_erroradd($retval);

  if ($showldata) {
    edd_jcipro_check_license_showdebug("INVALID licence found: $retval");
  }
  return $retval;
}

// deac
function edd_jcipro_deactivate_license() {

	// listen for our activate button to be clicked
	if( !isset( $_POST['edd_jcipro_license_deactivate'] ) ) {
		return; 
	}
    edd_license_erroradd_acdeac('');
    edd_license_erroradd('');
		// run a quick security check
		
   	if( ! check_admin_referer( 'edd_jcipro_nonce', 'edd_jcipro_nonce' ) ) {
      edd_license_erroradd_acdeac("nonce failed trying to DEactivate license: Maybe deactivating other plugins helps.");
			return FALSE; // get out if we didn't click the Activate button
    }

		// retrieve the license from the database
		$license = trim( get_option( 'edd_jcipro_license_key' ) );

		// data to send in our API request
		$requestingDomain = edd_jcipro_get_requestingDomain();
		edd_jcipro_log("TRY edd_jcipro_deactivate_license: $requestingDomain ");
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => rawurlencode( EDD_JCIPRO_ITEM_NAME ), // the name of our product in EDD
			'url'       => $requestingDomain
		);

		// Call the custom API.
		$response = wp_remote_post( EDD_JCIPRO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		#340 if ( is_wp_error( $response ) ) {
		$httpreturncode = wp_remote_retrieve_response_code( $response );
		if ( is_wp_error( $response ) || 200 !== $httpreturncode ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			}
			edd_license_erroradd_acdeac("Licencing-Server: FAILED trying to DEactivate license ($message) for $requestingDomain");
			edd_jcipro_log("RESULT edd_jcipro_deactivate_license $requestingDomain: $message");
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		edd_jcipro_log("result edd_jcipro_deactivate_license $requestingDomain ".print_r($license_data, TRUE));
		
		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_jcipro_license_status' );
			update_option( 'edd_jcipro_license_lifetime', -1 );
			edd_license_erroradd_acdeac("Deactivating of licence ok. ");
		} else {
			edd_license_erroradd_acdeac("SERVER: ".json_encode($license_data));
			edd_license_erroradd_acdeac("Deactivating of licence failed. ");
		}

	#}
}
 add_action('admin_init', 'edd_jcipro_deactivate_license');

/* EDD END */
?>