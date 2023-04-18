<?php

namespace forge12\contactform7\CF7Captcha {

    use Action_Scheduler\Migration\Controller;
    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class TimerValidatorElementor
     */
    class TimerValidatorElementor extends TimerValidatorController
    {
        /**
         * @private WordPress Hook
         */
        public function onInit()
        {
            add_action('elementor_pro/forms/validation', array($this, 'validateSpamProtection'), 10, 2);
            add_filter('elementor_pro/forms/render/item', array($this, 'addSpamProtection'), 10, 3);
        }

        public function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_timer';
            if (isset($settings['elementor']['protect_elementor_timer_fieldname'])) {
                $fieldname = $settings['elementor']['protect_elementor_timer_fieldname'];
            }
            return $fieldname;
        }

        public function addSpamProtection($item, $item_index, $form)
        {
            if ($item_index != 0 || (isset($_POST) && !empty($_POST))) {
                return $item;
            }

            echo $this->generateTimerInputField();

            return $item;
        }

        /**
         * Return the Validation Time in MS
         * @return int
         */
        public function getValidationTime()
        {
            return (int)CF7Captcha::getInstance()->getSettings('protect_elementor_time_ms', 'elementor');
        }

        /**
         * @param $value
         * @return void
         */
        public function validateSpamProtection($record, $ajax_handler)
        {
            $fields = $record->get('fields');

            if (!isset($fields)) {
                return;
            }

            if (isset($_POST) && isset($_POST[$this->getPostFieldName()])) {

                $data = [$this->getPostFieldName() => $_POST[$this->getPostFieldName()]];

                if ($this->validateSpam($data, true, $message)) {

                    /*
                     * Get the first field that is not hidden to show the error message to the visitor.
                     */
                    $field_name = '';
                    foreach ($fields as $key => $data) {
                        if ($data['type'] != 'hidden') {
                            $field_name = $key;
                        }
                    }

                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Elementor Form - Timer Validator', 'f12-captcha'),
                        $data,
                        'spam',
                        'TimerValidator failed in TimerValidatorElementor.class.php: '.$message);
                    Log_WordPress::store($Log_Item);

                    $ajax_handler->add_error( $field_name, esc_html__( 'Spam detected.', 'f12-captcha' ) );
                }
            }
        }

        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_elementor_time_enable', 'elementor');
        }
    }
}