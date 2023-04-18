<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class MultipleSubmissionProtection
     * Validate Avada Forms
     */
    class AvadaMultipleSubmissionProtection extends TimerValidatorController
    {
        /**
         * @return bool
         */
        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_avada_multiple_submissions', 'avada');
        }

        /**
         * Hook for Double Opt In.
         *
         * @return bool
         */
        public function validateSpamProtection($value)
        {
            if (isset($_POST) && isset($_POST['formData'])) {
                $data = ControllerAvada::formDataToArray($_POST['formData']);

                $fieldname = self::getPostFieldName();

                if (!isset($data[$fieldname]) || empty($data[$fieldname])) {
                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Avada Form - Multiple Submission Protection', 'f12-captcha'),
                        $data,
                        'spam',
                        'TimerValidator failed in AvadaMultipleSubmissionProtection.class.php: Multiple submission protection field missing or empty.');
                    Log_WordPress::store($Log_Item);

                    die(wp_json_encode(ControllerAvada::get_results_from_message('error', 'spam')));
                }

                $hash = sanitize_text_field($data[$fieldname]);
                $Timer = TimerValidator::getInstance()->getTimer($hash);

                if(!$Timer) {
                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Avada Form - Multiple Submission Protection', 'f12-captcha'),
                        $data,
                        'spam',
                        'TimerValidator failed in AvadaMultipleSubmissionProtection.class.php: Multiple submissions detected.');
                    Log_WordPress::store($Log_Item);

                    die(wp_json_encode(ControllerAvada::get_results_from_message('error', 'spam')));
                }

                $Timer->delete();
            }

            return $value;
        }

        /**
         * @private WordPress hook
         */
        public function onInit()
        {
            add_action('fusion_form_before_close', array($this, 'addToAvada'), 10, 2);
            add_filter('fusion_form_demo_mode', array($this, 'validateSpamProtection'), 10, 1);
        }

        public function addToAvada($args, $params)
        {
            echo $this->generateTimerInputField();
        }

        /**
         * Return the Validation Time in MS
         *
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