<?php
namespace H5AP\Addons;
if(!defined('ABSPATH')) {
    return;
}
use H5AP\Helper\DefaultArgs;
use H5AP\Model\AdvanceSystem;
use H5AP\Services\VideoTemplate;
use H5AP\Template\Player;


class Block{
    function register(){
        add_action('init', [$this, 'register_block']);
    }

    function register_block(){
        wp_register_script( 'bplugins-plyrio', H5AP_PLUGIN_DIR . 'js/plyr-v3.7.2.js' , array(), H5AP_PLUGIN_VERSION , false );
        wp_register_script(	'h5ap-blocks', H5AP_PLUGIN_DIR.'dist/blocks.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'jquery', 'bplugins-plyrio' ), H5AP_VER, true );
        
        
        wp_register_style( 'bplugins-plyrio', H5AP_PLUGIN_DIR . 'style/player-style.css', array(), H5AP_PLUGIN_VERSION , 'all'  );
        wp_register_style( 'h5ap-blocks', H5AP_PLUGIN_DIR . 'dist/blocks.css', array('bplugins-plyrio'), H5AP_VER, 'all' );

        register_block_type(H5AP_PATH .'blocks/audioplayer', array(
            'editor_script' => 'h5ap-blocks',
            'editor_style' => 'h5ap-blocks',
            'render_callback' => [$this, 'render_callback_audioplayer']
        ));
    }

    public function render_callback_audioplayer($attrs, $content){
        wp_enqueue_script('h5ap-public');
        wp_enqueue_style('bplugins-plyrio');

        $attr = $attrs['autoplay'] ? ' autoplay' : '';
        $attr .=$attrs['repeat'] ? ' loop' : '';
        $attr .= $attrs['muted'] ? ' muted' : '';

        $attrs['template'] = [
            'width' => $attrs['width'],
            'attr' => $attr,
            'source' => $attrs['source'],
        ];

        $args = DefaultArgs::parsePlayerArgs($attrs);
        return Player::html($args);
    }

}

