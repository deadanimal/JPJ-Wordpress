<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIDatabase
     */
    class UIDatabase extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'db', 'Database', 99);
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {
            return $settings;
        }



        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
            ?>
            <h2>
                <?php _e('Database', 'f12-cf7-captcha'); ?>
            </h2>

            <?php
        }

        protected function theSidebar($slug, $page)
        {
            return;
        }

        public function getSettings($settings)
        {
            return $settings;
        }
    }
}