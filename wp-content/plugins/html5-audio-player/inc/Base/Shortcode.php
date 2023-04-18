<?php
namespace H5AP\Base;
use H5AP\Model\AnalogSystem;
use H5AP\Model\AdvanceSystem;

class Shortcode {

    /**
     * construct function
     */
    public function register(){
        add_shortcode('player',[$this, 'player']);
        add_shortcode('h5ap_inline_player',[$this, 'h5ap_inline_player']);
    }

    /**
     * audio player
     */
    public function player($atts){
        $post_type = get_post_type($atts['id']);
        $isGutenberg = (boolean) get_post_meta($atts['id'], 'h5ap_gutenberg_enable', true);
        if($post_type !== 'audioplayer'){
            return false;
        }

        ob_start(); 
            if($isGutenberg){
                echo( AdvanceSystem::html($atts['id']));
            }else {
                echo AnalogSystem::html($atts['id']);
            }
            
        return ob_get_clean(); 
    }

    /**
     * Inline player
     * only play button
     */
    public function h5ap_inline_player($atts){
        extract(shortcode_atts(array(
            'src' => null,
        ), $atts));

        if(true){
            return "<span class='h5ap_inline_player'>
                        <audio src='" . esc_url($src) . "'></audio>
                        <span class='dashicons dashicons-controls-play' title='play'></span>
                        <span class='dashicons dashicons-controls-pause' title='pause'></span>
                    </span>
            ";
        }
        return $src;
    }
}