<?php
/*
 * : JCI pro 20211119
 */
 
add_action( 'init', 'jsoncontentimporterproGutenbergBlock' );

function jcipro_checkCacheFolder($cacheBaseFolder, $cacheFolder) {
	# wp version 4.4.2 and later: "/cache" is not created at install, so the plugin has to check and create...
	 if (!is_dir($cacheBaseFolder)) {
	   $mkdirError = @mkdir($cacheBaseFolder);
	   if (!$mkdirError) {
		 # mkdir failed, usually due to missing write-permissions
		 $errormsg .= "<hr><b>".__('caching not working, plugin aborted', 'json-content-importer').":</b><br>";
		 $errormsg .= __("plugin / wordpress / webserver can't create", 'json-content-importer')."<br><i>".$cacheBaseFolder."</i><br>";
		 $errormsg .= __('therefore: set directory-permissions to 0777 (or other depending on the way you create directories with your webserver)', 'json-content-importer')."<hr>";
		 # abort: no caching possible
		 return $errormsg;
	   }
	 }

	 if (!is_dir($cacheFolder)) {
	   # $this->cacheFolder is no dir: not existing
	   # try to create $this->cacheFolder
	   $mkdirError = @mkdir($cacheFolder);
	   if (!$mkdirError) {
		 # mkdir failed, usually due to missing write-permissions
		 $errormsg .= "<hr><b>".__('caching not working, plugin aborted', 'json-content-importer').":</b><br>";
		 $errormsg .= __("plugin / wordpress / webserver can't create", 'json-content-importer')."<br><i>".$cacheFolder."</i><br>";
		 $errormsg .= __('therefore: set directory-permissions to 0777 (or other depending on the way you create directories with your webserver)', 'json-content-importer')."<hr>";
		 # abort: no caching possible
		 return $errormsg;
	   }
	 }
	 # $this->cacheFolder writeable?
	 if (!is_writeable($cacheFolder)) {
	   $errormsg .= __('please check cacheFolder', 'json-content-importer').":<br>".$cacheFolder."<br>".__('is not writable. Please change permissions.', 'json-content-importer');
	   #exit;
	   return $errormsg;
	 }
 }

	 function jcipro_addShortcodeParam($key, $value) {
		 if (trim($value)=="") {
			 return "";
		 }
		 $asc = $key.'='.$value;
		 $asc .= " ";
		 return $asc;
	 }


 function jci_pro_render( $attributes, $content ) {
	if (edd_jcipro_check_license()!=-1) { return "Plugin JSON Content Importer Pro not running: Check Licence!";	}
	if (!isset($attributes['jusid'])) {
		return "jusid not set";
	}
#	$attri = json_decode(get_option('jci_pro_api_use_items', 'ne jci_pro_api_use_items'), TRUE);
	$json_all_use_sets = json_decode(get_option('jci_pro_api_use_items'), TRUE);
	if (!isset($json_all_use_sets[$attributes['jusid']])) {
		return "Click on this Block and select a JSON-Use-Set in the Gutenberrg-Block settings";
	}
	$json_selected_use_set = $json_all_use_sets[$attributes['jusid']];
	if (!isset($json_selected_use_set["selectejas"])) {
		return "jusid: ".$attributes['jusid']." // json_selected_use_set json_selected_use_set // selectejas not set";
	}
	$selected_use_set_id = $json_selected_use_set["selectejas"];
	$json_all_access_sets = json_decode(get_option('jci_pro_api_access_items'), TRUE);

	#return $selected_use_set_id;
	#return json_encode($json_all_access_sets);
	
		#############################################################################	
		#######
		# do request
		
		$formdata = $json_all_access_sets[$selected_use_set_id]["set"];
		#return json_encode($formdata);

		# BEGIN param
		$debugModeIsOn = FALSE;
		$debugLevel = 0;
		$cacheEnable = FALSE;
		if (current_user_can( 'edit_posts')) {
			if ($attributes['showdebug']) {
				$debugModeIsOn = TRUE;
				$debugLevel = 10;
			}
		}
		if (isset($attributes['cachetime'])) {
			$cachetime = (int) trim($attributes['cachetime']);
			if ($cachetime>0) {
				$cacheEnable = TRUE;
			}
		}
		$methodTmp = $formdata["method"];
		# END: param
		
		# BEGIN buildrequest
		require_once plugin_dir_path( __FILE__ ) . '../lib/lib_request.php';
		$jci_request_handler = new jci_request_prepare($formdata, $methodTmp, $formdata["methodtech"]);
		$curloptions = $jci_request_handler->getCurlOptionsString();
		$curloptions4Request = $jci_request_handler->getCurlOptions4Request($curloptions);
		$selectedmethod = $jci_request_handler->getSelectedmethod();
		# END buildrequest

		if ($cacheEnable) {
			$defaultcachepath = WP_CONTENT_DIR . "/cache/jsoncontentimporterpro/";
			$cachepath = get_option('jci_pro_cache_path');
			if (empty($cachepath)) {
				$cachepath = $defaultcachepath;
				update_option('jci_pro_cache_path', $cachepath);
			}
			#$cacheFileFingerPrint = $selectedJAS["nameofjas"]."-".json_encode($formdata);
			$cacheFileFingerPrint = json_encode($formdata);
			$cacheFile = $cachepath.md5($cacheFileFingerPrint).".cgi";
			$cacheExpireTime = $cachetime; # caching for 24 hrs
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
			require_once plugin_dir_path( __FILE__ ) . '../class-fileload-cache-pro.php';
		}
		$header = "";
		$urlencodepostpayload = "";
		$encodingofsource = "";
		$httpstatuscodemustbe200 = "no";
		$auth = "";
		$showapiresponse = FALSE;

		if ($debugModeIsOn) {
			require_once plugin_dir_path( __FILE__ ) . '../lib/logdebug.php';
			#logDebug::jci_addDebugMessage("formdata: ".print_r($formdata, TRUE), $debugModeIsOn, $debugLevel, 2, "", TRUE, FALSE, "", 70000); 
			$cachemsg = "off (Cachetime: ".$cachetime." Minutes)";
			if ($cacheEnable) {
				$cachemsg = "on, Expiretime: $cacheExpireTime Minutes";
			}
			logDebug::jci_addDebugMessage("Load JSON - Cache: ".$cachemsg, $debugModeIsOn, $debugLevel); 
			logDebug::jci_addDebugMessage("Load JSON - CacheFile: ".$cacheFile, $debugModeIsOn, $debugLevel); 
			logDebug::jci_addDebugMessage("Load JSON - URL via $selectedmethod: ".$formdata["jciurl"], $debugModeIsOn, $debugLevel); 
			logDebug::jci_addDebugMessage("Load JSON - payload: ".$formdata["payload"], $debugModeIsOn, $debugLevel); 
		}
		
		$out = "";#cacheExpireTime: $cacheExpireTime // cacheFile: $cacheFile // ";		
		$cacheExpireTimeTimeAdded = time() - (60*$cacheExpireTime);
        $fileLoadWithCacheObj = new FileLoadWithCachePro(
            $formdata["jciurl"], $formdata["timeout"], $cacheEnable, $cacheFile, $cacheExpireTimeTimeAdded, $selectedmethod, NULL, '', '',
            $formdata["payload"], $header, $auth, $formdata["payload"],
            $debugLevel, $debugModeIsOn, $urlencodepostpayload, $curloptions4Request,
            $httpstatuscodemustbe200, $encodingofsource, $showapiresponse
            );
        $fileLoadWithCacheObj->retrieveJsonData();
		$receivedData = $fileLoadWithCacheObj->getFeeddataWithoutpayloadinputstr();
		$httpcode = $fileLoadWithCacheObj->getErrormsgHttpCode();
		#return "selectedmethod: $selectedmethod<br>receivedData:<hr>$receivedData<hr><br>httpcode: $httpcode<br>";
		# END LOAD
		#############################################################################	
	
		## get JSON-Array
		$convertJsonNumbers2Strings = FALSE;
		$inputtype = $formdata["indataformat"];
		$csv_delimiter = $formdata["csvdelimiter"];
		$csv_csvline = $formdata["csvline"];
		$csv_enclosure = $formdata["csvenclosure"];
		$csv_skipempty = $formdata["csvskipempty"];
		$csv_escape = $formdata["csvescape"];
		
        $jsonDecodeObj = new JSONdecodePro($receivedData, TRUE, $debugLevel, $debugModeIsOn, 
			$convertJsonNumbers2Strings, $cacheFile, $fileLoadWithCacheObj->getContentType(), 
			$inputtype, $csv_delimiter, $csv_csvline, $csv_enclosure, $csv_skipempty, $csv_escape);
		$receivedDataArr = $jsonDecodeObj->getJsondata();
		$receivedData = json_encode($receivedDataArr);
		
		require_once plugin_dir_path( __FILE__ ) . '../lib/lib_jsonselector.php';
		$jsonSelector = new jsonSelector();
		$seljs = $json_selected_use_set["seljs"];

		#return $seljs;
		$reducedJson = $jsonSelector->processJson($receivedData, $seljs); # reduced json
	
	#return $reducedJson;
	$reducedJsonArr = json_decode($reducedJson, TRUE);
	$template = $attributes['template'];  

	#return $template;
	$twigResult = $jci_request_handler->twig_string($reducedJsonArr, $template);
	#return $twigResult;
	
#	$out = "";
	if ($debugModeIsOn) {
		logDebug::jci_addDebugMessage("Loaded JSON: ".$receivedData, $debugModeIsOn, $debugLevel); 
		logDebug::jci_addDebugMessage("httpcode: ".$httpcode, $debugModeIsOn, $debugLevel); 
		logDebug::jci_addDebugMessage("seljs: ".$seljs, $debugModeIsOn, $debugLevel); 
		logDebug::jci_addDebugMessage("template: ".$template, $debugModeIsOn, $debugLevel); 
		logDebug::jci_addDebugMessage("twigResult: ".$twigResult, $debugModeIsOn, $debugLevel); 
	}
	if (current_user_can( 'edit_posts')) {
		if ($attributes['showjson']) {
			$out .= "<div style=\"background-color: #ddd; color: #000; padding: 10px;\">";
			$out .= "JSON:<br>";
			$out .= $reducedJson;
			$out .= "</div>";
		}
		
		if ($attributes['showdebug']) {
			$out .= "<div style=\"background-color: #ddd; color: #000; padding: 10px;\">";
			$out .= logDebug::$debugmessage;
			$out .= "</div>";
		}
		
	}
	$out .= $twigResult;
	return $out;		
 }
 



