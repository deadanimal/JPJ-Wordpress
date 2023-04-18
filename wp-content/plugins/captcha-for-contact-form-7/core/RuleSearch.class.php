<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Handle Filters that will be used to validate input fields.
     */
    class RuleSearch extends Rule
    {
        /**
         * @var array
         */
        private $words = [];
        /**
         * @var int|mixed
         */
        private $limit = 0;

        /**
         * Greedy or Non Greedy search
         */
        private $greedy = 1;

        /**
         * @param array<string> $words
         * @param int           $limit
         */
        public function __construct($words, $limit = 0, $error_message = '', $greedy = 1)
        {
            $this->error_message = $error_message;
            $this->words = $words;
            $this->limit = $limit;
            $this->greedy = $greedy;
        }

        public function isSpam($value)
        {
            $error_message = $this->getErrorMessage();

            if($this->greedy == 1){
                foreach ($this->words as $word) {
                    $regex = "!([^a-zA-Z0-9]+|^)".preg_quote($word)."([^a-zA-Z0-9]+|$)!";
                    if(preg_match($regex, $value)){
                        $this->addMessage(sprintf($error_message, $word));
                        return true;
                    }
                }
            }else{
                foreach ($this->words as $word) {
                    $regex = "!(\ |^)".preg_quote($word)."(\ |$)!";
                    if(preg_match($regex, $value)){
                        $this->addMessage(sprintf($error_message, $word));
                        return true;
                    }
                }
            }
            return false;
        }
    }
}