<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ElementorRuleValidator
     */
    class ElementorRuleValidator
    {
        private static $_instance = null;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new ElementorRuleValidator();
            }
            return self::$_instance;
        }


        protected function __construct()
        {
            add_filter('elementor_pro/forms/validation', array($this, 'isSpam'), 10, 2);
        }

        /**
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public static function isSpam($record, $ajax_handler)
        {
            $fields = $record->get('fields');

            if(!isset($fields)){
                return false;
            }

            foreach ($fields as $key => $data) {
                if (RulesHandler::getInstance()->isSpam($data['value'])) {
                    $error_message = RulesHandler::getInstance()->getSpamMessage('', '');

                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Rule Validation failed', 'f12-captcha'),
                        $fields,
                        'spam',
                        'Rule Validation failed in ElementorRuleValidator.class.php: ' . $error_message);
                    Log_WordPress::store($Log_Item);

                    $ajax_handler->add_error($key, esc_html__($error_message, 'f12-captcha'));
                    return true;
                }
            }
        }
    }
}