<?php
class jsonSelector{
    function buildArray($array, $matched_data)
    {
        $new = array();
        $current = &$new;
    
        $last_key = count($array) - 1;
        foreach($array as $key => $value)
        {
            $content = ($key == $last_key) ? $matched_data : array();
    
            $current[$value] = $content;
            $current = &$current[$value];
    
        }
        return $new;
    }
    
    function arrayGet($array, $arr_key)
    {
        if (empty($arr_key)) return $array;
    
        foreach ($arr_key as $segment)
        {
			$segment = preg_replace("/#DOT##/", ".", $segment);
			$segment = preg_replace("/#KOMM##/", ",", $segment);
            if ( ! is_array($array) or ! array_key_exists($segment, $array))
            {
                return ;
            }
    
            $array = $array[$segment];
        }
    
        $matched_result = $array;
    
        if(empty($matched_result)) return;
        
        $nested_arr = $this->buildArray($arr_key, $matched_result);
        return $nested_arr;
    }

    function compareArrays($array1, $array2){
        $result = array("more"=>array(),"less"=>array(),"diff"=>array());
        foreach($array1 as $k => $v) {
          if(is_array($v) && isset($array2[$k]) && is_array($array2[$k])){
            $sub_result = $this->compareArrays($v, $array2[$k]);
            //merge results
            foreach(array_keys($sub_result) as $key){
              if(!empty($sub_result[$key])){
                $result[$key] = array_merge_recursive($result[$key],array($k => $sub_result[$key]));
              }
            }
          }else{
            if(isset($array2[$k])){
              if($v !== $array2[$k]){
                $result["diff"][$k] = array("from"=>$v,"to"=>$array2[$k]);
              }
            }else{
              $result["more"][$k] = $v;
            }
          }
        }
        foreach($array2 as $k => $v) {
            if(!isset($array1[$k])){
                $result["less"][$k] = $v;
            }
        }
        return $result;
    }

    function associativeToIndex($array){
        if(!is_array($array) || is_object($array)) return $array;

        $keyNumerics = true;
        foreach($array as $key => $value){
            if(is_array($value) || is_object($value)){
                $array[$key] = $this->associativeToIndex($value);
            }

            if(! is_numeric($key)) $keyNumerics = false;
        }

        if($keyNumerics){
            $array = array_values($array);
        }

        return $array;


    }

    function filterSelectors($selectors){
        if(empty($selectors)) return $selectors;

        $selector_array  = explode(',', $selectors);
        $selector_array = array_map(function($val){ return explode('.', $val);}, $selector_array);
        
        if(count($selector_array) < 2) return $selector_array;

        $cleanSelectors = [];
        for($i = 0; $i < count($selector_array); $i++){
            $currentselector = $selector_array[$i];

            //looking for same selector
            $foundMatch = false;
            foreach($selector_array as $idx => $subloop){
                if($i == $idx) continue;

                $compare = $this->compareArrays($currentselector, $subloop);

                //if have difference node, skip (suspect uniq selector)
                if(isset($compare['diff']) && !empty($compare['diff'])) continue;

                if(isset($compare['more']) && !empty($compare['more'])){
                    $foundMatch = true;
                } 
            }

            if(! $foundMatch){
                $cleanSelectors[] = $currentselector;
            }
        }

        return $cleanSelectors;

    }

    function processJson($json, $selectors){
        
        $origin_array    = json_decode($json, true);

        $selector_array = $this->filterSelectors($selectors);        

        $return = [];
        foreach($selector_array as $idx => $selector){

            $ret = $this->arrayGet($origin_array, $selector);

            if(empty($ret)) continue;

            //check differences to eliminate duplicate data
            $compareArrays = $this->compareArrays($return, $ret);

            if(empty($compareArrays['less']) && empty($compareArrays['diff'])) continue;
            
            $return = array_replace_recursive($return, $ret);
        }

        //scan for indexed array
        $return = $this->associativeToIndex($return);

        $return = json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); //JSON_PRETTY_PRINT make json view prettier
		$return = preg_replace("/#DOT##/", ".", $return);
		$return = preg_replace("/#KOMM##/", ",", $return);
		
        return $return;
    }
}
/*
$selector = 'hotels.hotel.0,hotels.hotel.5';
$jsonSelector = new jsonSelector();
$result = $jsonSelector->processJson($json, $selector);
echo $result;
*/
?>
