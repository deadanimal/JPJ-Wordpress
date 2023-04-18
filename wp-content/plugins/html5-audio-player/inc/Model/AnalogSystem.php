<?php
namespace H5AP\Model;
use H5AP\Helper\DefaultArgs;
use H5AP\Helper\Functions as Utils;
use H5AP\Template\Player;

class AnalogSystem{

    public static function html($id){
        $data = DefaultArgs::parsePlayerArgs(self::getData($id));
        return Player::html($data);
    }

    public static function getData($id){
        $width = Utils::meta($id, 'width', ['width' => '100', 'unit' => '%']);
        $autoplay = Utils::meta($id, 'autoplay', '0') === '1' ? true: false;
        $repeat = Utils::meta($id, 'repeat', '0') === '1' ? true : false;
        $muted = Utils::meta($id, 'muted', '0') === '1' ? true : false;

        $attr = $autoplay ? ' autoplay' : '';
        $attr .= $repeat ? ' loop' : '';
        $attr .= $muted ? ' muted' : '';

        $options = [
            
        ];

        $infos = [
            'autoplay' => $autoplay,
            'repeat' => $repeat,
            'muted' => $muted,
        ];

        $template = [
            'width' => $width['width'].$width['unit'],
            'autoplay' => Utils::meta($id, 'autoplay', '0') === '1' ? ' autoplay ': '',
            'repeat' => Utils::meta($id, 'repeat', '0') === '1' ? ' loop ': '',
            'muted' => Utils::meta($id, 'muted', '0') === '1' ? ' muted ': '',
            'attr' => $attr,
            'source' => Utils::meta($id, 'h5vp_default_audio')
        ];

        return [
            'options' => $options,
            'infos' => $infos,
            'template' => $template,
        ];
    }
}