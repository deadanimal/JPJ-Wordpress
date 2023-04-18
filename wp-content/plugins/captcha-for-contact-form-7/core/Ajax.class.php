<?php
namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    require_once('CaptchaAjax.class.php');
    require_once('RulesAjax.class.php');

    /**
     * Entry Point for Ajax Calls
     */
    class Ajax{
        public function __construct(){
            add_action('wp_ajax_f12_cf7_captcha_reload', '\forge12\contactform7\CF7Captcha\CaptchaAjax::handleReloadCaptcha');
            add_action('wp_ajax_nopriv_f12_cf7_captcha_reload', '\forge12\contactform7\CF7Captcha\CaptchaAjax::handleReloadCaptcha');

            add_action('wp_ajax_f12_cf7_captcha_timer_reload', '\forge12\contactform7\CF7Captcha\CaptchaAjax::handleReloadTimer');
            add_action('wp_ajax_nopriv_f12_cf7_captcha_timer_reload', '\forge12\contactform7\CF7Captcha\CaptchaAjax::handleReloadTimer');

            add_action('wp_ajax_f12_cf7_blacklist_sync', '\forge12\contactform7\CF7Captcha\RulesAjax::handleBlacklistSync');
            add_action('wp_ajax_nopriv_f12_cf7_blacklist_sync', '\forge12\contactform7\CF7Captcha\RulesAjax::handleBlacklistSync');
        }
    }

    new Ajax();
}