<?php
/*
Description: Class to extend WP_Widget
Version: 3.0.0
Author: Bernhard Kux
Author URI: https://json-content-importer.com
*/

class jci_widget_plugin extends WP_Widget {

	private $debugModeIsOn = FALSE;
	private $debugLevel = FALSE;

	/* constructor */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'jci_pro_widget_plugin',
			'description' => 'Import and display JSON-data as widget in footer or sidebar'
         );
		$val_jci_pro_debugmode = get_option('jci_pro_debugmode');
		$this->debugLevel = $val_jci_pro_debugmode;
		if ($val_jci_pro_debugmode>1) {
			$this->debugModeIsOn = TRUE;
		}
		parent::__construct(
			false,
			'JSON Content Importer Pro Widget',
			$widget_ops 
		);
	}

	public function jci_widget_plugin() {
		self::__construct();
	}

	public function jci_showDebugMessage($debugMessage, $debugLevel=2, $suffix="") {
		if (!$this->debugModeIsOn) {
			return "";
		}
		if ($this->debugLevel-$debugLevel<0) {
			return "";
		}
		if ($debugMessage=="") {
			return "";
		}
		echo "<i>DEBUG: ";
		$dm = htmlentities(utf8_encode($debugMessage), ENT_QUOTES, "UTF-8");
		if ($dm=="") {
			echo $debugMessage;
		} else {
			echo $dm;
		}
		echo "</i>\n";
		if ($suffix=="") {
			echo "<br>";
		} else {
			echo $suffix;
		}
    }

	/* widget form creation */
	function form($instance) {
		// Check values
		if( $instance) {
			$title = esc_attr($instance['title']);
			$textarea = esc_textarea($instance['textarea']);
			$json_url =  esc_textarea($instance['json_url']);
			$json_urlgettimeout =  esc_textarea($instance['json_urlgettimeout']);
			$json_basenode =   esc_textarea($instance['json_basenode']);
			$json_numberofitems =  esc_textarea($instance['json_numberofitems']);
			$extratext =  esc_textarea($instance['extratext']);
			$cacheflag =  esc_textarea($instance['cacheflag']);
			$cachetime =  esc_textarea($instance['cachetime']);
			$parserflag =  esc_textarea($instance['parserflag']);
		} else {
			$title = '';
			$textarea = '';
			$json_url =  '';
			$json_urlgettimeout =  5; # 5 sec
			$json_basenode = '';
			$json_numberofitems = '';
			$extratext = '';
			$cacheflag = '';
			$cachetime = '';
			$parserflag = '';
		}
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'jci_pro_widget_plugin'); ?> (optional):</label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('json_url'); ?>"><?php _e('JSON URL', 'jci_pro_widget_plugin'); ?> (mandatory):</label>
<input class="widefat" id="<?php echo $this->get_field_id('json_url'); ?>" name="<?php echo $this->get_field_name('json_url'); ?>" type="text" value="<?php echo $json_url; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('json_urlgettimeout'); ?>"><?php _e('JSON URL Timeout', 'jci_pro_widget_plugin'); ?> (if empty default 5 seconds):</label>
<input class="widefat" id="<?php echo $this->get_field_id('json_urlgettimeout'); ?>" name="<?php echo $this->get_field_name('json_urlgettimeout'); ?>" type="text" value="<?php echo $json_urlgettimeout; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('json_basenode'); ?>"><?php _e('JSON basenode', 'jci_pro_widget_plugin'); ?> (optional):</label>
<input class="widefat" id="<?php echo $this->get_field_id('json_basenode'); ?>" name="<?php echo $this->get_field_name('json_basenode'); ?>" type="text" value="<?php echo $json_basenode; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('json_numberofitems'); ?>"><?php _e('Number of displayed items', 'jci_pro_widget_plugin'); ?> (optional):</label>
<input class="widefat" id="<?php echo $this->get_field_id('json_numberofitems'); ?>" name="<?php echo $this->get_field_name('json_numberofitems'); ?>" type="text" value="<?php echo $json_numberofitems; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Template', 'jci_pro_widget_plugin'); ?> (mandatory):</label>
<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
</p>
<p>
<label for="<?php echo $this->get_field_id('extratext'); ?>"><?php _e('Extratext after data', 'jci_pro_widget_plugin'); ?> (optional):</label>
<textarea class="widefat" id="<?php echo $this->get_field_id('extratext'); ?>" name="<?php echo $this->get_field_name('extratext'); ?>"><?php echo $extratext; ?></textarea>

