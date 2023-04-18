<?php
// Control core classes for avoid errors
if (class_exists('CSF'))
{

  //-------------------------------------------------------------------------
  //   Player type
  // ------------------------------------------------------------------------
  $prefix = '_h5ap_plyr';


  CSF::createMetabox($prefix, array(
    'title' => 'Player Configuration',
    'post_type' => 'audioplayer',
  ));

  CSF::createSection($prefix, array(
    'fields' => array(
      array(
        'id' => 'h5vp_default_audio',
        'type' => 'upload',
        'title' => 'Audio source',
        'library' => 'audio',
        'placeholder' => 'http://',
        'button_title' => 'Add Audio',
        'remove_title' => 'Remove Audio',
      ),
      array(
        'id' => 'muted',
        'type' => 'switcher',
        'title' => 'Muted',
        'default' => false,
      ),
      array(
        'id' => 'autoplay',
        'type' => 'switcher',
        'title' => esc_html__('AutoPlay', 'html5audio') ,
        'desc' => 'AutoPlay will only work if you keep the player muted according the the latest autoplay policy. <a href="https://developers.google.com/web/updates/2017/09/autoplay-policy-changes" target="_blank" >Read More</a>',
        'default' => false,
      ),
      array(
        'id' => 'width',
        'type' => 'dimensions',
        'height' => false,
        'units' => array('px', '%') ,
        'title' => 'Player Width',
        'default' => array(
          'width' => '100',
          'unit' => '%',
        ),
      ),
      array(
        'id' => 'repeat',
        'type' => 'switcher',
        'title' => esc_html__('Repeat', 'streamcast') ,
        'default' => '0',
      ) ,
    )
  ));
}

