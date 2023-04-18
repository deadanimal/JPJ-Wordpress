<?php
/*
CLASS JCIHandleFileUpload
Description: Class for WP-plugin "JSON Content Importer Pro"
Version: 0.0.1
Author: Bernhard Kux
Author URI: https://json-content-importer.com
*/

class JCIHandleFileUpload {

	private $debugmessage = "";
	private $imgtype = "";
	private $imgname = "";
	private $imgtmpname = "";
	private $fildata = "";
	private $copadd = "";
	private $copaddout = "";
	private $uploadOk = FALSE;
	
	public function __construct(){
		if (!isset($_FILES)) {
			return TRUE;
		}
		$this->handleInput();

		$valsupload = array();
		#var_Dump($_FILES);
		#echo $img1."<hr>";
		$valsupload{"imgtype"} = $this->imgtype;
		$vals{"imgtype"} = $this->imgtype;
		$valsupload{"imgname"} = $this->imgname;			

		# this with @ should be enough....
		#$valsupload{"file"} = '@'.$imgtmpname;
		#if (file_exists($valsupload{"file_contents"})) { echo "yes"; } else { echo "no"; }

		# as the @.. does not work, this works:
		$this->loadImgWithfread();
		$valsupload{"fileimg"} = $this->fildata;
		#code in template something like this: '<img src="data:'.$imgtype.';base64, '.$fildata.'" title="'.$imgname.'" />';
		$this->uploadOk = TRUE;
		
		$this->copaddout = json_encode($valsupload);
		if (strlen($this->copaddout)>100) {
			$this->debugmessage .= ", add to CURLOPT_POSTFIELDS: ".substr($this->copaddout, 0, 100)."... (length: ".strlen($this->copaddout).")";
		}
		$this->copadd = ";CURLOPT_POSTFIELDS=".json_encode($valsupload);
	}
	
	public function getDebugmessage() {
		return $this->debugmessage;
	}
	public function getUploadStatus() {
		return $this->uploadOk;
	}
	public function getCrloptionsAdd() {
		return $this->copadd;
	}
	
	private function handleInput() {
		if (isset($_FILES)) {
			$this->debugmessage = "Handle Uploaded File: ".print_r($_FILES, TRUE);
			$img1 = @array_keys($_FILES)[0];
			if (""!=$img1) {
				if (""!=@$_FILES[$img1]['tmp_name']) {
					$this->imgtype = filter_var($_FILES[$img1]['type'], FILTER_SANITIZE_STRING);
					$this->imgname = filter_var($_FILES[$img1]['name'], FILTER_SANITIZE_STRING);
					$this->imgtmpname = $_FILES[$img1]['tmp_name'];
				}
			}
		}
	}
	
	private function loadImgWithfread() {
		$file = fopen($this->imgtmpname, 'r');
		$size = filesize($this->imgtmpname);
		$this->fildata = base64_encode(fread($file,$size));
	}
				
}
?>