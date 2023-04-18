<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class AvadaValidator
     */
    class AvadaIPLog
    {
        public function __construct()
        {
            add_filter('fusion_form_demo_mode', array($this, 'validateSpamProtection'), 10, 1);
        }

        public function validateSpamProtection($value)
        {
            // Validate the IP Address
            if(!IPValidator::getInstance()->validate()){
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('Avada Form - IP Log', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'IP Address is blocked in AvadaIPLog.class.php');
                Log_WordPress::store($Log_Item);

                die(wp_json_encode(ControllerAvada::get_results_from_message('error', 'spam')));
            }
            return false;
        }
    }
}