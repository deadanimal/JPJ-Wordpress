<?php
# version 20200328

class JSONdecodePro {
	/* internal */
	private $jsondata = "";
	private $feedData = "";
	private $jsonDecodeCompleteToArray = FALSE;
	private $isAllOk = TRUE;
	private $debugModeIsOn = FALSE;
	private $debugLevel = 2;
	private $convertJsonNumbers2Strings = FALSE;
	private $debugmessage = "";
	private $contenttype = "";
	private $cacheFile = "";
	private $inputtype="json";
	private $inputtypedelimiter = ",";
	private $inputtypecsvline = "\n";
	private	$inputtypeenclosure = '"';
	private $inputtypeskipempty = FALSE;
	private $inputtypeescape = "\\";


	public function __construct($feedData, $jsonDecodeCompleteToArray, $debugLevel, $debugModeIsOn, $convertJsonNumbers2Strings, $cacheFile="", $contenttyp="", 
			$inputtype="json", $inputtypedelimiter=",", $inputtypecsvline="\n", 
			$inputtypeenclosure='"', $inputtypeskipempty=FALSE, $inputtypeescape = "\\"
		){
		require_once plugin_dir_path( __FILE__ ) . '/lib/logdebug.php';
		$this->feedData = $feedData;
		$this->contenttyp = $contenttyp;
		$this->jsondata = "";
		$this->jsonDecodeCompleteToArray = $jsonDecodeCompleteToArray;
		$this->debugLevel = $debugLevel;
		$this->debugModeIsOn = $debugModeIsOn;
		$this->cacheFile = $cacheFile;
		$this->inputtype = $inputtype;
		$this->inputtypedelimiter = $inputtypedelimiter;
		$this->inputtypecsvline = $inputtypecsvline;
		$this->inputtypeenclosure = $inputtypeenclosure;
		$this->inputtypeskipempty = $inputtypeskipempty;
		$this->inputtypeescape = $inputtypeescape;
		
		if ("xml"==$this->inputtype) {
			$this->convertXML2JSON();
		}
		if ("csv"==$this->inputtype) {
			$this->convertCSV2JSON();
		}
		
		
		if ($convertJsonNumbers2Strings) {
			$this->convertJsonNumbers2Strings = TRUE;
		}
		$this->isAllOk = $this->decodeFeedData();
	}


		private function getJSONFromCache(){
					if (file_exists($this->cacheFile)) {
						$this->feedData = file_get_contents($this->cacheFile);
						$this->jci_load_collectDebugMessage("getJSONFromCache successful");
						return TRUE;
					} else {
						$this->jci_load_collectDebugMessage("getJSONFromCache failed");
						return FALSE;
					}
		}

	/* decodeFeedData: convert raw-json-data into array */
	/* return value: TRUE if all ok, FALSE if an error occured */
	private function decodeFeedData() {
		if(empty($this->feedData)) {
			$this->feedData = '{"nojsonvalue": "emptyapianswer"}';
			#$this->jci_load_collectDebugMessage("Empty JSON-Feed");
			#return FALSE;
		}
			if ($this->convertJsonNumbers2Strings) {    
				$this->feedData = preg_replace('/"([ ]*):([ ]*)([0-9.,]*)([ ]*)([,}])/', '"\1:\2"\3"\4\5', $this->feedData);
				$this->jci_load_collectDebugMessage("Convert JSON-Numbers to JSON-Strings to avoid unsatisfactory PHP-number-handling");
			}
			$this->jsondata =  json_decode($this->feedData, $this->jsonDecodeCompleteToArray);
			if (is_null($this->jsondata)) {
				# $enc = mb_detect_encoding($this->feedData);
				# utf8_encode JSON-datastring, then try json_decode again
				$this->jsondata = json_decode(utf8_encode($this->feedData), $this->jsonDecodeCompleteToArray);
				if (is_null($this->jsondata)) {
					# sometimes linefeeds in the string are corrupting the JSON
					$this->feedData = preg_replace("/([\n\r]*)/", "", $this->feedData);
					$this->jsondata =  json_decode($this->feedData, $this->jsonDecodeCompleteToArray);
					if (is_null($this->jsondata)) {
						# sometimes content-types gives us the key to convert to utf8
						if (!empty($this->contenttyp)) {
							if (preg_match("/charset=/", $this->contenttyp)) {
								$csArr = explode("charset=", $this->contenttyp);
								$ctsent = trim($csArr[1]);
							} else {
								$ctsent = $this->contenttyp;
							}
							$this->jci_load_collectDebugMessage("Try to convert Content-Type $ctsent to UTF-8 for being able to decode String to JSON");
							$str = @iconv($in_charset = $ctsent, $out_charset = 'UTF-8' , $this->feedData);
							$this->jsondata =  json_decode($str, $this->jsonDecodeCompleteToArray);
							if (is_null($this->jsondata)) {
								$this->jci_load_collectDebugMessage("FAILED: Try to convert Content-Type $ctsent to UTF-8 for being able to decode String to JSON");
							#	return FALSE; not needed
							} else {
								$this->jci_load_collectDebugMessage("OK: Try to convert Content-Type $ctsent to UTF-8 for being able to decode String to JSON");
							}
						}
						# last try: use cached json
						if (is_null($this->jsondata)) {				
							$jci_pro_api_errorhandling = get_option("jci_pro_api_errorhandling");
							if (
								(2==$jci_pro_api_errorhandling || 3==$jci_pro_api_errorhandling)
							) {
								$this->jci_load_collectDebugMessage("try to use JSON from the cache");
								if ($this->getJSONFromCache()) {
									$this->jsondata = json_decode($this->feedData, $this->jsonDecodeCompleteToArray);
									if (is_null($this->jsondata)) {
										$this->jci_load_collectDebugMessage("Failed: use JSON from the cache");
									} else {
										$this->jci_load_collectDebugMessage("Ok: use JSON from the cache");
									}
								}
							}
						}
					}
					if (
						(!is_Array($this->jsondata))
						&& (!is_Object($this->jsondata))
					) {
						$this->jci_load_collectDebugMessage("Invalid JSON-Feed after trying to heal JSON: Build artificial JSON-Feed with key 'nojsonvalue'. Value is the raw data from the API");
						#$tmp = $this->jsondata;
						unset($this->jsondata);
						$this->jsondata = array( "nojsonvalue" => $this->feedData);
					}
					return TRUE;
				}
			}
			if (
				(!is_Array($this->jsondata))
				&& (!is_Object($this->jsondata))
			) {  
				$this->jci_load_collectDebugMessage("Invalid JSON-Feed: Build artificial JSON-Feed with key 'nojsonvalue'. Value is the raw data from the API", 2, "", "<br>");
				$tmp = $this->jsondata;
				unset($this->jsondata);
				$this->jsondata = array( "nojsonvalue" => $tmp);
			}
			if (is_array($this->jsondata)) {
				if (isset($this->jsondata["jsonrpc"]) && ($this->jsondata["jsonrpc"]=="2.0") && isset($this->jsondata["result"]) && is_string($this->jsondata["result"])) {  
					$this->jci_load_collectDebugMessage("JSON-Feed is a jsonrpc 2.0 feed: try to convert result-string to array for twig-usage");
					$tmpArr =  json_decode(utf8_encode($this->jsondata["result"]), $this->jsonDecodeCompleteToArray);
					if (is_null($tmpArr)) {
						$this->jci_load_collectDebugMessage("JSON-Feed is a jsonrpc 2.0 feed: conversion failed");
					} else {
						unset($this->jsondata["result"]);
						$this->jsondata["result"] = $tmpArr;
						$this->jci_load_collectDebugMessage("JSON-Feed is a jsonrpc 2.0 feed: conversion successful, result-JSON is available for twig");
					}
				}
			} else {
				$this->jci_load_collectDebugMessage("no jsonrpc-detection: only with twig-parser, set parser=twig in shortcode for that");
			}
			return TRUE;
	}