<p>
<label for="<?php echo $this->get_field_id('cacheflag'); ?>"><?php _e('Cache of JSON URL', 'jci_pro_widget_cacheflag'); ?> (default: caching off):</label>
<br>Check for switching caching on:
<input class="widefat" id="<?php echo $this->get_field_id('cacheflag'); ?>" name="<?php echo $this->get_field_name('cacheflag'); ?>" type="checkbox" value="cacheon"
<?php if ($cacheflag=="cacheon") { echo " checked "; } ?>" />
<br>Cachetime in seconds: <input class="widefat" id="<?php echo $this->get_field_id('cachetime'); ?>" name="<?php echo $this->get_field_name('cachetime'); ?>" type="text" value="<?php echo $cachetime; ?>" />
</p>
<p><br>Use twig-templateengine (default is JCI-templateengine):
<input class="widefat" id="<?php echo $this->get_field_id('parserflag'); ?>" name="<?php echo $this->get_field_name('parserflag'); ?>" type="checkbox" value="usetwig"
<?php if ($parserflag=="usetwig") { echo " checked "; } ?>" />
</p>
<?php
}

// update widget
function update($new_instance, $old_instance) {
  $instance = $old_instance;
  // Fields
  $instance['title'] = strip_tags($new_instance['title']);
  $instance['extratext'] = $new_instance['extratext'];
  $instance['textarea'] = $new_instance['textarea'];
  $instance['json_url'] = strip_tags($new_instance['json_url']);
  $instance['json_urlgettimeout'] = strip_tags($new_instance['json_urlgettimeout']);
  $instance['json_basenode'] = strip_tags($new_instance['json_basenode']);
  $instance['json_numberofitems'] = strip_tags($new_instance['json_numberofitems']);
  $instance['cachetime'] = strip_tags($new_instance['cachetime']);
  $instance['cacheflag'] = strip_tags($new_instance['cacheflag']);
  $instance['parserflag'] = strip_tags($new_instance['parserflag']);
  return $instance;
}

