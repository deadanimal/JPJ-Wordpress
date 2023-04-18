<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Handle Filters that will be used to validate input fields.
     */
    class RuleRegex extends Rule
    {
        private $regex = '';
        private $limit = 0;

        public function __construct($regex, $limit = 0, $error_message = '')
        {
            $this->error_message = $error_message;
            $this->regex = $regex;
            $this->limit = $limit;
        }

        public function isSpam($value)
        {
            $error_message = $this->getErrorMessage();

            $pattern = "!" . $this->regex . "!im";

            $count = preg_match_all($pattern, $value, $matches);

            if ($count > $this->limit) {
                $urls = array_map('esc_url',$matches[0]);
                $this->addMessage(sprintf($error_message,(int)$this->limit, implode(',',$urls)));

                // If one Rule has matched or the limit is reached
                return true;
            }
            return false;
        }
    }
}