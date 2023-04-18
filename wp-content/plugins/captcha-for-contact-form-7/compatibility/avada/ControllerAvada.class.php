<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerAvada
     */
    class ControllerAvada
    {
        /**
         * Admin constructor.
         */
        public function __construct()
        {
            if (function_exists('Avada')) {
                $this->init();
            }
        }

        /**
         * @private WordPress Hook
         */
        public function init()
        {
            require_once('AvadaRuleValidator.class.php');
            $RuleValidator = AvadaRuleValidator::getInstance();

            if ($this->isCaptchaEnabled()) {
                require_once('AvadaValidator.class.php');
                $AV = new AvadaValidator();
            }

            if ($this->isTimeValidatorEnabled()) {
                require_once('TimerValidatorAvada.class.php');
                $TimerValidator = TimerValidatorAvada::getInstance();
            }

            if ($this->isMultipleSubmissionProtectionEnabled()) {
                require_once('AvadaMultipleSubmissionProtection.class.php');
                $MultipleSubmissionProtection = AvadaMultipleSubmissionProtection::getInstance();
            }

            if ($this->isIPValidatorEnabled()) {
                require_once('AvadaIPLog.class.php');
                $AIL = new AvadaIPLog();
            }


            add_action('wp_enqueue_scripts', array($this, 'addAssets'));
        }

        public function addAssets()
        {
            wp_enqueue_script('f12-cf7-captcha-avada', plugin_dir_url(__FILE__) . 'assets/f12-cf7-captcha-avada.js', array('jquery'));
            wp_localize_script('f12-cf7-captcha-avada', 'f12_cf7_captcha_avada', array(
                'ajaxurl' => admin_url('admin-ajax.php')
            ));
        }

        /**
         * @param string $type
         * @param string $info
         *
         * @return array
         */
        public static function get_results_from_message($type, $info)
        {
            return [
                'status' => $type,
                'info' => $info,
            ];
        }

        /**
         * @param $data
         *
         * @return array
         */
        public static function formDataToArray($data)
        {
            $data = wp_unslash($data); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput
            parse_str($data, $value);
            return $value;
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
            return (bool)CF7Captcha::getInstance()->getSettings('protect_avada_multiple_submissions', 'avada');
        }

        /**
         * @return bool
         */
        private function isTimeValidatorEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_avada_time_enable', 'avada');
        }

        /**
         * @return boolean
         */
        public function isCaptchaEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_avada', 'avada');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_avada_method', 'avada');
        }
    }
}