<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CaptchaHoneypotGenerator
     * Generate the custom captcha as an honeypot
     *
     * @package forge12\contactform7
     */
    class CaptchaHoneypotGenerator extends CaptchaGenerator
    {
        /**
         * constructor.
         */
        public function __construct()
        {
            parent::__construct(0);

            $this->init();
        }

        /**
         * Init the captcha
         */
        private function init()
        {
            $this->_captcha = '';
        }

        /**
         * Get the Value of the captcha
         *
         * @return string|void
         */
        public function get()
        {
            return $this->_captcha;
        }

        public static function validate($captcha_code)
        {
            if (!empty($captcha_code)) {
                return false;
            }
            return true;
        }


        public static function get_form_field($fieldname)
        {
            $captcha = '<input id="' . esc_attr($fieldname) . '" type="hidden" style="visibility:hidden; opacity:1; height:0; width:0; margin:0; padding:0;" name="' . esc_attr($fieldname) . '" value=""/>';
            return apply_filters('f12-cf7-captcha-get-form-field-honeypot', $captcha, $fieldname);

        }
    }
}