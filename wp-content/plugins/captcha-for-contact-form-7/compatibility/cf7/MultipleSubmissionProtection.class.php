<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class MultipleSubmissionProtection
     * Validate Contact Form 7 Forms
     */
    class MultipleSubmissionProtection extends TimerValidatorController
    {
        /**
         * @return bool
         */
        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_cf7_multiple_submissions', 'cf7');
        }

        /**
         * Hook for Double Opt In.
         * @return bool
         */
        public function isSpam($spam, $submission)
        {
            if($spam){
                return $spam;
            }

            $isSpam = false;

            $fieldname = self::getPostFieldName();

            if(empty($fieldname) || !isset($_POST[$fieldname])){
                $isSpam = true;
            }else {
                $hash = sanitize_text_field($_POST[$fieldname]);
                $Timer = TimerValidator::getInstance()->getTimer($hash);

                if (!$Timer) {
                    $isSpam = true;
                } else {
                    $Timer->delete();
                }
            }

            if($isSpam) {

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('CF7 Form - Multiple Submission Protection', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'TimerValidator failed in MultipleSubmissionProtection.class.php: Multiple submissions detected.');
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
            add_filter('wpcf7_spam', [$this, 'isSpam'], 100, 2);
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
            return 2000;
            //return (int)CF7Captcha::getInstance()->getSettings('protect_cf7_time_ms', 'cf7');
        }

        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_multiple_submission_protection';

            return $fieldname;
        }
    }
}