<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ElementorCaptcha
     */
    class ElementorCaptcha
    {
        public function __construct()
        {
            add_action('elementor_pro/forms/validation', array($this, 'validateSpamProtection'), 10, 2);
            add_filter('elementor_pro/forms/render/item', array($this, 'addSpamProtection'), 20, 3);
        }

        /**
         * @return string
         */
        private function getCaptchaMethod()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $method = 'image';

            if (isset($settings['elementor']['protect_elementor_method'])) {
                $method = $settings['elementor']['protect_elementor_method'];
            }
            return $method;
        }

        /**
         * @return string
         */
        private function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_captcha';
            if (isset($settings['elementor']['protect_elementor_fieldname'])) {
                $fieldname = $settings['elementor']['protect_elementor_fieldname'];
            }
            return $fieldname;
        }

        /**
         * @param string                                   $item
         * @param int                                      $item_index
         * @param \ElementorPro\Modules\Forms\Widgets\Form $form
         *
         * @return string
         */
        public function addSpamProtection($item, $item_index, $form)
        {
            $instance = $form->get_settings_for_display();
            $item_counter = count($instance['form_fields']);

            if ($item_index != $item_counter-1 || (isset($_POST) && !empty($_POST))) {
                return $item;
            }

            $fieldname = $this->getPostFieldName();

            if(ControllerElementor::getCaptchaMethod() == 'math') {
                $captcha = CaptchaMathGenerator::get_form_field($fieldname, 'elementor-field-type-text elementor-field-group elementor-column elementor-field-group-text elementor-col-100 elementor-field-required');
            }else{
                $captcha = CaptchaImageGenerator::get_form_field($fieldname, 'elementor-field-type-text elementor-field-group elementor-column elementor-field-group-text elementor-col-100 elementor-field-required');
            }

            echo $captcha;

            return $item;
        }

        public function validateSpamProtection($record, $ajax_handler)
        {
            $fields = $record->get('fields');

            if (!isset($fields)) {
                return false;
            }

            $is_valid = true;

            if(empty($_POST[$this->getPostFieldName()])){
                $is_valid = false;
            }

            $value = sanitize_text_field($_POST[$this->getPostFieldName()]);
            $hash = sanitize_text_field($_POST[$this->getPostFieldName().'_hash']);

            $Captcha = Captcha::getByHash($hash);
            if(!$Captcha || $value != $Captcha->getCode()){
                $is_valid = false;
            }

            /*
             * Validate the honeypot.
             */
            if (!$is_valid) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
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
                    __('Elementor Form - Captcha Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in ElementorCaptcha.class.php');
                Log_WordPress::store($Log_Item);

                $ajax_handler->add_error($field_name, esc_html__('Captcha not correct.', 'f12-captcha'));
            }else{
                $Captcha->setValidated(1);
                $Captcha->save();
            }
        }
    }
}