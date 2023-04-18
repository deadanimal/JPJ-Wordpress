<?php
/*
CLASS JsonContentParserPro
Description: 20160518 - Basic template engine Class: building code with JSON-data and template markups, Pro-Version
Version: 3.0.3
Author: Bernhard Kux
Author URI: https://json-content-importer.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


class JsonContentParserPro {

    /* shortcode-params */
		private $jsondata = "";
    private $datastructure = "";
    private $basenode = "";
    private $numberofdisplayeditems = -1; # -1: show all
    private $oneOfTheseWordsMustBeIn = "";
    private $oneOfTheseWordsMustBeInDepth = 1;
    private $oneOfTheseWordsMustNotBeIn = "";
    private $oneOfTheseWordsMustNotBeInDepth = 1;
    private $requiredFieldsAndValues = "";
    private $requiredFieldsAndValuesDepth = 1;
    private $requiredFieldsAndValuesLogicANDBetweenTwoFields = FALSE;
    private $doNOTdisplay = FALSE;

    private $param1 = "";
    private $param2 = "";

    /* internal */
    private $showDebugMessages = FALSE; # set TRUE in constructor for debugging
    private $triggerUnique = NULL;
    private $subLoopParamArr = NULL;
    private $regExpPatternDetect = "([a-zA-Z0-9\=\",;\_\-:!\*\,\<\>\/ ]+)";
    private $regExpPatternDetectWithBrackets =         "([{}a-zA-Z0-9\=\",;\_\-:!\*\,\<\>\/ \.]+)";
    private $addToResult = TRUE;
    private $thereIsASubloop = FALSE;
    private $loopWithoutSubloop = FALSE;
    private $delimiter = "##";

		public function __construct($jsonData, $datastructure, $basenode, $numberofdisplayeditems,
        $oneOfTheseWordsMustBeIn, $oneOfTheseWordsMustBeInDepth,
        $requiredFieldsAndValues, $requiredFieldsAndValuesDepth, $requiredFieldsAndValuesLogicANDBetweenTwoFields,
        $oneOfTheseWordsMustNotBeIn, $oneOfTheseWordsMustNotBeInDepth, $doNOTdisplay,
        $loopWithoutsubloop, $delimiter, $param1, $param2
      ){
   #  $this->showDebugMessages = TRUE; # sometimes helpful
      $this->param1 = $param1;
      $this->param2 = $param2;
      if ($loopWithoutsubloop=="y") {
        $this->loopWithoutSubloop = TRUE;
      }

      if ($doNOTdisplay) {
        $this->doNOTdisplay = $doNOTdisplay;
      }

      if (!empty($delimiter)) {
        $this->delimiter = $delimiter;
      }


      if (is_numeric($numberofdisplayeditems) || (preg_match("/([0-9]*),([0-9]*)/", $numberofdisplayeditems))) {
        $this->numberofdisplayeditems = $numberofdisplayeditems;
      }
      $this->oneOfTheseWordsMustBeIn = $oneOfTheseWordsMustBeIn;
      if (is_numeric($oneOfTheseWordsMustBeInDepth)) {
        $this->oneOfTheseWordsMustBeInDepth = $oneOfTheseWordsMustBeInDepth;
      }
      $this->oneOfTheseWordsMustNotBeIn = $oneOfTheseWordsMustNotBeIn;
      if (is_numeric($oneOfTheseWordsMustNotBeInDepth)) {
        $this->oneOfTheseWordsMustNotBeInDepth = $oneOfTheseWordsMustNotBeInDepth;
      }
      $this->jsondata = $jsonData;
      $this->datastructure = $datastructure;
      $this->datastructure = preg_replace("/\n/", "", $this->datastructure); # remove linefeeds from template
      $this->basenode = $basenode;
      $this->requiredFieldsAndValues = $requiredFieldsAndValues;
      if (is_numeric($requiredFieldsAndValuesDepth)) {
        $this->requiredFieldsAndValuesDepth = $requiredFieldsAndValuesDepth;
      }
      if (is_bool($requiredFieldsAndValuesLogicANDBetweenTwoFields) && $requiredFieldsAndValuesLogicANDBetweenTwoFields) {
          # if true: several fields in requiredFieldsAndValues: all must match
          # if false: several fields in requiredFieldsAndValues: one of it must match
          $this->requiredFieldsAndValuesLogicANDBetweenTwoFields = $requiredFieldsAndValuesLogicANDBetweenTwoFields;
      } else {
          $this->requiredFieldsAndValuesLogicANDBetweenTwoFields = FALSE;
      }

      $this->output = "";

      if (preg_match("/{subloop/", $datastructure)) {
        $this->thereIsASubloop = TRUE;
      }
		}

    /* retrieveDataAndBuildAllHtmlItems: get json-data, build html*/
		public function retrieveDataAndBuildAllHtmlItems(){
      if ($this->doNOTdisplay) {
        return "";  # tell the parser not to parse and give back an empty string, e.g.: if logged in display something - if not logged in display nothing
      }
      $jsonTree = $this->jsondata;
      $baseN = $this->basenode;
      $this->debugEcho("<hr>basenode: $baseN<br>");
      if ($baseN!="") {
        $baseNArr = explode(".", $baseN);  # path of basenode: separator is "."
        foreach($baseNArr as $key => $valin) {
          $val = $valin;
          if (is_object($jsonTree)) {
            $jsonTree = $jsonTree->$val;
          } else if (is_array($jsonTree)){
           foreach($jsonTree as $jsonTreekey => $jsonTreeval) {
              if (is_object($jsonTreeval)) {
                if (isset($jsonTree[$jsonTreekey]->$val) && (!is_null($jsonTree[$jsonTreekey]->$val))) {
                  $jsonTree1 = $jsonTree[$jsonTreekey];
                }
              } else {
                # not implemented yet: uncool, but possible - why not another array
                $this->debugEcho("<hr>double-array at root? not implemented yet<hr>", "wordpressticket");
              }
            }
          } else {
            # neither object nor array? not implemented yet: should never happen
            $this->debugEcho("<hr>neither object nor array? not implemented yet<hr>", "wordpressticket");
          }
        }
      }

      $this->debugEcho("basic entry with: <i>".gettype($jsonTree)."</i><br>");

      # $jsonTree has to be object or array
      if (!is_object($jsonTree) && !is_array($jsonTree)) {
        $this->debugEcho("<hr>problems with JSON-structure: JSON is ".gettype($jsonTree)."<hr>", "wordpressticket");
        #exit;
        return "errorjsonstruc";
      }

      # start parsing
      $startdepth = 0;
      $resultArr = $this->checkType($jsonTree, gettype($jsonTree), $this->datastructure, "", $startdepth, "", $this->numberofdisplayeditems, 1); # 1st call of checkType
      return trim($this->clearUnusedArrayDatafields($resultArr[1]));
		}


    private function resultIsInNumRange($noofFoundItems, $noofDisplayedItems) {
      if (is_numeric($noofDisplayedItems) && ($noofDisplayedItems<0 || $noofFoundItems<=$noofDisplayedItems)) {
        return TRUE;
      } else if (preg_match("/([0-9]*),([0-9]*)/", $noofDisplayedItems)) {
        $noofDisplayedItemsArr = explode("," , $noofDisplayedItems);
        if (is_numeric($noofDisplayedItemsArr[0]) && is_numeric($noofDisplayedItemsArr[1])) {
          if (
            ($noofFoundItems >= $noofDisplayedItemsArr[0]) &&
            ($noofFoundItems <= $noofDisplayedItemsArr[1])
            ){
              return TRUE;
            }
          }
       }
       return FALSE;
     }

     private function checkType($jsonIn, $type, $template, $node2check, $depth, $keyIn, $noofDisplayedItems, $currentNumber) {
        $result = "";
        $depth++;
        $doCheckRequiredFieldsAndValues = FALSE;
        $requiredFieldsAndValuesOk = TRUE;
        $resTmpCounter = NULL;
        $resTmp = NULL;
        $noofItems = 0;
        $returnHTMLinsideProc = "";
        $noofFoundItems = 0;

        if ($this->requiredFieldsAndValuesLogicANDBetweenTwoFields) {
				  $allFieldOfThisDepth = array();
          if ($this->requiredFieldsAndValuesDepth==($depth) && $this->requiredFieldsAndValues!="") {
            $doCheckRequiredFieldsAndValues = TRUE;
  					$requiredFieldsAndValuesArr = explode("#", trim($this->requiredFieldsAndValues));
  					$requiredFieldValuesSepArr  = array(); // sep = separated
	          foreach($requiredFieldsAndValuesArr as $requiredFieldsAndValuesItem) {
	            $fieldWithValueArr = explode("=", trim($requiredFieldsAndValuesItem));
		  				$fieldToCheck = trim($fieldWithValueArr[0]);
              array_shift($fieldWithValueArr);
			 	   		$valuesOfField = trim(join("=", $fieldWithValueArr));
			   			$requiredFieldValuesSepArr[$fieldToCheck] = $valuesOfField;
 					  }
          }
        }

        if (!$this->requiredFieldsAndValuesLogicANDBetweenTwoFields) {
          if ($this->requiredFieldsAndValuesDepth==($depth) && $this->requiredFieldsAndValues!="") {
            $doCheckRequiredFieldsAndValues = TRUE;
            $requiredFieldsAndValuesOk = FALSE;
          }
        }

        if ($noofDisplayedItems=="") {
          $noofDisplayedItems = -1;
        }
        if (!is_numeric($currentNumber)) {
          $currentNumber = 1;
        }

        $keypass = $keyIn.".".$node2check;
        $keypass = preg_replace("/^\./", "", $keypass);
        $keypass = preg_replace("/\.$/", "", $keypass);
        $keypass = preg_replace("/\.\./", ".", $keypass);
        $keypass = preg_replace('/'.'\$'.'/', '\\\$', $keypass);
        $this->debugEcho( "<hr><font color=blue>ENTER function checkType // depth: <i>$depth</i> // type: <i>$type</i> // keyIn: <i>$keypass</i> // node2check: <i>$node2check</i> // noofDisplayedItems: <i>$noofDisplayedItems</i> // template: <i>".@htmlentities($template)."</i>");
        $this->debugEcho("<br> // json-in: ", "showdump", $jsonIn);
        $this->debugEcho( "</font><br><font color=green>start loop</font><br>");

        foreach($jsonIn as $key => $val) {
          if (is_object($val)) {
            $this->debugEcho("object found: depth: <i>$depth</i> // key:  <i>$key</i> // type: <i>$type</i> // template: <i>".htmlentities($template)."</i> // node2check: <i>$node2check</i> // ");
            $this->debugEcho("json in loop: ", "showdump", $val);
            if ($type=="array") {
                $this->debugEcho("typ=array:  <i>$key</i> // val: <i>".gettype($val)."</i> // template: <i>".htmlentities($template)."</i><br>");
              list($returnHTMLinsideProc, $resultOfProcessedObjects, $noofItems) = $this->checkType($val, "object", $template, "", $depth, $keypass, $noofDisplayedItems, 1);
              if ($resultOfProcessedObjects!="") {
                $noofFoundItems++;
                if ($this->resultIsInNumRange($noofFoundItems, $noofDisplayedItems)) {
                  $result .= $resultOfProcessedObjects;
                }
              }
            } else if (is_numeric($key) ) {
             $this->debugEcho("num key:  <i>$key</i> // val: <i>".gettype($val)."</i> // template: <i>".htmlentities($template)."</i><br>");
              if (is_object($val)) {
                list($returnHTMLinsideProc, $resultOfProcessedObjects, $noofItems) = $this->checkType($val, "object", $template, "", $depth, $keypass, $noofDisplayedItems, $noofFoundItems);
                if ($resultOfProcessedObjects!="") {
                  $noofFoundItems++;
                  $this->debugEcho("numeric key: $key / noofFoundItems: $noofFoundItems / noofDisplayedItems1: $noofDisplayedItems<br>");
                  if ($this->resultIsInNumRange($noofFoundItems, $noofDisplayedItems)) {
                    $result .= $resultOfProcessedObjects;
                  }
                }
              }
            } else if (is_string($key) && (!$this->thereIsASubloop)) {
               $this->debugEcho("key is string, no subloop:  <i>$key</i> // val: <i>".gettype($val)."</i> // template: <i>".htmlentities($template)."</i><br>");
               list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($val, "", $template, "", $depth, $keypass, $noofDisplayedItems, 1);
               if ($template==$resultFromSubloopprocessing) {
               } else {
                if ( $this->loopWithoutSubloop) {
                  $noofFoundItems++;
                  if ($this->resultIsInNumRange($noofFoundItems, $noofDisplayedItems)) {
                    $result .= $resultFromSubloopprocessing;
                  }
                 }
               }
            } else {
                $this->debugEcho("else: process subloop:  <i>$key</i> // val: <i>".gettype($val)."</i> // template: <i>".htmlentities($template)."</i><br>");
              list($subloopNodeObj, $subLoopNumberObj, $subloopTemplate, $keypassreturn) = $this->process_subloop($template, $key, $keypass, $noofDisplayedItems);
              if ($subloopTemplate=="") {
                # no subloop: use template
                list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($val, "", $template, $subloopNodeObj, $depth, $keypass, $subLoopNumberObj, 1);

                $this->debugEcho("subloop noofFoundItems: $noofFoundItems / noofDisplayedItems: $subLoopNumberObj / subLoopNumberObj: $subLoopNumberObj<br>");
                if ($this->resultIsInNumRange($noofFoundItems, $subLoopNumberObj)) {
                  $template = $resultFromSubloopprocessing;
                }
              } else if (is_numeric($key)) {
                  $this->debugEcho("key is numeric:  <i>$key</i> // val: <i>".gettype($val)."</i> // template: <i>".htmlentities($template)."</i><br>");
                  list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($val, "", $subloopTemplate, $subloopNodeObj, $depth, $keypass, $subLoopNumberObj, 1);
                  $returnHTMLinsideProc = $this->replace_subloop($resultFromSubloopprocessing, $subloopNodeObj, $subLoopNumberObj, $subloopHTMLObj, $template, $keypass);
                  $noofFoundItems++;
                  $this->debugEcho("subloop numeric key noofFoundItems: $noofFoundItems / resultFromSubloopprocessing: $resultFromSubloopprocessing / noofItems: $noofItems / currentNumber: $currentNumber / key: $key / noofDisplayedItems: $subLoopNumberObj // returnHTMLinsideProc: $returnHTMLinsideProc<br>");
                  if ($this->resultIsInNumRange($currentNumber, $subLoopNumberObj)
                  ) {
                    $template = $returnHTMLinsideProc;
                    $result = $template;
                  } else {
                  }
              } else if ($key==$subloopNodeObj) {
                     $this->debugEcho("key eq sub:  <i>$key</i> // val: <i>".gettype($val)."</i> // template: <i>".htmlentities($template)."</i><br>");
        for ($i=0; $i<count($subloopTemplate); $i++) {
                  $subloopTemplate1 = $subloopTemplate[$i];
                  $subLoopNumberObj1 = $subLoopNumberObj[$i];
                  list($returnHTMLinsideProc, $resultFromSubloopprocessing, $noofItems) = $this->checkType($val, "", $subloopTemplate1, $subloopNodeObj, $depth, $keypass, $subLoopNumberObj1, 1);
                  @$returnHTMLinsideProc = $this->replace_subloop($resultFromSubloopprocessing, $subloopNodeObj, $subLoopNumberObj1, $subloopHTMLObj, $template, $keypass);
                  $noofFoundItems++;
                  @$this->debugEcho("subloop NON-numeric key noofFoundItems: $noofFoundItems / resultFromSubloopprocessing: $resultFromSubloopprocessing / noofItems: $noofItems / currentNumber: $currentNumber / key: $key / noofDisplayedItems: $subLoopNumberObj // subLoopNumberObj1: $subLoopNumberObj1 // returnHTMLinsideProc: $returnHTMLinsideProc<br>");
                  ####             if ($this->resultIsInNumRange($noofFoundItems, $subLoopNumberObj1)
                  ####                  ) {
                    $template = $returnHTMLinsideProc;
                    $result = $template;
                  ####           echo "result in range: $noofFoundItems - ".$subLoopNumberObj1."<br>";
                  ####                  } else {
                  ####                echo "result out of range: $noofFoundItems - ".$subLoopNumberObj1."<br>";
                  ####         }
             }
                } else {
                  $this->debugEcho("no match<hr>");
                }

            }
          } else if (is_array($val)) {
            $this->debugEcho("array found: key: <i>$key</i> // template: <i>".htmlentities($template)."</i> <br>// ");
            $this->debugEcho("jsininarray: ", "showdump", $val);
            list($subloopNode, $subLoopNumber, $subloopTemplate) = $this->process_subloop_array($template, $key, $keypass); # check on {subloop-array}
            if (count($subloopTemplate)==0) {
              $this->debugEcho("no such {subloop-array}: loop array one by one<br>");
			### new begin 2.0.15
              $noofmatches_jcix = NULL;
              foreach($val as $keynosubloop => $valnosubloop) {
                if (preg_match("/{".$key.".jcix:(.*?)".$this->delimiter."(.*?)".$this->delimiter."(.+?)".$this->delimiter."}/", $template)) {
                  $noofmatches_jcix[$key] = preg_match_all("/{".$key.".jcix:(.*?)".$this->delimiter."(.*?)".$this->delimiter."(.+?)".$this->delimiter."}/", $template, $match_jcix);
                    if ($noofmatches_jcix[$key]>0) {
  	                  for ($i=0; $i<$noofmatches_jcix[$key]; $i++) {
                        #@$resTmpCounter{$key."-".$i}++;
                        isset($resTmpCounter[$key."-".$i]) ? $resTmpCounter[$key."-".$i]++ : $resTmpCounter[$key."-".$i] = 1;
                        if ($match_jcix[3][$i]<0 || $resTmpCounter[$key."-".$i]<=$match_jcix[3][$i]) {
                          @$resTmp[$key."-".$i] .= $match_jcix[1][$i].$valnosubloop.$match_jcix[2][$i];
                        }
                      }
                    }
               }
             }
             if ($noofmatches_jcix[$key]>0) {
               for ($i=0; $i<$noofmatches_jcix[$key]; $i++) {
                  $pat1 = "{".$key.".jcix:".$match_jcix[1][$i].$this->delimiter.$match_jcix[2][$i].$this->delimiter.$match_jcix[3][$i].$this->delimiter."}";
                  $pat1=addcslashes($pat1, "/^$");
                 $template = preg_replace("/".$pat1."/", $resTmp[$key."-".$i], $template);
                 $result = $template;
               }
             }
			### new end  2.0.15            
	} else if ((!empty($subloopNode)) && ($key==$subloopNode)) {
              $this->debugEcho("subloopNode_array: <i>".@htmlentities($subloopNode)."</i> // no: <i>".@htmlentities($subLoopNumber)."</i> // html: <i>".@htmlentities($subloopTemplate)."</i><br>");
              if (is_array($subloopTemplate)) {
                for ($i=0; $i<count($subloopTemplate); $i++) {
                  $subLoopNumber1= $subLoopNumber[$i];
                  $subloopTemplate1 = $subloopTemplate[$i];
                  list($returnHTMLinsideProc, $resultFromSubloopArray, $noofItems) = $this->checkType($val, "array", $subloopTemplate1, $subloopNode, $depth, $keypass, $subLoopNumber1, 66);

                  if (preg_match("/{/", $resultFromSubloopArray)) {
                    $resultFromSubloopArray = preg_replace("/\{(.*?)\}/i", "", $resultFromSubloopArray);
                  }
                  $template = $this->replace_subloop_array($resultFromSubloopArray, $subloopNode, $subLoopNumber1, $subloopTemplate1, $template, $keypass);
                  $result = $template;
                }
              } else {
                  list($returnHTMLinsideProc, $resultFromSubloopArray, $noofItems) = $this->checkType($val, "array", $subloopTemplate, $subloopNode, $depth, $keypass, $subLoopNumber, 66);
                  $this->debugEcho("result: <i>".@htmlentities($returnHTMLinsideProc)."</i><br>");
                  if (preg_match("/{/", $resultFromSubloopArray)) {
                    $resultFromSubloopArray = preg_replace("/\{(.*?)\}/i", "", $resultFromSubloopArray);
                  }
                  $template = $this->replace_subloop_array($resultFromSubloopArray, $subloopNode, $subLoopNumber, $subloopTemplate, $template, $keypass);
                  $result = $template;
              }
            } else {
              # new from 2.0.14 on: array of array-loops
              list($returnHTMLinsideProc, $resultFromSubloopArray, $noofItems) = $this->checkType($val, "array", $subloopTemplate, $subloopNode, $depth, $keypass, $subLoopNumber, 66);
              $this->debugEcho("array of array-loops: $resultFromSubloopArray<br>");
              $result .= $resultFromSubloopArray;
            }
          } else if (is_string($val) || is_numeric($val)) {
            if (
              ($type=="array") && is_numeric($key) && ($key >= $noofDisplayedItems)
              ){
                if (!preg_match("/{".$keypass.".jcix:(.+?)}/", $template)) {
                  continue;
                }
            }

            if ($this->requiredFieldsAndValuesLogicANDBetweenTwoFields) {
              if ($doCheckRequiredFieldsAndValues) {
							  if (array_key_exists($key, $requiredFieldValuesSepArr)) {
                  $listOfRequiredValuesString = trim($requiredFieldValuesSepArr[$key]);
							 	  $field2CheckValuesArr = explode(",", $listOfRequiredValuesString);
                  if (count($field2CheckValuesArr)>0) {
  							    $oneOftheValueMatch = FALSE;
	   							  foreach($field2CheckValuesArr as $requiredValue) {
                     if (preg_match("/".trim($requiredValue)."/i", trim($val))) {
			   							  $oneOftheValueMatch = TRUE;
				  						  break;
					 			     }
                    }
                  } else {
                    $oneOftheValueMatch = TRUE;
                  }

                  if (!$oneOftheValueMatch) {
                    $requiredFieldsAndValuesOk = FALSE;
                  }
							  } else {
							  }
              }
            }

            if (!$this->requiredFieldsAndValuesLogicANDBetweenTwoFields) {
              if ($doCheckRequiredFieldsAndValues) {
                $requiredFieldsAndValuesArr = explode("#", trim($this->requiredFieldsAndValues));
                foreach($requiredFieldsAndValuesArr as $requiredFieldsAndValuesItem) {
                  $requiredFieldsAndValuesItemArr = explode("=", trim($requiredFieldsAndValuesItem));
                  $field2Check = trim($requiredFieldsAndValuesItemArr[0]);
                  array_shift($requiredFieldsAndValuesItemArr);
			 	   		    $requiredFieldsAndValuesItemArrItem = trim(join("=", $requiredFieldsAndValuesItemArr));
                  $field2CheckValuesArr = explode(",", $requiredFieldsAndValuesItemArrItem);
                  if ($field2Check==$key) {
                    foreach($field2CheckValuesArr as $requiredFieldsValue) {
                      if (preg_match("/".trim($requiredFieldsValue)."/i", trim($val))) {
                        $requiredFieldsAndValuesOk = TRUE;
                        $this->debugEcho("requiredFieldsAndValuesOk TRUE<br>");
                      }
                    }
                  }
                }
              }
            }

            $valout = $val;
	          if (!isset($valout)) {
              $valout = "";
            }



              if (preg_match("/{".$keypass.".jcix:(.*?)".$this->delimiter."(.*?)".$this->delimiter."(.+?)".$this->delimiter."}/", $template)) {
                $noofmatches_jcix[$keypass] = preg_match_all("/{".$keypass.".jcix:(.*?)".$this->delimiter."(.*?)".$this->delimiter."(.+?)".$this->delimiter."}/", $template, $match_jcix1);
                if ($noofmatches_jcix[$keypass]>0) {
  	          for ($i=0; $i<$noofmatches_jcix[$keypass]; $i++) {
                    $resTmpCounter1[$keypass]++;
                    $templateTmp = "";
                    if ($match_jcix1[3][$i]<0 || $resTmpCounter1[$keypass]<=$match_jcix1[3][$i]) {
                      $left1 = $match_jcix1[1][$i];
                      $right1 = $match_jcix1[2][$i];
                      $templateTmp = preg_replace("/{".$keypass.".jcix:".$left1.$this->delimiter.$right1.$this->delimiter.$match_jcix1[3][$i].$this->delimiter."}/", $left1."{".$key."}".$right1, $template);
                      $templateTmp = $this->replacePattern($templateTmp, $key, $valout, $keypass);
                    }
                  }
                }
              $result .= $templateTmp;
            } else {
              $template = $this->replacePattern($template, $key, $valout, $keypass);
              $result = $template;
            }
          } else if (is_bool($val)) {
            if ($val) {
              $valout = "true";
            } else {
              $valout = "false";
            }
            $template = $this->replacePattern($template, $key, $valout, $keypass);
            $result = $template;
          }

          if ($this->requiredFieldsAndValuesLogicANDBetweenTwoFields) {
            $allFieldOfThisDepth[] = $key;
          }
        }

        if ($this->requiredFieldsAndValuesLogicANDBetweenTwoFields) {
          if ($doCheckRequiredFieldsAndValues){
            foreach ($requiredFieldValuesSepArr as $field => $value){
						  if (!in_array($field,$allFieldOfThisDepth)){
							  $requiredFieldsAndValuesOk = FALSE;
              }
				    }
				  }
        }

        $this->debugEcho("<hr><font color=red>LEAVE function checkType: // depth: $depth<br>// result: <i>".htmlentities($result)."</i><br>// noofItems: <i>$noofItems</i><br>// returnHTMLinsideProc: <i>$returnHTMLinsideProc</i><hr></font>");
        if ($depth==$this->oneOfTheseWordsMustBeInDepth
          || $depth==$this->oneOfTheseWordsMustNotBeInDepth
        ) {
          $result = $this->checkIfAddToResult($result);
        }
        if ($doCheckRequiredFieldsAndValues && (!$requiredFieldsAndValuesOk)) {
          return NULL;
        }
        return array ($returnHTMLinsideProc, $result, $noofItems);
   }

    private function replace_subloop_with_nameofsubloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass, $nameofsubloop) {
      if (is_numeric($subLoopNumber)) {
        $subLoopNumberPattern = $subLoopNumber;
      } else if (preg_match("/([0-9]*),([0-9]*)/", $subLoopNumber)) {
        $subLoopNumberPattern = $subLoopNumber;
      } else {
        $subLoopNumberPattern = 100;#"([0-9])"; # if not set as numeric take 100 by default
      }
      if ($keypass=="") {
        $re = $subloopNode;
      } else {
        $re = $keypass.".".$subloopNode;
      }
      $re = preg_replace('/'.'\$'.'/', '\\\$', $re);

      $nameofsubloopTmp = "";
      if ($nameofsubloop!="") {
        $nameofsubloopTmp = "-".$nameofsubloop;
      }
      $sli = '/\{subloop'.$nameofsubloopTmp.':'.$re.':'.$subLoopNumberPattern.'\}(.*?)\{\/subloop'.$nameofsubloopTmp.':'.$re.'\}/i';
      $resulttmp = $this->preg_escape_dollar_slash($result);
      $ret = preg_replace($sli , $resulttmp , $datastructure);

      $sli = '/\{subloop'.$nameofsubloopTmp.':'.$re.':'.$subLoopNumberPattern.'\}(.*?)\{\/subloop'.$nameofsubloopTmp.'\}/i';
      $ret = preg_replace($sli , $result , $ret);
      return $ret;
    }

    private function preg_escape_dollar_slash($string) {
      // handle $: switch $ to $ and \ to \\
      return preg_replace('/(\$|\\\\)/', '\\\\\1', $string);
    }



    private function replace_subloop_array($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass) {
      return $this->replace_subloop_with_nameofsubloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass, "array");
    }
    private function replace_subloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass) {
      return $this->replace_subloop_with_nameofsubloop($result, $subloopNode, $subLoopNumber, $subloopStructure, $datastructure, $keypass, "");
    }


    /* replacePattern: replace markup with data and do the specials like urlencode etc.*/
    private function replacePattern($datastructure, $pattern, $value, $keyIn) {
      $tmp = $this->replacePatternWithKeyin($datastructure, $pattern, $value, $keyIn);
      $tmp = $this->replacePatternWithKeyin($tmp, $pattern, $value, "");
      return $tmp;
    }

    private function value2html($valueIn) {
      ## reverse htmlentities($keyIn, ENT_QUOTES, "UTF-8", FALSE) from replacePatternWithKeyin
      $ret = $valueIn;
      $nbspReplacer = "ANDnbspSEMICOL";
      $ret = str_replace("&nbsp;", $nbspReplacer, $ret);
      $ret = html_entity_decode($ret, ENT_NOQUOTES, "UTF-8");
      $ret = str_replace($nbspReplacer, "&nbsp;", $ret);
      return $ret;
    }

    private function value2htmlAndLineFeed2LineFeed($valueIn) { # proversion
      $ret = preg_replace("/\n/", "<br>", $valueIn);
      $ret = $this->value2html($ret);
      return $ret;
    }

    private function check_utf8($str) {
      $len = strlen($str);
      for($i = 0; $i < $len; $i++){
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                $i++;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                $bytes--;
            }
        }
      }
      return true;
    } // end of check_utf8

    private function wrapper_mb_check_encoding($text, $encoding) {
      # some php installations do not have the mb_... functions
      if (function_exists(mb_check_encoding())) {
        return mb_check_encoding($text, $encoding);
      }
      return $this->check_utf8($text);
    }

    private function replacePatternWithKeyin($datastructure, $pattern, $value, $keyIn) {
      # JSON data like { "$a": "$content", }
      $valueConv2Html = $value;
      if ($this->wrapper_mb_check_encoding($valueConv2Html, 'UTF-8')) {
        $valueConv2Html = htmlentities($valueConv2Html, ENT_QUOTES, "UTF-8", FALSE); # convert to HTML
      }
      $valueConv2Html = preg_quote($valueConv2Html);  // put backslash pre of char in regex
      $value = preg_quote($value);  // put backslash pre of char in regex

      if ($this->wrapper_mb_check_encoding($keyIn, 'UTF-8')) {
        $keyIn = htmlentities($keyIn, ENT_QUOTES, "UTF-8", FALSE); # convert to HTML
      }
      $pattern = preg_quote($pattern); // put backslash pre of char in regex
      #$pattern = $this->preg_escape_dollar_slash($pattern);

      if ($keyIn!="") {
         $pattern = $keyIn.".".$pattern;
      }

      // first check on {{..}} in templante
      $datastructure = preg_replace("/\{\{param1\}\}/", $this->param1, $datastructure); # insert shortcode-param paramNUMBER
      $datastructure = preg_replace("/\{\{param2\}\}/", $this->param2, $datastructure); # insert shortcode-param paramNUMBER
      if (preg_match("/^[^\W]+$/", $pattern)) { # do not allow param with other chars than a-zA-Z0-9_
        $datastructure = preg_replace("/\{\{".$pattern."\}\}/i", $pattern, $datastructure); # insert pattern-KEY-value
      }

      if (is_numeric($pattern)) {  # preg_replace: trouble with pattern {0}
        $datastructure = str_replace("{".$pattern."}" , $valueConv2Html , $datastructure);
        $datastructure = str_replace("{".$pattern.":htmlAndLinefeed2htmlLinefeed}" , $this->value2htmlAndLineFeed2LineFeed($valueConv2Html) , $datastructure);    # proversion
        $datastructure = str_replace("{".$pattern.":html}" , $this->value2html($valueConv2Html) , $datastructure);
        $datastructure = str_replace("{".$pattern.":purejsondata}" , $value, $datastructure);
        $datastructure = str_replace("{".$pattern.":urlencode}" , urlencode(html_entity_decode($valueConv2Html)) , $datastructure);
      } else {
        $pattern = preg_replace("/\//", "\/", $pattern); # change "aa/aa" to "aa\/aa"
        $datastructure = preg_replace("/\{".$pattern."\}/i" , $valueConv2Html , $datastructure);
        $datastructure = preg_replace("/\{".$pattern.":htmlAndLinefeed2htmlLinefeed\}/i" , $this->value2htmlAndLineFeed2LineFeed($valueConv2Html) , $datastructure);    # proversion
        $datastructure = preg_replace("/\{".$pattern.":html\}/i" , $this->value2html($valueConv2Html) , $datastructure);
        $datastructure = preg_replace("/\{".$pattern.":purejsondata\}/i" , $value, $datastructure);
        $datastructure = preg_replace("/\{".$pattern.":urlencode\}/i" , urlencode(html_entity_decode($valueConv2Html)) , $datastructure);
      }
      if (trim($valueConv2Html)=="") {
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyDel:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddLeftRight:(.*?)".$this->delimiter."(.*?)".$this->delimiter."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddLeftRight:(.*?)".$this->delimiter."(.*?)".$this->delimiter."}/i" , '' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyDelFromEnd:".$this->regExpPatternDetect."}/i" , '' , $datastructure);
      } else {
        $valuetmp = stripslashes($valueConv2Html);
        # preg_replace and /e deprectated, removed from PHP7.0.0 on
        #$datastructure = preg_replace('/\{'.$pattern.':ifNotEmptyDel:([0-9]+),([0-9]+)\}/Uise' , "substr('".$valuetmp."', '\${1}', '\${2}')" , $datastructure); # from left: first digit: start, 2nd digit: length
        $datastructure = preg_replace_callback('/\{'.$pattern.':ifNotEmptyDel:([0-9]+),([0-9]+)\}/Uis' ,
              function($matches) use ($valuetmp) {
                  return substr($valuetmp, $matches[1], $matches[2]);
              },
              $datastructure); # from left: first digit: start, 2nd digit: length
        #$datastructure = preg_replace('/\{'.$pattern.':ifNotEmptyDelFromEnd:([0-9]+),([0-9]+)\}/Uise' , "substr('".$valuetmp."', '\${1}', strlen('".$valuetmp."')-'\${2}')" , $datastructure); # from left: first digit: start, 2nd digit: length
        $datastructure = preg_replace_callback('/\{'.$pattern.':ifNotEmptyDelFromEnd:([0-9]+),([0-9]+)\}/Uis',
              function($matches) use ($valuetmp) {
                  return substr($valuetmp, $matches[1], strlen($valuetmp)-$matches[2]);
              },
              $datastructure); # from left: first digit: start, 2nd digit: length
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , $valueConv2Html.'${1}' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAdd:".$this->regExpPatternDetect."}/i" , $this->value2html($valueConv2Html.'${1}') , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , $valueConv2Html.'${1}' , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i" , $this->value2html($valueConv2Html.'${1}') , $datastructure);
        $datastructure = preg_replace('/\{'.$pattern.':ifNotEmptyAddLeft:'.$this->regExpPatternDetect.'\}/i' , '${1}'.$valueConv2Html , $datastructure);
        $datastructure = preg_replace("/{".$pattern.":html,ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i" , $this->value2html('${1}'.$valueConv2Html) , $datastructure);

        ## ifNotEmptyAddLeftRight: {} as placeholders allowed, therefore: "##" is separator and marks end of tag with "##}"
        $pat1 = "{".$pattern.":ifNotEmptyAddLeftRight:(.*?)".$this->delimiter."(.*?)".$this->delimiter."}";
        $pat1 = addcslashes($pat1, "/^$\_");
        $datastructure = preg_replace("/".$pat1."/i" , '${1}'.$valueConv2Html.'${2}' , $datastructure);

        $pat1 = "{".$pattern.":html,ifNotEmptyAddLeftRight:(.*?)".$this->delimiter."(.*?)".$this->delimiter."}";
        $pat1 = addcslashes($pat1, "/^$\_");
        $datastructure = preg_replace("/".$pat1."/i" , '${1}'.$this->value2html($valueConv2Html).'${2}' , $datastructure);

        if (is_numeric($valuetmp) && preg_match("/\{".$pattern.":round,/i", $datastructure)) {
          $noofmatches = preg_match_all("/\{".$pattern.":round,([\d]+?),([a-z]+?)\}/i", $datastructure, $match);
	        for ($i=0; $i<$noofmatches; $i++) {
            $nodigits = $match[1][$i];
            $roundtype = $match[2][$i];
            $roundtypestr = "";
            if ($roundtype=="up") {
              $roundtypestr = PHP_ROUND_HALF_UP;
            }
            if ($roundtype=="down") {
              $roundtypestr = PHP_ROUND_HALF_DOWN;
            }
            if (is_numeric($nodigits) && $nodigits>=0) {
              if ($roundtypestr=="") {
                $valuetmp = round($valuetmp, $nodigits);
              } else {
                $valuetmp = round($valuetmp, $nodigits, $roundtypestr);
              }
        		  $datastructure = preg_replace("/\{".$pattern.":round,".$nodigits.",".$roundtype."\}/i" , $valuetmp , $datastructure);
            }
          }
        }

        if (preg_match("/\{".$pattern.":datetime,/i", $datastructure)) {
          $noofmatches = preg_match_all("/\{".$pattern.":datetime,(.*?),([-\d]*?)\}/i", $datastructure, $match);
          global $wp_version;
	        for ($i=0; $i<$noofmatches; $i++) {
            $timezoneoffset = $match[2][$i];
	          if (is_numeric($valueConv2Html)) {
        	    $inTs = $valueConv2Html + $timezoneoffset; # input is numeric, hence assume unixtimestamp
          	} else {
	            $valuestripslashes = stripslashes($valueConv2Html);
        	    $inTs = strtotime($valuestripslashes) + 60*60*$timezoneoffset; # strtotime gives unixtimestamp
		        }
            if (isset($wp_version)) {
            	$outTs = date_i18n($match[1][$i], $inTs); # wordpress-funtion, does not work outside wordpress, check first if $wp_version is existing
            } else {
	            $outTs = date($match[1][$i], $inTs);
            }
            $outTs = preg_replace("/\&\#8220\;/", "", $outTs);
            $outTs = preg_replace("/\"/", "", $outTs);
            $match[1][$i] = preg_replace('/\//', '\\/', $match[1][$i]);
      		  $datastructure = preg_replace("/\{".$pattern.":datetime,".$match[1][$i].",".$match[2][$i]."\}/i" , $outTs , $datastructure);
	       }
        }
      }

      $datastructure = preg_replace("/\n/" , "", $datastructure);

      # a markup can be defined as unique: display only the FIRST data, ignore all following...
      $uniqueParam = '{'.$pattern.':unique}';
      if (preg_match("/$uniqueParam/", $datastructure)) {
    	   # there is a markup defined as unique
         $datastructure = str_replace("{".$pattern.":unique}" , $valueConv2Html , $datastructure);
         $this->triggerUnique[$valueConv2Html]++;
         if ($this->triggerUnique[$valueConv2Html]>1) {
            return "";
         }
      }

      $datastructure = stripslashes($datastructure); # remove backslashes
      $datastructure = preg_replace("/".urlencode(html_entity_decode("\\"))."/", "", $datastructure); # remove urlencoded-backslashes
      return $datastructure; # return template filled with data
    }

    private function process_subloop_param($sublooptype, $datastructure, $callingKey, $keypass) {
      $rege = "([a-zA-Z0-9\$\_\-]*)";
      $regereturn = "";
      $this->debugEcho("process_subloop '.$sublooptype.' || $callingKey || $keypass<br>");
      if (is_string($callingKey)) {
        $rege = $callingKey;
        if ($keypass!="") {
          $rege = $keypass.".".$callingKey;
        }
        $subloopNode = $callingKey; # name of subloop-datanode
        $regereturn = $rege;
        $rege = preg_replace('/'.'\$'.'/', '\\\$', $rege);
	$this->debugEcho('pattern'.$sublooptype.': <i>'.'/{subloop'.$sublooptype.':'.@htmlentities($rege).':'.@htmlentities($subLoopNumber).'}(.*?){\/subloop'.$sublooptype.':'.@htmlentities($rege).'}/'."</i><br>");
        preg_match_all('/{subloop'.$sublooptype.':'.$rege.':([\-0-9,]*)}(.*?){\/subloop'.$sublooptype.':'.$rege.'}/i', $datastructure, $subloopStructureArr);


        $subLoopNumber = $subloopStructureArr[1];
        $subloopStructure = $subloopStructureArr[2];
      } else {
	preg_match('/{subloop'.$sublooptype.':'.$rege.':([\-0-9,]*)}/', $datastructure, $subloopNodeArr);
        $subloopNode = $subloopNodeArr[1]; # name of subloop-datanode
        $subLoopNumber = $subloopNodeArr[2];
        preg_match('/{subloop'.$sublooptype.':'.$subloopNode.':'.$subLoopNumber.'}(.*?){\/subloop'.$sublooptype.':'.$subloopNode.'}/', $datastructure, $subloopStructureArr);
        $subloopStructure = $subloopStructureArr[1];
      }
      if ($subloopStructure=="") {
        #  subloop not found, e.g. in closing-tag no subloopNode?
        preg_match('/{subloop'.$sublooptype.':'.$subloopNode.':'.$subLoopNumber.'}(.*?){\/subloop'.$sublooptype.'}/', $datastructure, $subloopStructureArr);
        $subloopStructure = $subloopStructureArr[1];
      }
      if ($subloopStructure=="") {
        $subloopHTML = $datastructure;
      } else {
        $subloopHTML = $subloopStructure;
      }

      $this->debugEcho('subloop end: '.$sublooptype.' datastructure: <i>'.@htmlentities($datastructure)."</i> // node: <i>".@htmlentities($subloopNode)."</i> // subLoopNumber: <i>".@htmlentities($subLoopNumber)."</i> // html: <i>".@htmlentities($subloopHTML)."</i><br>");
      return array ($subloopNode, $subLoopNumber, $subloopHTML, $regereturn);
    }


    private function process_subloop_array($datastructure, $callingKey, $keypass) {
      return $this->process_subloop_param("-array", $datastructure, $callingKey, $keypass);
    }

    private function process_subloop($datastructure, $callingKey, $keypass) {
      return $this->process_subloop_param("", $datastructure, $callingKey, $keypass);
    }


    /* checkIfAddToResult: the code created by the template and the JSON-data is checked on
    - needed keywords --> return "" if not
    - ignore flag $this->addToResult might set somewhere before to FALSE
    */
    private function checkIfAddToResult($resultCode) {
      # is at least one keywords in the text? if not ignore this text
      if ($this->oneOfTheseWordsMustBeIn!="") {
        $isIn = $this->checkKeywordArray($this->oneOfTheseWordsMustBeIn, $resultCode);
        if (!$isIn) {   return "";    } # none of the keywords was found: ignore this
      }

      # if one of the keywords is in the text, ignore it
      if ($this->oneOfTheseWordsMustNotBeIn!="") {
        $isKeywordThere = $this->checkKeywordArray($this->oneOfTheseWordsMustNotBeIn, $resultCode);
        if ($isKeywordThere) {   return "";    } # one of the keywords was found: ignore this
      }

      if ($this->addToResult) {
        return $resultCode; # ok, add this code
      }
      return "";
    }

    /* is one of the keywords in the text? */
    private function checkKeywordArray($kwArrList, $resultCode) {
      $kwArr = explode(",", trim($kwArrList));
      $isIn = FALSE;
      foreach($kwArr as $keyword) {
          if (trim($keyword)=="") { continue; }
          $kw = $this->createUtf8Keyword($keyword);
          $isIn = $this->checkKeyword($kw, $resultCode);
          if ($isIn) {
            return TRUE;
          }
      }
      return $isIn;
    }

    /* is keyword in the text? */
    private function checkKeyword($kw, $resultCode) {
       if (preg_match("/".$kw."/i", strip_tags($resultCode))) {
          return TRUE;
       }
       return FALSE;
    }

    /* shortcode-text might be encoded or not */
    private function createUtf8Keyword($kw) {
      $kw = htmlentities(trim($kw), ENT_COMPAT, 'UTF-8', FALSE);
      if ($kw=="") {
        # if input was not utf8
        $kw = htmlentities(utf8_encode(trim($kw)), ENT_COMPAT, 'UTF-8', FALSE);
      }
      return $kw;
    }


    /* debugEcho: display debugMessages or not */
    private function debugEcho($txt, $paramIn="", $object=NULL) {
      if ($paramIn=="wordpressticket") {
        echo $txt."<br>please open ticket at <a href=\"https://wordpress.org/plugins/json-content-importer/\" target=\"_blank\">wordpress.org</a><hr>";
      }
      if ($this->showDebugMessages) {
        if ($paramIn=="showdump") {
          echo "$txt<br><i>";
         # print_r($object);
          echo "</i><br>";
        } else if ($paramIn=="") {
          echo $txt;
        }
      }
    }

    /* clearUnusedArrayDatafields: remove unfilled markups: we loop the JSON-data, not the markups. If there is no JSON, the markup might stay markup... */
    private function clearUnusedArrayDatafields($datastructure) {
      #return $datastructure; # test only

      $datastructure = preg_replace('/\/'.'\{/', "BRACKETLEFTBRACKETLEFT", $datastructure); # if { or } should be stay in the output mask them by /
      $datastructure = preg_replace('/\/'.'\}/', "BRACKETRIGHTBRACKETRIGHT", $datastructure);

      $regExpPatt = "(.+?)";  # (.*?) not ok when result should be JSON itself
      $datastructure = preg_replace("/{".$regExpPatt."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":urlencode}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":htmlAndLinefeed2htmlLinefeed}/i", "", $datastructure);
      $datastructure = preg_replace('/\{'.$regExpPatt.':html\}/i', '', $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":purejsondata}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":unique}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAdd:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAdd:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddLeft:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyDel:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyAddLeftRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":html,ifNotEmptyAddLeftRight:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyDelFromEnd:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":ifNotEmptyDelFromEnd:".$this->regExpPatternDetect."}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":round,([\d]+?),([a-z]+?)}/i", "", $datastructure);
      $datastructure = preg_replace("/{".$regExpPatt.":datetime,,(.*?),([-\d]*?)}/i", "", $datastructure);

      $datastructure = preg_replace("/BRACKETLEFTBRACKETLEFT/", '{' , $datastructure);
      $datastructure = preg_replace("/BRACKETRIGHTBRACKETRIGHT/", '}', $datastructure);
      return $datastructure;
    }
	}
?>