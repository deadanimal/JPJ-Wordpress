<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    require_once('IPLogCleaner.class.php');

    /**
     * Class IPLog
     *
     * @package forge12\contactform7
     */
    class IPLog
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
         * The datetime whenever the captcha code has been created
         * @var string
         */
        private $submitted = 0;

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

        /**
         * @param string $hash
         * @param string $hashPrevious
         * @return array<string> Timestamps|empty
         */
        public static function getTimestamps($hash, $hashPrevious, $seconds = 0){
            global $wpdb;

            if (!$wpdb) {
                return array();
            }

            $dt = new \DateTime();
            $dt->sub(new \DateInterval('PT'.$seconds.'S'));
            $createtime = $dt->format('Y-m-d H:i:s');

            $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.self::getTableName().' WHERE (hash=%s OR hash=%s) AND createtime > %s AND submitted=1 ORDER BY createtime DESC LIMIT 1', $hash, $hashPrevious, $createtime));

            if(is_array($results) && isset($results[0])){
                $value = array();
                foreach($results as $result){
                    $dt = new \DateTime($result->createtime, wp_timezone());
                    $value[] = $dt->getTimestamp();
                }
                return $value;
            }
            return array();
        }

        public static function getCount($hash = '', $hashPrevious = '', $submitted = -1, $seconds = 0){
            global $wpdb;

            if (!$wpdb) {
                return 0;
            }

            if(!empty($hash) && !empty($hashPrevious) && $submitted != -1) {
                $dt = new \DateTime();
                $dt->sub(new \DateInterval('PT' . $seconds . 'S'));
                $createtime = $dt->format('Y-m-d H:i:s');

                $results = $wpdb->get_results($wpdb->prepare('SELECT count(*) AS entries FROM ' . self::getTableName() . ' WHERE (hash=%s OR hash=%s) AND submitted=%d AND createtime > %s', $hash, $hashPrevious, $submitted, $createtime));
            }else if(!empty($hash) && !empty($hashPrevious) && $submitted == -1){
                $results = $wpdb->get_results($wpdb->prepare('SELECT count(*) AS entries FROM ' . self::getTableName() . ' WHERE hash=%s OR hash=%s', $hash, $hashPrevious));
            }else{
                $results = $wpdb->get_results('SELECT count(*) AS entries FROM ' . self::getTableName());
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
                submitted int(1) NOT NULL,
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
            $tableName = 'f12_cf7_ip';
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
        public function getCreatetime()
        {
            if (empty($this->createtime)) {
                $dt = new \DateTime();
                $dt->setTimezone(wp_timezone());
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
            $dt->setTimezone(wp_timezone());
            $this->createtime = $dt->format('Y-m-d H:i:s');
        }

        public function delete($hash, $hashPrevious, $submitted = 0){
            global $wpdb;
            return $wpdb->query($wpdb->prepare('DELETE FROM '.self::getTableName().' WHERE (hash=%s OR hash=%s) AND submitted=%d', $hash, $hashPrevious, $submitted));
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
                    'submitted' => $this->submitted,
                ));
            }
        }
    }
}