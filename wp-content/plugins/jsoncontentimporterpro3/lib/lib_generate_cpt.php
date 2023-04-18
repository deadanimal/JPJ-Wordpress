<?php

class jci_generate_cpt {
	private $json = NULL;
	private $jsonNodeArr = Array();
	private $jsonKeysArr = Array();
	private $seljsonnode = "";
	private $jsonselected = NULL;
	private $selected_cpt_key = "";
#	private $selectejas = "";
	private $selectejus = "";
	private $select = "";
	private $howjson = "";
	private $selectway = "";
	
	private $tmp_cpt = "";	
	private $get_cpt_args = Array();	
	private $post_types = NULL;
	private $found_cpf = Array();
	private $foundCPT = FALSE;
	
#	public function __construct($json, $selected_cpt_key, $selectejas, $select, $howjson, $selectway){
	public function __construct($json, $selected_cpt_key, $selectejus, $select, $howjson, $selectway){
		#$this->json = json_decode(json_encode($json));
		$this->json = $json;
		
		$this->selected_cpt_key = $selected_cpt_key;
		$this->selectejus = $selectejus;
		$this->select = $select;
		$this->howjson = $howjson;
		$this->selectway = $selectway;
		$this->get_cpt_args = array(
			'public'   => true,
		#	'_builtin' => false
		);
    }
	
	public function showShortcode($seljsonnode) {
		$nameofgenset = "";
		if (isset($_POST["nameofgenset"])) {
			$nameofgenset = $_POST["nameofgenset"];
		}
		if (isset($_POST["loadgenset"])) {
			$nameofgenset = $_POST["loadgenset"];
		}
		
		$genset = $this->getGeneratingSet($nameofgenset);
		
		$cpf4sc = '"customfields": #BRO#';
		$slugname = '"slugname": "';
		foreach($genset as $key => $val) {
			#echo $key." - $val<br>";
			if (preg_match("/mval-cpf-/", $key)) {
				$cpfname = preg_replace("/mval-cpf-/", "", $key);
				$val = stripslashes(preg_replace("/\"/", "#SQM#", $val));
				$cpf4sc .= '{"'.$cpfname.'":"'.$val.'"},';
			}
			#if (preg_match("/mval-nocpf-/", $key)) {
			#	$cpfnameval = explode("###", $val);
			#	$cpf4sc .= '{"'.$cpfnameval[0].'":"'.$cpfnameval[1].'"},';
			#}
		}
		$cpf4sc = substr($cpf4sc, 0, -1); # remove last ,
		$cpf4sc .= '#BRC# ';
		
		$title = $genset["mval-nocpf-title"];
		if (empty($title))  {
			$title = "define title in Generating-Set please";
		}

		$slug = $genset["mval-nocpf-slug"];
		if (empty($slug))  {
			$slug = "define slug  in Generating-Set please";
			#"cp-slug-{{name|replace({#SQM#/#SQM#: #SQM#-#SQM#})}}", 
		}
		
		#echo json_encode($genset);
		$sc = '[jsoncontentimporterpro nameofjas="'.$genset["selectejus"].'" 
				mode=create 
				createoptions=\'{
					"key": "'.$this->selected_cpt_key.'",
					"type": "'.$this->selected_cpt_key.'", 
					"loop":"'.$seljsonnode.'", 
					"title":"'.$title.'",
					"slugname": "'.$slug.'",
					'.$cpf4sc;
		@$sc1 .= '	,		"loopstart":1,					"loopend":4, ';
		@$sc1 .= '			"minimumcptocreate":"2", 
					"postpublishtime":"{{data_of_publishing_in_json}}", 
					"postdateoffset":3600, 
					"requiredfields":"name",
					"deleteold":"yes", 
					"featuredimage": #BRO# {"url_json_path":"detailpage.img", 
					"url_default":"http://api.json-content-importer.com/wp-content/uploads/2020/01/icon-256x256-1.png"}#BRC#, 
					"postdateoffset":"Europe/Berlin" ';
		$sc .=	'	}\']';
		
