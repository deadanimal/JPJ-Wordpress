<?php

namespace forge12\contactform7\CF7Captcha {

    use Action_Scheduler\Migration\Controller;
    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class TimerValidatorAvada
     */
    class TimerValidatorAvada extends TimerValidatorController
    {
        /**
         * @private WordPress Hook
         */
        public function onInit()
        {
            add_action('fusion_form_before_close', array($this, 'addToAvada'), 10, 2);
            add_filter('fusion_form_demo_mode', array($this, 'validateSpamProtection'), 10, 1);

        }

        public function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_timer';
            if (isset($settings['avada']['protect_avada_timer_fieldname'])) {
                $fieldname = $settings['avada']['protect_avada_timer_fieldname'];
            }
            return $fieldname;
        }

        public function addToAvada($args, $params)
        {
            echo $this->generateTimerInputField();
        }

        /**
         * Return the Validation Time in MS
         * @return int
         */
        public function getValidationTime()
        {
            return (int)CF7Captcha::getInstance()->getSettings('protect_avada_time_ms', 'avada');
        }

        /**
         * @param $value
         * @return void
         */
        public function validateSpamProtection($value)
        {
            if (isset($_POST) && isset($_POST['formData'])) {
                $data = ControllerAvada::formDataToArray($_POST['formData']);
                if ($this->validateSpam($data, true, $message)) {
                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Avada Form - Timer Validator', 'f12-captcha'),
                        $data,
                        'spam',
                        'TimerValidator failed in TimerValidatorAvada.class.php: '.$message);
                    Log_WordPress::store($Log_Item);

                    die(wp_json_encode(ControllerAvada::get_results_from_message('error', 'spam')));
                }
            }
            return $value;
        }

        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_avada_time_enable', 'avada');
        }
    }
}