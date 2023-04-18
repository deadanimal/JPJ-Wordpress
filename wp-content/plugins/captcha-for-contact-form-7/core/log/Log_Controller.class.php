<?php
namespace forge12\contactform7\CF7Captcha\core\log{
    if(!defined('ABSPATH')){
        exit;
    }

    require_once('Log_Item.class.php');
    require_once('Log_WordPress.class.php');
    require_once('Log_WordPress_PostType.class.php');
    require_once('Log_WordPress_Taxonomy_log_status.class.php');
    require_once('Log_WordPress_PostType_Menu.class.php');
    require_once('Array_Formatter.class.php');

    class Log_Controller{
        private static $instance;

        public static function get_instance(){
            if(self::$instance == null){
                self::$instance = new Log_Controller();
            }

            return self::$instance;
        }

        private function __construct(){
            Log_WordPress_Taxonomy_log_status::init();
            Log_WordPress_PostType::init();
            Log_WordPress_PostType_Menu::init();
        }
    }

    Log_Controller::get_instance();
}