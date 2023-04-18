<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIFilterRules
     */
    class UIFilterRules extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'filterrules', 'Filter Rules');
        }


        protected function onSave($settings)
        {
            foreach ($settings['rules'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['rules'][$key] = (int)$_POST[$key];
                    } elseif ($key == 'rule_blacklist_value') {
                        update_option('disallowed_keys', sanitize_textarea_field($_POST[$key]));
                    } else {
                        $settings['rules'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['rules'][$key] = 0;
                }
            }
            return $settings;
        }

        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['rules'];
            $settings['rule_blacklist_value'] = get_option('disallowed_keys');
            ?>
            <h2>
                <?php _e('Filter Rules', 'f12-cf7-captcha'); ?>
            </h2>
            <p>
                <?php _e('These rules, if enabled, will be applied to all supported forms (Avada, Comments, Elementor, CF7, ...).', 'f12-cf7-captcha'); ?>
            </p>

            <div class="section">
                <h3>
                    <?php _e('URL Filter', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="rule_url"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_url"
                                type="checkbox"
                                value="1"
                                name="rule_url"
                            <?php echo isset($settings['rule_url']) && $settings['rule_url'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <?php _e('If enabled, all fields will be checked if there are any urls exceeding the followed limit.', 'f12-cf7-captcha'); ?>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="rule_url_limit"><?php _e('Limiter', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_url_limit"
                                type="number"
                                value="<?php echo $settings['rule_url_limit'] ?? 0; ?>"
                                name="rule_url_limit"
                        />
                        <span><?php _e('Define how many links are allowed by one field.', 'f12-cf7-captcha'); ?></span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="rule_error_message_url"><?php _e('Error Message', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_error_message_url"
                                type="text"
                                value="<?php echo $settings['rule_error_message_url'] ?? __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'); ?>"
                                name="rule_error_message_url"
                        />
                        <p><?php _e('Define the error message displayed to the visitor.', 'f12-cf7-captcha'); ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>
                    <?php _e('BB Code Filter', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="rule_bbcode_url"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_bbcode_url"
                                type="checkbox"
                                value="1"
                                name="rule_bbcode_url"
                            <?php echo isset($settings['rule_bbcode_url']) && $settings['rule_bbcode_url'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <?php _e('Filter [url={url}]{text}[/url]', 'f12-cf7-captcha'); ?>
                    </span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="rule_error_message_bbcode"><?php _e('Error Message', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_error_message_bbcode"
                                type="text"
                                value="<?php echo $settings['rule_error_message_bbcode'] ?? __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'); ?>"
                                name="rule_error_message_bbcode"
                        />
                        <p><?php _e('Define the error message displayed to the visitor.', 'f12-cf7-captcha'); ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>
                    <?php _e('Blacklist', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="rule_blacklist"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_blacklist"
                                type="checkbox"
                                value="1"
                                name="rule_blacklist"
                            <?php echo isset($settings['rule_blacklist']) && $settings['rule_blacklist'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <?php _e('Enable the blacklist.', 'f12-cf7-captcha'); ?>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="rule_blacklist_greedy"><?php _e('Greedy/Ungreedy', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="rule_blacklist_greedy"
                                type="checkbox"
                                value="1"
                                name="rule_blacklist_greedy"
                            <?php echo isset($settings['rule_blacklist_greedy']) && $settings['rule_blacklist_greedy'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span><?php _e('Enable/Disable greedy filter.', 'f12-cf7-captcha'); ?></span>
                        <p>
                            <?php _e(' If the greedy filter is enabled, even parts of the word will causing the filter to trigger, e.g.: the word "com" is blacklisted and the greedy filter is enabled, this will cause "forge12.com", "composite" and "compose" to also trigger the error message.', 'f12-cf7-captcha'); ?>
                        </p>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="rule_blacklist_value"><?php _e('Blacklist', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <textarea
                                rows="20"
                                id="rule_blacklist_value"
                                name="rule_blacklist_value"
                        ><?php echo $settings['rule_blacklist_value'] ?? ''; ?></textarea>
                        <span><?php _e('Define words that should be blacklisted.', 'f12-cf7-captcha'); ?></span>
                        <br><br>
                        <input type="button" class="button" id="syncblacklist"
                               value="<?php _e('Load predefined Blacklist', 'f12-cf7-captcha'); ?>"/>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="rule_blacklist_value"><?php _e('Error Message', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <p><strong><?php _e('Error Message', 'f12-cf7-captcha'); ?></strong></p>
                        <input
                                id="rule_error_message_blacklist"
                                type="text"
                                value="<?php echo $settings['rule_error_message_blacklist'] ?? __('The word %s is blacklisted.', 'f12-captcha'); ?>"
                                name="rule_error_message_blacklist"
                        />
                        <p><?php _e('Define the error message displayed to the visitor.', 'f12-cf7-captcha'); ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="option">
                    <div class="label">
                        <label for="support"><?php _e('Support Forge12 Captcha: ', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <input type="hidden" class="toggle" name="support"
                               value="<?php esc_attr_e($settings['support']); ?>"
                               data-before="<?php _e('On', 'f12-cf7-captcha'); ?>"
                               data-after="<?php _e('Off', 'f12-cf7-captcha'); ?>"/>

                        <p>
                            <?php _e('The Footer will contain a noscript referral to support Forge12 Captcha.', 'f12-cf7-captcha'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * @param $slug
         * @param $page
         *
         * @return void
         */
        protected function theSidebar($slug, $page)
        {
            return;
        }

        /**
         * @param $settings
         *
         * @return mixed
         */
        public function getSettings($settings)
        {
            $settings['rules'] = array(
                'support' => 1,
                'rule_url' => 0,
                'rule_url_limit' => 0,
                'rule_blacklist' => 0,
                'rule_blacklist_greedy' => 1,
                'rule_blacklist_value' => '',
                'rule_bbcode_url' => 0,
                'rule_error_message_url' => __('The Limit %d has been reached. Remove the %s to continue.', 'f12-captcha'),
                'rule_error_message_bbcode' => __('BBCode is not allowed.', 'f12-captcha'),
                'rule_error_message_blacklist' => __('The word %s is blacklisted.', 'f12-captcha'),
            );

            return $settings;
        }

    }
}