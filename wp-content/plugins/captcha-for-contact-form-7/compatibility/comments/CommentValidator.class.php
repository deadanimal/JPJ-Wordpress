<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;
    use WP_Comment;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class CommentValidator
     * This class will enable the spam protection for the comment section of wordpress
     */
    class CommentValidator
    {
        public function __construct()
        {
            add_filter('comment_form_after_fields', array($this, 'addToComments'));
            add_filter('preprocess_comment', array($this, 'validateSpamProtection'), 1);
            add_action('wp_insert_comment', array($this, 'after_insert_comment'), 10, 2);
        }

        /**
         * @param            $id
         * @param WP_Comment $comment
         *
         * @return void
         */
        public function after_insert_comment($id, $comment)
        {
            /*
             * Add Log Entries
             */
            $Log_Item = new Log_Item(__('Comment', 'f12-captcha'), $comment->to_array(), 'verified', __('Comment successfully committed', 'f12-captcha'));
            Log_WordPress::store($Log_Item);
        }

        public function addToComments()
        {
            $fieldname = $this->getPostFieldName();

            switch (ControllerComments::getCaptchaMethod()) {
                case 'math':
                    $captcha = CaptchaMathGenerator::get_form_field($fieldname);
                    break;
                case 'image':
                    $captcha = CaptchaImageGenerator::get_form_field($fieldname);
                    break;
                default:
                    $captcha = CaptchaHoneypotGenerator::get_form_field($fieldname);
                    break;
            }

            echo $captcha;
        }

        protected function getPostFieldName()
        {
            $settings = CF7Captcha::getInstance()->getSettings();

            $fieldname = 'f12_captcha';
            if (isset($settings['comments']['protect_comments_fieldname'])) {
                $fieldname = $settings['comments']['protect_comments_fieldname'];
            }
            return $fieldname;
        }

        public function validateSpamProtection($commentdata)
        {
            /*
             * skip validation if user can moderate oder edit comments.
             */
            if (current_user_can('moderate_comments')) {
                return $commentdata;
            }

            /*
             * check for spam
             */
            $fieldname = $this->getPostFieldName();

            /*
             * Check for the captcha field
             */
            if (!isset($_POST[$fieldname])) {
                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('Comment Form - Validator', 'f12-captcha'),
                    $_POST,
                    'spam',
                    'Captcha failed in comment section of CommentValidator.class.php');
                Log_WordPress::store($Log_Item);

                wp_die(__('Error: Spam', 'f12-cf7-captcha'));
            }

            switch (ControllerComments::getCaptchaMethod()) {
                case 'math':
                    $isValid = CaptchaMathGenerator::validate($_POST[$fieldname], $_POST[$fieldname . '_hash']);
                    break;
                case 'image':
                    $isValid = CaptchaImageGenerator::validate($_POST[$fieldname], $_POST[$fieldname . '_hash']);
                    break;
                default:
                    $isValid = CaptchaHoneypotGenerator::validate($_POST[$fieldname]);
                    break;
            }

            if (!$isValid) {

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(__('Comment', 'f12-captcha'), $commentdata, 'spam', 'Captcha not matching in CommentValidator.class.php');
                Log_WordPress::store($Log_Item);

                wp_die(__('Error: Spam', 'f12-cf7-captcha'));
            }

            return $commentdata;
        }
    }
}