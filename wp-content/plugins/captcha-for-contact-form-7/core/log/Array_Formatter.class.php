<?php

namespace forge12\contactform7\CF7Captcha\core\log {

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Responsible to create the Post Type for the logs
     */
    class Array_Formatter
    {
        public static function to_string($data, $delimiter = '', $use_key_as_label = false)
        {
            $response = '';
            foreach ($data as $key => $value) {
                if (true === $use_key_as_label) {
                    $response .= $key . ': ';
                }
                if(is_array($value)){
                    $value = self::to_string($value, $delimiter, $use_key_as_label);
                }

                $response .= $value . $delimiter;
            }
            return $response;
        }
    }
}