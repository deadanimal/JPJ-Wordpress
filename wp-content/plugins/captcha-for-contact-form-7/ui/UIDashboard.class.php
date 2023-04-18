<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIDashboard
     */
    class UIDashboard extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'f12-cf7-captcha', 'Dashboard', 0);
        }

        /**
         * @param $settings
         *
         * @return mixed
         */
        public function getSettings($settings)
        {
            $settings['global'] = [
                'protection_level' => 0,
                'protection_method' => 'honey'
            ];

            return $settings;
        }

        protected function onSave($settings)
        {
            $level = isset($_POST['protection_level']) ? (int)$_POST['protection_level'] : 0;
            $method = isset($_POST['protection_method']) ? sanitize_text_field($_POST['protection_method']) : 'honey';

            $settings['global']['protection_level'] = $level;
            $settings['global']['protection_method'] = $method;

            $blacklist = $settings['rules']['rule_blacklist_value'];
            if (empty($blacklist)) {
                $blacklist = RulesAjax::get_blacklist_content();
                update_option('disallowed_keys', $blacklist);
            }

            // Set global settings
            if ($level === 0) {
                $settings['avada'] = array(
                    'protect_avada' => 0,
                    'protect_avada_time_enable' => 0,
                    'protect_avada_time_ms' => 500,
                    'protect_avada_fieldname' => 'f12_captcha',
                    'protect_avada_timer_fieldname' => 'f12_timer',
                    'protect_avada_multiple_submissions' => 0,
                    'protect_avada_method' => $method,
                    'protect_avada_position' => 'before_submit'
                );

                $settings['cf7'] = array(
                    'protect_cf7_time_enable' => 0,
                    'protect_cf7_time_ms' => 500,
                    'protect_cf7_timer_fieldname' => 'f12_timer',
                    'protect_cf7_fieldname' => 'f12_honey',
                    'protect_cf7_captcha_enable' => 0,
                    'protect_cf7_multiple_submissions' => 0,
                    'protect_cf7_method' => $method
                );

                $settings['comments'] = array(
                    'protect_comments' => 0,
                    'protect_comments_time_enable' => 0,
                    'protect_comments_time_ms' => 500,
                    'protect_comments_fieldname' => 'f12_captcha',
                    'protect_comments_timer_fieldname' => 'f12_timer',
                    'protect_comments_method' => $method
                );

                $settings['elementor'] = array(
                    'protect_elementor' => 0,
                    'protect_elementor_time_enable' => 0,
                    'protect_elementor_time_ms' => 500,
                    'protect_elementor_fieldname' => 'f12_captcha',
                    'protect_elementor_timer_fieldname' => 'f12_timer',
                    'protect_elementor_method' => $method,
                    'protect_elementor_multiple_submissions' => 0,
                );

                $settings['rules'] = array(
                    'support' => 1,
                    'rule_url' => 0,
                    'rule_url_limit' => 0,
                    'rule_blacklist' => 0,
                    'rule_blacklist_greedy' => 0,
                    'rule_blacklist_value' => '1',
                    'rule_bbcode_url' => 0,
                    'rule_error_message_url' => __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'),
                    'rule_error_message_bbcode' => __('BBCode is not allowed.', 'f12-captcha'),
                    'rule_error_message_blacklist' => __('The word %s is blacklisted.', 'f12-captcha'),
                );

                $settings['ip'] = array(
                    'protect_ip' => 0, // enabled or not
                    'max_retry' => 3, // max retries
                    'max_retry_period' => 300, // time in seconds,
                    'blockedtime' => 3600, // time in seconds - how long will the user be blocked if he fails to often
                    'period_between_submits' => 60, // time between forms submits
                );

                $settings['ultimatemember'] = array(
                    'protect_enable' => 0,
                    'protect_fieldname' => 'f12_captcha',
                    'protect_method' => $method,
                    'protect_login_enable' => 0,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                );

                $settings['woocommerce'] = array(
                    'protect_login' => 0,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 0,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method,
                );

                $settings['wp_login_page'] = array(
                    'protect_login' => 0,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 0,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method
                );
            }

            if ($level === 1) {
                $settings['avada'] = array(
                    'protect_avada' => 1,
                    'protect_avada_time_enable' => 0,
                    'protect_avada_time_ms' => 500,
                    'protect_avada_fieldname' => 'f12_captcha',
                    'protect_avada_timer_fieldname' => 'f12_timer',
                    'protect_avada_multiple_submissions' => 0,
                    'protect_avada_method' => $method,
                    'protect_avada_position' => 'before_submit'
                );

                $settings['cf7'] = array(
                    'protect_cf7_time_enable' => 0,
                    'protect_cf7_time_ms' => 500,
                    'protect_cf7_timer_fieldname' => 'f12_timer',
                    'protect_cf7_fieldname' => 'f12_honey',
                    'protect_cf7_captcha_enable' => 1,
                    'protect_cf7_multiple_submissions' => 0,
                    'protect_cf7_method' => $method
                );

                $settings['comments'] = array(
                    'protect_comments' => 1,
                    'protect_comments_time_enable' => 0,
                    'protect_comments_time_ms' => 500,
                    'protect_comments_fieldname' => 'f12_captcha',
                    'protect_comments_timer_fieldname' => 'f12_timer',
                    'protect_comments_method' => $method
                );

                $settings['elementor'] = array(
                    'protect_elementor' => 1,
                    'protect_elementor_time_enable' => 0,
                    'protect_elementor_time_ms' => 500,
                    'protect_elementor_fieldname' => 'f12_captcha',
                    'protect_elementor_timer_fieldname' => 'f12_timer',
                    'protect_elementor_method' => $method,
                    'protect_elementor_multiple_submissions' => 0,
                );

                $settings['rules'] = array(
                    'support' => 1,
                    'rule_url' => 0,
                    'rule_url_limit' => 0,
                    'rule_blacklist' => 1,
                    'rule_blacklist_greedy' => 0,
                    'rule_blacklist_value' => '1',
                    'rule_bbcode_url' => 0,
                    'rule_error_message_url' => __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'),
                    'rule_error_message_bbcode' => __('BBCode is not allowed.', 'f12-captcha'),
                    'rule_error_message_blacklist' => __('The word %s is blacklisted.', 'f12-captcha'),
                );

                $settings['ip'] = array(
                    'protect_ip' => 0, // enabled or not
                    'max_retry' => 3, // max retries
                    'max_retry_period' => 300, // time in seconds,
                    'blockedtime' => 3600, // time in seconds - how long will the user be blocked if he fails to often
                    'period_between_submits' => 60, // time between forms submits
                );

                $settings['ultimatemember'] = array(
                    'protect_enable' => 1,
                    'protect_fieldname' => 'f12_captcha',
                    'protect_method' => $method,
                    'protect_login_enable' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                );

                $settings['woocommerce'] = array(
                    'protect_login' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 1,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method,
                );

                $settings['wp_login_page'] = array(
                    'protect_login' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 1,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method
                );
            }

            if ($level === 2) {
                $settings['avada'] = array(
                    'protect_avada' => 1,
                    'protect_avada_time_enable' => 0,
                    'protect_avada_time_ms' => 500,
                    'protect_avada_fieldname' => 'f12_captcha',
                    'protect_avada_timer_fieldname' => 'f12_timer',
                    'protect_avada_multiple_submissions' => 1,
                    'protect_avada_method' => $method,
                    'protect_avada_position' => 'before_submit'
                );

                $settings['cf7'] = array(
                    'protect_cf7_time_enable' => 0,
                    'protect_cf7_time_ms' => 500,
                    'protect_cf7_timer_fieldname' => 'f12_timer',
                    'protect_cf7_fieldname' => 'f12_honey',
                    'protect_cf7_captcha_enable' => 1,
                    'protect_cf7_multiple_submissions' => 1,
                    'protect_cf7_method' => $method
                );

                $settings['comments'] = array(
                    'protect_comments' => 1,
                    'protect_comments_time_enable' => 0,
                    'protect_comments_time_ms' => 500,
                    'protect_comments_fieldname' => 'f12_captcha',
                    'protect_comments_timer_fieldname' => 'f12_timer',
                    'protect_comments_method' => $method
                );

                $settings['elementor'] = array(
                    'protect_elementor' => 1,
                    'protect_elementor_time_enable' => 0,
                    'protect_elementor_time_ms' => 500,
                    'protect_elementor_fieldname' => 'f12_captcha',
                    'protect_elementor_timer_fieldname' => 'f12_timer',
                    'protect_elementor_method' => $method,
                    'protect_elementor_multiple_submissions' => 1,
                );

                $settings['rules'] = array(
                    'support' => 1,
                    'rule_url' => 1,
                    'rule_url_limit' => 1,
                    'rule_blacklist' => 1,
                    'rule_blacklist_greedy' => 0,
                    'rule_blacklist_value' => '1',
                    'rule_bbcode_url' => 1,
                    'rule_error_message_url' => __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'),
                    'rule_error_message_bbcode' => __('BBCode is not allowed.', 'f12-captcha'),
                    'rule_error_message_blacklist' => __('The word %s is blacklisted.', 'f12-captcha'),
                );

                $settings['ip'] = array(
                    'protect_ip' => 0, // enabled or not
                    'max_retry' => 3, // max retries
                    'max_retry_period' => 300, // time in seconds,
                    'blockedtime' => 3600, // time in seconds - how long will the user be blocked if he fails to often
                    'period_between_submits' => 60, // time between forms submits
                );

                $settings['ultimatemember'] = array(
                    'protect_enable' => 1,
                    'protect_fieldname' => 'f12_captcha',
                    'protect_method' => $method,
                    'protect_login_enable' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                );

                $settings['woocommerce'] = array(
                    'protect_login' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 1,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method,
                );

                $settings['wp_login_page'] = array(
                    'protect_login' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 1,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method
                );
            }

            if ($level === 3) {
                $settings['avada'] = array(
                    'protect_avada' => 1,
                    'protect_avada_time_enable' => 1,
                    'protect_avada_time_ms' => 500,
                    'protect_avada_fieldname' => 'f12_captcha',
                    'protect_avada_timer_fieldname' => 'f12_timer',
                    'protect_avada_multiple_submissions' => 1,
                    'protect_avada_method' => $method,
                    'protect_avada_position' => 'before_submit'
                );

                $settings['cf7'] = array(
                    'protect_cf7_time_enable' => 1,
                    'protect_cf7_time_ms' => 500,
                    'protect_cf7_timer_fieldname' => 'f12_timer',
                    'protect_cf7_fieldname' => 'f12_honey',
                    'protect_cf7_captcha_enable' => 1,
                    'protect_cf7_multiple_submissions' => 1,
                    'protect_cf7_method' => $method
                );

                $settings['comments'] = array(
                    'protect_comments' => 1,
                    'protect_comments_time_enable' => 1,
                    'protect_comments_time_ms' => 500,
                    'protect_comments_fieldname' => 'f12_captcha',
                    'protect_comments_timer_fieldname' => 'f12_timer',
                    'protect_comments_method' => $method
                );

                $settings['elementor'] = array(
                    'protect_elementor' => 1,
                    'protect_elementor_time_enable' => 1,
                    'protect_elementor_time_ms' => 500,
                    'protect_elementor_fieldname' => 'f12_captcha',
                    'protect_elementor_timer_fieldname' => 'f12_timer',
                    'protect_elementor_method' => $method,
                    'protect_elementor_multiple_submissions' => 1,
                );

                $settings['rules'] = array(
                    'support' => 1,
                    'rule_url' => 1,
                    'rule_url_limit' => 1,
                    'rule_blacklist' => 1,
                    'rule_blacklist_greedy' => 0,
                    'rule_blacklist_value' => '1',
                    'rule_bbcode_url' => 1,
                    'rule_error_message_url' => __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'),
                    'rule_error_message_bbcode' => __('BBCode is not allowed.', 'f12-captcha'),
                    'rule_error_message_blacklist' => __('The word %s is blacklisted.', 'f12-captcha'),
                );

                $settings['ip'] = array(
                    'protect_ip' => 1, // enabled or not
                    'max_retry' => 3, // max retries
                    'max_retry_period' => 300, // time in seconds,
                    'blockedtime' => 3600, // time in seconds - how long will the user be blocked if he fails to often
                    'period_between_submits' => 60, // time between forms submits
                );

                $settings['ultimatemember'] = array(
                    'protect_enable' => 1,
                    'protect_fieldname' => 'f12_captcha',
                    'protect_method' => $method,
                    'protect_login_enable' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                );

                $settings['woocommerce'] = array(
                    'protect_login' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 1,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method,
                );

                $settings['wp_login_page'] = array(
                    'protect_login' => 1,
                    'protect_login_fieldname' => 'f12_captcha',
                    'protect_login_method' => $method,
                    'protect_registration' => 1,
                    'protect_registration_fieldname' => 'f12_captcha',
                    'protect_registration_method' => $method
                );
            }

            return $settings;
        }

        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['global'];
            ?>

            <div class="section">

                <h2>
                    <?php _e('General Settings', 'f12-cf7-captcha'); ?>
                </h2>
                <p>
                    <?php _e('Thanks for using Forge12 Spam Protection to protect your forms and logins with an GDPR ready captcha system. Use the settings below to configure your whole system at once, or enable the expert mode to customize each form to fit with your needs.', 'f12-cf7-captcha'); ?>
                </p>
                <br><br>

                <div class="option">
                    <div class="label">
                        <label for="protection_level_high"><?php _e('Protection Level', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protection_level_high"
                                type="radio"
                                value="3"
                                name="protection_level"
                            <?php echo isset($settings['protection_level']) && $settings['protection_level'] === 3 ? 'checked="checked"' : ''; ?>
                        />
                        <span><label
                                    for="protection_level_high"><strong><?php _e('High Security', 'f12-cf7-captcha'); ?></strong> - <?php _e('Enables captcha, IP Protection, Multiple Submission Protection, Filters, Blacklist and Time-Based Protection.', 'f12-cf7-captcha'); ?></label></span>
                        <br><br>

                        <!-- SEPARATOR -->
                        <input
                                id="protection_level_medium"
                                type="radio"
                                value="2"
                                name="protection_level"
                            <?php echo isset($settings['protection_level']) && $settings['protection_level'] === 2 ? 'checked="checked"' : ''; ?>
                        />
                        <span><label
                                    for="protection_level_medium"><strong><?php _e('Medium Security', 'f12-cf7-captcha'); ?></strong> - <?php _e('Enables captcha, Filters, Blacklist and Multiple Submission Protection.', 'f12-cf7-captcha'); ?></label></span>

                        <br><br>

                        <!-- SEPARATOR -->
                        <input
                                id="protection_level_minimum"
                                type="radio"
                                value="1"
                                name="protection_level"
                            <?php echo isset($settings['protection_level']) && $settings['protection_level'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span><label
                                    for="protection_level_minimum"><strong><?php _e('Minimum Security', 'f12-cf7-captcha'); ?></strong> - <?php _e('Enables captcha and Blacklist.', 'f12-cf7-captcha'); ?></label></span>
                        <br><br>

                        <!-- SEPARATOR -->
                        <input
                                id="protection_level_off"
                                type="radio"
                                value="0"
                                name="protection_level"
                            <?php echo isset($settings['protection_level']) && $settings['protection_level'] === 0 ? 'checked="checked"' : ''; ?>
                        />
                        <span><label
                                    for="protection_level_off"><strong><?php _e('Off', 'f12-cf7-captcha'); ?></strong> - <?php _e('Disable everything.', 'f12-cf7-captcha'); ?></label></span>
                    </div>

                </div>

                <div class="option">
                    <div class="label">
                        <label for="protection_method_honey"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protection_method_honey"
                                type="radio"
                                value="honey"
                                name="protection_method"
                            <?php echo isset($settings['protection_method']) && $settings['protection_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protection_method_honey"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protection_method_math"
                                type="radio"
                                value="math"
                                name="protection_method"
                            <?php echo isset($settings['protection_method']) && $settings['protection_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protection_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protection_method_image"
                                type="radio"
                                value="image"
                                name="protection_method"
                            <?php echo isset($settings['protection_method']) && $settings['protection_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protection_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
            </div>
            <p><?php _e('<strong>Warning:</strong> If you save this page, all customized settings will be overwritten.', 'f12-cf7-captcha'); ?></p>

            <?php
        }

        protected function theSidebar($slug, $page)
        {
            ?>
            <div class="box">
                <div class="section">
                    <h2>
                        <?php _e('Need help?', 'f12-cf7-captcha'); ?>
                    </h2>
                    <p>
                        <?php printf(__("Take a look at our <a href='%s' target='_blank'>Documentation</a>.", 'f12-cf7-captcha'), 'https://www.forge12.com/blog/so-verwendest-du-das-wordpress-captcha-um-deine-webseite-zu-schuetzen/'); ?>
                    </p>
                </div>
            </div>
            <?php
        }


    }
}