<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UITimer
     */
    class UICaptchaTimerProtection extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'timerprotection', 'Time Protection');
            add_action('f12_cf7_captcha_ui_db_after_content', array($this, 'theContentTimerReset'), 10, 1);
            add_filter('f12_cf7_captcha_ui_db_before_on_save', array($this, 'clean'), 10, 1);
        }

        public function theContentTimerReset($settings)
        {
            $entries = CaptchaTimer::getCount();

            ?>
            <div class="section">
                <div class="option">
                    <div class="label">
                        <label for="protect_comments"><?php _e('Timers', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <p style="margin-top:0;">
                            <strong><?php _e('Delete Database Captcha Timer Entries', 'f12-cf7-captcha'); ?></strong>
                        </p>
                        <p>
                            <?php _e('This entries will be deleted using a WP Cronjob. If you want to reset it manually, use the button below.', 'f12-cf7-captcha'); ?>
                        </p>
                        <p>
                            <strong><?php _e('Entries:', 'f12-cf7-captcha'); ?></strong>
                            <?php printf(__('%s entries in the database', 'f12-cf7-captcha'), $entries); ?>
                        </p>
                        <input type="submit" class="button" name="captcha-timer-clean-all"
                               value="<?php _e('Delete All', 'f12-cf7-captcha'); ?>"/>
                        <p>
                            <?php _e('Make sure to backup your database before clicking one of these buttons.', 'f12-cf7-captcha'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }

        public function hideInMenu()
        {
            return true;
        }

        /**
         * @private WP HOOK
         */
        public function getSettings($settings)
        {
            return $settings;
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {
            return $settings;
        }

        public function clean($settings)
        {
            if (isset($_POST['captcha-timer-clean-all'])) {
                $Cleaner = new CaptchaTimerCleaner();
                if ($Cleaner->resetTable()) {
                    Messages::getInstance()->add(__('Timers removed from database', 'f12-cf7-captcha'), 'success');
                } else {
                    Messages::getInstance()->add(__('Something went wrong, please try again later or contact the plugin author.', 'f12-cf7-captcha'), 'error');
                }
            }

            return $settings;
        }

        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
        }

        protected function theSidebar($slug, $page)
        {
        }
    }
}