<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UIWoocommerce
     */
    class UIWoocommerce extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'woocommerce', 'WooCommerce');
        }

        /**
         * Hide if the Avada Theme is not installed
         *
         * @return false|void
         */
        public function hideInMenu()
        {
            if (!function_exists('WC')) {
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
            $settings['woocommerce'] = array(
                'protect_login' => 0,
                'protect_login_fieldname' => 'f12_captcha',
                'protect_login_method' => 'honey',
                'protect_registration' => 0,
                'protect_registration_fieldname' => 'f12_captcha',
                'protect_registration_method' => 'honey',
            );

            return $settings;
        }

        /**
         * Save on form submit
         */
        protected function onSave($settings)
        {

            foreach ($settings['woocommerce'] as $key => $value) {
                if (isset($_POST[$key])) {
                    if (is_numeric($value)) {
                        $settings['woocommerce'][$key] = (int)$_POST[$key];
                    } else {
                        $settings['woocommerce'][$key] = sanitize_text_field($_POST[$key]);
                    }
                } else {
                    $settings['woocommerce'][$key] = 0;
                }
            }

            return $settings;
        }

        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['woocommerce'];
            ?>
            <h2>
                <?php _e('WooCommerce Login', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_login"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_login"
                                type="checkbox"
                                value="1"
                                name="protect_login"
                            <?php echo isset($settings['protect_login']) && $settings['protect_login'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_login"><?php _e('Enable Spam Protection for WordPress Login', 'f12-cf7-captcha'); ?></label>
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


            <h2>
                <?php _e('WooCommerce Registration', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <h3>
                    <?php _e('Captcha Settings', 'f12-cf7-captcha'); ?>
                </h3>
                <div class="option">
                    <div class="label">
                        <label for="protect_registration"><?php _e('Enable/Disable', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_registration"
                                type="checkbox"
                                value="1"
                                name="protect_registration"
                            <?php echo isset($settings['protect_registration']) && $settings['protect_registration'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_registration"><?php _e('Enable Spam Protection for WordPress Registration', 'f12-cf7-captcha'); ?></label>
                    </span>

                    </div>
                </div>

                <div class="option">
                    <div class="label">
                        <label for="protect_registration_method"><?php _e('Protection Method', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_registration_method"
                                type="radio"
                                value="honey"
                                name="protect_registration_method"
                            <?php echo isset($settings['protect_registration_method']) && $settings['protect_registration_method'] === 'honey' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_login_method"><?php _e('Honeypot', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_registration_method_math"
                                type="radio"
                                value="math"
                                name="protect_registration_method"
                            <?php echo isset($settings['protect_registration_method']) && $settings['protect_registration_method'] === 'math' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_registration_method_math"><?php _e('Arithmetic', 'f12-cf7-captcha'); ?></label>
                    </span><br><br>

                        <input
                                id="protect_registration_method_image"
                                type="radio"
                                value="image"
                                name="protect_registration_method"
                            <?php echo isset($settings['protect_registration_method']) && $settings['protect_registration_method'] === 'image' ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <label for="protect_registration_method_image"><?php _e('Image', 'f12-cf7-captcha'); ?></label>
                    </span>
                    </div>
                </div>


                <div class="option">
                    <div class="label">
                        <label for="protect_registration_fieldname"><?php _e('Fieldname', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <input
                                id="protect_registration_fieldname"
                                type="text"
                                value="<?php echo $settings['protect_registration_fieldname'] ?? 'f12_captcha'; ?>"
                                name="protect_registration_fieldname"
                        />
                        <span>
                        <label for="protect_registration_fieldname"><?php _e('Enter a unique name for the Captcha field. This makes it harder for bots to recognize the honeypot.', 'f12-cf7-captcha'); ?></label>
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