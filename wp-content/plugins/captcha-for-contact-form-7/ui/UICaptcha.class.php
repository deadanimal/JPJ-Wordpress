<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UICaptcha
     */
    class UICaptcha extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'captcha', 'Captcha');
            add_action('f12_cf7_captcha_ui_db_after_content', array($this, 'theContentCaptchaReset'), 10, 1);
            add_filter('f12_cf7_captcha_ui_db_before_on_save', array($this, 'clean'), 10, 1);
        }

        public function theContentCaptchaReset($settings)
        {
            $entries = Captcha::getCount();
            $validated = Captcha::getCount(1);
            $nonValidated = Captcha::getCount(0);

            ?>
            <div class="section">
                <div class="option">
                    <div class="label">
                        <label for="protect_comments"><?php _e('Captchas', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <p style="margin-top:0;">
                            <strong><?php _e('Delete Database Captcha Entries', 'f12-cf7-captcha'); ?></strong>
                        </p>
                        <p>
                            <?php _e('This entries will be deleted using a WP Cronjob. If you want to reset it manually, use the buttons below.', 'f12-cf7-captcha'); ?>
                        </p>
                        <p>
                            <strong><?php _e('Entries:', 'f12-cf7-captcha'); ?></strong>
                            <?php printf(__('%s entries in the database', 'f12-cf7-captcha'), $entries); ?>
                        </p>
                        <p>
                            <strong><?php _e('Validated:', 'f12-cf7-captcha'); ?></strong>
                            <?php printf(__('%s entries in the database', 'f12-cf7-captcha'), $validated); ?>
                        </p>
                        <p>
                            <strong><?php _e('Non-Validated:', 'f12-cf7-captcha'); ?></strong>
                            <?php printf(__('%s entries in the database', 'f12-cf7-captcha'), $nonValidated); ?>
                        </p>
                        <input type="submit" class="button" name="captcha-clean-all"
                               value="<?php _e('Delete All', 'f12-cf7-captcha'); ?>"/>
                        <input type="submit" class="button" name="captcha-clean-validated"
                               value="<?php _e('Delete Validated', 'f12-cf7-captcha'); ?>"/>
                        <input type="submit" class="button" name="captcha-clean-nonvalidated"
                               value="<?php _e('Deleted Non-Validated', 'f12-cf7-captcha'); ?>"/>
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
            $Cleaner = new CaptchaCleaner();
            if (isset($_POST['captcha-clean-all'])) {
                if ($Cleaner->resetTable()) {
                    Messages::getInstance()->add(__('Captchas removed from database', 'f12-cf7-captcha'), 'success');
                } else {
                    Messages::getInstance()->add(__('Something went wrong, please try again later or contact the plugin author.', 'f12-cf7-captcha'), 'error');
                }
            }
            if (isset($_POST['captcha-clean-validated'])) {
                if ($Cleaner->cleanValidated()) {
                    Messages::getInstance()->add(__('Validated Captchas removed from database', 'f12-cf7-captcha'), 'success');
                } else {
                    Messages::getInstance()->add(__('Something went wrong, please try again later or contact the plugin author.', 'f12-cf7-captcha'), 'error');
                }
            }
            if (isset($_POST['captcha-clean-nonvalidated'])) {
                if ($Cleaner->cleanNonValidated()) {
                    Messages::getInstance()->add(__('Non Validated Captchas removed from database', 'f12-cf7-captcha'), 'success');
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