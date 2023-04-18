<?php

namespace forge12\contactform7\CF7Captcha {
    if(!defined('ABSPATH')){
        exit();
    }

    /**
     * Class Support
     */
    class Support
    {
        /**
         * @var null
         */
        private static $_instance = null;

        /**
         * @return Support|null
         */
        public static function getInstance(){
            if(self::$_instance == null){
                self::$_instance = new Support();
            }
            return self::$_instance;
        }

        private function __construct()
        {
            $settings = get_option('f12_captcha_settings');
            if(is_array($settings) && (isset($settings['rules']['support']) && $settings['rules']['support'] != 0) || !isset($settings['rules']['support'])) {
                add_action('wp_footer', array($this, 'addLink'), 9999);
            }
        }

        public function addLink(){
            ?>
            <noscript><!-- Captcha Protection Powered By Forge12 Interactive --><a title="WordPress Agentur" href="https://www.forge12.com/">Captcha by Forge12</a></noscript>
            <?php
        }
    }
}