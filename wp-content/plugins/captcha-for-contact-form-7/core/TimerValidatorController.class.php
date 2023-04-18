<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    abstract class TimerValidatorController
    {
        /**
         * @var TimerValidatorController
         */
        private static $_instances = array();

        /**
         * @return TimerValidatorController
         */
        public static function getInstance(){
            $called_class = get_called_class();

            if(!isset(self::$_instances[$called_class])){
                self::$_instances[$called_class] = new $called_class();
            }
            return self::$_instances[$called_class];
        }

        protected function __construct()
        {
            add_action('f12_cf7_captcha_timer_validator_init', array($this,'init'));
        }

        /**
         * @private WordPress hook
         */
        protected abstract function onInit();

        /**
         * @return bool
         */
        protected abstract function isEnabled();

        /**
         * @return string
         */
        protected abstract function getPostFieldName();

        /**
         * @private WordPress Hook
         */
        public function init(){
            if($this->isEnabled()) {
                $this->onInit();
            }
        }

        /**
         * @return bool
         */
        protected function deleteTimer(){
            $fieldname = $this->getPostFieldName();

            if(empty($fieldname) || !isset($_POST[$fieldname])){
                return false;
            }

            $hash = sanitize_text_field($_POST[$this->getPostFieldName()]);
            return CaptchaTimer::deleteByHash($hash);
        }

        /**
         * @return string
         */
        protected function generateTimerInputField(){
            $fieldname = $this->getPostFieldName();
            $hash = TimerValidator::getInstance()->addTimer();
            $html ="<div class='f12t'><input type=\"hidden\" class=\"f12_timer\" name=\"".esc_attr($fieldname)."\" value=\"" . esc_attr($hash) . "\"/></div>";

            return $html;
        }

        /**
         * Return the Validation Time in MS
         * @return int
         */
        public abstract function getValidationTime();

        //public static abstract function isSpam(...$args);

        /**
         * @return bool - return false if everything is ok
         */
        public function validateSpam($data, $delete = true, &$message = '')
        {
            $fieldname = $this->getPostFieldName();

            if(empty($fieldname) || !isset($data[$fieldname])){
                return false;
            }

            $hash = sanitize_text_field($data[$fieldname]);
            $Timer = TimerValidator::getInstance()->getTimer($hash);

            if(!$Timer){
                return true;
            }

            $timeInMs = round(microtime(true) * 1000);

            $validationTimeInMs = $this->getValidationTime();

            if(($timeInMs - $Timer->getValue()) < $validationTimeInMs){ // 2.5 Sek
                $message = sprintf(__('Form was filled in %d milliseconds. This was lower than your defined value of %d milliseconds. If this is a false positive try to adjust the default value.', 'f12-captcha'),$timeInMs - $Timer->getValue(), $validationTimeInMs);
                $isSpam = true; // isSpam
            }else{
                $isSpam = false;
            }

            if($delete == true) {
                $Timer->delete();
            }

            return $isSpam;
        }
    }
}