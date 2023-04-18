<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CF7RuleValidator
     */
    class CF7RuleValidator
    {
        private static $_instance = null;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new CF7RuleValidator();
            }
            return self::$_instance;
        }


        protected function __construct()
        {
            add_filter('wpcf7_spam', '\forge12\contactform7\CF7Captcha\CF7RuleValidator::isSpam', 10, 2);
        }

        /**
         * @param bool $spam
         * @param \WPCF7_Submission $submission
         * @return mixed|void
         */
        public static function isSpam($spam, $submission)
        {
            if ($spam) {
                return $spam;
            }

            $data = $submission->get_posted_data();

            foreach ($data as $key => $value) {
                if (RulesHandler::getInstance()->isSpam($value)) {
                    $spam = true;
                    break;
                }
            }

            if ($spam) {
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('Rule Validation failed', 'f12-captcha'),
                    $data,
                    'spam',
                    'Rule Validation failed in CF7RuleValidator.class.php: '. RulesHandler::getInstance()->getSpamMessage('', ''));
                Log_WordPress::store($Log_Item);
            }

            return $spam;
        }
    }
}