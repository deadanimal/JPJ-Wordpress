<?php
namespace PDFP\Services;
use PDFP\Helper\DefaultArgs;
// use PDFP\Helper\Functions;
use PDFP\Services\AnalogSystem;

class CSSGenerator{
    public static $uniqid = null;
    protected static $styles = [];
    protected static $mediaQuery = [];

    public function __construct($id){
        self::$uniqid = $id;
    }

    /**
     * Render all styles added on $style variable
     */
    public static function renderStyle($template){
        $id = self::$uniqid;

        $output = '';
        foreach(self::$styles as $selector => $style){
            $new = '';
            foreach($style as $property => $value){
                if($value == ''){
                    $new .= $property;
                }else {
                    $new .= " $property: $value;";
                }
            }
            $output .= "$selector { $new }";
        }
        
        foreach(self::$mediaQuery as $query => $styles){
            $output .= $query."{";
            foreach($styles as $selector => $style){
                $new = '';
                foreach($style as $property => $value){
                    if($value == ''){
                        $new .= $property;
                    }else {
                        $new .= " $property: $value;";
                    }
                }
                $output .= "$selector { $new }";
            }
            $output .= "}";
        }



        return $output;
    }

    /**
     * Add Style to style variable
     */
    public static function addStyle($selector, $styles, $mediaQuery = false){
        if($mediaQuery){
            if(array_key_exists($mediaQuery, self::$mediaQuery)){
                if(array_key_exists($selector, self::$mediaQuery[$mediaQuery])){
                    self::$mediaQuery[$mediaQuery][$selector] = wp_parse_args(self::$mediaQuery[$mediaQuery][$selector], $styles);
                }else {
                    self::$mediaQuery[$mediaQuery] = wp_parse_args(self::$mediaQuery[$mediaQuery], [$selector => $styles]);
                }
             }else {
                 self::$mediaQuery[$mediaQuery] = [$selector => $styles];
             }
        }else {
            if(array_key_exists($selector, self::$styles)){
                self::$styles[$selector] = wp_parse_args(self::$styles[$selector], $styles);
             }else {
                 self::$styles[$selector] = $styles;
             }
        }
        
    }

}