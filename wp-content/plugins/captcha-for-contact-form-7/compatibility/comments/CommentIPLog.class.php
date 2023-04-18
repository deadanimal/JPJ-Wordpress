<?php

namespace forge12\contactform7\CF7Captcha {

    use forge12\contactform7\CF7Captcha\core\log\Log_Item;
    use forge12\contactform7\CF7Captcha\core\log\Log_WordPress;

    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Comment IP Log
     */
    class CommentIPLog
    {
        public function __construct()
        {
           add_filter('preprocess_comment', array($this, 'validateSpamProtection'));
        }

        public function validateSpamProtection($commentdata)
        {
            // Validate the IP Address
            if(!IPValidator::getInstance()->validate()){

                /*
                 * Add Log Entries
                 */
                $Log_Item = new Log_Item(
                    __('IP Log failed', 'f12-captcha'),
                    $commentdata,
                    'spam',
                    'IP Address is blocked in CommentIPLog.class.php');
                Log_WordPress::store($Log_Item);

                wp_die(__('Error: Spam'));
            }
            return $commentdata;
        }
    }
}