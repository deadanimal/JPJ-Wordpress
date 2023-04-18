<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIAvada
     */
    class UIAvada extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'avada', 'Avada Forms');
        }

        /**
         * Hide if the Avada Theme is not installed
         *
         * @return false|void
         */
        public function hideInMenu()
        {
            if (!function_exists('Avada')) {
                return true;
            }
            return parent::hideInMenu();
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {
            foreach ($settings['avada'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['avada'][$key] = (int)$_POST[$key];
                    } else {
                        $settings['avada'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['avada'][$key] = 0;
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
            $settings = $settings['avada'];

            ?>
            <h2>
                <?php _e('Avada Forms', 'f12-cf7-captcha'); ?>
            </h2>


            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_avada"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada"
                                type="checkbox"
                                value="1"
                                name="protect_avada"
                            <?php echo isset($settings['protect_avada']) && $settings['protect_avada'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_avada"><?php _e('Enable Spam Protection for Avada Forms', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_avada_method"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_method"
                                type="radio"
                                value="honey"
                                name="protect_avada_method"
                            <?php echo isset($settings['protect_avada_method']) && $settings['protect_avada_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_avada_method"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_avada_method_math"
                                type="radio"
                                value="math"
                                name="protect_avada_method"
                            <?php echo isset($settings['protect_avada_method']) && $settings['protect_avada_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_avada_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_avada_method_image"
                                type="radio"
                                value="image"
                                name="protect_avada_method"
                            <?php echo isset($settings['protect_avada_method']) && $settings['protect_avada_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_avada_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_avada_position"><?php _e('Position', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_position_after_submit"
                                type="radio"
                                value="after_submit"
                                name="protect_avada_position"
                            <?php echo isset($settings['protect_avada_position']) && $settings['protect_avada_position'] === 'after_submit' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_avada_position_after_submit"><?php _e('After Submit Button', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_avada_position_before_submit"
                                type="radio"
                                value="before_submit"
                                name="protect_avada_position"
                            <?php echo isset($settings['protect_avada_position']) && $settings['protect_avada_position'] === 'before_submit' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_avada_position_before_submit"><?php _e('Before Submit Button (Beta)', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_avada_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>

                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_avada_fieldname'] ?? 'f12_captcha'; ?>"
                                name="protect_avada_fieldname"
                        />
                        <span>
                        <label for="protect_avada_fieldname"><?php _e('Enter a unique name for the Captcha field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
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
                        <label for="protect_avada_time_enable"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_time_enable"
                                type="checkbox"
                                value="1"
                                name="protect_avada_time_enable"
                            <?php echo isset($settings['protect_avada_time_enable']) && $settings['protect_avada_time_enable'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span><label
                                    for="protect_avada_time_enable"><?php _e('Enable to track the time from entering till submitting the form.', 'f12-cf7-captcha'); ?></label></span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="protect_avada_time_ms"><?php _e('Time in Milliseconds', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_time_ms"
                                type="text"
                                value="<?php echo $settings['protect_avada_time_ms'] ?? 500; ?>"
                                name="protect_avada_time_ms"
                        />
                        <span><label
                                    for="protect_cf7_time_ms"><?php _e('Enter the Time in Milliseconds to determine if the user is a bot (e.g. enter 1000 for 1 second).', 'f12-cf7-captcha'); ?></label></span>
                    </div>
                </div>
                <div class="option">
                    <div class="label">
                        <label for="protect_avada_timer_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>

                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_timer_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_avada_timer_fieldname'] ?? 'f12_timer'; ?>"
                                name="protect_avada_timer_fieldname"
                        />
                        <span><label
                                    for="protect_avada_timer_fieldname"><?php _e('Enter a unique name for the Timer field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label></span>
                    </div>
                </div>
            </div>
            <div class="section">
                <h3>
                    <?php _e('Multiple Submission Protection', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_avada_multiple_submissions"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_avada_multiple_submissions"
                                type="checkbox"
                                value="1"
                                name="protect_avada_multiple_submissions"
                            <?php echo isset($settings['protect_avada_multiple_submissions']) && $settings['protect_avada_multiple_submissions'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                            <label for="protect_avada_multiple_submissions"><?php _e('Enable to prevent forms from being submitted multiple times.', 'f12-cf7-captcha'); ?></label>
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
            $settings['avada'] = array(
                'protect_avada' => 0,
                'protect_avada_time_enable' => 0,
                'protect_avada_time_ms' => 500,
                'protect_avada_fieldname' => 'f12_captcha',
                'protect_avada_timer_fieldname' => 'f12_timer',
                'protect_avada_multiple_submissions' => 0,
                'protect_avada_method' => 'honey',
                'protect_avada_position' => 'after_submit'
            );

            return $settings;
        }

    }
}