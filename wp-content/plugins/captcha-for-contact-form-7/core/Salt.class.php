<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Salt
     *
     * @package forge12\contactform7
     */
    class Salt
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
        private $salt = '';
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
                    if($key == 'salt'){
                        $value = base64_decode($value);
                    }
                    $this->{$key} = $value;
                }
            }
        }

        public static function removeOlderThan($period)
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            $timestamp = strtotime($period, time());

            $wpTableName = Captcha::getTableName();

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

        public static function getCount($validated = -1)
        {
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            $results = $wpdb->get_results('SELECT count(*) AS entries FROM ' . sanitize_text_field(Captcha::getTableName()));

            if (is_array($results) && isset($results[0])) {
                return $results[0]->entries;
            }
            return 0;
        }

        /**
         * Create the database which saves the captcha codes
         * for the validation to be wordpress conform
         * @return void
         */
        public static function createTable()
        {
            $wpTableName = self::getTableName();

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $sql = "CREATE TABLE " . $wpTableName . " (
                id int(11) NOT NULL auto_increment, 
                salt varchar(255) NOT NULL,
                createtime varchar(255) DEFAULT '', 
                PRIMARY KEY  (id)
            )";
            dbDelta($sql);

            // Add cron
            if (!wp_next_scheduled('weeklyIPClear')) {
                wp_schedule_event(time(), 'weekly', 'weeklyIPClear');
            }
        }

        public static function deleteTable()
        {
            global $wpdb;

            $wpTableName = self::getTableName();

            $wpdb->query("DROP TABLE IF EXISTS " . $wpTableName);

            # clear cron
            wp_clear_scheduled_hook('weeklyIPClear');
        }

        /**
         * Return the Table Name
         * @return string
         */
        public static function getTableName()
        {
            global $wpdb;
            $tableName = 'f12_cf7_salt';
            $wpTableName = $wpdb->prefix . $tableName;

            return $wpTableName;
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
        private function getSalt()
        {
            return $this->salt;
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
            $this->createtime = $dt->format('Y-m-d H:i:s');
        }

        /**
         * Return the first element found by the given id.
         * @param $id
         * @return Captcha|null
         */
        public static function getLast()
        {
            global $wpdb;

            if (!$wpdb) {
                return null;
            }

            $table = self::getTableName();

            $results = $wpdb->get_results("SELECT * FROM " . $table . " ORDER BY createtime DESC LIMIT 1", ARRAY_A);

            if (null != $results && isset($results[0])) {
                $results = new Salt($results[0]);
            }

            if (null == $results || self::isOlderThan($results->getCreatetime(), '+30 days')) {
                // Create a new salt if there is no salt
                $Salt = new Salt([
                    'salt' => self::generateSalt()
                ]);
                $Salt->save();

                $results = self::getLast();
            }
            return $results;
        }

        private static function isOlderThan($date, $days)
        {
            $d1 = new \DateTime($date);
            $d1->modify($days);

            $d2 = new \DateTime();

            if ($d2 > $d1) {
                return true;
            }
            return false;
        }

        /**
         * @return bool|string|void
         * @throws \Exception
         */
        private static function generateSalt()
        {
            return random_bytes(512);
        }

        /**
         * @param $value
         * @return string
         */
        public function getSalted($value){
            return hash_pbkdf2('sha512', $value, $this->salt, 10, 0, false);
        }

        /**
         * Return the first element found by the given id.
         * @param $id
         * @return Captcha|null
         */
        public static function get($offset = 1)
        {
            global $wpdb;

            if (!$wpdb) {
                return null;
            }

            $table = self::getTableName();

            $results = $wpdb->get_results("SELECT * FROM " . $table . " ORDER BY createtime DESC LIMIT 1 OFFSET " . (int)$offset, ARRAY_A);

            if (null != $results) {
                $results = new Salt($results[0]);
            }
            return $results;
        }

        private function maybeClean(){
            global $wpdb;
            if(!$wpdb){
                return null;
            }

            $table = self::getTableName();
            $dt = new \DateTime();
            $dt->sub(new \DateInterval('PT6480000S')); // 3 weeks
            $dtFormatted = $dt->format('Y-m-d H:i:s');

            return $wpdb->query($wpdb->prepare('DELETE FROM '.$table.' WHERE createtime < %s'),$dtFormatted);
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

            if ($this->id == 0) {
                $result = $wpdb->insert($table, array(
                    'salt' => base64_encode($this->salt),
                    'createtime' => $this->getCreatetime()
                ));

                // clean older than 3 weeks
                $this->maybeClean();

                return $result;
            }
        }
    }
}