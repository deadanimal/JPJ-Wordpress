<?php

class jci_request_prepare {
	private $formdata = NULL;
	private $methodTmp = "";
	private $methodtech = "";
	private $curloptions = "";
	
	
	public function __construct($formdata, $methodTmp, $methodtech){
		$this->formdata = $formdata;
		$this->methodTmp = $methodTmp;
		$this->methodtech = $methodtech;
    }
	
	public function getCurlOptions4Request($curloptions) {
		$propArr = $this->getPageArr();
		$curloptionsTwigDone = $this->twig_string($propArr, $curloptions);
		$curloptionsForRequest = $this->do_shortcode($curloptionsTwigDone);
		return $curloptionsForRequest;
	}
	

	public function getSelectedmethod() {
		$method["curlget"] = "curlget";
		$method["curlpost"] = "curlpost";
		$method["curlput"] = "curlput";
		$method["phpget"] = "rawget";
		$method["phppost"] = "rawpost";
		$method["wpget"] = "get";
		$method["wppost"] = "post";

		$selectedmethod = $method[$this->methodtech.$this->methodTmp];
		if (empty($selectedmethod)) {
			$selectedmethod = "curlget  ".$method[$this->methodtech.$this->methodTmp];
		}
		return $selectedmethod;
	}
	

	public function getCurlOptionsString() {
		$curloptions = "";
		$curloptions_there = FALSE;
		$curloptions_header_values = "";
		# header
		# CURLOPT_HTTPHEADER=accept:application/json##Authorization:Bearer whatever##a:{{urlparam.VAR1}}##c:d;CURLOPT_POSTFIELDS=e:f##{“g”:”h”}##i:j
		
		
		if ($this->formdata["headernooffilledheader"]>0) {
			$noofheaderitems = @$this->formdata["headernooffilledheader"];
			for ($i = 1; $i <= $noofheaderitems; $i++) {
				if (isset($this->formdata["headerl".$i]) && isset($this->formdata["headerr".$i])) {
					$key = preg_replace("/ /", "", $this->formdata["headerl".$i]);
					$val = $this->formdata["headerr".$i];
					if (!empty($key.$val)) {
						$curloptions_header_values .= jcipro_clear_httpheaderkey($key).':'.$val."##";
						$curloptions_there = TRUE;
					}
				}
			}
		}

		if ($this->formdata["cbheadaccess"]=="y") {
			$curloptions_header_values .= jcipro_clear_httpheaderkey($this->formdata["headaccesskey"]).":".$this->formdata["headaccessval"]."##";
			$curloptions_there = TRUE;
		}
		if ($this->formdata["cbheaduseragent"]=="y") {
			$curloptions_header_values .= jcipro_clear_httpheaderkey($this->formdata["headuseragentkey"]).":".$this->formdata["headuseragentval"]."##";
			$curloptions_there = TRUE;
		}
		if ($this->formdata["cbheadoauth2"]=="y") {
			$curloptions_header_values .= jcipro_clear_httpheaderkey($this->formdata["headoauth2key"]).":".$this->formdata["headoauth2val"]."##";
			$curloptions_there = TRUE;
		}
		if ($curloptions_there) {
			$curloptions_header_values = preg_replace("/##$/", "", $curloptions_header_values);
			$curloptions .= "CURLOPT_HTTPHEADER=".$curloptions_header_values.";";
		}
		
		if ($this->formdata["httpsverify"] == 2) {
			$httpverifycurl = "CURLOPT_SSL_VERIFYHOST=false;CURLOPT_SSL_VERIFYPEER=false;";
			$curloptions_there = TRUE;
			$curloptions .= $httpverifycurl;
		}

		#payload
		# CURLOPT_POSTFIELDS=
		if ($this->methodTmp=="post" || $this->methodTmp=="put") {
			$httppayload = "CURLOPT_POSTFIELDS=".$this->formdata["payload"].";"; #$postPayload;
			$curloptions .= $httppayload;
			$curloptions_there = TRUE;
		}
		if ($curloptions_there) {
			$curloptions = substr($curloptions, 0, -1);
		}

		$curloptions = $this->mask($curloptions);
		return stripslashes($curloptions);
	}
	
	private function mask($string) {
		$string = preg_replace("/\[/i", "#BRO#", $string);
		$string = preg_replace("/\]/i", "#BRC#", $string);
		$string = preg_replace("/\"/i", "#QM#", $string);
		return $string;
	}

	private function unmask($string) {
		$string = preg_replace("/#BRO#/i", "[", $string);
		$string = preg_replace("/#BRC#/i", "]", $string);
		$string = preg_replace("/#QM#/i", "\"", $string);
		return $string;
	}


	public function do_shortcode($shortcodestring) {
		$shortcodestring = $this->unmask($shortcodestring);
		$shortcodestring = do_shortcode($shortcodestring);
		return $shortcodestring;
	}

	public function get_method_methodtech_name() {
		$methodname["curlget"] = "curlget";
		$methodname["curlpost"] = "curlpost";
		$methodname["curlput"] = "curlput";
		$methodname["phpget"] = "rawget";
		$methodname["phppost"] = "rawpost";
		$methodname["wpget"] = "get";
		$methodname["wppost"] = "post";
		if (isset($methodname[$this->methodtech.$this->methodTmp])) {
			return $methodname[$this->methodtech.$this->methodTmp];
		}
		return $methodname["curlget"];
	}

	public function twig_string($jsonArr, $twig_string, $twigVersion = "twig332adj") {
		if (
			(!class_exists('doJCITwig'))
		) {
			require_once WP_PLUGIN_DIR . '/jsoncontentimporterpro3/lib/twig.php';
		}
		$twigHandler = new doJCITwig($twigVersion, TRUE);
		
		$twig_string_twigged = preg_replace("/#QM#/i", "\"", $twig_string);
		try {
			$twig_string_twigged = $twigHandler->executeTwig($jsonArr, $twig_string_twigged, $twigVersion, TRUE);
		} catch (Throwable $e) { 
			echo "<hr><font color=red>There is invalid twig-code in the Header- or Payload-Data: $twig_string_twigged</font><hr>";
		}
		if (preg_match("/^Twig-Error/", $twig_string_twigged)) {
			echo "<hr><font color=red>Error: There is invalid twig-code in the Header- or Payload-Data: $twig_string_twigged</font><hr>";
		} else {
			$twig_string = $twig_string_twigged;
		}
		return $twig_string;
	}

	public function getPageArr() {
		$propArr = Array();
		$propArr["get_permalink"] = get_permalink();
		$propArr["home_url"] = home_url();
		$userid = get_current_user_id();
		$propArr["get_current_user_id"] = $userid;
		$usrdataArr = get_userdata($userid);
		$propArr["userdata"] = $usrdataArr;

		$postparam = get_post();
		if (!is_null($postparam)) {
			#var_Dump($postparam);
			$postparam->post_content = preg_replace("/jsoncontentimporterpro/", "", trim($postparam->post_content)); # shortcode in $postparam->post_content are executed when used in twig, result: infinite loop or plugin-termination -> remove "jsoncontentimporterpro" to invalidate shortcode
			$propArr["get_post"] = $postparam;
			$pageid = $postparam->ID;
			$cpflist = get_post_meta($pageid);
			if (is_array($cpflist)) {
				$propArr["cpf"] = $cpflist;
			}
		}
		return $propArr;
	}

}
?>