  /* convert XML 2 JSON */
	public function convertXML2JSON() {
        $inputXMLtmp = str_replace("<soap:Body>","",$this->feedData);
        $inputXMLtmp = str_replace("</soap:Body>","",$inputXMLtmp);
        $xml = @simplexml_load_string($inputXMLtmp, "SimpleXMLElement", LIBXML_NOCDATA);
        $tmpFeedData = json_encode($xml);
        if ($tmpFeedData!="") {
          $this->feedData = $tmpFeedData;
        }
	}

  /* convert CSV 2 JSON */
	public function convertCSV2JSON() {
		$delimiter = ","; #field delimiter (one character only)
		if (!empty($this->inputtypedelimiter)) {
			$delimiter = $this->inputtypeparamArr_replacePlaceholders($this->inputtypedelimiter);
			$delimiter = preg_replace("/#TAB#/", "	", $delimiter);
		}
		
		$enclosure = '"'; # enclosure character (one character only)
		if (!empty($this->inputtypeenclosure)) {
			$enclosure = $this->inputtypeparamArr_replacePlaceholders($this->inputtypeenclosure);
		}

		$escape = "\\"; # Defaults as a backslash (\)
		if (!empty($this->inputtypeescape)) {
			$escape = $this->inputtypeparamArr_replacePlaceholders($this->inputtypeescape);
		}

		$csvline = "\n"; # 
		if (!empty($this->inputtypecsvline)) {
			$csvline = $this->inputtypeparamArr_replacePlaceholders($this->inputtypecsvline);
		}

		$skipempty = FALSE;
		if (!empty($this->inputtypeskipempty) && ("yes" == $this->inputtypeskipempty)) {
			$skipempty = TRUE;
		}
		
		$data = array();
		$csvLines = explode($csvline, $this->feedData);
		foreach ($csvLines as $line) {
			if ($skipempty && empty(trim($line))) {
				continue;
			}
			$tmp = str_getcsv($line, $delimiter, $enclosure, $escape);
			$data[] = $tmp;
		}
		$json = json_encode($data);
		if ($json) {
			$this->feedData = $json;
			return TRUE;
		}
	}

	private	function inputtypeparamArr_replacePlaceholders($value) {
		$value = preg_replace("/#QM#/i", "\"", $value);
		$value = preg_replace("/#CR#/i", "\r", $value);
		$value = preg_replace("/#LF#/i", "\n", $value);
		$value = preg_replace("/#BS#/i", "\\", $value);
		return $value;
	}


  /* get */
	public function getJsondata() {
		return $this->jsondata;
	}
  /* get */
	public function getIsAllOk() {
    return $this->isAllOk;
  }

    private function jci_load_collectDebugMessage($debugMessage, $debugLevel=2, $prefix="", $suffix="") {
		logDebug::jci_addDebugMessage($debugMessage, $this->debugModeIsOn, $this->debugLevel, $debugLevel, $suffix, TRUE, FALSE, $prefix, 400);
    }
}
/* class JSONdecode END */

/* class FileLoadWithCache BEGIN */
class FileLoadWithCachePro {
  /* internal */
  private $feedData = "";
  private $urlgettimeout = 5; # 5 seconds default timeout for get of JSON-URL
  private $cacheEnable = "";
  private $cacheFile = "";
  private $feedUrl = "";
  private $cacheExpireTime = 0;
  private $cacheWritesuccess = FALSE;
  private $method = "get";
  private $requestArgs = NULL;
  private $allok = TRUE;
  private $errormsgout = "";
  private $errorhttpcode = "";
  private $errorhttpcodestring = "";
  private $httpresponse = NULL;
  private $feedSource = "http";
  private $feedFilename = "";
  private $postPayload = "";
  private $header = "";
  private $auth = "";
  private $postbody = "";
  private $debugModeIsOn = FALSE;
  private $debugLevel = 2;
  private $urlencodepostpayload = '';
  private $curloptions = "";
  private $httpstatuscodemustbe200 = TRUE;
  private $debugmessage = "";
  private $contenttype = "";
  private $encodingofsource = "";
  private $showapiresponse = FALSE;
  private $apiresponseinfo = Array();
  private $followlocation = FALSE;

  public function getContentType() {
	  return $this->contenttype;
  }

  public function __construct($feedUrl, $urlgettimeout, $cacheEnable, $cacheFile, $cacheExpireTime, $method, $requestArgs, $feedSource, $feedFilename, $postPayload, $header, $auth, $postbody,
        $debugLevel, $debugModeIsOn, $urlencodepostpayload, $curloptions, $httpstatuscodemustbe200, $encodingofsource="", $showapiresponse=FALSE, $followlocation=FALSE
    ){
	require_once plugin_dir_path( __FILE__ ) . '/lib/logdebug.php';
    $this->cacheEnable = $cacheEnable;
    if ($urlgettimeout!="" && intval($urlgettimeout)>0) {
    #if (is_numeric($urlgettimeout) && $urlgettimeout>=0) {
      $this->urlgettimeout = $urlgettimeout;
    }
    $this->showapiresponse = $showapiresponse;
    $this->encodingofsource = $encodingofsource;
	$this->httpstatuscodemustbe200 = $httpstatuscodemustbe200;
    $this->cacheFile = $cacheFile;
    $this->feedUrl = $feedUrl;
    $this->feedSource = $feedSource;
    $this->feedFilename = $feedFilename;
    $this->cacheExpireTime = $cacheExpireTime;
      if ($method=="post" ||
        $method=="rawpost" ||
        $method=="get" ||
        $method=="curlget" ||
        $method=="curlpost" ||
        $method=="curldelete" ||
        $method=="curlput" ||
        $method=="rawget"
      ) {
      $this->method = $method;
    }
    $this->args = $requestArgs;
    $this->postPayload = $postPayload;
    $this->header = $header;
    $this->auth = $auth;
    if ("no"==$urlencodepostpayload) {
      $this->urlencodepostpayload = $urlencodepostpayload;
    }
    $this->curloptions = $curloptions;
    $this->debugLevel = $debugLevel;
    $this->debugModeIsOn = $debugModeIsOn;
	$this->followlocation = $followlocation;
    if (isset($postbody)) {
		$postbody = $this->replace_BRO_BRC($postbody);
		$this->postbody = $postbody;
		$postbodyout = $this->postbody;
		if (strlen($postbodyout)>10) {
			$postbodyout = substr($this->postbody, 0, 100)."... (length: ".strlen($this->postbody).")";
		}
		if ($this->method=="post") {
			$this->jci_load_collectDebugMessage("used postbody: ".$postbodyout);
		} else {
			$this->jci_load_collectDebugMessage("postbody IGNORED, this is used only if WP-POST is selected as method: ".$postbodyout);
		}
    }
}

  /* replace #BRO# and #BRC# */
private function replace_BRO_BRC($intxt) {
    $intxt = preg_replace("/#BRO#/", "[", $intxt);
    $intxt = preg_replace("/#BRC#/", "]", $intxt);
    return $intxt;
}


	/* get */
	public function getFeeddata() {
		return $this->feedData;
  }

  public function getFeeddataWithoutpayloadinputstr() {
    $payloadinputstrPattern = "##payloadinputstr##";
    $ret = $this->feedData;
    if (preg_match("/$payloadinputstrPattern/", $this->feedData)) {
      $tmpFeeddataArr = explode($payloadinputstrPattern, $this->feedData);
      $ret = $tmpFeeddataArr[0];
    }
    return $ret;
  }


  /* get errorlevel */
	public function getAllok() {
    return $this->allok;
  }
  /* get errorlevel */
	public function getErrormsg() {
    return $this->errormsgout;
  }
	public function getErrormsgHttpCode() {
    return $this->errorhttpcode;
  }
	public function getErrormsgHttpCodeString() {
    return $this->errorhttpcodestring;
  }
	public function getHttpResponse() {
    return $this->httpresponse;
  }