function jcipro_checkIntAttrib($value, $defaultvalue) {
	 $ret = $defaultvalue;
	 if (""!=$value) {
		 $valuetmp = $value;
		 if (is_numeric($valuetmp)) {
			 $ret = round($valuetmp);
		 }
	 }
	 return $ret;
 }

 function jcipro_add2Debug($debugmode, $message) {
	 if ($debugmode>0) {
		 return "<br>".$message;
	 }
	 return '';
 }


 function jcipro_buildDebugTextarea($message, $txt, $addline=FALSE) {
	 $norowsmax = 20;
	 $norows = $norowsmax; 
	 $strlentmp = round(strlen($txt)/90);
	 if ($strlentmp<20) {
	   $norows = $strlentmp;
	 }
	 $nooflines = substr_count($txt, "\n");
	 if ($nooflines > $norows) {
	   $norows = $nooflines;
	 }
	 if ($norows > $norowsmax) {
	   $norows = $norowsmax;
	 }
	 $norows = $norows + 2;
	 $out = $message."<br><textarea rows=".$norows." cols=90>".$txt."</textarea>";
	 if ($addline) {
	   $out .= "<hr>";
	 }
	 return $out;
 }

function jsoncontentimporterproGutenbergBlock() {

	wp_register_script(
		'jcipro-block-script', 
		plugins_url( 'jcipro-block.php', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-editor', 'wp-components'),
		filemtime( plugin_dir_path(__FILE__).'jcipro-block.php')
	);
	if (is_admin()) {
		wp_enqueue_script('jcipro-block-script');
	}
	$langpath = plugin_dir_path( __FILE__ ) . '../languages/' ;
	wp_set_script_translations( 'jcipro-block-script', 'json-content-importer', $langpath );
	load_plugin_textdomain('json-content-importer', false, $langpath);
	
	register_block_type( 'jci/jcipro-block-script', 
		array(
			'render_callback' => 'jci_pro_render',
			'attributes'	  => array(
				'apiURL'	 => array(
					'type' => 'string',
					'default' => '/json-content-importer/json/example1.json',
				),
				'template'	 => array(
					'type' => 'string',
					'default' => '',
				),
				'jusid'	 => array(
					'type' => 'string',
					'default' => '',
				),
				'cachetime'	 => array(
					'type' => 'string',
					'default' => '0',
				),
				'jsonuseset'	 => array(
					'type' => 'string',
					'default' => '',
				),
				'showjson'	 => array(
					'type' => 'boolean',
					'default' => true,
				),
				'showdebug'	 => array(
					'type' => 'boolean',
					'default' => false,
				),
			),
		)
	);
}
?>
