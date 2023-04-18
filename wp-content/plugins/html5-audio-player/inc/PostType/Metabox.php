<?php
namespace H5AP\PostType;

class Metabox{

    /**
     * construct function
     */
    public function register(){
        add_action( 'add_meta_boxes', [$this, 'myplugin_add_meta_box'] );
    }


    /**
     * register metabox
     */
    function myplugin_add_meta_box() {
        add_meta_box('myplugin', __( 'Support Html5 Audio Player', 'html5-audio-player' ), [$this, 'callback_2'], 'audioplayer', 'normal', 'high');	
    
        add_meta_box(
            'myplugin1',
            __( 'Get Plugin Customization Service', 'html5-audio-player' ),
            [$this, 'callback'],
            'audioplayer',
            'side'
        );	
        
    }

    /**
     * Plugins Customization
     */
    function callback( ) {
        echo'<a target="_blank" href="https://bplugins.gumroad.com/l/psupport"><img width="100%" src="'.H5AP_PLUGIN_DIR.'/img/customization.png" ></a>';
    }

    /**
     * Support HTML5 audio player
     */
    function callback_2( ) {
        echo '<p>It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy using the plugin and find it useful, please consider support by  <b>DONATION</b> or <b>BUY THE PRO VERSION (No ads)</b> of the Plugin. Your support will help encourage and support the plugins continued development and better user support.</p>
        <center>
            <a target="_blank" href="https://gum.co/wpdonate"><div><img width="200" src="'.H5AP_PLUGIN_DIR.'img/donation.png'.'" alt="Donate Now" /></div></a>
        </center> <br />
        <div class="gumroad-product-embed" data-gumroad-product-id="dkbMR" data-outbound-embed="true"><a target="_blank" href="https://gumroad.com/l/dkbMR">Loading...</a></div>';
    }
}