// display widget
function widget($args, $instance) {
	$checkLicence = edd_jcipro_check_license();
	if ($checkLicence!=-1) {
		echo '<div class="widget-text wp_widget_plugin_box">';
		echo $checkLicence;
		echo '</div>';
		return FALSE;
	}
	extract( $args );
	// these are the widget options
	$title = apply_filters('widget_title', $instance['title']);
	$textarea = $instance['textarea'];
	$extratext = $instance['extratext'];
	$json_url = $instance['json_url'];
	$json_urlgettimeout = $instance['json_urlgettimeout'];
	$json_basenode = $instance['json_basenode'];
	$json_numberofitemsIn = $instance['json_numberofitems'];
	if (is_numeric($json_numberofitemsIn)) {
		$json_numberofitems = $json_numberofitemsIn;
	} else {
		$json_numberofitemsArr = explode(" ", trim($json_numberofitemsIn));
		if (is_numeric(trim($json_numberofitemsArr[0]))) {
			$json_numberofitems = trim($json_numberofitemsArr[0]);
		} else {
			$json_numberofitems = -1;
		}
	}
	$cachetime = $instance['cachetime'];
	$cacheflag = $instance['cacheflag'];
	$parserflag = $instance['parserflag'];

	/* BEGIN: no widget fields yet, to be implemented in future version */
	$oneOfTheseWordsMustBeIn = "";
	$oneOfTheseWordsMustBeInDepth = "";
	$requiredFieldsAndValues    = "";
	$requiredFieldsAndValuesDepth = "";
	$requiredfieldsandvalueslogicandbetweentwofields = "";
	$hidedisplayflag = "";
	$loopWithoutSubloop = ""; # ==FALSE
	$oneOfTheseWordsMustNotBeIn  = "";
	$oneOfTheseWordsMustNotBeInDepth  = "";
	$delimiter = "##";
	$httpstatuscodemustbe200 = TRUE;
	/* END */

	echo $before_widget;
	// Display the widget
	echo '<div class="widget-text wp_widget_plugin_box">';

	$JsonContentImporterWidget = new JsonContentImporterWidget($json_url, $json_urlgettimeout, $json_basenode, $json_numberofitems, $textarea, $cacheflag, $cachetime,
        $oneOfTheseWordsMustBeIn, $oneOfTheseWordsMustBeInDepth,
        $requiredFieldsAndValues, $requiredFieldsAndValuesDepth,
        $requiredfieldsandvalueslogicandbetweentwofields,
        $oneOfTheseWordsMustNotBeIn, $oneOfTheseWordsMustNotBeInDepth,
        $hidedisplayflag, $loopWithoutSubloop, $delimiter,
        $this, $parserflag, $httpstatuscodemustbe200
    );

	// Check if title is set
	if ( $title!="" ) {
		echo $before_title . $title . $after_title;
	}
	echo $JsonContentImporterWidget->getOutputHTML();
	echo $extratext;
	echo '</div>';
	echo $after_widget;
}

}



class JsonContentImporterWidget {
    /* shortcode-params */
    private $numberofdisplayeditems = -1; # -1: show all
		private $feedUrl;
    private $json_urlgettimeout = 5;  #5 sec
    private $basenode = "";

    /* plugin settings */
    private $cacheEnable = FALSE;

    /* internal */
	private $cacheFile = "";
	private $jsondata;
	private $feedData  = "";
 	private $cacheFolder;
    private $cachetime = 0;
    private $cacheflag = "";
    private $datastructure = "";
    private $triggerUnique = NULL;
    private $outputHTML = "";
    private $cacheWritesuccess = "";
    private $cacheExpireTime = 0;
    private $postbody = "";
    private $oneOfTheseWordsMustBeIn   = "";
    private $oneOfTheseWordsMustBeInDepth = "";
    private $requiredFieldsAndValues     = "";
    private $requiredFieldsAndValuesDepth = "";
    private $oneOfTheseWordsMustNotBeIn    = "";
    private $oneOfTheseWordsMustNotBeInDepth = "";

    private $requiredfieldsandvalueslogicandbetweentwofields = "";
    private $hidedisplayflag = "";
    private $loopWithoutSubloop = "";
    private $delimiter = "##";
    private $widgetClass = "##";

    private $param1 = "";
    private $param2 = "";

    private $header = "";
    private $auth = "";

    private $parserflag = ""; # JCI-parser or twig
    /* TWIG vars BEGIN */
    private $twig_environment_settings = NULL;
		private $twig_loader = NULL;
		private $twig_environment = NULL;
    private $httpstatuscodemustbe200 = TRUE;
    /* TWIG vars END */


