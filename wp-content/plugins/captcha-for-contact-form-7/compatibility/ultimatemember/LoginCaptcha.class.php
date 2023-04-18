<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class LoginCaptcha
     *
     * @package forge12\contactform7\CF7Captcha
     */
    class LoginCaptcha
    {
        private $isValid = true;

        /**
         * Admin constructor.
         */
        public function __construct()
        {
            add_action('um_after_login_fields', [$this, 'addCaptcha']);
            add_action('um_submit_form_errors_hook_login', [$this, 'validateCaptcha'], 5 ,1);
        }

        /**
         * Validate the Captcha
         *
         * @param $fields
         * @param $field
         * @param $args
         *
         * @return void|\WP_Error
         */
        public function validateCaptcha($args)
        {
            // Add validation only for login
            if(!isset($args['mode']) || $args['mode'] != 'login'){
                return;
            }

            $fieldname = $this->getPostFieldName();
            $fieldname_hash = $fieldname.'_hash';

            if(!isset($_POST[$fieldname])){
                UM()->form()->add_error( $fieldname, __( 'This field is required', 'ultimate-member' ) );
                $this->isValid = false;

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('LoginCaptcha - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Hash failed in LoginCaptcha.class.php');
                Log_WordPress::store($Log_Item);
                return;
            }

            /*
             * validate captcha
             */
            switch(ControllerUltimateMember::getCaptchaMethodForLogin()) {
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
                UM()->form()->add_error( $fieldname, __( 'This field is required', 'ultimate-member' ) );
                $this->isValid = false;

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('LoginCaptcha - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in LoginCaptcha.class.php');
                Log_WordPress::store($Log_Item);
            }else{
                // Ensure the wp login validator is skipped if enabled.
                add_filter('f12_cf7_captcha_login_login_validator', '__return_true');
            }
        }

        /**
         * Get the Post Field Name from the settings.
         * @return mixed|string
         */
        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_captcha';
            if (isset($settings['ultimatemember']['protect_login_fieldname'])) {
                $fieldname = $settings['ultimatemember']['protect_login_fieldname'];
            }
            return $fieldname;
        }

        /**
         * Add Captcha Tag
         */
        public function addCaptcha()
        {
            $fieldname = $this->getPostFieldName();

            switch(ControllerUltimateMember::getCaptchaMethodForLogin()){
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


            /*
             * Check if Captcha is not correct
             */
            if(!$this->isValid){
                echo '<div class="um-field-error">'.__('Captcha not valid', 'f12-captcha').'</div>';
            }

        }
    }
}