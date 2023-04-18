<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class IPAddress
     *
     * @package forge12\contactform7
     */
    class IPAddress
    {
        public static function get(){
            //whether ip is from share internet
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_address = addslashes($_SERVER['HTTP_CLIENT_IP']);
            } //whether ip is from proxy
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = addslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
            } //whether ip is from remote address
            else {
                $ip_address = addslashes($_SERVER['REMOTE_ADDR']);
            }

            return $ip_address;
        }
    }
}