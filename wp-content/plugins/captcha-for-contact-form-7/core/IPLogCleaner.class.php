<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * This class will handle the clean up of the database
     * as defined by the user settings.
     */
    class IPLogCleaner
    {
        public function __construct()
        {
            add_action('weeklyIPClear', array($this, 'clean'));
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

            $wpTableName = IPLog::getTableName();

            $dt = new \DateTime();
            $dt->sub(new \DateInterval('PT1814400S')); // 3 weeks
            $dtFormatted = $dt->format('Y-m-d H:i:s');

            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpTableName . ' WHERE createtime < %s', $dtFormatted));
        }

        /**
         * @return bool|int
         */
        public function resetTable()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }
            $wpTableName = IPLog::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName);
        }

    }

    new IPLogCleaner();
}