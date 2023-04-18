<?php

namespace forge12\contactform7\CF7Captcha\core\log {

    use forge12\contactform7\CF7Captcha\CF7Captcha;

    if (!defined('ABSPATH')) {
        exit;
    }

    class Log_WordPress
    {
        public static function is_logging_enabled(){
            return (int)CF7Captcha::getInstance()->getSettings('enable','logs');
        }

        /**
         * @param Log_Item $Log_Item
         * @return void
         */
        public static function store($Log_Item)
        {
            if(!self::is_logging_enabled()){
                return;
            }

            $post_id = wp_insert_post([
                    'post_title' => wp_strip_all_tags(date('d.m.Y : H:i:s', time()) . ' - ' . $Log_Item->get_name()),
                    'post_content' => Array_Formatter::to_string(
                        array_merge(
                            $Log_Item->get_properties(),
                            ['Log Message' => $Log_Item->get_log_message()]
                        ),
                        '<br>',
                        true
                    ),
                    'post_type' => 'f12_captcha_log'
                ]
            );

            self::add_taxonomy_status($post_id, $Log_Item->get_log_status_slug());
        }

        /**
         * @param int $post_id
         * @param Log_Item $Log_item
         * @param string $log_status_slug
         * @return void
         */
        private static function add_taxonomy_status($post_id, $log_status_slug = 'verified')
        {
            if (!is_numeric($post_id) || 0 === $post_id) {
                return;
            }

            $result = wp_set_object_terms($post_id, $log_status_slug, 'log_status');
        }
    }
}