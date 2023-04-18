<?php
/*
CLASS JsonContentImporterPro
Description: Class for WP-plugin "JSON Content Importer Pro"
Version: 202102042249
Author: Bernhard Kux
Author URI: https://json-content-importer.com
*/

class JsonContentImporterPro {

    /* shortcode-params */
    private $numberofdisplayeditems = -1; # -1: show all
    private $feedUrl = Array(); # url of JSON-Feed
    private $urlparam = Array(); # dyn URL
    private $pathparam = Array(); # dyn PATH WARRNING : must be written in the order we wish: dir1#dirA#file -> /valuedir1/valuedirA/valuefile
    private $fileext = Array(); # extention of the file that finish the dyn Path
    private $urlgettimeout = Array(); 
    private $basenode = ""; # where in the JSON-Feed is the data?
    private $hidedisplayflag = FALSE; # display only if something is fullfilled: if TRUE show nothing
    private $loopWithoutSubloop = "";
    private $oneofthesewordsmustbein = ""; # optional: one of these ","-separated words have to be in the created html-code
    private $oneofthesewordsmustbeindepth = 1; # optional: json-tree-depth for $oneofthesewordsmustbein
    private $requiredfieldsandvalues = ""; # optional: if set only the "#"-separated list of "key=value" pairs are parsed, others are ignored
    private $requiredfieldsandvaluesdepth = 1; # optional:  json-tree-depth for $requiredfieldsandvalues
    private $delimiter = "##";

    private $sortField = "";
    private $sortorderIsUp = FALSE;
    private $sorttypeIsNatural = FALSE;
    private $filterresultsin = "";
    private $filterresultsnotin = "";

    private $templateid = "";
    private $nameoftemplate = "";
    private $jsonuseset = "";

    private $nestedlevel = 0; # initial value

    private $requiredfieldsandvalueslogicandbetweentwofields = FALSE;
      # if true: several fields in requiredFieldsAndValues: all must match
      # if false: several fields in requiredFieldsAndValues: one of it must match

    private $oneofthesewordsmustnotbeIn = ""; # optional: one of these ","-separated words must NOT in the created html-code
    private $oneofthesewordsmustnotbeindepth = 1; # optional:  json-tree-depth for $oneofthesewordsmustnotbeIn

    /* plugin settings */
    private $isCacheEnable = FALSE;

    /* internal */
	private $cacheFile = "";
	private $jsondata = "";
    private $feedsource = Array(); # default: http" - get json via http, file, ftp..., default: http, then empty
    private $feedfilename = Array();
	private $feedData  = "";
	private $feedDataArr  = Array();
 	#private $cacheFolder = "";
    #private $cacheBaseFolder = "";
    private $datastructure = "";
    private $triggerUnique = NULL;
    private $cacheExpireTime = 0;
    private $param1 = Array();
    private $param2 = Array();
    private $licencelevel = "-12";
    private $debugModeIsOn = Array();
    private $debugLevel = Array();
    private $debugMessage = "";
    private $method = Array();
    private $urlparamval = Array();
    private $postPayload = Array();
    private $postbody = Array();
    private $urlencodepostpayload = '';

    private $customfieldparam = Array();
    private $header = Array();
    private $auth = Array();
    private $inputtype = Array(); # "json";
    private $inputtypeparamArr = Array();  # "";
    private $urlgetaddrandom = Array();
    private $trytohealjson = Array();
    private $cachetime = Array();
    private $showapiresponse = Array();
	private $seljs = "";
	
    private $parser = Array();

    private $mode = Array();
    private $pageid = '';
    private $createoptionsArr = Array();
    private $convertJsonNumbers2Strings = Array();
    private $removeampfromurl = Array();
    private $curloptions = Array();
    private $urladdparam = "";
    private $urlparam4twig = Array();
	private $postdateoffset = 0;
	private $forceTemplate = FALSE;
	private $taxonomiesArr = NULL;
	private $encodingofsource = Array();
	private $secret = Array();
	private $fastdeletecp = FALSE;
	
    /* TWIG vars BEGIN */
    #private $twig_environment_settings = NULL;
	private $twig_loader = NULL;
	private $twigHandler = NULL;
	
	#private $twig_environment = NULL;
    #private $isTwig2 = FALSE;
    private $httpstatuscodemustbe200 = Array();
    private $maskspecialcharsinjsonFlag = Array();
    private $displayapireturn = Array();
    #private $errormessagecache = "";
    private $createmessage = "";
    private $addpostdata2json = FALSE;
    private $addcpf2json = FALSE;
	private $upload = FALSE;
    /* TWIG vars END */

	public function __construct(){
		add_action( 'admin_init', array( $this, 'check_licence' ) );
    	add_shortcode('jsoncontentimporterpro' , array(&$this , 'shortcodeExecute')); # hook shortcode
		add_action('wp_head', array(&$this, 'jci_wp_head'), 1 ); #put JSON-data into HTML-Head: CPF with twig-code
		add_filter('pre_get_document_title', array(&$this , 'jci_set_page_title'), 9); #set title
    }

	public function jci_wp_head() { #put JSON-data into HTML-Head
		# get CPF with twig-pattern 
		$cpfvArr = $this->jci_getCPF('jci_pagehead');
		if (isset($cpfvArr[0]) && ""!=$cpfvArr[0]) {
			$jsonres = $cpfvArr[0];
			if (isset($this->twigHandler)) { # title shortcode from jci_setpagetitle needed!! otherwise no json-data and twig...
				$jsonres = $this->twigHandler->executeTwig($this->feedDataArr, $cpfvArr[0], "twig3", FALSE);
			}
			$jsonres = preg_replace('/#LF#/', PHP_EOL, $jsonres);
			if (""!=$jsonres) {
				echo $jsonres.PHP_EOL;
			}
		}
		return TRUE;
	}

	public function jci_set_page_title() { # shortcode!
		$cpfvArr = $this->jci_getCPF('jci_pagetitle');
		if (isset($cpfvArr[0]) && ""!=$cpfvArr[0]) {
			$ti = do_shortcode($cpfvArr[0]);
			return $ti;
		}
	}

	private function jci_getCPF($nameOfCPF) { # get CustomPostField
		global $post;
		if (is_null($post) || is_null($post->ID)) {
			return NULL;
		}
		$cpfvArr = get_post_meta($post->ID, $nameOfCPF);
		return $cpfvArr;
	}

	/* debugging BEGIN */
	private function jci_showDebugMessage() {
		return logDebug::$debugmessage;
	}
    private function buildDebugTextarea($message, $txt, $addline=FALSE) {
		logDebug::jci_buildDebugTextarea($message, @$this->debugModeIsOn[$this->nestedlevel], @$this->debugLevel[$this->nestedlevel], $txt, $addline);
    }
	private function jci_collectDebugMessage($debugMessage, $debugLevel=2, $suffix="", $convert2html=TRUE, $switchoffDebugPrefix = FALSE, $prefix="", $maxlength=400) {
		logDebug::jci_addDebugMessage($debugMessage, @$this->debugModeIsOn[$this->nestedlevel], $debugLevel, $debugLevel, $suffix, $convert2html, $switchoffDebugPrefix, $prefix, $maxlength);
    }
	private function jci_collectCreateMessage($message, $convert2html=FALSE, $maxlength=400) {
		$tmpDebugLevelMode = $this->debugModeIsOn[$this->nestedlevel];
		$this->debugModeIsOn[$this->nestedlevel] = TRUE;
		$tmpDebugLevel = $this->debugLevel[$this->nestedlevel];
		$this->debugLevel[$this->nestedlevel] = 100;
		$this->jci_collectDebugMessage($message, 100, "", $convert2html, TRUE, FALSE, "", $maxlength);
		$this->debugModeIsOn[$this->nestedlevel] = $tmpDebugLevelMode;
		$this->debugLevel[$this->nestedlevel] = $tmpDebugLevel;
	}
	/* debugging END */



    function check_licence() {
        $this->licencelevel = edd_jcipro_check_license("admininit");
        #$this->jci_collectDebugMessage("check licence of plugin");
        if (!($this->licencelevel==-1)) {
            add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
	      }
    }

    function disabled_notice() {
       $this->jci_collectDebugMessage("licence of plugin is not active");
       echo '<div class="error">
	       <p>'.$this->licencelevel.'
	    </div>';
	  }

    # filtering JSON - BEGIN
	  # inspired by George from USA
      function filterJSON($jsonObj, $filterpattern, $filtertype) {
	      #filter the json-array if necessary
	      #first we filter on the results that should "match" the input
        if (!empty($filterpattern)) {
		      $filterpatternArr = explode(",",$filterpattern);
		      foreach($filterpatternArr as $filterpatternArrItem) {
			      if (strpos($filterpatternArrItem, '=') === false) {
				      $filterresultskey = $filterpatternArrItem;
				      $filterresultsvalue = sanitize_text_field($_GET[$filterpatternArrItem]);
              if (empty($filterresultsvalue)) {
  				      $filterresultsvalue = sanitize_text_field($_POST[$filterpatternArrItem]);
              }
			      } else {
				      $parts = explode('=', $filterpatternArrItem, 2);
				      $filterresultskey = $parts[0];
				      $filterresultsvalue = $parts[1];
			      }
			      if(!empty($filterresultsvalue)) {
              # if 1: use exact match, no regular-expression-match / if 2: use regular-expression-match, no exact match
              $val_jci_pro_allow_regexp = get_option('jci_pro_allow_regexp');
              if ($val_jci_pro_allow_regexp=="") {
                $val_jci_pro_allow_regexp = 2;
              }
              foreach ($jsonObj as $elementKey => $element) {
					      foreach($element as $valueKey => $value) {
                  if ($filtertype=="resultin") {
                    if ($val_jci_pro_allow_regexp==1) {
  						        if($valueKey == $filterresultskey && ($filterresultsvalue!=$value)) {
	 	   					        unset($jsonObj[$elementKey]);
	   					        }
                    } else {
  						        if($valueKey == $filterresultskey && (!preg_match("/".$filterresultsvalue."/", $value))) {
	 	   					        unset($jsonObj[$elementKey]);
	   					        }
                    }
                  }
                  if ($filtertype=="resultnotin") {
                    if ($val_jci_pro_allow_regexp==1) {
  						        if($valueKey==$filterresultskey && $filterresultsvalue==$value) {
	   						        unset($jsonObj[$elementKey]);
		  				        }
                    } else {
  						        if($valueKey==$filterresultskey && preg_match("/".$filterresultsvalue."/", $value)) {
	   						        unset($jsonObj[$elementKey]);
		  				        }
                    }
                  }
					      }
				      }
			      }
		      }
          return $jsonObj;
	      }
      }

      # sorting JSON - BEGIN
	    # inspired by George from USA
      function sortfunc($a, $b) {
        $sortfieldTmp = $this->sortField;
        $sorttypeIsNaturalTmp = $this->sorttypeIsNatural;
        $sortorderIsUpTmp = $this->sortorderIsUp;
        # $sortorder_is_up: if TRUE: UP; if FALSE: down
        # $sorttype: if TRUE sort natural, if FALSE standard-sort
        if ($sorttypeIsNaturalTmp) {
          if ($sortorderIsUpTmp) {
            return strnatcmp($b->$sortfieldTmp, $a->$sortfieldTmp);
          } else {
            return strnatcmp($a->$sortfieldTmp, $ba->$sortfieldTmp);
          }
        }
        if ($a->$sortfieldTmp == $b->$sortfieldTmp) {
          return 0;
        }
        if ($sortorderIsUpTmp) {
          return $a->$sortfieldTmp < $b->$sortfieldTmp ? 1 : -1;
        } else {
          return $a->$sortfieldTmp < $b->$sortfieldTmp ? -1 : 1;
        }
      }

