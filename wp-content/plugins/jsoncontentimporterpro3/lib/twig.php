<?php
/*
 * work with twig 20181005
 */
 
class doJCITwig {
	private $isJCIParser = false;
	private $isTwig2 = false;
	private $isTwig3 = false;
	private $isTwig332 = false;
	private $isTwig332adj = false;
	private $parser = "twig";
    private $twig_environment_settings = NULL;
	private $twig_loader = NULL;
	private $twig_environment = NULL;
	private $datastructure = "";
	private $ts = NULL;
	private $result = "";
	private $jsonObj = array();
	private $debugmsg = "";
	private $maskspecialcharsinjsonFlag = TRUE;
	private $debug = FALSE;

	
	public function __construct($parser, $maskspecialcharsinjsonFlag){ # construct - setTwig - tryTwig - getTwigResult - getTwigDebug
		ini_set('display_errors', 1);
		$this->parser = $parser;
		$this->maskspecialcharsinjsonFlag = $maskspecialcharsinjsonFlag;
		$this->jci_setup_twig_environment();
		$this->addTwigExtensions();
    }

	public function executeTwig($jsonObj, $datastructure, $parser, $maskspecialcharsinjsonFlag){
		$setTwigErorLevel = $this->setTwig($jsonObj, $datastructure, $parser, $maskspecialcharsinjsonFlag);
		if (!$setTwigErorLevel) {
			return "";
		}
		$this->tryTwig();
		return $this->getTwigResult();
	}

	public function getTwigResult(){
		return $this->result;
	}
	
	public function getTwigDebug(){
		return $this->debugmsg;
	}

	public function setTwig($jsonObj, $datastructure, $parser, $maskspecialcharsinjsonFlag){
		if (empty($datastructure)) {
			return FALSE;
		} 
		$this->datastructure = $datastructure;
		$this->debugmsg = ""; # clear 
		$this->parser = $parser;
		$this->jsonObj = $jsonObj;
		$this->maskspecialcharsinjsonFlag = $maskspecialcharsinjsonFlag;
		$this->setTwigTemplate();
		return TRUE;
	}

	public function tryTwigHandleCatchedError($e){
		// $template contains one or more syntax errors
		$this->jci_collectDebugMessage("twig: template-error: ".$e->getRawMessage());
		$errormsg = stripslashes(get_option('jci_pro_errormessage'));
		if (empty($errormsg)) {
			$this->result = "Twig-Error: ".$e->getRawMessage();
			return FALSE;
		}
		$this->result = $errormsg." (twig-error)";
		return FALSE;
	}

	public function tryTwig(){
		if ($this->isTwig2 || $this->isTwig3 || $this->isTwig332) {
			try {
				$this->twig_environment->parse($this->twig_environment->tokenize($this->ts));
			} catch (\Twig\Error\SyntaxError $e) {
				return $this->tryTwigHandleCatchedError($e);
			}
		} else if ($this->isTwig332adj) {
			try {
				$this->twig_environment->parse($this->twig_environment->tokenize($this->ts));
			} catch (\JCITwig\Error\SyntaxError $e) {
				return $this->tryTwigHandleCatchedError($e);
			}
		} else {
			try {
				$this->twig_environment->parse($this->twig_environment->tokenize($this->ts));
			} catch (Twig_Error_Syntax $e) {
				return $this->tryTwigHandleCatchedError($e);
			}
		}
		$template = $this->twig_environment->createTemplate($this->datastructure);
		$this->jci_collectDebugMessage("twig-template: ".$this->datastructure);
		$this->jci_collectDebugMessage("twig-JSON: ".print_r($this->jsonObj, TRUE));
		$resultTmp = $template->render($this->jsonObj);
		$this->result = $this->unmaskSpecialCharsInJSON($resultTmp);
		return TRUE;
	}
		
	public function maskSpecialCharsInJSON($intxt) {
        if (!$this->maskspecialcharsinjsonFlag) {
          return $intxt;
        }
        $intxt = preg_replace('/\$/', "_symbol_dollar_", $intxt);
        #$intxt = preg_replace('/</', "_symbol_smallerthan_", $intxt);
        #$intxt = preg_replace('/>/', "_symbol_greaterthan_", $intxt);
        #   $intxt = preg_replace('/\\\\\"/', "_symbol_backslash_masking_quotationmark_", $intxt);
        $intxt = preg_replace('/\\\\\//', "_symbol_backslash_masking_slash_", $intxt);
        #$intxt = preg_replace('/\//', "_symbol_slash_", $intxt);
        $intxt = preg_replace("/\@/", "_symbol_at_", $intxt);
        $intxt = preg_replace("/\!/", "_symbol_exclamationmark_", $intxt);
        return $intxt;
	}

	public function unMaskSpecialCharsInJSON($intxt) {
        if (!$this->maskspecialcharsinjsonFlag) {
          return $intxt;
        }
        $intxt = preg_replace("/_symbol_slash_/i", '/', $intxt);
        $intxt = preg_replace('/_symbol_backslash_masking_slash_/', '/', $intxt);
        $intxt = preg_replace('/_symbol_backslash_masking_quotationmark_/', '"', $intxt);
        $intxt = preg_replace("/_symbol_sqrbracket_open_/i", '[', $intxt);
        $intxt = preg_replace("/_symbol_sqrbracket_close_/i", ']', $intxt);
        $intxt = preg_replace("/_symbol_curlbracket_open_/i", '{', $intxt);
        $intxt = preg_replace("/_symbol_curlbracket_close_/i", '}', $intxt);
        $intxt = preg_replace("/_symbol_at_/i", "@", $intxt);
        $intxt = preg_replace("/_symbol_dot_/i", ".", $intxt);
        $intxt = preg_replace("/_symbol_exclamationmark_/i", "!", $intxt);
        $intxt = preg_replace('/_symbol_smallerthan_/', "<", $intxt);
        $intxt = preg_replace('/_symbol_greaterthan_/', ">", $intxt);
        $intxt = preg_replace("/_symbol_dollar_/i", '$', $intxt);
        return $intxt;
	}

