<?php

namespace forge12\contactform7\CF7Captcha {

    use Action_Scheduler\Migration\Controller;
    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ElementorMultipleSubmissionProtection
     */
    class ElementorMultipleSubmissionProtection extends TimerValidatorController
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

            $fieldname = 'f12_multiple_submission_protection';

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
            return 2000;
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

            $fieldname = $this->getPostFieldName();

            if (isset($_POST)) {

                if (!isset($_POST[$fieldname]) || empty($_POST[$fieldname])) {

                    $data = [];
                    foreach($_POST as $key => $value) {
                        $data[sanitize_text_field($key)] = sanitize_text_field($value);
                    }

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
                        'TimerValidator failed in AvadaMultipleSubmissionProtection.class.php: Multiple submission protection field missing or empty.');
                    Log_WordPress::store($Log_Item);

                    $ajax_handler->add_error($field_name, esc_html__('Spam detected.', 'f12-captcha'));
                }

                $hash = sanitize_text_field($_POST[$fieldname]);
                $Timer = TimerValidator::getInstance()->getTimer($hash);

                if(!$Timer){
                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Elementor Form - Timer Validator', 'f12-captcha'),
                        $data,
                        'spam',
                        'TimerValidator failed in AvadaMultipleSubmissionProtection.class.php: Multiple submissions detected.');
                    Log_WordPress::store($Log_Item);

                    $ajax_handler->add_error( $field_name, esc_html__( 'Spam detected.', 'f12-captcha' ) );
                }else {
                    $Timer->delete();
                }
            }
        }

        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_elementor_multiple_submissions', 'elementor');
        }
    }
}