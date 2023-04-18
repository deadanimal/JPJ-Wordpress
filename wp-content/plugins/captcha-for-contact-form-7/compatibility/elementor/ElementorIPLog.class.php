<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ElementorIPLog
     */
    class ElementorIPLog
    {
        public function __construct()
        {
            add_action('elementor_pro/forms/validation', array($this, 'validateSpamProtection'), 10, 2);
        }

        /**
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public function validateSpamProtection($record, $ajax_handler)
        {
            $fields = $record->get('fields');

            if(!isset($fields)){
                return false;
            }

            // Validate the IP Address
            if(!IPValidator::getInstance()->validate()){
                /*
                 * Get the first field that is not hidden to show the error message to the visitor.
                 */
                $field_name = '';
                foreach ($fields as $key => $data) {
                    if ($data['type'] != 'hidden') {
                        $field_name = $key;
                    }
                }

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('Elementor Form - IP Log', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'IP Address is blocked in ElementorIPLog.class.php');
                Log_WordPress::store($Log_Item);

                $ajax_handler->add_error($field_name, esc_html__('Your IP has been blocked, please try again later.', 'f12-captcha'));
            }
        }
    }
}