<?php
namespace H5AP\Model;
use H5AP\Model\Block;
use H5AP\Helper\DefaultArgs;
use H5AP\Template\Player;

class AdvanceSystem{

    public static function html($id){

        $blocks =  Block::getBlock($id);
        $output = '';
        if(is_array($blocks)){
            foreach($blocks as $block){
                if(isset($block['attrs'])){
                    $output .= render_block($block);
                }else {
                    $data = DefaultArgs::parsePlayerArgs(self::getData($block));
                    $output .= Player::html($data);
                }
            }
        }
        return $output;
    }

    public static function getData($block){

        $attr = self::i($block, 'autoplay', '', false) == true ? ' autoplay' : '';
        $attr .= self::i($block, 'repeat', '', false) == true ? ' loop' : '';
        $attr .=  self::i($block, 'muted', '', false) == true ? ' muted' : '';
        
        return [
            'options' => [],
            'infos' => [],
            'template' => [
                'width' => self::i($block, 'Width', '', '100%'),
                'autoplay' => self::i($block, 'autoplay', '', false),
                'loop' => self::i($block, 'repeat', '', false),
                'muted' => self::i($block, 'muted', '', false),
                'source' => self::i($block, 'source'),
                'attr' => $attr
            ]
        ];
    }

    public static function i($array, $key1, $key2 = '', $default = false){
        if(isset($array[$key1][$key2])){
            return $array[$key1][$key2];
        }else if (isset($array[$key1])){
            return $array[$key1];
        }
        return $default;
    }



    public static function parseControls($controls){
        $newControls = [];
        if(!is_array($controls)){
            return ['play-large','rewind', 'play', 'fast-forward', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'];
        }
        foreach($controls as $key => $value){
            if($value == 1){
                array_push($newControls, $key);
            }
        }
        return $newControls;
    }
}