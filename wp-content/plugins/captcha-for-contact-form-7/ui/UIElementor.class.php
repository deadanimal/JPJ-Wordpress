<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIElementor
     */
    class UIElementor extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'elementor', 'Elementor Forms');
        }

        /**
         * Hide if Elementor is not installed
         *
         * @return false|void
         */
        public function hideInMenu()
        {
            if (!defined('ELEMENTOR_VERSION')) {
                return true;
            }
            return parent::hideInMenu();
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {
            foreach ($settings['elementor'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['elementor'][$key] = (int)$_POST[$key];
                    } else {
                        $settings['elementor'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['elementor'][$key] = 0;
                }
            }

            return $settings;
        }

        protected function addContent($settings)
        {

        }

        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['elementor'];

            ?>
            <h2>
                <?php _e('Elementor Forms', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_elementor"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor"
                                type="checkbox"
                                value="1"
                                name="protect_elementor"
                            <?php echo isset($settings['protect_elementor']) && $settings['protect_elementor'] === 1 ? 'checked="checked"' : ''; ?>
                        />

                        <span>
                        <label for="protect_elementor"><?php _e('Enable Spam Protection for Elementor Forms', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_elementor_method"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor_method"
                                type="radio"
                                value="honey"
                                name="protect_elementor_method"
                            <?php echo isset($settings['protect_elementor_method']) && $settings['protect_elementor_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_elementor_method"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_elementor_method_math"
                                type="radio"
                                value="math"
                                name="protect_elementor_method"
                            <?php echo isset($settings['protect_elementor_method']) && $settings['protect_elementor_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_elementor_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_elementor_method_image"
                                type="radio"
                                value="image"
                                name="protect_elementor_method"
                            <?php echo isset($settings['protect_elementor_method']) && $settings['protect_elementor_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_elementor_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="protect_elementor_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_elementor_fieldname'] ?? 'f12_captcha'; ?>"
                                name="protect_elementor_fieldname"
                        />
                        <span>
                        <label for="protect_elementor_fieldname"><?php _e('Enter a unique name for the Captcha field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
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
                        <label for="protect_elementor_time_enable"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor_time_enable"
                                type="checkbox"
                                value="1"
                                name="protect_elementor_time_enable"
                            <?php echo isset($settings['protect_elementor_time_enable']) && $settings['protect_elementor_time_enable'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_elementor_time_enable"><?php _e('Enable to track the time from entering till submitting the form.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="protect_elementor_time_ms"><?php _e('Time in Milliseconds', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor_time_ms"
                                type="text"
                                value="<?php echo $settings['protect_elementor_time_ms'] ?? 500; ?>"
                                name="protect_elementor_time_ms"
                        />
                        <span>
                        <label for="protect_elementor_time_ms"><?php _e('Enter the Time in Milliseconds to determine if the user is a bot (e.g. enter 1000 for 1 second).', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="protect_elementor_timer_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor_timer_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_elementor_timer_fieldname'] ?? 'f12_timer'; ?>"
                                name="protect_elementor_timer_fieldname"
                        />
                        <span>
                        <label for="protect_elementor_timer_fieldname"><?php _e('Enter a unique name for the Timer field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

            </div>
            <div class="section">
                <h3>
                    <?php _e('Multiple Submission Protection', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_elementor_multiple_submissions"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_elementor_multiple_submissions"
                                type="checkbox"
                                value="1"
                                name="protect_elementor_multiple_submissions"
                            <?php echo isset($settings['protect_elementor_multiple_submissions']) && $settings['protect_elementor_multiple_submissions'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_elementor_multiple_submissions"><?php _e('Enable to prevent forms from being submitted multiple times.', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>
            </div>
            <?php
        }

        protected function theSidebar($slug, $page)
        {
            return;
        }

        /**
         * @param array $settings
         *
         * @return array<mixed>
         */
        public function getSettings($settings)
        {
            $settings['elementor'] = array(
                'protect_elementor' => 0,
                'protect_elementor_time_enable' => 0,
                'protect_elementor_time_ms' => 500,
                'protect_elementor_fieldname' => 'f12_captcha',
                'protect_elementor_timer_fieldname' => 'f12_timer',
                'protect_elementor_method' => 'honey',
                'protect_elementor_multiple_submissions' => 0,
            );

            return $settings;
        }

    }
}