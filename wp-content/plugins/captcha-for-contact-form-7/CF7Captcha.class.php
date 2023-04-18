<?php

namespace forge12\contactform7\CF7Captcha {
    /**
     * Dependencies
     */
    require_once('core/Ajax.class.php');
    require_once('core/IPAddress.class.php');
    require_once('core/IPValidator.class.php');
    require_once('core/IPBan.class.php');
    require_once('core/TimerValidatorController.class.php');
    require_once('core/Compatibility.class.php');
    require_once('core/Captcha.class.php');
    require_once('core/CaptchaGenerator.class.php');
    require_once('core/CaptchaImageGenerator.class.php');
    require_once('core/CaptchaMathGenerator.class.php');
    require_once('core/CaptchaHoneypotGenerator.class.php');
    require_once('core/CaptchaCleaner.class.php');
    require_once('core/CaptchaTimer.class.php');
    require_once('core/CaptchaTimerCleaner.class.php');
    require_once('core/Messages.class.php');
    require_once('core/UI.class.php');
    require_once('core/UIPage.class.php');
    require_once('core/TimerValidator.class.php');
    require_once('core/Salt.class.php');
    require_once('core/IPLog.class.php');
    require_once('core/RulesHandler.class.php');
    require_once('core/Support.class.php');

    require_once('core/log/Log_Controller.class.php');

    /**
     * Plugin Name: Captcha for Contact Form 7
     * Plugin URI: https://www.forge12.com/produkt/contact-form-7-captcha/
     * Description: This plugin allows you to add a captcha to your contact form 7 forms.
     * Version: 1.6.5
     * Author: Forge12 Interactive GmbH
     * Author URI: https://www.forge12.com
     * Text Domain: f12-cf7-captcha
     * Domain Path: /languages
     */
    define('FORGE12_CAPTCHA_VERSION', '1.6.5');
    define('FORGE12_CAPTCHA_SLUG', 'f12-cf7-captcha');
    define('FORGE12_CAPTCHA_BASENAME', plugin_basename(__FILE__));

    /**
     * Class CF7Captcha
     * Controller for the Custom Links.
     *
     * @package forge12\contactform7
     */
    class CF7Captcha
    {
        /**
         * @var CF7Captcha|Null
         */
        private static $_instance = null;

        /**
         * Get the instance of the class
         * @return CF7Captcha
         */
        public static function getInstance()
        {
            if (self::$_instance == null) {
                self::$_instance = new CF7Captcha();
            }

            return self::$_instance;
        }

        /**
         * Return the current url
         * @return string
         */
        public function getCurrentURL()
        {
            global $wp;
            return home_url($wp->request);
        }

        /**
         * Return all saved settings
         * @param string $single The Key of the setting to return only the required setting
         * @return array<mixed>
         */
        public function getSettings($single = '', $container = null)
        {
            $default = array();

            $default = apply_filters('f12_cf7_captcha_settings', $default);

            $settings = get_option('f12_captcha_settings');

            if (!is_array($settings)) {
                $settings = array();
            }

            foreach($default as $key => $data){
                if(isset($settings[$key])) {
                    $default[$key] = array_merge($default[$key], $settings[$key]);
                }
            }

            $settings = $default;

            if (!empty($single)) {
                if ($container != null) {
                    if (isset($settings[$container]) && isset($settings[$container][$single])) {
                        $settings = $settings[$container][$single];
                    }
                }
            } else {
                if (isset($settings[$single])) {
                    $settings = $settings[$single];
                }
            }
            return $settings;
        }

        /**
         * constructor.
         */
        private function __construct()
        {
            // Remove Filter which will not work with our filter list
            add_action('init', function(){
                remove_filter( 'wpcf7_spam', 'wpcf7_disallowed_list', 10 );
            });

            $UI = new UI(FORGE12_CAPTCHA_SLUG, 'Forge12 Spam Protection', 'manage_options');

            $Compatibility = new Compatibility();

            add_action('plugins_loaded', array($this, 'loadTextdomain'));

            $this->loadTextDomain();

            add_action('admin_enqueue_scripts', array($this, 'loadAssets'));

            // Support
            Support::getInstance();
        }

        public function loadAssets(){
            wp_enqueue_script('f12-cf7-captcha-toggle', plugins_url('core/assets/toggle.js', __FILE__), array('jquery'), '1.0');
        }

        /**
         * Check if is a plugin activated.
         * @param $plugin
         * @return bool
         */
        public function is_plugin_activated($plugin)
        {
            if (empty($this->plugins)) {
                $this->plugins = (array)get_option('active_plugins', array());
            }

            if (strpos($plugin, '.php') === false) {
                $plugin = trailingslashit($plugin) . $plugin . '.php';
            }

            return in_array($plugin, $this->plugins) || array_key_exists($plugin, $this->plugins);
        }

        /**
         * Load language files to enable plugin translation
         */
        public function loadTextdomain()
        {
            load_plugin_textdomain('f12-cf7-captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }
    }

    /**
     * Create all required tables to store the captcha codes within the database
     */
    function onActivation()
    {
        Captcha::createTable();
        CaptchaTimer::createTable();
        Salt::createTable();
        IPLog::createTable();
        IPBan::createTable();
    }

    register_activation_hook(__FILE__, 'forge12\contactform7\CF7Captcha\onActivation');

    /**
     * Delete the table only if the plugin is going to be uninstalled to remove all
     * plugin related databases.
     */
    function onDeactivation()
    {
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            return;
        }

        Captcha::deleteTable();
        CaptchaTimer::deleteTable();
        Salt::deleteTable();
        IPLog::deleteTable();
        IPBan::deleteTable();
    }

    register_deactivation_hook(__FILE__, 'forge12\contactform7\CF7Captcha\onDeactivation');

    /**
     * Init the contact form 7 captcha
     */
    CF7Captcha::getInstance();

    do_action('f12_cf7_captcha_init');
}