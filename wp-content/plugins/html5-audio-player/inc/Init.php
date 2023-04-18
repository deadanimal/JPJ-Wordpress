<?php

namespace H5AP;

use H5AP\Model\Import;

if (!defined('ABSPATH')) {
    exit;
} //Exit if accessed directly

class Init{
    private static $instance = null;
    private function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
        register_activation_hook(__FILE__, [$this, 'h5ap_plugin_activate']);
        add_action('admin_init', [$this, 'h5ap_plugin_redirect']);
	}

    public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

    // Register Activation Hook

    public function h5ap_plugin_activate() {
        Import::meta();
        add_option('h5ap_plugin_do_activation_redirect', true);
    }

    public function h5ap_plugin_redirect() {
        if (get_option('h5ap_plugin_do_activation_redirect', false)) {
            delete_option('h5ap_plugin_do_activation_redirect');
            wp_redirect('edit.php?post_type=audioplayer&page=how-to');
        }

        if(get_option('h5ap_import', '1.4.1') != '2.1.3'){
            Import::meta();
            update_option('h5ap_import', '2.1.3');
        }
    }


    public function i18n() {
		// load_plugin_textdomain('b3d-viewer-lite',false,dirname( plugin_basename( H5AP_PATH ) ) . '/languages/');
		// load_plugin_textdomain('b3d-viewer-lite',false,dirname( plugin_basename( H5AP_PATH ) ) . '/languages/');
	}
   
    public static function get_services(){
        return [
            Addons\Controls\Controls::class,
            Addons\Addons::class,
            Addons\Block::class,
            Model\Import::class,
            Model\Block::class,
            Model\AdvanceSystem::class,
            Model\AnalogSystem::class,
            Helper\Functions::class,
            Helper\DefaultArgs::class,
            Base\GlobalChanges::class,
            Base\AdminNotice::class,
            Base\EnqueueAssets::class,
            Base\Menu::class,
            PostType\AudioPlayer::class,
            PostType\Metabox::class,
            Base\Shortcode::class,
            Template\Player::class,
        ];
    }
    
    public static function init(){
        foreach(self::get_services() as $class){
            $file = str_replace('\\', '/', $class);
            $file = H5AP_PATH.str_replace('H5AP', 'inc', $file).'.php';
            if(file_exists($file)){
                require_once($file);
                $services = self::instantiate($class);
                if(method_exists($services, 'register')){
                    $services->register();
                }
            }
        }
    }

    private static function instantiate($class){
        return new $class();
    }
}


Init::instance();