	private function jci_setup_twig_environment() {
		# load and register Twig
		$tmppath = "";
		
		/*
		if (1==2 && class_exists( 'Twig_Autoloader' )) {
			$foundTwigVersion = $this->jci_getSelectedTwigVersion(); # Twig_Environment::VERSION;
			$this->isJCIParser = TRUE;
			if (preg_match("/^1/", $foundTwigVersion)) {
				$this->isTwig2 = FALSE;
				$this->isTwig3 = FALSE; 
				$this->isTwig332 = FALSE; 
				$this->isTwig332adj = FALSE; 
				$this->isJCIParser = FALSE;
			}
			if (preg_match("/^2/", $foundTwigVersion)) {
				$this->isTwig2 = TRUE;
				$this->isTwig3 = FALSE; 
				$this->isTwig332 = FALSE; 
				$this->isTwig332adj = FALSE; 
				$this->isJCIParser = FALSE;
			}
			if (preg_match("/^3/", $foundTwigVersion)) {
				$this->isTwig3 = TRUE; 
				$this->isTwig2 = FALSE; 
				$this->isTwig332 = FALSE; 
				$this->isTwig332adj = FALSE; 
				$this->isJCIParser = FALSE;
			}
			if (preg_match("/^3.3.2/", $foundTwigVersion)) {
				$this->isTwig3 = FALSE; 
				$this->isTwig2 = FALSE; 
				$this->isTwig332 = TRUE; 
				$this->isTwig332adj = FALSE; 
				$this->isJCIParser = FALSE;
			}
			if (preg_match("/^adj3.3.2/", $foundTwigVersion)) {
				$this->isTwig3 = FALSE; 
				$this->isTwig2 = FALSE; 
				$this->isTwig332 = FALSE; 
				$this->isTwig332adj = TRUE; 
				$this->isJCIParser = FALSE;
			}
			$this->jci_collectDebugMessage("plugin is using Twig from another plugin: twig-version is ".$foundTwigVersion);
			*/
		#} else {
			$this->jci_collectDebugMessage("load Twig ".$this->parser." from JCI-plugin");
			if ($this->parser=="twig243") {
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig243/vendor/autoload.php';
				require_once $inc;
				$this->isTwig2 = TRUE;
			} else if ($this->parser=="twig3") {
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig3/vendor/autoload.php';
				require_once $inc;
				$this->isTwig3 = TRUE;
			} else if ($this->parser=="twig332") {
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig332/vendor/autoload.php';
				require_once $inc;
				$this->isTwig332 = TRUE;
			} else if ($this->parser=="twig332adj") {
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig332adj/vendor/autoload.php';
				require_once $inc;
				$this->isTwig332adj = TRUE; 
			} else if ($this->parser=="jci") {
				$this->isTwig2 = FALSE; 
				$this->isTwig3 = FALSE; 
				$this->isTwig332 = FALSE; 
				$this->isTwig332adj = FALSE; 
				$this->isJCIParser = TRUE;
			} else {
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig1/Twig/Autoloader.php';
				if (!file_exists($inc) || !is_readable($inc)) {
					$this->jci_collectDebugMessage("Twig not found in ".$inc);
				} else {
					require_once $inc;
				}
				Twig_Autoloader::register();
			}
		#}
		
		if ($this->isTwig3 || $this->isTwig332) { 				# as we load the template via shortcode-param this is not needed - but I don't know how to avoid it...
			$this->twig_loader = new \Twig\Loader\FilesystemLoader(WP_PLUGIN_DIR."/jsoncontentimporterpro3/"); 
		} else if ($this->isTwig332adj){
			$this->twig_loader = new \JCITwig\Loader\FilesystemLoader(WP_PLUGIN_DIR."/jsoncontentimporterpro3/"); 
		} else if (!$this->isJCIParser){
			$this->twig_loader = new Twig_Loader_Filesystem(WP_PLUGIN_DIR."/jsoncontentimporterpro3/");
		}
		
		# set twig options
		$this->twig_environment_settings = array(
			'charset' => get_bloginfo('charset'),  # default is utf-8
			'autoescape' => false,
			'strict_variables' => false,  # otherwise errors thrown
			'auto_reload' => true,
			#'cache' => WP_CONTENT_DIR.'/cache/jsoncontentimporterpro/twigcache',
		);

		$cachepathdynamic = get_option('jci_pro_cache_path');
		if (empty($cachepathdynamic)) {
			$cache4Twig = WP_CONTENT_DIR.'/cache/jsoncontentimporterpro/twigcache';
		} else {
			$cache4Twig = $cachepathdynamic;
			if (!preg_match("/\/$/", $cache4Twig)) {
				$cache4Twig .= "/";
			}
			$cache4Twig .= 'twigcache';
		}
		$twigcacheactive = TRUE;
		if (1!=get_option('jci_pro_enable_twigcache')) {
			$twigcacheactive = FALSE;
		}
		if ($twigcacheactive) {
			# check on cache only if cache is active - open_basedir-Warning also for those who never used cache
			$twigCacheDirThere = FALSE;
			if (is_dir($cache4Twig)) {
				# all there
				$twigCacheDirThere = TRUE;
			} else if (!empty($cache4Twig)) {
				if ($twigcacheactive) {
					$mkdirError2 = @mkdir($cache4Twig, 0777, TRUE);
					if (is_dir($cache4Twig)) {
						# all there
						$twigCacheDirThere = TRUE;
					}
				}
			}
			if ($twigCacheDirThere && $twigcacheactive) {
				# all there
				$this->jci_collectDebugMessage("Twig cache active: ".$cache4Twig);
				$this->twig_environment_settings['cache'] = $cache4Twig;
			} else if ($twigcacheactive) {
				$this->jci_collectDebugMessage("Twig cache dir not there: ".$cache4Twig);
			}
		}
		if (defined('WP_DEBUG') && true === WP_DEBUG) {
			$this->twig_environment_settings['debug'] = true;
		}

		# invoke Twig
		
		if ($this->parser=="twig3" || $this->parser=="twig332") {
			$this->twig_environment = new \Twig\Environment($this->twig_loader, $this->twig_environment_settings);
			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				$this->twig_environment->addExtension(new \Twig\Extension\DebugExtension());
			}
		} else if ($this->parser=="twig332adj") {
			$this->twig_environment = new \JCITwig\Environment($this->twig_loader, $this->twig_environment_settings);
			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				$this->twig_environment->addExtension(new \JCITwig\Extension\DebugExtension());
			}
		} else if (!$this->isJCIParser) {
			$this->twig_environment = new Twig_Environment($this->twig_loader, $this->twig_environment_settings);
			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				$this->twig_environment->addExtension(new Twig_Extension_Debug());
			}
		}

		$this->jci_collectDebugMessage("success - Twig loaded, version: ".$this->jci_getSelectedTwigVersion()); 
	}

	private  function jci_collectDebugMessage($msg){
		$this->debugmsg .= htmlentities($msg)."<br>";
	}

	public function jci_getSelectedTwigVersion(){
		if ($this->isTwig2 || $this->isTwig3 ||$this->isTwig332 ) {
			return \Twig\Environment::VERSION;
		} else if ($this->isTwig332adj) {
			return "adj". \JCITwig\Environment::VERSION;
		} else if (!$this->isJCIParser) {
			return Twig_Environment::VERSION;
		}
	}

	private function setTwigTemplate(){
       if ($this->parser=="twig") {
          $this->ts = $this->datastructure;
        } else if ($this->isTwig2) {
          $this->ts = new Twig_Source($this->datastructure, "");
        } else if ($this->isTwig3 || $this->isTwig332) {
			$this->ts = new \Twig\Source($this->datastructure, "");
        } else if ($this->isTwig332adj) {
			$this->ts = new \JCITwig\Source($this->datastructure, "");
		}
    }
	
	
	private function twig_ext_sortbyjsonfield($data, $sortdata) {
			$sortdataArr = explode("##", $sortdata);
			$i = 1;
			foreach ($sortdataArr as $val) {
				$sortdetailArr = explode(",", $val);
				$sortfield[$i] = trim($sortdetailArr[0]);
				$sortorder[$i] = "";
				if (isset($sortdetailArr[1])) {
					if ("desc"==trim($sortdetailArr[1])) {
						$sortorder[$i] = "desc";
					}
				}
				$sortflag[$i] = "";
				if (isset($sortdetailArr[2])) {
					if ("num"==trim($sortdetailArr[2])) {
						$sortflag[$i] = "num";
					}
					if (preg_match("/^date/", trim($sortdetailArr[2]))) {
						$sortflag[$i] = $sortdetailArr[2];
					}
				}
				$i++;
			}
			$data = $this->sortJsonArray($data, $sortfield, $sortorder, $sortflag);
			return $data;
	}
	
	private function twig_ext_dateformat($data, $dateformatstr, $datetimezone, $datelocale) {
		    $curentTimezone = date_default_timezone_get();
 		    date_default_timezone_set($datetimezone);
		    $validDateLocale = FALSE;
		    if (preg_match("/([a-z_]*)/i", $datelocale)) {
				$validDateLocale = TRUE;
		    }
		    if ($validDateLocale) {
			    $getlocale = setlocale (LC_TIME,"0");
			    setlocale(LC_TIME, $datelocale);
		    }
			if (!( is_numeric($data) && (int)$data == $data )) {
				$data = strtotime($data);
			}
		    $formattedDate = strftime($dateformatstr, $data);
		    if ($validDateLocale) {
			    setlocale(LC_TIME, $getlocale);
		    }
		    date_default_timezone_set($curentTimezone);
		    return $formattedDate;
	}
	
	private function twig_ext_converthex2ascii($data) {
          #$data = "0xefef39a10000000000000000000000000000000000000000000000000000000000000005";
          $dataTmp = substr($data, 2, strlen($data));
          $dataTmp = @hex2bin ($dataTmp);
          if ($dataTmp) {
            $data = $dataTmp;
          }
	    return $data;
	}
	
	private function twig_ext_convert2html($data) {
		 $data = $this->unMaskSpecialCharsInJSON($data);
         $dataTmp = htmlentities($data, TRUE);
         if (empty($dataTmp)) {
           return $this->maskSpecialCharsInJSON($data);
         }
         return $this->maskSpecialCharsInJSON($dataTmp);
	}

	private function twig_ext_dump($data) {
		 $dataDump = print_R($data, TRUE);
		 return $dataDump;
	}

	private function twig_ext_md5($data) {
		 $datamd5 = md5($data);
		 return $datamd5;
	}

	private function twig_ext_preg_replace($data, $pattern, $replacement) {
		$dataModified = preg_replace($pattern, $replacement, $data);
		 return $dataModified;
	}
	
	private function twig_ext_preg_quote($data, $delimiter=NULL) {
		if (empty($data)) {
			return $data;
		}
		return preg_quote($data, $delimiter);
	}

	private function twig_ext_preg_match_all($data, $pattern, $flags = 0, $offset=0) {
		if (empty($pattern)) {
			return NULL;
		}
		preg_match_all($pattern, $data, $out, $flags, $offset);
		return $out;
	}

	private function twig_ext_sortbyarray($data, $sorttype, $sortflag=SORT_REGULAR) {
			if (!($sortflag>0)) {
				$sortflag = SORT_REGULAR;
			}
			if ("arsort"==$sorttype) {
				arsort($data, $sortflag);
			}
			if ("asort"==$sorttype) {
				asort($data, $sortflag);
			}
			if ("rsort"==$sorttype) {
				rsort($data, $sortflag);
			}
			if ("ksort"==$sorttype) {
				ksort($data, $sortflag);
			}
			if ("krsort"==$sorttype) {
				krsort($data, $sortflag);
			}
		return $data;
	}

	private function twig_ext_flush($data, $sleep=0) {
		@ob_end_flush();
		echo $data;
		@flush();
		@ob_flush();
		if (is_int($sleep) && $sleep>0) {
			sleep($sleep);
		}
	}

	private function twig_ext_html_entity_decode($data, $flags="", $encoding="") {
		  if (empty($encoding)) {
			$encoding = ini_get("default_charset");
		}
		$flagbitmask = 0;
		   
		if (empty($flags)) {
			$flags = ENT_COMPAT | ENT_HTML401;
		} else {
			$flagsArr = explode("|", $flags);
			foreach($flagsArr as $fl) {
				$cval = constant(trim($fl));
				$flagbitmask = $flagbitmask | $cval;
			}
		}

		$dataModified = html_entity_decode($data, $flagbitmask, $encoding);
		 return $dataModified;
	}

	private function twig_ext_removenonprintable($data, $replacement) {
		$dataModified = preg_replace( '/[^[:print:]]/', $replacement, $data);
		 return $dataModified;
	}


	private function twig_ext_htmlentities($data, $flags, $encoding, $double_encode) {
		if (empty($flags)) {
			$flags = ENT_COMPAT | ENT_HTML401;
		}
		if (empty($encoding)) {
			$encoding = ini_get("default_charset");
		}
		if (empty($double_encode)) {
			$double_encode = TRUE;
		}
		$dataModified = htmlentities($data, $flags, $encoding, $double_encode);
		 return $dataModified;
	}
	
	private function twig_ext_htmlspecialchars_decode($data) {
		 $data = $this->unMaskSpecialCharsInJSON($data);
		 $dataTmp = htmlspecialchars_decode($data);
         if (empty($dataTmp)) {
           return $this->maskSpecialCharsInJSON($data);
         }
         return $this->maskSpecialCharsInJSON($dataTmp);
	}
	
	private function twig_ext_base64decode($data) {
         $data = $this->unMaskSpecialCharsInJSON($data);
         $dataTmp = base64_decode($data);
         if (empty($dataTmp)) {
           return $this->maskSpecialCharsInJSON($data);
         }
         return $this->maskSpecialCharsInJSON($dataTmp);
	}
	
	private function twig_ext_base64encode($data) {
         $data = $this->unMaskSpecialCharsInJSON($data);
         $dataTmp = base64_encode($data);
         if (empty($dataTmp)) {
           return $this->maskSpecialCharsInJSON($data);
         }
         return $this->maskSpecialCharsInJSON($dataTmp);
	}

	private function twig_ext_removespecialcharsinurl($data) {
         $data = $this->unMaskSpecialCharsInJSON($data);
         $data = strtolower($data);
         $data = preg_replace("/ /i", "-", $data);
         $data = str_replace(array('ä','ö','ü','ß','Ä','Ö','Ü', '&auml;', '&ouml;', '&uuml;', '&szlig;', '&Auml;', '&Ouml;', '&Uuml;'),array('ae','oe','ue','ss','Ae','Oe','Ue', 'ae', 'oe', 'ue', 'ss', 'Ae', 'Oe', 'Ue'),utf8_decode($data));
         $data = preg_replace("/[^a-z0-9\-]/i", "", $data);
         $data = $this->maskSpecialCharsInJSON($data);
         return $data;
	}

	private function twig_ext_stringshorter($data, $length, $suffix) {
			$data = $this->unMaskSpecialCharsInJSON($data);
			if (strlen($data)<=$length) {
				return $this->maskSpecialCharsInJSON($data);
			}
			$data = mb_substr($data, 0, $length).$suffix;
			return $this->maskSpecialCharsInJSON($data);
	}

	private function twig_ext_doshortcode($data) {
		return do_shortcode($data);
	}

	private function twig_ext_jsondecode($data, $assoc=FALSE, $depth=512, $options=0 ) {
		$jde = json_decode($data, $assoc, $depth, $options);
		if (is_null($jde)) {
			return '{"json_decode":"null"}';
		} 
		return $jde;
	}

	private function twig_ext_jsondecode4twig($data, $assoc=FALSE, $depth=512, $options=0 ) {
		return $this->twig_ext_jsondecode($data, TRUE, $depth, $options );
	}

	private function twig_ext_get_data_of_uploaded_file($filename) {
		if (isset($_FILES[$filename])) {
			return $_FILES[$filename];
		}
		return NULL;
	}

	private function twig_ext_get_file($filename) {
		if (file_exists($filename)) {
			$tmpdata = @file_get_contents($filename);
			return $tmpdata;
		}
		return NULL;
	}
	
	private function twig_ext_db_delete($db_table_name, $db_where="", $is_wp_db=1) {  
		# DELETE FROM $db_table_name WHERE $db_where;
		require_once "wp-load.php";
		global $wpdb;
		$dbreturn = Array();

		$sql = "DELETE FROM `";
		if (1==$is_wp_db) {
			$sql .= $wpdb->prefix;
		}
		$sql .= $db_table_name."` ";
		if (!empty($db_where)) {
			$sql .= " WHERE ".$db_where;
		}
		$dbreturn = $this->jci_execute_db_request($wpdb, $sql);
		return $dbreturn;
	}
	
	private function jci_execute_db_request($wpdb, $sql, $request="") {  
		$dbreturn = Array();
		$dbreturn["sql"] = $sql;		
		$wpdb->hide_errors();
		if (empty($request)) {
			$myrows = $wpdb->query($sql);
		} else if ("get_results" == $request) {
			$myrows = $wpdb->get_results($sql);
		} else {
			return NULL;
		}
		if($wpdb->last_error == '') {
			$dbreturn["success"] = TRUE;
			$dbreturn["result"] = $myrows;
		} else {
			$dbreturn["success"] = FALSE;
			$dbreturn["result"] = $wpdb->last_error;
		}
		return $dbreturn;
	}
	
	
	
	private function jci_join_params( $params ) {
		$query_params = array();
		foreach ( $params as $param_key => $param_value ) {
			$string = $param_key . '=' . $param_value;
			$query_params[] = str_replace( array( '+', '%7E' ), array( ' ', '~' ), rawurlencode( $string ) );
		}
		return implode( '%26', $query_params );
	}	
	
	private function twig_extra_function_jci_wpml_element_language_details($pageid, $cpt="post") {
		if (!defined('ICL_LANGUAGE_CODE')) {
			return ""; # WPML-Plugin not active 
		}
		$pg = intval($pageid);
		if ($pg==0) {
			$wpmlres['result'] = "invalid pageid $pageid";
			return json_encode($wpmlres);
		}
		$args = array('element_id' => $pg, 'element_type' => $cpt );
		$wpmlres = apply_filters('wpml_element_language_details', null, $args );
		if (is_null($wpmlres)) {
			$wpmlres['result'] = "page with pageid $pg of CPT $cpt not found";
			return json_encode($wpmlres);
		}
		return json_encode($wpmlres);
	}

	private function twig_extra_function_wp_set_post_categories($postid, $post_categories, $append = false) {
		$ret = wp_set_post_categories($postid, $post_categories, $append);
		return json_encode($ret);
	}

	private function twig_extra_function_jci_wpml_set_element_language_details($pageid_original, $page_id_translation, $cpt="post", $set_langcode_original="", $set_langcode_translation="", $docheck=FALSE, $debug=FALSE) {
		if (!defined('ICL_LANGUAGE_CODE')) {
			return ""; # WPML-Plugin not active 
		}
		$this->debug = $debug;
	
		$wpml_element_type = apply_filters( 'wpml_element_type', $cpt );

		$get_language_args = array('element_id' => $pageid_original, 'element_type' => $cpt );
        $original_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );
		if (empty($set_langcode_original)) {
			$set_langcode_original = $original_post_language_info->language_code;
		}

		if (empty($set_langcode_translation)) {
			$get_language_args_transl = array('element_id' => $page_id_translation, 'element_type' => $cpt );
			$transl_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args_transl );
			$set_langcode_translation = $transl_post_language_info->language_code;
		}

		$this->debug_out("wpml-translation of $pageid_original (".($set_langcode_original).") is $page_id_translation (".$set_langcode_translation.")", "CPT: $cpt"); 

        $set_language_args = array(
            'element_id'    => $page_id_translation,
            'element_type'  => $wpml_element_type,
            'trid'   => $original_post_language_info->trid,
            'language_code'   => $set_langcode_translation,
            'source_language_code' => $set_langcode_original
        );
		do_action( 'wpml_set_element_language_details', $set_language_args );	

		#check
		if ($docheck) {
			$args = array('element_id' => $pageid_original, 'element_type' => $cpt );
			$check_org = apply_filters( 'wpml_element_language_details', null, $args );
			$args = array('element_id' => $page_id_translation, 'element_type' => $cpt );
			$check_transl = apply_filters( 'wpml_element_language_details', null, $args );
			if ($check_transl->trid == $check_org->trid) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
		return TRUE;
	}

	private function twig_extra_function_jci_forward($url) {
		header('Location: '.$url);
		exit;
	}

	private function twig_extra_function_jci_getcookie($name) {
		if (empty($name)) {
			return $_COOKIE;
		}
		return $_COOKIE[$name];
	}

	private function twig_extra_function_jci_setcookie($name, $value="", $expires=0, $path="", $domain="", $secure=false, $httponly=false) {
		if (empty($name)) {
			return NULL;
		}
		$errorlevSetCookie = setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
		/*if ($errorlevSetCookie) {
			echo "OK: setcookie $name<br>";
		} else {
			echo "FAIL: setcookie $name<br>";
		}*/
	}

	private function twig_extra_function_woocommmerce_calc_authorization($consumer_key, $consumer_secret, $request_uri, $http_method) {
		if (empty($consumer_key) || empty($consumer_secret) || empty($request_uri) || empty($http_method)) {
			return "";
		}
		$nonce = uniqid();
		$timestamp = time();
		$oauth_signature_method = 'HMAC-SHA1';
		$hash_algorithm = strtolower( str_replace( 'HMAC-', '', $oauth_signature_method ) ); // sha1
		$secret = $consumer_secret . '&';

		$base_request_uri = rawurlencode( $request_uri );
		$params = array( 
			'oauth_consumer_key' => $consumer_key, 
			'oauth_nonce' => $nonce, 
			'oauth_signature_method' => $oauth_signature_method, 
			'oauth_timestamp' => $timestamp 
		);
		$query_string = $this->jci_join_params( $params );

		$string_to_sign = $http_method . '&' . $base_request_uri . '&' . $query_string;
		$oauth_signature = base64_encode( hash_hmac( $hash_algorithm, $string_to_sign, $secret, true ) );
		
		$authstr = "Authorization: OAuth oauth_consumer_key=\"".$consumer_key."\",oauth_signature_method=\"".$oauth_signature_method."\",oauth_timestamp=\"".$timestamp."\",oauth_nonce=\"".$nonce."\",oauth_signature=\"".$oauth_signature."\"";
		return $authstr;
	}


	private function twig_extra_function_user_create($username, $password, $email="", $role="") {
		$dbreturn = Array();
		if (empty($username) || empty($password)) {
			$dbreturn["success"] = FALSE;
			$dbreturn["result"] = "username or passwort empty: no new user created";
			return $dbreturn;
		}

		$roleexisting = FALSE;
		if (!empty($role)) {
			$rolelist = wp_roles();
			foreach ($rolelist->roles as $k=>$v) {
				if ($k==$role) {
					$roleexisting = TRUE;
					break;
				}
			}
			if (!$roleexisting) {
				$dbreturn["success"] = FALSE;
				$dbreturn["result"] = "role for new user not existing - no new user created";
				return $dbreturn;
			}
		}
		
		$newuser = wp_create_user($username, $password, $email);
		if( is_wp_error( $newuser ) ) {
			$dbreturn["success"] = FALSE;
			$dbreturn["result"] = $newuser->get_error_message();
			return $dbreturn;
		}	
		if ($roleexisting) {
			$usridrole = new WP_User($newuser);
			$usridrole->set_role($role);
		}
		$dbreturn["success"] = TRUE;
		$dbreturn["result"] = $newuser;
		return $dbreturn;
	}

	private function twig_ext_db_create($db_table_name, $json_for_db, $is_wp_db=1) {  
		# CREATE TABLE table_name (    column2 datatype,    column3 datatype,   ....); 
		require_once "wp-load.php";
		global $wpdb;
		$dbreturn = Array();
		$jsonInsertArr = json_decode($json_for_db, TRUE);
		if (is_null($jsonInsertArr )) {
			$dbreturn["error"] = FALSE;
			$dbreturn["result"] = "invalid json";
			$dbreturn["json"] = $json_for_db;
			return $dbreturn;
		}
		$sql = "CREATE TABLE `";
		if (1==$is_wp_db) {
			$sql .= $wpdb->prefix;
		}
		$sql .= $db_table_name."` ( ";
		$updatestr = "";
		foreach($jsonInsertArr as $k=>$v) {
			$updatestr .= $k." ";
			$updatestr .= $v.",";
		}
		$createstr = rtrim($updatestr, ",");
		$sql .= $createstr." ) ";
		$dbreturn = $this->jci_execute_db_request($wpdb, $sql);
		return $dbreturn;
	}

	private function twig_ext_db_update($db_table_name, $json_for_db, $db_where="", $is_wp_db=1) {  
		# UPDATE table_name SET column1 = value1, column2 = value2, ... WHERE condition; 
		require_once "wp-load.php";
		global $wpdb;
		$dbreturn = Array();
		$jsonInsertArr = json_decode($json_for_db, TRUE);
		if (is_null($jsonInsertArr )) {
			$dbreturn["error"] = FALSE;
			$dbreturn["result"] = "invalid json";
			$dbreturn["json"] = $json_for_db;
			return $dbreturn;
		}
		$sql = "UPDATE `";
		if (1==$is_wp_db) {
			$sql .= $wpdb->prefix;
		}
		$sql .= $db_table_name."` SET ";
		$updatestr = "";
		foreach($jsonInsertArr as $k=>$v) {
			$updatestr .= "`".$k."`=";
			$updatestr .= "'".$v."',";
		}
		$updatestr = rtrim($updatestr, ",");
		$sql .= $updatestr;
		if (!empty($db_where)) {
			$sql .= " WHERE ".$db_where;
		}
		$dbreturn = $this->jci_execute_db_request($wpdb, $sql);
		return $dbreturn;
	}


	private function twig_ext_db_insert($db_table_name, $json_for_db, $is_wp_db=1) {  
		# INSERT INTO '$db_table_name' ('key1', 'key2',... ) VALUES ('val1', 'val2',...)
		require_once "wp-load.php";
		global $wpdb;
		$dbreturn = Array();
		$jsonInsertArr = json_decode($json_for_db, TRUE);
		if (is_null($jsonInsertArr )) {
			$dbreturn["error"] = FALSE;
			$dbreturn["result"] = "invalid json";
			$dbreturn["json"] = $json_for_db;
			return $dbreturn;
		}
		$sql = "INSERT INTO `";
		if (1==$is_wp_db) {
			$sql .= $wpdb->prefix;
		}
		$sql .= $db_table_name."` ";
		$keystr = "";
		$valstr = "";
		foreach($jsonInsertArr as $k=>$v) {
			$keystr .= "`".$k."`,";
			$valstr .= "'".$v."',";
		}
		$keystr = rtrim($keystr, ",");
		$valstr = rtrim($valstr, ",");
		$sql .= "(".$keystr.") VALUES "."(".$valstr.")"; 
		$dbreturn = $this->jci_execute_db_request($wpdb, $sql);
		return $dbreturn;
	}
	
	private function twig_ext_db_select($db_fields, $db_table_name, $db_where="", $is_wp_db=1) {  
		# SELECT $db_fields FROM $db_table_name WHERE $db_where
		require_once "wp-load.php";
		global $wpdb;
		$sql = "SELECT ".$db_fields." FROM `";
		if (1==$is_wp_db) {
			$sql .= $wpdb->prefix;
		}
		$sql .= $db_table_name."` ";
		if (!empty($db_where)) {
			$sql .= " WHERE ".$db_where;
		}
		$dbreturn = $this->jci_execute_db_request($wpdb, $sql, "get_results");
		return $dbreturn;
	}

	private function twig_ext_base64encode_savefile($filename) {
			#return NULL;
			$filenameb64 = $filename.'-b64';
			$chunkSize = 1024;
			$src = fopen($filename, 'rb');
			$dst = fopen($filenameb64, 'wb');
			while (!feof($src)) {
				fwrite($dst, base64_decode(fread($src, $chunkSize)));
			}
			fclose($dst);
			fclose($src);	
			return $filenameb64;
	}

	private function debug_out($intromsg, $msg) {
		if ($this->debug) {
			echo $intromsg.": ".htmlentities($msg)."<br>"; 
		}
	}

	private function twig_ext_delete_custom_post($postid, $force_delete = FALSE, $debug=FALSE) {
		$postid = (int) $postid;
		$this->debug_out("delete CP - postid", $postid); 
		$delcpt = wp_delete_post($postid, $force_delete);
		return $delcpt;
	}
	
	private function twig_ext_set_featured_image($postid, $thumbnail_id) {
		$postid = absint( $postid );
		$thumbnail_id = absint( $thumbnail_id );
		return set_post_thumbnail( $postid, $thumbnail_id );
	}

	private function twig_ext_get_featured_image($postid='', $size='post-thumbnail', $attr = '') {
		return get_the_post_thumbnail( $postid, $size, $attr );
	}

	private function twig_ext_update_custom_post($postid, $titel="", $name="", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $parentPageId="", $debug=FALSE) {
		$this->debug = $debug;
		$updateArrError = Array();
		if (!is_int(($postid))) {
			$updateArrError["status"] = FALSE;
			$updateArrError["msg"] = "invalid postid";
			return json_encode($updateArrError);
		}
		
		$this->debug_out("update CP - pageid", $postid); 
		
		$updateArr = Array();
		$updateArr["ID"] = $postid;
		if (!empty($titel)) { $updateArr["post_title"] = $titel; }
		if (!empty($name)) { $updateArr["post_name"] = $name; }
		if (!empty($content)) { $updateArr["post_content"] = $content; }
		if (!empty($excerpt)) { $updateArr["post_excerpt"] = $excerpt; }
		if (!empty($publishdate)) { $updateArr["post_date"] = $publishdate; }
		if (!empty($postStatusUsed)) { $updateArr["post_status"] = $postStatusUsed; }
		if (!empty($authorid)) { $updateArr["post_author"] = $authorid; }
		if (!empty($parentPageId)) { $updateArr["post_parent"] = $parentPageId;	}
		
		$this->debug_out("update CP - updates", json_encode($updateArr)); 
		
		$update_post_id = wp_update_post( $updateArr );
		if ($update_post_id==$postid) {
			$updateArrError["status"] = TRUE;
			$updateArrError["msg"] = "update ok";
			return json_encode($updateArrError);
		}
		$updateArrError["status"] = FALSE;
		$updateArrError["msg"] = "update failed";
		return json_encode($updateArrError);
	}
	
	private function twig_ext_get_attachment_image_url($attachment_id, $size = 'thumbnail', $icon = FALSE) {
		$attachment_id = absint( $attachment_id );
		$retval = Array();
		$retval["jci"]["status"] = "notok";
		$retval["jci"]["statusmsg"] = "";
		if (empty($attachment_id)) {
			$retval["jci"]["statusmsg"] = "attachment ID must be integer: ".htmlentities($attachment_id);
			return $retval;
		} else {
			$getAttImgUrl = wp_get_attachment_image_url( $attachment_id, $size, $icon);
		}
		if (!$getAttImgUrl) {
			$retval["jci"]["statusmsg"] = "no image found for ID ".htmlentities($attachment_id); # e.g. just delete image does not delete the CPF!
			return $retval;
		}
		$retval["jci"]["status"] = "ok";
		$retval["jci"]["url"] = $getAttImgUrl;
		return $retval;
	}


	private function twig_ext_create_new_custom_post($posttype, $titel="set title in twig, please", $name="set name in twig, please", $content="set content in twig, please",  $excerpt="", $publishdate="", $postStatusUsed="publish", $authorid="", $debug=FALSE) {
		$this->debug = $debug;
		$this->debug_out("create CP - posttype", $posttype); 
		$this->debug_out("create CP - publishdate in", $publishdate); 
		
		$publishdate = $this->executeTwig($this->jsonObj, $publishdate, "twig332adj", FALSE); # required format is Y-m-d H:i:s 
		$timestampofdate = strtotime($publishdate);
		$publishdate = date('Y-m-d H:i:s', $timestampofdate);
		if (empty($publishdate)) {
			$publishdate = date('Y-m-d H:i:s', strtotime('now'));
		}
		$this->debug_out("create CP - publishdate used", $publishdate); 
		$titel = $this->executeTwig($this->jsonObj, $titel, "twig332adj", FALSE);
		$this->debug_out("create CP - titel", $titel); 

		$name = $this->executeTwig($this->jsonObj, $name, "twig332adj", FALSE);
		$this->debug_out("create CP - name", $name); 

		$content = $this->executeTwig($this->jsonObj, $content, "twig332adj", FALSE);
		$this->debug_out("create CP - content", $content); 

		$this->debug_out("create CP - postStatusUsed", $postStatusUsed); 
		if (empty($authorid)) {
			$authorid = get_current_user_id();
		}
		$this->debug_out("create CP - authorid", $authorid); 
		$newPostArr = array(
			'post_title'   => $titel,
			'post_name'    => $name,
			'post_content' => $content,
			'post_status'  => $postStatusUsed,
			'post_type'    => $posttype,
			'post_date'    => $publishdate, 
			'post_author'  => $authorid,
			# 'post_category'=> $newPostCategory,
			# 'post_parent'  =>
		);

		if (!empty($excerpt)) {
			$newPostArr["post_excerpt"] = $excerpt;
			$this->debug_out("create CP - excerpt", $excerpt); 
		}


		$idOfNewPost = wp_insert_post($newPostArr);
		$this->debug_out("create CP - idOfNewPost", $idOfNewPost); 
		$this->debug = FALSE;
		return $idOfNewPost;						
	}
	

	private function twig_ext_medialist($postid= "", $pattern="") {
		$attachment = array( 
			'posts_per_page' => -1, 
			'post_type' => 'attachment', 
			'post_status' => 'any',
			#'post_status' => 'inherit'
		); 
		if (!empty($pattern)) {
			$attachment['s'] = $pattern;
		}
		if (!empty($postid)) {
			$attachment['post_parent'] = $postid;
		}
		$query_images = new WP_Query( $attachment ); 
		return $query_images->posts;
	}
	
	private function twig_ext_insert_taxonomy($pageid, $taxonomySlug, $taxonomyValue, $debug=FALSE) {
		$this->debug = $debug;
		$el = wp_set_object_terms( $pageid, $taxonomyValue, $taxonomySlug ); 
		$this->debug_out("add taxonomy to page $pageid", "$taxonomySlug"); 
		$this->debug = FALSE;
		if( is_wp_error( $el ) ) {
			return $el->get_error_message();
		}		
		return json_encode($el);
	}
	
	private function twig_ext_clear_taxonomy($taxonomySlug, $debug=FALSE) {
		$this->debug = $debug;
		$getTerms = get_terms( $taxonomySlug, array( 'fields' => 'ids', 'hide_empty' => false ) ); # get all pages with this taxonomyslug
		$this->debug_out("clear taxonomy $taxonomySlug", json_encode($getTerms)); 
		$this->debug = FALSE;
		foreach ( $getTerms as $valueTax ) {
			wp_delete_term( $valueTax, $taxonomySlug );
		}
		return json_encode($getTerms);
	}
	
	
	private function twig_ext_get_cp_by_cpf_keyvalue($post_type="", $key="", $value="", $debug=FALSE) {
		if (empty($post_type)) {
			return "1st argument for wp_get_cp_by_cpf_keyvalue is mandatory: this is the post_type like 'attachment'<br>";
		}
		$this->debug = $debug;
		$value = $this->executeTwig($this->jsonObj, $value, "twig332adj", FALSE);
		
		$getpostArray = array(
			'numberposts'	=> -1,
			'post_status' => 'any',
		);
		if (!empty($post_type)) {
			$getpostArray['post_type'] = $post_type;
		}

		if (!empty($key) && !empty($value)) {
			$keyValueArray = array(
				array(
					'key'	 	=> $key,
					'value'	  	=> $value,
				)
			);
			$getpostArray['meta_query'] = $keyValueArray;
		}
		$this->debug_out("get CP by CPF:", json_encode($getpostArray)); 
		
		$posts = get_posts($getpostArray);	
		$out = Array();
		$i = 0;
		if( $posts ) {
			foreach( $posts as $post ) {
				$out[$i] = $post->ID;
				$i++;
			}
		}
		$this->debug = FALSE;
		return $out;
	}
	
	private function twig_ext_get_page_properties($debug=FALSE, $pageid="") {
		$this->debug = $debug;
		$propArr = Array();
		if (empty($pageid)) {
			$propArr["get_permalink"] = get_permalink();
			$propArr["home_url"] = home_url();
			$userid = get_current_user_id();
			$propArr["get_current_user_id"] = $userid;
			$usrdataArr = get_userdata($userid);
			$propArr["userdata"] = $usrdataArr;
			$postparam = get_post();
			$pageid = $postparam->ID;
		} else {
			$postparam = get_post($pageid);
		}
		$postparam->post_content = preg_replace("/jsoncontentimporterpro/", "", trim($postparam->post_content)); # shortcode in $postparam->post_content are executed when used in twig, result: infinite loop or plugin-termination -> remove "jsoncontentimporterpro" to invalidate shortcode
		$propArr["get_post"] = $postparam;
		
		$cpflist = get_post_meta($pageid);
		if (is_array($cpflist)) {
			$propArr["cpf"] = $cpflist;
		}
		
		if (defined('ICL_LANGUAGE_CODE')) {
			$propArr["wpml"]["lang"] = ICL_LANGUAGE_CODE;
		}

		$this->debug_out("get page properties", print_r($propArr, TRUE)); 

		$this->debug = FALSE;
		return $propArr;
	}



	private function twig_ext_get_custom_field_value($pageid, $key="", $debug=FALSE) {
		$this->debug = $debug;
		$this->debug_out("get custom field value", "pageid: $pageid / key in: $key"); 
		$key = $this->executeTwig($this->jsonObj, $key, "twig332adj", FALSE);
		if (empty($key)) {
			$this->debug_out("get custom field value", "key is empty, no CPF"); 
			$this->debug = FALSE;
			return "";
		}
		
		
		$key = $this->executeTwig($this->jsonObj, $key, "twig332adj", FALSE);
		$this->debug_out("get custom field value", "pageid: $pageid / key used: $key"); 
		$cpfvalue = get_post_custom_values($key, $pageid);
		$this->debug_out("get custom field value", "found value: ".print_r($cpfvalue, TRUE)); 
		$this->debug = FALSE;
		return $cpfvalue;
	}

	private function twig_ext_create_insert_custom_field_keyvalue($pageid, $key="", $value="", $debug=FALSE) {
		$this->debug = $debug;
		$this->debug_out("set CPF $pageid in", "$key : $value"); 
		$key = $this->executeTwig($this->jsonObj, $key, "twig332adj", FALSE);
		$value = $this->executeTwig($this->jsonObj, $value, "twig332adj", FALSE);
		$cpfinsertstatus = NULL;
		if (!empty($key)) {
			$cpfinsertstatus = update_post_meta($pageid, $key, $value);
			$this->debug_out("set CPF $pageid used", "$key : $value, Status: $cpfinsertstatus"); 
		} else {
			$this->debug_out("set CPF $pageid not SET (empty key or value)", "$key : $value"); 
		}
		$this->debug = FALSE;
		return $cpfinsertstatus;
	}

	private function twig_ext_urldecode($data) {
		return urldecode($data);
	}
	private function twig_ext_stripslashes($data) {
		return stripslashes($data);
	}
	
	private function twig_ext_mediafilename($data, $withpath=TRUE, $removeqm=FALSE, $sourcetype="", $addfileformatname="") {
		if (is_null($data)) {
			return NULL;
		}
		/*
		$data_url = "https://www.whatever.de/wp/uploads/a.jpg?z=1&u=2";
		$data_linux = "/usr/bin/b.jpg??z=1&u=2";
		$data_win = "C:/whatever/this/is/c.jpg??z=1&u=2";
		$data = $data_linux;
		$removeqm = FALSE;
		$removeqm = TRUE;
		$withpath = FALSE; 
		$withpath = TRUE;
		*/
		
		$filename = $data;
		if ($sourcetype=="file") {
			if (!file_exists($data)) {
				$retval["jci"]["statusmsg"] = "file not found";
				return json_encode($retval);
			}
		}
		
		if (!$withpath) {
			$filename = basename($data);
		}

		if ($removeqm) {  #a.jpg?a=1 -> a.jpg
			$filenameArr = explode("?", $filename);
			$filename = $filenameArr[0];
		}
		
		$filename = preg_replace("/^\//", "", $filename);
		$filename = preg_replace("/\//", "-", $filename);
		$filename = preg_replace("/\-\-/", "-", $filename);
		$filename = preg_replace("/\:/", "", $filename);
		$filename = preg_replace("/\?/", "QM", $filename);
		$filename = preg_replace("/\#/", "HT", $filename);
		$filename = preg_replace("/\=/", "EQ", $filename);
		$filename = urlencode($filename);
		
		if (!empty($addfileformatname)) {
			$filename .= $addfileformatname;
		}
		
		return $filename;
	}


	private function twig_ext_mediastore($data, $parentPageId="", $mediatitle="", $postslug= "", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", 
		$sourcetype="", $withpath=TRUE, $removeqm=FALSE, $generate_thumbnails=TRUE, $checkLibraryOnMediafile=TRUE, $loadContext="", $addfileformatname="") {
		if (is_null($data)) {
			return NULL;
		}
		if (!$generate_thumbnails) {
			add_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );
		}
		
		$retval = array();
		$retval["jci"]["file"] = $data;
		$retval["jci"]["status"] = "notok";
		$retval["jci"]["statusmsg"] = "";
		
		$filename = $this->twig_ext_mediafilename($data, $withpath, $removeqm, "", $addfileformatname);

		$retval["jci"]["filename"] = $filename;
		$retval["jci"]["status"] = "";
		
		# filename = slug = post_name, and maybe post_title if there is no mediatitle
		$post_title = $filename; 
		if (!empty($mediatitle)) {
			$post_title = $mediatitle;
		}
		if ($checkLibraryOnMediafile) { # due to backwarda compatibility
			$this->CheckLibraryOnMediafile($filename);
		}
		
		$loadContext_stream_context = NULL;
		if (!empty($loadContext)) { # $loadContext must be JSON, example: {"http": {"method": "GET", "header": ["Accept-language: en\r\n Cookie: foo=bar\r\n"]}}
			$loadContext_stream_context_array = json_decode($loadContext, TRUE);
			if (is_array($loadContext_stream_context_array)) {
				$loadContext_stream_context = stream_context_create($loadContext_stream_context_array);
			}
		}
		$uploadfile = wp_upload_bits($filename, null, file_get_contents($data, false, $loadContext_stream_context)); # return: file, urltype, error (bool)
		if ($uploadfile['error']) {
			$retval["jci"]["statusmsg"] = "upload failed";
			$retval["jci"]["errormessage"] = $uploadfile;
			return json_encode($retval);
		} else {
			$wp_filetype = wp_check_filetype($filename, null);
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_status' => 'inherit'
			);
			
			$attachment['post_title'] = $post_title;
			if (!empty($excerpt)) {	$attachment['post_excerpt'] = $excerpt;		}
			$attachment['post_content'] = ''; 	
			if (!empty($content)) { $attachment['post_content'] = $content;	}
			if (!empty($parentPageId)) { $attachment['post_parent'] = $parentPageId;	}
			if (!empty($postslug)) { $attachment['post_name'] = $postslug;		}
			if (!empty($publishdate)) { $attachment["post_date"] = $publishdate; }
			if (!empty($postStatusUsed)) { $attachment["post_status"] = $postStatusUsed; }
			if (!empty($authorid)) { $attachment["post_author"] = $authorid; }
			
			
			$attachment_id = wp_insert_attachment($attachment, $uploadfile['file']);
			if (is_wp_error($attachment_id)) {
				$retval["jci"]["statusmsg"] = "is_wp_error";
				return json_encode($retval);
				#$retval .= "0";
			} else {
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				#$preulb = microtime(TRUE);
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $uploadfile['file'] );
				#$retval["jci"]["timeuploadbits"] = microtime(TRUE)-$preulb;
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
				$attdata = wp_get_attachment_metadata($attachment_id); 

				$retval["jci"]["statusmsg"] = "ok";
				$retval["jci"]["status"] = "ok";
				$retval["jci"]["attachment_id"] = $attachment_id;
				$retval["jci"]["attachment_url"] = wp_get_attachment_url($attachment_id);
			}
		}
		return json_encode($retval);
	}
	
	private function CheckLibraryOnMediafile($filename){
		global $wpdb;
		# get filename and extension
		$query = "SELECT ID FROM $wpdb->posts WHERE post_type='attachment' AND post_title = '".$filename."'";
		$resArr = $wpdb->get_results($query);
		#echo "<br>filename: $filename<br>";
		#var_Dump($resArr);
		if (0==count($resArr)) {
			return TRUE;
		} else {
			foreach( $resArr as $key => $row) {
				#echo "DELETE: $key<br";
				$upldir = wp_get_upload_dir();
				$baseURLUploads = $upldir["baseurl"];
				$baseDIRUploads = $upldir["basedir"];

				$postid = $row->ID;
				$atturl = wp_get_attachment_url($postid);
				$r = explode($baseURLUploads, $atturl);
				$r1 = explode("/", $r[1]);
				$mainfilename = array_pop($r1);
				$pa = $baseDIRUploads.join("/", $r1)."/";
				
				$attmdArr = wp_get_attachment_metadata($postid);
				#echo "<br>";
				#var_Dump($attmdArr);
				#echo "<br>";
				foreach($attmdArr["sizes"] as $k => $v) {
					$oldfile = $pa.$v["file"];
					if (file_exists($oldfile)) {
						#echo "unlink: ".$oldfile."<br>";
						unlink($oldfile);
					}
				}
				
				#wp_get_attachment_url
				#echo "unlink main: $pa$mainfilename";
				unlink($pa.$mainfilename);
				wp_delete_attachment( $postid, TRUE);
				
			}
		}
		return TRUE;
	}

	private function twig_ext_formatnumber($data, $decimals, $dec_point, $thousands_sep) {
         if (!is_numeric($data)) {
           return $data;
         }
         $numArr = explode("-", (string) $data);
         if (count($numArr)==2 && is_numeric($numArr[1]) && $numArr[1]>0) {
           $data = number_format($data, $numArr[1]+1, $dec_point, $thousands_sep);
           return $data;
         }
         $numArr = explode("+", (string) $data);
         if (count($numArr)==2 && is_numeric($numArr[1]) && $numArr[1]>0) {
           $data = number_format($data, 0, $dec_point, $thousands_sep);
           return $data;
         }
         $data = number_format($data, $decimals, $dec_point, $thousands_sep);
         return $data;
	}
	
	
	
