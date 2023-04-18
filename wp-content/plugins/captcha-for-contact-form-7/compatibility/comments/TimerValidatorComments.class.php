<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class TimerValidatorComments
     * Validate Comments by Time
     */
    class TimerValidatorComments extends TimerValidatorController
    {
        protected function isEnabled()
        {
            return (bool)CF7Captcha::getInstance()->getSettings('protect_comments_time_enable', 'comments');
        }

        public function addFieldsToComments($fields)
        {
            $fields['redirect'] = '';
            $fields[$this->getPostFieldName()] = '';
            return $fields;
        }

        /**
         * @private WordPress Hook
         */
        public function onInit()
        {
            add_filter('comment_form_default_fields', array($this, 'addFieldsToComments'));
            add_filter('comment_form', array($this, 'addToComments'));
            add_filter('preprocess_comment', array($this, 'validateSpamProtection'));
        }

        public function addToComments()
        {
            if (isset($_GET['spam'])) {
                echo '<div class="error"><p><center>' . __('Spam detected. Please try again or contact the site administrator', FORGE12_CAPTCHA_SLUG) . '</center></p></div>';
            }

            echo $this->generateTimerInputField();
            echo '<input type="hidden" style="" name="redirect" value="' . esc_attr(base64_encode(CF7Captcha::getInstance()->getCurrentURL())) . '" />';
        }

        /**
         * Return the Validation Time in MS
         * @return int
         */
        public function getValidationTime()
        {
            return (int)CF7Captcha::getInstance()->getSettings('protect_comments_time_ms', 'comments');
        }

        public function validateSpamProtection($commentdata)
        {
            if ($this->validateSpam($_POST, true, $message)) {

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('Avada Form - Timer Validator', 'f12-captcha'),
                    $commentdata,
                    'spam',
                    'TimerValidator failed in TimerValidatorComments.class.php: '.$message);
                Log_WordPress::store($Log_Item);

                /*
                 * Add Redirect
                 */
                $redirect = base64_decode(sanitize_text_field($_POST['redirect']));
                if (strpos('?', $redirect) !== false) {
                    $redirect .= '/&';
                } else {
                    $redirect .= '/?';
                }

                $redirect .= 'spam=1#reply-title';
                wp_redirect($redirect);
                die();
            } else {
                return $commentdata;
            }
        }

        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_timer';
            if (isset($settings['comments']['protect_comments_timer_fieldname'])) {
                $fieldname = $settings['comments']['protect_comments_timer_fieldname'];
            }
            return $fieldname;
        }
    }
}