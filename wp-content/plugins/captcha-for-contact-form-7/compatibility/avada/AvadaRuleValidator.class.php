<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class AvadaRuleValidator
     */
    class AvadaRuleValidator
    {
        private static $_instance = null;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new AvadaRuleValidator();
            }
            return self::$_instance;
        }


        protected function __construct()
        {
            add_filter('fusion_form_demo_mode', array($this, 'isSpam'), 10, 1);
            add_filter('init', array($this, 'isSpamContactTemplate'));
        }

        public function isSpamContactTemplate(){
            $form_fields = [
                'contact_name',
                'email',
                'url',
                'msg'
            ];

            if(!isset($_POST['submit'])){
                return;
            }

            foreach($form_fields as $field){
                if(!isset($_POST[$field])){
                    return;
                }
            }

            foreach($form_fields as $field){
                if(RulesHandler::getInstance()->isSpam($_POST[$field])){
                    $error_message = RulesHandler::getInstance()->getSpamMessage('', '');
                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Rule Validation failed', 'f12-captcha'),
                        $_POST,
                        'spam',
                        'Rule Validation failed in AvadaRuleValidator.class.php: ' . $error_message);
                    Log_WordPress::store($Log_Item);

                    wp_die('Sorry, this mail has been blocked by spam protection: '.$error_message);
                }
            }
        }

        /**
         * @param bool $spam
         * @param \WPCF7_Submission $submission
         * @return mixed|void
         */
        public static function isSpam($value)
        {
            /**
             * Avada sends the form fields as formdata string.
             */
            if (isset($_POST['formData'])) {
                $data = ControllerAvada::formDataToArray($_POST['formData']);

                foreach ($data as $key => $value) {
                    if (RulesHandler::getInstance()->isSpam($value)) {
                        $error_message = RulesHandler::getInstance()->getSpamMessage('', '');

                        /*
                         * Add Log Entries
                         */
                        $Log_Item = new Log_Item(
                            __('Rule Validation failed', 'f12-captcha'),
                            $data,
                            'spam',
                            'Rule Validation failed in AvadaRuleValidator.class.php: ' . $error_message);
                        Log_WordPress::store($Log_Item);

                        die(wp_json_encode(ControllerAvada::get_results_from_message('error', $error_message)));
                        return true;
                    }
                }
            }
        }
    }
}