<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class LoginValidaotor
     * This class will enable the spam protection for the login section of wordpress
     */
    class LoginValidator
    {
        public function __construct()
        {
            add_action('login_form', array($this, 'addToLogin'));
            add_filter('wp_authenticate_user', array($this, 'validateSpamProtection'), 10, 2);
        }

        /**
         * Hook into: login_form
         * Used to display the Captcha Input fields on the login page.
         *
         * @return void
         */
        public function addToLogin(){

            $fieldname = $this->getPostFieldName();

            switch(ControllerLogin::getCaptchaMethod()){
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

            echo $captcha;
        }

        /**
         * Get the Post Field Name from the settings.
         * @return mixed|string
         */
        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_captcha';
            if (isset($settings['wp_login_page']['protect_login_fieldname'])) {
                $fieldname = $settings['wp_login_page']['protect_login_fieldname'];
            }
            return $fieldname;
        }

        /**
         * Hook into Hook: wp_authenticate_user
         * Validate the Spam Captcha to ensure that the captcha matches.
         *
         * @param $user
         * @param $password
         * @return mixed|\WP_Error
         */
        public function validateSpamProtection($user, $password)
        {
            /*
             * Skip validation if login was submitted by woocommerce.
             */
            if(isset($_POST['woocommerce-login-nonce'])){
                return $user;
            }

            $fieldname = $this->getPostFieldName();
            $fieldname_hash = $fieldname.'_hash';

            if(!isset($_POST[$fieldname])){
                return new \WP_Error( '500', 'Captcha Hash Error. Please try again.' );
            }

            /*
             * validate captcha
             */
            switch(ControllerLogin::getCaptchaMethod()) {
                case 'math':
                    $isValid = CaptchaMathGenerator::validate(sanitize_text_field($_POST[$fieldname]), sanitize_text_field($_POST[$fieldname_hash]));
                    break;
                case 'image':
                    $isValid = CaptchaImageGenerator::validate(sanitize_text_field($_POST[$fieldname]), sanitize_text_field($_POST[$fieldname_hash]));
                    break;
                default:
                    $isValid = CaptchaHoneypotGenerator::validate(sanitize_text_field($_POST[$fieldname]));
                    break;
            }

            $isValid = apply_filters('f12_cf7_captcha_login_login_validator', $isValid);

            if(!$isValid){
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('WordPress Login - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in LoginValidator.class.php');
                Log_WordPress::store($Log_Item);

                return new \WP_Error( '500', 'Captcha Error. Please try again.' );
            }

            return $user;
        }
    }
}