	public function getApiresponseinfo() {
		if ($this->showapiresponse) {
			return $this->apiresponseinfo;
		}
		return NULL;
	}

  /* retrieveJsonData: get json-data and build json-array */
	public function retrieveJsonData(){
		if ($this->feedSource == "file") {
			$this->retrieveFeedFromFilesystem();
		} else {
			$isRetrieveFromWebok = TRUE;
			# check cache: is there a not expired file?
			if ($this->cacheEnable) {
				# use cache
				if ($this->isCacheFileExpired()) {
					# get json-data from cache
					$this->retrieveFeedFromCache();
				} else {
					$isRetrieveFromWebok = $this->retrieveFeedFromWeb();
				}
			} else {
				# no use of cache OR cachefile expired: retrieve json-url
				$isRetrieveFromWebok = $this->retrieveFeedFromWeb();
			}
			if (!$isRetrieveFromWebok) {
				# requesting JSON from the API via web failed
				# use cached JSON, if needed and available:
				# 1: use cache if http-response is not 200 --> this is handled here
				# 2: use cache if response is not JSON
				# 3: use cache if http-response is not 200 OR not JSON --> this is handled here
				$jci_pro_api_errorhandling = get_option("jci_pro_api_errorhandling");
				if (
					(1==$jci_pro_api_errorhandling || 3==$jci_pro_api_errorhandling)
					&& (200!=$this->errorhttpcode)
					) {
						# not 200, use cached JSON
						$this->getJSONFromCache();
				}
			}
		}
		
		if(""==$this->feedData) {
			$this->feedData = '{"nojsonvalue": "emptyapianswer"}';
			/*
			#if(empty($this->feedData)) {
			if ($this->allok) {
				$errormsg = stripslashes(get_option('jci_pro_errormessage'));
				if ($errormsg=="") {
					$this->errormsgout .= "error: get of json-data failed - plugin aborted: check json-feed";
				} else {
					$this->errormsgout .= $errormsg." (100)";
				}
			}
			$this->allok = FALSE;
			return FALSE;
			*/
		}
	}
	

	private function getJSONFromCache(){
		if(!class_exists('jci_Cache')) {
			require_once plugin_dir_path( __FILE__ ) . '/lib/cache.php';
		}
		$cacheFolderObj = new jci_Cache();
		$this->cacheFile = $cacheFolderObj->getCacheFileName($this->feedUrl, $this->postPayload, $this->postbody);
		if (file_exists($this->cacheFile)) {
			$this->feedData = file_get_contents($this->cacheFile);
			$this->allok = TRUE;
		}
	}
	

      /* isCacheFileExpired: check if cache enabled, if so: */
		public function isCacheFileExpired(){
			# get age of cachefile, if there is one...
      if (file_exists($this->cacheFile)) {
        $ageOfCachefile = filemtime($this->cacheFile);  # time of last change of cached file
      } else {
        # there is no cache file yet
        return FALSE;
      }

      # if $ageOfCachefile is < $cacheExpireTime use the cachefile:  isCacheFileExpired = FALSE
      if ($ageOfCachefile < $this->cacheExpireTime) {
        return FALSE;
      } else {
        return TRUE;
      }
		}

