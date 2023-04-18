<?php
namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerLogin
     */
    class ControllerLogin{
        /**
         * Constructor
         */
        public function __construct(){
            $this->init();
        }

        /**
         * Init all relevant functions
         * @return void
         */
        private function init(){
            if($this->isCaptchaProtectionEnabled()) {
                require_once('LoginValidator.class.php');
                $LoginValidator = new LoginValidator();
            }
        }

        /**
         * Validate if Comment Protection is enabled
         * @return boolean
         */
        public function isCaptchaProtectionEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_login', 'wp_login_page');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_login_method', 'wp_login_page');
        }
    }
}