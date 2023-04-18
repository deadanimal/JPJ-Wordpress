<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class TimerValidator
     * Enables the validation of forms / comments by submit time
     */
    class TimerValidator
    {
        /**
         * @var TimerValidator
         */
        private static $_instance = null;

        /**
         * Constructor
         */
        private function __construct(){
            add_action('init', array($this, '_init'));
        }

        /**
         * @private WordPress Hook
         */
        public function _init(){
            do_action('f12_cf7_captcha_timer_validator_init');
        }

        /**
         * Get the instance of the class
         * @return TimerValidator
         */
        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new TimerValidator();
            }

            return self::$_instance;
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
         * Return the Time in MS
         * @return
         */
        private function getTimeInMS(){
            return round(microtime(true) * 1000);
        }

        /**
         * Add a timer to the system
         * @param $ip
         * @param $timestamp
         * @return bool
         */
        public function addTimer()
        {
            $hash = $this->generateHash();

            $CaptchaTimer = new Captchatimer(
                [
                    'hash' => $hash,
                    'value' => $this->getTimeInMS(),
                    'createtime' => $this->getCreatetime()
                ]
            );

            if($CaptchaTimer->save()){
                return $hash;
            }
            return null;
        }

        /**
         * Return the hash object if any exists.
         * @param $hash
         * @return null|CaptchaTimer
         */
        public function getTimer($hash)
        {
            global $wpdb;

            if (!$wpdb) {
                return null;
            }

            return CaptchaTimer::getByHash($hash);
        }

        /**
         * Remove a Timer after validation
         * @param $hash
         * @return void
         */
        public function removeTimer($hash){
            global $wpdb;

            if (!$wpdb) {
                return null;
            }

            CaptchaTimer::deleteByHash($hash);
        }

        /**
         * Disable / delete tables on plugin deletion
         * @return void
         */
        /*public static function onDeactivation()
        {
            if (!defined('WP_UNINSTALL_PLUGIN')) {
                return;
            }

            CaptchaTimer::deleteTable();
        }*/

        /**
         * On Activation
         * @return void
         */
        /*public static function onActivation()
        {
           CaptchaTimer::createTable();
        }*/
    }

    TimerValidator::getInstance();

    //register_activation_hook('f12-cf7-captcha/CF7Captcha.class.php', 'forge12\contactform7\CF7Captcha\TimerValidator::onActivation');
    //register_deactivation_hook('f12-cf7-captcha/CF7Captcha.class.php', 'forge12\contactform7\CF7Captcha\TimerValidator::onDeactivation');
}