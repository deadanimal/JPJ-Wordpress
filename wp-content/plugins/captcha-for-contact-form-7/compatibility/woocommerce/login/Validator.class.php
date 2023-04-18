<?php

namespace forge12\contactform7\CF7Captcha\woocommerce\login {

    use forge12\contactform7\CF7Captcha\Captcha;
    use forge12\contactform7\CF7Captcha\CaptchaHoneypotGenerator;
    use forge12\contactform7\CF7Captcha\CaptchaImageGenerator;
    use forge12\contactform7\CF7Captcha\CaptchaMathGenerator;
    use forge12\contactform7\CF7Captcha\CF7Captcha;
    use forge12\contactform7\CF7Captcha\ControllerWoocommerce;
    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class LoginValidaotor
     * This class will enable the spam protection for the login section of wordpress
     */
    class Validator
    {
        public function __construct()
        {
            add_action('woocommerce_login_form', array($this, 'addToLogin'));
            add_filter('woocommerce_process_login_errors', array($this, 'validateSpamProtection'), 10, 3);
        }

        /**
         * Hook into: woocommerce_login_form
         * Used to display the Captcha Input fields on the woocommerce login page.
         *
         * @return void
         */
        public function addToLogin(){
            $fieldname = $this->getPostFieldName();

            switch(ControllerWoocommerce::getLoginCaptchaMethod()){
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
            if (isset($settings['woocommerce']['protect_login_fieldname'])) {
                $fieldname = $settings['woocommerce']['protect_login_fieldname'];
            }
            return $fieldname;
        }

        /**
         * @param \WP_Error $errors
         * @param string $user_login
         * @param string $user_password
         * @return \WP_Error
         */
        public function validateSpamProtection($errors, $user_login, $user_password)
        {
            $fieldname = $this->getPostFieldName();
            $fieldname_hash = $fieldname.'_hash';

            if(empty($fieldname) || !isset($_POST[$fieldname])){
                $errors->add('spam',  __( 'Please enter the captcha.' ) );
                return $errors;
            }

            /*
             * validate captcha
             */
            switch(ControllerWoocommerce::getLoginCaptchaMethod()){
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
                    __('WooCommerce Login - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in Validator.class.php');
                Log_WordPress::store($Log_Item);

                $errors->add('spam',  __( 'Captcha not correct.' ) );
                return $errors;
            }

            return $errors;
        }
    }
}