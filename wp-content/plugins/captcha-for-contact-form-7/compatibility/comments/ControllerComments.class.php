<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ControllerComments
     */
    class ControllerComments
    {
        /**
         * Admin constructor.
         */
        public function __construct()
        {
            $this->init();
        }

        /**
         * @private WordPress Hook
         */
        public function init(){
            if ($this->isCaptchaProtectionEnabled()) {
                require_once('CommentValidator.class.php');
                $CV = new CommentValidator();
            }

            require_once('CommentRuleValidator.class.php');
            $RuleValidator = CommentRuleValidator::getInstance();

            require_once('TimerValidatorComments.class.php');
            $TimerValidator = TimerValidatorComments::getInstance();


            if($this->isIPValidatorEnabled()) {
                require_once('CommentIPLog.class.php');
                $IPValidator = new CommentIPLog();
            }
        }

        /**
         * @return bool
         */
        private function isIPValidatorEnabled(){
            return (bool)CF7Captcha::getInstance()->getSettings('protect_ip', 'ip');
        }


        /**
         * Validate if Comment Protection is enabled
         * @return boolean
         */
        public function isCaptchaProtectionEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_comments', 'comments');
        }

        /**
         * @return mixed
         */
        public static function getCaptchaMethod()
        {
            return CF7Captcha::getInstance()->getSettings('protect_comments_method', 'comments');
        }
    }
}