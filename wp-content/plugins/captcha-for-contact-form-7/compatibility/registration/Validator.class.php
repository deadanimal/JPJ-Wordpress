<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Validator
     * This class will enable the protection for the registration form
     */
    class Validator
    {
        public function __construct()
        {
            add_action('register_form', array($this, 'addProtection'));
            add_filter('register_post', array($this, 'validateProtection'), 10, 3);
        }

        /**
         * Hook into: login_form
         * Used to display the Captcha Input fields on the login page.
         *
         * @return void
         */
        public function addProtection(){
            $fieldname = $this->getPostFieldName();

            switch(ControllerRegistration::getCaptchaMethod()){
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
            if (isset($settings['wp_login_page']['protect_registration_fieldname'])) {
                $fieldname = $settings['wp_login_page']['protect_registration_fieldname'];
            }
            return $fieldname;
        }

        /**
         * Hook into Hook: register_post
         * Validate the Captcha to ensure that the captcha matches.
         *
         * @param string
         * @param string
         * @param \WP_Error $errors
         *
         * @return null
         */
        public function validateProtection($user, $email, $errors)
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
                $errors->add('spam',  __( '<strong>Error:</strong> Please enter the captcha.' ) );
                return;
            }

            /*
             * validate captcha
             */
            switch(ControllerRegistration::getCaptchaMethod()) {
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

            if(!$isValid){
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('WordPress Registration - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in Validator.class.php');
                Log_WordPress::store($Log_Item);

                $errors->add('spam',  __( '<strong>Error:</strong> Please enter the captcha.' ) );
            }
        }
    }
}