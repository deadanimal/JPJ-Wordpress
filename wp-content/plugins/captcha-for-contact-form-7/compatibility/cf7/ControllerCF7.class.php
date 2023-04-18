<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerCF7
     */
    class ControllerCF7
    {
        /**
         * Admin constructor.
         */
        public function __construct()
        {
            if(function_exists('wpcf7')) {
                $this->init();
            }
        }

        /**
         * @private WordPress Hook
         */
        public function init(){
            require_once('Frontend.class.php');
            $Frontend = new Frontend();

            require_once('Backend.class.php');
            $Backend = new Backend();

            require_once('CF7RuleValidator.class.php');
            $RuleValidator = CF7RuleValidator::getInstance();

            require_once('TimerValidatorCF7.class.php');
            $TimerValidator = TimerValidatorCF7::getInstance();

            require_once('MultipleSubmissionProtection.class.php');
            $MultipleSubmissionProtection = MultipleSubmissionProtection::getInstance();

            if($this->isCaptchaEnabled()) {
                require_once('CF7HoneypotCaptcha.class.php');
                $CF7GLOBAL = CF7HoneypotCaptcha::getInstance();
            }

            if($this->isIPValidatorEnabled()) {
                require_once('CF7IPLog.class.php');
                $CF7IPL = CF7IPLog::getInstance();
            }

            add_action('wp_enqueue_scripts', array($this, 'loadAssets'));

            add_action('wpcf7_mail_sent', [$this, 'after_wpcf7_mail_sent'], 10, 1);
            add_action('wpcf7_mail_failed', [$this, 'after_wpcf7_mail_sent'], 10, 1);
        }

        /**
         * Add log entry that the form has been submitted
         * @param \WPCF7_ContactForm $contact_form
         * @return void
         */
        public function after_wpcf7_mail_sent($contact_form){
            $data = $_POST;
            /*
             * Add Log Entries
             */
            $Log_Item = new Log_Item(
                __('CF7 Form', 'f12-captcha'),
                $data,
                'verified',
                'Contact Form 7 successfully committed');
            Log_WordPress::store($Log_Item);
        }

        public function loadAssets(){
            wp_enqueue_script('f12-cf7-captcha-reload', plugin_dir_url(__FILE__).'assets/f12-cf7-captcha-cf7.js', array('jquery'), null, true);
            wp_localize_script('f12-cf7-captcha-reload', 'f12_cf7_captcha', array(
                'ajaxurl' => admin_url('admin-ajax.php')
            ) );
        }

        private function isCaptchaEnabled(){
            return (bool)CF7Captcha::getInstance()->getSettings('protect_cf7_captcha_enable', 'cf7');
        }

        /**
         * @return bool
         */
        private function isIPValidatorEnabled(){
            return (bool)CF7Captcha::getInstance()->getSettings('protect_ip', 'ip');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_cf7_method', 'cf7');
        }

    }

    function validateCF7Dependencies()
    {
        include_once('cf7-notice-dependencies.php');
    }
}