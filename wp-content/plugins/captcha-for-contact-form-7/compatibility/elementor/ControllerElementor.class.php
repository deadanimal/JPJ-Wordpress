<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerElementor
     */
    class ControllerElementor
    {
        /**
         * Admin constructor.
         */
        public function __construct()
        {
            if (defined('ELEMENTOR_VERSION')) {
                $this->init();
            }
        }

        /**
         * @private WordPress Hook
         */
        public function init()
        {
            require_once('ElementorRuleValidator.class.php');
            $RuleValidator = ElementorRuleValidator::getInstance();

            if ($this->isCaptchaEnabled()) {

                if (ControllerElementor::getCaptchaMethod() == 'honey') {
                    require_once('ElementorValidator.class.php');
                    $AV = new ElementorValidator();
                } else {
                    require_once('ElementorCaptcha.class.php');
                    $EC = new ElementorCaptcha();
                }
            }

            if ($this->isTimeValidatorEnabled()) {
                require_once('TimerValidatorElementor.class.php');
                $TimerValidator = TimerValidatorElementor::getInstance();
            }

            if ($this->isMultipleSubmissionProtectionEnabled()) {
                require_once('ElementorMultipleSubmissionProtection.class.php');
                $MultipleSubmissionProtection = ElementorMultipleSubmissionProtection::getInstance();
            }

            if ($this->isIPValidatorEnabled()) {
                require_once('ElementorIPLog.class.php');
                $AIL = new ElementorIPLog();
            }

            add_action('wp_enqueue_scripts', array($this, 'addAssets'));
        }

        public function addAssets()
        {
            wp_enqueue_script('f12-cf7-captcha-elementor', plugin_dir_url(__FILE__) . 'assets/f12-cf7-captcha-elementor.js', array('jquery'));
            wp_localize_script('f12-cf7-captcha-elementor', 'f12_cf7_captcha_elementor', array(
                'ajaxurl' => admin_url('admin-ajax.php')
            ));
        }

        /**
         * @return bool
         */
        private function isIPValidatorEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_ip', 'ip');
        }

        /**
         * @return bool
         */
        private function isMultipleSubmissionProtectionEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_elementor_multiple_submissions', 'elementor');
        }


        /**
         * @return bool
         */
        private function isTimeValidatorEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_elementor_time_enable', 'elementor');
        }

        /**         *
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_elementor_method', 'elementor');
        }

        /**
         * Validate if Comment Protection is enabled
         *
         * @return boolean
         */
        public function isCaptchaEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_elementor', 'elementor');
        }
    }
}