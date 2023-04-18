<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CF7HoneypotCaptcha
     */
    class CF7HoneypotCaptcha
    {
        private static $_instance = null;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new CF7HoneypotCaptcha();
            }
            return self::$_instance;
        }

        protected function __construct()
        {
            add_filter('wpcf7_form_elements', array($this, 'addCaptcha'), 100, 1);
            add_filter('wpcf7_spam', array($this, 'isSpam'), 100, 2);
        }

        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();
            $fieldname = 'f12_honey';
            if (isset($settings['cf7']['protect_cf7_fieldname'])) {
                $fieldname = $settings['cf7']['protect_cf7_fieldname'];
            }
            return $fieldname;
        }

        public function addCaptcha($content)
        {
            $fieldname = self::getInstance()->getPostFieldName();

            switch(ControllerCF7::getCaptchaMethod()){
                case 'math':
                    $captcha = CaptchaMathGenerator::get_form_field($fieldname);
                    break;
                case 'image':
                    $captcha = CaptchaImageGenerator::get_form_field($fieldname);
                    break;
                default:
                    $captcha = CaptchaHoneypotGenerator::get_form_field($fieldname);
                    break;
            }

            $captcha = '<p><span class="wpcf7-form-control-wrap">'.$captcha.'</span></p>';

            if(preg_match('!<input(.*)type="submit"!', $content,  $matches)){
                return str_replace($matches[0], $captcha.$matches[0],$content);
            }
            return $content;
        }

        public static function isSpam($spam, $submission)
        {
            if ($spam) {
                return $spam;
            }

            $fieldname = self::getInstance()->getPostFieldName();

            if (!isset($_POST[$fieldname])) {
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('CF7 Form - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Honeypot failed in CF7HoneypotCaptcha.class.php');
                Log_WordPress::store($Log_Item);

                return true;
            }

            switch(ControllerCF7::getCaptchaMethod()){
                case 'math':
                    $isValid = CaptchaMathGenerator::validate($_POST[$fieldname], $_POST[$fieldname.'_hash']);
                    break;
                case 'image':
                    $isValid = CaptchaImageGenerator::validate($_POST[$fieldname], $_POST[$fieldname.'_hash']);
                    break;
                default:
                    $isValid = CaptchaHoneypotGenerator::validate($_POST[$fieldname]);
                    break;
            }

            if(!$isValid){
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('CF7 Form - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in CF7HoneypotCaptcha.class.php');
                Log_WordPress::store($Log_Item);

                return true;
            }

            return $spam;
        }
    }
}