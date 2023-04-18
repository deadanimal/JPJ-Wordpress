<?php

class JsonToTwig
{
    const STUB_OBJECT_NAME = 'item';
    const NEWLINE = '<br>'.PHP_EOL;

    private $json;
    private $template;
    private $showKeys = TRUE;
    private $showDefinedConds;
    private $indent_step = '';

    public function __construct($json = '', $withIndent = true)
    {
        if($withIndent){
            $this->indent_step = '	';
        }

        $this->json = json_decode($json, true);
    }

    public function getTwig()
    {
        $return = $this->generate_template();
        return $return;
    }

    public function generate_template(){

        $typeArray = $this->typeArray($this->json);
        
        if(is_array($this->json) && $typeArray == 'indexed'){
            $item = $this->generate_array($this->json);
        
        }else if(is_array($this->json) && $typeArray == 'associative'){
            $item = $this->generate_context($this->json);
        
        }else{
            $item = $this->generate_item($this->json);
        }

        return $item;
    }

    public function generate_array($array, $level = 0, $currentName = ''){
        if(empty($currentName)) $currentName = '_parent';
        $itemKey = ($level == 0) ? self::STUB_OBJECT_NAME : "level_{$level}_".self::STUB_OBJECT_NAME;

        //merge multiple array item to one array
        $newArray = array();

        $haveStringValue = false;
        if(!empty($array)){
            foreach($array as $idx => $item){
                if(is_array($item)) {
                    $newArray = array_replace_recursive($newArray, $item);
                }else{
                    $haveStringValue = true;
                }
            }
        }

        //if have string value in array, add index in loop
        if($haveStringValue){
            $return = str_repeat($this->indent_step, $level) . "{% for key_{$itemKey},{$itemKey} in {$currentName} %}".self::NEWLINE;
            
            $return .= str_repeat($this->indent_step, ($level + 1)) . "{% if {$itemKey} is not iterable %}".self::NEWLINE;
            $return .= str_repeat($this->indent_step, ($level + 2)) . "{{key_{$itemKey}}} = {{ {$itemKey} }}".self::NEWLINE;
            $return .= str_repeat($this->indent_step, ($level + 1)) . "{% endif %}".self::NEWLINE;
           
        }else{
            $return = str_repeat($this->indent_step, $level) . "{% for {$itemKey} in {$currentName} %}".self::NEWLINE;
        }

        foreach($newArray as $idx => $item){
            //check if it need to add validation
            $check_defined = $this->need_defined_validation($array, $idx, ($level + 1));

            if($check_defined){
                $return .= str_repeat($this->indent_step, $level + 1) . "{% if {$itemKey}['$idx'] is defined %}<br>".PHP_EOL;
            }

            if(is_array($item)){

                $typeArray = $this->typeArray($item);
                if($typeArray == 'indexed'){
                    $return .= $this->generate_array($item, ($level + 1), "{$itemKey}['$idx']");
                }else{
                    $return .= $this->generate_context($item, ($level + 1), "{$itemKey}['$idx']");
                }

            }else{
                $return .= $this->generate_item($idx, ($level + 1), "{$itemKey}['$idx']");
            }

            if ($check_defined) {
                $return .= str_repeat($this->indent_step, $level + 1) . "{% endif %}".self::NEWLINE;
            }

        }
        
        $return .= str_repeat($this->indent_step, $level) . "{% endfor %}".self::NEWLINE;

        return $return;

    }

    public function generate_context($object, $level = 0, $currentName = ''){
        
        if($level == 0 && empty($currentName)) $currentName = '_context';
        
        $return = "";
        foreach($object as $idx => $item){
            if(is_array($item)){
                $typeArray = $this->typeArray($item);
                if($typeArray == 'indexed'){
                    $return .= $this->generate_array($item, ($level + 1), "{$currentName}['$idx']");
                }else{
                    $return .= $this->generate_context($item, ($level + 1), "{$currentName}['$idx']");
                }
            }else{
                $return .= $this->generate_item($idx, ($level + 1), "{$currentName}['$idx']");
            }
        }

        return $return;

    }

    private function generate_item($idx, $level = 0, $currentName = '')
    {
        $propKeyValueLiteral = "{$currentName}";

        $retSAtr = str_repeat($this->indent_step, $level);
		if ( $this->showKeys) {
			$propKeyEsc = addcslashes($idx, "{{}}");
			$retSAtr .= " $propKeyEsc = ";
		}
		$retSAtr .= "{{ $propKeyValueLiteral }}".self::NEWLINE;
		return $retSAtr;
    }

    function isAssoc($arr)
    {
        if(array_keys($arr) !== range(0, count($arr) - 1)){
            return true;
        } else{
            return false;
        }
    }

    function haveIndexedItem($arr){
        if(empty($arr)) return false;
        foreach($arr as $idx => $item){
            if(!is_array($item) && is_object($is_object)) return true;
        }

        return false;
    }

    function typeArray($arr)
    {
        return (array_keys($arr) !== range(0, count($arr) - 1)) ? 'associative' : 'indexed';
    }

    private function need_defined_validation($originArray, $index, $level){
        if(empty($originArray)) return false;

        foreach($originArray as $idx => $item){
            if(!is_array($item)) return true;

            if(! array_key_exists($index, $item)) return true;
        }

        if($level > 1){
            return true;
        }

        return false;

    }
}
