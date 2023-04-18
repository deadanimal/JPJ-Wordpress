<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIUltimateMember
     */
    class UIUltimateMember extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'ultimatemember', 'Ultimate Member');
        }

        /**
         * Hide if the CF7 Plugin is not installed
         *
         * @return false|void
         */
        public function hideInMenu()
        {
            if(!class_exists( 'UM_Functions' )) {
                return true;
            }
            return parent::hideInMenu();
        }


        /**
         * Return the Default settings for the
         * Wordpress Login page.
         *
         * @param $settings
         *
         * @return mixed
         */
        public function getSettings($settings)
        {
            $settings['ultimatemember'] = array(
                'protect_enable' => 0,
                'protect_fieldname' => 'f12_captcha',
                'protect_method' => 'honey',
                'protect_login_enable' => 0,
                'protect_login_fieldname' => 'f12_captcha',
                'protect_login_method' => 'honey',
            );

            return $settings;
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {

            foreach ($settings['ultimatemember'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['ultimatemember'][$key] = (int)$_POST[$key];
                    } else {
                        $settings['ultimatemember'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['ultimatemember'][$key] = 0;
                }
            }

            return $settings;
        }

        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['ultimatemember'];
            ?>
            <h2>
                <?php _e('Ultimate Member Registration', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_enable"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_enable"
                                type="checkbox"
                                value="1"
                                name="protect_enable"
                            <?php echo isset($settings['protect_enable']) && $settings['protect_enable'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_enable"><?php _e('Enable Spam Protection for Ultimate Member Registration', 'f12-cf7-captcha'); ?></label>
                    </span>

                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_method"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_method"
                                type="radio"
                                value="honey"
                                name="protect_method"
                            <?php echo isset($settings['protect_method']) && $settings['protect_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_method"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_method_math"
                                type="radio"
                                value="math"
                                name="protect_method"
                            <?php echo isset($settings['protect_method']) && $settings['protect_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_method_image"
                                type="radio"
                                value="image"
                                name="protect_method"
                            <?php echo isset($settings['protect_method']) && $settings['protect_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_fieldname'] ?? 'f12_captcha'; ?>"
                                name="protect_fieldname"
                        />
                        <span>
                        <label for="protect_fieldname"><?php _e('Enter a unique name for the Captcha field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
                    </span>

                    </div>
                </div>
            </div>

            <h2>
                <?php _e('Ultimate Member Login', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_login_enable"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_login_enable"
                                type="checkbox"
                                value="1"
                                name="protect_login_enable"
                            <?php echo isset($settings['protect_login_enable']) && $settings['protect_login_enable'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_login_enable"><?php _e('Enable Spam Protection for Ultimate Member Registration', 'f12-cf7-captcha'); ?></label>
                    </span>

                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_login_method"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_login_method"
                                type="radio"
                                value="honey"
                                name="protect_login_method"
                            <?php echo isset($settings['protect_login_method']) && $settings['protect_login_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_login_method"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_login_method_math"
                                type="radio"
                                value="math"
                                name="protect_login_method"
                            <?php echo isset($settings['protect_login_method']) && $settings['protect_login_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_login_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_login_method_image"
                                type="radio"
                                value="image"
                                name="protect_login_method"
                            <?php echo isset($settings['protect_login_method']) && $settings['protect_login_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_login_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_login_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_login_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_login_fieldname'] ?? 'f12_captcha'; ?>"
                                name="protect_login_fieldname"
                        />
                        <span>
                        <label for="protect_login_fieldname"><?php _e('Enter a unique name for the Captcha field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
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
    }
}