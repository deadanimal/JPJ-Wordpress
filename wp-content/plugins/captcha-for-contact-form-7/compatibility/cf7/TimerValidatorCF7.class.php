<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class TimerValidatorCF7
     * Validate Contact Form 7 Forms
     */
    class TimerValidatorCF7 extends TimerValidatorController
    {
        /**
         * @return bool
         */
        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_cf7_time_enable', 'cf7');
        }

        /**
         * Hook for Double Opt In.
         * @return bool
         */
        public static function isSpam($spam, $submission)
        {
            if($spam){
                return $spam;
            }
            $isSpam = TimerValidatorCF7::getInstance()->validateSpam($_POST, true, $message);

            if($isSpam) {

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('CF7 Form - Timer Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'TimerValidator failed in TimerValidatorCF7.class.php: '.$message);
                Log_WordPress::store($Log_Item);
            }

            return $isSpam;
        }

        /**
         * @private WordPress hook
         */
        public function onInit()
        {
            add_action('wpcf7_form_elements', array($this, 'addToCF7'), 100, 1);
            add_filter('wpcf7_spam', '\forge12\contactform7\CF7Captcha\TimerValidatorCF7::isSpam', 100, 2);
            add_action('wpcf7_before_send_mail', array($this, 'deleteCaptchaTimer'), 10, 3);
        }

        public function deleteCaptchaTimer($form, $abort, $submission)
        {
            $this->deleteTimer();
        }

        public function addToCF7($html)
        {
            return $html . $this->generateTimerInputField();
        }

        /**
         * Return the Validation Time in MS
         * @return int
         */
        public function getValidationTime()
        {
            return (int)CF7Captcha::getInstance()->getSettings('protect_cf7_time_ms', 'cf7');
        }

        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_timer';
            if (isset($settings['cf7']['protect_cf7_timer_fieldname'])) {
                $fieldname = $settings['cf7']['protect_cf7_timer_fieldname'];
            }
            return $fieldname;
        }
    }
}