    /* storeFeedInCache: store retrieved data in cache */
		private function storeFeedInCache(){
		  if (!$this->cacheEnable) {
        # no use of cache if cache is not enabled or not working
        return NULL;
      }
      $handle = fopen($this->cacheFile, 'w');
			if(isset($handle) && !empty($handle)){
				$this->cacheWritesuccess = fwrite($handle, $this->feedData); # false if failed
				fclose($handle);
        if (!$this->cacheWritesuccess) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
            $this->errormsgout .= "cache-error:<br>".$this->cacheFile."<br>can't be stored - plugin aborted";
          } else {
            $this->errormsgout .= $errormsg." (300)";
          }
          $this->allok = FALSE;
          return "";
        } else {
          return $this->cacheWritesuccess;
        }
			} else {
        $errormsg = stripslashes(get_option('jci_pro_errormessage'));
        if ($errormsg=="") {
          $this->errormsgout .= "cache-error:<br>".$this->cacheFile."<br>is either empty or unwriteable - plugin aborted";
        } else {
          $this->errormsgout .= $errormsg." (400)";
        }
        $this->allok = FALSE;
        return "";
      }
		}

		public function retrieveFeedFromFilesystem(){
      $val_jci_pro_json_fileload_basepath = get_option('jci_pro_json_fileload_basepath'); # basepath
      if ($val_jci_pro_json_fileload_basepath=="") {
        $val_jci_pro_json_fileload_basepath = WP_CONTENT_DIR;
      }
      if (!preg_match("/\/$/", $val_jci_pro_json_fileload_basepath)) {
        $val_jci_pro_json_fileload_basepath .= "/";
      }
      $filename = $this->feedFilename;
      if (preg_match("/^./", $filename)) {  # protect access to dirs beyond default-dir
        $filename = preg_replace("/(\.\.\/)+/", "", $filename);
      }
      $completefilename = $val_jci_pro_json_fileload_basepath.$filename;
      # get json-feed from filesystem
      $getFile = FALSE;
			if(file_exists($completefilename)) {
        $getFile = @file_get_contents($completefilename);
      }
      if (!$getFile) {
        # file loading failed
        $errormsg = stripslashes(get_option('jci_pro_errormessage'));
        if ($errormsg=="") {
          $this->errormsgout .= "reading of $completefilename failed";
        } else {
          $this->errormsgout .= $errormsg." (error: readfile)";
        }
        $this->allok = FALSE;
        return FALSE;
      } else {
        $this->feedData = $getFile;
        return TRUE;
      }
      return FALSE;
    }

	private function is_relative_url($url) {
		if (preg_match("/^\//", $url)) {
			return TRUE;
		}
		return FALSE;
	}
	
	private function add_domain_to_url($url) {
		return home_url().$url;
	}

	public function retrieveFeedFromWeb(){
      # wordpress unicodes http://openstates.org/api/v1/bills/?state=dc&q=taxi&apikey=4680b1234b1b4c04a77cdff59c91cfe7;
      # to  http://openstates.org/api/v1/bills/?state=dc&#038;q=taxi&#038;apikey=4680b1234b1b4c04a77cdff59c91cfe7
      # and the param-values are corrupted
      # un_unicode ampersand:
      $this->feedUrl = preg_replace("/&#038;/", "&", $this->feedUrl);
	  if ($this->is_relative_url($this->feedUrl)) {
			$this->feedUrl = $this->add_domain_to_url($this->feedUrl);
	  }
	  
      $argsHeader = array(
            'timeout'     => $this->urlgettimeout,
			'followlocation' => $this->followlocation
            );
      $val_jci_pro_http_header_useragent = get_option('jci_pro_http_header_useragent');
      if ($val_jci_pro_http_header_useragent!="") {
        $argsHeader["user-agent"] = $val_jci_pro_http_header_useragent;
      }

      $argsHeaderSub = array();
            #'httpversion' => '1.0',
            #'blocking'    => true,
            #'headers'     => array(),
            #'cookies'     => array(),
            #'body'        => null,
            #'compress'    => false,
            #'decompress'  => true,
            #'sslverify'   => true,
            #'stream'      => false,
            #'filename'    => null

      $val_jci_pro_allow_oauth_code = get_option('jci_pro_allow_oauth_code');
      if ($val_jci_pro_allow_oauth_code!="") {
          if (preg_match("/^Basic /", $val_jci_pro_allow_oauth_code)) {
            $outhheader = $val_jci_pro_allow_oauth_code;
          } else {
            $outhheader = 'Bearer '.$val_jci_pro_allow_oauth_code;
          }
          $argsHeaderSub["Authorization"] = $outhheader;
          $this->jci_load_collectDebugMessage("Add OAuth-Key from plugin-options to header: Authorization=".$outhheader);
          $argsHeader["sslverify"] = FALSE;
      }
      if ($this->auth!="") {
        $authShortcodeParamArr = explode("##", $this->auth);
        for ($i=0; $i<count($authShortcodeParamArr);$i++) {
          if (trim($authShortcodeParamArr[$i])=="") {
            continue;
          }
          $authTmpArr = explode(":", $authShortcodeParamArr[$i], 2);
          if (trim($authTmpArr[0])!="" && $authTmpArr[1]!="") {
            if (preg_match("/^true$/i", trim($authTmpArr[1]))) {
               $authTmpArr[1] = TRUE;
            }
            if (preg_match("/^false$/i", trim($authTmpArr[1]))) {
               $authTmpArr[1] = FALSE;
            }
            $argsHeaderSub[$authTmpArr[0]] = $authTmpArr[1];
          }
        }
      }

      $val_jci_pro_http_header_accept = get_option('jci_pro_http_header_accept');
      if ($val_jci_pro_http_header_accept!="") {
        $argsHeaderSub["Accept"] = $val_jci_pro_http_header_accept;
        $this->jci_load_collectDebugMessage("Add Accept from plugin-options to header: Accept=".$val_jci_pro_http_header_accept);
      }

      if ($this->header!="") {
        $headerShortcodeParamArr = explode("##", $this->header);
        for ($i=0; $i<count($headerShortcodeParamArr);$i++) {
          if (trim($headerShortcodeParamArr[$i])=="") {
            continue;
          }
          $hscTmpArr = explode(":", $headerShortcodeParamArr[$i], 2);
          if (trim($hscTmpArr[0])!="" && $hscTmpArr[1]!="") {
		  $argsHeaderSub[$hscTmpArr[0]] = $hscTmpArr[1];
            $this->jci_load_collectDebugMessage("Add header: key=".$hscTmpArr[0].", value=".$hscTmpArr[1]);
          }
        }
      }

      $argsHeader["headers"] = $argsHeaderSub;
	    $payloadInputJSONstr = "";

      if ($this->postPayload!="") {
        $this->jci_load_collectDebugMessage("POST-payload (before POSTGET-replacement): ".$this->postPayload);
        $this->postPayload = $this->replace_BRO_BRC($this->postPayload);

        ### insert data: match on a POSTGET_ field to be replaced by input values from $_GET or $_POST
        $number_of_to_be_filled_payloadfields = preg_match_all("/\"([a-z0-9]+)\"([ ]*):([ ]*)\"POSTGET_([a-z0-9]+)\"/i", $this->postPayload, $match_filler);
        if ($number_of_to_be_filled_payloadfields>0) {
  	      for ($i=0; $i<$number_of_to_be_filled_payloadfields; $i++) {
            $foundString = $match_filler[0][$i];
            $fi = trim($match_filler[1][$i]);
            $input = @sanitize_text_field($_GET[$fi]);
            if (empty($input)) {
              $input = @sanitize_text_field($_POST[$fi]);
            }
            $this->postPayload = preg_replace("/\"POSTGET_".$fi."\"/", "\"".$input."\"", $this->postPayload);
            $payloadInputArr[$fi] = $input;
          }
          $this->jci_load_collectDebugMessage("POST-payload (after POSTGET-replacement): ".$this->postPayload);
        } else {
          # static payload: no POSTGET_
          $this->jci_load_collectDebugMessage("POST-payload: no POSTGET-replacement");
        }
		 
		if (isset($payloadInputArr)) {
		    $payloadInputJSONstr = json_encode($payloadInputArr);
		   } else {
		    $payloadInputJSONstr = $this->postPayload;
        }

        # shortcodeparam payload (which must be a valid JSON-feed) has more for POST-requests
 	      $payloadArr =  json_decode($this->postPayload, TRUE);
        if (is_array($payloadArr)) {
		$argsHeader["payload"] = $payloadArr;
          $this->jci_load_collectDebugMessage("POST-payload sent to API: ".print_r($argsHeader["payload"], TRUE));
        } else {
			$argsHeader["payload"][] = $this->postPayload;
			$this->jci_load_collectDebugMessage("Invalid POST-payload - must be valid JSON: ".$this->postPayload);
        }
      }

	if ("dummyrequest" == $this->feedUrl || "localrequest" == $this->feedUrl) {
        $postparam = get_post();

			$out = array();
			$out["time"] = time();
			$request_body = file_get_contents('php://input');
			$out["payload"] = $request_body;
			$out["header"] = getallheaders();
			$out["server"] = $_SERVER;
			$pa["jcipagerequest"] = $out;
			#$this->feedData = json_encode($pa);			

        if ($postparam) {
			$pa["jcipageparam"]["post"] = $postparam;
        } else {
#			$out = array();
#			$out["time"] = time();
#			$request_body = file_get_contents('php://input');
#			$out["payload"] = $request_body;
#			$out["header"] = getallheaders();
#			$out["server"] = $_SERVER;
#			$pa["jcipageparam"] = $out;
			$this->feedData = json_encode($pa);			
			return TRUE;
		}
        $postId = $postparam->ID;
		if ($postId>0) {
			$custom_fields_arr = get_post_custom();
		} else {
			$custom_fields_arr = array();
		}
        $pa["jcipageparam"]["custom_fields"] = $custom_fields_arr;
		$this->feedData = json_encode($pa);
	} else {
		if ($this->method=="post") {
			$argsHeader["method"] = "POST";
			$this->jci_load_collectDebugMessage($argsHeader, 2, "WPPOST-Header sent to API: (", ")<br>");
			$argsHeader["body"] = $this->get_option_prepare_for_usage('jci_pro_http_body');
			if (""!=$this->postbody) {
				$argsHeader["body"] = $this->postbody;
			}
			$response = wp_remote_post($this->feedUrl, $argsHeader);
		} else if ($this->method=="rawget") {
			$response = $this->raw_remote_get($this->feedUrl, $argsHeader);
		} else if ($this->method=="curlget") {
			$response = $this->curl_get($this->feedUrl, $argsHeader);
		} else if ($this->method=="curlpost") {
			$response = $this->curl_post($this->feedUrl, $argsHeader);
		} else if ($this->method=="curlput") {
			#$response = $this->curl_put($this->feedUrl, $argsHeader);
			$response = $this->curl_post($this->feedUrl, $argsHeader, TRUE);
		} else if ($this->method=="curldelete") {
			$response = $this->curl_post($this->feedUrl, $argsHeader, FALSE, TRUE);
			
		} else if ($this->method=="rawpost") {
			$response = $this->raw_remote_post($this->feedUrl, $argsHeader);
		} else { # default: WP-Get
			$this->method = "get";
			$this->jci_load_collectDebugMessage($argsHeader, 2, "GET-Header sent to API: (", ")<br>");
			$response = wp_remote_get($this->feedUrl, $argsHeader);
			$this->errorhttpcode = wp_remote_retrieve_response_code($response);
			$this->errorhttpcodestring = wp_remote_retrieve_response_message($response);
		}
      $this->httpresponse = $response;
      # get http-status code
      if (
        $this->method=="post" ||
        $this->method=="get"
      ) {
        if (is_array($response)) {
          $http_errorcode = $response['response']['code'];
        } else {
          $http_errorcode = 1000;
        }
        if ( is_wp_error( $response ) ) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
            $error_message = $response->get_error_message();
            $this->errormsgout .= "Fetching URL failed: $error_message";
            $this->errorhttpcode = $error_message;
          } else {
            $this->errormsgout .= $errormsg." ($http_errorcode)";
            $this->errorhttpcode = $http_errorcode; #$error_message;
          }
           $this->allok = FALSE;
          return FALSE;
        }

        # response from JSON-Server, but maybe an invalid one
        # if there is an client error (errorcode 4xx) oder server error (5xx) do not cache and
        # do not use this "JSON" or whatever it is
        if (is_numeric($http_errorcode) && $http_errorcode>=400 && $http_errorcode<=600) {
          #if(isset($response['body']) && !empty($response['body'])) {
          # client or server error
          # do not cache, this would write invalid data into cache
          if ($this->cacheEnable) {
            # if cache is used try to use cached data, even if this is outdated
            $this->errormsgout .= "Fetching URL failed, loading cached data: http-errorcode $http_errorcode";
            $this->errorhttpcode = $http_errorcode;
            $this->retrieveFeedFromCache(FALSE);
            return TRUE;
          } else {
            # if cache is not used, display error message
            $errormsg = stripslashes(get_option('jci_pro_errormessage'));
            if ($errormsg=="") {
              $error_message = "http-errorcode ".$http_errorcode;
              $this->errormsgout .= "Fetching URL failed: http-errorcode $http_errorcode";
            } else {
              $this->errormsgout .= $errormsg." (error: $http_errorcode)";
            }
            $this->errorhttpcode = $http_errorcode;
            $this->allok = FALSE;
          }
          return FALSE;
        }
        # request was ok
		#echo "<hr>".$response['body']."<hr>";
		
		
     	 $this->feedData = $response['body'];
     } else if (
        $this->method=="curlget" ||
        $this->method=="curlpost" ||
        $this->method=="curlput" ||
        $this->method=="curldelete"
		) {
		if (is_bool($response) && !$response) {
			return FALSE;
		} else {
			$this->feedData = $response;
			if (!empty($this->encodingofsource)) {
				$this->feedData = iconv( $this->encodingofsource, 'UTF-8' , $this->feedData); 
			}
			/* maybe an option in special cases
			$contenttype = $this->getContentType();
			if (!empty($contenttype)) {
				$csArr = @explode("charset=", $contenttype);
				$ctsent = trim(@$csArr[1]);
				if ($ctsent=="utf-16le") {
					$this->feedData = iconv( "utf-16le", 'UTF-8' , $this->feedData); 
				}
			}
			*/
			if (!empty($payloadInputJSONstr)) {
				$this->feedData = $response."##payloadinputstr##".$payloadInputJSONstr;
			}
		}
     } else if (
        $this->method=="rawpost" ||
        $this->method=="rawget"
     ) {
      if (!$response) {
        return FALSE;
      } else {
    	   $this->feedData = $response;
      }
     }
     $this->storeFeedInCache();
	}
     return TRUE;
		}

    /* retrieveFeedFromCache: get cached filedata  */
	public function retrieveFeedFromCache($retrieveFeedFromWebIfTheresNoCachefile = TRUE){
		if(file_exists($this->cacheFile)) {
			$this->feedData = @file_get_contents($this->cacheFile);
			$this->errorhttpcode = "cache";
		} else {
			if ($retrieveFeedFromWebIfTheresNoCachefile) {
				# get from cache failed, try from web
				$this->retrieveFeedFromWeb();
			} else {
				$this->feedData = "";
			}
		}
	}


    private function get_option_prepare_for_usage($optionname) {
      return stripslashes(get_option($optionname));
    }
	
    private function jci_load_collectDebugMessage($debugMessage, $debugLevel=2, $prefix="", $suffix="", $convert2html=TRUE) {
		logDebug::jci_addDebugMessage($debugMessage, $this->debugModeIsOn, $this->debugLevel, $debugLevel, $suffix, $convert2html, FALSE, $prefix, 400);
		}


    private function curl_post($url, $argsHeader, $putflag=FALSE, $deleteflag=FALSE) {
     if (!empty($this->postPayload)) {
        $this->postPayload = $this->replace_BRO_BRC($this->postPayload);
        $this->jci_load_collectDebugMessage("curlPOST: postpayload parameter: ".$this->postPayload, 10);
      }

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

		$followLocation = FALSE;
		if ($argsHeader["followlocation"]=="yes") {
			$followLocation = TRUE;
		}
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $followLocation);

		if ($putflag) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		} else if ($deleteflag) {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		} else {
			curl_setopt($curl, CURLOPT_POST, 1);
		}

      $curlusernamepassword = trim(get_option('jci_pro_curl_usernamepassword'));
      if (""!=$curlusernamepassword) {
        $this->jci_load_collectDebugMessage("curlPOST: usernamepassword (only the first 15 chars are displayed): ".substr($curlusernamepassword, 0, 15), 10);
        curl_setopt($curl, CURLOPT_USERPWD, $curlusernamepassword);
      }

      $curlauthmethodstr = get_option('jci_pro_curl_authmethod');
		  if (""!=$curlauthmethodstr) {
			 $curlauthmethod = @constant($curlauthmethodstr);
			 if (""==$curlauthmethod) {
				$this->jci_load_collectDebugMessage("curlPOST: invalid curl-authmethod, check plugin-options: ".$curlauthmethodstr, 10);
			 } else {
				$this->jci_load_collectDebugMessage("curlPOST: authmethod: ".$curlauthmethodstr." (".$curlauthmethod.")", 10);
				curl_setopt($curl, CURLOPT_HTTPAUTH, $curlauthmethod);
			 }
		  }

      if (!empty($argsHeader["headers"])) {
        $curlhttpheaderaccept = get_option('jci_pro_http_header_accept');
        if (!empty($curlhttpheaderaccept)) {
          $this->jci_load_collectDebugMessage("curlPOST: CURLOPT_HTTPHEADER: ".$curlhttpheaderaccept, 10);
		      if (!is_array($curlhttpheaderaccept)) {
			      $curlhttpheaderaccept = array($curlhttpheaderaccept);
		      }
 		      curl_setopt($curl, CURLOPT_HTTPHEADER, $curlhttpheaderaccept);
        } else {
          $curlhttpheaderArr = NULL;
          $curlhttpheaderStr = "";
          foreach ($argsHeader["headers"] as $key => $value) {
            $curlhttpheaderStr .= $key.":".$value."#!!!#";
          }
          $curlhttpheaderArr = explode("#!!!#", $curlhttpheaderStr);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $curlhttpheaderArr);
        }
      }

      $val_jci_pro_http_header_useragent = get_option('jci_pro_http_header_useragent');
      if (""!=$val_jci_pro_http_header_useragent) {
        $this->jci_load_collectDebugMessage("curlPOST: useragent: ".$val_jci_pro_http_header_useragent, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, $val_jci_pro_http_header_useragent);
      }

      if (is_numeric($this->urlgettimeout) && $this->urlgettimeout>0) {
        $this->jci_load_collectDebugMessage("curlPOST: timeout: ".$this->urlgettimeout, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->urlgettimeout);
      }

      curl_setopt($curl, CURLOPT_URL, $url);
		if (!empty($this->postPayload)) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->postPayload);
			$this->jci_load_collectDebugMessage("curlPOST: postPayload: ".$this->postPayload, 10);
		}
      $this->set_curl_options($curl, "curlPOST");
      $curlgetreturnval = curl_exec($curl);
      $lastHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->contenttype = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
      $curlError = curl_error($curl);
	  
      $this->errorhttpcode = $lastHttp;
      $this->errorhttpcodestring = curl_error($curl);

      if ($this->httpstatuscodemustbe200) {
        if (""!=$curlError){
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
            $this->errormsgout .= "curlPOST: failed: $curlError";
          } else {
            $this->errormsgout .= $curlError." (cg)";
          }
          $this->allok = FALSE;
          return FALSE;
        } else if (200!=$lastHttp) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
            $this->errormsgout .= "curlPOST: failed, http-code: ".$lastHttp;
          } else {
            $this->errormsgout .= $lastHttp." (http-cp)";
          }
          $this->jci_load_collectDebugMessage("curlPOST: API-answer: ".$curlgetreturnval, 10);
          $this->allok = FALSE;
          return FALSE;
        }
      }
      curl_close($curl);
      return $curlgetreturnval;
    }
    
    
    private function set_curl_options($curl, $methodname) {
		# get curloptionliste
		# set default value for $curloptionlist from plugin default settings
		$curloptionlist = get_option('jci_pro_curl_optionlist');
		if (empty($this->curloptions) && empty($curloptionlist)) {
			$this->jci_load_collectDebugMessage("$methodname: no curloptions defined");
			return TRUE;
		} else if (empty($this->curloptions) && (!empty($curloptionlist))) {
			$this->jci_load_collectDebugMessage("$methodname: option used from plugin-settings: ".$curloptionlist);
		} else {
			# there are shortcode / template-settings for the curloptions: overwrite default values
			$curloptionlist = $this->curloptions;
			$this->jci_load_collectDebugMessage("$methodname: curl-option used from shortcode: ".$curloptionlist, 10);
		}
  		if (""==$curloptionlist) {
			return TRUE;
		}
      
		#$curloptionlistArr = explode(";", trim(stripslashes($curloptionlist))); # fails with CURLOPT_HTTPHEADER=Accept:text/xml##Content-Type:text/xml; charset=utf-8
		$curloptionlistArr = explode(";CURLOPT_", trim(stripslashes($curloptionlist))); # fails with CURLOPT_HTTPHEADER=Accept:text/xml##Content-Type:text/xml; charset=utf-8
		# begin loop curloptions
		
		$atLeastPHP8 = (version_compare(PHP_VERSION, '8.0.0') >= 0);

		for ($j=0;$j<count($curloptionlistArr);$j++) {
			$clo = $curloptionlistArr[$j];
			if (!preg_match("/CURLOPT_/", $clo)) {
				$clo = "CURLOPT_".$clo;
			}
			#$curloptionlistArr1 = explode("=", $curloptionlistArr[$j], 2);
			$curloptionlistArr1 = explode("=", $clo, 2);
			$curloptKeyStr = $curloptionlistArr1[0];
			$cono = "";
			if ($atLeastPHP8) {
				try {
					$cono = constant($curloptKeyStr);   # number of curlopt-param, empty if there isd no such PHP-curl-constant
				} catch (Throwable $e) {	}
			} else {
					$cono = @constant($curloptKeyStr);   # number of curlopt-param, empty if there isd no such PHP-curl-constant
			}
		
 			# $curloptionlistArr1[1] = intval($curloptionlistArr1[1]);  # removed v 345
			# check VALUES, sometimes constant, e.g. auth-method
			#$this->jci_load_collectDebugMessage("$methodname: ".$curloptionlistArr[$j], 10);
			$this->jci_load_collectDebugMessage("$methodname: ".$clo, 10);
			# sometimes the value of the curloption is a const, esp. for the several auth-methods
			
			$curloptValueStr = "";
			if ($atLeastPHP8) {
				if (isset($curloptionlistArr1[1])) {
					try {
						#var_Dump($curloptionlistArr1);  
						$curloptValueStr = constant(trim($curloptionlistArr1[1]));
					} catch (Throwable $e) {	} // no errorhandling, just leave $curloptValueStr empty
				}
			} else {
					$curloptValueStr = @constant(trim($curloptionlistArr1[1]));
			}
			if (""==$curloptValueStr) {
				# not a constant, take given value
				$curloptValueStr = "";
				if (isset($curloptionlistArr1[1])) {
					$curloptValueStr = $curloptionlistArr1[1];
				}
				# we get the curloptions as strings, therefore we have to check on leading and trailing quotation marks
				if (preg_match("/^\"/", $curloptValueStr) && preg_match("/\"$/", $curloptValueStr)) {
					$curloptValueStr = preg_replace("/^\"/i", "", trim($curloptValueStr));
					$curloptValueStr = preg_replace("/\"$/i", "", trim($curloptValueStr));
				}
  				$this->jci_load_collectDebugMessage("$methodname: key: $curloptKeyStr ($cono)- value: ".$curloptValueStr, 10);
				} else {
				  $this->jci_load_collectDebugMessage("$methodname: option-value is a constant: ".$curloptKeyStr." ($cono) - number: ".$curloptValueStr, 10);
				}
 				# check key
				if (
					(!empty($curloptKeyStr)) &&
					(preg_match("/curlopt_/i", $curloptKeyStr))
				) { 
				# some curlopt_ expect boolean, integer, string, or arrays as input: http://php.net/manual/de/function.curl-setopt.php
				# array: CURLOPT_HTTPHEADER has to be array (all other array-curlopt_ are not supported here)
				if (preg_match("/CURLOPT_HTTPHEADER/i", $curloptKeyStr)) {
					$this->jci_load_collectDebugMessage("$methodname: build array for CURLOPT_HTTPHEADER with ".$curloptValueStr, 10);
	 				$curloptvalHeaderArr = explode("##", $curloptValueStr); # CURLOPT_HTTPHEADER: explode  list of header-param, separated by ##
					$this->jci_load_collectDebugMessage("$methodname: array for CURLOPT_HTTPHEADER ($cono): ".print_r($curloptvalHeaderArr, TRUE), 10);
  					$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_HTTPHEADER", $cono, $curloptvalHeaderArr);
					if ($errorlevelcurlsetop) {
						$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_HTTPHEADER ($cono) successful", 10);
					} else {
						$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_HTTPHEADER ($cono) FAILED", 10);
					}
				}  else if (preg_match("/CURLOPT_UPLOAD_POSTFIELDS/i", $curloptKeyStr)) {
						# this is an JCI-CURL-Constant: via a JSON we can define an payload-array with postfields and it's values and a node named "jciuploadfiles" with the name:files to upload
						$cono = @constant("CURLOPT_POSTFIELDS");  
						$postFields = array();
						$jsonArrTmp = json_decode($curloptValueStr, TRUE);
						if (is_null($jsonArrTmp)) {
							# no json or invalid json
        					$this->jci_load_collectDebugMessage("$methodname, CURLOPT_UPLOAD_POSTFIELDS ($cono): no JSON or invalid JSON: ".$curloptValueStr, 10);
						} else {
							foreach ($jsonArrTmp as $k=>$v) {
								if (is_string($v)) {
									$postFields[$k] = $v;
								} elseif ("jciuploadfiles"==$k && is_array($v)) {
									foreach ($v as $k1=>$v1) {
										$filename = $v1["filename"];
										$uploadsamefilename = TRUE;
										if ("no"==$v1["uploadsamefilename"]) {
											$uploadsamefilename = FALSE;
										}
										if (file_exists($filename)) {
											$filesize = filesize($filename);
											$filesizeout = floor($filesize*10/(1024*1024))/10;
											if ($filesizeout==0) {
												$filesizeout = (floor($filesize*10/(1024))/10)." kB";
											} else {
												$filesizeout .= "MB";
											}
											$postfieldname = $v1["name"];
											if(function_exists('curl_file_create')) {
												$filePath = curl_file_create($filename);
											} else {
												$filePath = '@' . realpath($filename);
												curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
											}
											$postFields[$postfieldname] = $filePath;
											if ("yes"==$v1["store_upload_in_library"]) {
												$this->jci_load_collectDebugMessage("$methodname, CURLOPT_UPLOAD_POSTFIELDS ($cono): store uploaed file in local wp-media-library", 10);
												$filename_source = $filename;
												$filename_lib = $v1["library_file_name"];
												$retstore = $this->store_file_ln_wp_lib($filename_source, $filename_lib, $uploadsamefilename);
												$this->apiresponseinfo["upload"] = $retstore;
											} else {
												$this->jci_load_collectDebugMessage("$methodname, CURLOPT_UPLOAD_POSTFIELDS ($cono): NO store uploaed file in local wp-media-library: store_upload_in_library is NOT yes", 10);
											}
										}
									}
								} else {
									# no action
								}
							}
        					$this->jci_load_collectDebugMessage("$methodname, CURLOPT_UPLOAD_POSTFIELDS ($cono for CURLOPT_POSTFIELDS): ".print_r($postFields, TRUE), 10);
          					$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_UPLOAD_POSTFIELDS", $cono, $postFields, 10);
							if ($errorlevelcurlsetop) {
								$this->jci_load_collectDebugMessage("$methodname: t CURLOPT_UPLOAD_POSTFIELDS ($cono) successful", 10);
							} else {
								$this->jci_load_collectDebugMessage("$methodname: CURLOPT_UPLOAD_POSTFIELDS ($cono) FAILED", 10);
							}
						}
				}  else if (preg_match("/CURLOPT_POSTFIELDS/i", $curloptKeyStr)) {
					$this->jci_load_collectDebugMessage("$methodname: input for CURLOPT_POSTFIELDS ($cono): ".$curloptValueStr, 10);
					$curlopt_postfields_done = FALSE;
					$curlopt_postfields_is_array = TRUE;
					if (preg_match("/{/", $curloptValueStr)) {
						# maybe json
						$jsonArrTmp = json_decode($curloptValueStr, TRUE);
						if (is_null($jsonArrTmp)) {
							# no json or invalid json
        					$this->jci_load_collectDebugMessage("$methodname, CURLOPT_POSTFIELDS ($cono): no JSON or invalid JSON: ".$curloptValueStr, 10);
						} else {
							# valid json found
        					$this->jci_load_collectDebugMessage("$methodname, CURLOPT_POSTFIELDS ($cono): valid JSON: ".$curloptValueStr, 10);
							## set CURLOPT_POSTFIELDS with JSON-String
          					$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_POSTFIELDS", $cono, $curloptValueStr, 10);
							if ($errorlevelcurlsetop) {
								$this->jci_load_collectDebugMessage("$methodname: setting of valid JSON-feed at CURLOPT_POSTFIELDS ($cono) successful", 10);
							} else {
								$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono) FAILED", 10);
							}
							$curlopt_postfields_done = TRUE;
						}
					}
					if (!$curlopt_postfields_done) {
						$curloptvalItemArr = explode("##", $curloptValueStr);
						$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono): ".count($curloptvalItemArr)." fields in $curloptValueStr", 10);
						$cpfArr = array();
						$valouttmp = "";						
						for ($i=0;$i<count($curloptvalItemArr);$i++) {
							# check if JSON
							$jsonArrTmp = json_decode($curloptvalItemArr[$i], TRUE);
							if (is_null($jsonArrTmp)) {
								# no json or invalid json
								$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono): ".$curloptvalItemArr[$i]." is not JSON", 10);
								if (preg_match("/\:/", $curloptvalItemArr[$i])) {
									$curloptvalItemArrSingle = explode(":", $curloptvalItemArr[$i], 2);
									$cpfArr[$curloptvalItemArrSingle[0]] = $curloptvalItemArrSingle[1]; 
									$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono): key-value-pair ".$curloptvalItemArrSingle[0]." = ".$curloptvalItemArrSingle[1], 10);
									$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_POSTFIELDS", $cono, $cpfArr);
								} else {
									$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono), set string: ".$curloptvalItemArr[$i], 10);
									$valouttmp = $curloptvalItemArr[$i];
									$curlopt_postfields_is_array = FALSE;
									$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_POSTFIELDS", $cono, $curloptvalItemArr[$i]);  # set string not array
								}
							} else {
								# json found: add to array
								$cpfArr = array_replace($cpfArr, $jsonArrTmp);
								$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono): ".$curloptvalItemArr[$i]." is JSON: add to array", 10);
								$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_POSTFIELDS", $cono, $cpfArr);
							}
							#$curloptvalHeaderItemArr = explode(":", $curloptvalItemArr[$i], 2);
						}    
						if ($curlopt_postfields_is_array) {
							$valouttmp = print_r($cpfArr, TRUE);
						}
						if ($errorlevelcurlsetop) {
							$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono) successful: ".$valouttmp, 10);
						} else {
							$this->jci_load_collectDebugMessage("$methodname: setting of CURLOPT_POSTFIELDS ($cono) FAILED: ".$valouttmp, 10);
						}
					}
				} else {
					# bool, int, str curlopt_
					$this->jci_load_collectDebugMessage("$methodname: key-value-pair for $curloptKeyStr ($cono): $curloptValueStr", 10);
  					#$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_POSTFIELDS", $cono, $curloptValueStr);
  					$errorlevelcurlsetop = $this->curl_setopt_wrapper($methodname, $curl, $clo, $cono, $curloptValueStr);
					if ($errorlevelcurlsetop) {
						$this->jci_load_collectDebugMessage("$methodname: setting of $curloptKeyStr ($cono) to $curloptValueStr successful", 10);
					} else {
						$this->jci_load_collectDebugMessage("$methodname: setting of $curloptKeyStr ($cono) to $curloptValueStr FAILED", 10);
					}
				}
 			}
		} # end loop curloptions
    }
    
    
    private function store_file_ln_wp_lib($filename_source, $filename_lib, $uploadsamefilename=TRUE) {
		if (!$uploadsamefilename) {
			$foundImg = FALSE;
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => null, // any parent
			); 
			$attachments = get_posts($args);
			if ($attachments) {
				foreach ($attachments as $post) {
					if ($post->post_title==$filename_lib) {
						$foundImg = TRUE;
						break;
					}
				}
			}
			if ($foundImg) {
				$retval["status"] = "imgthere";
				$retval["msg"] = "no upload, as image with same name is already there";
				return $retval;
			}
		}
		
		$uploadfile = wp_upload_bits($filename_lib, null, file_get_contents($filename_source)); # return: file, urltype, error (bool)
		if ($uploadfile['error']) {
			$retval["status"] = "upload failed";
			$retval["msg"] = "ok";
			return $retval;
		} else {
			$wp_filetype = wp_check_filetype($filename_lib, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				#	'post_parent' => $parent_post_id,
				'post_title' => $filename_lib,
				'post_content' => '',
				'post_status' => 'inherit'
			);
			
			$attachment_id = wp_insert_attachment( $attachment, $uploadfile['file']);
			if (is_wp_error($attachment_id)) {
				$retval["status"] = "is_wp_error";
				#$retval .= "0";
			} else {
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $uploadfile['file'] );
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
				$retval["status"] = "ok";
				$retval["attachment_id"] = $attachment_id;
				$retval["attachment_url"] = wp_get_attachment_url($attachment_id);
			}
		}
		return $retval;
   }


    private function curl_setopt_wrapper($methodname, $curl, $curloptname, $curloptid, $curloptval) {
		$this->jci_load_collectDebugMessage($methodname.": curl_setopt $curloptname ($curloptid) with value ".print_r($curloptval, TRUE), 10);
		if (!is_integer($curloptid)) {
			return FALSE;
		}
		$errorlevelcurlsetop = FALSE;
		$atLeastPHP8 = (version_compare(PHP_VERSION, '8.0.0') >= 0);
		if ($atLeastPHP8) {
			try {
				$errorlevelcurlsetop = curl_setopt($curl, $curloptid, $curloptval);
			} catch (Throwable $e) {	}
			return $errorlevelcurlsetop;
		} else {
			$errorlevelcurlsetop = curl_setopt($curl, $curloptid, $curloptval);
		}
		return $errorlevelcurlsetop;
	}

    private function curl_get($url, $argsHeader) {
      $methodname = "curlGET";
      if (empty($this->postPayload)) {
        $url4curl = $url;
      } else {
        $this->postPayload = $this->replace_BRO_BRC($this->postPayload);
        $this->jci_load_collectDebugMessage("curlGET: payload parameter: ".$this->postPayload);
        $params = json_decode($this->postPayload, TRUE);
        $queryString = NULL;
        if (empty($params)) {
          $this->jci_load_collectDebugMessage("curlGET: payload parameter: json-decoding failed, check syntax!");
        } else {
          $queryString = http_build_query($params);
          $this->jci_load_collectDebugMessage("curlGET: payload parameter - querystring build: ".$queryString);
        }
        if (preg_match("/\?/", $url)) {
          $url4curl = $url.'&'.$queryString;
        } else {
          $url4curl = $url.'?'.$queryString;
        }
        $this->jci_load_collectDebugMessage("curlGET: payload parameter - url 4 API: ".$url4curl);
      }
		#$curl = curl_init($url4curl);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url4curl);
		
			$errorlevelcurlsetop2 = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_RETURNTRANSFER", CURLOPT_RETURNTRANSFER, TRUE);
		
		$followLocation = FALSE;
		if ($argsHeader["followlocation"]=="yes") {
			$followLocation = TRUE;
		}
		$errorlevelcurlsetop2 = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_FOLLOWLOCATION", CURLOPT_FOLLOWLOCATION, $followLocation);

      $curlusernamepassword = trim(get_option('jci_pro_curl_usernamepassword'));
      if (""!=$curlusernamepassword) {
        $this->jci_load_collectDebugMessage("curlGET-usernamepassword (only the first 15 chars are displayed): ".substr($curlusernamepassword, 0, 15));
  		  $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_USERPWD", CURLOPT_USERPWD, $curlusernamepassword);
      }

      $curlauthmethodstr = get_option('jci_pro_curl_authmethod');
		if (""!=$curlauthmethodstr) {
			$curlauthmethod = @constant($curlauthmethodstr);
			if (""==$curlauthmethod) {
				$this->jci_load_collectDebugMessage("curlGET: invalid curl-authmethod, check plugin-options: ".$curlauthmethodstr);
			} else {
				$this->jci_load_collectDebugMessage("curlGET: curl-authmethod: ".$curlauthmethodstr." (".$curlauthmethod.")");
  			$errorlevelcurlsetop2 = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_HTTPAUTH", CURLOPT_HTTPAUTH, $curlauthmethod);
			}
		}

    $curlhttpheaderaccept = get_option('jci_pro_http_header_accept');
    if (!empty($curlhttpheaderaccept)) {
      $this->jci_load_collectDebugMessage("curlGET: curl CURLOPT_HTTPHEADER: ".$curlhttpheaderaccept);
  		if (!is_array($curlhttpheaderaccept)) {
	 		$curlhttpheaderaccept = array($curlhttpheaderaccept);
	   	}
		$errorlevelcurlsetop2 = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_HTTPHEADER", CURLOPT_HTTPHEADER, $curlhttpheaderaccept);
     }

      $val_jci_pro_http_header_useragent = get_option('jci_pro_http_header_useragent');
      if (""!=$val_jci_pro_http_header_useragent) {
        $this->jci_load_collectDebugMessage("curlGET: curl-useragent: ".$val_jci_pro_http_header_useragent);
		$errorlevelcurlsetop2 = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_USERAGENT", CURLOPT_USERAGENT, $val_jci_pro_http_header_useragent);
      }

      if (is_numeric($this->urlgettimeout) && $this->urlgettimeout>0) {
        $this->jci_load_collectDebugMessage("curlGET: curl-timeout: ".$this->urlgettimeout);
		$errorlevelcurlsetop2 = $this->curl_setopt_wrapper($methodname, $curl, "CURLOPT_TIMEOUT", CURLOPT_TIMEOUT, $this->urlgettimeout);
      }

       $this->set_curl_options($curl, $methodname); # curl_get
      $curlgetreturnval = curl_exec($curl);
      $lastHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$this->contenttype = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
      $curlError = curl_error($curl);
      $this->errorhttpcode = $lastHttp;
      $this->errorhttpcodestring = curl_error($curl);
      if ($this->httpstatuscodemustbe200) {
        if (""!=$curlError) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
            $this->errormsgout .= "curlGET failed: $curlError";
          } else {
            $this->errormsgout .= $curlError." (cg)";
          }
          $this->allok = FALSE;
          return FALSE;
        } else if (200!=$lastHttp) {
          $this->jci_load_collectDebugMessage("curlGET: API-answer: ".$curlgetreturnval, 10);
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
            $this->errormsgout .= "curlGET failed, http-code: ".$lastHttp;
          } else {
            $this->errormsgout .= $errormsg." (http-cg)";
          }
          $this->allok = FALSE;
          return FALSE;
        }
      }
      curl_close($curl);
      return $curlgetreturnval;
    }

    private function raw_remote_get($url, $argsHeader) {
      $headerArr['method'] = 'GET';
      $headerStr = "";
      foreach ($argsHeader['headers'] as $key => $value) {
#      while(list($key, $value) = each($argsHeader['headers'])) {
        $headerStr .= $key.":".$value."\r\n";
      }
      $options = array('http' =>
        array(
          'method'  => $headerArr['method'],
          'header'  => $headerStr,
          'timeout' => $argsHeader['timeout'],
          'ignore_errors'  => TRUE
        )
      );
      $this->jci_load_collectDebugMessage("Raw-GET-Header sent to API: ".print_r($options, TRUE), 2, "". "", FALSE);
      $context = stream_context_create($options);
      $result = @file_get_contents($url, FALSE, $context); // no errormessages
	  
		if (!empty($http_response_header[0])) {
			$httpErrorArr = explode(" ", trim($http_response_header[0]));
			if (trim($httpErrorArr[1])>0) {
				$this->errorhttpcode = trim($httpErrorArr[1]);
			} else {
				$this->errorhttpcode = $http_response_header[0];
			}
			$this->errorhttpcodestring = $http_response_header[0];
		}
		
	#	echo $result;
		
	  
      $this->jci_load_collectDebugMessage("http response from API: ".print_r($http_response_header, TRUE), 2, "", "", FALSE);
      if ($result === FALSE) {
        $errormsg = stripslashes(get_option('jci_pro_errormessage'));
        if ($errormsg=="") {
          $this->errormsgout .= "Rawfetching URL by GET failed: $url";
        } else {
          $this->errormsgout .= $errormsg." (rg)";
        }
        $this->allok = FALSE;
        return FALSE;
      }
      return $result;
    }


    private function raw_remote_post($url, $argsHeader) {
      $headerArr['method'] = 'POST';
      $argsHeader['headers']['Content-type'] = "application/x-www-form-urlencoded";
      $content4httppost = "";
      if (isset($argsHeader["payload"])) {
        if ("no"==$this->urlencodepostpayload) {
          $content4httppost = http_build_query($argsHeader["payload"]);
        } else {
          $content4httppost = urlencode(http_build_query($argsHeader["payload"]));
        }
      }
      $headerStr = "";
      foreach ($argsHeader['headers'] as $key => $value) {
      #while(list($key, $value) = each($argsHeader['headers'])) {
        $headerStr .= $key.":".$value."\r\n";
      }
      $options = array('http' =>
        array(
          'method'  => $headerArr['method'],
          'header'  => $headerStr,
          'content' => $content4httppost,
          'timeout' => $argsHeader['timeout'],
          'ignore_errors'  => TRUE
        )
      );
      $this->jci_load_collectDebugMessage("Raw-POST-Header sent to API:<br>".print_r($options, TRUE));
      $context = stream_context_create($options);
      $result = @file_get_contents($url, false, $context); // do not show errormessage

		if (!empty($http_response_header[0])) {
			$httpErrorArr = explode(" ", trim($http_response_header[0]));
			if (trim($httpErrorArr[1])>0) {
				$this->errorhttpcode = trim($httpErrorArr[1]);
			} else {
				$this->errorhttpcode = $http_response_header[0];
			}
			$this->errorhttpcodestring = $http_response_header[0];
		}

      $this->jci_load_collectDebugMessage("http response from API:<br>".print_r($http_response_header, TRUE), 2, "", "", FALSE);
      if ($result === FALSE) {
        $errormsg = stripslashes(get_option('jci_pro_errormessage'));
        if ($errormsg=="") {
          $this->errormsgout .= "Rawfetching URL by POST failed: $url";
        } else {
          $this->errormsgout .= $errormsg." (rp)";
        }
        $this->allok = FALSE;
        return FALSE;
      }
      return $result;
    }
}
/* class FileLoadWithCache END */
?>