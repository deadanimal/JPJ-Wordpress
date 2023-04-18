<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class ElementorValidator
     */
    class ElementorValidator
    {
        public function __construct()
        {
            add_action('elementor_pro/forms/validation', array($this, 'validateSpamProtection'), 10, 2);
            add_filter('elementor_pro/forms/render/item', array($this, 'addSpamProtection'), 10, 3);
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
            if ($item_index != 0 || (isset($_POST) && !empty($_POST))) {
                return $item;
            }

            $fieldname = $this->getPostFieldName();

            echo '<div class="elementor-field-type-text elementor-field-group elementor-column elementor-field-group-text elementor-col-100 elementor-field-required"><input type="text" style="visibility:hidden; opacity:1; height:0; width:0; margin:0; padding:0;" name="' . esc_attr($fieldname) . '" value="" /></div>';

            return $item;
        }

        public function validateSpamProtection($record, $ajax_handler)
        {
            $fields = $record->get('fields');

            if (!isset($fields)) {
                return false;
            }

            /*
             * Validate the honeypot.
             */
            if (!empty($_POST[$this->getPostFieldName()])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

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
                    'Captcha failed in ElementorValidator.class.php');
                Log_WordPress::store($Log_Item);

                $ajax_handler->add_error($field_name, esc_html__('Spam detected.', 'f12-captcha'));
            }
        }
    }
}