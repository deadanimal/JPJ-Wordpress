<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CF7IPLog
     */
    class CF7IPLog
    {
        private static $_instance = null;

        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new CF7IPLog();
            }
            return self::$_instance;
        }


        protected function __construct()
        {
            add_filter('wpcf7_spam', '\forge12\contactform7\CF7Captcha\CF7IPLog::isSpam', 100, 2);
            add_action('wpcf7_mail_sent', '\forge12\contactform7\CF7Captcha\CF7IPLog::doLogIP', 100, 1);
        }

        public static function doLogIP($form){
            $ip = IPAddress::get();

            $SaltCurrent = Salt::getLast();
            $hashCurrent = $SaltCurrent->getSalted($ip);

            $SaltPrevious = Salt::get(1);
            $hashPrevious = $hashCurrent;

            if ($SaltPrevious != null) {
                $hashPrevious = $SaltPrevious->getSalted($ip);
            }

            $IPLog = new IPLog(['hash' => $hashCurrent, 'submitted' => 1]);
            $IPLog->save();

            // Remove failed Submits
            $IPLog->delete($hashCurrent, $hashPrevious, 0);
        }

        public static function isSpam($spam, $submission)
        {
            if($spam){
                return $spam;
            }

            $isSpam = CF7IPLog::getInstance()->validateSpam();

            if($isSpam){
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('CF7 Form - IP Log', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'IP Address is blocked in CF7IPLog.class.php');
                Log_WordPress::store($Log_Item);
            }

            return $isSpam;
        }

        public function validateSpam()
        {
            // Validate the IP Address
            if (!IPValidator::getInstance()->validate()) {
                return true;
            }
            return false;
        }
    }
}