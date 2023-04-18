<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIIP
     */
    class UIIPProtection extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'ip', 'IP Protection');

            add_action('f12_cf7_captcha_ui_db_after_content', array($this, 'theContentIPLogReset'), 10, 1);
            add_action('f12_cf7_captcha_ui_db_after_content', array($this, 'theContentIPBanReset'), 10, 1);
            add_filter('f12_cf7_captcha_ui_db_before_on_save', array($this, 'maybeClean'), 10, 1);
        }

        private function doCleanIPLog()
        {
            if (isset($_POST['captcha-ip-log-clean-all'])) {
                $IPLogCleaner = new IPLogCleaner();
                if ($IPLogCleaner->resetTable()) {
                    Messages::getInstance()->add(__('IP Logs removed from database', 'f12-cf7-captcha'), 'success');
                } else {
                    Messages::getInstance()->add(__('Something went wrong, please try again later or contact the plugin author.', 'f12-cf7-captcha'), 'error');
                }
            }
        }

        private function doCleanIPBan()
        {
            if (isset($_POST['captcha-ip-ban-clean-all'])) {
                $IPBanCleaner = new IPBanCleaner();
                if ($IPBanCleaner->resetTable()) {
                    Messages::getInstance()->add(__('IP Bans removed from database', 'f12-cf7-captcha'), 'success');
                } else {
                    Messages::getInstance()->add(__('Something went wrong, please try again later or contact the plugin author.', 'f12-cf7-captcha'), 'error');
                }
            }
        }

        /**
         * Clean the database
         *
         * @param $message
         * @param $parameter
         *
         * @return string
         */
        public function maybeClean($settings)
        {
            $this->doCleanIPLog();
            $this->doCleanIPBan();
            return $settings;
        }

        /**
         * @private WP HOOK
         */
        public function getSettings($settings)
        {
            $settings['ip'] = array(
                'protect_ip' => 0, // enabled or not
                'max_retry' => 3, // max retries
                'max_retry_period' => 300, // time in seconds,
                'blockedtime' => 3600, // time in seconds - how long will the user be blocked if he fails to often
                'period_between_submits' => 60, // time between forms submits
            );
            return $settings;
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {
            foreach ($settings['ip'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['ip'][$key] = (int)$_POST[$key];
                    } else {
                        $settings['ip'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['ip'][$key] = 0;
                }
            }

            return $settings;
        }

        public function theContentIPBanReset($settings)
        {
            $entries = IPBan::getCount();

            ?>
            <div class="section">
                <div class="option">
                    <div class="label">
                        <label for="protect_comments"><?php _e('IP Bans', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <p style="margin-top:0;">
                            <strong><?php _e('Delete IP Bans Entries', 'f12-cf7-captcha'); ?></strong>
                        </p>
                        <p>
                            <?php _e('This entries will be deleted after the blocked time is over using a WP Cronjob. If you want to reset it manually, use the button below.', 'f12-cf7-captcha'); ?>
                        </p>
                        <p>
                            <strong><?php _e('Entries:', 'f12-cf7-captcha'); ?></strong>
                            <?php printf(__('%s entries in the database', 'f12-cf7-captcha'), $entries); ?>
                        </p>
                        <input type="submit" class="button" name="captcha-ip-ban-clean-all"
                               value="<?php _e('Delete All', 'f12-cf7-captcha'); ?>"/>
                        <p>
                            <?php _e('Make sure to backup your database before clicking one of these buttons.', 'f12-cf7-captcha'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }

        public function theContentIPLogReset($settings)
        {
            $entries = IPLog::getCount();

            ?>
            <div class="section">
                <div class="option">
                    <div class="label">
                        <label for="protect_comments"><?php _e('IP Logs', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <p style="margin-top:0;">
                            <strong><?php _e('Delete IP Log Entries', 'f12-cf7-captcha'); ?></strong>
                        </p>
                        <p>
                            <?php _e('This entries will be deleted using a WP Cronjob. If you want to reset it manually, use the button below.', 'f12-cf7-captcha'); ?>
                        </p>
                        <p>
                            <strong><?php _e('Entries:', 'f12-cf7-captcha'); ?></strong>
                            <?php printf(__('%s entries in the database', 'f12-cf7-captcha'), $entries); ?>
                        </p>
                        <input type="submit" class="button" name="captcha-ip-log-clean-all"
                               value="<?php _e('Delete All', 'f12-cf7-captcha'); ?>"/>
                        <p>
                            <?php _e('Make sure to backup your database before clicking one of these buttons.', 'f12-cf7-captcha'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }

        protected function addContentGeneralSettings($settings)
        {
            ?>
            <div class="option">
                <div class="label">
                    <label for="protect_ip"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                </div>
                <div class="input">
                    <!-- SEPARATOR -->
                    <input
                            id="protect_ip"
                            type="checkbox"
                            value="1"
                            name="protect_ip"
                        <?php echo isset($settings['protect_ip']) && $settings['protect_ip'] === 1 ? 'checked="checked"' : ''; ?>
                    />
                    <span>
                        <label for="protect_ip"><?php _e('Enable IP Protection. This will store the IP address SHA512 encrypted within the database and catch all submits.', 'f12-cf7-captcha'); ?></label>
                    </span>
                </div>
            </div>

            <div class="option">
                <div class="label">
                    <label for="max_retry"><?php _e('Max Retries', 'f12-cf7-captcha'); ?></label>
                </div>
                <div class="input">
                    <!-- SEPARATOR -->
                    <input
                            id="max_retry"
                            type="text"
                            value="<?php echo (int)$settings['max_retry']; ?>"
                            name="max_retry"
                    />
                    <span>
                        <label for="max_retry"><?php _e('Number of failed attempts before the IP address is blocked., (recommend: 3 tries)', 'f12-cf7-captcha'); ?></label>
                    </span>
                </div>
            </div>

            <div class="option">
                <div class="label">
                    <label for="blockedtime"><?php _e('Period for IP address block', 'f12-cf7-captcha'); ?></label>
                </div>
                <div class="input">
                    <!-- SEPARATOR -->
                    <input
                            id="blockedtime"
                            type="text"
                            value="<?php echo (int)$settings['blockedtime'] ?? 3600; ?>"
                            name="blockedtime"
                    />
                    <span>
                        <label for="blockedtime"><?php _e('Define how long the IP-Address will be blocked before submitting any data again. (recommend: 3600 = 1 hour)', 'f12-cf7-captcha'); ?></label>
                    </span>
                </div>
            </div>

            <div class="option">
                <div class="label">
                    <label for="max_retry_period"><?php _e('Time interval for detection of subsequent attacks', 'f12-cf7-captcha'); ?></label>
                </div>
                <div class="input">
                    <!-- SEPARATOR -->
                    <input
                            id="max_retry_period"
                            type="text"
                            value="<?php echo (int)$settings['max_retry_period'] ?? 500; ?>"
                            name="max_retry_period"
                    />
                    <span>
                        <label for="max_retry_period"><?php _e('Enter the period of time that will be used to recognize spam (e.g. enter 1000 for 1 second) (recommend: 3600 = 1 hour).', 'f12-cf7-captcha'); ?></label>
                    </span>
                </div>
            </div>
            <?php
        }

        protected function addContentSpamProtectionSettings($settings)
        {
            ?>
            <div class="option">
                <div class="label">
                    <label for="period_between_submits"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                </div>
                <div class="input">
                    <!-- SEPARATOR -->
                    <input
                            id="period_between_submits"
                            type="text"
                            value="<?php echo (int)$settings['period_between_submits']; ?>"
                            name="period_between_submits"
                    />
                    <span>
                        <label for="period_between_submits"><?php _e('Number of seconds between form submits. If they are smaller then the entered value, the submit will be recognized as Spam. (recommend: 60 seconds)', 'f12-cf7-captcha'); ?></label>
                    </span>
                </div>
            </div>
            <?php
        }

        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['ip'];

            ?>
            <h2>
                <?php _e('IP Protection', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('IP Protection', 'f12-cf7-captcha'); ?>
                </h3>
                <?php $this->addContentGeneralSettings($settings); ?>
            </div>
            <div class="section">
                <h3>
                    <?php _e('Interval Protection', 'f12-cf7-captcha'); ?>
                </h3>
                <?php $this->addContentSpamProtectionSettings($settings); ?>
            </div>

            <?php
        }

        protected function theSidebar($slug, $page)
        {
            return;
        }
    }
}