<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class IPValidator
     */
    class IPValidator
    {
        /**
         * @var IPValidator
         */
        private static $_instance = null;

        /**
         * Constructor
         */
        private function __construct()
        {

        }

        /**
         * Get the instance of the class
         * @return IPValidator
         */
        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new IPValidator();
            }

            return self::$_instance;
        }

        /**
         * @return bool
         */
        public function validate()
        {
            $ip = IPAddress::get();

            $SaltCurrent = Salt::getLast();
            $hashCurrent = $SaltCurrent->getSalted($ip);

            $SaltPrevious = Salt::get(1);
            $hashPrevious = $hashCurrent;

            if ($SaltPrevious != null) {
                $hashPrevious = $SaltPrevious->getSalted($ip);
            }

            // Check if the IP has been blocked
            if (IPBan::getCount($hashCurrent, $hashPrevious) > 0) {
                return false;
            }

            // Check for entries
            $timestamps = IPLog::getTimestamps($hashCurrent, $hashPrevious, CF7Captcha::getInstance()->getSettings('max_retry_period', 'ip'));
            $timestamps[] = time();

            //$timestamps[] = $dt->getTimestamp();

            if (count($timestamps) > 0) {
                /**
                 * Meassure the period of time between those timestamps
                 */
                $allowedTimeBetween = CF7Captcha::getInstance()->getSettings('period_between_submits', 'ip');

                $previousIndex = null;
                foreach ($timestamps as $key => $value) {
                    if ($previousIndex === null) {
                        $previousIndex = $key;
                        continue;
                    }

                    if ($value - $timestamps[$previousIndex] < $allowedTimeBetween) {
                        $IPLog = new IPLog(['hash' => $hashCurrent, 'submitted' => 0]);
                        $IPLog->save();

                        // Check if there are 3+ entries for the given IP, if yes - block it
                        if (IPLog::getCount($hashCurrent, $hashPrevious, 0, CF7Captcha::getInstance()->getSettings('max_retry_period', 'ip')) >= CF7Captcha::getInstance()->getSettings('max_retry', 'ip')) {
                            $IPBan = new IPBan([
                                'hash' => $hashCurrent
                            ]);
                            $IPBan->setBlockedtime(CF7Captcha::getInstance()->getSettings('blockedtime', 'ip'));
                            $IPBan->save();
                        }

                        return false;
                    }

                    $previousIndex = $key;
                }
            }

            return true;
        }
    }

    IPValidator::getInstance();
}