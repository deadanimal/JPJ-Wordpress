<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    require_once('IPBanCleaner.class.php');

    /**
     * Class IPBan
     *
     * @package forge12\contactform7
     */
    class IPBan
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
         * The datetime whenever the captcha code has been created
         * @var string
         */
        private $createtime = '';
        /**
         * The datetime until the user is blocked for submitting data
         * @var string
         */
        private $blockedtime = '';

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

        public static function getCount($hash = '', $hashPrevious = ''){
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            if(!empty($hash) && !empty($hashPrevious)) {
                $dt = new \DateTime();
                $blocktime = $dt->format('Y-m-d H:i:s');

                $results = $wpdb->get_results($wpdb->prepare('SELECT count(*) AS entries FROM ' . self::getTableName() . ' WHERE (hash=%s OR hash=%s) AND blockedtime > %s', $hash, $hashPrevious, $blocktime));
            }
            else{
                $results = $wpdb->get_results('SELECT count(*) AS entries FROM '.self::getTableName());
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
                createtime varchar(255) DEFAULT '',
                blockedtime varchar(255) DEFAULT '',
                PRIMARY KEY  (id)
            )";
            dbDelta($sql);

            // Add cron
            if (!wp_next_scheduled('weeklyIPClear')) {
                wp_schedule_event(time(), 'weekly', 'weeklyIPClear');
            }
        }

        public static function deleteTable(){
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
            $tableName = 'f12_cf7_ip_ban';
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
            return $this->hash;
        }

        /**
         * @return string
         */
        public function getBlockedtime()
        {
            if (empty($this->blockedtime)) {
                $dt = new \DateTime();
                $this->blockedtime = $dt->format('Y-m-d H:i:s');
            }
            return $this->blockedtime;
        }

        /**
         * @param string $seconds
         */
        public function setBlockedtime($seconds)
        {
            $dt = new \DateTime();
            $dt->add(new \DateInterval('PT'.$seconds.'S'));
            $this->blockedtime = $dt->format('Y-m-d H:i:s');
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
         * Save the object to the database
         */
        public function save()
        {
            global $wpdb;

            if (!$wpdb) {
                return false;
            }

            $table = self::getTableName();

            if($this->id == 0){
                return $wpdb->insert($table, array(
                    'hash' => $this->getHash(),
                    'createtime' => $this->getCreatetime(),
                    'blockedtime' => $this->getBlockedTime()
                ));
            }
        }
    }
}