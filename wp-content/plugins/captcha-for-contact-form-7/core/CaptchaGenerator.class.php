<?php

namespace forge12\contactform7\CF7Captcha {
    if (!defined('ABSPATH')) {
        exit;
    }
	/**
	 * Class CaptchaGenerator
	 * Generate the custom captcha as an image
	 *
	 * @package forge12\contactform7
	 */
	class CaptchaGenerator {
		/**
		 * @var string List of allowed characters for the captcha
		 */
		private $_allowedCharacters = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';

		/**
		 * The Captcha string.
		 * @var string
		 */
		protected $_captcha = '';

		/**
		 * constructor.
		 */
		public function __construct( $length ) {
			$this->generateCaptcha( $length );
		}

		/**
		 * Initialize the captcha
		 *
		 * @param $length
		 */
		private function generateCaptcha( $length ) {
			$result = '';
			for ( $i = 0; $i < $length; $i ++ ) {
				$result .= $this->_allowedCharacters[ rand( 0, strlen( $this->_allowedCharacters ) - 1 ) ];
			}

			$this->_captcha = $result;
		}

		/**
		 * Generate the captcha string and return it
		 *
		 * @return string
		 */
		public function get() {
			return $this->_captcha;
		}
	}
}