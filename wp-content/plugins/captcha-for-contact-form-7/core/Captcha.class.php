<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Captcha
     * Model
     *
     * @package forge12\contactform7
     */
    class Captcha
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
         * The code validated against
         * @var string
         */
        private $code = '';
        /**
         * Flag if the code has been validated already
         * @var int
         */
        private $validated = 0;
        /**
         * The datetime whenever the captcha code has been created
         * @var string
         */
        private $createtime = '';
        /**
         * The datetime whenever the captcha code has been updated
         * @var string
         */
        private $updatetime = '';

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

        public static function getCount($validated = -1){
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            if($validated == -1) {
                $results = $wpdb->get_results('SELECT count(*) AS entries FROM ' . sanitize_text_field(Captcha::getTableName()));
            }else if($validated == 0){
                $results = $wpdb->get_results('SELECT count(*) AS entries FROM ' . sanitize_text_field(Captcha::getTableName()).' WHERE validated=0');
            }else{
                $results = $wpdb->get_results('SELECT count(*) AS entries FROM ' . sanitize_text_field(Captcha::getTableName()).' WHERE validated=1');
            }

            if(is_array($results) && isset($results[0])){
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
                hash varchar(255) NOT NULL, 
                code varchar(255) NOT NULL, 
                validated int(1) DEFAULT 0,
                createtime varchar(255) DEFAULT '', 
                updatetime varchar(255) DEFAULT '',
                PRIMARY KEY  (id)
            )";
            dbDelta($sql);

            // Add cron
            if (!wp_next_scheduled('dailyCaptchaClear')) {
                wp_schedule_event(time(), 'daily', 'dailyCaptchaClear');
            }
        }

        public static function deleteTable(){
            global $wpdb;

            $wpTableName = self::getTableName();

            $wpdb->query("DROP TABLE IF EXISTS " . $wpTableName);

            # clear cron
            wp_clear_scheduled_hook('dailyCaptchaClear');
        }

        /**
         * Return the Table Name
         * @return string
         */
        public static function getTableName()
        {
            global $wpdb;
            $tableName = 'f12_cf7_captcha';
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
        public function getCode()
        {
            return $this->code;
        }

        /**
         * @param string $code
         */
        public function setCode($code)
        {
            $this->code = $code;
        }

        /**
         * @return int
         */
        public function getValidated()
        {
            return $this->validated;
        }

        /**
         * @param int $validated
         */
        public function setValidated($validated)
        {
            $this->validated = $validated;
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
         * @return string
         */
        public function getUpdatetime()
        {
            if (empty($this->updatetime)) {
                $dt = new \DateTime();
                $this->updatetime = $dt->format('Y-m-d H:i:s');
            }
            return $this->updatetime;
        }

        /**
         * Updates the updatetime with the current timestamp
         */
        public function setUpdatetime()
        {
            $dt = new \DateTime();
            $this->updatetime = $dt->format('Y-m-d H:i:s');
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

        /**
         * Return the first element found by the given id.
         * @param $id
         * @return Captcha|null
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
                $results = new Captcha($results[0]);
            }
            return $results;
        }

        /**
         * Return the first element found by the given hash.
         * @param $hash
         * @return null|Captcha
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
                $results = new Captcha($results[0]);
            }
            return $results;
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
                    'updatetime' => $this->getUpdatetime(),
                    'code' => $this->getCode(),
                    'validated' => $this->getValidated(),
                ), array(
                    'id' => $this->getId()
                ));
            } else {
                return $wpdb->insert($table, array(
                    'hash' => $this->getHash(),
                    'code' => $this->getCode(),
                    'updatetime' => $this->getUpdatetime(),
                    'createtime' => $this->getCreatetime(),
                    'validated' => $this->getValidated()
                ));
            }
        }
    }
}