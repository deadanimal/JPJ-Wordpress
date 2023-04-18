<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIComments
     */
    class UIComments extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'comments', 'Comments');
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {

            foreach ($settings['comments'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['comments'][$key] = (int)$_POST[$key];
                    } else {
                        $settings['comments'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['comments'][$key] = 0;
                }
            }

            return $settings;
        }

        private function addContent($settings)
        {
            ?>
            <div class="option">
                <div class="label">
                    <label for="protect_comments"><?php _e('Comments / Discussions', 'f12-cf7-captcha'); ?></label>
                </div>
                <div class="input">
                    <!-- SEPARATOR -->
                    <p style="margin-top:0;"><strong><?php _e('Enable Captcha Protection', 'f12-cf7-captcha'); ?></strong>
                    </p>
                    <input
                            id="protect_comments"
                            type="checkbox"
                            value="1"
                            name="protect_comments"
                        <?php echo isset($settings['protect_comments']) && $settings['protect_comments'] === 1 ? 'checked="checked"' : ''; ?>
                    />
                    <span>
                        <label for="protect_comments"><?php _e('Enable Spam Protection for WordPress Comments', 'f12-cf7-captcha'); ?></label>
                    </span>

                    <!-- SEPARATOR -->
                    <p><strong><?php _e('Enable Time Based Protection', 'f12-cf7-captcha'); ?></strong></p>
                    <input
                            id="protect_comments_time_enable"
                            type="checkbox"
                            value="1"
                            name="protect_comments_time_enable"
                        <?php echo isset($settings['protect_comments_time_enable']) && $settings['protect_comments_time_enable'] === 1 ? 'checked="checked"' : ''; ?>
                    />
                    <span>
                                <label for="protect_comments_time_enable"><?php _e('Enable to track the time from entering till submitting the form.', 'f12-cf7-captcha'); ?></label>
                            </span>

                    <!-- SEPARATOR -->
                    <p><strong><?php _e('Time in Milliseconds', 'f12-cf7-captcha'); ?></strong></p>
                    <input
                            id="protect_comments_time_ms"
                            type="text"
                            value="<?php echo $settings['protect_comments_time_ms'] ?? 500; ?>"
                            name="protect_comments_time_ms"
                    />
                    <span>
                                <label for="protect_comments_time_ms"><?php _e('Enter the Time in Milliseconds to determine if the user is a bot (e.g. enter 1000 for 1 second).', 'f12-cf7-captcha'); ?></label>
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
            $settings = $settings['comments'];
            ?>
            <h2>
                <?php _e('Comments', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_comments"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_comments"
                                type="checkbox"
                                value="1"
                                name="protect_comments"
                            <?php echo isset($settings['protect_comments']) && $settings['protect_comments'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_comments"><?php _e('Enable captcha protection.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_comments_method"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_comments_method"
                                type="radio"
                                value="honey"
                                name="protect_comments_method"
                            <?php echo isset($settings['protect_comments_method']) && $settings['protect_comments_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_comments_method"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_comments_method_math"
                                type="radio"
                                value="math"
                                name="protect_comments_method"
                            <?php echo isset($settings['protect_comments_method']) && $settings['protect_comments_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_comments_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_comments_method_image"
                                type="radio"
                                value="image"
                                name="protect_comments_method"
                            <?php echo isset($settings['protect_comments_method']) && $settings['protect_comments_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_comments_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_comments_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_comments_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_comments_fieldname'] ?? 'f12_captcha'; ?>"
                                name="protect_comments_fieldname"
                        />
                        <span>
                        <label for="protect_comments_fieldname"><?php _e('Enter a unique name for the Captcha field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>
                    <?php _e('Time Based Protection', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_comments_time_enable"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_comments_time_enable"
                                type="checkbox"
                                value="1"
                                name="protect_comments_time_enable"
                            <?php echo isset($settings['protect_comments_time_enable']) && $settings['protect_comments_time_enable'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_comments_time_enable"><?php _e('Enable to track the time from entering till submitting the form.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_comments_time_ms"><?php _e('Time in Milliseconds', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_comments_time_ms"
                                type="text"
                                value="<?php echo $settings['protect_comments_time_ms'] ?? 500; ?>"
                                name="protect_comments_time_ms"
                        />
                        <span>
                        <label for="protect_comments_time_ms"><?php _e('Enter the Time in Milliseconds to determine if the user is a bot (e.g. enter 1000 for 1 second).', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="protect_comments_timer_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">

                        <!-- SEPARATOR -->
                        <input
                                id="protect_comments_timer_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_comments_timer_fieldname'] ?? 'f12_timer'; ?>"
                                name="protect_comments_timer_fieldname"
                        />
                        <span>
                        <label for="protect_comments_timer_fieldname"><?php _e('Enter a unique name for the Timer field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
            </div>
            <?php
        }

        protected function theSidebar($slug, $page)
        {
            if ($page != 'settings') {
                return;
            }
        }

        public function getSettings($settings)
        {
            $settings['comments'] = array(
                'protect_comments' => 0,
                'protect_comments_time_enable' => 0,
                'protect_comments_time_ms' => 500,
                'protect_comments_fieldname' => 'f12_captcha',
                'protect_comments_timer_fieldname' => 'f12_timer',
                'protect_comments_method' => 'honey'
            );

            return $settings;
        }
    }
}