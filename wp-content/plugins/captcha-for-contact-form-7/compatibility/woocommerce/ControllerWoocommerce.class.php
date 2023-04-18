<?php
namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerWoocommerce
     */
    class ControllerWoocommerce{
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
            if($this->isLoginProtectionEnabled()) {
                require_once('login/Validator.class.php');
                $Validator = new \forge12\contactform7\CF7Captcha\woocommerce\login\Validator();
            }

            if($this->isRegistrationProtectionEnabled()){
            require_once('registration/Validator.class.php');
            $Validator = new \forge12\contactform7\CF7Captcha\woocommerce\registration\Validator();
            }
        }

        /**
         * @return boolean
         */
        public function isRegistrationProtectionEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_registration', 'woocommerce');
        }

        /**
         * @return boolean
         */
        public function isLoginProtectionEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_login', 'woocommerce');
        }

        /**
         * @return mixed
         */
        public static function getLoginCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_login_method', 'woocommerce');
        }

        /**
         * @return mixed
         */
        public static function getRegistrationCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_registration_method', 'woocommerce');
        }
    }
}