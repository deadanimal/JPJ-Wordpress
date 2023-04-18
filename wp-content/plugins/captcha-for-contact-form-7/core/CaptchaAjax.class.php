<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Captcha
     * Model
     *
     * @package forge12\contactform7
     */
    class CaptchaAjax
    {
        /**
         * Reload a new Captcha For Ajax
         * @return void
         */
        public static function handleReloadCaptcha(){
            $atts = array(
                'captchamethod' => sanitize_text_field($_POST['captchamethod'])
            );

            if ($atts['captchamethod'] == 'math') {
                $Captcha = new CaptchaMathGenerator();
                $CaptchaItem = $Captcha->getCalculation();
            } else if ($atts['captchamethod'] == 'image') {
                $Captcha = new CaptchaImageGenerator(6);
                $CaptchaItem = $Captcha->getImage();
            } else {
                $Captcha = new CaptchaHoneypotGenerator();
                $CaptchaItem = '';
            }

            /**
             * Store the Captcha
             */
            $CaptchaSession = new Captcha();
            $CaptchaSession->setCode($Captcha->get());
            $CaptchaSession->save();

            echo wp_json_encode(['hash' => $CaptchaSession->getHash(), 'label' => $CaptchaItem]);
            wp_die();
        }

        /**
         * Return a new Timer hash for Ajax
         * @return void
         */
        public static function handleReloadTimer(){
            $hash = TimerValidator::getInstance()->addTimer();

            echo wp_json_encode(['hash' => $hash]);
            wp_die();
        }
    }
}