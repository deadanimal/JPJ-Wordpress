<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * This class will handle the clean up of the database
     * as defined by the user settings.
     */
    class CaptchaTimerCleaner
    {
        public function __construct()
        {
            add_action('dailyCaptchaTimerClear', array($this, 'clean'));
        }

        /**
         * Clear all confimred database entries if the period selected
         * is reached.
         */
        public function clean()
        {
            global $wpdb;

            if(!$wpdb){
                return 0;
            }

            $timestamp = strtotime('-1 days', time());

            $wpTableName = CaptchaTimer::getTableName();

            $dt = new \DateTime();
            $dt->setTimestamp($timestamp);
            $dtFormatted = $dt->format('Y-m-d H:i:s');

            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpTableName . ' WHERE createtime < %s', $dtFormatted));
        }

        /**
         * Clean all Captchas
         * @return bool|int
         */
        public function resetTable(){
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }
            $wpTableName = CaptchaTimer::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName);
        }

    }

    new CaptchaTimerCleaner();
}