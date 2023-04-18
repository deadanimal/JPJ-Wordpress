<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CommentRuleValidator
     */
    class CommentRuleValidator
    {
        private static $_instance = null;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new CommentRuleValidator();
            }
            return self::$_instance;
        }


        protected function __construct()
        {
            add_filter('preprocess_comment', array($this, 'isSpam'));
        }

        /**
         * @param bool $spam
         * @param \WPCF7_Submission $submission
         * @return mixed|void
         */
        public static function isSpam($data)
        {
            $error_message = '';
            foreach ($data as $key => $value) {
                if (RulesHandler::getInstance()->isSpam($value)) {
                    $error_message = RulesHandler::getInstance()->getSpamMessage($error_message, '');
                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Rule Validation failed', 'f12-captcha'),
                        $data,
                        'spam',
                        'Rule Validation failed in CommentRuleValidator.class.php: ' . $error_message);
                    Log_WordPress::store($Log_Item);

                    wp_die(__('Error: Spam detected. ' . $error_message, 'f12-cf7-captcha'));
                }
            }
            return $data;
        }
    }
}