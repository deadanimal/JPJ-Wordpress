<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * This class will handle the clean up of the database
     * as defined by the user settings.
     */
    class CaptchaCleaner
    {
        public function __construct()
        {
            add_action('dailyCaptchaClear', array($this, 'clean'));
        }

        /**
         * Clear all confimred database entries if the period selected
         * is reached.
         */
        public function clean()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            $timestamp = strtotime("-1 days", time());

            $wpTableName = Captcha::getTableName();

            $dt = new \DateTime();
            $dt->setTimestamp($timestamp);
            $dtFormatted = $dt->format('Y-m-d H:i:s');

            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpTableName . ' WHERE createtime < %s', $dtFormatted));
        }

        /**
         * Clean all Captchas
         * @return bool|int
         */
        public function resetTable()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }
            $wpTableName = Captcha::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName);
        }

        /**
         * Clean all Captchas
         * @return bool|int
         */
        public function cleanValidated()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            $wpTableName = Captcha::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName . ' WHERE validated = 1');
        }

        /**
         * Clean all Captchas
         * @return bool|int
         */
        public function cleanNonValidated()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            $wpTableName = Captcha::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName . ' WHERE validated = 0');
        }
    }

    new CaptchaCleaner();
}