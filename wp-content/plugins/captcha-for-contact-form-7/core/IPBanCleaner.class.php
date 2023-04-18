<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * This class will handle the clean up of the database
     * as defined by the user settings.
     */
    class IPBanCleaner
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

            $wpTableName = IPBan::getTableName();

            $dt = new \DateTime();
            $dtFormatted = $dt->format('Y-m-d H:i:s');

            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpTableName . ' WHERE blockedtime < %s', $dtFormatted));
        }


        public function resetTable()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }
            $wpTableName = IPBan::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName);
        }
    }

    new IPBanCleaner();
}