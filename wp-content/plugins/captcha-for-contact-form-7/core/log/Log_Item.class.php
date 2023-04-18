<?php
namespace forge12\contactform7\CF7Captcha\core\log {
    if (!defined('ABSPATH')) {
        exit;
    }

    class Log_Item
    {
        private $name;
        private $properties;
        private $log_status_slug;
        private $log_message;


        /**
         * @param $email - The E-Mail of the receiver / sender
         * @param $properties - Array containing all data of the submitted form
         * @param $log_status - The status of the log, could either be "spam" or "verified"
         * @param string $log_message
         */
        public function __construct($name, $properties, $log_status_slug = 'verified', $log_message = '')
        {
            $this->name = $name;
            $this->properties = $properties;
            $this->log_status_slug = $log_status_slug;
            $this->log_message = $log_message;
        }

        public function get_name()
        {
            return $this->name;
        }

        public function get_properties()
        {
            if(!is_array($this->properties)){
                return [];
            }
            return $this->properties;
        }

        /**
         * The Term item storing the Status (log_status Taxonomie).
         * @return object
         */
        public function get_log_status_slug()
        {
            return $this->log_status_slug;
        }

        public function get_log_message()
        {
            return $this->log_message;
        }
    }
}