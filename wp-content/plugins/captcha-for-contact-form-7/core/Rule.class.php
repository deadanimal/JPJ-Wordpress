<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Handle Filters that will be used to validate input fields.
     */
    abstract class Rule
    {
        /**
         * @var string
         */
        protected $error_message = '';

        /**
         * @var array<string>
         */
        private $messages = [];

        /**
         * @param $value
         *
         * @return bool
         */
        public abstract function isSpam($value);

        /**
         * @param string $message
         *
         * @return void
         */
        public function addMessage($message)
        {
            $this->messages[] = $message;
        }

        /**
         * @return string
         */
        public function getMessages()
        {
            return implode("<br/>", $this->messages);
        }

        public function getErrorMessage(){
            return $this->error_message;
        }
    }
}