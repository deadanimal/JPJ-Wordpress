<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }


    /**
     * Async Task for Rules
     */
    class RulesAjax
    {
        public function __construct()
        {

            add_action('admin_enqueue_scripts', array($this, 'loadAssets'));
        }

        public function loadAssets()
        {
            wp_enqueue_script('f12-cf7-rules-ajax', plugin_dir_url(__FILE__) . 'assets/f12-cf7-rules-ajax.js', array('jquery'), null, true);
            wp_localize_script('f12-cf7-rules-ajax', 'f12_cf7_captcha_rules', array('ajaxurl' => admin_url('admin-ajax.php')));
        }

        public static function get_blacklist_content()
        {
            $content = file_get_contents('https://api.forge12.com/v1/tools/blacklist.txt');
            return $content;
        }

        public static function handleBlacklistSync()
        {
            $content = self::get_blacklist_content();
            echo wp_json_encode(array('value' => $content));
            wp_die();
        }
    }

    new RulesAjax();
}