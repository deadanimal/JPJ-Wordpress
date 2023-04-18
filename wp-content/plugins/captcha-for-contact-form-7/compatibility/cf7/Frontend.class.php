<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }

    /**
     * Class Frontend
     * Responsible to handle the frontend of the Double OptIn
     *
     * @package forge12\contactform7\CF7Captcha
     */
    class Frontend
    {
        /**
         * Admin constructor.
         */
        public function __construct()
        {
            add_action('wpcf7_init', array($this, 'addFormTag'), 10, 0);
            add_filter('wpcf7_validate_f12_captcha', array($this, 'validateCaptcha'), 10, 2);
        }

        /**
         * Validate the Captcha
         *
         * @param $result
         * @param $tag
         */
        public function validateCaptcha($result, $tag)
        {
            if (empty($tag->name)) {
                $result->invalidate($tag, wpcf7_get_message('invalid_required'));
            } else {
                $value = sanitize_text_field($_POST[$tag->name]);
                $hash = sanitize_text_field($_POST[$tag->name.'_hash']);

                $captchamethod = $tag->get_option('captcha', '', true);

                $Captcha = Captcha::getByHash($hash);

                if (empty($value) && 'honey' != $captchamethod) {
                    $result->invalidate($tag, wpcf7_get_message('invalid_required'));
                } else if (!$Captcha || $value != $Captcha->getCode()) {
                    $result->invalidate($tag, __('Captcha not valid', 'f12-cf7-captcha'));
                } else if (!empty($value) && 'honey' == $captchamethod) {
                    $result->invalidate($tag, __('Spam', 'f12-cf7-captcha'));
                }else{
                    $Captcha->setValidated(1);
                    $Captcha->save();
                }
            }

            return $result;
        }

        /**
         * Add Captcha Tag
         */
        public function addFormTag()
        {
            $manager = \WPCF7_FormTagsManager::get_instance();
            $manager->add('f12_captcha', array($this, 'addFormTagHandler'), array('name-attr' => true));
        }

        /**
         * Add the captcha field to the form
         *
         * @param $tag
         *
         * @return string
         */
        public function addFormTagHandler($tag)
        {
            if (empty($tag->name)) {
                return '';
            }

            if (function_exists('wpcf7_get_validation_error')) {
                $validation_error = wpcf7_get_validation_error($tag->name);
            } else {
                return '';
            }

            if (function_exists('wpcf7_form_controls_class')) {
                $class = wpcf7_form_controls_class($tag->type);
            } else {
                $class = '';
            }

            $class .= ' wpcf7-validates-as-captcha';

            if ($validation_error) {
                $class .= ' wpcf7-not-valid';
            }

            $atts = array();

            $atts['captchamethod'] = $tag->get_option('captcha', '', true);
            $atts['class'] = $tag->get_class_option($class);
            $atts['id'] = $tag->get_id_option();
            $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);
            $atts['step'] = $tag->get_option('step', 'int', true);

            $atts['aria-required'] = 'true';

            if ($validation_error) {
                $atts['aria-invalid'] = 'true';
                $atts['aria-describedby'] = wpcf7_get_validation_error_reference(
                    $tag->name
                );
            } else {
                $atts['aria-invalid'] = 'false';
            }

            $value = (string)reset($tag->values);

            if ($tag->has_option('placeholder')
                or $tag->has_option('watermark')) {
                $atts['placeholder'] = $value;
                $value = '';
            } else {
                $atts['placeholder'] = __('Captcha', 'f12-cf7-captcha');
            }

            $value = $tag->get_default_option($value);

            $atts['value'] = $value;

            $atts['type'] = 'text';

            $atts['name'] = $tag->name;

            if ($atts['captchamethod'] == 'math') {
                $Captcha = new CaptchaMathGenerator();
                $CaptchaItem = $Captcha->getCalculation();
            } else if ($atts['captchamethod'] == 'image') {
                $Captcha = new CaptchaImageGenerator(6);
                $CaptchaItem = $Captcha->getImage();
            } else {
                $Captcha = new CaptchaHoneypotGenerator();
                $CaptchaItem = '';
                $atts['type'] = 'text';
                unset($atts['aria-required']);
            }

            /**
             * Store the Captcha
             */
            $CaptchaSession = new Captcha();
            $CaptchaSession->setCode($Captcha->get());
            $CaptchaSession->save();

            $styles = '';

            $method = $atts['captchamethod'];
            if($atts['captchamethod'] == 'honey'){
                $styles = 'width:0; height:0; max-width:0; max-height:0; opacity:0;';
            }

            if (function_exists('wpcf7_format_atts')) {
                $atts = wpcf7_format_atts($atts);
            }

            $html = sprintf(
                '<span class="wpcf7-form-control-wrap %1$s" data-name="%1$s" style="'.$styles.'"><input class="f12c wpcf7-validates-as-required" data-method="'.esc_attr($method).'" id="'.esc_attr($tag->name).'" %2$s style="'.$styles.'"/><label>%3$s %4$s</label></span>',
                sanitize_html_class($tag->name), $atts, $CaptchaItem, $validation_error
            );

            $html .= "<input type=\"hidden\" id=\"".esc_attr($tag->name)."_hash\" name=\"".esc_attr($tag->name)."_hash\" value=\"" . esc_attr($CaptchaSession->getHash()) . "\"/>";

            return $html;
        }
    }
}