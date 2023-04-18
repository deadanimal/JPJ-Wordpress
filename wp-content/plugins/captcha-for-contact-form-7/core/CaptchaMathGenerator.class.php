<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }
	/**
	 * Class CaptchaMathGenerator
	 * Generate the custom captcha as an image
	 *
	 * @package forge12\contactform7
	 */
	class CaptchaMathGenerator extends CaptchaGenerator {
		/**
		 * First number
		 */
		private $_number_1 = 0;

		/**
		 * Last number
		 */
		private $_number_2 = 0;

		/**
		 * Method
		 */
		private $_method = '+';

		/**
		 * Allowed math calculations
		 */
		private $_allowed_method = '+-*';

		/**
		 * constructor.
		 */
		public function __construct() {
			parent::__construct( 0 );

			$this->init();
		}

		/**
		 * Return a random number
		 *
		 * @param int min
		 * @param int max
		 *
		 * @return int
		 */
		private function generateNumber( $min, $max ) {
			return rand( $min, $max );
		}

		/**
		 * Init the captcha
		 */
		private function init() {
			$this->_number_1 = $this->generateNumber( 5, 10 );
			$this->_number_2 = $this->generateNumber( 1, 5 );

			$this->_method   = $this->_allowed_method[ $this->generateNumber( 0, 2 ) ];

			switch ( $this->_method ) {
				case '*':
					$this->_captcha = $this->_number_1 * $this->_number_2;
					break;
				case '-':
					$this->_captcha = $this->_number_1 - $this->_number_2;
					break;
				case '+':
				default:
					$this->_captcha = $this->_number_1 + $this->_number_2;
					break;
			}
		}

		/**
		 * Get the Value of the captcha
		 * @return string|void
		 */
		public function get() {
			return $this->_captcha;
		}

		/**
		 * Generate the Captcha
		 * @return string
		 */
		public function getCalculation() {
			return $this->_number_1 . ' ' . $this->_method . ' ' . $this->_number_2.' = ?';
		}

        public static function validate($captcha_code, $captcha_hash){
            $Captcha = Captcha::getByHash($captcha_hash);

            if(!$Captcha || $captcha_code != $Captcha->getCode()){
                return false;
            }

            return true;
        }

        public static function get_form_field($fieldname, $classes = ''){
            $Captcha = new CaptchaMathGenerator();
            $CaptchaItem = $Captcha->getCalculation();

            $CaptchaSession = new Captcha();
            $CaptchaSession->setCode($Captcha->get());
            $CaptchaSession->save();

            $captcha = '<input type="hidden" id="'.esc_attr($fieldname).'_hash" name="'.esc_attr($fieldname).'_hash" value="' . esc_attr($CaptchaSession->getHash()) . '"/>';
            $captcha .= '<div class="'.$classes.'"><label>'.$CaptchaItem.'</label><input type="text" id="'.esc_attr($fieldname).'" name="' . esc_attr($fieldname) . '" placeholder="'.__('Captcha','f12-captcha').'" value="" class="" /></div>';

            return apply_filters('f12-cf7-captcha-get-form-field-math', $captcha, $fieldname, $CaptchaItem, $CaptchaSession, $classes);
        }
	}
}