	public function __construct($json_url, $json_urlgettimeout, $json_basenode, $json_numberofitems, $textarea, $cacheflag, $cachetime,
        $oneOfTheseWordsMustBeIn, $oneOfTheseWordsMustBeInDepth,
        $requiredFieldsAndValues, $requiredFieldsAndValuesDepth,
        $requiredfieldsandvalueslogicandbetweentwofields,
        $oneOfTheseWordsMustNotBeIn, $oneOfTheseWordsMustNotBeInDepth,
        $hidedisplayflag, $loopWithoutSubloop, $delimiter, $widgetClass, $parserflag, $httpstatuscodemustbe200
    ){
      $this->feedUrl = $json_url;
      $this->httpstatuscodemustbe200 = $httpstatuscodemustbe200;
      $this->json_urlgettimeout = $json_urlgettimeout;
      $this->basenode = $json_basenode;
      $this->widgetClass = $widgetClass;
      $this->widgetClass->jci_showDebugMessage("construct JsonContentImporterWidget: url=".$this->feedUrl." / basenode=".$this->basenode);
      if (is_numeric($json_numberofitems) && $json_numberofitems>=0) {
        $this->numberofdisplayeditems = $json_numberofitems;
      }
      $this->datastructure = $textarea;
      if (is_numeric($cachetime) && $cachetime>0) {
        $this->cachetime = $cachetime;
      }
      if (!empty($delimiter)) {
        $this->delimiter = $delimiter;
      }
      $this->cacheflag = $cacheflag;
      $this->parserflag = $parserflag;
      $this->cacheExpireTime = time() - $this->cachetime; # 60 sec cachtime ;#string time, [int now])strtotime(date('Y-m-d H:i:s'  , strtotime(" -".$cacheTime." " . $format )));

      $this->oneOfTheseWordsMustBeIn = $oneOfTheseWordsMustBeIn;
      $this->oneOfTheseWordsMustBeInDepth = $oneOfTheseWordsMustBeInDepth;
      $this->requiredFieldsAndValues = $requiredFieldsAndValues;
      $this->requiredFieldsAndValuesDepth = $requiredFieldsAndValuesDepth;
      $this->oneOfTheseWordsMustNotBeIn = $oneOfTheseWordsMustNotBeIn;
      $this->oneOfTheseWordsMustNotBeInDepth = $oneOfTheseWordsMustNotBeInDepth;

      $this->requiredfieldsandvalueslogicandbetweentwofields = $requiredfieldsandvalueslogicandbetweentwofields;
      $this->hidedisplayflag = $hidedisplayflag;
      if ($loopWithoutSubloop=="y") {
        $this->loopWithoutSubloop = "y";
      }

      $this->param1 = ""; # not in use yet
      $this->param2 = ""; # not in use yet

      $this->header = "";
      $this->auth = "";

      $this->buildWidgetHTML();
		}


    /* shortcodeExecute: read shortcode-params and check cache */
		public function getOutputHTML(){
      return $this->outputHTML;
    }

