<?php
namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerRegistration
     */
    class ControllerRegistration{
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
                require_once('Validator.class.php');
                $Validator = new Validator();
            }
        }

        /**
         * Validate if Comment Protection is enabled
         * @return boolean
         */
        public function isCaptchaProtectionEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_registration', 'wp_login_page');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_registration_method', 'wp_login_page');
        }
    }
}