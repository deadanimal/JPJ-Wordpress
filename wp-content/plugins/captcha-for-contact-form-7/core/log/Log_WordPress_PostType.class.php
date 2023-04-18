<?php

namespace forge12\contactform7\CF7Captcha\core\log {

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Responsible to create the Post Type for the logs
     */
    class Log_WordPress_PostType
    {
        public static function init()
        {
            add_action('init', '\forge12\contactform7\CF7Captcha\core\log\Log_WordPress_PostType::register');
        }

        public static function register()
        {
            $labels = array(
                'name' => _x('Captcha Log', 'Post type general name', 'f12-captcha'),
                'singular_name' => _x('Captcha Log', 'Post type singular name', 'f12-captcha'),
                'menu_name' => _x('Captcha Log', 'Admin Menu text', 'f12-captcha'),
                'name_admin_bar' => _x('Captcha Log', 'Add New on Toolbar', 'f12-captcha'),
                'edit_item' => __('Edit', 'f12-captcha'),
                'view_item' => __('View', 'f12-captcha'),
            );

            $args = array(
                'labels' => $labels,
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'query_var' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor'),
                'taxonomies' => array('log_status')
            );

            register_post_type('f12_captcha_log', $args);
        }
    }
}