   /* shortcodeExecute: read shortcode-params and check cache */
		private function buildWidgetHTML(){

      if (
          (!class_exists('FileLoadWithCachePro'))
          || (!class_exists('JSONdecodePro')))
        {
        require_once plugin_dir_path( __FILE__ ) . '/class-fileload-cache-pro.php';
      }
      /* caching or not? */
      if ($this->cacheflag == "cacheon") {
        $this->cacheEnable = TRUE; # cache on
        $this->widgetClass->jci_showDebugMessage("buildWidgetHTML: cache ON");
       # $this->cacheBaseFolder = WP_CONTENT_DIR.'/cache/';
       # $this->cacheFolder = $this->cacheBaseFolder.'jsoncontentimporterpro/';

		require_once plugin_dir_path( __FILE__ ) . '/lib/cache.php';
        $checkCacheFolderObj = new jci_Cache();
        # cachefolder ok: set cachefile
    		$this->cacheFile = $checkCacheFolderObj->getCacheFileName($this->feedUrl);
			#sanitize_file_name($this->cacheFolder.md5($this->feedUrl).".cgi");  # cache json-feed
      } else {
        $this->cacheEnable = FALSE; # cache off
        $this->widgetClass->jci_showDebugMessage("buildWidgetHTML: cache OFF");
      }
      $this->postbody = get_option('jci_pro_http_body');
      $feedSource = "";
      $feedFilename = "";
      $postPayload = "";
      $fileLoadWithCacheObj = new FileLoadWithCachePro($this->feedUrl, $this->json_urlgettimeout, $this->cacheEnable, $this->cacheFile, $this->cacheExpireTime,
              FALSE, NULL, $feedSource, $feedFilename, $postPayload, $this->header, $this->auth, $this->postbody,
              $this->debugLevel, $this->debugModeIsOn, $this->urlencodepostpayload, $this->curloptions, $this->httpstatuscodemustbe200
          );
      $fileLoadWithCacheObj->retrieveJsonData();
      if (!$fileLoadWithCacheObj->getAllok()) {
        # loadig of JSON failed, errormessage is displayed at failed method
        $this->widgetClass->jci_showDebugMessage("loading ".$this->feedUrl." failed");
        $val_jci_pro_errormessage = stripslashes(get_option('jci_pro_errormessage'));
        $this->outputHTML = $val_jci_pro_errormessage;
        return "ERR500";
      }
      $this->feedData = $fileLoadWithCacheObj->getFeeddata();
      $this->widgetClass->jci_showDebugMessage("JSON data: ".$this->feedData, 10, "<hr>");

      $this->datastructure = preg_replace("/\n/", "", $this->datastructure);
      $this->widgetClass->jci_showDebugMessage("template: ".$this->datastructure, 10, "<hr>");

      if ($this->parserflag=="usetwig") {
  			# build json-array
        $jsonDecodeObj = new JSONdecodePro($this->feedData, TRUE, $this->debugLevel, $this->debugModeIsOn, $this->convertJsonNumbers2Strings);
        if (!$jsonDecodeObj->getIsAllOk()) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
           if ($errormsg=="") {
             $this->outputHTML = "JSON-Decoding failed. Check structure and encoding if JSON-data.";
           } else {
             $this->outputHTML = $errormsg;
           }
           return FALSE;
        }
        $this->widgetClass->jci_showDebugMessage("json decode for twig ok");
        $this->jsondata = $jsonDecodeObj->getJsondata();
        $this->widgetClass->jci_showDebugMessage("parser twig: ".$this->parserflag, 10, "<hr>");

        # invoke twig
        $this->jci_setup_twig_environment();
        $this->widgetClass->jci_showDebugMessage("twig-template: ". $this->datastructure, 10, "<hr>");
        $val_jci_pro_use_wpautop = get_option('jci_pro_use_wpautop');
        if ($val_jci_pro_use_wpautop==2) {
          $this->widgetClass->jci_showDebugMessage("twig: use wpautop");
          $this->datastructure = wpautop( $this->datastructure, FALSE);
        } else {
          $this->widgetClass->jci_showDebugMessage("twig: wpautop not used");
        }

        if ($this->datastructure=="") {
          return "no twigtemplate defined. plugin aborted!";
        } else {
          try {
            $this->twig_environment->parse($this->twig_environment->tokenize($this->datastructure));
            // the $template is valid
            $this->widgetClass->jci_showDebugMessage("twig-template: valid");
          } catch (/JCITwig/Twig_Error_Syntax $e) {
            // $template contains one or more syntax errors
            $this->widgetClass->jci_showDebugMessage("twig-template: INvalid!");
          }
          $template = $this->twig_environment->createTemplate($this->datastructure);
        }
  			$res = $template->render($this->jsondata);
        # execute shortcode in rendered text
        if (!preg_match("/\[jsoncontentimporterpro/", $res)) {  # prevent infinite looping
          $res = do_shortcode($res);
        }
        $this->widgetClass->jci_showDebugMessage("twig result: $res", 10, "<hr>");
        $this->outputHTML = $res;
      } else {
        $jsonDecodeObj = new JSONdecodePro($this->feedData, FALSE, $this->debugLevel, $this->debugModeIsOn, FALSE);
        if (!$jsonDecodeObj->getIsAllOk()) {
          $errormsg = stripslashes(get_option('jci_pro_errormessage'));
           if ($errormsg=="") {
             $this->outputHTML = "JSON-Decoding failed. Check structure and encoding if JSON-data.";
           } else {
             $this->outputHTML = $errormsg;
           }
           return FALSE;
        }
        $this->widgetClass->jci_showDebugMessage("json decode for JCI-parser");
        $this->jsondata = $jsonDecodeObj->getJsondata();
        $this->widgetClass->jci_showDebugMessage("parser JCI: ".$this->parserflag, 10, "<hr>");
        if(!class_exists('JsonContentParserPro')){     # the class might be already invoked
          require_once plugin_dir_path( __FILE__ ) . '/class-json-parser-pro.php';
        }
        $JsonContentParser = new JsonContentParserPro($this->jsondata, $this->datastructure, $this->basenode, $this->numberofdisplayeditems,
          $this->oneOfTheseWordsMustBeIn, $this->oneOfTheseWordsMustBeInDepth,
          $this->requiredFieldsAndValues, $this->requiredFieldsAndValuesDepth,
          $this->requiredfieldsandvalueslogicandbetweentwofields,
          $this->oneOfTheseWordsMustNotBeIn, $this->oneOfTheseWordsMustNotBeInDepth,
          $this->hidedisplayflag, $this->loopWithoutSubloop, $this->delimiter, $this->param1, $this->param2
        );
  			$this->outputHTML = $JsonContentParser->retrieveDataAndBuildAllHtmlItems();
     }
	}

		public function jci_setup_twig_environment() {
			# load and register Twig
      if (class_exists( '/JCITwig/Twig_Autoloader' ) ) {
        $this->widgetClass->jci_showDebugMessage("plugin is using Twig from another plugin");
      } else {
        $this->widgetClass->jci_showDebugMessage("load Twig from JCI-plugin");
        #$inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/Twig/Autoloader.php';
        $inc = WP_PLUGIN_DIR . '/jsoncontentimporterpro3/twiglib/twig332adj/vendor/autoloader.php'; 
        if (!file_exists($inc) || !is_readable($inc)) {
          echo '<span style="color:#f00;">Twig not found in '.$inc.'</span>';
        } else {
          require_once $inc;
        }
      }
      /JCITwig/Twig_Autoloader::register();

			# set path to Twig-Templates
  		$this->twig_loader = new Twig_Loader_Filesystem(WP_PLUGIN_DIR."/jsoncontentimporterpro3/"); # as we load the template via shortcode-param this is not needed - but I don'T know how to avoid it?

			# set twig options
			$this->twig_environment_settings = array(
				'charset' => get_bloginfo('charset'),
				'autoescape' => false,
				'auto_reload' => true,
				#'cache' => WP_PLUGIN_DIR.'/jsoncontentimporterpro/twig-cache',
			);
      $cachebaseFolder = WP_CONTENT_DIR.'/cache/';
      $cacheFolder1 = $cachebaseFolder.'jsoncontentimporterpro/';
      $cacheFolder2 = $cacheFolder1.'twigcache';
      $cacheDirThere = FALSE;
      if (is_dir($cacheFolder2)) {
        # all there
        $cacheDirThere = TRUE;
      } else if (is_dir($cacheFolder1)) {
        $mkdirError2 = @mkdir($cacheFolder2);
        if (is_dir($cacheFolder2)) {
          # all there
          $cacheDirThere = TRUE;
        }
      } else if (is_dir($cachebaseFolder)) {
        $mkdirError1 = @mkdir($cacheFolder1);
        $mkdirError2 = @mkdir($cacheFolder2);
        if (is_dir($cacheFolder2)) {
          # all there
          $cacheDirThere = TRUE;
        }
      }
      if ($cacheDirThere) {
        # all there
        $this->twig_environment_settings['cache'] = $cacheFolder2;
      }

			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				$this->twig_environment_settings['debug'] = true;
			}

      # invoke Twig
  		$this->twig_environment = new Twig_Environment($this->twig_loader, $this->twig_environment_settings);

			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				$this->twig_environment->addExtension(new /JCITwig/Twig_Extension_Debug());
			}
      $this->widgetClass->jci_showDebugMessage("success - Twig loaded");
		}

}
?>