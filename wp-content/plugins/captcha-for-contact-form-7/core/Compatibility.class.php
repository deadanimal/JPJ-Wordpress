<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Compatibility
     */
    class Compatibility
    {
        /**
         * @var array<string, string>
         */
        private $components = array();

        /**
         * UI constructor.
         * @param $slug
         */
        public function __construct()
        {
            $this->load(dirname(dirname(__FILE__)) . '/compatibility', 0);

            add_action('after_setup_theme', function () {
                add_action('f12_cf7_captcha_ui_after_load_compatibilities', array($this, 'registerComponents'), 10, 1);
                do_action('f12_cf7_captcha_ui_after_load_compatibilities', $this);
            });
        }

        public function registerComponents($Compatibility)
        {
            foreach ($this->components as $component) {
                if (isset($component['name']) && isset($component['path'])) {
                    require_once($component['path']);
                    new $component['name']();
                }
            }
        }

        private function addComponent($name, $path)
        {
            $this->components[] = array(
                'name' => $name,
                'path' => $path
            );
        }

        private function load($directory, $lvl)
        {
            if (is_dir($directory)) {
                $handle = opendir($directory);

                if (!$handle) {
                    return;
                }

                while (false !== ($entry = readdir($handle))) {
                    if ($entry != '.' && $entry != '..') {
                        if (is_dir($directory . '/' . $entry) && $lvl == 0) {
                            $this->load($directory . '/' . $entry, $lvl + 1);
                        } else {
                            if (preg_match('!Controller([a-zA-Z_0-9]+)\.class\.php!', $entry, $matches)) {
                                if (isset($matches[1])) {
                                    $this->addComponent('\\'.__NAMESPACE__.'\Controller' . $matches[1], $directory . '/' . $entry);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}