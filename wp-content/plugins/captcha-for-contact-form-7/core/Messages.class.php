<?php

namespace forge12\contactform7\CF7Captcha {
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    /**
     * Class Messages
     */
    class Messages
    {
        /**
         * @var Messages|null
         */
        private static $instance;

        /**
         * @var array
         */
        private $messages = [];

        /**
         * @return Messages|null
         */
        public static function getInstance()
        {
            if (null === self::$instance) {
                self::$instance = new Messages();
            }

            return self::$instance;
        }

        private function __clone()
        {

        }

        public function __wakeup()
        {

        }

        private function __construct()
        {
        }

        /**
         * getAll function.
         *
         * @access public
         * @return string
         */
        public function getAll()
        {
            return implode("\n", $this->messages);
        }

        /**
         * add function.
         *
         * @access public
         * @param mixed $message
         * @param mixed $type
         * @return void
         */
        public function add($message, $type)
        {
            if ($type === 'error') {
                $type = 'alert-danger';

            } elseif ($type === 'success') {
                $type = 'alert-success';

            } elseif ($type === 'info') {
                $type = 'alert-info';

            } elseif ($type === 'warning') {
                $type = 'alert-warning';

            } elseif ($type === 'offer') {
                $type = 'alert-offer';

            } elseif ($type === 'critical') {
                $type = 'alert-critical';
            }

            $this->messages[] = '<div class="box ' . \esc_attr($type) . '" role="alert"><div class="section">' . esc_html($message) . '</div></div>';
        }
    }
}