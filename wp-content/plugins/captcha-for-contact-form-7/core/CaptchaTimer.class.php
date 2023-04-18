<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CaptchaMathGenerator
     * Generate the custom captcha as an image
     *
     * @package forge12\contactform7
     */
    class CaptchaTimer
    {
        /**
         * The unique ID
         * @var int
         */
        private $id = 0;
        /**
         * The identifier used in the contact form
         * @var string
         */
        private $hash = '';
        /**
         * The value - stores the time in milliseconds
         * @var int
         */
        private $value = '';
        /**
         * The datetime whenever the captcha code has been created
         * @var string
         */
        private $createtime = '';

        /**
         * Create a new Captcha Object
         * @param $object
         */
        public function __construct($params = array())
        {
            foreach ($params as $key => $value) {
                if (isset($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }

        public static function getTableName()
        {
            global $wpdb;
            $tableName = 'f12_cf7_captcha_timer';
            $wpTableName = $wpdb->prefix . $tableName;

            return $wpTableName;
        }

        /**
         * Create the database which saves the captcha codes
         * for the validation to be wordpress conform
         * @return void
         */
        public static function createTable()
        {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $wpTableName = self::getTableName();

            $sql = "CREATE TABLE " . $wpTableName . " (
                id int(11) NOT NULL auto_increment, 
                hash varchar(255) NOT NULL, 
                value varchar(255) NOT NULL,
                createtime varchar(255) DEFAULT '',
                PRIMARY KEY  (id)
            )";
            dbDelta($sql);

            // Add cron
            if (!wp_next_scheduled('dailyCaptchaTimerClear')) {
                wp_schedule_event(time(), 'daily', 'dailyCaptchaTimerClear');
            }
        }

        public static function deleteTable()
        {
            global $wpdb;

            $wpTableName = self::getTableName();

            $wpdb->query("DROP TABLE IF EXISTS " . $wpTableName);

            # clear cron
            wp_clear_scheduled_hook('dailyCaptchaTimerClear');
        }

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param int $id
         */
        private function setId($id)
        {
            $this->id = $id;
        }

        /**
         * @return string
         */
        public function getHash()
        {
            if (empty($this->hash)) {
                $this->hash = $this->generateHash();
            }
            return $this->hash;
        }

        /**
         * Generate the hash code
         */
        private function generateHash()
        {
            $ip = IPAddress::get();
            if (empty($ip)) {
                return '';
            }
            return \password_hash(time() . $ip, PASSWORD_DEFAULT);
        }

        /**
         * Check if the hash is valid. Only if the ip adress could be determined.
         * If do not store this item in the db.
         */
        private function isValidHash()
        {
            return !empty($this->hash);
        }

        /**
         * @return string
         */
        public function getValue()
        {
            return $this->value;
        }

        /**
         * @param string $value
         */
        public function setValue($value)
        {
            $this->value = $value;
        }

        /**
         * @return string
         */
        public function getCreatetime()
        {
            if (empty($this->createtime)) {
                $dt = new \DateTime();
                $this->createtime = $dt->format('Y-m-d H:i:s');
            }
            return $this->createtime;
        }

        /**
         * Update the createtime with the current timestamp
         * @param string $createtime
         */
        public function setCreatetime()
        {
            $dt = new \DateTime();
            $this->updatetime = $dt->format('Y-m-d H:i:s');
        }

        /**
         * Return the first element found by the given id.
         * @param $id
         * @return CaptchaTimer|null
         */
        public static function getById($id)
        {
            global $wpdb;

            if (!$wpdb) {
                return null;
            }

            $table = self::getTableName();

            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table . " WHERE id=%d", $id), ARRAY_A);

            if (null != $results) {
                $results = new CaptchaTimer($results[0]);
            }
            return $results;
        }

        /**
         * Return the first element found by the given hash.
         * @param $hash
         * @return null|CaptchaTimer
         */
        public static function getByHash($hash)
        {
            global $wpdb;

            if (!$wpdb) {
                return null;
            }

            $table = self::getTableName();

            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table . " WHERE hash=%s", $hash), ARRAY_A);

            if (null != $results) {
                $results = new CaptchaTimer($results[0]);
            }
            return $results;
        }

        public function delete(){
            return self::deleteByHash($this->hash);
        }

        public static function deleteByHash($hash)
        {
            global $wpdb;

            $wpTableName = self::getTableName();

            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpTableName . ' WHERE hash=%s', $hash));
        }

        /**
         * Check if this is an update or a new object
         * @return bool
         */
        private function isUpdate()
        {
            if ($this->isValidHash() && $this->id != 0) {
                return true;
            }
            return false;
        }

        public static function removeOlderThan($period){
            global $wpdb;

            if(!$wpdb){
                return 0;
            }

            $timestamp = strtotime($period, time());

            $wpTableName = self::getTableName();

            $dt = new \DateTime();
            $dt->setTimestamp($timestamp);
            $dtFormatted = $dt->format('Y-m-d H:i:s');

            return $wpdb->query($wpdb->prepare('DELETE FROM ' . $wpTableName . ' WHERE createtime < %s', $dtFormatted));
        }

        public static function resetTable()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }
            $wpTableName = self::getTableName();

            return $wpdb->query('DELETE FROM ' . $wpTableName);
        }

        public static function getCount()
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            $results = $wpdb->get_results('SELECT count(*) AS entries FROM ' . sanitize_text_field(CaptchaTimer::getTableName()));

            if (is_array($results) && isset($results[0])) {
                return $results[0]->entries;
            }
            return 0;
        }

        /**
         * Save the object to the database
         */
        public function save()
        {
            global $wpdb;

            if (!$wpdb) {
                return false;
            }

            $table = self::getTableName();

            if ($this->isUpdate()) {
                return $wpdb->update($table, array(
                    'hash' => $this->getHash(),
                    'createtime' => $this->getCreatetime(),
                    'value' => $this->getValue(),
                ), array(
                    'id' => $this->getId()
                ));
            } else {
                return $wpdb->insert($table, array(
                    'hash' => $this->getHash(),
                    'value' => $this->getValue(),
                    'createtime' => $this->getCreatetime()
                ));
            }
        }
    }
}