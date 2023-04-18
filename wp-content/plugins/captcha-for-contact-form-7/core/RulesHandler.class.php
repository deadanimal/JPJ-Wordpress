<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    require_once('Rule.class.php');
    require_once('RuleRegex.class.php');
    require_once('RuleSearch.class.php');

    /**
     * Handle Filters that will be used to validate input fields.
     */
    class RulesHandler
    {
        /**
         * @var RulesHandler
         */
        private static $_instance = null;

        /**
         * @var array<Rule>
         */
        private $rules = [];

        /**
         * @var array<Rule>
         */
        private $spam = [];

        /**
         * @return RulesHandler
         */
        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new RulesHandler();
            }
            return self::$_instance;
        }

        public function __construct()
        {
            $this->addRuleURL();
            $this->addRuleBBCode();
            $this->addRuleBlacklist();

            add_filter('wpcf7_display_message', [$this, 'getSpamMessage'], 10, 2);
        }

        private function addRuleBlacklist()
        {
            $rule_enabled = CF7Captcha::getInstance()->getSettings('rule_blacklist', 'rules');

            if ($rule_enabled != 1) {
                return;
            }

            $rule_value = get_option('disallowed_keys');

            if (empty($rule_value)) {
                return;
            }

            $error_message = CF7Captcha::getInstance()->getSettings('rule_error_message_blacklist', 'rules' );
            $rule_greedy = CF7Captcha::getInstance()->getSettings('rule_blacklist_greedy', 'rules');

            if(empty($error_message)){
                $error_message = __('The word %s is blacklisted. Please remove it to continue.', 'f12-captcha');
            }

            // Convert new lines to | -> naked|sex|test|abc...
            $words = preg_split('/\r\n|[\r\n]/', $rule_value);

            $Rule = new RuleSearch($words, 0, $error_message, $rule_greedy);
            $this->addRule($Rule);
        }

        private function addRuleBBCode()
        {
            $rule_enabled = CF7Captcha::getInstance()->getSettings('rule_url', 'rules');

            if ($rule_enabled != 1) {
                return;
            }

            $error_message = CF7Captcha::getInstance()->getSettings('rule_error_message_bbcode', 'rules' );

            if(empty($error_message)){
                $error_message = __('The Limit %d for BBCode has been reached. Remove the %s to continue.', 'f12-captcha');
            }

            $Rule = new RuleRegex('\[url=(.+)\](.+)\[\/url\]', 0, $error_message);
            $this->addRule($Rule);
        }

        private function addRuleURL()
        {
            $rule_enabled = CF7Captcha::getInstance()->getSettings('rule_url', 'rules');

            if ($rule_enabled != 1) {
                return;
            }

            $rule_limit = CF7Captcha::getInstance()->getSettings('rule_url_limit', 'rules');

            if (!is_numeric($rule_limit)) {
                $rule_limit = 0;
            }

            $error_message = CF7Captcha::getInstance()->getSettings('rule_error_message_url', 'rules' );

            if(empty($error_message)){
                $error_message = __('The Limit %d for URLs has been reached. Remove the %s to continue.', 'f12-captcha');
            }

            $Rule = new RuleRegex('(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?', $rule_limit, $error_message);
            $this->addRule($Rule);
        }

        /**
         * @param Rule $Rule
         * @return void
         */
        public function addRule($Rule)
        {
            $this->rules[] = $Rule;
        }

        /**
         * @return Rule[]
         */
        public function getSpam(){
            return $this->spam;
        }

        /**
         * @param $message
         * @param $status
         *
         * @return string
         */
        public function getSpamMessage($message, $status){
            $spam = $this->getSpam();

            if(empty($spam)){
                return $message;
            }

            $response = '';

            foreach($spam as $Rule){
                $response .= $Rule->getMessages();
            }

            return $response;
        }

        /**
         * Check for spam
         * @param $value
         * @return bool
         */
        public function isSpam($value)
        {
            foreach ($this->rules as $key => $Rule/** @var Rule $Rule */) {
                if(is_array($value)){
                    foreach($value as $skey => $svalue){
                        if ($Rule->isSpam($svalue)) {
                            $this->spam[] = $Rule;
                            return true;
                        }
                    }
                }else{
                    if ($Rule->isSpam($value)) {
                        $this->spam[] = $Rule;
                        return true;
                    }
                }
            }
            return false;
        }
    }
}