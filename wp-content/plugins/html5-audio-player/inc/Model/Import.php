<?php
namespace H5AP\Model;
use H5AP\Helper\Functions;

class Import{

    public static function meta(){
        $players = new \WP_Query(array(
            'post_type' => 'audioplayer',
            'post_status' => 'any',
            'posts_per_page' => -1
        ));
    
        while ($players->have_posts()): $players->the_post();
    
            $id = get_the_ID();
            // $_h5ap_plyr = get_post_meta($id, '_h5ap_plyr', true);
            $source = get_post_meta($id, '_ahp_audio-file', true);
            $repeat = get_post_meta($id, '_ahp_audio-repeat', true) === 'loop' ? '1' : '0';
            $muted = get_post_meta($id, '_ahp_audio-muted', true) === 'on' ? '1' : '0';
            $autoplay = get_post_meta($id, '_ahp_audio-autoplay', true) === 'on' ? '1' : '0';
            $width = get_post_meta($id, '_ahp_audio-size', true);
            $width_unit = 'px';
            $matches[0][0] = 300;
            preg_match_all('!\d+!', $width, $matches);
            if(is_array($matches[0]) && count($matches[0]) < 1){
                $matches[0][0] = 100;
                $width_unit = '%';
            }
           
            $newData = array(
                'h5vp_default_audio' => $source,
                'muted' => $muted,
                'autoplay' => $autoplay,
                'width' => array(
                        'width' => $matches[0][0],
                        'unit' => $width_unit,
                    ),
                'repeat' => $repeat,
            );
    
            if (false == metadata_exists('post', $id, '_h5ap_plyr')) {
                update_post_meta($id, '_h5ap_plyr', $newData);
            }

        endwhile;
    }

    public static function createBlock(){
        $query = new \WP_Query(array(
            'post_type' => 'audioplayer',
            'post_status' => 'any',
            'posts_per_page' => -1
        ));

        $output = [];
        while($query->have_posts()): $query->the_post();
            $id = \get_the_ID();
            $width = Functions::meta($id, 'width', ['width' => '100', 'unit' => '%']);
            $output[] = [
                'ID' => $id,
                'post_content' => '<!-- wp:h5ap/parent --><div class="wp-block-h5ap-parent"><!-- wp:h5ap/audioplayer '.json_encode([
                    'source' => Functions::meta($id, 'h5vp_default_audio', true),
                    'repeat' => Functions::meta($id, 'repeat', true),
                    'autoplay' => Functions::meta($id, 'autoplay', true),
                    'muted' => Functions::meta($id, 'muted', true),
                    'Width' => $width['width'].$width['unit'],
                ]).' /--></div><!-- /wp:h5ap/parent -->'
            ];
        endwhile;

        return $output;
    }

}

function h5ap_import_data_ajax(){
    // Import::meta();
    echo wp_json_encode(array(
        'success' => true,
    ));
    die();
}
add_action("wp_ajax_h5ap_import_data", 'h5ap_import_data_ajax');
