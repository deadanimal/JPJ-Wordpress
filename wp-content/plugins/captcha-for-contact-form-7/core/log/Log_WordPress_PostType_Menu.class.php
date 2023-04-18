<?php

namespace forge12\contactform7\CF7Captcha\core\log {

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Adjusting the menu to show as submenu of the captcha plugin.
     */
    class Log_WordPress_PostType_Menu
    {
        public static function init()
        {
            add_action('admin_menu', '\forge12\contactform7\CF7Captcha\core\log\Log_WordPress_PostType_Menu::set_menu');
            add_filter('parent_file', '\forge12\contactform7\CF7Captcha\core\log\Log_WordPress_PostType_Menu::set_parent');
        }

        public static function set_menu()
        {
            add_submenu_page('f12-cf7-captcha', 'Log Entries', 'Log Entries', 'edit_pages' , 'edit.php?post_type=f12_captcha_log');
        }

        public static function set_parent($parent_file){
            global $submenu_file, $current_screen;

            // Set correct active/current menu and submenu in the WordPress Admin menu for the "example_cpt" Add-New/Edit/List
            if($current_screen->post_type == 'f12_captcha_log') {
                $submenu_file = 'edit.php?post_type=f12_captcha_log';
                $parent_file = 'f12-cf7-captcha';
            }
            return $parent_file;
        }
    }
}