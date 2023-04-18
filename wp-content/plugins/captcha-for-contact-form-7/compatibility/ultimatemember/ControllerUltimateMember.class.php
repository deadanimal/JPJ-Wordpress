<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControlerUltimateMember
     */
    class ControllerUltimateMember
    {
        /**
         * Admin constructor.
         */
        public function __construct()
        {
            if(class_exists( 'UM_Functions' )) {
                $this->init();
            }
        }

        /**
         * @private WordPress Hook
         */
        public function init(){
            if($this->isCaptchaForReigstrationFormEnabled()) {
                require_once('RegistrationCaptcha.class.php');
                $RegistrationCaptcha = new RegistrationCaptcha();
            }

            if($this->isCaptchaForLoginFormEnabled()){
                require_once('LoginCaptcha.class.php');
                $LoginCaptcha = new LoginCaptcha();
            }
        }

        private function isCaptchaForLoginFormEnabled(){
            return (bool)CF7Captcha::getInstance()->getSettings('protect_enable', 'ultimatemember');
        }

        private function isCaptchaForReigstrationFormEnabled(){
            return (bool)CF7Captcha::getInstance()->getSettings('protect_enable', 'ultimatemember');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_method', 'ultimatemember');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethodForLogin()
        {
            return CF7Captcha::getInstance()->getSettings('protect_login_method', 'ultimatemember');
        }
    }
}