<?php
namespace H5AP\Base;

class Menu {
    protected static $_instance = null;

    /**
     * construct function
     */
    public function register(){
        add_action( 'init', [$this, 'registerSettings'] );
        add_action( 'admin_menu', [$this, 'registerMenu'] );
        add_action( 'media_buttons', [$this, 'h5ap_shortcode_button'],15 );
        add_action('wp_head', [$this, 'add_inline_player_style']);
    }

    /**
     * Create instance function
     */
    public static function instance(){
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Register Settings
     */
    function registerSettings() {
        register_setting(
            'h5ap_settings',
            'h5ap_gutenberg_enable',
            array(
                'type'         => 'boolean',
                'show_in_rest' => true,
                'default'      => false,
            )
        );
    
    }

    /**
     * Register Menu
     */
    function registerMenu() {
        $page_hook_suffix = add_submenu_page(
            'edit.php?post_type=audioplayer',
            __( 'How To', 'h5ap' ),
            __( 'How To', 'h5ap' ),
            'manage_options',
            'how-to',
            [$this, 'settings_callback']
        );
    
        add_action( "admin_print_scripts-{$page_hook_suffix}", [$this, 'eqnueueAssets'] );
    }

    /**
     * Settings Page Callback
     */
    function settings_callback() {
        echo '<div id="h5ap-settings"></div>';
    }
    
    function eqnueueAssets() {
        wp_enqueue_script( 'h5ap-settings', H5AP_PLUGIN_DIR . 'dist/settings.js', array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), H5AP_PLUGIN_VERSION, true );
        wp_enqueue_style( 'h5ap-settings', H5AP_PLUGIN_DIR . 'dist/settings.css', array( 'wp-components' ) );
    }

    function h5ap_shortcode_button() {
		// $img = H5VP_PRO_PLUGIN_DIR .'img/icn.png';
		$context = '<a class="thickbox button" id="h5ap_shortcode_button" title="Insert Audio" style="outline: medium none !important; cursor: pointer;" >
		Add Inline Audio Player</a>';
		echo $context;
	}

    function add_inline_player_style(){
        ?>
            <style>
                .h5ap_inline_player {
                    display: inline-block;
                    line-height: 1.45;
                }
                .h5ap_inline_player span {
                    display: inline-block;
                    line-height: 1.20;
                    font-size: 130%;
                    width: auto;
                    height: auto;
                    cursor: pointer;
                }
                .h5ap_inline_player span.dashicons-controls-pause {
                    display: none;
                }
                </style>
        <?php
    }
}
Menu::instance();