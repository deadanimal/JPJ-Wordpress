<?php
namespace H5AP\Model;

class Block{

    public static function get($id){
        $content_post = get_post($id);
        $content = $content_post->post_content;
        return $content;
    }

    public static function getBlock($id){
        $blocks = parse_blocks(self::get($id));
        $out = [];
        
        if(!isset($blocks[0]['innerBlocks'])){
            return false;
        }
        foreach ($blocks[0]['innerBlocks'] as $block) {
            if($block['blockName'] === 'h5ap/audioplayer'){
                $out[] = $block['attrs'];
            }else {
                $out[] = $block;
            }
        }

        return $out;
    }
}