private function addTwigExtensions() {
		if ($this->isTwig3 || $this->isTwig332 ) {
			$this->twig_environment->addExtension(new \Twig\Extension\StringLoaderExtension()); #https://twig.symfony.com/doc/3.x/api.html 
		}
		if ($this->isTwig332adj) {
			$this->twig_environment->addExtension(new \JCITwig\Extension\StringLoaderExtension()); #https://twig.symfony.com/doc/3.x/api.html 
		}

		if ($this->isTwig2 || $this->isTwig3 || $this->isTwig332) {
			$twig_extra_filter_sortbyjsonfield = new \Twig\TwigFilter('sortbyjsonfield', function ($data, $sortdata) {
				return $this->twig_ext_sortbyjsonfield($data, $sortdata);
			});
			$twig_extra_filter_dateformat = new \Twig\TwigFilter('dateformat', function ($data, $dateformatstr, $datetimezone, $datelocale) {
				return $this->twig_ext_dateformat($data, $dateformatstr, $datetimezone, $datelocale);
			});
			$twig_extra_filter_converthex2ascii = new \Twig\TwigFilter('converthex2ascii', function ($data) {
				return $this->twig_ext_converthex2ascii($data);
			});
			$twig_extra_filter_convert2html = new \Twig\TwigFilter('convert2html', function ($data) {
				return $this->twig_ext_convert2html($data);
			});
			$twig_extra_filter_dump = new \Twig\TwigFilter('dump', function ($data) {
				return $this->twig_ext_dump($data);
			});
			$twig_extra_filter_md5 = new \Twig\TwigFilter('md5', function ($data) {
				return $this->twig_ext_md5($data);
			});
			$twig_extra_filter_preg_replace = new \Twig\TwigFilter('preg_replace', function ($data, $pattern, $replacement) {
				return $this->twig_ext_preg_replace($data, $pattern, $replacement);
			});
			$twig_extra_filter_preg_match_all = new \Twig\TwigFilter('preg_match_all', function ($data, $pattern, $flags = 0, $offset=0) {
				return $this->twig_ext_preg_match_all($data, $pattern, $flags, $offset);
			});
			$twig_extra_filter_preg_quote = new \Twig\TwigFilter('preg_quote', function ($data, $delimiter=NULL) {
				return $this->twig_ext_preg_quote($data, $delimiter);
			});
			
			$twig_extra_filter_sortbyarray = new \Twig\TwigFilter('sortbyarray', function ($data, $sorttype, $sortflag=SORT_REGULAR) {
				return $this->twig_ext_sortbyarray($data, $sorttype, $sortflag);
			});
			$twig_extra_filter_htmlentitydecode = new \Twig\TwigFilter('html_entity_decode', function ($data, $flags="", $encoding="") {
				return $this->twig_ext_html_entity_decode($data, $flags, $encoding);
			});
			
			$twig_extra_filter_flush = new \Twig\TwigFilter('flush', function ($data, $sleep=0) {
				return $this->twig_ext_flush($data, $sleep);
			});

			$twig_extra_filter_removenonprintable = new \Twig\TwigFilter('removenonprintable', function ($data, $replacement) {
				return $this->twig_ext_removenonprintable($data, $replacement);
			});
			$twig_extra_filter_htmlentities = new \Twig\TwigFilter('htmlentities', function ($data, $flags=ENT_QUOTES , $encoding=null, $double_encode=true) {
				return $this->twig_ext_htmlentities($data, $flags, $encoding, $double_encode);
			});
			$twig_extra_filter_htmlspecialchars_decode = new \Twig\TwigFilter('htmlspecialchars_decode', function ($data) {
				return $this->twig_ext_htmlspecialchars_decode($data);
			});
			$twig_extra_filter_base64encode = new \Twig\TwigFilter('base64encode', function ($data) {
				return $this->twig_ext_base64encode($data);        
			});
			$twig_extra_filter_base64decode = new \Twig\TwigFilter('base64decode', function ($data) {
				return $this->twig_ext_base64decode($data);        
			});
			$twig_extra_filter_removespecialcharsinurl  = new \Twig\TwigFilter('removespecialcharsinurl', function ($data) {
				return $this->twig_ext_removespecialcharsinurl($data);
			});
			$twig_extra_filter_stringshorter  = new \Twig\TwigFilter('stringshorter', function ($data, $length, $suffix) {
				return $this->twig_ext_stringshorter($data, $length, $suffix);
			});
			$twig_extra_filter_numberformat  = new \Twig\TwigFilter('formatnumber', function ($data, $decimals, $dec_point, $thousands_sep) {
				return $this->twig_ext_formatnumber($data, $decimals, $dec_point, $thousands_sep);
			});
			$twig_extra_filter_doshortcode  = new \Twig\TwigFilter('doshortcode', function ($data) {
				return $this->twig_ext_doshortcode($data);
			});
			$twig_extra_filter_jsondecode  = new \Twig\TwigFilter('json_decode', function ($data, $assoc=FALSE, $depth=512, $options=0) {
				return $this->twig_ext_jsondecode($data, $assoc, $depth, $options);
			});
			$twig_extra_filter_jsondecode4twig  = new \Twig\TwigFilter('json_decode_4twig', function ($data, $assoc=FALSE, $depth=512, $options=0) {
				return $this->twig_ext_jsondecode4twig($data, $assoc, $depth, $options);
			});

			$twig_extra_filter_mediastore = new \Twig\TwigFilter('mediastore', function ($data, $parentPageId="", $mediatitle="", $postslug= "", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $sourcetype="", 
				$withpath=TRUE, $removeqm=FALSE, $generate_thumbnails=TRUE, $checkLibraryOnMediafile=TRUE, $loadContext="", $addfileformatname="") {
				return $this->twig_ext_mediastore($data, $parentPageId, $mediatitle, $postslug, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $sourcetype, $withpath, $removeqm, $generate_thumbnails, $checkLibraryOnMediafile, $loadContext, $addfileformatname);
			});
			$twig_extra_filter_wp_mediastore = new \Twig\TwigFunction('wp_mediastore', function ($data, $parentPageId="", $mediatitle="", $postslug= "", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $sourcetype="", 
				$withpath=TRUE, $removeqm=FALSE, $generate_thumbnails=TRUE, $loadContext="", $addfileformatname="") {
				return $this->twig_ext_mediastore($data, $parentPageId, $mediatitle, $postslug, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $sourcetype, $withpath, $removeqm, $generate_thumbnails, FALSE, $loadContext, $addfileformatname);
			});
			$this->twig_environment->addFunction($twig_extra_filter_wp_mediastore);

			$twig_extra_filter_mediafilename = new \Twig\TwigFunction('wp_mediafilename', function ($data, $withpath=TRUE, $removeqm=FALSE, $sourcetype="", $addfileformatname="") {
				return $this->twig_ext_mediafilename($data, $withpath, $removeqm, $sourcetype, $addfileformatname);
			});
			$this->twig_environment->addFunction($twig_extra_filter_mediafilename);
			
			$twig_extra_filter_urldecode = new \Twig\TwigFilter('urldecode', function ($data) {
				return $this->twig_ext_urldecode($data);
			});
			$twig_extra_filter_stripslashes = new \Twig\TwigFilter('stripslashes', function ($data) {
				return $this->twig_ext_stripslashes($data);
			});

			# twig 2,3 only BEGIN
			$twig_extra_filter_insert_taxonomy = new \Twig\TwigFunction('wp_insert_taxonomy', function ($pageid, $taxonomySlug, $taxonomyValue, $debug=FALSE) {
				return $this->twig_ext_insert_taxonomy($pageid, $taxonomySlug, $taxonomyValue, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_insert_taxonomy);
			
			$twig_extra_filter_medialist = new \Twig\TwigFunction('wp_medialist', function ($postid="", $pattern="") {
				return $this->twig_ext_medialist($postid, $pattern);
			});
			$this->twig_environment->addFunction($twig_extra_filter_medialist);
			
			$twig_extra_filter_clear_taxonomy = new \Twig\TwigFunction('wp_clear_taxonomy', function ($taxonomySlug, $debug=FALSE) {
				return $this->twig_ext_clear_taxonomy($taxonomySlug, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_clear_taxonomy);

			$twig_extra_filter_get_page_properties = new \Twig\TwigFunction('wp_get_page_properties', function ($debug=FALSE, $pageid="") {
				return $this->twig_ext_get_page_properties($debug, $pageid);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_page_properties);

			$twig_extra_filter_get_cp_by_cpf_keyvalue = new \Twig\TwigFunction('wp_get_cp_by_cpf_keyvalue', function ($post_type="", $key="", $value="", $debug=FALSE) {
				return $this->twig_ext_get_cp_by_cpf_keyvalue($post_type, $key, $value, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_cp_by_cpf_keyvalue);

			$twig_extra_filter_get_custom_field_value = new \Twig\TwigFunction('wp_get_custom_field_value', function ($pageid, $key, $debug=FALSE) {
				return $this->twig_ext_get_custom_field_value($pageid, $key, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_custom_field_value);
			
			$twig_extra_filter_insert_custom_field_keyvalue = new \Twig\TwigFunction('wp_insert_custom_field_keyvalue', function ($pageid, $key, $value, $debug=FALSE) {
				return $this->twig_ext_create_insert_custom_field_keyvalue($pageid, $key, $value, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_insert_custom_field_keyvalue);

			$twig_extra_filter_create_new_custom_post = new \Twig\TwigFunction('wp_create_new_custom_post', function ($posttype, $titel, $name="", $content="", $publishdate="", $postStatusUsed="publish", $authorid="", $debug=FALSE) {
				return $this->twig_ext_create_new_custom_post($posttype, $titel, $name, $content, "", $publishdate, $postStatusUsed, $authorid, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_create_new_custom_post);
			$twig_extra_filter_new_custom_post = new \Twig\TwigFunction('wp_new_custom_post', function ($posttype, $titel, $name="", $content="", $excerpt="", $publishdate="", $postStatusUsed="publish", $authorid="", $debug=FALSE) {
				return $this->twig_ext_create_new_custom_post($posttype, $titel, $name, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_new_custom_post);
			
			$twig_extra_function_get_attachment_image_url = new \Twig\TwigFunction('wp_get_attachment_image_url', function ($attachment_id, $size = 'thumbnail', $icon = FALSE) {
				return $this->twig_ext_get_attachment_image_url($attachment_id, $size = 'thumbnail', $icon = FALSE);
			});
			$this->twig_environment->addFunction($twig_extra_function_get_attachment_image_url);
			
			$twig_extra_filter_update_custom_post = new \Twig\TwigFunction('wp_update_custom_post', function ($postid, $titel="", $name="", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $parentPageId="", $debug=FALSE) {
				return $this->twig_ext_update_custom_post($postid, $titel, $name, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $parentPageId, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_update_custom_post);

			$twig_extra_filter_delete_custom_post = new \Twig\TwigFunction('wp_delete_custom_post', function ($postid, $force_delete = FALSE, $debug=FALSE) {
				return $this->twig_ext_delete_custom_post($postid, $force_delete, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_delete_custom_post);
			
			$twig_extra_function_set_featured_image = new \Twig\TwigFunction('wp_set_featured_image', function ($postid, $thumbnail_id) {
				return $this->twig_ext_set_featured_image($postid, $thumbnail_id);
			});
			$this->twig_environment->addFunction($twig_extra_function_set_featured_image);
			
			$twig_extra_function_get_featured_image = new \Twig\TwigFunction('wp_get_featured_image', function ($postid='', $size='post-thumbnail', $attr = '') {
				return $this->twig_ext_get_featured_image($postid, $size, $attr);
			});
			$this->twig_environment->addFunction($twig_extra_function_get_featured_image);
			
			$twig_extra_filter_get_data_of_uploaded_file = new \Twig\TwigFunction('get_data_of_uploaded_file', function ($filename) {
				return $this->twig_ext_get_data_of_uploaded_file($filename);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_data_of_uploaded_file);

			$twig_extra_filter_get_file = new \Twig\TwigFunction('get_file', function ($filename) {
				return $this->twig_ext_get_file($filename);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_file);

			$twig_extra_function_base64encode_savefile = new \Twig\TwigFunction('base64encode_savefile', function ($filename) {
				return $this->twig_ext_base64encode_savefile($filename);
			});
			$this->twig_environment->addFunction($twig_extra_function_base64encode_savefile);

			$twig_extra_function_db_select = new \Twig\TwigFunction('jci_db_select', function ($db_fields, $db_table_name, $db_where="", $is_wp_db=1) {   # SELECT $db_fields FROM $db_table_name WHERE $db_where
				return $this->twig_ext_db_select($db_fields, $db_table_name, $db_where, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_select);
			
			$twig_extra_function_db_insert = new \Twig\TwigFunction('jci_db_insert', function ($db_table_name, $json_for_db, $is_wp_db=1) {   # INSERT INTO $db_table_name ('key1', 'key2',... ) VALUES ('val1', 'val2',...)
				return $this->twig_ext_db_insert($db_table_name, $json_for_db, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_insert);

			$twig_extra_function_db_delete = new \Twig\TwigFunction('jci_db_delete', function ($db_table_name, $db_where="", $is_wp_db=1) {   # DELETE FROM $db_table_name WHERE $db_where
				return $this->twig_ext_db_delete($db_table_name, $db_where, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_delete);

			$twig_extra_function_db_update = new \Twig\TwigFunction('jci_db_update', function ($db_table_name, $json_for_db, $db_where="", $is_wp_db=1) {   # DELETE FROM $db_table_name WHERE $db_where
				return $this->twig_ext_db_update($db_table_name, $json_for_db, $db_where, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_update);

			$twig_extra_function_db_create = new \Twig\TwigFunction('jci_db_create', function ($db_table_name, $json_for_db, $is_wp_db=1) {   # CREATE TABLE table_name (    column2 datatype,    column3 datatype,   ....); 
				return $this->twig_ext_db_create($db_table_name, $json_for_db, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_create);

			$twig_extra_function_user_create = new \Twig\TwigFunction('jci_user_create', function ($username, $password, $email="", $role="") { 
				return $this->twig_extra_function_user_create($username, $password, $email, $role);
			});
			$this->twig_environment->addFunction($twig_extra_function_user_create);

			$twig_extra_function_woocommmerce_calc_authorization = new \Twig\TwigFunction('jci_woo_calc_auth', function ($consumer_key, $consumer_secret, $request_uri, $http_method) { 
				return $this->twig_extra_function_woocommmerce_calc_authorization($consumer_key, $consumer_secret, $request_uri, $http_method);
			});
			$this->twig_environment->addFunction($twig_extra_function_woocommmerce_calc_authorization);

			$twig_extra_function_jci_setcookie = new \Twig\TwigFunction('jci_setcookie', function ($name, $value="", $expires=0, $path="", $domain="", $secure=false, $httponly=false) { 
				return $this->twig_extra_function_jci_setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_setcookie);
			$twig_extra_function_jci_getcookie = new \Twig\TwigFunction('jci_getcookie', function ($name="") { 
				return $this->twig_extra_function_jci_getcookie($name);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_getcookie);
			$twig_extra_function_jci_forward = new \Twig\TwigFunction('jci_forward', function ($url) { 
				return $this->twig_extra_function_jci_forward($url);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_forward);

		} else if ($this->isTwig332adj) {
			$twig_extra_filter_sortbyjsonfield = new \JCITwig\TwigFilter('sortbyjsonfield', function ($data, $sortdata) {
				return $this->twig_ext_sortbyjsonfield($data, $sortdata);
			});
			$twig_extra_filter_dateformat = new \JCITwig\TwigFilter('dateformat', function ($data, $dateformatstr, $datetimezone, $datelocale) {
				return $this->twig_ext_dateformat($data, $dateformatstr, $datetimezone, $datelocale);
			});
			$twig_extra_filter_converthex2ascii = new \JCITwig\TwigFilter('converthex2ascii', function ($data) {
				return $this->twig_ext_converthex2ascii($data);
			});
			$twig_extra_filter_convert2html = new \JCITwig\TwigFilter('convert2html', function ($data) {
				return $this->twig_ext_convert2html($data);
			});
			$twig_extra_filter_dump = new \JCITwig\TwigFilter('dump', function ($data) {
				return $this->twig_ext_dump($data);
			});
			$twig_extra_filter_md5 = new \JCITwig\TwigFilter('md5', function ($data) {
				return $this->twig_ext_md5($data);
			});
			$twig_extra_filter_preg_replace = new \JCITwig\TwigFilter('preg_replace', function ($data, $pattern, $replacement) {
				return $this->twig_ext_preg_replace($data, $pattern, $replacement);
			});
			$twig_extra_filter_preg_match_all = new \JCITwig\TwigFilter('preg_match_all', function ($data, $pattern, $flags = 0, $offset=0) {
				return $this->twig_ext_preg_match_all($data, $pattern, $flags, $offset);
			});
			$twig_extra_filter_preg_quote = new \JCITwig\TwigFilter('preg_quote', function ($data, $delimiter=NULL) {
				return $this->twig_ext_preg_quote($data, $delimiter);
			});
			
			$twig_extra_filter_sortbyarray = new \JCITwig\TwigFilter('sortbyarray', function ($data, $sorttype, $sortflag=SORT_REGULAR) {
				return $this->twig_ext_sortbyarray($data, $sorttype, $sortflag);
			});
			$twig_extra_filter_htmlentitydecode = new \JCITwig\TwigFilter('html_entity_decode', function ($data, $flags="", $encoding="") {
				return $this->twig_ext_html_entity_decode($data, $flags, $encoding);
			});
			
			$twig_extra_filter_flush = new \JCITwig\TwigFilter('flush', function ($data, $sleep=0) {
				return $this->twig_ext_flush($data, $sleep);
			});
			
			$twig_extra_filter_removenonprintable = new \JCITwig\TwigFilter('removenonprintable', function ($data, $replacement) {
				return $this->twig_ext_removenonprintable($data, $replacement);
			});
			$twig_extra_filter_htmlentities = new \JCITwig\TwigFilter('htmlentities', function ($data, $flags=ENT_QUOTES , $encoding=null, $double_encode=TRUE) {
				return $this->twig_ext_htmlentities($data, $flags, $encoding, $double_encode);
			});
			$twig_extra_filter_htmlspecialchars_decode = new \JCITwig\TwigFilter('htmlspecialchars_decode', function ($data) {
				return $this->twig_ext_htmlspecialchars_decode($data);
			});
			$twig_extra_filter_base64encode = new \JCITwig\TwigFilter('base64encode', function ($data) {
				return $this->twig_ext_base64encode($data);        
			});
			$twig_extra_filter_base64decode = new \JCITwig\TwigFilter('base64decode', function ($data) {
				return $this->twig_ext_base64decode($data);        
			});
			$twig_extra_filter_removespecialcharsinurl  = new \JCITwig\TwigFilter('removespecialcharsinurl', function ($data) {
				return $this->twig_ext_removespecialcharsinurl($data);
			});
			$twig_extra_filter_stringshorter  = new \JCITwig\TwigFilter('stringshorter', function ($data, $length, $suffix) {
				return $this->twig_ext_stringshorter($data, $length, $suffix);
			});
			$twig_extra_filter_numberformat  = new \JCITwig\TwigFilter('formatnumber', function ($data, $decimals, $dec_point, $thousands_sep) {
				return $this->twig_ext_formatnumber($data, $decimals, $dec_point, $thousands_sep);
			});
			$twig_extra_filter_doshortcode  = new \JCITwig\TwigFilter('doshortcode', function ($data) {
				return $this->twig_ext_doshortcode($data);
			});
			$twig_extra_filter_jsondecode  = new \JCITwig\TwigFilter('json_decode', function ($data, $assoc=FALSE, $depth=512, $options=0) {
				return $this->twig_ext_jsondecode($data, $assoc, $depth, $options);
			});
			$twig_extra_filter_jsondecode4twig  = new \JCITwig\TwigFilter('json_decode_4twig', function ($data, $assoc=FALSE, $depth=512, $options=0) {
				return $this->twig_ext_jsondecode4twig($data, $assoc, $depth, $options);
			});

			$twig_extra_filter_mediastore = new \JCITwig\TwigFilter('mediastore', function ($data, $parentPageId="", $mediatitle="", $postslug= "", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $sourcetype="", 
				$withpath=TRUE, $removeqm=FALSE, $generate_thumbnails=TRUE, $checkLibraryOnMediafile=TRUE, $loadContext="", $addfileformatname="") {
				return $this->twig_ext_mediastore($data, $parentPageId, $mediatitle, $postslug, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $sourcetype, $withpath, $removeqm, $generate_thumbnails, $checkLibraryOnMediafile, $loadContext, $addfileformatname);
			});
			
			$twig_extra_filter_wp_mediastore = new \JCITwig\TwigFunction('wp_mediastore', function ($data, $parentPageId="", $mediatitle="", $postslug= "", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $sourcetype="", 
				$withpath=TRUE, $removeqm=FALSE, $generate_thumbnails=TRUE, $loadContext="", $addfileformatname="") {
				return $this->twig_ext_mediastore($data, $parentPageId, $mediatitle, $postslug, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $sourcetype, $withpath, $removeqm, $generate_thumbnails, FALSE, $loadContext, $addfileformatname);
			});
			$this->twig_environment->addFunction($twig_extra_filter_wp_mediastore);
			
			$twig_extra_filter_mediafilename = new \JCITwig\TwigFunction('wp_mediafilename', function ($data, $withpath=TRUE, $removeqm=FALSE, $sourcetype="", $addfileformatname="") {
				return $this->twig_ext_mediafilename($data, $withpath, $removeqm, $sourcetype, $addfileformatname);
			});
			$this->twig_environment->addFunction($twig_extra_filter_mediafilename);

			$twig_extra_filter_urldecode = new \JCITwig\TwigFilter('urldecode', function ($data) {
				return $this->twig_ext_urldecode($data);
			});
			$twig_extra_filter_stripslashes = new \JCITwig\TwigFilter('stripslashes', function ($data) {
				return $this->twig_ext_stripslashes($data);
			});

			# twig 2,3 only BEGIN
			$twig_extra_filter_insert_taxonomy = new \JCITwig\TwigFunction('wp_insert_taxonomy', function ($pageid, $taxonomySlug, $taxonomyValue, $debug=FALSE) {
				return $this->twig_ext_insert_taxonomy($pageid, $taxonomySlug, $taxonomyValue, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_insert_taxonomy);

			$twig_extra_filter_medialist = new \JCITwig\TwigFunction('wp_medialist', function ($postid= "", $pattern="") {
				return $this->twig_ext_medialist($postid, $pattern);
			});
			$this->twig_environment->addFunction($twig_extra_filter_medialist);

			$twig_extra_filter_clear_taxonomy = new \JCITwig\TwigFunction('wp_clear_taxonomy', function ($taxonomySlug, $debug=FALSE) {
				return $this->twig_ext_clear_taxonomy($taxonomySlug, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_clear_taxonomy);

			$twig_extra_filter_get_page_properties = new \JCITwig\TwigFunction('wp_get_page_properties', function ($debug=FALSE, $pageid="") {
				return $this->twig_ext_get_page_properties($debug, $pageid);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_page_properties);

			$twig_extra_filter_get_cp_by_cpf_keyvalue = new \JCITwig\TwigFunction('wp_get_cp_by_cpf_keyvalue', function ($post_type="", $key="", $value="", $debug=FALSE) {
				return $this->twig_ext_get_cp_by_cpf_keyvalue($post_type, $key, $value, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_cp_by_cpf_keyvalue);

			$twig_extra_filter_get_custom_field_value = new \JCITwig\TwigFunction('wp_get_custom_field_value', function ($pageid, $key, $debug=FALSE) {
				return $this->twig_ext_get_custom_field_value($pageid, $key, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_custom_field_value);
			
			$twig_extra_filter_insert_custom_field_keyvalue = new \JCITwig\TwigFunction('wp_insert_custom_field_keyvalue', function ($pageid, $key, $value, $debug=FALSE) {
				return $this->twig_ext_create_insert_custom_field_keyvalue($pageid, $key, $value, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_insert_custom_field_keyvalue);

			$twig_extra_filter_create_new_custom_post = new \JCITwig\TwigFunction('wp_create_new_custom_post', function ($posttype, $titel, $name="", $content="", $publishdate="", $postStatusUsed="publish", $authorid="", $debug=FALSE) {
				return $this->twig_ext_create_new_custom_post($posttype, $titel, $name, $content, "", $publishdate, $postStatusUsed, $authorid, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_create_new_custom_post);
			$twig_extra_filter_new_custom_post = new \JCITwig\TwigFunction('wp_new_custom_post', function ($posttype, $titel, $name="", $content="", $excerpt="", $publishdate="", $postStatusUsed="publish", $authorid="", $debug=FALSE) {
				return $this->twig_ext_create_new_custom_post($posttype, $titel, $name, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_new_custom_post);

			$twig_extra_function_get_attachment_image_url = new \JCITwig\TwigFunction('wp_get_attachment_image_url', function ($attachment_id, $size = 'thumbnail', $icon = FALSE) {
				return $this->twig_ext_get_attachment_image_url($attachment_id, $size = 'thumbnail', $icon = FALSE);
			});
			$this->twig_environment->addFunction($twig_extra_function_get_attachment_image_url);
				
			
			$twig_extra_filter_update_custom_post = new \JCITwig\TwigFunction('wp_update_custom_post', function ($postid, $titel="", $name="", $content="", $excerpt="", $publishdate="", $postStatusUsed="", $authorid="", $parentPageId="", $debug=FALSE) {
				return $this->twig_ext_update_custom_post($postid, $titel, $name, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $parentPageId, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_update_custom_post);

			$twig_extra_filter_delete_custom_post = new \JCITwig\TwigFunction('wp_delete_custom_post', function ($postid, $force_delete = FALSE, $debug=FALSE) {
				return $this->twig_ext_delete_custom_post($postid, $force_delete, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_filter_delete_custom_post);

			$twig_extra_function_set_featured_image = new \JCITwig\TwigFunction('wp_set_featured_image', function ($postid, $thumbnail_id) {
				return $this->twig_ext_set_featured_image($postid, $thumbnail_id);
			});
			$this->twig_environment->addFunction($twig_extra_function_set_featured_image);

			$twig_extra_function_get_featured_image = new \JCITwig\TwigFunction('wp_get_featured_image', function ($postid='', $size='post-thumbnail', $attr = '') {
				return $this->twig_ext_get_featured_image($postid, $size, $attr);
			});
			$this->twig_environment->addFunction($twig_extra_function_get_featured_image);


			$twig_extra_filter_get_data_of_uploaded_file = new \JCITwig\TwigFunction('get_data_of_uploaded_file', function ($filename) {
				return $this->twig_ext_get_data_of_uploaded_file($filename);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_data_of_uploaded_file);

			$twig_extra_filter_get_file = new \JCITwig\TwigFunction('get_file', function ($filename) {
				return $this->twig_ext_get_file($filename);
			});
			$this->twig_environment->addFunction($twig_extra_filter_get_file);

			$twig_extra_function_base64encode_savefile = new \JCITwig\TwigFunction('base64encode_savefile', function ($filename) {
				return $this->twig_ext_base64encode_savefile($filename);
			});
			$this->twig_environment->addFunction($twig_extra_function_base64encode_savefile);

			$twig_extra_function_db_select = new \JCITwig\TwigFunction('jci_db_select', function ($db_fields, $db_table_name, $db_where="", $is_wp_db=1) {   # SELECT $db_fields FROM $db_table_name WHERE $db_where
				return $this->twig_ext_db_select($db_fields, $db_table_name, $db_where, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_select);
			
			$twig_extra_function_db_insert = new \JCITwig\TwigFunction('jci_db_insert', function ($db_table_name, $json_for_db, $is_wp_db=1) {   # INSERT INTO $db_table_name ('key1', 'key2',... ) VALUES ('val1', 'val2',...)
				return $this->twig_ext_db_insert($db_table_name, $json_for_db, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_insert);

			$twig_extra_function_db_delete = new \JCITwig\TwigFunction('jci_db_delete', function ($db_table_name, $db_where="", $is_wp_db=1) {   # DELETE FROM $db_table_name WHERE $db_where
				return $this->twig_ext_db_delete($db_table_name, $db_where, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_delete);

			$twig_extra_function_db_update = new \JCITwig\TwigFunction('jci_db_update', function ($db_table_name, $json_for_db, $db_where="", $is_wp_db=1) {   # DELETE FROM $db_table_name WHERE $db_where
				return $this->twig_ext_db_update($db_table_name, $json_for_db, $db_where, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_update);

			$twig_extra_function_db_create = new \JCITwig\TwigFunction('jci_db_create', function ($db_table_name, $json_for_db, $is_wp_db=1) {   # CREATE TABLE table_name (    column2 datatype,    column3 datatype,   ....); 
				return $this->twig_ext_db_create($db_table_name, $json_for_db, $is_wp_db);
			});
			$this->twig_environment->addFunction($twig_extra_function_db_create);

			$twig_extra_function_user_create = new \JCITwig\TwigFunction('jci_user_create', function ($username, $password, $email="", $role="") { 
				return $this->twig_extra_function_user_create($username, $password, $email, $role);
			});
			$this->twig_environment->addFunction($twig_extra_function_user_create);

			$twig_extra_function_woocommmerce_calc_authorization = new \JCITwig\TwigFunction('jci_woo_calc_auth', function ($consumer_key, $consumer_secret, $request_uri, $http_method) { 
				return $this->twig_extra_function_woocommmerce_calc_authorization($consumer_key, $consumer_secret, $request_uri, $http_method);
			});
			$this->twig_environment->addFunction($twig_extra_function_woocommmerce_calc_authorization);

			$twig_extra_function_jci_setcookie = new \JCITwig\TwigFunction('jci_setcookie', function ($name, $value="", $expires=0, $path="", $domain="", $secure=false, $httponly=false) { 
				return $this->twig_extra_function_jci_setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_setcookie);
			$twig_extra_function_jci_getcookie = new \JCITwig\TwigFunction('jci_getcookie', function ($name="") { 
				return $this->twig_extra_function_jci_getcookie($name);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_getcookie);

			$twig_extra_function_jci_forward = new \JCITwig\TwigFunction('jci_forward', function ($url) { 
				return $this->twig_extra_function_jci_forward($url);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_forward);

			$twig_extra_function_jci_wpml_set_element_language_details = new \JCITwig\TwigFunction('jci_wpml_set_element_language_details', function ($pageid_original, $page_id_translation, $cpt="post", $set_langcode_original="", $set_langcode_translation="", $docheck=FALSE, $debug=FALSE) {   # https://wpml.org/wpml-hook/wpml_set_element_language_details/
				return $this->twig_extra_function_jci_wpml_set_element_language_details($pageid_original, $page_id_translation, $cpt, $set_langcode_original, $set_langcode_translation, $docheck, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_wpml_set_element_language_details);

			$twig_extra_function_jci_wpml_element_language_details = new \JCITwig\TwigFunction('jci_wpml_element_language_details', function ($pageid, $cpt="post", $debug=FALSE) {   # https://wpml.org/wpml-hook/wpml_element_language_details/
				return $this->twig_extra_function_jci_wpml_element_language_details($pageid, $cpt, $debug);
			});
			$this->twig_environment->addFunction($twig_extra_function_jci_wpml_element_language_details);

			$twig_extra_function_wp_set_post_categories = new \JCITwig\TwigFunction('wp_set_post_categories', function ($postid, $post_categories, $append = false) {
				return "A";#$this->twig_extra_function_wp_set_post_categories($postid, $post_categories, $append);
			});
			$this->twig_environment->addFunction($twig_extra_function_wp_set_post_categories);

			# twig 2,3 only END
		} else if (!$this->isJCIParser) {
			$twig_extra_filter_sortbyjsonfield = new Twig_SimpleFilter('sortbyjsonfield', function ($data, $sortdata) {
				return $this->twig_ext_sortbyjsonfield($data, $sortdata);
			});
			$twig_extra_filter_dateformat = new Twig_SimpleFilter('dateformat', function ($data, $dateformatstr, $datetimezone, $datelocale) {
				return $this->twig_ext_dateformat($data, $dateformatstr, $datetimezone, $datelocale);
			});
			$twig_extra_filter_converthex2ascii = new Twig_SimpleFilter('converthex2ascii', function ($data) {
				return $this->twig_ext_converthex2ascii($data);
			});
			$twig_extra_filter_convert2html = new Twig_SimpleFilter('convert2html', function ($data) {
				return $this->twig_ext_convert2html($data);
			});
			$twig_extra_filter_dump = new Twig_SimpleFilter('dump', function ($data) {
				return $this->twig_ext_dump($data);
			});
			$twig_extra_filter_md5 = new Twig_SimpleFilter('md5', function ($data) {
				return $this->twig_ext_md5($data);
			});
			$twig_extra_filter_preg_replace = new Twig_SimpleFilter('preg_replace', function ($data, $pattern, $replacement) {
				return $this->twig_ext_preg_replace($data, $pattern, $replacement);
			});
			$twig_extra_filter_preg_match_all = new Twig_SimpleFilter('preg_match_all', function ($data, $pattern, $flags = 0, $offset=0) {
				return $this->twig_ext_preg_match_all($data, $pattern, $flags, $offset);
			});
			$twig_extra_filter_preg_quote = new Twig_SimpleFilter('preg_quote', function ($data, $delimiter=NULL) {
				return $this->twig_ext_preg_quote($data, $delimiter);
			});
			
			
			$twig_extra_filter_sortbyarray = new Twig_SimpleFilter('sortbyarray', function ($data, $sorttype, $sortflag=SORT_REGULAR) {
				return $this->twig_ext_sortbyarray($data, $sorttype, $sortflag);
			});
			$twig_extra_filter_htmlentitydecode = new Twig_SimpleFilter('html_entity_decode', function ($data, $flags="", $encoding="") {
				return $this->twig_ext_html_entity_decode($data, $flags, $encoding);
			});
			
			$twig_extra_filter_flush = new Twig_SimpleFilter('flush', function ($data, $sleep=0) {
				return $this->twig_ext_flush($data, $sleep);
			});
			
			$twig_extra_filter_removenonprintable = new Twig_SimpleFilter('removenonprintable', function ($data, $replacement) {
				return $this->twig_ext_removenonprintable($data, $replacement);
			});
			$twig_extra_filter_htmlentities = new Twig_SimpleFilter('htmlentities', function ($data, $flags=ENT_QUOTES , $encoding=null, $double_encode=true) {
				return $this->twig_ext_htmlentities($data, $flags, $encoding, $double_encode);
			});
			$twig_extra_filter_htmlspecialchars_decode = new Twig_SimpleFilter('htmlspecialchars_decode', function ($data) {
				return $this->twig_ext_htmlspecialchars_decode($data);
			});
			$twig_extra_filter_base64encode = new Twig_SimpleFilter('base64encode', function ($data) {
				return $this->twig_ext_base64encode($data);        
			});
			$twig_extra_filter_base64decode = new Twig_SimpleFilter('base64decode', function ($data) {
				return $this->twig_ext_base64decode($data);        
			});
			$twig_extra_filter_removespecialcharsinurl  = new Twig_SimpleFilter('removespecialcharsinurl', function ($data) {
				return $this->twig_ext_removespecialcharsinurl($data);
			});
			$twig_extra_filter_stringshorter  = new Twig_SimpleFilter('stringshorter', function ($data, $length, $suffix) {
				return $this->twig_ext_stringshorter($data, $length, $suffix);
			});
			$twig_extra_filter_numberformat  = new Twig_SimpleFilter('formatnumber', function ($data, $decimals, $dec_point, $thousands_sep) {
				return $this->twig_ext_formatnumber($data, $decimals, $dec_point, $thousands_sep);
			});
			$twig_extra_filter_doshortcode  = new Twig_SimpleFilter('doshortcode', function ($data) {
				return $this->twig_ext_doshortcode($data);
			});
			$twig_extra_filter_jsondecode  = new Twig_SimpleFilter('json_decode', function ($data, $assoc=FALSE, $depth=512, $options=0) {
				return $this->twig_ext_jsondecode($data, $assoc, $depth, $options);
			});
			$twig_extra_filter_jsondecode4twig = new Twig_SimpleFilter('json_decode_4twig', function ($data, $assoc=FALSE, $depth=512, $options=0) {
				return $this->twig_ext_jsondecode4twig($data, $assoc, $depth, $options);
			});

			$twig_extra_filter_mediastore = new Twig_SimpleFilter('mediastore', function ($data, $parentPageId="", $mediatitle="", $postslug= "", $content="", $excerpt="", 
				$publishdate="", $postStatusUsed="", $authorid="", $sourcetype="", $withpath=TRUE, $removeqm=FALSE, $generate_thumbnails=TRUE, $checkLibraryOnMediafile=TRUE, $loadContext="", $addfileformatname="") {
				return $this->twig_ext_mediastore($data, $parentPageId, $mediatitle, $postslug, $content, $excerpt, $publishdate, $postStatusUsed, $authorid, $sourcetype, $withpath, $removeqm, $generate_thumbnails, $checkLibraryOnMediafile, $loadContext, $addfileformatname);
			});
			#$twig_extra_filter_mediafilename = new Twig_SimpleFilter('mediafilename', function ($data, $withpath=TRUE, $removeqm=FALSE, $sourcetype="") {
			#	return $this->twig_ext_mediafilename($data, $withpath=TRUE, $removeqm=FALSE, $sourcetype="");
			#});

			$twig_extra_filter_urldecode = new Twig_SimpleFilter('urldecode', function ($data) {
				return $this->twig_ext_urldecode($data);
			});
			$twig_extra_filter_stripslashes = new Twig_SimpleFilter('stripslashes', function ($data) {
				return $this->twig_ext_stripslashes($data);
			});
		}

		if (!$this->isJCIParser) {
			$this->twig_environment->addFilter($twig_extra_filter_sortbyjsonfield);
			$this->twig_environment->addFilter($twig_extra_filter_dateformat);
			$this->twig_environment->addFilter($twig_extra_filter_convert2html);
			$this->twig_environment->addFilter($twig_extra_filter_htmlspecialchars_decode);
			$this->twig_environment->addFilter($twig_extra_filter_base64encode);
			$this->twig_environment->addFilter($twig_extra_filter_base64decode);
			$this->twig_environment->addFilter($twig_extra_filter_removespecialcharsinurl);
			$this->twig_environment->addFilter($twig_extra_filter_stringshorter);
			$this->twig_environment->addFilter($twig_extra_filter_numberformat);
			$this->twig_environment->addFilter($twig_extra_filter_converthex2ascii);
			$this->twig_environment->addFilter($twig_extra_filter_dump);
			$this->twig_environment->addFilter($twig_extra_filter_md5);
			$this->twig_environment->addFilter($twig_extra_filter_preg_replace);
			$this->twig_environment->addFilter($twig_extra_filter_preg_match_all);
			$this->twig_environment->addFilter($twig_extra_filter_preg_quote);
			$this->twig_environment->addFilter($twig_extra_filter_sortbyarray);
			$this->twig_environment->addFilter($twig_extra_filter_htmlentitydecode);
			$this->twig_environment->addFilter($twig_extra_filter_flush);
			$this->twig_environment->addFilter($twig_extra_filter_htmlentities);
			$this->twig_environment->addFilter($twig_extra_filter_removenonprintable);
			$this->twig_environment->addFilter($twig_extra_filter_doshortcode);
			$this->twig_environment->addFilter($twig_extra_filter_jsondecode);
			$this->twig_environment->addFilter($twig_extra_filter_jsondecode4twig);
			$this->twig_environment->addFilter($twig_extra_filter_mediastore);
			#$this->twig_environment->addFilter($twig_extra_filter_mediafilename);
			$this->twig_environment->addFilter($twig_extra_filter_urldecode); 
			$this->twig_environment->addFilter($twig_extra_filter_stripslashes); 

		}
	}
	
    private function sortJsonArray($arrayIn, $sortField, $sortOrder, $sortFlag) {
      @usort($arrayIn, function ($a, $b) use ($sortField, $sortOrder, $sortFlag) {
        $eval = NULL;
        for ($i=1;$i<=count($sortField);$i++) {
          if ($sortField[$i]=="") {
            continue;
          }
          $valA = $a[$sortField[$i]];
          $valB = $b[$sortField[$i]];
			if (preg_match("/^date/", trim($sortFlag[$i]))) {
				# sortbyjsonfield("datumString,desc,date#us#-") where - is the separator used in JSON, needed for us is /
				# sortbyjsonfield("datumString,desc,date#eu#/") where / is the separator used in JSON, needed for eu is -
				# see note at https://www.php.net/manual/en/function.strtotime.php
				# d-m-Y vs m/d/Y
				$sortdateArr = explode("#", $sortFlag[$i], 3);
				$dateformatinjson = $sortdateArr[1];
				$newdateseparatorinjson = "/"; # default us
				if ("eu"==$dateformatinjson) {
					$newdateseparatorinjson = "-"; 
				}
				$dateseparatorinjson = $sortdateArr[2];
				if (""!=$dateformatinjson && ""!=$dateseparatorinjson) {
					$dateArrAWithTime = explode(" ", $valA, 2);
					$dateArrA = explode($dateseparatorinjson, $dateArrAWithTime[0]);
					$valA = join($newdateseparatorinjson, $dateArrA);
					if (""!=$dateArrAWithTime[1]) {
						$valA .= " ".$dateArrAWithTime[1];
					}
					$dateArrBWithTime = explode(" ", $valB, 2);
					$dateArrB = explode($dateseparatorinjson, $dateArrBWithTime[0]);
					$valB = join($newdateseparatorinjson, $dateArrB);
					if (""!=$dateArrBWithTime[1]) {
						$valB .= " ".$dateArrBWithTime[1];
					}
				}
				
				$valAtmp = strtotime($valA);
				if ($valAtmp) {
					$valA = $valAtmp;
				}
				$valBtmp = strtotime($valB);
				if ($valBtmp) {
					$valB = $valBtmp;
				}
			}

			$sr = $sortField[$i];
		  
		  
          if (preg_match("/\./", $sr)) {
            $tmpA = $a;
            $tmpB = $b;
            $srArr = explode(".", $sr);
            for ($ji=0;$ji<count($srArr);$ji++) {
              $tmpA = $tmpA[$srArr[$ji]];
              $tmpB = $tmpB[$srArr[$ji]];
            }
            $valA = $tmpA;
            $valB = $tmpB;
          }

          $this->jci_collectDebugMessage("sorting $sr, order: ".$sortOrder[$i].": ".$valA." vs. ".$valB);
			if ($sortFlag[$i]=="num" && is_numeric($valA) && is_numeric($valB)) {
				if ($sortOrder[$i]=="desc") {
					$tmp =$valA;
					$valA= $valB;
					$valB = $tmp;
				}
				if ($valA == $valB) {
					return 0;
				}
				return ($valA < $valB) ? -1 : 1;
			} else if ($sortFlag[$i]=="date" && is_numeric($valA) && is_numeric($valB)) {
				if ($sortOrder[$i]=="desc") {
					$eval = $valB-$valA;
				} else {
					$eval = $valA-$valB;
				}
			} else {
				$d = strtolower($valB);
				$t = strtolower($valA);
				if ($sortOrder[$i]=="desc") {
					$eval = strcmp($d,$t);
				} else {
					$eval = strcmp($t,$d);
				}
			}
        }
        return $eval;
      });
      return $arrayIn;
    }	
}



?>