    private function removeInvalidQuotes($txtin) {
      $invalid1 = urldecode("%E2%80%9D");
      $invalid2 = urldecode("%E2%80%B3");
      $txtin = preg_replace("/^[".$invalid1."|".$invalid2."]*/i", "", $txtin);
      $txtin = preg_replace("/[".$invalid1."|".$invalid2."]*$/i", "", $txtin);
      return $txtin;
    }
    private function replaceInTwigCodeInvalidQuotesWithValidQuotes($txtin) {
      $invalid1 = urldecode("&#8222;");
      $invalid2 = urldecode("&#8220;");
      $txtin = preg_replace("/{{(.*)".$invalid1."(.*)".$invalid2."(.*)}}/i", '{{'.'${1}'."\"".'${2}'."\"".'${3}'.'}}', $txtin);
      return $txtin;
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
              $eval .= $valB-$valA;
            } else {
              $eval .= $valA-$valB;
            }
          } else {
             $d = strtolower($valB);
             $t = strtolower($valA);
          if ($sortOrder[$i]=="desc") {
              $eval .= strcmp($d,$t);
            } else {
              $eval .= strcmp($t,$d);
            }
          }
        }
        return $eval;
      });
      return $arrayIn;
    }

    private function func_trytohealjson($jsonin) {
       # handle "not-so-perfect-JSON" like https://www.google.com/finance/info?q=NASDAQ%3aGOOG
	   # and: https://delivery.travelsuite.de/offers?cid=909091&campaignSourceCode=antalya-landingpage
	   
      $fdTmp = trim($this->feedData);
      if (
        (!preg_match("/^(\[|\{)/", $fdTmp))
        ||
        (!preg_match("/(\[|\})$/", $fdTmp))
      ) {
		$posFirstCurl = strpos($fdTmp, "{");
		$posFirstSqr = strpos($fdTmp, "[");
		$left = min($posFirstCurl, $posFirstSqr);
		$fdTmp = substr($fdTmp, $left);
		
		$posLastCurl = strrpos($fdTmp, "}");
		$postLastSqr = strrpos($fdTmp, "]");
		$right = max($posLastCurl, $postLastSqr);
		$fdTmp = substr($fdTmp, 0, $right+1);
		
		$inspurl = "https://jsoneditoronline.org";
		$this->buildDebugTextarea("<br>Inspect trytohealjson-JSON: Copypaste (click in box, Strg-A marks all, then insert into clipboard) the JSON from the following box to <a href=\"".$inspurl."\" target=_blank>".$inspurl."</a>):", $fdTmp);
        return $fdTmp;
      }
    }


	private function deleteCPT($typeOfNewpage, $nameofthejsonimport) {
		$this->jci_collectCreateMessage( "<b>DELETE Custom Posts:</b><br>"."typeOfNewpage: $typeOfNewpage<br>nameofthejsonimport: $nameofthejsonimport");
		$go_jci_pro_cp_fastdelete = get_option('jci_pro_cp_fastdelete');
		if (1==$go_jci_pro_cp_fastdelete) {
			$this->fastdeletecp = TRUE;
		}
		$no_of_failed_pages_to_delete = 0;
		$start1 = time();
		$this->jci_collectCreateMessage( "<b>try to delete previous generated pages! key: $nameofthejsonimport</b>");
		if ($this->fastdeletecp) {
			$this->jci_collectCreateMessage( "the 'fast delete switch' is active in the JCI-plugin settings: if there are any unusual things, deactivate it, please");
			global $wpdb;
			$selectcpsq1 = "SELECT DISTINCT a.ID
				FROM ".$wpdb->prefix."posts a
				LEFT JOIN ".$wpdb->prefix."postmeta b ON ( a.ID = b.post_id )
				WHERE 
				a.post_type = '".$typeOfNewpage."'
				AND b.meta_key='jci_uniquekey_cr'
				AND b.meta_value='".$nameofthejsonimport."'
				";
			$idres = $wpdb->get_results($selectcpsq1);
			$listofIDs = "";
			foreach( $idres as $key => $row) {
				$deletecpsql = "DELETE FROM ".$wpdb->prefix."posts 
					WHERE ID=".$row->ID;
				$deletecpsq2 = "DELETE FROM ".$wpdb->prefix."postmeta 
					WHERE post_id=".$row->ID;
				$deletecpsq3 = "DELETE FROM ".$wpdb->prefix."term_relationships 
					WHERE object_id=".$row->ID;
				#echo $deletecpsql."<br>";
				#echo $deletecpsq2."<hr>";
				#echo $deletecpsq3."<hr>";
				$wpdb->query($deletecpsql);
				$wpdb->query($deletecpsq2);
				$wpdb->query($deletecpsq3);
			}

			$this->jci_collectCreateMessage( "fast delete runtime: ".(time()-$start1));
		} else {
			$args = array(
				'post_type'        => $typeOfNewpage,
				'numberposts'	   => -1,
				'meta_key'         => 'jci_uniquekey_cr',
				'meta_value'       => $nameofthejsonimport,
				'post_status' 	=> 'any',
			);		
		
			$allposts = get_posts( $args );
			$this->jci_collectCreateMessage( "found ".count($allposts)." pages of this type: <b>".$typeOfNewpage."</b> - try to delete these pages");
			
			$i = 0;
			foreach ($allposts as $eachpost) {
				$delActionOkTmp = wp_delete_post( $eachpost->ID, true );
				if (!$delActionOkTmp) {
					$this->jci_collectCreateMessage( "delete page ".$eachpost->ID." <b>FAILED</b>");
					$no_of_failed_pages_to_delete++;
				} else {
					#$this->jci_collectCreateMessage( "delete page ".$eachpost->ID." <b>OK</b>");
				}
				$i++;
			}

			$this->jci_collectCreateMessage( "delete runtime: ".(time()-$start1));

			if ($no_of_failed_pages_to_delete==0) {
				if ($i>0) {
					$this->jci_collectCreateMessage( "all ".$i." pages successfully deleted<br>");
				} else {
					$this->jci_collectCreateMessage( "tried to delete prevoius generated Custom Pages, but found none (e. g. initial run)<br>");
				}
			}  else {
				$this->jci_collectCreateMessage( "<hr>deletion of $no_of_failed_pages_to_delete pages of ".$query->found_posts." failed<hr>");
			}  
		}	
	}	


    private function createPage($no, $newPostType, $newPostTitle, $newPostSlugname, $newPostCategory, $content, $jci_uniquekey_createpost, $postStatusIn, $featuredImgURL, $featuredImgAlt, $featuredImgSrc, $postPublishTime="", $postauthorid="") {
		#$this->jci_collectDebugMessage("($no) pageID of creating page: ".$this->pageid);
		/*
		if (""==$this->pageid) {
			return -1;
		}
		$custom_fields_arr = get_post_custom($this->pageid);
		*/
		$nameofthejsonimport = $jci_uniquekey_createpost; #trim($custom_fields_arr['jci_uniquekey_createpost'][0]);
		if ($nameofthejsonimport=="") {
			$this->jci_collectDebugMessage( "($no) No page created: Set CustomField 'jci_uniquekey_createpost'<br>\n" );
			return -1;
		}
		$postdateoffsettmp = "";
		if (isset($this->createoptionsArr[$this->nestedlevel]['postdateoffset'])) {
			$postdateoffsettmp = trim($this->createoptionsArr[$this->nestedlevel]['postdateoffset']);
		}
		$postPublishTimeStamp = time();
		if (!empty($postPublishTime)) {
			# $postPublishTime must be a date-format: convert to timestamp
			$postPublishTimeStamp = strtotime($postPublishTime);
			$this->jci_collectCreateMessage( "($no) postPublishTime: ".$postPublishTime." is timestamp: ".$postPublishTimeStamp);
		}
		
		$postdate = NULL;
		if (is_numeric($postdateoffsettmp)) {
			$this->postdateoffset = $postdateoffsettmp;
			$postdatetimestamp = $postPublishTimeStamp - $this->postdateoffset;
			$postdate = date("Y-m-d H:i:s", $postdatetimestamp);
			$this->jci_collectCreateMessage( "numeric postdateoffset ".$this->postdateoffset.": ".$postdate);
		} else if ("wptimezone" == $postdateoffsettmp) {
			$wptimezone = get_option('timezone_string');
			$errorleveltimezoneset = date_default_timezone_set($wptimezone);
			$postdate = date("Y-m-d H:i:s", $postPublishTimeStamp);
			if ($errorleveltimezoneset) {
				$this->jci_collectCreateMessage( "($no) Use timezone $wptimezone of wordpress ok: ".$postdate);
			} else {
				$this->jci_collectCreateMessage( "($no) Use timezone $wptimezone of wordpress failed: ".$postdate);
			}
		} else if (""!=$postdateoffsettmp) {
			# valid timezone-string? https://www.php.net/manual/de/timezones.php
			$errorleveltimezoneset = date_default_timezone_set($postdateoffsettmp);
			$postdate = date("Y-m-d H:i:s", $postPublishTimeStamp);
			if ($errorleveltimezoneset) {
				$this->jci_collectCreateMessage( "($no) Use timezone $postdateoffsettmp of wordpress ok: ".$postdate);
			} else {
				$this->jci_collectCreateMessage( "($no) Use timezone $postdateoffsettmp of wordpress failed: ".$postdate);
			}
		} else {
			$postdate = date("Y-m-d H:i:s", $postPublishTimeStamp);
			$this->jci_collectCreateMessage( "($no) postdate without considering timezones: ".$postdate);
		}
        $this->jci_collectDebugMessage( "($no) page postdate: ".$postdate);
		
		$postStatusArr = array(
		    'publish' => 1,
			'pending' => 1,
			'draft' => 1,
			'auto-draft' => 1,
			'future' => 1,
			'private'  => 1,
			'inherit' => 1,
			'trash' => 1
		);
		
		$postStatusUsed = "publish";
		if ((!empty($postStatusIn)) && (isset($postStatusArr[$postStatusIn])) && (1==$postStatusArr[$postStatusIn])) {
			$postStatusUsed = $postStatusIn;
		}
        $this->jci_collectDebugMessage( "($no) page poststatus: ".$postStatusUsed);
		
		# executeable Shortcodes in the blueprint must be masked - otherwise the create-loop does not work
		$content = preg_replace("/#BRO#/", "[", $content);
		$content = preg_replace("/#BRC#/", "]", $content);
	
		$newPostArr = array(
			'post_title'   => $newPostTitle,
			'post_name'    => $newPostSlugname,
			'post_content' => $content,
			'post_status'  => $postStatusUsed,
			# 'post_author'   => 1,
			'post_type'    => $newPostType,
			'post_category'=> $newPostCategory,
			'post_date'    => $postdate,
			# 'post_parent'  =>
      );
	  
  		if (!empty($postauthorid) && is_numeric($postauthorid)) {
			$newPostArr['post_author'] = $postauthorid;
			$this->jci_collectCreateMessage( "($no) postauthorid: ".$postauthorid);
		}


      // Insert the post into the database.
      $idOfNewPost = FALSE;
      remove_all_filters("content_save_pre"); # otherwise tags like <script> and <style> are removed when build-url is called as not-logged in....

      $idOfNewPost = wp_insert_post( $newPostArr );
      if ( ! $idOfNewPost ) {
          $this->jci_collectCreateMessage("<b>($no) creating of new post failed</b><br>settings:<br>".print_r($newPostArr, TRUE));
          return -1;
      }
	  
  
	  /* BEGIN: insert document-attachment-connection and local URL to mediafile */
	  if (preg_match('/###jci#(\d+)#jci###/', $content)) {
		$this->jci_collectDebugMessage( "($no) Load and use Mediafiles");
		preg_match_all('/###jci#(\d+)#jci###/', $content, $matches, PREG_PATTERN_ORDER);
		$updataArr = array();
		foreach ($matches[1] as $key) {
			$updataArr['ID'] = $key;
			$updataArr['post_parent'] = $idOfNewPost;
			$update_attachment_id = wp_update_post($updataArr);
			$link2mediafile = wp_get_attachment_url($key);
			$content = preg_replace('/###jci#'.$key.'#jci###/', $link2mediafile, $content);
			$this->jci_collectDebugMessage( "($no) Mediafile: $key at $link2mediafile");
		}
		$post = get_post( $idOfNewPost );
		$post->post_content = $content;
		wp_update_post( $post );
	  }
	  /* END: insert document-attachment-connection and local URL to mediafile */
	  
      $this->jci_collectDebugMessage( "($no) Publishing date / time of page (this is the real server time...): ".$postdate);

      $this->jci_collectDebugMessage( "($no) creating of new post ok, id=$idOfNewPost" );
      /*  customfields are added after the pagecreation: and the twig code is executed then...
      $cf = @$this->createoptionsArr[$this->nestedlevel]['customfields'];
      if (empty($cf)) {
        echo "($no)".' no extra customfields in shortcode defined. Example: "customfields": #BRO# {"extracustomfield1":"extravalue1"}, {"extracustomfield2":"extravalue2"}#BRC#}'."<br>";
      } else {
        echo "($no) add custom fields:<br>";
        for ($j=0; $j<count($this->createoptionsArr[$this->nestedlevel]{'customfields'});$j++) {
          foreach ($this->createoptionsArr[$this->nestedlevel]{'customfields'}[$j] as $key => $value) {
            add_post_meta($idOfNewPost, $key, $value, true);
            echo "($no) add custom field value from shortcode: $key : $value<br>";
            $this->jci_collectDebugMessage("add custompost-param to $idOfNewPost:  $key : $value");
          }
        }
      }
      */
	  
	  ### featured images: store remote url in custom post field
		if ( ! empty( $featuredImgURL ) && ""!=$this->check_if_url_is_image( $featuredImgURL ) ) {
			$fis1 = update_post_meta($idOfNewPost, 'featuredimagebyurl', $featuredImgURL, true);
			$fis2 = update_post_meta($idOfNewPost, '_thumbnail_id', "by_url" );
			if ($fis1 && $fis2) {
				$this->jci_collectCreateMessage( "($no) featuredImgURL successfully stored in Custom Fields featuredimagebyurl ($featuredImgURL) and _thumbnail_id for Page $idOfNewPost");
			} else {
				$this->jci_collectCreateMessage( "($no) saving featuredImgURL failed! saving cpf featuredimagebyurl: $fis1 - saving cpf _thumbnail_id: $fis2");
			}
			if (!empty($featuredImgAlt)) {
				$fis1 = update_post_meta($idOfNewPost, 'featuredimagealt', $featuredImgAlt, true);
				$this->jci_collectCreateMessage( "($no) featuredImg Alt-Text: $featuredImgAlt");
			}
			if (!empty($featuredImgSrc)) {
				$fis1 = update_post_meta($idOfNewPost, 'featuredimagesrc', $featuredImgSrc, true);
				$this->jci_collectCreateMessage( "($no) featuredImg src: ".htmlentities($featuredImgSrc, TRUE));
			}		
	  }
	  
      if ($nameofthejsonimport!="") {
        add_post_meta($idOfNewPost, 'jci_uniquekey_cr', $nameofthejsonimport, true);
        $this->jci_collectDebugMessage( "($no) add custom field for delete: jci_uniquekey_cr : $nameofthejsonimport" );
      }
      return $idOfNewPost;
    }
		
	private function check_if_url_is_image( $url ) {
			
		$url = strip_tags(trim($url)); 
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return "";
		}
		$ext = array( 'jpeg', 'jpg', 'gif', 'png' );
		$urlArrWithoutGetParam = explode("?", $url);
		$urlArr = explode(".", $urlArrWithoutGetParam[0]);
		$urlExt = strtolower(trim($urlArr[count($urlArr)-1]));
		if (in_array($urlExt, $ext, true)) {
			return $url;
		} else {
			return "";
		}
	}
	

	private function getCustomPageSettingsFromPluginOptions($nameOfCustomPage) {
      $ctin = stripslashes(get_option( 'jci_pro_custom_post_types' ));
      $foundItem = FALSE;
      $ctinArr0 = explode("##", $ctin);
      $zorbTmp = array();
       for ($i=0;$i<count($ctinArr0);$i++) {
        $ctinArr1 = explode(";", $ctinArr0[$i]);
        for ($j=0;$j<count($ctinArr1);$j++) {
          $ctinArr2 = explode("=", $ctinArr1[$j]);
          if (!empty($ctinArr2[0]) && !empty($ctinArr2[1])) {
            $zorbTmp[$ctinArr2[0]] = $ctinArr2[1];
            if ($ctinArr2[1]==$nameOfCustomPage) {
              $foundItem = TRUE;
            }
          }
        }
        if ($foundItem) {
          break;
        }
        unset($zorbTmp);
      }
      if ($foundItem) {
        return $zorbTmp;
      }
      return NULL;
}


	private function addOneTaxonomiesInCP($idOfPost, $taxonomyValue, $taxonomyName) {
		wp_set_object_terms( $idOfPost, $taxonomyValue, $taxonomyName );
	}
	
	private function addOneCustomFieldInCP($idOfPost, $cfValue, $cfName, $k, $valueNode=NULL) {
		#$valueNode = "{{id}} and {{name}}";
		if (is_Array($cfValue)) {
			#echo print_r($cfValue, TRUE)."<br>";
			if (empty($valueNode)) {
				foreach ($cfValue as $key =>$v) {
					if (!empty($v)) {
						$this->jci_collectCreateMessage( "($k) Add CPF for pageid $idOfPost with '$cfName : $v'");
						add_post_meta($idOfPost, $cfName, $v);
					}
				}
			} else {
				foreach ($cfValue as $key =>$v) {
					if (preg_match("/{{/", $valueNode)) { # execute twig-code 
						if(!class_exists('doJCITwig')){
							$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
							require_once $inc;
							$this->twigHandler = new doJCITwig($this->parser[$this->nestedlevel], TRUE);
						}
						#echo "ok twig: $valueNode<br>JSON: ".print_R($v, TRUE)."<br>";
						$val4cpf = $this->twigHandler->executeTwig($v, $valueNode, $this->parser[$this->nestedlevel], TRUE);
						#echo $val4cpf."<br>";
					} else {
						$val4cpf = @$v[$valueNode];
					}
					if (!isset($val4cpf)) {
						$val4cpf = "unable to resolve CPF-value with $valueNode";
					}
					$this->jci_collectCreateMessage( "($k) Add CPF for pageid $idOfPost with '$cfName : $val4cpf'");
					add_post_meta($idOfPost, $cfName, $val4cpf);
				}
			}
			return TRUE;
		} else {
			return add_post_meta($idOfPost, $cfName, $cfValue);
		}
	}

	private	function twigcode_replacePlaceholders($value) {
		$value = preg_replace("/#SQM#/i", "'", $value);
		$value = preg_replace("/#GT#/i", ">", $value);
		$value = preg_replace("/#LT#/i", "<", $value);
		return $value;
	}



	private	function inputtypeparamArr_replacePlaceholders($value) {
		$value = preg_replace("/#QM#/i", "\"", $value);
		$value = preg_replace("/#CR#/i", "\r", $value);
		$value = preg_replace("/#LF#/i", "\n", $value);
		$value = preg_replace("/#BS#/i", "\\", $value);
		return $value;
	}

    /* shortcodeExecute: read shortcode-params and check cache */
	public function shortcodeExecute($atts , $content = ""){
		extract(shortcode_atts(array(
			'id' => '',
			'nameoftemplate' => '',
			'jsonuseset' => '',
			'url' => '',
			'urlparam' => '',
			'pathparam' => '',
			'fileext' => '',
			'urlgettimeout' => '',
			'numberofdisplayeditems' => '',
			'oneofthesewordsmustbein' => '',
			'oneofthesewordsmustbeindepth' => '',
			'oneofthesewordsmustnotbein' => '',
			'oneofthesewordsmustnotbeindepth' => '',
			'requiredfieldsandvalues' => '',
			'requiredfieldsandvaluesdepth' => '',
			'requiredfieldsandvalueslogicandbetweentwofields' => '',
			'basenode' => '',
			'param1' => '',
			'param2' => '',
			'dodisplayonlyif' => '',
			'filterresultsin' => '',
			'filterresultsnotin' => '',
			'sortfield' => '',
			'sortorderisup' => '',
			'sorttypeisnatural' => '',
			'loopwithoutsubloop' => '',
			'parser' => '',           
			'method' => '',
			'feedsource' => '',
			'feedfilename' => '',
			'postpayload' => '',
			'postbody' => '',
			'customfieldparam' => '',
			'header' => '',
			'auth' => '',
			'urlgetaddrandom' => '',
			'inputtype' => '',
			'inputtypeparam' => '',
			'trytohealjson' => '',
			'cachetime' => -1,
		    'debugmode' => -1,
			'urlencodepostpayload' => '',
			'mode' => '',
			'createoptions' => '',
			'convertjsonnumbers2strings' => '',
			'removeampfromurl' => '',
			'curloptions' => '',
			'urladdparam' => '',
			'urlparam4twig' => '',  
			'httpstatuscodemustbe200' => '',     
			'maskspecialcharsinjson' => '',     
			'displayapireturn' => '',
			'addpostdata2json' => FALSE,
			'addcpf2json' => FALSE,
			'forcetemplate' => FALSE,
			'encodingofsource' => '',
			'secret' => '',
			'orderofshortcodeeval' => 1,
			'showapiresponse' => FALSE,
		), $atts));

		$val_jci_pro_use_nestedlevel = get_option('jci_pro_use_nestedlevel');
		if (2==$val_jci_pro_use_nestedlevel || ($orderofshortcodeeval>2)) { 
			$this->nestedlevel++; # set depth of maybe nested shortcode calls
		} else {
			$this->nestedlevel = 0;
		}

		$val_jci_pro_debugmode = get_option('jci_pro_debugmode');
		$this->debugLevel[$this->nestedlevel] = $val_jci_pro_debugmode;
		$this->debugModeIsOn[$this->nestedlevel] = FALSE;
		if ($val_jci_pro_debugmode>1) {
			$this->debugModeIsOn[$this->nestedlevel] = TRUE;
		}
		require_once plugin_dir_path( __FILE__ ) . '/lib/logdebug.php';

		logDebug::jci_clearDebugMessage();
		$this->licencelevel = edd_jcipro_check_license();
		if ($this->licencelevel!=-1) {
			$this->nestedlevel--;
			return "Plugin JSON Content Importer Pro not running: Check Licence! Check that a Licence is active for ".edd_jcipro_get_requestingDomain();
		}

		if (intval($debugmode)>1) {
			# valid debugmode
			$this->debugLevel[$this->nestedlevel] = $debugmode;
			$this->debugModeIsOn[$this->nestedlevel] = TRUE;
			$this->jci_collectDebugMessage("set debugmode active via shortcode, level: ".$this->debugLevel[$this->nestedlevel], $this->debugLevel[$this->nestedlevel], "", TRUE, FALSE, "<br>");
   		} else {
			$this->debugLevel[$this->nestedlevel] = 1;
			$this->debugModeIsOn[$this->nestedlevel] = FALSE;
		}

		if ($this->licencelevel==-1) {
			$this->jci_collectDebugMessage("Plugin-Licence here is active for ".edd_jcipro_get_requestingDomain());
		}


	  $this->postPayload[$this->nestedlevel] = "";
	  $this->postbody[$this->nestedlevel] = "";   # default method
	  $this->method[$this->nestedlevel] = "get";   # default method

		$this->secret[$this->nestedlevel] = $secret;
		$sec = "";
		if (isset($_GET['sec'])) {
			$sec = htmlentities($_GET["sec"]);
		}
		if (!empty($this->secret[$this->nestedlevel])) {
			if (empty($sec) || ($sec!=htmlentities($this->secret[$this->nestedlevel]))) {
				$this->nestedlevel--;
				return "plugin aborted: access denied - check secret!";
			}
		}
            
		if ($forcetemplate==1) {
			$this->forceTemplate = TRUE;
		} else {
			$this->forceTemplate = FALSE;
		}
		
			$this->encodingofsource[$this->nestedlevel] = "";
		if (!empty($encodingofsource)) {
			$this->encodingofsource[$this->nestedlevel] = $encodingofsource;
		}
		
		$this->orderofshortcodeeval[$this->nestedlevel] = 1;
		$val_jci_pro_order_of_shortcodeeval = get_option('jci_pro_order_of_shortcodeeval');
		if (!empty($val_jci_pro_order_of_shortcodeeval)) {
			$this->orderofshortcodeeval[$this->nestedlevel] = $val_jci_pro_order_of_shortcodeeval;
		}
		if (!empty($orderofshortcodeeval)) {
			$this->orderofshortcodeeval[$this->nestedlevel] = $orderofshortcodeeval;
		}
		
        $this->httpstatuscodemustbe200[$this->nestedlevel] = TRUE;
      if ("no"==$httpstatuscodemustbe200) {
        $this->httpstatuscodemustbe200[$this->nestedlevel] = FALSE;
      }

        $this->maskspecialcharsinjsonFlag[$this->nestedlevel] = TRUE;
      if ("no"==$maskspecialcharsinjson) {
        $this->maskspecialcharsinjsonFlag[$this->nestedlevel] = FALSE;
      }
      $this->displayapireturn[$this->nestedlevel] = 0;
      if ($displayapireturn>0) {
        $this->displayapireturn[$this->nestedlevel] = $displayapireturn;
        #$this->maskspecialcharsinjsonFlag[$this->nestedlevel] = FALSE; # by default
      }
      if ($addpostdata2json=="y") {
       $this->addpostdata2json = TRUE;
      }
      if ($addcpf2json=="y") {
       $this->addcpf2json = TRUE;
      }
	  
	  
	  
		$this->curloptions[$this->nestedlevel] = "";

      if (!empty($curloptions)) {
        $this->jci_collectDebugMessage("set curloptions via shortcode: ".$curloptions);
        $this->curloptions[$this->nestedlevel] = $curloptions;
      }
 
      ################## get template if set
      $this->nameoftemplate = $this->removeInvalidQuotes($nameoftemplate);
      $this->jsonuseset = $this->removeInvalidQuotes($jsonuseset);
	  
      $id = $this->removeInvalidQuotes($id);
      if (is_numeric($id) && ($id>0)) {
        $this->templateid = $id;
      } else {
        $this->templateid = '';
      }
      $overwriteValuesFromTemplate = FALSE;
      $useValuesFromUseSet = FALSE;
      if ($content=="") {
        # shortcode without content: get textitem out of database
        # either via templateid or nameoftemplate. if both are set use id
        $thereIsAIdOrName = FALSE;
        if (is_numeric($this->templateid) && ($this->templateid>0)) {
          $this->jci_collectDebugMessage("load template with this id: ".$this->templateid);
          $thereIsAIdOrName = TRUE;
          $selectStr = " id = ".$this->templateid;
        } else if ($this->nameoftemplate!="") {
          $selectStr = " nameoftemplate = \"".$this->nameoftemplate."\"";
          $this->jci_collectDebugMessage("load template with this id: ".$this->nameoftemplate);
          $thereIsAIdOrName = TRUE;
        } else if ($this->jsonuseset!="") {
          $selectStr = " jsonuseset = \"".$this->jsonuseset."\"";
          $this->jci_collectDebugMessage("load template with jsonuseset: ".$this->jsonuseset);
        } else {
			$errmsg = "No template for the JSON Content Importer found: Either add nameoftemplate=... or jsonuseset=... to the Shortcode. Or add a closing Shortcode-Tag: [jsoncontentimporterpro...]TEMPLATE[/jsoncontentimporterpro]<br>";
			$errmsg .= "At this point the plugin is terminated.<br>";
			return $errmsg;
        }
        if ($thereIsAIdOrName) {
          global $wpdb;
          $tmpl = $wpdb->get_row( 'SELECT template, urloftemplate, basenode, urlparam4twig, method, parser, curloptions, postpayload, postbody, cachetime, urlgettimeout, debugmode FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates' );
          if (is_null($tmpl)) {
			$this->nestedlevel--;
              return $this->jci_showDebugMessage()."Template-Database was not updated when upgrading to 3.4.7 and later! Deactivate, then activate the Plugin";
		  }

          $tmpl = $wpdb->get_row( 'SELECT template, urloftemplate, basenode, urlparam4twig, method, parser, curloptions, postpayload, postbody, cachetime, urlgettimeout, debugmode FROM ' . $wpdb->prefix . 'plugin_jci_pro_templates WHERE '.$selectStr );
          if (is_null($tmpl)) {
			$this->nestedlevel--;
              return $this->jci_showDebugMessage()."Plugin-Template not found: Check if this plugin-template-id or -name is really existing!";
           }


			$this->parser[$this->nestedlevel] = "jci"; # either "jci" or "twig"

			if (("twig"==@$tmpl->parser) || ("twig243"==@$tmpl->parser) || ("twig3"==@$tmpl->parser) || ("twig332"==@$tmpl->parser) || ("twig332adj"==@$tmpl->parser)) { 
            $this->parser[$this->nestedlevel] = $this->removeInvalidQuotes($tmpl->parser);           
            $this->jci_collectDebugMessage("parser set via template ".$this->templateid.": ".$this->parser[$this->nestedlevel]);
          }
          #if (""==$debugmode) {  # removed 563: do not care other settings, use template-value
            # check template
            if (intval(@$tmpl->debugmode)>1) {
	 	          $this->debugLevel[$this->nestedlevel] = $tmpl->debugmode;
         	    $this->debugModeIsOn[$this->nestedlevel] = TRUE;
              $this->jci_collectDebugMessage("set debugmode active via template, level: ".$this->debugLevel[$this->nestedlevel]);
            } else {
              $this->jci_collectDebugMessage("debugmode via template unchanged: ".$this->debugLevel[$this->nestedlevel]);
            }
          #}
          $overwriteValuesFromTemplate = TRUE;
        } else if ($this->jsonuseset!="") {
			# json-access- and use-set
			$jci_pro_api_use_items = json_decode(get_option('jci_pro_api_use_items'), TRUE);
			if (is_null($jci_pro_api_use_items)) {
				$this->nestedlevel--;
				return $this->jci_showDebugMessage()."No JSON-Use-Sets defined. Plugin aborted.";
			}
			$found_use_set = NULL;
			foreach($jci_pro_api_use_items as $k => $v) {
				if ($this->jsonuseset==$v["savejusname"]) {
					$found_use_set = $v;
				}
			}
			if (empty($found_use_set)) {
				$this->nestedlevel--;
				return $this->jci_showDebugMessage()."No such JSON-Use-Set defined. Plugin aborted.";
			}
			$useValuesFromUseSet = TRUE;
			
			#echo print_r($found_use_set, true);
             $this->jci_collectDebugMessage("JSON-Use-Set:  ".json_encode($found_use_set));
			
			########################
			### load use-set, access-set to get JSON and twig etc.
			########################
			
			
			$found_access_set_id = $found_use_set["selectejas"];
			
			$jci_pro_api_access_items = json_decode(get_option('jci_pro_api_access_items'), TRUE);
			if (is_null($jci_pro_api_access_items)) {
				#echo "No JSON-Access-Sets defined. Plugin aborted.";
				$this->nestedlevel--;
				return $this->jci_showDebugMessage()."No JSON-Access-Sets defined. Plugin aborted.";
			}

			#var_Dump($jci_pro_api_access_items);
			$found_access_set = $jci_pro_api_access_items[$found_access_set_id];
			#echo htmlspecialchars(json_encode($found_access_set));
			$this->parser[$this->nestedlevel] = "twig332adj";
			
        } else {
			# json-use-set
			echo "Set a JCI-Template by nameoftemplate=... or a JSON-Use-Set by jsonuseset=... in the Shortcode, please";
			return "";
		}
      }
      $this->jci_collectDebugMessage("version of plugin: ".JCIPRO_VERSION);
      if (""!=$parser) {
        # if parser is set in shortcode: use this!!
        if ($parser=="twig" || $parser=="twig243" || $parser=="jci" || $parser=="twig3" || $parser=="twig332" || $parser=="twig332adj") {
          $this->parser[$this->nestedlevel] = $this->removeInvalidQuotes($parser);
          $this->jci_collectDebugMessage("parser set via shortcode: ".$this->parser[$this->nestedlevel]);
        } else {
          $this->jci_collectDebugMessage("parser NOT set via shortcode - invalid parser specified, selected parser is: ".$this->parser[$this->nestedlevel]);
        }
      }
      $this->jci_collectDebugMessage("selected parser: ".$this->parser[$this->nestedlevel]);

	################## set twig: even with the jci-parser we need it for urlparam4twig etc.
	### twig-init-begin
	$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
	require_once $inc;
	
    if (is_null($this->twigHandler)) {
		# single or first invoke of twig
			$this->twigHandler = new doJCITwig($this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
	} else {
		# there is another twig set by a included shortcode (jci-template parser=twig3 has another jci-template shortcode calling which is set to parser=twig243 --> set all to twig3)
		$twigthere = $this->twigHandler->jci_getSelectedTwigVersion();
		if (preg_match("/^3/", $twigthere)) {
			$this->parser[$this->nestedlevel] = "twig3";
		}
		if (preg_match("/^3.3.2/", $twigthere)) {
			$this->parser[$this->nestedlevel] = "twig332";
		}
		if (preg_match("/^2/", $twigthere)) {
			$this->parser[$this->nestedlevel] = "twig243";
		}
		if (preg_match("/^1/", $twigthere)) {
			$this->parser[$this->nestedlevel] = "twig";
		}
		if (preg_match("/^adj3.3.2/", $twigthere)) {
			$this->parser[$this->nestedlevel] = "twig332adj";
		}
	}
	### twig-init-end


      ###### shortcode: dodisplayonlyif: display NOT, if something is NOT fullfilled
      # e.g. POST- or GET-parameter is set with a special value:
      # dodisplayonlyif="POST/GET:variablename:variablevalue"
      # variablename:variablevalue: allowed only [0-9a-zA-Z_-.;]
      $dodisplayonlyif = $this->removeInvalidQuotes($dodisplayonlyif);
      if (!empty($dodisplayonlyif) && preg_match("/([a-z]+)\:([0-9a-z\_\-\.\;]+)\:([0-9a-z\_\-\.\;]+)/i", $dodisplayonlyif)) {
        $dodisplayonlyifArr = explode(":", $dodisplayonlyif, 3);
        $varimetho = trim($dodisplayonlyifArr[0]);
        $variname = trim($dodisplayonlyifArr[1]);
        $varivalue = trim($dodisplayonlyifArr[2]);

        if ($varimetho=="GET") {
          $vargot = sanitize_text_field($_GET[$variname]);
          if (!strcmp($varivalue, $vargot)) {
            $this->hidedisplayflag = TRUE;
          }
        } else if ($varimetho=="POST") {
          $vargot = sanitize_text_field($_POST[$variname]);
          if (!strcmp($varivalue, $vargot)) {
            $this->hidedisplayflag = TRUE;
          }
        }
      }

      if ("no"==$urlencodepostpayload) {
        $this->urlencodepostpayload = $urlencodepostpayload;
      }

      $loopwithoutsubloop = $this->removeInvalidQuotes($loopwithoutsubloop);
      if ($loopwithoutsubloop=="y") {
        $this->loopWithoutSubloop = "y";
      }

      $this->param1[$this->nestedlevel] = $this->removeInvalidQuotes($param1);
      $this->param2[$this->nestedlevel] = $this->removeInvalidQuotes($param2);
      
      $this->customfieldparam[$this->nestedlevel] = $this->removeInvalidQuotes($customfieldparam);

	    if (get_option('jci_pro_delimiter')!="") {
        $this->delimiter = get_option('jci_pro_delimiter');
      }

      $this->filterresultsin = $this->removeInvalidQuotes($filterresultsin);
      $this->filterresultsnotin = $this->removeInvalidQuotes($filterresultsnotin);

      $this->header[$this->nestedlevel] = $this->removeInvalidQuotes($header);
      $this->auth[$this->nestedlevel] = $this->removeInvalidQuotes($auth);
	  
        $this->urlgetaddrandom[$this->nestedlevel] = FALSE;
      if ("yes"==$urlgetaddrandom) {
        $this->urlgetaddrandom[$this->nestedlevel] = TRUE;
      }

      $this->convertJsonNumbers2Strings[$this->nestedlevel] = FALSE;
      if ($convertjsonnumbers2strings=="yes") {
        $this->convertJsonNumbers2Strings[$this->nestedlevel] = TRUE;
      }

      $this->trytohealjson[$this->nestedlevel] = FALSE;
      if ("yes"==$trytohealjson) {
        $this->trytohealjson[$this->nestedlevel] = TRUE;
      }

		
      $this->inputtype[$this->nestedlevel] = "json";
      if ("xml"==strtolower($inputtype) || "csv"==strtolower($inputtype)) {
        $this->inputtype[$this->nestedlevel] = strtolower($inputtype);
		if (!empty($inputtypeparam)) {
			$this->inputtypeparamArr[$this->nestedlevel] = json_decode($inputtypeparam);
			if (empty($this->inputtypeparamArr[$this->nestedlevel])) {
				$this->jci_collectDebugMessage( "Shortcodeparameter inputtypeparam ignored, as it is not valid JSON: \n".$inputtypeparam);
			}
		}
      }

      $this->sortField = $this->removeInvalidQuotes($sortfield);
      $sortorderisup = $this->removeInvalidQuotes($sortorderisup);
      if ($sortorderisup=="yes") {
        $this->sortorderIsUp = $sortorderisup;
      } else {
        $this->sortorderIsUp = "";
      }
      $sorttypeisnatural = $this->removeInvalidQuotes($sorttypeisnatural);
      if ($sorttypeisnatural=="yes") {
        $this->sorttypeIsNatural = $sorttypeisnatural;
      } else {
        $this->sorttypeIsNatural = "";
      }

      $this->mode[$this->nestedlevel] = $this->removeInvalidQuotes($mode);

	
	#########
	# SET CREATE BEGIN
	#########
	if ($this->mode[$this->nestedlevel]=="create") {
	# title and slugname
        global $wp_query;
        @$pageid = $wp_query->post->ID;
        if (empty($pageid)) {
          $this->jci_collectDebugMessage( "ID of the creating page not available. No extra CPF added.\n");
          #return $this->jci_showDebugMessage(); # move on even without a pageid: e. g. cronjob
        }  else {
          $this->pageid = $pageid;
          $this->jci_collectDebugMessage( "ID of the creating page is ".$this->pageid."\n");
        }
        $this->jci_collectDebugMessage( "mode: ".$this->mode[$this->nestedlevel]);    
        if ($createoptions!="") {
          $createoptionsTmp = $this->removeInvalidQuotes($createoptions);
          $createoptionsTmp = preg_replace("/#BRO#/", "[", $createoptionsTmp);
          $createoptionsTmp = preg_replace("/#BRC#/", "]", $createoptionsTmp);
          $this->jci_collectDebugMessage("createoptions: ".$createoptionsTmp);
          $this->createoptionsArr[$this->nestedlevel] = json_decode($createoptionsTmp, TRUE);
          if ($this->createoptionsArr[$this->nestedlevel]==NULL) {
            $this->jci_collectCreateMessage( "createoptions: JSON decoding fails, check JSON-syntax in Shortcode!<br>\n$createoptionsTmp\n<br>plugin aborted", FALSE, 40000);
			$this->nestedlevel--;
			return $this->jci_showDebugMessage();
          } else {
            $this->jci_collectDebugMessage( "<font color=green>createoptions in shortcode: JSON ok!</font>", $this->debugLevel[$this->nestedlevel], "", FALSE);
            #echo "<font color=green>createoptions in shortcode: JSON ok!</font><br>";
          }
        }
      }
	#########
	# SET CREATE END
	#########
	  
	  
      $this->oneofthesewordsmustbein = $this->removeInvalidQuotes($oneofthesewordsmustbein);
      $this->oneofthesewordsmustbeindepth = $this->removeInvalidQuotes($oneofthesewordsmustbeindepth);
      $this->oneofthesewordsmustnotbein = $this->removeInvalidQuotes($oneofthesewordsmustnotbein);
      $this->oneofthesewordsmustnotbeindepth = $this->removeInvalidQuotes($oneofthesewordsmustnotbeindepth);
      $this->requiredfieldsandvalues = $this->removeInvalidQuotes($requiredfieldsandvalues);
      $this->requiredfieldsandvaluesdepth = $this->removeInvalidQuotes($requiredfieldsandvaluesdepth);
      $requiredfieldsandvalueslogicandbetweentwofields = $this->removeInvalidQuotes($requiredfieldsandvalueslogicandbetweentwofields);
      if ($requiredfieldsandvalueslogicandbetweentwofields=="yes") {
        # yes: all fields must match
        $this->requiredfieldsandvalueslogicandbetweentwofields = TRUE;
      } else {
        $this->requiredfieldsandvalueslogicandbetweentwofields = FALSE;
      }

      $this->basenode = $this->removeInvalidQuotes($basenode);
	  
      $this->feedUrl[$this->nestedlevel] = $this->removeInvalidQuotes($url);
      if (""!=$this->basenode) {
        $this->jci_collectDebugMessage("basenode: ".$this->basenode);
      }
	  
		$this->cachetime[$this->nestedlevel] = 0;
		$this->urlgettimeout[$this->nestedlevel] = 5;
		$this->urlparam4twig[$this->nestedlevel] = "";   

      if (1==$showapiresponse) {
		$this->showapiresponse[$this->nestedlevel] = TRUE;   
	  } else {
		$this->showapiresponse[$this->nestedlevel] = FALSE;   		  
	  }
        
      if ($overwriteValuesFromTemplate) {
			$content = @$tmpl->template;
			if ($this->forceTemplate) { 
				$this->basenode = $tmpl->basenode;
				$this->jci_collectDebugMessage("force template via template ".$this->templateid.": ".$content);
				$this->jci_collectDebugMessage("force basenode via template ".$this->templateid.": ".$this->basenode);

				$this->feedUrl[$this->nestedlevel] = trim($tmpl->urloftemplate);
				$this->jci_collectDebugMessage("force url via template ".$this->templateid.": ".$this->feedUrl[$this->nestedlevel]);

				$this->urlparam4twig[$this->nestedlevel] = $tmpl->urlparam4twig;           
				$this->jci_collectDebugMessage("force urlparam4twig via template ".$this->templateid.", urlparam4twig and use: ".$this->urlparam4twig[$this->nestedlevel]);

				$this->method[$this->nestedlevel] = $tmpl->method;          
				$this->jci_collectDebugMessage("force method via template ".$this->templateid.", method: ".$this->method[$this->nestedlevel]);

				$this->curloptions[$this->nestedlevel] = $tmpl->curloptions;          
				$this->jci_collectDebugMessage("force curloptions via template ".$this->templateid.": ".$this->curloptions[$this->nestedlevel]);

				$this->postPayload[$this->nestedlevel] = $tmpl->postpayload;          
				$this->jci_collectDebugMessage("force postpayload via template ".$this->templateid.": ".$this->postPayload[$this->nestedlevel]);

				$this->postbody[$this->nestedlevel] = $tmpl->postbody;          
				$this->jci_collectDebugMessage("force postbody via template ".$this->templateid.": ".$this->postbody[$this->nestedlevel]);

				$this->cachetime[$this->nestedlevel] = $tmpl->cachetime;          
				$this->jci_collectDebugMessage("force cachetime via template ".$this->templateid.": ".$this->cachetime[$this->nestedlevel]);

				if (empty($tmpl->urlgettimeout)) {
					$tmpl->urlgettimeout = 5;
				}
				$this->urlgettimeout[$this->nestedlevel] = $tmpl->urlgettimeout;   
				$this->jci_collectDebugMessage("force urlgettimeout via template ".$this->templateid.": ".$this->urlgettimeout[$this->nestedlevel]);
			} else {
				if (@$tmpl->basenode!="") {
					$this->basenode = $tmpl->basenode;
				}
				if (@$tmpl->urloftemplate!="") {
					$this->feedUrl[$this->nestedlevel] = trim($tmpl->urloftemplate);
				}
				if (@$tmpl->urlparam4twig!="") {        
					$this->urlparam4twig[$this->nestedlevel] = $tmpl->urlparam4twig;           
					$this->jci_collectDebugMessage("load from template ".$this->templateid.", urlparam4twig and use: ".$this->urlparam4twig[$this->nestedlevel]);
				}
				if (@$tmpl->method!="") {   
					$this->method[$this->nestedlevel] = $tmpl->method;          
					$this->jci_collectDebugMessage("set method via template ".$this->templateid.", method: ".$this->method[$this->nestedlevel]);
				}
				if (@$tmpl->curloptions!="") {   
					$this->curloptions[$this->nestedlevel] = $tmpl->curloptions;          
					$this->jci_collectDebugMessage("set curloptions via template ".$this->templateid.": ".$this->curloptions[$this->nestedlevel]);
				}
				if (@$tmpl->postpayload!="") {   
					$this->postPayload[$this->nestedlevel] = $tmpl->postpayload;          
					$this->jci_collectDebugMessage("set postpayload via template ".$this->templateid.": ".$this->postPayload[$this->nestedlevel]);
				}
				if (@$tmpl->postbody!="") {   
					$this->postbody[$this->nestedlevel] = $tmpl->postbody;          
					$this->jci_collectDebugMessage("set postbody via template ".$this->templateid.": ".$this->postbody);
				}
				if (@$tmpl->cachetime!="") {   
					$this->cachetime[$this->nestedlevel] = $tmpl->cachetime;          
					$this->jci_collectDebugMessage("set cachetime via template ".$this->templateid.": ".$this->cachetime[$this->nestedlevel]);
				}
				if (@$tmpl->urlgettimeout!="") {   
					$this->urlgettimeout[$this->nestedlevel] = $tmpl->urlgettimeout;          
					$this->jci_collectDebugMessage("set urlgettimeout via template ".$this->templateid.": ".$this->urlgettimeout[$this->nestedlevel]);
				}
				$this->jci_collectDebugMessage("set template via template ".$this->templateid.": ".$content);
				$this->jci_collectDebugMessage("set url via template ".$this->templateid.": ".$this->feedUrl[$this->nestedlevel]);
				if (!empty($this->basenode)) {
					$this->jci_collectDebugMessage("set basenode via template ".$this->templateid.": ".$this->basenode);
				}
			}
      }
	  
	  if ($useValuesFromUseSet) {
		##############
		# BEGIN use of USE-SET
		$formdata = $found_access_set["set"];
		# BEGIN param
		## shortcode: debugmode=10
		$debugModeIsOn = $this->debugModeIsOn[$this->nestedlevel]; #FALSE;
		$debugLevel = $this->debugLevel[$this->nestedlevel];
		$cachetimeUsed = 0;
		if (isset($cachetime)) {
			$cachetimeInt = (int) $cachetime;
			if (is_int($cachetimeInt)) {
				$cachetimeUsed = $cachetimeInt;
			}
		}
		$cacheEnable = FALSE;
		if ($cachetimeUsed>0) {
			$cacheEnable = TRUE;
		}
		#return "cachetime: ".$cachetimeUsed."<hr>";
		# END: param
		
		# BEGIN buildrequest
		require_once plugin_dir_path( __FILE__ ) . 'lib/lib_request.php';
		$jci_request_handler = new jci_request_prepare($formdata, $formdata["method"], $formdata["methodtech"]);
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
			$cacheFileFingerPrint = json_encode($formdata);
			$cacheFile = $cachepath.md5($cacheFileFingerPrint).".cgi";
			$cacheExpireTime = time() - (60*$cachetimeUsed);
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
		
		$convertJsonNumbers2Strings = FALSE;
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
        $jsonArr = $jsonDecodeObj->getJsondata();
	
		if (empty($jsonArr)) {
			$errormsg = stripslashes(get_option('jci_pro_errormessage'));
			if (empty($errormsg)) {
				$this->nestedlevel--;
				return $this->jci_showDebugMessage()."Error: Problems loading data - reload page again, please ($httpcode)<hr>";
			}
			$this->nestedlevel--;
			return $this->jci_showDebugMessage().$errormsg;
		}
		#return "selectedmethod: $selectedmethod<br>receivedData:<hr>$receivedData<hr><br>httpcode: $httpcode<br>";
		
		# invoke basenode
		$selectedbasenode = $found_use_set["jsonbasenode"]; #return json_encode($jsonbasenode);
		#return "selectedbasenode: $selectedbasenode";
		if (!empty($selectedbasenode)) {
			require_once plugin_dir_path( __FILE__ ) . '/lib/lib_jsonphp.php';
			$wwj = new workWithJSON($jsonArr);
			$wwj->selectJSONnode($jsonArr, $selectedbasenode);
			$jsonArr = $wwj->getBasenodeSelectedJSON();
			#return "selectedbasenode: ".$selectedbasenode."<hr>".json_encode($jsonArr);
		}
		# reduce JSON with pattern
		$seljs = $found_use_set["seljs"]; #return json_encode($seljs);
		require_once plugin_dir_path( __FILE__ ) . '/lib/lib_jsonselector.php';
		$jsonSelector = new jsonSelector();
		$jsonStr = $jsonSelector->processJson(json_encode($jsonArr), $seljs);
		#return $jsonStr;
		
		# execute twig-template on reduced JSON
		$twigcontent = $found_use_set["content"]; #return json_encode($twigcontent);
		$reducedJsonArr = json_decode($jsonStr, TRUE);
		$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
		require_once $inc;
		$twigHandler = new doJCITwig("twig332adj", TRUE);
		$twigResult = $twigHandler->executeTwig($reducedJsonArr, $twigcontent, "twig332adj", TRUE);
        return $this->jci_showDebugMessage().$twigResult;
		
		# END use of USE-SET
		######################
	  }
	  
      if (intval($cachetime)>=0) {
        $this->cachetime[$this->nestedlevel] = $cachetime;
        $this->jci_collectDebugMessage("set cachetime via shortcode: ".$this->cachetime[$this->nestedlevel]);
      }
      if (!empty($postpayload)) {
        $this->postPayload[$this->nestedlevel] = $this->removeInvalidQuotes($postpayload);
        $this->jci_collectDebugMessage("set postpayload via shortcode: ".$this->postPayload[$this->nestedlevel]);
      }
      if (""!=$postbody) {
        $this->postbody[$this->nestedlevel] = $this->removeInvalidQuotes($postbody);
        $this->jci_collectDebugMessage("set postbody via shortcode: ".$this->postbody[$this->nestedlevel]);
      }
	  
      $method = $this->removeInvalidQuotes($method);
      if ($method=="post" ||
        $method=="rawpost" ||
        $method=="get" ||
        $method=="curlget" ||
        $method=="curlpost" ||
        $method=="curlput" ||
        $method=="curldelete" ||
        $method=="rawget"
      ) {
        $this->method[$this->nestedlevel] = $method;
        $this->jci_collectDebugMessage("set method via shortcode: ".$this->method[$this->nestedlevel]);
      }
      $this->jci_collectDebugMessage("active method: ".$this->method[$this->nestedlevel]);

      if (""!=$urladdparam) {
		$this->urladdparam = $urladdparam;    
        $dummyarray = array();   
		$this->urladdparam = $this->twigHandler->executeTwig($dummyarray, $this->urladdparam, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
		$twigDebugMsg = $this->twigHandler->getTwigDebug();
        $this->jci_collectDebugMessage("execute twig-code in urladdparam-parameter: ".$twigDebugMsg, 2, "", FALSE);
        $this->jci_collectDebugMessage("result twig-code in urladdparam-parameter: ".$this->urladdparam);
        if (preg_match("/#BRO#(.*)#BRC#/i", $this->urladdparam)) {
          $this->urladdparam = preg_replace("/#BRO#/i", "[", $this->urladdparam);
          $this->urladdparam = preg_replace("/#BRC#/i", "]", $this->urladdparam);
          $this->urladdparam = preg_replace("/%22/i", "\"", $this->urladdparam);
          $this->urladdparam = preg_replace("/&amp;/i", "&", $this->urladdparam);
          $this->jci_collectDebugMessage("execute Shortcode in urladdparam-parameter: ".$this->urladdparam);
          $this->urladdparam = do_shortcode($this->urladdparam);
        }
        $this->jci_collectDebugMessage("add to URL: ".$this->urladdparam);
        $this->feedUrl[$this->nestedlevel] .= $this->urladdparam;
      }      

        $this->removeampfromurl[$this->nestedlevel] = FALSE;
      if ("yes"==$removeampfromurl) {
        $this->removeampfromurl[$this->nestedlevel] = TRUE;
      }
      if ($this->removeampfromurl[$this->nestedlevel]) {
        $this->feedUrl[$this->nestedlevel] = preg_replace("/&amp;/i", "&", $this->feedUrl[$this->nestedlevel]);
        $this->jci_collectDebugMessage("&amp; in URL replaced by &");
      }
      if ($this->urlgetaddrandom[$this->nestedlevel]) {
        $uniqid = uniqid();
        $md5key = md5(time());
        if (preg_match("/\?/i", $this->feedUrl[$this->nestedlevel])) {
          $this->feedUrl[$this->nestedlevel] .= "&n".$uniqid."=".$md5key;
        } else {
          $this->feedUrl[$this->nestedlevel] .= "?w".$uniqid."=".$md5key;
        }
      }

      $this->pathparam[$this->nestedlevel] = $this->removeInvalidQuotes($pathparam);
      $this->fileext[$this->nestedlevel] = $this->removeInvalidQuotes($fileext);
      $dynpathadd = "";

      # inspired by Lucas Butty
      $val_jci_pro_allow_urldirdyn = get_option('jci_pro_allow_urldirdyn');
      if ($val_jci_pro_allow_urldirdyn=="") {
        $val_jci_pro_allow_urldirdyn = 1;
      }
      if ($val_jci_pro_allow_urldirdyn==2) {
        $debugmsg = "dynamic url allowed, pathparam: ";
        if ($this->pathparam[$this->nestedlevel]=="") { $debugmsg .= "no pathparam defined";  } else { $debugmsg .= $this->pathparam[$this->nestedlevel];   }
        $debugmsg .= ", fileext: ";
        if (empty($this->fileext[$this->nestedlevel]))   { $debugmsg .= "no fileext defined";     } else { $debugmsg .= $this->fileext[$this->nestedlevel];         }
        $this->jci_collectDebugMessage($debugmsg);
        if (!empty($this->pathparam[$this->nestedlevel])) {
          $pathparamArr = explode("#", $this->pathparam[$this->nestedlevel]);
          $size = count($pathparamArr);
          for ($pathlp=0; $pathlp<$size; $pathlp++) {
						if (isset($_GET[$pathparamArr[$pathlp]])) {
			         $valtmp = urlencode(sanitize_text_field($_GET[$pathparamArr[$pathlp]]));
				        if ($valtmp!="" && $pathlp < ($size-1)) {
					         $dynpathadd = $dynpathadd . $valtmp . "/";
				        } else if ($valtmp!="" && $pathlp == ($size-1)) {
					         $dynpathadd = $dynpathadd . $valtmp;
				        }
			       }
          }
        }
        if (!empty($this->fileext[$this->nestedlevel])) {
						if (preg_match("/^\?/", $this->fileext[$this->nestedlevel])) {
							$dynpathadd = $dynpathadd . $this->fileext[$this->nestedlevel];
							$this->jci_collectDebugMessage("create url: no extra dot");
						} else {
							$dynpathadd = $dynpathadd . '.' . $this->fileext[$this->nestedlevel];
							$this->jci_collectDebugMessage("create url: add extra dot");
						}
        }
        if ($dynpathadd!="") {
            if (substr($this->feedUrl[$this->nestedlevel], -1) != '/') {
              $dynpathadd = '/' . $dynpathadd;
            }
            if (preg_match("/\&$/", $dynpathadd)) {
              $dynpathadd = preg_replace("/\&$/", "", $dynpathadd);
            }
            $this->feedUrl[$this->nestedlevel] = $this->feedUrl[$this->nestedlevel] . $dynpathadd;
  					$this->jci_collectDebugMessage("created dynamic url: ".$this->feedUrl[$this->nestedlevel]);
        }

      } else {
        $this->jci_collectDebugMessage("dynamic url NOT allowed, therefore ignore pathparam / fileext. Switch on: See plugin-options");
      }

      $this->urlparam[$this->nestedlevel] = $this->removeInvalidQuotes($urlparam);

      $dynurladd = "";
      $val_jci_pro_allow_urlparam = get_option('jci_pro_allow_urlparam');
      if ($val_jci_pro_allow_urlparam=="") {
        $val_jci_pro_allow_urlparam = 2;
      }

      if (($val_jci_pro_allow_urlparam==2) && ($this->urlparam[$this->nestedlevel]!="")) {
        $this->jci_collectDebugMessage("urlparam allowed, urlparam: ".$this->urlparam[$this->nestedlevel]);
        $urlparamArr = explode("#", $this->urlparam[$this->nestedlevel]);
        for ($urlp=0; $urlp<count($urlparamArr); $urlp++) {
          unset($valtmp);
          unset($urlpkey);
          unset($urlpkeyjson);
          unset($urlpkeyjsonArr);
          # loop through each urlparam-item
          $urlpval = $urlparamArr[$urlp];                  
          if (preg_match("/%5B(.*)%5D/i", $urlpval) ) {
            # multidim GET or POST-parameter
            $g = NULL;
            if (@count($_GET)) {
              $g = $_GET;
            } else if (@count($_POST)) {
              $g = $_POST;
            }
            $tmpArr = explode("%5B", $urlpval);
            $g = @$g[$tmpArr[0]];
            $keyu = @$tmpArr[0];
            $namedparameter = TRUE;
            for ($k=0;$k<count($tmpArr);$k++) {
              # loop through each defined key
              if (preg_match("/%5D/i", $tmpArr[$k])) {
                $tmpArr1 = explode("%5D", $tmpArr[$k]);
                 if ($tmpArr1[0]=="") {
                    $namedparameter = FALSE;
                    for ($w=0;$w<count($g);$w++) {
                      $valtmp[$w] = sanitize_text_field($g[$w]);  # value of item
                      $urlpkeyjson[$w] = $keyu.".".$w."";
                      $urlpkey[$w] = $keyu."%5B%5D";
                    }
                } else {
                  $g = $g[$tmpArr1[0]];
                  $keyu .= ".".$tmpArr1[0];
                }
              } else {
              }
            }
            if ($namedparameter) {
              if (is_string($g)) {
                $valtmp[0] = sanitize_text_field($g);
              } else {
              }
              $urlpkeyjson[0] = $keyu;
              $urlpkey[0] = $urlpval;
            } else {
            }
          } else {
            # 1D-GET-param
            if (isset($_GET[$urlpval])) {
              $valtmp[0] = sanitize_text_field($_GET[$urlpval]);
            } else {
              $valtmp[0] = "";
            }
            $urlpkey[0] = sanitize_text_field($urlpval);
            $urlpkeyjson[0] = $urlpkey[0];
            # POST only with 1D-param
            #if (@count($valtmp)==0) {
            if (isset($_POST[$urlpval])) {
              $valtmp[0] = sanitize_text_field($_POST[$urlpval]);
            }
            #}
          }
          if (isset($valtmp) && count($valtmp)>0) {
#            $dynurladd .= sanitize_text_field($urlpval)."=".urlencode($valtmp)."&";
           # build url and json
            for ($w=0;$w<count($valtmp);$w++) {
               if (@$valtmp[$w]!="") {
                 $urlpkey[$w] = preg_replace("/\_nowpquery/i", "", $urlpkey[$w]);
                 $dynurladd .= @$urlpkey[$w]."=".urlencode(@$valtmp[$w])."&";
                 $urlpkeyjsonArr[$valtmp[$w]] = "x";
                 if (isset($urlpkey[$w]) && ($urlpkey[$w]!="")) {
                  $this->urlparamval[@$urlpkey[$w].''] = @$valtmp[$w];
                 }
               }
             }
            if (isset($keyu) && ($keyu!="")) {
              $this->urlparamval[$keyu.''] = @$urlpkeyjsonArr;
            }
          }
        }
        if ($dynurladd!="") {
          if (preg_match("/\&$/", $dynurladd)) {
            $dynurladd = preg_replace("/\&$/", "", $dynurladd);
          }
          if (preg_match("/\?/", $this->feedUrl[$this->nestedlevel])) {
            $this->feedUrl[$this->nestedlevel] .= "&".$dynurladd;
          } else {
            $this->feedUrl[$this->nestedlevel] .= "?".$dynurladd;
          }
        }
      }

      #$customfields
      $valcfp = NULL;
      if ($this->customfieldparam[$this->nestedlevel]!="") {
        $customfieldparamUrl = "";
        $this->jci_collectDebugMessage("customfieldparam: ".$this->customfieldparam[$this->nestedlevel]);
        $customfieldparamArr = explode(",", $this->customfieldparam[$this->nestedlevel]);
        # get page id
        global $wp_query;
        $pageid = $wp_query->post->ID;
        if (empty($pageid)) {
          $this->jci_collectDebugMessage("no pageid found for customfieldparam. no extra CPF.");
        }  else {
          $this->pageid = $pageid;
          $this->jci_collectDebugMessage("customfieldparam from pageid ".$this->pageid);
          for ($customfieldparamItemNo=0; $customfieldparamItemNo<count($customfieldparamArr); $customfieldparamItemNo++) {
            $this->jci_collectDebugMessage("customfieldparam ".$customfieldparamItemNo." value: ".$customfieldparamItemNo);
            $cfName = trim($customfieldparamArr[$customfieldparamItemNo]);
            $cfVal = trim(get_post_meta($this->pageid, $cfName, true));
            $this->jci_collectDebugMessage("pageid ".$this->pageid." value: ".$cfVal);
            if ($cfVal!="") {
              $this->jci_collectDebugMessage("customfieldparam ".$cfName." value: ".$cfVal);
              $this->feedUrl[$this->nestedlevel] = preg_replace("/\<cf_".$cfName."\>/", $cfVal, $this->feedUrl[$this->nestedlevel]);
              $valcfp[$cfName] = $cfVal;
            }
          }
        }
      }

		if (preg_match("/\<post\./", $this->feedUrl[$this->nestedlevel])) { # if there is a placeholder like "<post.FIELD>" this is replaced by the value of the FIELD
			$this->jci_collectDebugMessage("feedurl with <post.FIELD> in ".$this->feedUrl[$this->nestedlevel]);
			$number_of_pageval = preg_match_all("/\<post\.(.*)\>/i", $this->feedUrl[$this->nestedlevel], $match_filler_pageval);
			if ($number_of_pageval>0) {
				$postparam = get_post(); 
				for ($i=0; $i<$number_of_pageval; $i++) {
					$foundString = $match_filler_pageval[1][$i];
					$pageval = @$postparam->$foundString;
					$this->feedUrl[$this->nestedlevel] = preg_replace("/\<post\.".$foundString."\>/", $pageval, $this->feedUrl[$this->nestedlevel]);
				}
			}
		}
      
      # fill placeholders URLPARAMVAL_BEGIN_ with values
      if (preg_match("/URLPARAMVAL_BEGIN_/", $this->feedUrl[$this->nestedlevel])) {
        # replace URLPARAMVAL_... with input values
        $number_of_URLPARAMVAL = preg_match_all("/URLPARAMVAL_BEGIN_([a-z0-9]*)##(.*?)_URLPARAMVAL_END/i", $this->feedUrl[$this->nestedlevel], $match_filler);
        $this->jci_collectDebugMessage("Number of URLPARAMVAL_ placeholders in URL: ".$number_of_URLPARAMVAL);
        if ($number_of_URLPARAMVAL>0) {
  	      for ($i=0; $i<$number_of_URLPARAMVAL; $i++) {
            $foundString = $match_filler[0][$i];
            $fi = trim($match_filler[1][$i]);
            $defaultvalue = trim($match_filler[2][$i]);
            $suffix = trim($match_filler[3][$i]); # before the value, if theres a value
            $praefix = trim($match_filler[4][$i]); # after the value
            $tmp = sanitize_text_field(@$_GET[$fi]);
            if (""==$tmp) {
              $tmp = sanitize_text_field(@$_POST[$fi]);
              if (""==$tmp) {
                $tmp = $defaultvalue;
                $this->jci_collectDebugMessage("POST: URLPARAMVAL_ $fi default value in URL: ".$tmp);
              } else {
                $this->jci_collectDebugMessage("POST: URLPARAMVAL_ $fi in URL: ".$tmp);
              }
            } else {
              $this->jci_collectDebugMessage("GET: URLPARAMVAL_ $fi in URL: ".$tmp);
            }
            $this->feedUrl[$this->nestedlevel] = preg_replace("/".$foundString."/", $tmp,  $this->feedUrl[$this->nestedlevel]);
            #echo "<hr>set urparamaval: $fi : $tmp<hr>";
            $this->urlparamval[$fi] = $tmp;  
           # print_r($this->urlparamval);
          }
        }
      }
      if (""!=$urlparam4twig) {
        $this->urlparam4twig[$this->nestedlevel] = $this->removeInvalidQuotes($urlparam4twig);
      }
 
#       if (""!=($this->urlparam4twig[$this->nestedlevel])) {
       if (!empty($this->urlparam4twig[$this->nestedlevel])) {
        $this->jci_collectDebugMessage("read urlparam4twig : ".$this->urlparam4twig[$this->nestedlevel]);
        $urlparam4TwigArr = explode("#", $this->urlparam4twig[$this->nestedlevel]);
        for ($urlp=0; $urlp<count($urlparam4TwigArr); $urlp++) {
          #echo $urlparam4TwigArr[$urlp]."<hr>";
          $fi = trim($urlparam4TwigArr[$urlp]);
		  if (is_array(@$_GET[$fi])) {
			$tmp = @$_GET[$fi];
			$tmpout = join("", $tmp);
		  } else {
			$tmp = sanitize_text_field(@$_GET[$fi]);
			$tmpout = $tmp;
		  }
          if (empty($tmpout)) {
			if (is_array(@$_POST[$fi])) {
				$tmp = @$_POST[$fi];
				$tmpout = join("", $tmp);
			} else {
				$tmp = sanitize_text_field(@$_POST[$fi]);
				$tmpout = $tmp;
			}
            if (!empty($tmp)) {
              $this->jci_collectDebugMessage("POST: URLPARAMVAL_ $fi in URL: ".$tmpout);
            }
          } else {
            $this->jci_collectDebugMessage("GET: URLPARAMVAL_ $fi in URL: ".$tmpout);
          }
          $this->urlparamval[$fi] = $tmp;  
        }
      }
      # if there is a shotcode in the url: execute it
      if (preg_match("/#BRO#(.*)#BRC#/i", $this->feedUrl[$this->nestedlevel])) {
          $urlShortcodeExcecuted = preg_replace("/#BRO#/i", "[", $this->feedUrl[$this->nestedlevel]);
          $urlShortcodeExcecuted = preg_replace("/#BRC#/i", "]", $urlShortcodeExcecuted);
          $urlShortcodeExcecuted = preg_replace("/%22/i", "\"", $urlShortcodeExcecuted);
          $urlShortcodeExcecuted = preg_replace("/&amp;/i", "&", $urlShortcodeExcecuted);
          $this->jci_collectDebugMessage("execute Shortcode in url-parameter: ".$urlShortcodeExcecuted);
          $urlShortcodeExcecuted = do_shortcode($urlShortcodeExcecuted);
          $this->feedUrl[$this->nestedlevel] = $urlShortcodeExcecuted;
          $this->jci_collectDebugMessage("result url after executing Shortcode: ".$this->feedUrl[$this->nestedlevel]);
      }
      
            
      # if there is twig code in the url: execute it
      if (preg_match("/[{%]/", $this->feedUrl[$this->nestedlevel]) || preg_match("/[{{]/", $this->feedUrl[$this->nestedlevel])) {    # detect twig by "{%" or "{{"
        $this->jci_collectDebugMessage("{ or % in url: exexcute twig-parser on it with urlparam-data: ".$this->feedUrl[$this->nestedlevel]);
        $urlparamArr4Twig["urlparam"] = $this->urlparamval;
		$urlTwigExcecuted = $this->twigHandler->executeTwig($urlparamArr4Twig, $this->feedUrl[$this->nestedlevel], $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
		$twigDebugMsg = $this->twigHandler->getTwigDebug();

        $this->jci_collectDebugMessage("execute twig-code in url-parameter: ".$twigDebugMsg, 2, "", FALSE);
        $this->feedUrl[$this->nestedlevel] = $urlTwigExcecuted;
      }
	if (!empty($this->param1[$this->nestedlevel])) {
		$this->feedUrl[$this->nestedlevel] = preg_replace("/##param1##/", $this->param1[$this->nestedlevel], $this->feedUrl[$this->nestedlevel]);
	}
	if (!empty($this->param2[$this->nestedlevel])) {
		$this->feedUrl[$this->nestedlevel] = preg_replace("/##param2##/", $this->param2[$this->nestedlevel], $this->feedUrl[$this->nestedlevel]);
	}
    $this->feedUrl[$this->nestedlevel] = trim($this->feedUrl[$this->nestedlevel]);
	$this->jci_collectDebugMessage("JSON-url: ".$this->feedUrl[$this->nestedlevel], 10);

     if (!empty($this->curloptions[$this->nestedlevel])) {
		if (!empty($this->param1[$this->nestedlevel])) {
			$this->curloptions[$this->nestedlevel] = preg_replace("/##param1##/i", $this->param1[$this->nestedlevel], $this->curloptions[$this->nestedlevel]);
		}
		if (!empty($this->param2[$this->nestedlevel])) {
			$this->curloptions[$this->nestedlevel] = preg_replace("/##param2##/i", $this->param2[$this->nestedlevel], $this->curloptions[$this->nestedlevel]);
		}
        if (preg_match("/{{(.*)}}/i",  $this->curloptions[$this->nestedlevel])) {
			$urlparamArr4Twig["urlparam"] = $this->urlparamval;
			$curloptionTwigExcecuted = $this->twigHandler->executeTwig($urlparamArr4Twig, $this->curloptions[$this->nestedlevel], $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
			$twigDebugMsg = $this->twigHandler->getTwigDebug();
			$this->jci_collectDebugMessage("execute twig-code in curloptions: ".$twigDebugMsg, 10, "", FALSE);
			$this->curloptions[$this->nestedlevel] = $curloptionTwigExcecuted;
			$this->jci_collectDebugMessage("curloptions after twig-execution: ".$this->curloptions[$this->nestedlevel], 10);
        }
        $this->curloptions[$this->nestedlevel] = preg_replace("/%22/i", "\"", $this->curloptions[$this->nestedlevel]);
        $this->curloptions[$this->nestedlevel] = preg_replace("/%7B/i", "{", $this->curloptions[$this->nestedlevel]);
        $this->curloptions[$this->nestedlevel] = preg_replace("/%7D/i", "}", $this->curloptions[$this->nestedlevel]);
        if (preg_match("/#BRO#(.*)#BRC#/i",  $this->curloptions[$this->nestedlevel])) {
          $this->curloptions[$this->nestedlevel] = preg_replace("/#BRO#/i", "[", $this->curloptions[$this->nestedlevel]);
          $this->curloptions[$this->nestedlevel] = preg_replace("/#BRC#/i", "]", $this->curloptions[$this->nestedlevel]);
          $this->curloptions[$this->nestedlevel] = preg_replace("/#QM#/i", "\"", $this->curloptions[$this->nestedlevel]);
          $this->jci_collectDebugMessage("PRE-shortcode: curloptions-parameter shortcode-execution: ".$this->curloptions[$this->nestedlevel], 10);
          $this->curloptions[$this->nestedlevel] = do_shortcode($this->curloptions[$this->nestedlevel]);
          $this->jci_collectDebugMessage("POST-shortcode: curloptions-parameter shortcode-execution: ".$this->curloptions[$this->nestedlevel], 10);
        }
      }

      $this->jci_collectDebugMessage("curloptions really used: ".$this->curloptions[$this->nestedlevel]);

      /* caching or not? */
      /* cache */
      if (
          (!class_exists('FileLoadWithCachePro'))
          || (!class_exists('JSONdecodePro'))
      ) {
        require_once plugin_dir_path( __FILE__ ) . '/class-fileload-cache-pro.php';
      }

      # set cachetime BEGIN
			if (get_option('jci_pro_enable_cache')==1) {
        $this->isCacheEnable = TRUE;
      }

      $cacheTimeFromOption = get_option('jci_pro_cache_time');  # max age of cachefile: if younger use cache, if not retrieve from web
			$format = get_option('jci_pro_cache_time_format');
      $cacheExpireTime = strtotime(date('Y-m-d H:i:s', strtotime(" -".$cacheTimeFromOption." " . $format )));
      $this->cacheExpireTime = $cacheExpireTime;
      if ($this->cachetime[$this->nestedlevel] > 0) {
        $this->isCacheEnable = TRUE;
        $this->cacheExpireTime = time() - $this->cachetime[$this->nestedlevel];
      }
      # set cachetime END

        # check cacheFolder
		require_once plugin_dir_path( __FILE__ ) . '/lib/cache.php';
        $cacheFolderObj = new jci_Cache();
        #$this->errormessagecache = $checkCacheFolderObj->geterrormessage();
  	    $this->jci_collectDebugMessage("Caching-Foldercheck: ".$cacheFolderObj->geterrormessage(), 10);

        # cachefolder ok: set cachefile
		$this->cacheFile = $cacheFolderObj->getCacheFileName($this->feedUrl[$this->nestedlevel], $this->postPayload[$this->nestedlevel], $this->postbody[$this->nestedlevel]);
        $this->jci_collectDebugMessage("use this cachefile: ".$this->cacheFile, 10);
	if ($this->isCacheEnable) {
        # 1 = checkbox "enable cache" activ
        $ctt = ": Cachetime set via Plugin-Options: ".$cacheTimeFromOption." ".$format;
        if ($this->cachetime[$this->nestedlevel] > 0) {
          $ctt = ": Cachetime is set via Shortcode / Template to ".$this->cachetime[$this->nestedlevel]." seconds";
        }
  	    $this->jci_collectDebugMessage("Caching is enabled".$ctt);
      } else {
        # if not=1: no caching
  	    $this->jci_collectDebugMessage("Caching is NOT enabled");
        $this->isCacheEnable = FALSE;
      }

      /* set other parameter */
      $numberofdisplayeditems = $this->removeInvalidQuotes($numberofdisplayeditems);
      if ($numberofdisplayeditems>=0) {
        $this->numberofdisplayeditems = $numberofdisplayeditems;
      }
   
      $urlgettimeout = $this->removeInvalidQuotes($urlgettimeout);
      if (intval($urlgettimeout)>0 || $urlgettimeout!="") {
        $this->urlgettimeout[$this->nestedlevel] = $urlgettimeout;
        $this->jci_collectDebugMessage("set urlgettimeout via shortcode: ".$this->urlgettimeout[$this->nestedlevel]);
      }

      $this->feedsource[$this->nestedlevel] = "http";
      $feedsource = $this->removeInvalidQuotes($feedsource);
      $this->feedfilename[$this->nestedlevel] = "";
      if ($feedsource == "file") {
        $this->feedsource[$this->nestedlevel] = "file";
        $this->feedfilename[$this->nestedlevel] = $this->removeInvalidQuotes($feedfilename);
      } else if ($feedsource == "ftp") {
        #$this->feedsource = "ftp"; # maybe future use
      }

      if ($this->feedUrl[$this->nestedlevel]=="" && $this->feedsource[$this->nestedlevel]!="file") {
  	    $this->jci_collectDebugMessage("no URL defined: abort, display defined errormessage");
        $errormsg = stripslashes(get_option('jci_pro_errormessage'));
		
        if ($errormsg=="") {
			$this->nestedlevel--;
          return $this->jci_showDebugMessage()."No URL defined - plugin aborted: Check url= parameter, remove quotation marks and linefeeds!<hr>";
        }
			$this->nestedlevel--;
        return $this->jci_showDebugMessage().$errormsg;
      }

	  
		#echo "URL: ".$this->feedUrl[$this->nestedlevel]."<br>";
		#echo "timeout: ".$this->urlgettimeout[$this->nestedlevel]."<br>";
		#echo "isCacheEnable: ".$this->isCacheEnable."<br>";
		#echo "cacheFile: ".$this->cacheFile."<br>";
		#echo "cacheExpireTime: ".$this->cacheExpireTime."<br>";
		#echo "method: ".$this->method[$this->nestedlevel]."<br>";
		#echo "feedsource: ".$this->feedsource[$this->nestedlevel]."<br>";
		#echo "feedfilename: ".$this->feedfilename[$this->nestedlevel]."<br>";
		#echo "postPayload: ".$this->postPayload[$this->nestedlevel]."<br>";
		#echo "header: ".$this->header[$this->nestedlevel]."<br>"; 
		#echo "auth: ".$this->auth[$this->nestedlevel]."<br>"; 
		#echo "postbody: ".$this->postbody[$this->nestedlevel]."<br>";
		#echo "debugLevel: ".$this->debugLevel[$this->nestedlevel]."<br>"; 
		#echo "debugModeIsOn: ".$this->debugModeIsOn[$this->nestedlevel]."<br>"; 
		#echo "urlencodepostpayload: ".$this->urlencodepostpayload."<br>"; 
		#echo "curloptions: ".htmlentities($this->curloptions[$this->nestedlevel])."<br>";
		#echo "httpstatuscodemustbe200: ".$this->httpstatuscodemustbe200[$this->nestedlevel]."<br>";
		#echo "encodingofsource: ".$this->encodingofsource[$this->nestedlevel]."<br>";
		#echo "showapiresponse: ".@$this->showapiresponse[$this->nestedlevel]."<br>";
	 
	  
      $fileLoadWithCacheObj = new FileLoadWithCachePro(
            $this->feedUrl[$this->nestedlevel], $this->urlgettimeout[$this->nestedlevel], $this->isCacheEnable, $this->cacheFile,
            $this->cacheExpireTime, $this->method[$this->nestedlevel], NULL, $this->feedsource[$this->nestedlevel], $this->feedfilename[$this->nestedlevel],
            $this->postPayload[$this->nestedlevel], $this->header[$this->nestedlevel], $this->auth[$this->nestedlevel], $this->postbody[$this->nestedlevel],
            $this->debugLevel[$this->nestedlevel], $this->debugModeIsOn[$this->nestedlevel], $this->urlencodepostpayload, 
			$this->curloptions[$this->nestedlevel], $this->httpstatuscodemustbe200[$this->nestedlevel], $this->encodingofsource[$this->nestedlevel],
			$this->showapiresponse[$this->nestedlevel], FALSE
            );
			
      $fileLoadWithCacheObj->retrieveJsonData();

      if (!($fileLoadWithCacheObj->getAllok()) && $this->displayapireturn[$this->nestedlevel]==0) {
        # loading of JSON failed, errormessage is NOT displayed at failed method
		$outerrmsg = "";
		if (!empty($this->feedUrl[$this->nestedlevel])) {
			#$outerrmsg .= "error loading <a href=".$this->feedUrl[$this->nestedlevel]." target=_blank>JSON</a>: "; # do not show URL to the public in case of timeout 
		}
		$outerrmsg .= $fileLoadWithCacheObj->getErrormsg();
			$this->nestedlevel--;
        return $this->jci_showDebugMessage().$outerrmsg;
      }
      $this->feedData = $fileLoadWithCacheObj->getFeeddata();

      $gotjsontmp = "";
      if (preg_match("/##payloadinputstr##/", $this->feedData)) {
        $outTmp = explode("##payloadinputstr##", $this->feedData);
        $this->jci_collectDebugMessage("payloadinputstr: ".$outTmp[1], 10);
        $gotjsontmp = $outTmp[0];
      } else {
        $gotjsontmp = $this->feedData;
      }
	  
	  
	  ## convert XML 2 JSON
      #if ($this->inputtype[$this->nestedlevel]=="xml" && $gotjsontmp!="") {
		 #$gotjsontmp = $this->convertXML2JSON($gotjsontmp);
      #}
		$csv_delimiter = "";
		$csv_csvline = "";
		$csv_enclosure = "";
		$csv_skipempty = "";
		$csv_escape = "";
	
    if ($this->inputtype[$this->nestedlevel]=="csv" && $gotjsontmp!="") {
	  ## convert CSV 2 JSON - set Param BEGIN
        $this->jci_collectDebugMessage("loading CSV, try to convert to JSON: ".$gotjsontmp, 10);
		$csv_delimiter = ","; #field delimiter (one character only)
		if (!empty($this->inputtypeparamArr[$this->nestedlevel]->delimiter)) {
			$csv_delimiter = $this->inputtypeparamArr_replacePlaceholders($this->inputtypeparamArr[$this->nestedlevel]->delimiter);
			$csv_delimiter = preg_replace("/#TAB#/", "	", $csv_delimiter);
		}
		
		$csv_enclosure = '"'; # enclosure character (one character only)
		if (!empty($this->inputtypeparamArr[$this->nestedlevel]->enclosure)) {
			$csv_enclosure = $this->inputtypeparamArr_replacePlaceholders($this->inputtypeparamArr[$this->nestedlevel]->enclosure);
		}

		$csv_escape = "\\"; # Defaults as a backslash (\)
		if (!empty($this->inputtypeparamArr[$this->nestedlevel]->escape)) {
			$csv_escape = $this->inputtypeparamArr_replacePlaceholders($this->inputtypeparamArr[$this->nestedlevel]->escape);
		}

		$csv_csvline = "\n"; # 
		if (!empty($this->inputtypeparamArr[$this->nestedlevel]->csvline)) {
			$csv_csvline = $this->inputtypeparamArr_replacePlaceholders($this->inputtypeparamArr[$this->nestedlevel]->csvline);
		}

		$csv_skipempty = FALSE;
		if (!empty($this->inputtypeparamArr[$this->nestedlevel]->skipempty) && ("yes" == $this->inputtypeparamArr[$this->nestedlevel]->skipempty)) {
			$csv_skipempty = TRUE;
		}
		$this->buildDebugTextarea("api-answer csv-file:<br>", $gotjsontmp);
		  ## convert CSV 2 JSON - set Param END
	} else {
		# xml or JSON
		$inspurl = "https://jsoneditoronline.org";
		$this->buildDebugTextarea("api-answer:<br>If it's JSON: Copypaste (click in box, Strg-A marks all, then insert into clipboard) the JSON from the following box to <a href=\"".$inspurl."\" target=_blank>".$inspurl."</a>):", $gotjsontmp);
		
		$this->feedData = trim($this->feedData);
		if (preg_match("/^json\_callback/", $this->feedData)) {
			$this->feedData = preg_replace("/^json\_callback\(/", "", trim($this->feedData));
			$this->feedData = preg_replace("/\)\;$/", "", trim($this->feedData));
			$this->feedData = preg_replace("/\'/", "\"", trim($this->feedData));
		}
	}
	
	# build json-array
    if (($this->parser[$this->nestedlevel]=="twig") || ($this->parser[$this->nestedlevel]=="twig243") || ($this->parser[$this->nestedlevel]=="twig3") || ($this->parser[$this->nestedlevel]=="twig332") || ($this->parser[$this->nestedlevel]=="twig332adj")) {
        $content = $this->replaceInTwigCodeInvalidQuotesWithValidQuotes($content);
        ### keys with @ or ! or $
        $this->feedData = $this->twigHandler->maskSpecialCharsInJSON($this->feedData); ### feed-string is masked here to convert it to JSON

        $payloadinputstrPattern = "##payloadinputstr##";
        $payloadinputJson = NULL;
        if (preg_match("/$payloadinputstrPattern/", $this->feedData)) {
			$tmpFeeddataArr = explode($payloadinputstrPattern, $this->feedData);
			$this->feedData = $tmpFeeddataArr[0];
			$payloadinputstr = $tmpFeeddataArr[1];
			$payloadinputJson = json_decode($payloadinputstr, TRUE);
        }

        if ($this->trytohealjson[$this->nestedlevel]) {
			$this->feedData = $this->func_trytohealjson($this->feedData);
			#$this->jci_collectDebugMessage("trytohealjson JSON: ".$this->feedData, 10);
        }
        if ($this->displayapireturn[$this->nestedlevel]>0) {
			# if the api does not return JSON but something else (e.g. an encoded image) with this parameter the api-return can be taken via {{data}} 
			$this->jci_collectDebugMessage("displayapireturn: ".$this->displayapireturn[$this->nestedlevel], 10);
			$apianswer = $this->feedData;
			$apianswer = $this->twigHandler->unMaskSpecialCharsInJSON($apianswer);
			if ($this->displayapireturn[$this->nestedlevel] & 2) {
				$this->jci_collectDebugMessage("displayapireturn: execute base64_encode on api-answer", 10);
				$apianswer = base64_encode($apianswer);
			}
			if ($this->displayapireturn[$this->nestedlevel] & 4) {
				$this->jci_collectDebugMessage("displayapireturn: remove linefeed out of api-answer", 10);
				$apianswer = preg_replace("/\n/", "", $apianswer);
			}
			#$this->jci_collectDebugMessage("JSON: key 'data', value: ".$apianswer, 10);
			$apianswer = preg_replace("/\"/", '\\"', $apianswer);
			if ($this->upload) {
				$this->feedData = '{ "data" : '.$apianswer.'}';
			} else {
				$this->feedData = '{ "data" : "'.$apianswer.'"}';
				$inspurl = "https://jsoneditoronline.org";
				$this->buildDebugTextarea("converted JSON-answer:<br>Inspect JSON: Copypaste (click in box, Strg-A marks all, then insert into clipboard) the JSON from the following box to <a href=\"".$inspurl."\" target=_blank>".$inspurl."</a>):", $this->feedData);
			}
        }
        $this->feedData = $this->twigHandler->unMaskSpecialCharsInJSON($this->feedData);
		
        $jsonDecodeObj = new JSONdecodePro($this->feedData, TRUE, $this->debugLevel[$this->nestedlevel], $this->debugModeIsOn[$this->nestedlevel], $this->convertJsonNumbers2Strings[$this->nestedlevel], $this->cacheFile, $fileLoadWithCacheObj->getContentType(), 
			$this->inputtype[$this->nestedlevel], $csv_delimiter, $csv_csvline,	$csv_enclosure, $csv_skipempty, $csv_escape	);

		$vals = $jsonDecodeObj->getJsondata();
		
        if (!$jsonDecodeObj->getIsAllOk()) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
           if ($errormsg=="") {
			$this->nestedlevel--;
             return $this->jci_showDebugMessage()."JSON-Decoding failed. Check structure and encoding of JSON-data.";
           } else {
			$this->nestedlevel--;
             return $this->jci_showDebugMessage().$errormsg;
           }
        }
        if (isset($payloadinputJson)) {
          $vals["payloadparam"] = $payloadinputJson;
          $this->jci_collectDebugMessage("POST-payloadparam: ".$this->postPayload[$this->nestedlevel], 10);
        }

        if ($valcfp!=NULL) {
          $vals["cfp"] = $valcfp;
        }

      if (!empty($this->param1[$this->nestedlevel])) {
        $vals["param1"] = $this->param1[$this->nestedlevel];
      }
      if (!empty($this->param2[$this->nestedlevel])) {
        $vals["param2"] = $this->param2[$this->nestedlevel];
      }
      if (!empty($this->urlparamval)) {
        $vals["urlparam"] = $this->urlparamval;
      }
      if (isset($vals["urlparam"])) {
        $this->jci_collectDebugMessage("urlparam in JSON, call by 'urlparam.KEY': ".print_r($vals["urlparam"], TRUE), 10);
      }
	  
	  $apiresponseinfo = $fileLoadWithCacheObj->getApiresponseinfo();
	  if (!is_null($apiresponseinfo)) {
		#echo "<hr>apiresponseinfo:<br>";
		#var_Dump($apiresponseinfo);
        $vals["apiresponseinfo"] = $apiresponseinfo;
	  }
	  #var_Dump($apiresponseinfo);
	  
	  
      # add post-data
      if ($this->addpostdata2json) {
        $tmp1 = Array();
			
	$postparam = get_post(); #When output of get_post is OBJECT, a WP_Post instance is returned. NEVER use that in recursive twig-templates: memeory overflow through recursive call of itself
        if ($postparam) {
			$postparam->post_content = preg_replace("/jsoncontentimporterpro/", "", trim($postparam->post_content)); # shortcode in $postparam->post_content are executed when used in twig, result: infinite loop, stopped my "Fatal error: Allowed memory size of ... bytes exhausted..." -> remove "jsoncontentimporterpro" to invalidate shortcode
			$vals["jcipageparam"]["post"] = $postparam;
			$vals["jcipageparam"]["permalink"] = get_permalink();
			$vals["jcipageparam"]["homeurl"] = home_url();
			$vals["jcipageparam"]["currentuser"] = get_current_user_id();
		}
		$this->addpostdata2json = FALSE;
	  }
	  
	    if ($this->addcpf2json && ($this->nestedlevel<=1)) {
            $postparam = get_post();
			#echo "postparam: ".print_r($postparam, TRUE)."<br>";
			if ($postparam) {
				$postId = $postparam->ID;
				if ($postId>0) {
					$custom_fields_arr = get_post_custom($postId);
				} else {
					$custom_fields_arr = array();
				}
				$vals["jcipageparam"]["custom_fields"] = $custom_fields_arr;
				$this->addcpf2json = FALSE; # use this only once in the mother-cpf - not in nested shortcodes
			}
		}

        $val_jci_pro_use_wpautop = get_option('jci_pro_use_wpautop');
        if ($val_jci_pro_use_wpautop==2) {
          $this->jci_collectDebugMessage("twig: use wpautop");
          $postwithbreaks = wpautop( $content, FALSE);
        } else {
          $this->jci_collectDebugMessage("twig: wpautop not used");
          $postwithbreaks = $content;
        }
        $this->datastructure = $postwithbreaks;
        if ($this->datastructure=="") {
			# suggest template: create twig out of JSON
			require_once plugin_dir_path( __FILE__ ) . '/lib/JsonToTwigConverter.php';
			#json-data: check if really JSON
			if (json_decode($this->feedData, TRUE)) {
				# we have JSON
				$j2t = new JsonToTwigConverter($this->feedData);
				$res = $j2t->getTwig();
				$this->datastructure = "<b>the result of this computer-generated code:</b><br>".$res;
				echo "<hr><b>As there was no twig-template defined in the shortcode and plugin-template, an intelligent algorithm created twig-sourcecode:</b><br>Copy paste this to a plugin-template.<br><textarea rows=6 cols=90>$res</textarea><hr>";
			} else {
				# no JSON
				$this->nestedlevel--;
				return $this->jci_showDebugMessage()."<br>Invalid JSON - creating of example twig failed - work on access to API and JSON. Check debugmode! plugin aborted!";
			}
        }

        #$this->jci_collectDebugMessage("twig-template: ".$this->datastructure);
        $this->buildDebugTextarea("twig-template:", $this->datastructure);

        # check template-string
        # twig 1.x: tokenize expects string
        # twig 2.x: tokenize expects instance of Twig_Source
        $ts = NULL;

        #$val_jci_pro_order_of_shortcodeeval = get_option('jci_pro_order_of_shortcodeeval');
        #if ($val_jci_pro_order_of_shortcodeeval=="") {
        #  $val_jci_pro_order_of_shortcodeeval = 1;
        #}
        #if ($val_jci_pro_order_of_shortcodeeval==2) {
        if ($this->orderofshortcodeeval[$this->nestedlevel]==2) {
          $this->jci_collectDebugMessage("twig: eval shortcode in template BEFORE inserting JSON");
          $this->datastructure = do_shortcode($this->datastructure);
        }
		
##################
# elementor template
		#$elementorpagetemplate = @$this->createoptionsArr[$this->nestedlevel]['elementorpagetemplate'];
		if (isset($this->createoptionsArr[$this->nestedlevel]['elementorpagetemplate']) 
			&& (!empty($this->createoptionsArr[$this->nestedlevel]['elementorpagetemplate'])) 
			&& is_numeric($this->createoptionsArr[$this->nestedlevel]['elementorpagetemplate'])) {
			$elsc = '[elementor-template id="'.$this->createoptionsArr[$this->nestedlevel]['elementorpagetemplate'].'"]';
			$elscRes = do_shortcode($elsc);
			$this->datastructure = $elscRes;
		}
##################
		
		$this->feedDataArr = $vals;
		$this->datastructure = preg_replace('/<\\\\\/script>/', '</script>', $this->datastructure); ## v370: re-replace <\/script> with </script>, replaced in jcieditor.php when using the ace editor
		$res = $this->twigHandler->executeTwig($vals, $this->datastructure, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
		
        if (isset($vals["urlparam"]) && is_array($vals["urlparam"])){
          $urlparamStr = json_encode($vals["urlparam"]);
          $this->jci_collectDebugMessage("twig: available urlparam (adress via 'urlparam.NAME_OF_KEY') : ".$urlparamStr);
        }
        
		$inspurl = "https://jsoneditoronline.org";
        $this->buildDebugTextarea("JSON used for twig-template:<br>Inspect JSON: Copypaste (click in box, Strg-A marks all, then insert into clipboard) the JSON from the following box to <a href=\"".$inspurl."\" target=_blank>".$inspurl."</a>):", json_encode($vals));
        
        # execute shortcode in rendered text
        if (
          (@$this->orderofshortcodeeval[@$this->nestedlevel]==1)
          #($val_jci_pro_order_of_shortcodeeval==1)
          && (@$this->mode[$this->nestedlevel]!="create")
          ) {
          #if (!preg_match("/\[jsoncontentimporterpro/", $res)) {  # prevent infinite looping # removed v335
          $this->jci_collectDebugMessage("twig: eval shortcode in template AFTER inserting JSON");
          $res = $this->twigHandler->unMaskSpecialCharsInJSON($res); 
          $res = do_shortcode($res);
          $res = $this->twigHandler->maskSpecialCharsInJSON($res); 
          #} else {
          #  $this->jci_collectDebugMessage("twig: eval shortcode failed: double [jsoncontentimporterpro]");
          #}
        }
        #$this->jci_collectDebugMessage("twig result: $res", 10, "<hr>");
        $res = $this->twigHandler->unMaskSpecialCharsInJSON($res);
        $this->buildDebugTextarea("Twig-result:", $res);


        if (@$this->mode[$this->nestedlevel]=="create") {
# added 3.4.0: create custom post types BEGIN
##################
# cpttype set in shortcode, e. g. slug of Toolset-CPT
		$isToolsetCPTPlugin = FALSE;
		$cpttypeArr = NULL;
		if (isset($this->createoptionsArr[$this->nestedlevel]['toolsetcpt'])) {
			$cpttypeArr = $this->createoptionsArr[$this->nestedlevel]['toolsetcpt'];
		}
		if (empty($cpttypeArr)) {
			$cpttypeArr = $this->createoptionsArr[$this->nestedlevel]['cpt'];
		}
		if(defined( 'TYPES_VERSION' ) ) {
			$isToolsetCPTPlugin = TRUE;
		} else {
			# toolset-pluing types is NOT active
			$isToolsetCPTPlugin = FALSE;
		}
		$useGeneratedCPT = FALSE;
		$useContentFromToolsetLayout = FALSE;
		$cpfdbprefix = "";
		#if ($isToolsetCPTPlugin) {
		#	$cpfdbprefix = "wpcf-";
		#}
		if (is_array($cpttypeArr)) {
			$useGeneratedCPT = TRUE;
			$newPostType = $cpttypeArr[0]["slug"]; 
			$cpf2jsonMatches = array();
			if (!empty($cpttypeArr[0]["matches"])) {
				foreach ( $cpttypeArr[0]["matches"] as $k => $v ) {
					foreach ( $v as $k1 => $v1 ) {
						$v1 = preg_replace("/#QM#/i", "\"", $v1);
						$v1 = preg_replace("/#CBO#/i", "{", $v1);
						$v1 = preg_replace("/#CBC#/i", "}", $v1);
						$v1 = $this->twigcode_replacePlaceholders($v1);
						#echo $k1." $v1<br>";
						$cpf2jsonMatches[$cpfdbprefix.$k1] = $v1;
					}
				}
			}
			#var_Dump($cpf2jsonMatches);
			#exit;
			$newPostSlugname = $newPostType;
			$args = array(  'object_type' => array($newPostType) ); 
			$this->taxonomiesArr = $this->select_Taxonomies_From_CPT($newPostType); # list of taxonomies connected in toolset

            $this->jci_collectDebugMessage("taxonomies of that CPT: ".print_R($this->taxonomiesArr, true));
			$cpt_cpf_list = $this->select_CPF_From_CPT($newPostType, $isToolsetCPTPlugin);
		}

		#use toolset template: not used yet
		/* the connection between toolset-CPT-template is not well documented
		$contenttemplate = @$this->createoptionsArr[$this->nestedlevel]{'contenttemplate'};
		if (!empty($contenttemplate)){
			$content .= $contenttemplate; 
			$this->datastructure = $content;
			$useContentFromToolsetLayout = TRUE;
			$this->jci_collectDebugMessage("contenttemplate used: ".$content);
		}
		$toolset_template_option = get_option('wpv_options', array());
		$postIDWithLayout = $toolset_template_option["views_template_for_".$newPostType];
		if ($postIDWithLayout>0) {
			## no toolset layout plugin
			$toolset_templateObj = get_post($postIDWithLayout); 
			$toolset_layout_content = $toolset_templateObj->post_content;
			$content = $toolset_layout_content;
			$this->datastructure = $content;
			$useContentFromToolsetLayout = TRUE;
			$this->jci_collectDebugMessage("Toolset-Template used: ".$content);
		} else {
			# toolset layout plugin there
			$postIDWithLayout = $this->select_postID_Toolset_ddl_layout($newPostType);
			if (!empty($postIDWithLayout)) {
				$this->jci_collectDebugMessage("Toolset-Template used: ".$postIDWithLayout);
				$toolset_layout = get_post_meta( $postIDWithLayout, '_dd_layouts_settings');
				$toolset_layout_json = json_decode($toolset_layout[0], TRUE);
				## yet the plugin can eval only the first found cell 
				$toolset_layout_cell_0_0 = trim($toolset_layout_json["Rows"][0]["Cells"][0]["content"]["content"]);
				$this->jci_collectDebugMessage("Toolset-Template Layout 0.0: ".$toolset_layout_cell_0_0);
				if (!empty($toolset_layout_cell_0_0)) {
					$content = $toolset_layout_cell_0_0;
					$this->datastructure = $content;
					$useContentFromToolsetLayout = TRUE;
					$this->jci_collectDebugMessage("Toolset-Template Layout 0.0 used: ".$content);
				}
			}
		}
		*/
##################	


		## loop-settings BEGIN
			$this->jci_collectCreateMessage("<hr><b>start creating pages:</b>");
			$loopKey = @$this->createoptionsArr[$this->nestedlevel]['loop'];
			if (empty($loopKey)) {
				# work on single page
				$valsTmp = $vals;
				$this->jci_collectCreateMessage("create page without JSON-loopkey");
			} else if ("#singlepage#"==$loopKey) {
				$this->jci_collectCreateMessage("create ONE page with all JSON we got");
				$valsTmp[] = $vals;
			} else {
				# work on loop   
				$this->jci_collectCreateMessage("create page with JSON-loopkey: ".$loopKey);
				$loopKeyArr = explode(".", $loopKey);
				$valsTmp = $vals;
				foreach ($loopKeyArr as $lk) {
					if (isset($valsTmp[$lk])) {
						$valsTmp = $valsTmp[$lk]; #PHP8 fatal if no such key in JSON
					}
				}
			}

			# use only parts of the loop - begin
			$loopStart = "";
			if (isset($this->createoptionsArr[$this->nestedlevel]['loopstart'])) {
				$loopStart = $this->createoptionsArr[$this->nestedlevel]['loopstart'];
			}
			if (!empty($loopStart) && ($loopStart>=1)) {
				$loopStart--; # array counts from 0 to n, humans from 1 to (n+1)
				$loopEnd = @$this->createoptionsArr[$this->nestedlevel]['loopend'];
				if (!empty($loopEnd) && ($loopEnd>=1)) {
					$valsTmp = array_slice($valsTmp, $loopStart, $loopEnd);
					$this->jci_collectCreateMessage("use only parts of the loop. Start at item ".($loopStart+1)." and end at item $loopEnd");
				} else {
					$valsTmp = array_slice($valsTmp, $loopStart);
					$this->jci_collectCreateMessage("use only parts of the loop. Start at item ".($loopStart+1)." ");
				}
			}
			# use only parts of the loop - end
			
			$json_no_of_cp_to_create = count($valsTmp);
            $this->jci_collectCreateMessage( "no of pages to create: ".$json_no_of_cp_to_create);
			$minimumcptocreate = "";
			if (isset($this->createoptionsArr[$this->nestedlevel]['minimumcptocreate'])) {
				$minimumcptocreate = $this->createoptionsArr[$this->nestedlevel]['minimumcptocreate'];
			}
			if ($minimumcptocreate>0 && $minimumcptocreate > $json_no_of_cp_to_create) {
				$this->jci_collectCreateMessage( "at least $minimumcptocreate datasets must be in the JSON to create CP (to be safe if JSON is not ok): as there are only $json_no_of_cp_to_create the creation process is terminated here.");
				$this->nestedlevel--;
				return $this->jci_showDebugMessage()."Only $json_no_of_cp_to_create JSON-datasets, $minimumcptocreate needed: Generation terminated.\n";
			}
		## loop-setting end
           
			if (empty($this->pageid)) {
				$this->jci_collectCreateMessage("ID of the creating page not available. No extra CPF from there.<br>\n");
				#return $this->jci_showDebugMessage()."Page-ID not available\n";
            }
            #$this->jci_collectCreateMessage("ID of the creating page is ".$this->pageid);
			
		## set CPT to use: either via JCI-pluginoptions created CPT or as reference to Plugins creating CPT (e. g. Toolset)
			if ($useGeneratedCPT) {
				# use a generated CPT (e. g. by Toolset or other plugins)
				$typeOfNewpage = $newPostType; # overwrite 
				$redirectPath = $newPostType;
				$zorbArr = $this->getCustomPageSettingsFromPluginOptions($typeOfNewpage);
				$this->jci_collectCreateMessage( "Toolset pagetype set: ".$typeOfNewpage);
			} else {
				# use CPT set in JCI-plugin options
				$typeOfNewpage = @$this->createoptionsArr[$this->nestedlevel]['type'];
				if ("post"==$typeOfNewpage || "page"==$typeOfNewpage) {
					# use the default wordpress-post or -page
					$redirectPath = ".";
				} else {
					$this->jci_collectCreateMessage( "pagetype: ".$typeOfNewpage." ('type' in 'createoptions' in shortcode must match 'type' in in plugin-settings!)");
					$zorbArr = $this->getCustomPageSettingsFromPluginOptions($typeOfNewpage);
					$redirectPath = @$zorbArr["ptredirect"];
					if (""!=$typeOfNewpage && $typeOfNewpage==@$zorbArr["type"]) {
						$this->jci_collectCreateMessage( "<font color=green>Great! Pagetype ".$typeOfNewpage." defined in plugin-options!</font>");
					} else {
						$this->jci_collectCreateMessage( "<font color=red>Pagetype not defined in plugin-options: <a href=/wp-admin/admin.php?page=unique_jcipro_menu_slug&tab=customposttypes target=_blank>click here</a>!</font><br>");
					}
				}
			}

		## get key of cp
			if ($this->pageid>0) {
				$custom_fields_arr = get_post_custom($this->pageid);
			} else {
				$custom_fields_arr = array();
			}
            # delete previous created pages if flag deleteold is yes
            $deleteOldFlag = @$this->createoptionsArr[$this->nestedlevel]['deleteold'];
		if ($useGeneratedCPT) {
			$nameofthejsonimport = @$cpttypeArr[0]["key"];
			if (empty($nameofthejsonimport)) {
				$nameofthejsonimport = @$this->createoptionsArr[$this->nestedlevel]['cptkey'];
			}
			if (empty($nameofthejsonimport)) {
				$nameofthejsonimport = @$this->createoptionsArr[$this->nestedlevel]['key'];
			}
		} else {
			# if key in the shortcode: use this 
			$minlengthofkey = 5;
			$cptkey = @$this->createoptionsArr[$this->nestedlevel]['cptkey'];
			if (empty($cptkey)) {
				$cptkey = @$this->createoptionsArr[$this->nestedlevel]['key'];
			}
			if (strlen($cptkey)>$minlengthofkey) {
				$nameofthejsonimport = $cptkey;
				$this->jci_collectCreateMessage( "<font color=green>key of this Custom Post Type set to $nameofthejsonimport via the shortcode-key cptkey</font>");
			} else {
				$nameofthejsonimport = @$custom_fields_arr['jci_uniquekey_createpost'][0];  # is key in field?
				if (empty($nameofthejsonimport)) {
					$juktmp = "";
					$this->jci_collectCreateMessage( "Custom Field 'jci_uniquekey_createpost' missing: Try to use 'key' set in the Definition of the CPT in the Plugin-Options.");
					$juktmp = trim(@$zorbArr["key"]);
					if (empty($juktmp)) {
						$this->jci_collectCreateMessage( "<font color=red>key missing: set in the Definition of the CPT in the Plugin-Option key=... OR in the Shortcode at 'createoptions' with \"cptkey\":\"KEYWHATEVER\" </font><hr>");
						$this->nestedlevel--;
						return $this->jci_showDebugMessage();
					} else if (strlen($juktmp)>$minlengthofkey) {
						$nameofthejsonimport = $juktmp;
						$this->jci_collectCreateMessage( "<font color=green>key of this Custom Post Type set to $nameofthejsonimport via the plugin-options</font>");
					} else {
						$this->jci_collectCreateMessage( "<font color=red>key too short (length at least $minlengthofkey): use a longer one</font><hr>");
						$this->nestedlevel--;
						return $this->jci_showDebugMessage();
					}
				} else {
					$this->jci_collectCreateMessage( "<font color=green>Custom Fields 'jci_uniquekey_createpost' set to $nameofthejsonimport: delete pages with that key</font>");
				}
			}
			if (!empty($this->param1[$this->nestedlevel])) {
				$nameofthejsonimport = preg_replace("/##param1##/", $this->param1[$this->nestedlevel], $nameofthejsonimport);
			}
			if (!empty($this->param2[$this->nestedlevel])) {
				$nameofthejsonimport = preg_replace("/##param2##/", $this->param2[$this->nestedlevel], $nameofthejsonimport);
			}
		}
		
		## delete or not or some?
			if ("yes" == $deleteOldFlag) {
				# if all should be deleted, all cpt must have the same key. the md5 removes all twig etc out of the key
				$nameofthejsonimport = md5($nameofthejsonimport);
				$this->deleteCPT( $typeOfNewpage, $nameofthejsonimport );
			} else if ("some" == $deleteOldFlag) {
				$this->jci_collectCreateMessage( "<b>do NOT delete all previous generated pages, but maybe some - depending on the key and JSON-data!</b>");
            } else {
				$this->jci_collectCreateMessage( "<b>do NOT delete any previous generated pages!</b>");
			}

		## status of created CP
			$newPostStatus = "publish";
			if (isset($this->createoptionsArr[$this->nestedlevel]['poststatus'])) {
				$newPostStatus = $this->createoptionsArr[$this->nestedlevel]['poststatus']; # default: publish, other options: https://codex.wordpress.org/Function_Reference/get_post_status
			}
			
		## title of create CP
            $titelRawFull = "";
			if (isset($custom_fields_arr["jci_title_createpost"][0])) {
				$titelRawFull = $custom_fields_arr["jci_title_createpost"][0];
			}
            if (empty($titelRawFull)) {
				$titelRawFull = $this->createoptionsArr[$this->nestedlevel]['title'];
				#$titelRawFull = preg_replace("/#SQM#/", "'", $titelRawFull);
				$titelRawFull = $this->twigcode_replacePlaceholders($titelRawFull);
				$this->jci_collectCreateMessage( "title template from shortcode: ".$titelRawFull);
				#echo "title template from shortcode: ".$titelRawFull."<br>"; exit;
            } else {
				$titelRawFull = $this->twigcode_replacePlaceholders($titelRawFull);
				#$titelRawFull = preg_replace("/#SQM#/", "'", $titelRawFull);
				$this->jci_collectCreateMessage( "title template from custom tags: ".$titelRawFull);
				#echo "title template from custom tags: ".$titelRawFull."<br>";
            }

		## slug of CP
            $slugnameRawFull = "";
			if (isset($custom_fields_arr["jci_slugname_createpost"][0])) {
				$slugnameRawFull = $custom_fields_arr["jci_slugname_createpost"][0];
			}
            if (empty($slugnameRawFull)) {
				$slugnameRawFull = $this->createoptionsArr[$this->nestedlevel]['slugname'];
				$slugnameRawFull = $this->twigcode_replacePlaceholders($slugnameRawFull);
				#$slugnameRawFull = preg_replace("/#SQM#/", "'", $slugnameRawFull);
				$this->jci_collectCreateMessage( "slugname template from shortcode: ".$slugnameRawFull);
            } else {
				#$slugnameRawFull = preg_replace("/#SQM#/", "'", $slugnameRawFull);
				$slugnameRawFull = $this->twigcode_replacePlaceholders($slugnameRawFull);
				$this->jci_collectCreateMessage( "slugname template from custom tags: ".$slugnameRawFull);
            }
			

		## start loop
			$this->jci_collectCreateMessage( "<hr><b>start looping:</b><br>");

########### look into the JSON to detect and count the values of the taxonomies
		$jsonpathtotaxonomy = array();
		if ($useGeneratedCPT) {
			foreach ( $this->taxonomiesArr as $taxonomyByToolsetK => $taxonomyByToolsetV ) {
				@$tmp = trim($cpttypeArr[0]["taxonomiesmatches"][0][$taxonomyByToolsetK]);
				if (!empty($tmp)) {
					$tmp = $this->twigcode_replacePlaceholders($tmp);
					$tsTax = trim($taxonomyByToolsetK);
					## clear taxonomies
					$getTerms = get_terms( $tsTax, array( 'fields' => 'ids', 'hide_empty' => false ) );
					$jsonpathtotaxonomy[$tsTax] = $tmp;
					$keeptaxonomy = trim($cpttypeArr[0]["keeptaxonomies"]);
					#echo "keeptaxonomy: ".$keeptaxonomy."<br>";
					if ("yes"!=$keeptaxonomy) {
						foreach ( $getTerms as $valueTax ) {
							wp_delete_term( $valueTax, $tsTax );
						}
					}
				}
			}
		}
###############################	
		


			$k = 1;
			
			foreach ($valsTmp as $pageitem) {
				if (!is_array($pageitem)) {
					continue;
				}
		
			$pageitem["urlparam"] = "";
			if (isset($vals["urlparam"])) {
				$pageitem["urlparam"] = $vals["urlparam"];
			}
				
				# check if requiredfiles have values
				$requiredfields = "";
				if (isset($this->createoptionsArr[$this->nestedlevel]['requiredfields'])) {
					$requiredfields = $this->createoptionsArr[$this->nestedlevel]['requiredfields'];
				}
				$useDataset = TRUE;
				if (!empty($requiredfields)) {
					$requiredfieldsArr = explode("##", $requiredfields);
					$this->jci_collectCreateMessage("($k) check on required fields: $requiredfields");
					for ($i=0;$i<count($requiredfieldsArr);$i++) {
						if (empty($this->select_JSON_By_Node($pageitem, $requiredfieldsArr[$i]))) {
						#if (!array_key_exists(trim($requiredfieldsArr[$i]), $pageitem)) { # array_key_exists only in the 1st dimension, "a.b.c" key fail then...
							$useDataset = FALSE;
							$this->jci_collectCreateMessage("($k) dataset ignored, missing: ".$requiredfieldsArr[$i]);
						}
					}
				}
				if (!$useDataset) {
					$k++;
					continue;
				}

				## execute twig-code in the key: 
				$keyofitem = $this->twigHandler->executeTwig($pageitem, $nameofthejsonimport, $this->parser[$this->nestedlevel], FALSE);
				$this->jci_collectDebugMessage("($k) key of item: ".$keyofitem, 2, "", FALSE);
				if ( ("some" == $deleteOldFlag) && (""!=$keyofitem)) {
					# execute twig-code in the key:
					$this->deleteCPT( $typeOfNewpage, $keyofitem );
				}
				
				## execute twig in title
				$titelOfNewpage = $this->twigHandler->executeTwig($pageitem, $titelRawFull, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
				$this->jci_collectDebugMessage("($k) execute twig-code in title-template: ".$this->twigHandler->getTwigDebug(), 2, "", FALSE);
				$this->jci_collectCreateMessage("($k) title of created page: $titelOfNewpage", TRUE);
				
				## set slug
				$slugnameOfNewpage = $this->twigHandler->executeTwig($pageitem, $slugnameRawFull, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
				if ($slugnameOfNewpage!="") {
					$slugnameOfNewpage = sanitize_text_field($slugnameOfNewpage);
				}
				$this->jci_collectDebugMessage("($k) execute twig-code in slugname-template: ".$this->twigHandler->getTwigDebug(), 2, "", FALSE);
				$this->jci_collectCreateMessage("($k) slug: $slugnameOfNewpage", TRUE);
				$this->jci_collectCreateMessage("($k) <a href=/".$redirectPath."/".$slugnameOfNewpage." target=_blank>show created page</a>");

				## set category
				$newPostCategoryInputString = "";
				if (isset($this->createoptionsArr[$this->nestedlevel]['categoryids'])) {
					$newPostCategoryInputString = $this->createoptionsArr[$this->nestedlevel]['categoryids'];
				}

				$newPostCategoryArr = array();
				if ($newPostCategoryInputString!="") {
					$this->jci_collectCreateMessage( "($k) Category-IDs for created Page: ".$newPostCategoryInputString, TRUE);
					$newPostCategoryArr = explode(",", $newPostCategoryInputString);
				}
				
					$this->jci_collectCreateMessage( "($k) twig-template 4 page (1st 30 chars): ".substr(htmlentities($this->datastructure), 0, 30));
					$resTmp = $this->twigHandler->executeTwig($pageitem, $this->datastructure, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
					$this->jci_collectDebugMessage("($k) execute twig-code in template: ".$this->twigHandler->getTwigDebug(), 2, "", FALSE);			
				
					if (preg_match("/#posinloop#/", $resTmp)) {
						$resTmp = preg_replace("/#posinloop#/", $k, $resTmp);
						$this->jci_collectCreateMessage( "($k) Insert position in loop into shortcode: ".htmlentities($resTmp));
					}
					$resTmp = $this->twigHandler->unMaskSpecialCharsInJSON($resTmp);
					$this->jci_collectCreateMessage( "($k) content 4 page pre do_shortcode: ".htmlentities($resTmp));

					$save_content = $this->datastructure;
					$resTmp = do_shortcode($resTmp);
					$this->datastructure = $save_content;

					$this->jci_collectDebugMessage("($k) content 4 page after do_shortcode: ".htmlentities($resTmp));
				#}
				
				### set featuredImg
					$featuredImgURL = "";
					$fiArr = array();
					if (isset($this->createoptionsArr[$this->nestedlevel]['featuredimage'])) {
						$fiArr = $this->createoptionsArr[$this->nestedlevel]['featuredimage'];
					}
					$fitmp = "";
					$featuredImgAlt = "";
					$featuredImgSrc = "";
					$attid = 0;
					if (!empty($fiArr) && isset($fiArr[0]["url_json_path"])) {
						if (preg_match("/{/", $fiArr[0]["url_json_path"])) {
							# there is twig-code
							$fiArr[0]["url_json_path"] = $this->twigcode_replacePlaceholders($fiArr[0]["url_json_path"]);   # with that you can use twig with #SQM#-masks for ' etc in the featuredImg-setting
							$fitmpArr = $this->twigHandler->executeTwig($pageitem, $fiArr[0]["url_json_path"], $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
							#echo "TWIG: <br>";
							#var_Dump($fitmpArr);
							$jsonDecodeForMediastore = json_decode($fitmpArr);
							if (!is_null($jsonDecodeForMediastore)) {
								# img stored locally via twig-function mediastore
								if ("ok"==$jsonDecodeForMediastore->jci->status) {
									$fitmp = $jsonDecodeForMediastore->jci->filename;
									$attid = $jsonDecodeForMediastore->jci->attachment_id;
									$atturl = $jsonDecodeForMediastore->jci->attachment_url;
								} else {
									# mediastorage failed
								}
							} else {
								# no array returned, ordinaray non mediastore-twig-command: returnvalue is string with URL
								$fitmp = $fitmpArr;
							}							
						} else if (
							# no twig code, just an URL starting with http or /
							preg_match("/^http/", $fiArr[0]["url_json_path"])
							|| preg_match("/^\//", $fiArr[0]["url_json_path"])
						) {
							$fitmp = $fiArr[0]["url_json_path"];
							#echo "LINK";
						} else {
							# it's a JSON-Node-Path separated by dots
							$jsonUrlPath = explode(".", $fiArr[0]["url_json_path"]);
							$this->jci_collectDebugMessage("($k) json-field with featured image URL: ".print_r($jsonUrlPath, TRUE));
							$node = $pageitem;
							for ($i=0; $i<count($jsonUrlPath); $i++) {
								$node = @$node[$jsonUrlPath[$i]];
							}
							if (is_string($node)) {
								$fitmp = $node; 
							}
							

						}
						$featuredImgAlt = @$fiArr[0]["img_alt"]; 
						$featuredImgSrc = @$fiArr[0]["img_src"]; 
					} else {
						$this->jci_collectDebugMessage('($k) no featured set, shortcode-syntax if needed:  #BRO# {"url_json_path":"detailpage.img", "url_default":"URL_DEFAULT_IMAGE"}#BRC#');
					}
					$this->jci_collectCreateMessage("($k) featured image URL: ".$fitmp);
					
					if (empty($this->check_if_url_is_image($fitmp))) {
						# featured image is not an URL: eitherlocally stored or not existing
						if ($attid>0) {
							# locally stored image
							$featuredImgURL = $atturl;
						} else {
							# not existing, take default image via URL
							$this->jci_collectCreateMessage("($k) URL in JSON for featured image is not an image, try to use url_default: ".$fitmp);
							#default image if there is no in the json
							$featuredImgURL = "";
							if (isset($fiArr[0]["url_default"])) {
								$featuredImgURL = $fiArr[0]["url_default"]; 
							}
							if (empty($featuredImgURL)) {
								$this->jci_collectCreateMessage("($k) No url_default for featured image defined");
							} else {
								$this->jci_collectCreateMessage("($k) url_default used for featured image: <a href=$featuredImgURL target=_blank>Default: ".$featuredImgURL."</a>");
							}
						}
					} else {
						$featuredImgURL = $fitmp; # featured image from json is an image
						if (empty($featuredImgURL)) {
							$this->jci_collectCreateMessage("($k) No data in Json for featured image: <a href=$featuredImgURL target=_blank>".$featuredImgURL."</a>");
						} else {
							$this->jci_collectCreateMessage("($k) Json data used for featured image: <a href=$featuredImgURL target=_blank>".$featuredImgURL."</a>");
						}
					}
					if (empty($featuredImgURL)) {
						$this->jci_collectCreateMessage("($k) no featured image defined");
					}
					
					$postpublishtime = "";
					if (isset($this->createoptionsArr[$this->nestedlevel]['postpublishtime'])) {
						$postpublishtime = trim($this->createoptionsArr[$this->nestedlevel]['postpublishtime']);
					}
					if (!empty($postpublishtime)) {
						$postpublishtime = $this->twigcode_replacePlaceholders($postpublishtime);   # with that you can use twig with #SQM#-masks for ' etc in the featuredImg-setting
						$postpublishtime = $this->twigHandler->executeTwig($pageitem, $postpublishtime, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
					}
					
					$postauthorid = "";
					if (isset($this->createoptionsArr[$this->nestedlevel]['postauthorid'])) {
						$postauthorid = trim($this->createoptionsArr[$this->nestedlevel]['postauthorid']);
					}
					if (!empty($postauthorid)) {
						$postauthorid = $this->twigcode_replacePlaceholders($postauthorid);   # with that you can use twig with #SQM#-masks for ' etc in the featuredImg-setting
						$postauthorid = $this->twigHandler->executeTwig($pageitem, $postauthorid, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
					}
					
					if (!is_numeric($postauthorid)) {
						$postauthorid = "";
					}
								
				## create page
					$idOfNewPost = $this->createPage($k, $typeOfNewpage, $titelOfNewpage, $slugnameOfNewpage, $newPostCategoryArr, $resTmp, $keyofitem, $newPostStatus, $featuredImgURL, $featuredImgAlt, $featuredImgSrc, $postpublishtime, $postauthorid);

				if ($useGeneratedCPT) {
					## add taxonomies to CP
					if (is_Array($jsonpathtotaxonomy)) {
						foreach ( $jsonpathtotaxonomy as $taxonomyK => $taxonomyV ) {
							# loop all taxonomies of the CPT
							$tmpVal = $this->select_JSON_By_Node($pageitem, $taxonomyV);
							$taxonomyValueArr = explode(",", $tmpVal);
							$taxonomyValueArr2Set = array();
							foreach ( $taxonomyValueArr as $taxonomyValueArrItem) {
								#echo $taxonomyValueArrItem."<br>";
								$val = trim($taxonomyValueArrItem);
								if (!empty($val)) {
									array_push($taxonomyValueArr2Set, trim($taxonomyValueArrItem));
								}
							}
							$this->setOneTaxonomiesInCP($idOfNewPost, $taxonomyValueArr2Set, $taxonomyK); # taxonomies
							$this->jci_collectCreateMessage("($k) add term to taxonomy $taxonomyK: ".print_R($taxonomyValueArr2Set, TRUE));
						}
					}

					# fill CPF
					$this->setCPF("toolset", $cpt_cpf_list, $cpf2jsonMatches, $pageitem, $idOfNewPost, $newPostType, $k);
					$this->setCPF("acf", $cpt_cpf_list, $cpf2jsonMatches, $pageitem, $idOfNewPost, $newPostType, $k);
					$this->setCPF("pods", $cpt_cpf_list, $cpf2jsonMatches, $pageitem, $idOfNewPost, $newPostType, $k);
				}

				if (isset($idOfNewPost) && ($idOfNewPost>0)) {
					// add custom fields - BEGIN
					$cf = NULL;
					if (isset($this->createoptionsArr[$this->nestedlevel]['customfields'])) {
						$cf = @$this->createoptionsArr[$this->nestedlevel]['customfields'];
					}

					if (empty($cf)) {
						$this->jci_collectCreateMessage( "($k)".' no extra customfields in shortcode defined. Example: "customfields": #BRO# {"extracustomfield1":"extravalue1"}, {"1#SEP#extracustomfield2":"extravalue2#SQM#SingleQuote#SQM#"}, {"2#SEP#extracustomfield2":"extravalue3"}#BRC#}');
					} else {
						$noofcf = count($this->createoptionsArr[$this->nestedlevel]['customfields']);
						$this->jci_collectCreateMessage( "($k) add ".($noofcf)." custom fields, set by shortcode");
						for ($j=0; $j<$noofcf;$j++) {
							foreach ($this->createoptionsArr[$this->nestedlevel]['customfields'][$j] as $key => $value) {
								$key = $this->twigcode_replacePlaceholders($key);
								#$key = preg_replace("/#SQM#/", "'", $key);
								#$value = preg_replace("/#SQM#/", "'", $value);
								$value = $this->twigcode_replacePlaceholders($value);
								$uniquekey = TRUE;
								if (preg_match("/(.*)#SEP#/i", $key)) {  # identical keys: INTEGER#SEP#KEYNAME
										$keyArr = explode("#SEP#", $key);
										$key = $keyArr[1];
										$uniquekey = FALSE;
								}
								$this->jci_collectCreateMessage( "($k) template for custom field value from shortcode: $key : $value");
								$key = $this->twigHandler->executeTwig($pageitem, $key, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
								$this->jci_collectDebugMessage("($k) execute twig-code in customfield-key $key: ".$this->twigHandler->getTwigDebug(), 2, "", FALSE);

								$value = $this->twigHandler->executeTwig($pageitem, $value, $this->parser[$this->nestedlevel], $this->maskspecialcharsinjsonFlag[$this->nestedlevel]);
								$this->jci_collectDebugMessage("($k) execute twig-code in customfield-value $value: ".$this->twigHandler->getTwigDebug(), 2, "", FALSE);
								$value = $this->twigHandler->unMaskSpecialCharsInJSON($value);
								$cfcreateflag = add_post_meta($idOfNewPost, $key, $value, $uniquekey);
								if ($cfcreateflag) {
									$this->jci_collectCreateMessage( "($k) Success: add custom field value from shortcode to page $idOfNewPost: $key : $value");
								} else {
									$this->jci_collectCreateMessage( "($k) Failed: add custom field value from shortcode to page $idOfNewPost: $key : $value");
								}
							}
						}
					}
					
					# if FacetWP is used: https://facetwp.com/how-to-trigger-the-indexer-programmatically/
					if ( function_exists( 'FWP' ) ) {
						FWP()->indexer->index( $idOfNewPost );
						$this->jci_collectDebugMessage( "($no) FacetWP Plugin active: indexed started" );
					}
					# FacetWP END

				}
				


				
				// add custom fields - END
				$this->jci_collectCreateMessage( "<hr>");
				$k++;
			}
			
			# if FacetWP is used: https://facetwp.com/how-to-trigger-the-indexer-programmatically/
			#if ( function_exists( 'FWP' ) ) {
			#	FWP()->indexer->index();
			#	$this->jci_collectDebugMessage( "($no) FacetWP Plugin active: indexed started" );
			#}
			# FacetWP END
			
			$this->nestedlevel--; # return to parent shortcode
			return $this->jci_showDebugMessage();
			# added 3.4.0: create custom post types END
        } else {
			$res = preg_replace("/#BRO#/i", "[", $res);
			$res = preg_replace("/#BRC#/i", "]", $res);
			$res = preg_replace("/#CBO#/i", "{", $res);
			$res = preg_replace("/#CBC#/i", "}", $res);
			$this->nestedlevel--; # return to parent shortcode
			return $this->jci_showDebugMessage().$res;
        }

      } else {
        $payloadinputstrPattern = "##payloadinputstr##";
        if (preg_match("/$payloadinputstrPattern/", $this->feedData)) {
          $tmpFeeddataArr = explode($payloadinputstrPattern, $this->feedData);
          $this->feedData = $tmpFeeddataArr[0];
        }

        $jsonDecodeObj = new JSONdecodePro($this->feedData, FALSE, $this->debugLevel[$this->nestedlevel], $this->debugModeIsOn[$this->nestedlevel], $this->convertJsonNumbers2Strings[$this->nestedlevel], $this->cacheFile);
        $this->jsondata = $jsonDecodeObj->getJsondata();
        if (!$jsonDecodeObj->getIsAllOk()) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
			$this->nestedlevel--; # return to parent shortcode
           if ($errormsg=="") {
             return $this->jci_showDebugMessage()."JSON-Decoding failed. Check structure and encoding if JSON-data.";
           } else {
             return $this->jci_showDebugMessage().$errormsg;
           }
        }
        if ($sortorderisup=="yes") {
          $this->sortorderIsUp = TRUE;
        } else {
          $this->sortorderIsUp = FALSE;
        }
        if ($sorttypeisnatural=="yes") {
          $this->sorttypeIsNatural = TRUE;
        } else {
          $this->sorttypeIsNatural = FALSE;
        }
        $this->sortField = $sortfield;
	     if (!empty($this->sortField)) {
         usort($this->jsondata, array($this, 'sortfunc'));
        }
        # sorting JSON - END

        # filtering JSON - BEGIN
        if(!empty($this->filterresultsin)) {
          $this->jsondata = $this->filterJSON($this->jsondata, $this->filterresultsin, "resultin");
        }
        if(!empty($this->filterresultsnotin)) {
          $this->jsondata = $this->filterJSON($this->jsondata, $this->filterresultsnotin, "resultnotin");
        }
        # filtering JSON - END

        $val_jci_pro_use_wpautop = get_option('jci_pro_use_wpautop');
        if ($val_jci_pro_use_wpautop==2) {
          $this->jci_collectDebugMessage("JCI-parser: use wpautop");
          $postwithbreaks = wpautop( $content, FALSE);
        } else {
          $postwithbreaks = $content;
          $this->jci_collectDebugMessage("JCI-parser: wpautop not used");
        }
        $this->datastructure = preg_replace("/\n/", "", $postwithbreaks);

        require_once plugin_dir_path( __FILE__ ) . '/class-json-parser-pro.php';
        $this->jci_collectDebugMessage("JCI-parser loaded");

        #$val_jci_pro_order_of_shortcodeeval = get_option('jci_pro_order_of_shortcodeeval');
        #if ($val_jci_pro_order_of_shortcodeeval=="") {
        #  $val_jci_pro_order_of_shortcodeeval = 1;
        #}
        #if (($val_jci_pro_order_of_shortcodeeval==2)
        if (($this->orderofshortcodeeval[$this->nestedlevel]==2) 
          && ($this->mode[$this->nestedlevel]!="create")
        ){
          if (!preg_match("/\[jsoncontentimporterpro/", $this->datastructure)) {  # prevent infinite looping
            $this->jci_collectDebugMessage("eval shortcode in template BEFORE inserting JSON");
            $this->datastructure = do_shortcode($this->datastructure);
          } else {
            $this->jci_collectDebugMessage("jci: eval shortcode failed: double [jsoncontentimporterpro]");
          }
        }

        $this->buildDebugTextarea("JCI template:", $this->datastructure);
         $JsonContentParserPro = new JsonContentParserPro($this->jsondata, $this->datastructure, $this->basenode, $this->numberofdisplayeditems,
            $this->oneofthesewordsmustbein, $this->oneofthesewordsmustbeindepth,
            $this->requiredfieldsandvalues, $this->requiredfieldsandvaluesdepth,
            $this->requiredfieldsandvalueslogicandbetweentwofields,
            $this->oneofthesewordsmustnotbein, $this->oneofthesewordsmustnotbeindepth,
            $this->hidedisplayflag, $this->loopWithoutSubloop, $this->delimiter, $this->param1[$this->nestedlevel], $this->param2[$this->nestedlevel]
            );
        $retval = $JsonContentParserPro->retrieveDataAndBuildAllHtmlItems();
        if (($this->orderofshortcodeeval[$this->nestedlevel]==1)
        #if (($val_jci_pro_order_of_shortcodeeval==1)
          && ($this->mode[$this->nestedlevel]!="create")
          ) {
          if (!preg_match("/\[jsoncontentimporterpro/", $retval)) {  # prevent infinite looping
            $this->jci_collectDebugMessage("eval shortcode in template AFTER inserting JSON");
            $retval = do_shortcode($retval);
          } else {
            $this->jci_collectDebugMessage("jci: eval shortcode failed: double [jsoncontentimporterpro]");
          }
        }
        if ($retval=="errorjsonstruc") {  # failed json-parsing
          $this->jci_collectDebugMessage("JCI-parser result: failed json-parsing", 10, "<hr>");
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
          if ($errormsg=="") {
			$this->nestedlevel--; # return to parent shortcode
            return $this->jci_showDebugMessage()."JSON decoding failed: Is input-JSON valid? Check please!<hr>";
          }
          #$retval = do_shortcode($retval);  why here?
			$this->nestedlevel--; # return to parent shortcode
			return $this->jci_showDebugMessage().$errormsg;
        }
        $this->buildDebugTextarea("JCI-parser result:", $retval, TRUE);
		$this->nestedlevel--; # return to parent shortcode
        return $this->jci_showDebugMessage().$retval;
		}
		$this->nestedlevel--; # return to parent shortcode
	}
	
	
	public function convertXML2JSON($inputXML) {
        $inputXML = str_replace("<soap:Body>","",$inputXML);
        $inputXML = str_replace("</soap:Body>","",$inputXML);
        $this->jci_collectDebugMessage("loading XML, try to convert to JSON: ".$inputXML, 10);
        $xml = simplexml_load_string($inputXML, "SimpleXMLElement", LIBXML_NOCDATA);
        $tmpFeedData = json_encode($xml);
        if ($tmpFeedData!="") {
          #$this->feedData = $tmpFeedData;
          $inputXML = $tmpFeedData;
        }
		return $inputXML;
    }
	
	
	private function setCPF($typeOfCPF, $cpt_cpf_list, $cpf2jsonMatches, $pageitem, $idOfNewPost, $slugofpage, $k) {
				# loop all CPF of the CPT
				if (is_array($cpt_cpf_list[$typeOfCPF])) {
					# CPF made by $typeOfCPF
					foreach ( $cpt_cpf_list[$typeOfCPF] as $cfk => $cfkv ) {
						#echo "$typeOfCPF-CPF: $cfk<br>";
						$cpfdbprefix = "";
						if ("toolset"==$typeOfCPF) {
							$cpfdbprefix = "wpcf-";
						}
						$cfkTmp = preg_replace("/^$cpfdbprefix/", "", $cfk);
						if (!empty($cpf2jsonMatches[$cfkTmp])) {
							$jsonpath = $cpf2jsonMatches[$cfkTmp];
							
#							if (1==2 && @preg_match("/#path#/", $cpf2jsonMatches[$cfkTmp])) {
							if (is_array($cpf2jsonMatches[$cfkTmp])) {
								# fill multiple CPF with an JSON-array: path to JSON-array-node and fieldname in the node needed
								# json-syntax: "performances": {"path":"performances", "valuefield":"id"}
								#var_Dump($cpf2jsonMatches[$cfkTmp]);
								$node2Check = $cpf2jsonMatches[$cfkTmp]["path"];
								$valueNode = $cpf2jsonMatches[$cfkTmp]["valuefield"];
								$selNode = $this->select_JSON_By_Node($pageitem, $node2Check);
								if ($selNode==$node2Check) {
									# if the data is in an array-list, valuefield should be empty (not needed when list), and path gives the name of  {"path":"NODENAME", "valuefield":""}
									# if there is no such array ($selNode==$node2Check) is true, then set $selNode to NULL: do not add a CPF
									$selNode = "";
								} else {
									$selNodeJSON = json_encode($selNode);
									$jsonpath = 'JSON-node "'.$node2Check.'<br>", JSON-field: '.$valueNode;
									$this->jci_collectCreateMessage("($k) multiple CustomPostField filled by JSON-array at node $node2Check with $valueNode from ".$selNodeJSON);
								}
							} else {
								$valueNode = "";
								$selNode = $this->select_JSON_By_Node($pageitem, $cpf2jsonMatches[$cfkTmp]);
							}
							if (!empty($selNode)) {
								if ("toolset"==$typeOfCPF) {
									$errorlevel = $this->addOneCustomFieldInCP($idOfNewPost, $selNode, $cfk, $k, $valueNode);
								}
								if ("acf"==$typeOfCPF) {
									if(class_exists('ACF')) {
										$errorlevel = update_field($cfk, $selNode, $idOfNewPost);
									}
								}
								if ("pods"==$typeOfCPF) {
									if(class_exists('PodsAPI')){
										$pod = pods($slugofpage, $idOfNewPost);
										$data = array(
											$cfk  => $selNode,
										);
										$errorlevel = $pod->save($data);
									}
								}
								$el = "success";
								if (!@$errorlevel) {
									$el = "FAILED";
								}
								$this->jci_collectCreateMessage("($k) $el - fill $typeOfCPF-CustomPostField: $cfk, Pattern: ".$jsonpath.", value: ".print_R($selNode, TRUE));
							}
						}
					}
				}
	}


	private function select_JSON_By_Node($jsonArr, $jsonPath){
		if (preg_match("/{{/", $jsonPath)) { # execute twig-code 
			if(!class_exists('doJCITwig')){
				$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
				require_once $inc;
				$this->twigHandler = new doJCITwig($this->parser[$this->nestedlevel], TRUE);
			}
			$twigResult = $this->twigHandler->executeTwig($jsonArr, $jsonPath, $this->parser[$this->nestedlevel], TRUE);
			return $twigResult;
		}
		$jsonUrlPath = explode(".", $jsonPath);
		$node = $jsonArr;
		for ($i=0; $i<count($jsonUrlPath); $i++) {
			$node = @$node[$jsonUrlPath[$i]];
		}
		if (empty($node)) {
			# not a node but a ordinary string
			return $jsonPath;
		}
		return $node;
	}

	private function select_Taxonomies_From_CPT($cpttype){
		$foundTaxonomies = array();
	
		$taxList = get_object_taxonomies($cpttype); 
		#echo print_r($taxList, true); #exit;
		foreach ($taxList as $taxonomyno => $taxonomyname) {
			@$foundTaxonomies[$taxonomyname]++;
		}
		#var_Dump($foundTaxonomies);
		#exit;
		
		/*
		## toolset-plugin: https://toolset.com/de/
		$custom_taxonomies = get_option('wpcf-custom-taxonomies', array()); # this works for the toolset-plugin
		foreach ($custom_taxonomies as $taxonomy => $data) {
			$taxFlag = $data["supports"][$cpttype]; # ok for toolset-plugin
			if (1==$taxFlag) {
				$foundTaxonomies{$taxonomy}++;
			}
		}
		
		## cptui-plugin: https://de.wordpress.org/plugins/custom-post-type-ui/
		$custom_taxonomies = get_option('cptui_taxonomies', array()); # this works for the toolset-plugin
		foreach ($custom_taxonomies as $taxonomy => $data) {
			$cpt_list_4tax = $data["object_types"]; # ok for cptUI-Plugin
			#	echo print_r($cpt_list_4tax, true)."<br>"; 
			foreach ($cpt_list_4tax as $tax => $cptname) {
				if ($cpttype==$cptname) {
					$foundTaxonomies{$taxonomy}++;
					#echo "cpttype: $tax $cpttype<br>";
				}
			}
		}
		*/
		#echo "<br>cpttype: $cpttype<br>foundTaxonomies: ".print_r($foundTaxonomies, true); exit;
		return $foundTaxonomies;
	}


	/* not used yet
	private function select_postID_Toolset_ddl_layout($cpttype){
		# if toolset-layout-plugin is in use (soon deprecated!)
		global $wpdb;
		$query = "SELECT $wpdb->postmeta.post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = '_ddl_post_types_was_batched' 
			AND $wpdb->postmeta.meta_value LIKE '%\"".$cpttype."\"%'";
		#echo $query."<hr>";
		$this->jci_collectDebugMessage("Toolset-Template select_postID_Toolset_ddl_layout: ".$query);
		$fieldsArr = @$wpdb->get_col($wpdb->prepare($query, $cpttype));
		$fields = @$fieldsArr[0];
		$this->jci_collectDebugMessage("Toolset-Template select_postID_Toolset_ddl_layout: $fields");
		return $fields;
	}
	*/

	private function select_CPF_From_CPT($cpttype, $isToolsetCPTPlugin){
		$cpfAll = array();
		$cpfACF = $this->select_CPF_From_CPT_ACF($cpttype);
		$cpfACFArr = array("acf" => $cpfACF);
		#var_Dump($cpfACF);
		if ($isToolsetCPTPlugin) {
			# toolset
			$cpfToolsetPlugin = $this->select_CPF_From_CPT_Toolset($cpttype);
			$cpfToolsetPluginArr = array("toolset" => $cpfToolsetPlugin);
		} else {
			$cpfToolsetPluginArr = array("toolset" => NULL);			
		}
		# pods
		$cpfPodsPlugin = $this->select_CPF_From_CPT_Pods($cpttype);
		$cpfPodsPluginArr = array("pods" => $cpfPodsPlugin);
		$cpfAll = array_merge($cpfToolsetPluginArr, $cpfACFArr, $cpfPodsPluginArr);
		#var_Dump($cpfAll); exit;
		return $cpfAll;
	}
	
	private function select_CPF_From_CPT_ACF($cpttype){
        global $wpdb;
        $result = $wpdb->get_results(
                  "SELECT $wpdb->posts.ID FROM $wpdb->posts,$wpdb->postmeta WHERE post_type LIKE '%acf%'
                    AND $wpdb->posts.ID = $wpdb->postmeta.post_id AND post_content LIKE '%$cpttype%'"
				, ARRAY_A);

		$resultOut = array();
		foreach($result as $k => $v){
			@$resultOut[$v["ID"]]++;
		}

		$acfArr = array();
		foreach ($resultOut as $k => $v) {
			# get acf-fields
			$result = $wpdb->get_results(
                "SELECT post_excerpt FROM $wpdb->posts WHERE post_parent = $k AND post_type='acf-field'", 
				ARRAY_A);
			$acfArr = array_merge($acfArr, $result);
		}
		$acfArrOut = array();
		foreach ($acfArr as $k => $v) {
			@$acfArrOut[$v["post_excerpt"]]++;
		}
		return $acfArrOut;
	}


	

	private function select_CPF_From_CPT_Toolset($cpttype){
		global $wpdb;
		$query = "SELECT $wpdb->postmeta.meta_value FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = '_wp_types_group_fields' 
			AND $wpdb->postmeta.post_id in 
			     (SELECT $wpdb->postmeta.post_id FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = '_wp_types_group_post_types' AND $wpdb->postmeta.meta_value LIKE '%,$cpttype,%')
			";
		$fieldsArr = $wpdb->get_col(
			$query
			);
			
		if (0==count($fieldsArr)) {
			return NULL;
		}
		$fields = $fieldsArr[0];
		
		$fArr = explode(",", $fields);
		$CPTfield = array();
		foreach( $fArr as $f) {
			if (!empty($f)) {
				@$CPTfield["wpcf-".$f]++;
			}
		}
		return $CPTfield;
	}
	
	private function select_CPF_From_CPT_Pods($cpttype){
		## https://wordpress.stackexchange.com/questions/260442/how-to-get-all-custom-fields-of-any-post-type
		if(!class_exists('PodsAPI')){
			return NULL;
		}
		$podsAPI = new PodsAPI();
		try {
			$pod = $podsAPI->load_pod( array( 'name' => $cpttype ));
		} catch (Exception $e) {
			return NULL;
		}
		$CPTfield = array();
		if (isset($pod['fields'])) {
			foreach($pod['fields'] as $key => $value) {
				$keyuse = trim($key);
				if (!empty($key)) {
					if (isset($CPTfield[$key])) {
						$CPTfield[$key]++;
					} else {
						$CPTfield[$key] = 1;
					}
				}
			}
		}
		return $CPTfield;
	}
	
	private function setOneTaxonomiesInCP($idOfPost, $taxonomyValue, $taxonomyName) {
			$elv = wp_set_object_terms($idOfPost, $taxonomyValue, $taxonomyName);
			return $elv;
	}
	
	
}
?>