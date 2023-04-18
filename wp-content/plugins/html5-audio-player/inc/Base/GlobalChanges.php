<?php
namespace H5AP\Base;

class GlobalChanges{

    /**
     * construct function
     */
    public function register(){
        // add_action('wp_footer',[$this, 'initializePlayer'], 1000);
        // add_action( 'admin_menu', [$this, 'h5ap_addon_page'] );
        add_filter( 'admin_footer_text', [$this, 'h5ap_admin_footer']);	
        add_action( 'wp_dashboard_setup', [$this, 'h5ap_add_dashboard_widgets'] );

        if(is_admin()){
            add_action('admin_menu', [$this, 'add_submenu']);
            add_action('admin_footer', [$this, 'admin_footer']);
        }
    }

    function admin_footer() {
        $screen = get_current_screen();
		if($screen->post_type === 'audioplayer' || $screen->base === 'plugins'){ ?>
            <script type="text/javascript">
                document.querySelector("ul#adminmenu a[href$='https://bplugins.com/products/html5-audio-player/#pricing']")?.setAttribute('target', '_blank');
            </script> <?php
		}
    }

      /**
     * add submenu -> Upgrade to pro
     */
    function add_submenu() {
        global $submenu;
        $link = 'https://bplugins.com/products/html5-audio-player/#pricing';
        $submenu['edit.php?post_type=audioplayer'][] = array( 'Upgrade to PRO', 'manage_options', $link, 'meta'=>'target="_blank"' );
    }

    /**
     * Footer Text Change
     */
    function h5ap_admin_footer( $text ) {
		if ( 'audioplayer' == get_post_type() ) {
			$url = 'https://wordpress.org/support/plugin/html5-audio-player/reviews/?filter=5#new-post';
			$text = sprintf( __( 'If you like <strong>Html5 Audio Player</strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'h5ap-domain' ), $url );
		}

		return $text;
	}

    function h5ap_add_dashboard_widgets() {
        wp_add_dashboard_widget( 'example_dashboard_widget', 'Support html5 Audio Player', [$this, 'h5ap_dashboard_widget_function'] );
    
        global $wp_meta_boxes;
        $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
        $example_widget_backup = array( 'example_dashboard_widget' => $normal_dashboard['example_dashboard_widget'] );
        unset( $normal_dashboard['example_dashboard_widget'] );
       $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
   } 
   
   function h5ap_dashboard_widget_function() {
   
       // Display whatever it is you want to show.
        echo '<div class="uds-box" data-id="1"></div>';
        echo '<p>It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy using the plugin and find it useful, please consider support by <b>DONATION</b> Or <b>BUY THE PRO VERSION (NO ADS)</b> of the Plugin. Your support will help encourage and support the plugin’s continued development and better user support.</p>
       
        <center>
        <a target="_blank" href="https://gum.co/wpdonate"><div><img width="200" src="'.H5AP_PLUGIN_DIR.'img/donation.png'.'" alt="Donate Now" /></div></a>
        </center>	
            
        <br />
        <div class="gumroad-product-embed" data-gumroad-product-id="dkbMR" data-outbound-embed="true"><a target="_blank" href="https://gumroad.com/l/dkbMR">Loading...</a></div>';
    }
}
