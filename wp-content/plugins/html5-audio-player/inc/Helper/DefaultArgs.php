<?php
namespace H5AP\Helper;

class DefaultArgs{

    public static function parsePlayerArgs($data){
        $default = self::player();
        $data = wp_parse_args( $data, $default );
        $data['template'] = wp_parse_args( $data['template'], $default['template'] );
        return $data;
    }

    public static function player(){
        return [
            'options' => [],
            'infos' => [],
            'template' => [
                'width' => '100%',
                'autoplay' => '',
                'loop' =>'',
                'muted' => '',
                'source' => ''
            ]
        ];
    }
}