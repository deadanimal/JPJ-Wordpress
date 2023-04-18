<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }
	/**
	 * Class CaptchaImageGenerator
	 * Generate the custom captcha as an image
	 *
	 * @package forge12\contactform7
	 */
	class CaptchaImageGenerator extends CaptchaGenerator {
		/**
		 * @var string The font used for the captcha
		 */
		private $_font = '';

		/**
		 * constructor.
		 */
		public function __construct( $length ) {
			parent::__construct( $length );

			$this->_font = plugin_dir_path( __FILE__ ) . 'assets/arial.ttf';
		}

		/**
		 * Generate the Captcha
		 */
		public function getImage() {
			// the captcha
			$captcha = $this->get();

			// Create the image
			$image = imagecreate( 125, 30 );
			imagecolorallocate( $image, 255, 255, 255 );

			// Positioning
			$offsetLeft = 10;

			for ( $i = 0; $i < strlen( $captcha ); $i ++ ) {
				imagettftext( $image, 20, rand( - 10, 10 ), $offsetLeft + ( ( $i == 0 ? 5 : 15 ) * $i ), 25, imagecolorallocate( $image, 200, 200, 200 ), $this->_font, $captcha[ $i ] );
				imagettftext( $image, 16, rand( - 15, 15 ), $offsetLeft + ( ( $i == 0 ? 5 : 15 ) * $i ), 25, imagecolorallocate( $image, 69, 103, 137 ), $this->_font, $captcha[ $i ] );
			}

			ob_start();
			imagepng($image);
			$image = ob_get_contents();
			ob_end_clean();

			return '<img src="data:image/png;base64,'.base64_encode($image).'"/>';
		}

        public static function validate($captcha_code, $captcha_hash){
            $Captcha = Captcha::getByHash($captcha_hash);

            if(!$Captcha || $captcha_code != $Captcha->getCode()){
                return false;
            }

            return true;
        }

        public static function get_form_field($fieldname, $classes = ''){
            $Captcha = new CaptchaImageGenerator(6);
            $CaptchaItem = __('Captcha:','f12-captcha').' '.$Captcha->getImage();

            $CaptchaSession = new Captcha();
            $CaptchaSession->setCode($Captcha->get());
            $CaptchaSession->save();

            $captcha = '<input type="hidden" id="'.esc_attr($fieldname).'_hash" name="'.esc_attr($fieldname).'_hash" value="' . esc_attr($CaptchaSession->getHash()) . '"/>';
            $captcha .= '<div class="'.$classes.'"><label>'.$CaptchaItem.'</label><input type="text" id="'.esc_attr($fieldname).'" name="' . esc_attr($fieldname) . '" placeholder="'.__('Captcha','f12-captcha').'" value="" /></div>';

            return apply_filters('f12-cf7-captcha-get-form-field-image', $captcha, $fieldname, $CaptchaItem, $CaptchaSession, $classes);
        }
	}
}