		$scol = preg_replace("/(\n|\r)/", "", $sc);
		echo "<textarea cols=80 rows=15>$scol</textarea>";
	}
	
	
	public function DEAKgetJSON() {
		$this->showJSONnodes("", $this->json);
		#echo json_encode($this->jsonNodeArr)."<hr>";
		$seljsonnode = "";
		if (isset($_POST["seljsonnode"])) {
			$seljsonnode = $_POST["seljsonnode"];
		}
		$this->showform("Select a JSON-Node", FALSE, TRUE, $seljsonnode);		
	}
	
	public function selectJSONnode($json, $seljsonnode) {
		$seljsonnodeArr = explode(".", $seljsonnode);
		$curnode = array_shift($seljsonnodeArr);
		$restnode = join(".", $seljsonnodeArr);
		if (empty($restnode)) {
			#echo $curnode."<br>$restnode<br>";
			#echo json_encode($json[$curnode])."<hr>";
			$this->jsonselected = $json[$curnode];
		} else {
			$this->selectJSONnode($json[$curnode], $restnode);	
		}		
	}

	public function showJSONnodes($keynode, $jsonArr) {
		foreach($jsonArr as $key => $val) {
			if (is_array($val)) {
				$anz = count($val);
				if ($anz>0 && (!is_integer($key))) {
					#echo $keynode.$key." ($anz items)";
					if (empty($keynode)) {
						$completenode = $key;		
					} else {
						$completenode = $keynode.".".$key;
					}
					$this->jsonNodeArr[$completenode] = $anz;
					#echo "<br>";
					$this->showJSONnodes($completenode, $val);
				}
			}
		}
	}

	public function showJSONkeys($jsonArr, $currentnode = "", $callertype=null) {
		#echo "showJSONkeys IN: ".gettype($jsonArr)."<br>";
		foreach($jsonArr as $key => $val) {
			if (is_array($val)) {
				$anz = count($val);
				echo "json-array: ".$key.": ".gettype($val)."<br>";
				#$this->showJSONkeysItems("", $val);
					if (empty($currentnode)) {
						$completenode = $key;		
					} else {
						if (is_integer($key)) {
							$completenode = $currentnode;
						} else {
							#if ($completenode!=$currentnode) {
							$completenode = $key;
							#}
						}
					}
					#$this->jsonKeysArr[$completenode] = $anz;
					#echo "<br>";
					$this->showJSONkeys($val, $completenode, "array");
			} else if (is_object($val)) {
				echo "json-object: ".$key.": ".gettype($val).", caller: $callertype<br>";
				$this->showJSONkeys($val, $currentnode, "object");
			} else {
				echo "json-field: ".$key.": ".gettype($val).", caller: $callertype<br>";
				if (empty($currentnode)) {
					$selkey = $key;
				} else {
					$selkey = $currentnode.".".$key;
				}
				$this->jsonKeysArr[$selkey] = $key;				
			}
		}
	}

	public function DDshowJSONkeysItems($keynode, $jsonArrItem) {
		foreach($jsonArrItem as $key => $val) {
			#echo gettype($val)."<hr>";
			if (is_array($val)) {
				$anz = count($val);
				#echo "anz: $anz<br>";
					#echo $keynode.".".$key." ($anz items)<br>";
					if (empty($keynode)) {
						$completenode = $key;		
					} else {
						$completenode = $keynode.".".$key;
					}
					$this->jsonKeysArr[$completenode] = $anz;
					#echo "<br>";
					$this->showJSONkeysItems($completenode, $val);
			} else {
				if (empty($keynode)) {
					$selkey = $key;
				} else {
					$selkey = $keynode.".".$key;
				}
				$this->jsonKeysArr[$selkey] = $key;				
			}
		}
	}

	public function DEAKshowJSONkeys($jsonArr) {
		#echo "showJSONkeys IN: ".gettype($jsonArr)."<br>";
		foreach($jsonArr as $key => $val) {
			#echo "showJSONkeys: ".$key.": ".gettype($val)."<br>";
			$this->showJSONkeysItems("", $val);
		}
	}

	public function DEAKshowJSONkeysItems($keynode, $jsonArrItem) {
		foreach($jsonArrItem as $key => $val) {
			#echo gettype($val)."<hr>";
			if (is_array($val)) {
				$anz = count($val);
				#echo "anz: $anz<br>";
					#echo $keynode.".".$key." ($anz items)<br>";
					if (empty($keynode)) {
						$completenode = $key;		
					} else {
						$completenode = $keynode.".".$key;
					}
					$this->jsonKeysArr[$completenode] = $anz;
					#echo "<br>";
					$this->showJSONkeysItems($completenode, $val);
			} else {
				if (empty($keynode)) {
					$selkey = $key;
				} else {
					$selkey = $keynode.".".$key;
				}
				$this->jsonKeysArr[$selkey] = $key;				
			}
		}
	}
	
	public function get_found_cpf() {
		return $this->found_cpf;
	}
	
	public function get_jsonselected() {
		return $this->jsonselected;
	}

	public function get_jsonKeysArr() {
		return $this->jsonKeysArr;
	}
	
	public function getCPF() {
		$this->checkCPTexistence();
		if (FALSE===$this->tmp_cpt) {
			$this->generateCPT();
		}
		$cpflist = get_post_meta($this->tmp_cpt);
		#echo json_encode($cpflist);
		#$foundCPF = FALSE;
		foreach($cpflist as $cpfitemkey => $cpfitemval) {
			if (preg_match("/\_/", $cpfitemkey)) {
				# internal CPF
				continue;
			}
			$cpfnametmp = preg_replace("/wpcf\-/", "Toolset: ", $cpfitemkey);
			#echo $cpfnametmp."<br>";
			$this->found_cpf[$cpfitemkey] = $cpfnametmp;
			$this->foundCPT = TRUE;
		}
		
		if (!$this->foundCPT) {
			echo "<strong>No Custom Post Fields found for the Custom Post Type $this->selected_cpt_key</strong><br>";
			echo "There are two possible reasons for that:<br>";
			echo "1. There are no Custom Posts Fields defined for that Custom Post Type. You can do this with Plugins like ACF, Pods etc.<br>";
			echo "2. Due to the way, Wordpress manages Custom Post Fields, we have to open, then store, then close a instance of the Custom Post Type.
					For that open the below link and do so, please<br> ";
			echo '<a href="'.get_admin_url().'post.php?post='.$this->tmp_cpt.'&action=edit" target="_blank">Click here and "save draft" the page</a>';
		
			echo '<hr>';
			$this->showform("Reload this page", FALSE);	
			return FALSE;
		}
	}

	public function selectCPTForm() {
		$this->post_types = get_post_types( $this->get_cpt_args, 'object' ); // use 'names' if you want to get only name of the post type.
		#	echo '<pre>'.print_r($post_types, TRUE).'</pre>';
		// select a CPT
		#echo "SELUSE: ".$_POST['selectejus'];
		$this->showform("Select a Custom Post Type", TRUE);
	}
	
	private function getAllGeneratingSets() {
		$jci_pro_genset_items = json_decode(get_option('jci_pro_generating_set'), TRUE);
		return $jci_pro_genset_items;
	}

	private function getGeneratingSet($nameofgenset) {
		$jci_pro_genset_items = $this->getAllGeneratingSets();
		return $jci_pro_genset_items[$nameofgenset];
	}

	
	public function showExistingGenSets() {
		$genSetArr = $this->getAllGeneratingSets();
		if (is_null($genSetArr)) {
			echo "<hr><strong>No Generating-Set stored - Create one!</strong>";
			return "";
		}
		echo "<hr><table border=1>";
			echo "<tr bgcolor=#ccc><td valign=top>";
			echo 'Load this Generating-Set';
			echo "</td><td valign=top>";
			echo "Name of Generating-Set";
			echo "</td><td valign=top>";
			echo 'Last change';
			echo "</td><td valign=top>";
			echo 'Created at';
			echo "</td><td valign=top>";
			echo 'Status';
		#	echo "</td><td valign=top>";
		#	echo 'Delete?';
			echo "</td></tr>";
		foreach($genSetArr as $key => $val) {
			echo "<tr><td valign=top>";
			#echo json_encode($val["seljsonnode"]);
			$this->showform("Load Generating-Set", FALSE, FALSE, $val["seljsonnode"], TRUE, $key);
			echo "</td><td valign=top>";
			echo $key;
			echo "</td><td valign=top>";
			echo date("F d Y, H:i", $val["lastchangeat"]);;
			echo "</td><td valign=top>";
			echo date("F d Y, H:i", $val["createdat"]);;
			echo "</td><td valign=top>";
			echo $val["status"];
		#	echo "</td><td valign=top>";
		#	echo 'Delete?';
			echo "</td></tr>";
		}
		echo "</table>";	
	}
	
	
	private function saveGenSet($nameofgenset, $statusofgenset) {
		$genSetArr = $this->getAllGeneratingSets();
		if (isset($genSetArr[$nameofgenset])) {
			# update_option
			$createdat = $genSetArr[$nameofgenset]["createdat"];
		} else {
			# new
			$createdat = time();
		}
		$genSetArr[$nameofgenset] = $_POST;
		$genSetArr[$nameofgenset]["createdat"] = $createdat;
		$genSetArr[$nameofgenset]["lastchangeat"] = time();
		$genSetArr[$nameofgenset]["status"] = $statusofgenset;
		$save_str = "";
		$save_str = json_encode($genSetArr);
		update_option('jci_pro_generating_set', $save_str);
	}

	public function showformCPF2JSON($json2workwithNode, $seljsonnode) {
		$loadinggenset = FALSE;
		$genset = null;
		if (isset($_POST["loadgenset"])) {
			# load stored gen-set
			$loadinggenset = TRUE;
			$genset = $this->getGeneratingSet($_POST["loadgenset"]);
			$nameofgenset = $genset["nameofgenset"];
			echo "<strong>Loaded Generating-Set: ".$nameofgenset."</strong><br>";
			#echo json_encode($genset)."<hr>";
		} else {
			if (isset($_POST["nameofgenset"])) {
				$nameofgenset = $_POST["nameofgenset"];
				$statusofgenset = "active";
				$this->saveGenSet($nameofgenset, $statusofgenset);
			} else {
				$nameofgenset = time()."-generating-set";
			}
		}
		
		echo '<form method="post" id="target" action="?page=jciprostep2usejsonslug">';
		echo "<table border=1>";
			echo "<tr bgcolor=#ccc><td valign=top>";
			echo "Custom Post Field";
			echo "</td><td valign=top>";
			echo 'JSON-Fields';
			echo "</td><td valign=top>";
			echo 'Twig-Code for CPF: Only these values are used, not the ones left!<br>You might add formatting of date, time etc.';
			echo "</td></tr>";
		$foundCpf = $this->get_found_cpf();
		
		foreach($foundCpf as $cpfk => $cpfv) {
			$this->showformCPF2JSONLine($json2workwithNode, $cpfv, $cpfk, $loadinggenset, $genset);
		}
			echo "<tr bgcolor=#ccc><td valign=top>";
			echo "Page-Data out of JSON";
			echo "</td><td valign=top>";
			echo 'JSON-Fields';
			echo "</td><td valign=top>";
			echo 'Twig-Code: Only these values are used, not the ones left!<br>You might add formatting of date, time etc.';
			echo "</td></tr>";
			$this->showformCPF2JSONLine($json2workwithNode, "page title", "title", $loadinggenset, $genset, FALSE);
			$this->showformCPF2JSONLine($json2workwithNode, "page slug", "slug", $loadinggenset, $genset, FALSE);
			$this->showformCPF2JSONLine($json2workwithNode, "featured image (JSON: URL)", "featuredimage", $loadinggenset, $genset, FALSE);
		echo "</table>";	
		?>
		<script>
		jQuery(function() {
			try {
				jQuery('select[name*=pval-]').change(function() {
					var seljson = this.value.split("###");
					//alert('seljson: '+seljson[0]);
					var cpftwig = '{{' + seljson[1] + '}}';
					jQuery('input[id=mval-nocpf-'+seljson[0]+']').val(cpftwig);
					jQuery('input[id=mval-cpf-'+seljson[0]+']').val(cpftwig);
				});
			} catch(e) {
				alert('error: '+e);
			}
		});
		</script>		
		<?PHP		
		$this->formhiddenfields();
		echo '<input type=hidden name="selcpt" value="'.$this->selected_cpt_key.'" />';			
		echo '<input type=hidden name="seljsonnode" value="'.$seljsonnode.'" />';			
		echo '<p>Name of Generating-Set: <input type=text size=20 name="nameofgenset" placeholder="Name of Generating-Set" value="'.$nameofgenset.'" /> ';			
		submit_button("Save Generating-Set", 'primary', '', FALSE); 
		echo "</form>";			
	}

	private function formhiddenfields() {
		echo '<input type=hidden name=selecjs value="" />';
		echo '<input type=hidden name=selectejas value="'.$this->selectejus.'" />';
		echo '<input type=hidden name=select value="'.$this->select.'" />';
		echo '<input type=hidden name=howjson value="'.$this->howjson.'" />';
		echo '<input type=hidden name=selectway value="'.stripslashes($this->selectway).'" />';
		echo '<input type=hidden id="seljs" name="seljs" value="" />';
	}




	private function showformCPF2JSONLine($json2workwithNode, $nameofitem, $valofitem, $loadinggenset, $genset, $isCPF=TRUE) {
			$noCPFmarker = "cpf-";
			if (!$isCPF) {
				$noCPFmarker = "nocpf-";				
			}
			echo "<tr><td valign=top>";
			echo $nameofitem;
			echo "</td><td valign=top>";
			$pval = "###";
			if ($loadinggenset) {
				$pval = $genset["pval-".$noCPFmarker.$valofitem];			
			} else {
				if (isset($_POST["pval-".$noCPFmarker.$valofitem])) {
					$pval = $_POST["pval-".$noCPFmarker.$valofitem];
				}
			}
			$pvalArr = explode("###", $pval);
			$selp = $pvalArr[1];
			echo '<select name="pval-'.$noCPFmarker.$valofitem.'">';
				echo '<option value="###" '.$selfalg.'>ignore</option>';
			foreach($json2workwithNode as $key => $val) {
				$selfalg = "";
				if ($selp==$key) {
					$selfalg = "selected ";
				}
				echo '<option value="'.$valofitem.'###'.$key.'" '.$selfalg.'>';
				echo $key;
				echo "</option>";
			}
			echo "</select>";
			echo "</td><td valign=top>";
			$mval = "";
			if ($loadinggenset) {
				$mval = $genset["mval-".$noCPFmarker.$valofitem];
			} else {
				if (isset($_POST["mval-".$noCPFmarker.$valofitem])) {
					$mval = $_POST["mval-".$noCPFmarker.$valofitem];
				}
			}
			echo '<input type=text size=40 id="mval-'.$noCPFmarker.$valofitem.'" name="mval-'.
					$noCPFmarker.$valofitem.'" value="'.stripslashes(htmlentities($mval)).'">';
			echo "</td></tr>";
	}


	private function showform($submittext = "Send", $showSELCPTPulldown = TRUE, $showJSONNODEPulldown = FALSE, $seljsonnode = "", $loadgenset = FALSE, $loadgensetname= "", $selectejus="") {
		echo '<form method="post" id="target" action="?page=jciprostep2usejsonslug">';
		submit_button($submittext, 'primary', '', FALSE); 
		echo "<p>";
		$this->formhiddenfields();
		if ($showJSONNODEPulldown) {
			if ( !is_null($this->jsonNodeArr) ) {
				echo "<select name=seljsonnode>";
				foreach ( $this->jsonNodeArr as $jsonnodekey => $jsonnodeval ) {
					$opnodesel = "";
					if ($jsonnodekey==$seljsonnode) {
						#	if ($jsonnodekey==$_POST["seljsonnode"]) {				
						$opnodesel = "selected=selected";					
					}
					echo '<option value="'.$jsonnodekey.'" '.$opnodesel.'>'.$jsonnodekey.' ('.$jsonnodeval.' items)</option>';
				}	
				echo "</select>";
			}
		}
		if ($showSELCPTPulldown) {
			echo "<select name=selcpt>";
			if ( $this->post_types ) {
				foreach ( $this->post_types as $cpt_key => $cpt_val ) {
					$opcptsel = "";
					if ($cpt_key==$this->selected_cpt_key) {
						$opcptsel = "selected=selected";					
					}
					echo '<option value="'.$cpt_key.'" '.$opcptsel.'>'.$cpt_val->label.'</option>';
				}	
			}
			echo "</select>";
		} else {
			echo '<input type=hidden name="selcpt" value="'.$this->selected_cpt_key.'" />';			
		}
		
		if ($this->selectejus!="") {
			echo '<input type=hidden name="selectejus" value="'.$this->selectejus.'" />';			
		}
		
		
		if ($loadgenset) {
			echo '<input type=hidden name="seljsonnode" value="'.$seljsonnode.'" />';			
			echo '<input type=hidden name="loadgenset" value="'.$loadgensetname.'" />';			
		}
		
		
		echo "</form>";			
	}
	
	public function generateCPT() {
			$my_tmp_post = array(
				'post_title'    => 'JCI-Plugin: CPT for generating other CPT',
				'post_content'  => 'Thank you for opening this page: 
					This post is created by the JCI-plugin to get the Custom Post Fields when defining how to 
					create Custom Post Types. 
					1. check the Custom Post Fields (CPF)
					2. click on "save draft" to store this page with empty CPFs and
					3. close this tab
					4. click reload on the previous JCI-page
					5. You can delete this post if you\'re done with setting up generating CPT with ther JCI-Plugin:
					When you next time define the generation of CPT with the JCI-Plugin either this post or a newly created post is used. 
					',
				'post_status'   => 'draft',
				'post_type'     => $this->selected_cpt_key
			);
			$this->tmp_cpt =   wp_insert_post( $my_tmp_post );
	}

	public function checkCPTexistence() {
		$args = [ 'post_type' => $this->selected_cpt_key, 'post_status'   => 'draft', 'numberposts'	=> 1 ];
		$q = get_posts( $args );
		if (0==count($q)) {
			$this->tmp_cpt = FALSE;
			return FALSE;
		}
		$this->tmp_cpt = $q[0]->ID;
	}

	

}
?>