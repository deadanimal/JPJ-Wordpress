<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class UILog
     */
    class UILog extends UIPage
    {
        public function __construct($domain)
        {
            parent::__construct($domain, 'log', 'Log Settings', 99);
        }


        protected function onSave($settings)
        {
            foreach ($settings['logs'] as $key => $value) {
                if (isset($_POST[$key])) {
                    $settings['logs'][$key] = (int)$_POST[$key];
                } else {
                    $settings['logs'][$key] = 0;
                }
            }
            return $settings;
        }

        /**
         * Render the license subpage content
         */
        protected function theContent($slug, $page, $settings)
        {
            $settings = $settings['logs'];
            ?>
            <h2>
                <?php _e('Log Settings', 'f12-cf7-captcha'); ?>
            </h2>

            <div class="section">
                <div class="option">
                    <div class="label">
                        <label for="enable"><?php _e('Status', 'f12-cf7-captcha'); ?></label>
                    </div>
                    <div class="input">
                        <!-- SEPARATOR -->
                        <p style="margin-top:0;"><strong><?php _e('Enable Logging', 'f12-cf7-captcha'); ?></strong></p>
                        <input
                                id="enable"
                                type="checkbox"
                                value="1"
                                name="enable"
                            <?php echo isset($settings['enable']) && $settings['enable'] === 1 ? 'checked="checked"' : ''; ?>
                        />
                        <span>
                        <?php _e('If enabled, all submitted forms will be tracked within the log entries.', 'f12-cf7-captcha'); ?>
                    </span>
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
            $settings['logs'] = array(
                'enable' => 1,
            );

            return $settings;
        }

    }
}