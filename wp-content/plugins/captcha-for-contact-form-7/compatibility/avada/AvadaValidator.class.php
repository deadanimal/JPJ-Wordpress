<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class AvadaValidator
     */
    class AvadaValidator
    {
        public function __construct()
        {
            add_filter('fusion_element_form_content', array($this, 'addSpamProtection'), 10, 2);
            add_filter('fusion_form_demo_mode', array($this, 'validateSpamProtection'), 10, 1);

            add_filter('fusion_element_contact_form_content', array($this, 'addSpamProtectionToContactTemplate'), 10, 2);
            add_filter('init', array($this, 'validateSpamProtectionFromContactTemplate'));
        }

        /**
         * @return string
         */
        private function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_captcha';
            if (isset($settings['avada']['protect_avada_fieldname'])) {
                $fieldname = $settings['avada']['protect_avada_fieldname'];
            }
            return $fieldname;
        }

        public function addSpamProtectionToContactTemplate($html, $args)
        {
            $settings = CF7Captcha::getInstance()->getSettings();
            $fieldname = $this->getPostFieldName();

            $captcha = CaptchaHoneypotGenerator::get_form_field($fieldname);

            if (isset($settings['avada']) && isset($settings['protect_avada_position'])) {
                $position = $settings['avada']['protect_avada_position'];
            } else {
                $position = 'after_submit';
            }

            if ($position === 'before_submit') {
                $html = str_replace('<div id="comment-submit-container">', $captcha . '<div id="comment-submit-container">', $html);
            } else {
                $html = str_replace("</form>", $captcha . '</form>', $html);
            }

            return $html;
        }

        public function validateSpamProtectionFromContactTemplate()
        {
            if (!isset($_POST['submit']) || !isset($_POST[$this->getPostFieldName()]) || !isset($_POST['msg']) || !isset($_POST['contact_name'])) {
                return;
            }

            if (empty($_POST[$this->getPostFieldName()])) {
                return;
            }

            /*
             * Add Log Entries
             */
            $Log_Item = new Log_Item(
                __('Avada Form - Captcha Validator', 'f12-captcha'),
                $_POST,
                'spam',
                'Captcha failed in AvadaValidator.class.php for Contact Template');
            Log_WordPress::store($Log_Item);

            wp_die('Sorry, this mail has been blocked by spam protection.');
        }

        /**
         * @param array $default
         *
         * @return array
         */
        public function addSpamProtection($html, $args)
        {
            $settings = CF7Captcha::getInstance()->getSettings();
            $fieldname = $this->getPostFieldName();

            switch (ControllerAvada::getCaptchaMethod()) {
                case 'math':
                    $captcha = CaptchaMathGenerator::get_form_field($fieldname, 'fusion-form-input');
                    break;
                case 'image':
                    $captcha = CaptchaImageGenerator::get_form_field($fieldname, 'fusion-form-input');
                    break;
                default:
                    $captcha = CaptchaHoneypotGenerator::get_form_field($fieldname);
                    break;
            }

            if (isset($settings['avada']) && isset($settings['avada']['protect_avada_position'])) {
                $position = $settings['avada']['protect_avada_position'];
            } else {
                $position = 'after_submit';
            }

            if ($position === 'before_submit') {
                $html = str_replace('<div class="fusion-form-field fusion-form-submit-field', $captcha . '<div class="fusion-form-field fusion-form-submit-field', $html);
            } else {
                $html = str_replace("</form>", $captcha . '</form>', $html);
            }

            return $html;
        }

        public function validateSpamProtection($value)
        {
            /*
             * Avada sends the form fields as formdata string.
             */
            if (isset($_POST['formData'])) {
                $formData = ControllerAvada::formDataToArray($_POST['formData']);

                $fieldname = $this->getPostFieldName();

                /*
                 * Check if the fields exists
                 */
                if (!isset($formData[$fieldname])) {

                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Avada Form - Captcha Validator', 'f12-captcha'),
                        $_POST,
                        'spam',
                        'Captcha failed in AvadaValidator.class.php, captcha not found.');
                    Log_WordPress::store($Log_Item);

                    die(wp_json_encode(ControllerAvada::get_results_from_message('error', 'spam-0')));
                }


                /*
                 * Check if the Captcha is valid
                 */
                switch (ControllerAvada::getCaptchaMethod()) {
                    case 'math':
                        $isValid = CaptchaMathGenerator::validate($formData[$fieldname], $formData[$fieldname . '_hash']);
                        break;
                    case 'image':
                        $isValid = CaptchaImageGenerator::validate($formData[$fieldname], $formData[$fieldname . '_hash']);
                        break;
                    default:
                        $isValid = CaptchaHoneypotGenerator::validate($formData[$fieldname]);
                        break;
                }

                if (!$isValid) {

                    /*
                     * Add Log Entries
                     */
                    $Log_Item = new Log_Item(
                        __('Avada Form - Captcha Validator', 'f12-captcha'),
                        $_POST,
                        'spam',
                        'Captcha validation failed in AvadaValidator.class.php');
                    Log_WordPress::store($Log_Item);

                    die(wp_json_encode(ControllerAvada::get_results_from_message('error', 'spam-1')));
                }
            }
            return $value;
        }
    }
}