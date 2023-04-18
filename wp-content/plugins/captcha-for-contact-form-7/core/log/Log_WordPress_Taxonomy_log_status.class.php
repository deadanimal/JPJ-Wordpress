<?php

namespace forge12\contactform7\CF7Captcha\core\log {

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Responsible to create the Post Type for the logs
     */
    class Log_WordPress_Taxonomy_log_status
    {
        public static function init()
        {
            add_action('init', '\forge12\contactform7\CF7Captcha\core\log\Log_WordPress_Taxonomy_log_status::register');
        }

        public static function register()
        {
            $labels = array(
                'name' => _x('Status', 'Post type general name', 'f12-captcha'),
                'singular_name' => _x('Status', 'Post type singular name', 'f12-captcha'),
                'menu_name' => _x('Status', 'Admin Menu text', 'f12-captcha'),
            );

            register_taxonomy('log_status', array('deals'), array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
            ));

            /*
             * Create the default taxonomies if not exists
             */
            $terms = get_terms('log_status');

            $defaultTerms = ['spam' => 'Spam', 'verified' => 'Verified'];

            foreach ($terms as $term) {
                foreach ($defaultTerms as $slug => $l10n) {
                    if ($term->slug == $slug) {
                        unset($defaultTerms[$slug]);
                    }
                }
            }

            if (empty($defaultTerms)) {
                return;
            }

            /*
             * Add default data to the term
             */
            foreach ($defaultTerms as $slug => $l10n) {
                wp_insert_term($l10n, 'log_status', ['slug' => $slug]);
            }
        }
    }
}