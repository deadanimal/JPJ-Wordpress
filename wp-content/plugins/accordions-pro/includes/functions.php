<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



function accordions_ajax_track_header(){


    $response = array();
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $header_id = isset($_POST['header_id']) ? $_POST['header_id'] : '';


    $track_header = get_post_meta($post_id, 'track_header', true);

    if(empty($track_header)):

        $track_header  = array($header_id => 1);
        update_post_meta($post_id, 'track_header', $track_header);
    else:
        $track_header[$header_id]  += 1;
        update_post_meta($post_id, 'track_header', $track_header);
    endif;

    $response['header_id'] = $header_id;

    echo json_encode($response);
    die();
}
add_action('wp_ajax_accordions_ajax_track_header', 'accordions_ajax_track_header');
add_action('wp_ajax_nopriv_accordions_ajax_track_header', 'accordions_ajax_track_header');







